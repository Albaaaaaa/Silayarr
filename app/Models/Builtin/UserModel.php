<?php
namespace App\Models\Builtin;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class UserModel extends \App\Models\BaseModel
{
	public function getListUsers($where) {
		
		// Get user
		$columns = $this->request->getPost('columns');
		$order_by = '';
		
		// Search
		$search_all = @$this->request->getPost('search')['value'];
		if ($search_all) {
			
			foreach ($columns as $val) {
				if (strpos($val['data'], 'ignore') !== false)
					continue;
				
				$where_col[] = $val['data'] . ' LIKE "%' . $search_all . '%"';
			}
			 $where .= ' AND (' . join(' OR ', $where_col) . ') ';
		}
		
		// Order
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		
		$order_data = $this->request->getPost('order');
		$order = '';
		if (!empty($_POST['columns']) && strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = ' ORDER BY ' . $order_by . ' LIMIT ' . $start . ', ' . $length;
		}
		
		$sql = 'SELECT COUNT(*) as jml FROM
				(SELECT user.*, GROUP_CONCAT(judul_role) AS judul_role FROM user 
				LEFT JOIN user_role USING(id_user) 
				LEFT JOIN role ON user_role.id_role = role.id_role
				' . $where . '
				GROUP BY id_user) AS tabel';
				
		$query = $this->db->query($sql)->getRowArray();
		$total_filtered = $query['jml'];
		
		$sql = 'SELECT user.*, GROUP_CONCAT(judul_role) AS judul_role FROM user 
				LEFT JOIN user_role USING(id_user) 
				LEFT JOIN role ON user_role.id_role = role.id_role
				' . $where . '
				GROUP BY id_user
				' . $order;
		
		
		$data = $this->db->query($sql)->getResultArray();
		return ['data' => $data, 'total_filtered' => $total_filtered];
		
	}
	
	public function countAllUsers($where = null) {
		$query = $this->db->query('SELECT COUNT(*) as jml FROM user' . $where)->getRow();
		return $query->jml;
	}
	
