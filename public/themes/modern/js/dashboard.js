$(document).ready(function() {
	
	if ($('#table-dokumen-result').length) {
		const column = $.parseJSON($('#dataTablesDokumen-column').html());
		const url = $('#dataTablesDokumen-url').text();
		
		const settingDokumen = {
			"processing": true,
			"serverSide": true,
			"scrollX": true,
			pageLength : 5,
			lengthChange: false,
			"ajax": {
				"url": url,
				"type": "POST"
			},
			"columns": column
		}
		
		let $add_setting = $('#dataTablesDokumen-setting');
		if ($add_setting.length > 0) {
			add_setting = $.parseJSON($('#dataTablesDokumen-setting').html());
			for (k in add_setting) {
				settingDokumen[k] = add_setting[k];
			}
		}
		
		dataTablesDokumen =  $('#table-dokumen-result').DataTable( settingDokumen );
		$col = $('#table-dokumen-result_wrapper').children().eq(0).children().eq(0);
		$kategori = $('#id-dokumen-kategori').show();
		$kategori.appendTo($col);
		$('#id-dokumen-kategori').select2({'theme':'bootstrap-5'});
		
		$('#id-dokumen-kategori').change(function() {
			new_url =  base_url + 'dashboard/getDataDTDokumen?id_dokumen_kategori=' + $(this).val();
			dataTablesDokumen.ajax.url( new_url ).load();
		});
	}
	
	data_kategori = JSON.parse(jumlah_item_kategori);
	let background_kategori = [];
	data_kategori.map( () => {
		background_kategori.push(dynamicColors());
	})
	const dataChartKategori = {
		labels: JSON.parse(label_kategori),
		datasets: [{
			label: 'Top Kategori',
			data: data_kategori,
			backgroundColor: background_kategori,
			hoverOffset: 4
		}]
	};
	console.log(label_kategori);
	const configChartKategori = {
		type: 'doughnut',
		data: dataChartKategori,
		options: {
			responsive: false,
			// maintainAspectRatio: false,
			title: {
				display: false,
				text: '',
				fontSize: 14,
				lineHeight:3
			},
			plugins: {
			  legend: {
				display: true,
				position: 'bottom',
				fullWidth: false,
				labels: {
					padding: 10,
					boxWidth: 30
				}
			  },
			  title: {
				display: false,
				text: 'Kategori'
			  }
			}
		}
	};
	
	/* Kategori */
	if ( $('#chart-kategori').length > 0) {
		var ctx = document.getElementById('chart-kategori').getContext('2d');
		window.chartKategori = new Chart(ctx, configChartKategori);
	}
});