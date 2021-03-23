<?php
namespace app\defaults\controller;

use system\Controller;
use system\Config;
use comp\FUNC;

class application extends Controller{
	
	public function __construct(){
        parent::__construct();
	}

	protected function index(){
        $this->showResponse($this->errorMsg, $this->errorCode);
	}

    public function createRandomID($size = 10){
        return substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", $size)), 0, $size);
	}
    
    public function createCookie($session){
        $cookie = $this->cookie;
        setcookie($cookie, $session, time() + COOKIE_EXP, '/');
    }
    
    public function removeCookie(){
        $cookie = $this->cookie;
        unset($_COOKIE[$cookie]);
        setcookie($cookie, '', time() - COOKIE_EXP, '/');
    }

    protected function uploadImage($file, $prefix, $resize = true){
        ini_set('memory_limit', '-1');
        $result['status'] = 'success';
        $result['errorMsg'] = 'file tidak dilampirkan';
        $result['UploadFile'] = '';
        if(!empty($file['name'])){
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $opt_upload = array(
                'fileName' => $this->createRandomID().'.'.$ext,
                'fileType' => $this->file_type_image,
                'maxSize' => $this->max_size,
                'folder' => $this->dir_upload_image,
                'session' => false,
            );
            $result = $this->files->upload($file, $opt_upload);
            if($result['status'] == 'success'){
                if($resize){
                    $newfile = $prefix.'_'.$result['UploadFile'];
                    $src = $this->dir_upload_image.'/'.$result['UploadFile'];
                    $dst = $this->dir_upload_image.'/'.$newfile;
                    $result['UploadFile'] = $newfile;
                    FUNC::resizeImage(800, $src, $ext, $dst);
                }
            }
        }
        return $result;
    }

    // protected function uploadImage($file, $prefix, $action = ''){
    //     ini_set('memory_limit', '-1');
    //     $result['status'] = 'success';
    //     $result['errorMsg'] = 'file tidak dilampirkan';
    //     $result['UploadFile'] = '';
    //     if(isset($_FILES[$file]) && !empty($_FILES[$file]['name'])){
    //         $file = $_FILES[$file];
    //         $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    //         $opt_upload = array(
    //             'fileName' => date('dmYHis').'.'.$ext,
    //             'fileType' => $this->file_type_image,
    //             'maxSize' => $this->max_size,
    //             'folder' => $this->dir_upload_image,
    //             'session' => false,
    //         );
    //         $result = $this->files->upload($file, $opt_upload);
    //         if($result['status'] == 'success'){
    //             $src = $this->dir_upload_image.'/'.$result['UploadFile'];
    //             $dst = $this->dir_upload_image.'/'.$prefix.'_'.$result['UploadFile'];
    //             $result['UploadFile'] = $prefix.'_'.$result['UploadFile'];
    //             if($action == '' | $action == 'resize'){
    //                 FUNC::resizeImage(800, $src, $ext, $dst);
    //             }else if($action == 'crop'){
    //                 FUNC::cropImage(400, $src, $ext, $dst);
    //             }
    //         }
    //     }
    //     return $result;
    // }

    protected function uploadLampiran($file, $prefix){
        ini_set('memory_limit', '-1');
        $result['status'] = 'success';
        $result['errorMsg'] = 'file tidak dilampirkan';
        $result['UploadFile'] = '';
        if(isset($_FILES[$file]) && !empty($_FILES[$file]['name'])){
            $file = $_FILES[$file];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $opt_upload = array(
                'fileName' => $prefix.'_'.date('dmYHis').'.'.$ext,
                'fileType' => $this->file_type_attachment,
                'maxSize' => $this->max_size,
                'folder' => $this->dir_upload_attachment,
                'session' => false,
            );
            $result = $this->files->upload($file, $opt_upload);
        }
        return $result;
    }

    protected function FileExists($file, $action = ''){
        $exist = false;
        if(file_exists($file)) $exist = true;
        if($exist == true && $action == 'delete') unlink($file);
        return $exist;
    } 

    protected function sendMessagePost($data){
        /**
         * Params:
         * $data['url']
         * $data['header']
         * $data['fields']
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $data['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $data['header']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['fields']);
        $result = curl_exec($ch);           
        if($result === FALSE){
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
	
}
?>
