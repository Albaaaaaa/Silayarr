<?php

namespace App\Controllers;
use App\Models\FileManagerModel;

class File_manager extends \App\Controllers\BaseController
{
	public function __construct() {
		
		parent::__construct();
		
		$this->model = new FileManagerModel;	
		$this->configFilepicker = new \Config\Filepicker();
		
		$ajax = false;
		if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
			$ajax = true;
		}
		
		if (!$ajax) {
			$this->addJs('
				var filepicker_server_url = "' . $this->configFilepicker->serverURL . '";
				var filepicker_icon_url = "' . $this->configFilepicker->iconURL . '";', true
			);
		}
		
		$this->data['site_title'] = 'File Manager';
		// $this->addJs($this->config->baseURL . 'public/themes/modern/js/filepicker.js');
		$this->addJs($this->config->baseURL . 'public/vendors/dropzone/dropzone.min.js');
		
		$this->addJs ( $this->config->baseURL . 'public/themes/modern/js/file-manager.js');
		$this->addStyle($this->config->baseURL . 'public/themes/modern/css/filepicker.css');
	}
	
	public function index()
	{
		$this->hasPermissionPrefix('read');
		$this->view('file-manager-result.php', $this->data);
	}
	
	public function download_all_file() {
		$file = $this->model->downloadAllFile();
		
	}
	
	public function ajaxDeleteData() {
		$result = $this->model->deleteData($_POST['id']);
		// $result = true;
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
		} else {
			$message = ['status' => 'error', 'message' => 'Data gagal dihapus'];
		}
		
		echo json_encode($message);
	}
	
	public function ajaxDeleteAllData() {
		$result = $this->model->deleteAllData();
		// $result = true;
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
		} else {
			$message = ['status' => 'error', 'message' => 'Data gagal dihapus'];
		}
		
		echo json_encode($message);
	}
	
	public function ajaxUploadFile() 
	{
		$result = $this->model->uploadFile();
		
		// Return the response
		echo json_encode($result);
		exit;
	}
	
	public function ajaxSaveData() {
		$result = $this->model->saveData($_POST['id']);
		// $result = true;
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data berhasil disimpan'];
		} else {
			$message = ['status' => 'error', 'message' => 'Data gagal disimpan'];
		}
		
		echo json_encode($message);
	}
	
	public function ajaxGetFormData() {
		$this->data['file'] = $this->model->getFileById($_GET['id']);
		echo view('themes/modern/file-manager-form-edit.php', $this->data);
	}
		
	public function getDataDT() {
		
		$this->hasPermissionPrefix('read');
		
		$num_data = $this->model->countAllData( $this->whereOwn() );
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListData( $this->whereOwn() );
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper(['html', 'format','filepicker']);
		
		$no = $this->request->getPost('start') + 1 ?: 1;
		$config = new \Config\Filepicker();
		$list_file_type = file_type();
		
		foreach ($query['data'] as $key => &$val) 
		{
			$val['ignore_urut'] = $no;
			$val['size'] = '<div class="text-end">' . format_bytes($val['size']) . '</div>';
			$val['tgl_upload'] = '<div class="text-end text-nowrap">' . format_tanggal($val['tgl_upload'], 'dd-mm-yyyy', false) . '</div>';
			$val['ignore_action'] = '<div class="btn-group">' 
									. btn_link(['icon' => 'fas fa-download' 
											, 'url' => base_url() . '/dokumen/download?id=' . $val['id_file_picker']
											, 'attr' => ['data-id' => $val['id_file_picker']
														, 'class' => 'btn btn-primary btn-xs'
													]
											, 'label' => ''
										]) 
									. btn_label(['icon' => 'fas fa-edit' 
											, 'attr' => ['data-id' => $val['id_file_picker']
														, 'class' => 'btn btn-success btn-xs btn-edit-file'
													]
											, 'label' => ''
										]) 
									. btn_label(['icon' => 'fas fa-times' 
											, 'attr' => ['data-id' => $val['id_file_picker']
														, 'class' => 'btn btn-danger btn-xs btn-delete-file'
														, 'data-delete-title' => 'Hapus file: ' . $val['nama_file'] . ' ?']
											, 'label' => ''
										]) . '</div>';
			
			$file_icon = 'file';
			$pathinfo = pathinfo($val['nama_file']);
			$extension = strtolower($pathinfo['extension']) . '.png';
			
			if (key_exists($val['mime_type'], $list_file_type)) {
				$file_icon = $list_file_type[$val['mime_type']]['extension'];
			} else {
				foreach ($list_file_type as $val) {
					if ($val['extension'] == $extension) {
						if (in_array($extension, $list_file_icon)) {
							$file_icon = $extension;
						}
					}
				}
			}
			
			$icon_url = $config->iconURL . $file_icon . '.png';
			
			$val['ignore_download'] = '<a href="' . base_url() . '/dokumen/download?id=' . $val['id_file_picker'] . '" class="btn btn-outline-secondary text-start text-nowrap" style="max-width:auto"><i class="fas fa-download me-2"></i><span> Download</span></a>';
			$val['nama_file'] = '<div class="text-start d-flex" style="min-width:200px"><img style="max-height:24px" src="'. $icon_url . '" class="me-2"/><span>' . $val['nama_file'] . '</span></div>';
			$val['ignore_file_exists'] = $val['file_exists'];
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
	
}
