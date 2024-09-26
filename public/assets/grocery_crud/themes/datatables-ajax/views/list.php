<table cellpadding="0" cellspacing="0" border="0" class="display table table-striped table-border table-hover groceryCrudTable" id="<?php echo uniqid(); ?>">
	<thead>
		<tr>
			<?php foreach($columns as $column){?>
				<th><?php echo $column->display_as; ?></th>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
				<th class='actions'><?php echo $this->l('list_actions'); ?></th>
			<?php }?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<?php foreach($columns as $column){?>
				<th><?php echo $column->display_as; ?></th>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
				<th class='actions'><?php echo $this->l('list_actions'); ?></th>
			<?php }?>
		</tr>
	</tfoot>
</table>
<?php
/* echo '<pre>';
print_r($columns);
die; */
$act = ['unset_read', 'unset_clone', 'unset_edit', 'unset_delete'];
$query_string = '';
foreach ($act as $val) {
	$status = ${$val} ? 1 : 0;
	$query_string .= $val . '=' . $status . '&';
}

$actions = ['read_url', 'edit_url','clone_url', 'delete_url'];
foreach ($actions as $action) {
	$query_string .= $action . '=' . ${$action} . '&';
}

$query_string .= 'lang_view=' . $this->l('list_view');
$query_string .= '&lang_clone=' . $this->l('list_clone');
$query_string .= '&lang_edit=' . $this->l('list_edit');
$query_string .= '&lang_delete=' . $this->l('list_delete');
$query_string .= '&database_table=' . $database_table;
$query_string .= '&primary_key=' . $primary_key;
?>
<span style="display:none" id="grocery-data-tables-ajax-url"><?=$data_tables_ajax_url . '?' . $query_string?></span>
<span style="display:none" id="grocery-data-tables-ajax-setting"><?=json_encode($data_tables_ajax_setting)?></span>
<?php
$list_column = [];

foreach ($columns as $obj) {
	$list_column[] = ['data' => $obj->field_name_original];
}

if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){
	$list_column[] = ['data' => 'ignore_actions'];
}?>
<span style="display:none" id="grocery-data-tables-ajax-column"><?=json_encode($list_column)?></span>