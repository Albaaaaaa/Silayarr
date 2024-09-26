<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/


namespace App\Controllers\Builtin;
use App\Models\Builtin\SettingLayoutModel;

class Setting_layout extends \App\Controllers\BaseController
{
	public function __construct() {
		
		parent::__construct();
		// $this->mustLoggedIn();
		
		$this->model = new SettingLayoutModel;	
		$this->data['site_title'] = 'Halaman Setting';
		
		$this->addJs ( $this->config->baseURL . 'public/themes/modern/builtin/js/setting-layout.js?r=' . time());
		$this->addStyle ( $this->config->baseURL . 'public/themes/modern/builtin/css/setting-layout.css');
		
		
		helper(['cookie', 'form']);
	}
	
	public function index() 
	{
		$data = $this->data;
		if (!empty($_POST['submit'])) 
		{
			
			if ($this->hasPermission('update_all')
				|| $this->hasPermission('update_own')
			) {
				$save = $this->model->saveData();
				
				if ($save) {
					$data['status'] = 'ok';
					$data['message'] = 'Data berhasil disimpan';
				} else {
					$data['status'] = 'error';
					$data['message'] = 'Data gagal disimpan';
				}
			} else {
				$data['status'] = 'error';
				$data['message'] = 'Role anda tidak diperbolehkan melakukan perubahan';
			}
			
			if (!empty($_POST['ajax'])) {
				echo json_encode($data); die;
			}
		}
		
		$user_setting = $this->model->getUserSetting();
					
		if ($user_setting) {
			$user_param = json_decode($user_setting['param'], true);
			$data = array_merge($data, $user_param);
		} else {
			$query = $this->model->getDefaultSetting();
			
			foreach($query as $val) {
				$data[$val['param']] = $val['value'];
			}
		}
		
		$data['list_bootswatch_theme'] = scandir(ROOTPATH . 'public/vendors/bootswatch');
	
		$data['title'] = 'Edit ' . $this->currentModule['judul_module'];
		$this->view('builtin/setting-layout-form.php', $data);
	}
	
}