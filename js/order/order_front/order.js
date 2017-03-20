function processSubmit(option){
	jQuery("#frm_order").validate();
	if (jQuery("#frm_order").valid())
	{
		jQuery('#save_option').val(option);
		jQuery("#frm_order").submit();
	}
	else
	{
		return false;
	}

}

function processDelete(aid, name){
	if(confirm("Confirm to delete '"+name+"' ?")){
		var sid = Math.floor(Math.random()*10000000000);	  
		var full_url = base_url+"admin/order/ajax-delete-one/"+sid;
		$.ajax({
			type: "POST",
			url: full_url,
			data: ({aid_selected:aid }),
			beforeSend: function(data) {
				$('#tbldata_processing').removeClass("hidden");
			},
			success: function(msg){
				if(msg != "success"){
					jQuery("#result-msg-box").removeClass().addClass("box box-error");
					jQuery("#result-msg-box").html(msg);
					return "";
				}else{
					jQuery("#result-msg-box").removeClass().addClass("box box-success");
					jQuery("#result-msg-box").html("'"+name+"'"+" has been deleted.");
					showSearchResult();
				}
			}
		});
	}
}
 
function processChangeStatus(aid, name, status){
	var txt = 'inactive';
	if(status == 1){
		txt = 'active';
	}
	if(confirm("Confirm to "+txt+" '"+name+"' ?")){
		var sid = Math.floor(Math.random()*10000000000);	  
		var full_url = base_url+"admin/order/ajax-set-status/"+sid;
		$.ajax({
			type: "POST",
			url: full_url,
			data: ({aid_selected:aid,status:status }),
			beforeSend: function(data) {
				$('#tbldata_processing').removeClass("hidden");
			},
			success: function(msg){
				if(msg != "success"){
					jQuery("#result-msg-box").removeClass().addClass("box box-error");
					jQuery("#result-msg-box").html(msg);
					return "";
				}else{
					if(status == 1){
						jQuery("#result-msg-box").removeClass().addClass("box box-success");
						jQuery("#result-msg-box").html("'"+name+"'"+" set to active.");
					}else{
						jQuery("#result-msg-box").removeClass().addClass("box box-success");
						jQuery("#result-msg-box").html("'"+name+"'"+" set to inactive.");
					}
					showSearchResult();
				}
			}
		});
	}
}

function showSearchResult(){
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/order/ajax-get-order-list/"+sid;
	// alert(full_url);
	var search_order_by = $('#search_order_by').val();	
	var page_selected = $('#page_selected').val();	
	var search_record_per_page = jQuery('select[name=search_record_per_page]').val();	
	var search_post_word = $('#search_post_word').val();	

	var created_date_from = $('#created_date_from').val();	
	var created_date_to = $('#created_date_to').val();	
	
	search_in = new Array();
	jQuery("input[name=search_in[]]").each(function()
	{
		if(this.checked){
			search_in.push(this.value);
		}	
	});
	
	search_status = new Array();
	jQuery("input[name=search_status[]]").each(function()
	{
		if(this.checked){
			search_status.push(this.value);
		}	
	});
	
	jQuery.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, search_in:search_in, search_status:search_status, created_date_from:created_date_from, created_date_to:created_date_to }) , 
		function(data){
			if (null != data) {
				draw_table_result(data,'tbldata_wrapper',true);
			}
		}
	);

}

