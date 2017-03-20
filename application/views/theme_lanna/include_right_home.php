<?php 
	$new_list = @$new_list;
	$recommended_list = @$recommended_list;
	$product_type_aid = @$product_type_aid;

	$word = "books";
	$class = "";
	if($product_type_aid == "2"){
		$word = "magazines";
	}else	if($product_type_aid == "3"){
		$word = "vdo";
		$class = "vdo-";
	}
?>

<aside id="sidebar2" class="col-md-4 custom-right-box">
	<?php include_once("include_right_ads.php"); ?>
	
	<!-- Show New Box -->
	<?php if(is_var_array($new_list)){ ?>
	<div class="boxFocus ">
		<h3>Most Popular</h3>
		<p>Most popular <?=$word?>, Let check it out!</p>
		<?php foreach($new_list as $item){ ?>
			<div class="col-md-12">
				<section class="boxContent">
					<h4><i class="fa fa-circle"></i>&nbsp;<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid",""))?>"><?=get_array_value($item,"title_short","") ?></a></h4>
				</section>
				<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid",""))?>"><img src="<?=get_image(get_array_value($item,"cover_image_thumb_path",""), $class."thumb")?>" alt="<?=removeAllQuote(get_array_value($item,"title",""))?>" class="mb15 img-responsive"></a>
			</div>
		<?php } ?>
	</div>
	<hr/>
	<?php } ?>
	<!-- End : Show New Box -->
	
	<!-- Show Recommend Box -->
	<?php if(is_var_array($recommended_list)){ ?>
	<div class="boxFocus ">
		<h3>Librarian's Choices</h3>
		<p>The recommended <?=$word?> selected by your librarian.</p>
		<?php foreach($recommended_list as $item){ ?>
			<div class="col-md-12">
				<section class="boxContent">
					<h4><i class="fa fa-circle"></i>&nbsp;&nbsp;&nbsp;<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid",""))?>"><?=get_array_value($item,"title_short","") ?></a></h4>
				</section>
				<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid",""))?>"><img src="<?=get_image(get_array_value($item,"cover_image_thumb_path",""), $class."thumb")?>" alt="<?=removeAllQuote(get_array_value($item,"title",""))?>" class="mb15 img-responsive"></a>
			</div>
		<?php } ?>
	</div>
	<hr/>
	<?php } ?>
	<!-- End : Recommend Box -->
	
	<!-- FB Box -->
	<?php if(CONST_SHOW_FB == '1'){ ?>
		<div class="boxFocus fb-box">
			<h3>Facebook</h3>
			<div class="fb_home">
				<div class="fb-like-box" data-href="<?=CONST_FB_LINK?>" data-width="200" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="true" data-show-border="false"></div>
			</div>
		</div>

		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<?php } ?>
	<!-- End : FB Box -->

</aside>