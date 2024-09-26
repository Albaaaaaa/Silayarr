<?php
namespace App\Models;
require_once('app/ThirdParty/Imageworkshop/autoload.php');
use PHPImageWorkshop\ImageWorkshop;

class FilepickerModel extends \App\Models\BaseModel
{
	public function __construct() {
		parent::__construct();
	}
	
	public function getData($item_per_page) 
	{
		$where = ' WHERE 1 = 1';
		helper('filepicker');
		$list_file_type = file_type();
		$result['filter_tgl'] = [];
		
		if (!empty($_GET['id_file_picker'])) {
			$where .= ' AND id_file_picker = ' . $_GET['id_file_picker'];
			
		} else {
			
			// Filter File Type
			if (!empty($_GET['filter_file'])) {
								
				$split = explode(' ', $_GET['filter_file']);
				$list_filter = [];
				foreach ($split as $filter) 
				{
					
					$filter = trim($filter);
					if (!$filter)
						continue;
					
					$list_mime = [];
					foreach ($list_file_type as $mime => $val) {
						if ($val['file_type'] == $filter) {
							$list_mime[] = $mime;
						}
					}
					
					if ($list_mime) {
						$list_filter[] = 'mime_type IN ("' . join ('","', $list_mime) . '")';
					}
				}
				
				if ($list_filter) {
					$where .= ' AND (' . join(' OR ', $list_filter) . ')';
				}
			}
			
			// Date Options
			$sql = 'SELECT DATE_FORMAT(tgl_upload,"%Y-%m") AS bulan FROM file_picker GROUP BY bulan ORDER BY bulan DESC ';
			$tanggal = $this->db->query($sql)->getResultArray();
			$nama_bulan = nama_bulan();
			foreach ($tanggal as $val) {
				$exp = explode('-', $val['bulan']);
				$result['filter_tgl'][$val['bulan']] = $nama_bulan[$exp[1] * 1] . ' ' . $exp[0];
			}
			
			// FIlter Tgl
			if ( !empty($_GET['filter_tgl']) ) {
				$where .= ' AND tgl_upload LIKE "' . $_GET['filter_tgl'] . '%"';
			}
			
			// Filter Search 
			if ( !empty($_GET['q']) && trim($_GET['q']) != '' ) {
				$where .= ' AND (title LIKE "%' . $_GET['q'] . '%" OR nama_file LIKE "%' . $_GET['q'] . '%")';
			}
		}
		
		if (empty($_GET['page'])) {
			$_GET['page'] = 1;
		}

		$limit = $item_per_page * ( $_GET['page'] - 1 ) . ', ' . $item_per_page;
		
        $sql = 'SELECT * FROM file_picker ' . $where . ' ORDER BY tgl_upload DESC LIMIT ' . $limit;
		$result['data'] = $this->db->query($sql)->getResultArray();
		
		$sql = 'SELECT COUNT(*) AS total_item FROM file_picker ' . $where;
		$query = $this->db->query($sql)->getRowArray();
		$total_item = $query['total_item'];
		$result['total_item'] = $total_item;
		
		$jml_data = count($result['data']);
		$loaded_item = $jml_data < $item_per_page ? $jml_data : $item_per_page;
		$result['loaded_item'] = ( $item_per_page * ($_GET['page'] - 1) ) + count($result['data']);
					
		foreach ($result['data'] as $key => $val) 
		{
			$meta_file = json_decode($val['meta_file'], true);
			$properties = $this->getFileProperties($val['mime_type'], $val['nama_file'], $meta_file);
			$result['data'][$key] = array_merge($result['data'][$key], $properties);
		}
		
		return ['result' => $result, 'total_item' => $total_item, 'loaded_item' => $loaded_item];
	}
	
