<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class My_Model
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
 */

class MY_Model extends CI_Model
{
    public $success_result =1;
    public $error_result = 0;

    public $websystem='System';
    public $websystem_id=1;


    public function __construct()
    {
        date_default_timezone_set('America/New_York');
        parent::__construct();
    }

    public function userlog($user_id, $action='', $activity = 0) {
        $this->load->model('useractivity_model');
        $this->useractivity_model->addactivity($user_id, $action, $activity);
    }

}
?>