<?php 
$command = @$command;
$item_detail = @$item_detail;
?>
<link rel="stylesheet" type="text/css" href="<?=THEME_ADMIN_PATH?>additional/jquery-easyui-1.3.4/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?=THEME_ADMIN_PATH?>additional/jquery-easyui-1.3.4/themes/icon.css">
<script type="text/javascript" src="<?=THEME_ADMIN_PATH?>additional/jquery-easyui-1.3.4/jquery.easyui.min.js"></script>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/type_field.js"></script>
<form id="frm_product_type_field" name="frm_product_type_field" method="POST" action="<?=site_url('admin/product-type-field/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="product_type_aid" name="product_type_aid" value="<?=get_array_value($product_type_result,"aid","")?>" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>

				<div class="panel-body">

					<?php if(is_root_admin_or_higher()){ ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cid">Code</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="cid" name="cid" value="<?=get_array_value($item_detail,"cid","")?>" maxlength="12" />
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="name">Field name</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="name" name="name" value="<?=get_array_value($item_detail,"name","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="tag">Tag</label>
						<div class="col-md-12 col-lg-8 input-group">
							<div class="">
								<input class="form-control" type="text" id="tag" name="tag" value="<?=get_array_value($item_detail,"tag","")?>" maxlength="6" />
								<span id="buttonTag" name="buttonTag" class="input-group-btn">
									<button class="btn btn-default" type="button" onclick="openTagDialog('<?=get_array_value($item_detail,"tag","")?>');">Browse</button>
								</span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="subfield_cd">Sub Field</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="subfield_cd" name="subfield_cd" class="" value="<?=get_array_value($item_detail,"subfield_cd","")?>" maxlength="1" onkeypress="" />
						</div>
					</div>

					<?php if(CONST_USE_PRODUCT_TOPIC == '1'){ ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="product_topic_main_cid">Topic Code</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="product_topic_main_cid" name="product_topic_main_cid" value="<?=get_array_value($item_detail,"product_topic_main_cid","")?>" maxlength="10" />
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="is_required">Is required?</label>
						<div class="col-md-12 col-lg-8 input-group">
							<?php 
								$is_required =  get_array_value($item_detail,"is_required","");
							?>
							<label class="checkbox-inline">
								<input type="checkbox" name="is_required" id="is_required" value="1" <?php if($is_required == "1") echo 'checked="checked"';?> />
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="input_type">Input type</label>
						<div class="col-md-12 col-lg-8 input-group">
							<?php 
								$input_type =  get_array_value($item_detail,"input_type","textbox");
							?>
							<label class="radio-inline">
								<input type="radio" name="input_type" id="input_type_textbox" value="textbox" <?php if($input_type == "textbox") echo 'checked="checked"';?> />Text box
							</label>
							<label class="radio-inline">
								<input type="radio" name="input_type" id="input_type_textarea" value="textarea" <?php if($input_type == "textarea") echo 'checked="checked"';?> />Text area
							</label>
						</div>
					</div>


					<?php if(is_root_admin_or_higher()){ ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="fixed_field">Fixed field?</label>
						<div class="col-md-12 col-lg-8 input-group">
							<?php 
								$fixed_field =  get_array_value($item_detail,"fixed_field","0");
							?>
							<label class="checkbox-inline">
								<input type="checkbox" name="fixed_field" id="fixed_field" value="1" <?php if($fixed_field == "1") echo 'checked="checked"';?> />
							</label>
						</div>
					</div>
					<?php }?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($item_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8 input-group">
							<?php $status =  get_array_value($item_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_product_type_field', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-type-field/<?=get_array_value($product_type_result,"aid","")?>/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		
		$("#frm_product_type_field").validate({
			rules: {
				name: {
					required: true
				}
			},
			messages: {
				name: {
					required: "Enter publisher name."
				}
			}
		});
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>
<!-- ui-dialog -->
<div class="modal fade" id="da-dialog-form-tag-div" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-header"><!-- Title goes here...--></h4>
			</div>
			<div class="modal-body" id="modal-msg">
			<!-- Body goes here...-->
				<ul id="tree-tag" class="" style="padding:20px 0;"></ul>
			</div>
			<div class="modal-footer" id="modal-button">
			<!-- Button goes here...-->
			</div>
		</div>
	</div>
</div>

<div id="da-dialog-form-topic-div" class="no-padding">
	<ul id="tree-topic" class="" style="padding:20px 0;"></ul>
</div>
