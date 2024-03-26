<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Utils extends CI_Controller
{
    private $mimetypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'eps' => 'image/x-eps', // 'application/postscript',
        'ai' => 'application/postscript',
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
    ];

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
                $ressave = $file->save($path . $filename . '.' . $ext);
                if ($ressave) {
                    $mimeext = $this->mimetypes[$ext];
                    $mimetype = mime_content_type($path . $filename . '.' . $ext);
                    if ($mimetype==$mimeext) {
                        echo(json_encode(array('success' => true, 'filename' => $path_sh . $filename . '.' . $ext, 'filesize' => $filesize, 'source' => $file->getName())));
                    } else {
                        @unlink($path . $filename . '.' . $ext);
                        // Insert data into log
                        $user=usersession('usr_data');
                        $usrid = ifset($user,'id',0);
                        $this->db->set('file_name', $file->getName());
                        $this->db->set('file_ext', $mimetype);
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                        }
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $this->db->set('site', $_SERVER['HTTP_HOST']);
                        }
                        $this->db->set('user_ip', $this->input->ip_address());
                        if ($usrid) {
                            $this->db->set('user_id', $usrid);
                        }
                        $this->db->insert('ts_uploadfile_logs');
                        echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                    }
                } else {
                    echo (json_encode(array('success' => false,'error'=> 'Error During save File')));
                }
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }

        die('{error: "server-error query params not passed"}');
    }

    function redrawattach() {

        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');

        $arrayext=array('jpg','gif', 'jpeg', 'pdf', 'ai', 'eps', 'png'); // 'doc', 'docx',
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
                $saveres = $file->save($path . $filename . '.' . $ext);
                if ($saveres) {
                    $mimeext = $this->mimetypes[$ext];
                    $mimetype = mime_content_type($path . $filename . '.' . $ext);
                    if ($mimetype==$mimeext) {
                        echo (json_encode(array('success' => true, 'filename' => $path_sh.$filename . '.' . $ext, 'filesize' => $filesize,'source'=>$file->getName())));
                    } else {
                        @unlink($path . $filename . '.' . $ext);
                        // Insert data into log
                        $user=usersession('usr_data');
                        $usrid = ifset($user,'id',0);
                        $this->db->set('file_name', $file->getName());
                        $this->db->set('file_ext', $mimetype);
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                        }
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $this->db->set('site', $_SERVER['HTTP_HOST']);
                        }
                        $this->db->set('user_ip', $this->input->ip_address());
                        if ($usrid) {
                            $this->db->set('user_id', $usrid);
                        }
                        $this->db->insert('ts_uploadfile_logs');
                        echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                    }
                } else {
                    echo (json_encode(array('success' => false, 'error' => 'Error During save File')));
                }
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }
        die('{error: "server-error query params not passed"}');
    }

    function ticketattach() {
        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');

        $arrayext=array('jpg', 'jpeg', 'pdf', 'ai', 'eps'); // 'doc', 'docx'
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
                $saveres = $file->save($path . $filename . '.' . $ext);
                if ($saveres) {
                    $mimeext = $this->mimetypes[$ext];
                    $mimetype = mime_content_type($path . $filename . '.' . $ext);
                    if ($mimetype==$mimeext) {
                        echo (json_encode(array('success' => true, 'filename' => $path.$filename . '.' . $ext, 'filesize' => $filesize)));
                    } else {
                        @unlink($path . $filename . '.' . $ext);
                        // Insert data into log
                        $user=usersession('usr_data');
                        $usrid = ifset($user,'id',0);
                        $this->db->set('file_name', $file->getName());
                        $this->db->set('file_ext', $mimetype);
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                        }
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $this->db->set('site', $_SERVER['HTTP_HOST']);
                        }
                        $this->db->set('user_ip', $this->input->ip_address());
                        if ($usrid) {
                            $this->db->set('user_id', $usrid);
                        }
                        $this->db->insert('ts_uploadfile_logs');
                        echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                    }
                } else {
                    echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                }
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }
        die('{error: "server-error query params not passed"}');
    }

    public function vendorcenterattach() {
        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');
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
            $filenamesrc = $file->getName();
            $filesize = $file->getSize();

            if ($filesize == 0)
                die('{error: "server-error file size is zero"}');

            $pathinfo = pathinfo($file->getName());

            $filename = uniq_link(12);
            $ext = strtolower($pathinfo['extension']);
            $file->save($path . $filename . '.' . $ext);
            echo (json_encode(array('success' => true, 'filename' => $path_sh.$filename . '.' . $ext, 'filesize' => $filesize,'source'=>$filenamesrc)));
            exit();
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }
        die('{error: "server-error query params not passed"}');

    }

    function save_itemprooftemplate() {
        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');

        $arrayext=array('ai','pdf');
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
                $ressave = $file->save($path . $filename . '.' . $ext);
                if ($ressave) {
                    $mimeext = $this->mimetypes[$ext];
                    $mimetype = mime_content_type($path . $filename . '.' . $ext);
                    if ($mimetype==$mimeext) {
                        echo(json_encode(array('success' => true, 'filename' => $path_sh . $filename . '.' . $ext, 'filesize' => $filesize, 'source' => $file->getName())));
                    } else {
                        @unlink($path . $filename . '.' . $ext);
                        // Insert data into log
                        $user=usersession('usr_data');
                        $usrid = ifset($user,'id',0);
                        $this->db->set('file_name', $file->getName());
                        $this->db->set('file_ext', $mimetype);
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                        }
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $this->db->set('site', $_SERVER['HTTP_HOST']);
                        }
                        $this->db->set('user_ip', $this->input->ip_address());
                        if ($usrid) {
                            $this->db->set('user_id', $usrid);
                        }
                        $this->db->insert('ts_uploadfile_logs');
                        echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                    }
                } else {
                    echo (json_encode(array('success' => false,'error'=> 'Error During save File')));
                }
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }

        die('{error: "server-error query params not passed"}');
    }

    function save_itemplatetemplate() {
        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');

        $arrayext=array('ai');
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
                $ressave = $file->save($path . $filename . '.' . $ext);
                if ($ressave) {
                    $mimeext = $this->mimetypes[$ext];
                    $mimetype = mime_content_type($path . $filename . '.' . $ext);
                    if ($mimetype==$mimeext) {
                        echo (json_encode(array('success' => true, 'filename' => $path_sh.$filename . '.' . $ext, 'filesize' => $filesize, 'source'=>$file->getName())));
                    } else {
                        @unlink($path . $filename . '.' . $ext);
                        // Insert data into log
                        $user=usersession('usr_data');
                        $usrid = ifset($user,'id',0);
                        $this->db->set('file_name', $file->getName());
                        $this->db->set('file_ext', $mimetype);
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                        }
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $this->db->set('site', $_SERVER['HTTP_HOST']);
                        }
                        $this->db->set('user_ip', $this->input->ip_address());
                        if ($usrid) {
                            $this->db->set('user_id', $usrid);
                        }
                        $this->db->insert('ts_uploadfile_logs');
                        echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                    }
                } else {
                    echo (json_encode(array('success' => false,'error'=> 'Error During save File')));
                }
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }
        die('{error: "server-error query params not passed"}');
    }

    function save_itemboxtemplate() {
        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');

        $arrayext=array('jpg','jpeg','png','pdf');
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
                $ressave = $file->save($path . $filename . '.' . $ext);
                if ($ressave) {
                    $mimeext = $this->mimetypes[$ext];
                    $mimetype = mime_content_type($path . $filename . '.' . $ext);
                    if ($mimetype==$mimeext) {
                        echo (json_encode(array('success' => true, 'filename' => $path_sh.$filename . '.' . $ext, 'filesize' => $filesize, 'source'=>$file->getName())));
                    } else {
                        @unlink($path . $filename . '.' . $ext);
                        // Insert data into log
                        $user=usersession('usr_data');
                        $usrid = ifset($user,'id',0);
                        $this->db->set('file_name', $file->getName());
                        $this->db->set('file_ext', $mimetype);
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                        }
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $this->db->set('site', $_SERVER['HTTP_HOST']);
                        }
                        $this->db->set('user_ip', $this->input->ip_address());
                        if ($usrid) {
                            $this->db->set('user_id', $usrid);
                        }
                        $this->db->insert('ts_uploadfile_logs');
                        echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                    }
                } else {
                    echo (json_encode(array('success' => false,'error'=> 'Error During save File')));
                }
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }

        die('{error: "server-error query params not passed"}');
    }


    function save_leadattach() {
        $this->load->helper('upload');
        $file = null;
        $path = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');

        $arrayext=array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG','pdf','PDF','ai','AI','psd','PSD','eps','EPS');
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
            $filenamesrc = $file->getName();
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
                $ressave = $file->save($path . $filename . '.' . $ext);
                if ($ressave) {
                    $mimeext = $this->mimetypes[$ext];
                    $mimetype = mime_content_type($path . $filename . '.' . $ext);
                    if ($mimetype==$mimeext) {
                        echo (json_encode(array('success' => true, 'filename' => $path_sh.$filename . '.' . $ext, 'filesize' => $filesize, 'source'=>$filenamesrc)));
                    } else {
                        @unlink($path . $filename . '.' . $ext);
                        // Insert data into log
                        $user=usersession('usr_data');
                        $usrid = ifset($user,'id',0);
                        $this->db->set('file_name', $file->getName());
                        $this->db->set('file_ext', $mimetype);
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                        }
                        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                            $this->db->set('site', $_SERVER['HTTP_HOST']);
                        }
                        $this->db->set('user_ip', $this->input->ip_address());
                        if ($usrid) {
                            $this->db->set('user_id', $usrid);
                        }
                        $this->db->insert('ts_uploadfile_logs');
                        echo(json_encode(array('success' => false, 'error' => 'Error During save File')));
                    }
                } else {
                    echo (json_encode(array('success' => false,'error'=> 'Error During save File')));
                }
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }

        die('{error: "server-error query params not passed"}');
    }

}