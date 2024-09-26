<?php
namespace App\Models\Builtin;

class SettingRegistrasiModel extends \App\Models\BaseModel
{
	public function getRole() {
		$sql = 'SELECT * FROM role';
		$query = $this->db->query($sql)->getResultArray();
		return $query;
	}
	
	public function getSettingRegistrasi() {
		$sql = 'SELECT * FROM setting WHERE type="register"';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function getListModules() {
		
		$sql = 'SELECT * FROM module LEFT JOIN module_status USING(id_module_status) ORDER BY nama_module';
		return $this->db->query($sql)->getResultArray();
	}
	
	public function saveData() 
	{
		$data_db[] = ['type' => 'register', 'param' => 'enable', 'value' => $_POST['enable'] ];
		$data_db[] = ['type' => 'register', 'param' => 'metode_aktivasi', 'value' => $_POST['metode_aktivasi'] ];
		$data_db[] = ['type' => 'register', 'param' => 'id_role', 'value' => $_POST['id_role'] ];
		$data_db[] = ['type' => 'register', 'param' => 'default_page_type', 'value' => $_POST['option_default_page'] ];
		$data_db[] = ['type' => 'register', 'param' => 'default_page_id_role', 'value' => $_POST['id_role'] ];
		$data_db[] = ['type' => 'register', 'param' => 'default_page_id_module', 'value' => $_POST['default_page_id_module'] ];
		$data_db[] = ['type' => 'register', 'param' => 'default_page_url', 'value' => $_POST['default_page_url'] ];
		
		$this->db->transStart();
		$this->db->table('setting')->delete(['type' => 'register']);
		$this->db->table('setting')->insertBatch($data_db);
		$query = $this->db->transComplete();
		$query_result = $this->db->transStatus();
		
		if ($query_result) {
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil disimpan';
		} else {
			$result['status'] = 'error';
			$result['message'] = 'Data gagal disimpan';
		}
		
		return $result;
	}
}
?>