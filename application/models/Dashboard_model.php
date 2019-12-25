<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Dashboard_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_totals($dayview) {
        if ($dayview =='day') {
            $options = [
                'conversions' => 45,
                'sales' => 16,
                'revenue' => 5448,
            ];
            $label = date('l, F j, Y');
        } else {
            $options = [
                'conversions' => 204,
                'sales' => 125,
                'revenue' => 124486,
            ];
            $weeknum = date('W');
            $year = date('Y');
            $dates = getDatesByWeek($weeknum, $year);
            $label = 'M '.date('M j', $dates['start_week']).' - S '.date('M j', $dates['end_week']).' '.$year;
        }
        return ['data'=>$options, 'label'=>$label];
    }
}
?>