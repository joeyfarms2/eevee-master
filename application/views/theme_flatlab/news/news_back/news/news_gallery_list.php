<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/news_gallery.js"></script>
<?php 
$data_search = "";
$init_adv_search = @$init_adv_search;
if($init_adv_search != "clear"){
	$dataSearchSession = new CI_Session();
	$data_search = $dataSearchSession->userdata('productBookSampleBackDataSearchSession'); 
}
$search_record_per_page = get_array_value($data_search,"search_record_per_page","");
$search_order_by = get_array_value($data_search,"search_order_by","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_in = get_array_value($data_search,"search_in","");
$search_status = get_array_value($data_search,"search_status","");

$parent_detail = @$parent_detail;
$news_aid = get_array_value($parent_detail,"aid","");

?>
<form id="frm_product" name="frm_product" method="POST" action="" class="form-horizontal tasi-form">
<input type="hidden" id="aid_selected" name="aid_selected" />
<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
<input type="hidden" id="search_record_per_page" name="search_record_per_page" value="<?=$search_record_per_page?>" />
<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />
<input type="hidden" id="news_aid" name="news_aid" value="<?=$news_aid?>" />

	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_menu.php'); ?>
				
				<div class="panel-body">
					<div class="col-xs-12 no-left-padding">
						<a href="<?=site_url('admin/news/edit/'.get_array_value($parent_detail,"aid","").'/gallery/add')?>" class="btn btn-primary">
							<i class="fa fa-plus"></i> Add new photo
						</a>
					</div>
					<br>
					<small><em>* All photos in the gallery will be displayed after news info.</em></small>
					
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
						<div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">No photo found.</div>
					</div>
				</div>
			</section>
		</div>
	</div>

</form>
<script type="text/javascript">
$(document).ready(function() {

	showSearchResult();
		
	<?=@$message?>
	<?=@$js_code?>	
} );
</script>