<?php
helper('html');
?>
<form method="post" class="modal-form" id="add-form" action="<?=current_url()?>" >
	<div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Kategori</label>
			<div class="col-sm-8">
				<input class="form-control" type="text" name="nama_kategori" value="<?=@$kategori['nama_kategori']?>" placeholder="Nama Kategori" required="required"/>
			</div>
		</div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Deskripsi</label>
			<div class="col-sm-8">
				<textarea class="form-control" name="deskripsi" required="required"/><?=@$kategori['deskripsi']?></textarea>
			</div>
		</div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Aktif</label>
			<div class="col-sm-8">
				<?php
					$checked = @$kategori['aktif'] == 'Y' ? 'checked="checked"' : '';
				?>
				<div class="form-check-input-sm form-switch"><input name="aktif" type="checkbox" class="form-check-input" value="1" <?=$checked?>></div>
				<small class="form-text text-muted"><em>Jika tidak aktif, semua children tidak akan dimunculkan</em></small>
			</div>
		</div>
		<div class="row mb-3">
			<label class="col-sm-3 col-form-label">Use icon</label>
			<div class="col-sm-8 form-inline">
				<?php 
					$selected = @$kategori['icon'] ? 1 : 0;
					$options = array(1 => 'Ya', 0 => 'Tidak');
					$display = $selected ? '' : 'style="display:none"';
					echo options(['name' => 'use_icon'], $options, $selected);
					$icon = @$kategori['icon'] ? $kategori['icon'] : 'far fa-circle';
				?>
				<a href="javascript:void(0)" class="icon-preview" data-action="faPicker" <?=$display?>><i class="<?=$icon?>"></i></a>
				<input type="hidden" name="icon_class" value="<?=$icon?>"/>
			</div>
		</div>
		<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
		
	</div>
</form>