jQuery(document).ready(function () {
	$('select[name="enable"]').change(function(){
		if (this.value == 'N') {
			$('.detail-container').hide();
		} else {
			$('.detail-container').show();
		}
	});
	
	$('#option-default-page').change(function(){
		$this = $(this);
		$parent = $this.parent();
		$parent.find('.default-page').hide();
		if ($this.val() == 'url') {
			$parent.find('.default-page-url').show();
		} else if ($this.val() == 'id_module') {
			$parent.find('.default-page-id-module').show();
		} else {
			$parent.find('.default-page-id-role').show();
		}
	})
});
