<?php
	$product_home_result = @$product_home_result;

	$news_cat_result = @$news_cat_result;

	$latest_news_result = @$latest_news_result;

	$news_highlight_list = @$news_highlight_list;

	// echo "<pre>";
	// print_r($latest_news_result);
	// echo "</pre>";
	//print_r($news_highlight_list);
	//$news_recommended_list = @$news_recommended_list;

	//$news_talk_of_the_town_list = @$news_talk_of_the_town_list;

	//print_r($news_talk_of_the_town_list);
	
?>
<style>
.img-head-news
{
	width: inherit;
	height: auto;
}

.img-head-news img
{
	max-width: ;
	min-width: ;
	height: auto;
}

.text-center
{
	margin-bottom: 20px;
}

.latest-news-header
{
	text-indent: 2em;
}

.bg-gray
{
	background-color: #f6f6f6;
	padding: 10px 0px 10px 0px;
}
.bg-gray-box
{
	background-color: #f6f6f6;
	height: 150px;
	width: 405px;
	padding: 10px;
	/*line-height: 150px;
	vertical-align: middle;*/
}
.box-left
{
	margin-right: 0px;
}
.news-box
{
	position: relative;
	height: 130px;
	/*line-height: 130px;*/
	display: table-cell;
	vertical-align: middle;
}
.news-link
{
	/*width: 168px;*/
}
.news-box img
{
	/*position: absolute;
	max-width:168px;
	max-height: 130px;
	margin:0 auto;*/
	max-width:100%;
	max-height: 100%;
}

.news-box span
{
	line-height: 20px;
}

.interesting-news-head
{
	color:#f35607; 
	font-size:20px;
}
.title-manu-home
{
	/*display: none;*/
	width: 100%;
    height:25px;
    border: none;
    background-repeat: no-repeat;
}
.show-title-manu-home
{
	/*display: none;*/
	width: 100%;
    height:25px;
    background-position: 0 0;
}

.boxContent-head
{
	width: 100%;
	height:3.4em;
	text-align: center;
}

.boxContent-head a
{
	color: #000;
	font-size: 16px;
}

.boxContent-head a:hover
{
	color: #000;
	text-decoration: none;
}
.imgWrapper
{
	width: 178px;
	height: 250px;
	text-align: center;
	position: relative;
	margin: 0px auto;
}



.imgWrapper img
{
	max-width: 100%;
    max-height: 100%;
    border:1px solid #000;
    position: absolute;
    left: 0px;
    bottom: 2px;
}

.vdoWrapper
{
	width: 200px;
	height: 150px;
	text-align: center;
	position: relative;
	margin: 0px auto;
}

.vdoWrapper img
{
	max-width: 100%;
    max-height: 100%;
    margin: 0px auto;
}

.shelf-under-book-big
{
	width: 100%;
	margin-top: -35px;
	z-index: -100;
}

.shelf-under-book
{
	margin-top: -78px;
	width: 100%;
	height:39px;
	z-index: -100;
}

.shelf-under-book-small
{
	width: 100%;
	height: 39px;
	margin-top: -35px;
	background-image: url(/styles/nia/images/background/bg-shelf-item.png);
	background-repeat: no-repeat;
	z-index: -100;
}

.visible-xs
{
	z-index: -100;	
}
.custom-home-box-seeall:hover
{
	text-decoration: none;
}

