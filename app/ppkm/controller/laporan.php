<?php

namespace app\ppkm\controller;

use app\ppkm\controller\main;

class laporan extends main{
	
	public function __construct(){
		parent::__construct();
		$this->modul = $this->link($this->getProject().$this->getController());
	}

	public function index(){
		$this->data['page_title'] = 'Identitas Warga Terkonfirmasi Covid 19';
		$this->data['breadcrumb'] = '<li>PPKM</li><li><a href="'.$this->modul.'/'.__FUNCTION__.'">Laporan Covid 19</a></li>';
		$this->showView('index', $this->data, 'appui');
	}

}
?>