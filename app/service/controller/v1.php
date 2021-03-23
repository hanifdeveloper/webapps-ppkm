<?php

namespace app\service\controller;

use app\defaults\controller\application;
use comp\FUNC;

class v1 extends application{ 
	
	public function __construct(){
		parent::__construct();
		$this->data['url_path'] = $this->link($this->getProject().$this->getController());
	}

	protected function index(){
		if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') {
			$this->showResponse($this->errorMsg, 404);
		}
	}

	protected function script(){
		header('Content-Type: application/javascript');
		$this->subView('script', $this->data);
	}

	protected function ppkm($method = '', $action = ''){
		$this->checkAuthToken($this->token);
		$database = new \app\ppkm\model\database();
		switch ($method) {
			case 'laporan':
				$input = $this->postValidate();
				switch ($action) {
					case 'form':
						$data = $database->getFormLaporanCovid($input['id']);
						$this->succesMsg['data'] = $data;
						$this->showResponse($this->succesMsg);
						break;

					case 'list':
						$input = $database->paramsFilter(['page' => 1, 'size' => 10, 'cari' => '', 'status' => '', 'kondisi' => '', 'kecamatan' => '', 'keldesa' => '', 'rt' => '', 'rw' => ''], $input);
						$data = $database->getListLaporanCovid($input);
						$this->succesMsg['data'] = $data;
						$this->showResponse($this->succesMsg);
						break;
						
					case 'save':
						$data = $database->getFormLaporanCovid($input['id_laporan_covid']);
						$data = $database->paramsFilter($data['form'], $input);
						$result = $database->save_update('tb_laporan_covid', $data);
						$this->errorMsg = ($result['success']) ? 
											array('status' => 'success', 'message' => array(
												'title' => 'Sukses',
												'text' => 'Data telah disimpan',
											)) : 
											array('status' => 'error', 'message' => array(
												'title' => 'Maaf',
												'text' => $result['message'],
											)); 

						$this->showResponse($this->errorMsg);
						break;

					case 'delete':
						$result = $database->delete('tb_laporan_covid', ['id_laporan_covid' => $input['id']]);
						$this->errorMsg = ($result['success']) ? 
											array('status' => 'success', 'message' => array(
												'title' => 'Sukses',
												'text' => 'Data telah dihapus',
											)) : 
											array('status' => 'error', 'message' => array(
												'title' => 'Maaf',
												'text' => $result['message'],
											)); 

						$this->showResponse($this->errorMsg);
						break;
					
					default:
						$this->showResponse($this->errorMsg, $this->errorCode);
						break;
				}
				break;
			
			default:
				$this->showResponse($this->errorMsg, $this->errorCode);
				break;
		}
	}

}
?>
