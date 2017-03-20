<?php
	$result_list = @$resultList;
	$optional = @$optional;
	
	$show_option = @$show_option;
	$sort_by = @$sort_by;

	// print_r($resultList);
?>	
<script type="text/javascript" src="<?=JS_PATH?>shelf/my_bookshelf_vdo.js"></script>
<link rel="stylesheet" href="<?=CSS_PATH?><?=CONST_CODENAME?>/css/myshelf_vdo.css" type="text/css" media="all">

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
	margin-top: -8px;
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

.iconLinks i
{
	color: #d16b00;
}

.boxContent
{
	padding: 5px;
}
@media (min-width:901px) and (max-width:1024px)
{
	.head-image
	{
		position:absolute;
	    clip:rect(0,500px,25px,0);
	}

	.shelf-under-book
	{
		margin-top: -36px;
	}

	.shelf-under-book-big 
	{
		margin-top: -8px;
	}

	.vdoWrapper
	{
		width: 150px;
		height: 180px;	
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

	.imgWrapper
	{
		width: 130px;
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
		margin-top: -36px;
	}

	.shelf-under-book-big 
	{
		margin-top: -10px;
	}

	.vdoWrapper
	{
		width: 150px;
		height: 180px;	
	}
}
@media (min-width:480px) and (max-width:767px) 
{	
	.container
	{
		/*width: 450px;*/
		width: 100%;
	}

	.custom-home-box-seeall h3 
	{
		font-size: 16px;
	}
	
	.boxContent
	{
		padding: 5px;
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
		width: 70%;
		height: 100px;	
	}


}
@media (min-width:360px) and (max-width:479px)
{
	.container
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
		width: 150px;
		height: 90px;	
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
		width: 130px;
		height: 150px;
		
	}

	
}
</style>

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
	
			<div class="row hidden-xs">
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"0",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"1",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"2",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"3",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"4",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"5",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"6",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"7",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"8",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"9",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"10",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"11",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"12",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"13",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"14",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-sm-3">
					<?=draw_item(get_array_value($result_list,"15",""), $optional, $show_option, $sort_by);?>
				</div>
			</div>

			<div class="row visible-xs">
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"0",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"1",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"2",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"3",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"4",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"5",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"6",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"7",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"8",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"9",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"10",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"11",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"12",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"13",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"14",""), $optional, $show_option, $sort_by);?>
				</div>
				<div class="col-xs-6">
					<?=draw_item(get_array_value($result_list,"15",""), $optional, $show_option, $sort_by);?>
				</div>
			</div>


	<section id="my-bookshelf-box">
		<div class="container mt15 shelf-box">

			<?php /*<div class="row shelf-item">
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
			</div>*/ ?>
			<?=getPagination($optional);?>
		</div>
	</section>


<?php } ?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>

		$('.boxContent').hover(
	    		function () {

	    			var $this=$(this);

	    			var fromTop = 30;//($('.imgWrapper', $this).height()/2 - $('.iconLinks', $this).height()/2);
	    			$('.iconLinks', $this).css('margin-top',fromTop);

					$('.mask', this).css('width', $('.imgWrapper', this).width()+2); // +2 = fix for TF preview iframe bug

	    			if($('.pinBox').length){

	    				$('.mediaHover', $this).height($('.imgWrapper', $this).height() + 15);   
	    				$('.mask', this).css('height', $('.imgWrapper', this).height() + 15);


	    			}else{

	    				$('.mediaHover', $this).height($('.imgWrapper', $this).height());   
	    				$('.mask', this).css('height', $('.imgWrapper', this).height());

	    				if($this.hasClass('minimalBox')){
	    					$('.mask', this).css('left', '0'); 
	    					$('.mask', this).css('top', '0'); 
	    				}

	    			}

	    			$('.mask', this).css('margin-top', 0);

	    			$('.mask', this).stop(1).show().css('opacity',0).animate({opacity :1},200, function() {

	    				$('.iconLinks', $this).css('display', 'block');
	    				if(Modernizr.csstransitions) {
	    					$('.iconLinks a').addClass('animated');


	    					$('.iconLinks a', $this).removeClass('flipOutX'); 
	    					$('.iconLinks a', $this).addClass('bounceInDown'); 

	    				}else{

	    					$('.iconLinks', $this).stop(true, false).fadeIn('fast');
	    				}


	    			});



	    		},function () {

	    			var $this=$(this);


	    			$('.mask', this).stop(1).show().animate({marginTop: $('.imgWrapper', $this).height()},200, function() {

	    				if(Modernizr.csstransitions) {
	    					$('.iconLinks a', $this).removeClass('bounceInDown'); 
	    					$('.iconLinks a', $this).addClass('flipOutX'); 

	    				}else{
	    					$('.iconLinks', $this).stop(true, false).fadeOut('fast');
	    				}

	    			});

	    		});
	});
	
