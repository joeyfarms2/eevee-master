function drawReviewPoint(rate, max_rate){
	for (var i = 1; i <= max_rate; i++) {
		if(i <= rate){
			$('#rate-'+i).addClass("focus");
		}else{
			$('#rate-'+i).removeClass("focus");
		}
	};
	$('#point').val(rate);
}

function showReview(){
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"review/ajax-get-main-list/"+sid;
	// get_alert_box("Debug",full_url);

	var product_type_aid = $('#product_type_aid').val();
	var parent_aid = $('#parent_aid').val();
	var max_rate = $('#max_rate').val();

	// alert(product_type_aid);
	// alert(parent_aid);
	
	$.getJSON(full_url, ({ product_type_aid:product_type_aid,parent_aid:parent_aid }) , 
		function(data){
			if(null != data){
				if(data.status == "success"){
					$.each(data.result, function(i,item){
						// Find summary
						var is_admin = data.is_admin;
						var total_record = data.optional.total_record;
						var summary_txt = data.optional.total_record+' Review';
						var my_review = data.my_review;
						if(total_record > '1'){
							summary_txt += 's';
						}
						$("#review_summary_area").html(summary_txt);

						var txt = '';
						$.each(data.result, function(i,item){
							var point = item.point;
							var status = item.status;
							var cls = '';
							if(status == '0'){
								cls = 'inactive';
							}
							txt += '<div class="row mb30">';
							txt += '	<div class="col-lg-12 review-list '+cls+'">';
							txt += '<div class="avatar col-lg-1">'+item.avatar_mini+'</div>';
							txt += '	<div class="col-lg-11">';
							txt += '	<div class=" col-lg-12 mb10 row">';
							txt += '	<div class="col-lg-8 rname">';
							txt += item.user_info;
							txt += '	<span class="rating-small">';
							if(point >= '5'){
								txt += '	<span class="star focus"></span>';
							}else{
								txt += '	<span class="star"></span>';
							}
							if(point >= '4'){
								txt += '	<span class="star focus"></span>';
							}else{
								txt += '	<span class="star"></span>';
							}
							if(point >= '3'){
								txt += '	<span class="star focus"></span>';
							}else{
								txt += '	<span class="star"></span>';
							}
							if(point >= '2'){
								txt += '	<span class="star focus"></span>';
							}else{
								txt += '	<span class="star"></span>';
							}
							if(point >= '1'){
								txt += '	<span class="star focus"></span>';
							}else{
								txt += '	<span class="star"></span>';
							}
							txt += '	</span>';

							if(my_review.user_aid == item.user_aid){
								txt += '<a class="link" onclick="editReview(my_review,\''+max_rate+'\')">Edit</a>&nbsp;';
							}
							if(is_admin == '1'){
								if(item.status == '1'){
									txt += '<a class="link" onclick="changReviewStatus(\''+item.user_aid+'\', \'0\')">Hide</a>&nbsp;';
								}else{
								txt += '<a class="link" onclick="changReviewStatus(\''+item.user_aid+'\', \'1\')">Unhide</a>&nbsp;';
								}
							}

							txt += '</div>';
							txt += '	<div class="col-lg-4 a-right rdate">';
							txt += item.created_date_show;
							txt += '	</div>';
							txt += '	</div>';
							txt += '	<div class="col-lg-12">';
							txt += '	<div class="col-lg-12 row rdesc">';
							txt += item.description;
							txt += '	</div>';
							txt += '	</div>';
							txt += '	</div>';
							txt += '	</div>';
							txt += '</div>';
						});
						$("#review_list_area").html(txt);

						$('#description').val('');
						$('#point').val('0');
						drawReviewPoint(0, max_rate);
						if(my_review.user_aid > 0){
							$('#review_form_area').addClass('hide');
						}else{
							$('#review_form_area').removeClass('hide');
						}
					});
				}else if(data.status == "warning"){
					// Do noting
				}else{
					show_result_box(data);
				}

			}
		}
	);
}

function editReview(my_review, max_rate){
	var point = my_review.point;
	$('#description').val(my_review.description);
	// $('#point').val(point);
	$('#review_form_area').removeClass('hide');
	drawReviewPoint(point, max_rate);
}

function saveReview(){
	var product_type_aid = $('#product_type_aid').val();
	var parent_aid = $('#parent_aid').val();
	var description = $('#description').val();
	var point = $('#point').val();
	var max_rate = $('#max_rate').val();
	// if(description == ''){
	// 	alert("Please write review.");
	// 	$('#description').focus();
	// 	return "";
	// }
	if(point <= 0){
		alert("Please choose rate.");
		return "";
	}

	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"review/ajax-save-review/"+sid;
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_aid:product_type_aid, parent_aid:parent_aid, description:description, point:point }),
		dataType: "json",
		beforeSend: function(data) {
			$('#tbldata_processing').removeClass("hidden");
		},
		success: function(data){
			location.reload();
			// $('#review_form_area').addClass('hide');
			// $('#description').val('');
			// $('#point').val('0');
			// drawReviewPoint(0, max_rate);
			// show_result_box(data);
			// showReview();
		}
	});
}

function changReviewStatus(user_aid, status){
	var product_type_aid = $('#product_type_aid').val();
	var parent_aid = $('#parent_aid').val();

	// alert(product_type_aid);
	// alert(parent_aid);

	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"review/ajax-hide-review/"+sid;
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({product_type_aid:product_type_aid, parent_aid:parent_aid, user_aid:user_aid, status:status }),
		dataType: "json",
		beforeSend: function(data) {
			$('#tbldata_processing').removeClass("hidden");
		},
		success: function(data){
			location.reload();
		}
	});
}