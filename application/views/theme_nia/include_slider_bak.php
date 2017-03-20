<?php
$this_front_tab_menu = @thisFrontTabMenu;
$this_front_sub_menu = @thisFrontSubMenu;
$master_product_main = @$master_product_main;
$this_product_main_name = @$this_product_main_name;
$page_name = @$page_name;

// print_r($master_product_main);
// echo "<br/>";
// print_r($this_product_main_name);
?>
<?php if(is_var_array(@$banner_result)){ ?>
<section id="layer-slider">
	<div id="layerslider-container-fw"> 
		<?php /*<div id="layerslider" style="width: 100%; height: 450px;" class="">*/ ?>
		<div id="layerslider" style="width: 100%; height: 450px;" class="">
			<?php 
				foreach ($banner_result as $banner) {
					$img_src = get_image(get_array_value($banner,"cover_image_actual_path",""),"small","off");
					$ref_link = get_array_value($banner,"ref_link",""); 
					$target = get_array_value($banner,"target","");
					 
				?>
				<?php if(!is_blank($img_src)){ 
					
				?>
				<div class="ls-layer" style="slidedirection: right; transition2d: 5; ">
					<?php if(!is_blank($ref_link)){ 
						
					?>
						<a href="<?=$ref_link?>"><img src="<?=$img_src?>" class="ls-bg button" alt="" onclick="processRedirect('<?=$ref_link?>', '<?=$target?>')" /></a>
					<?php }else{ ?>
						<img src="<?=$img_src?>" class="ls-bg" alt="" />
					<?php } ?>
					
				</div>
				<?php } ?>
			<?php } ?>
			<div class="ls-s-1 text-slider-header-top" style="position: absolute; z-index:99;top:360px;right:0px; padding:15px; color:#FFF; background-color: rgba(0, 0, 0, .5); font-weight:300; slidedirection : top; slideoutdirection : right; durationin : 1000; durationout : 1000; easingin : easeOutElastic; easingout : easeInOutQuint; delayin : 500;">
				<span class="textStart"><p>DIGITAL LIBRARY</p></span>
				<span class="textSub">NATIONAL INTELLIGENCE AGENCY</span>
			</div>
			<?php /*
			<div class="ls-s-1" style="position: absolute;width: 100%; z-index:99; font-weight:300;slidedirection : top; slideoutdirection : right; durationin : 1000; durationout : 1000; easingin : easeOutElastic; easingout : easeInOutQuint; delayin : 500; background: linear-gradient(rgba(255,255, 255, 1), rgba(255,255, 255, 0));">
				<section id="main-header">
					<div class="container">
						<div class="row">
							<div class="col-md-5">
								<a href="<?=site_url('home')?>"><img class="logo_image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/logo-header-bar.png" /></a>
							</div>
							<div class="col-md-7 header-menu">
								<div class="row">
									<ul>
									<?php if(is_login()){ ?>
											<li><a href="<?=site_url('logout')?>">SIGN OUT</a></li>
									<?php } ?>

										

									<?php if(is_login()){ ?>

										<li><a href="<?=site_url('my-bookshelf')?>">MY SHELF</a></li>
										
										<?php if(is_staff_or_higher() && @$mode != "backend"){ ?>
											<li><a href="<?=site_url('admin/dashboard')?>">CONTROL PANEL</a></li>
										<?php } ?>

										<?php if(is_staff_or_higher() && @$mode != "front"){ ?>
											<li><?=anchor("home",'Home',array('title'=>''))?></li>
										<?php } ?>
										
										<li ><a href="<?=site_url('my-account')?>"><?=getUserLoginFullName($user_login_info)?></a></li>
										<li style="padding:0px 0px !important;"><span class="circle"><?=get_array_value($user_login_info,"avatar_tiny","")?></span></li>							
									
									<?php }else{ ?>
										<?php if(CONST_ONLINE_REGIS == '1'){ ?>
											<li><a href="<?=site_url('registration')?>">SIGN UP</a></li>
										<?php } ?>
											<li><a href="<?=site_url('login')?>">SIGN IN</a></li>
									<?php } ?>
									</ul>
								</div>
								<?php if(@thisController != "search_front_controller"){ ?>
									<?php include_once("include_search_box.php"); ?>
								<?php } ?>
							</div>
						</div>
					</div>	
				</section>
			</div>
			*/ ?>
		</div>

	</div>
</section>
<?php } ?>
<style>

</style>