	private function getFileProperties($mime, $file_name, $meta_file) 
	{			
		$config = new \Config\Filepicker();
		helper('filepicker');
		$list_file_type = file_type();
		
		
		$extension_color = $extension = '';
		$mime_image = ['image/png', 'image/jpeg', 'image/bmp', 'image/gif'];
		
		$file_exists = true;
		// echo $config['filepicker_upload_path'] . $file_name; die;
		// echo $config->uploadPath; die;
		if (file_exists($config->uploadPath . $file_name)) 
		{
			$result['file_exists']['original'] = 'found';
		} else {
			$file_exists = false;
			$result['file_exists']['original'] = 'not_found';
		}
		
		if (in_array($mime, $mime_image)) {
			
			$thumbnail_file = $file_name;
			if (key_exists('thumbnail', $meta_file)) 
			{
				$thumbnail = $meta_file['thumbnail'];
				foreach ($thumbnail as $size => $val) 
				{
					if (file_exists($config->uploadPath . $val['filename'])) {
						$result['file_exists']['thumbnail'][$size] = 'found';
					} else {
						$file_exists = false;
						$result['file_exists']['thumbnail'][$size] = 'not_found';
					}
				}
				
				if (key_exists('small', $thumbnail)) {
					$thumbnail_file = $thumbnail['small']['filename'];
				}
			}
			
			$thumbnail_url = $config->uploadURL . $thumbnail_file; 
			$file_type = 'image';

		} else {
			
			$pathinfo = pathinfo($file_name);
			$extension = $pathinfo['extension'];
			
			$file_icon = 'file';
			$file_type = 'non_image';
			
			if (key_exists($mime, $list_file_type)) {
				$file_icon = $list_file_type[$mime]['extension'];
				$file_type = $list_file_type[$mime]['file_type'];
			} else {
				
				foreach ($list_file_type as $val) {
					if ($val['extension'] == $extension) {
						$file_icon = strtolower($extension);
						$file_type = $val['file_type'];
					}
				}
			}
			
			$thumbnail_url = $config->iconURL . $file_icon . '.png';
			
		}
		
		if (!$file_exists) {
			$thumbnail_url = $config->iconURL . 'file_not_found.png';
		}
		
		
		if (!key_exists('thumbnail', $result['file_exists']) ) {
			 $result['file_exists']['thumbnail'] = [];
		}
		
		if ($file_exists) {
			$result['file_not_found'] = 'false';
		} else {
			$result['file_not_found'] = 'true';
		}
		
		$result['file_type'] = $file_type;
		$result['url'] = $config->uploadURL . $file_name; 
		$result['thumbnail']['url'] = $thumbnail_url;
		$result['thumbnail']['extension_name'] = $extension;
		
		return $result;
	}
	
	public function updateMetaFile() {
		$update = $this->db->table('file_picker')->update([$_POST['name'] => $_POST['value']], ['id_file_picker' => $_POST['id']]);
		return $update;
	}
	
	public function deleteFile() 
	{
		$config = new \Config\Filepicker();
		$this->db->transBegin();
		$id_files = json_decode($_POST['id'], true);
		
		if (!is_array($id_files)) {
			if ($id_files) {
				$id_files = [ $id_files ];
			} else {
				$id_files = [ $_POST['id'] ];
			}
		}
		
		$error = [];
		foreach ($id_files as $id_file) 
		{
			$sql = 'SELECT * FROM file_picker WHERE id_file_picker = ?';
			$file = $this->db->query($sql, $id_file)->getRowArray();
			
			if (!$file) {
				$error[] = 'File tidak ditemukan';
			} else {
	
				$delete = $this->db->table('file_picker')->delete(['id_file_picker' => $id_file]);
				if ($delete) {
					$meta = json_decode($file['meta_file'], true);
					
					$dir = trim($config->uploadPath, '/');
					$dir = trim($dir, '\\');
					$dir = $dir . '/';
					
					// Main File
					if (file_exists($dir . $file['nama_file'])) { 
						$unlink = delete_file($dir . $file['nama_file']);
						if (!$unlink) {
							$error[] = 'Gagal menghapus file: ' . $val['filename'];
						}
					}
					
					// Thumbnail
					if(key_exists('thumbnail', $meta)) 
					{
						foreach ($meta['thumbnail'] as $val) {
							if (file_exists($dir . $val['filename'])) { 
								$unlink = delete_file($dir . $val['filename']);
								if (!$unlink) {
									$error[] = 'Gagal menghapus file: ' . $val['filename'];
								}
							}
						}
					}
					
				} else {
					$error[] = 'Gagal menghapus data database file ID: ' . $id_file;
				}
			}
		}
		
		$sql = 'SELECT COUNT(*) AS jml FROM file_picker';
		$result = $this->db->query($sql)->getRowArray();
		if (!$result['jml']) {
			$sql = 'ALTER TABLE file_picker AUTO_INCREMENT 1';
			$this->db->query($sql);
		}
		
		if ($error) {
			$this->db->transRollback();
			$result['status'] = 'error';
			$result['message'] = '<ul><li>' . join('</li></li>', $error) . '</li></ul>';
		} else {
			$this->db->transCommit();
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil dihapus';
		}
		
		return $result;
	}
	
