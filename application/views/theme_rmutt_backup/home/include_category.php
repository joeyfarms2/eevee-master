<?php 
	$master_category = @$master_category;
	$master_product_main = @$master_product_main;
	$master_publisher_by_product_main = @$master_publisher_by_product_main;
	$this_product_main_name = @$this_product_main_name;
	$this_category_name = @$this_category_name;
	$this_publisher_name = @$this_publisher_name;
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="">
	<tr>
		<td style="height:23px;">&nbsp;</td>
	</tr>
	<tr>
		<td class="right-header">TYPE</td>
	</tr>
	<tr>
		<td>
			<p class="style40 menulink">
				<?php 
					if(is_var_array($master_product_main)){
						foreach($master_product_main as $item){
							$selected = "";
							if($this_product_main_name == get_array_value($item,"name","-")){
								$selected = " class='selected' ";
							}
							echo '<a href="'.site_url('category/'.get_array_value($item,"name","-")).'" '.$selected.'>'.get_array_value($item,"name","-").'</a><BR>';
						}
					} 
				?>
			</p>                
		</td>
	</tr>
<?php if(is_var_array($master_category)){ ?>
	<tr>
		<td class="right-header">CATEGORIES</td>
	</tr>
	<tr>
		<td height="">
			<p class="style40 menulink">
				<a href="<?=site_url('category/'.$this_product_main_name)?>" <?php if(is_blank($this_category_name)) echo" class='selected' " ?>>All</a><br />
				<?php 
					foreach($master_category as $item){
						$selected = "";
						if($this_category_name == get_array_value($item,"name","-")){
							$selected = " class='selected' ";
						}
						echo '<a href="'.site_url('category/'.$this_product_main_name.'/c-'.get_array_value($item,"name","-")).'" '.$selected.'>'.get_array_value($item,"name","-");
						if(get_array_value($item,"total","0") > 0) echo ' ('.get_array_value($item,"total","0").')';
						echo '</a><BR>';
					}
				?>
			</p>
		</td>
	</tr>
<?php } ?>
<?php if(is_var_array($master_publisher_by_product_main)){ ?>
	<tr>
		<td class="right-header">PUBLISHER</td>
	</tr>
	<tr>
		<td>
			<p class="style40 menulink">
				<a href="<?=site_url('category/'.$this_product_main_name.'/'.$this_category_name)?>" <?php if(is_blank($this_publisher_name)) echo" class='selected' " ?>>All</a><br />
				<?php 
					if(is_var_array($master_publisher_by_product_main)){
						foreach($master_publisher_by_product_main as $item){
							$selected = "";
							if($this_publisher_name == get_array_value($item,"aid","-")){
								$selected = " class='selected' ";
							}
							echo '<a href="'.site_url('category/'.$this_product_main_name.'/p-'.get_array_value($item,"aid","-")).'" '.$selected.'>'.get_array_value($item,"name","-");
							if(get_array_value($item,"total","0") > 0) echo ' ('.get_array_value($item,"total","0").')';
							echo '</a><BR>';
						}
					} 
				?>
			</p>                
		</td>
	</tr>
	<tr>
		<td align="center">
			<div style="padding-top:20px;">
				<a href="http://itunes.apple.com/th/app/bookdose/id459444997?mt=8" target="_blank"><img src="<?=IMAGE_PATH?>appstore.png" class="button" /></a>
			</div>
		</td>
	</tr>
<?php } ?>
</table>