<?php 
$command = @$command;
$item_detail = @$item_detail;
?>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/section.js"></script>
<form id="frm_section" name="frm_section" method="POST" action="<?=site_url('admin/user-section/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
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

					<div class="form-group hide">
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
						<label class="col-md-12 col-lg-2 control-label" for="option">Option</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="is_default" name="is_default" value="1" <?php if(get_array_value($item_detail,"is_default","") == "1") echo 'checked="checked"';?> />is default for new user
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="name">Section</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="name" name="name" value="<?=get_array_value($item_detail,"name","")?>" maxlength="100" />
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

			<?php if(CONST_HAS_TRANSACTION == "1"){ ?>
			<section class="panel">
				<header class="panel-heading fieldset">
					Transaction Default Setting
				</header>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="default_rental_period">Number of rental day</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="default_rental_period" name="default_rental_period" value="<?=get_array_value($item_detail,"default_rental_period","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="default_rental_fee">Rental fee (First period)</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="default_rental_fee" name="default_rental_fee" value="<?=get_array_value($item_detail,"default_rental_fee","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group hide">
						<label class="col-md-12 col-lg-2 control-label" for="default_rental_fee_point">Rental fee point (First period)</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="default_rental_fee_point" name="default_rental_fee_point" value="<?=get_array_value($item_detail,"default_rental_fee_point","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="default_rental_fine_fee">Overtime fee (per day)</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="default_rental_fine_fee" name="default_rental_fine_fee" value="<?=get_array_value($item_detail,"default_rental_fine_fee","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>


					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="default_renew_time">Renew time</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="default_renew_time" name="default_renew_time" value="<?=get_array_value($item_detail,"default_renew_time","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>


					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="default_renew_period">Renew period</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="default_renew_period" name="default_renew_period" value="<?=get_array_value($item_detail,"default_renew_period","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

				</div>
			</section>
			<?php } ?>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_section', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/user-section/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		
		$("#frm_section").validate({
			rules: {
				name: {
					required: true
				}
			},
			messages: {
				name: {
					required: "Enter section name."
				}
			}
		});
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>