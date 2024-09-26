<?php
namespace App\Models\Builtin;

class ModuleRoleModel extends \App\Models\BaseModel
{
	public function getAllModule() {
		$sql = 'SELECT * FROM module';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getModule($id) {
		$sql = 'SELECT * FROM module WHERE id_module = ?';
		$result = $this->db->query($sql, [$id])->getRowArray();

		return $result;
	}
	
	public function getAllRole() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getRoleDetail() {
		$sql = 'SELECT * FROM role_detail';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getAllModuleRole() {
		$sql = 'SELECT module_role.*, nama_role, judul_role FROM module_role LEFT JOIN role USING(id_role)';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getModuleRoleById($id) {
		$sql = 'SELECT * FROM module_role WHERE id_module = ?';
		$result = $this->db->query($sql, [$id])->getResultArray();
		// echo '<pre>'; print_r($result); die;
		return $result;
	}
	
	public function getModuleStatus() {
		$sql = 'SELECT * FROM module
				LEFT JOIN module_status USING(id_module_status)';
				
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function deleteData() {
		$this->db->table('module_role')->delete(['id_module' => $_POST['id_module'], 'id_role' => $_POST['id_role']]);
		return $this->db->affectedRows();
	}
	
	public function saveData() 
	{
		foreach ($_POST as $key => $val) {
			$exp = explode('_', $key);
			if ($exp[0] == 'role') {
				$id_role = $exp[1];
				$data_db[] = ['id_module' => $_POST['id']
								, 'id_role' => $id_role
								, 'read_data' => $_POST['akses_read_data_' . $id_role]
								, 'create_data' => $_POST['akses_create_data_' . $id_role]
								, 'update_data' => $_POST['akses_update_data_' . $id_role]
								, 'delete_data' => $_POST['akses_delete_data_' . $id_role]
							];
			}
		}
		
		// INSERT - UPDATE
		$this->db->transStart();
		$this->db->table('module_role')->delete(['id_module' => $_POST['id']]);
		$this->db->table('module_role')->insertBatch($data_db);
		$this->db->transComplete();
		$result = $this->db->transStatus();
								
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