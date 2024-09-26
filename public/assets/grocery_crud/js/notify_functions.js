function success_message(success_message)
{
	noty({
		  text: success_message,
		  type: 'success',
		  dismissQueue: true,
		  layout: 'top',
		  callback: {
		    afterShow: function() {

		        setTimeout(function(){
		        	$.noty.closeAll();
		        },7000);
		    }
		  }
	});
}

function error_message(error_message)
{
	noty({
		  text: error_message,
		  type: 'error',
		  layout: 'top',
		  dismissQueue: true
	});
}

function form_success_message(success_message)
{
	let alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">' + success_message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
		
	$('#report-success').html(alert_message);
	$('#report-success').show();
	// $('#report-success').slideUp('fast');

	if ($('#report-success').closest('.ui-dialog').length !== 0) {
		$('.go-to-edit-form').click(function(){

			fnOpenEditForm($(this));

			return false;
		});
	}
}

function form_error_message(error_message)
{
	let alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' + error_message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	
	$('#report-error').slideUp('fast');
	$('#report-error').html(alert_message);
	$('#report-error').slideDown('normal');
	$('#report-success').slideUp('fast').html('');
}