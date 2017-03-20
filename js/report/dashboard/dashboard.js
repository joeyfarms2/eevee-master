function showSearchResult(){
	var month_th_name=["January","February","March","April","May","June","July","August","September","October","November","December"];
	
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	
	var created_date_from_obj = new Date(created_date_from);
	var created_date_to_obj = new Date(created_date_to);

	if(created_date_to_obj != "" && created_date_from_obj > created_date_to_obj){
		alert("Report date to must greater than report date from.");
		return "";
	}
	
	var created_date_from_txt = '';
	var created_date_to_txt = '';

	if(created_date_from_obj.getDate()){
		created_date_from_txt = created_date_from_obj.getDate()+' '+month_th_name[created_date_from_obj.getMonth()]+' '+ (created_date_from_obj.getFullYear());
	}
	
	if(created_date_to_obj.getDate()){
		created_date_to_txt = created_date_to_obj.getDate()+' '+month_th_name[created_date_to_obj.getMonth()]+' '+ (created_date_to_obj.getFullYear());
	}
	
	var title = "";
	if(created_date_from_txt == "" && created_date_to_txt == ""){
		title += "from the beginning";
	}else {
		if(created_date_from_txt != ""){
			title += "from "+created_date_from_txt+" ";
		}else{
			title += "from the beginning";
		}
		if(created_date_to_txt != ""){
			title += "to "+created_date_to_txt;
		}else{
			title += "to present";
		}
		title += '.';
	}
	jQuery('#report_title_area').html(title);

	call_report();
	if(created_date_from != "" || created_date_to != ""){
		call_report_more();
	}
}

function get_summary(){
	var sid = Math.floor(Math.random()*10000000000);
	var full_url = base_url+"admin/dashboard/ajax-get-data-summary/"+sid;
	// get_alert_box("Debug",full_url);

	var created_date_from = $('#created_date_from').val();
	var created_date_to = $('#created_date_to').val();	
	if(created_date_from != "" && created_date_to != ""){
		if(created_date_from > created_date_to){
			get_alert_box("Warning message","End date must greater than or equal to start date.");
			$('#tbldata_processing').addClass("hidden");
			return "";
		}
	}
	
	$.getJSON(full_url, ({ created_date_from:created_date_from, created_date_to:created_date_to }) , 
		function(data){
			// alert(data);
			if(null != data){
				var result = data.result;
				if(result.user_total && $('#user_total_area')) showCount('user_total_area', result.user_total); else showCount('user_total_area', 0);
				if(result.event_total && $('#event_total_area')) showCount('event_total_area', result.event_total); else showCount('event_total_area', 0);
				if(result.book_total && $('#book_total_area')) showCount('book_total_area', result.book_total); else showCount('book_total_area', 0);
				if(result.vdo_total && $('#vdo_total_area')) showCount('vdo_total_area', result.vdo_total); else showCount('vdo_total_area', 0);
				if(result.shelf_history_total && $('#shelf_history_total_area')) showCount('shelf_history_total_area', result.shelf_history_total); else showCount('shelf_history_total_area', 0);
			}
		}		
	);	
}

function get_product_book_by_product_main(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-product-book-by-product-main/"+sid+"";
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#product_book_by_product_main_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'product_book_by_product_main_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					xAxis: {
						categories: data.task_title,
						labels: {
							align: 'center',
							formatter: function() {
								return (this.value).toString().substring(0,20);
							}
						}
					},
					yAxis: [{ // Primary yAxis
						min: 0,
						minRange: 5,
						labels: {
							formatter: function() {
								return this.value;
							}
						},
						title: {
							text: ''
						}
					}],					
					legend: {
						enabled: true
					},
					series: data.data_chart
				});
			}else{
				jQuery('#product_book_by_product_main_chart').html('No data available.');
			}
			
		}
	});
}

function get_product_book_and_copy_by_product_main(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-product-book-and-copy-by-product-main/"+sid+"";
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#product_book_by_product_main_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'product_book_by_product_main_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					xAxis: {
						categories: data.task_title,
						labels: {
							align: 'center',
							formatter: function() {
								return (this.value).toString().substring(0,20);
							}
						}
					},
					yAxis: [{ // Primary yAxis
						min: 0,
						minRange: 5,
						labels: {
							formatter: function() {
								return this.value;
							}
						},
						title: {
							text: ''
						}
					}],					
					legend: {
						enabled: true
					},
					series: data.data_chart
				});
			}else{
				jQuery('#product_book_by_product_main_chart').html('No data available.');
			}
			
		}
	});
}

function get_user_login_by_device(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-user-login-by-device/"+sid+"";
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#user_login_by_device_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'user_login_by_device_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					legend: {
						enabled: true
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							}
						}
					},
					series: data.data_chart
				});
			}else{
				jQuery('#user_login_by_device_chart').html('No data available.');
			}
			
		}
	});
}

function get_user_registration_by_device(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-user-registration-by-device/"+sid+"";
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#user_registration_by_device_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'user_registration_by_device_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					legend: {
						enabled: true
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							}
						}
					},
					series: data.data_chart
				});
			}else{
				jQuery('#user_registration_by_device_chart').html('No data available.');
			}
			
		}
	});
}

function get_product_book_by_category(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-product-book-by-category/"+sid+"";
	var c_category_book_product_main_aid = $("#c_category_book_product_main_aid").val();
	$('#c_category_book_'+c_category_book_product_main_aid).addClass('active');
	// alert(c_category_book_product_main_aid);
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { c_category_book_product_main_aid:c_category_book_product_main_aid, created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#product_book_by_category_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'product_book_by_category_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					xAxis: {
						lineColor: '#FFFFFF',
						categories: data.task_title,
						labels: {
							rotation: -45,
							align: 'right',
							formatter: function() {
								return (this.value).toString().substring(0,20);
							}
						}
					},
					yAxis: [{ // Primary yAxis
						gridLineColor: '#FFFFFF',
						min: 0,
						minRange: 5,
						labels: {
							formatter: function() {
								return this.value;
							}
						},
						title: {
							text: ''
						}
					}],					
					legend: {
						enabled: true
					},
					series: data.data_chart
				});
			}else{
				jQuery('#product_book_by_category_chart').html('No data available.');
			}
			
		}
	});
}

