function processSubmit(option){
	$("#frm_transaction").validate();
	if ($("#frm_transaction").valid())
	{
		$('#save_option').val(option);
		$("#frm_transaction").submit();
	}
	else
	{
		return false;
	}
}

function showSearchResult(mode){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/transaction/ajax-get-main-list/"+sid;
	// alert(full_url);
	var search_record_per_page = ($('select[name=search_record_per_page]').val())? $('select[name=search_record_per_page]').val() : $('#search_record_per_page').val();
	var search_order_by = $('#search_order_by').val();
	var page_selected = $('#page_selected').val();
	var search_post_word = $('#search_post_word').val();

	var borrowing_date_from = $('#borrowing_date_from').val();
	var borrowing_date_to = $('#borrowing_date_to').val();	
	if(borrowing_date_from != "" && borrowing_date_to != ""){
		if(borrowing_date_from > borrowing_date_to){
			get_alert_box("Warning message","End date must greater than or equal to start date.");
			$('#tbldata_processing').addClass("hidden");
			return "";
		}
	}
	
	var due_date_from = $('#due_date_from').val();
	var due_date_to = $('#due_date_to').val();	
	if(due_date_from != "" && due_date_to != ""){
		if(due_date_from > due_date_to){
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
	
	var user_aid = $('#user_aid').val();
	var search_option = $("input[name='search_option']:checked").val();
	
	if(mode == '1'){
		full_url = full_url+"?search_order_by="+search_order_by+"&search_post_word="+search_post_word+"&borrowing_date_from="+borrowing_date_from+"&borrowing_date_to="+borrowing_date_to+"&due_date_from="+due_date_from+"&due_date_to="+due_date_to+"&search_in="+search_in+"&search_status="+search_status+"&user_aid="+user_aid+"&mode="+mode+"&search_option="+search_option;
		// alert(full_url);
		window.open(full_url);
		$('#tbldata_processing').addClass("hidden");
		return;
	}

	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, borrowing_date_from:borrowing_date_from, borrowing_date_to:borrowing_date_to, due_date_from:due_date_from, due_date_to:due_date_to, search_in:search_in, search_status:search_status, user_aid:user_aid, mode:mode, search_option:search_option }) , 
		function(data){
			if (null != data) {
				if(mode == '2'){
					draw_table_result(data,'tbldata_wrapper',false);
				}else{
					draw_table_result(data,'tbldata_wrapper',true);
				}
				highlight_overdue('tbldata_wrapper');
			}
		}
	);
}

function highlight_overdue(divName){
	var table = $('#'+divName).find('table');
	var tr = $('#'+divName+' > tbody > tr');
	var today = new Date();
	$('#'+divName+' table tr').each(function(i, tr) {
		var due_date = $(".cls_due_date", tr).text();
		var returning_date = $(".cls_returning_date", tr).text();
		if(due_date != '' && returning_date == ''){
			var due_date = new Date(due_date);
			if(due_date <= today){
				$(".cls_due_date", tr).addClass('overdue');
			}
		}
	});
}

function clearSearchResult(){
	set_toggle_advance_search('hide');

	$('#page_selected').val('');
	$('#search_order_by').val('');
	$('#search_post_word').val('');
	$('#borrowing_date_from').val('');
	$('#borrowing_date_to').val('');
	$('#due_date_from').val('');
	$('#due_date_to').val('');

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

	$("#search_option_0").attr('checked', true);
}

function checkToggleAdvanceSearch(){
	var search_post_word = $('#search_post_word').val();
	var borrowing_date_from = $('#borrowing_date_from').val();
	var borrowing_date_to = $('#borrowing_date_to').val();
	var due_date_from = $('#due_date_from').val();
	var due_date_to = $('#due_date_to').val();

	var search_status_all = $("#search_status_all:checked").val();
	var search_option = $("input[name='search_option']:checked").val();

	if( search_post_word == "" && search_post_word == "" && borrowing_date_from == "" && borrowing_date_to == "" && due_date_from == "" && due_date_to == "" && search_status_all && search_option == "0"){
		set_toggle_advance_search('hide');
	}else{
		set_toggle_advance_search('show');
	}
}

function searchUser(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/user/ajax-get-popup-list/"+sid;
	var search_post_word_user = $('#search_post_word_user').val();
	search_in_user = new Array();
	$("input[name='search_in_user[]']").each(function()
	{
		if(this.checked){
			search_in_user.push(this.value);
		}	
	});
	var search_order_by_user = $('#search_order_by_user').val();
	var page_selected_user = $('#page_selected_user').val();

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({search_post_word_user:search_post_word_user , search_in_user:search_in_user , search_order_by_user:search_order_by_user , page_selected_user:page_selected_user }),
		dataType: "json",
		beforeSend: function(data) {
			$('#search_user_button').removeClass('fa-search').addClass('fa-spinner');
		},
		success: function(data){
			$('#search_user_button').addClass('fa-search').removeClass('fa-spinner');
			var status = data.status;
			var msg = data.msg;
			if(status == "success"){
				var result = data.result;
				if(result.length == 1){
					processRedirect('admin/transaction/user/'+result[0].aid);
				}else{
					draw_popup_result(data,'modal-msg',false,'searchUser','user');
					$('#modal-header').html('User List');
					$('#modal-button').html('<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>');
					$('#dialog_box').modal({
						backdrop: 'static',
						keyboard: false
					});
				}
			}else{
				get_alert_box(status , msg);
			}

		}
	});
}

