<?php 
$master_user_domain = @$master_user_domain;

?>
<script type="text/javascript" src="<?=JS_PATH?>user/login.js"></script>

<div id="message-box">
	<div id="result-msg-box" class="hidden mt20" ></div>
</div>

		<section id="page" class="box-login">

			<section id="content" class="mt30 mb30">
				<div class="container">

					<div class="row">

						<div class="col-md-12">
							<div class="">

								<form id="frm_login" name="frm_login" class="form-horizontal tasi-form" method="POST" action="<?=site_url('login/verify')?>" data-role="validator" novalidate="novalidate">

									<div class="form-group">
										<div class="col-md-2 input-group">
											<label class="control-label">Username</label>
										</div>
										<div class="col-md-10">
											<input type="text" class="required form-control" name="user_name" id="user_name" placeholder=""  title=""/>
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-2 input-group">
											<label class="control-label">Password</label>
										</div>
										<div class="col-md-10">
											<div class="iconic-input right">
												<i class="fa fa-lock"></i>
												<input type="password" class="required form-control" name="user_password" id="user_password" placeholder="" title=""/>
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-2 col-md-offset-2 .col-md-push-3">
											<button type="submit" name="btn_login" id="btn_login" class="btn btn-success" onClick="processSubmit('frm_login')"> Login > </button>
										</div>
										<div class="col-sm-6  .col-md-pull-3 checkbox">
											
												<input type="checkbox" id="remember" name="remember" value="1"> Remember me
											 | 
											
												<a href="<?=site_url('forgot')?>">Forgot Password?</a>
											
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

				</div>
			</section>
		</section>

<script type="text/javascript">
	$(document).ready(function() {
		// validator_frm_login();
		setUserDomain(null);
		<?=@$message?>
		<?=@$js_code?>
	});
</script>
