<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Controllers\Builtin;
use App\Models\Builtin\UserModel;
use \Config\App;

class User extends \App\Controllers\BaseController
{
	protected $model;
	protected $moduleURL;
	
	public function __construct() {
		
		parent::__construct();
		
		$this->model = new UserModel;	
		$this->formValidation =  \Config\Services::validation();
		$this->data['site_title'] = 'Halaman Profil';
		
		$this->addJs($this->config->baseURL . 'public/vendors/bootstrap-datepicker/js/bootstrap-datepicker.min.js');
		$this->addJs($this->config->baseURL . 'public/themes/modern/js/date-picker.js');
		$this->addJs($this->config->baseURL . 'public/themes/modern/builtin/js/user.js');
		$this->addJs($this->config->baseURL . 'public/themes/modern/builtin/js/image-upload.js');
		$this->addStyle($this->config->baseURL . 'public/vendors/bootstrap-datepicker/css/bootstrap-datepicker.min.css');
		
		$this->addJs ( $this->config->baseURL . 'public/vendors/jquery.select2/js/select2.full.min.js' );
		$this->addStyle ( $this->config->baseURL . 'public/vendors/jquery.select2/css/select2.min.css' );
		$this->addStyle ( $this->config->baseURL . 'public/vendors/jquery.select2/bootstrap-5-theme/select2-bootstrap-5-theme.min.css' );
		
		helper(['cookie', 'form']);
		// echo '<pre>'; print_r($this->userPermission); die;
	}
	
	public function index()
	{
		$this->hasPermissionPrefix('read');

		$data['title'] = 'Data User';		
		$this->view('builtin/user/result.php', array_merge($data, $this->data));
	}
	
	public function getDataDT() {
		
		$this->hasPermission('read_all');
		
		$num_users = $this->model->countAllUsers($this->whereOwn('id_user'));
		// $user = $this->model->getListUsers($this->actionUser, $this->whereOwn('id_user'));
		$user = $this->model->getListUsers($this->whereOwn('id_user'));
		
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_users;
		$result['recordsFiltered'] = $user['total_filtered'];		
		
		helper('html');
		$avatar_path = ROOTPATH . 'public/images/user/';
		
		foreach ($user['data'] as $key => &$val) {
			
			if ($val['avatar']) {
				if (file_exists($avatar_path . $val['avatar'])) {
					$avatar = $val['avatar'];
				} else {
					$avatar = 'default.png';
				}
				
			} else {
				$avatar = 'default.png';
			}
			
			$role = '';
			if ($val['judul_role']) {
				$split = explode(',', $val['judul_role']);
				foreach ($split as $judul_role) {
					$role .= '<span class="badge badge-role bg-secondary me-2 py-2 px-3">' . $judul_role . '</span>';
				}	
			}
			
			$val['judul_role'] = '<div style="white-space:break-spaces">' . $role . '</div>';
			
			$val['verified'] =  $val['verified'] == 1 ? 'Ya' : 'Tidak' ;
			$val['ignore_avatar'] = '<img src="'. $this->config->baseURL . 'public/images/user/' . $avatar . '">';
								
			$btn['edit'] = ['url' => $this->moduleURL . '/edit?id='. $val['id_user']];
			if ($this->hasPermission('delete_own') || $this->hasPermission('delete_all')) {
				$btn['delete'] = ['url' => $this->moduleURL
												, 'id' =>  $val['id_user']
												, 'delete-title' => 'Hapus data user: <strong>'.$val['nama'].'</strong> ?'
											]
							;
			}
			$val['ignore_btn_action'] = btn_action($btn);
		}
					
		$result['data'] = $user['data'];
		echo json_encode($result); exit();
	}
	
	public function add() 
	{
		$this->hasPermission('create');
		
		$breadcrumb['Add'] = '';
		
		$this->setData();
		$data = $this->data;
		$data['title'] = 'Tambah User';
		$setting = $this->model->getSettingRegister();
		$data['setting_registrasi'] = [];
		foreach ($setting as $val) {
			$data['setting_registrasi'][$val['param']] = $val['value'];
		}
		
		$error = false;
		if ($this->request->getPost('submit'))
		{
			$data['message'] = $this->saveData();
			if ($data['message']['status'] == 'ok') {
				$result = $this->model->getUserById($data['message']['id_user'], true);
			
				if (!$result) {
					$this->errorDataNotFound();
					return;
				} else {
					$data = array_merge($data, $result);
				}
			}
		}

		$this->view('builtin/user/form.php', $data);
	}
	
