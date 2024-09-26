<?php

namespace App\Controllers;
use App\Models\FilepickerModel;

class Filepicker extends BaseController
{
	private $configFilepicker;
	
	public function __construct() 
	{
		parent::__construct();
		$this->model = new FilepickerModel;
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
	
		$this->addJs($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker.js');
		$this->addJs($this->config->baseURL . 'public/themes/modern/js/jwdfilepicker-defaults.js');
		$this->addJs($this->config->baseURL . 'public/themes/modern/js/filepicker.js');
		$this->addJs($this->config->baseURL . 'public/vendors/dropzone/dropzone.min.js');

		$this->addStyle($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker.css');
		$this->addStyle($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker-loader.css');
		$this->addStyle($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker-modal.css');
		$this->addStyle($this->config->baseURL . 'public/themes/modern/css/filepicker.css');

	}

    public function index()
	{
        $message = [];
		$item_per_page = !empty($_GET['item_per_page']) ? $_GET['item_per_page'] : $this->configFilepicker->itemPerPage;
		
		$load_item = $this->model->getData($item_per_page);
		
		if ( !empty($_GET['ajax']) ) {
			echo json_encode($load_item['result']);
			exit();
		}
				
		$this->data['title'] = 'File Picker Manager';
		$this->data['filter_file'] = ['' => 'All Files', 'image' => 'Image', 'video' => 'Video', 'document' => 'Dokumen', 'archive' => 'Archive'];
		$this->data['filter_tgl'] = @$load_item['result']['filter_tgl'];
		$this->data['total_item'] = $load_item['total_item'];
		$this->data['loaded_item'] = $load_item['loaded_item'];
		$this->data['item_per_page'] = $item_per_page;
        $this->data['result'] = $load_item['result'];
        $this->data['message'] = $message;
		

        if (!$this->data['result']) {
            $this->errorDataNotfound();
			return;
		}

		$this->view('filepicker-result.php', $this->data);
	}
	
	public function ajaxUpdateFile() 
	{
		$update = $this->model->updateMetaFile();
		if ($update)
			echo json_encode( ['status' => 'ok'] );
		else
			echo json_encode( ['status' => 'error'] );
		
		exit;
	}
	
	public function ajaxUploadFile() 
	{
		$result = $this->model->uploadFile();
		
		// Return the response
		echo json_encode($result);
		exit;
	}
	
	public function ajaxDeleteFile() {
	
		$result['status'] = 'error';
		$result['message'] = 'Bad request';
		
		$error = [];
		
		if (empty($_POST['submit']) 
			|| empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
			|| @strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'
		) {
			$error[] = 'Bad request';
		}

		if (empty($_POST['id'])) {
			$error[] = 'ID file tidak valid';
		}
		
		if (!$error) {
			
			$result = $this->model->deleteFile();
		}
		
		
		echo json_encode($result);
		exit();
	}
	
	public function tinymce() 
	{
		echo view('themes/modern/filepicker-tinymce.php', $this->data);
		exit;
	}
	
	public function ajaxFileIcon()
	{
		helper('filepicker');
		$list_file_type = file_type();
		
		$result['status'] = 'error';
		$result['icon']	= '';
		
		$file_icon = 'file';
		
		if (key_exists($_GET['mime'], $list_file_type)) {
			$file_icon =$list_file_type[$_GET['mime']]['extension'];
		} else {
			
			foreach ($list_file_type as $val) {
				if ($val['extension'] == $_GET['ext']) {
					$file_icon = strtolower($_GET['ext']);
				}
			}
		}
		
		$icon_path = $this->configFilepicker->iconPath . $file_icon . '.png';			
			
		if (file_exists($icon_path)) 
		{
			$result['status'] = 'ok';
			$result['icon']	= 'data:image/png;base64,' . base64_encode(file_get_contents($icon_path));
		}
		
		echo file_get_contents($icon_path);
		echo json_encode($result);
		exit;
	}
	
	public function ajaxDeleteAll() {
		
		$result['status'] = 'error';
		$result['message'] = 'Bad request';
		if (!empty($_POST['submit'])) 
		{
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
				&& !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
				&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
			) {

				$result = $this->model->deleteAllFiles();
			}
		}
		echo json_encode($result);
		exit;
	}
}