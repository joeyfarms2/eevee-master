<?php 
$command = @$command;
$redeem_main_detail = @$redeem_main_detail;

$class = '';
$disabled = '';
if($command == "_update"){
	$class = 'readonly';
	$disabled = 'disabled';
}

?>
<script type="text/javascript" src="<?=SCRIPT_PATH?>additional/nicedit/nicEdit.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/redeem.js"></script>
<form id="frm_redeem" name="frm_redeem" method="POST" action="<?=site_url('admin/redeem/save')?>" class="cmxform form-horizontal tasi-form">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($redeem_main_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="cid" name="cid" value="<?=get_array_value($redeem_main_detail,"cid","")?>" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_menu.php'); ?>
				<header class="panel-heading no-radius fieldset">
					General Info
				</header>
				<div class="panel-body">

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($redeem_main_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="start_date">Start date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="start_date" name="start_date" value="<?=get_array_value($redeem_main_detail,"start_date","")?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('start_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="expired_date">Expiration date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="expired_date" name="expired_date" value="<?=get_array_value($redeem_main_detail,"expired_date","")?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('expired_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="title">Title</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="title" name="title" value="<?=get_array_value($redeem_main_detail,"title","")?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="type">Type</label>
						<div class="col-md-12 col-lg-8">
							<?php 
								$master_redeem_type = explode(":",CONST_MASTER_REDEEM_TYPE);
								$type =  get_array_value($redeem_main_detail,"type",get_array_value($master_redeem_type,"0",""));
								$i = 0;
								if(is_var_array($master_redeem_type)){
									foreach ($master_redeem_type as $item) {
										// echo "item = $item <BR />";
										$i++;
							?>
							<label class="radio-inline">
								<input type="radio" name="type" id="type_<?=$i?>" value="<?=$item?>" <?=($type == $item) ? 'checked="checked"' : ''?> <?=$disabled?> /><?=get_language_line($this, 'redeem_master_type_'.$item, ucfirst($item))?>
							</label>
							<?php } } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="value">Value</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control required <?=$class?>" type="text" id="value" name="value" value="<?=get_array_value($redeem_main_detail,"value","")?>" onkeypress="isNumeric(event, this.value)" maxlength="6" <?=$class?> />
						</div>
					</div>


				</div>
			</section>

			<section class="panel">
				<header class="panel-heading fieldset">
					Code Generator option
				</header>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="amount">Amount of code</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control required <?=$class?>" type="text" id="amount" name="amount" value="<?=get_array_value($redeem_main_detail,"amount","")?>" onkeypress="isNumeric(event, this.value)" maxlength="6" <?=$class?> />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="limit_per_code">Limitation per code</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control required <?=$class?>" type="text" id="limit_per_code" name="limit_per_code" value="<?=get_array_value($redeem_main_detail,"limit_per_code",CONST_REDEEM_DEFAULT_LIMIT_PER_CODE)?>" onkeypress="isNumeric(event, this.value)" maxlength="6" <?=$class?> />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="limit_per_user">Limitation per user</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control required <?=$class?>" type="text" id="limit_per_user" name="limit_per_user" value="<?=get_array_value($redeem_main_detail,"limit_per_user",CONST_REDEEM_DEFAULT_LIMIT_PER_USER)?>" onkeypress="isNumeric(event, this.value)" maxlength="6" <?=$class?> />
						</div>
					</div>

					<div class="form-group">
						<div class="">
							<div class="col-md-12 col-lg-8 col-lg-offset-2">
								Example code : <span id="redeem_example"></span>
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="code_prefix">Code pre-fix</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control <?=$class?>" type="text" id="code_prefix" name="code_prefix" value="<?=get_array_value($redeem_main_detail,"code_prefix","")?>" maxlength="50" onkeyup="generate_example();" <?=$class?> />
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label required" for="code_length">Random code length</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control required <?=$class?>" type="text" id="code_length" name="code_length" value="<?=get_array_value($redeem_main_detail,"code_length",CONST_REDEEM_DEFAULT_CODE_LENGTH)?>" maxlength="2" onkeypress="isNumeric(event, this.value);" onkeyup="generate_example();" <?=$class?> />
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="code_postfix">Code post-Fix</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control <?=$class?>" type="text" id="code_postfix" name="code_postfix" value="<?=get_array_value($redeem_main_detail,"code_postfix","")?>" maxlength="50" onkeyup="generate_example();" <?=$class?> />
							</div>
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/redeem/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){			
		$("#start_date, #expired_date").datepicker({
			format: "yyyy-mm-dd",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
		});

		generate_example();

		<?=@$message?>
		<?=@$js_code?>
	});
</script>