	public function uploadExcel() 
	{
		$this->hasPermission('create');
		
		$breadcrumb['Upload Excel'] = '';
		$this->data['title'] = 'Upload Data User';
				
		$error = false;
		if ($this->request->getPost('submit'))
		{
			$form_errors = $this->validateFormUpload();
			if ($form_errors) {
				$this->data['message']['status'] = 'error';
				$this->data['message']['content'] = $form_errors;
			} else {
				$this->data['message'] = $this->model->uploadExcel();	
			}
		}

		$this->view('builtin/user/form-uploadexcel.php', $this->data);
	}
	
	function validateFormUpload() {

		$form_errors = [];

		if ($_FILES['file_excel']['name']) 
		{
			$file_type = $_FILES['file_excel']['type'];
			$allowed = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
				
			if (!in_array($file_type, $allowed)) {
				$form_errors['file_excel'] = 'Tipe file harus ' . join(', ', $allowed);
			}
		} else {
			$form_errors['file_excel'] = 'File belum dipilih';
		}
		
		return $form_errors;
	}
	
	public function edit()
	{
		
		if (!key_exists('update_all', $this->userPermission)) {
			if ($_GET['id'] != $this->session->get('user')['id_user']) {
				$this->printError('Anda tidak berhak mengubah data user ini');
				return;
			}
		}
		
		$this->setData();
		$data = $this->data;
		$data['title'] = 'Edit User';
		$breadcrumb['Edit'] = '';
			
		// Submit
		$data['message'] = [];
		if ($this->request->getPost('submit')) 
		{
			$save_message = $this->saveData();
			$data = array_merge( $data, $save_message);
		}
		
		$result = $this->model->getUserById($this->request->getGet('id'), true);
		
		if (!$result) {
			$this->errorDataNotFound();
			return;
		} else {
			$data = array_merge($data, ['user_edit' => $result]);
		}
		
		$this->view('builtin/user/form.php', $data);
	}
	
	public function setData() {
		$this->data['roles'] = $this->model->getRoles();
		$this->data['user_permission'] = $this->userPermission;
		$this->data['list_module'] = $this->model->getListModules();
	}
	
	private function saveData() 
	{		
		$form_errors = $this->validateForm();
		$error = false;		
		
		if ($form_errors) {
			$data['message'] = ['status' => 'error', 'message' => $form_errors];
			$error = true;
		}
		
		if (!$error) {				
			$data = $this->model->saveData($this->userPermission);
		}
		
		return $data;
	}
	
	public function ajaxDeleteUser() 
	{
		$delete_permission = $this->hasPermissionPrefix('delete');
		if ($delete_permission) {
			$delete = $this->model->deleteUser();
			if ($delete) {
				$result = ['status' => 'ok', 'message' => 'Data user berhasil dihapus'];
			} else {
				$result = ['status' => 'warning', 'message' => 'Tidak ada data yang dihapus'];
			}
		} else {
			$result = ['status' => 'error', 'message' => 'Role Anda tidak diperkenankan untuk menghapus data'];
		}
		
		echo json_encode($result);
	}
	
