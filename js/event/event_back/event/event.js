function getCategoryByEventMainAid(){
	var sid = Math.floor(Math.random()*10000000000);
	if($("input[name='event_main_aid']").is(':radio')){
		var event_main_aid = $("input[name='event_main_aid']:checked").val()
	}else{
		var event_main_aid = $("#event_main_aid").val()
	}
	var full_url = base_url+"admin/event-category/ajax-get-category-by-event-main/"+sid+'/'+event_main_aid;

	jQuery.getJSON(full_url, 
		function(data){
			if (null != data) {
				if(data.status=="error"){
					alert(data.msg);
				}else if(data.status=="warning"){
					var txt = '<label class="control-label">';
					txt += data.msg;
					txt += '</label>';
					$('#category_area').html(txt);
				
				}else if(data.status=="success"){
					var txt = '';
					var default_category = jQuery('#category').val();
					var rList = default_category.split(/,/);
					// txt += '<ul class="da-form-list inline">';
					$.each(data.result, function(i,item){
						txt += '<label class="checkbox-inline"><input type="checkbox" id="category_'+item.aid+'" name="category[]" value="'+item.aid+'"';
						if(jQuery.inArray(item.aid, rList) >= 0){
							txt += ' checked';
						}
						txt += ' />'+item.name+'</label>';
					});
					// txt += '</ul>';
					$('#category_area').html(txt);
				}
			}
		}
	);
}

function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/event/ajax-get-main-list/"+sid;
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
	
	search_event_category = new Array();
	$("input[name='search_event_category[]']").each(function()
	{
		if(this.checked){
			search_event_category.push(this.value);
		}	
	});
	
	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status, search_event_category:search_event_category }) , 
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

	$("#search_event_category_all").attr('checked', true);
	$("input[name='search_event_category[]']").each(function()
	{
		this.checked = true;	
	});	
}

function checkToggleAdvanceSearch(){
	var search_post_word = $('#search_post_word').val();
	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();

	var search_status_all = $("#search_status_all:checked").val();
	var search_event_category_all = $("#search_event_category_all:checked").val();

	if( search_post_word == "" && search_post_word == "" && created_date_from == "" && created_date_to == "" && search_status_all && search_event_category_all){
		set_toggle_advance_search('hide');
	}else{
		set_toggle_advance_search('show');
	}
}

function processSubmit(option){
	$("#frm_event").validate();
	if ($("#frm_event").valid())
	{
		$('#save_option').val(option);
		// $('#description').val(nicEditors.findEditor('description').getContent());
		$('#description').val( tinyMCE.get('description_1').getContent() );
		$("#frm_event").submit();
	}
	else
	{
		return false;
	}
}
