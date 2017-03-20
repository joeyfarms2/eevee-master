<script type="text/javascript">
	$(document).ready(function() {
	
		$("#frm_user").validate({
			rules: {
				new_password_2: {
					required: true,
					rangelength: [4, 12]
				},
				retype_password: {
					required: true,
					equalTo: "#new_password_2"
				}
			},
			messages: {
				retype_password: {
					equalTo: "Password does not match"
				},
				new_password_2: {
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
		<h1>Change password</h1>
	</div>
	<div id="da-login-box-content">
		<form id="frm_user" name="frm_user" method="POST" action="<?=site_url('change-password/save')?>">	
			<div id="da-login-input-wrapper">
				<div id="result-msg-box" class="hidden" ></div>
				<div class="da-login-input">
					<input type="password" id="old_password" name="old_password" placeholder="Old password" class="top required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
				</div>
				<div class="da-login-input">
					<input type="password" id="new_password_2" name="new_password_2" placeholder="New password" class="middle required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
				</div>
				<div class="da-login-input">
					<input type="password" id="retype_password" name="retype_password" placeholder="Retype-password" class="bottom required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
				</div>
			</div>
			<div id="da-login-button">
				<input type="submit" value="Submit" id="da-login-submit" onClick="processSubmit('frm_user')" />
			</div>
		</form>
	</div>
	<div id="da-login-box-footer">
			<?=anchor('my-account', 'Not change now.');?>
		<div id="da-login-tape"></div>
	</div>
</div>