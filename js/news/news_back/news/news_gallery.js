function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"admin/news-gallery/ajax-get-main-list/"+sid;
	// get_alert_box("Debug",full_url);
	var search_record_per_page = ($('select[name=search_record_per_page]').val())? $('select[name=search_record_per_page]').val() : $('#search_record_per_page').val();
	var search_order_by = $('#search_order_by').val();	
	var page_selected = $('#page_selected').val();	
	var search_post_word = $('#search_post_word').val();	

	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();	
	if(created_date_from != "" && created_date_to != ""){
		if(created_date_from > created_date_to){
			get_alert_box("Warning message","End date must greater than or equal to start date.");
			$('#tbldata_processing').addClass("hidden");
			return "";
		}
	}

	search_in = new Array();
	$("input[name='search_in[]']").each(function()
	{
		if(this.checked){
			search_in.push(this.value);
		}	
	});
	
	search_status = new Array();
	$("input[name='search_status[]']").each(function()
	{
		if(this.checked){
			search_status.push(this.value);
		}	
	});
	
	var news_aid = $('#news_aid').val();	

	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status, news_aid:news_aid }) , 
		function(data){
			if(null != data){
				draw_table_result(data,'tbldata_wrapper',true);
			}
		}
	);
}

function checkBeforeProcess(form_name,option){
	$("#"+form_name).validate();
	if ($("#"+form_name).valid())
	{
		// var update_copies = $("#update_copies:checked").val();
		// if(update_copies){
		// 	txt += '- All price';
		// }
		$("#"+form_name).submit();
	}
	else
	{
		return false;
	}
	// processSubmitOption('frm_product', '0');		
}

