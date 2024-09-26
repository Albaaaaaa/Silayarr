<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Controllers\Builtin;
use App\Models\Builtin\ModuleModel;

class Module extends \App\Controllers\BaseController
{
	protected $model;
	private $formValidation;
	
	public function __construct() {
		
		parent::__construct();
		$this->model = new ModuleModel;	
		$this->data['site_title'] = 'Module';
		$this->addJs ($this->config->baseURL . 'public/themes/modern/builtin/js/module.js');
	}
	
	public function index()
	{
		$this->hasPermission('read_all');
		$data = $this->data;
		$this->view('builtin/module-result.php', $data);
	}
	
	public function ajaxDeleteData() {
		
		$delete_permission = $this->hasPermission('delete_all');
		if ($delete_permission) {
			
			$used = $this->model->checkModuleUsedDefaultPage();
			if ($used) {
				$result = ['status' => 'error', 'message' => 'Module digunakan sebagai halaman default user'];
			} else {
				$delete = $this->model->deleteData();
				if ($delete) {
					$result = ['status' => 'ok', 'message' => 'Data user berhasil dihapus'];
				} else {
					$result = ['status' => 'error', 'message' => 'Tidak ada data yang dihapus'];
				}
			}
		} else {
			$result = ['status' => 'error', 'message' => 'Role Anda tidak diperkenankan untuk menghapus data'];
		}
		
		echo json_encode($result);
	}
	
	public function ajaxSwitchModuleStatus() {
		
		// Module Aktif/Nonaktif/Login
		if (!empty($_POST['change_module_attr'])) 
		{
			$update_status = $this->model->updateStatus();
					
			if (!empty($_POST['ajax'])) {
				if ($update_status) {
					echo 'ok';
				} else {
					echo 'error';
				}
			}
		}
	}
	
	public function add() 
	{
		$this->hasPermission('create');
		
		$this->setData();
		$this->data['module_status'] = $this->model->getAllModuleStatus();
		$data = $this->data;
		
		$breadcrumb['Add'] = '';
		$data['title'] = 'Tambah ' . $this->currentModule['judul_module'];
		$data['message'] = [];
		
		if ($this->request->getPost('submit'))
		{
			$save_message = $this->saveData();
			$data['message'] = $save_message;
		}
		
		$this->view('builtin/module-form.php', $data);
	}
	
	public function edit()
	{
		$this->hasPermission('update_all');
		
		$save_message = [];
		if ($this->request->getPost('submit'))
		{
			$save_message = $this->saveData();
		}
		
		$this->setData($_GET['id']);
		$data = $this->data;
		$data['message'] = $save_message;
		
		$data['title'] = 'Edit Data Module';
		
		$module = $this->model->getModule($_GET['id']);
		$data = array_merge($data, $module);

		$data['module_status'] = $this->model->getAllModuleStatus();
		$breadcrumb['Edit'] = '';
		$this->view('builtin/module-form.php', $data);
	}
	
	private function setData($id_module = null) 
	{
		$this->data['id'] = $id_module;
		$this->data['role_permission_module'] = [];
		$this->data['module_permission'] = [];
		if ($id_module){
			$this->data['module'] = $this->model->getModule($id_module);
			$this->data['role_permission_module'] = $this->model->getRolePermissionByModule($id_module);
			$this->data['module_permission'] = $this->model->getModulePermission($id_module);
		}
		$list_role = $this->model->getAllRoles();
		foreach ($list_role as $val) {
			$roles[$val['id_role']] = $val;
		}
		$this->data['roles'] = $roles;
		
	}
	
	private function saveData() 
	{
		$unique = false;
		if ($_POST['nama_module'] != $_POST['nama_module_old']) {
			$unique = true;
		}
		
		$form_errors = $this->validateForm($unique);
	
		if ($form_errors) {
			$data['status'] = 'error';
			$data['form_errors'] = $form_errors;
			$data['message'] = $form_errors;
		} else {
			$data = $this->model->saveData();
		}
		
		return $data;
	}
	
	private function validateForm($check_unique = false) {
	
		$validation =  \Config\Services::validation();
		$unique = '';
		if ($check_unique) {
			$unique = '|is_unique[module.nama_module]';
		}
		$validation->setRule('nama_module', 'Nama Module', 'trim|required' . $unique);
		$validation->setRule('judul_module', 'Judul Module', 'trim|required');
		$validation->setRule('deskripsi', 'Deskripsi Module', 'trim|required');
		$validation->setRule('id_module_status', 'ID Module Status', 'trim|required');
		$validation->withRequest($this->request)->run();
		$form_errors = $validation->getErrors();
		
		return $form_errors;
	}
	
	public function getDataDT() {
		
		$this->hasPermission('read_all');
		
		$num_data = $this->model->countAllData( $this->whereOwn() );
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListData( $this->whereOwn() );
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper('html');		
		$no = $this->request->getPost('start') + 1 ?: 1;
		$login = ['Y' => 'Ya', 'N' => 'Tidak', 'R' => 'Restrict'];
		
		$files = \list_files(ROOTPATH . '/app/Controllers');
		$files = array_map('strtolower', $files);
		
		foreach ($query['data'] as $key => &$val) 
		{
			$checked = $val['id_module_status'] == 1 ? 'checked' : '';
			// Disbled module builtin/module
			$disabled = $this->currentModule == $val['nama_module'] ? ' disabled' : '';
			$file_exists = in_array( str_replace('-', '_', $val['nama_module']) . '.php', $files) ? 'Ada' : 'Tidak Ada';
			
			$val['login'] = $login[$val['login']];
			$val['ignore_file_exists'] = $file_exists;
			$val['ignore_aktif'] = '<div class="form-switch">
								<input name="aktif" type="checkbox" class="form-check-input switch" data-module-id="'.$val['id_module'].'" ' . $checked . $disabled . '>
							</div>';
			$val['ignore_no_urut'] = $no;
			$val['ignore_action'] = '<div class="form-inline btn-action-group">'
										. btn_link(
												['icon' => 'fas fa-edit'
													, 'url' => base_url() . '/builtin/module/edit?id=' . $val['id_module']
													, 'attr' => ['class' => 'btn btn-success btn-edit btn-xs me-1', 'data-id' => $val['id_module']]
													, 'label' => 'Edit'
												])
										. btn_label(
												['icon' => 'fas fa-times'
													, 'attr' => ['class' => 'btn btn-danger btn-delete btn-xs'
																	, 'data-id' => $val['id_module']
																	, 'data-delete-title' => 'Hapus data module: <strong>' . $val['judul_module'] . '</strong>'
																]
													, 'label' => 'Delete'
												]) . 
										
										'</div>';
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
	
}
