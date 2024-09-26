<?php

/* Create breadcrumb
$data: title as key, and url as value */ 

function list_files($dir, $subdir = false, $data = []) {
	
	$files = scandir($dir . '/' . $subdir);
	
	$result = $files;
	
	if ($subdir) {
		foreach ($result as &$val) {
			$val = $subdir . '/' . $val;
		}
	}
	
	$result = array_merge ($data, $result);
	
	
	
	foreach ($files as $file) {
		if ($file == '.' || $file == '..')
			continue;
		
		if (is_dir($dir . '/' . $subdir . '/' . $file)) {
			$nextdir = $subdir ?  $subdir . '/' . $file : $file;
			$result = list_files($dir, $nextdir, $result, true);
		}
	}
	
	
	return $result;
}

function delete_file($path) 
{
	if (file_exists($path)) {
		$unlink = unlink($path);
		if ($unlink) {
			return true;
		}
		return false;
	}
	
	return true;
}

if (!function_exists('breadcrumb')) {
function breadcrumb($data) 
{
	$separator = '&raquo;';
	echo '<nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="8" height="8"%3E%3Cpath d="M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z" fill="%236c757d/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
  <ol class="breadcrumb shadow-sm">';
	foreach ($data as $title => $url) {
		if ($url) {
			echo '<li class="breadcrumb-item"><a href="'.$url.'">'.$title.'</a></li>';
		} else {
			echo '<li class="breadcrumb-item active" aria-current="page">'.$title.'</li>';
		}
	}
	echo '
  </ol>
</nav>';
}
}

if (!function_exists('set_value')) {
	function set_value($field_name, $default = '') 
	{
		$request = array_merge($_GET, $_POST);
		$search = $field_name;

		// If Array
		$is_array = false;
		if (strpos($search, '[')) {
			$is_array = true;
			$exp = explode('[', $field_name);
			$field_name = $exp[0];
			
		}

		if (isset($request[$field_name])) {
			if ($is_array) {
				$exp_close = explode(']', $exp[1]);
				$index = $exp_close[0];
				return $request[$field_name][$index];
			}
			return $request[$field_name];
		}
		return $default;
	}
}


function format_tanggal($date, $format = 'dd mmmm yyyy', $using_time = true) 
{
	if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == '')
		return $date;
	
	$time = '';
	// Date time
	if (strlen($date) == 19) {
		$exp = explode(' ', $date);
		$date = $exp[0];
		$time = ' ' . $exp[1];
	}

	$format = strtolower($format);
	$new_format = $date;
	
	list($year, $month, $date) = explode('-', $date);
	if (strpos($format, 'dd') !== false) {
		$new_format = str_replace('dd', $date, $format);
	}
	
	if (strpos($format, 'mmmm') !== false) {
		$bulan = nama_bulan();
		$new_format = str_replace('mmmm', $bulan[ ($month * 1) ], $new_format);
	} else if (strpos($format, 'mm') !== false) {
		$new_format = str_replace('mm', $month, $new_format);
	}
	
	if (strpos($format, 'yyyy') !== false) {
		$new_format = str_replace('yyyy', $year, $new_format);
	}
	if (!$using_time) {
		$time = '';
	}
	return $new_format . $time;
}

function get_theme_mode() {
	if (!empty($_COOKIE['jwd_adm_theme'])) {
		return $_COOKIE['jwd_adm_theme'];
	}
	return 'light';
}

function prepare_datadb($data) {
	foreach ($data as $field) {
		$result[$field] = $_POST[$field];
	}
	return $result;
}

function theme_url() {
	
	return $config['base_url'] . 'themes/modern' ;
}

function module_url($action = false) {
	
	$config = new \Config\App();
	$url = $config->baseURL;
	
	$session = session();
	$web = $session->get('web');
	$nama_module = $web['nama_module'];

	$url .= $nama_module;
		
	if (!empty($_GET['action']) && $_GET['action'] != 'index' && $action) {
		$url .= $_GET['action'];
	}

	return $url;
}

