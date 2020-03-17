<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Rates_model
 *
 * @author german polovnikov
 */

class Rates_model extends My_Model
{

    private $error_message='Unknown error. Try later';

    function __construct()
    {
        parent::__construct();
    }

    public function get_ratestable($calc_type, $tax_type) {
        $this->db->select('*');
        $this->db->from('ts_tax_rates');
        $this->db->where('calc_type', $calc_type);
        $this->db->where('tax_type', $tax_type);
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            $income='$'.QTYOutput($row['limit_start']);
            if ($row['limit_finish']==0) {
                $income.=' +';
            } else {
                $income.=' - '.QTYOutput($row['limit_finish']);
            }
            $precis=3;
            $rateval=number_format($row['rate'],$precis);

            for ($i=$precis; $i>=0; $i--) {
                if (substr($rateval, -1)!=='0') {
                    break;
                }
                $rateval=substr($rateval,0,-1);
            }
            $newrateval=number_format($row['rate'],$i);
            $out[]=array(
                'rate'=>$newrateval.'%',
                'income'=>$income,
            );
        }
        return $out;
    }

    public function get_ownertaxdata($calc_type, $user, $brand) {
        if ($brand=='ALL') {
            $this->db->select('sum(salary) as salary, sum(other_income) as other_income, sum(partner_income) as partner_income, sum(k401) as k401');
            $this->db->select('sum(property_tax) as property_tax, sum(mortgage_int) as mortgage_int, sum(other_deduct) as other_deduct');
            $this->db->select('sum(fed_withheld) as fed_withheld, sum(state_withheld) as state_withheld, sum(other_taxes) as other_taxes, count(owner_tax_id) as owner_tax_id');
        } else {
            $this->db->select('*');
        }
        $this->db->from('ts_owner_taxes');
        $this->db->where('calc_type', $calc_type);
        $this->db->where('owner', $user);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $data=$this->db->get()->row_array();
        if (!isset($data['owner_tax_id']) && $brand!=='ALL') {
            $this->db->set('calc_type', $calc_type);
            $this->db->set('owner', strtoupper($user));
            $this->db->set('brand', $brand);
            $this->db->insert('ts_owner_taxes');
            // Repeat Get
            $this->db->select('*');
            $this->db->from('ts_owner_taxes');
            $this->db->where('calc_type', $calc_type);
            $this->db->where('brand', $brand);
            $data=$this->db->get()->row_array();
        }
        return $data;
    }

    public function get_owner_taxes($calc_type, $data) {
        $salary=floatval($data['salary']);
        $other_income=floatval($data['other_income']);
        // Get Netprofit data
        $owner_drawer = round(($data['netprofit'])*$data['profitkf'],2);
        $partner_income=floatval($data['partner_income']);
        $total_income=$owner_drawer*$data['od_incl']+$salary+$partner_income+$other_income;

        // Deductions
        $k401=floatval($data['k401']);
        $property_tax=floatval($data['property_tax']);
        $mortgage_int=floatval($data['mortgage_int']);
        $other_deduct=floatval($data['other_deduct']);
        // Taxable Income
        $taxable_income=$total_income-($k401+$property_tax+$mortgage_int+$other_deduct);
        // Calc Federal Tax
        $fed_taxes=$this->calctaxvalue($taxable_income, $calc_type, 'federal');
        $fed_withheld=floatval($data['fed_withheld']);
        $fed_taxes_due=$fed_taxes-$fed_withheld;
        $fed_pay=round($fed_taxes_due/4,2);
        // State Tax
        $state_taxes=$this->calctaxvalue($taxable_income, $calc_type, 'state');
        $state_withheld=floatval($data['state_withheld']);
        $state_taxes_due=$state_taxes-$state_withheld;
        $state_pay=round($state_taxes_due/4,2);
        // Other Tax
        $other_taxes=floatval($data['other_taxes']);
        // Taxable Income - Federal Taxes - NJ Taxes - Other Taxes
        $take_home=$taxable_income-$fed_taxes-$state_taxes-$other_taxes;
        $out=array(
            'salary'=>$salary,
            'other_income'=>$other_income,
            'partner_income'=>$partner_income,
            'owner_drawer'=>$owner_drawer,
            'total_income'=>$total_income,
            'k401'=>$k401,
            'property_tax'=>$property_tax,
            'mortgage_int'=>$mortgage_int,
            'other_deduct'=>$other_deduct,
            'taxable_income'=>$taxable_income,
            'fed_taxes'=>$fed_taxes,
            'fed_withheld'=>$fed_withheld,
            'fed_taxes_due'=>$fed_taxes_due,
            'fed_pay'=>$fed_pay,
            'state_taxes'=>$state_taxes,
            'state_withheld'=>$state_withheld,
            'state_taxes_due'=>$state_taxes_due,
            'state_pay'=>$state_pay,
            'other_taxes'=>$other_taxes,
            'take_home'=>$take_home,
            'netprofit'=>$data['netprofit'],
            'profitkf'=>$data['profitkf'],
            'od_incl'=>$data['od_incl'],
        );
        return $out;
    }

    public function taxowner_change($session, $calc_type, $fldname, $newval, $session_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>'Field Not Found');
        $calcdata=$session[$calc_type];
        // Change Parameter
        if (array_key_exists($fldname, $calcdata)) {
            $out['result']=$this->success_result;
            $calcdata[$fldname]=floatval($newval);
            $out['newval']=floatval($newval);
            $newcalcdata=$this->get_owner_taxes($calc_type, $calcdata);
            $out['calc']=$newcalcdata;
            $session[$calc_type]=$newcalcdata;
            usersession($session_id, $session);
        }
        return $out;
    }

    public function taxowner_change_odincl($session, $od_incl, $session_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>'Field Not Found');
        $single=$session['single'];
        $joint=$session['joint'];
        $single['od_incl']=$joint['od_incl']=($od_incl==0 ? 1 : 0);
        $newsingle=$this->get_owner_taxes('single', $single);
        $newjoint=$this->get_owner_taxes('joint', $joint);
        $session['single']=$newsingle;
        $session['joint']=$newjoint;
        $out['result']=$this->success_result;
        usersession($session_id, $session);
        return $out;
    }

    // Save Tax Owner Inputs
    public function taxowner_save($session, $user, $session_id, $brand) {
        $out=array('result'=>  $this->error_result, 'msg'=>'Field Not Found');
        $single=$session['single'];
        $joint=$session['joint'];
        // Single Calc
        $this->db->select('*');
        $this->db->from('ts_owner_taxes');
        $this->db->where('calc_type','single');
        $this->db->where('owner', $user);
        $this->db->where('brand', $brand);
        $singleres=$this->db->get()->row_array();
        $single_key=0;
        if (isset($singleres['owner_tax_id'])) {
            $single_key=$singleres['owner_tax_id'];
        }
        // Update Single
        $this->db->set('salary', $single['salary']);
        $this->db->set('other_income', $single['other_income']);
        $this->db->set('k401', $single['k401']);
        $this->db->set('property_tax', $single['property_tax']);
        $this->db->set('mortgage_int', $single['mortgage_int']);
        $this->db->set('other_deduct', $single['other_deduct']);
        $this->db->set('fed_withheld', $single['fed_withheld']);
        $this->db->set('state_withheld', $single['state_withheld']);
        $this->db->set('other_taxes', $single['other_taxes']);
        if ($single_key==0) {
            $this->db->set('owner', $user);
            $this->db->set('calc_type', 'single');
            $this->db->insert('ts_owner_taxes');
        } else {
            $this->db->where('owner_tax_id', $single_key);
            $this->db->update('ts_owner_taxes');
        }
        // Joint Calc
        $this->db->select('*');
        $this->db->from('ts_owner_taxes');
        $this->db->where('calc_type','joint');
        $this->db->where('owner', $user);
        $this->db->where('brand', $brand);
        $jointres=$this->db->get()->row_array();
        $joint_key=0;
        if (isset($jointres['owner_tax_id'])) {
            $joint_key=$jointres['owner_tax_id'];
        }
        // Update Joint
        $this->db->set('salary', $joint['salary']);
        $this->db->set('other_income', $joint['other_income']);
        $this->db->set('partner_income', $joint['partner_income']);
        $this->db->set('k401', $joint['k401']);
        $this->db->set('property_tax', $joint['property_tax']);
        $this->db->set('mortgage_int', $joint['mortgage_int']);
        $this->db->set('other_deduct', $joint['other_deduct']);
        $this->db->set('fed_withheld', $joint['fed_withheld']);
        $this->db->set('state_withheld', $joint['state_withheld']);
        $this->db->set('other_taxes', $joint['other_taxes']);
        if ($joint_key==0) {
            $this->db->set('owner', $user);
            $this->db->set('calc_type', 'joint');
            $this->db->insert('ts_owner_taxes');
        } else {
            $this->db->where('owner_tax_id', $joint_key);
            $this->db->update('ts_owner_taxes');
        }
        $out['result']=$this->success_result;
        usersession($session_id,NULL);
        return $out;
    }

    public function calctaxvalue($taxable, $calc_type, $tax_type) {
        $this->db->select('*');
        $this->db->from('ts_tax_rates');
        $this->db->where('calc_type', $calc_type);
        $this->db->where('tax_type', $tax_type);
        $this->db->order_by('limit_start');
        $rates=$this->db->get()->result_array();

        $tax=0;
        // Lets go
        foreach ($rates as $row) {
            if ($taxable>=$row['limit_start']) {
                // Add tax
                $limit=($row['limit_finish']==0 ? 999999999999 : $row['limit_finish']);
                if ($taxable>$limit) {
                    $base=$limit-$row['limit_start'];
                } else {
                    $base=($taxable-$row['limit_start']);
                }
                $taxpart=round($base*($row['rate']/100),2);
                // echo 'Rate '.$row['rate'].' Base '.$base.' Part '.$taxpart.PHP_EOL;
                $tax+=$taxpart;
            }
        }
        return $tax;
   }

}