@media (min-width:901px) and (max-width:1024px)
{
	.head-image
	{
		position:absolute;
	    clip:rect(0,500px,25px,0);
	}
}
@media (min-width:768px) and (max-width:900px)
{	
	.container
	{
		width: 700px;
	}

	.bg-gray-box
	{
		height: 180px;
		width: 335px;
	}

	.shelf-under-book-big 
	{
		margin-top: -40px;
	}
}
@media (min-width:480px) and (max-width:767px) 
{
	.container
	{
		width: 400px;
	}

	.img-head-news img
	{
		width: 350px;
		height: auto;
	}
	.custom-content-home-box .vdo-item, .custom-content-home-box .book-item
	{
		background: none;
		width: 450px;
	}
	.custom-home-box-seeall h3 
	{
		font-size: 16px;
	}
	
	.boxContent{
		padding: 10px;
	}

	.imgWrapper
	{
		width: 150px;
		height: 200px;
	}

	.imgWrapper img
	{
		max-width: 100%;
	    max-height: 100%;
	    border:1px solid #000;
	    position: absolute;
	    left: 0px;
	    bottom: 2px;
	}

	.shelf-under-book
	{
		margin-top: -56px;
	}


	.vdoWrapper
	{
		width: 250px;
		height: 180px;	
	}
}
@media (min-width:360px) and (max-width:479px)
{
	.container
	{
		width: 350px;
	}

	.img-head-news img
	{
		width: 300px;
		height: auto;
	}
	.bg-gray-box
	{
		width: 350px;
	}
	.custom-content-home-box .vdo-item, .custom-content-home-box .book-item
	{
		background: none;
		width: 350px;
	}
	.custom-home-box-seeall h3 
	{
		font-size: 14px;
		margin-top: 5px;
	}

	.boxContent{
		padding: 10px;
	}

	.imgWrapper
	{
		width: 130px;
		height: 180px;
	}

	.imgWrapper img
	{
		max-width: 100%;
	    max-height: 100%;
	    border:1px solid #000;
	    position: absolute;
	    left: 0px;
	    bottom: 2px;
	}

	.shelf-under-book
	{
		margin-top: -56px;
	}


	.vdoWrapper
	{
		width: 200px;
		height: 150px;	
	}



}
@media (min-width:320px) and (max-width:359px)
{
	.container
	{
		width: 300px;
	}

	.img-head-news img
	{
		width: 250px;
		height: auto;
	}
	.bg-gray-box
	{
		width: 300px;
	}
	.custom-content-home-box .vdo-item, .custom-content-home-box .book-item
	{
		background: none;
		width: 300px;
	}
	.custom-home-box-seeall h3 
	{
		font-size: 12px;
		margin-top: 7px;
	}

	.boxContent{
		padding: 10px;
	}
	
	.imgWrapper
	{
		width: 100px;
		height: 150px;
	}

	.imgWrapper img
	{
		max-width: 100%;
	    max-height: 100%;
	    border:1px solid #000;
	    position: absolute;
	    left: 0px;
	    bottom: 2px;
	}

	.shelf-under-book
	{
		margin-top: -56px;
	}

	.vdoWrapper
	{
		width: 180px;
		height: 150px;
		
	}

	
}
</style>
<div id="message-box">
	<div id="result-msg-box" class="hidden" ></div>
</div>

