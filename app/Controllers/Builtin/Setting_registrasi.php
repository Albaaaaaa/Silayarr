<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Controllers\Builtin;
use App\Models\Builtin\SettingRegistrasiModel;

class Setting_registrasi extends \App\Controllers\BaseController
{
	public function __construct() {
		
		parent::__construct();

		$this->model = new SettingRegistrasiModel;	
		$this->data['site_title'] = 'Halaman Setting Registrasi';
		
		$this->addJs ( $this->config->baseURL . 'public/themes/modern/js/setting-registrasi.js');
	}
	
	public function index() 
	{
		$data = $this->data;
		if (!empty($_POST['submit'])) 
		{
			$form_errors = $this->validateForm();
			
			if ($form_errors) {
				$data['message'] = ['status' => 'error', 'message' => $form_errors];
			} else {
				
				if (!$this->hasPermission('update_all'))
				{
					$data['message'] = ['status' => 'error', 'message' => 'Role anda tidak diperbolehkan melakukan perubahan'];
				} else {
					$result = $this->model->saveData();
					$data['message'] = ['status' => $result['status'], 'message' => $result['message']];
				}
			}
		}
		
		$query = $this->model->getSettingRegistrasi();
		foreach($query as $val) {
			$data['setting'][$val['param']] = $val['value'];
		}
		
		$data['title'] = 'Edit ' . $this->currentModule['judul_module'];
		$data['role'] = $this->model->getRole();
		$data['list_module'] = $this->model->getListModules();
		
		$this->view('builtin/setting-registrasi-form.php', $data);
	}

	private function validateForm() 
	{
		$validation =  \Config\Services::validation();		
		$validation->setRule('enable', 'Enable', 'trim|required');
		$validation->setRule('metode_aktivasi', 'Metode Aktivasi', 'trim|required');
		$validation->setRule('id_role', 'Role', 'trim|required');
		
		$validation->withRequest($this->request)
					->run();
		$form_errors =  $validation->getErrors();
		
		if ($this->request->getPost('option_default_page') == 'url') {
			if (empty($this->request->getPost('default_page_url'))) {
				$form_errors['url'] = 'Url tidak boleh kosong';
			}
		}

		return $form_errors;
	}	
}