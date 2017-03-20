<?php
	$item_result = @$item_result;
	$product_type_detail = @$product_type_detail;
	
	$product_main_url = get_array_value($item_result,"product_main_url","none");
	$product_main_aid = get_array_value($item_result,"product_main_aid","");
	// echo "<pre>";
	// print_r($item_result);
	// echo "</pre>";
	
	$product_type_aid = get_array_value($product_type_detail,"aid","1");

	$has_license = get_array_value($item_result,"has_license","0");
	$biblio_field_result = get_array_value($item_result,"biblio_field_result","");

	$data = getimagesize(get_image(get_array_value($item_result,"cover_image_detail_path",""),"detail", get_array_value($item_result,"large_image","")));
	$width = $data[0];
	$height = $data[1];
	$description = get_array_value($item_result,"description","");
	$width_2 = (($width/3));
	$height_2 = (($height/5)-20);


?>	
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/product.js?eji2o"></script>
<script type="text/javascript" src="<?=JS_PATH?>product/product_front/review.js"></script>
		
		<section id="page">
			<section id="content" class="mt30 pb30">
				<div class="container">
					<div class="row mb30">
						<div class="col-lg-12">
							<div class="product-title"><i class="fa fa-circle"></i>&nbsp;<?=get_array_value($item_result,"title","")?>&nbsp;</div>
						</div>
						<div class="col-lg-12">
							<div class="product-author"><a href="<?=site_url('search/'.get_array_value($item_result,"author",""))?>?search_in=author"><?=get_array_value($item_result,"author","")?></a>&nbsp;</div>
						</div>
					</div>
					<div class="row mb30">
						<div class="col-sm-12 col-md-4" style="text-align: center;"> 
							<div class="cover-detail">

								<?php if($width < $height){ ?>
										<?php if($has_license == '1'){ ?>
													<div class="mask-ipad2" style="margin-left:<?=$width_2?>px;margin-top: 35%"></div>
										<?php } ?>
												<img src="<?=get_image(get_array_value($item_result,"cover_image_detail_path",""),"detail",get_array_value($item_result,"large_image",""))?>" class="" /> 
								<?php }else{ ?>
										<?php if($has_license == '1'){ ?>
													<div class="mask-ipad2" style="margin-top:<?=$height_2?>px;margin-left: 30%;"></div>
										<?php } ?>
												<img src="<?=get_image(get_array_value($item_result,"cover_image_detail_path",""),"detail",get_array_value($item_result,"large_image",""))?>" class="cover-detail-w" /> 	
								<?php } ?>
							</div>
						</div>
						<div class="col-sm-12 col-md-8">
							<?php if(!is_blank($description)){?>
								<p class="description"><?=get_array_value($item_result,"description","")?></p>
							<?php }else{?>
								<div style="margin-top:-20px;"></div>
							<?php
								}
								if($product_main_aid == '7'){
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
								<?php
									if($product_main_aid == '1' || $product_main_aid == '2' || $product_main_aid == '6' || $product_main_aid == '8'){
								?>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Call No.</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"call_number","-")?>
								</div>
								<?php
									}
								?>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title"><?=$publish_date_title?></span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item_result,"publish_date",""), "-")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Publisher</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"publisher_name","-")?>
								</div>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Category</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"category_link","-")?>
								</div>
								<?php
									if($product_main_aid == '8'){
								?>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Series</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"total_page","-")?>
								</div>
								<?php }else{ ?>
								<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
									<span class="product-sub-title">Page</span>
								</div>
								<div class="col-xs-7 col-sm-10 col-md-8 col-lg-4 no-left-padding no-right-padding">
									: <?=get_array_value($item_result,"total_page","-")?>
								</div>
								<?php } ?>
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
							if(is_var_array($digital_list) && $product_main_aid != '7' && $product_main_url != 'online-book'){
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
								<!-- <div class="">
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
								</div> -->
								<div class="">
									<div class="container status-box first">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding">
											<span class="product-sub-title-big">Available</span>
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
											<span class="product-sub-title-big">Status</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail status">
												
											<?php 
												if(is_var_array($description_list)){
													foreach ($description_list as $desc) {
														echo '&nbsp;&nbsp;'.$desc.'&nbsp;';
													}
												}else if(is_var_array($remark_list) && is_blank($button)){
													echo '&nbsp;&nbsp;'.$remark_list[0].'&nbsp;';
												}

												switch ($button) {
													case 'required_login':
																echo '<a class="btn btn-md btn-default subTitleRadius" href="'.site_url('login').'">';
																echo '<i class="fa fa-lock"></i>&nbsp;&nbsp;';
																echo 'Please login';
																echo '</a>';
														break;
													
													case 'add_to_cart':
																echo '<a class="btn btn-md btn-primary subTitleRadius" onclick="ajax_add_to_basket(\''.$product_type_cid.'\', \''.$copy_aid.'\',\'1\')">';
																echo '<i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;';
																echo 'Add to cart';
																echo '</a>';
														break;
													
													case 'add_to_cart_disabled':
																echo '<a class="btn btn-md btn-primary subTitleRadius" disabled>';
																echo '<i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;';
																echo 'Add to cart';
																echo '</a>';
														break;
													
													case 'add_to_shelf':
																echo '<a class="btn btn-md btn-danger subTitleRadius" onclick="addToShelf(\''.$product_type_cid.'\', \''.$copy_aid.'\')">';
																echo '<i class="fa fa-bookmark"></i>&nbsp;&nbsp;';
																echo 'Add to my bookshelf';
																echo '</a>';
														break;
													
													case 'read':
																$url = site_url($product_type_cid.'/show-product/'.$copy_aid);
																echo '<a class="btn btn-md btn-warning subTitleRadius" href="'.$url.'" target="blank">';
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
																	echo '<a class="btn btn-md btn-primary subTitleRadius" onclick="reserveProduct(\''.$product_type_cid.'\', \''.$copy_aid.'\', \'digital\')">';
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
											<!-- <img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/share.png" /> -->
											<h3 class="product-sub-title" style="margin-top:5px;">SHARE ON SOCIAL MEDIA</h3>
										</div>
										<!-- AddThis Button BEGIN -->
										<div class="fleft addthis_toolbox addthis_default_style addthis_32x32_style" style="margin-left:20px;">
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
						<?php }else if($product_main_aid == '7'){ ?>
						
							
									<?php 
										$ext_source = get_array_value($item_result,"ext_source","");
										$publish_date = get_datetime_pattern('Y-m-d', get_array_value($item_result,"publish_date",""), "");
										$expired_date = get_datetime_pattern('Y-m-d', get_array_value($item_result,"expired_date",""), "");
										$today = date('Y-m-d');
										if($publish_date <= $today && $expired_date > $today && !is_blank($ext_source)){ 

									?>
									<div class="col-sm-12 col-md-8 detail-box">
										<p class="spaceUp">
											<div class="">
												<a class="btn btn-lg btn-info subTitleRadius" href="<?=site_url('show-knowledge-resources?type='.$product_type_cid.'&id='.get_array_value($item_result,"aid",""))?>" target="blank">
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Access&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												</a>
											</div>
										</p>
									</div>
									<?php } ?>
									<div class="col-sm-12 col-md-8 detail-box">
										<p class="spaceUp">
											<div class="">
												<a class="btn btn-lg btn-success subTitleRadius" href="<?=site_url('ask-librarian/book-and-product-inquiry?type='.$product_type_cid.'&id='.get_array_value($item_result,"aid",""))?>">
												&nbsp;&nbsp;&nbsp;Ask Librarian&nbsp;&nbsp;&nbsp;
												</a>
											</div>
										</p>
									</div>
						<?php }else if($product_main_aid == '1' || $product_main_aid == '2' || $product_main_aid == '8'){
							
						?>	
						<div class="col-sm-12 col-md-8 detail-box">
							<p class="spaceUp">
								<table cellpadding="1" cellspacing="1" border="2" width="700px" class="table_book">
									<tr class="head_box_book">
										<td class="head_box_book_padding" width="120px"><center>Barcode</center></td>
										<td class="head_box_book_padding" width="320px"><center>Title of Copy</center></td>
										<td class="head_box_book_padding" width="120px"><center>Status</center></td>
										<td class="head_box_book_padding"></td>
									</tr>
						<?php
							$product_copy_list = get_array_value($item_result,"product_copy_list","");
							//print_r($product_copy_list);
							if(is_var_array($product_copy_list)){
								foreach($product_copy_list as $m_item){	

						?>
									<tr class="" height="38px">
										<td class="box_book_padding" width="120px"><center><?=get_array_value($m_item,"barcode","")?></center></td>
										<td class="box_book_padding" width="320px"><?=get_array_value($item_result,"title","")?>  <?=get_array_value($m_item,"copy_title","");?></td>
										<td class="box_book_padding" width="120px" <?php if(get_array_value($m_item,"shelf_status_name","") == "Borrowed") echo "style='color:red;'";?>><center><?=get_array_value($m_item,"shelf_status_name","");?></center>
										<center><span style='color:#777777;'><?=get_array_value($m_item,"shelf_location_name","")?></span></center>
										</td>
										<td class="box_book_padding">
										<?php
											if(!is_login()){
										?>
											<center>
												<a class="btn btn-success box_book_btn" href="<?=site_url('login')?>">
													&nbsp;Login&nbsp;
												</a>
											</center>
										<?php
											}else if(get_array_value($m_item,"my_turn","") == "1"){
										?>
											Pick up within <?=get_array_value($m_item,"my_expiration_date","")?>
										<?php
											}else if(get_array_value($m_item,"my_queue","") <= 0){

												if(getUserLoginSectionAid($this->user_login_info) == "1"){
										?>
													<center>
														<span style="color:red;">สอบถามบรรณารักษ์</span>
														
															<!-- &nbsp;Reserve&nbsp; -->
													</center>
										<?php  
												}else{
										?>
													<center>
														<a class="btn btn-success box_book_btn" onclick="reserveProduct('<?=$product_type_cid?>','<?=get_array_value($m_item,"aid","0")?>','product')">
															&nbsp;Reserve&nbsp;
														</a>
													</center>
										<?php } ?>
										</td>
										<?php
											}else{
												/*
												$queue = get_array_value($m_item,"queue","0");
												$my_queue = get_array_value($m_item,"my_queue","0");
												switch ($my_queue) {
													case '1':
														// echo 'You are next.';
														echo '<center>Reserved.</center>';
														break;
													
													default:
														// echo 'Your queue number is '.$my_queue.'';
													echo '<center>Reserved.</center>';
														break;
												}
												*/
										?>
											<center>
												<a class="btn btn-success box_book_cancel_btn" onclick="cancelReserveProduct('<?=$product_type_cid?>','<?=get_array_value($m_item,"aid","0")?>','product')">
													&nbsp;Cancel Reserve&nbsp;
												</a>
											</center>
										<?php
											}
										?>
										
									</tr>
						<?php
									}
							}
						?>
									
								</table>
							</p>
						</div>
						<?php
						}else{ 
							$product_copy_list = get_array_value($item_result,"product_copy_list","");
							if(is_var_array($product_copy_list)){
								$product_copy_list = $product_copy_list[0];
							}
						?>
						<!-- <div class="col-sm-12 col-md-8 detail-box">
							<p class="spaceUp">
								<div class="">
									<div class="container status-box-header">
										<div class="col-xs-5 col-sm-2 col-md-4 col-lg-2 no-left-padding no-right-padding first">
											<span class="product-sub-title ">Type</span>
										</div>
										<div class="col-xs-7 col-sm-10 col-md-8 col-lg-10 no-left-padding no-right-padding">
											<div class="status-box-detail status">
												< ?=get_array_value($item_result,"copy_type_minor_show","-");?>
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
												< ?=get_array_value($product_copy_list,"shelf_status_name","N/A");?>
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
											<img src="< ?=CSS_PATH?><?=CONST_CODENAME?>/images/background/share.png" />
										</div>
										< !- - AddThis Button BEGIN - ->
										<div class="fleft addthis_toolbox addthis_default_style addthis_32x32_style" style="margin-top:18px; margin-left:20px;">
										<a class="addthis_button_facebook"></a>
										<a class="addthis_button_twitter"></a>
										<a class="addthis_button_pinterest_share"></a>
										<a class="addthis_button_google_plusone_share"></a>
										<a class="addthis_button_email"></a>
										<a class="addthis_counter addthis_bubble_style"></a>
										</div> -->
										<?php //if(CONST_MODE != '2'){ ?>
										<!-- 
										<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
										<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52f20c1e5ce5970f"></script>
										-->
										<?php  //} ?>
										<!-- AddThis Button END -->
									<!-- </div>
								</div>< !- -inner-box- - >
							</aside>< !- -end:widget- - >	
						</div> -->
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
							// echo "<pre>";
							// print_r($biblio_field_result);
							// echo "</pre>";
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
								if(get_array_value($field,"field_data_link","") != ""){


						?>
							<div class="row mb10">
								<div class="col-sm-3"><div class="marc-title"><?=$tag_name;?>&nbsp;</div></div>
							<?php
								if($tag == "856"){
							?>
								<div class="col-sm-9"><div class="marc-body" style="white-space: nowrap;overflow: hidden;text-overflow:ellipsis;"><?=get_array_value($field,"field_data_link","");?>&nbsp;</div></div>
							
							<?php
								}else{
							?>
								<div class="col-sm-9"><div class="marc-body" ><?=get_array_value($field,"field_data_link","");?>&nbsp;</div></div>
							
						<?php 
								}
						?>

							</div>
						<?php 
								}
							}
						?>
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