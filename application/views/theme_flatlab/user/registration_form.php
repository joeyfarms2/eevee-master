<?php 
$command = @$command;
$item_detail = @$item_detail;
?>
<script type="text/javascript">
	jQuery(document).ready(function($){	
		var min_username = '<?=CONST_MIN_LENGTH_USERNAME?>';
		var max_username = '<?=CONST_MAX_LENGTH_USERNAME?>';
		var min_password = '<?=CONST_MIN_LENGTH_PASSWORD?>';
		var max_password = '<?=CONST_MAX_LENGTH_PASSWORD?>';
		validator_frm_user(min_username, max_username, min_password, max_password);
		<?=@$message?>
		<?=@$js_code?>
	});
</script>
<script type="text/javascript" src="<?=JS_PATH?>user/register.js"></script>

<div id="da-login-box">
	<div id="da-login-box-header">
		<h1>Register</h1>
	</div>
	<div id="da-login-box-content">
		<form id="frm_user" name="frm_user" method="POST" action="<?=site_url('registration/save')?>">
			<input type="hidden" id="command" name="command" value="<?=$command?>" />
			<div id="da-login-input-wrapper">
				<div id="result-msg-box" class="hidden" ></div>
				<div class="da-login-input">
					<input type="text" id="email" name="email" placeholder="Email address" class="top required email" value="<?=get_array_value($item_detail,"email","")?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
				</div>
				
				<?php if(is_web_service()){ ?>
					<div class="da-login-input">
						<input type="text" id="owner_alias" name="owner_alias" placeholder="Shop alias" class="middle required" value="<?=get_array_value($item_detail,"owner_alias","")?>" onkeypress="isKeyUsername(event);isEnterGoTo(event,'processSubmit(\'frm_user\')')" maxlength="<?=CONST_MAX_LENGTH_USERNAME?>" />
					</div>
				<?php }else{ ?>
					<input type="hidden" id="owner_alias" name="owner_alias" value="0000" />
				<?php } ?>
				
				<?php if(is_specify_username()){ ?>
					<div class="da-login-input">
						<input type="text" id="username" name="username" placeholder="Username" class="middle required" value="<?=get_array_value($item_detail,"username","")?>" onkeypress="isKeyUsername(event);isEnterGoTo(event,'processSubmit(\'frm_user\')')" maxlength="<?=CONST_MAX_LENGTH_USERNAME?>" />
					</div>
				<?php }else{ ?>
					<input type="hidden" id="username" name="username" value="0000" />
				<?php } ?>
				
				<?php if(is_specify_password()){ ?>
					<div class="da-login-input">
						<input type="text" id="password" name="password" placeholder="Password" class="middle required" value="" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" />
					</div>
					
					<div class="da-login-input">
						<input type="text" id="retype_password" name="retype_password" placeholder="Password again" class="middle required" value="" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" />
					</div>
				<?php }else{ ?>
					<input type="hidden" id="password" name="password" value="0000" />
					<input type="hidden" id="retype_password" name="retype_password" value="0000" />						
				<?php } ?>
				
				<div class="da-login-input">
					<input type="text" id="captcha_code" name="captcha_code" placeholder="Captcha" class="bottom required" onkeypress="isEnterGoTo(event,'processSubmit('frm_user')')" value="" />
				</div>
				<BR />
				<div class="da-login-input">
					<p><img id="captcha" src="<?=INCLUDE_PATH?>securimage/securimage_show.php" alt="Captcha Image" /></p>
					<p class="button" onclick="document.getElementById('captcha').src = '<?=INCLUDE_PATH?>securimage/securimage_show.php?' + Math.random(); return false"><img src="<?=INCLUDE_PATH?>securimage/images/refresh.gif" align="top" title="Click to renew image" />Click to renew image</p>
				</div>
				

			</div>
			<div id="da-login-button">
				<input type="submit" value="Register" id="da-login-submit" onClick="processSubmit('frm_user')" />
			</div>
		</form>
	</div>
	<div id="da-login-box-footer">
			<?=anchor('login', 'Login');?> | <?=anchor('forgot', 'Forgot Password?');?>
		<div id="da-login-tape"></div>
	</div>
</div>