<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Controllers\Builtin;
use App\Models\Builtin\UserRoleModel;

class User_role extends \App\Controllers\BaseController
{
	protected $model;
	private $formValidation;
	
	public function __construct() {
		
		parent::__construct();
		$this->addJs ($this->config->baseURL . 'public/themes/modern/builtin/js/user-role.js');
		$this->addStyle ($this->config->baseURL . 'public/vendors/wdi/wdi-loader.css');
		
		$this->model = new UserRoleModel;	
		$this->data['site_title'] = 'User Role';
		
		$roles= $this->model->getAllRole();
		foreach($roles as $row) {
			$this->data['roles'][$row['id_role']] = $row;
		}
	}
	
	public function index()
	{
		$this->hasPermission('read_all');
		
		$data = $this->data;
		if (!empty($_POST['delete'])) {
			$result = $this->model->deleteData();
			
			if ($result) {
				$data['msg'] = ['status' => 'ok', 'message' => 'Data user-role berhasil dihapus'];
			} else {
				$data['msg'] = ['status' => 'error', 'message' => 'Data user-role gagal dihapus'];
			}
		}
		
		// Get user
		$data['users'] = $this->model->getAllUser();
		$this->view('builtin/user-role.php', $data);
	}
	
	public function checkbox() {
		
		$user_role = $this->model->getUserRoleByID($_GET['id']);
		$this->data['user_role'] = $user_role;
		
		echo view('themes/modern/builtin/user-role-form.php', $this->data);
	}
	
	public function delete() {
		if (isset($_POST['id_user'])) 
		{
			$result = $this->model->deleteData();
			if ($result) {
				$message = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
			} else {
				$message = ['status' => 'error', 'message' => 'Data gagal dihapus'];
			}
			echo json_encode($message);
		}
	}
	
	public function edit() 
	{
		if (isset($_POST['id_user'])) 
		{	
			$result = $this->model->saveData();
			
			if ($result) {
				$message = ['status' => 'ok', 'message' => 'Data berhasil disimpan'];
			} else {
				$message = ['status' => 'error', 'message' => 'Data gagal disimpan'];
			}
		
			echo json_encode($message);
		}
	}
	
	public function getDataDT() {
		
		$this->hasPermission('read_all');
		
		$num_data = $this->model->countAllData( $this->whereOwn() );
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListData( $this->whereOwn() );
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper('html');
	
		$user_role = [];
		$user_role_all = $this->model->getUserRole();
		foreach($user_role_all as $row) {
			$user_role[$row['id_user']][] = $row;
		}
		
		$no = $this->request->getPost('start') + 1 ?: 1;
		foreach ($query['data'] as $key => &$val) 
		{
			
			$list_role = '';
			if (key_exists($val['id_user'], $user_role)) {
				$roles = $user_role[$val['id_user']];
				foreach ($roles as $role) 
				{
					$list_role .= '<span class="badge badge-secondary badge-role bg-secondary px-3 py-2 me-1 mb-1 pe-4">' . $role['judul_role'] . '<a data-action="remove-role" data-id-user="'.$val['id_user'].'" data-role-id="'.$role['id_role'].'" href="javascript:void(0)" class="text-danger"><i class="fas fa-times"></i></a></span>';
				}
			}
			
			$val['ignore_role'] = $list_role;
			$val['ignore_no_urut'] = $no;
			$val['ignore_action'] = btn_label(['label' => 'Edit', 'icon' => 'fas fa-edit', 'attr' => ['data-id-user' => $val['id_user'], 'class' => 'btn btn-edit btn-success btn-xs']]);
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
	
}