	private function validateForm() {
	
		$validation =  \Config\Services::validation();
		$validation->setRule('nama', 'Nama', 'trim|required');
		$validation->setRule('username', 'Username', 'trim|required');
		$validation->setRule('email', 'Email', 'trim|required|valid_email');
		
		if ($this->request->getPost('id')) {
			if ($this->request->getPost('email') != $this->request->getPost('email_lama')) {
				// echo 'sss'; die;
				$validation->setRules(
					['email' => [
							'label'  => 'Email',
							'rules'  => 'required|valid_email|is_unique[user.email]',
							'errors' => [
								'is_unique' => 'Email sudah digunakan'
								, 'valid_email' => 'Email tidak valid'
								, 'required' => 'Email harus diisi'
							]
						]
					]
				);
			}
			
			if ($this->request->getPost('username') != $this->request->getPost('username_lama')) 
			{
				$validation->setRules(
					[
						'username' => [
							'label'  => 'Email',
							'rules'  => 'trim|required|is_unique[user.username]',
							'errors' => [
								'is_unique' => 'Username sudah digunakan',
								'required' => 'Username harus diisi'
							]
						]
					]
				);
			}
		} else {
			if ($this->hasPermission('create')) 
			{
				$validation->setRule('password', 'Password', 'trim|required|min_length[3]');
				$validation->setRules(
					[
						'email' => [
							'label'  => 'Email',
							'rules'  => 'required|valid_email|is_unique[user.email]',
							'errors' => [
								'is_unique' => 'Email sudah digunakan'
								, 'valid_email' => 'Email tidak valid'
							]
						],
						'username' => [
							'label'  => 'Email',
							'rules'  => 'trim|required|is_unique[user.username]',
							'errors' => [
								'is_unique' => 'Username sudah digunakan',
								'required' => 'Username harus diisi'
							]
						],
						'ulangi_password' => [
							'label'  => 'Ulangi Password',
							'rules'  => 'required|matches[password]',
							'errors' => [
								'required' => 'Ulangi password tidak boleh kosong'
								, 'matches' => 'Ulangi password tidak cocok dengan password'
							]
						]
					]
					
				);
			}
		}
		
		$valid = $validation->withRequest($this->request)->run();
		$form_errors = $validation->getErrors();
		
		if (!empty($_POST['option_ubah_password']) && $_POST['option_ubah_password'] == 'Y' ) {
			if (empty($_POST['password'])) {
				$form_errors['password'] = 'Password masih kosong';
			} else if ($_POST['password'] != $_POST['ulangi_password']) {
				$form_errors['password'] = 'Password dan ulangi password tidak sama';
			}
		}

		$file = $this->request->getFile('avatar');
		if ($file && $file->getName())
		{
			if ($file->isValid())
			{
				$type = $file->getMimeType();
				$allowed = ['image/png', 'image/jpeg', 'image/jpg'];
				
				if (!in_array($type, $allowed)) {
					$form_errors['avatar'] = 'Tipe file harus ' . join(', ', $allowed);
				}
				
				if ($file->getSize() > 300 * 1024) {
					$form_errors['avatar'] = 'Ukuran file maksimal 300Kb';
				}
				
				$info = \Config\Services::image()
						->withFile($file->getTempName())
						->getFile()
						->getProperties(true);
				
				if ($info['height'] < 100 || $info['width'] < 100) {
					$form_errors['avatar'] = 'Dimensi file minimal: 100px x 100px';
				}
				
			} else {
				$form_errors['avatar'] = $file->getErrorString().'('.$file->getError().')';
			}
		}
		
		if ($this->request->getPost('option_default_page') == 'url') {
			if (empty($this->request->getPost('default_page_url'))) {
				$form_errors['url'] = 'Url tidak boleh kosong';
			}
		}
		
		return $form_errors;
	}
	
	public function edit_password()
	{
		$data['title'] = 'Edit Password';
		$breadcrumb['Edit Password'] = '';
		
		$form_errors = null;
		$this->data['status'] = '';
		
		if ($this->request->getPost('submit')) 
		{
			$result = $this->model->getUserById();
			$error = false;
			
			if ($result) {
				
				if (!password_verify($this->request->getPost('password_old'), $result['password'])) {
					$error = true;
					$this->data['message'] = ['status' => 'error', 'message' => 'Password lama tidak cocok'];
				}
			} else {
				$error = true;
				$this->data['message'] = ['status' => 'error', 'message' => 'Data user tidak ditemukan'];
			}
		
			if (!$error) {
		
				$this->formValidation->setRule('password_new', 'Password', 'trim|required');
				$this->formValidation->setRule('password_new_confirm', 'Confirm Password', 'trim|required|matches[password_new]');
					
				$this->formValidation->withRequest($this->request)->run();
				$errors = $this->formValidation->getErrors();
				
				$custom_validation = new \App\Libraries\FormValidation;
				$custom_validation->checkPassword('password_new', $this->request->getPost('password_new'));
			
				$form_errors = array_merge($custom_validation->getErrors(), $errors);
					
				if ($form_errors) {
					$this->data['message'] = ['status' => 'error', 'message' => $form_errors];
				} else {
					$update = $this->model->updatePassword();
					if ($update) {
						$this->data['message'] = ['status' => 'ok', 'message' => 'Password Anda berhasil diupdate'];
					} else {
						$this->data['message'] = ['status' => 'error', 'message' => 'Password Anda gagal diupdate... Mohon hubungi admin. Terima Kasih...'];
					}
				}
			}
		}
		
		$this->data['title'] = 'Edit Password';
		$this->data['form_errors'] = $form_errors;
		$this->data['user'] = $this->model->getUserById($this->user['id_user']);
		$this->view('builtin/user/form-edit-password.php', $this->data);
	}
}
