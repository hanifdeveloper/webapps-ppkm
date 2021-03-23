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
 
namespace system;
use ReflectionClass, RecursiveIteratorIterator, RecursiveDirectoryIterator;

class Controller{

	const IS_PUBLIC = \ReflectionMethod::IS_PUBLIC;
	const IS_PROTECTED = \ReflectionMethod::IS_PROTECTED;

	protected $errorCode = 404;
    protected $errorMsg = array('status' => 'error', 'message' => array(
		'title' => 'Oops',
		'text' => 'Missing Parameter or method not found',
    ));
    protected $succesMsg = array('status' => 'success', 'data' => array());
	
	public function __construct(){
		$this->getUrl = new Url();
		$this->files = new Files();
		$this->project = $this->getUrl->ProjectName;
		$this->session = $this->getUrl->mainConfig['project'][$this->project]['session'];
		$this->cookie = $this->getUrl->mainConfig['project'][$this->project]['cookie'];
		$setting = $this->getUrl->mainConfig['setting'];
		if(!empty($setting)){
			foreach($setting as $set => $value) $this->{$set} = $value;
		}
	}

	protected function isAssoc($arr){
        if(!is_array($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
	
	public function runMethod($ctrl, $method, $idKey){
		if(is_array($idKey)){
			if($this->isAssoc($idKey) || empty($idKey)){
				return $ctrl->{$method}($idKey);
			}
			else{
				call_user_func_array([$ctrl, $method], $idKey);
			}
		}
		else{
			return $ctrl->{$method}($idKey);
		}
	}
	
	/**
	 * Memanggil view dari controller project lain (menjalankan controller suatu project)
	 */
	public function getView($project, $controller, $method, $idKey){
		$PathController = 'app\\' . $project . '\\controller\\';
		$Controller = $controller;
		$ctrl = $PathController . $Controller;
		$this->setSession('getView', array('PathController' => $project, 'Controller' => $Controller));
		if(class_exists($ctrl)){
			if(method_exists($ctrl, $method)){
				$ctrl = new $ctrl();
				$ctrl->runMethod($ctrl, $method, $idKey);
			}	
			else{
				echo 'method not found';
			}
		}
		else{
			echo 'controller not found';
		}
	}

	/**
	 * Memanggil view dari project lain (requred file view)
	 */
	protected function projectView($project, $controller, $fileView = '', $data = array()){
		extract($data, EXTR_SKIP);
		$viewPath = APP . $project . '/view/' . $controller . '/' . $fileView . '.' . $controller . '.php';
		require_once $viewPath;
		unset($data);
	}
	
	protected function showView($fileView = '', $data = array(), $template = '', $validation = false){
		$Caller = $this->getCaller();
		$PathController = (!empty($Caller['PathController'])) ? $Caller['PathController'] : $this->getUrl->getPathController();
		$Controller = (!empty($Caller['Controller'])) ? $Caller['Controller'] : $this->getUrl->getController();
		$css = $this->getUrl->mainConfig['template'][$template]['css'];
		$js = $this->getUrl->mainConfig['template'][$template]['js'];
		$template = (empty($template)) ? $this->getUrl->mainConfig['template'][$this->getUrl->defaultTemplate]['basePath'] : $this->getUrl->mainConfig['template'][$template]['basePath'];
		$basePath = $this->getUrl->baseUrl . 'template/' . $template;

		$cssPath = '';
        foreach ($css as $key => $value) {
            $cssPath .= '<link rel="stylesheet" href="'.$value.'">'."\n";
        };

        $jsPath = '';
        foreach ($js as $key => $value) {
            $jsPath .= '<script src="'.$value.'"></script>'."\n";
        };

		extract($data, EXTR_SKIP);
		$viewPath = APP . $PathController . '/view/' . $Controller . '/' . $fileView . '.' . $Controller . '.php';
		require_once TEMPLATE . $template . 'template.php';
		unset($data, $template);
	}
	
	protected function subView($fileView = '', $data = array()){
		$caller = $this->getSession('getView');
		$PathController = !empty($caller['PathController']) ? $caller['PathController'] : $this->getUrl->getPathController();
		$Controller = !empty($caller['Controller']) ? $caller['Controller'] : $this->getUrl->getController();
		extract($data, EXTR_SKIP);
		$viewPath = APP . $PathController . '/view/' . $Controller . '/' . $fileView . '.' . $Controller . '.php';
		require_once $viewPath;
		$this->delSession('getView');
		unset($data);
	}

	protected function getCaller(){
		$caller = debug_backtrace();
		$caller = $caller[2];
		$result['PathController'] = '';
		$result['Controller'] = '';
		if(isset($caller['object'])){
			$_caller = get_class($caller['object']);
			$_caller = explode('\\', $_caller);
			$result['PathController'] = strtolower($_caller[1]);
			$result['Controller'] = strtolower($_caller[3]);
		}
		return $result;
	}
	
	protected function getProject(){
		return ($this->getUrl->ProjectName == $this->getUrl->defaultProject) ? '' : $this->getUrl->ProjectName . '/';
	}
	
	protected function getController(){
		return $this->getUrl->getController();
	}

	protected function getMethod(){
		return $this->getUrl->getMethod();
	}

	protected function getID(){
		return $this->getUrl->getID();
	}
	
	protected function link($location = ''){
		return $this->getUrl->baseUrl . $location;
	}
	
	protected function activeLink($location = ''){
		return $this->getUrl->activeUrl . $location;
	}
	
	protected function redirect($location, $status = 302){
		$location = (empty($location)) ? $this->link() : $location;
		if(substr($location, 0, 4) != 'http')
			$location = $this->link() . $location;
		header('Location: ' . $location, true, $status);
		exit;
	}
	
	protected function setSession($name, $data){
		$_SESSION[$this->session][$name] = $data;
	}
	
	protected function getSession($name){
		return isset($_SESSION[$this->session][$name]) ? $_SESSION[$this->session][$name] : '';
	}
	
	protected function delSession($name){
		if(isset($_SESSION[$this->session][$name])) unset($_SESSION[$this->session][$name]);
	}
	
	protected function desSession(){
		if(isset($_SESSION[$this->session])) unset($_SESSION[$this->session]);
	}
	
	protected function post($validation = false, $key = false, $filterType = false){
		if($validation === true){
			if(isset($_SESSION[$this->session])){
				if(!$key) {
					return (phpversion() > '7.3') ? filter_var_array($_POST, FILTER_SANITIZE_ADD_SLASHES) : filter_var_array($_POST, FILTER_SANITIZE_MAGIC_QUOTES);    
				}
				if($filterType) return filter_input(INPUT_POST, $key, $filterType);
				else return $_POST[$key];
			}
			else{
				return false;
			}
		}
		else{
			if(!$key) {
				return (phpversion() > '7.3') ? filter_var_array($_POST, FILTER_SANITIZE_ADD_SLASHES) : filter_var_array($_POST, FILTER_SANITIZE_MAGIC_QUOTES);    
			}
			if($filterType) return filter_input(INPUT_POST, $key, $filterType);
			else return $_POST[$key];
		}
	}

	/**
	 * General Function
	 */
	
	protected function showResponse($errorMsg, $code = ''){
    	$_content_type = 'application/json';
    	$_code = 200;
        if(empty($code)) $code = $_code;
        header('HTTP/1.1 '.$code);
        header('Content-Type:'.$_content_type);
        echo json_encode($errorMsg);
    }

    protected function debugResponse($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
	}
	
	public function paramsFilter($params, $input) {
		foreach ($params as $key => $value) { if(isset($input[$key]) && ($input[$key] != "")) $params[$key] = $input[$key]; }
		return $params;
	}

	protected function checkAccessMethod($pathController, $method, $level){
		$ctrl = new ReflectionClass($pathController);
		$access = false;
		foreach ($ctrl->getMethods($level) as $key => $value) {
			// echo $value->class."\n";
			// echo $value->name."\n";
			// echo $ctrl."\n";
			if($value->class == $pathController && $value->name == $method){
				$access = true;
				// echo 'asas';
				break;
			}
		}
		return $access;
    }

    public function checkAuthToken($token = ''){
        $_header = $this->getHeaders();
        $_tokens = isset($_header['Token']) ? $_header['Token'] : '';
        if($_tokens != $token){
            $this->errorMsg['message']['text'] = 'Invalid Token';
            $this->showResponse($this->errorMsg, $this->errorCode);
            die;
        }
    }

    public function createToken(){
        // Generated New Tokens
        return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);
	}

	protected function runAjax(){
        $_header = $this->getHeaders();
        return (isset($_header['XRequestedWith']) && $_header['XRequestedWith'] === 'XMLHttpRequest');
	}
	
	protected function getHeaders(){
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }

    protected function getRequestHeader(){
        $headers = apache_request_headers();
        if(isset($headers['Cookie'])) unset($headers['Cookie']); // Bug Fix Send Header with Curl
        $header = array();
        foreach ($headers as $key => $value) {array_push($header, $key.':'.$value);}
        return $header;
    }

    protected function getRequestFields(){
        $input = $this->post(false);
        $fields = array();
        foreach ($input as $key => $value) {array_push($fields, $key.'='.$value);}
        return implode('&', $fields);
    }
	
	protected function inputValidate($params = []){
		$input = file_get_contents('php://input');
		$input = json_decode($input, true);
		if(!$input) {
			$this->errorMsg['message']['text'] = 'Requires Parameter';
			if(!empty($params)){
				$this->errorMsg['message']['params'] = $params;
			}
			$this->showResponse($this->errorMsg, $this->errorCode);
			die;
		}

		return $input;
	}

	protected function postValidate($session = false, $params = []){
		$input = $this->post($session);
		if(!$input) {
			$this->errorMsg['message']['text'] = 'Requires Parameter';
			if(!empty($params)){
				$this->errorMsg['message']['params'] = $params;
			}
			$this->showResponse($this->errorMsg, $this->errorCode);
			die;
		}

		return $input;
	}

	protected function getValidate($id){
		if($id == '') {
			$this->errorMsg['message']['text'] = 'Requires Parameter';
			$this->showResponse($this->errorMsg, $this->errorCode);
			die;
		}

		return $id;
	}
	
}
?>