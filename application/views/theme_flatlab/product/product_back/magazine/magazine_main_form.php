<?php 
$command = @$command;
$magazine_main_detail = @$magazine_main_detail;

$product_main_result = @$product_main_result;
$product_main_aid = get_array_value($product_main_result,"aid","");
$product_main_url = get_array_value($product_main_result,"url","");

?>
<script type="text/javascript" src="<?=JS_PATH?>product/product_back/product_back_init.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/magazine_main.js"></script>
<form id="frm_product" name="frm_product" method="POST" action="<?=site_url('admin/product/magazine-main/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($magazine_main_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="product_main_aid" name="product_main_aid" value="<?=$product_main_aid?>" />
	<input type="hidden" id="product_type_aid" name="product_type_aid" value="<?=@$this_product_type_aid?>" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_main_menu.php'); ?>
				<header class="panel-heading no-radius fieldset">
					General Information
				</header>

				<div class="panel-body">
					<?php if(is_root_admin_or_higher() && $command == "_update"){ ?>
						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label required" for="aid_readonly">Product Aid</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control readonly" type="text" id="aid_readonly" name="aid_readonly" value="<?=get_array_value($magazine_main_detail,"aid","0")?>" readonly />
							</div>
						</div>

					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($magazine_main_detail,"status",""); ?>
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
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($magazine_main_detail,"weight","")?>" onkeypress="isWeight(event, this.value);isEnterGoTo(event, 'processSubmit(\'frm_product\')')" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="publisher">Publisher</label>
						<div class="col-md-12 col-lg-8">
							<?php $publisher_aid =  get_array_value($magazine_main_detail,"publisher_aid",""); ?>
							<select id="publisher_aid" name="publisher_aid" class="form-control chzn-select required" >
								<option value=""><?=$publisher_aid?>Choose publisher..</option>
								<?php 
									if(is_var_array($master_publisher)){ 
										foreach($master_publisher as $m_item){
								?>
										<option value="<?=get_array_value($m_item,"aid","")?>" <?php if($publisher_aid == get_array_value($m_item,"aid","")) echo 'selected="selected"';?>><?=get_array_value($m_item,"name","")?></option>
								<?php } } ?>
							</select>
							<p class="help-block a-right"><a href="<?=site_url('admin/publisher/add')?>">Add new publisher</a></p>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="title">Title</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control required" type="text" id="title" name="title" value="<?=get_array_value($magazine_main_detail,"title","")?>" onkeypress="isEnterGoTo(event, 'processSubmit(\'frm_product\')')" />
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_product', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-<?=$product_main_url?>/magazine-main');" />Cancel</a>
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