<?php 
$command = @$command;
$parent_detail = @$parent_detail;
$copy_item_detail = @$copy_item_detail;
$master_user_section = @$master_user_section;
$product_ref_user_section_result = @$product_ref_user_section_result;

$product_main_result = @$product_main_result;
$product_main_aid = get_array_value($product_main_result,"aid","");
$product_main_url = get_array_value($product_main_result,"url","");
$parent_aid = get_array_value($parent_detail,"aid","");

?>
<script type="text/javascript" src="<?=JS_PATH?>product/product_back/product_back_init.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/magazine.js"></script>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/magazine_copy.js"></script>
<form id="frm_product" name="frm_product" method="POST" action="<?=site_url('admin/product/magazine-copy/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($copy_item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="product_main_aid" name="product_main_aid" value="<?=$product_main_aid?>" />
	<input type="hidden" id="product_type_aid" name="product_type_aid" value="<?=@$this_product_type_aid?>" />
	<input type="hidden" id="parent_aid" name="parent_aid" value="<?=$parent_aid?>" />
	<input type="hidden" id="cid" name="cid" value="<?=get_array_value($copy_item_detail,"cid","")?>" />

	<div id="result-msg-box"></div>

	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<?php include_once('include_menu.php'); ?>
				<header class="panel-heading no-radius fieldset">
					General Information
				</header>

				<div class="panel-body">

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="barcode">Barcode</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="barcode" name="barcode" value="<?=get_array_value($copy_item_detail,"barcode","")?>" />
							<p class="help-block">Leave blank for generate by system.</p>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($copy_item_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

					<?php if(CONST_HAS_IPAD_APP == '1'){ ?>
						<div class="form-group hide">
							<label class="col-md-12 col-lg-2 control-label" for="nonconsume_identifier">Nonconsume identifier for ipad</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="nonconsume_identifier" name="nonconsume_identifier" value="<?=get_array_value($copy_item_detail,"nonconsume_identifier","")?>" />
							</div>
						</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="copy_title">Title alias</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="copy_title" name="copy_title" value="<?=get_array_value($copy_item_detail,"copy_title","")?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cover_price">Cover price</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="cover_price" name="cover_price" value="<?=get_array_value($copy_item_detail,"cover_price","")?>" />
						</div>
					</div>
					<?php if(CONST_HAS_TRANSACTION == "1"){ 
							if($product_main_aid == "1" || $product_main_aid == "2"){
					?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="">Call number</label>
						<div class="col-md-12 col-lg-2">
							<input class="form-control" type="text" id="no_1" name="no_1" value="<?=get_array_value($parent_detail,"no_1","")?>" readonly />
							<p class="help-block">Call number - 1</p>
						</div>
						<div class="col-md-12 col-lg-2">
							<input class="form-control" type="text" id="no_2" name="no_2" value="<?=get_array_value($parent_detail,"no_2","")?>" readonly />
							<p class="help-block">Call number - 2</p>
						</div>
						<div class="col-md-12 col-lg-2">
							<input class="form-control" type="text" id="no_3" name="no_3" value="<?=get_array_value($parent_detail,"no_3","")?>" readonly />
							<p class="help-block">Call number - 3</p>
						</div>
						<div class="col-md-12 col-lg-2">
							<input class="form-control" type="text" id="no_4" name="no_4" value="<?=get_array_value($parent_detail,"no_4","")?>" readonly />
							<p class="help-block">Call number - 4</p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="source">Source</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="source" name="source" value="<?=get_array_value($copy_item_detail,"source","")?>" />
						</div>
					</div>
					<?php 
							}
						} 
					?>
				
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Publish date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="publish_date" name="publish_date" value="<?=get_array_value($copy_item_detail,"publish_date",get_array_value($parent_detail,"publish_date",""))?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('publish_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div>

					<!-- <div class="form-group">
						<label class="col-md-12 col-lg-2 control-label">Expiration date</label>
						<div class="col-xs-11 col-md-3">
							<div class="input-group date form_datetime-adv" data-date="">
								<input class="form-control" type="text" id="publish_date" name="expired_date" value="<?=get_array_value($copy_item_detail,"expired_date",get_array_value($parent_detail,"expired_date",""))?>" />

								<div class="input-group-btn">
									<button class="btn btn-danger" type="button" onclick="clearValue('expired_date');">
									<i class="fa fa-times"></i>
									</button>
								</div>
							</div>
						</div>
					</div> -->
					
					<?php /*
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="file_upload">File upload<BR />(Web version)</label>
						<div class="col-md-12 col-lg-8">
							<?php $use_digital_gen = get_array_value($copy_item_detail,"use_digital_gen",""); ?>
							<label class="checkbox-inline">
								<input type="checkbox" id="use_digital_gen" name="use_digital_gen" value="1" onclick="check_file_limit()" <?=($use_digital_gen == "1")?"checked":"";?> />Use e-book generator 
							</label>
							<input class="spaceUp default" type="file" id="file_upload" name="file_upload"/>
							<p class="help-block hide" id="file_upload_limit_for_default">Only file extension <?=get_file_type(CONST_ALLOW_FILE_TYPE_DEFAULT)?> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_DEFAULT)?>.</p>
							<p class="help-block hide" id="file_upload_limit_for_digital_gen">Only file extension <?=get_file_type(CONST_ALLOW_FILE_TYPE_FOR_DIGITAL_GEN)?> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_FOR_DIGITAL_GEN)?>.</p>
						</div>
						<?php
							$upload_path = get_array_value($copy_item_detail,"upload_path","")."file/";
							$file_upload = get_array_value($copy_item_detail,"file_upload","");
							// echo "path : ".$upload_path.$file_upload;
							if(is_file($upload_path.$file_upload)){
						?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
								<a href="<?=site_url($upload_path.$file_upload)?>" target="_blank"><?=$file_upload?></a>
						</div>
						<?php } ?>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="file_upload_app">File upload<BR />(App version)</label>
						<div class="col-md-12 col-lg-8">
							<?php $use_digital_gen = get_array_value($copy_item_detail,"use_digital_gen",""); ?>
							<input class="spaceUp default" type="file" id="file_upload_app" name="file_upload_app"/>
							<p class="help-block" id="">Only file extension .zip and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_DEFAULT)?>.</p>
						</div>
					</div>
					*/ ?>
					
				<?php 
					if($product_main_aid != "1" && $product_main_aid != "2"){ 
				?>
					<?php /* AIR EDITED START */ ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="digital_file_type">File type</label>
						<div class="col-md-12 col-lg-8">
							<?php $digital_file_type =  get_array_value($copy_item_detail,"digital_file_type",""); ?>
							<label class="radio-inline">
								<input type="radio" name="digital_file_type" id="digital_file_type_upload_file" value="upload_file" <?php if($digital_file_type == "epub") echo 'checked="checked"';?> onclick="check_file_limit();" />Upload File
							</label>
							<label class="radio-inline">
								<input type="radio" name="digital_file_type" id="digital_file_type_ftp" value="ftp" checked onclick="check_file_limit();" />FTP
							</label>
							<label class="radio-inline">
								<input type="radio" name="digital_file_type" id="digital_file_type_pdf" value="pdf" <?php if($digital_file_type == "pdf") echo 'checked="checked"';?> onclick="check_file_limit();" />Show Files
							</label>
							<label class="radio-inline">
								<input type="radio" name="digital_file_type" id="digital_file_type_ftp_update" value="ftp_update" onclick="check_file_limit();" />FTP Update
							</label>
						</div>
					</div>
					<?php } ?>
					<div class="form-group hide" id="file_upload_area">
						<label class="col-md-12 col-lg-2 control-label" for="file_upload">File upload</label>
						<div class="col-md-12 col-lg-8">
						<?php /*
							<input class="default" type="file" id="file_upload" name="file_upload"/>
							<p class="help-block" id="file_upload_limit_for_default">Only file extension <span id="file_type_desc">N/A</span> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_DEFAULT)?>.</p> */ ?>
							<p>File List :</p>
						</div>
						<?php
							$upload_path = get_array_value($copy_item_detail,"upload_path","");
							$file_upload = "./".$upload_path."app/";
							// echo "path : ".$upload_path.$file_upload;
							if(is_var_array(glob($file_upload."/*.*"))){
								foreach (glob($file_upload."/*.*") as $filename){
						?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
								<?php /*File :*/ ?> <?=basename($filename)?> | Size : <?php $file_size = filesize($filename); $file_size_txt = round($file_size/1000); if($file_size_txt > 1000){ echo "<span style='color:#FF0000;'>".$file_size_txt."</span>";}else{echo "<span>".$file_size_txt."</span>";}  ?> kB<BR> 
						</div>
						<?php } } ?>

						<?php
							$upload_path = get_array_value($copy_item_detail,"upload_path","");
							$file_upload = "./".$upload_path."others/".get_array_value($copy_item_detail,"file_upload","");
							// echo "path : ".$file_upload;
							if(is_file($file_upload)){
						?>
						<div class="col-md-12 col-lg-offset-2 col-lg-8 spaceUp">
								File for web : <a href="<?=site_url($file_upload)?>" target="_blank"><?=basename($file_upload)?></a>
						</div>
						<?php } ?>
					</div>
					
					<div class="form-group hide" id="file_upload_area_2">
						<label class="col-md-12 col-lg-2 control-label" for="file_upload">File upload</label>
						<div class="col-md-12 col-lg-8">
							<input class="default" type="file" id="file_upload" name="file_upload"/>
							<p class="help-block" id="file_upload_limit_for_default">Only file extension .pdf<span id="file_type_desc"></span> and size not over <?=get_size(CONST_ALLOW_FILE_SIZE_DEFAULT)?>.</p>
						</div>
					</div>
					<div class="form-group hide" id="file_upload_ftp_area">
						<div class="col-md-12 col-lg-8 col-lg-offset-2">
							<?php if($command == "_update"){?>
							<p class="help-block">After upload file to this path : <?=$upload_path."ftp"?><BR/>Come to click button below to complete step.</p>
							<BR />
							<a id="generate_file_button" class="btn btn-danger" onclick="generateCopyFile('<?=@$this_product_type_aid?>','<?=get_array_value($copy_item_detail,"aid","")?>');" />Process File</a>
							<p id="generate_file_message" class="help-block hide">Generating file.... please do not hit any button.</p>
							<?php }else{ ?>
							<p class="help-block">Save this copy then you'll get path for upload files.</p>
							<?php } ?>
						</div>
					</div>
					
					<div class="form-group hide" id="file_upload_ftp_update_area">
						<div class="col-md-12 col-lg-8 col-lg-offset-2">
							<?php if($command == "_update"){?>
							<p class="help-block">After upload pdf files to this path : <?=$upload_path."file"?>
							<BR />name format : pdf-%4d.pdf
							<BR />Come to click button below to complete step.</p>
							<BR />
							<a id="update_file_button" class="btn btn-danger" onclick="updateCopyFile('<?=@$this_product_type_aid?>','<?=get_array_value($copy_item_detail,"aid","")?>');" />Process File</a>
							<p id="generate_file_message_update" class="help-block hide">Generating file.... please do not hit any button.</p>
							<?php }else{ ?>
							<p class="help-block">Save this copy then you'll get path for upload files.</p>
							<?php } ?>
						</div>
					</div>
					<?php /* AIR EDITED END */ ?>
					
				</div>
			</section>

			<?php if(true){ ?>
			<section class="panel">
				<header class="panel-heading fieldset">
					Possession / Price Information
				</header>
				<div class="panel-body">
					<?php 
						$is_license =  get_array_value($copy_item_detail,"is_license","");
						$disabled = "";
						$hide = "";
						if($command == "_update" && $is_license == "1" && !is_general_admin_or_higher()){
							$disabled = "disabled";
							$hide = "hide";
						}
					?>

					<?php 

						if($product_main_aid != "1" && $product_main_aid != "2" && $product_main_aid != "8"){
					?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="type">Copy type</label>
						<div class="col-md-12 col-lg-8">
							<?php $type =  get_array_value($copy_item_detail,"type",""); ?>
							<label class="radio-inline">
								<input type="radio" name="type" id="type_1" value="1" checked onclick="rewritePriceZone();" <?=$disabled?> />Digital
							</label>
							<label class="radio-inline hide">
								<input type="radio" name="type" id="type_2" value="2" <?php if($type == "2") echo 'checked="checked"';?> onclick="rewritePriceZone();" <?=$disabled?> />Paper
							</label>
						</div>
					</div>

					
					<?php 
						}else{
					?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="type">Copy type</label>
						<div class="col-md-12 col-lg-8">
							<?php $type =  get_array_value($copy_item_detail,"type",""); ?>
							<label class="radio-inline hide">
								<input type="radio" name="type" id="type_1" value="1" <?php if($type == "1") echo 'checked="checked"';?> onclick="rewritePriceZone();" <?=$disabled?> />Digital
							</label>
							<label class="radio-inline">
								<input type="radio" name="type" id="type_2" value="2" checked onclick="rewritePriceZone();" <?=$disabled?> />Paper
							</label>
						</div>
					</div>

					<?php		
						}
					?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="type_minor">Resource type</label>
						<div class="col-md-12 col-lg-8">
							<?php $type_minor =  get_array_value($copy_item_detail,"type_minor",""); ?>
							<?php 
								if($product_main_aid != "1" && $product_main_aid != "2" && $product_main_aid != "8"){
							?>
								<label class="radio-inline ">
									<input type="radio" name="type_minor" id="type_minor_1" value="1" checked />E-Book
								</label>
								<label class="radio-inline">
									<input type="radio" name="type_minor" id="type_minor_2" value="2" <?php if($type_minor == "2") echo 'checked="checked"';?> />E-Magazine
								</label>
								<label class="radio-inline">
									<input type="radio" name="type_minor" id="type_minor_5" value="5" <?php if($type_minor == "5") echo 'checked="checked"';?> />Knowledge Resources
								</label>
								<label class="radio-inline">
									<input type="radio" name="type_minor" id="type_minor_8" value="8" <?php if($type_minor == "8") echo 'checked="checked"';?> />Information
								</label>


							<?php		
								}else{
							?>
								<label class="radio-inline">
									<input type="radio" name="type_minor" id="type_minor_3" value="3" <?php if($type_minor == "3") echo 'checked="checked"';?> />Book
								</label>
								<label class="radio-inline">
									<input type="radio" name="type_minor" id="type_minor_4" value="4"  checked/>Magazine
								</label>
								<label class="radio-inline">
									<input type="radio" name="type_minor" id="type_minor_6" value="6" <?php if($type_minor == "6") echo 'checked="checked"';?> />Book+CD
								</label>
								<label class="radio-inline">
									<input type="radio" name="type_minor" id="type_minor_7" value="7" <?php if($type_minor == "7") echo 'checked="checked"';?> />Media
								</label>
							<?php		
								}
							?>
						</div>
					</div>
					<?php 
						if($product_main_aid != "1" && $product_main_aid != "2" && $product_main_aid != "8"){
					?>
					<div class="form-group <?=$hide?>" id="option_license_area">
						<div>
							<label class="col-md-12 col-lg-2 control-label" for="status">License</label>
							<div class="col-md-12 col-lg-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" id="is_license" name="is_license" value="1" <?php if($is_license == "1") echo 'checked="checked"';?> onclick="rewritePriceZone();" <?=$disabled?>>
										This book use license from Bookdose
									</label>
								</div>
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp hide" id="concurrence_area">
							<label class="col-md-12 col-lg-2 control-label" for="ebook_concurrence">Number of digital concurrence</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="ebook_concurrence" name="ebook_concurrence" value="<?=get_array_value($copy_item_detail,"ebook_concurrence","")?>" onkeypress="isNumeric(event, this.value)" <?=$disabled?> />
							</div>
						</div>
					</div>

					<div class="form-group">
						<div>
							<label class="col-md-12 col-lg-2 control-label" for="possession">Possession</label>
							<div class="col-md-12 col-lg-8">
								<?php $possession =  get_array_value($copy_item_detail,"possession",""); ?>
								<label class="radio-inline">
									<input type="radio" name="possession" id="possession_1" value="1" checked onclick="rewritePriceZone();" <?=$disabled?> />Free
								</label>
								<label class="radio-inline">
									<input type="radio" name="possession" id="possession_2" value="2" <?php if($possession == "2") echo 'checked="checked"';?> onclick="rewritePriceZone();" <?=$disabled?> />Rental
								</label>
							</div>
						</div>
					</div>
					<?php		
						}else{
					?>
					<div class="form-group">
						<div>
							<label class="col-md-12 col-lg-2 control-label" for="possession">Possession</label>
							<div class="col-md-12 col-lg-8">
								<?php $possession =  get_array_value($copy_item_detail,"possession",""); ?>
								<label class="radio-inline hide">
									<input type="radio" name="possession" id="possession_1" value="1" <?php if($possession == "1") echo 'checked="checked"';?> onclick="rewritePriceZone();" <?=$disabled?> />Free
								</label>
								<label class="radio-inline">
									<input type="radio" name="possession" id="possession_2" value="2" checked onclick="rewritePriceZone();" <?=$disabled?> />Rental
								</label>
							</div>
						</div>
					</div>
					<?php
						}
					?>
					
					<div class="hide">
						<div class="form-group hide" id="option_digital_area">
							<div class="">
								<label class="col-md-12 col-lg-2 control-label" for="digital_price">Price of digital edition</label>
								<div class="col-md-12 col-lg-8">
									<input class="form-control" type="text" id="digital_price" name="digital_price" value="<?=get_array_value($copy_item_detail,"digital_price","0")?>" onkeypress="isNumeric(event, this.value)" />
								</div>
							</div>

							<div class="clear"></div>
							<div class="spaceUp">
								<label class="col-md-12 col-lg-2 control-label" for="digital_point">Point of digital edition</label>
								<div class="col-md-12 col-lg-8">
									<input class="form-control" type="text" id="digital_point" name="digital_point" value="<?=get_array_value($copy_item_detail,"digital_point","0")?>" onkeypress="isNumeric(event, this.value)" />
								</div>
							</div>
						</div>
					</div>

					<div class="hide">
						<div class="hide" id="option_paper_area">
							<div class="form-group">
								<div class="">
									<label class="col-md-12 col-lg-2 control-label" for="paper_price">Price of paper edition</label>
									<div class="col-md-12 col-lg-8">
										<input class="form-control" type="text" id="paper_price" name="paper_price" value="<?=get_array_value($copy_item_detail,"paper_price","0")?>" onkeypress="isNumeric(event, this.value)" />
									</div>
								</div>

								<div class="clear"></div>
								<div class="spaceUp">
									<label class="col-md-12 col-lg-2 control-label" for="paper_point">Point of paper edition</label>
									<div class="col-md-12 col-lg-8">
										<input class="form-control" type="text" id="paper_point" name="paper_point" value="<?=get_array_value($copy_item_detail,"paper_point","0")?>" onkeypress="isNumeric(event, this.value)" />
									</div>
								</div>

								<div class="clear"></div>
								<div class="spaceUp">
									<label class="col-md-12 col-lg-2 control-label" for="in_stock">Number left in stock</label>
									<div class="col-md-12 col-lg-8">
										<input class="form-control" type="text" id="in_stock" name="in_stock" value="<?=get_array_value($copy_item_detail,"in_stock","0")?>" onkeypress="isNumeric(event, this.value)" />
									</div>
								</div>
							</div>

							<div class="form-group">
							<div class="">
								<label class="col-md-12 col-lg-2 control-label required" for="transport_aid">Transport option</label>
								<div class="col-md-12 col-lg-8">
									<?php $transport_aid =  get_array_value($copy_item_detail,"transport_aid","0"); ?>
									<?php 
										if(is_var_array(@$master_transport)){ 
											foreach ($master_transport as $item) {
									?>
									<label class="radio-inline">
										<input type="radio" name="transport_aid" id="transport_aid_<?=get_array_value($item,"aid","0");?>" value="<?=get_array_value($item,"aid","0");?>" <?php if($transport_aid == get_array_value($item,"aid","0")) echo 'checked="checked"';?> onclick="changeTrasportType();" /><?=get_array_value($item,"title","-");?>
									</label>
									<?php } } ?>

									<label class="radio-inline">
										<input type="radio" name="transport_aid" id="transport_aid_0" value="0" <?php if($transport_aid == 0) echo 'checked="checked"';?> onclick="changeTrasportType();" />Custom
									</label>
								</div>
							</div>

								<div class="clear"></div>
								<div class="spaceUp">
									<label class="col-md-12 col-lg-2 control-label" for="transport_price">Transport custom</label>
									<div class="col-md-12 col-lg-8">
										<input class="form-control" type="text" id="transport_price" name="transport_price" value="<?=get_array_value($copy_item_detail,"transport_price","")?>" onkeypress="isNumeric(event, this.value)" />
									</div>
								</div>

							</div>
						</div>
					</div>

					<div class="form-group hide" id="option_rental_area">

						<div class="hide" id="digital_rental_day_area">
							<label class="col-md-12 col-lg-2 control-label" for="rental_period">Number of rental day</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="rental_period" name="rental_period" value="<?=get_array_value($copy_item_detail,"rental_period","")?>" onkeypress="isNumeric(event, this.value)" />
							</div>
						</div>

						<div id="rental_price_area" class="hide">
						<?php if(is_var_array($master_user_section)){ ?>
							
							<?php 
								foreach ($master_user_section as $section) { 
									$user_section_aid = get_array_value($section,"aid","0");
									$product_ref_user_section = get_array_value($product_ref_user_section_result,$user_section_aid,"");
									$default_rental_period = get_array_value($section,"default_rental_period","0");
									$default_rental_fee = get_array_value($section,"default_rental_fee","0");
									$default_rental_fee_point = get_array_value($section,"default_rental_fee_point","0");
									$default_rental_fine_fee = get_array_value($section,"default_rental_fine_fee","0");
									$default_renew_time = get_array_value($section,"default_renew_time","0");
									$default_renew_period = get_array_value($section,"default_renew_period","0");
									// echo "default_rental_period = $default_rental_period , default_rental_fee = $default_rental_fee , default_rental_fine_fee = $default_rental_fine_fee<BR>";
							?>
							<div class="clear"></div>
							<div class="spaceUp">
								<label class="col-md-12 col-lg-2 control-label" for=""><?=get_array_value($section,"name","N/A");?></label>
								<div class="col-md-12 col-lg-2">
									<input class="form-control" type="text" id="<?=$user_section_aid?>_rental_period" name="<?=$user_section_aid?>_rental_period" value="<?=get_array_value($product_ref_user_section,"rental_period",$default_rental_period)?>" onkeypress="isNumeric(event, this.value)" />
									<p class="help-block">Number of rental day</p>
								</div>
								<div class="col-md-12 col-lg-2">
									<input class="form-control" type="text" id="<?=$user_section_aid?>_rental_fee" name="<?=$user_section_aid?>_rental_fee" value="<?=get_array_value($product_ref_user_section,"rental_fee",$default_rental_fee)?>" onkeypress="isNumeric(event, this.value)" />
									<p class="help-block">Rental fee (First period)</p>
								</div>
								<div class="col-md-12 col-lg-2">
									<input class="form-control" type="text" id="<?=$user_section_aid?>_rental_fine_fee" name="<?=$user_section_aid?>_rental_fine_fee" value="<?=get_array_value($product_ref_user_section,"rental_fine_fee",$default_rental_fine_fee)?>" onkeypress="isNumeric(event, this.value)" />
									<p class="help-block">Overtime fee (per day)</p>
								</div>
								<div class="col-md-12 col-lg-2">
									<input class="form-control" type="text" id="<?=$user_section_aid?>_renew_time" name="<?=$user_section_aid?>_renew_time" value="<?=get_array_value($product_ref_user_section,"renew_time",$default_renew_time)?>" onkeypress="isNumeric(event, this.value)" />
									<p class="help-block">Renew time</p>
								</div>
								<div class="col-md-12 col-lg-2">
									<input class="form-control" type="text" id="<?=$user_section_aid?>_renew_period" name="<?=$user_section_aid?>_renew_period" value="<?=get_array_value($product_ref_user_section,"renew_period",$default_renew_period)?>" onkeypress="isNumeric(event, this.value)" />
									<p class="help-block">Renew period</p>
								</div>
							</div>
							<?php } ?>
							
						<?php }else{ ?>
							<div class="clear"></div>
							<div class="spaceUp">
								<label class="col-md-12 col-lg-2 control-label" for="rental_fee">Rental fee (first period)</label>
								<div class="col-md-12 col-lg-8">
									<input class="form-control" type="text" id="rental_fee" name="rental_fee" value="<?=get_array_value($copy_item_detail,"rental_fee","")?>" onkeypress="isNumeric(event, this.value)" />
								</div>
							</div>

							<div class="hide">
								<label class="col-md-12 col-lg-2 control-label" for="rental_fee_point">Rental fee by point (first period)</label>
								<div class="col-md-12 col-lg-8">
									<input class="form-control" type="text" id="rental_fee_point" name="rental_fee_point" value="<?=get_array_value($copy_item_detail,"rental_fee_point","")?>" onkeypress="isNumeric(event, this.value)" />
								</div>
							</div>

							<div class="clear"></div>
							<div class="spaceUp">
								<label class="col-md-12 col-lg-2 control-label" for="rental_fine_fee">Overtime fee (per day)</label>
								<div class="col-md-12 col-lg-8">
									<input class="form-control" type="text" id="rental_fine_fee" name="rental_fine_fee" value="<?=get_array_value($copy_item_detail,"rental_fine_fee","")?>" onkeypress="isNumeric(event, this.value)" />
								</div>
							</div>
						<?php } ?>
						</div>
					</div>
					
					<div class="form-group hide" id="option_shelf_area">
						<div class="">
							<label class="col-md-12 col-lg-2 control-label required" for="shelf_status">Shelf status</label>
							<div class="col-md-12 col-lg-8">
								<?php $shelf_status =  get_array_value($copy_item_detail,"shelf_status",""); ?>
								<label class="radio-inline">
									<input type="radio" name="shelf_status" id="shelf_status_1" value="1" checked />On shelf
								</label>
								<label class="radio-inline">
									<input type="radio" name="shelf_status" id="shelf_status_2" value="2" <?php if($shelf_status == "2") echo 'checked="checked"';?> />Borrowed
								</label>
								<label class="radio-inline">
									<input type="radio" name="shelf_status" id="shelf_status_3" value="3" <?php if($shelf_status == "3") echo 'checked="checked"';?> />Damage
								</label>
								<label class="radio-inline">
									<input type="radio" name="shelf_status" id="shelf_status_4" value="4" <?php if($shelf_status == "4") echo 'checked="checked"';?> />Lost
								</label>
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<!-- <label class="col-md-12 col-lg-2 control-label" for="shelf_name">Shelf location</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="shelf_name" name="shelf_name" value="<?=get_array_value($copy_item_detail,"shelf_name","")?>">
							</div> -->
						

						
							<label class="col-md-12 col-lg-2 control-label" for="shelf_name">Shelf location</label>
							<div class="col-md-12 col-lg-8">
								<?php $shelf_name =  get_array_value($copy_item_detail,"shelf_name",""); ?>
								<select id="shelf_name" name="shelf_name" class="form-control chzn-select" >
									<option value="">Choose Shelf Location..</option>
									<option value="1" <?php if($shelf_name == "1") echo 'selected="selected"';?>>ห้องสมุดศูนย์ความรู้จังหวัดชายแดนภาคใต้</option>
									<option value="2" <?php if($shelf_name == "2") echo 'selected="selected"';?>>ห้องสมุดศูนย์ฝึกอบรมประชานาถ</option>
									<option value="3" <?php if($shelf_name == "3") echo 'selected="selected"';?>>ห้องสมุดสำนักข่าวกรองแห่งชาติ</option>

								</select>
							</div>

						
						</div>

					</div>

					<div class="form-group hide">
						<div class="col-md-12 col-lg-offset-2 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="update_copies" name="update_copies" value="1" <?php if(get_array_value($copy_item_detail,"update_copies","") == "1") echo 'checked="checked"';?> />Update all this value to every copies of this magazine
							</label>
						</div>
					</div>


				</div>
			</section>
			<?php } ?>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="checkBeforeProcess('frm_product', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product-<?=$product_main_url?>/magazine/edit/<?=$parent_aid?>/copy');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#publish_date, #expired_date").datepicker({
			format: "yyyy-mm-dd",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
		});

		rewritePriceZone();
		check_file_limit();
		changeTrasportType();
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>