function cek_hakakses($action, $param = false) 
{
	global $list_action;
	global $app_module;
	
	$allowed = $list_action[$action];
	if ($allowed == 'no') {
		// echo 'Anda tidak berhak mengakses halaman ini ' . $app_module['judul_module']; die;
		$app_module['nama_module'] = 'error';
		load_view('views/error.php', ['status' => 'error', 'message' => 'Anda tidak berhak mengakses halaman ini']);
	}
}
/*
	$message = ['status' => 'ok', 'message' => 'Data berhasil disimpan'];
	show_message($message);
	
	$msg = ['status' => 'ok', 'content' => 'Data berhasil disimpan'];
	show_message($msg['content'], $msg['status']);
	
	$error = ['role_name' => ['Data sudah ada di database', 'Data harus disi']];
	show_message($error, 'error');
	
	$error = ['Data sudah ada di database', 'Data harus disi'];
	show_message($error, 'error');
*/
function show_message($message, $type = null, $dismiss = true) {
	//<ul class="list-error">
	if (is_array($message)) {
		
		// $message = ['status' => 'ok', 'message' => 'Data berhasil disimpan'];
		if (key_exists('status', $message)) 
		{
			$type = $message['status'];
			if (key_exists('message', $message)) {
				$message_source = $message['message'];
			} else if (key_exists('content', $message)) {
				$message_source = $message['content'];
			}
			
			
			if (is_array($message_source)) {
				$message_content = $message_source;
			} else {
				$message_content[] = $message_source;
			}
		
		} else {
			if (is_array($message)) {
				foreach ($message as $key => $val) {
					if (is_array($val)) {
						foreach ($val as $key2 => $val2) {
							$message_content[] = $val2;
						}
					} else {
						$message_content[] = $val;
					}
				}
			}
		}
		// print_r($message_content);
		if (count($message_content) > 1) {
			
			$message_content = recursive_loop($message_content);
			$message = '<ul><li>' . join('</li><li>', $message_content) . '</li></ul>';
		}
		else {
			// echo '<pre>'; print_r($message_content);
			$message_content = recursive_loop($message_content);
			// echo '<pre>'; print_r($message_content);
			$message = $message_content[0];
		}
	}
	
	switch ($type) {
		case 'error' :
			$alert_type = 'danger';
			break;
		case 'warning' :
			$alert_type = 'warning';
			break;
		default:
			$alert_type = 'success';
			break;
	}
	
	$close_btn = '';
	if ($dismiss) {
		$close_btn = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'; 
	} 

	echo '<div class="alert alert-dismissible fade show alert-'.$alert_type.'" role="alert">'. $message . $close_btn . '</div>';
}

function recursive_loop($array, $result = []) {
	// echo '<pre>'; print_r($array);
	foreach ($array as $val) {
		if (is_array($val)) {
			$result = recursive_loop($val, $result);
		} else {
			$result[] = $val;
		}
		// echo '<pre>'; print_r($result);
	}
	return $result;
}


function show_alert($message, $title = null, $dismiss = true) {

	if (is_array($message)) 
	{
		// $message = ['status' => 'ok', 'message' => 'Data berhasil disimpan'];
		if (key_exists('status', $message)) {
			$type = $message['status'];
		}

		if (key_exists('message', $message)) {
			$message = $message['message'];
		}
		
		if (is_array($message)) {
			foreach ($message as $key => $val) {
				if (is_array($val)) {
					foreach ($val as $key2 => $val2) {
						$message_content[] = $val2;
					}
				} else {
					$message_content[] = $val;
				}
			}
			
			if (count($message_content) > 1) {
				$message = '<ul><li>' . join($message_content, '</li><li>') . '</li></ul>';
			}
			else {
				$message = $message_content[0];
			}
		}
	}
	
	if (!$title) {
		switch ($type) {
			case 'error' :
				$title = 'ERROR !!!';
				$icon_type = 'error';
				break;
			case 'warning' :
				$title = 'WARNIG !!!';
				$icon_type = 'error';
				break;
			default:
				$title = 'SUKSES !!!';
				$icon_type = 'success';
				break;
		}
	}
	
	echo '<script type="text/javascript">
			Swal.fire({
				title: "'.$title.'",
				html: "'.$message.'",
				icon: "'.$icon_type.'",
				showCloseButton: '.$dismiss.',
				confirmButtonText: "OK"
			})
		</script>';
}

