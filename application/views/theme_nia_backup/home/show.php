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
	//echo $sort_by;

?>	

<section id="sort-by">
	<div class="container mt15 book-box">
		<div class="row">
			<div class="container mb20">
				<div class=" col-xs-8 sort-by-menu pl0">
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
		<div class="container mt15 book-box">
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
	<section id="projects">
		<div class="container mt15 book-box">
			<div class="row book-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"0",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"0",""));?>
						<?php } ?>	
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"1",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"1",""));?>
						<?php } ?>
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"2",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"2",""));?>
						<?php } ?>
					</article>
				</section>
			</div>
			<div class="row book-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"3",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"3",""));?>
						<?php } ?>
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"4",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"4",""));?>
						<?php } ?>
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"5",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"5",""));?>
						<?php } ?>
					</article>
				</section>
			</div>
			<div class="row book-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"6",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"6",""));?>
						<?php } ?>
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"7",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"7",""));?>
						<?php } ?>
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"8",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"8",""));?>
						<?php } ?>
					</article>
				</section>
			</div>
			<div class="row book-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"9",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"9",""));?>
						<?php } ?>
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"10",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"10",""));?>
						<?php } ?>
					</article>
					<article class="item text-center">
						<?php if($sort_by == 'author_a' || $sort_by == 'author_d'){ ?>
							<?=draw_item_author(get_array_value($resultList,"11",""));?>
						<?php }else{ ?>
							<?=draw_item(get_array_value($resultList,"11",""));?>
						<?php } ?>
					</article>
				</section>
			</div>
			<?=getPagination($optional);?>
		</div>
	</section>
<?php } ?>
<?php 
}else{
?>
<?php $message = set_message_error("Sorry, Do not have book in this category.")?>
<?php } ?>
<?php 
function draw_item($item){
	$txt = '';
	$txt .=	'<section class="boxContent">';
	
		if(is_var_array($item)){
			$txt .=	'<ul class=""><li><i class="fa fa-circle"></i>&nbsp;&nbsp;&nbsp;<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title_short","N/A") .'</a></li></ul>';
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
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),"thumb", get_array_value($item,"thumbnail_image","")).'" class="img-responsive"></a>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	return $txt;
}
function draw_item_author($item){
	$txt = '';
	$txt .=	'<section class="boxContent">';
	
		if(is_var_array($item)){
			$txt .=	'<ul class=""><li><i class="fa fa-circle"></i>&nbsp;&nbsp;&nbsp;<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"author","-").'</a></li></ul>';
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
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),"thumb",get_array_value($item,"thumbnail_image","")).'" class="img-responsive"></a>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	return $txt;
}
function draw_item_list($item){


	$data = getimagesize(get_image(get_array_value($item,"cover_image_thumb_path",""),"thumb", get_array_value($item,"thumbnail_image","")));
	$width = $data[0];
	$height = $data[1];
	//echo $width."  ".$height;
	// $description = get_array_value($item_result,"description","");
	$width_2 = ($width/3);
	//$height_2 = (($height/5)-20);

	$txt = '';
	if(is_var_array($item)){
		$txt .=	'<div class="row book-list">';
		$txt .=	'<section class="imgWrapper">';
		$is_license = get_array_value($item,"has_license","0");
		if($is_license == '1'){
			$txt .= '<div class="mask-ipad-view" style="margin-left:'.$width_2.'px;"></div>';
		}
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),"thumb",get_array_value($item,"thumbnail_image","")).'" class="img-responsive" ></a>';
		$txt .=	'</section>';

		$txt .=	'<section class="boxContent">';
		$txt .=	'<ul class=""><li><a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title","N/A") .'</a></li></ul>';
		$txt .=	'<div class="info"><span class="txt-red">Author :</span> '.get_array_value($item,"author","-").'</div>';
		$txt .=	'<div class="info"><span class="txt-red">Publishing Date :</span> '.get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($item,"publish_date",""), "N/A").'</div>';
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