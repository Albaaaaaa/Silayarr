function show_alert(title, content, icon, timer) {
	
	let message = content;
	if (typeof (content) == 'object') 
	{
		message = '<ul>';
		for (k in content) {
			message += '<li>' + content[k] + '</li>';
		}
		message += '</ul>';
	}
	
	const setting = { 
		title: title,
		html: message,
		icon: icon,
		showConfirmButton : true
	}
	
	if (timer) {
		setting.timer = timer
		setting.showConfirmButton = false
	}
	
	Swal.fire( setting )
}

function generate_alert(type, message) {
	return '<div class="alert alert-dismissible alert-'+type+'" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
	
}

function format_ribuan(bilangan) 
{
	bilangan = parseFloat( bilangan.toString().replace(/\D/g, '') );
	
	if (bilangan == 0 || bilangan == '') {
		return 0;
	}
	
	if (!bilangan)
		return 0;
	
	let minus = bilangan.toString().substr(0,1) == '-' ? '-' : '';
	
	
	var	reverse = bilangan.toString().split('').reverse().join(''),
		ribuan 	= reverse.match(/\d{1,3}/g);
		ribuan	= ribuan.join('.').split('').reverse().join('');
	
	return minus + ribuan;
}

function setInt (number) {
	number = parseFloat( number.replace(/\D/g, '') );
	if (!number)
		return 0;
	return number;
}