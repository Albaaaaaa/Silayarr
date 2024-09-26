<?php

namespace App\Controllers;
use App\Models\DokumenModel;

class Dokumen extends \App\Controllers\BaseController
{
	protected $model;

	public function __construct() {
		
		parent::__construct();
		
		$this->model = new DokumenModel();	
		$this->data['site_title'] = 'Dokumen';
		
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
		
		$this->addJs($this->config->baseURL . 'public/vendors/flatpickr/dist/flatpickr.js');
		$this->addStyle($this->config->baseURL . 'public/vendors/flatpickr/dist/flatpickr.min.css');
		$this->addStyle($this->config->baseURL . 'public/vendors/flatpickr/dist/themes/material_blue.css');
		
		$this->addJs ( $this->config->baseURL . 'public/vendors/jquery.select2/js/select2.full.min.js' );
		$this->addStyle ( $this->config->baseURL . 'public/vendors/jquery.select2/css/select2.min.css' );
		$this->addStyle ( $this->config->baseURL . 'public/vendors/jquery.select2/bootstrap-5-theme/select2-bootstrap-5-theme.min.css' );
		
		$this->addJs ( $this->config->baseURL . 'public/themes/modern/js/select2-kategori.js');
		$this->addJs ( $this->config->baseURL . 'public/themes/modern/js/dokumen.js');
	}
	
	public function index()
	{
		$this->hasPermissionPrefix('read');
		
		$result = $this->model->getKategoriUsed();
		$list_kategori = [];
		$list_kategori[''] = 'Semua Kategori';
		foreach ($result as $val) {
			$list_kategori[$val['id_dokumen_kategori']] = $val['nama_kategori'];
		}
		$this->data['list_kategori'] = $list_kategori;
		
		$this->view('dokumen-result.php', $this->data);
	}
	
	public function ajaxDeleteData() {
		$result = $this->model->deleteData($_POST['id']);
		// $result = true;
		if ($result) {
			$result = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
		} else {
			$result = ['status' => 'error', 'message' => 'Data gagal dihapus'];
		}
		echo json_encode($result);
	}
	
	public function ajaxDeleteAllData() {
		$result = $this->model->deleteAllData();
		// $result = true;
		if ($result) {
			$result = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
		} else {
			$result = ['status' => 'error', 'message' => 'Data gagal dihapus'];
		}
		echo json_encode($result);
	}
	
	public function add() 
	{
		$data = $this->data;
		$data['title'] = 'Tambah Dokumen';
		$data['breadcrumb']['Add'] = '';
		
		$result = [];
		$this->data['dokumen']= [];
		if (isset($_POST['submit'])) 
		{
			$error = $this->validateForm();
			
			if ($error) {
				$result['status'] = 'error';
				$result['message'] = $error;
			} else {
				$result = $this->model->saveData();
			}
		}
	
		if (!empty($_POST['id_file_picker'])) {
			$files = [];
			foreach ($_POST['id_file_picker'] as $index => $val) {
				$files[] = ['id_file_picker' => $val, 'nama_file' => $_POST['nama_file'][$index]];				
			}
			$this->data['dokumen']['files'] = $files;
		}

		$this->setData();
		$this->data['title'] = 'Tambah Dokumen';
        $this->data['message'] = $result;
        $this->view('dokumen-form.php', $this->data);
	}
	
	public function edit()
	{
		$this->hasPermissionPrefix('update');
		
        if (empty($_GET['id'])) {
            $this->errorDataNotfound();
			return;
		}

        $result = [];
        if (!empty($_POST['submit'])) {
			$error = $this->validateForm();
			
			if ($error) {
				$result['status'] = 'error';
				$result['message'] = $error;
			} else {
				$result = $this->model->saveData();
			}
        }
		
		$this->setData($_GET['id']);
		if (!$this->data['dokumen']) {
			$this->errorDataNotfound();
			return;
		}
	
        $this->data['title'] = 'Edit Dokumen';
        $this->data['message'] = $result;
        $this->data['id'] = $_GET['id'];
        $this->view('dokumen-form.php', $this->data);
	}
	
	private function validateForm() {
		
		$error = [];
		$validation =  \Config\Services::validation();
		
		$validation->setRule('judul_dokumen', 'Judul Dokumen', 'trim|required');
		$validation->setRule('deskripsi_dokumen', 'Deskripsi Dokumen', 'trim|required');
		$validation->setRule('tgl_upload', 'Tanggal Upload', 'trim|required');
		$validation->withRequest($this->request)->run();
		$error = $validation->getErrors();
		
		if (empty($_POST['id_file_picker'])) {
			$error[] = 'File dokumen belum dipilih';
		}

		return $error;
	}
	
	private function setData($id = null) {
		if ($id) {
			$dokumen = $this->model->getDokumenById($this->whereOwn() . ' AND id_dokumen = ' . $id);
			$this->data['dokumen'] = $dokumen;
		}
		$result = $this->model->getKategori();
		$list_kategori = kategori_list($result);
		$this->data['list_kategori'] = $this->buildKategoriList($list_kategori);
	}
	
