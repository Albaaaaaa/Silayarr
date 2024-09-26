<?php

namespace App\Models;

class FileManagerModel extends \App\Models\BaseModel
{
	public function __construct() {
		parent::__construct();
	}
	
	public function deleteData($id) {
		$sql = 'SELECT * FROM file_picker WHERE id_file_picker = ?';
		$data = $this->db->query($sql, $id)->getRowArray();
		$delete = $this->db->table('file_picker')->delete(['id_file_picker' => $_POST['id']]);
		// File
		$file = $data['nama_file'];
		$path = ROOTPATH . 'public/files/uploads/' . $file;
		delete_file ($path);
		// Thumbnail
		$meta_file = json_decode($data['meta_file'], true);
		if (key_exists('thumbnail', $meta_file)) {
			foreach ($meta_file['thumbnail'] as $val) {
				$path = ROOTPATH . 'public/files/uploads/' . $val['filename'];
				delete_file ($path);
			}
		}
		return $delete;
	}
	
	public function deleteAllData() 
	{
		$sql = 'SELECT * FROM file_picker';
		$data = $this->db->query($sql)->getResultArray();
		// $this->db->table('file_picker')->truncate();
		$this->db->table('file_picker')->emptyTable();
		foreach ($data as $val) {
			// $delete = $this->db->table('file_picker')->delete(['id_file_picker' => $_POST['id']]);
			// File
			$file = $val['nama_file'];
			$path = ROOTPATH . 'public/files/uploads/' . $file;
			delete_file ($path);
			// Thumbnail
			$meta_file = json_decode($val['meta_file'], true);
			if (key_exists('thumbnail', $meta_file)) {
				foreach ($meta_file['thumbnail'] as $thumb) {
					$path = ROOTPATH . 'public/files/uploads/' . $thumb['filename'];
					delete_file ($path);
				}
			}
		}
		
		$this->resetAutoIncrement('file_picker');
		
		return true;
	}
	
	public function saveData($id) {
		$data_db['title'] = $_POST['title'];
		$data_db['caption'] = $_POST['caption'];
		$data_db['description'] = $_POST['description'];
		$data_db['alt_text'] = $_POST['alt_text'];
		$query = $this->db->table('file_picker')->update($data_db, ['id_file_picker' => $id]);
		return $query;
	}
	
	public function getFileById($id) {
		$sql = 'SELECT * FROM file_picker WHERE id_file_picker = ?';
		$result = $this->db->query($sql, $id)->getRowArray();
		return $result;
	}
	
	public function getAllFile() {
		
		return $result;
	}
	
	public function writeExcel() 
	{
		require_once(ROOTPATH . "/app/ThirdParty/PHPXlsxWriter/xlsxwriter.class.php");
		helper('format');
		
		$sql = 'SELECT judul_dokumen, nama_kategori, dokumen.tgl_upload, GROUP_CONCAT(nama_file SEPARATOR ", ") AS FILE, SUM(size) AS file_size_byte
				FROM dokumen
				LEFT JOIN dokumen_kategori USING(id_dokumen_kategori)
				LEFT JOIN dokumen_file_picker USING(id_dokumen)
				LEFT JOIN file_picker USING(id_file_picker)
				GROUP BY id_dokumen';
				
		$query = $this->db->query($sql);
		
		$colls = [
					'no' 				=> ['type' => '#,##0', 'width' => 5, 'title' => 'No'],
					'judul_dokumen' 	=> ['type' => 'string', 'width' => 50, 'title' => 'Judul Dokumen'],
					'nama_kategori' 	=> ['type' => 'string', 'width' => 14, 'title' => 'Nama Kategori'],
					'tgl_upload' 		=> ['type' => 'string', 'width' => 13, 'title' => 'Tgl. Upload'],
					'file' 				=> ['type' => 'string', 'width' => 50, 'title' => 'Nama File'],
					'file_size_byte' 	=> ['type' => '#,##0', 'width' => 15, 'title' => 'Ukuran File (Byte)'],
					'file_size_format' 	=> ['type' => 'string', 'width' => 15, 'title' => 'Ukuran File (Format)'],
				];
		
		$col_type = $col_width = $col_header = [];
		foreach ($colls as $field => $val) {
			$col_type[$field] = $val['type'];
			$col_header[$field] = $val['title'];
			$col_header_type[$field] = 'string';
			$col_width[] = $val['width'];
		}
		
		// Excel
		$sheet_name = strtoupper('Daftar File Dokumen');
		$writer = new \XLSXWriter();
		$writer->setAuthor('Jagowebdev');
		
		$writer->writeSheetHeader($sheet_name, $col_header_type, $col_options = ['widths'=> $col_width, 'suppress_row'=>true]);
		$writer->writeSheetRow($sheet_name, $col_header);
		$writer->updateFormat($sheet_name, $col_type);
		
		$no = 1;
		while ($row = $query->getUnbufferedRow('array')) {
			array_unshift($row, $no);
			$row['tgl_upload'] = format_tanggal($row['tgl_upload'], 'dd-mm-yyyy', false);
			$row[] = format_bytes($row['file_size_byte']);
			$writer->writeSheetRow($sheet_name, $row);
			$no++;
		}
		
		$tmp_file = ROOTPATH . 'public/tmp/file_dokumen_' . time() . '.xlsx.tmp';
		$writer->writeToFile($tmp_file);
		return $tmp_file;
	}
	
