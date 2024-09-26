<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Models\Builtin;

class UserRoleModel extends \App\Models\BaseModel
{
	public function getAllRole() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getUserRole() {
		$sql = 'SELECT * FROM user_role LEFT JOIN role USING(id_role)';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getUserRoleByID($id) {
		$sql = 'SELECT * FROM user_role WHERE id_user = ?';
		$result = $this->db->query($sql, $id)->getResultArray();
		return $result;
	}
	
	public function getAllUser() {
		$sql = 'SELECT * FROM user';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function deleteData() {
		$this->db->table('user_role')->delete(['id_user' => $_POST['id_user'], 'id_role' => $_POST['id_role']]);
		return $this->db->affectedRows();
	}
	
	public function saveData() 
	{
		$this->db->transStart();
		$this->db->table('user_role')->delete(['id_user' => $_POST['id_user']]);
		
		if (!empty($_POST['id_role'])) {
			foreach ($_POST['id_role'] as $key => $id_role) {
				$insert[] = ['id_user' => $_POST['id_user'], 'id_role' => $id_role];
			}
			$this->db->table('user_role')->insertBatch($insert);
		}
		$this->db->transComplete();
		$result = $this->db->transStatus();
		
		return $result;
	}
	
	public function countAllData() {
		$sql = 'SELECT COUNT(*) AS jml FROM user';
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
		$sql = 'SELECT COUNT(*) AS jml_data FROM user ' . $where;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT * FROM user 
				' . $where . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();

		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}
?>