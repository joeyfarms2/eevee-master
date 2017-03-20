<?php
	$result_list = @$resultList;
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
<style>
.title-manu-home
{
	/*display: none;*/
	width: 100%;
    height:25px;
    border: none;
    background-repeat: no-repeat;
}
.show-title-manu-home
{
	/*display: none;*/
	width: 100%;
    height:25px;
    background-position: 0 0;
}

.boxContent-head
{
	width: 100%;
	height:3.4em;
	text-align: center;
}

.boxContent-head a
{
	color: #000;
	font-size: 16px;
}

.boxContent-head a:hover
{
	color: #000;
	text-decoration: none;
}
.imgWrapper
{
	width: 178px;
	height: 250px;
	text-align: center;
	position: relative;
	margin: 0px auto;
}



.imgWrapper img
{
	max-width: 100%;
    max-height: 100%;
    border:1px solid #000;
    position: absolute;
    left: 0px;
    bottom: 2px;
}

.vdoWrapper
{
	width: 200px;
	height: 150px;
	text-align: center;
	position: relative;
	margin: 0px auto;
}

.vdoWrapper img
{
	max-width: 100%;
    max-height: 100%;
    margin: 0px auto;
}

.shelf-under-book-big
{
	width: 100%;
	margin-top: -35px;
	z-index: -100;
}

.shelf-under-book
{
	margin-top: -78px;
	width: 100%;
	height:39px;
	z-index: -100;
}

.shelf-under-book-small
{
	width: 100%;
	height: 39px;
	margin-top: -35px;
	background-image: url(/styles/nia/images/background/bg-shelf-item.png);
	background-repeat: no-repeat;
	z-index: -100;
}

.visible-xs
{
	z-index: -100;	
}
.custom-home-box-seeall:hover
{
	text-decoration: none;
}

.menu-order li
{
	float: none;
	text-align: left
}
@media (min-width:901px) and (max-width:1024px)
{
	.head-image
	{
		position:absolute;
	    clip:rect(0,500px,25px,0);
	}
}
@media (min-width:768px) and (max-width:900px)
{	
	.container
	{
		width: 700px;
	}

	.bg-gray-box
	{
		height: 180px;
		width: 335px;
	}

	.shelf-under-book-big 
	{
		margin-top: -40px;
	}
}
@media (min-width:480px) and (max-width:767px) 
{
	.container
	{
		width: 400px;
	}

	.img-head-news img
	{
		width: 350px;
		height: auto;
	}
	.custom-content-home-box .vdo-item, .custom-content-home-box .book-item
	{
		background: none;
		width: 450px;
	}
	.custom-home-box-seeall h3 
	{
		font-size: 16px;
	}
	
	.boxContent{
		padding: 10px;
	}

	.imgWrapper
	{
		width: 150px;
		height: 200px;
	}

	.imgWrapper img
	{
		max-width: 100%;
	    max-height: 100%;
	    border:1px solid #000;
	    position: absolute;
	    left: 0px;
	    bottom: 2px;
	}

	.shelf-under-book
	{
		margin-top: -56px;
	}


	.vdoWrapper
	{
		width: 250px;
		height: 180px;	
	}
}
@media (min-width:360px) and (max-width:479px)
{
	.container
	{
		width: 350px;
	}

	.img-head-news img
	{
		width: 300px;
		height: auto;
	}
	.bg-gray-box
	{
		width: 350px;
	}
	.custom-content-home-box .vdo-item, .custom-content-home-box .book-item
	{
		background: none;
		width: 350px;
	}
	.custom-home-box-seeall h3 
	{
		font-size: 14px;
		margin-top: 5px;
	}

	.boxContent{
		padding: 10px;
	}

	.imgWrapper
	{
		width: 130px;
		height: 180px;
	}

	.imgWrapper img
	{
		max-width: 100%;
	    max-height: 100%;
	    border:1px solid #000;
	    position: absolute;
	    left: 0px;
	    bottom: 2px;
	}

	.shelf-under-book
	{
		margin-top: -56px;
	}


	.vdoWrapper
	{
		width: 200px;
		height: 150px;	
	}



}
@media (min-width:320px) and (max-width:359px)
{
	.container
	{
		width: 300px;
	}

	.img-head-news img
	{
		width: 250px;
		height: auto;
	}
	.bg-gray-box
	{
		width: 300px;
	}
	.custom-content-home-box .vdo-item, .custom-content-home-box .book-item
	{
		background: none;
		width: 300px;
	}
	.custom-home-box-seeall h3 
	{
		font-size: 12px;
		margin-top: 7px;
	}

	.boxContent{
		padding: 10px;
	}
	
	.imgWrapper
	{
		width: 100px;
		height: 150px;
	}

	.imgWrapper img
	{
		max-width: 100%;
	    max-height: 100%;
	    border:1px solid #000;
	    position: absolute;
	    left: 0px;
	    bottom: 2px;
	}

	.shelf-under-book
	{
		margin-top: -56px;
	}

	.vdoWrapper
	{
		width: 180px;
		height: 150px;
		
	}

	
}
</style>

<section id="sort-by" class="hidden-xs">
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

