var t = setTimeout(check(),8000)


function check(){
	var sid = Math.floor(Math.random()*10000000000);	 
	var full_url = "/product/issue_controller/ajax_open_issue/"+sid+"/"+aid;
	jQuery.getJSON(full_url, 
		function(data){
			if (null != data) {
				var status = data.status;
				if(status == "success"){
					return "";
				}else{
					$('body').html('');
					var url = data.url;
					if(url == ''){
						window.location.href = '/home';
					}else{
						window.location.href = url;
					}
				}
			}
		}
	);
}
