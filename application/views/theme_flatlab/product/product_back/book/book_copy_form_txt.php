<?php 
$command = @$command;
$item_detail = @$item_detail;
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
	
		getCategoryByProductMainAid();
		getProductFieldByProductTypeAid();

		rewritePriceZone();
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/book.js"></script>
<form id="frm_product" name="frm_product" method="POST" action="<?=site_url('admin/product/book/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="product_main_aid" name="product_main_aid" value="1" />
	<input type="hidden" id="product_type_aid" name="product_type_aid" value="1" />

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
					<?php if(is_root_admin_or_higher() && $command == "_update"){ ?>
						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label required" for="aid_readonly">Product Code</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control readonly" type="text" id="aid_readonly" name="aid_readonly" value="<?=get_text_pad(get_array_value($item_detail,"aid","0"))?>" readonly />
							</div>
						</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($item_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="category">Category</label>
						<div class="col-md-12 col-lg-8">
							<input type="hidden" name="category" id="category" value="<?=get_array_value($item_detail,"category","")?>" />
							<div id="category_area"></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="option">Option</label>
						<div class="col-md-12 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="is_new" name="is_new" value="1" <?php if(get_array_value($item_detail,"is_new","") == "1") echo 'checked="checked"';?> />New
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_recommended" name="is_recommended" value="1" <?php if(get_array_value($item_detail,"is_recommended","") == "1") echo 'checked="checked"';?> />Recommended
							</label>
							<label class="checkbox-inline">
								<input type="checkbox" id="is_home" name="is_home" value="1" <?php if(get_array_value($item_detail,"is_home","") == "1") echo 'checked="checked"';?> />Show in home
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="publisher">Publisher</label>
						<div class="col-md-12 col-lg-8">
							<?php $publisher_aid =  get_array_value($item_detail,"publisher_aid",""); ?>
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

				</div>
			</section>

			<?php if(true){ ?>
			<section class="panel">
				<header class="panel-heading fieldset">
					Process / Price Information
				</header>
				<div class="panel-body">

					<?php if(is_general_admin_or_higher()){ ?>
					<div class="form-group" id="option_license_area">
						<div>
							<label class="col-md-12 col-lg-2 control-label" for="status">License</label>
							<div class="col-md-12 col-lg-8">
								<div class="checkbox">
									<label>
										<input type="checkbox" id="is_license" name="is_license" value="1" onclick="rewritePriceZone()">
										This book use license from Bookdose
									</label>
								</div>
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp hide" id="concurrence_area">
							<label class="col-md-12 col-lg-2 control-label" for="price">Number of digital concurrence</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="ebook_concurrence" name="ebook_concurrence" value="<?=get_array_value($item_detail,"ebook_concurrence","")?>">
							</div>
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<div>
							<label class="col-md-12 col-lg-2 control-label" for="status">Possession</label>
							<div class="col-md-12 col-lg-8">
								<?php $possession =  get_array_value($item_detail,"possession",""); ?>
								<label class="radio-inline">
									<input type="radio" name="possession" id="possession_1" value="1" checked onclick="rewritePriceZone()" />Buy out (Free or Paid)
								</label>
								<label class="radio-inline">
									<input type="radio" name="possession" id="possession_2" value="2" <?php if($possession == "2") echo 'checked="checked"';?> onclick="rewritePriceZone()" />Rental
								</label>
							</div>
						</div>
					</div>
					
					<div class="form-group hide" id="option_price_area">
						<div class="">
							<label class="col-md-12 col-lg-2 control-label" for="paper_price">Price of paper edition</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="paper_price" name="paper_price" value="<?=get_array_value($item_detail,"paper_price","")?>">
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="digital_price">Price of digital edition</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="digital_price" name="digital_price" value="<?=get_array_value($item_detail,"digital_price","")?>">
							</div>
						</div>
					</div>

					<div class="form-group hide" id="option_point_area">
						<div class="">
							<label class="col-md-12 col-lg-2 control-label" for="paper_point">Point of paper edition</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="paper_point" name="paper_point" value="<?=get_array_value($item_detail,"paper_point","")?>">
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="digital_point">Point of digital edition</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="digital_point" name="digital_point" value="<?=get_array_value($item_detail,"digital_point","")?>">
							</div>
						</div>
					</div>

					<div class="form-group hide" id="option_rental_area">
						<div>
							<label class="col-md-12 col-lg-2 control-label" for="rental_day">Number of rental day</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="rental_day" name="rental_day" value="<?=get_array_value($item_detail,"rental_day","")?>">
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="rental_fee">Rental fee (first period)</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="rental_fee" name="rental_fee" value="<?=get_array_value($item_detail,"rental_fee","")?>">
							</div>
						</div>

						<div class="clear"></div>
						<div class="spaceUp">
							<label class="col-md-12 col-lg-2 control-label" for="rental_overtime_fee">Overtime fee (per day)</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="rental_overtime_fee" name="rental_overtime_fee" value="<?=get_array_value($item_detail,"rental_overtime_fee","")?>">
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12 col-lg-offset-2 col-lg-8">
							<label class="checkbox-inline">
								<input type="checkbox" id="update_copies" name="update_copies" value="1" <?php if(get_array_value($item_detail,"update_copies","") == "1") echo 'checked="checked"';?> />Update all this value to every copies of this book
							</label>
						</div>
					</div>


				</div>
			</section>
			<?php } ?>

			<section class="panel">
				<header class="panel-heading fieldset">
					Marc Information
				</header>
				<div class="panel-body">
					<div id="marc_field_list_area"></div>
				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="checkBeforeProcess('frm_product', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/product/book');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>
		</div>
	</div>
</form>