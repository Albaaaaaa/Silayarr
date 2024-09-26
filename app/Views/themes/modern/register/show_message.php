<?php 
$this->extend('themes/modern/register/layout');
$this->section('content');

$type = $message['status'] == 'error' ? 'danger' : 'success';
$title = $message['status'] == 'error' ? 'Error...' : 'Sukses...';
?>
<div class="card-header pb-3">
	<div class="logo">
		<img src="<?php echo $config->baseURL . 'public/images/' . $settingAplikasi['logo_register'] ?>?r=<?=time()?>">
	</div>
</div>
<div class="card-body">
	<div class="alert alert-last alert-<?=$type?>">
		<h4><?=$title?></h4>
		<?=$message['message']?>
	</div>
</div>
<?= $this->endSection() ?>