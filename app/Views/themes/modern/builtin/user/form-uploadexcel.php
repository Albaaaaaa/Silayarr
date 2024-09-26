<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php
		helper(['html', 'format']);
		if (!empty($message)) {
			if ($message['status'] == 'upload_excel') {
				foreach ($message['message'] as $status => $val) {
					show_message(['status' => $status, 'message' => $val]);
				}
			} else {
				show_message($message);
			}
		}
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="tab-content" id="myTabContent">
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Pilih File Excel</label>
					<div class="col-sm-5">
						<input type="file" class="file form-control" name="file_excel">
							<?php if (!empty($form_errors['file_excel'])) echo '<small class="alert alert-danger">' . $form_errors['file_excel'] . '</small>'?>
							<small class="small" style="display:block">Ekstensi file harus .xlsx</small>
						<div class="upload-file-thumb"><span class="file-prop"></span></div>
						<div class="mt-1">Contoh file: <a title="Contoh Data User" href="<?=$config->baseURL?>public/files/Format Data User.xlsx">Data User.xlsx</a></div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-5">
						<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
						<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>