<?php

namespace App\Models;

class DokumenModel extends \App\Models\BaseModel
{	
	protected $table = 'dokumen';
	public function __construct() {
		parent::__construct();
	}
	
	public function getFiles($where) 
	{
		$sql 	= 'SELECT * 
					FROM dokumen 
					LEFT JOIN dokumen_file_picker USING(id_dokumen) 
					LEFT JOIN file_picker USING(id_file_picker) ' . $where;
        $result = $this->db->query($sql)->getResultArray();
		return $result;
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
	
	public function getKategoriUsed() {
		$sql = 'SELECT * FROM dokumen LEFT JOIN dokumen_kategori USING(id_dokumen_kategori) GROUP BY id_dokumen_kategori';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}

	public function getDokumenByIds($ids)
	{
		return $this->table('dokumen')
					->whereIn('id_dokumen', $ids)
					->findAll();
	}
	
	public function getDokumenById($where) 
	{
		$sql 	= 'SELECT *
					FROM dokumen ' . $where;
		$result = $this->db->query($sql)->getRowArray();
		if ($result) {
		
			$sql 	= 'SELECT *
						FROM dokumen 
						LEFT JOIN dokumen_file_picker USING(id_dokumen) 
						LEFT JOIN file_picker USING(id_file_picker)' . 				
						$where;
			$result['files'] = $this->db->query($sql)->getResultArray();
		}
		return $result;
	}
	
	public function deleteData($id) 
	{
		$this->db->transStart();
		$this->db->table('dokumen')->delete(['id_dokumen' => $id]);
		$this->db->table('dokumen_file_picker')->delete(['id_dokumen' => $id]);
		$this->db->transComplete();
		if ($this->db->transStatus()) {
			$message['status'] = 'ok';
			$message['message'] = 'Data berhasil dihapus';
		} else {
			$message['status'] = 'error';
			$message['message'] = 'Data gagal dihapus';
		}
		
		return $message;
	}
	
	public function deleteAllData() {
		
		$list_table = [
						'dokumen',
						'dokumen_download_log',
						'dokumen_file_picker',
					];
					
		try {
			$this->db->transException(true)->transStart();
			
			foreach ($list_table as $table) 
			{
				$this->db->table($table)->emptyTable();
				$this->resetAutoIncrement($table);
			}
			
			$this->db->transComplete();
			
			if ($this->db->transStatus() == true)
				return ['status' => 'ok', 'message' => 'Data berhasil dihapus'];
			
			return ['status' => 'error', 'message' => 'Database error'];
			
		} catch (DatabaseException $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}
	}
	
	
	public function saveData() 
	{
		$result = [];
		$id_dokumen = '';
		if (!empty($_POST['submit'])) {
			$data_db['no_berkas'] = $_POST['no_berkas'];
			$data_db['kode_klasifikasi'] = $_POST['kode_klasifikasi'];
			$data_db['no_item'] = $_POST['no_item'];
			$data_db['tingkat_perkembangan'] = $_POST['tingkat_perkembangan'] === 'other' ? $_POST['tingkat_perkembangan_other'] : $_POST['tingkat_perkembangan'];
			$data_db['jumlah_berkas'] = $_POST['jumlah_berkas'];
			$data_db['kondisi_berkas'] = $_POST['kondisi_berkas'] === 'other' ? $_POST['kondisi_berkas_other'] : $_POST['kondisi_berkas'];
			$data_db['judul_dokumen'] = $_POST['judul_dokumen'];
			$data_db['deskripsi_dokumen'] = $_POST['deskripsi_dokumen'];
			$data_db['opd'] = $_POST['opd'];
			$data_db['id_dokumen_kategori'] = $_POST['id_dokumen_kategori'];
			$exp = explode('-', $_POST['tgl_upload']);
			$data_db['inaktif'] = $_POST['inaktif'];
			$data_db['nasib_akhir'] = $_POST['nasib_akhir'] === 'other' ? $_POST['nasib_akhir_other'] : $_POST['nasib_akhir'];
			$data_db['tgl_upload'] = $exp[2] . '-' . $exp[1] . '-' . $exp[0] . ' ' . date('H:i:s');
			
			

			
			$this->db->table('dokumen_file_picker')->delete(['id_dokumen' => $_POST['id']]);
			if (!empty($_POST['id'])) {
				$data_db['id_user_update'] = $_SESSION['user']['id_user'];
				$query = $this->db->table('dokumen')->update($data_db, ['id_dokumen' => $_POST['id']]);
				$id_dokumen = $_POST['id'];
			} else {
				$data_db['id_user_input'] = $_SESSION['user']['id_user'];
				$query = $this->db->table('dokumen')->insert($data_db);
				$id_dokumen = $this->db->insertID();
			}
			
			$data_db_file = [];
			foreach ($_POST['id_file_picker'] as $val) {
				$data_db_file[] = ['id_dokumen'=> $id_dokumen, 'id_file_picker' => $val];
			}
			$query = $this->db->table('dokumen_file_picker')->insertBatch($data_db_file);
			
			if ($query) {
				$result['status'] = 'ok';
				$result['message'] = 'Data berhasil disimpan';
			} else {
				$result['status'] = 'error';
				$result['message'] = 'Data gagal disimpan';
			}
			$result['id'] = $id_dokumen;
		}
		return $result;
	}
	
	public function saveDownloadLog($file) {
		
		$data_db['id_user'] = $_SESSION['user']['id_user'];
		$data_db['id_dokumen'] = $file['id_dokumen'];
		$data_db['judul_file'] = $file['title'];
		$data_db['id_file_picker'] = $file['id_file_picker'];
		$data_db['filename'] = $file['nama_file'];
		$data_db['tgl_download'] = date('Y-m-d H:i:s');
		
		$this->db->table('dokumen_download_log')->insert($data_db);
	}
	
	public function countAllData($where) {
		
		if (!empty($_GET['id_dokumen_kategori'])) {
			$where .= ' AND id_dokumen_kategori= ' . filter_var($_GET['id_dokumen_kategori'], FILTER_VALIDATE_INT);
		}
		
		$sql = 'SELECT COUNT(*) AS jml FROM dokumen' . $where;
		$result = $this->db->query($sql)->getRow();
		return $result->jml;
	}
	
	public function getListData($where) {

		$columns = $this->request->getPost('columns');
		
		if (!empty($_GET['id_dokumen_kategori'])) {
			$where .= ' AND id_dokumen_kategori= ' . filter_var($_GET['id_dokumen_kategori'], FILTER_VALIDATE_INT);
		}
		
		// Search
		$search_all = @$this->request->getPost('search')['value'];
		if ($search_all) {

			foreach ($columns as $val) {
				
				if (strpos($val['data'], 'ignore_search') !== false) 
					continue;
				
				if (strpos($val['data'], 'ignore') !== false)
					continue;
				
				if ($val['data'] == 'tgl_upload') {
					$val['data'] = 'dokumen.tgl_upload';
				}

				$where_col[] = $val['data'] . ' LIKE "%' . $search_all . '%"';
			}
			 $where .= ' AND (' . join(' OR ', $where_col) . ') ';
		}
		
		// Order		
		$order_data = $this->request->getPost('order');
		$order = '';
		if (strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore_search') === false) {
			if ($columns[$order_data[0]['column']]['data'] == 'tgl_upload') {
				$columns[$order_data[0]['column']]['data'] = 'dokumen.tgl_upload';
			}
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = ' ORDER BY ' . $order_by;
		}

		// Query Total Filtered
		$sql = 'SELECT COUNT(*) AS jml_data
				FROM (SELECT id_dokumen 
					FROM dokumen
					LEFT JOIN dokumen_file_picker USING(id_dokumen)
					LEFT JOIN file_picker USING(id_file_picker) ' . $where . ' GROUP BY id_dokumen 
				) AS tabel';
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		// echo $sql; die;
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT *, GROUP_CONCAT(title) as judul_file
				, GROUP_CONCAT(id_file_picker) as id_file_picker
				, GROUP_CONCAT(nama_file) as nama_file
				, GROUP_CONCAT(mime_type) as mime_type
				, GROUP_CONCAT(nama_file) as nama_file
				, dokumen.tgl_upload AS tgl_upload
				FROM dokumen
				LEFT JOIN dokumen_file_picker USING(id_dokumen)
				LEFT JOIN file_picker USING(id_file_picker)
				' . $where . ' GROUP BY id_dokumen' . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();
				
		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}
?>