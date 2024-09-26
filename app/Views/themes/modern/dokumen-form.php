<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	<div class="card-body">
		<a href="<?=base_url()?>/dokumen/add" class="btn btn-success btn-xs me-2"><i class="fa fa-plus pe-1"></i> Tambah Data</a>
		<a href="<?=base_url()?>/dokumen" class="btn btn-light btn-xs" id="add-menu"><i class="fa fa-arrow-circle-left pe-1"></i> Daftar Dokumen</a>
		<hr/>
		<?php
			if (!empty($message)) {
					show_message($message);
		} 
		helper('html');
		?>
		<form method="post" action="<?=current_url(true)?>" class="form-horizontal" enctype="multipart/form-data">
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">No Berkas</label>
				<div class="col-sm-5">
					<input class="form-control" type="text" name="no_berkas" value="<?=set_value('no_berkas', @$dokumen['no_berkas'])?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Kode Klasifikasi</label>
				<div class="col-sm-5">
					<input class="form-control" type="text" name="kode_klasifikasi" value="<?=set_value('kode_klasifikasi', @$dokumen['kode_klasifikasi'])?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Uraian Berkas</label>
				<div class="col-sm-5">
					<input class="form-control" type="text" name="judul_dokumen" value="<?=set_value('judul_dokumen', @$dokumen['judul_dokumen'])?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">No Item</label>
				<div class="col-sm-5">
					<input class="form-control" type="text" name="no_item" value="<?=set_value('no_item', @$dokumen['no_item'])?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Uraian Isi Berkas</label>
				<div class="col-sm-5">
					<textarea class="form-control" rows="4" name="deskripsi_dokumen"><?=set_value('deskripsi_dokumen', @$dokumen['deskripsi_dokumen'])?></textarea>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">OPD</label>
					<div class="col-sm-5">
						<input class="form-control" type="text" name="opd" value="<?=set_value('opd', @$dokumen['opd'])?>" required="required"/>
					</div>
				</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Tangal Berkas</label>
				<div class="col-sm-5">
					<?php
					if (empty($dokumen['tgl_upload'])) {
						$tgl_upload = date('d-m-Y');
					} else {
						$exp = explode('-', $dokumen['tgl_upload']);
						$tgl_upload = $exp[2] . '-' . $exp[1] . '-' . $exp[0];
					}
					?>
					<input class="form-control flatpickr" type="text" name="tgl_upload" value="<?=set_value('tgl_upload', $tgl_upload)?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Tingkat Perkembangan</label>
				<div class="col-sm-5">
					<select class="form-control" name="tingkat_perkembangan" id="tingkat_perkembangan" required="required">
						<option value="">Pilih Tingkat Perkembangan</option>
						<option value="ASLI" <?= (@$dokumen['tingkat_perkembangan'] == 'ASLI') ? 'selected' : ''; ?>>ASLI</option>
						<option value="COPY" <?= (@$dokumen['tingkat_perkembangan'] == 'COPY') ? 'selected' : ''; ?>>COPY</option>
						<option value="other">Lainnya</option>
					</select>
					<input type="text" class="form-control mt-2" id="tingkat_perkembangan_other" name="tingkat_perkembangan_other" style="display:none;" placeholder="Masukkan tingkat perkembangan lainnya">
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Jumlah Berkas</label>
				<div class="col-sm-5">
					<input class="form-control" type="number" name="jumlah_berkas" value="<?=set_value('jumlah_berkas', @$dokumen['jumlah_berkas'])?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Kondisi Berkas</label>
				<div class="col-sm-5">
					<select class="form-control" name="kondisi_berkas" id="kondisi_berkas" required="required">
						<option value="">Pilih Kondisi Berkas</option>
						<option value="BAIK" <?= (@$dokumen['kondisi_berkas'] == 'BAIK') ? 'selected' : ''; ?>>BAIK</option>
						<option value="KURANG BAIK" <?= (@$dokumen['kondisi_berkas'] == 'KURANG BAIK') ? 'selected' : ''; ?>>KURANG BAIK</option>
						<option value="other">Lainnya</option>
					</select>
					<input type="text" class="form-control mt-2" id="kondisi_berkas_other" name="kondisi_berkas_other" style="display:none;" placeholder="Masukkan kondisi berkas lainnya">
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Tahun Inaktif</label>
				<div class="col-sm-5">
					<input class="form-control" type="number" name="inaktif" value="<?=set_value('inaktif', @$dokumen['inaktif'])?>" required="required"/>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Nasib Akhir</label>
				<div class="col-sm-5">
					<select class="form-control" name="nasib_akhir" id="nasib_akhir" required="required">
						<option value="">Pilih Nasib Akhir</option>
						<option value="MUSNAH" <?= (@$dokumen['nasib_akhir'] == 'MUSNAH') ? 'selected' : ''; ?>>MUSNAH</option>
						<option value="PERMANEN" <?= (@$dokumen['nasib_akhir'] == 'PERMANEN') ? 'selected' : ''; ?>>PERMANEN</option>
						<option value="DINILAI KEMBALI" <?= (@$dokumen['nasib_akhir'] == 'DINILAI KEMBALI') ? 'selected' : ''; ?>>DINILAI KEMBALI</option>
						<option value="other">Lainnya</option>
					</select>
					<input type="text" class="form-control mt-2" id="nasib_akhir_other" name="nasib_akhir_other" style="display:none;" placeholder="Masukkan nasib akhir lainnya">
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Penyimpanan</label>
				<div class="col-sm-5">
                    <?=options(['name' => 'id_dokumen_kategori', 'class' => 'list-kategori', 'style' => 'width:100%'], $list_kategori, set_value('id_dokumen_kategori', @$dokumen['id_dokumen_kategori']))?>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Dokumen</label>
				<div class="col-sm-5">
					<button class="btn btn-success add-file btn-xs mb-2" type="button"><i class="fas fa-plus me-2"></i>Tambah File</button>
					<div id="list-file-container">
						<?php

						if (key_exists('files', $dokumen)) {
							foreach($dokumen['files'] as $val) {
								if (!$val['nama_file']) {
									continue;
								}
							?>
								<div class="input-group mb-2">
									<input type="text" name="nama_file[]" class="form-control" placeholder="" aria-label="Choose File" aria-describedby="" value="<?=@$val['nama_file']?>" readonly>
									<input type="hidden" name="id_file_picker[]" class="id-file-picker" value="<?=$val['id_file_picker']?>"/>
									<button class="btn btn-danger del-file" type="button"><i class="fas fa-times"></i></button>
								</div>
							<?php
							}
						}?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-5">
					<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
					<input type="hidden" name="id" value="<?=@$id?>"/>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    ['tingkat_perkembangan', 'kondisi_berkas', 'nasib_akhir'].forEach(function(id) {
        var select = document.getElementById(id);
        var otherInput = document.getElementById(id + '_other');
        
        select.addEventListener('change', function() {
            if (this.value === 'other') {
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
            }
        });
    });
});
</script>