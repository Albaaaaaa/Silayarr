<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Models\Builtin;

class MenuRoleModel extends \App\Models\BaseModel
{
	public function getAllMenu() {
		$sql = 'SELECT * FROM menu';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getAllRole() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getAllMenuRole() {
		$sql = 'SELECT * FROM menu_role LEFT JOIN role USING(id_role)';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getMenuRoleById($id) {
		$sql = 'SELECT * FROM menu_role WHERE id_menu = ?';
		$result = $this->db->query($sql, $id)->getResultArray();
		return $result;
	}
	
	public function deleteData() {
		$this->db->table('menu_role')->delete(['id_menu' => $this->request->getPost('id_menu'), 'id_role' => $_POST['id_role']]);
		return $this->db->affectedRows();
	}
	
	public function saveData() 
	{
		// Find all parent
		$menu_parent = $this->allParents($_POST['id_menu']);
		
		$insert_parent = [];
		if ($menu_parent && !empty($_POST['id_role'])) 
		{
			// Cek apakah parent telah diassign di role yang tercentang, jika belum buat insert nya
			foreach($menu_parent as $id_menu_parent) {
				foreach ($_POST['id_role'] as $id_role) {
					$sql = 'SELECT * FROM menu_role WHERE id_menu = ? AND id_role = ?';
					$data = [$id_menu_parent, $id_role];
					$query = $this->db->query($sql, $data)->getResultArray();
					if (!$query) {
						$insert_parent[] = ['id_menu' => $id_menu_parent, 'id_role' => $id_role];
					}
				}
			}
		}

		// INSERT - DELETE
		$this->db->transStart();
		
		// Insert Parent
		if ($insert_parent) {
			$this->db->table('menu_role')->insertBatch($insert_parent);
		}
		
		// Hapus role pada menu
		$this->db->table('menu_role')->delete(['id_menu' => $_POST['id_menu']]);
		
		// Insert role yang tercentang
		if (!empty($_POST['id_role'])) {
			$data_db = [];
			foreach ($_POST['id_role'] as $id_role) {
				$data_db[] = ['id_menu' => $_POST['id_menu'], 'id_role' => $id_role];
			}
			$this->db->table('menu_role')->insertBatch($data_db);
		}

		$this->db->transComplete();
		$trans = $this->db->transStatus();
		
		if ($trans) {
			$result['status'] = 'ok';
			$result['insert_parent'] = $insert_parent;
		} else {
			$result['status'] = 'error';
		}
		return $result;
	}
	
	private function allParents($id_menu, &$list_parent = []) {
		
		$query = $this->db->query('SELECT * FROM menu')->getResultArray();
		foreach($query as $val) {
			$menu[$val['id_menu']] = $val;
		}
		
		if (key_exists($id_menu, $menu)) {
			$parent = $menu[$id_menu]['id_parent'];
			if ($parent) {
				$list_parent[$parent] = &$parent;
				$this->allParents($parent, $list_parent);
			}
		}
		
		return $list_parent;
	}
	
	public function countAllData() {
		$sql = 'SELECT COUNT(*) AS jml FROM menu';
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
		$sql = 'SELECT COUNT(*) AS jml_data FROM menu ' . $where;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT * FROM menu 
				' . $where . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();

		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}
?>