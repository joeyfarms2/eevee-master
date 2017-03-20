<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<?php include_once("include_meta.php"); ?>

	<script type="text/javascript" src="<?=JS_PATH?>shelf/my_bookshelf.js"></script>
	<link rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/css/myshelf.css" type="text/css" media="all">
</head>

<script type="text/javascript">
	jQuery(document).ready(function() {
		// getShelfBadge();
		// update_basket_badge();
	});
</script>

<?php
$thisFrontSubMenu = thisFrontSubMenu;
?>

<style>
.tab-menu
{
	padding: 10px 20px;
}

/*.hidden-xs
{
	display:block !important;
}*/
.container
{
	width: 970px;
}

.pull-right li a
{
	text-align: right;
}

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
						<div class="col-xs-12 col-sm-9 pl0">
							<h2 class="subTitle mb15">
								<a href="<?=site_url('home');?>"> <i class="fa fa-home"></i> </a>
							</h2>
							<h2 class="subTitle mb15"> | </h2>
							<h2 class="subTitle mb15">
								<a href="<?=site_url('your-library');?>"> MAINMENU </a>
							</h2>
							<h2 class="subTitle mb15"> | </h2>
							<h2 class="subTitle mb15"> <?=@$page_title?> </h2>
						</div>
						<?php if($thisFrontSubMenu!='my_transaction' && $thisFrontSubMenu!='my_bookshelf_vdo'){ ?>
						<div class="col-sm-3 view-by-menu pr10 mt10 hidden-xs">
							<ul>
								<li class="<?=($show_option == 'shelf') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf/sort-'.$sort_by)?>"><i class="fa fa-th-large"></i></a></li>
								<li class="<?=($show_option == 'list') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf-list/sort-'.$sort_by)?>"><i class="fa fa-list-ul"></i></a></li>
								<li>View : </li>
							</ul>
						</div>
						<?php }?>
					</div>
				</div>     
			</div>		

			
			<div class="container">
				<div class="row hidden-xs">
				<?php if($thisFrontSubMenu != 'my_transaction'){ ?>
					<div class=" col-xs-6 sort-by-menu mt10 pl10">
						<?php 
							$sort_by = @$sort_by; 
							$show_option = @$show_option;
							$url = 'my-bookshelf-vdo';
							if($show_option == "list"){
								$url .= '-list';
							}
						?>
						<ul>
							<li>Sort by : </li>
							<?php 
								switch ($sort_by) {
									case 'date_d':
									case 'date_a':
										if($sort_by == 'date_a'){
											echo '<li class="active"><a href="'. site_url($url.'/sort-date_d') .'">Date</a></li>';
										}else{
											echo '<li class="active"><a href="'. site_url($url.'/sort-date_a') .'">Date</a></li>';
										}
										echo '<li>|</li>';
										echo '<li><a href="'. site_url($url.'/sort-name_a') .'">Title</a></li>';
										break;
											
									case 'name_d':
									case 'name_a':
										echo '<li><a href="'. site_url($url.'/sort-date_d') .'">Date</a></li>';
										echo '<li>|</li>';
										if($sort_by == 'name_a'){
											echo '<li class="active"><a href="'. site_url($url.'/sort-name_d') .'">Title</a></li>';
										}else{
											echo '<li class="active"><a href="'. site_url($url.'/sort-name_a') .'">Title</a></li>';
										}
										break;
											
									default:
										break;
								}
							?>
						</ul>
					</div>
				<?php }else{?>
				<div class=" col-xs-6 sort-by-menu mt10 pl10">
					&nbsp;
				</div>
				<?php }?>
					<div class=" col-xs-6 sort-by-menu a-right">
						<div class="tab-menu <?=($thisFrontSubMenu=='my_transaction') ? "active" : "";?>"><a href="<?=site_url('my-transaction-list')?>">Book</a></div>
						<div class="tab-menu <?=($thisFrontSubMenu=='my_bookshelf_vdo') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf-vdo')?>">VDO</a></div>
						<div class="tab-menu <?=($thisFrontSubMenu=='my_bookshelf') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf')?>">E-Book</a></div>
					</div>
				</div>
				<div class="row visible-xs">
					<?php if($thisFrontSubMenu!='my_transaction' && $thisFrontSubMenu!='my_bookshelf_vdo'){ ?>
					<div class="mt15 col-xs-4" style="z-index:999;">
						<div class="btn-group">
						   	<button type="button" class="btn btn-primary dropdown-toggle" style="background:#d67d2c; border:#fff !important" data-toggle="dropdown">
								View <span class="caret"></span>
							</button>
							<ul class="dropdown-menu menu-order" role="menu">
								<li class="<?=($show_option == 'shelf') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf/sort-'.$sort_by)?>"><i class="fa fa-th-large"></i></a></li>
								<li class="<?=($show_option == 'list') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf-list/sort-'.$sort_by)?>"><i class="fa fa-list-ul"></i></a></li>
							</ul>
						</div>
					</div> 
					<?php }else{?>
					<div class="mt15 col-xs-4" style="z-index:999;">
						&nbsp;
					</div>
					<?php }?>

					<?php if($thisFrontSubMenu!='my_transaction'){ ?>
					<div class="mt15 col-xs-4 pr10 " style="z-index:999;">
						<div class="btn-group">
						   	<button type="button" class="btn btn-primary dropdown-toggle" style="background:#d67d2c; border:#fff !important" data-toggle="dropdown">
								Order By <span class="caret"></span>
							</button>
							<ul class="dropdown-menu menu-order" role="menu">
								<?php 
									switch ($sort_by) {
										case 'date_d':
										case 'date_a':
											if($sort_by == 'date_a'){
												echo '<li class="active"><a href="'. site_url($url.'/sort-date_d') .'">Date</a></li>';
											}else{
												echo '<li class="active"><a href="'. site_url($url.'/sort-date_a') .'">Date</a></li>';
											}
											echo '<li><a href="'. site_url($url.'/sort-name_a') .'">Title</a></li>';
											break;
												
										case 'name_d':
										case 'name_a':
											echo '<li><a href="'. site_url($url.'/sort-date_d') .'">Date</a></li>';
											if($sort_by == 'name_a'){
												echo '<li class="active"><a href="'. site_url($url.'/sort-name_d') .'">Title</a></li>';
											}else{
												echo '<li class="active"><a href="'. site_url($url.'/sort-name_a') .'">Title</a></li>';
											}
											break;
												
										default:
											break;
									}		
								?>
							</ul>
						</div>
					</div>
					<?php }else{?>
					<div class="mt15 col-xs-4 pr10 " style="z-index:999;">
					</div>
					<?php }?>
					<div class="mt15 col-xs-4">
						<div class="btn-group">
						   	<button type="button" class="btn btn-primary dropdown-toggle" style="background:#d67d2c; border:#fff !important" data-toggle="dropdown">
								Type <span class="caret"></span>
							</button>
							<ul class="dropdown-menu pull-right menu-order" role="menu">
								<li class="<?=($thisFrontSubMenu=='my_transaction') ? "active" : "";?>"><a href="<?=site_url('my-transaction-list')?>">Book</a></li>
								<li class="<?=($thisFrontSubMenu=='my_bookshelf_vdo') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf-vdo')?>">VDO</a></li>
								<li class="<?=($thisFrontSubMenu=='my_bookshelf') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf')?>">E-Book</a></li>
							</ul>
						</div>
					</div>

				</div>
			</div>

			
			<!-- title -->
			<div id="my-bookshelf">
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
