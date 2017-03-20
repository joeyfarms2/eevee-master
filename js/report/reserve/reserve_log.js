function showSearchResult(){
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	
	var created_date_from_obj = new Date(created_date_from);
	var created_date_to_obj = new Date(created_date_to);

	if(created_date_to_obj != "" && created_date_from_obj > created_date_to_obj){
		alert("Report date to must greater than report date from.");
		return "";
	}

	jQuery("#frm_report").validate();
	if (jQuery("#frm_report").valid())
	{

		jQuery("#result-msg-box").html("");
		jQuery("#result-msg-box").removeClass().addClass("hidden");
		$('#tbldata_processing').removeClass("hidden");
		
		var sid = Math.floor(Math.random()*10000000000);	  
		var full_url = base_url+"admin/report/reserve-log/ajax-get-reserve-log-list/"+sid+"/owner";
		// alert(full_url);
		var search_order_by = $('#search_order_by').val();	
		var page_selected = $('#page_selected').val();	
		var search_record_per_page = jQuery('select[name=search_record_per_page]').val();	

		var created_date_from = $('#created_date_from').val();	
		var created_date_to = $('#created_date_to').val();	
				
		jQuery.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected, created_date_from:created_date_from, created_date_to:created_date_to }) , 
			function(data){
				if (null != data) {
					draw_table_result(data,'tbldata_wrapper',true);
				}
			}
		);

	}
	else
	{
		return false;
	}
}

function exportToExcel(){
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	
	var created_date_from_obj = new Date(created_date_from);
	var created_date_to_obj = new Date(created_date_to);

	if(created_date_to_obj != "" && created_date_from_obj > created_date_to_obj){
		alert("Report date to must greater than report date from.");
		return "";
	}

	var search_order_by = $('#search_order_by').val();	

	jQuery("#frm_report").validate();
	if (jQuery("#frm_report").valid())
	{

		jQuery("#result-msg-box").html("");
		jQuery("#result-msg-box").removeClass().addClass("hidden");
		
		var sid = Math.floor(Math.random()*10000000000);	  
		var full_url = base_url+"admin/report/reserve-log/export-reserve-log?created_date_from="+created_date_from+"&created_date_to="+created_date_to+"&search_order_by="+search_order_by;
		// alert(full_url);
		window.open(full_url);
	}
	else
	{
		return false;
	}
}