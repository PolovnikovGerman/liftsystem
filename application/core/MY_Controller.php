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
