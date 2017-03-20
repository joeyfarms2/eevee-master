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
							<h2 class="subTitle mb15">
								<a href="<?=site_url('home');?>"> <i class="fa fa-home"></i> </a>
							</h2>
							<h2 class="subTitle mb15"> | </h2>
							<h2 class="subTitle mb15">
								<a href="<?=site_url('your-library');?>"> MAINMENU </a>
							</h2><h2 class="subTitle mb15"> | </h2>
							<h2 class="subTitle mb15"> <?=@$page_title?> </h2>
						</div>
					</div>     
			
			</div>		    
					<div class="container">
						<div class="row">
							<?php if($thisFrontSubMenu=='my_transaction'){ ?>
							<div class="col-xs-5 sort-by-menu pl0 sheft-top">
								&nbsp;
							</div>
							<?php }else if($thisFrontSubMenu=='my_bookshelf_vdo'){ ?>
							<div class=" col-xs-5 sort-by-menu pl0 sheft-top">
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
							<?php }else{ ?>
							<div class=" col-xs-3 sort-by-menu pl0 sheft-top">
								<?php 
									$sort_by = @$sort_by; 
									$show_option = @$show_option;
									$url = 'my-bookshelf';
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

							<div class=" col-xs-2 view-by-menu pr10 sheft-top">
								<?php 
									$sort_by = @$sort_by; 
									$show_option = @$show_option;
									$url = 'my-bookshelf';
									if($show_option == "list"){
										$url .= '-list';
									}
								?>
								<ul>
									<li class="<?=($show_option == 'shelf') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf/sort-'.$sort_by)?>"><i class="fa fa-th-large"></i></a></li>
									<li class="<?=($show_option == 'list') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf-list/sort-'.$sort_by)?>"><i class="fa fa-list-ul"></i></a></li>
									<li>View : </li>
								</ul>
							</div>
							<?php } ?>


							<div class=" col-xs-6 sort-by-menu pl0 a-right">
								<div class="tab-menu <?=($thisFrontSubMenu=='my_transaction') ? "active" : "";?>"><a href="<?=site_url('my-transaction-list')?>">Book</a></div>
								<div class="tab-menu <?=($thisFrontSubMenu=='my_bookshelf_vdo') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf-vdo')?>">VDO</a></div>
								<div class="tab-menu <?=($thisFrontSubMenu=='my_bookshelf') ? "active" : "";?>"><a href="<?=site_url('my-bookshelf')?>">E-Book</a></div>
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
