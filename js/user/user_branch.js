function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/user-branch/ajax-get-main-list/"+sid;
	// get_alert_box("Debug",full_url);
	var search_order_by = $('#search_order_by').val();	
	var page_selected = $('#page_selected').val();	
	var search_record_per_page = $('select[name=search_record_per_page]').val();	
	var search_post_word = $('#search_post_word').val();	

	var created_date_from = $('#created_date_from').val();	
	var created_date_to = $('#created_date_to').val();	
	var expiration_date_from = $('#expiration_date_from').val();	
	var expiration_date_to = $('#expiration_date_to').val();	
	
	search_in = new Array();
	$("input[name='search_in[]']").each(function()
	{
		if(this.checked){
			search_in.push(this.value);
		}	
	});
	
	search_role = new Array();
	$("input[name='search_role[]']").each(function()
	{
		if(this.checked){
			search_role.push(this.value);
		}	
	});
	
	search_status = new Array();
	$("input[name='search_status[]']").each(function()
	{
		if(this.checked){
			search_status.push(this.value);
		}	
	});
	
	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, search_in:search_in, search_role:search_role, search_status:search_status, created_date_from:created_date_from, created_date_to:created_date_to, expiration_date_from:expiration_date_from, expiration_date_to:expiration_date_to }) , 
		function(data){
			if (null != data) {
				draw_table_result(data,'tbldata_wrapper',true);
			}
		}
	);
}
