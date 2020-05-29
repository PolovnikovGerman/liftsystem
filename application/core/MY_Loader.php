<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

    /**
     * Database Loader [modified to synchronize mysql database timezone]
     * @author Sujeet <sujeetkv90@gmail.com>
     *
     * @param	mixed	$params		Database configuration options
     * @param	bool	$return 	Whether to return the database object
     * @param	bool	$query_builder	Whether to enable Query Builder
     *					(overrides the configuration setting)
     *
     * @return	object|bool	Database object if $return is set to TRUE,
     *					FALSE on failure, CI_Loader instance in any other case
     */
    public function database($params = '', $return = FALSE, $query_builder = NULL)
    {
        if ($return === TRUE)
        {
            $db =& parent::database($params, $return, $query_builder);

            if (in_array($db->platform(), array('mysql', 'mysqli')))
            {
                $db->query("SET time_zone = '".date('P')."'");
            }

            return $db;
        }

        if (parent::database($params, $return, $query_builder))
        {
            $CI =& get_instance();

            if (in_array($CI->db->platform(), array('mysql', 'mysqli')))
            {
                $CI->db->query("SET time_zone = '-05:00'");
            }

            return $this;
        }
        else
        {
            return FALSE;
        }
    }

}

/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */