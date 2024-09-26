<?php
namespace App\Models\Builtin;

class RolePermissionModel extends \App\Models\BaseModel
{
		
	public function deletePermission($id_role, $id_permission) {
		$delete = $this->db->table('role_module_permission')->delete(['id_role' => $id_role, 'id_module_permission' => $id_permission]);
		return $delete;
	}
	
	public function deleteRolePermissionByModule($id_role, $id_module) {
		$sql = 'DELETE FROM role_module_permission 
					WHERE id_role = ? AND id_module_permission 
					IN (SELECT id_module_permission FROM module_permission WHERE id_module = ?)';
		$delete = $this->db->query($sql, [$id_role, $id_module]);
		return $delete;
	}
	
	public function getRolePermissionByIdRole($id) 
	{
		$sql = 'SELECT * FROM role_module_permission WHERE id_role = ?';
		$query = $this->db->query($sql, $id)->getResultArray();
		
		$result = [];
		foreach ($query as $val) {
			$result[$val['id_module_permission']] = $val;
		}

		return $result;
	}
	
	public function getAllPermissionByModule() 
	{
		$sql = 'SELECT * FROM module_permission LEFT JOIN module USING(id_module) ORDER BY judul_module';
		$module_permission = $this->db->query($sql)->getResultArray();
				
		foreach ($module_permission as $val) {
			$result[$val['id_module']][$val['id_module_permission']] = $val;
		}

		return $result;
	}
	
	public function getAllModules() {
		
		$sql = 'SELECT * FROM module ORDER BY judul_module';
		
		$query = $this->db->query($sql)->getResultArray();
		foreach ($query as $val) {
			$result[$val['id_module']] = $val;
		}
		return $result;
	}
	
	public function getAllModulesById($id_module = '') {

		if ($id_module) {
			$id_module = ' WHERE id_module = ' . $_GET['id_module'];
		}
		$sql = 'SELECT * FROM module ' . $id_module . ' ORDER BY judul_module';
		
		$query = $this->db->query($sql)->getResultArray();
		foreach ($query as $val) {
			$result[$val['id_module']] = $val;
		}
		return $result;
	}
	
	public function getRoleById($id) {
		$sql = 'SELECT * FROM role WHERE id_role = ?';
		$result = $this->db->query($sql, $id)->getRowArray();
		return $result;
	}
	
	public function getAllRole() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getAllRolePermission() {
		$sql = 'SELECT * FROM role_module_permission 
					LEFT JOIN module_permission USING(id_module_permission) 
					LEFT JOIN module USING(id_module)';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function saveData() 
	{
		$this->db->transStart();
		
		$table = $this->db->table('role_module_permission');
		// Via ajax
		if (!empty($_POST['id_module']) && $_POST['id_module'] != 'semua_module') {
			// echo '<pre>'; print_r($_POST); die;
			$sql = 'DELETE FROM role_module_permission 
						WHERE id_role = ? AND id_module_permission 
								IN (SELECT id_module_permission FROM module_permission WHERE id_module = ?)';
								
			$this->db->query($sql, [$_POST['id'], $_POST['id_module']]);
		} else {
			$table->delete(['id_role' => $_POST['id']]);
		}
		
		if (key_exists('permission', $_POST)) {
			foreach ($_POST['permission'] as $val) {
				$data_db[] = ['id_role' => $_POST['id'], 'id_module_permission' => $val];
			}
		}
		$table->insertBatch($data_db);
		
		$this->db->transComplete();
		if ($this->db->transStatus() == false) {
			return false;
		}
		
		return true;
	}
	
	public function hasAllPermission($id_role) {
		$sql = '
				SELECT COUNT(*) AS jml FROM module_permission 
				LEFT JOIN module USING(id_module)
				LEFT JOIN 
				
				( SELECT * FROM role_module_permission WHERE id_role = ' . $id_role . ' )
				AS tabel USING (id_module_permission)
				WHERE id_role IS NULL';
				
		$data = $this->db->query($sql)->getRowArray();
		return $data['jml'] ? false : true;
	}
	
	public function deleteAllPermission() {
		return $this->db->table('role_module_permission')->delete(['id_role' => $_POST['id_role']]);
	}
	
	public function assignPermission() 
	{
		if ($_POST['assign'] == 'Y') {
			return $this->db->table('role_module_permission')->insert(['id_role' => $_POST['id_role'], 'id_module_permission' => $_POST['id_module_permission']]);
		}
		
		return $this->db->table('role_module_permission')->delete(['id_role' => $_POST['id_role'], 'id_module_permission' => $_POST['id_module_permission']]);
	}
	
	public function assignAllPermission() 
	{
		if ($_POST['assign_all'] == 'Y') {
			$sql = 'SELECT * FROM module_permission';
			$data = $this->db->query($sql)->getResultArray();
			foreach ($data as $val) {
				$data_db[] = ['id_role' => $_POST['id_role'], 'id_module_permission' => $val['id_module_permission']];
			}
			$this->db->transStart();
			$this->db->table('role_module_permission')->delete(['id_role' => $_POST['id_role']]);
			$this->db->table('role_module_permission')->insertBatch($data_db);
			$this->db->transComplete();
			return $this->db->transStatus();
		}
		
		return $this->db->table('role_module_permission')->delete(['id_role' => $_POST['id_role']]);
	}
	
	
	public function countAllDataPermission() {
		$sql = 'SELECT COUNT(*) AS jml FROM module_permission';
		$result = $this->db->query($sql)->getRow();
		return $result->jml;
	}
	
	public function getListDataPermission() {

		$columns = $this->request->getPost('columns');

		// Search
		$search_all = @$this->request->getPost('search')['value'];
		$where = ' WHERE 1=1 ';
		if ($search_all) {
			foreach ($columns as $val) {
				
				if (strpos($val['data'], 'ignore_search') !== false) 
					continue;
				
				if (strpos($val['data'], 'ignore') !== false)
					continue;
				
				$where_col[] = $val['data'] . ' LIKE "%' . $search_all . '%"';
			}
			 $where .= ' AND (' . join(' OR ', $where_col) . ') ';
		}
		
		// Order		
		$order_data = $this->request->getPost('order');
		$order = '';
		if (@strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = ' ORDER BY ' . $order_by;
		}

		// Query Total Filtered
		$sql = 'SELECT COUNT(*) AS jml_data FROM module_permission
				LEFT JOIN module USING(id_module)
				LEFT JOIN 
				( SELECT * FROM role_module_permission WHERE id_role = ' . $_GET['id'] . ' )
				AS tabel USING (id_module_permission) ' . $where;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT module_permission.*, nama_module, judul_module, tabel.id_role FROM module_permission 
				LEFT JOIN module USING(id_module)
				LEFT JOIN 
				( SELECT * FROM role_module_permission WHERE id_role = ' . $_GET['id'] . ' )
				AS tabel USING (id_module_permission)
				' . $where . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();
				
		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
	
	public function countAllData() {
		$sql = 'SELECT COUNT(*) AS jml FROM role';
		$result = $this->db->query($sql)->getRow();
		return $result->jml;
	}
	
	public function getListData() {

		$columns = $this->request->getPost('columns');

		// Search
		$search_all = @$this->request->getPost('search')['value'];
		$where = ' WHERE 1=1 ';
		if ($search_all) {
			foreach ($columns as $val) {
				
				if (strpos($val['data'], 'ignore_search') !== false) 
					continue;
				
				if (strpos($val['data'], 'ignore') !== false)
					continue;
				
				$where_col[] = $val['data'] . ' LIKE "%' . $search_all . '%"';
			}
			 $where .= ' AND (' . join(' OR ', $where_col) . ') ';
		}
		
		// Order		
		$order_data = $this->request->getPost('order');
		$order = '';
		if (@strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = ' ORDER BY ' . $order_by;
		}

		// Query Total Filtered
		$sql = 'SELECT COUNT(*) AS jml_data FROM role ' . $where;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT role.*, jml_module, COUNT(id_module_permission) AS jml_permission 
				FROM role
				LEFT JOIN role_module_permission USING(id_role)
				LEFT JOIN module_permission USING(id_module_permission)
				LEFT JOIN (
					SELECT id_role, COUNT(id_module) AS jml_module
					FROM (SELECT id_role, module_permission.id_module AS id_module
						FROM role
						LEFT JOIN role_module_permission USING(id_role)
						LEFT JOIN module_permission USING(id_module_permission)
						GROUP BY id_role, module_permission.id_module
					) AS tabel
					GROUP BY id_role
				) AS tabel USING(id_role)
				' . $where . '
				GROUP BY id_role
				' . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();
				
		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}
?>