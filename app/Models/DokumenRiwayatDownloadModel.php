<?php

namespace App\Models;

class DokumenRiwayatDownloadModel extends \App\Models\BaseModel
{
	public function __construct() {
		parent::__construct();
	}
	
	public function deleteData() {
		$result = $this->db->table('dokumen_download_log')->delete(['id_dokumen_download_log' => $_POST['id']]);
		return $result;
	}

	public function countAllData($where) {
		$sql = 'SELECT COUNT(*) AS jml FROM dokumen_download_log' . $where;
		$result = $this->db->query($sql)->getRow();
		return $result->jml;
	}
	
	public function getListData($where) {

		$columns = $this->request->getPost('columns');

		// Search
		$search_all = @$this->request->getPost('search')['value'];
		if ($search_all) {
			// Additional Search
			$columns[]['data'] = 'tempat_lahir';
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
		if (strpos($_POST['columns'][$order_data[0]['column']]['data'], 'ignore_search') === false) {
			$order_by = $columns[$order_data[0]['column']]['data'] . ' ' . strtoupper($order_data[0]['dir']);
			$order = ' ORDER BY ' . $order_by;
		}

		// Query Total Filtered
		$sql = 'SELECT COUNT(*) AS jml_data 
				FROM dokumen_download_log 
				LEFT JOIN dokumen USING(id_dokumen)
				LEFT JOIN file_picker USING(id_file_picker)
				LEFT JOIN user USING(id_user)
				' . $where;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT * 
				FROM dokumen_download_log 
				LEFT JOIN dokumen USING(id_dokumen)
				LEFT JOIN file_picker USING(id_file_picker)
				LEFT JOIN user USING(id_user)
				' . $where . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();
				
		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}
?>