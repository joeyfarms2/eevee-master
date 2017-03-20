<style type="text/css">
.owl-item {
	max-width: 300px !important;
}

.news-text img
{
	width: 90%;
	height: auto;
}
</style>

<?php 
	$version = "123454321";

?>
<div id="message-box">
	<div id="result-msg-box" class="hidden" ></div>
</div>


<section id="projects">
	<div class="container mt15 pan">

		<?php if(is_var_array($item_detail)) { ?>

			<!-- Start news box -->
			<div class="box-generic box-news new" data-parent-news-aid="<?=get_array_value($item_detail, 'aid')?>">


				
				<!-- Start news content -->
				<div class="news-content clearfix">

					<div class="media">
				      <a href="#" class="pull-left">
				        <!-- <img src='<?=get_array_value($item_detail,"avatar_mini_path","")?>' class='img-responsive size-m'/> -->
				        <?=get_array_value($item_detail, 'avatar_mini')?>
				      </a>
				      <div class="media-body clearfix">
				        <h4 class="media-heading pull-left">
				        	<?=get_array_value($item_detail,"full_name_th","")?>
				        	<div class="line-2"><?=get_array_value($item_detail,"department","")?></div>
				        	<div class="line-3">
				        		<?=get_pretty_date(get_array_value($item_detail,"publish_date",""));?>
				        	</div>
				        </h4>
				        
				      </div>
				    </div>


					<h3 class="txt-blue mbl"><?=get_array_value($item_detail,"title","")?></h3>
				    

					<div class="news-text mtl clearfix">
				    	<?=nl2br(makeClickableLinks(get_array_value($item_detail,"description","")))?>
			    	</div>

			    	<?php if (is_var_array($news_gallery_list)) { ?>
			    		<section class="owl-carousel nekoDataOwl imgHover gallery-container ptl" data-neko_items="4">
			    		<?php foreach ($news_gallery_list as $photo_detail) { ?>
				    		<?php
								$upload_path = get_array_value($item_detail,"upload_path","")."galleries/";
								$image_name = get_array_value($photo_detail,"file_name","");
								$file_title_name = get_array_value($photo_detail,"title","");
								// echo "path : ".$upload_path.$image_name;
								if(is_file($upload_path.'thumb/'.$image_name)){
							?>
				    			<div class="item gallery-item ptl">
				    				<section class="imgWrapper">
				    					<a href="<?=site_url($upload_path.'original/'.$image_name)?>" title="<?=$file_title_name?>"><img src="<?=site_url($upload_path.'thumb/'.$image_name)?>"/ title="<?=$file_title_name?>" style="max-width:400px;"></a>
				    				</section>
				    			</div>
				    		<?php } ?>
			    		<?php } ?>
			    		</section>
			    	<?php } ?>

			    	<?php if (!is_blank($item_detail['ref_link'])) { ?>
			    		<div class="ptl">Link: <?=makeClickableLinks(get_array_value($item_detail,"ref_link",""))?></div>
			    	<?php } ?>

			    	<?php if ( get_array_value($item_detail,"ref_link2_image_url","") != "" ) { ?>
						<div class="text-left mtl"><small>Reference Link:</small></div>
						<div class="media man">
							<div class="media-left pull-left">
							<?php if ( get_array_value($item_detail,"ref_link2_url","") != "" ) { ?>
			 					<a target='_blank' href='<?=get_array_value($item_detail,"ref_link2_url","javascript:void(0);")?>'>
			 						<img src='<?=get_array_value($item_detail,"ref_link2_image_url")?>' class="media-object fb-ref-link"/>
			 					</a>
			 				<?php } else { ?>
			 					<img src='<?=get_array_value($item_detail,"ref_link2_image_url")?>' class="media-object size-xl"/>
			 				<?php } ?>
			 				</div>
			 				<div class="media-body">
			 					<h4 class="media-heading">
			 						<a href='<?=get_array_value($item_detail,"ref_link2_url","javascript:void(0);")?>'><?=get_array_value($item_detail,"ref_link2_title")?>
			 						</a>
			 					</h4>
			 					<p class="ptm"><?=get_array_value($item_detail,"ref_link2_desc")?></p>
		 					</div>
						</div>
					<?php
					}
					?>

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
			        	<textarea class="form-control txt-your-comment new" name="your_comment" placeholder="Write your comment..." data-parent-news-aid="<?=get_array_value($item_detail,'aid','')?>"></textarea>
			      </div>
			    </div>
		    </div>
		    <?php }  ?>
		    <div class="msg-news-login hidden"></div>
		    <!-- End my comment panel (only for login user) -->
			    
			</div>

		<?php 

		}else{
		?>
		<?php $message = set_message_error("Oops! It looks like this news has been deleted.")?>
		<?php } ?>

		</div>
	</section>

