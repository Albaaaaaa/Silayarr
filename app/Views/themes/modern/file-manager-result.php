<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$current_module['judul_module']?></h5>
	</div>
	
	<div class="card-body">
		<button type="button" class="btn btn-success btn-xs btn-upload-file"><i class="fas fa-upload me-2"></i>Upload File</button>
		<a href="<?=base_url()?>/file-manager/download-all-file" class="btn btn-primary btn-xs"><i class="fas fa-download me-2"></i>Download Semua File</a>
		<button type="button" class="btn btn-danger btn-xs btn-delete-all-data"><i class="fas fa-trash me-2"></i>Hapus Semua Data</button>
		<div id="dropzone-container" class="mt-3" style="display:none">
			<form action="<?=$config->baseURL?>filepicker/ajaxUploadFile" class="dropzone-area" id="form-dropzone">
					<div class="dz-message dz-default needsclick">
						<div><i class="fas fa-cloud-upload-alt"></i></div>
						<div>Drag &amp; Drop File Disini</div>
					</div>
					<div class="preview-container dz-preview uploaded-files" style="display:none">
						<div id="file-previews">
							<div id="dropzone-preview-template">
								<div class="dropzone-info">
									<div class="uploaded-thumb"><img data-dz-thumbnail/></div>
									<div class="details">
										<div class="file-info">
											<span data-dz-name></span> (<span data-dz-size></span>)<span class="progress-text"></span>
										</div>
										<div class="dz-progress progress"><div class="dz-upload progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width:0" data-dz-uploadprogress></div></div>
										<button class="btn btn-close" data-dz-remove></button>
									</div>
									<div class="dz-error-message"><span data-dz-errormessage></span></div>
								</div>
							</div>
						</div>
					</div>
					<div id="jwd-dz-error">
					</div>
					
				</form>
				<hr/>
			</div>
		<hr/>
		<?php 
		if (!empty($msg)) {
			show_alert($msg);
		}
			
		$column =[
					'ignore_urut' => 'No'
					, 'judul_dokumen' => 'Uraian Berkas'
					, 'deskripsi_dokumen' => 'Uraian Isi Berkas'
					, 'kode_klasifikasi' => 'Kode Klasifikasi'
					, 'opd' => 'OPD'
					, 'ignore_file_exists' => 'File Eksis'
					, 'size' => 'Ukuran File'
					, 'tgl_upload' => 'Tanggal Berkas'
					// , 'ignore_download' => 'Download'
					, 'nama_file' => 'File'
					, 'ignore_action' => 'Action'
				];
		
		$settings['order'] = [1,'asc'];
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
		<span id="dataTables-url" style="display:none"><?=base_url() . '/file-manager/getDataDT'?></span>
	</div>
</div>