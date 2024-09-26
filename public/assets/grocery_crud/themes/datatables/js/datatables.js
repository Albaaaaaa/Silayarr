var default_per_page = typeof default_per_page !== 'undefined' ? default_per_page : 25;
var oTable = null;
var oTableArray = [];
var oTableMapping = [];

function supports_html5_storage()
{
	try {
		JSON.parse("{}");
		return 'localStorage' in window && window['localStorage'] !== null;
	} catch (e) {
		return false;
	}
}

function success_message(message) {
	$('#list-report-success').html(message);
    $('#list-report-success').slideDown();
}

function error_message(message) {
    $('#list-report-error').html(message);
    $('#list-report-error').slideDown();
}

var use_storage = supports_html5_storage();

var aButtons = [];
var mColumns = [];

$(document).ready(function() {

	$('table.groceryCrudTable thead tr th').each(function(index){
		if(!$(this).hasClass('actions'))
		{
			mColumns[index] = index;
		}
	});

    if(!unset_export)
    {
        aButtons.push({
            "sExtends":    "text",
            "sButtonText": export_text
        });
    }

	if(!unset_print)
	{
		aButtons.push({
	         "sExtends":    "print",
	         "sButtonText": print_text,
	         "mColumns": mColumns
	     });
	}

	//For mutliplegrids disable bStateSave as it is causing many problems
	if ($('.groceryCrudTable').length > 1) {
		use_storage = false;
	}

	$('.groceryCrudTable').each(function(index){
		if (typeof oTableArray[index] !== 'undefined') {
			return false;
		}

		oTableMapping[$(this).attr('id')] = index;

		oTableArray[index] = loadDataTable(this);
	});

	$(".groceryCrudTable tfoot input").keyup( function () {

		chosen_table = datatables_get_chosen_table($(this).closest('.groceryCrudTable'));

		chosen_table.fnFilter( this.value, chosen_table.find("tfoot input").index(this) );

		if(use_storage)
		{
			var search_values_array = [];

			chosen_table.find("tfoot tr th").each(function(index,value){
				search_values_array[index] = $(this).children(':first').val();
			});

			localStorage.setItem( 'datatables_search_'+ unique_hash ,'["' + search_values_array.join('","') + '"]');
		}
	} );

	var search_values = localStorage.getItem('datatables_search_'+ unique_hash);

	if( search_values !== null)
	{
		$.each($.parseJSON(search_values),function(num,val){
			if(val !== '')
			{
				$(".groceryCrudTable tfoot tr th:eq("+num+")").children(':first').val(val);
			}
		});
	}

	$('.clear-filtering').click(function(){
		localStorage.removeItem( 'DataTables_' + unique_hash);
		localStorage.removeItem( 'datatables_search_'+ unique_hash);

		chosen_table = datatables_get_chosen_table($(this).closest('.groceryCrudTable'));

		chosen_table.fnFilterClear();
		$(this).closest('.groceryCrudTable').find("tfoot tr th input").val("");
	});

	loadListenersForDatatables();

	$('a.ui-button').on("mouseover mouseout", function(event) {
		  if ( event.type == "mouseover" ) {
			  $(this).addClass('ui-state-hover');
		  } else {
			  $(this).removeClass('ui-state-hover');
		  }
	});

	$('th.actions').unbind('click');
	$('th.actions>div .DataTables_sort_icon').remove();
	
	$('table').on('click','.delete-row', function(){
		let delete_url = $(this).attr('href');
		let this_container = $(this).closest('.flexigrid');
		let $this = $(this);
		let row_id = $this.attr('data-row-id');
		
		$bootbox =  bootbox.dialog({
			title: '',
			message: message_alert_delete,
			buttons: {
				cancel: {
					label: lang_btn_cancel
				},
				success: {
					label: lang_btn_ok,
					className: 'btn-success submit',
					callback: function() 
					{
						let $button = $bootbox.find('button').prop('disabled', true);
						let $button_submit = $bootbox.find('button.submit');
						let $spinner = $('<span class="spinner-border spinner-border-sm me-2"></span>');
						$button_submit.prepend($spinner);
						$button.prop('disabled', true);
						
						form = $bootbox.find('form')[0];
						$.ajax({
							url: delete_url,
							dataType: 'json',
							success: function (data) {
								
								$bootbox.modal('hide');
								if (data.success) {
								/* 	
									if(data.success)
				{
					chosen_table = datatables_get_chosen_table($('tr#row-'+row_id).closest('.groceryCrudTable'));

					$('tr#row-'+row_id).addClass('row_selected');
					var anSelected = fnGetSelected( chosen_table );
					chosen_table.fnDeleteRow( anSelected[0] );
				}
				else
				{
					error_message(data.error_message);
				} */
				
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
										html: '<div class="toast-content"><i class="far fa-check-circle me-2"></i> ' + lang_delete_success_message + '</div>'
									})
									
									chosen_table = datatables_get_chosen_table($('tr#row-'+row_id).closest('.groceryCrudTable'));

									$('tr#row-'+row_id).addClass('row_selected');
									var anSelected = fnGetSelected( chosen_table );
									chosen_table.fnDeleteRow( anSelected[0] );
									
								} else {
									show_alert(data.error_message, 'error');
								}
							},
							error: function (xhr) {
								$bootbox.modal('hide');
								show_alert('Error !!!', xhr.responseText, 'error');
								console.log(xhr.responseText);
							}
						})
						return false;
					}
				}
			}
		});

		/* if( confirm( message_alert_delete ) )
		{
			$.ajax({
				url: delete_url,
				dataType: 'json',
				success: function(data)
				{
					if(data.success)
					{
						this_container.find('.ajax_refresh_and_loading').trigger('click');
					}
					else
					{
						error_message(data.error_message);

					}
				}
			});
		}
 */
		return false;
	});

} );

