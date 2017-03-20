function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"admin/user/ajax-get-main-list/"+sid;
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
	
	search_role = new Array();
	$("input[name='search_role[]']").each(function()
	{
		if(this.checked){
			search_role.push(this.value);
		}	
	});
	
	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status, search_role:search_role }) , 
		function(data){
			if(null != data){
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

	$("#search_role_all").attr('checked', true);
	$("input[name='search_role[]']").each(function()
	{
		this.checked = true;	
	});	
}

function checkToggleAdvanceSearch(){
	var search_post_word = $('#search_post_word').val();
	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();

	var search_status_all = $("#search_status_all:checked").val();
	var search_role_all = $("#search_role_all:checked").val();

	if( search_post_word == "" && search_post_word == "" && created_date_from == "" && created_date_to == "" && search_status_all && search_role_all){
		set_toggle_advance_search('hide');
	}else{
		set_toggle_advance_search('show');
	}
}

function changGenPassOption(obj){
	$("input[id=password]").removeClass('error');
	$("input[id=password_retype]").removeClass('error');
	$("input[id=password]").val('');
	$("input[id=password_retype]").val('');
	if(obj.checked){
		$("input[id=password]").attr('disabled', true);
		$("input[id=password_retype]").attr('disabled', true);
		// $("#send_activate").attr('checked', false);
	}else{
		$("input[id=password]").attr('disabled', false);
		$("input[id=password_retype]").attr('disabled', false);
		// $("#send_activate").attr('checked', true);
	}
}

function changeUserRoleAid(){
	var user_role_aid = "";
	$("input[name='user_role_aid']").each(function()
	{
		if(this.checked){
			user_role_aid = this.value;
		}	
	});
	
	if(user_role_aid == 6){
		$('#publisher_area').removeClass('hidden');
		$('#publisher_aid').removeClass('readonly');
		$("select[id='publisher_aid']").attr('disabled', false);
	}else{
		$('#publisher_area').addClass('hidden');
		$('#publisher_aid').val('0');
		$('#publisher_aid').addClass('readonly');
		$("select[id='publisher_aid']").attr('disabled', true);
	}
}

function changeUserSectionAid(){
	 var user_section_aid = "";
	$("input[name='user_section_aid']").each(function()
	{
		if(this.checked){
			user_section_aid = this.value;
		}	
	});
		if(user_section_aid == 1) {
			$('#form_note_2').removeClass("hide");
			$('#form_note_1').removeClass("hide");
			$('#form_department').addClass("hide");
			$('#form_note_4').addClass("hide");

		}else{
			
			$('#form_note_2').addClass("hide");
			$('#form_note_1').addClass("hide");
			$('#form_department').removeClass("hide");
			$('#form_note_4').removeClass("hide");
		}
}

function exportToExcel(){
	clearThenHide('result-msg-box');
	
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"admin/user/ajax-get-main-list/"+sid;
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
	
	search_role = new Array();
	$("input[name='search_role[]']").each(function()
	{
		if(this.checked){
			search_role.push(this.value);
		}	
	});

	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/user/export-user?search_order_by="+search_order_by+"&search_post_word="+search_post_word+"&created_date_from="+created_date_from+"&created_date_to="+created_date_to+"&search_in="+search_in+"&search_status="+search_status+"&search_role="+search_role;
	// alert(full_url);
	window.open(full_url);

}