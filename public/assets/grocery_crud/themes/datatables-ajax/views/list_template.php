<?php
	$this->set_css($this->default_assets_path.'/vendors/datatables/dist/css/dataTables.bootstrap5.min.css');
	
	$this->set_js($this->default_assets_path.'/vendors/datatables/dist/js/jquery.dataTables.min.js');
	$this->set_js($this->default_assets_path.'/vendors/datatables/dist/js/dataTables.bootstrap5.min.js');
	$this->set_js_lib($this->default_javascript_path.'/common/list.js');
	$this->set_js($this->default_theme_path.'/datatables-ajax/js/datatables-extras.js');
	$this->set_js($this->default_theme_path.'/datatables-ajax/js/datatables-ajax.js');
?>
<script type='text/javascript'>
	var grocery_base_url = '<?php echo base_url();?>';
	var subject = '<?php echo addslashes($subject); ?>';

	var unique_hash = '<?php echo $unique_hash; ?>';

	var displaying_paging_string = "<?php echo str_replace( array('{start}','{end}','{results}'),
		array('_START_', '_END_', '_TOTAL_'),
		$this->l('list_displaying')
	   ); ?>";
	var filtered_from_string 	= "<?php echo str_replace('{total_results}','_MAX_',$this->l('list_filtered_from') ); ?>";
	var show_entries_string 	= "<?php echo str_replace('{paging}','_MENU_',$this->l('list_show_entries') ); ?>";
	var search_string 			= "<?php echo $this->l('list_search'); ?>";
	var list_no_items 			= "<?php echo $this->l('list_no_items'); ?>";
	var list_zero_entries 			= "<?php echo $this->l('list_zero_entries'); ?>";

	var list_loading 			= "<?php echo $this->l('list_loading'); ?>";

	var paging_first 	= "<?php echo $this->l('list_paging_first'); ?>";
	var paging_previous = "<?php echo $this->l('list_paging_previous'); ?>";
	var paging_next 	= "<?php echo $this->l('list_paging_next'); ?>";
	var paging_last 	= "<?php echo $this->l('list_paging_last'); ?>";

	var message_alert_delete = "<?php echo $this->l('alert_delete'); ?>";
	
	var default_per_page = <?php echo $default_per_page;?>;

	var unset_export = <?php echo ($unset_export ? 'true' : 'false'); ?>;
	var unset_print = <?php echo ($unset_print ? 'true' : 'false'); ?>;

	var export_text = '<?php echo $this->l('list_export');?>';
	var print_text = '<?php echo $this->l('list_print');?>';
	var export_url = '<?php echo $export_url; ?>';
	
	let lang_btn_ok = "<?php echo $this->l('btn_ok'); ?>";
	let lang_btn_cancel = "<?php echo $this->l('btn_cancel'); ?>";
	let lang_delete_success_message = "<?php echo $this->l('delete_success_message'); ?>";
	let lang_delete_error_message = "<?php echo $this->l('delete_error_message'); ?>";
	
	<?php
	//A work around for method order_by that doesn't work correctly on datatables theme
	//@todo remove PHP logic from the view to the basic library
	$ordering = 0;
	$sorting = 'asc';
	if(!empty($order_by))
	{
		foreach($columns as $num => $column) {
			if($column->field_name == $order_by[0]) {
				$ordering = $num;
				$sorting = isset($order_by[1]) && $order_by[1] == 'asc' || $order_by[1] == 'desc' ? $order_by[1] : $sorting ;
			}
		}
	}
	?>

	var datatables_aaSorting = [[ <?php echo $ordering; ?>, "<?php echo $sorting;?>" ]];

</script>
<?php
	if(!empty($actions)){
?>
	<style type="text/css">
		<?php foreach($actions as $action_unique_id => $action){?>
			<?php if(!empty($action->image_url)){ ?>
				.<?php echo $action_unique_id; ?>{
					background: url('<?php echo $action->image_url; ?>') !important;
				}
			<?php }?>
		<?php }?>
	</style>
<?php
	}
?>
<?php if($unset_export && $unset_print){?>
<style type="text/css">
	.datatables-add-button
	{
		position: static !important;
	}
</style>
<?php }?>
<div class="grocerycrud-container">
	<div id='list-report-error' class='report-div error report-list'></div>
	<div id='list-report-success' class='report-div success report-list' <?php if($success_message !== null){?>style="display:block"<?php }?>><?php
	 if($success_message !== null){?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<?php echo $success_message; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php }
	?></div>
	<div class="dataTablesContainer">
		<?php if(!$unset_add){?>
			<div class="datatables-add-button">
			<a role="button" class="add_button btn btn-success" href="<?php echo $add_url?>">
				<i class="fas fa-plus me-2"></i><?php echo $this->l('list_add'); ?> <?php echo $subject?>
			</a>
			</div>
		<?php }?>

		<div style='height:10px;'></div>
		<?php echo $list_view?>
	</div>
</div>