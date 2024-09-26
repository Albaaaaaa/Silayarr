<?php
/**
*	App Name	: Aplikasi Arsip Digital Jagowebdev	
*	Author		: Agus Prawoto Hadi
*	Website		: https://jagowebdev.com
*	Year		: 2023
*/

namespace App\Models\Builtin;

class MenuModel extends \App\Models\BaseModel
{
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getMenuDb($aktif = 'all', $show_all = false) {
		
		global $db;
		global $app_module;
		
		$result = [];
		$nama_module = $app_module['nama_module'];
		
		$where = ' ';
		$where_aktif = '';
		if ($aktif != 'all') {
			$where_aktif = ' AND aktif = '.$aktif;
		}
		
		$role = '';
		if (!$show_all) {
			$role = ' AND id_role = ' . $_SESSION['user']['id_role'];
		}
		
		$sql = 'SELECT * FROM menu 
					LEFT JOIN menu_role USING (id_menu)
					LEFT JOIN module USING (id_module)
				WHERE 1 = 1 ' . $role
					. $where_aktif.' 
				ORDER BY urut';
		
		$this->db->query($sql)->resultArray();
		
		$current_id = '';
		foreach ($query->getResult('array') as $row) {
			$result[$row['id_menu']] = $row;
			$result[$row['id_menu']]['highlight'] = 0;
			$result[$row['id_menu']]['depth'] = 0;

			if ($nama_module == $row['nama_module']) {
				
				$current_id = $row['id_menu'];
				$result[$row['id_menu']]['highlight'] = 1;
			}
		}
		
		if ($current_id) {
			menu_current($result, $current_id);
		}
		
		return $result;
	}
	
	public function getMenuByKategori($id_menu_kategori) {
		
		$result = [];
		if ($id_menu_kategori) {
			$where_id_menu_kategori = 'id_menu_kategori = '. $id_menu_kategori;
		} else {
			$where_id_menu_kategori = '( id_menu_kategori = 0 OR id_menu_kategori = "" OR id_menu_kategori IS NULL )';
		}
		
		$sql = 'SELECT * FROM menu 
					LEFT JOIN menu_role USING (id_menu)
					LEFT JOIN module USING (id_module)
				WHERE 1 = 1 
				AND ' . $where_id_menu_kategori . '
				ORDER BY urut';
		
		$query = $this->db->query($sql)->getResultArray();

		foreach ($query as $row) {
			$result[$row['id_menu']] = $row;
			$result[$row['id_menu']]['highlight'] = 0;
			$result[$row['id_menu']]['depth'] = 0;
		}
				
		return $result;
	}
	
	public function updateKategoriUrut($list_kategori) {
		$this->db->transStart();
		$urut = 1;
		foreach ($list_kategori as $id_kategori) {
			$this->db->table('menu_kategori')->update(['urut' => $urut], ['id_menu_kategori' => $id_kategori]); 
			$urut++;
		}
		$this->db->transComplete();
		return $this->db->transStatus();
	}
	
	public function updateMenuUrut() {
		
		$json = json_decode(trim($_POST['data']), true);
		$array = $this->buildChild($json);
		
		foreach ($array as $id_parent => $arr) {
			foreach ($arr as $key => $id_menu) {
				$list_menu[$id_menu] = ['id_parent' => $id_parent, 'urut' => ($key + 1)];
			}
		}
	
		$id_menu_kategori = trim($_POST['id_menu_kategori']);
		if (empty($id_menu_kategori)) {
			$where_id_menu_kategori = ' id_menu_kategori = "" OR id_menu_kategori IS NULL';
		} else {
			$where_id_menu_kategori = ' id_menu_kategori = ' . $id_menu_kategori;
		}
		
		$sql = 'SELECT * FROM menu WHERE ' . $where_id_menu_kategori;
		
		$result = $this->db->query($sql)->getResultArray();
		
		$this->db->transStart();
		$menu_updated = [];
		
		foreach ($result as $key => $row) 
		{
			$data_db = [];
			if ($list_menu[$row['id_menu']]['id_parent'] != $row['id_parent']) {
				$id_parent =  $list_menu[$row['id_menu']]['id_parent'] == 0 ? NULL : $list_menu[$row['id_menu']]['id_parent'];
				$data_db['id_parent'] = $id_parent;
			}
			
			if ($list_menu[$row['id_menu']]['urut'] != $row['urut']) {
				$data_db['urut'] = $list_menu[$row['id_menu']]['urut'];
			}
			
			if ($data_db) {
				$result = $this->db->table('menu')->update($data_db, ['id_menu=' => $row['id_menu']]);
				if ($result) {
					$menu_updated[$row['id_menu']] = $row['id_menu'];
				}
			}
		}
		
		$this->db->transComplete();
		
		return $this->db->transStatus();
	}
	
