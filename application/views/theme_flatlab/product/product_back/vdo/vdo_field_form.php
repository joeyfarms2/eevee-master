<?php 
$command = @$command;
$parent_detail = @$parent_detail;
$field_item_detail = @$field_item_detail;

$product_main_result = @$product_main_result;
$product_main_aid = get_array_value($product_main_result,"aid","");
$product_main_url = get_array_value($product_main_result,"url","");

$parent_aid = get_array_value($parent_detail,"aid","");

?>
<link rel="stylesheet" type="text/css" href="<?=THEME_ADMIN_PATH?>additional/jquery-easyui-1.3.4/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="<?=THEME_ADMIN_PATH?>additional/jquery-easyui-1.3.4/themes/icon.css">
<script type="text/javascript" src="<?=THEME_ADMIN_PATH?>additional/jquery-easyui-1.3.4/jquery.easyui.min.js"></script>

<script type="text/javascript" src="<?=JS_PATH?>product/product_back/product_back_init.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/vdo.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/vdo_copy.js"></script>
<form id="frm_product" name="frm_product" method="POST" action="<?=site_url('admin/product/vdo-field/save')?>" class="cmxform form-horizontal tasi-form">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($field_item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="product_main_aid" name="product_main_aid" value="<?=$product_main_aid?>" />
	<input type="hidden" id="product_type_aid" name="product_type_aid" value="<?=@$this_product_type_aid?>" />
	<input type="hidden" id="parent_aid" name="parent_aid" value="<?=$parent_aid?>" />
	<input type="hidden" id="sequence" name="sequence" value="<?=get_array_value($field_item_detail,"sequence","")?>" />

	<div id="result-msg-box"></div>

	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_menu.php'); ?>
				<div class="panel-body">
				<?php 
					$product_main_field_aid = get_array_value($field_item_detail,"product_main_field_aid","");
					$readonly = "";
					if($product_main_field_aid != ""){
						$readonly = "readonly";
					}
				?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="name">Field Name : <?=$product_main_field_aid?></label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="name" name="name" value="<?=get_array_value($field_item_detail,"product_main_field_name","")?>" maxlength="255" <?=$readonly?> />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="tag">Tag</label>
						<div class="col-md-12 col-lg-8 input-group">
							<div class="">
								<input class="form-control required" type="text" id="tag" name="tag" value="<?=get_array_value($field_item_detail,"tag","")?>" maxlength="6" <?=$readonly?> />
								<?php if($readonly == ''){ ?>
								<span id="buttonTag" name="buttonTag" class="input-group-btn">
									<button class="btn btn-default" type="button" onclick="openTagDialog('<?=get_array_value($field_item_detail,"tag","")?>');">Browse</button>
								</span>
								<?php } ?>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="subfield_cd">Sub Field</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="subfield_cd" name="subfield_cd" class="<?=$readonly?>" value="<?=get_array_value($field_item_detail,"subfield_cd","")?>" maxlength="1" onkeypress="" <?=$readonly?> />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="ind1_cd">Indicator 1</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="ind1_cd" name="ind1_cd" class="" value="<?=get_array_value($field_item_detail,"ind1_cd","")?>" maxlength="1" onkeypress="" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="ind2_cd">Indicator 2</label>
						<div class="col-md-12 col-lg-8 input-group">
							<input class="form-control" type="text" id="ind2_cd" name="ind2_cd" class="" value="<?=get_array_value($field_item_detail,"ind2_cd","")?>" maxlength="1" onkeypress="" />
						</div>
					</div>
					
					<?php 
						$input_type = get_array_value($field_item_detail,"input_type","textbox");
						$is_required = get_array_value($field_item_detail,"is_required","");
						$required_class = "";
						if($is_required == '1'){
							$required_class = "required";
						}
						
					?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label <?=$required_class?>" for="field_data">Data</label>
						<div class="col-md-12 col-lg-8 input-group">
						<?php if($input_type == "textarea"){ ?>
							<textarea class="<?=$required_class?>" id="field_data" name="field_data"><?=get_array_value($field_item_detail,"field_data","")?></textarea>
						<?php }else if($input_type == "textbox_topic"){ ?>
							<div class="">
								<input class="form-control <?=$required_class?>" type="text" id="field_data" name="field_data" value="<?=get_array_value($field_item_detail,"field_data","")?>" />
								<span id="buttonTopic" name="buttonTopic" class="customfile-button" aria-hidden="true" onclick="openTopicDialog('<?=get_array_value($field_item_detail,"product_topic_main_cid","")?>','field_data');">Browse</span>
							</div>
						<?php }else{ ?>
							<input class="form-control" type="text" id="field_data" name="field_data" class="<?=$required_class?>" value="<?=get_array_value($field_item_detail,"field_data","")?>" />
						<?php } ?>
						</div>
					</div>




				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmit('frm_product', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-<?=$product_main_url?>/vdo/edit/<?=$parent_aid?>/field');" />Cancel</a>
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
