<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Controllers\Builtin;
use App\Models\Builtin\RolePermissionModel;

class Role_permission extends \App\Controllers\BaseController
{
	protected $model;
	private $formValidation;
	
	public function __construct() {
		
		parent::__construct();
		
		$this->model = new RolePermissionModel;	
		$this->data['site_title'] = 'Halaman Role';
		
		$this->addJs(base_url() . '/public/themes/modern/builtin/js/role-permission.js');
		
		helper(['cookie', 'form']);
	}
	
	public function index()
	{
		$this->hasPermission('read_all');
		
		/* if ($this->request->getPost('delete')) 
		{
			$this->hasPermissionPrefix('delete');
			$result = $this->model->deleteAllPermission();
			if ($result) {
				$this->data['msg'] = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
			} else {
				$this->data['msg'] = ['status' => 'warning', 'message' => 'Tidak ada data yang dihapus'];
			}
		} */
		
		/* $this->setData();
		$data = $this->data;
		$data['role'] = $this->model->getAllRole(); */
		
		$this->data['title'] = 'Role Permission';
		$this->view('builtin/role-permission-result.php', $this->data);
	}
	
	//From controller module
	public function ajaxEdit() {
		$result['message'] = ['status' => 'error', 'message' => 'Invalid Input'];
		if (!empty($_POST['id_module']) && !empty($_POST['submit'])) {
			$save = $this->model->saveData();
			if ($save) {
				$result['status'] = 'ok';
				$result['message'] = 'Data berhasil disimpan';
			} else {
				$result['status'] = 'error';
				$result['message'] = $save['message'];
			}
		}
		echo json_encode($result);	
	}
	
	public function ajaxDeletePermission() {
		$delete = $this->model->deletePermission($_POST['id_role'], $_POST['id_permission']);
		if ($delete) {
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil dishapus';
		} else {
			$result['status'] = 'error';
			$result['message'] = 'Data gagal dihapus';
		}
		echo json_encode($result);	
	}
	
	public function ajaxDeleteRolePermissionByModule() {
		$delete = $this->model->deleteRolePermissionByModule($_POST['id_role'], $_POST['id_module']);
		if ($delete) {
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil dishapus';
		} else {
			$result['status'] = 'error';
			$result['message'] = 'Data gagal dihapus';
		}
		echo json_encode($result);	
	}
	//-
	
	public function editNotDataTables()
	{
		$this->hasPermission('update_all');
		
		if (!$this->request->getGet('id')) {
			$this->printError(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
		}
		
		$this->setData();
		$data = $this->data;
		$data['title'] = 'Edit ' . $this->currentModule['judul_module'];
		$breadcrumb['Edit'] = '';
	
		// Submit
		$data['msg'] = [];
		if ($this->request->getPost('submit')) 
		{
			$form_errors = $this->validateForm();
	
			if ($form_errors) {
				$data['msg']['status'] = 'error';
				$data['form_errors'] = $form_errors;
				$data['msg']['message'] = $form_errors;
			} else {
				$save = $this->model->saveData();
				if ($save) {
					$data['msg']['status'] = 'ok';
					$data['msg']['message'] = 'Data berhasil disimpan';
					// $data = array_merge($data, $save);
				} else {
					$data['msg']['status'] = 'error';
					$data['msg']['message'] = $save['message'];
				}
			}
		}
		
		$data['role'] = $this->model->getRoleById($_GET['id']);
		$data['role_permission'] = $this->model->getRolePermissionByIdRole($_GET['id']);
		$this->view('builtin/role-permission-form.php', $data);
	}
	
	public function edit()
	{
		$this->hasPermission('update_all');
		
		if (!$this->request->getGet('id')) {
			$this->printError(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
		}
		$this->data['title'] = 'Edit ' . $this->currentModule['judul_module'];
		$this->data['breadcrumb']['Edit'] = '';
		$this->data['has_all_permission'] = $this->model->hasAllPermission($_GET['id']);
		$this->data['role'] = $this->model->getRoleById($_GET['id']);
		
		$this->view('builtin/role-permission-form.php', $this->data);
	}
	
	public function setData() {
		$this->data['all_modules'] = $this->model->getAllModules();
		$this->data['selected_module'] = $this->model->getAllModulesById(@$_GET['id_module']);
		$this->data['permission_permodule'] = $this->model->getAllPermissionByModule();
		$this->data['role_permission'] = $this->model->getAllRolePermission();
		// $this->data['all_role_permission'] = $this->model->getAllRolePermission();
	}
	
	private function validateForm() {

		$validation =  \Config\Services::validation();
		$validation->setRule('id', 'ID Role', 'trim|required');
		$validation->withRequest($this->request)->run();
		$form_errors = $validation->getErrors();
			
		return $form_errors;
	}
	
	public function getDataDT() {
		
		$this->hasPermission('read_all');
		
		$num_data = $this->model->countAllData();
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListData();
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper('html');
		
		$no = $this->request->getPost('start') + 1 ?: 1;
		foreach ($query['data'] as $key => &$val) 
		{
			$val['ignore_search_urut'] = $no;
			$val['ignore_jml_module'] = $val['jml_module'] ?: 0;
			$val['ignore_jml_permission'] = $val['jml_permission'];
			$val['ignore_search_action'] =	
						'<div class="btn-action-group">' 
							. btn_link(['url' => base_url() . '/builtin/role-permission/edit?id=' . $val['id_role'], 'attr' => ['class' => 'btn btn-success btn-xs me-1'], 'icon' => 'fas fa-edit', 'label' => 'Edit']);
							
			if ($val['jml_permission']) {
				$val['ignore_search_action'] .= btn_label(['attr' => ['class' => 'delete-all-permission btn btn-danger btn-xs', 'data-delete-title' => 'Hapus semua permission pada role ' . $val['nama_role'] . ' ? ', 'data-id-role' => $val['id_role']], 'icon' => 'fas fa-times', 'label' => 'Delete']);
			}
			
			$val['ignore_search_action'] .= '</div>';
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
	
	public function ajaxDeleteAllPermission() 
	{
		$result = $this->model->deleteAllPermission();
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
		} else {
			$message = ['status' => 'error', 'message' => 'Data gagal dihapus'];
		}
		
		echo json_encode($message);
	}
	
	public function ajaxAssignPermission() 
	{
		$result = $this->model->assignPermission();
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data berhasil disimpan', 'hasAllPermission' => $this->model->hasAllPermission($_POST['id_role'])];
		} else {
			$message = ['status' => 'error', 'message' => 'Data gagal disimpan'];
		}
		
		echo json_encode($message);
	}
	
	public function ajaxAssignAllPermission() 
	{
		$result = $this->model->assignAllPermission();
		if ($result) {
			$message = ['status' => 'ok', 'message' => 'Data berhasil disimpan'];
		} else {
			$message = ['status' => 'error', 'message' => 'Data gagal disimpan'];
		}
		
		echo json_encode($message);
	}
	
	public function getDataDTPermission() {
		
		$this->hasPermission('read_all');
		
		$num_data = $this->model->countAllDataPermission();
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListDataPermission( $_GET['id'] );
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper('html');
		
		$no = $this->request->getPost('start') + 1 ?: 1;
		foreach ($query['data'] as $key => &$val) 
		{
			$val['ignore_search_urut'] = $no;
			$checked = $val['id_role'] ? 'checked' : '';
			$val['id_role'] = '<div class="form-check-input-xs form-switch text-center"><input name="aktif" type="checkbox" class="form-check-input assign" data-id-module-permission="' . $val['id_module_permission'] . '" value="1" ' . $checked . '></div>';
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
}
