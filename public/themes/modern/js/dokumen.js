/**
* Written by: Agus Prawoto Hadi
* Year		: 2021-2023
* Website	: jagowebdev.com
*/

jQuery(document).ready(function () {
	
	if ($('#table-result').length) {
		const column = $.parseJSON($('#dataTables-column').html());
		const url = $('#dataTables-url').text();
		
		const settings = {
			"processing": true,
			"serverSide": true,
			"scrollX": true,
			"ajax": {
				"url": url,
				"type": "POST"
			},
			"columns": column
		}
		
		let $add_setting = $('#dataTables-setting');
		if ($add_setting.length > 0) {
			add_setting = $.parseJSON($('#dataTables-setting').html());
			for (k in add_setting) {
				settings[k] = add_setting[k];
			}
		}
		
		dataTables =  $('#table-result').DataTable( settings );
		
		$col = $('#table-dokumen-result_wrapper').children().eq(0).children().eq(0);
		$kategori = $('#id-dokumen-kategori').show();
		$kategori.appendTo($col);
		$('#id-dokumen-kategori').select2({'theme':'bootstrap-5'});
		
		$('#id-dokumen-kategori').change(function() {
			new_url =  base_url + 'dokumen/getDataDT?id_dokumen_kategori=' + $(this).val();
			dataTables.ajax.url( new_url ).load();
		});
	}
	
	$('.add-file').click(function(e){
		e.preventDefault();
		jwdfilepicker.init({
			title : 'Pilih File',
			filter_file : '',
			onSelect: function ($elm) {
				var meta = JSON.parse($elm.find('.meta-file').html());
				html = '<div class="input-group mb-2">' +
					'<input type="text" name="nama_file[]" class="form-control" placeholder="" aria-label="Choose File" aria-describedby="" value="' + meta.nama_file + '" readonly> ' +
					' <input type="hidden" name="id_file_picker[]" value="' + meta.id_file_picker + '"/> ' +
					' <button class="btn btn-danger del-file" type="button"><i class="fas fa-times"></i></button>' +
				'</div>';
				$('#list-file-container').append(html);
			}
		});
	});
	
	$('#table-result').delegate('.btn-delete', 'click', function(e) {
		e.preventDefault();
		id = $(this).attr('data-id');
		$bootbox = bootbox.confirm({
			message: $(this).attr('data-delete-title'),
			callback: function(confirmed) {
				if (confirmed) {
					$button = $bootbox.find('button');
					$button.attr('disabled', 'disabled');
					$spinner = $('<div class="spinner-border spinner-border-sm me-2"></div>');
					$spinner.prependTo($bootbox.find('.bootbox-accept'));
					$.ajax({
						type: 'POST',
						url: current_url + '/ajaxDeleteData',
						data: 'id=' + id,
						dataType: 'json',
						success: function (data) {
							$bootbox.modal('hide');
							if (data.status == 'ok') {
								const Toast = Swal.mixin({
									toast: true,
									position: 'top-end',
									showConfirmButton: false,
									timer: 2500,
									timerProgressBar: true,
									iconColor: 'white',
									customClass: {
										popup: 'bg-success text-light toast p-2'
									},
									didOpen: (toast) => {
										toast.addEventListener('mouseenter', Swal.stopTimer)
										toast.addEventListener('mouseleave', Swal.resumeTimer)
									}
								})
								Toast.fire({
									html: '<div class="toast-content"><i class="far fa-check-circle me-2"></i> Data berhasil dihapus</div>'
								})
								dataTables.draw();
							} else {
								$spinner.remove();
							$button.removeAttr('disabled');
								show_alert('Error !!!', data.message, 'error');
							}
						},
						error: function (xhr) {
							$spinner.remove();
							$button.removeAttr('disabled');
							show_alert('Error !!!', xhr.responseText, 'error');
							console.log(xhr.responseText);
						}
					})
					return false;
				}
			}
		});
	})
	
	$('body').delegate('.del-file', 'click', function() {
		$(this).parents('.input-group').remove();
	})
	
	$(".list-kategori").select2tree({theme : 'bootstrap-5'});
	$('.flatpickr').flatpickr({
		dateFormat: "d-m-Y",
	});
	
	$('.btn-delete-all-data').click(function() {
		$this = $(this);
		$bootbox =  bootbox.dialog({
			title: 'Hapus Semua Data',
			message: '<div class="px-2">' +
						'<p>Tindakan ini akan menghapus semua data pada database tabel:<ul class="list-circle"><li>dokumen</li><li>dokumen_download_log</li><li>dokumen_file_picker</li>' +
				'</div>'+
			'</form>',
			buttons: {
				cancel: {
					label: 'Cancel'
				},
				success: {
					label: 'Delete',
					className: 'btn-danger submit',
					callback: function() 
					{
						var $button = $bootbox.find('button').prop('disabled', true);
						var $button_submit = $bootbox.find('button.submit');
						
						$bootbox.find('.alert').remove();
						$spinner = $('<div class="spinner-border spinner-border-sm me-2"></div>');
						$button_submit.prepend($spinner);
						$button.prop('disabled', true);
						
						$.ajax({
							type: 'GET',
							url: base_url + 'dokumen/ajaxDeleteAllData',
							dataType: 'text',
							success: function (data) {
								data = $.parseJSON(data);
								console.log(data);
								$spinner.remove();
								$button.prop('disabled', false);
								
								if (data.status == 'ok') 
								{
									$bootbox.modal('hide');
									const Toast = Swal.mixin({
										toast: true,
										position: 'top-end',
										showConfirmButton: false,
										timer: 2500,
										timerProgressBar: true,
										iconColor: 'white',
										customClass: {
											popup: 'bg-success text-light toast p-2'
										},
										didOpen: (toast) => {
											toast.addEventListener('mouseenter', Swal.stopTimer)
											toast.addEventListener('mouseleave', Swal.resumeTimer)
										}
									})
									Toast.fire({
										html: '<div class="toast-content"><i class="far fa-check-circle me-2"></i> Data berhasil dihapus</div>'
									})
									
									dataTables.draw();
									$this.prop('disabled', true);
								} else {
									Swal.fire({
										title: 'Error !!!',
										html: data.message,
										icon: 'error',
										showCloseButton: true,
										confirmButtonText: 'OK'
									})
								}
							},
							error: function (xhr) {
								console.log(xhr.responseText);
								$spinner.remove();
								$button.prop('disabled', false);
								Swal.fire({
									title: 'Error !!!',
									html: xhr.responseText,
									icon: 'error',
									showCloseButton: true,
									confirmButtonText: 'OK'
								})
							}
						})
						return false;
					}
				}
			}
		});
	});
	
});