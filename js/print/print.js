function processSubmit(option){
	$("#frm_ptint").validate();
	if ($("#frm_ptint").valid())
	{
		$("#frm_ptint").submit();
	}
	else
	{
		return false;
	}
}