function nama_bulan() {
	return [1=> 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
}

function is_ajax_request() {
	if (key_exists('HTTP_X_REQUESTED_WITH', $_SERVER)) {
		return $_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	return false;
}

function format_date($tgl, $nama_bulan = true) {
	if ($tgl == '0000-00-00 00:00:00' || !$tgl) {
		return false;
	}
	$exp = explode (' ', $tgl);
	$exp_tgl = explode ('-', $exp[0]);
	$bulan = nama_bulan();
	return $exp_tgl[2] . ' ' . $bulan[ (int) $exp_tgl[1] ] . ' ' . $exp_tgl[0];
}

function format_number($value) 
{
	if ($value == '')
		return '';
	
	if (!is_numeric($value))
		return '';
	
	if ($value == 0)
		return 0;
	
	if (empty($value))
		return;
	$value = preg_replace('/\D/', '', $value);
	return number_format($value, 0, ',', '.');
}
function format_datedb($tgl) {
	if ($tgl == '0000-00-00 00:00:00' || !$tgl) {
		return false;
	}
	$exp = explode (' ', $tgl);
	$exp_tgl = explode ('-', $exp[0]);
	return $exp_tgl[2] . '-' . $exp_tgl[1] . '-' . $exp_tgl[0];
}

function download_authorization($user, $product) {
	// CHECK ACCOUNT
	$error = false;
	$session = \Config\Services::session();
	if ($session->user) 
	{
		if ($product->account_type == 'premium')
		{
			if ($user->account_type == 'free') {
				$error = 'Product ini khusus untuk member Premium, silakan upgrade akun Anda';
			} else {
				$expired = $user->account_expires;
				if ($expired < date('Y-m-d H:i:s') && $product->publish_date > $expired) {
					$error = 'Akun premium Anda sudah expired, silahkan diperbarui';
				}
			}
		}
	} else {
		$error = 'Silakan login terlebih dahulu';
	}
	
	return $error;
}

function format_size($size) {
	if ($size > 1024 * 1024) {
		return round($size / (1024 * 1024), 2) . 'Mb';
	} else {
		return round($size / 1024, 2) . 'Kb';
	}
}

function comment_list($result)
{
	$refs = array();
	$list = array();

	foreach ($result as $key => $data)
	{
		$thisref = &$refs[ $data['comment_id'] ];
		foreach ($data as $field => $value) {
			$thisref[$field] = $value;
		}

		// no parent
		if ($data['parent_id'] == 0) {
			
			$list[ $data['comment_id'] ] = &$thisref;
		} else {
			@$thisref['depth'] = ++$refs[ $data['comment_id']]['depth'];			
			$refs[ $data['parent_id'] ]['children'][ $data['comment_id'] ] = &$thisref;
		}
	}
	set_depth($list);	
	return $list;
}

function set_depth(&$result, $depth = 0) {
	foreach ($result as $key => &$val) 
	{
		$val['depth'] = $depth;
		if (key_exists('children', $val)) {
			set_depth($val['children'], $val['depth'] + 1);
		}
	}
}

function has_permission($permission) {
	return in_array($permission, $_SESSION['user']['permission']);
}

function kategori_list($result)
{
	// print_r($result); 
	$refs = array();
	$list = array();

	foreach ($result as $key => $data)
	{
		if (!$key || empty($data['id_dokumen_kategori'])) // Highlight OR No parent
			continue;
		
		$thisref = &$refs[ $data['id_dokumen_kategori'] ];
		foreach ($data as $field => $value) {
			$thisref[$field] = $value;
		}

		// no parent
		if ($data['id_parent'] == 0) {
			
			$list[ $data['id_dokumen_kategori'] ] = &$thisref;
		} else {
			
			$thisref['depth'] = ++$refs[ $data['id_dokumen_kategori']]['depth'];			
			$refs[ $data['id_parent'] ]['children'][ $data['id_dokumen_kategori'] ] = &$thisref;
		}
	}
	set_depth($list);	
	return $list;
}

function menu_list($result)
{
	$refs = array();
	$list = array();
	// echo '<pre>'; print_r($result);
	foreach ($result as $key => $data)
	{
		if (!$key || empty($data['id_menu'])) // Highlight OR No parent
			continue;
		
		$thisref = &$refs[ $data['id_menu'] ];
		foreach ($data as $field => $value) {
			$thisref[$field] = $value;
		}

		// no parent
		if ($data['id_parent'] == 0) {
			
			$list[ $data['id_menu'] ] = &$thisref;
		} else {
			
			$thisref['depth'] = ++$refs[ $data['id_menu']]['depth'];			
			$refs[ $data['id_parent'] ]['children'][ $data['id_menu'] ] = &$thisref;
		}
	}
	set_depth($list);	
	return $list;
}

function build_menu( $current_module, $arr_menu, $submenu = false)
{
	$menu = "\n" . '<ul'.$submenu.'>'."\r\n";

	foreach ($arr_menu as $key => $val) 
	{
	// echo '<pre>ff'; print_r($arr); die;
		if (!$key)
			continue;
	
		// Check new
		$new = '';
		if (key_exists('new', $val)) {
			$new = $val['new'] == 1 ? '<span class="menu-baru">NEW</span>' : '';
		}
		$arrow = key_exists('children', $val) ? '<span class="pull-right-container">
								<i class="fa fa-angle-left arrow"></i>
							</span>' : '';
		$has_child = key_exists('children', $val) ? 'has-children' : '';
		
		if ($has_child) {
			$url = '#';
			$onClick = ' onclick="javascript:void(0)"';
		} else {
			$onClick = '';
			$url = $val['url'];
		}
		
		// class attribute for <li>
		$class_li = [];		
		if ($current_module['nama_module'] == $val['nama_module']) {
			$class_li[] = 'tree-open';
		}
		
		if ($val['highlight']) {
			$class_li[] = 'highlight tree-open';
		}
		
		if ($class_li) {
			$class_li = ' class="' . join(' ', $class_li) . '"';
		} else {
			$class_li = '';
		}
		
		// Class attribute for <a>, children of <li>
		$class_a = ['depth-' . $val['depth']];
		if ($has_child) {
			$class_a[] = 'has-children';
		}
		
		$class_a = ' class="' . join(' ', $class_a) . '"';
		
		// Menu icon
		$menu_icon = '';
		if ($val['class']) {
			$menu_icon = '<i class="sidebar-menu-icon ' . $val['class'] . '"></i>';
		}

		// Menu
		$config = new \Config\App();
		
		if (substr($url, 0, 4) != 'http') {
			$url = $config->baseURL . $url;
		}
		$menu .= '<li'. $class_li . '>
					<a '.$class_a.' href="'. $url . '"'.$onClick.'>'.
						'<span class="menu-item">' .
							$menu_icon.
							'<span class="text">' . $val['nama_menu'] . '</span>' .
						'</span>' . 
						$arrow.
					'</a>'.$new;
		
		if (key_exists('children', $val))
		{ 	
			$menu .= build_menu($current_module, $val['children'], ' class="submenu"');
		} 
		$menu .= "</li>\n";
	}
	$menu .= "</ul>\n";
	return $menu;
}

function email_content($content) 
{
		return '<html>
<head>
<style>
body{
	font-family: "segoe ui", "open sans", arial;
	font-size: 16px;
}
h1, h2, h3, h4, h5, h6, ul, ol, p {
    margin: 0;
    padding: 0;
}
ul.list-circle {
	list-style: circle;
}
ul.list-circle li{
	margin-left: 25px;
}
h1 {
	font-weight: normal;
    font-size: 200%;
}
h2 {
	font-weight: normal;
    font-size: 150%;
}
.box-title {
	text-align: center;
}
ul li{
	font-size: 16px;
}
.button {
	text-decoration:none;
	display:inline-block;
	margin-bottom:0;
	font-weight:normal;
	text-align:center;
	vertical-align:middle;
	background-image:none;
	border:1px solid transparent;
	white-space:nowrap;
	padding:7px 15px;
	line-height:1.5384616;
	background-color:#0277bd;
	border-color:#0277bd;
	color:#FFFFFF;
}
.button span {
	font-family:arial,helvetica,sans-serif;
	font-size: 16px;
	color:#FFFFFF;
}
p {
	font-size: 16px;
	line-height: 1.5;
    margin: 15px 0;
}
mb-15{
	margin-bottom:15px;
}
hr {
	border: 0;
    border-bottom: 1px solid #CCCCCC;
}
.mt-10 { margin-top: 10px}
.mb-5 { margin-bottom: 5px }
.mb-10 { margin-bottom: 10px }
.mb-20 { margin-bottom: 20px }
.mb-40 { margin-bottom: 40px }
p {
	margin-top: 7px;
	margin-bottom: 7px;
}
.thankyou h1 {
	font-weight: normal;
    font-size: 200%;
}
.thankyou h2 {
	font-weight: normal;
    font-size: 150%;
}
.thankyou h3 {
	font-weight: normal;
    font-size: 120%;
}
.aligncenter  {
	text-align: center;
}
.alert {
    display: inline-block;
    margin-bottom: 0;
    font-weight: normal;
    text-align: left;
    vertical-align: middle;
    background-image: none;
    border: 1px solid transparent;
    padding: 7px 15px;
    line-height: 1.5384616;
    background-color: #ffb4b4;
    border-color: #ff9c9c;
	color: #c34949;
	font-size: 16px;
}
</style>
</head>
<body>' . $content . '</body>
</html>';
	}