</script>

<?php 
/*function draw_item($item, $optional, $show_option, $sort_by){
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
		$txt .=	'</div>';
		$txt .=	'<div class="mediaHover" style="height: 210px;">';
		$txt .=	'<div class="mask" style="width: 272px; height: 210px; margin-top: 210px; display: block; opacity: 1;"></div>';
		$txt .=	'<div class="iconLinks" style="margin-top: 80px; display: block;">  ';
		if($is_license == "1"){

		}else{
		$txt .=	'<a class="animated flipOutX" title="Read" href="'.site_url(get_array_value($item,"product_type_cid","").'-detail/'.get_array_value($item,"parent_aid","")).'" target="_blank">';
		$txt .=	'<i class="button glyphicon glyphicon-play iconRounded iconLarge"></i>';
		$txt .=	'</a>&nbsp;&nbsp;';
		}
		$txt .=	'<a class="animated flipOutX" title="Remove" onclick="confirm_delete_shelf(\''. get_array_value($item,"product_type_aid","0") .'\',\''.get_array_value($item,"parent_aid","").'\',\''.get_array_value($optional,"page_selected","0").'\', \''.$show_option.'\', \''.$sort_by.'\')">';
		$txt .=	'<i class="button glyphicon glyphicon-trash iconRounded iconLarge"></i>';
		$txt .=	'</a> ';
		$txt .=	'</div>';
		$txt .=	'</div>';
		$txt .=	'</article>';
	}
	return $txt;
}*/

function draw_item($item, $optional, $show_option, $sort_by){
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
		$txt .=	'<a class="" href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'" title="'.removeAllQuote(get_array_value($item,"title","N/A")).'" >'.get_array_value($item,"title","N/A") .'</a>'; //title, title_short
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

		// if($product_type_cid != 'vdo')
		// {
		// 	$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb", get_array_value($item,"thumbnail_image","")).'?v='.$version.'" ></a>';
		// }else{
		// 	$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), $cover_class."thumb").'?v='.$version.'" ></a>';
		// }
		$txt .=	'<img alt="" src="'.get_array_value($item,"cover_image_thumb","").'?v='.$version.'" >';


		$txt .=	'<div class="mediaHover" style="height: 210px;">';
		$txt .=	'<div class="mask" style="width: 100%; height: auto; margin-top: 210px; display: block; opacity: 1;"></div>';
		$txt .=	'<div class="iconLinks" style="margin-top: 80px; display: block;">  ';
		if($is_license == "1"){

		}else{
		$txt .=	'<a class="animated flipOutX" title="Play" href="'.site_url(get_array_value($item,"product_type_cid","").'/show-product/'.get_array_value($item,"copy_aid","")).'" target="_blank">';
		$txt .=	'<i class="button glyphicon glyphicon-play iconRounded iconLarge"></i>';
		$txt .=	'</a>&nbsp;&nbsp;';
		}
		$txt .=	'<a class="animated flipOutX" title="Remove" onclick="confirm_delete_shelf(\''. get_array_value($item,"product_type_aid","0") .'\',\''.get_array_value($item,"parent_aid","").'\',\''.get_array_value($optional,"page_selected","0").'\', \''.$show_option.'\', \''.$sort_by.'\')">';
		$txt .=	'<i class="button glyphicon glyphicon-trash iconRounded iconLarge"></i>';
		$txt .=	'</a> ';
		$txt .=	'</div>';
		$txt .=	'</div>';

	}else{
		$txt .=	'&nbsp;';
	}

	$txt .= '</div>';

	$txt .=	'</div>';

	return $txt;
}
?>
