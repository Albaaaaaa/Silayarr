<?php

namespace App\Controllers;
use App\Models\RegisterModel;
use \Config\App;
use App\Libraries\Auth;

class Register extends \App\Controllers\BaseController
{
	protected $model = '';
	
	public function __construct() {
		parent::__construct();
		$this->model = new RegisterModel;	
		$this->data['site_title'] = 'Register Akun';
		
		helper(['cookie', 'form']);
		
		$this->addJs($this->config->baseURL . 'public/vendors/jquery/jquery.min.js');
		$this->addJs($this->config->baseURL . 'public/vendors/bootstrap/js/bootstrap.min.js');
										
		$this->addStyle($this->config->baseURL . 'public/vendors/bootstrap/css/bootstrap.min.css');
		$this->addStyle($this->config->baseURL . 'public/vendors/font-awesome/css/font-awesome.min.css');
		$this->addStyle($this->config->baseURL . 'public/themes/modern/css/register.css');

		$this->addJs($this->config->baseURL . 'public/vendors/jquery.pwstrength.bootstrap/pwstrength-bootstrap.min.js');
		$this->addJs($this->config->baseURL . 'public/themes/modern/js/password-meter.js');
		
	}
	
	public function index()
	{
		
		$this->mustNotLoggedIn();
		$this->data['title'] = 'Register Akun';
		$message = [];
		
		if (!empty($_POST['submit'])) 
		{
			$error = false;
			$message['status'] = 'error';
			
			array_map('trim', $_POST);
			$form_error = $this->validateForm();

			if ($form_error) {
				$message['message'] = $form_error;
				$error = true;
			}
				
			// Submit data
			if (!$error) {		
				$message = $this->model->insertUser();
				if ($message['status'] == 'error') {
					$error = true;
				}
			}	
		}
		
		$file = 'form.php';
		if (!empty($_POST['submit']) && !$error) {
			$file = 'show_message.php';
		}
		
		$this->data['message'] = $message;
		$this->data['style'] = ' style="max-width:500px; margin-top:50px"';
		return view('themes/modern/register/' . $file, $this->data);
	}
	
	public function confirm() 
	{
		$this->data['title'] = 'Konfirmasi Alamat Email';
		
		$error = false;
		$message['status'] = 'error';
		
		if (empty($_GET['token'])) {
			$message['message'] = 'Token tidak ditemukan';
			$error = true;
		} else {
			@list($selector, $url_token) = explode(':', $_GET['token']);
			if (!$selector || !$url_token) {
				$message['message'] = 'Token tidak ditemukan';
				$error = true;
			}
		}
		
		if (!$error) {
						
			$dbtoken = $this->model->checkToken($selector);
			
			if ($dbtoken) 
			{
				$error = false;
				$user = $this->model->checkUserById($dbtoken['id_user']);
				$auth = new Auth;
				
				if ($user['verified'] == 1) {
					$message['message'] = 'Akun sudah pernah diaktifkan';
					$error = true;
				} 
				else if ($dbtoken['expires'] < date('Y-m-d H:i:s')) {
					$message['message'] = 'Link expired, silakan request <a href="'. $this->config->baseURL .'register/resendlink">link aktivasi</a> yang baru';
					$error = true;
				} 
				else if (!$auth->validateToken($url_token, $dbtoken['token'])) {
					$message['message'] = 'Token invalid, silakan <a href="'. $this->config->baseURL.'register">register</a> ulang atau request <a href="'. $this->config->baseURL.'resendlink">link aktivasi</a> yang baru';
					$error = true;
				}
				
			} else {
				$message['message'] = 'Token tidak ditemukan atau akun sudah pernah diaktifkan';
				$error = true;
			}
		}
		
		if (!$error)
		{
			$update = $this->model->updateUser($dbtoken);
		
			if ($update) {
				$message['status'] = 'ok';
				$message['message'] = 'Selamat!!!, akun Anda berhasil diaktifkan, Anda sekarang dapat <a href="'.$this->config->baseURL.'login">Login</a> menggunakan akun Anda';
			} else {
				$email_config = new \Config\EmailConfig;
				$this->data['message'] = 'Token ditemukan tetapi saat ini akun tidak dapat diaktifkan karena ada gangguan pada sistem, silakan coba dilain waktu, atau hubungi <a href="mailto:' . $email_config->emailSupport . '" title="Hubungi kami via email">' . $email_config->emailSupport . '</a>';
			}					
		}
		
		$this->data['message'] = $message;
		return view('themes/modern/register/show_message.php', $this->data);
	}
	
