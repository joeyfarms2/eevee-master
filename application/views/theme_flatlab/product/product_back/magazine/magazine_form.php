<?php 
$command = @$command;
$parent_detail = @$parent_detail;

$product_main_result = @$product_main_result;
$product_main_aid = get_array_value($product_main_result,"aid","");
$product_main_url = get_array_value($product_main_result,"url","");

?>
<script type="text/javascript" src="<?=JS_PATH?>product/product_back/product_back_init.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/magazine.js"></script>
<form id="frm_product" name="frm_product" method="POST" action="<?=site_url('admin/product/magazine/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($parent_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="product_main_aid" name="product_main_aid" value="<?=$product_main_aid?>" />
	<input type="hidden" id="product_type_aid" name="product_type_aid" value="<?=@$this_product_type_aid?>" />
	<input type="hidden" id="force" name="force" value="false" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_menu.php'); ?>
				<header class="panel-heading no-radius fieldset">
					<?=get_language_line($this, 'product_menu_general_info', 'General Info')?> <!-- General Info -->
				</header>

				<div class="panel-body">
					<?php if(is_root_admin_or_higher() && $command == "_update"){ ?>
						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label required" for="aid_readonly">Product Code</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control readonly" type="text" id="aid_readonly" name="aid_readonly" value="<?=get_text_pad(get_array_value($parent_detail,"aid","0"))?>" readonly />
							</div>
						</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($parent_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="category">Category</label>
						<div class="col-md-12 col-lg-8">
							<input type="hidden" name="category" id="category" value="<?=get_array_value($parent_detail,"category","")?>" />
							<div id="category_area"></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="option">Option</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="is_new" name="is_new" value="1" <?php if(get_array_value($parent_detail,"is_new","") == "1") echo 'checked="checked"';?> />Most Popular<? //=get_language_line($this, 'product_field_option_new', 'New')?>
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_recommended" name="is_recommended" value="1" <?php if(get_array_value($parent_detail,"is_recommended","") == "1") echo 'checked="checked"';?> />Librarian's Choices<? //=get_language_line($this, 'product_field_option_recommended', 'Recommended')?>
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_home" name="is_home" value="1" <?php if(get_array_value($parent_detail,"is_home","") == "1") echo 'checked="checked"';?> />New Releases<? //=get_language_line($this, 'product_field_option_home', 'Show in Home')?>
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($parent_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="magazine_main">Magazine</label>
						<div class="col-md-12 col-lg-8">
							<?php 
								$magazine_main_aid =  get_array_value($parent_detail,"magazine_main_aid","");
								if(!is_number_no_zero($magazine_main_aid)){
									$magazine_main_aid = @$init_magazine_main_aid;
								}
							?>

							<?php if($command == "_update" && $magazine_main_aid > 0){ ?>
							<input type="hidden" id="magazine_main_aid" name="magazine_main_aid" value="<?=$magazine_main_aid?>" />
							<p id="magazine_main_title" class="form-control " readonly>
								<?php 
									if(is_var_array($master_magazine_main)){ 
										foreach($master_magazine_main as $m_item){
											if($magazine_main_aid == get_array_value($m_item,"aid","")) echo get_array_value($m_item,"title","N/A");
								?>
								<?php } } ?>
							</p>
							<?php }else{ ?>
								<select id="magazine_main_aid" name="magazine_main_aid" class="form-control chzn-select required" onchange="changeMagazineMain()" required>
									<option value="">Choose magazine..</option>
									<?php 
										if(is_var_array($master_magazine_main)){ 
											foreach($master_magazine_main as $m_item){
									?>
											<option value="<?=get_array_value($m_item,"aid","")?>" data-title="<?=get_array_value($m_item,"title","")?>" <?php if($magazine_main_aid == get_array_value($m_item,"aid","")) echo 'selected="selected"';?>><?=get_array_value($m_item,"title","")?></option>
									<?php } } ?>
								</select>
							<?php } ?>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="volumn">Volumn</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="volumn" name="volumn" value="<?=get_array_value($parent_detail,"volumn","")?>" onkeyup="$('#force').val(true);auto_set_title();" maxlength="8" />
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="issue">Issue</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="issue" name="issue" value="<?=get_array_value($parent_detail,"issue","")?>" onkeypress="isNumeric(event, this.value);" onkeyup="$('#force').val(true);auto_set_title();" maxlength="8" />
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="desc">Else</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="desc" name="desc" value="<?=get_array_value($parent_detail,"desc","")?>" onkeyup="$('#force').val(true);auto_set_title();" maxlength="128" />
							</div>
						</div>

					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Publish date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="publish_date" name="publish_date" value="<?=get_array_value($parent_detail,"publish_date","")?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('publish_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<!-- <div class="form-group hide">
						<label class="col-md-12 col-lg-2 control-label">Expiration date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="expired_date" name="expired_date" value="<?=get_array_value($parent_detail,"expired_date","")?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('expired_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div> -->

					

					<?php if(CONST_HAS_REWARD_POINT){ ?>
					<div class="form-group hidden">
						<label class="col-md-12 col-lg-2 control-label" for="reward_point">Reward point</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="reward_point" name="reward_point" value="<?=get_array_value($parent_detail,"reward_point","")?>" />
						</div>
					</div>
					<?php } ?>


					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cover_image">Cover image</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="cover_image" name="cover_image"/>
							<p class="help-block">Only file extension .jpg, .jpeg, .png, .gif and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_IMAGE)?>.  <span style="color:red;">( width : 320px * height : 450px )</span></p>
						</div>
						<?php $img_src = get_image(get_array_value($parent_detail,"cover_image_small_path",""),"small","off"); ?>
						<?php if(!is_blank($img_src)){ ?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
							<img src="<?=$img_src?>" />
						</div>
						<?php } ?>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="tag">Keyword</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="tag" name="tag" value="<?=get_array_value($parent_detail,"tag","")?>" />
						</div>
					</div>

					<div class="form-group hidden">
						<label class="col-md-12 col-lg-2 control-label" for="file_upload">Sample</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="file_upload" name="file_upload"/>
							<p class="help-block" >Only file extension <span id="file_type_desc">.pdf</span> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_DEFAULT)?>.</p>
						</div>
						<?php
							$sample_file_path = get_array_value($parent_detail,"sample_file_path","");
							// echo "sample_file_path : ".$sample_file_path;
							if(is_file($sample_file_path)){
						?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
								<a href="<?=site_url($sample_file_path)?>" target="_blank"><?=basename($sample_file_path)?></a>
						</div>
						<?php } ?>
					</div>

				</div>
			</section>

			<section class="panel">
				<header class="panel-heading fieldset">
					Marc Information
				</header>
				<div class="panel-body">
					<div id="marc_field_list_area"></div>
				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_product', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-<?=$product_main_url?>/magazine');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){

		const_has_reward_point = '<?=CONST_HAS_REWARD_POINT?>';
		const_reward_point = '<?=CONST_REWARD_POINT?>';
		lang_volumn = '<?=@$lang_product_magazine_volumn?>';
		lang_issue = '<?=@$lang_product_magazine_issue?>';

		$("#publish_date, #expired_date").datepicker({
			format: "yyyy-mm-dd",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
		});

		getCategoryByProductMainAid();
		getProductFieldByProductMainAid();

		rewritePriceZone();
		auto_set_title();
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>