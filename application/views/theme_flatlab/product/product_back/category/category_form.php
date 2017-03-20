<?php 
$command = @$command;
$item_detail = @$item_detail;
$master_product_main = @$master_product_main;
$master_user_section = @$master_user_section;
$ref_user_section = @$ref_user_section;
$lasted_product_main = "";
if(is_var_array($master_product_main)){
	$lasted_product_main = get_array_value(reset($master_product_main),"aid","");
}
?>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/category.js"></script>
<form id="frm_category" name="frm_category" method="POST" action="<?=site_url('admin/product-category/save')?>" class="cmxform form-horizontal tasi-form">
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

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="product_main_aid">Product main</label>
						<div class="col-md-12 col-lg-8">
						<?php $product_main_aid =  get_array_value($item_detail,"product_main_aid",""); ?>
						<?php if(is_var_array($master_product_main)){ ?>
							<?php foreach($master_product_main as $m_item){ ?>
							<label class="radio-inline">
								<input type="radio" name="product_main_aid" id="product_main_aid_<?=get_array_value($m_item,"aid","")?>" value="<?=get_array_value($m_item,"aid","")?>" <?php if($product_main_aid == get_array_value($m_item,"aid","")) echo 'checked="checked"';?> /><?=get_array_value($m_item,"name","")?>
							</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>

					<?php if(CONST_CATEGORY_MODE == "2"){ ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="parent_aid">Parent</label>
						<div class="col-md-12 col-lg-8">
							<select class="form-control" id="parent_aid" name="parent_aid">
								<option value="0">Choose parent</option>
								<?php 
									$parent_aid = get_array_value($item_detail,"parent_aid","0");
									if(is_var_array(@$all_parent_category)){
										foreach ($all_parent_category as $item) {
											echo '<option value="'.get_array_value($item,"aid","").'"';	
											if($parent_aid == get_array_value($item,"aid","")){
												echo ' selected ';
											}
											echo '>'.get_array_value($item,"name","").'</option>';
										}
									}
								?>
							</select>
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="name">Name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="name" name="name" value="<?=get_array_value($item_detail,"name","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="url">Url</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="url" name="url" value="<?=get_array_value($item_detail,"url","")?>" onkeypress="isKeyUrl(event, this.value)" maxlength="100" />
							<p class="help-block">Url must be [a-z] , [0-9] and [-]</p>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($item_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="category">Restriction section</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="ref_user_section_all" name="ref_user_section_all" onclick="changeCheckAll('ref_user_section_all','ref_user_section[]',false,false);" />All
							</label>
						<?php if(is_var_array($master_user_section)){ ?>
							<?php foreach($master_user_section as $m_item){ ?>
								<label class="checkbox-inline">
									<input type="checkbox" id="ref_user_section_<?=get_array_value($m_item,"aid","")?>" name="ref_user_section[]" value="<?=get_array_value($m_item,"aid","")?>" <?php if(is_in_array(get_array_value($m_item,"aid",""),$ref_user_section)) echo 'checked="checked"';?> onclick="changeCheckItem('ref_user_section_all','ref_user_section[]',false,true);" /> <?=get_array_value($m_item,"name","")?>
								</label>
							<?php } ?>
						<?php } ?>
							<label class="checkbox-inline">
								<input type="checkbox" id="ref_user_section_0" name="ref_user_section[]" value="0" <?php if(is_in_array("0",$ref_user_section)) echo 'checked="checked"';?> onclick="changeCheckItem('ref_user_section_all','ref_user_section[]',false,true);" /> Anonymous
							</label>
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_category', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-category/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var radios = jQuery('input:radio[name=product_main_aid]');
		if(radios.is(':checked') === false) {
			radios.filter('[value=<?=$lasted_product_main?>]').attr('checked', true);
		}
		
		changeCheckItem('ref_user_section_all','ref_user_section[]',false,true);
		
		$("#frm_category").validate({
			rules: {
				name: {
					required: true
				},
				product_main_aid: {
					required: true
				}
			},
			messages: {
				name: {
					required: "Enter category name."
				}
			}
		});
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>