function draw_order_list(order_main_aid,message){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/order/ajax-get-order-detail/"+sid+'/'+order_main_aid;
	var chk = true;
	var result = "";
	var txt = "";
	var no = 0;
	var readonly = "";
	jQuery("#notificationsLoader").removeClass('hidden');
	jQuery("#result-msg-box").html('');
	jQuery("#result-msg-box-sub").html('');
	jQuery("#result-msg-box").addClass('hidden');
	jQuery("#result-msg-box-sub").addClass('hidden');
	jQuery.getJSON(full_url,
		function(data){
			if (null != data && data) {
				if (parseInt(data.all_unit_total) > 0) {
					if (parseInt(data.all_unit_total) > 0) {
						result = data.order_detail_result;
						
						jQuery('#area_order_status').html(data.status_name);
						
						if(data.status > 1){
							readonly = 'readonly';
						}
						
						txt += '<div id="result-msg-box-sub" name="result-msg-box-sub" class="hidden" ></div>';
						txt += '<table cellspacing="0" cellpadding="0" border="0" id="tbldata" class="display">';
						txt += '<thead>';
						txt += '<tr>';
						txt += '<th class="w10 hcenter">No.</th>';
						txt += '<th class="w30 hcenter">&nbsp;</th>';
						txt += '<th class="hcenter">Product</th>';
						txt += '<th class="w50 hcenter">Price</th>';
						txt += '<th class="w30 hcenter">Unit</th>';
						txt += '<th class="w50 hcenter">Available</th>';
						txt += '<th class="w50 hcenter">Total</th>';
						txt += '<th class="w100 hcenter">Remark</th>';
						txt += '</tr>';
						txt += '</thead>';
						txt += '<tbody>';
						
						if(result){
							jQuery.each(result, function(i,product) {
								chk = false;
								no++;
								if(no%2 != 0){
									classname = "odd";
								}else{
									classname = "even";
								}
								txt += '<tr class="'+classname+'">';
								txt += '<td>'+no+'.</td>';
								txt += '<td><img src="'+product.product_img+'" class="cover-image" /></td>';
								txt += '<td>'+product.product_fullname+'</td>';
								txt += '<td class="hright">'+product.product_price_full+'</td>';
								txt += '<td class="hright">'+product.product_unit_show+'</td>';
								txt += '<td class="hright">';
								txt += '<input type="hidden" id="price_'+product.product_aid+'" name="price[]" value="'+product.product_price+'" >';
								txt += '<input type="hidden" id="unit_'+product.product_aid+'" name="unit[]" value="'+product.product_unit+'" >';
								txt += '<input class="half price '+readonly+'" type="text" id="unit_change_'+product.product_aid+'" name="unit_change[]" value="'+product.product_unit_change_show+'" onchange="calculateOrder(\''+order_main_aid+'\',0)" onkeypress="isKeyNumber(event);" '+readonly+' >';
								txt += '</td>';
								txt += '<td class="hright">'+product.product_price_total_change+'</td>';
								txt += '<td><textarea class="w90p h40 '+readonly+'" id="remark_'+product.product_aid+'" name="remark[]" onchange="calculateOrder(\''+order_main_aid+'\',0)" '+readonly+'>'+product.remark+'</textarea></td>';
								txt += '</tr>';
							});
							
							txt += '<tr class="lineup">';
							txt += '<td colspan="3">&nbsp;</td>';
							txt += '<td colspan="3" class="hright">Total</td>';
							txt += '<td class="hright">'+data.all_price_total_change_show+'</td>';
							txt += '<td colspan="">&nbsp;</td>';
							txt += '</tr>';

							var transport_type_change = data.transport_type_change;							
							txt += '<tr class="">';
							txt += '<td colspan="2">&nbsp;</td>';
							txt += '<td colspan="4" class="hright">Transport : ';
							if(readonly == "readonly"){
								txt += '<input type="hidden" name="transport_type_change" id="transport_type_change" value="'+transport_type_change+'" />';
								txt += '<span class="hbold">';
								if(transport_type_change == "ems"){ txt += 'EMS'; }
								else{ txt += 'Normal'; }
								txt += '</span>';
							}else{
								txt += '<input type="radio" name="transport_type_change" id="transport_type_change" value="normal" onclick="calculateOrder(\''+order_main_aid+'\',0)" ';
								if(transport_type_change == "normal"){ txt += 'checked'; }
								txt += '/>Normal ';
								txt += '<input type="radio" name="transport_type_change" id="transport_type_change" value="ems" onclick="calculateOrder(\''+order_main_aid+'\',0)" ';
								if(transport_type_change == "ems"){ txt += 'checked'; }
								txt += '/>EMS';
							}
							txt += '</td>';
							txt += '<td class="hright"><input class="half price '+readonly+'" type="text" id="transport_fee_change" name="transport_fee_change" value="'+data.transport_fee_change+'" onchange="calculateOrder(\''+order_main_aid+'\',0)" onkeypress="isKeyNumber(event);" '+readonly+' ></td>';
							txt += '<td class="">&nbsp;</td>';
							txt += '</tr>';

							txt += '<tr class="lineup linedown">';
							txt += '<td colspan="4">&nbsp;</td>';
							txt += '<td colspan="" class="hright"><div id="notificationsLoader" class="notifications"></div></td>';
							txt += '<td colspan="" class="hright">Grand Total</td>';
							txt += '<td class="hright hbold">'+data.all_price_summary_change_show+'</td>';
							txt += '<td colspan="">&nbsp;</td>';
							txt += '</tr>';
							
							txt += '<tr class="lineup linedown">';
							txt += '<td colspan="3">&nbsp;</td>';
							txt += '<td colspan="3" class="hright">Remark</td>';
							txt += '<td colspan="2" class="hright"><textarea class="full h40" id="remark_change" name="remark_change" onchange="calculateOrder(\''+order_main_aid+'\',0)">'+data.remark_change+'</textarea></td>';
							txt += '</tr>';
							
							txt += '<tr class="lineup linedown">';
							txt += '<td colspan="3">&nbsp;</td>';
							txt += '<td colspan="3" class="hright">Package code</td>';
							txt += '<td colspan="2" class="hright"><input class="full" type="text" id="package_code" name="package_code" value="'+data.package_code+'" onchange="calculateOrder(\''+order_main_aid+'\',0)" ></td>';
							txt += '</tr>';
							
							master_status_order = data.master_status_order;
							txt += '<tr class="lineup linedown">';
							txt += '<td colspan="3">&nbsp;</td>';
							txt += '<td colspan="3" class="hright">Status</td>';
							txt += '<td colspan="2" class="hright">';
							txt += '<select id="status" name="status" class="full" onchange="calculateOrder(\''+order_main_aid+'\',0)">';
							if(master_status_order){
								jQuery.each(master_status_order, function(i,item) {
									txt += '<option value="'+item.aid+'"';
									if(item.aid == data.status){
										txt += ' selected '
									}
									txt += '>';
									txt += item.name;
									txt += '</option>';
								});
							}
							txt += '</select>';
							txt += '</td>';
							txt += '</tr>';
							
							txt += '<tr class="lineup linedown">';
							txt += '<td colspan="3">&nbsp;</td>';
							txt += '<td colspan="5" class="hright">';
							txt += '<input type="button" class="btn btn-blue big" value="Save" onClick="calculateOrder(\''+order_main_aid+'\',0)"/>';
							
							if(data.status == '2' || data.status == '4' || data.status == '5' || data.status == '7' ){
								txt += '<input type="button" class="btn btn-green big clear-right" value="Send Email" onClick="sendEmail(\''+order_main_aid+'\')"/>';
							}
							txt += '</td>';
							txt += '</tr>';
							
							
							txt += '</tbody>';
							txt += '</table>';
							jQuery('#basketListItemsWrap').html(txt);
							set_tooltip();
						}
					}
				}
			}
			if(chk){
				jQuery('#basketListItemsWrap').html('<div class="box box-info">No order detail.</div>');
			}		
			jQuery("#processingLoader").addClass('hidden');
			jQuery("#notificationsLoader").addClass('hidden');
			if(message != ""){
				eval(message);
			}
		}
	);
}

