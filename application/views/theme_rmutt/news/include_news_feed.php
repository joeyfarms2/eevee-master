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
			      <div class="media-body clearfix">
			        <h4 class="media-heading pull-left">
			        	<?=get_array_value($item,"full_name_th","")?>
			        	<div class="line-2"><?=get_array_value($item,"department","")?></div>
			        	<div class="line-3">
			        		<?=get_pretty_date(get_array_value($item,"publish_date",""));?>
			        	</div>
			        </h4>
			        <div class="pull-right">
			        	<?php if ( get_array_value($item,"created_by","") == getUserLoginAid($this->user_login_info) ) { ?>
			        		<a href="javascript:void(0);" class="link edit-news" onclick="editMyNews(this);" data-aid="<?=get_array_value($item, 'aid')?>">Edit</a> | 
				        		<a href="javascript:void(0);" class="link text-danger delete-news" onclick="deleteMyNews(this);" data-aid="<?=get_array_value($item, 'aid')?>">Delete</a>
			        	<?php } ?>
			        </div>
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
			    <?php 
					}
			    	else if (isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
 					<div class="media-cover text-center clearfix">
		    			<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>"><?=$item['dummy_cover_image']?></a>
		    		</div>
		 		<?php } 
		 		}
		 		else if ( get_array_value($item,"ref_link2_image_url","") != "" ) { ?>
 					<div class="text-center clearfix">
 						<img src='<?=get_array_value($item,"ref_link2_image_url")?>' class="img-responsive" style="margin:auto;"/>
 					</div>
				<?php
				}
		 		else if (isset($item['dummy_cover_image']) && !empty($item['dummy_cover_image'])) { ?>
		 			<div class="media-cover text-center clearfix">
		    			<a href="<?=site_url('news/detail/'.get_array_value($item,"aid",""))?>"><?=$item['dummy_cover_image']?></a>
		    		</div>
		 		<?php 
		 		} ?>


		 		<!--
		 		<?php if ( get_array_value($item,"ref_link2_image_url","") != "" ) { ?>
					<div class="text-left mtl"><small>Reference Link:</small></div>
					<div class="media man">
						<div class="media-left pull-left">
						<?php if ( get_array_value($item,"ref_link2_url","") != "" ) { ?>
		 					<a target='_blank' href='<?=get_array_value($item,"ref_link2_url","javascript:void(0);")?>'>
		 						<img src='<?=get_array_value($item,"ref_link2_image_url")?>' class="media-object size-xl"/>
		 					</a>
		 				<?php } else { ?>
		 					<img src='<?=get_array_value($item,"ref_link2_image_url")?>' class="media-object size-xl"/>
		 				<?php } ?>
		 				</div>
		 				<div class="media-body">
		 					<h4 class="media-heading">
		 						<a href='<?=get_array_value($item,"ref_link2_url","javascript:void(0);")?>'><?=get_array_value($item,"ref_link2_title")?>
		 						</a>
		 					</h4>
		 					<p class="ptm"><?=get_array_value($item,"ref_link2_desc")?></p>
	 					</div>
					</div>
				<?php
				}
				?>
				-->

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