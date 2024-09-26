$(document).ready(function(){
	jwdfilepicker.init({
		title : 'Feature Image',
		filter_file : 'image',
		url_file_process : filepicker_server_url + 'tinymce',
		show_title : false,
		show_close_btn : false,
		margin_style : 'margin-narrow',
		use_backdrop : false,
		onSelect: function ($elm) {
			meta_file = JSON.parse($elm.find('.meta-file').html());
			window.parent.postMessage({
				mceAction: 'setFileUrl',
				meta: {
					url: meta_file.url,
					alt_text : meta.alt_text,
					title : meta.title
				}
			});
			window.parent.postMessage({ mceAction: 'close' });
		}
	});
});