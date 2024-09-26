<div class="card">
	<div class="card-body">
	<div class="table-responsive">
		<?php
		if (has_permission('create')) {
			echo '<a href="' . current_url() . '/add" class="btn btn-success btn-xs"><i class="fa fa-plus pe-1"></i> Tambah Data</a>
			<a href="<?=current_url()?>/uploadexcel" class="btn btn-success btn-xs"><i class="fas fa-arrow-up-from-bracket pe-1"></i> Upload Excel</a>
			<hr/>';
		}

		if (!empty($message)) {
			show_message($message);
		}
		
		$column =[
					'ignore_avatar' => 'Avatar'
					, 'username' => 'Username'
					, 'email' => 'Email'
					, 'nama' => 'Nama'
					, 'judul_role' => 'Role'
					, 'verified' => 'Verified'
					, 'ignore_btn_action' => 'Aksi'
				];
		$th = '';
		foreach ($column as $val) {
			$th .= '<th>' . $val . '</th>'; 
		}
		?>
		<table id="table-result" class="table display nowrap table-striped table-bordered" style="width:100%">
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
			$settings['order'] = [3,'asc'];
			$index = 0;
			foreach ($column as $key => $val) {
				$column_dt[] = ['data' => $key];
				if (strpos($key, 'ignore') !== false) {
					$settings['columnDefs'][] = ["targets" => $index, "orderable" => false];
				}
				$index++;
			}
		?>
		<span id="dataTables-column" style="display:none"><?=json_encode($column_dt)?></span>
		<span id="dataTables-setting" style="display:none"><?=json_encode($settings)?></span>
		<span id="dataTables-url" style="display:none"><?=current_url() . '/getDataDT'?></span>
	</div>
	</div>
</div>