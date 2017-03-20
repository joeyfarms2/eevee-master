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
<?php
	$url_for_list = @$url_for_list;
	$url_for_shelf = @$url_for_shelf;
	$this_product_main_name = @$this_product_main_name;

?>

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
					<div class="mt15 row">
						<?php if($this_product_main_name == "Knowledge Resources"){?>
						<div class="col-xs-12 col-sm-9 pl0">
							<h2 class="subTitle mb15"><a href="<?=site_url('home');?>"> <i class="fa fa-home"></i> </a></h2><h2 class="subTitle mb15"> | </h2><h2 class="subTitle mb15"><a href="<?=site_url('your-library');?>"> MAINMENU </a></h2><h2 class="subTitle mb15"> | </h2><h2 class="subTitle mb15"> Knowledge </h2>
						</div>
						<?php }else{?>
						<div class="col-xs-12 col-sm-9 pl0">
							<h2 class="subTitle mb15"><a href="<?=site_url('home');?>"> <i class="fa fa-home"></i> </a></h2><h2 class="subTitle mb15"> | </h2><h2 class="subTitle mb15"><a href="<?=site_url('your-library');?>"> MAINMENU </a></h2><h2 class="subTitle mb15"> | </h2><h2 class="subTitle mb15"> <?=@$this_product_main_name?> </h2>
						</div>
							<?php } ?>
						<div class="col-sm-3 view-by-menu pr10 mt10 hidden-xs">
							<ul>
								<li class="<?=($show_option == 'shelf') ? "active" : "";?>"><a href="<?=site_url($url_for_shelf)?>"><i class="fa fa-th-large"></i></a></li>
								<li class="<?=($show_option == 'list') ? "active" : "";?>"><a href="<?=site_url($url_for_list)?>"><i class="fa fa-list-ul"></i></a></li>
								<li >View : </li>
							</ul>
						</div>		
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
