function addToShelf(product_type_cid, copy_aid){
	// alert(copy_aid);
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"ajax-add-product-to-shelf/"+sid+"/"+product_type_cid+"/"+copy_aid;
	// alert(full_url);

	jQuery.getJSON(full_url, 
		function(data){
			if (null != data) {
				if(data.status=="error"){
					alert(data.msg);
					return"";
				}else if(data.status=="warning"){
					alert(data.msg);
					return"";
				}else if(data.status=="success"){
					location.reload();
				}
			}
		}
	);
}

function addToShelfVdo(product_type_cid, parent_aid){
	// alert(parent_aid);
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"ajax-add-vdo-to-shelf/"+sid+"/"+product_type_cid+"/"+parent_aid;
	// alert(full_url);

	jQuery.getJSON(full_url, 
		function(data){
			if (null != data) {
				if(data.status=="error"){
					alert(data.msg);
					return"";
				}else if(data.status=="warning"){
					alert(data.msg);
					return"";
				}else if(data.status=="success"){
					location.reload();
				}
			}
		}
	);
}

function reserveProduct(product_type_cid, copy_aid, type){
	// alert(copy_aid);
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"ajax-save-reserve-product/"+sid+"/"+product_type_cid+"/"+copy_aid+"/"+type;

	// alert(full_url);

	jQuery.getJSON(full_url, 
		function(data){
			if (null != data) {
				if(data.status=="error"){
					alert(data.msg);
					return"";
				}else if(data.status=="warning"){
					alert(data.msg);
					location.reload();
					return"";
				}else if(data.status=="success"){
					location.reload();
				}
			}
		}
	);
}

function cancelReserveProduct(product_type_cid, copy_aid, type){
	get_confirm_box('Confirmation?','Do you want to cancel this reservation?','confirmCancelReserveProduct(\''+product_type_cid+'\', \''+copy_aid+'\', \''+type+'\')');
}

function confirmCancelReserveProduct(product_type_cid, copy_aid, type){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"ajax-cancel-reserve-product/"+sid+"/"+product_type_cid+"/"+copy_aid+"/"+type;

	// alert(full_url);

	jQuery.getJSON(full_url, 
		function(data){
			if (null != data) {
				if(data.status=="error"){
					alert(data.msg);
					return"";
				}else if(data.status=="warning"){
					alert(data.msg);
					location.reload();
					return"";
				}else if(data.status=="success"){
					location.reload();
				}
			}
		}
	);	
}

function openIssue(copy_aid,browser_type,browser_name){
	// alert(copy_aid);
	processRedirect('product/show-product/'+copy_aid);
}
