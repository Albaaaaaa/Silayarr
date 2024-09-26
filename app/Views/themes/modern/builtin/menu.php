<?php
helper('html');?>
<div class="card">
	<div class="card-header">
		<h5 class="card-title">Data Menu</h5>
	</div>
	<div class="card-body row">
		<div class="col-sm-5 col-md-4 kategori-container">
			<a href="?module=gedung&action=add" class="btn btn-primary btn-xs" id="add-kategori"><i class="fa fa-plus pe-1"></i> Tambah Kategori</a>
			<hr/>
			<div id="list-kategori">
				<ul class="list-group menu-kategori-container" id="list-kategori-container">
					<?php
					
					foreach ($menu_kategori as $index => $val) {
						if ($val['id_menu_kategori'] == '')
							continue;
						
						$active = $index == 0 ? 'list-group-item-primary' : ''; 
						echo '<li data-id-kategori="' . $val['id_menu_kategori'] . '" class="kategori-item list-group-item list-group-item-action ' . $active . '">
								<span class="text">' . $val['nama_kategori'] . '</span>
								<ul class="toolbox">
									<li>
										<a class="btn-action text-success btn-edit" href="javascript:void(0)"><i class="fas fa-pen"></i></a>
									</li>
									<li>
										<a class="btn-action text-danger btn-remove" href="javascript:void(0)"><i class="fas fa-times"></i></a>
									</li>
								</ul>
							</li>';
						
					}
					?>
					<li data-id-kategori="" class="kategori-item list-group-item list-group-item-action list-group-item-secondary uncategorized">
						<span class="text">Uncategorized</span>
					</li>
				</ul>
				<li data-id-kategori="" class="kategori-item list-group-item list-group-item-action" id="kategori-item-template" style="display:none">
					<span class="text"></span>
					<ul class="toolbox">
						<li>
							<a class="btn-action text-success btn-edit" href="javascript:void(0)"><i class="fas fa-pen"></i></a>
						</li>
						<li>
							<a class="btn-action text-danger btn-remove" href="javascript:void(0)"><i class="fas fa-times"></i></a>
						</li>
					</ul>
				</li>
			</div>
		</div>
		<div class="col-sm-7 col-md-8 menu-container">
			<a href="?module=gedung&action=add" class="btn btn-success btn-xs" id="add-menu"><i class="fa fa-plus pe-1"></i> Tambah Menu</a>
			<hr/>
			<div class="dd" id="list-menu">
				<?= $list_menu ?: '<div class="alert alert-danger">Data tidak ditemukan</div>'?>
			</div>
		</div>
	</div>
</div>