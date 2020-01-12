<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Utils extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function save_itemimg() {
        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');

        $arrayext=array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
        if (isset($_GET['qqfile'])) {
            $file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $file = new qqUploadedFileForm();
        } elseif (isset($_POST['qqfile'])) {
            $file = new qqUploadedFileXhr();
        } else {
            die('{error: "server-error file not passed"}');
        }



        if ($file) {
            $filename = $file->getName();
            $filesize = $file->getSize();

            if ($filesize == 0)
                die('{error: "server-error file size is zero"}');

            $pathinfo = pathinfo($file->getName());

            $filename = uniq_link(12);
            $ext = strtolower($pathinfo['extension']);
            if (!in_array($ext, $arrayext )) {
                $these = implode(', ', $arrayext);
                echo (json_encode(array('success' => false, 'error' => 'File has an invalid extension, it should be one of '. $these . '.')));
                exit();
            } else {
                $file->save($path . $filename . '.' . $ext);
                echo (json_encode(array('success' => true, 'filename' => $path_sh.$filename . '.' . $ext, 'filesize' => $filesize)));
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }

        die('{error: "server-error query params not passed"}');
    }

}