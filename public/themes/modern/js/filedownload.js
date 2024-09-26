var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
$(document).ready(function(){
	
	$('.choose-file').click(function(e){
		e.preventDefault();
		jwdfilepicker.init({
			title : 'Pilih File',
			filter_file : '',
			onSelect: function ($elm) {
				var meta = JSON.parse($elm.find('.meta-file').html());
				console.log(meta);
				$('.filename').val(meta.nama_file);
				$('.id-file-picker').val(meta.id_file_picker);
			}
		});
	});
	
	var window_width = $(window).width();
	var window_height = $(window).height();

	if (window_width < 768) {
		var filepicker_width = window_width;
		var filepicker_height = window_height;
	} else {
		var filepicker_width = window_width - 25;
		var filepicker_height = window_height - 25;
	}
	
	$('a.btn-remove').click(function(e) {
		e.stopPropagation();
		$this = $(this);
		$parent = $this.parents('.feature-image').eq(0);
		$parent.find('img').remove();
		$parent.find('.text').show();
		$parent.find('input').val('');
		$this.hide();
	});
});