<?php 
$redeem_main_detail = @$redeem_main_detail;
?>

<?php if(is_var_array($redeem_main_detail)){ ?>
	<header class="panel-heading no-radius tab-bg-dark-navy-blue">
		<ul class="nav nav-tabs">

			<?php if($this_admin_sub_menu == "general_info"){ ?>
				<li class="active">
					<a>
						<i class="fa fa-info"></i>
						General Info
					</a>
				</li>
			<?php }else{ ?>
				<li>
					<a href="<?=site_url('admin/redeem/edit/'.get_array_value($redeem_main_detail,"aid",""))?>">
						<i class="fa fa-info"></i>
						General Info
					</a>
				</li>
			<?php }?>

			<?php if($this_admin_sub_menu == "detail"){ ?>
				<li class="active">
					<a>
						<i class="fa fa-copy"></i>
						Detail
					</a>
				</li>
			<?php }else{ ?>
				<li>
					<a href="<?=site_url('admin/redeem/edit/'.get_array_value($redeem_main_detail,"aid","").'/detail')?>">
						<i class="fa fa-copy"></i>
						Detail
					</a>
				</li>
			<?php }?>

		</ul>
	</header>
<?php }?>
