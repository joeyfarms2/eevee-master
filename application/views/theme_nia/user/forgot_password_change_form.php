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
		<section id="page">

			<section id="content" class="mt30 mb30">
				<div class="container">

					<div class="row">
						<div class="col-sm-12">
							<div class="">

								<form id="frm_forgot" name="frm_forgot" class="clearfix frm-class" method="POST" action="<?=site_url('forgot/change/save')?>" data-role="validator">  
									<input type="hidden" id="aid" name="aid" value="<?=@$aid?>" />
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
											<label class="required" for="new_password">New password: <i><?=CONST_MIN_LENGTH_PASSWORD?>-<?=CONST_MAX_LENGTH_PASSWORD?> Charactor</i></label>
											<input type="password" id="new_password" name="new_password" placeholder="" class="form-control required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_forgot\')')" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" />
										</div>
										<div class="form-group">
											<label class="required" for="retype_password">New password again</label>
											<input type="password" id="retype_password" name="retype_password" placeholder="" class="form-control required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_forgot\')')" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" />
										</div>
									</div>
									<div class="clear"></div>
									<p class="frm-button">         
											<button type="submit" name="btn_change" id="btn_change" class="btn btn-primary" onClick="processSubmit('frm_forgot')"> Change password > </button>

											<button type="submit" name="btn_cancel" id="btn_cancel" class="btn btn-danger" onClick="processRedirect('home')"> Cancel > </button>
									</p>                        
								</form>
							</div>
						</div>
					</div>
				</div>
		
			</section>
		</section>