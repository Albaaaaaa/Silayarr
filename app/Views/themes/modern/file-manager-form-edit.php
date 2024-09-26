<?php
helper('html');
?>
<form method="post" action="" class="form-horizontal p-3">
	<div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Nama File</label>
			<div class="col-sm-9">
				<div class="pt-2"><?=@$file['nama_file']?></div>
			</div>
		</div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Judul File</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" name="title" value="<?=@$file['title']?>" required="required"/>
			</div>
		</div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Caption</label>
			<div class="col-sm-9">
				<textarea name="caption" class="form-control"><?=$file['caption']?></textarea>
			</div>
		</div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Description</label>
			<div class="col-sm-9">
				<textarea name="description" class="form-control"><?=$file['description']?></textarea>
			</div>
		</div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Alt. Text</label>
			<div class="col-sm-9">
				<input class="form-control" type="text" name="alt_text" value="<?=@$file['alt_text']?>"/>
			</div>
		</div>
	</div>
	<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
</form>