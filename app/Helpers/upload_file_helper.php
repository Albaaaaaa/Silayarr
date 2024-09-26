<?php
function get_filename($file_name, $path) {
	
	$file_name_path = $path . $file_name;
	if ($file_name != "" && file_exists($file_name_path))
	{
		$file_ext = strrchr($file_name, '.');
		$file_basename = substr($file_name, 0, strripos($file_name, '.'));
		$num = 1;
		while (file_exists($file_name_path)){
			$file_name = $file_basename."($num)".$file_ext;
			$num++;
			$file_name_path = $path . $file_name;
		}
		
		return $file_name;
	}
	return $file_name;
}

function upload_image($path, $file, $max_w = 500, $max_h = 500) 
{
	
	$file_type = $file['type'];
	$new_name =  get_filename(stripslashes($file['name']), $path); ;
	$move = move_uploaded_file($file['tmp_name'], $path . $new_name);
	
	$save_image = false;
	if ($move) {
		$dim = image_dimension($path . $new_name, $max_w, $max_h);
		$save_image = save_image($path . $new_name, $file_type, $dim[0], $dim[1]);
	}
	
	if ($save_image)
		return $new_name;
	else
		return false;
}

function image_dimension($images, $maxw=null, $maxh=null)
{
	if($images)
	{
		$img_size = @getimagesize($images);
		$w = $img_size[0];
		$h = $img_size[1];
		$dim = array('w','h');
		foreach($dim AS $val){
			$max = "max{$val}";
			if(${$val} > ${$max} && ${$max}){
				$alt = ($val == 'w') ? 'h' : 'w';
				$ratio = ${$alt} / ${$val};
				${$val} = ${$max};
				${$alt} = ${$val} * $ratio;
			}
		}
		return array($w,$h);
	}
}

function save_image($image, $file_type, $w, $h) 
{
	require_once(ROOTPATH . 'app/ThirdParty/imageworkshop/autoload.php');	
	$layer = PHPImageWorkshop\ImageWorkshop::initFromPath($image);
	$layer->resizeInPixel($w, $h, true);
	$name_path = pathinfo($image);
	$layer->save($name_path['dirname'], $name_path['basename'], false, false, 100);
	return true;
}

function upload_file($path, $file) 
{
	$new_name =  get_filename(stripslashes($file['name']), $path); ;
	$move = move_uploaded_file($file['tmp_name'], $path . $new_name);
	if ($move) 
		return $new_name;
	else
		return false;
}

function create_thumbnail($path, $file_name, $add_name, $maxw=null, $maxh=null) 
{
	$file_path = pathinfo($file_name);
	$new_file_name = $file_path['filename'] . '_' . $add_name . '.' . $file_path['extension'];
	$dim = image_dimension ($path . $file_name, $maxw);
	$image_type = @getimagesize($path . $file_name);
	
	$save_image = save_image($path . $file_name, $path . $new_file_name, $image_type['mime'], ceil($dim[0]), ceil($dim[1]) );
	if ($save_image) {
		return $new_file_name;
	} else
		return false;
	
}