	public function getListModules() {
		
		$sql = 'SELECT * FROM module LEFT JOIN module_status USING(id_module_status) ORDER BY nama_module';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getKategori() {
		
		$sql = 'SELECT * FROM menu_kategori ORDER BY urut';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getKategoriById($id) {
		
		$sql = 'SELECT * FROM menu_kategori WHERE id_menu_kategori = ?';
		return $this->db->query($sql, $id)->getRowArray();
	}
	
	public function getAllRole() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getMenuById($id) {
		$sql = 'SELECT menu.*, GROUP_CONCAT(id_role) AS id_role
				FROM menu 
				LEFT JOIN menu_role USING(id_menu) 
				WHERE id_menu = ? GROUP BY id_menu';
		$result = $this->db->query($sql, $id)->getRowArray();
		return $result;
	}
	
	public function saveMenu($id = null) 
	{
		$data_db['nama_menu'] = $_POST['nama_menu'];
		$data_db['id_module'] = $_POST['id_module'] ?: NULL;
		$data_db['url'] = $_POST['url'];
		
		if (trim($_POST['id_menu_kategori']) == '') {
			$id_menu_kategori = NULL;
		} else {
			$id_menu_kategori = $_POST['id_menu_kategori'];
		}
		$data_db['id_menu_kategori'] = $id_menu_kategori;
			
		if (empty($_POST['aktif'])) {
			$data_db['aktif'] = 0;
		} else {
			$data_db['aktif'] = 1;
		}
		
		if ($_POST['use_icon']) {
			$data_db['class'] = $_POST['icon_class'];
		} else {
			$data_db['class'] = NULL;
		}
		
		if ($id) {
			$this->db->transStart();
			
			// Cek ganti group
			$sql = 'SELECT id_menu_kategori FROM menu WHERE id_menu = ?';
			$query = $this->db->query($sql, $_POST['id'])->getRowArray();
			if ($query['id_menu_kategori'] != $id_menu_kategori) {
				$data_db['id_parent'] = NULL;
			}
			
			$this->db->table('menu')->update($data_db, ['id_menu' => $_POST['id']]);
						
			// Update group to all child
			$json = json_decode(trim($_POST['menu_tree']), true);
			$array = $this->buildChild($json);
			$all_child = $this->allChild($_POST['id'], $array);
			foreach ($all_child as $val) {
				$this->db->table('menu')->update(['id_menu_kategori' => $id_menu_kategori], ['id_menu' => $val]);
			}
			
			// Update role
			$data_db = [];
			foreach ($_POST['id_role'] as $val) {
				$data_db[] = ['id_menu' => $_POST['id'], 'id_role' => $val];
			}
			$this->db->table('menu_role')->delete(['id_menu' => $_POST['id']]);
			$this->db->table('menu_role')->insertBatch($data_db);
			
			$this->db->transComplete();
			return $this->db->transStatus();
	
		} else {
				
			$save = $this->db->table('menu')->insert($data_db);
			$insert_id = $this->db->insertID();
			if (!empty($_POST['id_role'])) {
				$data_db = [];
				foreach ($_POST['id_role'] as $val) {
					$data_db[] = ['id_menu' => $insert_id, 'id_role' => $val];
				}
				$this->db->table('menu_role')->insertBatch($data_db);
			}
			return $insert_id;
		}
	}
	
	public function deleteMenu() {
		$this->db->transStart();
		
		// Delete all parent and child
		$json = json_decode(trim($_POST['menu_tree']), true);
		$array = $this->buildChild($json);
		$all_child = $this->allChild($_POST['id'], $array);
		if ($all_child) {
			foreach ($all_child as $id_menu) {
				$this->db->table('menu')->delete(['id_menu' => $id_menu]);
			}
		} else {
			$this->db->table('menu')->delete(['id_menu' => $_POST['id']]);
		}
		$this->db->transComplete();
		return $this->db->transStatus();
	}
	
	public function getAllMenu() {
		$result = $this->db->query('SELECT * FROM menu')->getResultArray();
		return $result;
	}
	
	public function saveKategori($data) {
		
		$data_db['nama_kategori'] = $data['nama_kategori'];
		$data_db['deskripsi'] = $data['deskripsi'];
		$data_db['aktif'] = $data['aktif'];
		$data_db['show_title'] = $data['show_title'];

		if (@$data['id']) {
			$save = $this->db->table('menu_kategori')->update($data_db, ['id_menu_kategori' => $data['id']]);
		} else {
			$sql = 'SELECT MAX(urut) AS urut FROM menu_kategori';
			$last_urut = $this->db->query($sql)->getRowArray();
			$data_db['urut'] = $last_urut['urut'] + 1;
			$save = $this->db->table('menu_kategori')->insert($data_db);
		}
		
		if ($save) {
			$message['status'] = 'ok';
			$message['message'] = 'Menu berhasil diupdate';
			$message['id_kategori'] = $this->db->insertID();
		} else {
			$message['status'] = 'warning';
			$message['message'] = 'Tidak ada menu yang diupdate';
		}
		return $message;
	}
	
	public function deleteKategoriById($id) 
	{
		$this->db->transStart();
		$this->db->table('menu_kategori')->delete(['id_menu_kategori' => $id]);
		$this->db->table('menu')->update(['id_menu_kategori' => null], ['id_menu_kategori' => $id]);
		$this->db->transComplete();
		
		return $this->db->transStatus();
	}
	
	private function buildChild($arr, $parent=0, &$list=[]) 
	{
		foreach ($arr as $key => $val) 
		{
			$list[$parent][] = $val['id'];

			if (key_exists('children', $val))
			{ 
				$this->buildChild($val['children'], $val['id'], $list);
			}
		}
		
		return $list;
	}
	
	private function allChild($id, $list, &$result = []) 
	{
		if (!key_exists($id, $list)) {
			return $result;
		}
		
		$result[$id] = $id;
		foreach ($list[$id] as $val) 
		{
			$result[$val] = $val;
			if (key_exists($val, $list)) {
				$this->allChild($val, $list, $result);
			}
		}
		return $result;
	}
}
?>