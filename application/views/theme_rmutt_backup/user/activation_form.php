<script type="text/javascript">
	$(document).ready(function() {
		<?=@$message?>
		<?=@$js_code?>
	});
</script>

<div id="da-login-box">
	<div id="da-login-box-header">
		<h1>Need activation?</h1>
	</div>
	<div id="da-login-box-content">
		<form id="frm_actvation" name="frm_actvation" method="POST" action="<?=site_url('activation/verify')?>">	
			<input type="hidden" name="command" id="command" value="_activate" />
			<input type="hidden" name="email" id="email" value="<?=@$email?>" />
			<div id="da-login-input-wrapper">
				<div id="result-msg-box" class="hidden" ></div>
			</div>
		</form>
	</div>
	<div id="da-login-box-footer">
		<?=anchor('login', 'Login', array('class' => 'member-link'));?>
		&nbsp;|&nbsp; <?=anchor('registration', 'Register', array('class' => 'member-link'));?>
		<div id="da-login-tape"></div>
	</div>
</div>