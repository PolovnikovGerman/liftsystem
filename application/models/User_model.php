<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class User_model extends MY_Model
{

    private $delete_status=3;
    private $user_active = 1;
    private $user_paused = 2;

    public function __construct() {
        parent::__construct();
    }

}
/* End of file user_model.php */
/* Location: ./application/models/user_model.php */