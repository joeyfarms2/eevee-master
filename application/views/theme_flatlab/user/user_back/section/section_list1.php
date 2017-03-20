<script type="text/javascript" src="<?=JS_PATH?><?=@folderName?>/section.js"></script>
<?php 
$data_search = "";
$init_adv_search = @$init_adv_search;
if($init_adv_search != "clear"){
	$dataSearchSession = new CI_Session();
	$data_search = $dataSearchSession->userdata('userSectionBackDataSearchSession'); 
}
$search_record_per_page = get_array_value($data_search,"search_record_per_page","");
$search_order_by = get_array_value($data_search,"search_order_by","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_in = get_array_value($data_search,"search_in","");
$search_status = get_array_value($data_search,"search_status","");

// print_r($data_search);
?>
<form id="frm_section" name="frm_section" method="POST" action="" class="form-horizontal tasi-form">
<input type="hidden" id="aid_selected" name="aid_selected" />
<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
<input type="hidden" id="search_record_per_page" name="search_record_per_page" value="<?=$search_record_per_page?>" />
<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />

	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a href="<?=site_url('admin/user-section/add')?>" class="btn btn-primary">
				<i class="fa fa-plus"></i> Add new
			</a>
		</div>
	</div>

	<!-- Advance Search -->
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
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_all" name="search_in_all" onclick="changeCheckAll('search_in_all','search_in[]',false,false);showSearchResult();" />All
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="name" <?php if(is_in_array("name",$search_in)) echo "checked"; ?> />Name
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Status</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="search_status_all" name="search_status_all" onclick="changeCheckAll('search_status_all','search_status[]',false,false);showSearchResult();" />All
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_status_0" name="search_status[]"  onclick="changeCheckItem('search_status_all','search_status[]',false,true);showSearchResult();" value="0" <?php if(is_in_array("0",$search_status)) echo "checked"; ?> />Inactive
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_status_1" name="search_status[]"  onclick="changeCheckItem('search_status_all','search_status[]',false,true);showSearchResult();" value="1" <?php if(is_in_array("1",$search_status)) echo "checked"; ?> />Active
							</label>
						</div>
					</div>

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

					<!-- Button -->
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-8">
							<a class="btn btn-primary" onclick="showSearchResult()" />Search</a>
							<a class="btn btn-default" onclick="ajaxClearSearchResult('userSectionBackDataSearchSession')" />Clear</a>
						</div>
					</div>
					<!-- End : Button -->
				</div>
			</section>
		</div>
	</div>
	<!-- End : Advance Search-->

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

		changeCheckItem('search_in_all','search_in[]',false,true);
		changeCheckItem('search_status_all','search_status[]',false,true);

		showSearchResult();
		checkToggleAdvanceSearch();

		<?=@$message?>
		<?=@$js_code?>
	} );
</script>