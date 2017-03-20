function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);
	var type = $('#type').val();
	var barcode = $('#barcode').val();
	var user_cid = $('#user_cid').val();
	var full_url = base_url+"admin/reservation-"+type+"/ajax-get-main-list/"+sid;
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
	
	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status, barcode:barcode, user_cid:user_cid }) , 
		function(data){
			if (null != data) {
				draw_table_result(data,'tbldata_wrapper',true);
			}
		}
	);
}

function clearSearchResult(type){
	set_toggle_advance_search('show');

	$('#page_selected').val('');
	$('#search_order_by').val('');
	$('#search_post_word').val('');
	$('#created_date_from').val('');
	$('#created_date_to').val('');
	$('#barcode').val('');
	$('#user_cid').val('');

	$("#search_in_all").attr('checked', true);
	$("input[name='search_in[]']").each(function()
	{
		this.checked = true;	
	});

	$("#search_status_all").attr('checked', false);
	$("#search_status_1").attr('checked', true);
	$("#search_status_2").attr('checked', false);
	$("#search_status_3").attr('checked', false);
	$("#search_status_4").attr('checked', false);
}

function checkToggleAdvanceSearch(){
	var search_post_word = $('#search_post_word').val();
	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();

	var search_status_all = $("#search_status_all:checked").val();
	var search_status_0 = $("#search_status_0:checked").val();
	var search_status_1 = $("#search_status_1:checked").val();
	var search_status_2 = $("#search_status_2:checked").val();
	var search_status_3 = $("#search_status_3:checked").val();

	if( search_post_word == "" && search_post_word == "" && created_date_from == "" && created_date_to == "" && search_status_all){
		set_toggle_advance_search('show');
	}else{
		set_toggle_advance_search('show');
	}
}

function processChangeReserveValue(msg, url, aid, command){
	clearThenHide('result-msg-box');
	msg = msg.replace('active ', 'activate ');
	var expiration_date = $('#init_expiration_date').val();
	txt = '';
	txt += '<div data-date-viewmode="month" data-date-format="dd-mm-yyyy" data-date=""  class="input-group date form_datetime-adv col-md-6">';
	txt += '<input id="expiration_date" name="expiration_date" type="text" readonly="" value="'+expiration_date+'" size="16" class="form-control">';
	txt += '<span class="input-group-btn">';
	txt += '<button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>';
	txt += '</span>';
	txt += '</div>';

	get_confirm_box("Confirmation?","Approve this reservation?<BR><BR>Pick up within :"+txt,"ajaxChangeReserveValue('"+url+"', '"+aid+"', '"+command+"')");

	$("#expiration_date").datepicker({
		format: "yyyy-mm-dd",
		todayBtn: true,
		todayHighlight: true,
		autoclose: true
	}).on('changeDate', function(ev){
	});

}

function ajaxChangeReserveValue(url, aid, command){
	// alert(command);
	var sid = Math.floor(Math.random()*10000000000);	
	var arr = command.split("=");
	var full_url = base_url+url+"/ajax-set-value"+"/"+sid;
	var expiration_date = $('#expiration_date').val();

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({aid_selected:aid, f_name:arr[0], f_value:arr[1], expiration_date:expiration_date }),
		dataType: "json",
		beforeSend: function(data) {
			$('#tbldata_processing').removeClass("hidden");
		},
		success: function(data){
			showSearchResult();
			show_result_box(data);
		}
	});
}
