function validator_frm_user(min_username, max_username, min_password, max_password){
		var validator = jQuery("#frm_user").validate({
			rules: {
				username: {
					required: true,
					rangelength: [min_username, max_username]
				},
				email: {
					required: true
				},
				owner_alias:{
					required: true,
					rangelength: [min_username, max_username]
				},
				password:{
					required: true,
					rangelength: [min_password, max_password]
				},
				retype_password:{
					required: true,
					equalTo: "#password"
				}
			},
			messages: {
				username: {
					required: "Username must be "+min_username+"-"+max_username+" characters.",
					rangelength: "Username must be "+min_username+"-"+max_username+" characters."
				},
				email: {
					required: "A valid email address required."
				},
				owner_alias: {
					required: "Shop alias must be "+min_username+"-"+max_username+" characters.",
					rangelength: "Shop alias must be "+min_username+"-"+max_username+" characters."
				},
				password: {
					required: "Password must be "+min_password+"-"+max_password+" characters.",
					rangelength: "Password must be "+min_password+"-"+max_password+" characters."
				},
				retype_password: {
					required: "Password must be "+min_password+"-"+max_password+" characters.",
					equalTo: "Password does not match."
				},
				captcha_code: {
					required: "Corrected character required."
				}
			}
		});	
		
		var validator_2 = jQuery("#frm_login").validate({
			rules: {
				user_name: {
					required: true
				}, 
				user_password: {
					required: true
				}
			},
			messages: {
				user_name: {
					required: "Please enter email/username",
				},
				user_password: {
					required: "Please enter password",
				}
			}
		});

}

function setUserDomain(domain){
	if(domain){
		$('#user_domain_show').html('@'+domain);
		$('#user_domain_name').val('@'+domain);
	}else{
		domain = '@'+$("#domain-dropdown li").first().data('value');
		$('#user_domain_show').html(domain);
		$('#user_domain_name').val(domain);
	}
}

function chackvalidmail(txt){
	var emailblockReg =/^([\w-\.]+@(?!nia.go.th)([\w-]+\.)+[\w-]{2,4})?$/;
		if(!emailblockReg.test(txt)) {
			$('#form_note_2').addClass("hide");
			$('#form_note_1').addClass("hide");
			$('#form_department').removeClass("hide");
			$('#form_note_4').removeClass("hide");
		}else{
			
			$('#form_note_2').removeClass("hide");
			$('#form_note_1').removeClass("hide");
			$('#form_department').addClass("hide");
			$('#form_note_4').addClass("hide");
		}
}