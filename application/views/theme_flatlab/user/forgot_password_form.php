<script type="text/javascript">
	$(document).ready(function() {
	
		$("#frm_forgot").validate({
			rules: {
				user_email: {
					required: true,
					email: true
				},
				captcha_code: {
					required: true
				}
			}
		});

		<?=@$message?>
		<?=@$js_code?>
	});
</script>

<div id="da-login-box">
	<div id="da-login-box-header">
		<h1>Forgot your password?</h1>
	</div>
	<div id="da-login-box-content">
		<form id="frm_forgot" name="frm_forgot" method="POST" action="<?=site_url('forgot/verify')?>">	
			<div id="da-login-input-wrapper">
				<div id="result-msg-box" class="hidden" ></div>
				<div class="da-login-input">
					<input type="text" id="user_email" name="user_email" placeholder="Email" class="top required email" onkeypress="isEnterGoTo(event,'processSubmit('frm_forgot')')" value="<?=@$forgot_email?>" />
				</div>
				<div class="da-login-input">
					<input type="text" id="captcha_code" name="captcha_code" placeholder="Captcha" class="bottom required" onkeypress="isEnterGoTo(event,'processSubmit('frm_forgot')')" value="" />
				</div>
				<BR />
				<div class="da-login-input">
					<p><img id="captcha" src="<?=INCLUDE_PATH?>securimage/securimage_show.php" alt="Captcha Image" /></p>
					<p class="button" onclick="document.getElementById('captcha').src = '<?=INCLUDE_PATH?>securimage/securimage_show.php?' + Math.random(); return false"><img src="<?=INCLUDE_PATH?>securimage/images/refresh.gif" align="top" title="Click to renew image" />Click to renew image</p>
				</div>
			</div>
			<div id="da-login-button">
				<input type="submit" value="Submit" id="da-login-submit" onClick="processSubmit('frm_login')" />
			</div>
		</form>
	</div>
	<div id="da-login-box-footer">
			<?=anchor('login', 'Login');?> | 
			<?=anchor('registration', 'Not a member?');?>
		<div id="da-login-tape"></div>
	</div>
</div>