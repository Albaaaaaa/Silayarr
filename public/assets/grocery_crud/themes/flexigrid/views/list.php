<?php 

	$column_width = (int)(80/count($columns));
	
	if(!empty($list)){
?><div class="bDiv flexigrid-list-container" >
		<table cellspacing="0" cellpadding="0" border="0" id="flex1" class="table table-striped table-hover table-border">
		<thead>
			<tr class='hDiv'>
				<?php foreach($columns as $column){?>
				<th width='<?php echo $column_width?>%'>
					<div class="text-left field-sorting <?php if(isset($order_by[0]) &&  $column->field_name == $order_by[0]){?><?php echo $order_by[1]?><?php }?>" 
						rel='<?php echo $column->field_name?>'>
						<?php echo '<div>' . $column->display_as . '</div>'?>
					</div>
				</th>
				<?php }?>
				<?php if(!$unset_delete || !$unset_edit || !$unset_read || !$unset_clone || !empty($actions)){?>
				<th align="left" abbr="tools" axis="col1" class="" width='20%'>
					<div>
						<?php echo $this->l('list_actions'); ?>
					</div>
				</th>
				<?php }?>
			</tr>
		</thead>		
		<tbody>
<?php foreach($list as $num_row => $row){ ?>        
		<tr  <?php if($num_row % 2 == 1){?>class="erow"<?php }?>>
			<?php foreach($columns as $column){?>
			<td width='<?php echo $column_width?>%' class='<?php if(isset($order_by[0]) &&  $column->field_name == $order_by[0]){?>sorted<?php }?>'>
				<div class='text-left'><?php echo $row->{$column->field_name} != '' ? $row->{$column->field_name} : '&nbsp;' ; ?></div>
			</td>
			<?php }?>
			<?php if(!$unset_delete || !$unset_edit || !$unset_read || !empty($actions)){?>
			<td class="d-flex">
				<div class='btn-group'>				
					<?php if(!$unset_delete){?>
                    	<a href='<?php echo $row->delete_url?>' title='<?php echo $this->l('list_delete')?> <?php echo $subject?>' class="btn btn-danger btn-xs delete-row d-flex align-items-center" >
                    			<i class="fas fa-times me-2"></i><?php echo $this->l('list_delete'); ?>
                    	</a>
                    <?php }?>
                    <?php if(!$unset_edit){?>
						<a href='<?php echo $row->edit_url?>' title='<?php echo $this->l('list_edit')?> <?php echo $subject?>' class="btn btn-success btn-xs d-flex align-items-center">
							<i class="fas fa-pencil-alt me-2"></i><?php echo $this->l('list_edit'); ?>
						</a>
					<?php }?>
                    <?php if(!$unset_clone){?>
                        <a href='<?php echo $row->clone_url?>' title='Clone <?php echo $subject?>' class="btn btn-primary btn-xs d-flex align-items-center">
							<i class="fas fa-copy me-2"></i><?php echo $this->l('list_clone'); ?>
						</a>
                    <?php }?>
					<?php if(!$unset_read){?>
						<a href='<?php echo $row->read_url?>' title='<?php echo $this->l('list_view')?> <?php echo $subject?>' class="btn btn-secondary btn-xs d-flex align-items-center">
							<i class="fas fa-eye me-2"></i><?php echo $this->l('list_view'); ?>
						</a>
					<?php }?>
					<?php 
					if(!empty($row->action_urls)){
						foreach($row->action_urls as $action_unique_id => $action_url){ 
							$action = $actions[$action_unique_id];
					?>
							<a href="<?php echo $action_url; ?>"
                               class="<?php echo $action->css_class; ?> crud-action"
                               title="<?php echo $action->label?>"
                               <?php if ($action->new_tab) { ?>
                                   target="_blank"
                               <?php } ?>
                            >
                                <?php echo $action->label?>
                            </a>
					<?php }
					}
					?>					
				</div>
			</td>
			<?php }?>
		</tr>
<?php } ?>        
		</tbody>
		</table>
		
	</div>
<?php }else{?>
	<div class="alert alert-danger"><?php echo $this->l('list_no_items'); ?></div>
<?php }?>
