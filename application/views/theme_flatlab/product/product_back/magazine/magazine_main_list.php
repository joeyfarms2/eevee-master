<script type="text/javascript" src="<?=JS_PATH?>product/product_back/product_back_init.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=@folderName?>/magazine_main.js"></script>
<?php 
$data_search = "";
$init_adv_search = @$init_adv_search;
if($init_adv_search != "clear"){
	$dataSearchSession = new CI_Session();
	$data_search = $dataSearchSession->userdata('productMagazineMainBackDataSearchSession'); 
}
$search_record_per_page = get_array_value($data_search,"search_record_per_page","");
$search_order_by = get_array_value($data_search,"search_order_by","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_in = get_array_value($data_search,"search_in","");
$search_status = get_array_value($data_search,"search_status","");
$search_product_category = get_array_value($data_search,"search_product_category","");
$master_product_category = @$master_product_category;

$product_main_result = @$product_main_result;
$product_main_aid = get_array_value($product_main_result,"aid","");
$product_main_url = get_array_value($product_main_result,"url","");


// print_r($data_search);
?>

<form id="frm_product_main" name="frm_product_main" method="POST" action="" class="form-horizontal tasi-form">
<input type="hidden" id="aid_selected" name="aid_selected" />
<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
<input type="hidden" id="search_record_per_page" name="search_record_per_page" value="<?=$search_record_per_page?>" />
<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />
<input type="hidden" id="product_main_aid" name="product_main_aid" value="<?=$product_main_aid?>" />

	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a href="<?=site_url('admin/product-'.$product_main_url.'/magazine-main/add')?>" class="btn btn-primary">
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
								<input type="checkbox" id="search_in_1" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="title" <?php if(is_in_array("title",$search_in)) echo "checked"; ?> />Title
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="search_in_2" name="search_in[]"  onclick="changeCheckItem('search_in_all','search_in[]',false,true);showSearchResult();" value="author" <?php if(is_in_array("author",$search_in)) echo "checked"; ?> />Author
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Category</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="search_product_category_all" name="search_product_category_all" onclick="changeCheckAll('search_product_category_all','search_product_category[]',false,false);showSearchResult();" />All
							</label>
							<?php 
								if(is_var_array($master_product_category)){ 
									foreach($master_product_category as $m_item){
							?>
									<label class="checkbox-inline">
										<input type="checkbox" id="search_product_category_<?=get_array_value($m_item,"aid","")?>" name="search_product_category[]" onclick="changeCheckItem('search_product_category_all','search_product_category[]',false,true);showSearchResult();" value="<?=get_array_value($m_item,"aid","")?>" <?php if(is_in_array(get_array_value($m_item,"aid",""),$search_product_category)) echo 'checked="checked"';?>  /><?=get_array_value($m_item,"name","")?>
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
							<a class="btn btn-default" onclick="ajaxClearSearchResult('productMagazineMainBackDataSearchSession')" />Clear</a>
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
		changeCheckItem('search_product_category_all','search_product_category[]',false,false);
		changeCheckItem('search_status_all','search_status[]',false,true);

		showSearchResult();
		checkToggleAdvanceSearch();

		<?=@$message?>
		<?=@$js_code?>
	});
</script>