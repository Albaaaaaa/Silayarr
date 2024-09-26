<?php
namespace App\Models;

class FiledownloadModel extends \App\Models\BaseModel
{
	public function __construct() {
		parent::__construct();
	}
	
	public function getFiles($where) 
	{
		$sql 	= 'SELECT * FROM file_download LEFT JOIN file_picker USING(id_file_picker) ' . $where;
        $result = $this->db->query($sql)->getResultArray();
		return $result;
	}
	
	public function deleteFile($id) 
	{
		$delete = $this->db->table('file_download')->delete(['id_file_download' => $id]);
		if ($delete) {
			$message['status'] = 'ok';
			$message['message'] = 'Data berhasil dihapus';
		} else {
			$message['status'] = 'error';
			$message['message'] = 'Data gagal dihapus';
		}
		
		return $message;
	}
	
	private function validateForm() {
		
		$error = false;
		if (empty(trim($_POST['judul_file']))) {
			$error[] = 'Judul file harus diisi';
		}

		if (empty(trim($_POST['deskripsi_file']))) {
			$error[] = 'Deskripsi file harus diisi';
		}

		return $error;
	}

	public function saveData() 
	{
		$message = [];
		$id_file_download = '';
		if (!empty($_POST['submit'])) {
			$error = $this->validateForm();
			if ($error) {
				$message['status'] = 'error';
				$message['message'] = $error;
			} else {
				$data_db['judul_file'] = $_POST['judul_file'];
				$data_db['deskripsi_file'] = $_POST['deskripsi_file'];
				$data_db['id_file_picker'] = $_POST['id_file_picker'];
				if (!empty($_POST['id'])) {
					$data_db['id_user_update'] = $_SESSION['user']['id_user'];
					$query = $this->db->table('file_download')->update($data_db, ['id_file_download' => $_POST['id']]);
					$id_file_download = $_POST['id'];
				} else {
					$data_db['id_user_input'] = $_SESSION['user']['id_user'];
					$query = $this->db->table('file_download')->insert($data_db);
					$id_file_download = $this->db->insertID();
				}
				
				if ($query) {
					$message['status'] = 'ok';
					$message['message'] = 'Data berhasil disimpan';
				} else {
					$message['status'] = 'error';
					$message['message'] = 'Data gagal disimpan';
				}
			}
		}
		return ['message' => $message, 'id' => $id_file_download, 'id_file_picker' => $_POST['id_file_picker']];
	}
	
	public function saveDownloadLog($file) {
		
		$data_db['id_user'] = $_SESSION['user']['id_user'];
		$data_db['id_file_download'] = $file['id_file_download'];
		$data_db['judul_file'] = $file['judul_file'];
		$data_db['id_file_picker'] = $file['id_file_picker'];
		$data_db['filename'] = $file['nama_file'];
		$data_db['tgl_download'] = date('Y-m-d H:i:s');
		
		$this->db->table('file_download_log')->insert($data_db);
	}
	
}