<div class="scroll-fragment">
<?php if(is_var_array($news_result)) { ?>
	<?php 
		foreach($news_result as $item){
	?>
		
		<!-- Start news box -->
		<div class="box-generic box-news new" data-parent-news-aid="<?=get_array_value($item, 'aid')?>">

			<!-- Start news content -->
			<div class="news-content clearfix">
				<div class="media">
			      <a href="#" class="pull-left">
			        <!-- <img src='<?=get_array_value($item,"avatar_mini_path","")?>' class='img-responsive size-m'/> -->
			        <?=get_array_value($item, 'avatar_mini')?>
			      </a>
			      <div class="media-body">
			        <h4 class="media-heading">
			        	<?=get_array_value($item,"full_name_th","")?>
			        	<div class="line-2"><?=get_array_value($item,"department","")?></div>
			        	<div class="line-3">
			        		<?=get_pretty_date(get_array_value($item,"publish_date",""));?>
			        	</div>
			        </h4>
			      </div>
			    </div>
				
				<?php if (!empty($item['title'])) { ?>
			    	<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>">
			    		<h3 class="txt-blue"><?=get_array_value($item,"title","")?></h3>
			    	</a>
			    <?php } ?>
			    
			    <div class="news-text">
			    	<?=get_array_value($item,"short_description_highlight","")?>... 
			    	<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>" class="readmore">Continue Reading</a>
			    </div>

			    <?php 
			    if (get_array_value($item,"cover_image_file_type","") != "") {
			    	$cover_image_full_path = './'.get_array_value($item,"cover_image_actual","");
			    	if (file_exists($cover_image_full_path)) {
			    ?>
				    <div class="media-cover text-center clearfix">
				    	<!-- <img src='<?=$cover_image_full_path?>' /> -->
				    	<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>"><img src='<?=get_image(get_array_value($item,"cover_image_actual",""), "-actual")?>' /></a>
				    </div>
			    <?php } else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
 					<div class="media-cover text-center clearfix">
		    			<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>"><?=$item['dummy_cover_image']?></a>
		    		</div>
		 		<?php } }
		 		else if(isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
		 			<div class="media-cover text-center clearfix">
		    			<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>"><?=$item['dummy_cover_image']?></a>
		    		</div>
		 		<?php 
		 		} ?>
		   </div>
		   <!-- End news content -->

		   <!-- Start news action buttons (wow, join, comment) -->
		   <div class="news-actions">
		    </div>
		    <!-- End news action buttons (wow, join, comment) -->

		   <!-- Start others' comments panel -->
		   <div class="news-comments">
	    	</div>
	    	<!-- End others' comments panel -->


		   <!-- Start my comment panel (only for login user) -->
		    <?php if(is_login()) { ?>
	    	<div class="news-my-comment">
		    	<div class="media">
			      <a href="#" class="pull-left">
			        <?=get_array_value($user_login_info,"avatar_tiny","")?>
			      </a>
			      <div class="media-body">
			        	<textarea class="form-control txt-your-comment new" name="your_comment" placeholder="Write your comment..." data-parent-news-aid="<?=get_array_value($item,'aid','')?>"></textarea>
			      </div>
			    </div>
		    </div>
		    <?php }  ?>
		    <div class="msg-news-login hidden"></div>
		    <!-- End my comment panel (only for login user) -->

		</div>
		<!-- End news box -->

	<?php 
		} //foreach
	}else{
	?>
	<?php $message = set_message_error("Oops! It looks like no any news feed at the moment.")?>
	<?php } ?>
</div>