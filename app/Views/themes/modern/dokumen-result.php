<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$current_module['judul_module']?></h5>
	</div>
	
	<div class="card-body">
		<?php
		$button = false;
		if (has_permission('create')) 
		{
			echo '<a href="' . base_url() . '/dokumen/add" class="btn btn-success btn-xs"><i class="fa fa-plus me-2"></i>Tambah Data</a>';
			$button = true;
		}
		
		if (has_permission('delete_all')) {
			$margin = $button ? ' ms-2' : '';
			echo '<button type="button" class="btn btn-danger btn-delete-all-data btn-xs' . $margin . '"><i class="fa fa-trash me-2"></i>Hapus Semua Data</button>';
			$button = true;
		}
		 
		// Tambahkan tombol cetak
		echo '<button type="button" id="btn-print" class="btn btn-primary btn-xs ms-2"><i class="fa fa-print me-2"></i>Cetak Dokumen Terpilih</button>';
		$button = true;
		
		if ($button) {
			echo '<hr/>';
		}
		
		if (!empty($msg)) {
			show_alert($msg);
		}
			
		$column =[
					'ignore_checkbox' => '<input type="checkbox" id="check-all">',
					'ignore_urut' => 'No'
					, 'no_berkas' => 'No Berkas'
					, 'kode_klasifikasi' => 'Kode Klasifikasi'
					, 'judul_dokumen' => 'Uraian Berkas'
					, 'no_item' => 'No Item'
					, 'deskripsi_dokumen' => 'Uraian Isi Berkas'
					, 'opd' => 'OPD'
					, 'tgl_upload' => '<div class="text-nowrap">Tanggal Berkas</div>'
					, 'tingkat_perkembangan' => 'Tingkat Perkembangan'
					, 'jumlah_berkas' => 'Jumlah Berkas'
					, 'kondisi_berkas' => 'Kondisi Berkas'
					, 'inaktif' => 'Tahun Inaktif'
					, 'nasib_akhir' => 'Nasib Akhir'
					, 'title' => 'File'
					
				];

		if (has_permission('update_all') || has_permission('delete_all')) {
			$column['ignore_action'] = 'Action';
		}
		
		$settings['order'] = [2,'asc'];
		$index = 0;
		$th = '';
		foreach ($column as $key => $val) {
			$th .= '<th>' . $val . '</th>'; 
			if (strpos($key, 'ignore') !== false) {
				$settings['columnDefs'][] = ["targets" => $index, "orderable" => false];
			}
			$index++;
		}
		helper('html');
			echo '<div class="d-flex mb-3">
				<span class="input-group-text" style="width:100px">Cari Per Kategori</span>' . options(['name' => 'id_dokumen_kategori', 'id' => 'id-dokumen-kategori', 'style' => 'width:auto'], $list_kategori).'</div>';
			
		?>
		
		<table id="table-result" class="table display table-striped table-bordered table-hover" style="width:100%">
		<thead>
			<tr>
				<?=$th?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<?=$th?>
			</tr>
		</tfoot>
		</table>
		<?php
			foreach ($column as $key => $val) {
				$column_dt[] = ['data' => $key];
			}
		?>
		<span id="dataTables-column" style="display:none"><?=json_encode($column_dt)?></span>
		<span id="dataTables-setting" style="display:none"><?=json_encode($settings)?></span>
		<span id="dataTables-url" style="display:none"><?=current_url() . '/getDataDT'?></span>
	</div>
</div>

<script>
$(document).ready(function() {
	// Fungsi untuk menangani klik pada tombol cetak
	$('#btn-print').on('click', function() {
		var selectedIds = [];
		$('input[name="dokumen_ids[]"]:checked').each(function() {
			selectedIds.push($(this).val());
		});

		if (selectedIds.length > 0) {
			window.location.href = '<?= base_url() ?>/dokumen/print?ids=' + selectedIds.join(',');
		} else {
			alert('Pilih setidaknya satu dokumen untuk dicetak.');
		}
	});

	// Fungsi untuk menangani checkbox "pilih semua"
	$('#check-all').on('click', function() {
		$('input[name="dokumen_ids[]"]').prop('checked', this.checked);
	});
});
</script>