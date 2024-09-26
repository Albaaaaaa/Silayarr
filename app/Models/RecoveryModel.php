<?php

namespace App\Models;
use App\Libraries\Auth;

class RecoveryModel extends \App\Models\BaseModel
{
	public function getUserByEmail($email) {
		$sql = 'SELECT * FROM user WHERE email = ?';
		$result = $this->db->query($sql, $email)->getRowArray();
		return $result;
	}
	
	public function checkToken($selector) {
		
		$sql = 'SELECT * FROM user_token
				WHERE selector = ?';
			
		$dbtoken = $this->db->query($sql, $selector)->getRowArray();
		return $dbtoken;
	}
	
	public function sendLink() 
	{
		$error = false;
		$message['status'] = 'error';
		
		$sql = 'SELECT * FROM user WHERE email = ?';
		$user = $this->db->query($sql, $_POST['email'])->getRowArray();
		
		$this->db->transStart();
		
		$this->db->table('user_token')->delete(['action' => 'recovery', 'id_user' => $user['id_user']]);
		$auth = new Auth;
		$token = $auth->generateDbToken();
		$data_db['selector'] = $token['selector'];
		$data_db['token'] = $token['db'];
		$data_db['action'] = 'recovery';
		$data_db['id_user'] = $user['id_user'];
		$data_db['created'] = date('Y-m-d H:i:s');
		$data_db['expires'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
		
		$insert_token = $this->db->table('user_token')->insert($data_db);
		
			// $save = true;
		if ($insert_token)
		{
			$url_token = $token['selector'] . ':' . $token['external'];
			$url = base_url().'/recovery/reset?token='.$url_token;
			
			helper('email_registrasi');
			$email_config = new \Config\EmailConfig;
			$email_data = array('from_email' => $email_config->from
							, 'from_title' => 'Jagowebdev'
							, 'to_email' => $_POST['email']
							, 'to_name' => $_POST['email']
							, 'email_subject' => 'Reset Password'
							, 'email_content' => str_replace('{{url}}', $url, email_recovery_content() )
							, 'images' => ['logo_text' => ROOTPATH . 'public/images/logo_text.png']
			);
			
			require_once('app/Libraries/SendEmail.php');
			$emaillib = new \App\Libraries\SendEmail;
			$emaillib->init();
			$emaillib->setProvider($email_config->provider);
			$send_email =  $emaillib->send($email_data);
		
			if ($send_email['status'] == 'ok')
			{
				$this->db->transCommit();
				
				$message['status'] = 'ok';
				$message['message'] = '
				Link reset password berhasil dikirim ke alamat email: <strong>'. $_POST['email'] . '</strong>, silakan gunakan link tersebut untuk mereset password Anda<br/></br>Biasanya, email akan sampai kurang dari satu menit, namun jika lebih dari lima menit email belum sampai, coba cek folder spam. Jika email benar benar tidak sampai, silakan hubungi kami di <a href="mailto:'.$email_config->emailSupport.'" target="_blank">'.$email_config->emailSupport .'</a>';
			} else {
				$message['message'] = 'Error: Link reset password gagal dikirim... <strong>' . $send_email['message'] . '</strong>';
			}
		} else {
			$message['message'] = 'Gagal menyimpan data token, silakan hubungi kami di: <a href="mailto:'.$email_config->emailSupport .'" target="_blank">'.$email_config->emailSupport.'</a>';	
		}
		
		return $message;
	}
	
	public function updatePassword($dbtoken) {
		$this->db->table('user_token')->delete(['selector' => $dbtoken['selector']]);
		$update = $this->db->table('user')->update(['password' => password_hash($_POST['password'], PASSWORD_DEFAULT)], ['id_user' => $dbtoken['id_user']]);
		return $update;
	}
}
?>