function get_product_vdo_by_category(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-product-vdo-by-category/"+sid+"";
	var c_category_vdo_product_main_aid = $("#c_category_vdo_product_main_aid").val();
	$('#c_category_vdo_'+c_category_vdo_product_main_aid).addClass('active');
	// alert(c_category_vdo_product_main_aid);
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { c_category_vdo_product_main_aid:c_category_vdo_product_main_aid, created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#product_vdo_by_category_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'product_vdo_by_category_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					xAxis: {
						lineColor: '#FFFFFF',
						categories: data.task_title,
						labels: {
							rotation: -45,
							align: 'right',
							formatter: function() {
								return (this.value).toString().substring(0,20);
							}
						}
					},
					yAxis: [{ // Primary yAxis
						gridLineColor: '#FFFFFF',
						min: 0,
						minRange: 5,
						labels: {
							formatter: function() {
								return this.value;
							}
						},
						title: {
							text: ''
						}
					}],					
					legend: {
						enabled: false
					},
					series: data.data_chart
				});
			}else{
				jQuery('#product_vdo_by_category_chart').html('No data available.');
			}
			
		}
	});
}

function get_product_book_popular_download(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-product-book-popular-download/"+sid+"";
	var c_popular_book_product_main_aid = $("#c_popular_book_product_main_aid").val();
	$('#c_popular_book_'+c_popular_book_product_main_aid).addClass('active');
	// alert(c_popolar_product_main_aid);
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { c_popular_book_product_main_aid:c_popular_book_product_main_aid, created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#product_book_popular_download_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'product_book_popular_download_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					xAxis: {
						categories: data.task_title,
						labels: {
							rotation: -45,
							align: 'right',
							formatter: function() {
								return (this.value).toString().substring(0,20);
							}
						}
					},
					yAxis: [{ // Primary yAxis
						min: 0,
						minRange: 5,
						labels: {
							formatter: function() {
								return this.value;
							}
						},
						title: {
							text: ''
						}
					}],					
					legend: {
						enabled: false
					},
					series: data.data_chart
				});
			}else{
				jQuery('#product_book_popular_download_chart').html('No data available.');
			}
			
		}
	});	
}

function get_search_popular(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-search-popular/"+sid+"";
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#search_popular_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'search_popular_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					xAxis: {
						categories: data.task_title,
						labels: {
							rotation: -45,
							align: 'right',
							formatter: function() {
								return (this.value).toString().substring(0,20);
							}
						}
					},
					yAxis: [{ // Primary yAxis
						min: 0,
						minRange: 5,
						labels: {
							formatter: function() {
								return this.value;
							}
						},
						title: {
							text: ''
						}
					}],					
					legend: {
						enabled: false
					},
					series: data.data_chart
				});
			}else{
				jQuery('#search_popular_chart').html('No data available.');
			}
			
		}
	});	
}

function get_download_by_product_main(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-download-by-product-main/"+sid+"";
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#download_by_product_main_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					chart: {
						renderTo: 'download_by_product_main_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					legend: {
						enabled: true
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
							}
						}
					},
					series: data.data_chart
				});
			}else{
				jQuery('#download_by_product_main_chart').html('No data available.');
			}
			
		}
	});
}

function get_reserve_popular(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = "/admin/dashboard/ajax-get-data-reserve-popular/"+sid+"";
	var created_date_from = $("#created_date_from").val();
	var created_date_to = $("#created_date_to").val();
	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: full_url,
		data: { created_date_from: created_date_from, created_date_to: created_date_to, max_show_in_chart: "50", max_show_in_table: "0" },
		beforeSend: function() {
			jQuery('#reserve_popular_chart').html('Generating data...');
		},
		success: function(data) {
			
			//Draw Chart
			if(data.data_chart){
				chart_1 = new Highcharts.Chart({
					colors:["#FFFFFF"],
					chart: {
						renderTo: 'reserve_popular_chart'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.x +'</b><br/>'+
								this.series.name +': '+ this.y;
						}
					},						
					xAxis: {
						lineColor: '#FFFFFF',
						categories: data.task_title,
						labels: {
							rotation: -45,
							align: 'right',
							formatter: function() {
								return (this.value).toString().substring(0,8);
							}
						}
					},
					yAxis: [{ // Primary yAxis
						gridLineColor: '#FFFFFF',
						min: 0,
						minRange: 5,
						labels: {
							formatter: function() {
								return this.value;
							}
						},
						title: {
							text: ''
						}
					}],					
					legend: {
						enabled: false
					},
					series: data.data_chart
				});
			}else{
				jQuery('#reserve_popular_chart').html('No data available.');
			}
			
		}
	});	
}

function c_category_book_change_product_main_aid(value){
	$("#c_category_book_product_main_aid").val(value);
	$("a[name='c_category_book[]']").each(function()
	{
		var id = '#'+this.id;
		$(id).removeClass('active');
	});
	get_product_book_by_category();
}
function c_category_vdo_change_product_main_aid(value){
	$("#c_category_vdo_product_main_aid").val(value);
	$("a[name='c_category_vdo[]']").each(function()
	{
		var id = '#'+this.id;
		$(id).removeClass('active');
	});
	get_product_vdo_by_category();
}
