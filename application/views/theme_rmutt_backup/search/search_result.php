<?php
	$resultList = @$resultList;
	$optional = @$optional;
	$sort_by = @$sort_by;
	$url_for_sort = @$url_for_sort;
	$search_clear = @$search_clear;
?>	
<script type="text/javascript">
	jQuery(document).ready(function($){
	
		<?=@$message?>
		<?=@$js_code?>
	});
</script>

<section id="projects">
	<div class="container mt15 book-box">
	<?php if($search_clear == "clear"){ ?>
		<header class="entry-header">
			<h4 class="feature-title">Advance search</h4>
		</header>
	<?php }else{ ?>
		<header>
			<h4><span class="search-title">Results the word <span class="txt-red">"<?=@$keyword?>"</span> : <?=get_array_value($optional,"total_record","0")?> result(s) found.</span></h4>
		</header>
		<?php if(is_var_array($resultList)){ ?>
		<section class="mt30 imgHover">
			<div class="row search-item">
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
	<?php } ?>
	</div><!--container-->
</section><!--end:feature-widget-->

<?php 
function draw_item($item){
	$txt = '';
	$txt .=	'<section class="boxContent">';
	if(is_var_array($item)){
		$txt .=	'<ul class=""><li><a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'" title="'.removeAllQuote(get_array_value($item,"title","N/A")).'" >'.get_array_value($item,"title_short","N/A") .'</a></li></ul>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	$txt .=	'<section class="imgWrapper">';
	if(is_var_array($item)){
		$is_license = get_array_value($item,"has_license","0");
		if($is_license == '1'){
			$txt .= '<div class="mask-ipad-search"></div>';
		}
		$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),"thumb",get_array_value($item,"thumbnail_image","")).'" class="img-responsive"></a>';
		$txt .= '<div>'.get_array_value($item,"copy_type_minor_show","").'</div>';
	}else{
		$txt .=	'&nbsp;';
	}
	$txt .=	'</section>';
	return $txt;
}
?>