	public function getRoles() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getSettingRegister() {
		$sql = 'SELECT * FROM setting WHERE type="register"';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getListModules() {
		
		$sql = 'SELECT * FROM module LEFT JOIN module_status USING(id_module_status) ORDER BY nama_module';
		return $this->db->query($sql)->getResultArray();
	}
		
	public function saveData($user_permission = []) 
	{ 
		$fields = ['nama', 'email'];
		if (in_array('update_all', $user_permission)) {
			$add_field = ['username', 'status', 'verified', 'default_page_id_role', 'default_page_id_module', 'default_page_url'];
			$fields = array_merge($fields, $add_field);
		}

		foreach ($fields as $field) {
			$data_db[$field] = $this->request->getPost($field);
		}
		
		if ($this->request->getPost('password')) {
			$data_db['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
		}
		
		$data_db['default_page_type'] = $this->request->getPost('option_default_page');
		$this->db->transStart();
		
		if (!$this->request->getPost('id')) {
			$data_db['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
		}
		
		// Save database
		if ($this->request->getPost('id')) {
			$id_user = $this->request->getPost('id');
			$this->db->table('user')->update($data_db, ['id_user' => $id_user]);
		} else {
			$this->db->table('user')->insert($data_db);
			$id_user = $this->db->insertID();
		}
				
		if (in_array('update_all', $user_permission)) {
			$data_db = [];
			foreach ($_POST['id_role'] as $id_role) {
				$data_db[] = ['id_user' => $id_user, 'id_role' => $id_role];
			}
		
			$this->db->table('user_role')->delete(['id_user' => $id_user]);
			$this->db->table('user_role')->insertBatch($data_db);
		}
		
		$this->db->transComplete();
		$trans = $this->db->transStatus();
		
		$save = false;
		if ($trans) {
			
			$file = $this->request->getFile('avatar');
			$path = ROOTPATH . 'public/images/user/';
			
			$sql = 'SELECT avatar FROM user WHERE id_user = ?';
			$img_db = $this->db->query($sql, $id_user)->getRowArray();
			$new_name = $img_db['avatar'];
			
			if (!empty($_POST['avatar_delete_img'])) 
			{
				$del = delete_file($path . $img_db['avatar']);
				$new_name = '';
				if (!$del) {
					$result = ['status' =>'error', 'message' => 'Gagal menghapus gambar lama'];
					$error = true;
				}
			}
					
			if ($file && $file->getName()) 
			{
				//old file
				if ($img_db['avatar']) {
					if (file_exists($path . $img_db['avatar'])) {
						$unlink = delete_file($path . $img_db['avatar']);
						if (!$unlink) {
							$result = ['status' => 'error', 'message' => 'Gagal menghapus gambar lama'];
						}
					}
				}
							
				helper('upload_file');
				$new_name =  get_filename($file->getName(), $path);
				$file->move($path, $new_name);
					
				if (!$file->hasMoved()) {
					$result = ['status' => 'error', 'message' => 'Error saat memperoses gambar'];
					return $result;
				}
			}
			
			// Update avatar
			$data_db = [];
			$data_db['avatar'] = $new_name;
			$save = $this->db->table('user')->update($data_db, ['id_user' => $id_user]);
		}

		if ($save) {
			$result = ['status' =>'ok', 'message' => 'Data berhasil disimpan', 'id_user' => $id_user];

			if ($this->session->get('user')['id_user'] == $id_user) {
				// Reload data user
				$this->session->set('user', $this->getUserById($this->session->get('user')['id_user']) );
			}
		} else {
			$result = ['status' => 'error', 'message' => 'Data gagal disimpan'];
		}
								
		return $result;
	}
	
	private function getUserByEmail($email) {
		$sql = 'SELECT * FROM user WHERE email = ?';
		return $this->db->query($sql, $email)->getRowArray();
	}
	
	private function getUserByUsername($username) {
		$sql = 'SELECT * FROM user WHERE username = ?';
		return $this->db->query($sql, $username)->getRowArray();
	}
	
	public function uploadExcel() 
	{
		helper(['upload_file', 'format']);
		$path = ROOTPATH . 'public/tmp/';
		
		
		$file = $this->request->getFile('file_excel');
		if (! $file->isValid())
		{
			throw new RuntimeException($file->getErrorString().'('.$file->getError().')');
		}
				
		require_once 'app/ThirdParty/Spout/src/Spout/Autoloader/autoload.php';
		
		$filename = upload_file($path, $_FILES['file_excel']);
		$reader = ReaderEntityFactory::createReaderFromFile($path . $filename);
		$reader->open($path . $filename);
		
		$sql = 'SELECT * FROM role';
		$data = $this->db->query($sql)->getResultArray();
		$roles = [];
		foreach($data as $val) {
			$roles[$val['nama_role']] = $val['id_role'];
		}
		
		$warning = [];
		$error_message = [];
		$row_inserted = 0;
		foreach ($reader->getSheetIterator() as $sheet) 
		{
			$num_row = 0;
			foreach ($sheet->getRowIterator() as $num_row => $row) 
			{
				$role_list = [];
				$data_db = [];
				$data_db_role = [];
				$error_message_row = [];
				
				$cols = $row->toArray();
								
				if ($num_row == 1) {
					$field_table = $cols;
					$field_name = array_map('strtolower', $field_table);
					continue;
				}
				
				$name = $email = $username = '';
				$error = false;
				
				foreach ($field_name as $num_col => $field) 
				{
					$val = null;
					if (key_exists($num_col, $cols) && $cols[$num_col] != '') {
						$val = $cols[$num_col];
					}
					
					if ($val instanceof \DateTime) {
						$val = $val->format('Y-m-d H:i:s');
					}
					
					if ($field == 'role') {
						if (trim($val)) {
							$exp = explode(',', $val);
							$role_list = array_map('trim', $exp);
						}
						continue;
					}
					
					if ($field == 'nama') {
						$nama = $val;
					}
					
					if ($field == 'email') {
						$email = $val;
					}
					
					if ($field == 'username') {
						$username = $val;
					}

					if ($field == 'password') {
						$val = password_hash($val, PASSWORD_DEFAULT);
					}
					
					$data_db[$field] = $val;
				}
				
				if ($email) {
					if ($this->getUserByEmail($email)) {
						$error_message_row[] = 'Email ' . $email . ' sudah digunakan';
						$error = true;
					}
				}
				
				if ($username) {
					if ($this->getUserByUsername($username)) {
						$error_message_row[] = 'Username ' . $username . ' sudah digunakan';
						$error = true;
					}
				}
				
				if (!$error) {
					
					$data_db['verified'] = 1;
					$data_db['status'] = 'aktif';
		
					$this->db->transStart();
					$query = $this->db->table('user')->insert($data_db);
					$id_user = $this->db->insertID();
					
					
					
					$data_db_role = [];
					if ($role_list) {
						foreach ($role_list as $role_name) {
							if (key_exists($role_name, $roles)) {
								$data_db_role[] = ['id_user' => $id_user, 'id_role' => $roles[$role_name]];
							} else {
								$warning[] = 'Role ' . $role_name . ' pada user ' . $nama . ' tidak ada di tabel user_role';
							}
						}
						
						if ($data_db_role) {
							$query = $this->db->table('user_role')->insertBatch($data_db_role);
						}
					} else {
						$warning[] = 'Role untuk user ' . $nama . ' belum didefinisikan';
					}
					
					$this->db->transComplete();
					
					if ($this->db->transStatus()) {
						$row_inserted++;
					} else {
						$error_message_row[] =  'Data gagal disimpan';
					}
				}

				if ($error_message_row) {
					$error_message[] = 'Baris ' . $num_row . ': ' . join(', ', $error_message_row);
				}
				
				$num_row += 1;
				
			}
			
			/* if ($data_db) {
				$query = $this->db->table('user')->insertBatch($data_db);
			} */
		}
		$reader->close();
		delete_file($path . $filename);
		
		$message = [];
		$message['ok'] = ['Data berhasil di masukkan ke dalam tabel user sebanyak ' . format_ribuan($row_inserted) . ' baris'
							, 'Jumlah baris diproses: ' . $num_row . ' baris'
						];
		if ($warning) {
			$message['warning'] = $warning; 
		}

		if ($error_message) {
			$message['error'] = $error_message;
		}
		
		$result['status'] = 'upload_excel';
		$result['message'] = $message;
		
		return $result;
	}
	
	public function deleteUser() 
	{
		$id_user = $this->request->getPost('id');
		$sql = 'SELECT * FROM user WHERE id_user = ?';
		$user = $this->db->query($sql, $id_user)->getRowArray();
		if (!$user) {
			return false;
		}
			
		$this->db->transStart();
		$this->db->table('user')->delete(['id_user' => $id_user]);
		$this->db->table('user_role')->delete(['id_user' => $id_user]);
		$delete = $this->db->affectedRows();
		$this->db->transComplete();
		$trans = $this->db->transStatus();
		
		if ($trans) {
			if (!empty($user['avatar'])) {
				delete_file(ROOTPATH . 'public/images/user/' . $user['avatar']);
			}
		} else {
			return false;
		}
		
		return true;
	}

	public function updatePassword() {
		$password_hash = password_hash($this->request->getPost('password_new'), PASSWORD_DEFAULT);
		$update = $this->db->query('UPDATE user SET password = ? 
									WHERE id_user = ? ', [$password_hash, $this->user['id_user']]
								);		
		return $update;
	}
}
?>