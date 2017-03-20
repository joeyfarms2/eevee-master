<?php
$data_search = @$data_search;
$c_category_book_product_main_aid = get_array_value($data_search,"c_category_book_product_main_aid","1");
$c_category_vdo_product_main_aid = get_array_value($data_search,"c_category_vdo_product_main_aid","4");
?>
<script type="text/javascript" src="<?=JS_PATH?>report/dashboard/dashboard.js"></script>
<script type="text/javascript" src="<?=JS_PATH?>report/report_init.js"></script>

<script src="<?=SCRIPT_PATH?>additional/highcharts-3.0.7/js/highcharts.js"></script>
<script src="<?=SCRIPT_PATH?>additional/highcharts-3.0.7/js/themes/bookdose.js"></script>

<form id="frm_dashboard" name="frm_dashboard" method="POST" action="" class="form-horizontal tasi-form">
<!-- Advance Search -->
<div class="row">
	<div class="col-xs-12">
		<section class="panel">
			<div id="adv-area" class="panel-body" >
				<div class="form-group">
					<label class="col-md-12 col-lg-2 control-label">Choose report's date</label>
					<div class="col-md-12 col-lg-8">
						<div class="input-group date form_datetime-adv" data-date="">
							<span class="input-group-addon">From</span>
							<input class="form-control" type="text" id="created_date_from" name="created_date_from" value="<?=get_array_value($data_search,"created_date_from","")?>" onchange="showSearchResult();" />

							<span class="input-group-addon">To</span>
							<input class="form-control" type="text" id="created_date_to" name="created_date_to" value="<?=get_array_value($data_search,"created_date_to","")?>" onchange="showSearchResult();" />

							<div class="input-group-btn">
								<button class="btn btn-danger" type="button" onclick="clearValue('created_date_from');clearValue('created_date_to');showSearchResult();">
								<i class="fa fa-times"></i>
								</button>
							</div>
						</div>
					</div>
					<div>
						<a class="btn btn-primary" onclick="showSearchResult()" />Submit</a>
						<span id="tbldata_processing" class="loading hidden"></span>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
<!-- End : Advance Search-->
</form>

<div class="row">
	<div class="col-lg-12 mb20">
		<h4>This report start <span id="report_title_area"></span></h4>
	</div>
</div>

<!--state overview start-->
<div class="row state-overview">
	<div class="col-lg-3 col-sm-6">
		<section class="panel">
			<div class="symbol terques">
				<i class="fa fa-user"></i>
			</div>
			<div class="value">
				<h1 class="" id="user_total_area">
					0
				</h1>
				<p>Users</p>
			</div>
		</section>
	</div>
	<div class="col-lg-3 col-sm-6">
		<section class="panel">
			<div class="symbol red">
				<i class="fa fa-book"></i>
			</div>
			<div class="value">
				<h1 class="" id="book_total_area">
					0
				</h1>
				<p>Books</p>
			</div>
		</section>
	</div>
	<div class="col-lg-3 col-sm-6">
		<section class="panel">
			<div class="symbol yellow">
					<i class="fa fa-video-camera"></i>
			</div>
			<div class="value">
				<h1 class="" id="vdo_total_area">
					0
				</h1>
				<p>Multimedias</p>
			</div>
		</section>
	</div>
	<div class="col-lg-3 col-sm-6">
		<section class="panel">
			<div class="symbol blue">
				<i class="fa fa-bookmark"></i>
			</div>
			<div class="value">
				<h1 class="" id="shelf_history_total_area">
					0
				</h1>
				<p>#Download</p>
			</div>
		</section>
	</div>
</div>
<!--state overview end-->

<div class="row">
	<div class="col-lg-6">
		<!--new earning start-->
		<div class="panel terques-chart">
			<div class="panel-body chart-texture pt0 pb0">
				<div class="" id="download_by_product_main_chart" style="width: 100%; height: 200px; margin: 0"></div>
			</div>
			<div class="chart-tittle">
				<span class="title">#Download seperate by type</span>
			</div>
		</div>
		<!--new earning end-->
	</div>
	<div class="col-lg-6">
		<!--total earning start-->
		<div class="panel green-chart">
			<div class="panel-body pt0 pb0">
				<div class="" id="reserve_popular_chart" style="width: 100%; height: 200px; margin: 0"></div>
			</div>
			<div class="chart-tittle">
				<span class="title">Popular reserve</span>
			</div>
		</div>
		<!--total earning end-->
	</div>
</div>

