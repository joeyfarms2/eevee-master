<?php 
$command = @$command;
$item_detail = @$item_detail;
$master_ads_category = @$master_ads_category;

$random = rand();
?>
<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> 
<script type="text/javascript">
//<![CDATA[
        bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
  //]]>
  </script>
<!--<script src="<?=SCRIPT_PATH?>additional/nicedit/nicEdit.js" ></script>-->
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/ads.js"></script>
<form id="frm_ads" name="frm_ads" method="POST" action="<?=site_url('admin/ads/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="cid" name="cid" value="<?=get_array_value($item_detail,"cid","")?>" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>

				<div class="panel-body">

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($item_detail,"status",""); ?>
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
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($item_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="category">Category</label>
						<div class="col-md-12 col-lg-8">
						<?php 
							$category =  get_array_value($item_detail,"category",""); 
							$category_arr = preg_split("/,/", $category, 0 , PREG_SPLIT_NO_EMPTY);
						?>
						<?php if(is_var_array($master_ads_category)){ ?>
							<?php foreach($master_ads_category as $m_item){ ?>
								<label class="checkbox-inline">
									<input type="checkbox" id="category_<?=get_array_value($m_item,"aid","")?>" name="category[]" value="<?=get_array_value($m_item,"aid","")?>" <?php if(is_in_array(get_array_value($m_item,"aid",""),$category_arr)) echo 'checked="checked"';?> /> <?=get_array_value($m_item,"name","")?>
								</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="title">Title</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="title" name="title" value="<?=get_array_value($item_detail,"title","")?>" maxlength="255" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="ref_link">Link</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="ref_link" name="ref_link" value="<?=get_array_value($item_detail,"ref_link","")?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="status">Open link in</label>
						<div class="col-md-12 col-lg-8">
							<?php $target =  get_array_value($item_detail,"target",""); ?>
							<label class="radio-inline">
								<input type="radio" name="target" id="target_1" value="_self" checked />Same page
							</label>
							<label class="radio-inline">
								<input type="radio" name="target" id="target_0" value="_blank" <?php if($target == "0") echo 'checked="checked"';?> />New page
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="tag">Image</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="cover_image" name="cover_image"/>
							<p class="help-block">Only file extension .jpg, .jpeg, .png, .gif and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_IMAGE)?>.</p>
						</div>
						<?php $img_src = get_image(get_array_value($item_detail,"cover_image_thumb_path",""),"small","off"); ?>
						<?php if(!is_blank($img_src)){ ?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
							<img src="<?=$img_src?>" />
						</div>
						<?php } ?>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Description</label>
						<div class="col-md-12 col-lg-8">
							<textarea class="form-control" id="description" name="description"><?=get_array_value($item_detail,"description","")?></textarea>
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/ads/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){			
		bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
		<?=@$message?>
		<?=@$js_code?>
	});
</script>