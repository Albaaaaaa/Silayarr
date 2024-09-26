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
	<tbody>
		<?php foreach($list as $num_row => $row){ ?>
		<tr id='row-<?php echo $num_row?>'>
			<?php foreach($columns as $column){?>
				<td><?php echo $row->{$column->field_name}?></td>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
			<td class='actions'>
				<div class="btn-group">
					<?php
					if(!empty($row->action_urls)){
						foreach($row->action_urls as $action_unique_id => $action_url){
							$action = $actions[$action_unique_id];
					?>
							<a
									href="<?php echo $action_url; ?>"
									class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"
									role="button"
								<?php if ($action->new_tab) { ?>
									target="_blank"
								<?php } ?>
							>
								<span class="<?php echo $action->css_class; ?>"></span><span class="ui-button-text">&nbsp;<?php echo $action->label?></span>
							</a>
					<?php }
					}
					?>
					<?php if(!$unset_read){?>
						<a href="<?php echo $row->read_url?>" class="edit_button btn btn-secondary btn-xs d-flex align-items-center" role="button">
							<i class="fas fa-eye me-2"></i><?php echo $this->l('list_view'); ?>
						</a>
					<?php }?>

					<?php if(!$unset_clone){?>
						<a href="<?php echo $row->clone_url?>" class="edit_button btn btn-primary btn-xs d-flex align-items-center" role="button">
							<i class="fas fa-copy me-2"></i><?php echo $this->l('list_clone'); ?>
						</a>
					<?php }?>

					<?php if(!$unset_edit){?>
						<a href="<?php echo $row->edit_url?>" class="edit_button btn btn-success btn-xs d-flex align-items-center" role="button">
							<i class="fas fa-pencil-alt me-2"></i><?php echo $this->l('list_edit'); ?>
						</a>
					<?php }?>

					<?php if(!$unset_delete){?>
						<a href="<?=$row->delete_url?>" data-row-id="<?=$num_row?>" class="delete_button btn btn-danger btn-xs d-flex align-items-center delete-row" role="button">
							<i class="fas fa-times me-2"></i><?php echo $this->l('list_delete'); ?>
						</a>
					<?php }?>
				</div>
			</td>
			<?php }?>
		</tr>
		<?php }?>
	</tbody>
	<tfoot>
		<tr>
			<?php foreach($columns as $column){?>
				<th><input type="text" name="<?php echo $column->field_name; ?>" placeholder="<?php echo $this->l('list_search').' '.$column->display_as; ?>" class="form-control search_<?php echo $column->field_name; ?>" /></th>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
				<th>
					<a href="javascript:void(0)" role="button" class="clear-filtering btn btn-outline-secondary btn-xs d-flex align-items-center text-nowrap">
						<i class="fas fa-undo me-2"></i><?php echo $this->l('list_clear_filtering');?>
					</a>
				</th>
			<?php }?>
		</tr>
	</tfoot>
</table>