function showSearchResult(){
	clearThenHide('result-msg-box');
	$('#tbldata_processing').removeClass("hidden");
	
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"admin/product/magazine/ajax-get-main-list/"+sid;
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
	
	search_product_category = new Array();
	$("input[name='search_product_category[]']").each(function()
	{
		if(this.checked){
			search_product_category.push(this.value);
		}	
	});

	var product_main_aid = $('#product_main_aid').val();

	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status, search_product_category:search_product_category, product_main_aid:product_main_aid }) , 
		function(data){
			if(null != data){
				draw_table_result(data,'tbldata_wrapper',true);
			}
		}
	);
}

function changeMagazineMain(){
	var magazine_main_aid = $("#magazine_main_aid").val();
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"admin/product/magazine-main/ajax-get-magazine-main-by-aid/"+sid;
	$.ajax({
		type: "POST",
		url: full_url,
		data: ({magazine_main_aid:magazine_main_aid }),
		dataType: "json",
		beforeSend: function(data) {
			$('#volumn').addClass("spinner");
			$('#issue').addClass("spinner");
			$('#desc').addClass("spinner");
		},
		success: function(data){
			var result = data.result;
			var magazine_main_detail = result.magazine_main_detail;
			var latest_magazine = result.latest_magazine;
			var latest_volumn = result.latest_volumn;
			var latest_issue = result.latest_issue;
			var latest_desc = result.latest_desc;

			$('#volumn').val(latest_volumn);
			$('#issue').val(latest_issue);
			$('#desc').val(latest_desc);

			$('#volumn').removeClass("spinner");
			$('#issue').removeClass("spinner");
			$('#desc').removeClass("spinner");
			auto_set_title();
		}
	});

}

function auto_set_title(){
	var latest_title = $("input[data-cid=title]").val();
	var volumn = $('#volumn').val();
	var issue = $('#issue').val();
	var desc = $('#desc').val();
	var force = $('#force').val();
	var magazine_main_title = "";

	if($('#magazine_main_title').length){
		magazine_main_title = $.trim($('#magazine_main_title').text());
	}else if($('#magazine_main_aid').length && $('#magazine_main_aid').find(":selected").val() != ""){
		magazine_main_title = $('#magazine_main_aid').find(":selected").text();
	}
	
	var title = "";

	if(magazine_main_title != ""){
		title = ""+magazine_main_title;
	}

	if(volumn != ""){
		title += " "+lang_volumn+" "+volumn;
	}
	if(issue != ""){
		title += " "+lang_issue+" "+issue;
	}
	if(desc != ""){
		title += " "+desc;
	}

	if(force || latest_title == ""){
		$("input[data-cid=title]").val(title);
	}

}