function searchProduct(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var search_type_product = $("#search_type_product:checked").val();
	var product_main_aid = "6";
	if(search_type_product != 'magazine'){
		search_type_product = 'book';
	}else{
		product_main_aid = "7";
	}
	var full_url = base_url+"admin/product/"+search_type_product+"-copy/ajax-get-popup-list/"+sid;
	var search_post_word_product = $('#search_post_word_product').val();
	search_in_product = new Array();
	$("input[name='search_in_product[]']").each(function()
	{
		if(this.checked){
			search_in_product.push(this.value);
		}	
	});
	var search_order_by_product = $('#search_order_by_product').val();
	var page_selected_product = $('#page_selected_product').val();
	var user_aid = $('#user_aid').val();

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({product_main_aid:product_main_aid , search_post_word_product:search_post_word_product , search_in_product:search_in_product , search_order_by_product:search_order_by_product , page_selected_product:page_selected_product , user_aid:user_aid , search_type_product:search_type_product }),
		dataType: "json",
		beforeSend: function(data) {
			$('#search_product_button').removeClass('fa-search').addClass('fa-spinner');
		},
		success: function(data){
			$('#search_product_button').addClass('fa-search').removeClass('fa-spinner');
			var status = data.status;
			var msg = data.msg;
			if(status == "success"){
				var result = data.result;
				if(result.length == 1){
					// processRedirect('admin/transaction/user/'+result[0].aid);
					var barcode = result[0].barcode;
					var product_type_aid = result[0].product_type_aid;
					var user_aid = $('#user_aid').val();

					addProductToTransactionByUser(user_aid, product_type_aid, barcode)
				}else{
					draw_popup_result(data,'modal-msg',false,'searchProduct','product');
					$('#modal-header').html('Product List');
					$('#modal-button').html('<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>');
					$('#dialog_box').modal({
						backdrop: 'static',
						keyboard: false
					})
				}
			}else{
				get_alert_box(status , msg);
			}

		}
	});
}

function clearPopup(){
	$('#search_order_by').val('');
	$('#page_selected').val('');
}

function showTransactionResultByUser(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing_today').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/transaction/ajax-get-transaction-list-by-user/"+sid;
	// alert(full_url);

	var user_aid = $('#user_aid').val();

	$.getJSON(full_url, ({ user_aid:user_aid }) , 
		function(data){
			$('#tbldata_processing_today').addClass("hidden");
			if (null != data) {
				draw_transaction_result(data,'tbldata_wrapper_today',false);
				var summary = data.summary;
				$('#total_fee').val(summary.total_fee);
			}
			$('#search_post_word_product').val('');
			$('#search_post_word_product').focus();
			$('#total_receive').val('0');
			calculate_change();
		}
	);
}

function draw_transaction_result(data,divName,show_i){
	if(data.status=="error"){
		get_alert_box("Error",data.msg);
	}else if(data.status=="warning"){
		var txt = '';
		txt += '<div class="da-form-row">';
		txt += data.msg;
		txt += '</div>';
		$('#'+divName).html(txt);
	
	}else if(data.status=="success"){
		var user_aid = $('#user_aid').val();
		var txt = '';
		txt += '<table cellspacing="0" cellpadding="0" border="0" id="tbldata" class="display table table-bordered table-striped dataTable">';
		txt += '<thead>';
		txt += '<tr role="row">';
		txt += '<th class="hcenter">Name</th>';
		txt += '<th class="w170 hcenter">Due Date</th>';
		txt += '<th class="w50 hcenter">Type</th>';
		txt += '<th class="w50 hcenter">Fee</th>';
		txt += '<th class="w50">&nbsp;</th>';
		txt += '</tr>';
		txt += '</thead>';
		txt += '<tbody>';
		$.each(data.result, function(i,item){
			txt += '<tr role="row">';
			txt += '<td class="">'+item.title+'</td>';
			txt += '<td class="">';

			if(item.type == "return"){
				txt += item.due_date;
			}else{
				txt += '<div data-date-viewmode="month" data-date-format="dd-mm-yyyy" data-date=""  class="input-group date form_datetime-adv">';
				txt += '<input id="due_date_'+item.product_type_aid+'_'+item.copy_aid+'" name="due_date_'+item.product_type_aid+'_'+item.copy_aid+'" type="text" readonly="" value="'+item.due_date+'" size="16" class="form-control" onchange="changeDueDateProductToTransactionByUser(\''+user_aid+'\', \''+item.product_type_aid+'\', \''+item.barcode+'\', this.value);">';
				txt += '<span class="input-group-btn">';
				txt += '<button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>';
				txt += '</span>';
				txt += '</div>';
			}

			txt += '</td>';
			txt += '<td class="">'+item.type+'</td>';
			txt += '<td class="">'+item.fee+'</td>';
			txt += '<td>';
			txt += '<button class="btn btn-danger" type="button" onclick="removeProductToTransactionByUser(\''+user_aid+'\', \''+item.product_type_aid+'\', \''+item.barcode+'\');">';
			txt += '<i class="fa fa-trash-o"></i>';
			txt += '</button>';
			txt += '</td>';
			txt += '</tr>';
		});
		txt += '</tbody>';
		txt += '</table>';

		txt += '<div><i>*The transactions above are not saved until you click on “Save” button for confirmation.</i></div>';

		$('#'+divName).html(txt);
		
		$(".form_datetime-adv").datepicker({
			format: "yyyy-mm-dd",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
		}).on('changeDate', function(ev){
		});
	}
}

