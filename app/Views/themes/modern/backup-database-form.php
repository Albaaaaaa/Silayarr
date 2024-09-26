<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	<div class="card-body">
		<?php
			if (!empty($message)) {
					show_message($message);
		} 
		
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="row mb-3">
				<div class="col-sm-12 pt-2">
					Sistem akan membackup database <strong><?=$config_database->database?></strong>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-5">
					<button type="submit" name="submit" value="submit" class="btn btn-primary"><i class="fas fa-download me-2"></i>Download Backup</button>
				</div>
			</div>
		</form>
	</div>
</div>