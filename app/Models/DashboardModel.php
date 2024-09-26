<?php
namespace App\Models;

class DashboardModel extends \App\Models\BaseModel
{
	public function __construct() {
		parent::__construct();
	}
	
	public function getJumlahDokumen() {
		$sql = 'SELECT COUNT(*) AS jml FROM dokumen';
		$data = $this->db->query($sql)->getRowArray();
		return $data['jml'];
	}
	
	public function getJumlahFile() {
		$sql = 'SELECT COUNT(*) As jml FROM file_picker';
		$data = $this->db->query($sql)->getRowArray();
		return $data['jml'];
	}
	
	public function getTotalFileSize() {
		$sql = 'SELECT SUM(size) As total FROM file_picker';
		$data = $this->db->query($sql)->getRowArray();
		return $data['total'];
	}
	
	public function getJumlahKategori() {
		$sql = 'SELECT COUNT(*) As jml FROM (
					SELECT id_dokumen FROM dokumen GROUP BY id_dokumen_kategori
				) AS tabel';
		$data = $this->db->query($sql)->getRowArray();
		return $data['jml'];
	}
	
	public function getKategoriUsed() {
		$sql = 'SELECT * FROM dokumen LEFT JOIN dokumen_kategori USING(id_dokumen_kategori) GROUP BY id_dokumen_kategori';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function jumlahDokumenPerKategori() {
		$sql = 'SELECT * FROM (SELECT id_dokumen_kategori, COUNT(*) AS jml FROM dokumen
				LEFT JOIN dokumen_kategori USING(id_dokumen_kategori)
				GROUP BY id_dokumen_kategori) AS tabel
				LEFT JOIN dokumen_kategori USING(id_dokumen_kategori)';
		$result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function countAllData($where) {
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
				FROM (SELECT id_dokumen FROM dokumen
				LEFT JOIN dokumen_file_picker USING(id_dokumen)
				LEFT JOIN file_picker USING(id_file_picker) ' . $where . ' GROUP BY id_dokumen) AS tabel';

		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT *, GROUP_CONCAT(title) as judul_file
				, GROUP_CONCAT(id_file_picker) as id_file_picker
				, GROUP_CONCAT(nama_file) as nama_file
				, GROUP_CONCAT(mime_type) as mime_type
				, GROUP_CONCAT(nama_file) as nama_file
				, dokumen.tgl_upload AS tgl_upload
				, SUM(size) AS file_size
				FROM dokumen
				LEFT JOIN dokumen_file_picker USING(id_dokumen)
				LEFT JOIN file_picker USING(id_file_picker)
				' . $where . ' GROUP BY id_dokumen' . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();
				
		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}