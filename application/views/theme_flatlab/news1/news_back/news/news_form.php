<?php 
$command = @$command;
$item_detail = @$item_detail;
$master_news_main = @$master_news_main;
$lasted_news_main = "";
if(is_var_array($master_news_main)){
	$lasted_news_main = get_array_value(reset($master_news_main),"aid","");
}
?>
<script type="text/javascript" src="<?=SCRIPT_PATH?>additional/nicedit/nicEdit.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/news.js"></script>
<form id="frm_news" name="frm_news" method="POST" action="<?=site_url('admin/news/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />

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

					<?php
						$class = "";
						if(CONST_EVENT_MODE != "1"){
							$class = "hide";
						}
					?>
					<div class="form-group <?=$class?>">
						<label class="col-md-12 col-lg-2 control-label required" for="news_main_aid">Main</label>
						<div class="col-md-12 col-lg-8">
						<?php $news_main_aid =  get_array_value($item_detail,"news_main_aid",""); ?>
						<?php if(is_var_array($master_news_main)){ ?>
							<?php foreach($master_news_main as $m_item){ ?>
							<label class="radio-inline">
								<input type="radio" name="news_main_aid" id="news_main_aid_<?=get_array_value($m_item,"aid","")?>" value="<?=get_array_value($m_item,"aid","")?>" <?php if($news_main_aid == get_array_value($m_item,"aid","")) echo 'checked="checked"';?> /><?=get_array_value($m_item,"name","")?>
							</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>

					<div class="form-group <?=$class?>">
						<label class="col-md-12 col-lg-2 control-label" for="category">Category</label>
						<div class="col-md-12 col-lg-8">
							<input type="hidden" name="category" id="category" value="<?=get_array_value($item_detail,"category","")?>" />
							<div id="category_area"></div>
						</div>
					</div>

					<div class="form-group <?=$class?>">
						<label class="col-md-12 col-lg-2 control-label" for="option">Option</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="is_highlight" name="is_highlight" value="1" <?php if(get_array_value($item_detail,"is_highlight","") == "1") echo 'checked="checked"';?> />Highlight
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_recommended" name="is_recommended" value="1" <?php if(get_array_value($item_detail,"is_recommended","") == "1") echo 'checked="checked"';?> />Recommended
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_home" name="is_home" value="1" <?php if( get_array_value($item_detail,"is_home","") == "1" || ($command=='_insert' && CONST_DEFAULT_CHECKED_SHOW_IN_HOME=='1') ) echo 'checked="checked"';?> />Show in home
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($item_detail,"weight","")?>" onkeypress="isWeight(news, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="title">Title</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="title" name="title" value="<?=get_array_value($item_detail,"title","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Publish date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="publish_date" name="publish_date" value="<?=get_array_value($item_detail,"publish_date","")?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('publish_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="description">Description</label>
						<div class="col-md-12 col-lg-8">
							<textarea class="form-control" id="description" name="description"><?=get_array_value($item_detail,"description","")?></textarea>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cover_image">Cover image</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="cover_image" name="cover_image"/>
							<p class="help-block">Only file extension <?=get_file_type(CONST_ALLOW_FILE_TYPE_FOR_EVENT_IMAGE)?> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_EVENT_IMAGE)?>.</p>
						</div>
						<?php $img_src = get_image(get_array_value($item_detail,"cover_image_small_path",""),"small","off"); ?>
						<?php if(!is_blank($img_src)){ ?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
							<img src="<?=$img_src?>" />
						</div>
						<?php } ?>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="ref_link">Link</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="ref_link" name="ref_link" value="<?=get_array_value($item_detail,"ref_link","")?>" maxlength="100" />
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmit('0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/news/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var radios = jQuery('input:radio[name=news_main_aid]');
		if(radios.is(':checked') === false) {
			radios.filter('[value=<?=$lasted_news_main?>]').attr('checked', true);
		}
		
		$("#publish_date").datepicker({
			changeMonth: true,
			changeYear: true,
			format: "yyyy-mm-dd",
			dateISO:"true",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
		});

		$("#frm_news").validate({
			rules: {
				title: {
					required: true
				},
				news_main_aid: {
					required: true
				}
			},
			messages: {
				title: {
					required: "Enter news title."
				}
			}
		});
	
		bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
		<?=@$message?>
		<?=@$js_code?>
	});
</script>