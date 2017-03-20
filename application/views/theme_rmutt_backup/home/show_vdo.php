<?php
	$resultList = @$resultList;
	$optional = @$optional;
	$sort_by = @$sort_by;
	$url = @$url_for_sort;
	$url_for_sort = $url;
	$this_product_main_name = @$this_product_main_name;
	$product_main_aid = @$product_main_aid;
	$product_type_cid = @$product_type_cid;
	$master_category = @$master_category;
	$show_option = @$show_option;
?>	

<section id="sort-by">
	<div class="container mt15 vdo-box">
		<div class="row">
			<div class="container mb20">
				<div class=" col-xs-9 sort-by-menu pl0">
					<ul>
						<li>Sort by : </li>
						<?php 
							if($sort_by == 'pop_a' || $sort_by == 'pop_d'){ 
								if($sort_by == 'pop_d'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-pop_a", 'Popular',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'current', 'title' => '')).'</li>';
								}
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-name_a", 'Title',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-author_a", 'Author',array('class' => 'normal', 'title' => '')).'</li>';
							}else
							if($sort_by == 'date_a' || $sort_by == 'date_d'){ 
								echo '<li class="">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								if($sort_by == 'date_a'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-date_a", 'Date',array('class' => 'current', 'title' => '')).'</li>';
								}
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-name_a", 'Title',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-author_a", 'Author',array('class' => 'normal', 'title' => '')).'</li>';
							}else
							
							if($sort_by == 'name_a' || $sort_by == 'name_d'){ 
								echo '<li class="">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								if($sort_by == 'name_a'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-name_d", 'Title (A-Z)',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-name_a", 'Title (Z-A)',array('class' => 'current', 'title' => '')).'</li>';
								}
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-author_a", 'Author',array('class' => 'normal', 'title' => '')).'</li>';
							}

							if($sort_by == 'author_a' || $sort_by == 'author_d'){ 
								echo '<li class="">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-name_a", 'Title',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li>|</li>';
								if($sort_by == 'author_a'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-author_d", 'Author (A-Z)',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-author_a", 'Author (Z-A)',array('class' => 'current', 'title' => '')).'</li>';
								}
							}
						?>
					</ul>
				</div>

				<div class=" col-xs-3 pr0">
					<?php 
						$url_tmp = 'list-'.$product_type_cid.'/category/'.$this_product_main_url;
						if($show_option == "list"){
							$url_tmp .= "-list";
						}
						$url_tmp .= "/c-";
					?>
					<select class="form-control" id="category" onchange="processRedirect('<?=$url_tmp?>'+this.value)">
						<option value="0">All</option>
						<?php 
							if(is_var_array($master_category)){ 
								foreach ($master_category as $category) {
						?>
							<option value="<?=get_array_value($category,"url","none");?>" <?=($this_category_url == get_array_value($category,"url","none"))?"selected":"";?>><?=get_array_value($category,"name","N/A");?></option>
						<?php } } ?>
					</select>
				</div>
			</div>
		</div>     
	</div>
</section>

<section id="message-box">
	<div class="container">
		<div id="result-msg-box" class="hidden" ></div>
	</div>
</section>

<?php 
if(is_var_array($resultList)){
	if($show_option == "list"){
?>
	<section id="projects">
		<div class="container mt15 vdo-box">
<?php
		foreach ($resultList as $item) {
			echo draw_item_list($item);
		}
?>	
		<div>&nbsp;</div>
		<?=getPagination($optional);?>
		</div>
	</section>
<?php

	}else{
?>	
	<section class="mt30 imgHover">
		<div class="row vdo-item">
		<?php foreach($resultList as $item){ 
			// print_r($item);
			$product_main_aid = get_array_value($item,"product_main_aid","0");
		?>
			<article class="item text-center">
				<?=draw_item($item);?>
			</article>
		<?php } ?>									
		</div>
		<?=getPagination($optional);?>
	</section>
<?php } ?>
<?php 
}else{
?>
<?php $message = set_message_error("Sorry, Do not have vdo in this category.")?>
<?php } ?>
<?php 
function draw_item($item){
	$product_type_aid = get_array_value($item,"product_type_aid","");
	$class = ($product_type_aid == '3') ? "vdo-" : "";
	$txt = '';
	$txt .=	'<section class="boxContent">';
	if(is_var_array($item)){
		$txt .=	'<ul class=""><li><a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title_short","N/A") .'</a></li></ul>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	$txt .=	'<section class="imgWrapper">';
	if(is_var_array($item)){
		$is_license = get_array_value($item,"has_license","0");
		if($is_license == '1'){
			$txt .= '<div class="mask-ipad"></div>';
		}
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),$class."thumb").'" class="img-responsive"></a>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	return $txt;
}
function draw_item_list($item){
	$product_type_aid = get_array_value($item,"product_type_aid","");
	$class = ($product_type_aid == '3') ? "vdo-" : "";
	$txt = '';
	if(is_var_array($item)){
		$txt .=	'<div class="row vdo-list">';
		$txt .=	'<section class="imgWrapper">';
		$is_license = get_array_value($item,"has_license","0");
		if($is_license == '1'){
			$txt .= '<div class="mask-ipad"></div>';
		}
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),$class."thumb").'" class="img-responsive"></a>';
		$txt .=	'</section>';

		$txt .=	'<section class="boxContent">';
		$txt .=	'<ul class=""><li><a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title","N/A") .'</a></li></ul>';
		$txt .=	'<div class="info"><span class="txt-red">Author :</span> '.get_array_value($item,"author","-").'</div>';
		// $txt .=	'<div class="info"><span class="txt-red">Publishing Date :</span> '.get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item,"publish_date",""), "N/A").'</div>';
		$txt .=	'<div class="desc">'.get_array_value($item,"description","").'</div>';
		$txt .=	'</section>';
		$txt .=	'</div>';

	}
	return $txt;
}
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>