<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Controllers\Builtin;
use App\Models\Builtin\MenuRoleModel;

class Menu_role extends \App\Controllers\BaseController
{
	protected $model;
	private $formValidation;
	
	public function __construct() {
		
		parent::__construct();
		$this->addJs ($this->config->baseURL . 'public/themes/modern/builtin/js/menu-role.js');
		$this->addStyle($this->config->baseURL . 'public/vendors/wdi/wdi-loader.css');

		$this->model = new MenuRoleModel;	
		
		$roles = $this->model->getAllRole();
		foreach($roles as $row) {
			$this->data['roles'][$row['id_role']] = $row;
		}
	}
	
	public function index()
	{
		$this->hasPermission('read_all');
		$this->view('builtin/menu-role.php', $this->data);
	}
	
	public function delete() {
		if (isset($_POST['id_menu'])) 
		{
			$query = $this->model->deleteData();
			if ($query) {
				$message = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
			} else {
				$message = ['status' => 'error', 'message' => 'Data gagal dihapus'];
			}
			echo json_encode($message);
		}
	}
	
	public function checkbox(){

		$menu_role =$this->model->getMenuRoleById($_GET['id']);
		$checked = [];
		foreach ($menu_role as $row) {
			$checked[] = $row['id_role'];
		}
	
		$this->data['checked'] = $checked;
		echo view('themes/modern/builtin/menu-role-form.php', $this->data);
	}
	
	public function edit()
	{
		$this->hasPermission('update_all');
		
		// Submit data
		if (isset($_POST['id_menu'])) 
		{
			$result = $this->model->saveData();
			
			if ($result['status'] == 'ok') {
				$message = ['status' => 'ok', 'message' => 'Data berhasil disimpan', 'data_parent' => json_encode($result['insert_parent'])];
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
		
		$menu_role = [];
		$menu_role_all = $this->model->getAllMenuRole();
		foreach($menu_role_all as $row) {
			$menu_role[$row['id_menu']][] = $row;
		}
		
		$no = $this->request->getPost('start') + 1 ?: 1;
		foreach ($query['data'] as $key => &$val) 
		{
			
			$list_role = '';
			if (key_exists($val['id_menu'], $menu_role)) {
				$roles = $menu_role[$val['id_menu']];
				// print_r($roles); die;
				foreach ($roles as $role) 
				{
					$list_role .= '<span class="badge badge-secondary bg-secondary badge-role px-3 py-2 me-1 mb-1 pe-4">' . $role['judul_role'] . '<a data-action="remove-role" data-id-menu="'.$val['id_menu'].'" data-role-id="'.$role['id_role'].'" href="javascript:void(0)" class="text-danger"><i class="fas fa-times"></i></a></span>';
				}
			}
			
			$val['ignore_role'] = $list_role;
			$val['ignore_no_urut'] = $no;
			$val['ignore_action'] = btn_label(['label' => 'Edit', 'icon' => 'fas fa-edit', 'attr' => ['data-id-menu' => $val['id_menu'], 'class' => 'btn btn-edit btn-success btn-xs']]);
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
}