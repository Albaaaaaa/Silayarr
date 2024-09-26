<?php

// options(['name' => 'gender'], ['M' => 'Male', 'F' => 'Female'], ['input_field', 'default'])
function options($attr, $data, $selected = '', $print = false) 
{
	if (empty($attr['class'])) {
		$attr['class'] = 'form-select';
	} else {
		$attr['class'] = $attr['class'] . ' form-select';
	}
	
	foreach ($attr as $key => $val) {
		$attribute[] = $key . '="' . $val . '"'; 
	}
	$attribute = join(' ', $attribute);
	
	if ($selected != '') {
		if (!is_array($selected)) {
			$selected = [$selected];
		}
	}
	
	$result = '
	<select '. $attribute .'>';
		foreach($data as $key => $value) 
		{
			$attr_option = '';
			if (is_array($value)) {
				$text = $value['text'];
				if (key_exists('attr', $value)) {
					$attr_option = ' ';
					foreach ($value['attr'] as $attr_key => $attr_val) {
						$attr_option .= $attr_key . '="' . $attr_val . '"'; 
					}
				}
				
			} else {
				$text = $value;
			}
			
			$option_selected = '';
			if ($selected != '') {
				if ( @empty($_REQUEST[$selected[0]]) ) {
					if (in_array( $key, $selected)) {
						$option_selected = true;
					}
				} else {
					if ($key == $_REQUEST[$selected[0]]) {
						$option_selected = true;
					}
				}
			}
			
			if ($option_selected) {
				$option_selected = ' selected';
			}
			$result .= '<option ' . $attr_option . ' value="'.$key.'"'.$option_selected.'>'.$text.'</option>';
		}
		
	$result .= '</select>';
	
	if ($print) {
		echo $result;
	} else {
		return $result;
	}
	
}
/*
checkbox(
	[
		'attr' => ['name' => $module[$id_module]['nama_module']
				, 'class=' => 'module-name'
				, 'id' => $module[$id_module]['nama_module']
			]
		, 'label' => '<strong>' . $module[$id_module]['judul_module'] . '</strong>'
	]
)

checkbox(
	[
		'attr_parent' => ['class' => 'ms-4']
		, 'attr' => ['name' => 'permission[]'
					,'class' => 'permission'
					, 'id' => $val['nama_permission']
				]
		, 'label' => $val['nama_permission']
	]
)

checkbox(
	[
		[
			'attr_parent' => ['class' => 'ms-4']
			, 'attr' => ['name' => 'permission[]'
						,'class' => 'permission'
						, 'id' => $val['nama_permission']
					]
			, 'label' => $val['nama_permission']
		],
		[
			'attr_parent' => ['class' => 'ms-4']
			, 'attr' => ['name' => 'permission[]'
						,'class' => 'permission'
						, 'id' => $val['nama_permission']
					]
			, 'label' => $val['nama_permission']
		]
	], $checked
)

*/
function checkbox($data, $checked = []) 
{
	
	if (!is_array($data)) {
		$data[] = ['attr' => ['name' => $data, 'id' => $data]];
	} else {
		if (!key_exists(0, $data)) {
			$clone = $data;
			$data = [];
			$data[] = $clone;
		}
	}
	
	$checkbox = '';
	foreach ($data as $key => $val) 
	{
		// Container
		$container_class = 'checkbox form-check mb-1';
		$attr_container = '';
		
		if (key_exists('attr_container', $val)) 
		{
			if (key_exists('class', $val['attr_container'])) {
				$container_class .= ' ' . $val['attr_container']['class'];
				unset ( $val['attr_container']['class'] );
			}
			
			foreach ($val['attr_container'] as $attr_name => $attr_value) {
				$attr_container[] = $attr_name . '=' . $attr_value;
			}
			
			if ($attr_container) {
				$attr_container = ' ' . join(' ', $attr_container);
			}
		}
		
		// Checkbox
		$attr_checked = '';
		if ($checked === true) {
			$attr_checked = 'checked';
		} else {
			if (is_array($checked)) {
				if (in_array($val['attr']['name'], $checked)) {
					$attr_checked = 'checked';
				}
			} else {
				if ($val['attr']['name'] == $checked) {
					$attr_checked = 'checked';
				}
			}
		}
		
		if (key_exists('class', $val['attr'])) {
			$val['attr']['class'] = $val['attr']['class'] . ' form-check-input';
		} else {
			$val['attr']['class'] = 'form-check-input';
		}
		
		$attr_checkbox = [];
		foreach ($val['attr'] as $attr_name => $attr_value) {
			$attr_checkbox[] = $attr_name . '="' . $attr_value . '"';
		}
		// echo '<pre>'; print_r($attr_checkbox); die;
		$attr_checkbox = ' ' . join(' ', $attr_checkbox) . ' ';
		
		$checkbox .= '<div class="'. $container_class .'"' . $attr_container . '>
			<input type="checkbox"'. $attr_checkbox . $attr_checked.' >
			<label class="form-check-label" for="'. $val['attr']['id'].'">' . $val['label'] . '</label>
		</div>';
	}
	
	return $checkbox;
}

