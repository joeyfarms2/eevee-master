<div id="message-box">
	<div id="result-msg-box" class="hidden" ></div>
</div>


<section id="projects">
	<div class="container mt15 pan">
		
		<?php //if(is_var_array($news_result)) { ?>
			<div class="row mbl">
				<div class="col-md-8"><h3 id="page_category_name" class="textSub mts mbn"></h3></div>
				<div class="col-md-4">
					<?php if(is_var_array($news_cat_result)) { ?>
						<select id="ddl_news_category" class="form-control">
							<option value="all">All categories</option>
							<?php foreach ($news_cat_result as $k => $item) {
								echo '<option value="'.$item['aid'].'">'.$item['name'].'</option>';
							}
							?>
						</select>
					<?php } ?>
				</div>
			</div>
		<?php //} ?>


		<div id="main_content">
		</div>


	</div>
</section>

<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/autosize/jquery.autosize.min.js"></script>
<script type="text/javascript">
	$(document).ready(function($){

		function initWhoClicks() {
			// On mouse over who wows this news
			$('.news-actions').undelegate('.who-wow', 'mouseenter');
			$('.news-actions').delegate('.who-wow', 'mouseenter',function() {
				var this_node = $(this);
				var panel_box_news = $(this_node).closest('.box-news');
				var url = '<?=site_url("news/ajax-load-who-wow/")?>'+sid;
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
				var url = '<?=site_url("news/ajax-load-who-cheer/")?>'+sid;
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
				var url = '<?=site_url("news/ajax-load-who-comment/")?>'+sid;
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
				var url = '<?=site_url("news/ajax-load-who-thanks/")?>'+sid;
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

	   	// Initialize everything after loading button actions and comments panels
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
			// $('.news-comments.new').removeClass('new');
			// $('.news-activity.new').removeClass('new');
		}

		/*
     	function getDocHeight() {
		    var D = document;
		    return Math.max(
		        D.body.scrollHeight, D.documentElement.scrollHeight,
		        D.body.offsetHeight, D.documentElement.offsetHeight,
		        D.body.clientHeight, D.documentElement.clientHeight
		    );
		}
		*/
		function loadNextPage() {
			var sid = Math.floor(Math.random()*10000000000);
        	$.getJSON(
        		'<?=site_url("news/ajax-load-news-feed/")?>'+sid, 
				{ 
					page: localStorage.getItem('next_page'), 
					total_items: localStorage.getItem('total_items'),
					category_aid: $('#ddl_news_category').val()
				},
				function(data) {
			   	$('#main_content').append(data.html);
			   	setTimeout( initAll(), 500 );
			   	if (parseInt(data.next_page) > 0) {
			   		localStorage.setItem('next_page', data.next_page);
			   		localStorage.setItem('total_items', data.total_items);
			   		setTimeout( function() { $(window).on('scroll', loadNextPage()); }, 300 );
			   	}
			   	else {
			   		$(window).off('scroll');
			   	}
				}
        	);
		}

		function initWindowScroll() {
			// Scroll to bottom of the page andn then reload the next page
			$(window).scroll(function(e) {
				
		    	if ( document.documentElement.clientHeight + $(document).scrollTop() >= document.body.offsetHeight - 900 ) { 
		      // if($(window).scrollTop() + $(window).height() == getDocHeight()) {
		      	$(window).off('scroll');
		      	if (localStorage.getItem('next_page') != "") {
		      		loadNextPage();
		      	}
			    }
			});
		}


		// First load init, load the 1st page
		var sid = Math.floor(Math.random()*10000000000);
     	$.getJSON(
     		'<?=site_url("news/ajax-load-news-feed/")?>'+sid, 
			{ 
				page: 1, 
				total_items: '<?=$total_items?>',
				category_aid: $('#ddl_news_category').val()
			},
			function(data) {
		   	$('#main_content').append(data.html);
		   	localStorage.setItem('next_page', data.next_page);
		   	localStorage.setItem('total_items', data.total_items);
		   	initAll();
			}
     	);
     	$('#page_category_name').html($('#ddl_news_category').find('option:selected').text());
     	initWindowScroll();
		
		


		$('#ddl_news_category').change(function() {
			var sid = Math.floor(Math.random()*10000000000);
			var url = '<?=site_url("news/ajax-load-news-feed/")?>'+sid;
			$.ajax({
            url: url,
            type: "post",
            dataType: "json",
            data: { 
						page: 1, 
						total_items: localStorage.getItem('total_items'),
						category_aid: $(this).val()
					},
            beforeSend:function(data) {
  					$('#main_content').html('<div>Loading...</div>');
  				}
        	})
        	.success(function( data ) {
        		if ( data.status == 'success' ) {
        			$('#main_content').html(data.html);
	            localStorage.setItem('next_page', data.next_page);
	            localStorage.setItem('total_items', data.total_items);
	            $('#page_category_name').html($('#ddl_news_category').find('option:selected').text());
			   	initAll();
			   	initWindowScroll();
        		}

         });

     	});
		
		

		<?=@$message?>
		<?=@$js_code?>
	});
</script>