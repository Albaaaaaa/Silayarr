<?php

namespace App\Controllers;
use App\Models\DokumenKategoriModel;

class Dokumen_kategori extends \App\Controllers\BaseController
{
	protected $model;
	
	public function __construct() {
		
		parent::__construct();
		// $this->mustLoggedIn();
		
		$this->model = new DokumenKategoriModel;	
		$this->data['site_title'] = 'Kategori Dokumen';
		
		$this->addStyle ( $this->config->baseURL . 'public/vendors/jquery-nestable/jquery.nestable.min.css?r='.time());
		$this->addStyle ( $this->config->baseURL . 'public/vendors/wdi/wdi-modal.css?r=' . time());
		$this->addStyle ( $this->config->baseURL . 'public/vendors/wdi/wdi-fapicker.css?r=' . time());
		$this->addStyle ( $this->config->baseURL . 'public/vendors/wdi/wdi-loader.css?r=' . time());
		
		$this->addJs ( $this->config->baseURL . 'public/vendors/wdi/wdi-fapicker.js?r=' . time());
		$this->addJs ($this->config->baseURL . 'public/themes/modern/js/dokumen-kategori.js');
		$this->addJs ( $this->config->baseURL . 'public/vendors/jquery-nestable/jquery.nestable.js?r=' . time());
		// $js[] = $config['base_url'] . 'public/vendors/jquery-nestable/jquery.nestable-edit.js?r=' . time();
		$this->addJs ( $this->config->baseURL . 'public/vendors/js-yaml/js-yaml.min.js?r=' . time());
		$this->addJs ( $this->config->baseURL . 'public/vendors/jquery-nestable/jquery.wdi-menueditor.js?r=' . time());

		helper(['cookie', 'form']);
	}
	
	public function index()
	{
		$this->hasPermission('read_all');
		
		$data = $this->data;
		
		$menu_updated = [];
		$message = [];
		if (!empty($_POST['submit'])) 
		{
			$menu_updated = $this->model->updateData();
			
			if ($menu_updated) {
				$message['status'] = 'ok';
				$message['content'] = 'Menu berhasil diupdate';
			} else {
				$message['status'] = 'warning';
				$message['content'] = 'Tidak ada menu yang diupdate';
			}
		}
		// End Submit

		// helper('builtin/admin_menu');
		$result = $this->model->getKategori();
		$list_kategori = kategori_list($result);
// echo '<pre>'; print_r($result); die;
		$data['list_kategori'] = $this->buildKategoriList($list_kategori);
		
		$data['list_kategori_json'] = $this->buildKategoriListJson($list_kategori);
		// echo '<pre>'; print_r($data['list_kategori_json']); die;
		$data['message'] = $message;
		$this->view('dokumen-kategori.php', $data);
	}
		
	public function ajaxEditKategori()
	{
		if (isset($_POST['nama_kategori'])) 
		{
			$error = $this->checkForm();
			if ($error) {
				$result['status'] = 'error';
				$result['message'] = '<ul class="list-error"><li>' . join('</li><li>', $error) . '</li></ul>';
			} else {
				
				
				if (empty($_POST['id'])) {
					$query = $this->model->saveData();
					$message = 'Kategori berhasil ditambahkan';
					$result['id_menu'] = $query;
				} else {
					$query = $this->model->saveData($_POST['id']);
					$message = 'Kategori berhasil diupdate';
				}
				
				$query = true;
				if ($query) {
					$result['status'] = 'ok';
					$result['message'] = $message;
					// $data['msg']['message'] = 'Kategori berhasil diupdate';
				} else {
					$result['status'] = 'error';
					$result['message'] = 'Data gagal disimpan';
					$result['error_query'] = true;
				}	
			}
			echo json_encode($result);
			exit();
		}
	}
	
	public function ajaxDeleteKategori() {
		$result = $this->model->deleteKategori();
		
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data kategori berhasil dihapus'];
			echo json_encode($message);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Data kategori gagal dihapus']);
		}
	}
	
	public function ajaxUpdateUrut() {
		
		$updated = $this->model->updateKategoriUrut();
		if ($updated) {
			$message['status'] = 'ok';
			$message['message'] = 'Kategori berhasil diupdate';
		} else {
			$message['status'] = 'warning';
			$message['message'] = 'Tidak ada kategori yang diupdate';
		}
		
		echo json_encode($message);
	}
	
	public function ajaxGetKategoriForm() 
	{
		$this->data['kategori'] =[];
		if (!empty($_GET['id'])) {
			$this->data['kategori'] = $this->model->getKategoriById($_GET['id']);
		}
		echo view('themes/modern/dokumen-kategori-form.php', $this->data);
	}
	
	public function ajaxKategoriDetail() {
		
		$result = $this->model->getKategoriDetail();
		echo json_encode($result);
	}
	
	private function checkForm() 
	{
		$error = [];
		if (trim($_POST['nama_kategori']) == '') {
			$error[] = 'Nama kategori harus diisi';
		}
		
		if (trim($_POST['deskripsi']) == '') {
			$error[] = 'Deskripsi harus diisi';
		}
		
		return $error;
	}
	
	function buildKategoriListJson($arr, $id_parent = '')
	{
		// $option = "\n" . '<select id="tree1" style="width: 550px">'."\r\n";
		$option = '';

		foreach ($arr as $key => $val) 
		{
			// Check new
			$new = @$val['new'] == 1 ? '<span class="menu-baru">NEW</span>' : '';
			$icon = '';
			if ($val['icon']) {
				$icon = '<i class="'.$val['icon'].'"></i>';
				
			}
			
			
			
			if (key_exists('children', $val))
			{ 	
				$option .= '<option value="' . $val['id_dokumen_kategori'] . '" data-parent="' . $id_parent . '" disabled="disabled">' . $icon . $val['nama_kategori'] . '</option>' . "\r\n";
				$option .= $this->buildKategoriListJson($val['children'], $val['id_dokumen_kategori']);
			} else {
				$option .= '<option value="' . $val['id_dokumen_kategori'] . '" data-parent="' . $id_parent . '">' . $icon . $val['nama_kategori'] . '</option>'  . "\r\n";
			}
			
			
		}
		// $option .= "</select>\n";
		return $option;
	}
	
	function buildKategoriList($arr)
	{
		$kategori = "\n" . '<ol class="dd-list">'."\r\n";

		foreach ($arr as $key => $val) 
		{
			// Check new
			$new = @$val['new'] == 1 ? '<span class="menu-baru">NEW</span>' : '';
			$icon = '';
			if ($val['icon']) {
				$icon = '<i class="'.$val['icon'].'"></i>';
				
			}
			
			$kategori .= '<li class="dd-item" data-id="'.$val['id_dokumen_kategori'].'"><div class="dd-handle">'.$icon.'<span class="menu-title">'.$val['nama_kategori'].'</span></div>';
			
			if (key_exists('children', $val))
			{ 	
				$kategori .= $this->buildKategoriList($val['children'], ' class="submenu"');
			}
			$kategori .= "</li>\n";
		}
		$kategori .= "</ol>\n";
		return $kategori;
	}
}