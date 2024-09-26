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
	
	$('body').delegate('.btn-delete-log', 'click', function(e) {
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
	
});