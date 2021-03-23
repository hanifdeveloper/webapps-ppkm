<?php
namespace app\ppkm\model;

use system\Model;
use comp\FUNC;

class database extends Model{
	
	public function __construct(){
		parent::__construct();
        parent::setConnection('database');
        parent::setDefaultValue(array(
			'id_laporan_covid' => $this->createDateID(),
			'nama_warga' => '',
			'tempat_lahir' => '',
			'tanggal_lahir' => date('Y-m-d'),
			'jenis_kelamin' => 'pria', // pria | wanita
			'tanggal_terkonfirmasi' => date('Y-m-d'),
			'status_kondisi' => '', // gejala | otg
			'kondisi_saat_ini' => '', // isolasi mandiri | dirujuk rs | sembuh
			'kecamatan_id' => '',
			'keldesa_id' => '',
			'rt' => '',
			'rw' => '',
			'datetime' => date('Y-m-d H:i:s'),
		));

		$this->baseUrl = $this->getUrl->baseUrl;
		$this->imageUrl = $this->baseUrl.$this->dir_upload_image;
	}

	public function createRandomID($size = 10){
        return substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", $size)), 0, $size);
	}

	public function createDateID(){
        return date('YmdHis');
	}
	
	public function getPilihanJenkel(){
		return [
			'pria' => ['text' => 'Laki-laki'],
			'wanita' => ['text' => 'Perempuan'],
		];
	}

	public function getPilihanStatusKondisi(){
		return [
			'gejala' => ['text' => 'Gejala'],
			'otg' => ['text' => 'OTG'],
		];
	}

	public function getPilihanKondisiSaatIni(){
		return [
			'isolasi' => ['text' => 'Isolasi Mandiri'],
			'rujuk' => ['text' => 'Dirujuk Rumah Sakit'],
			'sembuh' => ['text' => 'Sembuh'],
		];
	}

	public function getPilihanSizeLimit(){
		return [
			'0' => ['text' => 'Semua Data'],
			'15' => ['text' => '15 Data'],
			'10' => ['text' => '10 Data'],
			'5' => ['text' => '5 Data'],
		];
	}

	public function getPilihanKecamatan($kab = '3325'){ // Kabupaten Batang
		$result = [];
		$data = $this->getData('SELECT * FROM tb_kecamatan WHERE (kabkota_id = ?) ORDER BY id_kecamatan ASC', [$kab]);
		foreach ($data['value'] as $key => $value) {
			// $result[$value['id_kecamatan']] = ['text' => $value['nama_kecamatan']];
			$result[$value['id_kecamatan']] = ['text' => ucwords(strtolower($value['nama_kecamatan']))];
		}

		return $result;
	}

	public function getPilihanKeldesa($kec = '3325%'){
		$result = [];
		$data = $this->getData('SELECT * FROM tb_keldesa  WHERE (kecamatan_id LIKE ?) ORDER BY id_keldesa ASC', [$kec]);
		foreach ($data['value'] as $key => $value) {
			// $result[$value['id_keldesa']] = ['text' => $value['nama_keldesa']];
			$result[$value['id_keldesa']] = ['text' => ucwords(strtolower($value['nama_keldesa']))];
		}

		return $result;
	}

	public function getPilihanRT(){
		$result = [];
		$max = 30;
		for ($i=1; $i <= $max; $i++) { 
			// $id = sprintf('%0'.strlen($max).'s', $i);
			$id = sprintf('%03s', $i);
			$result['rt_'.$id] = ['text' => 'RT '.$id];
		}

		return $result;
	}

	public function getPilihanRW(){
		$result = [];
		$max = 30;
		for ($i=1; $i <= $max; $i++) { 
			// $id = sprintf('%0'.strlen($max).'s', $i);
			$id = sprintf('%03s', $i);
			$result['rw_'.$id] = ['text' => 'RW '.$id];
		}

		return $result;
	}

