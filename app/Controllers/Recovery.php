<?php

namespace App\Controllers;
use App\Models\RecoveryModel;
use \Config\App;
use App\Libraries\Auth;

class Recovery extends \App\Controllers\BaseController
{
	protected $model = '';
	
	public function __construct() {
		parent::__construct();
		$this->model = new RecoveryModel;	
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
		$message = [];
		$this->data['title'] = 'Reset Password';
		
		if (!empty($_POST['submit'])) 
		{
			// Cek isian form
			array_map('trim', $_POST);
			$form_error = $this->validateForm();
			
			$error = false;
			$message['status'] = 'error';
			if ($form_error) {
				$message['message'] = $form_error;
				$error = true;
			}
			
			// Submit data
			if (!$error) 
			{
				$message = $this->model->sendLink();
				if ($message['status'] == 'error') {
					$error = true;
				}
			}
		}
		
		$file = 'form-recovery';
		if (!empty($_POST['submit']) && !$error) {
			$file = 'show_message.php';
		}
		

		$this->data['message'] = $message;
		return view('themes/modern/register/' . $file, $this->data);
	}
	
	public function reset() 
	{
		$error = false;
		$message = [];
		$this->data['title'] = 'Reset Password';
		
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
				$auth = new Auth;
				if ($dbtoken['expires'] < date('Y-m-d H:i:s')) {
					$message['message'] = 'Link expired, silakan request <a href="'. $this->config->baseURL.'/recovery">link reset password</a> yang baru';
					$error = true;
				} 
				else if (!$auth->validateToken($url_token, $dbtoken['token'])) {
					$message['message'] = 'Token invalid, silakan request <a href="'. $this->config->baseURL.'/recovery">link reset password</a> yang baru';
					$error = true;
				}
				
			} else {
				$message['message'] = 'Token tidak ditemukan, silakan request <a href="'. $this->config->baseURL .'/recovery">link reset password</a> yang baru';
				$error = true;
			}
		}		

		if (!$error)
		{			
			if (!empty($_POST['submit'])) {
				// Cek isian form
				array_map('trim', $_POST);
				$form_error = $this->validateFormReset();

				if ($form_error) {
					$message['message'] = $form_error;
					$error = true;
				}
				
				// Submit data
				if (!$error) {
					
					$update = $this->model->updatePassword($dbtoken);
					
					if ($update) {
						$message['status'] = 'ok';
						$message['message'] = 'Password Anda berhasil diupdate, sekarang Anda dapat <a href="'.$this->config->baseURL.'login">Login</a> menggunakan password baru Anda';
					} else {
						$email_config = new \Config\EmailConfig;
						$message['message'] = 'Password gagal diupdate, silakan coba dilain waktu, atau hubungi <a href="mailto:' . $email_config->emailSupport . '" title="Hubungi kami via email">' . $email_config->emailSupport . '</a>';
						$error = true;
					}		
					
				}
			}
		}
		
		if ($error) {
			$message['status'] = 'error';
		}
		
		$file = 'form-reset-password.php';
		if (!empty($_POST['submit']) && !$error) {
			$file = 'show_message.php';
		}
		
		$this->data['message'] = $message;
		return view('themes/modern/register/' . $file, $this->data);
	}
	
	private function validateForm() 
	{
		$error = [];
		
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
				if ($user['verified'] == 0) {
					$error[] = 'Email belum diaktifkan, silakan <a href="' . base_url() . '/register/resendlink" title="Kirim ulang link aktivasi">aktifkan disini</a>';
				}
			} else {
				$error[] = 'Email belum terdaftar, silakan <a href="' . base_url() . '/register" title="Register Akun">register akun disini</a>';
			}
		} else {
			$error = $validation->getErrors();
		}

		return $error;
	}
	
	private function validateFormReset() {
		
		$error = [];
	
		$validation_message = csrf_validation();
		// Cek CSRF token
		if ($validation_message) {
			return [$validation_message['message']];
		}
		
		$validation =  \Config\Services::validation();
		$validation->setRules(
			[
				'password' => ['label' => 'Password', 'rules' => 'trim|required'],
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
			helper('form_requirement');			
			$invalid = password_requirements($_POST['password']);
			if ($invalid) {
				$error = array_merge($error, $invalid);
			}
		} else {
			$error = $validation->getErrors();
		}

		return $error;
	}
}
