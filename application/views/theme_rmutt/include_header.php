<?php
$this_front_tab_menu = @thisFrontTabMenu;
$this_front_sub_menu = @thisFrontSubMenu;
$master_product_main = @$master_product_main;
$this_product_main_name = @$this_product_main_name;
$page_name = @$page_name;
?>
<section id="main-header">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<a href="<?=site_url('home')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/logo-header-bar.png" /></a>
			</div>
			<div class="col-md-7 header-menu">
				<div class="row">
					<ul>
					<?php if(is_login()){ ?>
							<li><a href="<?=site_url('logout')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/btn-logout.png" class="header-btn" /></a></li>
							<li style="font-size:22px;color:#7e7e7e">|</li>
					<?php } ?>

						<li><a href="<?=site_url('my-bookshelf')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/btn-shelf.png" class="header-btn" /></a></li>
						<li style="font-size:22px;color:#7e7e7e">|</li>

					<?php if(is_login()){ ?>
						<?php if(is_staff_or_higher() && @$mode != "backend"){ ?>
							<li><a href="<?=site_url('admin/dashboard')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/btn-setting.png" class="header-btn" /></a></li>
							<li style="font-size:22px;color:#7e7e7e">|</li>
						<?php } ?>

						<?php if(is_staff_or_higher() && @$mode != "front"){ ?>
							<li><?=anchor("home",'Home',array('title'=>''))?></li>
						<?php } ?>
						
						<li><a href="<?=site_url('my-account')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/btn-account.png" class="header-btn" /></a></li>
														
					<?php }else{ ?>
						<?php if(CONST_ONLINE_REGIS == '1'){ ?>
							<li><a href="<?=site_url('registration')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/btn-signup.png" class="header-btn" /></a></li>
							<li style="font-size:22px;color:#7e7e7e">|</li>
						<?php } ?>
							<li><a href="<?=site_url('login')?>"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/btn-login.png" class="header-btn" /></a></li>
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