<script type="text/javascript">
	$(document).ready(function() {
	
		$("#frm_forgot").validate({
			rules: {
				new_password: {
					required: true,
					rangelength: [4, 12]
				},
				retype_password: {
					required: true,
					equalTo: "#new_password"
				}
			},
			messages: {
				retype_password: {
					equalTo: "Password does not match"
				},
				new_password: {
					rangelength: "Password must be 4-12 characters"
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
		<form id="frm_forgot" name="frm_forgot" method="POST" action="<?=site_url('forgot/change/save')?>">	
			<input type="hidden" id="aid" name="aid" value="<?=@$aid?>" />
			<div id="da-login-input-wrapper">
				<div id="result-msg-box" class="hidden" ></div>
				<div class="da-login-input">
					<input type="password" name="new_password" id="new_password" placeholder="New password" class="required" value="" onkeypress="isEnterGoTo(event,'processSubmit('frm_forgot')')" />
				</div>
				<div class="da-login-input">
					<input type="password" name="retype_password" id="retype_password" placeholder="Re-type password" class="required" value="" onkeypress="isEnterGoTo(event,'processSubmit('frm_forgot')')" />
				</div>
			</div>
			<div id="da-login-button">
				<input type="submit" value="Change password" id="da-login-submit" onClick="processSubmit('frm_forgot')" />
			</div>
		</form>
	</div>
	<div id="da-login-box-footer">
		<div id="da-login-tape"></div>
	</div>
</div>