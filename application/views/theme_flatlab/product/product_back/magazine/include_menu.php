<?php 
$product_main_result = @$product_main_result;
$product_main_url = get_array_value($product_main_result,"url","");
?>

<?php if(is_var_array($parent_detail)){ ?>
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
					<a href="<?=site_url('admin/product-'.$product_main_url.'/magazine/edit/'.get_array_value($parent_detail,"aid",""))?>">
						<i class="fa fa-info"></i>
						General Info
					</a>
				</li>
			<?php }?>

			<?php if($this_admin_sub_menu == "marc"){ ?>
				<li class="active">
					<a>
						<i class="fa fa-magazine"></i>
						MARC
					</a>
				</li>
			<?php }else{ ?>
				<li class=>
					<a href="<?=site_url('admin/product-'.$product_main_url.'/magazine/edit/'.get_array_value($parent_detail,"aid","").'/field')?>">
						<i class="fa fa-magazine"></i>
						MARC
					</a>
				</li>
			<?php }?>

			<?php if($this_admin_sub_menu == "copy"){ ?>
				<li class="active">
					<a>
						<i class="fa fa-copy"></i>
						Copy
					</a>
				</li>
			<?php }else{ ?>
				<li>
					<a href="<?=site_url('admin/product-'.$product_main_url.'/magazine/edit/'.get_array_value($parent_detail,"aid","").'/copy')?>">
						<i class="fa fa-copy"></i>
						Copy
					</a>
				</li>
			<?php }?>

		</ul>
	</header>
<?php }?>
