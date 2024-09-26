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
		<form method="post" action="" class="form-horizontal">
			<div class="tab-content" id="myTabContent">
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Nama</label>
					<div class="col-sm-8 form-inline">
						<span><?=$user['nama']?></span>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Password Lama</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="password" name="password_old" required="required"/>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Password Baru</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="password" name="password_new" required="required"/>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Ulangi Password Baru</label>
					<div class="col-sm-8 form-inline">
						<input class="form-control" type="password" name="password_new_confirm" required="required"/>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-8">
						<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
						<input type="hidden" name="id" value="<?=@$_GET['id']?>"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>