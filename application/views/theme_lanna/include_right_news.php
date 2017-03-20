<?php 
	$event_list = @$event_list;
	$news_popular_list = @$news_popular_list['results'];
	$news_recommended_list = @$news_recommended_list['results'];
	$news_talk_of_the_town_list = @$news_talk_of_the_town_list['results'];
	$news_top_commenters = @$news_top_commenters['results'];
	$word = "news";
?>

<aside id="sidebar2" class="col-md-4 custom-right-box">
	<!-- Start : Rss box -->



	<!-- End : Rss box -->
	<!-- Start : Event box -->
	<?php if(is_var_array($event_list)){ ?>
	<div class="boxFocus">
		<div class="boxTitle clearfix">
			<h3 class="txt-green pull-left">Events</h3>
			<a href="<?=site_url('event')?>" class="pull-right">See all</a>
		</div>
		
		<div class="boxContent">
		<?php foreach($event_list as $item){ ?>
		<div class="row pan box-event" data-event-aid='<?=get_array_value($item,"aid","")?>'>
			<div class="col-md-12 event-content" >
				<div class="media">
			      <a href="#" class="pull-left">
			        <?php if (file_exists( str_replace(site_url(), '', get_array_value($item,"avatar_mini_path","")) )) { ?>
			        		<img src='<?=get_array_value($item,"avatar_mini_path","")?>' class='img-responsive size-s' />
			        	<?php } else { ?>
			        		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive size-s" />
			        	<?php } ?>
			      </a>
			      <div class="media-body" style="padding-left:20px;">
			        <div class="media-heading clearfix">
			        	<div  class="event-header col-sm-7">
				        	<a href="<?=site_url('event/detail/'.get_array_value($item,"aid",""))?>">
				        		<h4 class="mbn pln prn"><?=get_array_value($item,"title","")?></h4>
				        	</a>
				        	<div class="line-2"><?=get_array_value($item, 'event_period_date_txt', '')?></div>
			        	</div>
			        	<div class="event-actions col-sm-5">
			        	<?php if ($item['has_action'] == true) { ?>
			        		<span class="has-action"><?=get_array_value($item, 'has_joined_txt', '')?></span>
			        	<?php } else { ?>
			        		<a href="javascript:void(0);" class="action-event" data-val='1'>Join</a>
			        		<a href="javascript:void(0);" class="action-event" data-val='0'>No</a>
			        	<?php } ?>
			        	</div>
			        	<div class="clearfix"></div>
			        	<div class="line-3"><?=get_array_value($item, 'location', '')?></div>
			        </div>
			      </div>
		    	</div>
		    </div>
	    </div>
	    <?php } ?>
	    </div>

	</div>
	<div class="separator"></div>
	<?php } ?>
	<!-- End : Event box -->


	<!-- Start : Popular News box -->
	<?php if(is_var_array($news_popular_list)){  ?>
	

	<div class="boxFocus">
		<div class="boxTitle">
			<h3 class="txt-green">Popular News</h3>
		</div>
		<p>Our popular <?=$word?>.</p>

		<div class="boxContent">
		<?php foreach($news_popular_list as $item){ ?>
			<div class="row">
				<div class="col-md-12 news-content">
					<div class="media">
				      <div class="pull-left mrn">
					      <a href="<?=site_url('news/detail/'.get_array_value($item, 'aid'))?>" class="clearfix">
					      	<?php 
					      	if (get_array_value($item,"cover_image_file_type","") != "") { 
				    				$cover_image_full_path = './'.get_array_value($item,"cover_image_actual","");
				    				if (file_exists($cover_image_full_path)) { 
				    			?>
				    					<img src='<?=get_image(get_array_value($item,"cover_image_thumb",""), "-thumb")?>' class='img-responsive size-w-l'/>
					        	<?php 
					        		} 
					        		else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
					        			<?=$item['dummy_cover_image']?>
					        	<?php 
						        	}
						        	else {
						      ?>
						      		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive" />
						      <?php
						        	}
					        	} else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
					        		<?=$item['dummy_cover_image']?>
					        	<?php } else { ?>
					        		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive" />
					        	<?php } ?>
					      </a>
				        	<div class="under-img">
					      	<i class="bd-icon bd-icon-wow" title="Wow!"></i> 
					      	<div class="text">
					      		<span class="badge bg-orange"><?=get_array_value($item, 'total_wow', '0')?> &nbsp;Wow!</span>
					      	</div>
					      </div>
				      </div>
				      
				      <div class="media-body" style="padding-left:20px;">
				        <div class="media-heading clearfix">
				        	<div  class="event-header col-sm-12">
					        	<a href="<?=site_url('news/detail/'.get_array_value($item, 'aid'))?>"><h4 class="line-1 mbn textSub pln prn"><?=get_array_value($item,"short_title","")?></h4></a>
				        	</div>
				        	<div class="clearfix"></div>
				        	<div class="line-2 col-sm-12"><?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item,"publish_date",""), '');?></div>
				        </div>
				      </div>
			    	</div>
			    </div>
		    </div>

		<?php } ?>
		</div>

	</div>
	<div class="separator"></div>
	<?php } ?>
	<!-- End : Popular News box -->


	<!-- Start : Recommended box -->
	<?php if(is_var_array($news_recommended_list)){ ?>
	

	<div class="boxFocus">
		<div class="boxTitle">
			<h3 class="txt-green">Recommended</h3>
		</div>
		<p>The recommended <?=$word?> selected by your librarian.</p>

		<div class="boxContent">
		<?php foreach($news_recommended_list as $item){ ?>
			<div class="row">
				<div class="col-md-12 news-content">
					<div class="media">
						<div class="pull-left mrn">
				      <a href="<?=site_url('news/detail/'.get_array_value($item, 'aid'))?>" class="clearfix">
				      	<?php 
				      	if (get_array_value($item,"cover_image_file_type","") != "") { 
			    				$cover_image_full_path = './'.get_array_value($item,"cover_image_actual","");
			    				if (file_exists($cover_image_full_path)) { 
			    			?>
			    					<img src='<?=get_image(get_array_value($item,"cover_image_thumb",""), "-thumb")?>' class='img-responsive size-w-l'/>
				        	<?php 
				        		} 
				        		else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
				        			<?=$item['dummy_cover_image']?>
				        	<?php 
					        	}
					        	else {
					      ?>
					      		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive" />
					      <?php
					        	}
				        	} else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
				        		<?=$item['dummy_cover_image']?>
				        	<?php } else { ?>
				        		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive" />
				        	<?php } ?>
				      </a>
				      </div>
				      <div class="media-body" style="padding-left:20px;">
				        <div class="media-heading clearfix">
				        	<div  class="event-header col-sm-12">
					        	<a href="<?=site_url('news/detail/'.get_array_value($item, 'aid'))?>"><h4 class="line-1 mbn textSub pln prn"><?=get_array_value($item,"short_title","")?></h4></a>
				        	</div>
				        	<div class="clearfix"></div>
				        	<div class="line-2 col-sm-12"><?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item,"publish_date",""), '');?></div>
				        </div>
				      </div>
			    	</div>
			    </div>
		    </div>

		<?php } ?>
		</div>

	</div>
	<div class="separator"></div>
	<?php } ?>
	<!-- End : Recommended box -->


	<!-- Start : Talk of the town box -->
	<?php if(is_var_array($news_talk_of_the_town_list)){ ?>
	

	<div class="boxFocus">
		<div class="boxTitle">
			<h3 class="txt-green">Talk of the Town</h3>
		</div>

		<div class="boxContent">
		<?php foreach($news_talk_of_the_town_list as $item){ ?>
			<div class="row">
				<div class="col-md-12 news-content">
					<div class="media">
				      <div class="pull-left mrn">
					      <a href="<?=site_url('news/detail/'.get_array_value($item, 'aid'))?>" class="clearfix">
					      	<?php 
					      	if (get_array_value($item,"cover_image_file_type","") != "") { 
				    				$cover_image_full_path = './'.get_array_value($item,"cover_image_actual","");
				    				if (file_exists($cover_image_full_path)) { 
				    			?>
				    					<img src='<?=get_image(get_array_value($item,"cover_image_thumb",""), "-thumb")?>' class='img-responsive size-w-l'/>
					        	<?php 
					        		} 
					        		else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
					        			<?=$item['dummy_cover_image']?>
					        	<?php 
						        	}
						        	else {
						      ?>
						      		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive" />
						      <?php
						        	}
					        	} else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
					        		<?=$item['dummy_cover_image']?>
					        	<?php } else { ?>
					        		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive" />
					        	<?php } ?>
					      </a>
				        	<div class="under-img">
					      	<i class="bd-icon bd-icon-comment" title="Comment"></i> 
					      	<div class="text">
					      		<span class="badge bg-orange"><?=get_array_value($item, 'total_comment', '0')?> &nbsp;Comments</span>
					      	</div>
					      </div>
				      </div>
				      <div class="media-body">
				        <div class="media-heading clearfix">
				        	<div  class="event-header col-sm-12">
					        	<a href="<?=site_url('news/detail/'.get_array_value($item, 'aid'))?>"><h4 class="line-1 mbn textSub pln prn"><?=get_array_value($item,"short_title","")?></h4></a>
				        	</div>
				        	<div class="clearfix"></div>
				        	<div class="line-2 col-sm-12"><?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item,"publish_date",""), '');?></div>
				        </div>
				      </div>
			    	</div>
			    </div>
		    </div>

		<?php } ?>
		</div>

	</div>
	<div class="separator"></div>
	<?php } ?>
	<!-- End : Talk of the town box -->



	<!-- Start : Hot comments box -->
	<?php if(is_var_array($news_top_commenters)){ ?>
	
	<div class="boxFocus">
		<div class="boxTitle">
			<h3 class="txt-green">Hot Comments</h3>
		</div>
		
		<div class="boxContent">
		<?php foreach($news_top_commenters as $item){ ?>
		<div class="row">
			<div class="col-md-12">
				<div class="media">
			      <a href="#" class="pull-left">
			        <?php if (file_exists( str_replace(site_url(), '', get_array_value($item,"avatar_mini_path","")) )) { ?>
			        		<img src='<?=get_array_value($item,"avatar_mini_path","")?>' class='img-responsive size-s' />
			        	<?php } else { ?>
			        		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive size-s" />
			        	<?php } ?>
			      </a>
			      <div class="media-body">
			        <div class="media-heading clearfix">
			        	<div>
				        	<h4 class="mbn pln prn"><?=get_array_value($item,"full_name_th","")?></h4>
				        	<!-- <div class="line-2"><?=get_array_value($item,"department","")?></div> -->
			        	</div>
			        	<div class="textStart"><?=get_array_value($item,"total_comment","0")?> <?=$item['total_comment'] > 1 ? 'comments' : 'comment'?></div>
			        </div>
			      </div>
		    	</div>
		    </div>
	    </div>
	    <?php } ?>
	    </div>

	</div>
	<?php } ?>
	<!-- End : Hot comments box -->

</aside>