function addProductToTransactionByUser(user_aid, product_type_aid, barcode){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/transaction/ajax-add-product-to-transaction-by-user/"+sid;

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({user_aid:user_aid , product_type_aid:product_type_aid , barcode:barcode }),
		dataType: "json",
		beforeSend: function(data) {
			$('#search_product_button').removeClass('fa-search').addClass('fa-spinner');
			$('#search_order_by').val('');
		},
		success: function(data){
			$('#search_product_button').addClass('fa-search').removeClass('fa-spinner');
			var status = data.status;
			var msg = data.msg;
			if(status == "success"){
				showTransactionResultByUser();
			}else{
				get_alert_box(status , msg);
			}
		}
	});
}

function changeDueDateProductToTransactionByUser(user_aid, product_type_aid, barcode, new_due_date){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/transaction/ajax-change-due-date-product-to-transaction-by-user/"+sid;

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({user_aid:user_aid , product_type_aid:product_type_aid , barcode:barcode , new_due_date:new_due_date }),
		dataType: "json",
		beforeSend: function(data) {
			$('#search_product_button').removeClass('fa-search').addClass('fa-spinner');
		},
		success: function(data){
			$('#search_product_button').addClass('fa-search').removeClass('fa-spinner');
			var status = data.status;
			var msg = data.msg;
			if(status == "success"){
				showTransactionResultByUser();
			}else{
				get_alert_box(status , msg);
			}
		}
	});
}

function clearProductToTransactionByUser(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/transaction/ajax-clear-product-to-transaction-by-user/"+sid;

	var user_aid = $('#user_aid').val();
	$('#total_receive').val('');
	$('#search_post_word_product').val('');

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({user_aid:user_aid }),
		dataType: "json",
		beforeSend: function(data) {
			$('#search_product_button').removeClass('fa-search').addClass('fa-spinner');
		},
		success: function(data){
			$('#search_product_button').addClass('fa-search').removeClass('fa-spinner');
			var status = data.status;
			var msg = data.msg;
			if(status == "success"){
				showTransactionResultByUser();
			}else{
				get_alert_box(status , msg);
			}
		}
	});
}

function removeProductToTransactionByUser(user_aid, product_type_aid, barcode){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/transaction/ajax-remove-product-to-transaction-by-user/"+sid;

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({user_aid:user_aid , product_type_aid:product_type_aid , barcode:barcode }),
		dataType: "json",
		beforeSend: function(data) {
			$('#search_product_button').removeClass('fa-search').addClass('fa-spinner');
		},
		success: function(data){
			$('#search_product_button').addClass('fa-search').removeClass('fa-spinner');
			var status = data.status;
			var msg = data.msg;
			if(status == "success"){
				showTransactionResultByUser();
			}else{
				get_alert_box(status , msg);
			}
		}
	});
}

function saveTransactionByUser(){
	var total_fee = parseInt($('#total_fee').val());
	var total_receive = parseInt($('#total_receive').val());
	if(total_fee > 0 && total_receive < total_fee){
		get_alert_box("Error" , "Receive can not be less than total fee.");
		$('#total_receive').focus();
		return "";
	}

	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/transaction/ajax-save-product-transaction-by-user/"+sid;

	var user_aid = $('#user_aid').val();

	$.ajax({
		type: "POST",
		url: full_url,
		data: ({user_aid:user_aid}),
		dataType: "json",
		beforeSend: function(data) {
			$('#search_product_button').removeClass('fa-search').addClass('fa-spinner');
		},
		success: function(data){
			$('#search_product_button').addClass('fa-search').removeClass('fa-spinner');
			var status = data.status;
			var msg = data.msg;
			if(status == "success"){
				$('#total_receive').val('0');
				showSearchResult();
				showTransactionResultByUser();
			}else{
				get_alert_box(status , msg);
			}
		}
	});
}

function calculate_change(){
	var total_fee = $('#total_fee').val();
	var total_receive = $('#total_receive').val();
	// alert(total_fee);
	// alert(total_receive);
	var change = total_receive - total_fee;
	if(change > 0){
		$('#change_area').html(change);
	}else{
		$('#change_area').html(0);
	}
}