<script>
$(document).ready(function(){
	var jsonGlobal;
	var global_product_main_id = 0;
	var main_url = "//bdcloudroom.com:7777/";
	var token = $.cookie('globals');
	var isLogin = $.cookie('isLogin');
	if(!isLogin || token == null || token == "")
	{
		console.log("not login");
		var url = main_url+"users/login";
		$.ajax({
			url: url,
			data:"email=eevee@bookdose.com&password=bookdose",
			type:"post",
			beforeSend: function (request)
            {
            	console.log("sending");
            },
			success: function(data, status) 
			{ 
				console.dir(data);
				if(data.status == "success")
				{
					console.log("status success");
					var mytoken = data.token;
					var expireDate = new Date();
    				expireDate.setDate(expireDate.getDate() + 1);
					$.cookie("globals", mytoken, { expires: expireDate });
					token = mytoken;
					$.cookie("isLogin", 1, { expires: expireDate });
					//load_econtent();

				}
				else{
					console.log("status error");
					$.removeCookie("globals");
					$.cookie("isLogin", 0);
				}
			},
			error: function(xhr, status, exception) 
			{ 
				console.log("error");
				console.log(status);
			}
		});
	}
	else{
		console.log("isLogin");
		//load_econtent();
	}

	function load_econtent(query="", product_main_id)
	{
		var url;
		if(product_main_id == 1)
		{
			
			url = main_url+"ebooks/list";
		}
		else if(product_main_id == 2)
		{
			
			url = main_url+"emagazineIssues/list";
		}
		
		
		//console.log(token);
		$.ajax({
			url: url,
			type:"get",
			data:"status=1"+query,
			headers: {
	        	'Authorization':'Bearer '+token,
	        	'Content-Type':'application/json'
	    	},
			beforeSend: function (request)
	        {
	           	console.log("sending");
	        },
			success: function(data, status) 
			{ 
				console.dir(data);
				if(data.status == "success")
				{
					console.log("status success");
					console.dir(data);
					var json = data.result;
					jsonGlobal = json;
					var html = genTable(json);
					//$("#show_area").html(html);
					$("#show_area").append(html);
					$("#content_area").css({"display":"block"});
				}
				else{
					console.log("status error");
					
				}
			},
			error: function(xhr, status, exception) 
			{ 
				console.log("error");
				console.log(status);
			}
		});
	}


	function load_category(product_main_id)
	{
		var url = main_url+"category/list?status=1&product_main_id="+product_main_id;
		$.ajax({
			url: url,
			type:"get",
			headers: {
	        	'Authorization':'Bearer '+token,
	        	'Content-Type':'application/json'
	    	},
			beforeSend: function (request)
	        {
	           	console.log("sending");
	        },
			success: function(data, status) 
			{ 
				console.dir(data);
				if(data.status == "success")
				{
					var json = data.result;
					var txt = "<option value='all'>All</option>";
					$.each(json, function(i, item){
						txt += "<option value='"+item.name_th+"'>"+item.name_th+"</option>";
					});
					
					if(product_main_id == 1)
					{
						$("#ebook_category").html(txt);
					}
					else if(product_main_id == 2)
					{
						$("#emagazine_category").html(txt);
					}
				}
				else{
					console.log("status error");
					
				}
			},
			error: function(xhr, status, exception) 
			{ 
				console.log("error");
				console.log(status);
			}
		});
	}

	function load_publisher()
	{
		var url = main_url+"publisher/list?status=1";
		$.ajax({
			url: url,
			type:"get",
			headers: {
	        	'Authorization':'Bearer '+token,
	        	'Content-Type':'application/json'
	    	},
			beforeSend: function (request)
	        {
	           	console.log("sending");
	        },
			success: function(data, status) 
			{ 
				if(data.status == "success")
				{
					var json = data.result;
					var txt = "<option value='all'>All</option>";
					$.each(json, function(i, item){
						txt += "<option value='"+item.name+"'>"+item.name+"</option>";
					});
				
					$("#ebook_publisher").html(txt);
				}
				else{
					console.log("status error");
					
				}
			},
			error: function(xhr, status, exception) 
			{ 
				console.log("error");
				console.log(status);
			}
		});
	}

	function load_emagazines()
	{
		var url = main_url+"emagazines/list?status=1";
		$.ajax({
			url: url,
			type:"get",
			headers: {
	        	'Authorization':'Bearer '+token,
	        	'Content-Type':'application/json'
	    	},
			beforeSend: function (request)
	        {
	           	console.log("sending");
	        },
			success: function(data, status) 
			{ 
				if(data.status == "success")
				{
					var json = data.result;
					var txt = "<option value='all'>All</option>";
					$.each(json, function(i, item){
						txt += "<option value='"+item.name+"'>"+item.name+"</option>";
					});
				
					$("#emagazine_emag").html(txt);
				}
				else{
					console.log("status error");
					
				}
			},
			error: function(xhr, status, exception) 
			{ 
				console.log("error");
				console.log(status);
			}
		});
	}
	
	
	function genTable(json)
	{
		var txt = "";

		$.each( json, function(i, item) {
  			txt += "<tr style='height:100px;'>";
  			txt += "<td><input type='checkbox' name='abc' id='";

  			if(global_product_main_id == 1)
  			{
  				txt += item.ISBN;
  				txt += "'/></td>";
  				txt += "<td><img src='"+item.images.cover_image+"' alt='"+item.title+"' style='height:80px; width:auto;' /></td>";
  				txt += "<td><label>"+item.title+"</label></td>";
				txt += "</td></tr>";
  			}
  			else{
  				txt += item.ISSN;
  				txt += "'/></td>";
  				txt += "<td><img src='"+item.images.cover_image+"' alt='"+item.title+"' style='height:80px; width:auto;' /></td>";
  				//txt += "<td><label>"+item.title+" "+item.volume+" "+item.issue+" "+item.issueelse+"</label></td>";
  				txt += "<td><label>";
  				txt += item.title;
  				if (item.volume === undefined || item.volume === null || item.volume == 0) 
  				{
     				// do something 
				}
				else{
					txt += " vol."+item.volume;
				}

				if (item.issue === undefined || item.issue === null || item.issue == 0) 
				{
     				// do something 
				}
				else{
					txt += " issue "+item.issue;
				}

				if (item.issueelse === undefined || item.issueelse === null) 
				{
     				// do something 
				}
				else{
					txt += " "+item.issueelse;	
				}

  				txt += "</label></td>";
				txt += "</td></tr>";
  			}
  			
  				
		});

		txt += "</table>";


		return txt;
	}

	
	//transfer control
	// $("#show_area").delegate("#select_content","click", function(){
	// 	console.log("click!");
	// 	$("#show_area input:checked").each(function(chkboxIndex, chkbox){
	// 		console.log("index = "+chkboxIndex);
	// 	});
	// 	//console.dir(jsonGlobal);
	// });
	$("#select_content").click(function(){
		//console.log("click!");
		var xxx = $("#selected_content").html();

		var html = "";
		$("#show_area input:checked").each(function(chkboxIndex, chkbox){
			//console.log("index = "+chkboxIndex);
			var obj_td = $(this).parent();
			var obj_tr = obj_td.parent();
			if(xxx.indexOf(obj_tr.html())<0)
			{
				html += "<tr>"+obj_tr.html()+"</tr>";
			}
			else{
				//console.log("index = "+xxx.indexOf(obj_tr.html()));
			}
			
		});
		$("#selected_content").append(html);
	});

	$("#get_back").click(function(){
		var xxx = $("#selected_content").html();

		$("#selected_content input:checked").each(function(chkboxIndex, chkbox){
			var obj_td = $(this).parent();
			var obj_tr = obj_td.parent();
			var html = "<tr>"+obj_tr.html()+"</tr>";
			var html_len = html.length;
			var html_point = xxx.indexOf(obj_tr.html());
			xxx = xxx.replace(html, "");
			//console.log(xxx);
		});
		//console.log(xxx);
		$("#selected_content").html(xxx);
	});

	$("#transfer").click(function(){
		var arr = [];
		$("#selected_content input").each(function(index, inputbox){
			//console.log("index = "+index+" | ISBN = "+$(inputbox).attr("id"));
			arr[index] = String($(inputbox).attr("id"));
			//arr[] = $(inputbox).attr("id");
		});

		if(arr.length == 0)
		{
			console.log("no content");
			return;
		}

		var txt = "";
		if(global_product_main_id == 1)
		{
			txt = "isbn[]="+arr;
		}
		else{
			txt = "issn[]="+arr;
		}

		console.dir(arr);
		if(global_product_main_id == 1)
		{
			//ebook
			var url = main_url+"ebooks/list";
			$.ajax({
				url: url,
				type:"get",
				data:txt,
				headers: {
		        	'Authorization':'Bearer '+token,
		        	'Content-Type':'application/json'
		    	},
				beforeSend: function (request)
		        {
		           	console.log("sending");
		        },
				success: function(data, status) 
				{ 
					if(data.status == "success")
					{
						//console.dir(data.result);
						var json = data.result;
						add_book(json);
					}
					else{
						console.log("status error :"+JSON.stringify(data));
						
					}
				},
				error: function(xhr, status, exception) 
				{ 
					console.log("error");
					console.log(status);
				}
			});

		}
		else if(global_product_main_id = 2)
		{
			//emagazine
			var url = main_url+"emagazineIssues/list";
			$.ajax({
				url: url,
				type:"get",
				data:txt,
				headers: {
		        	'Authorization':'Bearer '+token,
		        	'Content-Type':'application/json'
		    	},
				beforeSend: function (request)
		        {
		           	console.log("sending");
		        },
				success: function(data, status) 
				{ 
					console.dir(data.result);
					// if(data.status == "success")
					// {
					// 	//console.dir(data.result);
					// 	var json = data.result;
					// 	add_magazine(json);
					// }
					// else{
					// 	console.log("status error :"+JSON.stringify(data));
						
					// }
				},
				error: function(xhr, status, exception) 
				{ 
					console.log("error");
					console.log(status);
				}
			});
		}

	});
	//tabs control
	$(".tablinks").click(function(){
		var aid = $(this).attr("aid");

		if(aid == "ebook")
		{
			global_product_main_id = 1;
			load_category(1);
			load_publisher();
		}
		else if(aid == "emagazine")
		{
			global_product_main_id = 2;
			load_category(2);
			load_emagazines();
		}
		// Get all elements with class="tabcontent" and hide them
	 	$(".tabcontent").css({"display":"none"});

	    // Get all elements with class="tablinks" and remove the class "active"
		$(".tablinks").removeClass("active");

	    // Show the current tab, and add an "active" class to the link that opened the tab
	    $("#"+aid).css({"display":"block"});
	    $(this).addClass("active");
	    $("#show_area").html("");
	    $("#content_area").css({"display":"none"});
	    jsonGlobal = {};

	});

	$("#search_ebook").click(function(){
		var query = "";

		var title = $("#ebook_title").val();
		if(title != "")
		{
			query += "&title="+title;
		}
		var isbn = $("#ebook_isbn").val();
		if(isbn != "")
		{
			query += "&isbn="+isbn;
		}
		var cat = $("#ebook_category").val();
		if(cat != "")
		{
			if(cat != "all")
			{
				query += "&category="+cat;
			}
		}
		var pub = $("#ebook_publisher").val();
		if(pub != "")
		{
			if(pub != "all")
			{
				query += "&publisher="+pub;
			}
		}
		var license = $('input[name="ebook_license"]:checked').val();
		if(license != "")
		{
			if(license != 9)
			{
				query += "&license="+license;
			}
		}
		
		$("#show_area").html("");
		//console.log(query);
		
		load_econtent(query, 1);

	});

	$("#search_emagazine").click(function(){
		var query = "";

		var title = $("#emagazine_emag").val();
		if(title != "")
		{
			if(title != "all")
			{
				query += "&title="+title;
			}
		}
		var issn = $("#emagazine_issn").val();
		if(issn != "")
		{
			query += "&issn="+issn;
		}
		var cat = $("#emagazine_category").val();
		if(cat != "")
		{
			if(cat != "all")
			{
				query += "&category="+cat;
			}
		}
		var license = $('input[name="emagazine_license"]:checked').val();
		if(license != "")
		{
			if(license != 9)
			{
				query += "&license="+license;
			}
		}
		$("#show_area").html("");
		//console.log(query);
		
		load_econtent(query, 2);
	});

	function add_magazine(myArray)
	{
		console.log("Add Magazine");
		var myjson = [];
		$.each(myArray, function(i, json){
			myjson[i] =  JSON.stringify(json);
		});
		console.dir(myjson);
		var url = "/webservice/add-magazine-and-magazine-copy";
		$.ajax({
			url: url,
			type:"post",
			data:{"json":myjson},
			headers: {
		       	
		   	},
			beforeSend: function (request)
	        {
	        	
		    },
			success: function(data, status) 
			{ 
				//console.log("success");
				console.log(data);
				alert("success");
			},
			error: function(xhr, status, exception) 
			{ 
				console.log("error | "+JSON.stringify(xhr)+" | "+status+" | "+exception);
				alert("error");
			}

		});
	}

	function add_book(myArray)
	{
		
		var myjson = [];
		$.each(myArray, function(i, json){
			myjson[i] =  JSON.stringify(json);
		});
		console.dir(myjson);
		var url = "/webservice/add-book-and-book-copy";
		$.ajax({
			url: url,
			type:"post",
			data:{"json":myjson},
			headers: {
		       	
		   	},
			beforeSend: function (request)
	        {
	        	console.log("sending");
		    },
			success: function(data, status) 
			{ 
				//console.log("success");
				//console.log(data);
				alert("success");
			},
			error: function(xhr, status, exception) 
			{ 
				console.log("error | "+JSON.stringify(xhr)+" | "+status+" | "+exception);
				alert("error");
			}

		});
		/*
		$.each(myArray, function(i, json){
			console.log("#######################################");
			console.log("i = "+i);
			var publisher_name = json.publisher;
			var url = "/admin/publisher/add-if-not-exist";
			$.ajax({
				url: url,
				type:"post",
				data:"name="+publisher_name,
				async:false,
				headers: {
		        	'Content-Type':'application/json'
		    	},
				beforeSend: function (request)
		        {
		           	//console.log("sending");
		        },
				success: function(data, status) 
				{ 
					var mydata = JSON.parse(data);
					var publisher_aid = parseInt(mydata.aid);
					console.log("pub aid = "+publisher_aid);
					if(publisher_aid > 0)
					{
						//add book parent
						var txt_input = "";
						txt_input += "product_main_aid=3";
						txt_input += "&product_type_aid=1";
						txt_input += "&status=0";
						txt_input += "&publisher_aid="+publisher_aid;
						txt_input += "&cover_image="+json.images.cover_image;
						txt_input += "&thumbnail_image="+json.images.thumbnail_image;
						txt_input += "&large_image="+json.images.large_image;
						txt_input += "&bcc_status=1";
						txt_input += "&field_1="+json.title;
						txt_input += "&field_2="+json.author[0].name;
						txt_input += "&field_3="+json.description;
						txt_input += "&field_4="+json.pages;
						txt_input += "&field_22="+json.ISBN;
						console.log("xxx = "+txt_input);
						$.ajax({
							url: "/admin/product/book/add_new_book",
							type: "post",
							data:txt_input,
							async:false,
							headers: {
					        	
					    	},
							beforeSend: function (request)
					        {
					           	//console.log("sending add book parent");
					        },
							success: function(data, status) 
							{
								console.log("add book parent success")
								var bookdata = JSON.parse(data);
								//console.dir(bookdata);
								var parent_aid = bookdata.parent_aid;
								if(parent_aid > 0)
								{
									//add book parent
									var txt_input = "";
									txt_input += "product_main_aid=3";
									txt_input += "&product_type_aid=1";
									txt_input += "&parent_aid="+parent_aid;
									txt_input += "&status=1";
									txt_input += "&bcc_status=1";
									txt_input += "&cover_price="+json.cover_price;
									txt_input += "&type=1";//1=digital, 2=paper
									
									if(parseInt(json.is_license) == 1)
									{
										txt_input += "&ebook_concurrence=2";
										txt_input += "&rental_period=5";
										txt_input += "&possession=2";//1=buy out, 2=rental
										//txt_input += "&type_minor=7";//6=license+free, 7=license+rental
									}
									else{
										txt_input += "&possession=1";//1=buy out, 2=rental
										//txt_input += "&type_minor=3";//3=free, 4=sale, 5=rental
									}
									
									txt_input += "&is_ebook=1";

									console.log("zzz = "+txt_input);
									$.ajax({
										url: "/admin/product/book-copy/add_new_book",
										type: "post",
										data:txt_input,
										async:false,
										headers: {
								        	
								    	},
										beforeSend: function (request)
								        {
								           	//console.log("sending add book parent");
								        },
										success: function(data, status) 
										{
											console.log("add book copy success")
											var bookdata = JSON.parse(data);
											//console.dir(bookdata);
											var copy_aid = bookdata.copy_aid;
											console.log("copy_aid = "+copy_aid);
											console.log("$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");
										},
										error: function(xhr, status, exception) 
										{ 
											console.log("error");
											console.log(status);
										}

									});
								}
								else{
									console.log("error status: publisher/add-if-not-exist");
									console.log(JSON.stringify(data));
								}
							},
							error: function(xhr, status, exception) 
							{ 
								console.log("error");
								console.log(status);
							}

						});
					}
					else{
						console.log("error status: publisher/add-if-not-exist");
						console.log(JSON.stringify(data));
					}
				},
				error: function(xhr, status, exception) 
				{ 
					console.log("error: publisher/add-if-not-exist");
					console.log(status);
				}
			});
		});
		*/
	}
});

