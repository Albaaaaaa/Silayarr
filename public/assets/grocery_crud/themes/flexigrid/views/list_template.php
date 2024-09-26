<?php
	$this->set_css($this->default_theme_path.'/flexigrid/css/flexigrid.css');
	$this->set_js_lib($this->default_javascript_path.'/' . \App\Libraries\GroceryCrud::JQUERY);

    $this->set_js_lib($this->default_javascript_path.'/common/list.js');

	$this->set_js($this->default_theme_path.'/flexigrid/js/cookies.js');
	$this->set_js($this->default_theme_path.'/flexigrid/js/flexigrid.js');

    $this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.form.min.js');

	$this->set_js($this->default_javascript_path.'/jquery_plugins/jquery.numeric.min.js');
	$this->set_js($this->default_theme_path.'/flexigrid/js/jquery.printElement.min.js');
?>
<script type='text/javascript'>
	var grocery_base_url = '<?php echo base_url();?>';

	var subject = '<?php echo addslashes($subject); ?>';
	var ajax_list_info_url = '<?php echo $ajax_list_info_url; ?>';
	var unique_hash = '<?php echo $unique_hash; ?>';
	var export_url = '<?php echo $export_url; ?>';

	var message_alert_delete = "<?php echo $this->l('alert_delete'); ?>";
	let lang_btn_ok = "<?php echo $this->l('btn_ok'); ?>";
	let lang_btn_cancel = "<?php echo $this->l('btn_cancel'); ?>";
	let lang_delete_success_message = "<?php echo $this->l('delete_success_message'); ?>";
	let lang_delete_error_message = "<?php echo $this->l('delete_error_message'); ?>";

</script>
<div id='list-report-error' class='report-div error'></div>
<div id='list-report-success' class='report-list' <?=$success_message !== null ? 'style="display:block"' : 'style="display:none"'?>><?php
if($success_message !== null){?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?php echo $success_message; ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php }
?></div>

<?php if(!$unset_add){?>
<div class="tDiv2 mb-3 d-flex justify-content-between">
	<a href='<?php echo $add_url?>' title='<?php echo $this->l('list_add'); ?> <?php echo $subject?>' class='add-anchor add_button btn btn-success'>
		<i class="fas fa-plus me-2"></i><?php echo $this->l('list_add'); ?> <?php echo $subject?>
	</a>
	<?php if(!$unset_add || !$unset_export || !$unset_print){?>
		<div class="tDiv">
			
			<div class="tDiv3">
				<?php if(!$unset_export) { ?>
				<a class="export-anchor btn btn-outline-secondary" href="<?php echo $export_url; ?>" download>
					<i class="fas fa-file-excel me-2"></i><?php echo $this->l('list_export');?>
				</a>
				<?php } ?>
				<?php if(!$unset_print) { ?>
				<a class="print-anchor btn btn-outline-secondary" data-url="<?php echo $print_url; ?>">
					<i class="fas fa-print me-2"></i><?php echo $this->l('list_print');?>
				</a>
				<?php }?>
			</div>
			<div class='clear'></div>
		</div>
	<?php }?>
</div>
<?php }?>
		
<div class="flexigrid" style='width: 100%;' data-unique-hash="<?php echo $unique_hash; ?>">
	<div id="hidden-operations" class="hidden-operations"></div>
	
	<div id='main-table-box' class="main-table-box">
		
		<?php echo form_open( $ajax_list_url, 'method="post" id="filtering_form" class="filtering_form" autocomplete = "off" data-ajax-list-info-url="'.$ajax_list_info_url.'"'); ?>
		<div class="d-flex justify-content-between mb-3">
			<div class="pGroup d-flex">
					<span class="pcontrol d-flex align-items-center">
						<?php list($show_lang_string, $entries_lang_string) = explode('{paging}', $this->l('list_show_entries')); ?>
						<?php echo $show_lang_string; ?>
						<select name="per_page" id='per_page' class="per_page form-select ms-2 me-2">
							<?php foreach($paging_options as $option){?>
								<option value="<?php echo $option; ?>" <?php if($option == $default_per_page){?>selected="selected"<?php }?>><?php echo $option; ?>&nbsp;&nbsp;</option>
							<?php }?>
						</select>
						<?php echo $entries_lang_string; ?>
						<input type='hidden' name='order_by[0]' id='hidden-sorting' class='hidden-sorting' value='<?php if(!empty($order_by[0])){?><?php echo $order_by[0]?><?php }?>' />
						<input type='hidden' name='order_by[1]' id='hidden-ordering' class='hidden-ordering'  value='<?php if(!empty($order_by[1])){?><?php echo $order_by[1]?><?php }?>'/>
					</span>
			</div>
			
			<div class="sDiv quickSearchBox d-flex justify-content-between" id='quickSearchBox'>
				<div class="sDiv2 d-flex">
					<input type="text" class="qsbsearch_fieldox search_text form-control me-2" placeholder="Search..." name="search_text" id='search_text'>
					<select name="search_field" class="form-select me-2" id="search_field">
						<?php foreach($columns as $column){?>
						<option value="<?php echo $column->field_name?>"><?php echo $column->display_as?>&nbsp;&nbsp;</option>
						<?php }?>
					</select>
					<button type="button" value="" class="crud_search btn btn-secondary" id='crud_search'><i class="fas fa-search"></i></button>
					<button type="button" id='search_clear' class="search_clear btn btn-secondary ms-1"><i class="far fa-trash-alt"></i></button>
					<?php
					// <?php echo $this->l('list_clear_filtering');
					?>
				</div>
			</div>
		</div>
		<div id='ajax_list' class="ajax_list">
			<?php echo $list_view?>
		</div>
		<div class="pDiv mt-3">
			<div class="pDiv2 d-flex justify-content-between">				
				<div class="pGroup">
					<span class="pPageStat">
						<?php $paging_starts_from = "<span id='page-starts-from' class='page-starts-from'>1</span>"; ?>
						<?php $paging_ends_to = "<span id='page-ends-to' class='page-ends-to'>". ($total_results < $default_per_page ? $total_results : $default_per_page) ."</span>"; ?>
						<?php $paging_total_results = "<span id='total_items' class='total_items'>$total_results</span>"?>
						<?php echo str_replace( array('{start}','{end}','{results}'),
												array($paging_starts_from, $paging_ends_to, $paging_total_results),
												$this->l('list_displaying')
											   ); ?>
					</span>
				</div>
				<div class="flexgrid-pagination d-flex">
					<div class="pGroup d-flex me-2">
						<div class="pPrev prev-button me-2">
							<button class="btn btn-outline-secondary"><i class="fas fa-step-backward"></i></button>
						</div>
						<div class="pFirst first-button">
							<button class="btn btn-outline-secondary"><i class="fas fa-caret-left"></i></button>
						</div>
					</div>
					<div class="pGroup d-flex me-2">
						<span class="pcontrol d-flex align-items-center">
							<?php echo $this->l('list_page'); ?> <input name='page' type="text" value="1" style="width:75px" id='crud_page' class="crud_page form-control ms-2 me-2">
							<?php echo $this->l('list_paging_of'); ?>
							<span id='last-page-number' class="last-page-number ms-1"><?php echo ceil($total_results / $default_per_page)?></span>
						</span>
					</div>
					<div class="pGroup d-flex">
						<div class="pNext next-button me-2">
							<button class="btn btn-outline-secondary"><i class="fas fa-caret-right"></i></button>
						</div>
						<div class="pLast last-button">
							<button class="btn btn-outline-secondary"><i class="fas fa-step-forward"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