<?php //$this->load->view("theme_eastwater/news/modal_post_news"); ?>

<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/autosize/jquery.autosize.min.js"></script>
<script type="text/javascript">
	$(document).ready(function($){
		var sid = Math.floor(Math.random()*10000000000);

		$('.gallery-container').magnificPopup({
			delegate: 'a', // child items selector, by clicking on it popup will open
		  	type: 'image',
		  	gallery:{
		    	enabled:true
		  	}
		});


		function initWhoClicks() {
			// On mouse over who wows this news
			$('.news-actions').undelegate('.who-wow', 'mouseenter');
			$('.news-actions').delegate('.who-wow', 'mouseenter',function() {
				var this_node = $(this);
				var panel_box_news = $(this_node).closest('.box-news');
				var url = '<?=site_url("news/ajax-load-who-wow/")?><?php echo "v=".$version; ?>'+sid;
				var arr_input_data = [];
				arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
				if ($(this_node).closest('li').hasClass('has-clicked')) {
					arr_input_data.push({ name: 'include_me', value: '1' });
				}
				else {
					arr_input_data.push({ name: 'include_me', value: '0' });
				}
				
				if ($(this_node).attr('data-original-title') == '' && $(this_node).text() != '0') {
					$.getJSON(
						url, 
						arr_input_data,
						function(data) {
							// $(this_node).tooltip({content: data.html, html: true}).tooltip('show');
							$(this_node).tooltip({ html:true });
							$(this_node).attr('data-original-title', data.html)
		                	.tooltip('fixTitle')
		                	.tooltip('show');
		               
						}
					);
				}
			});

			$('.news-actions').undelegate('.who-cheer', 'mouseenter');
			$('.news-actions').delegate('.who-cheer', 'mouseenter',function() {
				var this_node = $(this);
				var panel_box_news = $(this_node).closest('.box-news');
				var url = '<?=site_url("news/ajax-load-who-cheer/")?><?php echo "v=".$version; ?>'+sid;
				var arr_input_data = [];
				arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
				if ($(this_node).closest('li').hasClass('has-clicked')) {
					arr_input_data.push({ name: 'include_me', value: '1' });
				}
				else {
					arr_input_data.push({ name: 'include_me', value: '0' });
				}
				
				if ($(this_node).attr('data-original-title') == '' && $(this_node).text() != '0') {
					$.getJSON(
						url, 
						arr_input_data,
						function(data) {
							// $(this_node).tooltip({content: data.html, html: true}).tooltip('show');
							$(this_node).tooltip({ html:true });
							$(this_node).attr('data-original-title', data.html)
		                	.tooltip('fixTitle')
		                	.tooltip('show');
		               
						}
					);
				}
			});

			$('.news-actions').undelegate('.who-comment', 'mouseenter');
			$('.news-actions').delegate('.who-comment', 'mouseenter',function() {
				var this_node = $(this);
				var panel_box_news = $(this_node).closest('.box-news');
				var url = '<?=site_url("news/ajax-load-who-comment/")?><?php echo "v=".$version; ?>'+sid;
				var arr_input_data = [];
				arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
				if ($(this_node).closest('li').hasClass('has-clicked')) {
					arr_input_data.push({ name: 'include_me', value: '1' });
				}
				else {
					arr_input_data.push({ name: 'include_me', value: '0' });
				}
				
				if ($(this_node).attr('data-original-title') == '' && $(this_node).text() != '0') {
					$.getJSON(
						url, 
						arr_input_data,
						function(data) {
							// $(this_node).tooltip({content: data.html, html: true}).tooltip('show');
							$(this_node).tooltip({ html:true });
							$(this_node).attr('data-original-title', data.html)
		                	.tooltip('fixTitle')
		                	.tooltip('show');
		               
						}
					);
				}
			});

			$('.news-actions').undelegate('.who-thanks', 'mouseenter');
			$('.news-actions').delegate('.who-thanks', 'mouseenter',function() {
				var this_node = $(this);
				var panel_box_news = $(this_node).closest('.box-news');
				var url = '<?=site_url("news/ajax-load-who-thanks/")?><?php echo "v=".$version; ?>'+sid;
				var arr_input_data = [];
				arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
				if ($(this_node).closest('li').hasClass('has-clicked')) {
					arr_input_data.push({ name: 'include_me', value: '1' });
				}
				else {
					arr_input_data.push({ name: 'include_me', value: '0' });
				}
				
				if ($(this_node).attr('data-original-title') == '' && $(this_node).text() != '0') {
					$.getJSON(
						url, 
						arr_input_data,
						function(data) {
							// $(this_node).tooltip({content: data.html, html: true}).tooltip('show');
							$(this_node).tooltip({ html:true });
							$(this_node).attr('data-original-title', data.html)
		                	.tooltip('fixTitle')
		                	.tooltip('show');
		               
						}
					);
				}
			});

		}

		function initWhoWowsThisComment() {
			// On mouse over who wows this news
			$('.news-comments').undelegate('a.who-wow-this-comment', 'mouseenter');
			$('.news-comments').delegate('a.who-wow-this-comment', 'mouseenter',function() {
				var this_node = $(this);
				var panel_this_comment = $(this_node).closest('.box-comment');
				var url = '<?=site_url("news/ajax-load-who-wow-this-comment/")?>'+sid;
				var arr_input_data = [];
				arr_input_data.push({ name: 'comment_aid', value: $(panel_this_comment).attr('data-comment-aid') });
				
				if ($(this_node).attr('data-original-title') == '') {
					$.getJSON(
						url, 
						arr_input_data,
						function(data) {
							// $(this_node).tooltip({content: data.html, html: true}).tooltip('show');
							$(this_node).tooltip({ html:true });
							$(this_node).attr('data-original-title', data.html)
		                	.tooltip('fixTitle')
		                	.tooltip('show');
		               
						}
					);
				}

			});
		}

		function initViewAllComments() {
			if ($('a.view-more-comments').length > 0) {
				$('.news-comments').undelegate('a.view-more-comments', 'click');
				$('.news-comments').delegate('a.view-more-comments', 'click',function() {
					var this_node = $(this);
					var panel_box_news = $(this_node).closest('.box-news');
					var panel_view_more_comments = $(this_node).closest('.panel-view-more-comments');
					var panel_news_comments = $(this_node).closest('.news-comments');
					var url = '<?=site_url("news/ajax-load-view-all-comments/")?>'+sid;
					var arr_input_data = [];
					arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
					
					$.ajax({
		               url: url,
		               type: "post",
		               dataType: "json",
		               data: arr_input_data,
		               beforeSend:function() {
			  					$(panel_view_more_comments).html('<div>Loading...</div>');
			  				}
		           	})
		           	.success(function( data ) {
		               if ( data.status == "success" ) {
		                  $(panel_news_comments).html(data.html_panel_comment);
		               }
		               else {

		               }
		            });

				});
			}
		}

		function initAll() {
			// $('[data-toggle="tooltip"]').tooltip('destroy');

			// Initialize event actions
			$('.action-event').click(function() {
				<?php if(is_login()) { ?>
				var panel_event_actions = $(this).closest('.event-actions');
				var event_aid = $(this).closest('.box-event').attr('data-event-aid');
				var arr_input_data = [];
		  		arr_input_data.push({ name: 'event_aid', value: event_aid });
		  		arr_input_data.push({ name: 'has_joined', value: $(this).attr('data-val') });
		  		$.ajax({
	               url: '<?=site_url("event/ajax-save-action-join")?>'+"/"+sid,
	               type: "post",
	               dataType: "json",
	               data: arr_input_data,
	               beforeSend:function() {
		  					$(panel_event_actions).html('<span>Saving...</span>');
		  				}
	           	})
	           	.success(function( data ) {
	               if ( data.status == "success" ) {
	               	$(panel_event_actions).html('<span class="has-action">'+data.msg+'</span>');
	               }
	            });
	         <?php } else { ?>
	         	alert('Please log in before accept this invitation.');
	         <?php } ?>
			});

	   	// Initialize everything after loading comments panel and who wows panel
			$('.box-news.new').each(function() {
				var panel_news_comments = $(this).find('.news-comments');
				var panel_news_actions = $(this).find('.news-actions');
				var panel_news_activity = $(this).find('.news-activity');
				var panel_news_activity_msg = $(panel_news_activity).find('.news-user-activity');
				var sid = Math.floor(Math.random()*10000000000);
		  		var arr_input_data = [];
		  		arr_input_data.push({ name: 'parent_news_aid', value: $(this).attr('data-parent-news-aid') });
		  		// var serializedData = arr_input_data.serialize();
		  		$.ajax({
	               url: '<?=site_url("news/ajax-load-user-panels")?>'+"/"+sid,
	               type: "post",
	               dataType: "json",
	               data: arr_input_data,
	               beforeSend:function() {
		  					$(panel_news_comments).html('<div>Loading...</div>');
		  				}
	           	})
	           	.success(function( data ) {
	               if ( data.status == "success" ) {
	                  $(panel_news_actions).html(data.html_panel_actions);
	                  $(panel_news_comments).html(data.html_panel_comment);
	                  
							initWhoClicks();
							initWhoWowsThisComment();
							initViewAllComments();

	                  // Init button action-icon-wow
	                  $(panel_news_actions).undelegate('.action-icon-wow', 'click');
							$(panel_news_actions).delegate('.action-icon-wow', 'click', function() {
								<?php if(is_login()) { ?>
									var this_node = $(this);
									var panel_box_news = $(this_node).closest('.box-news');
									var panel_news_actions = $(panel_box_news).find('.news-actions');
							  		var sid = Math.floor(Math.random()*10000000000);
							  		var arr_input_data = [];
							  		arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
							  		if ($(this_node).closest('li').hasClass('has-clicked')) {
										var post_url = '<?=site_url("news/ajax-unwow")?>';
										arr_input_data.push({ name: 'status', value: '0' });
									}
									else {
										var post_url = '<?=site_url("news/ajax-wow")?>';
										arr_input_data.push({ name: 'status', value: '1' });
									}
							  		$.ajax({
						               url: post_url+"/"+sid,
						               type: "post",
						               dataType: "json",
						               data: arr_input_data
						           	})
						           	.success(function( data ) {
						               if ( data.status == "success" ) {
						                  $(panel_news_actions).html(data.html_panel_actions);
						                  initWhoClicks();
						               }
						               return false;
						           	});

								<?php } else { ?>
							    	$(this).closest('.news-actions').siblings('.msg-news-login')
								   	.removeClass('hidden')
								   	.html('Please <a href="'+'<?=site_url("login")?>'+'"><strong>log in</strong></a> to say wow!')
								   	.effect("highlight", {color: '#FFF3BE'}, 2000);
							    <?php } ?>
							});

	                  // Init button action-icon-cheer
	                  $(panel_news_actions).undelegate('.action-icon-cheer', 'click');
							$(panel_news_actions).delegate('.action-icon-cheer', 'click', function() {
								<?php if(is_login()) { ?>
									var this_node = $(this);
									var panel_box_news = $(this_node).closest('.box-news');
									var panel_news_actions = $(panel_box_news).find('.news-actions');
							  		var sid = Math.floor(Math.random()*10000000000);
							  		var arr_input_data = [];
							  		arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
							  		if ($(this_node).closest('li').hasClass('has-clicked')) {
										var post_url = '<?=site_url("news/ajax-uncheer")?>';
										arr_input_data.push({ name: 'status', value: '0' });
									}
									else {
										var post_url = '<?=site_url("news/ajax-cheer")?>';
										arr_input_data.push({ name: 'status', value: '1' });
									}
							  		$.ajax({
						               url: post_url+"/"+sid,
						               type: "post",
						               dataType: "json",
						               data: arr_input_data
						           	})
						           	.success(function( data ) {
						               if ( data.status == "success" ) {
						                  $(panel_news_actions).html(data.html_panel_actions);
						                  initWhoClicks();
						               }
						               return false;
						           	});

								<?php } else { ?>
							    	$(this).closest('.news-actions').siblings('.msg-news-login')
								   	.removeClass('hidden')
								   	.html('Please <a href="'+'<?=site_url("login")?>'+'"><strong>log in</strong></a> to cheer up.')
								   	.effect("highlight", {color: '#FFF3BE'}, 2000);
							    <?php } ?>
							});

	                  // Init button action-icon-thanks
	                  $(panel_news_actions).undelegate('.action-icon-thanks', 'click');
							$(panel_news_actions).delegate('.action-icon-thanks', 'click', function() {
								<?php if(is_login()) { ?>
									var this_node = $(this);
									var panel_box_news = $(this_node).closest('.box-news');
									var panel_news_actions = $(panel_box_news).find('.news-actions');
							  		var sid = Math.floor(Math.random()*10000000000);
							  		var arr_input_data = [];
							  		arr_input_data.push({ name: 'news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
							  		if ($(this_node).closest('li').hasClass('has-clicked')) {
										var post_url = '<?=site_url("news/ajax-unthanks")?>';
										arr_input_data.push({ name: 'status', value: '0' });
									}
									else {
										var post_url = '<?=site_url("news/ajax-thanks")?>';
										arr_input_data.push({ name: 'status', value: '1' });
									}
							  		$.ajax({
						               url: post_url+"/"+sid,
						               type: "post",
						               dataType: "json",
						               data: arr_input_data
						           	})
						           	.success(function( data ) {
						               if ( data.status == "success" ) {
						                  $(panel_news_actions).html(data.html_panel_actions);
						                  initWhoClicks();
						               }
						               return false;
						           	});

								<?php } else { ?>
							    	$(this).closest('.news-actions').siblings('.msg-news-login')
								   	.removeClass('hidden')
								   	.html('Please <a href="'+'<?=site_url("login")?>'+'"><strong>log in</strong></a> to say thanks.')
								   	.effect("highlight", {color: '#FFF3BE'}, 2000);
							    <?php } ?>
							});

							// Init button for action-icon-comment
							$(panel_news_actions).undelegate('.action-icon-comment', 'click');
							$(panel_news_actions).delegate('.action-icon-comment', 'click', function() {
								<?php if(is_login()) { ?>
									$(this).closest('.news-actions').siblings('.news-my-comment')
										.removeClass('hidden')
										.find('.txt-your-comment').val('').focus();
								<?php } else { ?>
							    	$(this).closest('.news-actions').siblings('.msg-news-login')
								   	.removeClass('hidden')
								   	.html('Please <a href="'+'<?=site_url("login")?>'+'"><strong>log in</strong></a> to write your comment.')
								   	.effect("highlight", {color: '#FFF3BE'}, 2000);
							    <?php } ?>
							});

	                  // Wow news comment
	                  $(panel_news_comments).undelegate('.action-icon-wow-comment', 'click');
							$(panel_news_comments).delegate('.action-icon-wow-comment', 'click', function() {
								<?php if(is_login()) { ?>
									var panel_this_comment = $(this).closest('.box-comment');
									var panel_comment_total_wow = $(this).siblings('.panel-comment-total-wow');
									var this_node = $(this);
									var sid = Math.floor(Math.random()*10000000000);
							  		var arr_input_data = [];
							  		arr_input_data.push({ name: 'comment_aid', value: $(panel_this_comment).attr('data-comment-aid') });
							  		if ($(this_node).hasClass('wowed')) {
										var post_url = '<?=site_url("news/ajax-unwow-comment")?>';
										arr_input_data.push({ name: 'status', value: '0' });
									}
									else {
										var post_url = '<?=site_url("news/ajax-wow-comment")?>';
										arr_input_data.push({ name: 'status', value: '1' });
									}
							  		$.ajax({
						               url: post_url+"/"+sid,
						               type: "post",
						               dataType: "json",
						               data: arr_input_data
						           	})
						           	.success(function( data ) {
						               if ( data.status == "success" ) {
						                  $(panel_comment_total_wow).html(data.html_panel_comment_total_wow);
						                  $(panel_comment_total_wow).addClass('hidden');
						                  if (data.total_wow > 0) {
						                  	$(panel_comment_total_wow).removeClass('hidden');
						                  	initWhoWowsThisComment();
						                  }
						                  $(this_node).removeClass('wowed');
						                  if (data.has_wowed) {
						                  	$(this_node).addClass('wowed');
						                  }
						                  $(this_node).text(data.new_txt_action);
						               }
						               return false;
						           	});
								<?php } else { ?>
							    	$(this).closest('.news-comments').siblings('.msg-news-login')
								   	.removeClass('hidden')
								   	.html('Please <a href="'+'<?=site_url("login")?>'+'"><strong>log in</strong></a> to say wow!')
								   	.effect("highlight", {color: '#FFF3BE'}, 2000);
							    <?php } ?>
							});

	               }
	               return false;
	           	});
			});

			// Toggle show/hide delete (my) comment link, when on mouseenter and on mouseleave
			$('.box-news.new').delegate('.box-comment', 'mouseenter mouseleave', function() {
				$(this).find('.panel-delete-comment').toggleClass('hidden');
			});
			$('.box-news.new').delegate('.panel-delete-comment > a', 'click', function() {
				var this_node = $(this);
		  		var panel_news_comments = $(this_node).closest('.news-comments');
		  		var panel_news_actions = $(panel_news_comments).siblings('.news-actions');
		  		var panel_view_more_comments = $(this_node).closest('.panel-comments').siblings('.panel-view-more-comments');
		  		var panel_this_comment = $(this_node).closest('.box-comment');
		  		var panel_box_news = $(this_node).closest('.box-news');
		  		var sid = Math.floor(Math.random()*10000000000);
		  		var arr_input_data = [];
		  		arr_input_data.push({ name: 'comment_aid', value: $(panel_this_comment).attr('data-comment-aid') });
		  		arr_input_data.push({ name: 'parent_news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
		  		arr_input_data.push({ name: 'has_view_all_link', value: $(panel_view_more_comments).length });
		  		$.ajax({
	               url: '<?=site_url("news/ajax-delete-comment")?>'+"/"+sid,
	               type: "post",
	               dataType: "json",
	               data: arr_input_data
	           	})
	           	.success(function( data ) {
	               if ( data.status == "success" ) {
	                  $(panel_news_comments).html(data.html_panel_comment);
	                  $(panel_news_actions).html(data.html_panel_actions);
	                  return false;
	               }
	               else {
	                  return false;
	               }
	           	});
			});

			// Toggle hide/unhide comment link, when on mouseenter and on mouseleave (for admin)
			$('.box-news.new').delegate('.box-comment', 'mouseenter mouseleave', function() {
				$(this).find('.panel-hide-comment').toggleClass('hidden');
			});
			$('.box-news.new').delegate('.panel-hide-comment > a', 'click', function() {
		  		var this_node = $(this);
		  		var panel_news_comments = $(this_node).closest('.news-comments');
		  		var panel_news_actions = $(panel_news_comments).siblings('.news-actions');
		  		var panel_view_more_comments = $(this_node).closest('.panel-comments').siblings('.panel-view-more-comments');
		  		var panel_this_comment = $(this_node).closest('.box-comment');
		  		var panel_box_news = $(this_node).closest('.box-news');
		  		var sid = Math.floor(Math.random()*10000000000);
		  		var arr_input_data = [];
		  		arr_input_data.push({ name: 'comment_aid', value: $(panel_this_comment).attr('data-comment-aid') });
		  		arr_input_data.push({ name: 'parent_news_aid', value: $(panel_box_news).attr('data-parent-news-aid') });
		  		arr_input_data.push({ name: 'status', value: $(this_node).attr('data-status') });
		  		arr_input_data.push({ name: 'has_view_all_link', value: $(panel_view_more_comments).length });
		  		$.ajax({
	               url: '<?=site_url("news/ajax-hide-comment")?>'+"/"+sid,
	               type: "post",
	               dataType: "json",
	               data: arr_input_data
	           	})
	           	.success(function( data ) {
	               if ( data.status == "success" ) {
	                  $(panel_news_comments).html(data.html_panel_comment);
	                  $(panel_news_actions).html(data.html_panel_actions);
	                  return false;
	               }
	               else {
	                  return false;
	               }
	           	});
			});

			// Initialize textareas for writing comment
			$('.txt-your-comment.new').autosize(); 
			$('.txt-your-comment.new').css('height', '35px');

			$(".txt-your-comment.new").blur(function() {
				if ($.trim($(this).val())=="") $(this).css('height', '35px');
			});
			$('.txt-your-comment.new').keydown(function(e) {
			    if (e.keyCode == 13 && !e.shiftKey) {
			    		// Enter was pressed without shift key
			        	// prevent default behavior
			        	e.preventDefault();

			        	if ($.trim($(this).val()) == "")
				  			return false;

				  		var this_node = $(this);
				  		var panel_news_comments = $(this_node).closest('.news-my-comment').siblings('.news-comments');
				  		var panel_news_actions = $(this_node).closest('.news-my-comment').siblings('.news-actions');
				  		var panel_view_more_comments = $(panel_news_comments).find('.panel-view-more-comments');
				  		var sid = Math.floor(Math.random()*10000000000);
				  		var arr_input_data = [];
				  		arr_input_data.push({ name: 'comment', value: $(this_node).val() });
				  		arr_input_data.push({ name: 'parent_news_aid', value: $(this_node).attr('data-parent-news-aid') });
				  		arr_input_data.push({ name: 'has_view_all_link', value: $(panel_view_more_comments).length });
				  		// var serializedData = arr_input_data.serialize();
				  		$.ajax({
			               url: '<?=site_url("news/ajax-add-comment")?>'+"/"+sid,
			               type: "post",
			               dataType: "json",
			               data: arr_input_data
			           	})
			           	.success(function( data ) {
			               if ( data.status == "success" ) {
			                  $(panel_news_comments).html(data.html_panel_comment);
			                  $(panel_news_actions).html(data.html_panel_actions);
			                  $(this_node).val('');
			                  return false;
			               }
			               else {
			                  return false;
			               }
			           	});
			    }
			});


			$('[data-toggle="tooltip"]').tooltip({ html: true });

			$('.box-news.new').removeClass('new');
			$('.txt-your-comment.new').removeClass('new');
			$('.action-icon-wow.new').removeClass('new');
			$('.action-icon-comment.new').removeClass('new');
		}

		/*
		$('.media-body .link.edit-news').click(function() {
			$('#frm_news').removeClass('hidden');
      		$('#modal_front_post_news').find('.result-msg').addClass('hidden');
      		$('#modal_front_post_news').find('.modal-body').css('height', '400px');

     		$('#modal_front_post_news').modal('show');
     		$('#command').val('_update');
		});

		$('.media-body .link.delete-news').click(function() {
			if (confirm('Are you sure you want to delete this news?')) {
				$.ajax({
	               url: "<?=site_url('news/ajax-delete-one/'.rand())?>",
	               type: "post",
	               dataType: "json",
	               data: { news_aid: '<?=get_array_value($item_detail, "aid")?>'}
	      			// beforeSend:function() {
	  				// 	$(panel_view_more_comments).html('<div>Loading...</div>');
	  				// }
	           	})
	           	.success(function( data ) {
	               if ( data.status == "success" ) {
	                  window.location = "<?=site_url('news')?>";
	               }
	               else {
	               	alert(data.msg);
	               }
	            });
			}
		});
		*/


		// First load init, load the 1st page
		initAll();
		
		<?=@$message?>
		<?=@$js_code?>
	});


function editMyNews(e) {
	$.ajax({
       url: "<?=site_url('news/ajax-edit-one/'.rand())?>",
       type: "post",
       dataType: "json",
       data: { news_aid: $(e).attr('data-aid') },
		beforeSend:function() {
			$('#modal_front_post_news').find('.modal-title').html('Update news');
			$('#frm_news').addClass('hidden');
			$('#modal_front_post_news').find('.result-msg').html('<div>Loading...</div>').removeClass('hidden');
			$('#modal_front_post_news').find('.modal-body').css('height', 'auto');
			// $(panel_view_more_comments).html('<div>Loading...</div>');
		}
   	})
   	.success(function( data ) {
       	if ( data.status == "success" ) {
       		// Fill up the form
       		$('#news_aid').val(data.result['aid']);
       		$('#title').val(data.result['title']);
       		tinyMCE.get('description_1').setContent( data.result['description'] );
       		$('#ref_link').val(data.result['ref_link']);
       		$('#ref_link2_url').val(data.result['ref_link2_url']);
       		$('#ref_link2_image_url').val(data.result['ref_link2_image_url']);
       		$('#ref_link2_title').val(data.result['ref_link2_title']);
       		$('#ref_link2_desc').val(data.result['ref_link2_desc']);

       		// Images
       		$('#panel_news_gallery_list').html('');
       		if (data.num_gallery > 0) {
				$.each(data.news_gallery_list, function(i, photo_detail) {
					$('#panel_news_gallery_list').append('<div class="gallery-photo pts pbs"><img src="'+'<?=site_url()?>'+data.result['upload_path']+'galleries/thumb/'+photo_detail['file_name']+'" style="max-width:150px;"/> <br><a href="javascript:void(0);" class="text-danger" onclick="deleteMyNewsGalleryPhoto(this)" data-aid="'+photo_detail['aid']+'">Delete</a></div>');
					
				});
       		}
       			
       		// Category list
       		var rList = data.result['category'].split(/,/);
			// $('input[name=category]').removeAttr('checked');
			$('input[name=category]').prop('checked', false);
			$.each(rList, function(i,category_aid){
				if ($('#category_'+category_aid).length > 0) {
					$('#category_'+category_aid).prop('checked', true);
				}
			});

       		// Show modal
	        $('#frm_news').removeClass('hidden');
			$('#modal_front_post_news').find('.result-msg').addClass('hidden');
			$('#modal_front_post_news').find('.modal-body').css('height', '400px');
			$('#modal_front_post_news').on('shown.bs.modal', function(e) {
				if (data.result['ref_link2_url'] != "" && data.result['ref_link2_image_url'] != "") {
		       		$('#get_url').val(data.result['ref_link2_url']).trigger('keyup', [ data.result['ref_link2_image_url'] ]);
	       		}
	       		else if (data.result['ref_link2_url'] != "") {
		       		$('#get_url').val(data.result['ref_link2_url']).trigger('keyup');
	       		}
			});
			$('#modal_front_post_news').modal('show');
			$('#command').val('_update');
       	}
       	else {
       		alert(data.msg);
       	}
    });
}

function deleteMyNews(e) {
	if (confirm('Are you sure you want to delete this news?')) {
		$.ajax({
           url: "<?=site_url('news/ajax-delete-one/'.rand())?>",
           type: "post",
           dataType: "json",
           data: { news_aid: $(e).attr('data-aid') }
       	})
       	.success(function( data ) {
           if ( data.status == "success" ) {
              window.location = "<?=site_url('news')?>";
              // $(e).closest('div.box-news').fadeOut();
           }
           else {
           	alert(data.msg);
           }
        });
	}
}

function deleteMyNewsGalleryPhoto(e) {
	if (confirm('Are you sure you want to delete this image?')) {
		$.ajax({
           url: "<?=site_url('news/ajax-delete-one-gallery-photo/'.rand())?>",
           type: "post",
           dataType: "json",
           data: { news_gallery_aid: $(e).attr('data-aid') }
       	})
       	.success(function( data ) {
           if ( data.status == "success" ) {
              $(e).closest('div.gallery-photo').fadeOut();
           }
           else {
           	alert(data.msg);
           }
        });
	}
}


</script>