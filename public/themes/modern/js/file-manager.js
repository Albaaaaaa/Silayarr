/**
* Written by: Agus Prawoto Hadi
* Year		: 2021-2023
* Website	: jagowebdev.com
*/

jQuery(document).ready(function () {
	
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
	
	$('.btn-upload-file').click(function() 
	{
		$form_dropzone = $('#dropzone-container');
		if ($form_dropzone.is(':visible')) {
			$form_dropzone.stop(true, true).slideUp('fast');
		} else {
			$form_dropzone.stop(true, true).slideDown('fast');
		}
	});
	
	$('body').delegate('.btn-delete-file', 'click', function(e) {
		e.preventDefault();
		id = $(this).attr('data-id');
		$bootbox = bootbox.confirm({
			message: $(this).attr('data-delete-title'),
			callback: function(confirmed) {
				let $button = $bootbox.find('button').prop('disabled', true);
				let $button_submit = $bootbox.find('button.bootbox-accept');
				if (confirmed) {
					$spinner = $('<span class="spinner-border spinner-border-sm me-2"></span>');
					$spinner.prependTo($button_submit);
					$.ajax({
						type: 'POST',
						url: current_url + '/ajaxDeleteData',
						data: 'id=' + id,
						dataType: 'json',
						success: function (data) {
							$button.prop('disabled', false);
							$spinner.remove();
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
								show_alert('Error !!!', data.message, 'error');
							}
						},
						error: function (xhr) {
							$button.prop('disabled', false);
							$spinner.remove();
							show_alert('Error !!!', xhr.responseText, 'error');
							console.log(xhr.responseText);
						}
					})
					return false;
				}
			}
		});
	})
	
	$('.btn-delete-all-data').click(function() {
		$this = $(this);
		$bootbox =  bootbox.dialog({
			title: 'Hapus Semua Data',
			message: '<div class="px-2">' +
						'<p>Tindakan ini akan menghapus semua data pada database tabel <strong>file_picker</strong> dan semua file pada folder <strong>public/files/uploads/</strong></p>' +
						'<p><span class="text-danger">Note</span>: File dokumen yang berkaitan juga akan ikut terhapus</p>' +
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
							url: base_url + 'list-file/ajaxDeleteAllData',
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
	
	// DROPZONE
	var $preview = $("#dropzone-preview-template").removeAttr('id'),
				$warning = $("#jwd-dz-error");
				
				$clone = $("#dropzone-preview-template").clone().show();
				$clone.attr('id', "");
				
	var target = '.dropzone-area';
	var previewTemplate = $preview.parent().html();
		$preview.remove();
			
			Dropzone.autoDiscover = false;
	var FileDropzone = new Dropzone(target, 
	{
		url: $(target).attr("action"),
		// maxFiles: 1,
		maxFilesize: 20,
		// acceptedFiles: "image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv,.tsv,.ppt,.pptx,.pages,.odt,.rtf",
		previewTemplate: previewTemplate,
		previewsContainer: "#file-previews",
		clickable: true,
		dictFallbackMessage: "Browser Anda tidak support drag'n'drop file uploads.",
		dictFileTooBig: "Ukuran file Anda terlalu besar: ({{filesize}}MiB). Ukuran maksimal file yang diperkenankan: {{maxFilesize}}MiB.",
		// dictInvalidFileType: "You can't upload files of this type.", // Default: You can't upload files of this type.
		dictResponseError: "Server error code: {{statusCode}}.",
		// dictMaxFilesExceeded: "Maksimal 3 file sekali upload.",
		dictFileSizeUnits: {tb: "TB", gb: "GB", mb: "MB", kb: "KB", b: "b"},
	});
				
	/* function fileType (fileName) {
		var fileType = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
		return fileType[0];
	} */
	
	$('.dropzone-area').on('dragover', function() {
		$(this).addClass("dropzone-hover");
	})
	
	list_upload = {};
	FileDropzone.on("addedfile", function(file) 
	{
		list_upload[file.name] = file.name;
		$(window).off('scroll');
		
		$(target).removeClass("dropzone-hover");
		$('.preview-container').show();
		$warning.empty();
		
		filename = file.name.toLowerCase();
		ext = filename.split('.').pop();
		mime = file.type;
		if(mime != 'image/png' && mime != 'image/jpg' && mime != 'image/jpeg' && mime != 'image/bmp') {
		  $(file.previewElement).find('img').attr('src', filepicker_server_url + 'ajaxFileIcon?mime=' + mime + '&ext=' + ext);
		}
	});

	FileDropzone.on("totaluploadprogress", function (progress) {
		var $prog = $(".progress .determinate");
		if ($prog === undefined || $prog === null) return;

		$prog.css(progress + "%");
		$(".progress-text").html(' - ' + progress + '%');
		
	});

	FileDropzone.on('dragenter', function () {
		// $(target).addClass("dropzone-hover");
	});

	FileDropzone.on('dragleave', function () {
		$(target).removeClass("dropzone-hover");			
	});

	FileDropzone.on('drop', function () {
		$(target).removeClass("-dropzone-hover");	
	});
	
	FileDropzone.on('error', function (file, response) {
		console.log(file.previewElement);
		$('#previews').children('.dz-success, .dz-complete').remove();
		$(file.previewElement).find('.details').remove();
		$(file.previewElement).find('.dz-error-message').css('width', '100%');
		$(file.previewElement).find('.dz-error-message')
			.html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
					  '<strong>Error</strong> ' + response.message + 
					  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
					'</div>');
		$('.dropzone-area').delegate('.btn-close', 'click', function() {
			$(this).parents('.dz-processing').eq(0).remove();
		});
		
	});
	
	FileDropzone.on("success", function(file, response) 
	{
		let parsedResponse = JSON.parse(response);
		
		// Progress bar
		$(file.previewElement).find('.progress-bar').addClass('progress-bar-success');
			
		/* $(file.previewElement).fadeOut('fast', function()
		{
			$(this).remove();
		}); */
		
		if ( parsedResponse.status == 'error' ) {
			$warning.html('<div class="alert alert-danger">' + parsedResponse.message + '</div>');
		} else {
			dataTables.draw();
		}
	});
	
	$('body').delegate('.btn-edit-file', 'click', function(e) {
		id = $(this).attr('data-id');
		showForm(id);
		
	})
	
	function showForm(id) {
		$bootbox =  bootbox.dialog({
			title: 'Edit Data',
			message: '<div class="text-center text-secondary"><div class="spinner-border"></div></div>',
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
						$spinner = $('<div class="spinner-border spinner-border-sm me-2"></div>');
						$button_submit.prepend($spinner);
						$button.prop('disabled', true);
						
						$form = $bootbox.find('form');
						$.ajax({
							type: 'POST',
							url: current_url + '/ajaxSaveData',
							data: $form.serialize(),
							dataType: 'json',
							success: function (data) {
								
								$spinner.remove();
								$button.prop('disabled', false);
								if (data.status == 'ok') {
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
										html: '<div class="toast-content"><i class="far fa-check-circle me-2"></i> Data berhasil disimpan</div>'
									})
									
									dataTables.draw();
									
									$('.btn-delete-all-kelas').prop('disabled', false);
								} else {
									show_alert('Error !!!', data.message, 'error');
								}
							},
							error: function (xhr) {
								$spinner.remove();
								$button.prop('disabled', false);
								show_alert('Error !!!', xhr.responseText, 'error');
								console.log(xhr.responseText);
							}
						})
						return false;
					}
				}
			}
		});
	
		var $button = $bootbox.find('button').prop('disabled', true);
		var $button_submit = $bootbox.find('button.submit');
		
		$.get(base_url + 'file-manager/ajaxGetFormData?id=' + id, function(html){
			$button.prop('disabled', false);
			$bootbox.find('.modal-body').empty().append(html);
		});
	}
});