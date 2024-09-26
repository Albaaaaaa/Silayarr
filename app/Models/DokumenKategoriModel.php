<?php

namespace App\Models;

class DokumenKategoriModel extends \App\Models\BaseModel
{
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getKategori() 
	{
		$result = [];
		
		$sql = 'SELECT * FROM dokumen_kategori
				ORDER BY urut';
		
		$kategori = $this->db->query($sql)->getResultArray();

		foreach ($kategori as $val) 
		{
			$result[$val['id_dokumen_kategori']] = $val;
			$result[$val['id_dokumen_kategori']]['depth'] = 0;			
		}		
		return $result;
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
	
	public function getListModules() {
		
		$sql = 'SELECT * FROM module LEFT JOIN module_status USING(id_module_status)';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getAllRole() {
		$sql = 'SELECT * FROM role';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function getKategoriById($id) {
		$sql = 'SELECT * FROM dokumen_kategori WHERE id_dokumen_kategori = ?';
		$result = $this->db->query($sql, $id)->getRowArray();
		return $result;
	}
	
	public function saveData($id = null) 
	{
		$data_db['nama_kategori'] = $_POST['nama_kategori'];
		$data_db['deskripsi'] = $_POST['deskripsi'];
		
		if (empty($_POST['aktif'])) {
			$data_db['aktif'] = 'N';
		} else {
			$data_db['aktif'] = 'Y';
		}
		
		if ($_POST['use_icon']) {
			$data_db['icon'] = $_POST['icon_class'];
		} else {
			$data_db['icon'] = NULL;
		}
		
		if ($id) {
			$this->db->table('dokumen_kategori')->update($data_db, 'id_dokumen_kategori = ' . $id);
			return $this->db->affectedRows();
		} else {
			$save = $this->db->table('dokumen_kategori')->insert($data_db);
			$insert_id = $this->db->insertID();
			return $insert_id;
		}
	}
	
	public function deleteKategori() {
		$this->db->transStart();
		
		// Delete all parent and child
		$json = json_decode(trim($_POST['kategori_tree']), true);
		$array = $this->buildChild($json);
		$all_child = $this->allChild($_POST['id'], $array);
		if ($all_child) {
			foreach ($all_child as $id_kategori) {
				$this->db->table('dokumen_kategori')->delete(['id_dokumen_kategori' => $id_kategori]);
			}
		} else {
			$this->db->table('dokumen_kategori')->delete(['id_dokumen_kategori' => $_POST['id']]);
		}
		$sql = 'SELECT COUNT(*) as jml FROM dokumen_kategori';
		$result = $this->db->query($sql)->getRowArray();
		if (!$result['jml']) {
			$sql = 'ALTER TABLE dokumen_kategori AUTO_INCREMENT 1';
			$this->db->query($sql);
		}
		$this->db->transComplete();
		return $this->db->transStatus();
	}
	
	public function deleteData() {
		$this->db->table('menu')->delete(['id_menu' => $this->request->getPost('id')]);
		return $this->db->affectedRows();
	}
	
	public function getAllMenu() {
		$result = $this->db->query('SELECT * FROM menu')->getResultArray();
		return $result;
	}
	
	public function updateData() {
		
		$json = json_decode(trim($_POST['data']), true);
		// echo '<pre>'; print_r($json);die;
		$array = $this->buildChild($json);
		
		foreach ($array as $id_parent => $arr) {
			foreach ($arr as $key => $id_menu) {
				$list_menu[$id_menu] = ['id_parent' => $id_parent, 'urut' => ($key + 1)];
			}
		}
		// echo '<pre>'; print_r($list_menu);die;
		$result = $this->getAllMenu();
		$menu_updated = [];
		foreach ($result as $key => $row) 
		{
			$update = [];
			if ($list_menu[$row['id_menu']]['id_parent'] != $row['id_parent']) {
				$id_parent =  $list_menu[$row['id_menu']]['id_parent'] == 0 ? NULL : $list_menu[$row['id_menu']]['id_parent'];
				$update['id_parent'] = $id_parent;
			}
			
			if ($list_menu[$row['id_menu']]['urut'] != $row['urut']) {
				$update['urut'] = $list_menu[$row['id_menu']]['urut'];
			}
			
			if ($update) {
				$result = $this->db->table('menu')->update($update, ['id_menu=' => $row['id_menu']]);
				if ($result) {
					$menu_updated[$row['id_menu']] = $row['id_menu'];
				}
			}
		}
		return $menu_updated;
	}
	
	public function updateKategoriUrut() {
		
		$json = json_decode(trim($_POST['data']), true);
		$array = $this->buildChild($json);

		foreach ($array as $id_parent => $arr) {
			foreach ($arr as $key => $id_dokumen_kategori) {
				$list_menu[$id_dokumen_kategori] = ['id_parent' => $id_parent, 'urut' => ($key + 1)];
			}
		}
	
				
		$sql = 'SELECT * FROM dokumen_kategori';
		$result = $this->db->query($sql)->getResultArray();
		
		$this->db->transStart();
		$menu_updated = [];
		
		foreach ($result as $key => $row) 
		{
			$data_db = [];
			if ($list_menu[$row['id_dokumen_kategori']]['id_parent'] != $row['id_parent']) {
				$id_parent =  $list_menu[$row['id_dokumen_kategori']]['id_parent'] == 0 ? NULL : $list_menu[$row['id_dokumen_kategori']]['id_parent'];
				$data_db['id_parent'] = $id_parent;
			}
			
			if ($list_menu[$row['id_dokumen_kategori']]['urut'] != $row['urut']) {
				$data_db['urut'] = $list_menu[$row['id_dokumen_kategori']]['urut'];
			}
			
			if ($data_db) {
				$result = $this->db->table('dokumen_kategori')->update($data_db, ['id_dokumen_kategori=' => $row['id_dokumen_kategori']]);
				if ($result) {
					$menu_updated[$row['id_dokumen_kategori']] = $row['id_dokumen_kategori'];
				}
			}
		}
		
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