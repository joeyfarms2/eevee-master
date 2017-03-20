function update_basket_badge(){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-get-basket-badge/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({sid:sid}),
		dataType: "json",
		beforeSend: function(data) {

		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					$("#basket_badge").html(data.result);
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function show_basket_modal(){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-get-basket/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({sid:sid}),
		dataType: "json",
		beforeSend: function(data) {
			$('#result-msg-box').addClass('hidden');
			$('#b_item_list').html('');
			$('#b_price').html('');

			$('#b_list_zone').addClass('hide');
			$('#b_button_zone').addClass('hide');
			$('#b_summary_zone').addClass('hide');

			$('#b_redeem_code').html('');			
		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					var result = data.result;
					if(result){
						var payment_type = result.payment_type;
						var payment_type_txt = result.payment_type_txt;
						var actual_classifier = result.actual_classifier;
						var actual_grand_total = result.actual_grand_total;
						var after_redeem_grand_total = result.after_redeem_grand_total;
						var redeem_actual_discount = result.redeem_actual_discount;
						var actual_total = result.actual_total;
						var unit_grand_total = result.unit_grand_total;
						var redeem_code = result.redeem_code;
						var redeem_object = result.redeem_object;
						var total_transport_fee = result.total_transport_fee;
						var item_list = result.item_list;
						var txt = '';

						if(!redeem_code){
							redeem_code = '';
						}

						$("#b_unit_total").html(unit_grand_total);
						$("#b_price").html(actual_classifier+" "+actual_grand_total);
						$("#b_payment_type").val(payment_type);

						$("#b_redeem_code").val(redeem_code);
						var redeem_status = '';
						var redeem_msg = '';
						if(redeem_object){
							redeem_status = redeem_object.status;
							redeem_msg = redeem_object.msg;
							// alert(redeem_status);
							eval(redeem_object.message);
							eval(redeem_object.js_code);
						}

						if(item_list){
							$.each(item_list, function(i,item){
								txt += '<div class="col-xs-6 col-sm-4 mb20">';
								// txt += '<img src="'+item.cover_image_small+'" title="'+item.title_label+'" />';
								txt += '<img src="'+item.cover_image_small+'" />';
								txt += '</div>';
							});

							$('#b_item_list').html(txt);
							$('#b_list_zone').removeClass('hide');							
							$('#b_button_zone').removeClass('hide');							
							$('#b_summary_zone').removeClass('hide');							
						}

						update_basket_badge();
						$('#basket_modal').modal({
							keyboard: false
						});
					}
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function show_basket_list(stype){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-get-basket/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({sid:sid}),
		dataType: "json",
		beforeSend: function(data) {
			$('#result-msg-box').addClass('hidden');
			$('#b_page_unit_total').html('');
			$('#b_page_price').html('');
			$('#b_page_item_list').html('');
			$('#b_redeem_code').html('');
		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					var result = data.result;
					if(result){
						var payment_type = result.payment_type;
						var payment_type_txt = result.payment_type_txt;
						var actual_classifier = result.actual_classifier;
						var actual_grand_total = result.actual_grand_total;
						var after_redeem_grand_total = result.after_redeem_grand_total;
						var redeem_actual_discount = result.redeem_actual_discount;
						var actual_total = result.actual_total;
						var unit_grand_total = result.unit_grand_total;
						var redeem_code = result.redeem_code;
						var redeem_object = result.redeem_object;
						var total_transport_fee = result.total_transport_fee;
						var item_list = result.item_list;
						var txt = '';
						var cls = '';

						if(!redeem_code){
							redeem_code = '';
						}

						$("#b_page_unit_total").html(unit_grand_total);
						$("#b_page_price").html(actual_classifier+" "+actual_grand_total);
						if(stype == "confirm"){
							$("#b_page_payment_type").html(payment_type_txt);
						}else{
							$("#b_page_payment_type").val(payment_type);
						}

						$("#b_redeem_code").val(redeem_code);
						var redeem_status = '';
						var redeem_msg = '';
						if(redeem_object){
							redeem_status = redeem_object.status;
							redeem_msg = redeem_object.msg;
							// alert(redeem_status);
							eval(redeem_object.message);
							eval(redeem_object.js_code);
						}

						txt += '';

						if(item_list){
							$.each(item_list, function(i,item){
								if(item.stock_status){
									cls = 'has-change';
								}else{
									cls = '';
								}
								txt += '<div class="row basket-item '+cls+'">';
								txt += '<div class="col-xs-12 col-sm-2">';
								txt += '<img src="'+item.cover_image_small+'"/>';
								txt += '</div>';
								txt += '<div class="col-xs-12 col-sm-5 col-lg-6">';
								txt += '<p class="form-control-static">';
								txt += item.parent_title+'<br />';
								txt += item.title_label+'';
								if(item.stock_status){
									txt += '<div class="out-of-stock">'+item.stock_status+'</div>';
								}
								txt += '</p>';
								txt += '</div>';
								txt += '<div class="col-xs-4 col-sm-2 col-lg-1">';
								txt += '<div class="form-group">';
								txt += '<input type="text" class="form-control" name="unit[]" id="unit" value="'+item.unit+'" data-ptypecid="'+item.product_type_cid+'" data-aid="'+item.aid+'" maxlength="2" onkeypress="isKeyNumber(event)" onchange="update_basket(this)" ';
								if(item.is_ebook == "1" || stype == "confirm"){
									txt += 'disabled ';
								}
								txt += '/>';
								txt += '</div>';
								txt += '</div>';
								txt += '<div class="col-xs-4 col-sm-2">';
								txt += '<p class="form-control-static a-right">'+actual_classifier+' : '+item.actual_total_per_item+'</p>';
								txt += '</div>';
								txt += '<div class="col-xs-4 col-sm-1">';
								if(stype == "confirm"){
									txt += '&nbsp;';
								}else{
									txt += '<p class="form-control-static"><span class="button iconMedium icon-trash" onclick="remove_basket(\''+item.product_type_cid+'\',\''+item.aid+'\')"></span></p>';							
								}
								txt += '</div>';
								txt += '</div>';
							});
							
							//Total
							txt += '<div class="row summary-item '+cls+'">';
							txt += '<div class="col-xs-12 col-sm-7 col-lg-8 a-right">';
							txt += 'Total : ';
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-2 col-lg-1 a-center">';
							txt += ''+unit_grand_total;
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-2 a-right">';
							txt += ''+actual_total;
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-1">';
							txt += '&nbsp;';							
							txt += '</div>';
							txt += '</div>';

							//Redeem
							txt += '<div class="row summary-item '+cls+'">';
							txt += '<div class="col-xs-12 col-sm-5 col-lg-7 a-right">';
							txt += '<p>Redeem : '+redeem_msg+'</p>';
							txt += '</div>';
							txt += '<div class="col-xs-12 col-sm-4 col-lg-2 a-right">';
							txt += '<input type="text" class="form-control required" id="b_redeem_code" name="b_redeem_code" value="'+redeem_code+'" placeholder="Redeem Code" onchange="add_redeem(this.value, \'page\')"';
							if(stype == "confirm"){
								txt += 'disabled ';
							}
							txt += ' />';
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-2 a-right">';
							txt += ''+redeem_actual_discount;
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-1">';
							txt += '&nbsp;';							
							txt += '</div>';
							txt += '</div>';

							//Transport
							txt += '<div class="row summary-item '+cls+'">';
							txt += '<div class="col-xs-12 col-sm-9 col-lg-9 a-right">';
							txt += 'Transport fee';
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-2 a-right">';
							txt += ''+total_transport_fee;
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-1">';
							txt += '&nbsp;';							
							txt += '</div>';
							txt += '</div>';

							//Grand total
							txt += '<div class="row grandtotal-item '+cls+'">';
							txt += '<div class="col-xs-12 col-sm-9 col-lg-9 a-right">';
							txt += 'Grand Total';
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-2 a-right">';
							txt += ''+actual_grand_total;
							txt += '</div>';
							txt += '<div class="col-xs-4 col-sm-1">';
							txt += '&nbsp;';							
							txt += '</div>';
							txt += '</div>';

							if(stype != "confirm"){
								txt += '<div class="col-md-5 col-md-offset-7 mt20 mb10 om-button fleft">';
								txt += '<a id="om_button_checkout" class="btn btn-block btn-lg btn-danger" onclick="basket_checkout();">';
								txt += '<i class="fa fa-check-square-o"></i>';
								txt += 'Check out';
								txt += '</a>&nbsp;';
								txt += '</div>';
							}


							$('#b_page_item_list').html(txt);
							$('#b_page_summary_zone').removeClass('hide');
							update_basket_badge();
						}
					}
				}else{
					txt = data.msg;
					$('#b_page_item_list').html(txt);
					$('#b_page_summary_zone').addClass('hide');
				}
			}
		}
	});
}