	public function resendlink() 
	{
		$this->data['title'] = 'Kirim Ulang Link Aktivasi Akun';
		$message = [];
		$error = false;
		
		helper('registrasi');
		$setting_register = $this->model->getSettingRegistrasi();
		
		if ($setting_register['metode_aktivasi'] != 'email') {
			
			$email_config = new \Config\EmailConfig;
			$message['status'] = 'error';
			$message['message'] = 'Metode aktivasi yang digunakan bukan melalui email. Untuk mengaktifkan akun, silakan hubungi administrator di: <a href="mailto:' . $email_config->emailSupport . '" title="Hubungi Support">' . $email_config->emailSupport . '</a>';
		
		} else {
		
			if (!empty($_POST['submit'])) 
			{
				// Cek isian form
				array_map('trim', $_POST);
				$form_error = $this->validateFormResendlink();
				
				$message['status'] = 'error';
				if ($form_error) {
					$message['message'] = $form_error;
					$error = true;
				}

				// Submit data
				if (!$error) {
					$message = $this->model->resendLink();
					if ($message['status'] == 'error') {
						$error = true;
					}
				}
			}
		}
		
		$file = 'form-resendlink.php';
		if ($setting_register['metode_aktivasi'] != 'email' || (!$error && !empty($_POST['submit'])) ) {
			$file = 'show_message.php';
		}
		
		$this->data['message'] = $message;
		return view('themes/modern/register/' . $file, $this->data);
	}
	
	private function validateForm() 
	{

		helper ('form_requirement');
		
		$error = [];
		
		$validation_message = csrf_validation();

		// Cek CSRF token
		if ($validation_message) {
			return [$validation_message['message']];
		}
		
		// Cek email
		$user_exists = $this->model->getUserByEmail($_POST['email']);
		if ($user_exists) {
			if($user_exists['verified'] == 0) {
				$error['message'] = 'Email sudah terdaftar tetap belum diaktifkan, silakan <a href="' . $this->config->baseURL . 'register/resendlink" title="Kirim ulang link aktivasi">aktifkan disini</a>';
			}
			return $error;
		}
		
		$validation =  \Config\Services::validation();
		$validation->setRules(
			[
				'nama' => ['label' => 'Nama', 'rules' => 'trim|required|min_length[5]'],
				'password' => ['label' => 'Password', 'rules' => 'trim|required'],
				'email' => [
					'label'  => 'Email',
					'rules'  => 'trim|required|valid_email|is_unique[user.email]',
					'errors' => [
						'is_unique' => 'Email sudah digunakan'
						, 'valid_email' => 'Email tidak valid'
						, 'required' => 'Email harus diisi'
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
				'password_confirm' => [
					'label'  => 'Ulangi Password',
					'rules'  => 'trim|required|matches[password]',
					'errors' => [
						'required' => 'Ulangi password tidak boleh kosong'
						, 'matches' => 'Ulangi password tidak cocok dengan password'
					]
				]
			]
			
		);

		$validate = $validation->withRequest($this->request)->run();
		
		if ($validate) 
		{
			// Passsword
			$invalid = password_requirements($_POST['password']);
			if ($invalid) {
				$error = array_merge($error, $invalid);
			}
			
			// Email
			$invalid = email_requirements($_POST['email']);
			if ($invalid) {
				$error = array_merge($error, $invalid);
			}
		} else {
			$error = $validation->getErrors();
		}

		return $error;
	}
	
	private function validateFormResendlink() {
		
		$error = false;
		
		$validation_message = csrf_validation();

		// Cek CSRF token
		if ($validation_message) {
			return [$validation_message['message']];
		}
		
		$validation =  \Config\Services::validation();
		$validation->setRules(
			[
				'email' => [
					'label'  => 'Email',
					'rules'  => 'trim|required|valid_email',
					'errors' => [
						'valid_email' => 'Alamat email tidak valid'
					]
				]
			]
		);

		$validate = $validation->withRequest($this->request)->run();
		
		if ($validate) 
		{		
			$user = $this->model->getUserByEmail($_POST['email']);
			if ($user) {
				if ($user['verified'] == 1) {
					$error[] = 'Akun sudah pernah diaktifkan, silakan <a href="' . $this->config->baseURL . 'login" title="Login">login</a> ke akun Anda';
				}
			} else {
				$error[] = 'Email belum terdaftar, silakan <a href="' . $this->config->baseURL . 'register" title="Register Akun">register akun disini</a>';
			}
		} else {
			$error = $validation->getErrors();
		}

		return $error;
	}
}