<section id="sort-by" class="visible-xs">
	<div class="container">
		<div class="row">
		<div class="mt15 col-xs-4 text-left pr10 " style="z-index:999;margin-top:15px; margin-left:-15px;">
			<div class="btn-group">
			   	<button type="button" class="btn btn-primary dropdown-toggle" style="background:#d67d2c; border:#fff !important" data-toggle="dropdown">
					View <span class="caret"></span>
				</button>
				<ul class="dropdown-menu menu-order" role="menu">
					<li class="<?=($show_option == 'shelf') ? "active" : "";?>"><a href="<?=site_url($url_for_shelf)?>"><i class="fa fa-th-large"></i></a></li>
					<li class="<?=($show_option == 'list') ? "active" : "";?>"><a href="<?=site_url($url_for_list)?>"><i class="fa fa-list-ul"></i></a></li>
				</ul>
			</div>
		</div> 

		<div class="mt15 col-xs-4 pr10 " style="z-index:999;margin-top:15px; margin-left:-15px;">
			<div class="btn-group">
			   	<button type="button" class="btn btn-primary dropdown-toggle" style="background:#d67d2c; border:#fff !important" data-toggle="dropdown">
					Order By <span class="caret"></span>
				</button>
				<ul class="dropdown-menu menu-order" role="menu">
					<?php 
							if($sort_by == 'pop_a' || $sort_by == 'pop_d'){ 
								if($sort_by == 'pop_d'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-pop_a", 'Popular',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'current', 'title' => '')).'</li>';
								}
								echo '<li class="">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-name_a", 'Title',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-author_a", 'Author',array('class' => 'normal', 'title' => '')).'</li>';
							}else
							if($sort_by == 'date_a' || $sort_by == 'date_d'){ 
								echo '<li class="">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'normal', 'title' => '')).'</li>';
								if($sort_by == 'date_a'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-date_a", 'Date',array('class' => 'current', 'title' => '')).'</li>';
								}
								echo '<li class="">'.anchor($url_for_sort."/sort-name_a", 'Title',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-author_a", 'Author',array('class' => 'normal', 'title' => '')).'</li>';
							}else
							
							if($sort_by == 'name_a' || $sort_by == 'name_d'){ 
								echo '<li class="">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'normal', 'title' => '')).'</li>';
								if($sort_by == 'name_a'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-name_d", 'Title (A-Z)',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-name_a", 'Title (Z-A)',array('class' => 'current', 'title' => '')).'</li>';
								}
								echo '<li class="">'.anchor($url_for_sort."/sort-author_a", 'Author',array('class' => 'normal', 'title' => '')).'</li>';
							}

							if($sort_by == 'author_a' || $sort_by == 'author_d'){ 
								echo '<li class="">'.anchor($url_for_sort."/sort-pop_d", 'Popular',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-date_d", 'Date',array('class' => 'normal', 'title' => '')).'</li>';
								echo '<li class="">'.anchor($url_for_sort."/sort-name_a", 'Title',array('class' => 'normal', 'title' => '')).'</li>';
								if($sort_by == 'author_a'){
									echo '<li class="active">'.anchor($url_for_sort."/sort-author_d", 'Author (A-Z)',array('class' => 'current', 'title' => '')).'</li>';
								}else{
									echo '<li class="active">'.anchor($url_for_sort."/sort-author_a", 'Author (Z-A)',array('class' => 'current', 'title' => '')).'</li>';
								}
							}
						?>
				</ul>
			</div>
		</div>  

		<div class=" col-xs-4 pr0 mt15">
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
	<?php /*<section class="mt30 imgHover">
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
	</section>*/ ?>
			<div class="row">
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"0",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"1",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"2",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"3",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"4",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"5",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"6",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"7",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"8",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"9",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"10",""));?>
				</div>
				<div class="col-xs-12 col-sm-4">
					<?=draw_item(get_array_value($result_list,"11",""));?>
				</div>
			</div>
<?php } ?>
<?php 
}else{
?>
<?php $message = set_message_error("Sorry, Do not have vdo in this category.")?>
<?php } ?>
<?php 
/*function draw_item($item){
	$product_type_aid = get_array_value($item,"product_type_aid","");
	$class = ($product_type_aid == '3') ? "vdo-" : "";
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
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),$class."thumb").'" class="img-responsive"></a>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	return $txt;
}*/
function draw_item($item){
	$version = "123454321";
	$product_type_cid = get_array_value($item,"product_type_cid","");
	$cover_class = "";

	if($product_type_cid == 'vdo'){
		$cover_class = "vdo-";
	}
	$txt = '';
	$txt .=	'<div class="boxContent">';

	//$txt .= '<div class="boxContent-head hidden-xs">';
	$txt .= '<div class="boxContent-head">';

	if(is_var_array($item)){
		$txt .=	'<a class="" href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'" title="'.removeAllQuote(get_array_value($item,"title","N/A")).'" >'.get_array_value($item,"title_short","N/A") .'</a>'; //title, title_short
	}else{
		$txt .=	'&nbsp;';
	}

	$txt .= '</div>';
	
	if($product_type_cid != 'vdo')
	{
		$txt .= '<div class="imgWrapper">';
	}
	else{
		$txt .= '<div class="vdoWrapper">';
	}

	if(is_var_array($item)){
		$is_license = get_array_value($item,"has_license","0");
		if($is_license == '1'){
			$txt .= '<div class="mask-ipad"></div>';
		}
		if($product_type_cid != 'vdo')
		{
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb", get_array_value($item,"thumbnail_image","")).'?v='.$version.'" ></a>';
		}else{
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb").'?v='.$version.'" ></a>';
		}
	}else{
		$txt .=	'&nbsp;';
	}

	$txt .= '</div>';

	$txt .=	'</div>';

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