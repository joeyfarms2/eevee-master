<?php 
	$event_list = @$event_list;
	$recommended_list = @$recommended_list;
	$product_type_aid = @$product_type_aid;

	$word = "news";
?>

<aside id="sidebar2" class="col-md-4 custom-right-box">
	
	<!-- Start : Event box -->
	<?php if(is_var_array($event_list)){ ?>
	<div class="boxFocus">
		<div class="boxTitle clearfix">
			<h3 class="txt-green pull-left">Events</h3>
			<a href="#" class="pull-right">See all</a>
		</div>
		
		<div class="boxContent">
		<?php foreach($event_list as $item){ ?>
		<div class="row">
			<div class="col-md-12 event-content">
				<div class="media">
			      <a href="#" class="pull-left">
			        <img src='<?=get_array_value($item,"avatar_mini_path","")?>' class='img-responsive size-s'/>
			      </a>
			      <div class="media-body">
			        <div class="media-heading clearfix">
			        	<div  class="event-header col-sm-7">
				        	<h4 class="mbn"><?=get_array_value($item,"full_name_th","")?></h4>
				        	<div class="line-2">Accounting</div>
			        	</div>
			        	<div class="event-actions col-sm-5">
			        		<a href="Join">Join</a>
			        		<a href="Join">No</a>
			        	</div>
			        	<div class="clearfix"></div>
			        	<!-- <div class="line-3">09/09/2014 16:44 : ประชุมโครงการปลูกป่าประจำปี 2558</div> -->
			        	<div class="line-3"><?=get_datetime_pattern('d/m/Y H:i', get_array_value($item,"publish_date",""), '');?> : <?=get_array_value($item, 'title', '')?></div>
			        </div>
			      </div>
		    	</div>
		    </div>
	    </div>
	    <?php } ?>
	    </div>

	</div>
	<?php } ?>
	<!-- End : Event box -->


	

	<!-- Start : Recommended box -->
	<?php if(is_var_array($news_recommended_list)){ ?>
	<div class="separator"></div>

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
				      <a href="#" class="pull-left mrn">
				      	<?php if (file_exists($cover_image_full_path)) { ?>
				        		<img src='<?=get_image(get_array_value($item,"cover_image_thumb",""), "-thumb")?>' class='img-responsive size-w-l' />
				        	<?php } else { ?>
				        		<img data-src="<?=JS_PATH?>holder.js/100x100/text:no-image" class="img-responsive" />
				        	<?php } ?>
				      </a>
				      <div class="media-body">
				        <div class="media-heading clearfix">
				        	<div  class="event-header col-sm-12">
					        	<h4 class="line-1 mbn"><?=get_array_value($item,"full_name_th","")?></h4>
					        	<div class="line-2 dept">Accounting</div>
					        	
				        	</div>
				        	<div class="clearfix"></div>
				        	<!-- <div class="line-3">09/09/2014 16:44 : ประชุมโครงการปลูกป่าประจำปี 2558</div> -->
				        	<div class="line-3 col-sm-12"><?=get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item,"publish_date",""), '');?> : <?=get_array_value($item, 'title', '')?></div>
				        </div>
				      </div>
			    	</div>
			    </div>
		    </div>

		<?php } ?>
		</div>

	</div>
	<?php } ?>
	<!-- End : Recommended box -->


</aside>