function loadListenersForDatatables() {

	$('.refresh-data').click(function(){
		var this_container = $(this).closest('.dataTablesContainer');

		var new_container = $("<div/>").addClass('dataTablesContainer');

		this_container.after(new_container);
		this_container.remove();

		$.ajax({
			url: $(this).attr('data-url'),
			success: function(my_output){
				new_container.html(my_output);

				loadDataTable(new_container.find('.groceryCrudTable'));

				loadListenersForDatatables();
			}
		});
	});
}

function loadDataTable(this_datatables) {
	
	/* let url = $('#grocery-data-tables-ajax-url').text();
	let column_new = JSON.parse($('#grocery-data-tables-ajax-column').text());
	// console.log(column);
	console.log(column_new);
	
		
	const settings = {
        "processing": true,
        "serverSide": true,
		"scrollX": true,
		"ajax": {
            "url": url,
            "type": "POST"
        },
        "columns": column_new,
		"initComplete": function( settings, json ) {
			
		 }
    }
	
	let $add_setting = $('#grocery-data-tables-ajax-setting');
	if ($add_setting.length > 0) {
		add_setting = JSON.parse($add_setting.text());
		if (add_setting) {
			for (k in add_setting) {
				settings[k] = add_setting[k];
			}
		}
	}
	
	return $(this_datatables).DataTable( settings ); */
	
	return $(this_datatables).dataTable({
		"bStateSave": use_storage,
        "fnStateSave": function (oSettings, oData) {
            localStorage.setItem( 'DataTables_' + unique_hash, JSON.stringify(oData) );
        },
    	"fnStateLoad": function (oSettings) {
            return JSON.parse( localStorage.getItem('DataTables_'+unique_hash) );
    	},
		"iDisplayLength": default_per_page,
		"aaSorting": datatables_aaSorting,
		"fnInitComplete" : function () {
            $('.DTTT_button_text').attr('download', '');
            $('.DTTT_button_text').attr('href', export_url);
		},
		"oLanguage":{
		    "sProcessing":   list_loading,
		    "sLengthMenu":   show_entries_string,
		    "sZeroRecords":  list_no_items,
		    "sInfo":         displaying_paging_string,
		    "sInfoEmpty":   list_zero_entries,
		    "sInfoFiltered": filtered_from_string,
		    "sSearch":       search_string+":",
		    "oPaginate": {
		        "sFirst":    paging_first,
		        "sPrevious": paging_previous,
		        "sNext":     paging_next,
		        "sLast":     paging_last
		    }
		},
		"bDestory": true,
		"bRetrieve": true,
		"fnDrawCallback": function() {
			add_edit_button_listener();
            $('.DTTT_button_text').attr('href', export_url);
		},
		
	    "oTableTools": {
	    	"aButtons": aButtons,
	        "sSwfPath": base_url+"assets/grocery_crud/themes/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
	    }
		
	});
	
	/* return $(this_datatables).dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": use_storage,
        "fnStateSave": function (oSettings, oData) {
            localStorage.setItem( 'DataTables_' + unique_hash, JSON.stringify(oData) );
        },
    	"fnStateLoad": function (oSettings) {
            return JSON.parse( localStorage.getItem('DataTables_'+unique_hash) );
    	},
		"iDisplayLength": default_per_page,
		"aaSorting": datatables_aaSorting,
		"fnInitComplete" : function () {
            $('.DTTT_button_text').attr('download', '');
            $('.DTTT_button_text').attr('href', export_url);
		},
		"oLanguage":{
		    "sProcessing":   list_loading,
		    "sLengthMenu":   show_entries_string,
		    "sZeroRecords":  list_no_items,
		    "sInfo":         displaying_paging_string,
		    "sInfoEmpty":   list_zero_entries,
		    "sInfoFiltered": filtered_from_string,
		    "sSearch":       search_string+":",
		    "oPaginate": {
		        "sFirst":    paging_first,
		        "sPrevious": paging_previous,
		        "sNext":     paging_next,
		        "sLast":     paging_last
		    }
		},
		"bDestory": true,
		"bRetrieve": true,
		"fnDrawCallback": function() {
			add_edit_button_listener();
            $('.DTTT_button_text').attr('href', export_url);
		},
		"sDom": 'T<"clear"><"H"lfr>t<"F"ip>',
	    "oTableTools": {
	    	"aButtons": aButtons,
	        "sSwfPath": base_url+"assets/grocery_crud/themes/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
	    }
	}); */
}

function delete_row(delete_url , row_id)
{
	if(confirm(message_alert_delete))
	{
		$.ajax({
			url: delete_url,
			dataType: 'json',
			success: function(data)
			{
				if(data.success)
				{
					chosen_table = datatables_get_chosen_table($('tr#row-'+row_id).closest('.groceryCrudTable'));

					$('tr#row-'+row_id).addClass('row_selected');
					var anSelected = fnGetSelected( chosen_table );
					chosen_table.fnDeleteRow( anSelected[0] );
				}
				else
				{
					error_message(data.error_message);
				}
			}
		});
	}

	return false;
}

function datatables_get_chosen_table(table_as_object)
{
	chosen_table_index = oTableMapping[table_as_object.attr('id')];
	return oTableArray[chosen_table_index];
}

function fnGetSelected( oTableLocal )
{
	var aReturn = new Array();
	var aTrs = oTableLocal.fnGetNodes();

	for ( var i=0 ; i<aTrs.length ; i++ )
	{
		if ( $(aTrs[i]).hasClass('row_selected') )
		{
			aReturn.push( aTrs[i] );
		}
	}
	return aReturn;
}