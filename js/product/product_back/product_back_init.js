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

	$("#search_product_category_all").attr('checked', false);
	$("input[name='search_product_category[]']").each(function()
	{
		this.checked = false;	
	});	
}

function checkToggleAdvanceSearch(){
	var search_post_word = $('#search_post_word').val();
	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();

	var search_status_all = $("#search_status_all:checked").val();
	var search_product_category_all = $("#search_product_category_all:checked").val();
	var search_product_category_check = true;
	$("input[name='search_product_category[]']").each(function()
	{
		if(this.checked == true){
			search_product_category_check = false;
		}
	});	

	if( search_post_word == "" && search_post_word == "" && created_date_from == "" && created_date_to == "" && search_status_all && search_product_category_check == true){
		set_toggle_advance_search('hide');
	}else{
		set_toggle_advance_search('show');
	}
}

function check_file_uploads_limit(){
	var use_digital_gen = $('input[name=use_digital_gen]:checked').val();
	if(use_digital_gen == "0"){
		$('#file_upload_limit_for_default').addClass("hide");
		$('#file_upload_limit_for_digital_gen').removeClass("hide");
		check_file_limit();
	}else if(use_digital_gen == "1"){
		$('#file_upload_limit_for_default').removeClass("hide");
	 	$('#file_upload_limit_for_digital_gen').addClass("hide");
		$('#file_upload_area').addClass("hide");
		$('#file_upload_ftp_area').addClass("hide");
		$('#file_upload_ftp_update_area').addClass("hide");
		$('#file_upload_area_2').addClass("hide");

	}
}

function check_file_limit(){
	var digital_file_type = $('input[name=digital_file_type]:checked').val();
	if(digital_file_type == "others"){
		$("#file_upload_area").removeClass("hide");
		$("#file_upload_ftp_area").addClass("hide");
		$("#file_type_desc").html(localStorage.getItem("const_file_type_default"));
		$("#file_upload_area_2").addClass("hide");
	}else if(digital_file_type == "upload_file"){
		$("#file_upload_area_2").removeClass("hide");
		$("#file_upload_ftp_area").addClass("hide");
		//$("#file_type_desc").html(".pdf");
		$("#file_upload_ftp_update_area").addClass("hide");
		$("#file_upload_area").addClass("hide");
	}else if(digital_file_type == "pdf"){
		$("#file_upload_area").removeClass("hide");
		$("#file_upload_ftp_area").addClass("hide");
		$("#file_type_desc").html(".pdf");
		$("#file_upload_area_2").addClass("hide");
	}else if(digital_file_type == "ftp"){
		$("#file_upload_area").addClass("hide");
		$("#file_upload_ftp_update_area").addClass("hide");
		$("#file_upload_ftp_area").removeClass("hide");
		$("#digital_file_type_ftp").attr('checked', true);
		$("#file_type_desc").html("");
		$("#file_upload_area_2").addClass("hide");
	}
	else if(digital_file_type == "ftp_update"){
		$("#file_upload_area").addClass("hide");
		$("#file_upload_ftp_area").addClass("hide");
		$("#file_upload_ftp_update_area").removeClass("hide");
		$("#digital_file_type_ftp_update").attr('checked', true);
		$("#file_type_desc").html("");
		$("#file_upload_area_2").addClass("hide");
	}
}

