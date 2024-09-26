<?php

namespace App\Controllers;
use App\Models\DashboardModel;

class Dashboard extends BaseController
{
	public function __construct() {
		parent::__construct();
		$this->model = new DashboardModel;
		$this->addJs($this->config->baseURL . 'public/vendors/chartjs/chart.js');
		$this->addStyle($this->config->baseURL . 'public/vendors/material-icons/css.css');
		
		$this->addJs ( $this->config->baseURL . 'public/vendors/datatables/extensions/Buttons/js/dataTables.buttons.min.js');
		$this->addJs ( $this->config->baseURL . 'public/vendors/datatables/extensions/Buttons/js/buttons.bootstrap5.min.js');
		$this->addJs ( $this->config->baseURL . 'public/vendors/datatables/extensions/JSZip/jszip.min.js');
		$this->addJs ( $this->config->baseURL . 'public/vendors/datatables/extensions/pdfmake/pdfmake.min.js');
		$this->addJs ( $this->config->baseURL . 'public/vendors/datatables/extensions/pdfmake/vfs_fonts.js');
		$this->addJs ( $this->config->baseURL . 'public/vendors/datatables/extensions/Buttons/js/buttons.html5.min.js');
		$this->addJs ( $this->config->baseURL . 'public/vendors/datatables/extensions/Buttons/js/buttons.print.min.js');
		$this->addStyle ( $this->config->baseURL . 'public/vendors/datatables/extensions/Buttons/css/buttons.bootstrap5.min.css');
		
		$this->addJs ( $this->config->baseURL . 'public/vendors/jquery.select2/js/select2.full.min.js' );
		$this->addStyle ( $this->config->baseURL . 'public/vendors/jquery.select2/css/select2.min.css' );
		$this->addStyle ( $this->config->baseURL . 'public/vendors/jquery.select2/bootstrap-5-theme/select2-bootstrap-5-theme.min.css' );
		
		$this->addStyle($this->config->baseURL . 'public/themes/modern/css/dashboard.css');
		$this->addJs($this->config->baseURL . 'public/themes/modern/js/dashboard.js');
	}
	
	public function index()
	{
		// Baris pertama
		$this->data['total_dokumen'] = $this->model->getJumlahDokumen();
		$this->data['total_file'] = $this->model->getJumlahFile();
		$this->data['total_kategori'] = $this->model->getJumlahKategori();
		$this->data['jumlah_dokumen_per_kategori'] = $this->model->jumlahDokumenPerKategori();
		
		$result = $this->model->getKategoriUsed();
		$list_kategori = [];
		$list_kategori[''] = 'Semua Kategori';
		foreach ($result as $val) {
			$list_kategori[$val['id_dokumen_kategori']] = $val['nama_kategori'];
		}
		$this->data['list_kategori'] = $list_kategori;
		
		helper('format');
		$total_file_size = $this->model->getTotalFileSize();
		$total_file_size = $total_file_size ? format_bytes($total_file_size) : 0;
		
		$this->data['total_file_size'] = $total_file_size;
						
		$this->view('dashboard.php', $this->data);
	}
	
	public function getDataDTDokumen() {
		
		$num_data = $this->model->countAllData( $this->whereOwn() );
		$result['draw'] = $start = $this->request->getPost('draw') ?: 1;
		$result['recordsTotal'] = $num_data;
		
		$query = $this->model->getListData( $this->whereOwn() );
		$result['recordsFiltered'] = $query['total_filtered'];
				
		helper('html');
		
		$no = $this->request->getPost('start') + 1 ?: 1;
		$config = new \Config\Filepicker();
		helper('filepicker');
		$list_file_type = file_type();
		
		foreach ($query['data'] as $key => &$val) 
		{
			$val['tgl_upload'] = '<div class="text-nowrap">' . format_tanggal($val['tgl_upload'], 'dd-mm-yyyy', false) . '</div>';
			$val['deskripsi_dokumen'] = '<div class="text-wrap" style="max-width:500px">' . $val['deskripsi_dokumen'] . '</div>';
			
			$list_file = [];
			// echo '<pre>'; print_r($list_file_icon); die;
			if ($val['nama_file']){
				$list_file_icon = scandir(ROOTPATH . 'public/images/filepicker_images/');
				$exp_id_file = explode (',', $val['id_file_picker']);
				$exp_judul_file = explode (',', $val['judul_file']);
				$exp_mime = explode (',', $val['mime_type']);
				$exp_nama_file = explode (',', $val['nama_file']);
				foreach ($exp_nama_file as $index => $nama_file) 
				{
					$file_icon = 'file';
					$pathinfo = pathinfo($nama_file);
					$extension = strtolower($pathinfo['extension']) . '.png';
					$mime = $exp_mime[$index];
					if (key_exists($mime, $list_file_type)) {
						$file_icon = $list_file_type[$mime]['extension'];
					} else {
						foreach ($list_file_type as $val) {
							if ($val['extension'] == $extension) {
								if (in_array($extension, $list_file_icon)) {
									$file_icon = $extension;
								}
							}
						}
					}
					
					$icon_url = $config->iconURL . $file_icon . '.png';
					$list_file[] = '<a href="' . base_url() . '/dokumen/download?id=' . $val['id_file_picker'] . '" class="text-start mb-2" style="width:auto"><span class="d-flex" style="width:200px"><img style="max-height:24px" src="'. $icon_url . '" class="me-2"/><span>' . $exp_judul_file[$index] . '</span></span></a>';
				}
			}
			
			if (!$list_file) {
				$list_file[] = '<div class="text-nowrap">Tidak ada</div>';
			}
			
			$val['title'] = '<ul class=list-circle"><li style="max-width: 200px;" class="mb-2">' . join('</li><li style="max-width: 200px;" class="mb-2">', $list_file) . '</li></ul>';			
			$val['ignore_urut'] = $no;
			
			$val['ignore_action'] = '<div class="btn-group">';
			if ($this->hasPermission('update_all')) {
				
				$val['ignore_action'] .= btn_link([
										'url' => base_url() . '/dokumen/edit?id=' . $val['id_dokumen'],
										'icon' => 'fas fa-edit',
										'attr' => ['class' => 'btn btn-success btn-xs btn-edit', 'data-id' => $val['id_dokumen']],
										'label'=> ''
									]);
			}
			
			if ($this->hasPermission('delete_all')) {
				$val['ignore_action'] .= btn_label([
										'icon' => 'fas fa-times',
										'attr' => ['class' => 'btn btn-danger btn-xs btn-delete', 'data-id' => $val['id_dokumen'], 'data-delete-title' => 'Hapus data dokumen dengan judul: <strong>' . $val['judul_dokumen'] . '</strong>?'],
										'label'=> ''
									]);
			}
			$val['ignore_action'] .= '</div>';
			// $val['judul_dokumen'] = $val['judul_dokumen'] . '<br/><span class="badge badge-success">' . format_size($val['file_size']) . '</span>';
			$no++;
		}
					
		$result['data'] = $query['data'];
		echo json_encode($result); exit();
	}
}