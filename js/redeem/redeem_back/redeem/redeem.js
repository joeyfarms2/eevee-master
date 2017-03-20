function processSubmit(option){
	$("#frm_redeem").validate();
	if ($("#frm_redeem").valid())
	{
		$('#save_option').val(option);
		$("#frm_redeem").submit();
	}
	else
	{
		return false;
	}
}

function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/redeem/ajax-get-main-list/"+sid;
	// alert(full_url);
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
	
	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status }) , 
		function(data){
			if (null != data) {
				draw_table_result(data,'tbldata_wrapper',true);
			}
		}
	);
}

function clearSearchResult(){
	set_toggle_advance_search('hide');

	$('#page_selected').val('');
	$('#search_order_by').val('');
	$('#search_post_word').val('');
	$('#created_date_from').val('');
	$('#created_date_to').val('');

	$("#search_in_all").attr('checked', true);
	$("input[name='search_in[]']").each(function()
	{
		this.checked = true;	
	});

	$("#search_status_all").attr('checked', true);
	$("input[name='search_status[]']").each(function()
	{
		this.checked = true;	
	});
}

function checkToggleAdvanceSearch(){
	var search_post_word = $('#search_post_word').val();
	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();

	var search_status_all = $("#search_status_all:checked").val();

	if( search_post_word == "" && search_post_word == "" && created_date_from == "" && created_date_to == "" && search_status_all){
		set_toggle_advance_search('hide');
	}else{
		set_toggle_advance_search('show');
	}
}

function generate_example(){
	var code_prefix = $("#code_prefix").val();
	var code_length = $("#code_length").val();
	var code_postfix = $("#code_postfix").val();

	var result = '';
	if(code_prefix != ''){
		result += code_prefix;
	}

	if(code_length > 0){
		result += randomRedeem(code_length);
	}

	if(code_postfix != ''){
		result += code_postfix;
	}

	$('#redeem_example').html(result);

}

function randomRedeem(string_length) {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZ";
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
}
