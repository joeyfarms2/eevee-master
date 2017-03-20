<script type="text/javascript">
	$(document).ready(function() {
	
		// $("#frm_forgot").validate({
		// 	rules: {
		// 		user_email: {
		// 			required: true
		// 		},
		// 		captcha_code: {
		// 			required: true
		// 		}
		// 	}
		// });

		<?=@$message?>
		<?=@$js_code?>
	});
</script>


					
		<section id="page">

			<section id="content" class="mt30 mb30">
				<div class="container">

					<div class="row">
						<div class="col-sm-12">
							<div class="">

								<form id="frm_forgot" name="frm_forgot" class="clearfix frm-class" method="POST" action="<?=site_url('forgot/verify')?>" data-role="validator">  
									<div id="result-msg-box" class="hidden" ></div>								
									<div class="comment-left">
										<?php if(is_web_service()){ ?>
										<div class="control-group">
											<label class="required" for="owner_alias">Shop alias:</label>
											<div class="controls">
												<input type="text" id="owner_alias" name="owner_alias" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_forgot\')')" validationmessage="Please enter your name" class="k-textbox" />
											</div>
										</div>
										<?php } ?>
										<div class="form-group">
											<label class="required" for="user_email">Username / Email:</label>
											<input type="text" id="user_email" name="user_email" placeholder="" class="form-control required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_forgot\')')" value="<?=@$forgot_email?>" />
										</div>
										<div class="form-group">                                                
											<label class="required" for="captcha_code">Captcha:</label><BR />
											<img id="captcha" src="<?=INCLUDE_PATH?>securimage/securimage_show.php" alt="Captcha Image" />
											<div class="button" onclick="document.getElementById('captcha').src = '<?=INCLUDE_PATH?>securimage/securimage_show.php?' + Math.random(); return false"><img src="<?=INCLUDE_PATH?>securimage/images/refresh.gif" align="top" title="Click to renew image" />Click to renew image</div>
											<input type="text" id="captcha_code" name="captcha_code" placeholder="" class="form-control required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_forgot\')')" value="" />
										</div>
									</div>
									<div class="clear"></div>
									<p class="frm-button">         
											<button type="submit" name="btn_forgot" id="btn_forgot" class="btn btn-primary" onClick="processSubmit('frm_forgot')"> Reset New Password > </button>
										<?=anchor("login","Login")?> 
										<?php if(CONST_ONLINE_REGIS == '1'){ ?>
											| <?=anchor("registration","Sign Up")?>
										<?php } ?>
									</p>                        
								</form>
							</div>
						</div>
					</div>
				</div>
		
			</section>
		</section>