<section id="projects">
	<div class="container mt15">
		
		<div class="row">
			<div class="col-md-8 pln">
				<div style="margin:20px auto;"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_news.png"></div>
			</div>
		</div>

		<!-- Start news section -->
		<?php /*
		<?php if(is_var_array($latest_news_result)) { ?>
			<?php 
				foreach($latest_news_result as $item){
					//print_r($item);
			?>

			<!-- Start news box -->
			
		<div class="row">		
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
				</div>
				<div class="custom-news-home-box">
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
		</div>
				<!-- End news box -->

			<?php 
				} //foreach
			}else{
			?>
			<?php $message = set_message_error("Oops! It looks like no any news feed at the moment.")?>
			<?php } ?>
		*/?>
		<?php if(is_var_array($latest_news_result)) 
		{ ?>
		<div class="row bg-gray">
			<?php foreach($latest_news_result as $item)
			{ 
				if (get_array_value($item,"cover_image_file_type","") != "") 
				{
					$cover_image_full_path = './'.get_array_value($item,"cover_image_actual","");
					if (file_exists($cover_image_full_path)) 
					{ ?>
					<div class="col-xs-12 text-center">
						<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>" class="img-head-news">
							<img src='<?=get_image(get_array_value($item,"cover_image_actual",""), "-actual", get_array_value($item,"large_image",""))?>' />
						</a>
					</div>
				<?php } 
				} 
				else if(get_array_value($item,"dummy_cover_image","") != "")
				{ ?>
					<div class="col-xs-12 text-center">
						<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>" class="img-head-news">
							<?=get_array_value($item,"dummy_cover_image","")?>
						</a>
					</div>
				<?php }
				else
				{ ?>
				ERROR!
				<?php } ?>
				<div class="col-xs-12">
					<div>
						<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>">
							<h3 class="txt-blue latest-news-header"><?=get_array_value($item,"title","")?></h3>
						</a>
					</div>
					<div>
						<?=get_array_value($item,"short_description","")?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
		<!-- End news section -->

		<!-- Start interesting news section -->
		<?php /*
	<div class="row">
		<div class="custom-news-home-box-small">
			<div class="custom-news-home-box-small-text-left">ข่าวอื่นๆที่หน้าสนใจ</div>
			<div class="custom-news-home-box-small-text-right"><a href="<?=site_url('news')?>">อ่านข่าวทั้งหมด...</a></div>
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
					<div class="custom-news-home-box-small-news" style="padding-left:10px;">
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
	*/ ?>
	<div class="row" style="margin-right:0px; !important">
		<div class="row">
			&nbsp;
		</div>
		<div class="row" style="margin-right:0px; !important">
			<div class="col-xs-6 text-left">
				<label class="interesting-news-head">ข่าวที่น่าสนใจ</label>
			</div>
			<div class="col-xs-6 text-right">
				<a href="/news"><label style="text-decoration:none; color:#cacaca; cursor:pointer; margin-right:5px;">อ่านข่าวทั้งหมด...</label></a>
			</div>
		</div>
		<div class="row" style="margin-right:0px; !important">
			<?php
			if(is_var_array($news_highlight_list)) 
			{
				foreach($news_highlight_list as $item_highlight)
				{
					?>
					<div class="col-sm-6">
						<div class="bg-gray-box">
								<?php if(get_array_value($item_highlight,"cover_image_file_type","") != "") 
								{
									$cover_image_full_path = './'.get_array_value($item_highlight,"cover_image_actual","");
									if(file_exists($cover_image_full_path)) 
									{
										$haveImage = true;
										?>
										<div class="col-xs-6 news-box">
											<a class="news-link" href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
									    		<img src='<?=get_image(get_array_value($item_highlight,"cover_image_actual",""), "-actual", get_array_value($item_highlight,"large_image",""))?>' />
									    	</a>
										</div>
										<?php
									}
									else if(isset($item_highlight['dummy_cover_image']) && !empty($item_highlight['dummy_cover_image'])) 
									{
										$haveImage = true;
										?>
										<div class="col-xs-6 news-box">
											<a class="news-link" href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
							    				<?=$item_highlight['dummy_cover_image']?>
							    			</a>
										</div>
										<?php
									}
									else{
										$haveImage = false;
									}
								}
								else if ( get_array_value($item_highlight,"ref_link2_image_url","") != "" ) 
								{
									$haveImage = true;
									?>
									<div class="col-sm-6 news-box">
										<a class="news-link" href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
						    				<img src='<?=get_array_value($item_highlight,"ref_link2_image_url")?>' />
						    			</a>
									</div>
									<?php
								}
								else if(isset($item_highlight['dummy_cover_image']) && !empty($item_highlight['dummy_cover_image'])) 
								{
									$haveImage = true;
									?>
									<div class="col-sm-6 news-box">
										<a class="news-link" href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
						    				<?=$item_highlight['dummy_cover_image']?>
						    			</a>
									</div>
									<?php
								}
								else{
									$haveImage = false;
								}


								if($haveImage == true)
								{
									if (!empty($item_highlight['title'])) 
									{
										?>
										<div class="col-sm-6 news-box">
											<a href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
												<?=get_array_value($item_highlight,"title","")?>
											</a>
										</div>
										<?php
									}
								}
								else{
									if (!empty($item_highlight['title'])) 
									{
										?>
										<div class="col-sm-12 news-box">
											<a href="<?=site_url('news/detail/'.get_array_value($item_highlight,"aid",""))?>">
												<?=get_array_value($item_highlight,"title","")?>
											</a>
										</div>
										<?php
									}
								}
								?>
						</div>
					</div>
					<?
				}
			}
			?>
			<?php /*<div class="col-sm-6">
				<div class="bg-gray-box">
				</div>
			</div>
			<div class="col-sm-6">
				<div class="bg-gray-box">
				</div>
			</div>*/ ?>
		</div>
	</div>
	<!-- End interesting news section -->
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
	<section id="projects" style="margin-top:25px;">
		<div class="container mt15 <?=$class?>-box">
			<div class="row">
				<div class="col-xs-9">
					<?php
					if(get_array_value($item,"product_main_id","-") == "1") {
					?>
						<?php /*<div class="title-manu-home"><img class="head-image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_book.png"></div>*/ ?>
						<div class="title-manu-home text-right" style="background-image:url(<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_book.png)">
							
						</div>
					<?php
					}
					if(get_array_value($item,"product_main_id","-") == "3") {
					?>
						<?php /*<div class="title-manu-home"><img class="head-image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_ebook.png"></div>*/ ?>
						<div class="title-manu-home text-right" style="background-image:url(<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_ebook.png)">
							
						</div>
					<?php
					}
					if(get_array_value($item,"product_main_id","-") == "4") {
					?>
						<?php /*<div class="title-manu-home"><img class="head-image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_emagazine.png"></div>*/ ?>
						<div class="title-manu-home text-right" style="background-image:url(<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_emagazine.png)">
							
						</div>
					<?php
					}
					if(get_array_value($item,"product_main_id","-") == "5") {
					?>
						<?php /*<div class="title-manu-home"><img class="head-image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_multimedia.png"></div>*/ ?>
						<div class="title-manu-home text-right" style="background-image:url(<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_multimedia.png)">
							
						</div>
					<?php
					}
					if(get_array_value($item,"product_main_id","-") == "6") {
					?>
						<?php /*<div class="title-manu-home"><img class="head-image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_information.png"></div>*/ ?>
						<div class="title-manu-home text-right" style="background-image:url(<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_information.png)">
							
						</div>
					<?php
					}
					if(get_array_value($item,"product_main_id","-") == "7") {
					?>
						<?php /*<div class="title-manu-home"><img class="head-image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_knowledge.png"></div>*/ ?>
						<div class="title-manu-home text-right" style="background-image:url(<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_knowledge.png)">
							
						</div>
					<?php
					}
					if(get_array_value($item,"product_main_id","-") == "8") {
					?>
						<?php /*<div class="title-manu-home"><img class="head-image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_cddvd.png"></div>*/ ?>
						<div class="title-manu-home text-right" style="background-image:url(<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/header_cddvd.png)">
							
						</div>
					<?php
					}
					?>
				</div>
				<div class="col-xs-3 text-right">
					<a class="custom-home-box-seeall" href="<?=site_url('list-'.get_array_value($item,"product_type_cid","none").'/category/'.get_array_value($item,"product_main_url","none"))?>">
						<h3>See All</h3>
					</a>
				</div>
			</div>

			
			<?php if(get_array_value($item,"product_main_id","-") != "5")
			{
			?>
			<div class="row">
				<div class="col-xs-6 col-sm-4">
					<?=draw_item(get_array_value($result_list,"0",""));?>
				</div>
				<div class="col-xs-6 col-sm-4">
					<?=draw_item(get_array_value($result_list,"1",""));?>
				</div>
				<div class="col-xs-12 visible-xs">
					<img class="shelf-under-book" src="/styles/<?=CONST_CODENAME?>/images/background/bg-shelf-item.png">
				</div>
				<div class="col-xs-6 col-sm-4">
					<?=draw_item(get_array_value($result_list,"2",""));?>
				</div>
				<div class="col-md-12 hidden-xs">
					<img class="shelf-under-book-big" src="/styles/<?=CONST_CODENAME?>/images/background/bg-shelf-item.png">
				</div>
				<div class="col-xs-6 col-sm-4">
					<?=draw_item(get_array_value($result_list,"3",""));?>
				</div>
				<div class="col-xs-12 visible-xs">
					<img class="shelf-under-book" src="/styles/<?=CONST_CODENAME?>/images/background/bg-shelf-item.png">
				</div>
				<div class="col-xs-6 col-sm-4">
					<?=draw_item(get_array_value($result_list,"4",""));?>
				</div>
				<div class="col-xs-6 col-sm-4">
					<?=draw_item(get_array_value($result_list,"5",""));?>
				</div>
				<div class="col-md-12 hidden-xs">
					<img class="shelf-under-book-big" src="/styles/<?=CONST_CODENAME?>/images/background/bg-shelf-item.png">
				</div>
				<div class="col-md-12 visible-xs">
					<img class="shelf-under-book" src="/styles/<?=CONST_CODENAME?>/images/background/bg-shelf-item.png">
				</div>
			</div>
			<?php
			}
			else
			{
			?>
			<div class="row">
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"0",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"1",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"2",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"3",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"4",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"5",""));?>
				</div>
			</div>
			<?php
			} ?>
			
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
	/*
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
		if($product_type_cid != 'vdo'){
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb", get_array_value($item,"thumbnail_image","")).'" class="img-responsive"></a>';
		}else{
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb").'" class="img-responsive"></a>';
		}
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	return $txt;
	*/
	$version = "123454321";
	$product_type_cid = get_array_value($item,"product_type_cid","");
	$cover_class = "";

	if($product_type_cid == 'vdo'){
		$cover_class = "vdo-";
	}
	$txt = '';
	$txt .=	'<div class="boxContent">';

	//$txt .= '<div class="boxContent-head hidden-xs">';
	$txt .= '<div class="boxContent-head">';

	if(is_var_array($item)){
		$txt .=	'<a class="" href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'" title="'.removeAllQuote(get_array_value($item,"title","N/A")).'" >'.get_array_value($item,"title_short","N/A") .'</a>'; //title, title_short
	}else{
		$txt .=	'&nbsp;';
	}

	$txt .= '</div>';
	
	if($product_type_cid != 'vdo')
	{
		$txt .= '<div class="imgWrapper">';
	}
	else{
		$txt .= '<div class="vdoWrapper">';
	}

	if(is_var_array($item)){
		$is_license = get_array_value($item,"has_license","0");
		if($is_license == '1'){
			$txt .= '<div class="mask-ipad"></div>';
		}
		if($product_type_cid != 'vdo')
		{
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb", get_array_value($item,"thumbnail_image","")).'?v='.$version.'" ></a>';
		}else{
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb").'?v='.$version.'" ></a>';
		}
	}else{
		$txt .=	'&nbsp;';
	}

	$txt .= '</div>';

	$txt .=	'</div>';

	return $txt;
}
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>