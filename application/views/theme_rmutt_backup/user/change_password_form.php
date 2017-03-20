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

<div id="message-box">
	<div id="result-msg-box" class="hidden" ></div>
</div>

		<section id="page">

			<section id="content" class="mt30 mb30">
				<div class="container">

					<div class="row">
						<div class="col-sm-12">
							<div class="">

								<form id="frm_user" name="frm_user" class="" method="POST" action="<?=site_url('change-password/save')?>" data-role="validator" novalidate="novalidate"> 
									<div id="result-msg-box" class="hidden" ></div>								
									<div class="comment-left">
										
										<div class="form-group">                                                
											<label class="required" for="old_password">Old Password:</label>
											<input type="password" class="form-control required" id="old_password" name="old_password" value="" maxlength="" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
										</div>

										<div class="form-group">                                                
											<label class="required" for="new_password_2">New Password: <i><?=CONST_MIN_LENGTH_PASSWORD?>-<?=CONST_MAX_LENGTH_PASSWORD?> Charactor</i></label>
											<input type="password" class="form-control required" id="new_password_2" name="new_password_2" value="" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
										</div>

										<div class="form-group">                                                
											<label class="required" for="retype_password">New Password again:</label>
											<input type="password" class="form-control required" id="retype_password" name="retype_password" value="" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
										</div>
																				
									</div>
									<div class="clear"></div>
									<p class="frm-button">         
										<button type="submit" name="btn_change" id="btn_login" class="btn btn-primary" onClick="processSubmit('frm_user')"> Change password > </button>
										<button type="button" name="btn_cancel" id="btn_login" class="btn btn-danger" onClick="processRedirect('home')"> Cancel > </button>
									</p>                        
								</form>
							</div>
						</div>
					</div>
				</div>
		
			</section>
		</section>
		
