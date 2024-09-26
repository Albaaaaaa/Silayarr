/**
* Written by: Agus Prawoto Hadi
* Year		: 2022
* Website	: jagowebdev.com
*/

$(document).ready(function() 
{
	$('body').delegate('form', 'submit', function(e) {
		e.preventDefault();
		return false;
	})
	
	if ($('#list-menu').find('li').length == 0) {
		$('#list-menu').append('<div class="alert alert-danger">Data tidak ditemukan</div>');
	}
	$('#list-menu').wdiMenuEditor({
		expandBtnHTML   : '<button data-action="expand" class="fa fa-plus" type="button">Expand</button>',
        collapseBtnHTML : '<button data-action="collapse" class="fa fa-minus" type="button">Collapse</button>',
		editBtnCallback : function($list) 
		{
			$bootbox = showForm('edit', $list.data('id'));			
		},
		beforeRemove: function(item, plugin) {
			$bootbox = bootbox.confirm({
				message: "Yakin akan menghapus kategori?<br/>Semua sub kategori (jika ada) akan ikut terhapus",
				buttons: {
					confirm: {
						label: 'Yes',
						className: 'btn-success submit'
					},
					cancel: {
						label: 'No',
						className: 'btn-danger'
					}
				},
				callback: function(result) {
					if(result) {
						$button = $bootbox.find('button').prop('disabled', true);
						$button_submit = $bootbox.find('button.submit');
						$button_submit.prepend('<i class="fas fa-circle-notch fa-spin me-2 fa-lg"></i>');
						
						list_data = $('#list-menu').wdiMenuEditor('serialize');
						kategori_tree = JSON.stringify(list_data);
						
						$.ajax({
							type: 'POST',
							url: base_url + 'dokumen-kategori/ajaxDeleteKategori',
							data: 'id=' + item.attr('data-id') + '&kategori_tree=' + kategori_tree,
							success: function(msg) 
							{
								$bootbox.modal('hide');
								msg = $.parseJSON(msg);
								if (msg.status == 'ok') {
									plugin.deleteList(item);
									if ($('#list-menu').find('li').length == 0) {
										$('#list-menu').append('<div class="alert alert-danger">Data tidak ditemukan</div>');
									}
								} else {
									Swal.fire({
										title: 'Error !!!',
										text: msg.message,
										icon: 'error',
										showCloseButton: true,
										confirmButtonText: 'OK'
									})
								}
							},
							error: function() {
								$bootbox.modal('hide');
							}
						})
						return false;
					} else {
						return true;
					}
				}
				
			});
		},
		
		// Drag end
		onChange: function(el) 
		{
			list_data = $('#list-menu').wdiMenuEditor('serialize');
			data = JSON.stringify(list_data) + '&id_dokumen_kategori=' + $('.list-group-item-primary').attr('data-id-kategori');
			$.ajax({
				url: base_url + 'dokumen-kategori/ajaxUpdateUrut',
				type: 'post',
				dataType: 'json',
				data: 'data=' + data,
				success: function(result) {
					if (result.status == 'error') {
						show_alert('Error !!!', data.message, 'error');
					}
				}, 
				error: function (xhr) {
					show_alert('Error !!!', 'Ajax error, untuk detailnya bisa di cek di console browser', 'error');
					console.log(xhr);
				}
			});
		}
	});
	
	$('#save-menu').submit(function(e) 
	{
		list_data = $('#list-menu').wdiMenuEditor('serialize');
		data = JSON.stringify(list_data);
		$('#menu-data').empty().text(data);
	})
	
	$(document).on('change', 'select[name="use_icon"]', function(){
		$this = $(this);
		if (this.value == 1) 
		{
			$icon_preview = $this.next().show();
			$this.next().show();
			var calass_name = $icon_preview.find('i').attr('class');
			$this.parent().find('[name="icon_class"]').val(calass_name);
		} else {
			$this.next().hide();
		}
	});
	
	$('#add-menu').click(function(e) 
	{
		e.preventDefault();
		var $add_form = $('#form-edit').clone();
		var id = 'id_' + Math.random();
		$checkbox = $add_form.find('[type="checkbox"]').attr('id', id);
		$checkbox.siblings('label').attr('for', id);
		$bootbox = showForm('add');
		// $('.bootbox-body').overlayScrollbars({scrollbars : {autoHide: 'leave', autoHideDelay: 100} });
	});
	
	function showForm(type='add', id='') 
	{
		var $button = '';
		var $button_submit = '';
			
		$bootbox =  bootbox.dialog({
			title: type == 'edit' ? 'Edit Kategori' : 'Tambah Kategori',
			message: '<div class="text-center"><div class="spinner-border text-secondary" role="status"></div>',
			buttons: {
				cancel: {
					label: 'Cancel'
				},
				success: {
					label: 'Submit',
					className: 'btn-success submit',
					callback: function() 
					{
						$bootbox.find('.alert').remove();
						$button_submit.prepend('<i class="fas fa-circle-notch fa-spin me-2 fa-lg"></i>');
						$button.prop('disabled', true);
						$form_filled = $bootbox.find('form');
						
						$.ajax({
							type: 'POST',
							url: base_url + 'dokumen-kategori/ajaxEditKategori',
							data: $form_filled.serialize(),
							dataType: 'text',
							success: function (data) {
								
								data = $.parseJSON(data);
								
								if (data.status == 'ok') 
								{
									var nama_kategori = $form_filled.find('input[name="nama_kategori"]').val();
									var id = $form_filled.find('input[name="id"]').val();
									var use_icon = $form_filled.find('select[name="use_icon"]').val();
									var icon_class = $form_filled.find('input[name="icon_class"]').val();
									// edit
									if (id) {
										$list_kategori = $('#list-menu').find('[data-id="'+id+'"]');
										$list_kategori.find('.menu-title:eq(0)').text(nama_kategori);
										$handler = $list_kategori.find('.dd-handle:eq(0)');
									} 
									// add
									else {
										$menu_container = $('#list-menu');
										$menu_container.find('.alert').remove();
										if ($menu_container.find('li').length) {
											$new_kategori = $menu_container.find('li:eq(0)').clone();
											$new_kategori.find('ol, ul').remove();
											$new_kategori.find('[data-action="collapse"]').remove();
											$new_kategori.find('[data-action="expand"]').remove();
											$new_kategori.attr('data-id', data.id_menu);
											$new_kategori.find('.menu-title').text(nama_kategori);
											$menu_container = $menu_container.find('ol:eq(0)');
										} else {
											$menu_container.empty();
											$new_kategori = $('<ol class="dd-list"><li class="dd-item" data-id="' + data.id_menu + '">' +
												'<button data-action="remove" type="button" class="fas fa-times pull-right dd-remove"></button>' +
												'<button data-action="edit" type="button" class="text-success far fa-pen pull-right"></button>' +
												'<div class="dd-handle">' +
													'<i class="far fa-circle"></i><span class="menu-title">' + nama_kategori + '</span>' +
												'</div>' +
											'</li></ol>');
										}
										
										$handler = $new_kategori.find('.dd-handle:eq(0)');
									}
									
									$handler.find('i').remove();
									
									if (use_icon == 1) {
										$handler.prepend('<i class="'+icon_class+'"></i>');
									}
									
									if (!id) {
										$menu_container.prepend($new_kategori);
									}
										
									$bootbox.modal('hide');
									Swal.fire({
										title: 'Sukses !!!',
										text: data.message,
										icon: 'success',
										showCloseButton: true,
										confirmButtonText: 'OK'
									})
									
								} else {
									$button_submit.find('i').remove();
									$button.prop('disabled', false);
									if (data.error_query != undefined) {
										Swal.fire({
											title: 'Error !!!',
											html: data.message,
											icon: 'error',
											showCloseButton: true,
											confirmButtonText: 'OK'
										})
									} else {
										$bootbox.find('.modal-body').prepend('<div class="alert alert-dismissible alert-danger" role="alert">' + data.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
									}
								}
							},
							error: function (xhr) {
								console.log(xhr.responseText);
							}
						})
						return false;
					}
				}
			}
		});
		
		$button = $bootbox.find('button').prop('disabled', true);
		$button_submit = $bootbox.find('button.submit');
		$button.prop('disabled', true);
		$bootbox.find('.modal-dialog').css('max-width', '550px');
		$bootbox.find('.modal-body').addClass('p-4');
		const url = base_url + 'dokumen-kategori/ajaxGetKategoriForm?id='+ id;
		$.get(url, function(result) 
		{
			$button.prop('disabled', false);
			$bootbox.find('.modal-body').html(result);
			// $('.select2').select2({theme: 'bootstrap-5', dropdownParent: $(".bootbox")});
			if (type == 'add') {
				id_menu_kategori = $('#list-kategori').find('.list-group-item-primary').attr('data-id-kategori');
				$bootbox.find('select[name="id_menu_kategori"]').val(id_menu_kategori);
			}
		})
		
		return $bootbox;
	}
	
	$(document).on('click', '.icon-preview', function() {
		$bootbox.hide();
		$this = $(this);
		fapicker({
			iconUrl: base_url + 'public/vendors/fontawesome/metadata/icons.yml',
			onSelect: function (elm) {
				$bootbox.show();
				var icon_class = $(elm).data('icon');
				$this.find('i').removeAttr('class').addClass(icon_class);
				$this.parent().find('[name="icon_class"]').val(icon_class);
			},
			onClose: function() {
				$bootbox.show();
			}
		});
	});
});