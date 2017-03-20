<script type="text/javascript">
	$(document).ready(function($){
	
		$("#frm_user").validate({
			rules: {
				email: {
					required: true,
					email: true
				}
			},
			messages: {
				email: {
					required: "Please enter a valid email address",
					email: "Please enter a valid email address"
				}
			}
		});
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script> 
<?php 
$item_detail = @$item_detail;
$user_avatar = @$user_avatar;
?>
<script type="text/javascript" src="<?=JS_PATH?>user/user.js"></script>

<form id="frm_user" name="frm_user" method="POST" action="<?=site_url('my-account/save')?>" class="da-form">
	<div class="grid_4">
		<div class="da-panel">
			<div class="da-panel-header">
				<span class="da-panel-title">
					<img src="<?=CSS_PATH?>dandelion/images/icons/black/16/pencil.png" alt="" />
					My profile
				</span>
				
			</div>
			<div class="da-panel-content">
				<div class="da-form-inline">
					<div id="result-msg-box" class="hidden" ></div>
					
					<div class="da-form-row">
						<label class="" for="avatar">Avatar</label>
						<div class="da-form-item default">
							<input name="avatar" id="avatar" type="file" />
						</div>
						<BR/>
						<div class="da-form-item auto">
							<?=$user_avatar?>
						</div>
					</div>
											
					<div class="da-form-row">
						<label class="required" for="email">Email</label>
						<div class="da-form-item default">
							<input type="text" id="email" name="email" class="required email" value="<?=get_array_value($item_detail,"email","")?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
						</div>
					</div>
					
					<?php //if(is_specify_username()){ ?>
					<div class="da-form-row">
						<label class="" for="username">Username</label>
						<div class="da-form-item default">
							<input type="text" id="username" name="username" class="" value="<?=get_array_value($item_detail,"username","")?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
						</div>
					</div>
					<?php //} ?>
											
					<div class="da-form-row">
						<label class="" for="first_name_th">First Name</label>
						<div class="da-form-item default">
							<input type="text" id="first_name_th" name="first_name_th" class="" value="<?=get_array_value($item_detail,"first_name_th","")?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
						</div>
					</div>
											
					<div class="da-form-row">
						<label class="" for="last_name_th">Last Name</label>
						<div class="da-form-item default">
							<input type="text" id="last_name_th" name="last_name_th" class="" value="<?=get_array_value($item_detail,"last_name_th","")?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
						</div>
					</div>
											
					<div class="da-button-row">
						<input type="button" value="Save" class="da-button blue large right" onclick="processSubmitOption('frm_user', '0')" />
					</div>
		
				</div>
			</div>
		</div>
	</div>
</form>