<script type="text/javascript" src="<?=JS_PATH?>user/user.js"></script>
<?php 
$data_search = "";
$init_adv_search = @$init_adv_search;
if($init_adv_search != "clear"){
	$dataSearchSession = new CI_Session();
	$data_search = $dataSearchSession->userdata('userBackDataSearchSession'); 
}
$search_record_per_page = get_array_value($data_search,"search_record_per_page","");
$search_order_by = get_array_value($data_search,"search_order_by","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_in = get_array_value($data_search,"search_in","");
$search_status = get_array_value($data_search,"search_status","");
$search_role = get_array_value($data_search,"search_role","");
$master_user_role = @$master_user_role;

// print_r($data_search);
?>

<form id="frm_user" name="frm_user" method="POST" action="" class="form-horizontal tasi-form">
<input type="hidden" id="aid_selected" name="aid_selected" />
<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
<input type="hidden" id="search_record_per_page" name="search_record_per_page" value="<?=$search_record_per_page?>" />
<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />

	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a href="<?=site_url('admin/user/add')?>" class="btn btn-primary">
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
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="email" <?php if(is_in_array("email",$search_in)) echo "checked"; ?> />Email
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_2" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="username" <?php if(is_in_array("username",$search_in)) echo "checked"; ?> />Username
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_3" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="first_name_th" <?php if(is_in_array("first_name_th",$search_in)) echo "checked"; ?> />Firstname
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_4" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="last_name_th" <?php if(is_in_array("last_name_th",$search_in)) echo "checked"; ?> />Lastname
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_5" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="display_name" <?php if(is_in_array("display_name",$search_in)) echo "checked"; ?> />Display name
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_6" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="cid" <?php if(is_in_array("cid",$search_in)) echo "checked"; ?> />Member ID
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">User role</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="search_role_all" name="search_role_all" onclick="changeCheckAll('search_role_all','search_role[]',false,false);showSearchResult();" />All
							</label>
							<?php 
								if(is_var_array($master_user_role)){ 
									foreach($master_user_role as $m_item){
							?>
									<label class="checkbox-inline">
										<input type="checkbox" id="search_role_<?=get_array_value($m_item,"aid","")?>" name="search_role[]" onclick="changeCheckItem('search_role_all','search_role[]',false,true);showSearchResult();" value="<?=get_array_value($m_item,"aid","")?>" <?php if(is_in_array(get_array_value($m_item,"aid",""),$search_role)) echo 'checked="checked"';?>  /><?=get_array_value($m_item,"name","")?>
									</label>
								<?php } //foreach ?>
							<?php } //if ?>
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
						<label class="col-md-12 col-lg-2 control-label">Joining date</label>
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
							<a class="btn btn-default" onclick="ajaxClearSearchResult('userBackDataSearchSession')" />Clear</a>
						</div>
					</div>
					<!-- End : Button -->
				</div>
			</section>
		</div>
	</div>
	<!-- End : Advance Search-->

<?php if(is_owner_admin_or_higher()){?>
	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a onclick="exportToExcel()" class="btn btn-info">
				<i class="fa fa-save"></i> Export to Excel
			</a>
		</div>
	</div>
<?php } ?>

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
		changeCheckItem('search_role_all','search_role[]',false,true);

		showSearchResult();
		checkToggleAdvanceSearch();

		<?=@$message?>
		<?=@$js_code?>
	});
</script>