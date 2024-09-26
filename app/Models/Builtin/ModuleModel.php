<?php
namespace App\Models\Builtin;

class ModuleModel extends \App\Models\BaseModel
{
	public function getAllModules() {
		
		$sql = 'SELECT * FROM module ORDER BY judul_module';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getAllModuleStatus() {
		
		$sql = 'SELECT * FROM module_status';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getModule($id_module) {
		
		$sql = 'SELECT * FROM module WHERE id_module = ?';
		return $this->db->query($sql, [$id_module])->getRowArray();
	}
	
	public function getAllModuleRole() {
		$sql = 'SELECT * FROM module_role LEFT JOIN module USING(id_module)';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getAllRoles() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	private function getPermissionByIdModule($id) {
		$sql = 'SELECT * FROM module_permission WHERE id_module = ?';
		$result = $this->db->query($sql, (int) $id)->getResultArray();
		return $result;
	}
	
	public function getRoleByIdModule($id) {
		$sql = 'SELECT * FROM role WHERE id_module = ?';
		$result = $this->db->query($sql, (int) $id)->getResultArray();
		return $result;
	}
	
	public function checkModuleUsedDefaultPage() {
		$sql = 'SELECT * FROM user WHERE default_page_type = "id_module" AND default_page_id_module = ?';
		return $this->db->query($sql, $this->request->getPost('id'))->getResultArray();
	}
	
	public function deleteData() {
		
		$this->db->transStart();
		
		$id = $this->request->getPost('id');
		$this->db->table('module')->delete(['id_module' => $id]);
		$module_permission = $this->getPermissionByIdModule( $id );
		$this->db->table('module_permission')->delete(['id_module' => $id]);
		if ($module_permission) {
			foreach ($module_permission as $val) {
				$this->db->table('role_module_permission')->delete(['id_module_permission' => $val['id_module_permission']]);
			}
		}
		
		$role = $this->getRoleByIdModule($id);
		if ($role) {
			foreach ($role as $val) {
				$this->db->table('role')->update(['id_module' => null], ['id_role' => $val['id_role']]);
			}
		}
		
		$this->db->transComplete();
		if ($this->db->transStatus() === false) {
			return false;
		} 
		
		return true;

	}
	
	public function updateStatus() {
		
		$field = $_POST['switch_type'] == 'aktif' ? 'id_module_status' : 'login';
		$this->db->table('module')
					->update( 
						[$field => $_POST['id_result']], 
						['id_module' => $_POST['id_module']]
					);
	}
	
	public function getModules() {
		$sql = 'SELECT * FROM module LEFT JOIN module_status USING(id_module_status) ORDER BY judul_module';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getModulePermission($id_module) {
		$sql = 'SELECT * FROM module_permission WHERE id_module = ?';
		return $this->db->query($sql, $id_module)->getResultArray();
	}
	
	public function getRolePermissionByModule($id_module) {
		$sql = 'SELECT * FROM role_module_permission LEFT JOIN module_permission USING(id_module_permission) WHERE id_module = ?';
		$query = $this->db->query($sql, $id_module)->getResultArray();
		$result = [];
		foreach ($query as $val) {
			$result[$val['id_role']][$val['id_module_permission']] = $val;
		}
		return $result;
	}
	
	public function saveData() 
	{
		$fields = ['nama_module', 'judul_module', 'deskripsi', 'id_module_status', 'login'];

		foreach ($fields as $field) {
			$data_db[$field] = $this->request->getPost($field);
		}
		
		// Save database
		$this->db->transStart();
		
		if ($this->request->getPost('id')) {
			$id_module = $this->request->getPost('id');
			$save = $this->db->table('module')->update($data_db, ['id_module' => $_POST['id']]);
		} else {
			$save = $this->db->table('module')->insert($data_db);
			$id_module = $this->db->insertID();
		}
		
		// Permission
		if (!empty($_POST['generate_permission'])) {
			$_POST['id_module'] = $id_module;
			$model = new \App\Models\Builtin\PermissionModel;
			$model->saveData();
		}
				
		$this->db->transComplete();
		
		if ($this->db->transStatus() === false) {
			$result['status'] = 'error';
			$result['message'] = 'Data gagal disimpan';
		} else {
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil disimpan';
			$result['id_module'] = $id_module;
		}
								
		return $result;
	}
	
	// EDIT
	public function getRole() {
		$id_role = $this->request->getGet('id');
		$sql = 'SELECT * FROM role WHERE id_role = ?';
		$result = $this->db->query($sql, [$id_role])->getRowArray();
		if (!$result)
			$result = [];
		return $result;
	}
	
	public function countAllData() {
		$sql = 'SELECT COUNT(*) AS jml FROM module';
		$result = $this->db->query($sql)->getRow();
		return $result->jml;
	}
	
	public function getListData($where) {

		$columns = $this->request->getPost('columns');

		// Search
		$search_all = @$this->request->getPost('search')['value'];
		if ($search_all) {
			foreach ($columns as $val) 
			{
				if (strpos($val['data'], 'ignore') !== false)
					continue;
				
				$where_col[] = $val['data'] . ' LIKE "%' . $search_all . '%"';
			}
			 $where .= ' AND (' . join(' OR ', $where_col) . ') ';
		}
		
		// Order		
		$order_data = $this->request->getPost('order');
		$order = '';
		if (strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = ' ORDER BY ' . $order_by;
		}

		// Query Total Filtered
		$sql = 'SELECT COUNT(*) AS jml_data FROM module ' . $where;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT * FROM module 
				' . $where . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();

		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
	
}
?>