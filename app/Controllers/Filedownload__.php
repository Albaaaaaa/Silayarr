<?php

namespace App\Controllers;
use App\Models\FiledownloadModel;

class Filedownload extends BaseController
{
	private $configFilepicker;
	
	public function __construct() 
	{
		parent::__construct();
		$this->model = new FiledownloadModel;
		$this->configFilepicker = new \Config\Filepicker();
		
		$this->addJs('
			var filepicker_server_url = "' . $this->configFilepicker->serverURL . '";
			var filepicker_icon_url = "' . $this->configFilepicker->iconURL . '";', true
		);

		$this->addJs($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker.js');
		$this->addJs($this->config->baseURL . 'public/themes/modern/js/jwdfilepicker-defaults.js');
		$this->addJs($this->config->baseURL . 'public/vendors/dropzone/dropzone.min.js');

		$this->addStyle($this->config->baseURL . 'public/vendors/dropzone/dropzone.min.css');
		$this->addStyle($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker.css');
		$this->addStyle($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker-loader.css');
		$this->addStyle($this->config->baseURL . 'public/vendors/jwdfilepicker/jwdfilepicker-modal.css');
		$this->addJs($this->config->baseURL . 'public/themes/modern/js/filedownload.js');
	}

    public function index()
	{
       $message = [];
        if (!empty($_POST['delete'])) {
			
			$this->hasPermissionPrefix('delete');
            $message = $this->model->deleteFile($_POST['id']);
        }
		
		$where = $this->whereOwn();
		$result = $this->model->getFiles($where);
		
        $this->data['result'] = $result;
        $this->data['message'] = $message;

        if (!$this->data['result']) {
            $this->errorDataNotfound();
			return;
		}
		
        $this->view('filedownload-result.php', $this->data);
	}
	
	public function add() {
		
		$this->data['title'] = 'Add File Download';
        $result = ['message' => '', 'id' => '', 'id_file_picker' => ''];
        if (!empty($_POST['submit'])) {
            $result = $this->model->saveData();
			if ($result['message']['status'] == 'ok') {
				$data['title'] = 'Edit File Download';
			}
        }

        $this->data['message'] = $result['message'];
        $this->data['id'] = $result['id'];
        $this->data['id_file_picker'] = $result['id_file_picker'];

        $this->view('filedownload-form.php', $this->data);
	}
	
	public function edit() {
		
		$this->hasPermissionPrefix('update');
		
        if (empty($_GET['id'])) {
            $this->errorDataNotfound();
			return;
		}

        $result['message'] = [];
        if (!empty($_POST['submit'])) {
            $result = $this->model->saveData();
        }

		$file_download = $this->model->getFiles($this->whereOwn() . ' AND id_file_download = ' . $_GET['id']);
		
        if (!$file_download) {
            $this->errorDataNotfound();
			return;
		}
	
        $this->data['title'] = 'Edit Data File Download';
        $this->data['file_download'] = $file_download[0];
        $this->data['message'] = $result['message'];
        $this->data['id'] = $_GET['id'];
        $this->data['id_file_picker'] = $file_download[0]['id_file_picker'];
        $this->view('filedownload-form.php', $this->data);
	}
	
	public function download() {
	
		$file = $this->model->getFiles($this->whereOwn() . ' AND id_file_download = ' . $_GET['id']);
		
		if (!$file) {
			$this->errorDataNotfound();
			return;
		}
		
		$file = $file[0];
		
		$filepicker = new \Config\Filepicker();
		$file_path = $filepicker->uploadPath . $file['nama_file'];

		if (!file_exists($file_path)) {
			exit_error( 'File ' . $file['nama_file'] . ' tidak ditemukan, mohon menghubungi admin, terima kasih' );
		}
		
		$this->model->saveDownloadLog($file);
	
		header('Content-Description: File Transfer');
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary"); 
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Disposition: attachment; filename=\"".$file['nama_file']."");
		header("Content-Length: " . filesize($file_path));
		ob_end_clean();
		ob_end_flush();
		readfile($file_path);
		exit;
	}
}