function btn_submit($data = []) {
	$html = $attr = '';
	// echo '<pre>'; print_r($data);
	foreach ($data as $key => $val) {
		if (key_exists('attr', $val)) {
			foreach($val['attr'] as $key_attr => $val_attr) {
				$attr .= $key_attr . '="' . $val_attr . '"';
			}
		}
			
		$html .= '<button type="submit" class="btn '.$val['btn_class'].' btn-xs"'.$attr.'>
							<span class="btn-label-icon"><i class="'.$val['icon'].'"></i></span> '.$val['text'].'
			</button>';
	}
	
	return $html;
}

function btn_action($data = []) {

	$html = '<div class="form-inline btn-action-group">';
	$attr = '';
	foreach ($data as $key => $val) 
	{
		if ($key == 'edit') 
		{
			$btn_class = 'btn btn-success btn-xs me-1';
			if (!key_exists('attr', $val)) {
				
				 $val['attr'] = ['class' => $btn_class];
			}
			
			foreach ($val['attr'] as $attr_name => $attr_value) {
				if ($attr_name == 'class') {
					$attr_value = $btn_class . ' ' . $attr_value;
				}
				
				$attr .= $attr_name . '="' . $attr_value . '"';
			}
			
			$html .= '<a href="'.$data[$key]['url'].'" ' . $attr . '>
						<span class="btn-label-icon"><i class="fa fa-edit pe-1"></i></span> Edit
					</a>';
		}
		
		else if ($key == 'delete') {
			$html .= '<form method="post" action="'. $data[$key]['url'] .'">
					<button type="submit" data-action="delete-data" data-delete-title="'.$data[$key]['delete-title'].'" class="btn btn-danger btn-xs">
						<span class="btn-label-icon"><i class="fa fa-times pe-1"></i></span> Delete
					</button>
					<input type="hidden" name="delete" value="delete"/>
					<input type="hidden" name="id" value="'.$data[$key]['id'].'"/>
				</form>';
		}
		else {
			
			if (key_exists('attr', $data[$key])) {
				foreach($data[$key]['attr'] as $key_attr => $val_attr) {
					$attr .= $key_attr . '="' . $val_attr . '"';
				}
			}
			// print_r($attr); die;
			$html .= '<a href="'.$data[$key]['url'].'" class="btn '.$data[$key]['btn_class'].' btn-xs me-1" ' . $attr . '>
						<span class="btn-label-icon"><i class="'.$data[$key]['icon'].'"></i></span>&nbsp;'.$data[$key]['text'].'
					</a>';
			
		}
	}
	
	$html .= '</div>';
	return $html;
}

function btn_label($data) 
{
	$attr = [];
	if (key_exists('attr', $data)) {
		foreach($data['attr'] as $name => $value) {
			if ($name == 'class') {
				// $value = ' d-flex  ' . $value;
			}
			$attr[] = $name . '="' . $value . '"';
		}
	}
	
	$label = '';
	if (key_exists('label', $data)) {
		$label = $data['label'];
	}
	
	$icon = '';
	if (key_exists('icon', $data)) {
		$margin = $label ? ' me-1 ' : '';
		$icon = '<span class="btn-label-icon"><i class="' . $data['icon'] . $margin . '"></i></span> ';
	}
	
	if ($icon && $label) {
		$content = '<span class="d-flex">' . $icon. $label . '</span>';
	} else {
		$content = $icon. $label;
	}
	$html = '<button  type="button" ' . join(' ', $attr) . '>'.$content . '</button>';

	return $html;
}

/*
	btn_link(
		['icon' => 'fas fa-pencil-alt', 
			'attr' => ['class' => 'btn btn-success btn-xs'], 
			'url' => BASE_URL . $current_module['nama_module'] . '/edit?id='. $val['id_siswa'],
			'label' => 'Edit'
		]
	);
*/
function btn_link($data) 
{
	$attr = [];
	if (key_exists('attr', $data)) {
		foreach($data['attr'] as $name => $value) {
			if ($name == 'class') {
				// $value = ' d-flex  ' . $value;
			}
			$attr[] = $name . '="' . $value . '"';
		}
	}
	
	$label = '';
	if (key_exists('label', $data)) {
		$label = $data['label'];
	}
	
	$icon = '';
	if (key_exists('icon', $data)) {
		$margin = $label ? ' me-1 ' : ''; 
		$icon = '<span class="btn-label-icon"><i class="' . $data['icon'] . $margin . '"></i></span> ';
	}
	
	if ($icon && $label) {
		$content = '<span class="d-flex">' . $icon. $label . '</span>';
	} else {
		$content = $icon. $label;
	}
	$html = '
		<a href="'.$data['url'].'" ' . join(' ', $attr) . '>'. $content . '</a>';
	return $html;
}