function rewritePriceZone(){
	var type = $('input[name=type]:checked').val();
	if(type == 1){ // digital
		$('#is_license').attr('disabled', false);
	}else{ // paper
		$('#is_license').attr('checked', false);
		$('#is_license').attr('disabled', true);
		$("#possession_2").attr('checked', true);
		$('#possession_1').attr('disabled', true);
	}

	var is_license = $("#is_license:checked").val();
	if(is_license){
		$('#ebook_concurrence').addClass('required');

		$('#concurrence_area').removeClass('hide');
		$("#possession_2").prop("checked", true); 
		$('#possession_1').attr('disabled', true);
	}else{
		$('#ebook_concurrence').removeClass('error');
		$('#ebook_concurrence').removeClass('required');

		$('#ebook_concurrence').val('');
		$('#concurrence_area').addClass('hide')
		$('#possession_1').attr('disabled', false);
	}
	var possession = $('input[name=possession]:checked').val();
	if(possession == 2){ // rental
		$('#rental_period').addClass('required');
		$('#rental_fee').addClass('required');
		$('#rental_fee_point').addClass('required');
		$('#rental_fine_fee').addClass('required');

		if(type == 1){  // digital
			$('#rental_price_area').addClass('hide');
			$('#digital_rental_day_area').removeClass('hide');
		}else{ //paper
			$('#rental_price_area').removeClass('hide');
			$('#digital_rental_day_area').addClass('hide');
		}

		$('#option_digital_area').addClass('hide');
		$('#option_paper_area').addClass('hide');
		$('#option_rental_area').removeClass('hide');
	}else{ // free
		if(type == 1){ // digital
			$('#option_digital_area').removeClass('hide');
			$('#option_paper_area').addClass('hide');
		}else{ //paper
			$('#option_digital_area').addClass('hide');
			$('#option_paper_area').removeClass('hide');
		}

		$('#rental_price_area').addClass('hide');

		$('#rental_period').val('');
		$('#rental_fee').val('');
		$('#rental_fee_point').val('');
		$('#rental_fine_fee').val('');
		$('#shelf_name').val('');

		$('#rental_period').removeClass('error');
		$('#rental_fee').removeClass('error');
		$('#rental_fee_point').removeClass('error');
		$('#rental_fine_fee').removeClass('error');
		$('#rental_period').removeClass('required');
		$('#rental_fee').removeClass('required');
		$('#rental_fee_point').removeClass('required');
		$('#rental_fine_fee').removeClass('required');

		$('#option_rental_area').addClass('hide');
	}

	if(type == 2 && possession == 2){
		$('#option_shelf_area').removeClass('hide');
	}else{
		$('#option_shelf_area').addClass('hide');
		$("#shelf_status_1").prop("checked", true); 
		$('#shelf_status').val('');
	}
}

function changeTrasportType(){
	var transport_aid = $('input[name=transport_aid]:checked').val();
	if(transport_aid == 0){
		$('#transport_price').attr('disabled', false);
	}else{
		$('#transport_price').val('');
		$('#transport_price').attr('disabled', true);
	}
}

function generateCopyFile(product_type_aid, copy_aid){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/product/ajax-generate-file/"+sid;
	console.log(product_type_aid+" | "+ copy_aid);
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_aid:product_type_aid, copy_aid:copy_aid }),
		dataType: "json",
		beforeSend: function(data) {
			$('#generate_file_message').removeClass('hide');
			$('#generate_file_button').addClass('hide');
		},
		success: function(data){
			console.log(data);
			var status = data.status;
			var msg = data.msg;
			if(status == "error"){
				get_alert_box("Error message",msg);
			}else if(status == "success"){
				get_alert_box("Success message",msg);
			}
			$('#generate_file_message').addClass('hide');
			$('#generate_file_button').removeClass('hide');
		},
		error: function(xhr, status, exception) { 
			console.log("^_^ | "+status+" | "+exception+" | "+JSON.stringify(xhr)); 
		}
	});
}

function updateCopyFile(product_type_aid, copy_aid){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/product/ajax-generate-update-file/"+sid;
	console.log(product_type_aid+" | "+ copy_aid);
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_aid:product_type_aid, copy_aid:copy_aid }),
		dataType: "json",
		beforeSend: function(data) {
			$('#generate_file_message_update').removeClass('hide');
			$('#update_file_button').addClass('hide');
		},
		success: function(data){
			console.log(data);
			var status = data.status;
			var msg = data.msg;
			if(status == "error"){
				get_alert_box("Error message",msg);
			}else if(status == "success"){
				get_alert_box("Success message",msg);
			}
			$('#generate_file_message_update').addClass('hide');
			$('#update_file_button').removeClass('hide');
		},
		error: function(xhr, status, exception) { 
			console.log("^_^ | "+status+" | "+exception+" | "+JSON.stringify(xhr)); 
		}
	});
}