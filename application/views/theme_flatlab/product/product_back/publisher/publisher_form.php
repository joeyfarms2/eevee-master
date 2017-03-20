<?php 
$command = @$command;
$item_detail = @$item_detail;
?>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/publisher.js"></script>
<form id="frm_publisher" name="frm_publisher" method="POST" action="<?=site_url('admin/publisher/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
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

					<?php if($command == "_update"){ ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cid">Publisher code</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="cid" name="cid" value="<?=get_array_value($item_detail,"cid","")?>" readonly />
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="name">Publisher name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="name" name="name" value="<?=get_array_value($item_detail,"name","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="url">Publisher url</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="url" name="url" value="<?=get_array_value($item_detail,"url","")?>" onkeypress="isKeyUrl(event, this.value)" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="contact_name">Contact name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="contact_name" name="contact_name" value="<?=get_array_value($item_detail,"contact_name","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="contact_number">Contact number</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="contact_number" name="contact_number" value="<?=get_array_value($item_detail,"contact_number","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="email">Contact email</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="email" name="email" value="<?=get_array_value($item_detail,"email","")?>" maxlength="100" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="remark">Remark</label>
						<div class="col-md-12 col-lg-8">
							<textarea class="form-control" id="remark" name="remark"><?=get_array_value($item_detail,"remark","")?></textarea>
						</div>
					</div>


				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_publisher', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/publisher/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		
		$("#frm_publisher").validate({
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