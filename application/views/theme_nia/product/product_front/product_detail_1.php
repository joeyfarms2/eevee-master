<?php
	$item_result = @$item_result;
	$product_type_detail = @$product_type_detail;
	$product_main_url = get_array_value($item_result,"product_main_url","none");
	
	$product_type_aid = get_array_value($product_type_detail,"aid","1");

	$has_license = get_array_value($item_result,"has_license","0");
	$biblio_field_result = get_array_value($item_result,"biblio_field_result","");
	
?>	
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/product.js"></script>
<script type="text/javascript" src="<?=JS_PATH?>product/product_front/review.js"></script>
		
		<section id="page">
			<section id="content" class="mt30 pb30">
				<div class="container">
					<div class="row mb30">
						<div class="col-lg-12">
							<div class="product-title"><?=get_array_value($item_result,"title","")?>&nbsp;</div>
						</div>
						<div class="col-lg-12">
							<div class="product-author"><a href="<?=site_url('search/'.get_array_value($item_result,"author",""))?>?search_in=author"><?=get_array_value($item_result,"author","")?></a>&nbsp;</div>
						</div>
					</div>
					<div class="row mb30">
						<div class="col-sm-12 col-md-4"> 
							<div class="cover-detail">
								<?php if($has_license == '1'){ ?>
									<div class="mask-ipad"></div>
								<?php } ?>
								<img src="<?=get_image(get_array_value($item_result,"cover_image_detail_path",""),"detail",get_array_value($item_result,"large_image",""))?>" class="" /> 
							</div>
						</div>
						<div class="col-sm-12 col-md-8">
							<p class="description"><?=get_array_value($item_result,"description","")?></p>
							<?php
								if($product_main_url == 'data-subscription'){
							?>
							<p class="spaceUp">
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Effective Date</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item_result,"publish_date",""), "N/A")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Expired Date</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item_result,"expired_date",""), "N/A")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Category</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"category_link","-")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Publisher</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"publisher_name","N/A")?>
								</div>
							</p>


							<?php }else{
								$publish_date_title = ($product_type_aid == "2") ? "Issued date" : "Published date"; 
							?>
							<p class="spaceUp">
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title"><?=$publish_date_title?></span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item_result,"publish_date",""), "N/A")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Publisher</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"publisher_name","N/A")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Category</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"category_link","-")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Page</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"total_page","N/A")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Rating</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: 
									<?php $review_point = get_array_value($item_result,"review_point","0"); ?>
									<span class="rating-show">
										<span class="star <?=($review_point >= 5) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 4) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 3) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 2) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 1) ? "focus" : ""?>"></span>
									</span>

								</div>
							</p>
							<?php } ?>
						</div>

						<?php
							if(is_var_array($digital_list) && $product_main_url != 'data-subscription'){
								$data = $digital_list[0];
								// print_r($data);
								$copy_aid = get_array_value($data,"aid","");
								$product_type_cid = get_array_value($data,"product_type_cid","");
								$point = get_array_value($data,"point","");
								$price = get_array_value($data,"price","");
								$button = get_array_value($data,"button","");
								$show_price = get_array_value($data,"show_price","");
								$description_list = get_array_value($data,"description_list","");
								$remark_list = get_array_value($data,"remark_list","");
								$copy_title = get_array_value($data,"copy_title","");
						?>
						<div class="col-sm-12 col-md-8 detail-box">
							<p class="spaceUp">
								<div class="">
									<div class="container status-box-header">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding first">
											<span class="product-sub-title ">Type</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail status">
												<?=get_array_value($item_result,"copy_type_minor_show","-");?>
											</div>
										</div>
									</div>
								</div>
								<div class="">
									<div class="container status-box first">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
											<span class="product-sub-title">Available</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail">
											<?php
												$total_available = get_array_value($data,"total_available","0");
												// echo "total_available = $total_available<BR>";
												// if($total_available == "unlimited"){
												// 	echo "Unlimited";
												// }else if($total_available == "0"){
												// 	echo "Out of library shelf!";
												// }else{
												// 	echo "Only ".$total_available." more copies!";
												// }
												switch ($total_available) {
													case '0':
														echo "Out of library shelf!";
														break;
													case 'unlimited':
														echo "Unlimited";
														break;
													default:
														echo "Only ".$total_available." more copies!";
														break;
												}


												// if(is_var_array($remark_list)){
												// 	foreach ($remark_list as $desc) {
												// 		echo ''.$desc.'&nbsp;';
												// 	}
												// }
											?>
											</div>
										</div>
									</div>
								</div>
								<div class="">
									<div class="container status-box">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
											<span class="product-sub-title">Status</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail status">
											<?php 
												if(is_var_array($description_list)){
													foreach ($description_list as $desc) {
														echo ''.$desc.'&nbsp;';
													}
												}else if(is_var_array($remark_list) && is_blank($button)){
													echo ''.$remark_list[0].'&nbsp;';
												}

												switch ($button) {
													case 'required_login':
																echo '<a class="btn btn-md btn-default" href="'.site_url('login').'">';
																echo '<i class="fa fa-lock"></i>&nbsp;&nbsp;';
																echo 'Please login';
																echo '</a>';
														break;
													
													case 'add_to_cart':
																echo '<a class="btn btn-md btn-primary" onclick="ajax_add_to_basket(\''.$product_type_cid.'\', \''.$copy_aid.'\',\'1\')">';
																echo '<i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;';
																echo 'Add to cart';
																echo '</a>';
														break;
													
													case 'add_to_cart_disabled':
																echo '<a class="btn btn-md btn-primary" disabled>';
																echo '<i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;';
																echo 'Add to cart';
																echo '</a>';
														break;
													
													case 'add_to_shelf':
																echo '<a class="btn btn-md btn-danger" onclick="addToShelf(\''.$product_type_cid.'\', \''.$copy_aid.'\')">';
																echo '<i class="fa fa-bookmark"></i>&nbsp;&nbsp;';
																echo 'Add to my bookshelf';
																echo '</a>';
														break;
													
													case 'read':
																$url = site_url($product_type_cid.'/show-product/'.$copy_aid);
																echo '<a class="btn btn-md btn-warning" href="'.$url.'" target="blank">';
																echo '<i class="fa fa-play-circle"></i>&nbsp;&nbsp;';
																echo 'Read this book';
																echo '</a>';
														break;
													
													case 'reserve':
																$queue = get_array_value($data,"queue","0");
																$my_queue = get_array_value($data,"my_queue","0");
																if($my_queue > 0){
																	switch ($my_queue) {
																		case '1':
																			echo 'You are next.';
																			break;
																		
																		default:
																			echo 'Your queue number is '.$my_queue.'';
																			break;
																	}
																}else{
																	echo '<a class="btn btn-md btn-primary" onclick="reserveProduct(\''.$product_type_cid.'\', \''.$copy_aid.'\')">';
																	echo '<i class="fa fa-pencil"></i>&nbsp;&nbsp;';
																	echo 'Reserve (waiting list)';
																	echo '</a>&nbsp;';
																	if($queue > 0){
																		echo $queue.' people in waiting list.';
																	}else{
																		echo 'No one in waiting list.';
																	}
																}
														break;
													
													default:
														break;
												}

											?>
											</div>
										</div>
									</div>
								</div>
							</p>
							<aside class="shadow-box clearfix mt20">
								<div class="inner-box">
									<div class="">   
										<div class="fleft">   
											<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/share.png" />
										</div>
										<!-- AddThis Button BEGIN -->
										<div class="fleft addthis_toolbox addthis_default_style addthis_32x32_style" style="margin-top:18px; margin-left:20px;">
										<a class="addthis_button_facebook"></a>
										<a class="addthis_button_twitter"></a>
										<a class="addthis_button_pinterest_share"></a>
										<a class="addthis_button_google_plusone_share"></a>
										<a class="addthis_button_email"></a>
										<a class="addthis_counter addthis_bubble_style"></a>
										</div>
										<?php if(CONST_MODE != '2'){ ?>
										<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
										<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52f20c1e5ce5970f"></script>
										<?php } ?>
										<!-- AddThis Button END -->
									</div>
								</div><!--inner-box-->
							</aside><!--end:widget-->	
						</div>
						<?php }else if($product_main_url == 'data-subscription'){ ?>
						<div class="col-sm-12 col-md-8 detail-box">
							<p class="spaceUp">
								<div class="">
									<?php 
										$ext_source = get_array_value($item_result,"ext_source","");
										$publish_date = get_datetime_pattern('Y-m-d', get_array_value($item_result,"publish_date",""), "");
										$expired_date = get_datetime_pattern('Y-m-d', get_array_value($item_result,"expired_date",""), "");
										$today = date('Y-m-d');
										if($publish_date <= $today && $expired_date > $today && !is_blank($ext_source)){ 
									?>
										<a class="btn btn-lg btn-info" href="<?=site_url('show-data-subscription?type='.$product_type_cid.'&id='.get_array_value($item_result,"aid",""))?>" target="blank">
										&nbsp;&nbsp;&nbsp;Access&nbsp;&nbsp;&nbsp;
										</a>
										&nbsp;&nbsp;&nbsp;
									<?php } ?>
									<a class="btn btn-lg btn-success" href="<?=site_url('ask-librarian/book-and-product-inquiry?type='.$product_type_cid.'&id='.get_array_value($item_result,"aid",""))?>">
									&nbsp;&nbsp;&nbsp;Ask Librarian&nbsp;&nbsp;&nbsp;
									</a>
								</div>
							</p>
						</div>
						<?php }else{ 
							$product_copy_list = get_array_value($item_result,"product_copy_list","");
							if(is_var_array($product_copy_list)){
								$product_copy_list = $product_copy_list[0];
							}
						?>
						<div class="col-sm-12 col-md-8 detail-box">
							<p class="spaceUp">
								<div class="">
									<div class="container status-box-header">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding first">
											<span class="product-sub-title ">Type</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail status">
												<?=get_array_value($item_result,"copy_type_minor_show","-");?>
											</div>
										</div>
									</div>
								</div>
								<div class="">
									<div class="container status-box first">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
											<span class="product-sub-title">Status</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail status">
												<?=get_array_value($product_copy_list,"shelf_status_name","N/A");?>
											</div>
										</div>
									</div>
								</div>
								<div class="">
									<div class="container status-box">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
											<span class="product-sub-title">&nbsp;</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail">
											</div>
										</div>
									</div>
								</div>
							</p>
							<aside class="shadow-box clearfix mt20">
								<div class="inner-box">
									<div class="">   
										<div class="fleft">   
											<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/share.png" />
										</div>
										<!-- AddThis Button BEGIN -->
										<div class="fleft addthis_toolbox addthis_default_style addthis_32x32_style" style="margin-top:18px; margin-left:20px;">
										<a class="addthis_button_facebook"></a>
										<a class="addthis_button_twitter"></a>
										<a class="addthis_button_pinterest_share"></a>
										<a class="addthis_button_google_plusone_share"></a>
										<a class="addthis_button_email"></a>
										<a class="addthis_counter addthis_bubble_style"></a>
										</div>
										<?php if(CONST_MODE != '2'){ ?>
										<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
										<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52f20c1e5ce5970f"></script>
										<?php } ?>
										<!-- AddThis Button END -->
									</div>
								</div><!--inner-box-->
							</aside><!--end:widget-->	
						</div>
						<?php } ?>
					</div>
				</div>
			</section>
		</section>

		<?php if(is_var_array($biblio_field_result)){ ?>
		<section id="marc">
			<section id="content" class="mt30 pb30">
				<div class="container">
					<div class="row mb30">
						<div class="col-lg-12">
							<div class="review-title button" onclick="toggleMarc()">
								<div class="col-lg-10">MARC Information</div>
								<div class="col-lg-2 review-span a-right"><i id="marc-arrow" class="fa fa-plus"></i></div>
							</div>
						</div>
					</div>
					<div id="marc-content" class="marc-box hide">
						<?php 
							foreach ($biblio_field_result as $field) { 
								$tag = get_array_value($field,"tag","");
								$subfield_cd = get_array_value($field,"subfield_cd","");
								$name = get_array_value($field,"name","");

								$tag_name = trim($tag." ".$subfield_cd);
								if(!is_blank($tag_name)){
									$tag_name .= " : ".$name;
								}else{
									$tag_name = $name;
								}

						?>
							<div class="row mb10">
								<div class="col-sm-3"><div class="marc-title"><?=$tag_name;?>&nbsp;</div></div>
								<div class="col-sm-9"><div class="marc-body"><?=get_array_value($field,"field_data_link","");?>&nbsp;</div></div>
							</div>
						<?php } ?>
					</div>
				</div>
			</section>
		</section>
		<?php } ?>

	<form id="frm_review" name="frm_review">
		<input type="hidden" id="product_type_aid" name="product_type_aid" value="<?=get_array_value($item_result,"product_type_aid","")?>" />
		<input type="hidden" id="parent_aid" name="parent_aid" value="<?=get_array_value($item_result,"aid","")?>" />
		<input type="hidden" id="max_rate" name="max_rate" value="<?=CONST_REVIEW_MAX_POINT?>" />
		<section id="review">
			<section id="content" class="mt30 pb30">
				<div class="container">
					<div class="row mb30">
						<div class="col-lg-12">
							<div class="review-title" id="review_summary_area">No Reviews</div>
						</div>
					</div>
					<div id="review_list_area">There are no reviews yet, why not be the first.</div>

					<div id="review_form_area">
					<?php if(!is_login()){ ?>
						<BR/>Please <a href="<?=site_url('login')?>">Login</a> before write a review.
					<?php }else{ ?>
						<div class="row mt30 pt30 mb20">
							<div class="col-lg-12">
								<div class="review-title">Write a new review by <span class="name"><?=getUserLoginFullName($user_login_info)?></span></div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12 review-form mb20">
								<?php $user_login_info["avatar_mode"] = "thumb"; ?>
								<div class="avatar col-lg-2"><?=getUserLoginAvatar($user_login_info)?></div>
								<div class="form col-lg-10"><textarea id="description" name="description"></textarea></div>
							</div>
							<div class="col-lg-12 review-button">
								Click to Rate : 
								<span class="rating">
									<span class="star" id="rate-5" data-rate="5" onclick="drawReviewPoint('5', '<?=CONST_REVIEW_MAX_POINT?>')"></span>
									<span class="star" id="rate-4" data-rate="4" onclick="drawReviewPoint('4', '<?=CONST_REVIEW_MAX_POINT?>')"></span>
									<span class="star" id="rate-3" data-rate="3" onclick="drawReviewPoint('3', '<?=CONST_REVIEW_MAX_POINT?>')"></span>
									<span class="star" id="rate-2" data-rate="2" onclick="drawReviewPoint('2', '<?=CONST_REVIEW_MAX_POINT?>')"></span>
									<span class="star" id="rate-1" data-rate="1" onclick="drawReviewPoint('1', '<?=CONST_REVIEW_MAX_POINT?>')"></span>
								</span>
								<input type="hidden" id="point" name="point" value="0" />
								<button type="button" name="btn_login" id="btn_login" class="btn btn-success" onClick="saveReview()">Post Review </button>
							</div>
						</div>						
					<?php } ?>
					</div>
				</div>
			</section>
		</section>
	</form>
<script type="text/javascript">
	jQuery(document).ready(function($){
		showReview();
		<?=@$message?>
		<?=@$js_code?>		
	});
</script>