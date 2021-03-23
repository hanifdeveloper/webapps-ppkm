<?php
/**
 * frameduzPHP v7
 *
 * @Author  	: M. Hanif Afiatna <hanif.softdev@gmail.com>
 * @Since   	: version 7.0.0
 * @Date		: 04 Agustus 2020
 * @package 	: core system
 * @Description : 
 */

class frameduzPHP{

	private $getUrl;
	private $logs;
	
	public function __construct($logs = false){
		session_start();
		$this->logs = $logs;
		spl_autoload_register(array($this, 'loader'));
		if(isset($_GET['fileAccess'])){
			$PathController = 'app\\defaults\\controller\\';
    		$ctrl = $PathController . 'main';
    		$ctrl = new $ctrl();
    		$ctrl->runMethod('fileAccess', $_GET['fileAccess']);
		}else{
		    $this->getUrl = new system\Url;
			$this->runController();
		}
	}
	
	private function loader($file){
		if($this->logs)	echo 'LOG[\'autoload\'] : ' . $file . ' --> Time : ' . round((microtime(true) - TIME_LOAD), 3) . ' sec<br>';
		if(file_exists($file = ROOT . str_ireplace('\\', '/', $file) . '.php')) require_once $file;
	}
	
	private function stripslashesGPC(){
		$value = stripslashes($value);
	}
	
	private function getHeaders(){
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }

    private function runAjax(){
        $_headers = $this->getHeaders();
        return (isset($_headers['XRequestedWith']) && $_headers['XRequestedWith'] === 'XMLHttpRequest');
    }

    private function showError($type){
        if($this->runAjax()){
			$errorMsg = array('status' => 'error', 'message' => array(
				'title' => 'Oops',
				'text' => 'Missing Parameter or method not found',
			));
    		$this->showResponse($errorMsg, 404);
    	}else{
    		$PathController = 'app\\defaults\\controller\\';
    		$ctrl = $PathController . 'main';
    		$ctrl = new $ctrl();
			$ctrl->runMethod($ctrl, 'error', $this->getUrl->getID());
    	}
    }

    private function showMaintenance(){
        if($this->runAjax()){
	        $errorMsg = array('status' => 'maintenance', 'data' => 'url');
    		$this->showResponse($errorMsg, 404);
    	}else{
    		$PathController = 'app\\defaults\\controller\\';
    		$ctrl = $PathController . 'main';
    		$ctrl = new $ctrl();
			$ctrl->runMethod($ctrl, 'maintenance', $this->getUrl->getID());
    	}
    }

    private function showResponse($errorMsg, $code = ''){
    	$_content_type = 'application/json';
    	$_code = 200;
        if(empty($code)) $code = $_code;
        header('HTTP/1.1 '.$code);
        header('Content-Type:'.$_content_type);
        echo json_encode($errorMsg);
    }

	private function runController(){
		$PathController = $this->getUrl->getPathController();
		$PathController = 'app\\' . $PathController . '\\controller\\';
		$controller = $this->getUrl->getController();
		$method = $this->getUrl->getMethod();
		$ctrl = $PathController . $controller;
		$maintenance = (isset($this->getUrl->mainConfig['project'][$this->getUrl->ProjectName]['is_maintenance'])) ? $this->getUrl->mainConfig['project'][$this->getUrl->ProjectName]['is_maintenance'] : 0;
		if(class_exists($ctrl)){
			if((int)method_exists($ctrl, $method) > 0){
				if($maintenance){
        			$this->showMaintenance();
        		}else{
					$ctrl = new $ctrl();
					$ctrl->runMethod($ctrl, $method, $this->getUrl->getID());
        		}
		    }else{
		    	$this->showError('Method '.$method);
		    }
		}
		else{
			$this->showError('Controller '.$controller);
		}
	}
	
}

new frameduzPHP;
?>	