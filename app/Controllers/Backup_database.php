<?php

namespace App\Controllers;

require_once(ROOTPATH . 'app/ThirdParty/Mysqldump/autoload.php');
use Ifsnop\Mysqldump as IMysqldump;

class Backup_database extends \App\Controllers\BaseController
{
	public function __construct() {
		// Memanggil konstruktor kelas induk dan mengatur judul situs
		parent::__construct();
		$this->data['site_title'] = 'Backup Database';
	}
	
	public function index()
	{
		 // Inisialisasi array pesan
		$message = [];
		// Mengambil konfigurasi database
		$config = new \Config\Database();
		$config = $config->getConnections()['default'];
		// Jika tombol submit ditekan, jalankan fungsi download		
		if (!empty($_POST['submit'])) {
			$this->download();
		}
		// Menyimpan pesan dan konfigurasi database ke dalam data untuk ditampilkan
		$this->data['message'] = $message;
		$this->data['config_database'] = $config;
		$this->data['title'] = 'Backup Database';
		// Memuat view dengan data yang telah disiapkan
		$this->view('backup-database-form.php', $this->data);
	}
	
	public function download() 
	{
		// Mengambil konfigurasi database
		$config = new \Config\Database();
		$config = $config->getConnections()['default'];
		
		try {
			// Membuat objek Mysqldump untuk melakukan backup database
		$dump = new IMysqldump\Mysqldump('mysql:host=localhost;dbname=' . $config->database, $config->username, $config->password);
		
		$path = ROOTPATH . 'public/tmp/';
		$file_sql = 'database_' . time() . '.sql';
		$dump->start( $path . $file_sql);
		
		// Membuat file zip untuk menyimpan backup database
		$file_zip = 'database_' . time() . '.zip';
		$zip = new \ZipArchive();
		$zip->open($path . $file_zip, \ZipArchive::CREATE);
		$zip->addFile($path . $file_sql, $config->database . '_' . date('Y-m-d') . '.sql');
		$zip->close();
		
		// Menghapus file SQL setelah zip		
		unlink($path . $file_sql);
		
		// Mengatur header untuk mengirim file zip sebagai response
		header('Content-Description: File Transfer');
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: Binary"); 
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Disposition: attachment; filename=\"database_". $config->database . '_' . date('Y-m-d') . ".zip");
		header("Content-Length: " . filesize($path . $file_zip));
		ob_end_clean();
		ob_end_flush();
		// Membaca file zip untuk di-download
		readfile($path . $file_zip);
		// Menghapus file zip setelah di-download
		unlink($path . $file_zip);
		exit;
		
		// $message = ['status' => 'ok', 'message' => 'Database berhasil dibackup'];
			
		} catch (\Exception $e) {
			$message = ['status' => 'error', 'message' => 'mysqldump-php error: ' . $e->getMessage()];
		}
	}
}