	public function download() {
	
		$file = $this->model->getFiles(' WHERE id_file_picker = ' . $_GET['id']);
		
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
	
	private function buildKategoriList($arr, $id_parent = '', &$result = [])
	{
		
		foreach ($arr as $key => $val) 
		{
			$result[$val['id_dokumen_kategori']] = ['attr' => ['data-parent' => $id_parent, 'data-icon' => $val['icon'], 'data-new' => $val['new']]
													, 'text' => $val['nama_kategori']
												];
			if (key_exists('children', $val))
			{
				$result[$val['id_dokumen_kategori']]['attr']['disabled'] = 'disabled';
				$this->buildKategoriList($val['children'], $val['id_dokumen_kategori'], $result);
			}
		}
		return $result;
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
		$config = new \Config\Filepicker();
		helper('filepicker');
		$list_file_type = file_type();
		
		foreach ($query['data'] as $key => &$val) 
		{
			$val['ignore_checkbox'] = '<input type="checkbox" name="dokumen_ids[]" value="' . $val['id_dokumen'] . '">';
			$val['tgl_upload'] = '<div class="text-nowrap">' . format_tanggal($val['tgl_upload'], 'dd mmmm yyyy', false) . '</div>';
			$val['deskripsi_dokumen'] = '<div class="text-wrap" style="max-width:500px">' . $val['deskripsi_dokumen'] . '</div>';
			
			$list_file = [];
			// echo '<pre>'; print_r($list_file_icon); die;
			if ($val['nama_file']){
				$list_file_icon = scandir(ROOTPATH . 'public/images/filepicker_images/');
				$exp_id_file = explode (',', $val['id_file_picker']);
				$exp_judul_file = explode (',', $val['judul_file']);
				$exp_mime = explode (',', $val['mime_type']);
				$exp_nama_file = explode (',', $val['nama_file']);
				foreach ($exp_nama_file as $index => $nama_file) 
				{
					$file_icon = 'file';
					$pathinfo = pathinfo($nama_file);
					$extension = strtolower($pathinfo['extension']) . '.png';
					$mime = $exp_mime[$index];
					if (key_exists($mime, $list_file_type)) {
						$file_icon = $list_file_type[$mime]['extension'];
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
					$list_file[] = '<a href="' . base_url() . '/dokumen/download?id=' . $exp_id_file[$index] . '" class="btn btn-outline-secondary text-start d-flex mb-2" style="min-width:200px"><img style="max-height:24px" src="'. $icon_url . '" class="me-2"/><span>' . $exp_judul_file[$index] . '</span></a>';
				}
			}
			
			if (!$list_file) {
				$list_file[] = '<div class="text-nowrap">Tidak ada</div>';
			}
			
			$val['title'] = '<ul class=list-circle"><li style="max-width: 230px;">' . join('</li><li style="max-width: 230px;">', $list_file) . '</li></ul>';			
			$val['ignore_urut'] = $no;
			
			$val['ignore_action'] = '<div class="d-flex">';
			if ($this->hasPermission('update_all')) {
				
				$val['ignore_action'] .= btn_link([
										'url' => base_url() . '/dokumen/edit?id=' . $val['id_dokumen'],
										'icon' => 'fas fa-edit',
										'attr' => ['class' => 'btn btn-success btn-xs btn-edit me-1', 'data-id' => $val['id_dokumen']],
										'label'=> 'Edit'
									]);
			}
			
			if ($this->hasPermission('delete_all')) {
				$val['ignore_action'] .= btn_label([
										'icon' => 'fas fa-times',
										'attr' => ['class' => 'btn btn-danger btn-xs btn-delete', 'data-id' => $val['id_dokumen'], 'data-delete-title' => 'Hapus data dokumen dengan judul: <strong>' . $val['judul_dokumen'] . '</strong>?'],
										'label'=> 'Delete'
									]);
			}
			$val['ignore_action'] .= '</div>';
			$val['preview'] = '<button class="btn btn-sm btn-info preview-btn" data-id="' . $val['id_dokumen'] . '">Preview</button>';
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
	
	public function preview($id)
	{
		$dokumen = $this->model->getDokumenById(['WHERE dokumen.id_dokumen' => $id]);
		
		if (!$dokumen) {
			return $this->response->setJSON(['error' => 'Dokumen tidak ditemukan']);
		}
		
		$file = $this->model->getFiles(' WHERE id_file_picker = ' . $dokumen['id_file_picker']);
		
		if (!$file) {
			return $this->response->setJSON(['error' => 'File tidak ditemukan']);
		}
		
		$file = $file[0];
		
		$filepicker = new \Config\Filepicker();
		$file_path = $filepicker->uploadPath . $file['nama_file'];
		
		if (!file_exists($file_path)) {
			return $this->response->setJSON(['error' => 'File ' . $file['nama_file'] . ' tidak ditemukan, mohon menghubungi admin, terima kasih']);
		}
		
		$this->model->saveDownloadLog($file);
		
		$this->response->setHeader('Content-Type', $file['mime_type']);
		$this->response->setBody(file_get_contents($file_path));
		return $this->response;
	}

	public function print()
	{
		$this->hasPermissionPrefix('read');

		$ids = $this->request->getGet('ids');
		if (empty($ids)) {
			return redirect()->to(base_url('dokumen'))->with('error', 'Tidak ada dokumen yang dipilih untuk dicetak.');
		}

		$id_array = explode(',', $ids);
		$dokumen = $this->model->getDokumenByIds($id_array);

		if (empty($dokumen)) {
			return redirect()->to(base_url('dokumen'))->with('error', 'Dokumen tidak ditemukan.');
		}

		$data = [
			'dokumen' => $dokumen,
			'title' => 'Cetak Dokumen'
		];

		return view('themes/modern/builtin/dokumen-print', $data);
	}
}