function calculateOrder(order_main_aid,send_mail_option){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/order/ajax-recal-order-detail/"+sid+'/'+order_main_aid;
	// alert(full_url);
	
	unit = new Array();
	jQuery("input[name=unit[]]").each(function()
	{
		full_aid = this.id;
		aid = full_aid.replace('unit_','');
		unit.push(''+aid+','+this.value);
	});
	
	price = new Array();
	jQuery("input[name=price[]]").each(function()
	{
		price.push(this.value);
	});
	
	unit_change = new Array();
	jQuery("input[name=unit_change[]]").each(function()
	{
		unit_change.push(this.value);
	});
	
	remark = new Array();
	jQuery("textarea[name=remark[]]").each(function()
	{
		remark.push(this.value);
	});
	
	var transport_type_change = "";
	jQuery("input[name=transport_type_change]").each(function()
	{
		if(this.checked){
			transport_type_change = this.value;
		}	
	});
	
	var transport_fee_change = jQuery("#transport_fee_change").val();
	var remark_change = jQuery("#remark_change").val();
	var package_code = jQuery("#package_code").val();
	var status = jQuery("#status").val();
	
	jQuery.getJSON(full_url, ({ order_main_aid:order_main_aid, unit:unit, price:price, unit_change:unit_change, remark:remark, transport_type_change:transport_type_change, transport_fee_change:transport_fee_change, remark_change:remark_change, package_code:package_code, send_mail_option:send_mail_option ,status:status }) , 
		function(data){
			if (null != data) {
				var status = data.status;
				draw_order_list(order_main_aid,data.message);
			}
		}
	);
}

function sendEmail(order_main_aid){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/order/ajax-send-email-order/"+sid;
	// alert(full_url);
	
	jQuery.getJSON(full_url, ({ order_main_aid:order_main_aid }) , 
		function(data){
			if (null != data) {
				var status = data.status;
				eval(data.message);
				// draw_order_list(order_main_aid,data.message);
			}
		}
	);
}