<div class="row">
	<div class="col-lg-8">
		<!--custom chart start-->
		<div class="border-head">
			<h3>Number of books seperate by type</h3>
		</div>
		<div class="" id="product_book_by_product_main_chart" style="width: 100%; height: 495px; margin: 0"></div>
		<!--custom chart end-->
	</div>
	<div class="col-lg-4">
		<!--new earning start-->
		<div class="panel terques-chart">
			<div class="panel-body chart-texture pt0 pb0">
				<div class="" id="user_registration_by_device_chart" style="width: 100%; height: 200px; margin: 0"></div>
			</div>
			<div class="chart-tittle">
				<span class="title">User registration seperate by device</span>
			</div>
		</div>
		<!--new earning end-->

		<!--total earning start-->
		<div class="panel green-chart">
			<div class="panel-body pt0 pb0">
				<div class="" id="user_login_by_device_chart" style="width: 100%; height: 200px; margin: 0"></div>
			</div>
			<div class="chart-tittle">
				<span class="title">User login by device</span>
			</div>
		</div>
		<!--total earning end-->
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel terques-chart">
			<div class="panel-body chart-texture pt0 pb0">
				<div class="" id="product_book_by_category_chart" style="width: 100%; height: 495px; margin: 0"></div>
			</div>
			<div class="chart-tittle">
				<span class="title">Number of book seperate by category</span>
				<input type="hidden" id="c_category_book_product_main_aid" name="c_category_book_product_main_aid" value="<?=$c_category_book_product_main_aid?>">
				<?php if(is_var_array($master_product_main)){ $i=0; ?>
				<span class="value">
				<?php 
						foreach ($master_product_main as $item){
							$aid = get_array_value($item,"aid","");
							$product_type_aid = get_array_value($item,"product_type_aid","");
							if($product_type_aid == "1" || $product_type_aid == "2"){
								$i++;
								echo ($i > 1) ? " | " : "";
				?>
								<a id="c_category_book_<?=$aid?>" name="c_category_book[]" onclick="c_category_book_change_product_main_aid('<?=$aid?>')" class="button"><?=get_array_value($item,"name","-");?></a>
				<?php } } ?>
				</span>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel terques-chart">
			<div class="panel-body chart-texture pt0 pb0">
				<div class="" id="product_vdo_by_category_chart" style="width: 100%; height: 495px; margin: 0"></div>
			</div>
			<div class="chart-tittle">
				<span class="title">Number of vdo seperate by category</span>
				<input type="hidden" id="c_category_vdo_product_main_aid" name="c_category_vdo_product_main_aid" value="<?=$c_category_vdo_product_main_aid?>">
				<?php if(is_var_array($master_product_main)){ $i=0; ?>
				<span class="value">
				<?php 
						foreach ($master_product_main as $item){
							$aid = get_array_value($item,"aid","");
							$product_type_aid = get_array_value($item,"product_type_aid","");
							if($product_type_aid == "3"){
								$i++;
								echo ($i > 1) ? " | " : "";
				?>
								<a id="c_category_vdo_<?=$aid?>" name="c_category_vdo[]" onclick="c_category_vdo_change_product_main_aid('<?=$aid?>')" class="button"><?=get_array_value($item,"name","-");?></a>
				<?php } } ?>
				</span>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<!--custom chart start-->
		<div class="border-head">
			<h3>20 Popular download</h3>
		</div>
		<div class="" id="product_book_popular_download_chart" style="width: 100%; height: 495px; margin: 0"></div>
		<!--custom chart end-->
	</div>
</div>
	
<div class="row">
	<div class="col-lg-12">
		<!--custom chart start-->
		<div class="border-head">
			<h3>20 Popular search word</h3>
		</div>
		<div class="" id="search_popular_chart" style="width: 100%; height: 495px; margin: 0"></div>
		<!--custom chart end-->
	</div>
</div>
	
<script>

function call_report(){
	get_summary();
	get_download_by_product_main();
	get_reserve_popular();
	get_product_book_and_copy_by_product_main();
	get_user_registration_by_device();
	get_user_login_by_device();
	get_product_book_by_category();
	get_product_vdo_by_category();
	get_product_book_popular_download();
	get_search_popular();
}

function call_report_more(){

}

$(document).ready(function() {
	$("#created_date_from, #created_date_to").datepicker({
		format: "yyyy-mm-dd",
		todayBtn: true,
		todayHighlight: true,
		autoclose: true
	}).on('changeDate', function(ev){
	});

	showSearchResult();

	//owl carousel
	$("#owl-demo").owlCarousel({
		navigation : true,
		slideSpeed : 300,
		paginationSpeed : 400,
		singleItem : true,
		autoPlay:true
	});
});

//custom select box

$(function(){
		$('select.styled').customSelect();
});

</script>
