<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta property="og:title" content="<?=get_array_value(@$item_result,"title",DEFAULT_TITLE)?>"/>
	<meta property="og:author" content="<?=get_array_value(@$item_result,"author",DEFAULT_TITLE)?>"/>
	<meta property="og:type" content="book"/>
	<meta property="og:site_name" content="<?=ADMIN_EMAIL_NAME?>"/>
	<meta property="fb:admins" content="100002033774631"/>
	<meta property="og:image" content="<?=get_image(get_array_value($item_result,"cover_image_detail_path",""),"detail", get_array_value($item_result,"large_image",""))?>" />
	<meta property="og:description" content='<?=removeSingleQuote(get_array_value(@$item_result,"description",""))?>'/>	
	<?php include_once("include_meta.php"); ?>

</head>

<script type="text/javascript">
	jQuery(document).ready(function() {
		// getShelfBadge();
		// update_basket_badge();
	});
</script>

<?php

?>

<body class="header2">
	<!-- globalWrapper -->
	<div id="globalWrapper" class="localscroll">
		<?php //include_once("include_header.php"); ?>
		<?php include_once("include_slider.php"); ?>
		<?php include_once("include_menu.php"); ?>
		<section id="content">
			<!-- title -->
			<div class="bar-header">
					<div class="container">
						<div class="mt15 row" >
							<h2 class="subTitle mb15"><a href="<?=site_url('home');?>"> <i class="fa fa-home"></i> </a></h2><h2 class="subTitle mb15"> | </h2><h2 class="subTitle mb15"><a href="<?=site_url('your-library');?>"> MAINMENU </a></h2><h2 class="subTitle mb15"> | </h2><h2 class="subTitle mb15"> <?=@$page_title?> </h2>
						</div>
					</div>     
			</div>
			<!-- title -->
		<section id="message-box">
			<div class="container">
				<div id="result-msg-box" class="hidden" ></div>
			</div>
		</section>
			
			<div id="product-detail">
				<div class="container">
					<div class="row">
						<div class="col-md-12 custom-content-shelf-box">
							<!-- Content -->
							<?php
							include(get_content_file(@$view_the_content));
							?>
							<!-- End : Content -->
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php include_once("include_footer.php"); ?>
	</div>
	<?php include_once("include_script.php"); ?>
</body>
</html>
