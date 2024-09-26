<a class="btn btn-outline-secondary btn-xs" href="<?=$list_url?>"><i class="fas fa-arrow-circle-left me-2"></i><?=$this->l('form_cancel')?></a>
<hr/>
<h5 class="mb-3"><?php echo $this->l('list_record'); ?> <?php echo $subject?></h5>
<div id='main-table-box'>
	<?php echo form_open( $read_url, 'method="post" id="crudForm"  enctype="multipart/form-data"'); ?>
	<div class='form-div'>
		<?php
		$counter = 0;
			foreach($fields as $field)
			{
		?>
			
			
			<div class="row mb-3" id="<?php echo $field->field_name; ?>_field_box">
				<label class="col-sm-2" id="<?php echo $field->field_name; ?>_display_as_box">
					<?php echo $input_fields[$field->field_name]->display_as?><?php echo ($input_fields[$field->field_name]->required)? "<span class='required'>*</span> " : ""?>
				</label>
				<div class="col-sm-8" id="<?php echo $field->field_name; ?>_input_box">
					<?php echo $input_fields[$field->field_name]->input?>
				</div>
			</div>
		<?php }?>
		<?php if(!empty($hidden_fields)){?>
		<!-- Start of hidden inputs -->
			<?php
				foreach($hidden_fields as $hidden_field){
					echo $hidden_field->input;
				}
			?>
		<!-- End of hidden inputs -->
		<?php }?>
		<?php if ($is_ajax) { ?><input type="hidden" name="is_ajax" value="true" /><?php }?>
		<div id='report-error' class='report-div error'></div>
		<div id='report-success' class='report-div success'></div>
	</div>
	<?php echo form_close(); ?>
</div>
</div>
<script>
	var validation_url = '<?php echo $validation_url?>';
	var list_url = '<?php echo $list_url?>';

	var message_alert_edit_form = "<?php echo $this->l('alert_edit_form')?>";
	var message_update_error = "<?php echo $this->l('update_error')?>";
</script>
