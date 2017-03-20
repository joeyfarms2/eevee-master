<?php
	$item_result = @$item_result;
	$product_type_detail = @$product_type_detail;
	$product_main_url = get_array_value($item_result,"product_main_url","none");
	
	$product_type_aid = get_array_value($product_type_detail,"aid","1");

	$has_license = get_array_value($item_result,"has_license","0");
	
?>	
<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/product.js"></script>
<script type="text/javascript" src="<?=JS_PATH?>product/product_front/review.js"></script>
		
		<section id="page">
			<section id="content" class="mt30 pb30">
				<div class="container">
					<div class="row mb30">
						<div class="col-lg-12">
							<div class="product-title">
								<i class="fa fa-circle"></i>&nbsp;
									<?=get_array_value($item_result,"title","")?>&nbsp;
								<span class="status-box-detail status">
									<?php $review_point = get_array_value($item_result,"review_point","0"); ?>
									<span class="rating-show">
										<span class="star <?=($review_point >= 5) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 4) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 3) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 2) ? "focus" : ""?>"></span>
										<span class="star <?=($review_point >= 1) ? "focus" : ""?>"></span>
									</span>
								</span>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="product-author">
								&nbsp;&nbsp;<a href="<?=site_url('search/'.get_array_value($item_result,"author",""))?>?search_in=author"><?=get_array_value($item_result,"author","")?></a>&nbsp;
							</div>
						</div>
					</div>
					<div class="row mb30">
						<div class="col-lg-8"> 

								<?php
									$upload_path = get_array_value($item_result,"upload_path","")."file/";
									$file_upload = get_array_value($item_result,"uri","");
									$file = $upload_path.$file_upload;
									// echo "path : ".$file;
									$ext_source =  get_array_value($item_result,"ext_source","");
									// echo "ext_source = $ext_source<BR>";
									if(preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $ext_source, $matches) ){
										//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
										// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
										$ext_source = $matches[1];
									}
									if(strstr($ext_source, "https://youtu.be/")){
										//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
										// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
										$ext_source = substr(strrchr($ext_source,"/"),1);
									}
									
									if(is_file($file)){
									?>
										<video id="my_video_1" class="cover-vdo-detail border-box video-js vjs-default-skin" autoplay="autoplay" controls
											preload="auto" width="745" height="440" poster=""
											data-setup="{}">
											<source src="<?=site_url($file)?>" id="vdo_mp4">
										</video>	
									<?php 
									}elseif(!is_blank($ext_source)){
									?>
										<!-- <iframe class="cover-vdo-detail" width="745" height="440" src="http://www.youtube.com/embed/<?=$ext_source?>" frameborder="0" allowfullscreen></iframe> -->
										<iframe class="cover-vdo-detail" width="745" height="440" src="http://www.youtube.com/embed/<?=$ext_source?>" frameborder="0" allowfullscreen></iframe>
									<?php
									}else{
								?>
								<div class="cover-vdo-detail">
									<img src="<?=get_image(get_array_value($item_result,"cover_image_ori_path",""),"vdo-ori")?>" class="" /> 
								</div>
								<?php } ?>
						</div>
						<?php if(!is_blank(get_array_value($item_result,"description",""))){ ?>
						<div class="col-lg-4">
							<p class="description-vdo"><?=get_array_value($item_result,"description","")?></p>
						</div>
						<?php } ?>
					</div>
				</div>
			</section>
		</section>

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