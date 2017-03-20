<?php 
$command = @$command;
$item_detail = @$item_detail;
$master_product_type = @$master_product_type;
$lasted_product_type = "";
if(is_var_array($master_product_type)){
	$lasted_product_type = get_array_value(reset($master_product_type),"aid","");
}

?>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/main.js"></script>
<form id="frm_product_main" name="frm_product_main" method="POST" action="<?=site_url('admin/product-main/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
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
						<label class="col-md-12 col-lg-2 control-label required" for="product_type_aid">Product type</label>
						<div class="col-md-12 col-lg-8">
							<?php $product_type_aid =  get_array_value($item_detail,"product_type_aid",""); ?>
							<?php 
								if(is_var_array($master_product_type)){ 
									foreach($master_product_type as $m_item){
							?>
										<label class="radio-inline">
											<input type="radio" name="product_type_aid" id="product_type_aid_<?=get_array_value($m_item,"aid","")?>" value="<?=get_array_value($m_item,"aid","")?>" <?php if($product_type_aid == get_array_value($m_item,"aid","")) echo 'checked="checked"';?> /><?=get_array_value($m_item,"name","")?>
										</label>
							<?php } } ?>
							<span id="publisher_area" class="hidden">
								<select id="publisher_aid" name="publisher_aid" disabled="disabled" class="required form-control w-auto inline">
									<option value="">Choose publisher..</option>
									<?php
									if(is_var_array($master_publisher)){
										foreach($master_publisher as $item){
											$selected = (get_array_value($item,"aid","0") == get_array_value($item_detail,"publisher_aid","")) ? 'selected="selected"' : '';
											echo '<option value="'.get_array_value($item,"aid","0").'" '.$selected.'>'.get_array_value($item,"name","N/A").'</option>';
										}									
									}
									?>
								</select>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="name">Name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="name" name="name" value="<?=get_array_value($item_detail,"name","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="url">Url</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="url" name="url" value="<?=get_array_value($item_detail,"url","")?>" onkeypress="isKeyUrl(event, this.value)" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($item_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="icon">Icon name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="icon" name="icon" value="<?=get_array_value($item_detail,"icon","")?>" maxlength="100" />
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_product_main', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-main/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var radios = $('input:radio[name=product_type_aid]');
		if(radios.is(':checked') === false) {
			radios.filter('[value=<?=$lasted_product_type?>]').attr('checked', true);
		}

		$("#frm_product_main").validate({
			rules: {
				name: {
					required: true
				}
			},
			messages: {
				name: {
					required: "Enter name."
				}
			}
		});
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>