function processSubmitMyAccount(form_name){
	$("#"+form_name).validate();
	if ($("#"+form_name).valid())
	{
		
		product_category_ignore_list = new Array();
		$("input[name='product_category_aid[]']").each(function()
		{
			if(!this.checked){
				product_category_ignore_list.push(this.value);
			}
		});

		$('#product_category_ignore_list').val(product_category_ignore_list);
		
		event_category_ignore_list = new Array();
		$("input[name='event_category_aid[]']").each(function()
		{
			if(!this.checked){
				event_category_ignore_list.push(this.value);
			}
		});

		$('#event_category_ignore_list').val(event_category_ignore_list);
		$("#"+form_name).submit();
	}
	else
	{
		return false;
	}
}

function validator_frm_user(){
		var validator = jQuery("#frm_user").validate({
			rules: {
				
				email: {
					required: true
				}
				
			},
			messages: {
				
				email: {
					required: "A valid email address required."
				},
				captcha_code: {
					required: "Corrected character required."
				}
			}
		});	
}