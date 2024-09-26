<?php

	$this->set_css($this->default_theme_path.'/flexigrid/css/flexigrid.css');
	$this->set_js_lib($this->default_theme_path.'/flexigrid/js/jquery.form.js');
    $this->set_js_lib($this->default_javascript_path.'/jquery_plugins/jquery.form.min.js');
	$this->set_js_config($this->default_theme_path.'/flexigrid/js/flexigrid-add.js');
?>
<?php echo $this->l('form_add'); ?> <?php echo $subject?>
<hr/>
<a class="btn btn-outline-secondary btn-xs" href="<?=$list_url?>"><i class="fas fa-arrow-circle-left me-2"></i><?=$this->l('form_cancel')?></a>
<hr/>
<div class="flexigrid crud-form" style='width: 100%;' data-unique-hash="<?php echo $unique_hash; ?>">
	<div id='main-table-box'>
	<?php echo form_open( $insert_url, 'method="post" id="crudForm"  enctype="multipart/form-data"'); ?>
		<div class='form-div'>
			<?php
			$counter = 0;
				foreach($fields as $field)
				{
					$even_odd = $counter % 2 == 0 ? 'odd' : 'even';
					$counter++;
			?>
			<div class='row mb-3' id="<?php echo $field->field_name; ?>_field_box">
				<label class='col-sm-2' id="<?php echo $field->field_name; ?>_display_as_box">
					<?php echo $input_fields[$field->field_name]->display_as; ?><?php echo ($input_fields[$field->field_name]->required)? "<span class='required'>*</span> " : ""; ?>
				</label>
				<div class='col-sm-8' id="<?php echo $field->field_name; ?>_input_box">
					<?php echo $input_fields[$field->field_name]->input?>
				</div>
			</div>
			<?php }?>
			<!-- Start of hidden inputs -->
				<?php
					foreach($hidden_fields as $hidden_field){
						echo $hidden_field->input;
					}
				?>
			<!-- End of hidden inputs -->
			<?php if ($is_ajax) { ?><input type="hidden" name="is_ajax" value="true" /><?php }?>
			<div id='report-error' class='alert alert-error' style="display:none"></div>
			<div id='report-success' class='alert alert-success' style="display:none"></div>
		</div>
		<div>
			<button id="form-button-save" type='submit' class="btn btn-primary"><?php echo $this->l('form_save'); ?></button>
<?php 	if(!$this->unset_back_to_list) { ?>
			<button type='button' id="save-and-go-back-button"  class="btn btn-outline-secondary"><?php echo $this->l('form_save_and_go_back'); ?></button>
<?php 	} ?>
			<div class='mt-2' style="display:none" id='FormLoading'>
				<span class="spinner-border spinner-border-sm text-secondary me-1"></span>
				<?php echo $this->l('form_insert_loading'); ?>
			</div>
		</div>
	<?php echo form_close(); ?>
</div>
</div>
<script>
	var validation_url = '<?php echo $validation_url?>';
	var list_url = '<?php echo $list_url?>';

	var message_alert_add_form = "<?php echo $this->l('alert_add_form')?>";
	var message_insert_error = "<?php echo $this->l('insert_error')?>";
</script>