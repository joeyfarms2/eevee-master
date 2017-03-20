<?php 

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
					<a href="<?=site_url('admin/news/edit/'.get_array_value($parent_detail,"aid",""))?>">
						<i class="fa fa-info"></i>
						General Info
					</a>
				</li>
			<?php }?>

			<?php if($this_admin_sub_menu == "gallery"){ ?>
				<li class="active">
					<a>
						<i class="fa fa-picture-o"></i>
						Photo Gallery
					</a>
				</li>
			<?php }else{ ?>
				<li>
					<a href="<?=site_url('admin/news/edit/'.get_array_value($parent_detail,"aid","").'/gallery')?>">
						<i class="fa fa-picture-o"></i>
						Photo Gallery
					</a>
				</li>
			<?php }?>

		</ul>
	</header>
<?php }?>
