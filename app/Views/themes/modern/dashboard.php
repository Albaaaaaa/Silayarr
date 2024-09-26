<?php helper('html')?>
<div class="card-body dashboard">
	<div class="row">
		<div class="col-lg-3 col-sm-6 col-xs-12 mb-4">
			<div class="card text-bg-primary shadow">
				<div class="card-body card-stats">
					<div class="description">
						<h5 class="card-title h4"><?=$total_dokumen ? format_number($total_dokumen) : 0?></h5>
						<p class="card-text">Total Dokumen</p>
						
					</div>
					<div class="icon bg-warning-light">
						<!-- <i class="fas fa-clipboard-list"></i> -->
						<i class="material-symbols-outlined">note</i>
					</div>
				</div>
				<div class="card-footer">
					<div class="card-footer-left">
						<i class="material-icons me-2 mt-1" style="font-size:110%">calendar_today</i>
						<p>Total Seluruh Arsip</p>
					</div>
					<div class="card-footer-right">
						<p></p>
					</div>
				</div>	
			</div>
		</div>
		<div class="col-lg-3 col-sm-6 col-xs-12 mb-4">
			<div class="card text-white bg-success shadow">
				<div class="card-body card-stats">
					<div class="description">
						<h5 class="card-title"><?=$total_file_size?></h5>
						<p class="card-text">Total Size</p>
					</div>
					<div class="icon">
						<!-- <i class="fas fa-shopping-cart"></i>-->
						<i class="material-symbols-outlined">folder_zip</i>
					</div>
				</div>
				<div class="card-footer">
					<div class="card-footer-left">
						<i class="material-symbols-outlined me-2" style="font-size:150%">info</i>
						<p>File size dokumen</p>
					</div>
					<div class="card-footer-right">
						<p></p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6 col-xs-12 mb-4">
			<div class="card text-white bg-warning shadow">
				<div class="card-body card-stats">
					<div class="description">
						<h5 class="card-title"><?=$total_kategori?></h5>
						<p class="card-text">Total Kategori</p>
					</div>
					<div class="icon">
						<!-- <i class="fas fa-money-bill-wave"></i> -->
						<i class="material-symbols-outlined">folder</i>
					</div>
				</div>
				<div class="card-footer">
					<div class="card-footer-left">
						<i class="material-symbols-outlined me-2" style="font-size:150%">info</i>
						<p>Kategori digunakan</p>
					</div>
					<div class="card-footer-right">
						<p></p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6 col-xs-12 mb-4">
			<a href="<?=base_url()?>/dokumen/add" title="Cetak Kartu" class="btn btn-depan btn-light p-2 p-0 bg-opacity-10 w-100 align-items-stretch d-flex justify-content-between shadow-sm" style="padding:0 !important; height: 138px;">
				<div class="text text-start py-3 px-3">
					<h5 class="title">Tambah Arsip</h5>
					<hr>
					<small class="text-muted">Tambah arsip dokumen digital</small>
				</div>
				<div class="icon d-flex bg-danger bg-opacity-5 align-items-center justify-content-center rounded-end text-light" style="min-width: 75px">
					<i class="material-symbols-outlined" style="font-size:230%">add_ad</i>
				</div>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-lg-8 mb-4">
			<div class="card" style="height:100%">
				<div class="card-header">
					<div class="card-header-start">
						<h5 class="card-title">Dokumen</h5>
					</div>
					<div class="card-header-end">
						<?php
						
						?>
					</div>
				</div>
				<div class="card-body">
					<?php
					if (!$total_dokumen) {
						echo '<div class="alert alert-danger">Data tidak ditemukan</div>';
					} else {
						
						?>
						<div class="table-responsive">
							<?php
							$column =[
								'ignore_urut' => 'No'
								, 'judul_dokumen' => 'Judul'
								, 'tgl_upload' => '<div class="text-nowrap">Tgl. Upload</div>'
								, 'title' => 'File'
								, 'ignore_action' => 'Action'
							];

							
							$settings['order'] = [2,'desc'];
							$index = 0;
							$th = '';
							foreach ($column as $key => $val) {
								$th .= '<th>' . $val . '</th>'; 
								if (strpos($key, 'ignore') !== false) {
									$settings['columnDefs'][] = ["targets" => $index, "orderable" => false];
								}
								$index++;
							}
							
							?>
							
							<table id="table-dokumen-result" class="table display table-striped table-bordered table-hover" style="width:100%">
							<thead>
								<tr>
									<?=$th?>
								</tr>
							</thead>
							</table>
							<?php
								echo options(['name' => 'id_dokumen_kategori', 'id' => 'id-dokumen-kategori', 'style' => 'display:none;width:auto', 'class' => 'select2'], $list_kategori);
								foreach ($column as $key => $val) {
									$column_dt[] = ['data' => $key];
								}
							?>
							<span id="dataTablesDokumen-column" style="display:none"><?=json_encode($column_dt)?></span>
							<span id="dataTablesDokumen-setting" style="display:none"><?=json_encode($settings)?></span>
							<span id="dataTablesDokumen-url" style="display:none"><?=base_url() . '/dashboard/getDataDTDokumen?id_dokumen_kategori=' . @$_GET['id_dokumen_kategori']?></span>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="col-md-12 col-lg-4 mb-4">
			<div class="card" style="height:100%">
				<div class="card-header">
					<div class="card-header-start">
						<h5 class="card-title">Kategori</h5>
					</div>
					<div class="card-header-end">
						<?php
							if (!empty($list_tahun)) {
								echo '<form method="get" action="" class="d-flex">
										' . options(['name' => 'tahun', 'id' => 'tahun-item-terjual'], $list_tahun, $tahun ) . '
									</form>';
							}
						?>
					</div>
				</div>
				<div class="card-body" style="display:flex; justify-content: center; align-items: center;">
					<div style="overflow: auto; width:100%">
						<?php
						if ($total_dokumen) {
							echo '<canvas id="chart-kategori" style="margin:auto"></canvas>';
						} else {
							echo '<div class="alert alert-danger">Data tidak ditemukan</div>';
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 mb-4">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title">Tren Upload Dokumen</h5>
				</div>
				<div class="card-body">
					<canvas id="trendChart" width="400" height="200"></canvas>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header">Quick Menu</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 col-sm-6 col-xs-12 mb-4">
							<a href="<?=base_url()?>/backup-database/download" title="Backup Database" class="btn btn-depan btn-light p-2 p-0 bg-opacity-10 w-100 align-items-stretch d-flex justify-content-between shadow-sm" style="padding:0 !important">
								<div class="text text-start py-3 px-3">
									<h5 class="title">Backup Database</h5>
									<small class="text-muted">Ekspor dan download database</small>
								</div>
								<div class="icon d-flex bg-primary text-light bg-opacity-5 align-items-center justify-content-center px-4">
									<i class="material-symbols-outlined fs-2">database</i>
								</div>
							</a>
						</div>
						<div class="col-lg-4 col-sm-6 col-xs-12 mb-4">
							<a href="<?=base_url()?>/file-manager/download-all-file" title="Backup Dokumen" class="btn btn-depan btn-light p-2 p-0 bg-opacity-10 w-100 align-items-stretch d-flex justify-content-between shadow-sm" style="padding:0 !important">
								<div class="text text-start py-3 px-3">
									<h5 class="title">Backup Dokumen</h5>
									<small class="text-muted">Download semua file dokumen</small>
								</div>
								<div class="icon d-flex bg-success bg-opacity-5 align-items-center text-light justify-content-center px-4">
									<i class="material-symbols-outlined fs-2">download</i>
								</div>
							</a>
						</div>
						<div class="col-lg-4 col-sm-6 col-xs-12 mb-4">
							<a href="<?=base_url()?>/builtin/user" title="Kelola User" class="btn btn-depan btn-light p-2 p-0 bg-opacity-10 w-100 align-items-stretch d-flex justify-content-between shadow-sm" style="padding:0 !important">
								<div class="text text-start py-3 px-3">
									<h5 class="title">User</h5>
									<small class="text-muted">Kelola data user</small>
								</div>
								<div class="icon d-flex bg-danger bg-opacity-5 align-items-center justify-content-center text-light px-4">
									<i class="material-symbols-outlined fs-2">group</i>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
<?php
$label_kategori = [];
$jumlah_item_kategori = [];
if ($total_dokumen) {
	foreach ($jumlah_dokumen_per_kategori as $val) {
		$label_kategori[] = $val['nama_kategori'];
		$jumlah_item_kategori[] = $val['jml'];
	}
}
?>
<style>
@keyframes colorChange {
  0% { background-color: rgba(255, 0, 0, 0.8); }
  33% { background-color: rgba(0, 255, 0, 0.8); }
  66% { background-color: rgba(0, 0, 255, 0.8); }
  100% { background-color: rgba(255, 0, 0, 0.8); }
}

.card-stats .icon {
  animation: colorChange 3s linear infinite;
  transition: background-color 1s ease;
}
</style>

<script type="text/javascript">
function dynamicColors() {
	var r = Math.floor(Math.random() * 255);
	var g = Math.floor(Math.random() * 255);
	var b = Math.floor(Math.random() * 255);
	return "rgba(" + r + "," + g + "," + b + ", 0.8)";
}
let label_kategori = '<?=json_encode($label_kategori)?>';
let jumlah_item_kategori = '<?=json_encode($jumlah_item_kategori)?>';

// Tambahkan fungsi untuk mengubah warna ikon
function changeIconColors() {
  const icons = document.querySelectorAll('.card-stats .icon');
  icons.forEach(icon => {
    const r = Math.floor(Math.random() * 255);
    const g = Math.floor(Math.random() * 255);
    const b = Math.floor(Math.random() * 255);
    icon.style.backgroundColor = `rgba(${r}, ${g}, ${b}, 0.8)`;
  });
}

// Panggil fungsi setiap detik
setInterval(changeIconColors, 1000);
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Data dummy untuk contoh, ganti dengan data sebenarnya dari backend
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  const uploadData = [65, 59, 80, 81, 56, 55, 40, 65, 59, 80, 81, 56];

  const ctx = document.getElementById('trendChart').getContext('2d');
  const gradientFill = ctx.createLinearGradient(0, 0, 0, 400);
  gradientFill.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
  gradientFill.addColorStop(1, 'rgba(54, 162, 235, 0.1)');

  const trendChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Jumlah Upload',
        data: uploadData,
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: gradientFill,
        borderWidth: 2,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Tren Upload Dokumen per Bulan'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Jumlah Dokumen'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Bulan'
          }
        }
      },
      animation: {
        duration: 2000,
        easing: 'easeOutQuart'
      }
    }
  });

  // Fungsi untuk mengupdate data secara acak
  function updateChartData() {
    const newData = uploadData.map(() => Math.floor(Math.random() * 100));
    trendChart.data.datasets[0].data = newData;
    trendChart.update();
  }

  // Update data setiap 5 detik
  setInterval(updateChartData, 5000);

  // Chart untuk kategori
  const ctxKategori = document.getElementById('chart-kategori').getContext('2d');
  const labelKategori = JSON.parse('<?= json_encode($label_kategori) ?>');
  const jumlahItemKategori = JSON.parse('<?= json_encode($jumlah_item_kategori) ?>');

  new Chart(ctxKategori, {
    type: 'pie',
    data: {
      labels: labelKategori,
      datasets: [{
        data: jumlahItemKategori,
        backgroundColor: labelKategori.map(() => dynamicColors()),
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
        },
        title: {
          display: true,
          text: 'Distribusi Dokumen per Kategori'
        }
      }
    }
  });
});
</script>