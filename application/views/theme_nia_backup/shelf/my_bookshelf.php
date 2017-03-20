<?php
	$resultList = @$resultList;
	$optional = @$optional;
	
	$show_option = @$show_option;
	$sort_by = @$sort_by;

	//print_r($resultList);
?>	
<section id="message-box">
	<div class="container">
		<div id="result-msg-box" class="hidden" ></div>
	</div>
</section>

<?php if($show_option == "list"){ ?>
	<section id="my-bookshelf-list">
		<div class="container mt30 list-box">

		<?php if(is_var_array($resultList)){ ?>
		<?php foreach($resultList as $item){ ?>
				<div class="row list-item">
					<article class="clearfix">
						<div class="item">
							<a class="button" onclick="confirm_delete_shelf('<?=get_array_value($item,"product_type_aid","0")?>','<?=get_array_value($item,"copy_aid","")?>','<?=get_array_value($optional,"page_selected","0")?>','<?=$show_option?>','<?=$sort_by?>')"><img src="<?=IMAGE_PATH?>icons/shelf-delete.png" title="Remove" /></a>
						</div>
						<div class="item">
							<?php $is_license = get_array_value($item,"is_license","0"); ?>
							<?php if($is_license == '1'){ ?>
							<div class="mask-ipad"></div>
							<?php } ?>
							<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'/show-product/'.get_array_value($item,"copy_aid",""))?>" target="_blank"><img alt="" src="<?=get_array_value($item,"cover_image_small","")?>" class="img-responsive" style=""></a>
						</div>
						<div class="item">
							<div class="i-detail i-title">
								<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'/show-product/'.get_array_value($item,"copy_aid",""))?>" target="_blank"><?=get_array_value($item,"title","")?></a>
							</div>
							<div class="i-detail"><span class="i-header">Author :</span> <?=get_array_value($item,"parent_author","N/A");?></div>
							<div class="i-detail"><span class="i-header">Publishing Date :</span> <?=get_array_value($item,"parent_publish_date_txt","N/A");?></div>
							<div class="i-detail"><span class="i-desc"></span></div>

					</div>
					</article>
				</div>
			<?php } ?>		

		<?php }else{ ?>
			<div class="row list-item">
				<h3 class="empty">Shelf is empty.</h3>
			</div>
		<?php } ?>
		</div>
	</section>
<?php }else{ ?>

	<section id="my-bookshelf-box">
		<div class="container mt15 shelf-box">

			<div class="row shelf-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?=draw_item(get_array_value($resultList,"0",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"1",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"2",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"3",""), $optional, $show_option, $sort_by);?>
					</article>
				</section>
			</div>

			<div class="row shelf-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?=draw_item(get_array_value($resultList,"4",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"5",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"6",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"7",""), $optional, $show_option, $sort_by);?>
					</article>
				</section>
			</div>

			<div class="row shelf-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?=draw_item(get_array_value($resultList,"8",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"9",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"10",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"11",""), $optional, $show_option, $sort_by);?>
					</article>
				</section>
			</div>

			<div class="row shelf-item">
				<section class=" pb30 imgHover">
					<article class="item text-center first">
						<?=draw_item(get_array_value($resultList,"12",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"13",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"14",""), $optional, $show_option, $sort_by);?>
					</article>
					<article class="item text-center">
						<?=draw_item(get_array_value($resultList,"15",""), $optional, $show_option, $sort_by);?>
					</article>
				</section>
			</div>
			<?=getPagination($optional);?>
		</div>
	</section>


<?php } ?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
	
</script>

<?php 
function draw_item($item, $optional, $show_option, $sort_by){
	$is_license = get_array_value($item,"is_license","0");
	$txt = '';
	if(is_var_array($item)){
		$txt .=	'<article class="boxContent">';
		$txt .=	'<div class="imgWrapper">';
		$txt .=	'<div class="imgTitle">';
		$txt .=	'<ul class=""><li><i class="fa fa-circle"></i>&nbsp;&nbsp;&nbsp;'.get_array_value($item,"title","N/A") .'</li></ul>';
		$txt .=	'</div>';

		$class = "";
		if($is_license == "1"){ 
		$txt .=	'<div class="mask-ipad"></div>';
		}
		$txt .=	'<img alt="" src="'.get_array_value($item,"cover_image_thumb","").'" class="img-responsive" style="">';
		// $txt .=	'<img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),"thumb",get_array_value($item,"thumbnail_image","")).'" class="img-responsive" style="">';
		$txt .=	'</div>';
		$txt .=	'<div class="mediaHover" style="height: 210px;">';
		$txt .=	'<div class="mask" style="width: 272px; height: 210px; margin-top: 210px; display: block; opacity: 1;"></div>';
		$txt .=	'<div class="iconLinks" style="margin-top: 80px; display: block;">  ';
		if($is_license == "1"){

		}else{
		$txt .=	'<a class="animated flipOutX" title="Read" href="'.site_url(get_array_value($item,"product_type_cid","").'/show-product/'.get_array_value($item,"copy_aid","")).'" target="_blank">';
		$txt .=	'<i class="button glyphicon glyphicon-book iconRounded iconLarge"></i>';
		$txt .=	'</a>&nbsp;&nbsp;';
		}
		$txt .=	'<a class="animated flipOutX" title="Remove" onclick="confirm_delete_shelf(\''. get_array_value($item,"product_type_aid","0") .'\',\''.get_array_value($item,"copy_aid","").'\',\''.get_array_value($optional,"page_selected","0").'\', \''.$show_option.'\', \''.$sort_by.'\')">';
		$txt .=	'<i class="button glyphicon glyphicon-trash iconRounded iconLarge"></i>';
		$txt .=	'</a> ';
		$txt .=	'</div>';
		$txt .=	'</div>';
		$txt .=	'</article>';
	}
	return $txt;
}
?>