	public function deleteAllFiles() 
	{
		$config = new \Config\Filepicker();
		$path = $config->uploadPath;
		
		$list_file = @scandir($path);
		if ($list_file) 
		{
			$truncate = $this->db->table('file_picker')->truncate();
			if ($truncate) {
				foreach ($list_file as $val) 
				{
					if ($val == '.' || $val == '..') {
						continue;
					}
					
					delete_file($path . $val);
				}
			}
			
			$result['status'] = 'ok';
			$result['message'] = 'Data berhasil dihapus';
		} else {
			$result['status'] = 'error';
			$result['message'] = 'Folder ' . $path . ' kosong';
		}
		
		return $result;
	}
	
	public function uploadFile() 
	{		
		$config = new \Config\Filepicker();
		$nama_bulan = nama_bulan();
		
		$path = $config->uploadPath;
		
		helper('filepicker');
		helper('upload_file');
		$list_file_type = file_type();
		
		if ( !empty($_FILES) ) {
						
			if ( file_exists($path) && is_dir($path) ) {
				
				if ( !is_writable($path) ) {
					$result = array (
						'status' => 'error',
						'message'   => 'Tidak dapat menulis file ke folder'
					);
					
				} else {

					$new_name = upload_file($path, $_FILES['file']);
					if ($new_name) {
						
						$meta_file = [];
						
						$mime_image = ['image/png', 'image/jpeg', 'image/bmp', 'image/gif'];
						$current_mime_type = mime_content_type ($path . $new_name);
						
						if (in_array($current_mime_type, $mime_image)) 
						{
							$img_size = @getimagesize($path . $new_name);
						
							$meta_file['default'] = ['width' => $img_size[0]
														, 'height' => $img_size[1]
														, 'size' => $_FILES['file']['size']
													];

							foreach ($config->thumbnail as $size => $dim) 
							{
							
								if ($img_size[0] > $dim['w'] || $img_size[1] > $dim['h']) 
								{
									$img_dim = image_dimension($path. $new_name, $dim['w'], $dim['h']);
									$img_width = ceil($img_dim[0]);
									$img_height = ceil($img_dim[1]);
									
									$width = $height = null;
									if ($img_width >= $dim['w']) {
										
										$width = $dim['w'];
										
									} else if ($img_height >= $dim['h']) {
										
										$height = $dim['h'];
									}

									$layer = ImageWorkshop::initFromPath($path . $new_name);
									$layer->resizeInPixel($width, $height, true);
									$name_path = pathinfo($new_name);
									$thumb_name = $name_path['filename'] . '_' . $size . '.' . $name_path['extension'];
									$layer->save($path, $thumb_name, false, false, 97);
									
									$thumb_dim =  @getimagesize($path . $thumb_name);
									$meta_file['thumbnail'][$size] = [
															'filename' => $thumb_name
															, 'width' => $thumb_dim[0]
															, 'height' => $thumb_dim[1]
															, 'size' => @filesize($path . $thumb_name)
														];
								}
							}
						}
						
						$judul_file = pathinfo($new_name);
						$data_db['title'] = $judul_file['filename'];
						$data_db['nama_file'] = $new_name;
						$data_db['mime_type'] = $current_mime_type;
						$data_db['size'] = $_FILES['file']['size'];
						$data_db['tgl_upload'] = date('Y-m-d H:i:s');
						$data_db['id_user_upload'] = $_SESSION['user']['id_user'];
						$data_db['meta_file'] = json_encode($meta_file);
						
						$insert = $this->db->table('file_picker')->insert($data_db);
						
						$file_info = $data_db;
						$file_info['bulan_upload'][date('Y-m')] = $nama_bulan[date('n')] . ' ' . date('Y');
						$file_info['id_file_picker'] = $this->db->insertID();
						$result = $this->getFileProperties($current_mime_type, $new_name, $meta_file);
						$file_info = array_merge($file_info, $result);
						
						$result = [
								'status'    => 'success',
								'message'      => 'File berhasil diupload.',
								'file_info' => $file_info
						];
					} else {
						$result = [
							'status' => 'error',
							'message'   => 'System error'
						];
					}
				}

			} else {
				$result = [
					'status' => 'error',
					'message'   => 'Folder ' . $path . ' tidak ditemukan'
				];
			}
			
		} else {
			$result = [
				'status' => 'error',
				'message'   => 'file empty'
			];
		}
		
		return $result;
	}
}