<?php 
$command = @$command;
$item_detail = @$item_detail;
?>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/device_message.js"></script>
<form id="frm_device_message" name="frm_device_message" method="POST" action="<?=site_url('admin/device-message/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
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
						<label class="col-md-12 col-lg-2 control-label" for="weight">Message</label>
						<div class="col-md-12 col-lg-8">
							<textarea class="form-control" id="message" name="message"><?=get_array_value($item_detail,"message","")?></textarea>
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_device_message','0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/device-message/show');" />Cancel</a>
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