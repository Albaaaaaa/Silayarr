<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Models\Builtin;
use App\Libraries\Auth;

class LoginModel extends \App\Models\BaseModel
{	
	public function recordLogin() 
	{
		$username = $this->request->getPost('username'); 
		$data_user = $this->db->query('SELECT id_user 
									FROM user
									WHERE username = ?', [$username]
								)
							->getRow();

		$data = array('id_user' => $data_user->id_user
					, 'id_activity' => 1
					, 'time' => date('Y-m-d H:i:s')
				);
		
		$this->db->table('user_login_activity')->insert($data);
	}
	
	public function setUserToken($user) 
	{
		$auth = new Auth;
		$token = $auth->generateDbToken();
		$expired_time = time() + (7*24*3600); // 7 day
		setcookie('remember', $token['selector'] . ':' . $token['external'], $expired_time, '/');
		
		$data_db = array ( 'id_user' => $user['id_user']
						, 'selector' => $token['selector']
						, 'token' => $token['db']
						, 'action' => 'remember'
						, 'created' => date('Y-m-d H:i:s')
						, 'expires' => date('Y-m-d H:i:s', $expired_time)
					);

		$this->db->table('user_token')->insert($data_db);
	}
	
	public function deleteAuthCookie($id_user) 
	{
		$this->db->table('user_token')->delete(['action' => 'remember', 'id_user' => $id_user]);
		setcookie('remember', '', time() - 360000, '/');	
	}
	
	public function getSettingRegistrasi() 
	{
		$sql = 'SELECT * FROM setting WHERE type="register"';
		$query = $this->db->query($sql)->getResultArray();
		
		return $query;
	}
	
	/* See base model
	public function checkUser($username) 
	{
		
	} */
}
?>