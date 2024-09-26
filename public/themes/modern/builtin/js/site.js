/**
* Written by: Agus Prawoto Hadi
* Year		: 2023
* Website	: jagowebdev.com
*/
const cookie_jwd_adm_theme = Cookies.get('jwd_adm_theme');
const light_color = '#4c5152';
const dark_color = '#adb5bd';

jQuery(document).ready(function () {
	$('.has-children').mouseenter(function(){
		$(this).children('ul').stop(true, true).fadeIn('fast');
	}).mouseleave(function(){
		$(this).children('ul').stop(true, true).fadeOut('fast');
	});
	
	$('.has-children').click(function(){
		var $this = $(this);
		
		$(this).next().stop(true, true).slideToggle('fast', function(){
			$this.parent().toggleClass('tree-open');
		});
		return false;
	});
	
	$('#mobile-menu-btn').click(function(){
		$('body').toggleClass('mobile-menu-show');
		if ($('body').hasClass('mobile-menu-show')) {
			Cookies.set('jwd_adm_mobile', '1');
		} else {
			Cookies.set('jwd_adm_mobile', '0');
		}
		return false;
	});
	
	$('.sidebar-guide').mouseenter(function(){
		$('body').addClass('show-sidebar');
	});
	$('.sidebar').mouseleave(function(){
		$('body').removeClass('show-sidebar');
	});
	
	$('#mobile-menu-btn-right').click(function(){
		$('header').toggleClass('mobile-right-menu-show');
		return false;
	});
	/* $('.profile-btn').click(function(){
		$(this).next().stop(true, true).fadeToggle();
		return false;
	}); */
	
	bootbox.setDefaults({
		animate: false,
		centerVertical : true
	});
	
	// DELETE
	$('table').on('click', '[data-action="delete-data"]', function(e){
		e.preventDefault();
		var $this =  $(this)
			, $form = $this.parents('form:eq(0)');
		bootbox.confirm({
			message: $this.attr('data-delete-title'),
			callback: function(confirmed) {
				if (confirmed) {
					$form.submit();
				}
			},
			centerVertical: true
		});
	})
	
	$('.sidebar, .overlayscollbar').overlayScrollbars({scrollbars : {autoHide: 'leave', autoHideDelay: 100} });
	
	$('.number-only').keyup(function(){
		this.value = this.value.replace(/\D/i, '');
	});
	
	$.extend( $.fn.dataTable.defaults, {
		"language": {
			"processing": '<span><span class="spinner-border text-secondary" role="status"></span></span>',
		}
	});
	
	window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
		theme_system = Cookies.get('jwd_adm_theme_system');
		if (theme_system == 'true') {
			color = e.matches ? 'dark' : 'light';
			$('html').attr('data-bs-theme', color);
			Cookies.set('jwd_adm_theme', color);
		}
	})
	
	$('body').delegate('.nav-theme-option button', 'click', function() 
	{
		$this = $(this);
		$ul = $this.parents('ul').eq(0);
		$icon = $this.children('.bi:not(.check)').clone().removeClass('me-2');
		$link = $ul.prev().empty();
		$link.append($icon);
		
		$ul.find('button').removeClass('active');
		$this.addClass('active');
		theme_value = $(this).attr('data-theme-value');
		theme_color = '';
		theme_current = Cookies.get('jwd_adm_theme');
		if (theme_value == 'system') 
		{
			if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
				theme_color = 'dark';
			} else {
				theme_color = 'light';
			}
			Cookies.set('jwd_adm_theme_system', 'true');
		} else {
			theme_color = theme_value;
			Cookies.set('jwd_adm_theme_system', 'false');
		}
		$('html').attr('data-bs-theme', theme_color);
		Cookies.set('jwd_adm_theme', theme_color);
		
		// Flatpicker
		$style = $('#style-head-flatpickr');
		if ($style.length) {
			href = $style.attr('href');
			$style.attr('href', href.replace(theme_current, theme_color));
		}
		
		// Tinymce
		$iframe = $('.card-body').find('iframe');
		if ($iframe.length) {
			$iframe_content = $iframe.contents();
			$iframe_content.find('#theme-style, #theme-style-scrollbar').remove();
			if (theme_value == 'dark') {
				$iframe_content.find("head").append('<style id="theme-style">body{color: #adb5bd}</style>'); 
				$iframe_content.find("head").append('<style id="theme-style-scrollbar">::-webkit-scrollbar { width: 15px; height: 3px;}::-webkit-scrollbar-button {  background-color: #141925;height: 0; }::-webkit-scrollbar-track {  background-color: #646464;}::-webkit-scrollbar-track-piece { background-color: #202632;}::-webkit-scrollbar-thumb { height: 35px; background-color: #181c26;border-radius: 0;}::-webkit-scrollbar-corner { background-color: #646464;}}::-webkit-resizer { background-color: #666;}</style>');
			}
		}
		
		// Bootswatch
		bootswatch_theme = theme_color == 'light' ? current_bootswatch_theme : 'default';
		url = base_url + '/public/vendors/bootswatch/' + bootswatch_theme  + '/bootstrap.min.css?r=' + Math.floor(Date.now() /10000);
		$('#style-switch-bootswatch').attr('href', url);
	})
});