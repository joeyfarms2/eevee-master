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
		var full_url = base_url+"admin/export/export-all/export-to-excel";
		// alert(full_url);
		window.open(full_url);
	}
	else
	{
		return false;
	}
}