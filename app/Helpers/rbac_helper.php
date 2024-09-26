<?php

function where_own($column = null) 
{
	global $list_action, $check_role_action;
	
	if (!$column)
		$column = $check_role_action['field'];
		
	if ($list_action['read_data'] == 'own') {
		return ' WHERE ' . $column . ' = ' . $_SESSION['user']['id_user'];
	}
	
	return ' WHERE 1 = 1 ';
}