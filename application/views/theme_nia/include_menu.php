<!-- Navbar -->
<?php /*<div id="screen_area"></div>*/ ?>
<div class="show-resolution" id="screen_area"></div>
<nav class="navbar navbar-default">
  	<div class="container">
    	<div class="navbar-header">
      		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>                        
      		</button>
      	<a class="navbar-brand" href="<?=site_url('home')?>"><img class="logo_image" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/logo-header-bar.png" /></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      	<ul class="nav navbar-nav navbar-right">
      		<?php if(is_login()){ ?>
      		<li style="padding:0px 0px !important;"></li>
      		<li ><a href="<?=site_url('my-account')?>" title="Profile"><span class="circle"><?php /*=get_array_value($user_login_info,"avatar_tiny","")*/?><img src="/styles/<?=CONST_CODENAME?>/images/avatar/avatar.png" /></span>&nbsp;<span class="hidden-sm"><?=getUserLoginFirstNameTh($user_login_info)?></span></a></li>
      		<?php if(is_staff_or_higher() && @$mode != "backend"){ ?>
			<li><a href="<?=site_url('admin/dashboard')?>" title="SETTING"><span class="circle"><img src="/styles/<?=CONST_CODENAME?>/images/avatar/setting.png" /></span>&nbsp;<span class="hidden-sm">SETTING</span></a></li>
			<?php } ?>
      		<li><a href="<?=site_url('my-bookshelf')?>" title="MY SHELF"><span class="circle"><img src="/styles/<?=CONST_CODENAME?>/images/avatar/myshelf.png" /></span>&nbsp;<span class="hidden-sm">MY SHELF</span></a></li>
      		<li><a href="<?=site_url('logout')?>" title="SIGN OUT"><span class="circle"><img src="/styles/<?=CONST_CODENAME?>/images/avatar/logout.png" /></span>&nbsp;<span class="hidden-sm">SIGN OUT</span></a></li>
      		<?php } else {?>
      		<li><a href="<?=site_url('login')?>" title="SIGN IN"><span class="circle"><img src="/styles/<?=CONST_CODENAME?>/images/avatar/login.png" /></span>&nbsp;<span class="hidden-sm">SIGN IN</span></a></li>
      		<li><a href="<?=site_url('registration')?>" title="SIGN UP"><span class="circle"><img src="/styles/<?=CONST_CODENAME?>/images/avatar/register.png" /></span>&nbsp;<span class="hidden-sm">SIGN UP</span></a></li>
      		<?php } ?>
    	</ul>
    </div>
  </div>
</nav>
<?php if(@thisController != "search_front_controller"){ ?>
<div class="container">
	<div class="row">
		<section id="search-box" class="widget search clearfix">
			<form id="frm_search" name="frm_search" class="form-inline" role="form" method="get">
				<input type="hidden" id="search_type" name="search_type" value="marc" />  
							<!-- <div class="input-group">					
								<input type="text" class="form-control search-txt-box" size="20" placeholder="Keyword" value="" id="keyword" name="keyword" onkeypress="isEnterGoTo(event,'search_advance(1)')" />
								<span class="input-group-btn">
									<button type="button" style="background:#d67d2c !important;" class="btn btn-sm search-btn" onclick="search_advance(1)">
										<i class="fa fa-search fa-lg"></i> 
									</button>
								</span>
							</div> -->
				<div class="input-group">
				    <input type="text" class="form-control" placeholder="Keyword" id="keyword" name="keyword" onkeypress="isEnterGoTo(event,'search_advance(1)')">
				    <span class="input-group-btn">
				    	<button type="button" class="btn btn-flat" style="background:#d67d2c !important;"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</section>
	</div>
</div>
<?php }?>
<style>

.show-resolution
{
	position: fixed;
	top: 0px;
	width: 100%;
	height: 30px;
	text-align: right;
	padding-right: 10px;
}

.nav > li > a 
{
    padding: 10px 10px;
    font-size: 18px;
}

.navbar-nav > li > a:hover
{
	color: #555 !important;
}

.navbar
{
	margin-bottom: 0px;
}

#search-box 
{
	max-width: 100% !important;
	/*float: right !important;*/
	width: 500px !important;
	padding-right: 10px; !important
}

#keyword
{
	width: 300px !important;
}
.nav
{
	margin-top: 18px !important;
}

@media (min-width:901px) and (max-width:1024px)
{
	.logo_image
	{
		width: 270px;
	}
}

@media (min-width:768px) and (max-width:900px)
{	
	.logo_image
	{
		width: 250px;
	}

}
@media (min-width:480px) and (max-width:767px) 
{
	.text-slider-header-top
	{
		visibility: hidden;	
	}
	.logo_image
	{
		width: 220px;
	}
	#keyword
	{
		width: 100% !important;
	}
}
@media (min-width:360px) and (max-width:479px)
{
	.text-slider-header-top
	{
		visibility: hidden;	
	}
	.logo_image
	{
		width: 200px;	
	}

	#keyword
	{
		width: 100% !important;
	}
}
@media (min-width:320px) and (max-width:359px)
{
	.text-slider-header-top
	{
		visibility: hidden;	
	}
	.logo_image
	{
		width: 150px;
	}

	#keyword
	{
		width: 100% !important;
	}
}




</style>
<script>
	$(document).ready(function(){
		//var x = "Total Width: " + screen.width + "px";
		var x = "Total Width: " + $(window).width() + "px";
    	//document.getElementById("screen_area").innerHTML = x;	
    	$("#screen_area").html(x);

    	$( window ).resize(function() {
		  	var x = "Total Width: " + $(window).width() + "px";
    	
    		$("#screen_area").html(x);
		});
	});
	
</script>