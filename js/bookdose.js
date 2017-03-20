var isCtrl = false;
var const_has_reward_point = 0;
var const_reward_point = 0;
/****************************************************************/
function getCategoryByProductMainAid(){
	var sid = Math.floor(Math.random()*10000000000);
	if($("input[name='product_main_aid']").is(':radio')){
		var product_main_aid = $("input[name='product_main_aid']:checked").val()
	}else{
		var product_main_aid = $("#product_main_aid").val()
	}
	var full_url = base_url+"admin/product-category/ajax-get-category-by-product-main/"+sid+"/"+product_main_aid;
	// alert(full_url);

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
						txt += '<label class="checkbox-inline"><input type="checkbox" id="category_'+item.aid+'" name="category[]" value="'+item.aid+'" class="required" ';
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

function getProductFieldByProductMainAid(){	
	var sid = Math.floor(Math.random()*10000000000);
	if($("input[name='product_main_aid']").is(':radio')){
		var product_main_aid = $("input[name='product_main_aid']:checked").val()
	}else{
		var product_main_aid = $("#product_main_aid").val()
	}
	// alert(product_main_aid);
	// var product_main_aid = 1;
	if($("input[name='product_type_aid']").is(':radio')){
		var product_type_aid = $("input[name='product_type_aid']:checked").val()
	}else{
		var product_type_aid = $("#product_type_aid").val()
	}
	// alert(product_type_aid);
	// var product_type_aid = 1;
	var parent_aid = $("#aid").val()
	var full_url = base_url+"admin/product-main-field/ajax-get-field-list-by-product-main-aid/"+sid;
	// alert(full_url);

	$.getJSON(full_url, ({ parent_aid:parent_aid, product_type_aid:product_type_aid, product_main_aid:product_main_aid }) ,
		function(data){
			if (null != data) {
				if(data.status=="error"){
					alert(data.msg);
				}else if(data.status=="warning"){
					//Do not found field list
					$('#marc_field_list_fieldset').addClass('hidden');
				}else if(data.status=="success"){
					var txt = '';
					var is_required = "";
					var required_class = "";
					var input_type = "";
					var product_topic_main_cid = "";
					var product_field_result = data.product_field_result;
					var val = "";
					var label_name = "";
					var description = "";
					$.each(data.result, function(i,item){
						txt += '';
						cid = item.cid;
						product_topic_main_cid = item.product_topic_main_cid;
						is_required = item.is_required;
						field_aid = item.aid;
						if(is_required == '1'){
							required_class = "required";
						}else{
							required_class = "";
						}
						input_type = item.input_type;
						// alert(input_type);
						// if(input_type != "textbox" && input_type != "textarea"){
						// 	input_type = "textbox";
						// }

						// if(product_topic_main_cid != ''){
							// input_type = "textbox_topic";
						// }
						
						val = "";
						$.each(data.product_field_result, function(i,field){
							if(field.product_main_field_aid == field_aid){
								val = field.field_data;
							}
						});
						
						label_name = "";
						if(item.tag != ""){
							label_name += item.tag;
							if(item.subfield_cd != ""){
								label_name += " "+item.subfield_cd+" ";
							}
							label_name += " : ";
						}
						label_name += item.name;
						
						txt += '<div class="form-group">';
						txt += '<label class="col-sm-2 control-label '+required_class+'" for="field_'+field_aid+'">'+label_name+'</label>';
						txt += '<div class="col-sm-8">';
						if(input_type == "textarea"){
							txt += '<textarea class="form-control '+required_class+'" id="field_'+field_aid+'" name="field_'+field_aid+'" data-cid="'+cid+'" ';
							if(cid == 'total_page'){
								txt += ' onblur="updateParentRewardPoint(this.value)"';
							}
							txt += '>'+val+'</textarea>';
						}else if(input_type == "textbox_topic"){
							txt += '<div class="">';
							txt += '<input type="text" id="field_'+field_aid+'" name="field_'+field_aid+'" data-cid="'+cid+'" value="'+val+'" class="form-control '+required_class+'" />';
							txt += '<span id="buttonTag_'+field_aid+'" name="buttonTag_'+field_aid+'" class="customfile-button" aria-hidden="true" onclick="openTopicDialog(\''+product_topic_main_cid+'\',\'field_'+field_aid+'\');">Browse</span>';
							txt += '</div>';
						}else{
							txt += '<input type="text" id="field_'+field_aid+'" name="field_'+field_aid+'" data-cid="'+cid+'" class="form-control '+required_class+'" value="'+val+'"';
							if(cid == 'total_page'){
								txt += ' onblur="updateParentRewardPoint(this.value)"';
							}
							txt += ' />';
						}

						description = item.description;
						if(description && description != ""){
							txt += '<p class="help-block">'+description+'</p>';
						}
						txt += '</div>';
						txt += '</div>';
						
						

					});
					
					$('#marc_field_list_area').html(txt);
					$('#marc_field_list_fieldset').removeClass('hidden');
					
					$(".customfile-button").bind("click", function(event) {
						$("#da-dialog-form-topic-div").dialog("option", {modal: true}).dialog("open");
						event.preventDefault();
					});

					if($("#magazine_main_aid").length){
						auto_set_title(false);
					}
								
				}
			}
		}
	);
}

function updateParentRewardPoint(page){
	// alert(page);
	// alert(const_has_reward_point);
	// alert(const_reward_point);
	if(const_has_reward_point = '1'){
		var point = Math.ceil(page/const_reward_point);
		// alert($('#reward_point'));
		if($('#reward_point')){
			$('#reward_point').val(point);
		}
	}
}

function getProductTypeMinorByMainProduct(){
	var sid = Math.floor(Math.random()*10000000000);	  
	if($("input[name='product_main_aid']").is(':radio')){
		var product_main_aid = $("input[name='product_main_aid']:checked").val();
	}else{
		var product_main_aid = $("#product_main_aid").val();
	}

	var is_license = $("#is_license:checked").val();

	var full_url = base_url+"admin/product-type-minor/ajax-get-product-type-minor-by-product-main/"+sid+"/"+product_main_aid;
	// alert(full_url);

	jQuery.getJSON(full_url, ({ is_license:is_license }) ,
		function(data){
			if (null != data) {
				if(data.status=="error"){
					alert(data.msg);
				}else if(data.status=="warning"){
					var txt = '<label class="control-label">';
					txt += data.msg;
					txt += '</label>';
					$('#product_type_minor_area').html(txt);
				
				}else if(data.status=="success"){
					var txt = '';
					var product_type_minor_aid = jQuery('#product_type_minor_aid_old').val();
					// txt += '<ul class="da-form-list inline">';
					$.each(data.result, function(i,item){
						txt += '<label class="radio-inline"><input type="radio" id="product_type_minor_aid_'+item.aid+'" name="product_type_minor_aid" value="'+item.aid+'"';
						if(product_type_minor_aid == item.aid || i==0){
							txt += ' checked';
						}
						txt += '/>'+item.name+'</label>';
					});
					// txt += '</ul>';
					$('#product_type_minor_area').html(txt);
				}
			}
		}
	);
}

function openTagDialog(){
	$("#tree-tag").html('<div class="spaceLeft loading">Loading..</div>');
	var sid = Math.floor(Math.random()*10000000000);	  
	$("#tree-tag").tree({
		url: '/admin/product-main-field/ajax-get-tag-tree/'+sid,
		loadFilter: function(rows){
			return convert(rows);
		},
		method: 'get',
		animate: true,
		dnd: false,
		onLoadSuccess: function(node , data){
			// $("#tree-tag").addClass("hidden");
			$(this).tree('collapseAll');
			var tag = $("#tag").val();
			var subfield_cd = $("#subfield_cd").val();
			var topic_id_selected = "";
			if(tag != ""){
				topic_id_selected += tag;
			}
			if(tag != "" && subfield_cd != ""){
				topic_id_selected = topic_id_selected+"."+subfield_cd;
			}
			// var topic_id_selected = $("#subfield_cd").val();
			if(topic_id_selected != ""){
				// alert(topic_id_selected);
				var node = $('#tree-tag').tree('find', topic_id_selected);
				// alert(node);
				if(node){
					$(this).tree('expandTo', node.target);
					$(this).tree('select', node.target);
				}
			}
		}
	});

	$('#modal-header').html("MARC Field Selector");
	// $('#modal-msg').html(msg);
	$('#modal-button').html('<button class="btn btn-success" type="button" onclick="selectTag()">Select</button><button data-dismiss="modal" class="btn btn-default" type="button">Close</button>');
	$('#da-dialog-form-tag-div').modal({
		backdrop: 'static',
		keyboard: false
	})



	

	// $("#da-dialog-form-tag-div").dialog({
	// 	autoOpen: false, 
	// 	title: "MARC Field Selector", 
	// 	modal: true, 
	// 	width: "60%", 
	// 	height: "400", 
	// 	buttons: {
	// 		"Select": function() { 
	// 			var node = $('#tree-tag').tree('getSelected');
	// 			var children = $('#tree-tag').tree('getChildren',node.target);
	// 			if (node && children == ""){
	// 				var node_id = node.id;
	// 				var res = node_id.split(".");
	// 				var tag_id = res[0];
	// 				var subfield_cd = res[1];
					
	// 				var name = $("#name").val();
	// 				$("#tag").val(tag_id);
	// 				$("#subfield_cd").val(subfield_cd);
	// 				if(name == ""){
	// 					$("#name").val(node.text);
	// 				}
	// 				$(this).dialog("close"); 
					
	// 			}else{
	// 				get_alert_box("Warning", "Please select sub field.");
	// 				return "";
	// 			}

	// 		},
	// 		"Cancel": function() { 
	// 			$(this).dialog("close"); 
	// 		}
	// 	}
	// });
}

function selectTag(){
	var children = "";
	var node = $('#tree-tag').tree('getSelected');
	if(node){
		children = $('#tree-tag').tree('getChildren',node.target);
	}

	if (node && children == ""){
		var node_id = node.id;
		var res = node_id.split(".");
		var tag_id = res[0];
		var subfield_cd = res[1];
		
		var name = $("#name").val();
		$("#tag").val(tag_id);
		$("#subfield_cd").val(subfield_cd);
		if(name == ""){
			$("#name").val(node.text);
		}
		$('#da-dialog-form-tag-div').modal('hide');
	}else{
		alert("Please select sub field.");
		return "";
	}
}

function getShelfBadge(){
	if($('#shelf_badge').length){
		var sid = Math.floor(Math.random()*10000000000);	  
		var full_url = base_url+"my-bookshelf/ajax-get-badge-my-bookshelf/"+sid;
		if($('#shelf_badge')){
			jQuery.getJSON(full_url, 
				function(data){
					if (null != data) {
						if(data.status=="error"){
							alert(data.msg);
						}else if(data.status=="warning"){
							// $('#category_area').html(data.msg);
						}else if(data.status=="success"){
							var txt = data.result;
							if(txt > 0){
								$('#shelf_badge').html(txt);
								jQuery("#shelf_badge").removeClass('blind');
							}else{
								$('#shelf_badge').html('');
								jQuery("#shelf_badge").addClass("blind");
							}
						}
					}
				}
			);
		}
	}
}

function getCartBadge(){
	if($('#cart_badge').length){
		var sid = Math.floor(Math.random()*10000000000);	  
		var full_url = base_url+"basket/ajax-get-badge/"+sid;
		if($('#cart_badge')){
			jQuery.getJSON(full_url, 
				function(data){
					if (null != data) {
						if(data.status=="error"){
							alert(data.msg);
						}else if(data.status=="warning"){
							// $('#category_area').html(data.msg);
						}else if(data.status=="success"){
							var txt = data.result;
							if(txt > 0){
								$('#cart_badge').html(txt);
								jQuery("#cart_badge").removeClass('blind');
							}else{
								$('#cart_badge').html('');
								jQuery("#cart_badge").addClass("blind");
							}
						}
					}
				}
			);
		}
	}
}

function focus_search(){
	var keyword = jQuery("#keyword").val();
	if(keyword == "" || keyword == "ใส่คำค้นหาแล้วกด Enter"){
		jQuery("#keyword").val('');
	}
}

function blur_search(){
	var keyword = jQuery("#keyword").val();
	if(keyword == "" || keyword == "ใส่คำค้นหาแล้วกด Enter"){
		jQuery("#keyword").val('ใส่คำค้นหาแล้วกด Enter');
	}
}

function search(){
	var keyword = jQuery("#keyword").val();
	
	var search_option = "";
	$("input[name='search_option']").each(function()
	{
		if(this.checked){
			search_option = this.value;
		}	
	});
	
	keyword = keyword.replace(/[^a-zA-Z0-9ก-ฮ๐-๙\-.ๅุูึๆไใำะัํี๊ฯโเ้็่๋าแิฺื์\s]/g, '');
	keyword = $.trim(keyword);
	if(keyword != "" && keyword != "ใส่คำค้นหาแล้วกด Enter"){
		processRedirect('search/'+keyword+'/option-'+search_option);
	}
}

function search_advance(page){
	var keyword = jQuery("#keyword").val();
	if(page == '' || page <= 0){
		page = 1;
	}
	
	var search_option = "or";
	$("input[name='search_option']").each(function()
	{
		if(this.checked){
			search_option = this.value;
		}	
	});
	
	$('#page_selected').val(page);
	
	keyword = keyword.replace(/[^a-zA-Z0-9ก-ฮ๐-๙\-.ๅุูึๆไใำะัํี๊ฯโเ้็่๋าแิฺื์\s]/g, '');
	keyword = $.trim(keyword);
	if(keyword != ""){
		$("#frm_search").attr('action', '/search/'+keyword+'/option-'+search_option);
		$("#frm_search").submit();
	}
}



	



function setReadOnlyObj(val, val_obj, obj_name){
	if(val == val_obj){
		$("input[id="+obj_name+"]").attr('disabled', false);
		$("input[id="+obj_name+"]").attr('readonly', false);
		$("input[id="+obj_name+"]").removeClass('readonly');
	}else{
		$("input[id="+obj_name+"]").val("");
		$("input[id="+obj_name+"]").attr('disabled', true);
		$("input[id="+obj_name+"]").attr('readonly', true);
		$("input[id="+obj_name+"]").addClass('readonly');
	}
}

function convert(rows){
    function exists(rows, parentId){
        for(var i=0; i<rows.length; i++){
            if (rows[i].id == parentId) return true;
        }
        return false;
    }
    
    var nodes = [];
    // get the top level nodes
    for(var i=0; i<rows.length; i++){
        var row = rows[i];
        if (!exists(rows, row.parentId)){
            nodes.push({
                id:row.id,
                text:row.name,
                state:row.state
            });
        }
    }
    
    var toDo = [];
    for(var i=0; i<nodes.length; i++){
        toDo.push(nodes[i]);
    }
    while(toDo.length){
        var node = toDo.shift();    // the parent node
        // get the children nodes
        for(var i=0; i<rows.length; i++){
            var row = rows[i];
            if (row.parentId == node.id){
                var child = {id:row.id,text:row.name,state:row.state};
                if (node.children){
                    node.children.push(child);
                } else {
                    node.children = [child];
                }
                toDo.push(child);
            }
        }
    }
    return nodes;
}

function openTopicDialog(topic_main_cid,field_id){
	$("#tree-topic").html('');
	var sid = Math.floor(Math.random()*10000000000);	  
	$("#tree-topic").tree({
		url: '/admin/product-topic/ajax-get-topic-tree-by-cid/'+sid+'/'+topic_main_cid,
		loadFilter: function(rows){
			return convert(rows);
		},
		method: 'get',
		animate: true,
		dnd: false,
		onLoadSuccess: function(node , data){
			if(data != ""){
				$(this).tree('collapseAll');
			}else{
				$("#tree-topic").html('<div class="spaceLeft">Topic not found.</div>');
			}
		}
	});

	$("#da-dialog-form-topic-div").dialog({
		autoOpen: false, 
		title: "Choose topic", 
		modal: true, 
		width: "60%", 
		height: "400", 
		buttons: {
			"Select": function() { 
				var node = $('#tree-topic').tree('getSelected');
				var children = $('#tree-topic').tree('getChildren',node.target);
				
				if (node && children == ""){
					var targetId = 0;
					targetId = parseInt(node.id);
					targetText = node.text;
					
					var child = $('#tree-topic').tree('getChildren', node.target);
					var parent = $('#tree-topic').tree('getParent', node.target);
					
					while( parent ){
						targetText = parent.text+"--"+targetText;
						parent = $('#tree-topic').tree('getParent', parent.target);
					}
					$("#"+field_id).val(targetText);
					$(this).dialog("close"); 
					
				}else{
					get_alert_box("Warning", "Please select topic.");
					return "";
				}

			},
			"Cancel": function() { 
				$(this).dialog("close"); 
			}
		}
	});
}

function toggleMarc(){
	var is_hide = $("#marc-content").hasClass("hide");
	if(is_hide){
		$("#marc-content").removeClass("hide");
		$("#marc-arrow").removeClass("fa-plus").addClass("fa-minus");
	}else{
		$("#marc-content").addClass("hide");
		$("#marc-arrow").removeClass("fa-minus").addClass("fa-plus");
	}
}