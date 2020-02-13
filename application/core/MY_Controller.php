<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class Base_Controller
 *
 * @property Template template
 * @property User_model user_model
 * @property Useractivity_model useractivity_model
 * @property Dashboard_model dashboard_model
 * @property Menuitems_model menuitems_model
 * @property Suppliers_model suppliers_model
 * @property Items_model items_model
 * @property Sections_model sections_model
 * @property Categories_model categories_model
 * @property Orders_model orders_model
 * @property Artproof_model artproof_model
 * @property Artwork_model artwork_model
 * @property Email_model email_model
 * @property Leadorder_model leadorder_model
 * @property Leads_model leads_model
 * @property Questions_model questions_model
 * @property Quotes_model quotes_model
 * @property Otherprices_model otherprices_model
 * @property Vendors_model vendors_model
 * @property Itemcategory_model itemcategory_model
 * @property Itemimages_model itemimages_model
 * @property Prices_model prices_model
 * @property Staticpages_model staticpages_model
 * @property Imprints_model imprints_model
 * @property Itemcolors_model itemcolors_model
 * @property Similars_model similars_model
 * @property Itemdetails_model itemdetails_model
 * @property Creditapp_model creditapp_model
 * @property Artlead_model artlead_model
 * @property Shipping_model shipping_model
 * @property Engaded_model engaded_model
 * @property Balances_model balances_model
 * @property Tickets_model tickets_model
 * @property Batches_model batches_model
 * @property Permissions_model permissions_model
 * @property Reports_model reports_model
 * @property Searchresults_model searchresults_model
*/


class Base_Controller extends CI_Controller
{
}

class MY_Controller extends Base_Controller
{

    public $success_result =1;
    public $error_result = 0;
    public $USR_ROLE='';
    public $USR_ID='';
    public $USER_NAME='';
    public $URER_LOGO='';
    public $USER_EMAIL='';
    public $USER_REPLICA='';

    public function __construct()
    {
        parent::__construct();

        $this->load->library('user_agent');
        $this->load->model('user_model');
        $this->load->model('useractivity_model');
        $this->load->model('menuitems_model');
        date_default_timezone_set('America/New_York');

        $user = $this->user_model->current_user();
        if ($user['result']==0) {
            if ($this->isAjax()) {
                $this->ajaxResponse(array('url'=>'/login'),'Your connection has been lost. Please log in');
            } else {
                redirect('/login');
            }
        }

        $userdat = $user['data'];
        $this->USR_ROLE = $userdat['user_logged_in'];
        $this->USR_ID = $userdat['id'];
        $this->USER_NAME = $userdat['user_name'];
        $this->URER_LOGO = $userdat['user_logo'];
        $this->USER_EMAIL = $userdat['user_email'];
        $this->URER_LOGO = $userdat['user_logo'];
        $this->USER_REPLICA=$userdat['user_replica'];

    }

    public function userlog($user_id, $action='', $activity=0) {
        $this->useractivity_model->addactivity($user_id, $action, $activity);
    }

    public function ajaxResponse($mdata, $merrors = array())
    {
        $aResponse = array(
            'data' => $mdata,
            'errors' => $merrors
        );
        echo(json_encode($aResponse));
        exit;
    }

    public function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            return TRUE;
        }

        return FALSE;
    }


}
