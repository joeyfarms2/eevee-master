<?php 
$command = @$command;
$parent_detail = @$parent_detail;
$gallery_item_detail = @$gallery_item_detail;

$news_aid = get_array_value($parent_detail,"aid","");

?>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/news_gallery.js"></script>
<form id="frm_product" name="frm_product" method="POST" action="<?=site_url('admin/news-gallery/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($gallery_item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="news_aid" name="news_aid" value="<?=$news_aid?>" />
	<input type="hidden" id="cid" name="cid" value="<?=get_array_value($gallery_item_detail,"cid","")?>" />

	<div id="result-msg-box"></div>

	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_menu.php'); ?>
				

				<div class="panel-body">

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="image_name">File(s)</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="image_name" name="image_name[]" multiple="" accept="image/*" />
							<p class="help-block" id="image_name_limit_for_default">Only file extension <?=get_file_type(CONST_ALLOW_FILE_TYPE_FOR_IMAGE)?> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_IMAGE)?>.</p>
						</div>
						<?php
							$upload_path = get_array_value($gallery_item_detail,"upload_path","")."galleries/";
							$image_name = get_array_value($gallery_item_detail,"file_name","");
							$file_title_name = (get_array_value($gallery_item_detail,"title")!="" ? get_array_value($gallery_item_detail,"title") : get_array_value($gallery_item_detail,"file_name", ''));
							// echo "path : ".$upload_path.$image_name;
							if(is_file($upload_path.'thumb/'.$image_name)){
						?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
								<a href="<?=site_url($upload_path.'original/'.$image_name)?>" target="_blank"><img src="<?=site_url($upload_path.'thumb/'.$image_name)?>"/ title="<?=$file_title_name?>"></a>
						</div>
						<?php } ?>
					</div>

					<?php if ($command == "_update") { ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="title">Title</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="title" name="title" value="<?=get_array_value($gallery_item_detail,"title","")?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($gallery_item_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($gallery_item_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>
					<?php } ?>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="checkBeforeProcess('frm_product', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/news/edit/<?=$news_aid?>/gallery');" /><i class="fa fa-chevron-left"></i> Back to photo gallery</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>