function validator_frm_login(){
	var validator = jQuery("#frm_login").validate({
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
				required: "Please enter username or email",
			},
			user_password: {
				required: "Please enter password",
			},
			owner_alias: {
				required: "Please enter shop alias",
			}
		}
	});

}

function processLogin(){
	jQuery("#frm_login").validate();
	if (jQuery("#frm_login").valid())
	{
		jQuery("#frm_login").attr("action",base_url+"login/verify");
		jQuery("#frm_login").submit();
	}
	else
	{
		return false;
	}
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