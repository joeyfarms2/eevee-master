function confirm_delete_shelf(product_type_aid,aid,page_selected,show_option,sort_by){
	var sid = Math.floor(Math.random()*10000000000);	  
	
	// var show_option = "shelf";
	// $("input[name='show_option']").each(function()
	// {
	// 	if(this.checked){
	// 		show_option = this.value;
	// 	}	
	// });

	// var sort_by = jQuery("#sort_by").val();
	// var sort_by = "date_d";
	var function_name = "processRedirect('my-bookshelf-vdo/ajax-delete-my-bookshelf/"+sid+"/"+product_type_aid+"/"+aid+"/"+show_option+"/"+sort_by+"/"+"/"+page_selected+"')";
	$('#modal-header').html('Confirm delete vdo from shelf?');
	$('#modal-msg').html('This vdo will be deleted from my bookshelf.');
	$('#modal-button').html('<button onclick="eval('+function_name+')" data-dismiss="modal" class="btn btn-success" type="button">Confirm delete</button><button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>');
	$('#dialog_box').modal({
		backdrop: 'static',
		keyboard: false
	})
}

function show_bookshelf(){
	var url = "my-bookshelf-vdo";
	var show_option = "";
	$("input[name='show_option']").each(function()
	{
		if(this.checked){
			show_option = this.value;
		}	
	});

	if(show_option == "list"){
		url += "-list";
	}
	var sort_by = jQuery("#sort_by").val();
	if(sort_by != ""){
		url += "/sort-"+sort_by;
	}
	processRedirect(url);
}