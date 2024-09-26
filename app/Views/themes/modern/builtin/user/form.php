<div class="card">
	<div class="card-header">
		<h5 class="card-title"><?=$title?></h5>
	</div>
	
	<div class="card-body">
		<?php 
			helper('html');
			helper('builtin/util');
			if (in_array('create', $user_permission)) {
				echo btn_link(['attr' => ['class' => 'btn btn-success btn-xs'],
					'url' => $module_url . '/add',
					'icon' => 'fa fa-plus',
					'label' => 'Tambah User'
				]);
				
				echo btn_link(['attr' => ['class' => 'btn btn-success btn-xs'],
					'url' => $module_url . '/uploadexcel',
					'icon' => 'fas fa-arrow-up-from-bracket',
					'label' => 'Upload Excel'
				]);
			}
			
			echo btn_link(['attr' => ['class' => 'btn btn-light btn-xs'],
				'url' => $module_url,
				'icon' => 'fa fa-arrow-circle-left',
				'label' => 'Daftar User'
			]);
			
			if (in_array('update_own', $user_permission)) {
				echo btn_link(['attr' => ['class' => 'btn btn-danger btn-xs'],
				'url' => base_url() . '/builtin/user/edit-password',
				'icon' => 'fas fa-edit',
				'label' => 'Ganti Password'
			]);
			}
		?>
		<hr/>
		<?php
		if (!empty($message)) {
			show_message($message);
		}
		?>
		<form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
			<div class="tab-content">
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Foto</label>
					<div class="col-sm-8 col-md-6 col-lg-4">
						<?php 
						$avatar = @$_FILES['file']['name'] ?: @$user_edit['avatar'];
						if (!empty($avatar) && file_exists(ROOTPATH . 'public/images/user/' . $avatar) ) {
							echo '<div class="img-choose" style="margin:inherit;margin-bottom:10px">
									<div class="img-choose-container">
										<img src="'.$config->baseURL. '/public/images/user/' . $avatar . '?r=' . time() . '"/>
										<a href="javascript:void(0)" class="remove-img"><i class="fas fa-times"></i></a>
									</div>
								</div>
								';
						}
						?>
						<input type="hidden" class="avatar-delete-img" name="avatar_delete_img" value="0">
						<input type="file" class="file form-control" name="avatar">
							<?php if (!empty($form_errors['avatar'])) echo '<small style="display:block" class="alert alert-danger mb-0">' . $form_errors['avatar'] . '</small>'?>
						<small class="small" style="display:block">Maksimal 300Kb, Minimal 100px x 100px, Tipe file: .JPG, .JPEG, .PNG</small>
						<div class="upload-file-thumb mb-2"><span class="file-prop"></span></div>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Username</label>
					<div class="col-sm-8 col-md-6 col-lg-4">
						<?php 
						$readonly = 'readonly="readonly" class="disabled"';
						if (@$user_permission['update_all']) {
							$readonly = '';
						}
						?>
						<input class="form-control" type="text" name="username" <?=$readonly?> value="<?=set_value('username', @$user_edit['username'])?>" placeholder="" required="required"/>
						<input type="hidden" name="username_lama" value="<?=set_value('username', @$user_edit['username'])?>" />
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Nama</label>
					<div class="col-sm-8 col-md-6 col-lg-4">
						<input class="form-control" type="text" name="nama" value="<?=set_value('nama', @$user_edit['nama'])?>" placeholder="" required="required"/>
					</div>
				</div>
				<div class="row mb-3">
					<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Email</label>
					<div class="col-sm-8 col-md-6 col-lg-4">
						<input class="form-control" type="text" name="email" value="<?=set_value('email', @$user_edit['email'])?>" placeholder="" required="required"/>
						<input type="hidden" name="email_lama" value="<?=set_value('email', @$user_edit['email'])?>" />
					</div>
				</div>
				<?php
				if (@$user_permission['update_all']) {
					?>
					<div class="row mb-3">
						<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Verified</label>
						<div class="col-sm-8 col-md-6 col-lg-4">
							<?php
							if (!isset($user_edit['verified']) && !key_exists('verified', $_POST) ) {
								$selected = 1;
							} else {
								$selected = set_value('verified', @$user_edit['verified']);
							}
							?>
							<?php echo options(['name' => 'verified'], [1=>'Ya', 0 => 'Tidak'], $selected); ?>
						</div>
					</div>
					<div class="row mb-3">
						<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Status</label>
						<div class="col-sm-8 col-md-6 col-lg-4">
							<?php echo options(['name' => 'status'], ['active' => 'Aktif', 'suspended' => 'Suspended', 'deleted' => 'Deleted'], set_value('status', @$user_edit['status'])); ?>
						</div>
					</div>
					<div class="row mb-3">
						<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Role</label>
						<div class="col-sm-8 col-md-6 col-lg-4">
							<?php
							foreach ($roles as $key => $val) {
								$role_options[$val['id_role']] = $val['judul_role'];
							}
							
							if (!empty($user_edit['role'])) {
								foreach ($user_edit['role'] as $val) {
									$id_role_selected[] = $val['id_role'];
								}
							}
							
							if (!empty($_POST) && empty($_POST['id_role'])) {
								$selected = '';
							} else {
								$selected = set_value('id_role', @$id_role_selected);
							}
							
							echo options(['name' => 'id_role[]', 'multiple' => 'multiple', 'class' => 'select2 select-role'], $role_options, set_value('id_role', @$id_role_selected));
							?>
						</div>
					</div>
					<div class="row mb-3">
						<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Halaman Default</label>
						<div class="col-sm-8 col-md-6 col-lg-4">
							<?php
							if (empty(@$user_edit['default_page_type'])) {
								$user_edit['default_page_type'] = 'id_module';
								$user_edit['id_module'] = 5;
							}
							$default_page_type = set_value('option_default_page', @$user_edit['default_page_type']);
							?>
							<?=options(['name' => 'option_default_page', 'id' => 'option-default-page', 'class' => 'mb-2'], ['url' => 'URL', 'id_module' => 'Module', 'id_role' => 'Role'], $default_page_type )?>
							<?php
							$display_url = $default_page_type == 'url' ? '' : ' style="display:none"';
							$display_module = $default_page_type == 'id_module' ? '' : ' style="display:none"';
							$display_role = $default_page_type == 'id_role' ? '' : ' style="display:none"';
							
							?>
							<div class="default-page-url default-page" <?=$display_url?>>
								<input type="text" class="form-control" name="default_page_url" value="<?=set_value('default_page_url', @$user_edit['default_page_url'])?>"/>
								<small>Gunakan {{BASE_URL}} untuk menggunakan base url aplikasi, misal: {{BASE_URL}}builtin/user/edit?id=1</small>
							</div>
							<div class="default-page-id-module default-page" <?=$display_module?>>
								<?php
								foreach ($list_module as $val) {
									$options[$val['id_module']] = $val['nama_module'] . ' - ' . $val['judul_module'];
								}
								
								if (!@$user_edit['default_page_id_module']) {
									$user_edit['default_page_id_module'] = 5;
								}
								
								echo options(['name' => 'default_page_id_module'], $options, set_value('default_page_id_module', @$user_edit['default_page_id_module'])); 
								?>
								<span class="text-muted">Pastikan user memiliki hak akses ke module</span>
							</div>
							<?php
							$default_page_role = [];
							if (!empty($user_edit['role'])) {
								foreach ($user_edit['role'] as $val) {
									$default_page_role[$val['id_role']] = $val['judul_role'];
								}
							}
							if (!$default_page_role) {
								$default_page_role = ['' => '-- Pilih Role --'];
							}
							?>
							<div class="default-page-id-role default-page" <?=$display_role?>>
								<?=options(['name' => 'default_page_id_role'], $default_page_role, set_value('default_page_id_role', @$user_edit['default_page_id_role']));?>
								<small>Halaman default sama dengan halaman default <a title="Halaman Role" href="<?=base_url() . '/builtin/role'?>" target="blank">role</a></small>
							</div>
						</div>
					</div>
					<?php
					if (!empty($_GET['id'])) { ?>
						<div class="row mb-3">
							<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Ubah Password</label>
							<div class="col-sm-8 col-md-6 col-lg-4">
								<?= options(['name' => 'option_ubah_password', 'id' => 'option-ubah-password'], ['N' => 'Tidak', 'Y' => 'Ya'], set_value('option_ubah_password', '')) ?>
							</div>
						</div>
						<?php
					}
					$display = (!empty($_POST['option_ubah_password']) && $_POST['option_ubah_password'] == 'Y') || empty($_GET['id']) ? '' : ' style="display:none"';
						?>
						
					<div id="password-container" <?=$display?>>
						<div class="row mb-3">
							<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Password Baru</label>
							<div class="col-sm-8 col-md-6 col-lg-4">
								<input class="form-control" type="password" name="password" value="<?=set_value('password', '')?>"/>
							</div>
						</div>
						<div class="row mb-3">
							<label class="col-sm-3 col-md-2 col-lg-3 col-xl-2 col-form-label">Ulangi Password Baru</label>
							<div class="col-sm-8 col-md-6 col-lg-4">
								<input class="form-control" type="password" name="ulangi_password" value="<?=set_value('ulangi_password', '')?>"/>
							</div>
						</div>
					</div>
				<?php
				}
				// echo  @$user_edit['id_module']; die;
				?>
				<div class="row">
					<div class="col-sm-8">
						<button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
						<input type="hidden" name="id" value="<?=@$user_edit['id_user']?>"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>