</script>
<style>
	/* Style the list */
	ul.tab {
	    list-style-type: none;
	    margin: 0;
	    padding: 0;
	    overflow: hidden;
	    border: 1px solid #ccc;
	    background-color: #f1f1f1;
	}

	/* Float the list items side by side */
	ul.tab li {float: left;}

	/* Style the links inside the list items */
	ul.tab li a {
	    display: inline-block;
	    color: black;
	    text-align: center;
	    padding: 14px 16px;
	    text-decoration: none;
	    transition: 0.3s;
	    font-size: 17px;
	}

	/* Change background color of links on hover */
	ul.tab li a:hover {background-color: #ddd;}

	/* Create an active/current tablink class */
	ul.tab li a:focus, .active {background-color: #ccc;}

	/* Style the tab content */
	.tabcontent {
	    display: none;
	    padding: 6px 12px;
	    border: 1px solid #ccc;
	    border-top: none;
	}

	.abc 
	{
		height: auto;
		float: left;
		max-height: 500px;
		overflow: auto;
	}
	.abc-box
	{
		width:45%;
		border: 1px solid #cccccc;
		padding: 5px;
	}
	.abc-action
	{
		width:10%;
	}
	.abc-action div
	{
		margin-top: 230px;
	}
</style>
<ul class="tab">
  <li><a href="#" class="tablinks" aid="ebook">Ebook</a></li>
  <li><a href="#" class="tablinks" aid="emagazine">Emagazine</a></li>
</ul>
<div id="ebook" class="tabcontent">
	<h3>Search Ebook</h3>
	<p><b>Title</b></p>
	<p><input class="form-control" type="text" id="ebook_title" value="" /></p>
	<p><b>ISBN</b></p>
	<p><input class="form-control" type="text" id="ebook_isbn" value="" /></p>
	<p><b>Choose Category</b></p>
	<p><select class="form-control" id="ebook_category"></select></p>
	<p><b>Choose Publisher</b></p>
	<p><select class="form-control" id="ebook_publisher"><option>1</option><option>2</option></select></p>
	<p><b>License</b></p>
	<p><input type="radio" name="ebook_license" value="9" checked/> All &nbsp; <input type="radio" name="ebook_license" value="1"/> License &nbsp; <input type="radio" name="ebook_license" value="0"/> Unlicense</p>
	<p><button class="btn btn-info" id="search_ebook">Submit</button></p>
</div>

<div id="emagazine" class="tabcontent">
	<h3>Search Emagazine</h3>
	<p><b>ISSN</b></p>
	<p><input class="form-control" type="text" id="emagazine_issn" value="" /></p>
	<p><b>Choose Category</b></p>
	<p><select class="form-control" id="emagazine_category"><option>1</option><option>2</option></select></p>
	<p><b>Choose Magazine Title</b></p>
	<p><select class="form-control" id="emagazine_emag"></select></p>
	<p><b>License</b></p>
	<p><input type="radio" name="emagazine_license" value="9" checked/> All &nbsp; <input type="radio" name="emagazine_license" value="1"/> License &nbsp; <input type="radio" name="emagazine_license" value="0"/> Unlicense</p>
	<p><button class="btn btn-info" id="search_emagazine">Submit</button></p>
</div>

<div id="content_area" style="display:none;">
	<div style='width:100%'>
		<div class='abc abc-box'>
			<center><h3>e-content from server</h3></center><br />
			<table style='width:100%;' id="show_area">
				<!-- <th colspan='3' style='text-align:center'>ebooks from server</th> -->
			</table>
		</div>
		<div class='abc abc-action'>
			<div>
				<p><center><button id='select_content'> >> </button></center></p>
				<p><center><button id='get_back'> << </button></center></p>
			</div>
		</div>
		<div class='abc abc-box'>
			<center><h3>selected ebooks</h3></center><br />
			<table style='width:100%;' id="selected_content">
				<!-- <th colspan='3' style='text-align:center'>selected ebooks</th> -->
			</table>
			<p><center><button class='btn btn-success' id='transfer'>Start to Transfer</button></center></p>
		</div>
	</div>
</div>