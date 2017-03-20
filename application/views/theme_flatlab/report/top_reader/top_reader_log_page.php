<script type="text/javascript" src="<?=JS_PATH?>report/top_reader/top_reader_log.js"></script>
<?php 
$data_search = @$data_search;
$search_action = get_array_value($data_search,"search_action","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_order_by = get_array_value($data_search,"search_order_by","");

?>
<form id="frm_report" name="frm_report" method="POST" action="" class="form-horizontal tasi-form">
	<input type="hidden" id="aid_selected" name="aid_selected" />
	<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
	<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />

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
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Limit</label>
						<div class="col-md-12 col-lg-8">
							<?php $search_limit = @$search_limit?>
							<label class="radio-inline">
								<input type="radio" id="search_limit_0" name="search_limit"   value="5" checked/>5
							</label>
							<label class="radio-inline">
								<input type="radio" id="search_limit_1" name="search_limit"   value="10" <?=($search_limit == "10") ? "checked" : ""?> />10
							</label>
							<label class="radio-inline">
								<input type="radio" id="search_limit_2" name="search_limit"   value="20" <?=($search_limit == "20") ? "checked" : ""?> />20
							</label>
							<label class="radio-inline">
								<input type="radio" id="search_limit_3" name="search_limit"   value="50" <?=($search_limit == "50") ? "checked" : ""?> />50
							</label>
						</div>
					</div>


					<!-- <div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Limit</label>
						<div class="col-md-12 col-lg-8">
							
							<select id="limit" name="limit_aid" class="form-control chzn-select" >
								<option value="">Choose Limit..</option>
								<option value="5" < ?php if($limit_aid == "5") echo 'selected="selected"';?>>5</option>
								<option value="10" < ?php if($limit_aid == "10") echo 'selected="selected"';?>>10</option>
								<option value="20" < ?php if($limit_aid == "20") echo 'selected="selected"';?>>20</option>
								<option value="50" < ?php if($limit_aid == "50") echo 'selected="selected"';?>>50</option>
								
							</select>
						</div>
					</div> -->
					<!-- Button -->
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-8">
							<a class="btn btn-primary" onclick="showSearchResult()" />Submit</a>
							
						</div>
					</div>
					<!-- End : Button -->
					
				</div>
			</section>
		</div>
	</div>
	<!-- End : Advance Search-->

	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a onclick="exportToExcel()" class="btn btn-info">
				<i class="fa fa-save"></i> Export to Excel
			</a>
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
$(document).ready(function() {
	
	$("#created_date_from, #created_date_to").datepicker({
		format: "yyyy-mm-dd",
		todayBtn: true,
		todayHighlight: true,
		autoclose: true
	}).on('changeDate', function(ev){
	});
	
	changeCheckItem('search_action_all','search_action[]',false,true);
	
	showSearchResult();
	
	<?=@$message?>
	<?=@$js_code?>
	
} );
</script>
