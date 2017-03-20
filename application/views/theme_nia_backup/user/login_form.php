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

					<div id="area_form" class="row">

						<div class="col-xs-12">
							<div class="">
								<form id="frm_login" name="frm_login" class="form-horizontal tasi-form" method="POST" action="<?=site_url('login/verify')?>" data-role="validator" novalidate="novalidate">

									<div class="form-group">
										<label class="col-sm-2 control-label col-lg-2">Username</label>
										<div class="col-sm-10 input-group">
											<input type="text" class="required form-control" name="user_name" id="user_name" placeholder=""  title=""/>
											<div class="input-group-btn">
												<input type="hidden" id="user_domain_name" name="user_domain_name" value="" />
												<?php 
													if(is_var_array($master_user_domain)){ 
												?>
													<button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown">
														<span class="current-font" id="user_domain_show">@</span>
														<i class="fa fa-angle-down"></i>
													</button>
													<ul class="dropdown-menu pull-right" id="domain-dropdown">
															<?php foreach($master_user_domain as $item){ ?>
																<li data-value="<?=get_array_value($item,"name","");?>"><a href="javascript:setUserDomain('<?=get_array_value($item,"name","");?>');" ><?=get_array_value($item,"name","");?></a></li>
															<?php } ?>
													</ul>
												<?php } ?>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label col-lg-2">Password</label>
										<div class="col-sm-10 ">
											<div class="iconic-input right">
												<i class="fa fa-lock"></i>
												<input type="password" class="required form-control" name="user_password" id="user_password" placeholder="" title=""/>
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-offset-2 col-md-3">
											<button type="submit" name="btn_login" id="btn_login" class="btn btn-success" onClick="processSubmit('frm_login')"> Login > </button>
										</div>
										<div class="col-sm-6 col-md-7 checkbox">
											<label>
												<input type="checkbox" id="remember" name="remember" value="1"> Remember me
											</label> | 
											<label>
												<a href="<?=site_url('forgot')?>">Forgot Password?</a>
											</label>
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
