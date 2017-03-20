<?php
	$resultList = @$resultList;
	$optional = @$optional;
	
	$show_option = @$show_option;
	$sort_by = @$sort_by;

	// print_r($resultList);
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
}
@media (min-width:480px) and (max-width:767px) 
{	
	.container
	{
		width: 450px;
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
<section id="message-box">
	<div class="container">
		<div id="result-msg-box" class="hidden" ></div>
	</div>
</section>

	<section id="my-bookshelf-list">
		<div class="container mt30 list-box">

		<?php if(is_var_array($resultList)){ ?>
		<?php foreach($resultList as $item){ ?>
				<div class="row list-item">
					<article class="clearfix">
						<div class="item">
							<?php $is_license = get_array_value($item,"is_license","0"); ?>
							<?php if($is_license == '1'){ ?>
							<div class="mask-ipad"></div>
							<?php } ?>
							<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"parent_aid",""))?>"><img alt="" src="<?=get_array_value($item,"cover_image_small","")?>" class="img-responsive" style=""></a>
						</div>
						<div class="item">
							<div class="i-detail i-title">
								<a href="<?=site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"parent_aid",""))?>"><?=get_array_value($item,"title","")?></a>
							</div>
							<div class="i-detail"><span class="i-header">Author :</span> <?=get_array_value($item,"parent_author","N/A");?></div>
							<div class="i-detail"><span class="i-header">Due Date :</span> <?=get_array_value($item,"due_date_txt","N/A");?></div>
							<?php 
								$due_date = get_array_value($item,"due_date","");
								$return_status = get_array_value($item,"return_status","");
								$diff = get_diff_date($due_date, date('Y-m-d'));
								$date_left = '';
								if($return_status == "1"){
									$date_left = '<span class=textSub>Returned</span>';
								}else{
									if($diff <= 0){
										$date_left = '<span class="textRed">Overdue</span>';
									}else if($diff == '1'){
										$date_left = '<span class=textStart>'.$diff.' day left.</span>';
									}else{
										$date_left = '<span class=textStart>'.$diff.' day lefts.</span>';
									}
								}
							?>
							<div class="i-detail"><span class="i-header">Status :</span> <?=$date_left?></div>
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

<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
	
</script>
