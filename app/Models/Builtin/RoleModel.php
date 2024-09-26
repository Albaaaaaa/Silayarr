<?php
namespace App\Models\Builtin;

class RoleModel extends \App\Models\BaseModel
{
	public function getAllModules() {
		
		$sql = 'SELECT * FROM module';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getModuleStatus() {
		$sql = 'SELECT * FROM module_status';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function listModuleRole() {
		$sql = 'SELECT * FROM role LEFT JOIN module USING(id_module)';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getAllRole() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getListModules() {
		
		$sql = 'SELECT * FROM role_module_permission
					LEFT JOIN module_permission USING(id_module_permission)
					LEFT JOIN module USING(id_module)
					LEFT JOIN module_status USING(id_module_status)
				ORDER BY nama_module';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function checkRoleUsedDefaultPage() {
		$sql = 'SELECT * FROM user WHERE default_page_type = "id_role" AND default_page_id_role = ?';
		return $this->db->query($sql, $this->request->getPost('id'))->getResultArray();
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
	
	public function saveData() 
	{
		$fields = ['nama_role', 'judul_role', 'keterangan', 'id_module'];

		foreach ($fields as $field) {
			$data_db[$field] = $this->request->getPost($field);
		}
		$fields['id_module'] = $this->request->getPost('id_module') ?: 0;
		
		// Save database
		if ($this->request->getPost('id')) {
			$id_role = $this->request->getPost('id');
			$save = $this->db->table('role')->update($data_db, ['id_role' => $id_role]);
		} else {
			$save = $this->db->table('role')->insert($data_db);
			$id_role = $this->db->insertID();
		}
		
		if ($save) {
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil disimpan';
			$result['id_role'] = $id_role;
		} else {
			$result['status'] = 'error';
			$result['message'] = 'Data gagal disimpan';
		}
								
		return $result;
	}
	
	public function deleteData() {
		$this->db->table('role')->delete(['id_role' => $this->request->getPost('id')]);
		return $this->db->affectedRows();
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
		$where = ' WHERE 1 = 1 ';
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
		$sql = 'SELECT COUNT(*) AS jml_data FROM role ' . $where;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT * FROM role 
				' . $where . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();

		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}
?>