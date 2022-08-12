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
 * @property Calendars_model calendars_model
 * @property Permissions_model permissions_model
 * @property Reports_model reports_model
 * @property Searchresults_model searchresults_model
 * @property Coupons_model coupons_model
 * @property Payments_model payments_model
 * @property Printshop_model printshop_model
 * @property Exportexcell_model exportexcell_model
 * @property Rates_model rates_model
 * @property Customform_model customform_model
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