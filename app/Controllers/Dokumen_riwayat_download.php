<?php

namespace App\Controllers;
use App\Models\DokumenRiwayatDownloadModel;

class Dokumen_riwayat_download extends \App\Controllers\BaseController
{
	public function __construct() {
		
		parent::__construct();
		
		$this->model = new DokumenRiwayatDownloadModel;	
		$this->data['site_title'] = 'Riwayat Download Dokumen';
		$this->addJs ( $this->config->baseURL . 'public/themes/modern/js/dokumen-riwayat-download.js');
	}
	
	public function index()
	{
		$this->hasPermissionPrefix('read');
		$this->view('dokumen-riwayat-download-result.php', $this->data);
	}
	
	public function ajaxDeleteData() {
		$result = $this->model->deleteData();
		// $result = true;
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
		} else {
			$message = ['status' => 'error', 'message' => 'Data gagal dihapus'];
		}
		
		echo json_encode($message);
	}
		
	public function getDataDT() {
		
		$this->hasPermissionPrefix('read');
		
		$num_data = $this->model->countAllData( $this->whereOwn() );
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListData( $this->whereOwn() );
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper('html');
		
		$no = $this->request->getPost('start') + 1 ?: 1;
		foreach ($query['data'] as $key => &$val) 
		{
			$val['ignore_urut'] = $no;
			$val['tgl_download'] = format_date($val['tgl_download']);
			$val['ignore_action'] = btn_label(['icon' => 'fas fa-times' 
											, 'attr' => ['data-id' => $val['id_dokumen_download_log']
														, 'class' => 'btn btn-danger btn-xs btn-delete-log'
														, 'data-delete-title' => 'Hapus data riwayat download ?']
											, 'label' => 'Delete'
										]);
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
	
}
