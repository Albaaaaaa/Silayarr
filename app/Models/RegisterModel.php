<?php

namespace App\Models;
use App\Libraries\Auth;

class RegisterModel extends \App\Models\BaseModel
{
	public function getUserByEmail($email) {
		$sql = 'SELECT * FROM user WHERE email = ?';
		$result = $this->db->query($sql, $email)->getRowArray();
		return $result;
	}
	
	public function resendLink() 
	{
		$error = false;
		$message['status'] = 'error';
		
		$user = $this->getUserByEmail( $_POST['email'] );
					
		$this->db->transBegin();
		
		$this->db->table('user_token')->delete(['action' => 'activation', 'id_user' => $user['id_user']]);

		$auth = new Auth;
		$token = $auth->generateDbToken();	
		$data_db['selector'] = $token['selector'];
		$data_db['token'] = $token['db'];
		$data_db['action'] = 'activation';
		$data_db['id_user'] = $user['id_user'];
		$data_db['created'] = date('Y-m-d H:i:s');
		$data_db['expires'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
		
		$insert_token = $this->db->table('user_token')->insert($data_db);
		
		// $save = true;
		if ($insert_token)
		{
			$send_email = $this->sendConfirmEmail($token, $user, 'link_aktivasi');
						
			if ($send_email['status'] == 'ok')
			{
				$this->db->transCommit();
				$email_config = new \Config\EmailConfig;
				$message['status'] = 'ok';
				$message['message'] = '
				Link aktivasi berhasil dikirim ke alamat email: <strong>'. $_POST['email'] . '</strong>, silakan gunakan link tersebut untuk aktivasi akun Anda<br/></br>Biasanya, email akan sampai kurang dari satu menit, namun jika lebih dari lima menit email belum sampai, coba cek folder spam. Jika email benar benar tidak sampai, silakan hubungi kami di <a href="mailto:'.$email_config->emailSupport.'" target="_blank">'.$email_config->emailSupport.'</a>';
			} else {
				$message['message'] = 'Error: Link aktivasi gagal dikirim... <strong>' . $send_email['message'] . '</strong>';
				$error = true;
			}
		} else {
			$message['message'] = 'Gagal menyimpan data token, silakan hubungi kami di: <a href="mailto:'.$email_config->emailSupport.'" target="_blank">'.$config['email_support'].'</a>';
			$error = true;
		}
		
		if ($error) {
			$this->db->transRollback();
		}
		
		return $message;
	}
	
	public function checkUserById($id_user) 
	{
		$sql = 'SELECT * FROM user WHERE id_user = ?';
		$user = $this->db->query($sql, $id_user)->getRowArray();
		return $user;
	}
	
	public function updateUser($dbtoken) {
		$this->db->transStart();

		$query = $this->db->table('user_token')->delete(['selector' => $dbtoken['selector']]);
		$query = $this->db->table('user_token')->delete(['action' => 'register', 'id_user' => $dbtoken['id_user']]);
		
		$query = $this->db->table('user')->update(['verified' => 1], ['id_user' => $dbtoken['id_user']]);
		
		$update = $this->db->transComplete();
		return $update;
	}
	
	public function checkToken($selector) {
		
		$sql = 'SELECT * FROM user_token
				WHERE selector = ?';
			
		$dbtoken = $this->db->query($sql, $selector)->getRowArray();
		return $dbtoken;
	}
	
	public function insertUser() 
	{
		$error = false;
		$message['status'] = 'error';
		
		$this->db->transBegin();
		$setting_register = $this->getSettingRegistrasi();
		$verified = $setting_register['metode_aktivasi'] == 'langsung' ? 1 : 0;
				
		$data_db['nama'] = $_POST['nama'];
		$data_db['email'] = $_POST['email'];
		$data_db['username'] = $_POST['username'];
		$data_db['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$data_db['verified'] = $verified;
		$data_db['status'] = 'active';
		$data_db['created'] = date('Y-m-d H:i:s');
		$data_db['default_page_id_module'] = $setting_register['default_page_id_module'];
		$data_db['default_page_type'] = $setting_register['default_page_type'];
		$data_db['default_page_id_role'] = $setting_register['default_page_id_role'];
		$data_db['default_page_url'] = $setting_register['default_page_url'];

		$insert_user = $this->db->table('user')->insert($data_db);
		$id_user = $this->db->insertID();
		
		if (!$id_user)
		{
			$message['message'] = 'System error, please try again later...';
			$error = true;
		
		} else {
			
			// Default role
			$sql = 'SELECT * FROM setting WHERE type = "register" AND param="id_role"';
			$setting = $this->db->query($sql)->getRowArray();
			$id_role = $setting['value'];
			
			$data_db = [];
			$data_db['id_user'] = $id_user;
			$data_db['id_role'] = $id_role;
			$insert_user = $this->db->table('user_role')->insert($data_db);
			
			if ($setting_register['metode_aktivasi'] == 'manual') 
			{
				$message['message'] = 'Terima kasih telah melakukan registrasi, aktivasi akun Anda menunggu persetujuan Administrator. Terima Kasih';
				
			} else if ($setting_register['metode_aktivasi'] == 'langsung') {
				
				$message['message'] = 'Terima kasih telah melakukan registrasi, akun Anda otomatis aktif dan langsung dapat digunakan, silakan <a href="' . base_url() . '/login">login disini</a>';
				
			} else if ($setting_register['metode_aktivasi'] == 'email') {
				

				$auth = new Auth;
				$token = $auth->generateDbToken();					
				$data_db = [];
				$data_db['selector'] = $token['selector'];
				$data_db['token'] = $token['db'];
				$data_db['action'] = 'register';
				$data_db['id_user'] = $id_user;
				$data_db['created'] = date('Y-m-d H:i:s');
				$data_db['expires'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
				
				$this->db->table('user_token')->insert($data_db);
				
				$send_email = $this->sendConfirmEmail($token, $_POST);
			
				if ($send_email['status'] == 'error')
				{
					$message['message'] = 'Error: Link konfirmasi gagal dikirim... <strong>' . $send_email['message'] . '</strong>';
					$error = true;
				} else {
					$message['message'] = 'Terima kasih telah melakukan registrasi, untuk memastikan bahwa kamu adalah pemilik alamat email <strong>' . $_POST['email'] . '</strong>, mohon klik link konfirmasi yang baru saja kami kirimkan ke alamat email tersebut<br/><br/>Biasanya, email akan sampai kurang dari satu menit, namun jika lebih dari lima menit email belum sampai, coba cek folder spam. Jika email benar benar tidak sampai, silakan hubungi kami di support@jagowebdev.com';
				}
			}
		}
		
		if ($error) {
			$this->db->transRollback();
		} else {
			$this->db->transCommit();
			$message['status'] = 'ok';
		}
	
		return $message;
	}
	
	private function sendConfirmEmail($token, $user, $type = 'email_confirm')
	{
		helper('email_registrasi');
		
		if ($type == 'email_confirm') {
			$email_text = email_registration_content();
		} else {
			$email_text = email_resendlink_content();
		}
		
		$url_token = $token['selector'] . ':' . $token['external'];
		$url = base_url() .'/register/confirm?token='.$url_token;
		$email_content = str_replace('{{NAME}}'
									, $user['nama']
									, $email_text
								);
								
		$email_content = str_replace('{{url}}', $url, $email_content);
		
		$email_config = new \Config\EmailConfig;
		$email_data = array('from_email' => $email_config->from
						, 'from_title' => 'Jagowebdev'
						, 'to_email' => $user['email']
						, 'to_name' => $user['nama']
						, 'email_subject' => 'Konfirmasi Registrasi Akun'
						, 'email_content' => $email_content
						, 'images' => ['logo_text' => ROOTPATH . 'public/images/logo_text.png']
		);
		
		require_once('app/Libraries/SendEmail.php');

		$emaillib = new \App\Libraries\SendEmail;
		$emaillib->init();
		$emaillib->setProvider($email_config->provider);
		$send_email =  $emaillib->send($email_data);
		
		return $send_email;
	}
}
?>