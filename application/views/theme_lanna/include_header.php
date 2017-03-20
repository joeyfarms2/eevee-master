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
<section id="main-header">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<a href="<?=site_url('home')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/logo-header-bar.png" style="position:absolute;z-index:9;" /></a>
			</div>
			<div class="col-md-7 header-menu">
				<div class="row">
					<ul>
					<?php if(is_login()){ ?>
							<li><a href="<?=site_url('logout')?>">SING OUT</a></li>
							<li style="font-size:22px;color:#ffffff">|</li>

							<li><a href="<?=site_url('my-bookshelf')?>">MY SHELF</a></li>
							<li style="font-size:22px;color:#ffffff">|</li>

					<?php } ?>



					<?php if(is_login()){ ?>
						<?php if(is_staff_or_higher() && @$mode != "backend"){ ?>
							<li><a href="<?=site_url('admin/dashboard')?>">CONTROL PANEL</a></li>
							<li style="font-size:22px;color:#ffffff">|</li>
						<?php } ?>

						<?php if(is_staff_or_higher() && @$mode != "front"){ ?>
							<li><?=anchor("home",'Home',array('title'=>''))?></li>
							<li style="font-size:22px;color:#ffffff">|</li>
						<?php } ?>
						
						<li ><a href="<?=site_url('my-account')?>"><?=getUserLoginFullName($user_login_info)?></a></li>
						<li style="padding:2px 0px 0px 0px  !important;"><span class=""><?=get_array_value($user_login_info,"avatar_tiny","")?></span></li>							
					
					<?php }else{ ?>
						<?php if(CONST_ONLINE_REGIS == '1'){ ?>
							<li><a href="<?=site_url('registration')?>">SING UP</a></li>
							<li style="font-size:22px;color:#ffffff">|</li>
						<?php } ?>
							<li><a href="<?=site_url('login')?>">SING IN</a></li>
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