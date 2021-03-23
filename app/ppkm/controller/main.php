<?php

namespace app\ppkm\controller;

use app\defaults\controller\application;

class main extends application{
	
	public function __construct(){
		parent::__construct();
		$this->database = new \app\ppkm\model\database();
		$this->modul = $this->link($this->getProject());
		$this->data['api_path'] = $this->link('api/v1');
		$this->data['url_path'] = $this->link($this->getProject().$this->getController());
		$this->data['pilihan_kecamatan'] = ['' => ['text' => 'Semua Kecamatan']] + $this->database->getPilihanKecamatan();
		$this->data['pilihan_keldesa'] = ['' => ['text' => 'Semua Kel/Desa']] + $this->database->getPilihanKeldesa();
		$this->data['pilihan_rw'] = ['' => ['text' => 'Semua R/W']] + $this->database->getPilihanRW();
		$this->data['pilihan_rt'] = ['' => ['text' => 'Semua R/T']] + $this->database->getPilihanRT();
		$this->data['pilihan_status_kondisi'] = ['' => ['text' => 'Semua Status']] + $this->database->getPilihanStatusKondisi();
		$this->data['pilihan_kondisi_saat_ini'] = ['' => ['text' => 'Semua Kondisi']] + $this->database->getPilihanKondisiSaatIni();
	}

	public function index(){
		// $this->data['page_title'] = 'Dashboard';
		// $this->data['breadcrumb'] = '<li>Polres Batang</li><li><a href="'.$this->modul.'">Dashboard</a></li>';
		// $this->showView('index', $this->data, 'appui');
		$this->redirect('laporan');
	}

	public function logout(){
		$this->desSession();
		$this->redirect('');
	}

	public function preloader(){
		$this->subView('preloader', $this->data);
	}

	public function header(){
		$this->subView('header', $this->data);
	}

	public function navbar(){
		$this->subView('navbar', $this->data);
	}

	public function modal(){
		$this->subView('modal', $this->data);
	}
	
	protected function script(){
		header('Content-Type: application/javascript');
		$this->subView('script', $this->data);
	}

}
?>