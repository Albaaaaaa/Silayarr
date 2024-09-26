<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	<div class="card-body">
		<?php
			if (!empty($message)) {
					show_message($message);
		} ?>
		<form method="post" action="<?=current_url(true)?>" class="form-horizontal" enctype="multipart/form-data">
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Judul File</label>
				<div class="col-sm-5">
					<input class="form-control" type="text" name="judul_file" value="<?=set_value('judul_file', @$file_download['judul_file'])?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Deskripsi File</label>
				<div class="col-sm-5">
					<textarea class="form-control" rows="4" name="deskripsi_file"><?=set_value('deskripsi_file', @$file_download['deskripsi_file'])?></textarea>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">File</label>
				<div class="col-sm-5">
					<div class="input-group mb-3">
						<input type="text" name="filename" class="form-control choose-file filename" placeholder="" aria-label="Choose File" aria-describedby="" value="<?=set_value('filename', @$file_download['nama_file'])?>" required="required" readonly>
						<button class="btn btn-secondary choose-file" type="button">Browse</button>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-5">
					<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
					<input type="hidden" name="id" value="<?=$id?>"/>
					<input type="hidden" name="id_file_picker" class="id-file-picker" value="<?=$id_file_picker?>"/>
				</div>
			</div>
		</form>
	</div>
</div>