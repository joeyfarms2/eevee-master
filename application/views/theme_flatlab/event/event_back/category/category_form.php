<?php 
$command = @$command;
$item_detail = @$item_detail;
$master_event_main = @$master_event_main;
$lasted_event_main = "";
if(is_var_array($master_event_main)){
	$lasted_event_main = get_array_value(reset($master_event_main),"aid","");
}
?>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/category.js"></script>
<form id="frm_category" name="frm_category" method="POST" action="<?=site_url('admin/event-category/save')?>" class="cmxform form-horizontal tasi-form">
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
						<label class="col-md-12 col-lg-2 control-label required" for="event_main_aid">Main</label>
						<div class="col-md-12 col-lg-8">
						<?php $event_main_aid =  get_array_value($item_detail,"event_main_aid",""); ?>
						<?php if(is_var_array($master_event_main)){ ?>
							<?php foreach($master_event_main as $m_item){ ?>
							<label class="radio-inline">
								<input type="radio" name="event_main_aid" id="event_main_aid_<?=get_array_value($m_item,"aid","")?>" value="<?=get_array_value($m_item,"aid","")?>" <?php if($event_main_aid == get_array_value($m_item,"aid","")) echo 'checked="checked"';?> /><?=get_array_value($m_item,"name","")?>
							</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>

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

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_category', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/event-category/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var radios = jQuery('input:radio[name=event_main_aid]');
		if(radios.is(':checked') === false) {
			radios.filter('[value=<?=$lasted_event_main?>]').attr('checked', true);
		}
		
		$("#frm_category").validate({
			rules: {
				name: {
					required: true
				},
				event_main_aid: {
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