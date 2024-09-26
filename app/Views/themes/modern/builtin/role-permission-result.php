<div class="card">
	<div class="card-header">
		<h5 class="card-title">Daftar Role</h5>
	</div>
	
	<div class="card-body">
		<?php

helper('html');
// echo '<pre>'; print_r($module);die;
?>
		<?php			
		$column =[
					'ignore_search_urut' => 'No'
					, 'nama_role' => 'Nama Role'
					, 'judul_role' => 'Judul Role'
					, 'ignore_jml_module' => 'Jml. Module'
					, 'ignore_jml_permission' => 'Jml. Permission'
					, 'ignore_search_action' => 'Action'
				];
		
		$settings['order'] = [1,'asc'];
		$index = 0;
		$th = '';
		foreach ($column as $key => $val) {
			$th .= '<th>' . $val . '</th>'; 
			if (strpos($key, 'ignore_search') !== false) {
				$settings['columnDefs'][] = ["targets" => $index, "orderable" => false];
			}
			$index++;
		}
		
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