	public function getFormLaporanCovid($id = ''){
		$form = $this->getDataTabel('tb_laporan_covid', ['id_laporan_covid', $id]);
		$result['form'] = $form;
		$result['form_title'] = empty($id) ? 'Input Laporan' : 'Edit Laporan';
		$result['pilihan_jenis_kelamin'] = ['' => ['text' => '-- Pilih Jenis Kelamin --']] + $this->getPilihanJenkel();
		$result['pilihan_kecamatan'] = ['' => ['text' => '-- Pilih Kecamatan --']] + $this->getPilihanKecamatan();
		$result['pilihan_keldesa'] = ['' => ['text' => '-- Pilih Kel/Desa --']] + $this->getPilihanKeldesa();
		$result['pilihan_rw'] = ['' => ['text' => '-- Pilih R/W --']] + $this->getPilihanRW();
		$result['pilihan_rt'] = ['' => ['text' => '-- Pilih R/T --']] + $this->getPilihanRT();
		$result['pilihan_status_kondisi'] = ['' => ['text' => '-- Pilih Status --']] + $this->getPilihanStatusKondisi();
		$result['pilihan_kondisi_saat_ini'] = ['' => ['text' => '-- Pilih Kondisi --']] + $this->getPilihanKondisiSaatIni();
		return $result;
	}

	public function getListLaporanCovid($params){
		$page = $params['page'];
		$cari = '%'.$params['cari'].'%';
		$status = '%'.$params['status'].'%';
		$kondisi = '%'.$params['kondisi'].'%';
		$kecamatan = '%'.$params['kecamatan'].'%';
		$keldesa = '%'.$params['keldesa'].'%';
		$rt = '%'.$params['rt'].'%';
		$rw = '%'.$params['rw'].'%';
		$where = [
			'(nama_warga LIKE ?)', 
			'(status_kondisi LIKE ?)', 
			'(kondisi_saat_ini LIKE ?)', 
			'(kecamatan_id LIKE ?)', 
			'(keldesa_id LIKE ?)', 
			'(rt LIKE ?)', 
			'(rw LIKE ?)', 
		];

		$query = 'FROM tb_laporan_covid laporan WHERE '.implode('AND ', $where);
		$q_value = 'SELECT * '.$query.' ORDER BY kecamatan_id, keldesa_id, rw, rt';
		$q_count = 'SELECT COUNT(*) AS counts '.$query;
		$keyValue = [$cari, $status, $kondisi, $kecamatan, $keldesa, $rt, $rw];
		$size = $params['size'];
		$cursor = ($page - 1) * $size;
		$pilihan_kecamatan = $this->getPilihanKecamatan();
		$pilihan_keldesa = $this->getPilihanKeldesa();
		$pilihan_rw = $this->getPilihanRW();
		$pilihan_rt = $this->getPilihanRT();
		$pilihan_status_kondisi = $this->getPilihanStatusKondisi();
		$pilihan_kondisi_saat_ini = $this->getPilihanKondisiSaatIni();
		
		if ($size == 0) {
			$dataValue = $this->getData($q_value, $keyValue);
		}
		else {
			$dataValue = $this->getData($q_value.' LIMIT '.$cursor.','.$size, $keyValue);
		}

		$contents = [];
		foreach ($dataValue['value'] as $key => $value) {
			$value['nama_kecamatan'] = isset($pilihan_kecamatan[$value['kecamatan_id']]) ? $pilihan_kecamatan[$value['kecamatan_id']]['text'] : '';
			$value['nama_keldesa'] = isset($pilihan_keldesa[$value['keldesa_id']]) ? $pilihan_keldesa[$value['keldesa_id']]['text'] : '';
			$value['nama_rw'] = isset($pilihan_rw[$value['rw']]) ? $pilihan_rw[$value['rw']]['text'] : '';
			$value['nama_rt'] = isset($pilihan_rt[$value['rt']]) ? $pilihan_rt[$value['rt']]['text'] : '';
			$value['nama_status_kondisi'] = isset($pilihan_status_kondisi[$value['status_kondisi']]) ? $pilihan_status_kondisi[$value['status_kondisi']]['text'] : '';
			$value['nama_kondisi_saat_ini'] = isset($pilihan_kondisi_saat_ini[$value['kondisi_saat_ini']]) ? $pilihan_kondisi_saat_ini[$value['kondisi_saat_ini']]['text'] : '';
			array_push($contents, $value);
		}
		
		$dataCount = $this->getData($q_count, $keyValue);
		$result['number'] = $cursor + 1;
		$result['page'] = $page;
		$result['size'] = ($size > 0) ? $size : $dataCount['value'][0]['counts'];
		$result['total'] = $dataCount['value'][0]['counts'];
		$result['totalpages'] = ceil($result['total'] / $result['size']);
		$result['contents'] = $contents;
		$result['query'] = $dataValue['query'];
		return $result;
	}

}
?>