function show_product_option_modal(product_type_cid, parent_aid){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"ajax-get-product-option/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_cid:product_type_cid, parent_aid:parent_aid}),
		dataType: "json",
		beforeSend: function(data) {
			$('#result-msg-box').addClass('hidden');
			$('#cp_title').html('');
			$('#cp_cover_image').html('');
			$('#cp_digital').html('');
			$('#cp_paper').html('');
			$('#cp_price').html('-');

			$('#cp_digital').addClass('hide');
			$('#cp_paper').addClass('hide');
		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					var result = data.result;
					if(result){
						var product_detail = result.product_detail;
						if(product_detail){
							var digital_result = result.digital_result;
							var paper_result = result.paper_result;
							$('#cp_title').html(product_detail.title);
							$('#cp_cover_image').html('<img src="'+product_detail.cover_image_small+'" />');
							if(digital_result){
								txt = '<h4>Digital edition</h4>';
								txt += '<div class="col-xs-12">';
								$.each(digital_result, function(i,item){
									var disabled = "";
									var classname = "";
									if(item.status == "0"){
										disabled = "disabled";
										classname = "disabled";
									}
									txt += '<div class="radio digital-option">';
									txt += '<label class="'+classname+'">';
									txt += '<input type="radio" name="option" id="option_'+item.product_type_cid+'_'+item.copy_aid+'" value="'+item.copy_aid+'" onclick="draw_option_price_list(this)" data-pricetxt="'+item.price_txt+'" data-ptypecid="'+item.product_type_cid+'" '+disabled+' />'+ item.title_label;
									txt += '</label>';
									txt += '</div>';
								});
								txt += '</div>';
								$('#cp_digital').html(txt);
								$('#cp_digital').removeClass('hide');							
							}

							if(paper_result){
								txt = '<h4>Paper edition</h4>';
								txt += '<div class="col-xs-12">';
								$.each(paper_result, function(i,item){
									var disabled = "";
									var classname = "";
									if(item.status == "0"){
										disabled = "disabled";
										classname = "disabled";
									}
									txt += '<div class="radio paper-option">';
									txt += '<label class="'+classname+'">';
									txt += '<input type="radio" name="option" id="option_'+item.product_type_cid+'_'+item.copy_aid+'" value="'+item.copy_aid+'" onclick="draw_option_price_list(this)" data-pricetxt="'+item.price_txt+'" data-ptypecid="'+item.product_type_cid+'" '+disabled+' />'+ item.title_label;
									txt += '</label>';
									txt += '</div>';
									});
								txt += '</div>';
								$('#cp_paper').html(txt);
								$('#cp_paper').removeClass('hide');			
							}

							draw_option_price_list();
							$('#choose_product_modal').modal({
								keyboard: false
							});

						}else{
							get_alert_box('Error','Error occured');
						}
					}else{
						get_alert_box('Error','Error occured');
					}
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function draw_option_price_list(data){
	if(data){
		$('#cp_price').html($(data).attr("data-pricetxt"));	
	}
	if($("input[name='option']:checked").val() > 0){
		$('#om_button_add').attr("disabled", false);
	}else{
		$('#om_button_add').attr("disabled", true);
	}
}

function add_to_basket(unit){
	if($("input[name='option']:checked").val() > 0){
		var product_type_cid = $("input[name='option']:checked").attr("data-ptypecid");
		var copy_aid = $("input[name='option']:checked").val();
		ajax_add_to_basket(product_type_cid, copy_aid, unit);
		$('#choose_product_modal').modal('hide');
	}
}

function ajax_add_to_basket(product_type_cid, copy_aid, unit){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-add-basket/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_cid:product_type_cid, copy_aid:copy_aid, unit:unit}),
		dataType: "json",
		beforeSend: function(data) {
			$('#result-msg-box').addClass('hidden');
		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					show_basket_modal();
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function change_payment_type(payment_type, return_to){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-change-payment-type/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({payment_type:payment_type}),
		dataType: "json",
		beforeSend: function(data) {

		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					if(return_to == "modal"){
						show_basket_modal();
					}else if(return_to == "page"){
						show_basket_list();
					}else if(return_to == "point"){
						$("#point_payment_type").val(payment_type);
					}
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function add_redeem(redeem_code, return_to){
	// alert(redeem_code);
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-add-redeem/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({redeem_code:redeem_code}),
		dataType: "json",
		beforeSend: function(data) {

		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					if(return_to == "modal"){
						processRedirect('my-cart');
					}else if(return_to == "page"){
						show_basket_list();
					}
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function remove_basket(product_type_cid, copy_aid){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-remove-basket/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_cid:product_type_cid, copy_aid:copy_aid}),
		dataType: "json",
		beforeSend: function(data) {
			$('#result-msg-box').addClass('hidden');
		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					show_basket_list();
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function update_basket(data){
	var unit = data.value;
	var product_type_cid = $(data).attr("data-ptypecid");
	var copy_aid = $(data).attr("data-aid");
	if(!unit){
		data.value = 1;
		unit=1;
	}

	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"basket/ajax-update-basket/"+sid;
	var ajaxCall = $.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_cid:product_type_cid, copy_aid:copy_aid, unit:unit}),
		dataType: "json",
		beforeSend: function(data) {
			$('#result-msg-box').addClass('hidden');
		},
		success: function(data){
			if (null != data) {
				if(data.status == "success"){
					show_basket_list();
				}else{
					get_alert_box(data.status, data.msg);					
				}
			}
		}
	});
}

function refresh_basket(is_checkout){
	var all_item = [];
	$("input[name='unit[]']").each(function()
	{
		var unit = this.value;
		var product_type_cid = $(this).attr("data-ptypecid");
		var copy_aid = $(this).attr("data-aid");
		if(!unit){
			unit=0;
		}

		var item = {product_type_cid:product_type_cid, copy_aid:copy_aid, unit:unit};
		all_item[all_item.length] = item;

	});

	if(all_item){
		var sid = Math.floor(Math.random()*10000000000);
		var full_url = base_url+"basket/ajax-refresh-basket/"+sid;
		var ajaxCall = $.ajax({
			type: "POST",
			url: full_url,
			data: ({all_item:all_item}),
			dataType: "json",
			beforeSend: function(data) {
				$('#result-msg-box').addClass('hidden');
			},
			success: function(data){
				if (null != data) {
					if(data.status == "success"){
						show_basket_list();
						if(is_checkout == '1'){
							processRedirect('basket/confirm');
						}
					}else{
						get_alert_box(data.status, data.msg);					
					}
				}
			}
		});	

	}
}

function basket_checkout(){
	refresh_basket(1);
}

function processSubmit(){
	jQuery("#frm_basket").validate();
	if (jQuery("#frm_basket").valid())
	{
		jQuery("#frm_basket").submit();
	}
	else
	{
		return false;
	}
}

function processSubmitPaysbuy(){
	jQuery("#frm_basket").validate();
	if (jQuery("#frm_basket").valid())
	{
		jQuery("#frm_basket").attr("action",base_url+"basket/confirm/save-with-paysbuy");
		jQuery("#frm_basket").submit();
	}
	else
	{
		return false;
	}
}

function processSubmitPoint(){
	jQuery("#frm_basket").validate();
	if (jQuery("#frm_basket").valid())
	{
		jQuery("#frm_basket").attr("action",base_url+"basket/confirm/save-with-point");
		jQuery("#frm_basket").submit();
	}
	else
	{
		return false;
	}
}

function show_payment_option_modal(package_point_aid, price){
	$('#point_price').html(price);
	$('#package_point_aid').val(package_point_aid);
	$("#cpm_button_checkout").attr("href", base_url+"order/package-point/confirm/package-"+package_point_aid)
	$('#choose_payment_modal').modal({
		keyboard: false
	});
}


