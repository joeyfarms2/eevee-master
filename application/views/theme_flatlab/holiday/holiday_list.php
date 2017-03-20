<style>
.da-box-content {
	padding: 30px;
}
.da-box-content input {
	float: left;
	margin-right: 10px;
}
.da-box-content label {
	float: left;
	margin-right: 20px;
}
</style>
<script type="text/javascript">
$(document).ready(function() {

	set_toggle_advance_search();
	
	$("#created_date_from, #created_date_to").datepicker({
		// changeMonth: true,
		// changeYear: true,
		// dateFormat: "yy-mm-dd",
		// dateISO:"true"
			format: "yyyy-mm-dd",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
	});
		
	showSearchResult();

	$(':checkbox.weekly_days').click(function() {
		var sun = $('#sun').is(':checked');
		var mon = $('#mon').is(':checked');
		var tue = $('#tue').is(':checked');
		var wed = $('#wed').is(':checked');
		var thu = $('#thu').is(':checked');
		var fri = $('#fri').is(':checked');
		var sat = $('#sat').is(':checked');
		
		var sid = Math.floor(Math.random()*10000000000);
		var full_url = base_url+"admin/holiday/ajax-save-weekend";
		$.getJSON(
			full_url,
			({
				sun: sun,
				mon: mon,
				tue: tue,
				wed: wed,
				thu: thu,
				fri: fri,
				sat: sat,
				sid: sid
			}),
			function(data) {
				$('#result-msg-box-weekly')
					.removeClass('hidden error')
					.addClass(data.status);
					
				$("#result-msg-box-weekly").html(data.msg);
				$("#result-msg-box-weekly").fadeIn("slow", function() {
					$(this).delay(3000).fadeOut("slow");
				});
			}
		);
	});
		
	<?=@$message?>
	<?=@$js_code?>	
} );
</script>

<script type="text/javascript" src="<?=JS_PATH?>holiday/holiday.js"></script>
<?php 
$data_search = @$data_search;
$search_in = get_array_value($data_search,"search_in","");
$search_product_main = get_array_value($data_search,"search_product_main","");
$search_status = get_array_value($data_search,"search_status","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_order_by = get_array_value($data_search,"search_order_by","");
$master_product_main = @$master_product_main;
?>
<form id="frm_holidays" name="frm_holidays" method="POST" action="<?=site_url('admin/holiday')?>" class="da-form">
	<input type="hidden" id="aid_selected" name="aid_selected" />
	<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
	<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />

	<div class="form-group">
		<div id="result-msg-box-weekly" class="da-message hidden"></div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<section class="panel">
				<header class="panel-heading">
					Weekly Holidays
				</header>
				<div  class="panel-body">

					<div class="form-group">
						<div class="col-md-12 col-lg-12">
							<!-- <div class="da-panel-content">
								<div class="da-box-content"> -->
									<label class="checkbox-inline">
										<input type="checkbox" id="sun" class="weekly_days" name="sun" value="sun" <?=$weekend_items['sun']=="1" ? "checked='checked'" : ""?> /> <label for="sun">ทุกวันอาทิตย์</label>&nbsp;&nbsp;&nbsp;
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="mon" class="weekly_days" name="mon" value="mon" <?=$weekend_items['mon']=="1" ? "checked='checked'" : ""?> /> <label for="mon">ทุกวันจันทร์ </label>&nbsp;&nbsp;&nbsp;
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="tue" class="weekly_days" name="tue" value="tue" <?=$weekend_items['tue']=="1" ? "checked='checked'" : ""?> /> <label for="tue">ทุกวันอังคาร </label>&nbsp;&nbsp;&nbsp;
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="wed" class="weekly_days" name="wed" value="wed" <?=$weekend_items['wed']=="1" ? "checked='checked'" : ""?>/> <label for="wed">ทุกวันพุธ </label>&nbsp;&nbsp;&nbsp;
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="thu" class="weekly_days" name="thu" value="thu" <?=$weekend_items['thu']=="1" ? "checked='checked'" : ""?>/> <label for="thu">ทุกวันพฤหัส </label>&nbsp;&nbsp;&nbsp;
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="fri" class="weekly_days" name="fri" value="fri" <?=$weekend_items['fri']=="1" ? "checked='checked'" : ""?>/> <label for="fri">ทุกวันศุกร์ </label>&nbsp;&nbsp;&nbsp;
									</label>
									<label class="checkbox-inline">
										<input type="checkbox" id="sat" class="weekly_days" name="sat" value="sat" <?=$weekend_items['sat']=="1" ? "checked='checked'" : ""?>/> <label for="sat">ทุกวันเสาร์</label>
									</label>
							<!-- 	</div>
							</div> -->
						</div>
					</div>

				</div>
			</section>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a href="<?=site_url('admin/holiday/add')?>" class="btn btn-info">
				<i class="fa fa-save"></i> Add New
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<section class="panel">
				<header class="panel-heading">
					Advance Search
					<span class="tools pull-right">
						<a id="adv-icon" href="javascript:;" class="fa fa-chevron fa-chevron-up"></a>
					</span>
				</header>

				<div id="adv-area" class="panel-body" style="display: none;">

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">By word</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="search_post_word" name="search_post_word" value="<?=get_array_value($data_search,"search_post_word","")?>" onkeyup="showSearchResult()" />
						</div>
					</div>

					<div class="form-group">&nbsp;</div>

						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label">Created date</label>
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
						</div>

						<div class="form-group">&nbsp;</div>

							<!-- Button -->
						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label">&nbsp;&nbsp;&nbsp;</label>
							<div class="col-md-12 col-lg-8">
								<a class="btn btn-primary" onclick="showSearchResult()" />Search</a>
								<a class="btn btn-default" onclick="ajaxClearSearchResult('transactionBackDataSearchSession')" />Clear</a>
							</div>
						</div>
							<!-- End : Button -->
					</div>
				</section>
			</div>
		</div>
		<div id="result-msg-box"></div>
		<div class="row">
			<div class="col-xs-12">
				<section class="panel">
					<header class="panel-heading">
						Result <span id="tbldata_processing" class="loading hidden"></span>
					</header>
					<div class="panel-body">
						<div class="adv-table">
							<div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">No record found.</div>
						</div>
					</div>
				</section>
			</div>
		</div>
		
</form>
<script type="text/javascript">
// 	$(document).ready(function() {

// 		showSearchResult();
// 		checkToggleAdvanceSearch();

// 		<?=@$message?>
// 		<?=@$js_code?>
// 	});
</script>