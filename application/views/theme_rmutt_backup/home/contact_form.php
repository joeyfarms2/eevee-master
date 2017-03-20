<?php 
$command = @$command;
$item_detail = @$item_detail;
?>
<script>
function check()
{
if(document.frm_contact.first_name_th.value == "")
{
    //alert("Please enter First Name");
    document.frm_contact.first_name_th.focus();
    return false;
}else if(document.frm_contact.last_name_th.value == "")
{
    //alert("Please enter First Name");
    document.frm_contact.last_name_th.focus();
    return false;
}else if(document.frm_contact.email.value == "")
{
    //alert("Please enter First Name");
    document.frm_contact.email.focus();
    return false;
}else if(document.frm_contact.subject.value == ""){
//alert("Please enter Subject");
document.frm_contact.subject.focus();
    return false;
}else if(document.frm_contact.message.value == ""){
//alert("Please enter Message");
document.frm_contact.message.focus();
    return false;
}
else if(document.frm_contact.captcha_code.value == ""){
//alert("Please enter Message");
document.frm_contact.captcha_code.focus();
    return false;
}
else
{
    document.frmMr.submit();
}
}
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
								<form id="frm_contact" name="frm_contact" class="form-horizontal tasi-form" method="POST" onsubmit="return check();" action="<?=site_url('ask-librarian-save')?>" data-role="validator"> 
									<input type="hidden" id="command" name="command" value="<?=$command?>" />
									<div class="comment-left">
										<div class="form-group">
											<label class="control-label col-sm-12 required" for="first_name_th">First Name</label>
											<div class="col-sm-12">
											<input type="text" class="form-control required" id="first_name_th" name="first_name_th" value="<?=get_array_value($item_detail,"first_name_th",getUserLoginFirstNameTh($user_login_info))?>" />
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-12 required" for="last_name_th">Last Name</label>
											<div class="col-sm-12">
											<input type="text" class="form-control required" id="last_name_th" name="last_name_th" value="<?=get_array_value($item_detail,"last_name_th",getUserLoginLastNameTh($user_login_info))?>" />
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-sm-12 required" for="contact_topic_aid">Topic Area</label>
											<div class="col-sm-12">
												<select class="form-control required" id="contact_topic_aid" name="contact_topic_aid">
													<?php 
														$this_topic_url = @$this_topic_url;
														if(is_var_array(@$master_contact_topic)){
															foreach($master_contact_topic as $item){
																$url = get_array_value($item,"url","none");
													?>
														<option value="<?=get_array_value($item,"aid","0")?>" <?=($url == $this_topic_url) ? "selected" : "";?>><?=get_array_value($item,"name","N/A")?></option>
													<?php } ?>
													<?php } ?>
												</select>
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-12 required" for="email">Email Address</label>
											<div class="col-sm-12">
											<input type="text" class="form-control required" id="email" name="email" value="<?=get_array_value($item_detail,"email",getUserLoginEmail($user_login_info))?>" />
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-12 required" for="subject">Subject</label>
											<div class="col-sm-12">
											<input type="text" class="form-control required" id="subject" name="subject" value="<?=get_array_value($item_detail,"subject","")?>" />
											</div>
										</div>
										
										<div class="form-group">
											<label class="control-label col-sm-12 required" for="message">Comment (include any additional URLs necessary)</label>
											<div class="col-sm-12">
											<textarea class="form-control" id="message" name="message"><?=get_array_value($item_detail,"message","")?></textarea>
											</div>
										</div>

										<div class="form-group">                                                
											<label class="control-label col-sm-3 required" for="captcha_code">Captcha</label>
											<div class="col-sm-9">
											<img id="captcha" src="<?=INCLUDE_PATH?>securimage/securimage_show.php" alt="Captcha Image" />
											<div class="button" onclick="document.getElementById('captcha').src = '<?=INCLUDE_PATH?>securimage/securimage_show.php?' + Math.random(); return false"><img src="<?=INCLUDE_PATH?>securimage/images/refresh.gif" align="top" title="Click to renew image" />Click to renew image</div>
											<input type="text" name="captcha_code" id="captcha_code" size="10" maxlength="6" class="form-control required" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
											</div>
										</div>
															
									</div>
									<div class="result"></div>
									<div class="form-group"> 
										<div class="col-sm-12 a-right">
											<button type="submit" name="btn_login" id="btn_login" class="btn btn-primary" onClick="processSubmit('frm_contact')">Submit</button>
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
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>