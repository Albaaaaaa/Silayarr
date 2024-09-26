<?php 

if (empty($_SESSION['user'])) {
	$content = 'Layout halaman ini memerlukan login';
	include ('app/Views/themes/modern/header-error.php');
	exit;
}
?>
<!DOCTYPE HTML>
<html lang="en" data-bs-theme="<?=$theme_mode?>">
<head>
<title><?=$current_module['judul_module']?> | <?=$settingAplikasi['judul_web']?></title>
<meta name="descrition" content="<?=$current_module['deskripsi']?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="<?=$config->baseURL . 'public/images/favicon.png?r='.time()?>" />
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/vendors/fontawesome/css/all.css'?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/vendors/bootstrap/css/bootstrap.min.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/vendors/bootstrap-icons/bootstrap-icons.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/vendors/sweetalert2/sweetalert2.min.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/vendors/overlayscrollbars/OverlayScrollbars.min.css?r='.time()?>"/>

<!-- Data Tables -->
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/vendors/datatables/dist/css/dataTables.bootstrap5.min.css?r='.time()?>"/>
<!-- // Data Tables -->



<link rel="stylesheet" id="style-switch-bootswatch" type="text/css" href="<?=$config->baseURL . 'public/vendors/bootswatch/'. ( $theme_mode == 'light' ? $app_layout['bootswatch_theme'] : 'default' ) .'/bootstrap.min.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/themes/modern/builtin/css/site.css?r='.time()?>"/>
<link rel="stylesheet" id="font-switch" type="text/css" href="<?=$config->baseURL . 'public/themes/modern/builtin/css/fonts/'.$app_layout['font_family'].'.css?r='.time()?>"/>
<link rel="stylesheet" id="style-switch" type="text/css" href="<?=$config->baseURL . 'public/themes/modern/builtin/css/color-schemes/'.$app_layout['color_scheme'].'.css?r='.time()?>"/>
<link rel="stylesheet" id="style-switch-sidebar" type="text/css" href="<?=$config->baseURL . 'public/themes/modern/builtin/css/color-schemes/'.$app_layout['sidebar_color'].'-sidebar.css?r='.time()?>"/>
<link rel="stylesheet" id="logo-background-color-switch" type="text/css" href="<?=$config->baseURL . 'public/themes/modern/builtin/css/color-schemes/'.$app_layout['logo_background_color'].'-logo-background.css?r='.time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/themes/modern/builtin/css/bootstrap-custom.css?r=' . time()?>"/>
<link rel="stylesheet" type="text/css" href="<?=$config->baseURL . 'public/themes/modern/builtin/css/dark-theme.css?r='.time()?>"/>

<style type="text/css">
html, body {
	font-size: <?=$app_layout['font_size']?>px;
}
</style>
<!-- Dynamic styles -->
<?php
if (@$styles) {
	foreach($styles as $file) {
		if (is_array($file)) {
			$attr = '';
			if (key_exists('attr', $file)) {
				foreach($file['attr'] as $param => $val) {
					$attr .= $param . '="' . $val . '"';
				}
			}
			$file = $file['url'];
			echo '<link rel="stylesheet" ' . $attr . ' type="text/css" href="'.$file.'?r='.time().'"/>' . "\n";
		} else {
			echo '<link rel="stylesheet" type="text/css" href="'.$file.'?r='.time().'"/>' . "\n";
		}
	}
}

?>
<script type="text/javascript">
	let base_url = "<?=$config->baseURL?>";
	let module_url = "<?=$module_url?>";
	let current_url = "<?=current_url()?>";
	let theme_url = "<?=$config->baseURL . '/public/themes/modern/builtin/'?>";
	let current_bootswatch_theme = "<?=$app_layout['bootswatch_theme']?>";
</script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/jquery/jquery.min.js'?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/bootstrap/js/bootstrap.bundle.min.js'?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/bootbox/bootbox.min.js'?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/sweetalert2/sweetalert2.min.js'?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/overlayscrollbars/jquery.overlayScrollbars.min.js'?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/js.cookie/js.cookie.min.js'?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/themes/modern/builtin/js/functions.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/themes/modern/builtin/js/site.js?r='.time()?>"></script>

<!-- Data Tables -->
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/datatables/dist/js/jquery.dataTables.min.js?r='.time()?>"></script>
<script type="text/javascript" src="<?=$config->baseURL . 'public/vendors/datatables/dist/js/dataTables.bootstrap5.min.js?r='.time()?>"></script>
<!-- // Data Tables -->

