<?php
	$product_home_result = @$product_home_result;

	$news_cat_result = @$news_cat_result;

	$latest_news_result = @$latest_news_result;

	$news_highlight_list = @$news_highlight_list;

	//print_r($news_highlight_list);
	//$news_recommended_list = @$news_recommended_list;

	//$news_talk_of_the_town_list = @$news_talk_of_the_town_list;

	//print_r($news_talk_of_the_town_list);

?>
<div id="message-box">
	<div id="result-msg-box" class="hidden" ></div>
</div>

<section id="projects">
	<div class="container mt15">
		
		<div class="row">
			<div class="col-md-8 pln">
				<div class="fleft custom-home-box-header"><h2>News</h2></div>
			</div>
		</div>

		<div>
		<?php if(is_var_array($latest_news_result)) { ?>
			<?php 
				foreach($latest_news_result as $item){
					//print_r($item);
			?>

			<!-- Start news box -->
				
				<!-- Start news content -->
				<div class="custom-news-home-box">
					<div class="custom-news-home-box-left">
						<?php if (get_array_value($item,"cover_image_file_type","") != "") {
					    	$cover_image_full_path = './'.get_array_value($item,"cover_image_actual","");
					    	if (file_exists($cover_image_full_path)) {
					    ?>
							    	<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>">
							    		<img src='<?=get_image(get_array_value($item,"cover_image_actual",""), "-actual", get_array_value($item,"large_image",""))?>' />
							    	</a>
						    
					    <?php } else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
	    					
				    			<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>">
				    				<?=$item['dummy_cover_image']?>
				    			</a>
				    		
				 		<?php } 
				 		}else if ( get_array_value($item,"ref_link2_image_url","") != "" ) { ?>
		 					
		 						<img src='<?=get_array_value($item,"ref_link2_image_url")?>' class="img-responsive" style="margin:auto;"/>
		 					
						<?php
						}
				 		else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
				 			
				    			<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>">
				    				<?=$item['dummy_cover_image']?>
				    			</a>
				    		
				 		<?php } ?>
					</div>
					<div class="custom-news-home-box-right">
						<div class="custom-news-home-box-text">
							<div class="custom-news-home-box-text-one">
								<?php if (!empty($item['title'])) { ?>
									    	<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>">
									    		<h3 class="txt-blue"><?=get_array_value($item,"title","")?></h3>
									    	</a>	
						    	<?php } ?>
							</div>
							<div class="custom-news-home-box-text-one">
								
									<?=get_array_value($item,"short_description","")?>
								
							</div>
						</div>
						<div class="custom-news-home-box-text-two">
							<div class="custom-news-home-box-day">
					        		<?=get_datetime_pattern("db_date_format",get_array_value($item,"publish_date",""),"");?>
					        </div>
					        <div class="custom-news-home-box-user">
					        		<?=get_array_value($item,"full_name_th","")?>
					        </div>
						</div>

					</div>

				</div>
 
				   <!-- End news content -->

				<!-- End news box -->

			<?php 
				} //foreach
			}else{
			?>
			<?php $message = set_message_error("Oops! It looks like no any news feed at the moment.")?>
			<?php } ?>
		</div>
		<hr/>
		<div class="custom-news-home-box-small">
			<div class="custom-news-home-box-small-text-left">ข่าวอื่นๆที่หน้าสนใจ</div>
			<div class="custom-news-home-box-small-text-right"><a href="<?=site_url('news')?>">อ่านต่อ...</a></div>
			<?php 
				if(is_var_array($news_highlight_list)) { 

			?>
			<?php 
					foreach($news_highlight_list as $item_highlight){
					//print_r($item_highlight);
			?>
				<div class="custom-news-home-box-small-left">
					<div class="custom-news-home-box-small-news">
						
							<?php if (get_array_value($item_highlight,"cover_image_file_type","") != "") {
						    	$cover_image_full_path = './'.get_array_value($item_highlight,"cover_image_actual","");

						    	if (file_exists($cover_image_full_path)) {
						    ?>
								    	<a href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
								    		<img src='<?=get_image(get_array_value($item_highlight,"cover_image_actual",""), "-actual", get_array_value($item_highlight,"large_image",""))?>' />
								    	</a>
							    
						    <?php } else if(isset($item_highlight['dummy_cover_image']) && !empty($item_highlight['dummy_cover_image'])) { ?>
		    					
					    			<a href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
					    				<?=$item_highlight['dummy_cover_image']?>
					    			</a>
					    		
					 		<?php } 
					 		}else if ( get_array_value($item_highlight,"ref_link2_image_url","") != "" ) { ?>
			 					
			 						<img src='<?=get_array_value($item_highlight,"ref_link2_image_url")?>' class="img-responsive" style="margin:auto;"/>
			 					
							<?php
							}
					 		else if(isset($item_highlight['dummy_cover_image']) && !empty($item_highlight['dummy_cover_image'])) { ?>
					 			
					    			<a href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
					    				<?=$item_highlight['dummy_cover_image']?>
					    			</a>
					    		
					 		<?php } ?>
					 	

					</div>
					<div class="custom-news-home-box-small-news">
						<?php if (!empty($item_highlight['title'])) { ?>
								<a href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
									<h4 class="txt-blue"><?=get_array_value($item_highlight,"title","")?></h3>
								</a>	
						<?php } ?>
					</div>
				</div>
			<?php	
				}
			}
			?>
		</div>

	</div>
