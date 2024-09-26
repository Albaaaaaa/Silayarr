<?= $this->extend('themes/modern/register/layout') ?>
<?= $this->section('content') ?>
<div class="card-header">
	<div class="logo">
		<img src="<?php echo $config->baseURL . 'public/images/' . $settingAplikasi['logo_register'] ?>?r=<?=time()?>">
	</div>
</div>
<div class="card-body">
	<?php
	if (@$message) {
		show_message($message);
	}
	?>
	<p>Halaman ini digunakan untuk reset password Akun. Silakan isikan alamat email Anda pada form di bawah ini, kami akan mengirimkan link reset password ke alamat email Anda</p>

	<form method="post" action="<?=current_url()?>">
	<div class="mb-3">
		<input type="email"  name="email" value="<?=set_value('email')?>" class="form-control register-input" placeholder="Email" aria-label="Email" required>
	</div>
	<div class="mb-3" style="margin-bottom:0">
		<button type="submit" name="submit" value="submit" class="btn btn-success" style="display:block;width:100%">Submit</button>
		<?=csrf_formfield()?>
	</div>
	</form>
</div>
<?= $this->endSection() ?>