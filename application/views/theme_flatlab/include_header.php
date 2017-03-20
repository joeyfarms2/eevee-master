<?php 
$my_user_branch_lilst = @$my_user_branch_lilst;
$branch_selected = @$branch_selected;

$reserve_noti_status_1 = @$reserve_noti_status_1;
$reserve_noti_status_2_today = @$reserve_noti_status_2_today;
$reserve_noti_status_2_overdue = @$reserve_noti_status_2_overdue;

?>


<header class="header white-bg">
	<div class="sidebar-toggle-box">
		<div class="fa fa-bars tooltips" data-placement="right" data-original-title=""></div>
	</div>
	<!--logo start-->
	<a href="<?=site_url('home')?>" class="logo"><?=$this->lang->line('ui_backend_logo_header')?></a>
	<!--logo end-->

	<div class="nav notify-row" id="top_menu">
		<?php if(CONST_HAS_TRANSACTION == "1"){ ?>
		<!--  notification start -->
		<ul class="nav top-menu">
			<!-- settings start -->
			<li class="dropdown">
				<a data-toggle="dropdown" class="dropdown-toggle" href="#">
					<i class="fa fa-big fa-clock-o status-0"></i>
					<?php if(is_var_array($reserve_noti_status_1)){ echo '<span class="badge bg-warning">'.count($reserve_noti_status_1).'</span>'; } ?>
				</a>
				<ul class="dropdown-menu extended tasks-bar">
					<div class="notify-arrow notify-arrow-yellow"></div>
					<?php
						if(is_var_array($reserve_noti_status_1)){
							echo '
							<li>
								<p class="yellow">You have '.count($reserve_noti_status_1).' pending tasks</p>
							</li>
							';
							foreach ($reserve_noti_status_1 as $item) {
								$barcode = get_array_value($item,"barcode","");
								$title = get_array_value($item,"title","");
								echo '
								<li>
									<a href="'.site_url('admin/reservation-product/show?barcode='.$barcode).'">'.$title.'</a>
								</li>
								';
							}
						}else{
							echo '
							<li>
								<p class="yellow">No pending tasks</p>
							</li>
							';
						}
					?>
				</ul>
			</li>

			<li class="dropdown">
				<a data-toggle="dropdown" class="dropdown-toggle" href="#">
					<i class="fa fa-big fa-bell status-3"></i>
					<?php if(is_var_array($reserve_noti_status_2_today)){ echo '<span class="badge bg-success">'.count($reserve_noti_status_2_today).'</span>'; } ?>
				</a>
				<ul class="dropdown-menu extended tasks-bar">
					<div class="notify-arrow notify-arrow-green"></div>
					<?php
						if(is_var_array($reserve_noti_status_2_today)){
							echo '
							<li>
								<p class="green">You have '.count($reserve_noti_status_2_today).' pending tasks</p>
							</li>
							';
							foreach ($reserve_noti_status_2_today as $item) {
								$barcode = get_array_value($item,"barcode","");
								$title = get_array_value($item,"title","");
								echo '
								<li>
									<a href="'.site_url('admin/reservation-product/show?barcode='.$barcode).'">'.$title.'</a>
								</li>
								';
							}
						}else{
							echo '
							<li>
								<p class="green">No pending tasks</p>
							</li>
							';
						}
					?>
				</ul>
			</li>

			<li class="dropdown">
				<a data-toggle="dropdown" class="dropdown-toggle" href="#">
					<i class="fa fa-big fa-bell status-4"></i>
					<?php if(is_var_array($reserve_noti_status_2_overdue)){ echo '<span class="badge bg-important">'.count($reserve_noti_status_2_overdue).'</span>'; } ?>
				</a>
				<ul class="dropdown-menu extended tasks-bar">
					<div class="notify-arrow notify-arrow-red"></div>
					<?php
						if(is_var_array($reserve_noti_status_2_overdue)){
							echo '
							<li>
								<p class="red">You have '.count($reserve_noti_status_2_overdue).' pending tasks</p>
							</li>
							';
							foreach ($reserve_noti_status_2_overdue as $item) {
								$barcode = get_array_value($item,"barcode","");
								$title = get_array_value($item,"title","");
								echo '
								<li>
									<a href="'.site_url('admin/reservation-product/show?barcode='.$barcode).'">'.$title.'</a>
								</li>
								';
							}
						}else{
							echo '
							<li>
								<p class="red">No pending tasks</p>
							</li>
							';
						}
					?>
				</ul>
			</li>

		</ul>
		<?php } ?>
	</div>

	<div class="top-nav ">
		<!--search & user info start-->
		<ul class="nav pull-right top-menu">
			<?php if(CONST_HAS_TRANSACTION == "1"){ ?>
			<li>
				<a class="transactions-menu" href="<?=site_url('admin/transaction/add')?>">
					<i class=" fa fa-calendar"></i><span class="username hidden-xs"> <?=get_language_line($this, 'ui_backend_menu_transaction', 'Circulation')?><!-- Circulation --></span>
				</a>
			</li>
			<!-- <li>
				<a class="print-menu" href="< ?=site_url('admin/print/add')?>">
					<i class=" fa fa-print"></i><span class="username"> < ?=get_language_line($this, 'ui_backend_menu_print', 'Print Barcode')?><!- - Print Barcode - -></span>
				</a>
			</li> -->
			<?php } ?>
			<!-- user login dropdown start-->
			<li class="dropdown">
				<a data-toggle="dropdown" class="dropdown-toggle user-info" href="#">
					<?=get_array_value($user_login_info,"avatar_tiny","")?>
					<span class="username hidden-xs"><?=getUserLoginFullName($user_login_info)?></span>
					<b class="caret"></b>
				</a>
				<ul class="dropdown-menu extended logout">
					<div class="log-arrow-up"></div>
					<li><a href="<?=site_url('my-account')?>"><i class=" fa fa-user"></i>Profile</a></li>
					<li><a href="<?=site_url('change-password')?>"><i class="fa fa-lock"></i>Change<BR />Password</a></li>
					<li><a href="<?=site_url('home')?>"><i class="fa fa-home"></i>Go to<BR />Homepage</a></li>
					<li><a href="<?=site_url('logout')?>"><i class="fa fa-key"></i> Log Out</a></li>
				</ul>
			</li>
			<!-- user login dropdown end -->
		</ul>
		<!--search & user info end-->
	</div>
</header>
