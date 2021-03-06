<?php 
$command = @$command;
$item_detail = @$item_detail;
?>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/domain.js"></script>
<form id="frm_domain" name="frm_domain" method="POST" action="<?=site_url('admin/user-domain/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
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
						<label class="col-md-12 col-lg-2 control-label" for="name">Domain</label>
						<div class="col-md-12 col-lg-8">
							<div class="input-group">
								<span class="input-group-addon">@</span>
								<input class="form-control" type="text" id="name" name="name" value="<?=get_array_value($item_detail,"name","")?>" onkeypress="isKeyDomain(event, this.value)" maxlength="100" />
							</div>							
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($item_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_domain', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/user-domain/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		
		$("#frm_domain").validate({
			rules: {
				name: {
					required: true
				}
			},
			messages: {
				name: {
					required: "Enter domain name."
				}
			}
		});
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>