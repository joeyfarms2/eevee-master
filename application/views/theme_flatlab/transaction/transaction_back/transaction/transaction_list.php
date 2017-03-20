<script type="text/javascript" src="<?=JS_PATH?><?=@folderName?>/transaction.js"></script>
<?php 
$data_search = "";
$init_adv_search = @$init_adv_search;
if($init_adv_search != "clear"){
	$dataSearchSession = new CI_Session();
	$data_search = $dataSearchSession->userdata('transactionBackDataSearchSession'); 
}
$search_record_per_page = get_array_value($data_search,"search_record_per_page","");
$search_order_by = get_array_value($data_search,"search_order_by","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_in = get_array_value($data_search,"search_in","");
$search_status = get_array_value($data_search,"search_status","");
$search_option = get_array_value($data_search,"search_option","");

// print_r($data_search);
?>
<form id="frm_transaction" name="frm_transaction" method="POST" action="" class="form-horizontal tasi-form">
<input type="hidden" id="aid_selected" name="aid_selected" />
<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
<input type="hidden" id="search_record_per_page" name="search_record_per_page" value="<?=$search_record_per_page?>" />
<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />
<input type="hidden" id="user_aid" name="user_aid" value="" />
<?php /*
	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a href="<?=site_url('admin/transaction/add')?>" class="btn btn-primary">
				<i class="fa fa-plus"></i> Add new
			</a>
		</div>
	</div>
*/ ?>
	<!-- Advance Search -->
	<div class="row">
		<div class="col-xs-12">
			<section class="panel">
				<header class="panel-heading">
					Advance Search
					<!-- <span class="tools pull-right">
						<a id="adv-icon" href="javascript:;" class="fa fa-chevron fa-chevron-up"></a>
					</span> -->
				</header>

				<div  class="panel-body">

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">By word</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="search_post_word" name="search_post_word" value="<?=get_array_value($data_search,"search_post_word","")?>" onkeyup="showSearchResult()" />
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_all" name="search_in_all" onclick="changeCheckAll('search_in_all','search_in[]',false,false);showSearchResult();" />All
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="barcode" <?php if(is_in_array("barcode",$search_in)) echo "checked"; ?> />Book Barcode
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="title" <?php if(is_in_array("title",$search_in)) echo "checked"; ?> />Book Title
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="user.first_name_th" <?php if(is_in_array("user.first_name_th",$search_in)) echo "checked"; ?> />User First Name
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="user.last_name_th" <?php if(is_in_array("user.last_name_th",$search_in)) echo "checked"; ?> />User Last Name
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="user.email" <?php if(is_in_array("user.email",$search_in)) echo "checked"; ?> />User Email
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="user.cid" <?php if(is_in_array("user.cid",$search_in)) echo "checked"; ?> />Member ID
							</label>
						</div>
					</div>

					<div class="form-group hide">
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
						<label class="col-md-12 col-lg-2 control-label">Borrowing date</label>
						<div class="col-md-12 col-lg-8">
							<div class="input-group date form_datetime-adv" data-date="">
								<span class="input-group-addon">From</span>
								<input class="form-control" type="text" id="borrowing_date_from" name="borrowing_date_from" value="<?=get_array_value($data_search,"borrowing_date_from","")?>" onchange="showSearchResult();" />

								<span class="input-group-addon">To</span>
								<input class="form-control" type="text" id="borrowing_date_to" name="borrowing_date_to" value="<?=get_array_value($data_search,"borrowing_date_to","")?>" onchange="showSearchResult();" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('borrowing_date_from');clearValue('borrowing_date_to');showSearchResult();">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Due date</label>
						<div class="col-md-12 col-lg-8">
							<div class="input-group date form_datetime-adv" data-date="">
								<span class="input-group-addon">From</span>
								<input class="form-control" type="text" id="due_date_from" name="due_date_from" value="<?=get_array_value($data_search,"due_date_from","")?>" onchange="showSearchResult();" />

								<span class="input-group-addon">To</span>
								<input class="form-control" type="text" id="due_date_to" name="due_date_to" value="<?=get_array_value($data_search,"due_date_to","")?>" onchange="showSearchResult();" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('due_date_from');clearValue('due_date_to');showSearchResult();">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Option</label>
						<div class="col-md-12 col-lg-8">
							<label class="radio-inline">
								<input type="radio" id="search_option_0" name="search_option" onclick="showSearchResult();" value="0" <?php if($search_option == "1") echo "checked"; ?> />All
							</label>
							<label class="radio-inline">
								<input type="radio" id="search_option_1" name="search_option"  onclick="showSearchResult();" value="1" <?php if($search_option == "1") echo "checked"; ?> />Returned
							</label>
							<label class="radio-inline">
								<input type="radio" id="search_option_2" name="search_option"  onclick="showSearchResult();" value="2" <?php if($search_option == "2") echo "checked"; ?> />Borrowing
							</label>
							<label class="radio-inline">
								<input type="radio" id="search_option_3" name="search_option"  onclick="showSearchResult();" value="3" <?php if($search_option == "3") echo "checked"; ?> />Not Overdue
							</label>
							<label class="radio-inline">
								<input type="radio" id="search_option_4" name="search_option"  onclick="showSearchResult();" value="4" checked />Overdue
							</label>
						</div>
					</div>

					<!-- Button -->
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-8">
							<a class="btn btn-primary" onclick="showSearchResult()" />Search</a>
							<a class="btn btn-default" onclick="ajaxClearSearchResult('transactionBackDataSearchSession')" />Clear</a>
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
			<a onclick="showSearchResult(1);" class="btn btn-info">
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

		$("#borrowing_date_from, #borrowing_date_to, #due_date_from, #due_date_to").datepicker({
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