	public function downloadAllFile() {
		$sql = 'SELECT * FROM file_picker';
		$path = ROOTPATH . 'public/files/uploads/';
		$path_tmp = ROOTPATH . 'public/tmp/';
		// $file_sql = 'database_' . time() . '.sql';
		$file_excel = $this->writeExcel();
			
		$file_zip = 'file_' . time() . '.zip';
		$zip = new \ZipArchive();
		$zip->open($path_tmp . $file_zip, \ZipArchive::CREATE);
		
		$all_files = $this->db->query($sql)->getResultArray();
		$dir = scandir($path);
		
		$zip->addFile($file_excel, 'List File Dokumen.xlsx');
		$dir = 'file/';
		$zip->addEmptyDir($dir);
		foreach ($all_files as $file) {
			if (file_exists($path . $file['nama_file'])) {
				$zip->addFile($path . $file['nama_file'], $dir . $file['nama_file']);
			}
		}
		
		$zip->close();
			
		header('Content-Description: File Transfer');
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary"); 
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Disposition: attachment; filename=\"Backup File - " . date('Y-m-d') . ".zip");
		header("Content-Length: " . filesize($path_tmp . $file_zip));
		ob_end_clean();
		ob_end_flush();
		readfile($path_tmp . $file_zip);
		unlink($path_tmp . $file_zip);
		unlink($file_excel);
		exit;
	}
	
	public function countAllData($where) {
		$sql = 'SELECT COUNT(*) AS jml FROM file_picker' . $where;
		$result = $this->db->query($sql)->getRow();
		return $result->jml;
	}
	
	public function getListData($where) {

		$columns = $this->request->getPost('columns');

		// Search
		$search_all = @$this->request->getPost('search')['value'];
		if ($search_all) {

			foreach ($columns as $val) {
				
				if (strpos($val['data'], 'ignore_search') !== false) 
					continue;
				
				if (strpos($val['data'], 'ignore') !== false)
					continue;
				
				if ($val['data'] == 'tgl_upload')
					$val['data'] = 'dokumen.tgl_upload';
				
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
				FROM file_picker 
				LEFT JOIN dokumen_file_picker USING(id_file_picker)
				LEFT JOIN dokumen USING(id_dokumen)
				' . $where;
			// echo $sql;
		$total_filtered = $this->db->query($sql)->getRowArray()['jml_data'];
		
		// Query Data
		$start = $this->request->getPost('start') ?: 0;
		$length = $this->request->getPost('length') ?: 10;
		$sql = 'SELECT *, file_picker.*
				FROM file_picker
				LEFT JOIN dokumen_file_picker USING(id_file_picker)
				LEFT JOIN dokumen USING(id_dokumen)				
				' . $where . $order . ' LIMIT ' . $start . ', ' . $length;
		$data = $this->db->query($sql)->getResultArray();
		foreach ($data as &$val) {
			$path = ROOTPATH . 'public/files/uploads/' . $val['nama_file'];
			$val['file_exists'] = file_exists($path) ? '<span class="badge text-bg-success">Ada</span>' : '<span class="badge text-bg-danger">Tidak Ada</span>';
		}
				
		return ['data' => $data, 'total_filtered' => $total_filtered];
	}
}
?>