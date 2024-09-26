<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Controllers\Builtin;
use App\Models\Builtin\ModuleRoleModel;

class Module_role extends \App\Controllers\BaseController
{
	protected $model;
	private $formValidation;
	
	public function __construct() {
		
		parent::__construct();
		$this->addJs ($this->config->baseURL . 'public/themes/modern/builtin/js/module-role.js');
		$this->addStyle($this->config->baseURL . 'public/vendors/wdi/wdi-loader.css');
		
		$this->model = new ModuleRoleModel;	
		$this->data['site_title'] = 'Module Role';
	}
	
	public function index()
	{
		$this->hasPermission('read_all');
		$this->view('builtin/module-role.php', $this->data);
	}
	
	public function delete() {
		if (isset($_POST['id_module'])) 
		{
			$query = $this->model->deleteData();
			if ($query) {
				$message = ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
			} else {
				$message = ['status' => 'error', 'message' => 'Data gagal dihapus'];
			}
			echo json_encode($message);
			exit;
		}
	}
	
	public function edit()
	{
		$this->hasPermission('update_all');
		$breadcrumb['Edit'] = '';
		
		// Submit data
		if (isset($_POST['submit'])) 
		{
			
			$query = $this->model->saveData();
			
			if ($query) {
				$message = ['status' => 'ok', 'content' => 'Data berhasil disimpan'];
			} else {
				$message = ['status' => 'error', 'content' => 'Data gagal disimpan'];
			}
			$data['message'] = $message;
		}
		
		$data = $this->data;
		$data['title'] = 'Edit ' . $this->currentModule['judul_module'];
		$data['module'] = $this->model->getModule($_GET['id']);
		$data['role'] = $this->model->getAllRole();
		$data['role_detail'] = $this->model->getRoleDetail();
		$data['module_role'] = $this->model->getModuleRoleById($_GET['id']);
	
		$this->view('builtin/module-role-form.php', $data);
	}
	
	public function detail() {
		$breadcrumb['Detail'] = '';
		
		$data = $this->data;
		$data['title'] = 'Edit ' . $this->currentModule['judul_module'];

		$data['module'] = $this->model->getModule($_GET['id']);
		$data['role'] = $this->model->getAllRole();
		$data['role_detail'] = $this->model->getRoleDetail();
		$data['module_role'] = $this->model->getModuleRoleById($_GET['id']);
		
		$this->view('builtin/module-role-detail.php', $data);
	}
	
	public function getDataDT() {
		
		$this->hasPermission('read_all');
		
		$num_data = $this->model->countAllData( $this->whereOwn() );
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListData( $this->whereOwn() );
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper('html');
		
		$module_role = [];
		$module_role_all = $this->model->getAllModuleRole();
		foreach($module_role_all as $row) {
			$module_role[$row['id_module']][] = $row;
		}
	
		$no = $this->request->getPost('start') + 1 ?: 1;
		foreach ($query['data'] as $key => &$val) 
		{
			
			$list_role = '';
			if (key_exists($val['id_module'], $module_role)) {
				$roles = $module_role[$val['id_module']];
				foreach ($roles as $role) 
				{
					$list_role .= '<span class="badge badge-secondary badge-role px-3 py-2 me-1 mb-1 pe-4">' . $role['judul_role'] . '<a data-action="remove-role" data-id-module="'.$val['id_module'].'" data-id-role="'.$role['id_role'].'" href="javascript:void(0)" class="text-danger"><i class="fas fa-times"></i></a></span>';
				}
			}
			
			$val['ignore_role'] = $list_role;
			$val['ignore_no_urut'] = $no;
			$val['ignore_action'] = '<div class="btn-action-group">'.
									btn_link(['url' => $this->config->baseURL . 'builtin/module-role/edit?id=' . $val['id_module'], 'label' => 'Edit', 'icon' => 'fas fa-edit', 
												'attr' => ['class' => 'btn btn-success btn-xs me-2', 'target' => '_blank']]
											). 
									btn_link(['url' => $this->config->baseURL . 'builtin/module-role/detail?id=' . $val['id_module'], 'label' => 'Detail', 'icon' => 'fas fa-eye', 
												'attr' => ['class' => 'btn btn-primary btn-xs', 'target' => '_blank']]
											).
									'</div>';
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result);
	}
}