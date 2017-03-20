<?php 
$command = @$command;
$item_detail = @$item_detail;
$master_news_main = @$master_news_main;
$lasted_news_main = "";
if(is_var_array($master_news_main)){
	$lasted_news_main = get_array_value(reset($master_news_main),"aid","");
}
if($command == '_insert') {
	$news_main_aid = '1';
}
else {
	$news_main_aid = get_array_value($item_detail, 'news_main_aid', '1');
}
?>
<script type="text/javascript" src="<?=SCRIPT_PATH?>additional/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/news.js"></script>
<form id="frm_news" name="frm_news" method="POST" action="<?=site_url('admin/news/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail, 'aid', '')?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="status" name="status" value="<?=get_array_value($item_detail, 'status', '')?>" />

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
						<label class="col-md-12 col-lg-2 control-label required" for="title">Title</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="title" name="title" value="<?=get_array_value($item_detail,"title","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Published date</label>
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

					<?php if ($command == '_update') { ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="status">Published status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($item_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status_1" id="status_1" value="1" <?php if($status == "1") echo 'checked="checked"';?> disabled />Published
							</label>
							<label class="radio-inline">
								<input type="radio" name="status_0" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> disabled />Draft
							</label>
						</div>
					</div>
					<?php } ?>

					<?php
						$class = "";
						if(CONST_NEWS_MODE != "1"){
							$class = "hide";
						}
					?>

					<?php if (is_general_admin_or_higher()) { ?>
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
					<?php } else { ?>
					<input type='hidden' id='news_main_aid' name='news_main_aid' value='<?=$news_main_aid?>'/>
					<?php } ?>
					

					<div class="form-group <?=$class?>">
						<label class="col-md-12 col-lg-2 control-label required" for="category">Category</label>
						<div class="col-md-12 col-lg-8">
							<fieldset>
							<input type="hidden" name="category" id="category" value="<?=get_array_value($item_detail,"category","")?>"/>
							<div id="category_area"></div>
							</fieldset>
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
								<input type="checkbox" id="is_home" name="is_home" value="1" <?php if( get_array_value($item_detail,"is_home","") == "1" || ($command=='_insert' && CONST_NEWS_DEFAULT_CHECKED_SHOW_IN_HOME=='1') ) echo 'checked="checked"';?> disabled="disabled" />Show in homepage
								<?php if (CONST_NEWS_DEFAULT_CHECKED_SHOW_IN_HOME=='1') { ?>
									<input type="hidden" name="is_home" value="1"/>
								<?php } ?>
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
						<label class="col-md-12 col-lg-2 control-label" for="description_1">Description</label>
						<div class="col-md-12 col-lg-8">
							<textarea class="form-control" id="description_1" name="description_1"><?=get_array_value($item_detail,"description","")?></textarea>
							<textarea class="hidden" id="description" name="description"></textarea>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cover_image">Cover image</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="cover_image" name="cover_image"/>
							<p class="help-block">Only file extension <?=get_file_type(CONST_ALLOW_FILE_TYPE_FOR_EVENT_IMAGE)?> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_EVENT_IMAGE)?>.</p>
						</div>

						<?php 
							if (get_array_value($item_detail,"cover_image_file_type","") != "") { 
					    	$cover_image_full_path = './'.get_array_value($item_detail,"upload_path","").get_array_value($item_detail,"cid","").'-thumb'.get_array_value($item_detail,"cover_image_file_type","");
					    	if (file_exists($cover_image_full_path)) {
					    ?>
					    <div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
					    	<img src='<?=site_url().$cover_image_full_path?>' />
					    </div>
					    <?php } } ?>


					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="ref_link">Reference URL</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="ref_link" name="ref_link" value="<?=get_array_value($item_detail,"ref_link","")?>" maxlength="100" placeholder='http://'/>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="option">Email notification</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="email_notify" name="email_notify" value="1" /> Notify everyone via email
							</label>
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a id="btn_preview" class="btn btn-info" onclick="processSubmit('2');" /><i class="fa fa-search prs"></i> Preview</a>
					<a id="btn_save" class="btn btn-primary" onclick="processSubmit('0');" /><i class="fa fa-globe prs"></i> Save &amp; Publish</a>
					<a id="btn_save_draft" class="btn btn-default" onclick="processSubmit('1');" /><i class="fa fa-download prs"></i> Save as draft</a>
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
				},
				'category[]': {
					required: true
				}
			},
			messages: {
				title: {
					required: "Enter news title."
				},
				'category[]': {
					required: "Please choose at least one category."
				}
			},
			errorClass: 'error',
			errorPlacement: function(error, element) {
	        if (element.is(':checkbox') || element.is(':radio'))
	            error.insertAfter($(element).closest('fieldset'));
	        else
	            error.insertAfter(element);
		    }
		});

		tinymce.init({
		    selector: "#description_1",
		    height: 400,
		    theme: "modern",
		    plugins: [
				"advlist autolink lists link image charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars code fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons template paste textcolor colorpicker textpattern moxiemanager"
			],
		    toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | forecolor backcolor emoticons",
		    image_advtab: true,
		});

		getCategoryByNewsMainAid();

		<?=@$message?>
		<?=@$js_code?>
	});
</script>