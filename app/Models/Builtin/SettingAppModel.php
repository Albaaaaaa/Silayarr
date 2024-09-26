<?php
namespace App\Models\Builtin;

class SettingAppModel extends \App\Models\BaseModel
{
	public function getSettingAplikasi() {
		$sql = 'SELECT * FROM setting WHERE type="app"';
		$query = $this->db->query($sql)->getResultArray();
		return $query;
	}
	
	public function getUserSetting() {
		$sql = 'SELECT * FROM setting_user WHERE id_user = ? AND type="layout"';
		$data = $this->db->query($sql, $_SESSION['user']['id_user'])
					->getResultArray();
		return $data;
	}
	
	public function saveData() 
	{
		$query = false;
		helper(['util', 'upload_file']);
		
		$sql = 'SELECT * FROM setting WHERE type="app"';
		$query = $this->db->query($sql)->getResultArray();
		
		foreach($query as $val) {
			$curr_db[$val['param']] = $val['value'];
		}
		
		// Logo Login
		$logo_login = $curr_db['logo_login'];
		$path = ROOTPATH . 'public/images/';
		if ($_FILES['logo_login']['name']) 
		{
			//old file
			if ($curr_db['logo_login']) {
				if (file_exists($path . $curr_db['logo_login'])) {
					$unlink = delete_file($path . $curr_db['logo_login']);
					if (!$unlink) {
						$data['msg']['status'] = 'error';
						$data['msg']['content'] = 'Gagal menghapus gambar lama';
					}
				}
			}
			
			$logo_login = \upload_file($path, $_FILES['logo_login']);
		}
		
		// Logo App
		$logo_app = $curr_db['logo_app'];
		if ($_FILES['logo_app']['name']) 
		{
			//old file
			if ($curr_db['logo_app']) {
				if (file_exists($path . $curr_db['logo_app'])) {
					$unlink = delete_file($path . $curr_db['logo_app']);
					if (!$unlink) {
						$data['msg']['status'] = 'error';
						$data['msg']['content'] = 'Gagal menghapus gambar lama';
					}
				}
			}
			
			$logo_app = \upload_file($path, $_FILES['logo_app']);
		}
		
		// Favicon
		$favicon = $curr_db['favicon'];
		if ($_FILES['favicon']['name']) 
		{
			//old file
			if ($curr_db['favicon']) {
				if (file_exists($path . $curr_db['favicon'])) {
					$unlink = delete_file($path . $curr_db['favicon']);
					if (!$unlink) {
						$data['msg']['status'] = 'error';
						$data['msg']['content'] = 'Gagal menghapus gambar lama';
					}
				}
			}
			
			$favicon = \upload_file($path, $_FILES['favicon']);
		}
		
		// Logo Register
		$logo_register = $curr_db['logo_register'];
		if ($_FILES['logo_register']['name']) 
		{
			//old file
			if ($curr_db['logo_register']) {
				if (file_exists($path . $curr_db['logo_register'])) {
					$unlink = delete_file($path . $curr_db['logo_register']);
					if (!$unlink) {
						$data['msg']['status'] = 'error';
						$data['msg']['content'] = 'Gagal menghapus gambar lama';
					}
				}
			}
			
			$logo_register = \upload_file($path, $_FILES['logo_register']);
		}
		
		$data_db =[];
		if ($logo_login && $logo_app && $favicon && $logo_register) 
		{
			$data_db[] = ['type' => 'app', 'param' => 'logo_login', 'value' => $logo_login];
			$data_db[] = ['type' => 'app', 'param' => 'logo_app', 'value' => $logo_app];
			$data_db[] = ['type' => 'app', 'param' => 'footer_login', 'value' => htmlentities($_POST['footer_login'])];
			$data_db[] = ['type' => 'app', 'param' => 'btn_login', 'value' => $_POST['btn_login']];
			$data_db[] = ['type' => 'app', 'param' => 'footer_app', 'value' => htmlentities($_POST['footer_app'])];
			$data_db[] = ['type' => 'app', 'param' => 'background_logo', 'value' => $_POST['background_logo']];
			$data_db[] = ['type' => 'app', 'param' => 'judul_web', 'value' => $_POST['judul_web']];
			$data_db[] = ['type' => 'app', 'param' => 'deskripsi_web', 'value' => $_POST['deskripsi_web']];
			$data_db[] = ['type' => 'app', 'param' => 'favicon', 'value' => $favicon];
			$data_db[] = ['type' => 'app', 'param' => 'logo_register', 'value' => $logo_register];
			
			$this->db->transStart();
			$this->db->table('setting')->delete(['type' => 'app']);
			$this->db->table('setting')->insertBatch($data_db);
			$query = $this->db->transComplete();
			$query_result = $this->db->transStatus();
			
			if ($query_result) {
				$file_name = ROOTPATH . 'public/themes/modern/builtin/css/login-header.css';
				$css = '.login-header {background-color: '.$_POST['background_logo'].';}.edit-logo-login-container {background: '.$_POST['background_logo'].';}';
				
				if (file_exists($file_name)) {
					file_put_contents($file_name, $css);
				}
				
				$result['status'] = 'ok';
				$result['message'] = 'Data berhasil disimpan';
			} else {
				$result['status'] = 'error';
				$result['message'] = 'Data gagal disimpan';
			}
			
		} else {
			$result['status'] = 'error';
			$result['content'] = 'Error saat memperoses gambar';
		}

		return $result;
	}
}
?>