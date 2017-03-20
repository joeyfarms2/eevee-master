function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"admin/product/book-field/ajax-get-main-list/"+sid;
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
	
	var product_main_aid = $('#product_main_aid').val();
	var parent_aid = $('#parent_aid').val();	

	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status, product_main_aid:product_main_aid, parent_aid:parent_aid }) , 
		function(data){
			if(null != data){
				draw_table_result(data,'tbldata_wrapper',true);
			}
		}
	);
}

function clearSearchResult(){
	$('#page_selected').val('');
	$('#search_order_by').val('');
	$('#search_post_word').val('');
	$('#created_date_from').val('');
	$('#created_date_to').val('');

	$("#search_status_all").attr('checked', true);
	$("input[name='search_status[]']").each(function()
	{
		this.checked = true;	
	});

	$("#search_in_all").attr('checked', true);
	$("input[name='search_in[]']").each(function()
	{
		this.checked = true;	
	});

	$("#search_product_main_all").attr('checked', false);
	$("input[name='search_product_main[]']").each(function()
	{
		this.checked = false;	
	});	
}

function processDeleteField(parent_aid, sequence, url, name){
	clearThenHide('result-msg-box');
	get_confirm_box("Confirmation?","Confirm to delete '"+name+"' ?","ajaxDeleteField('"+parent_aid+"', '"+sequence+"', '"+url+"')");
}

function ajaxDeleteField(parent_aid, sequence , url){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+url+"/ajax-delete-one/"+sid;
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({parent_aid:parent_aid, sequence:sequence }),
		dataType: "json",
		beforeSend: function(data) {
			$('#tbldata_processing').removeClass("hidden");
		},
		success: function(data){
			showSearchResult(data);
			show_result_box(data);
		}
	});
}
