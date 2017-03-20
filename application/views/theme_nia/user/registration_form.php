<?php 
$command = @$command;
$item_detail = @$item_detail;
$master_user_domain = @$master_user_domain;
$master_user_department = @$master_user_department;

?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var min_username = '<?=CONST_MIN_LENGTH_USERNAME?>';
		var max_username = '<?=CONST_MAX_LENGTH_USERNAME?>';
		var min_password = '<?=CONST_MIN_LENGTH_PASSWORD?>';
		var max_password = '<?=CONST_MAX_LENGTH_PASSWORD?>';
		validator_frm_user(min_username, max_username, min_password, max_password);
		setUserDomain(null);
		chackvalidmail(null);
		<?=@$message?>
		<?=@$js_code?>
	});
</script>

<script type="text/javascript" src="<?=JS_PATH?>user/register.js"></script>

<div id="message-box">
	<div id="result-msg-box" class="hidden" ></div>
</div>


		<section id="page">

			<section id="content" class="mt30 mb30">
				
				<div class="container">

					<div class="row">
						
						<div class="col-sm-12">
							<div class="">

								<form id="frm_user" name="frm_user" class="form-horizontal tasi-form" method="POST" action="<?=site_url('registration/save')?>" data-role="validator" novalidate="novalidate"> 
									<input type="hidden" id="command" name="command" value="<?=$command?>" />
									<input type="hidden" id="owner_alias" name="owner_alias" value="0000" />
									<input type="hidden" id="username" name="username" value="" />
									<div class="comment-left">



 									<div class="form-group">
										<label class="control-label col-sm-3 required" for="email">Email</label>
											<div class="col-sm-8 input-group">
												<input type="text" class="required form-control" id="email" name="email" value="<?=get_array_value($item_detail,"email","")?>" onblur="chackvalidmail(this.value);"/>									
											</div>
									</div>

									<?php if(is_specify_password()){ ?>
											<div class="form-group">                                                
												<label class="control-label col-sm-3 required" for="password">Your Password </label>
												<div class="col-sm-8">
												<input type="password" class="form-control required" id="password" name="password" value="" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" />
												<div class=""><i><?=CONST_MIN_LENGTH_PASSWORD?>-<?=CONST_MAX_LENGTH_PASSWORD?> Charactor and allow only a-z, A-Z, 0-9 or - or _</i></div>
												</div>
											</div>

											<div class="form-group">                                                
												<label class="control-label col-sm-3 required" for="retype_password">Confirm Password</label>
												<div class="col-sm-8">
												<input type="password" class="form-control required" id="retype_password" name="retype_password" value="" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" />
												</div>
											</div>
										<?php }else{ ?>
											<input type="hidden" id="password" name="password" value="0000" />
											<input type="hidden" id="retype_password" name="retype_password" value="0000" />						
										<?php } ?>

								<!-- 		<div class="form-group">
											<label class="control-label col-sm-3 required" for="cid">Citizen ID / Passport ID</label>
											<div class="col-sm-8">
											<input type="text" class="form-control required" id="cid" name="cid" value="<?=get_array_value($item_detail,"cid","")?>" />
											</div>
										</div>
 -->
										<div class="form-group">
											<label class="control-label col-sm-3 required" for="first_name_th">First Name</label>
											<div class="col-sm-8">
											<input type="text" class="form-control required" id="first_name_th" name="first_name_th" value="<?=get_array_value($item_detail,"first_name_th","")?>" />
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-3 required" for="last_name_th">Last Name</label>
											<div class="col-sm-8">
											<input type="text" class="form-control required" id="last_name_th" name="last_name_th" value="<?=get_array_value($item_detail,"last_name_th","")?>" />
											</div>
										</div>


										<div class="form-group ">
											<label class="col-md-12 col-lg-3 control-label required" for="gender">Gender</label>
											<div class="col-md-12 col-lg-8">
												<?php $gender =  get_array_value($item_detail,"gender",""); ?>
												<label class="radio-inline">
													<input type="radio" id="gender_female" name="gender" value="f" checked />Female
												</label>
												<label class="radio-inline">
													<input type="radio" id="gender_male" name="gender" value="m" <?php if(get_array_value($item_detail,"gender","") == "m"){ echo 'checked'; } ?> />Male
												</label>
											</div>
										</div>


										
										
																				
										<div class="form-group">
											<label class="control-label col-sm-3 required" for="contact_number">Phone</label>
											<div class="col-sm-8">
											<input type="text" class="form-control required" id="contact_number" name="contact_number" value="<?=get_array_value($item_detail,"contact_number","")?>" />
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-3 required" for="address">Address</label>
											<div class="col-sm-8">
												<textarea class="form-control required" id="address" name="address"><?=get_array_value($item_detail,"address","")?></textarea>
											</div>
										</div>


										<div class="form-group hide" id="form_note_2">
											<label class="control-label col-sm-3 required" for="note_2">Company Name</label>
											<div class="col-sm-8">
												<input type="text" class="form-control required" id="note_2" name="note_2" value="<?=get_array_value($item_detail,"note_2","")?>" />
											</div>
										</div>

										<div class="form-group">  
											<label class="control-label col-sm-3 required" for="position">Job Position</label>
											<div class="col-sm-8">
												<input type="text" class="form-control required" id="position" name="position" value="<?=get_array_value($item_detail,"position","")?>" />
											</div>
										</div>
										<div class="form-group hide" id="form_department">
											<label class="control-label col-sm-3 required" for="department">Department</label>
						
											<div class="col-sm-8">
												<div class="required">
													<?php $department_aid =  get_array_value($item_detail,"department_aid",""); ?>
													<select id="department_aid" name="department_aid" class="form-control required chzn-select" >
														<option value="">Choose Department..</option>
														<?php 
															if(is_var_array($master_user_department)){ 
																foreach($master_user_department as $m_item){

														?>
																<option value="<?=get_array_value($m_item,"aid","")?>" <?php if($department_aid == get_array_value($m_item,"aid","")) echo 'selected="selected"';?>><?=get_array_value($m_item,"name","")?>
																</option>
														<?php
														 	} 
														} 
														?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group hide" id="form_note_4">  
											<label class="control-label col-sm-3 required" for="note_4">Company Phone Number</label>
											<div class="col-sm-8">
												<input type="text" class="form-control required" id="note_4" name="note_4" value="<?=get_array_value($item_detail,"note_4","")?>" />
											</div>
										</div>


										<div class="form-group hide" id="form_note_1">  
											<label class="control-label col-sm-3 required" for="note_1">Department</label>
											<div class="col-sm-8">
												<input type="text" class="form-control required" id="note_1" name="note_1" value="<?=get_array_value($item_detail,"note_1","")?>" />
											</div>
										</div>

										



										<div class="form-group">                                                
											<label class="control-label col-sm-3 required" for="captcha_code">Captcha</label>
											<div class="col-sm-8">
											<img id="captcha" src="<?=INCLUDE_PATH?>securimage/securimage_show.php" alt="Captcha Image" />
											<div class="button" onclick="document.getElementById('captcha').src = '<?=INCLUDE_PATH?>securimage/securimage_show.php?' + Math.random(); return false"><img src="<?=INCLUDE_PATH?>securimage/images/refresh.gif" align="top" title="Click to renew image" />Click to renew image</div>
											<input type="text" name="captcha_code" id="captcha_code" size="10" maxlength="6" class="form-control required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
											</div>
										</div>

								

										
									</div>


									<div class="result"></div>
									<div class="form-group"> 
										<div class="col-sm-6 col-sm-offset-6">
											<button type="submit" name="btn_login" id="btn_login" class="btn btn-primary" onClick="processSubmit('frm_user')">Sign Up</button>
											<button name="btn_cancel" id="btn_cancel" class="btn btn-default" onClick="processRedirect('login')">Cancel</button>
									</div>
								</div>

								</form>
							</div>
						</div>
					</div>
				</div>
		
			</section>
		</section>
