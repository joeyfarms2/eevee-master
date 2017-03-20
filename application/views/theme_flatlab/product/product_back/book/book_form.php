<?php  
$command = @$command;
$parent_detail = @$parent_detail;

$product_main_result = @$product_main_result;
$product_main_aid = get_array_value($product_main_result,"aid","");
$product_main_url = get_array_value($product_main_result,"url","");
?>
<script type="text/javascript" src="<?=JS_PATH?>product/product_back/product_back_init.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/book.js"></script>
<form id="frm_product" name="frm_product" method="POST" action="<?=site_url('admin/product/book/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($parent_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="product_main_aid" name="product_main_aid" value="<?=$product_main_aid?>" />
	<input type="hidden" id="product_type_aid" name="product_type_aid" value="<?=@$this_product_type_aid?>" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_menu.php'); ?>
				<header class="panel-heading no-radius fieldset">
					<?=$this->lang->line('product_menu_general_info')?> <!-- General Info -->
				</header>

				<div class="panel-body">
					<?php if(is_root_admin_or_higher() && $command == "_update"){ ?>
						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label required" for="aid_readonly">Product Code</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control readonly" type="text" id="aid_readonly" name="aid_readonly" value="<?=get_text_pad(get_array_value($parent_detail,"aid","0"))?>" readonly />
							</div>
						</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($parent_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="category">Category</label>
						<div class="col-md-12 col-lg-8">
							<input type="hidden" name="category" id="category" value="<?=get_array_value($parent_detail,"category","")?>" />
							<div id="category_area"></div>
							<div id="category_error_area" ></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="option">Option</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="is_new" name="is_new" value="1" <?php if(get_array_value($parent_detail,"is_new","") == "1") echo 'checked="checked"';?> />Most Popular
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_recommended" name="is_recommended" value="1" <?php if(get_array_value($parent_detail,"is_recommended","") == "1") echo 'checked="checked"';?> />Librarian's Choices
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_home" name="is_home" value="1" <?php if(get_array_value($parent_detail,"is_home","") == "1") echo 'checked="checked"';?> />New Releases
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="weight">Weight</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="weight" name="weight" value="<?=get_array_value($parent_detail,"weight","")?>" onkeypress="isWeight(event, this.value)" maxlength="6" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="publisher">Publisher</label>
						<div class="col-md-12 col-lg-8">
							<?php $publisher_aid =  get_array_value($parent_detail,"publisher_aid",""); ?>
							<select id="publisher_aid" name="publisher_aid" class="form-control chzn-select" >
								<option value="">Choose publisher..</option>
								<?php 
									if(is_var_array($master_publisher)){ 
										foreach($master_publisher as $m_item){

								?>
										<option value="<?=get_array_value($m_item,"aid","")?>" <?php if($publisher_aid == get_array_value($m_item,"aid","")) echo 'selected="selected"';?>><?=get_array_value($m_item,"name","")?></option>
								<?php } } ?>
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Publish date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="publish_date" name="publish_date" value="<?=get_array_value($parent_detail,"publish_date","")?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('publish_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<?php
						if($product_main_aid == "7"){
					?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Expiration date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="expired_date" name="expired_date" value="<?=get_array_value($parent_detail,"expired_date","")?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('expired_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<?php
						}
					?>

					<?php if(CONST_HAS_REWARD_POINT){ ?>
					<div class="form-group hidden">
						<label class="col-md-12 col-lg-2 control-label" for="reward_point">Reward point</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="reward_point" name="reward_point" value="<?=get_array_value($parent_detail,"reward_point","")?>" />
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cover_image">Cover image</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="cover_image" name="cover_image"/>
							<p class="help-block">Only file extension .jpg, .jpeg, .png, .gif and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_IMAGE)?>.  <span style="color:red;">( width : 320px * height : 450px )</span></p>
						</div>
						<?php $img_src = get_image(get_array_value($parent_detail,"cover_image_small_path",""),"small","off"); ?>
						<?php if(!is_blank($img_src)){ ?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
							<img src="<?=$img_src?>" />
						</div>
						<?php } ?>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="tag">Keyword</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="tag" name="tag" value="<?=get_array_value($parent_detail,"tag","")?>" />
						</div>
					</div>

				</div>
			</section>

			<section class="panel">
				<header class="panel-heading fieldset">
					<span>Marc Information</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span><a class="btn btn-warning" data-toggle="modal" data-target="#myModal" data-backdrop="static" data-keyboard="false">import marc record</a></span>
				</header>
				<div class="panel-body">
					<div id="marc_field_list_area"></div>
				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_product', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-<?=$product_main_url?>/book');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  	<div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	<h4 class="modal-title">Import Marc Record</h4>
	      	</div>
	      	<div class="modal-body">
	        	<ul class="nav nav-tabs">
		    		<li class="active"><a data-toggle="tab" href="#candp">Copy & Paste</a></li>
		    		<li><a data-toggle="tab" href="#uploadfile">Upload File</a></li>
		  		</ul>
		  		<div class="tab-content">
		  			<div id="candp" class="tab-pane fade in active">
		  				<p></p>
      					<p><textarea id="marc" style="width:100%; height:300px;"></textarea></p>
      					<p class="text-right"><button id="textarea_btn" class="btn btn-danger">Import</button></p>
    				</div>
    				<div id="uploadfile" class="tab-pane fade">
    					<p></p>
      					<p><input type="file" id="file" /></p>
      					<p class="text-right"><button id="file_btn" class="btn btn-danger">Import</button></p>
    				</div>
		  		</div>
	      	</div>
	      	<div class="modal-footer">
	        	<?php /*<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>*/ ?>
	      	</div>
	    </div>

  	</div>
</div>
<style>
.nav-tabs > li 
{
	margin-bottom: -1px; 
}
</style>
<!-- End Modal -->

<script type="text/javascript">
	jQuery(document).ready(function($){

		const_has_reward_point = '<?=CONST_HAS_REWARD_POINT?>';
		const_reward_point = '<?=CONST_REWARD_POINT?>';

		$("#frm_product").validate({
			errorPlacement: function(error, element) {
				if (element.attr('name') == "category[]") {
					// error.insertAfter($('input[name=\'' + element.attr('name') + '\']').last());
					error.insertAfter($('#category_error_area'));
				} else {
					error.insertAfter(element);
				}
			}
		});

		$("#publish_date, #expired_date").datepicker({
			format: "yyyy-mm-dd",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
		});

		getCategoryByProductMainAid();
		getProductFieldByProductMainAid();

		rewritePriceZone();
		
		<?=@$message?>
		<?=@$js_code?>

		$("#textarea_btn").click(function(){
			var marc = $("#marc").val();
			marc1(marc, "text");
			//printCode(marc);
		});

		$("#file_btn").click(function(){
			var file = $("#file")[0].files[0];
			//console.dir(file);
			//console.log(file[0].name);
			if (file) 
			{
		        // create reader
		        var reader = new FileReader();
		        reader.readAsText(file);
		        reader.onload = function(e) {
		            // browser completed reading file - display it
		            var marc = e.target.result;
		            marc1(marc, "file");
		            //printCode(marc);
		        };
		    }
		});

		function marc1(marc, type)
		{
			var lines = marc.split('\n');
			var result = [];
			var arr = [0,1,2,3,4,5,6,7,8,9];

			for(var i = 0;i < lines.length;i++)
			{
				//if(lines[i][0].charCodeAt(0) >= 48 && lines[i][0].charCodeAt(0) <= 57)
				if($.isNumeric(lines[i][0]))
				//if(true)
				{
					var tag = "";
					var ind1 = "";
					var ind2 = "";
					var data = [];
					var x = 0;
					var key = "";
					var value = "";
					
					for(var k = 0; k < lines[i].length; k++)
					{
						//console.log(lines[i][k]+" ==> "+lines[i][k].charCodeAt(0));
						if(k >= 0 && k < 3)
						{
							//tag
							if(lines[i][k].charCodeAt(0) >= 48 && lines[i][k].charCodeAt(0) <= 57)
							{
								tag += lines[i][k];	
							}
						}
						else if(k >= 3 && k < 8)
						{
							//space 1
						}
						else if(k >= 8 && k < 10)
						{
							//indicator
							if(k==8)
							{
								ind1 = lines[i][k];
							}
							else if(k == 9)
							{
								ind2 = lines[i][k];
							}
						}
						else if(k >= 10 && k < 15)
						{
							//space 2
						}
						else{
							if(lines[i][k].charCodeAt(0) == 31)
							{
								x = k;
								if(key.length > 0 && value.length > 0)
								{
									var json = '{"'+key+'":"'+value+'"}';
									var obj = JSON.parse(json);
									data.push(obj);
									key = "";
									value = "";
								}
							}
							else if(k == x+1)
							{
								key = lines[i][k];
							}
							else if(k == lines[i].length-1)
							{
								if(lines[i][k].charCodeAt(0) == 30)
								{
									
								}
								if(key.length > 0 && value.length > 0)
								{
									var json = '{"'+key+'":"'+value+'"}';
									//console.log("json = "+json);
									var obj = JSON.parse(json);
									data.push(obj);
									key = "";
									value = "";	
								}
								else if(value.length > 0)
								{
									data.push(value);
									value = "";
								}
							}
							else{

								if(lines[i][k].charCodeAt(0) == 30)
								{
								
								}
								else{
									value += lines[i][k];	
								}
							}

						}
					}//for
					
					// console.log("tag = "+tag);
					// console.log("ind1 = "+ind1);
					// console.log("ind2 = "+ind2);
					// console.dir(data);
					// console.log('<================>');

					var json = {
								"tag":tag,
								"ind1":ind1,
								"ind2":ind2,
								"data":data
								};
					
					result.push(json);
						
				}//if	
			}//for
			//display(result);
			console.dir(result);
		}
	});
</script>