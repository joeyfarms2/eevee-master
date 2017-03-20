<?php 
$command = @$command;
$item_detail = @$item_detail;
$user_result = @$user_result;

?>
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/transaction.js?iw2l13"></script>
<form id="frm_transaction" name="frm_transaction" method="POST" class="cmxform form-horizontal tasi-form" onsubmit="return false;">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	<input type="hidden" id="cid" name="cid" value="<?=get_array_value($item_detail,"cid","")?>" />
	<input type="hidden" id="page_selected" name="page_selected" value="<?=@$page_selected?>" />
	<input type="hidden" id="search_record_per_page" name="search_record_per_page" value="<?=@$search_record_per_page?>" />
	<input type="hidden" id="search_order_by" name="search_order_by" value="<?=@$search_order_by?>" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-6">
			<section class="panel">
				<div class="panel-body">
					<div class="form-group">
						<div class="col-lg-12">
							<?php 
								$search_in_user = @$search_in_user; 
							?>
							<input type="hidden" id="page_selected_user" name="page_selected_user" value="<?=@$page_selected_user?>" />
							<input type="hidden" id="search_record_per_page_user" name="search_record_per_page_user" value="<?=@$search_record_per_page_user?>" />
							<input type="hidden" id="search_order_by_user" name="search_order_by_user" value="<?=@$search_order_by_user?>" />
							<div class="input-group">
								<input type="text" class="form-control" placeholder="Member id" id="search_post_word_user" name="search_post_word_user" onkeypress="isEnterGoTo(event, 'searchUser()')">
								<span class="input-group-btn">
									<button type="button" class="btn btn-white" title="Find member" onclick="clearPopup();searchUser()"><i id="search_user_button" class="fa fa-search"></i></button>
								</span>
							</div>
							<div class="">
								<div class="form-group">
									<div class="col-md-12">
										<label class="radio-inline">
											<input type="radio" id="search_in_user_1" name="search_in_user[]"  onclick="set_focus('search_post_word_user')" value="cid" checked />Member ID
										</label>
										<label class="radio-inline">
											<input type="radio" id="search_in_user_2" name="search_in_user[]"  onclick="set_focus('search_post_word_user')" value="email" <?php if(is_in_array("email",$search_in_user)) echo "checked"; ?> />Email
										</label>
										<label class="radio-inline">
											<input type="radio" id="search_in_user_3" name="search_in_user[]"  onclick="set_focus('search_post_word_user')" value="first_name_th" <?php if(is_in_array("first_name_th",$search_in_user)) echo "checked"; ?> />Firstname
										</label>
										<label class="radio-inline">
											<input type="radio" id="search_in_user_4" name="search_in_user[]"  onclick="set_focus('search_post_word_user')" value="last_name_th" <?php if(is_in_array("last_name_th",$search_in_user)) echo "checked"; ?> />Lastname
										</label>
										<label class="radio-inline">
											<input type="radio" id="search_in_user_5" name="search_in_user[]"  onclick="set_focus('search_post_word_user')" value="contact_number" <?php if(is_in_array("contact_number",$search_in_user)) echo "checked"; ?> />Phone
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>

		<?php if(false && !is_var_array($user_result)){ ?>
		<div class="col-md-6">
			<section class="panel">
				<div class="panel-body">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="input-group m-bot15">
								<input type="text" class="form-control" placeholder="Product barcode" id="search_post_word_product" name="search_post_word_product" onkeypress="isEnterGoTo(event, 'searchProduct()')">
								<span class="input-group-btn">
									<button type="button" class="btn btn-white" title="Find product" onclick="clearPopup();searchProduct()"><i id="search_product_button" class="fa fa-search"></i></button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<?php } ?>

		<input type="hidden" id="user_aid" name="user_aid" value="<?=get_array_value($user_result,"aid","")?>" />
		<?php if(is_var_array($user_result)){ ?>
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						<?=@$header_title?>
					</header>
					<div class="panel panel-primary">
						<div class="panel-body">
							<div class="form-group">
								<div class="col-lg-4 col-sm-4">
									<ul class="unstyled">
										<li>User Code : <strong><?=get_array_value($user_result,"cid","-")?></strong></li>
										<li>Email : <strong><?=get_array_value($user_result,"email","-")?></strong></li>
										<li>Tel : <strong><?=get_array_value($user_result,"contact_number","-")?></strong></li>
									</ul>
								</div>
								<div class="col-lg-4 col-sm-4">
									<ul class="unstyled">
										<li>Gender : <strong><?=get_array_value($user_result,"gender_name","-")?></strong></li>
										<li>Department : <strong><?=get_array_value($user_result,"department_name","-")?></strong></li>
									</ul>
								</div>
								<div class="col-lg-4 col-sm-4">
									<ul class="unstyled">
										<li>Status : <strong><?=get_array_value($user_result,"status_name","-")?></strong></li>
										<li>User role : <strong><?=get_array_value($user_result,"user_role_name","-")?></strong></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>

			<div class="col-md-6">
				<section class="panel">
					<div id="result-msg-box"></div>
					<div class="row">
						<div class="col-xs-12">
							<section class="panel">
								<header class="panel-heading">
									On going circulation <span id="tbldata_processing_today" class="loading hidden"></span>
								</header>
								<div class="panel-body">
									<div class="form-group">
										<div class="">
											<label for="total_fee" class="col-lg-3 control-label"><h4>Total Fee</h4></label>
											<div class="col-lg-9">
												<input type="text" class="form-control input-lg" id="total_fee" name="total_fee" placeholder="" value="0" readonly>
											</div>
										</div>
										<div class="clear"></div>
										<div class="spaceUp">
											<label for="total_receive" class="col-lg-3 control-label"><h4>Paid</h4></label>
											<div class="col-lg-9">
												<input type="text" class="form-control input-lg" id="total_receive" name="total_receive" placeholder="" value="0" onkeyup="calculate_change()" >
											</div>
										</div>
										<div class="clear"></div>
										<div class="">
											<label for="change" class="col-lg-3 control-label pt20"><h4>Change</h4></label>
											<div class="col-lg-9">
												<h1 id="change_area">0</h1>
											</div>
										</div>
									</div>

									<!-- Button -->
									<div class="form-group">
										<div class="col-lg-12">
											<a class="btn btn-primary" onclick="saveTransactionByUser()" />Confirm</a>
											<a class="btn btn-default" onclick="clearProductToTransactionByUser()" />Clear</a>
										</div>
									</div>
									<!-- End : Button -->
									<?php 
										$search_in_product = @$search_in_product; 
										$search_type_product = @$search_type_product; 
									?>
									<input type="hidden" id="page_selected_product" name="page_selected_product" value="<?=@$page_selected_product?>" />
									<input type="hidden" id="search_record_per_page_product" name="search_record_per_page_product" value="<?=@$search_record_per_page_product?>" />
									<input type="hidden" id="search_order_by_product" name="search_order_by_product" value="<?=@$search_order_by_product?>" />
									<div class="form-group">
										<div class="col-lg-12">
											<div class="">
												<label for="search_type_product" class="radio-inline pl0">Search In : </label>
												<label class="radio-inline">
													<input type="radio" id="search_type_product" name="search_type_product"  onclick="set_focus('search_post_word_product')" value="book" checked />Book
												</label>
												<!-- <label class="radio-inline">
													<input type="radio" id="search_type_product" name="search_type_product"  onclick="set_focus('search_post_word_product')" value="magazine" <?php if($search_type_product == 'magazine') echo "checked"; ?> />Magazine
												</label>
												<label class="radio-inline">
													<input type="radio" id="search_type_product" name="search_type_product"  onclick="set_focus('search_post_word_product')" value="media" <?php if($search_type_product == 'media') echo "checked"; ?> />Media
												</label> -->
											</div>
											<div class="clear"></div>
											<div class="">
												<label for="search_in_product" class="radio-inline pl0">Search By : </label>
												<label class="radio-inline">
													<input type="radio" id="search_in_product_1" name="search_in_product[]"  onclick="set_focus('search_post_word_product')" value="barcode" checked />Barcode
												</label>
												<label class="radio-inline">
													<input type="radio" id="search_in_product_2" name="search_in_product[]"  onclick="set_focus('search_post_word_product')" value="parent.title" <?php if(is_in_array("title",$search_in_product)) echo "checked"; ?> />Title
												</label>
											</div><br/>
											<div class="clear"></div>
											<div class="input-group">
												<input type="text" class="form-control" placeholder="Product barcode" id="search_post_word_product" name="search_post_word_product" onkeypress="isEnterGoTo(event, 'searchProduct()')">
												<span class="input-group-btn">
													<button type="button" class="btn btn-white" title="Find product" onclick="clearPopup();searchProduct()"><i id="search_product_button" class="fa fa-search"></i></button>
												</span>
											</div>
											
											<!-- <div class="">
												<label for="search_type_product" class="radio-inline pl0">Search In : </label>
												<label class="radio-inline">
													<input type="radio" id="search_type_product" name="search_type_product"  onclick="set_focus('search_post_word_product')" value="book" checked />Book
												</label>
												<label class="radio-inline">
													<input type="radio" id="search_type_product" name="search_type_product"  onclick="set_focus('search_post_word_product')" value="magazine" <?php if($search_type_product == 'magazine') echo "checked"; ?> />Magazine
												</label>
											</div>
											<div class="clear"></div>
											<div class="">
												<label for="search_in_product" class="radio-inline pl0">Search By : </label>
												<label class="radio-inline">
													<input type="radio" id="search_in_product_1" name="search_in_product[]"  onclick="set_focus('search_post_word_product')" value="barcode" checked />Barcode
												</label>
												<label class="radio-inline">
													<input type="radio" id="search_in_product_2" name="search_in_product[]"  onclick="set_focus('search_post_word_product')" value="parent.title" <?php if(is_in_array("title",$search_in_product)) echo "checked"; ?> />Title
												</label>
											</div> -->
										</div>
									</div>

								</div>
								<div class="panel-body">
									<div class="adv-table">
										<div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper_today">&nbsp;</div>
									</div>
								</div>
							</section>
						</div>
					</div>
				</section>
			</div>

			<div class="col-md-6">
				<section class="panel">
					<div id="result-msg-box"></div>
					<div class="row">
						<div class="col-xs-12">
							<section class="panel">
								<header class="panel-heading">
									Borrowed <span id="tbldata_processing" class="loading hidden"></span>
								</header>
								<div class="panel-body">
									<div class="adv-table">
										<div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">No record found.</div>
									</div>
								</div>
							</section>
						</div>
					</div>
				</section>
			</div>

		<?php } ?>

	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var user_aid = $('#user_aid').val();
		if(user_aid > 0){
			showSearchResult(2);
			showTransactionResultByUser();
			$('#search_post_word_product').focus();
			$('body').scrollTo('#search_post_word_product');
		}else{
			$('#search_post_word_user').focus();
		}

		<?=@$message?>
		<?=@$js_code?>
	});
</script>