<!-- Dynamic scripts -->
<?php
if (@$scripts) {
	foreach($scripts as $file) {
		if (is_array($file)) {
			if ($file['print']) {
				echo '<script type="text/javascript">' . $file['script'] . '</script>' . "\n";
			}
		} else {
			echo '<script type="text/javascript" src="'.$file.'?r='.time().'"></script>' . "\n";
		}
	}
}

$user = $_SESSION['user'];

?>
</head>
<body class="<?=@$_COOKIE['jwd_adm_mobile'] ? 'mobile-menu-show' : ''?>">
	<header class="nav-header shadow">
		<div class="nav-header-logo pull-left">
			<a class="header-logo" href="<?=$config->baseURL?>" title="Jagowebdev">
				<img src="<?=$config->baseURL . '/public/images/' . $settingAplikasi['logo_app']?>"/>
			</a>
		</div>
		<div class="pull-left nav-header-left">
			<ul class="nav-header">
				<li>
					<a href="#" id="mobile-menu-btn">
						<i class="fa fa-bars"></i>
					</a>
				</li>
			</ul>
		</div>
		<div class="pull-right mobile-menu-btn-right">
			<a href="#" id="mobile-menu-btn-right">
				<i class="fa fa-ellipsis-h"></i>
			</a>
		</div>
		<div class="pull-right nav-header nav-header-right">
			<ul class="d-flex align-items-center">
				<li>
					<a class="icon-link" href="<?=$config->baseURL?>builtin/setting-layout"><i class="bi bi-gear"></i></a>
				</li>
				<li class="ps-2 nav-account">
					<?php 
					$img_url = !empty($user['avatar']) && file_exists(ROOTPATH . '/public/images/user/' . $user['avatar']) ? $config->baseURL . '/public/images/user/' . $user['avatar'] : $config->baseURL . '/public/images/user/default.png';
					$account_link = $config->baseURL . 'user';
					?>
					<a class="profile-btn" href="<?=$account_link?>" data-bs-toggle="dropdown"><img src="<?=$img_url?>" alt="user_img"></a>
						<?php
						if ($isloggedin) { 
							?>
							<ul class="dropdown-menu">
								<li class="dropdown-profile px-4 pt-4 pb-2">
									<div class="avatar">
										<a href="<?=$config->baseURL . 'builtin/user/edit?id=' . $user['id_user'];?>">
											<img src="<?=$img_url?>" alt="user_img">
										</a>
									</div>
									<div class="card-content mt-3">
									<p><?=strtoupper($user['nama'])?></p>
									<p><small>Email: <?=$user['email']?></small></p>
									</div>
								</li>
								<li>
									<a class="dropdown-item py-2" href="<?=$config->baseURL?>builtin/user/edit-password">Change Password</a>
								</li>
								<li>
									<li><a class="dropdown-item py-2" href="<?=$config->baseURL?>login/logout">Logout</a></li>
								</li>
							</ul>
						<?php } else { ?>
							<div class="float-login">
							<form method="post" action="<?=$config->baseURL?>login">
								<input type="email" name="email" value="" placeholder="Email" required>
								<input type="password" name="password" value="" placeholder="Password" required>
								<div class="checkbox">
									<label style="font-weight:normal"><input name="remember" value="1" type="checkbox">&nbsp;&nbsp;Remember me</label>
								</div>
								<button type="submit"  style="width:100%" class="btn btn-success" name="submit">Submit</button>
								<?php
								$form_token = $auth->generateFormToken('login_form_token_header');
								?>
								<input type="hidden" name="form_token" value="<?=$form_token?>"/>
								<input type="hidden" name="login_form_header" value="login_form_header"/>
							</form>
							<a href="<?=$config->baseURL . 'recovery'?>">Lupa password?</a>
							</div>
						<?php }?>
				</li>
			</ul>
		</div>
	</header>
	<div class="site-content">
		<div class="sidebar-guide">
			<div class="arrow" style="font-size:18px">
				<i class="fa-solid fa-angles-right"></i>
			</div>
		</div>
		<div class="sidebar shadow">
			<nav>
				<?php
				foreach ($menu as $val) {
					$list_menu = menu_list($val['menu']);
					
					if ($list_menu) {
						$kategori = $val['kategori'];
						if ($kategori['show_title'] == 'Y') {
							echo '<div class="menu-kategori">
									<div class="menu-kategori-wrapper">
										<h6 class="title">' . $kategori['nama_kategori'] . '</h6>';
										if ($kategori['deskripsi']) {
											echo '<small class="description">' . $kategori['deskripsi'] . '</small>';
										}
							echo '</div>
								</div>';
						}
						
						echo build_menu($current_module, $list_menu);
					}
				}
				?>
			</nav>
		</div>
		<div class="content">
		<?=!empty($breadcrumb) ? breadcrumb($breadcrumb) : ''?>
		<div class="content-wrapper">