</section>

<?php 
if(is_var_array($product_home_result)){
	foreach($product_home_result as $item){
		$result_list = get_array_value($item,"result_list","");
		$product_type_cid = get_array_value($item,"product_type_cid","");
		if($product_type_cid == "vdo"){
			$class = "vdo";
		}else{
			$class = "book";
		}
?>	
	<section id="projects">
		<div class="container mt15 <?=$class?>-box">
			<div class="row">
				<div class="col-md-8">
					<div class="fleft custom-home-box-header"><h2><?=get_array_value($item,"product_main_name","N/A")?></h2></div>
					<!-- <div class="fleft custom-home-box-header"><h3>All Latest <?=get_array_value($item,"product_main_name","N/A")?></h3></div> -->
				</div>
				<div class="col-md-4">
					<a class="custom-home-box-seeall" href="<?=site_url('list-'.get_array_value($item,"product_type_cid","none").'/category/'.get_array_value($item,"product_main_url","none"))?>">See All</a>
				</div>
			</div>

			<div class="row <?=$class?>-item">
				<section class="pb30 imgHover">
					<article class="item text-center first">
						<?=draw_item(get_array_value($result_list,"0",""));?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($result_list,"1",""));?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($result_list,"2",""));?>
					</article>
			</section>
			</div>
			<div class="row <?=$class?>-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?=draw_item(get_array_value($result_list,"3",""));?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($result_list,"4",""));?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($result_list,"5",""));?>
					</article>
			</section>
			</div>
			
		</div>
	</section>
<?php 
	} //foreach
}else{
?>
<?php $message = set_message_error("Sorry, Product main not found. Please contact your administrator.")?>
<?php } ?>

<?php 
function draw_item($item){
	$product_type_cid = get_array_value($item,"product_type_cid","");
	$cover_class = "";
	if($product_type_cid == 'vdo'){
		$cover_class = "vdo-";
	}
	$txt = '';
	$txt .=	'<section class="boxContent">';
	if(is_var_array($item)){
		$txt .=	'<ul class=""><li><i class="fa fa-circle"></i>&nbsp;&nbsp;&nbsp;<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'" title="'.removeAllQuote(get_array_value($item,"title","N/A")).'" >'.get_array_value($item,"title_short","N/A") .'</a></li></ul>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	$txt .=	'<section class="imgWrapper">';
	if(is_var_array($item)){
		$is_license = get_array_value($item,"has_license","0");
		if($is_license == '1'){
			$txt .= '<div class="mask-ipad"></div>';
		}
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb", get_array_value($item,"thumbnail_image","")).'" class="img-responsive"></a>';
		
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	return $txt;
}
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>
