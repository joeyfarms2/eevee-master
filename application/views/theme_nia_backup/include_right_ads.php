<?php 
	$ads_list = @$ads_list;
	// print_r($ads_list);
?>

	<!-- Show ads Box -->
	<?php if(is_var_array($ads_list)){ ?>
		<?php 
			foreach($ads_list as $ads){ 
			echo '<div class="boxFocus mb30">';
				$ref_link = get_array_value($ads,"ref_link","");
				$target = get_array_value($ads,"target","_self");
				$link_prefix = '';
				$link_post_fix = '';
				if(!is_blank($ref_link)){
					$link_prefix = '<a href="'.$ref_link.'" target="'.$target.'">';
					$link_post_fix = '</a>';
				}
				$title = get_array_value($ads,"title","");
				if(!is_blank($title)){
					echo '<h3>'.$link_prefix.$title.$link_post_fix.'</h3>';
				}
				$cover_image_thumb = get_array_value($ads,"cover_image_thumb","");
				if(!is_blank($cover_image_thumb)){
					echo '<p>'.$link_prefix.'<img src="'.$cover_image_thumb.'" />'.$link_post_fix.'</p>';
				}
				$description = get_array_value($ads,"description","");
				if(!is_blank($description)){
					echo '<p>'.$description.'</p>';
				}
			echo '</div>';
		?>

		<?php } ?>
	<?php } ?>
	<!-- End : Show ads Box -->