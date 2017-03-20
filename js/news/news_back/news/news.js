function getCategoryByNewsMainAid(){
	var sid = Math.floor(Math.random()*10000000000);
	if($("input[name='news_main_aid']").is(':radio')){
		var news_main_aid = $("input[name='news_main_aid']:checked").val()
	}else{
		var news_main_aid = $("#news_main_aid").val()
	}
	var full_url = base_url+"admin/news-category/ajax-get-category-by-news-main/"+sid+'/'+news_main_aid;

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
	var full_url = base_url+"admin/news/ajax-get-main-list/"+sid;
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
	
	search_news_category = new Array();
	$("input[name='search_news_category[]']").each(function()
	{
		if(this.checked){
			search_news_category.push(this.value);
		}	
	});
	
	$.getJSON(full_url, ({ search_record_per_page:search_record_per_page,search_order_by:search_order_by,page_selected:page_selected,search_post_word:search_post_word, created_date_from:created_date_from, created_date_to:created_date_to, search_in:search_in, search_status:search_status, search_news_category:search_news_category }) , 
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

	$("#search_news_category_all").attr('checked', true);
	$("input[name='search_news_category[]']").each(function()
	{
		this.checked = true;	
	});	
}

function checkToggleAdvanceSearch(){
	var search_post_word = $('#search_post_word').val();
	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();
	var search_status_all = $("#search_status_all").is(':checked');
	if ($("#search_news_category_all").length > 0) {
		var search_news_category_all = $("#search_news_category_all").is(':checked');
	}
	else {
		var search_news_category_all = true;
	}
	if( search_post_word == "" && search_post_word == "" && created_date_from == "" && created_date_to == "" && search_status_all && search_news_category_all){
		set_toggle_advance_search('hide');
	}else{
		set_toggle_advance_search('show');
	}
}

function processSubmit(option){
	$("#frm_news").validate();
	if ($("#frm_news").valid())
	{
		$('#save_option').val(option);
		// $('#description').val(nicEditors.findEditor('description').getContent());
		// alert($('#description').tinymce().getContent());
		$('#description').val( tinyMCE.get('description_1').getContent() );
		if (option == '2') {
			if ($('#command').val() == '_insert') {
				$('#status').val('0');
			}

			var sid = Math.floor(Math.random()*10000000000);
			var serializedData = $("#frm_news").serialize();
			var full_url = base_url + "admin/news/ajax-save-preview/" + sid;
			$.ajax({
			  type: "POST",
			  url: full_url,
			  dataType: 'json',
			  data: serializedData,
			  beforeSend: function(data) {
			      $('#btn_preview').addClass('disabled').html('<i class="fa fa-search prs"></i> Saving...');
			  },
			  success: function(data) {
			      $('#btn_preview').removeClass('disabled').html('<i class="fa fa-search prs"></i> Preview');
			      if (data.status=='success') {
			          $('#aid').val(data.aid);
			          $('#command').val('_update');
			          window.open(data.preview_url, 'Preview News');
			          return false;
			      }
			      else {
			      	window.location = data.redirect_url;
			      	return false;
			      }
			  }
			});
		}
		else if (option == '1') {
			$('#status').val('0');
			$("#frm_news").submit();
		}
		else {
			$('#status').val('1');
			$("#frm_news").submit();
		}
	}
	else
	{
		$('input.error,select.error,textarea.error').first().focus();
		$('html,body').animate({scrollTop: $('.error:first').offset().top-200 });
		return false;
	}
}

function processViewAllComments(aid) {
	var sid = Math.floor(Math.random()*10000000000);
	window.location = base_url + 'admin/news-comment?aid=' + aid + '&sid=' +sid;
}