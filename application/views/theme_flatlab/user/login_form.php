<script type="text/javascript">
	$(document).ready(function() {
		validator_frm_login();
		<?=@$message?>
		<?=@$js_code?>
	});
</script>
<script type="text/javascript" src="<?=JS_PATH?>user/login.js"></script>

<div id="da-login-box">
	<div id="da-login-box-header">
		<h1>Login</h1>
	</div>
	<div id="da-login-box-content">
		<form id="frm_login" name="frm_login" method="POST" action="<?=site_url('login/verify')?>">	
			<div id="da-login-input-wrapper">
				<div id="result-msg-box" class="hidden" ></div>
				<?php if(is_web_service()){ ?>
				<div class="da-login-input">
					<input type="text" id="owner_alias" name="owner_alias" placeholder="Shop alias" class="top required" value="<?=@$owner_alias?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_login\')')" />
				</div>
				<?php } ?>
				
				<div class="da-login-input">
					<input type="text" id="user_name" name="user_name" placeholder="Username" class="middle required" value="<?=@$username?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_login\')')" />
				</div>
				<div class="da-login-input">
					<input type="password" id="user_password" name="user_password" placeholder="Password" class="bottom required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_login\')')" />
				</div>
			</div>
			<div id="da-login-button">
				<input type="submit" value="Login" id="da-login-submit" onClick="processSubmit('frm_login')" />
			</div>
		</form>
	</div>
	<div id="da-login-box-footer">
			<?=anchor('registration', 'Not a member?');?> | <?=anchor('forgot', 'Forgot Password?');?>
		<div id="da-login-tape"></div>
	</div>
</div>