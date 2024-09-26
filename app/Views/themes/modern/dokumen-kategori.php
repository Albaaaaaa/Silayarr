<?php
helper('html');?>
<div class="card">
	<div class="card-header">
		<h5 class="card-title">Data Kategori</h5>
	</div>
	
	<div class="card-body">
		<a href="<?=$module_url?>" class="btn btn-success btn-xs" id="add-menu"><i class="fa fa-plus pe-1"></i> Tambah Kategori</a>
		<hr/>
		
		<?php

		if (!empty($message)) {
			show_message($message['content'], $message['status']);
		}
		?>
		
		<div class="dd" id="list-menu">
			<?=$list_kategori?>
		</div>

		<span style="display:none" id="url-delete"><?=$config->baseURL . 'builtin/menu/delete'?></span>
		<span style="display:none" id="url-edit"><?=$config->baseURL . 'builtin/menu/edit'?></span>
		<span style="display:none" id="url-detail"><?=$config->baseURL . 'builtin/menu/menuDetail?ajax=true&id='?></span>
	</div>
</div>