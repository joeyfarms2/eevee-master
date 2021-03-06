<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<?php include_once("include_meta.php"); ?>
</head>

<script type="text/javascript">
	jQuery(document).ready(function() {
		// getShelfBadge();
		// update_basket_badge();
	});
</script>

<style>
@media (min-width:480px) and (max-width:767px) 
{	
	h2.subTitle 
	{
	    font-size: 20px;
	    margin-top: 5px;
	}
}
@media (min-width:360px) and (max-width:479px)
{
	h2.subTitle 
	{
	    font-size: 18px;
	    margin-top: 5px;
	}
}

@media (min-width:320px) and (max-width:359px)
{
	h2.subTitle 
	{
	    font-size: 15px;
	    margin-top: 5px;
	}
}

</style>


<body class="header2">
	<!-- globalWrapper -->
	<div id="globalWrapper" class="localscroll">
		<?php //include_once("include_header.php"); ?>
		<?php include_once("include_menu.php"); ?>
		<?php include_once("include_slider.php"); ?>
		
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

			<div class="container">
				<div class="row">
					<div class="col-md-9 custom-content-home-box">
						<!-- Content -->
						<?php
						include(get_content_file(@$view_the_content));
						?>
						<!-- End : Content -->
					</div>

					<!-- sidebar -->
					<?php include_once("include_right_home.php"); ?>
					<!-- End : Sidebar -->
				</div>
			</div>
		</section>
		<?php include_once("include_footer.php"); ?>
	</div>
	<?php include_once("include_script.php"); ?>
</body>
</html>
