$(function(){
   /*  $('.datetime-input').datetimepicker({
    	timeFormat: 'HH:mm:ss',
		dateFormat: js_date_format,
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true
    });
    
	$('.datetime-input-clear').button();
	
	$('.datetime-input-clear').click(function(){
		$(this).parent().find('.datetime-input').val("");
		return false;
	});	 */
	console.log(js_date_format);
	$('.datetime-input').flatpickr({
		enableTime: true,
		dateFormat: js_date_format + " H:i:s",
		enableSeconds: true,
		time_24hr: true
	});

});