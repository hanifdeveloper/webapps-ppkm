<?php
/**
 * frameduzPHP v6
 *
 * @Author  	: M. Hanif Afiatna <hanif.softdev@gmail.com>
 * @Since   	: version 6.0.0
 * @Date		: 21 Mei 2019
 * @package 	: core system
 * @Description : 
 */

namespace system;

class Url{
	
	public function __construct(){
		$selfArr = explode('/', rtrim($_SERVER['PHP_SELF'], '/'));
		$selfKey = array_search(INDEX_FILE, $selfArr);
		$this->baseUrl = $this->isHttps() ? 'https://':'http://';
		$this->baseUrl .= $_SERVER['HTTP_HOST'] . implode('/', array_slice($selfArr, 0, $selfKey)) . '/';
		$this->activeUrl = $this->isHttps() ? 'https://':'http://';
		$this->activeUrl .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$this->mainConfig = Config::Load('main');
		$this->defaultProject =  $this->mainConfig['defaultController']['project'];
		$this->defaultTemplate = $this->mainConfig['defaultTemplate']['template'];
		$defaultPathController = $this->mainConfig['project'][$this->defaultProject]['path'];
		$defaultController = $this->mainConfig['project'][$this->defaultProject]['controller'];
		$defaultMethod = $this->mainConfig['project'][$this->defaultProject]['method'];
		
		$this->ProjectName =  $this->defaultProject;
		$this->PathController = $defaultPathController;
		$this->Controller = $defaultController;
		$this->Method = $defaultMethod;
		$this->ID = '';

		// if(!empty($_GET['p1'])){
		// 	$this->ProjectName = $_GET['p1'];
		// 	if(array_key_exists($this->ProjectName, $this->mainConfig['project'])){ // Jika sebuah project
		// 		$this->PathController = $this->mainConfig['project'][$this->ProjectName]['path'];
		// 		$this->Controller = $this->mainConfig['project'][$this->ProjectName]['controller'];
		// 		$this->Method = $this->mainConfig['project'][$this->ProjectName]['method'];
		// 		$this->ID = null;
		// 		if(!empty($_GET['p2'])) $this->Controller = $_GET['p2'];
		// 		if(!empty($_GET['p3'])) $this->Method = $_GET['p3'];
		// 		if(!empty($_GET['p4'])) $this->ID = $_GET['p4'];
		// 	}
		// 	else{
		// 		$this->ProjectName =  $this->defaultProject;
		// 		$this->PathController = $defaultPathController;
		// 		$this->Controller = $_GET['p1'];
		// 		if(!empty($_GET['p2'])) $this->Method = $_GET['p2'];
		// 		if(!empty($_GET['p3'])) $this->ID = $_GET['p3'];
		// 	}
		// }

		if(isset($_GET['url'])) {
			$url = rtrim($_GET['url'], '/');
			$url = filter_var($url, FILTER_SANITIZE_URL);
			$url = explode('/', $url);

			if(isset($url[0])){
				// Check Project Name
				if(array_key_exists($url[0], $this->mainConfig['project'])){ // Jika sebuah project
					$this->ProjectName = $url[0];
					unset($url[0]);
					$this->PathController = $this->mainConfig['project'][$this->ProjectName]['path'];
					$this->Controller = isset($url[1]) ? $url[1] : $this->mainConfig['project'][$this->ProjectName]['controller'];
					unset($url[1]);
					$this->Method = isset($url[2]) ? $url[2] : $this->mainConfig['project'][$this->ProjectName]['method'];
					unset($url[2]);
					$this->ID = array_values($url);
					$this->ID = !empty($this->ID) ? $this->ID : '';
				}
				else{ // Maka sebuah controller
					$this->ProjectName =  $this->defaultProject;
					$this->PathController = $defaultPathController;
					$this->Controller = isset($url[0]) ? $url[0] : $this->Controller;
					unset($url[0]);
					$this->Method = isset($url[1]) ? $url[1] : $this->Method;
					unset($url[1]);
					$this->ID = array_values($url);
					$this->ID = !empty($this->ID) ? $this->ID : '';
				}
			}
		}

		// $a=array("Name"=>"Peter","Age"=>"41","Country"=>"USA");
		// print_r(array_values($a));
		// print_r($a);die;

		// echo "ProjectName: $this->ProjectName<br>";
		// echo "PathController: $this->PathController<br>";
		// echo "Controller: $this->Controller<br>";
		// echo "Method: $this->Method<br>";
		// var_dump($this->ID);
		// die;
	}

	public function isHttps(){
		if(isset($_SERVER['HTTPS'])){
			if(strtolower($_SERVER['HTTPS']) == 'on') return true;
			if($_SERVER['HTTPS'] == '1') return true;
		}
		elseif(isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443')){
			return true;
        }
        elseif(isset($_SERVER['HTTP_X_FORWARDED_PORT']) && ($_SERVER['HTTP_X_FORWARDED_PORT'] == '443')){
			return true;
		}
		return false;
	}
	
	public function getPathController(){
		return strtolower($this->PathController);
	}
	
	public function setPathController($project){
		$this->PathController = $this->mainConfig['project'][$project]['path'];
	}
	
	public function getController(){
		return strtolower($this->Controller);
	}
	
	public function setController($Controller){
		$this->Controller = $Controller;
	}
	
	public function getMethod(){
		return $this->Method;
	}
	
	public function setMethod($Method){
		$this->Method = $Method;
	}
	
	public function getID(){
		return $this->ID;
	}
	
	public function setID($ID){
		$this->ID = $ID;
	}
	
}
?>
