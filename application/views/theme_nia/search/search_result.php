<?php 
	$master_category = @$master_category;
	$master_product_main = @$master_product_main;
	$master_publisher_by_product_main = @$master_publisher_by_product_main;
	$this_product_main_name = @$this_product_main_name;
	$this_product_main_url = @$this_product_main_url;
	$this_category_name = @$this_category_name;
	$this_publisher_name = @$this_publisher_name;

	//$search_history_popular_result = @$search_history_popular_result;
	$search_history_popular_result = "";

	$resultList = @$resultList;
	$optional = @$optional;
	$sort_by = @$sort_by;
	$url_for_sort = @$url_for_sort;
	$search_clear = @$search_clear;

	// echo "<pre>";
	// print_r($resultList);
	// echo "</pre>";
?>	

<style>
.img-head-news
{
	width: inherit;
	height: auto;
}

.img-head-news img
{
	max-width: ;
	min-width: ;
	height: auto;
}

.text-center
{
	margin-bottom: 20px;
}

.latest-news-header
{
	text-indent: 2em;
}

.bg-gray
{
	background-color: #f6f6f6;
	padding: 10px 0px 10px 0px;
}
.bg-gray-box
{
	background-color: #f6f6f6;
	height: 150px;
	width: 405px;
	padding: 10px;
	/*line-height: 150px;
	vertical-align: middle;*/
}
.box-left
{
	margin-right: 0px;
}
.news-box
{
	position: relative;
	height: 130px;
	/*line-height: 130px;*/
	display: table-cell;
	vertical-align: middle;
}
.news-link
{
	/*width: 168px;*/
}
.news-box img
{
	/*position: absolute;
	max-width:168px;
	max-height: 130px;
	margin:0 auto;*/
	max-width:100%;
	max-height: 100%;
}

.news-box span
{
	line-height: 20px;
}

.interesting-news-head
{
	color:#f35607; 
	font-size:20px;
}
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

#keyword
{
	width: 100% !important;
}

.boxFocus
{
	padding: 0px !important; 
}

.a-left
{
	padding: 0px !important; 	
}

.search-title 
{
	border:none;
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

	.form-group
	{
		margin-bottom: 0px !important;
	}

	.form-group div
	{
		margin-bottom: 10px !important;
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

	.form-group
	{
		margin-bottom: 0px !important;
	}

	.form-group div
	{
		margin-bottom: 10px !important;
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

	.form-group
	{
		margin-bottom: 0px !important;
	}

	.form-group div
	{
		margin-bottom: 10px !important;
	}

}
</style>
<div class="row">
	<div class="col-sm-12 visible-md visible-sm visible-xs" style="z-index:99999;">
		<aside id="sidebar2">
			<div class="boxFocus ">
				<h3>Advance Search</h3>
				<div class="col-md-12 a-left">
					<form id="frm_search" name="frm_search" class="form-horizontal tasi-form" method="get">
						<input type="hidden" name="page_selected" id="page_selected" value="<?=@$page_selected?>" />
						<div class="form-group">
							<label class="control-label col-xs-12">Condition</label>
							<div class="col-xs-12">
								<?php $search_option = @$search_option?>
								<label class="radio-inline">
									<input type="radio" name="search_option" id="search_option_and" value="and" checked />And
								</label>
								<label class="radio-inline">
									<input type="radio" name="search_option" id="search_option_or" value="or" <?=($search_option == "or") ? "checked" : ""?> />Or
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-12">Search In</label>
							<div class="col-xs-12">
								<?php $search_type = @$search_type?>
								<select class="form-control" id="search_type" name="search_type" onchange="search_advance('1')">
									<option value="marc" <?=($search_type=="marc")?"selected":"";?>> Book/Multimedia</option>
									<option value="news" <?=($search_type=="news")?"selected":"";?>> News</option>
								</select>
							</div>
						</div>

						<?php if($search_type == "news"){ ?>
							<div class="form-group">
								<label class="control-label col-xs-12">Field</label>
								<div class="col-xs-12">
									<?php $search_in = @$search_in?>
									<select id="search_in" name="search_in" class="form-control" onchange="search_advance('1')">
										<option value="all" <?php if($search_in == "all"){ echo "selected"; } ?>>All</option>
										<option value="title" <?php if($search_in == "title"){ echo "selected"; } ?>>Title</option>
										<option value="description" <?php if($search_in == "description"){ echo "selected"; } ?>>Description</option>
										<!-- <option value="tag" < ?php if($search_in == "tag"){ echo "selected"; } ?>>Keyword</option>
										 -->
										 <option value="posted_by" <?php if($search_in == "posted_by"){ echo "selected"; } ?>>Post By</option>
									</select>
								</div>
							</div>
						<?php }else{ ?>
							<div class="form-group">
								<label class="control-label col-xs-12">Type</label>
								<div class="col-xs-12">
									<?php $search_in_product_main = @$search_in_product_main?>
									<select class="form-control" id="search_in_product_main" name="search_in_product_main" onchange="search_advance('1')">
										<option value="0">All</option>
										<?php if(is_var_array($master_product_main)){ ?>
										<?php foreach($master_product_main as $item){ ?>
											<option value="<?=get_array_value($item,"aid","N/A");?>" <?=($search_in_product_main==get_array_value($item,"aid","0"))?"selected":"";?>><?=get_array_value($item,"name","N/A");?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-xs-12">Field</label>
								<div class="col-xs-12">
									<?php $search_in = @$search_in?>
									<select id="search_in" name="search_in" class="form-control" onchange="search_advance('1')">
										<option value="all" <?php if($search_in == "all"){ echo "selected"; } ?>>All</option>
										<option value="title" <?php if($search_in == "title"){ echo "selected"; } ?>>Title</option>
										<option value="author" <?php if($search_in == "author"){ echo "selected"; } ?>>Author</option>
										<option value="isbn" <?php if($search_in == "isbn"){ echo "selected"; } ?>>ISBN</option>
										<option value="content" <?php if($search_in == "content"){ echo "selected"; } ?>>Description</option>
										<option value="publisher" <?php if($search_in == "publisher"){ echo "selected"; } ?>>Publisher</option>
										<option value="tag" <?php if($search_in == "tag"){ echo "selected"; } ?>>Keyword</option>
										<!-- <option value="call_number" <?php if($search_in == "call_number"){ echo "selected"; } ?>>Call Number</option> -->
										<option value="subject" <?php if($search_in == "subject"){ echo "selected"; } ?>>Subject</option>
									</select>
								</div>
							</div>

						<?php } ?>

						<div class="form-group">
							<label class="control-label col-xs-12">Keyword</label>
							<div class="col-xs-12">
								<input type="text" size="40" class="form-control" name="keyword" class="mykeyword" id="keyword" value="<?=@$keyword?>" onkeypress="isEnterGoTo(event,'search_advance(\'1\')')"  />
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-xs-12">Order By</label>
							<div class="col-xs-12">
								<?php $sort_by = @$sort_by?>
								<select id="sort_by" name="sort_by" class="form-control" onchange="search_advance('1')">
								<option value="match" <?php if($sort_by == "match"){ echo "selected"; } ?>>Best match</option>
								<option value="date_d" <?php if($sort_by == "date_d"){ echo "selected"; } ?>>Date (Newer)</option>
								<option value="date_a" <?php if($sort_by == "date_a"){ echo "selected"; } ?>>Date (Older)</option>
								<option value="name_a" <?php if($sort_by == "name_a"){ echo "selected"; } ?>>Name (A-Z)</option>
								<option value="name_d" <?php if($sort_by == "name_d"){ echo "selected"; } ?>>Name (Z-A)</option>
								</select>
							</div>
						</div>

						<!-- Button -->
						<div class="form-group">
							<div class="col-xs-12">
								<a class="btn btn-block btn-primary" onclick="search_advance('1');" />Search</a>
							</div>
						</div>
						<!-- End : Button -->
					</form>
				</div>
			</div>

			<!-- Show Popular Search -->
			<?php if(is_var_array($search_history_popular_result)){ ?>
			<div class="boxFocus mt30">
				<h3>Most Popular Search</h3>
					<?php 
						$i=0;
						foreach($search_history_popular_result as $item){ 
							$i++;
					?>
					<div class="col-md-12 a-left">
						<section class="boxContent">
							<h4><a href="<?=site_url('search/'.get_array_value($item,"word","-"))?>"><?=$i.'. '.get_array_value($item,"word","-");?></a></h4>
						</section>
					</div>
				<?php } ?>
			</div>
			<?php } ?>
			<!-- End : Popular Search -->

		</aside>
	</div>
	<!-- search result -->
	<div class="col-xs-12 col-md-9">
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
				<?php /*<section class="mt30 imgHover">
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
				</section>*/ ?>
				<div class="row">
					<?php foreach($resultList as $item){ ?>
						<div class="col-xs-6 col-sm-4">
							<?=draw_item($item);?>
						</div>
					<?php }?>
				</div>
				<?php } ?>
			<?php } ?>
			</div><!--container-->
		</section><!--end:feature-widget-->
	</div>
	<!-- end search result -->
	<div class="col-sm-3 hidden-md hidden-sm hidden-xs">
		<aside id="sidebar2" class="col-md-3 custom-right-box">
			<div class="boxFocus ">
				<h3>Advance Search</h3>
				<div class="col-md-12 a-left">
					<form id="frm_search" name="frm_search" class="form-horizontal tasi-form" method="get">
						<input type="hidden" name="page_selected" id="page_selected" value="<?=@$page_selected?>" />
						<div class="form-group">
							<label class="control-label col-sm-6 col-lg-4">Condition</label>
							<div class="col-sm-6 col-lg-8">
								<?php $search_option = @$search_option?>
								<label class="radio-inline">
									<input type="radio" name="search_option" id="search_option_and" value="and" checked />And
								</label>
								<label class="radio-inline">
									<input type="radio" name="search_option" id="search_option_or" value="or" <?=($search_option == "or") ? "checked" : ""?> />Or
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-6 col-lg-4">Search In</label>
							<div class="col-sm-6 col-lg-8">
								<?php $search_type = @$search_type?>
								<select class="form-control" id="search_type" name="search_type" onchange="search_advance('1')">
									<option value="marc" <?=($search_type=="marc")?"selected":"";?>> Book/Multimedia</option>
									<option value="news" <?=($search_type=="news")?"selected":"";?>> News</option>
								</select>
							</div>
						</div>

						<?php if($search_type == "news"){ ?>
							<div class="form-group">
								<label class="control-label col-sm-6 col-lg-4">Field</label>
								<div class="col-sm-6 col-lg-8">
									<?php $search_in = @$search_in?>
									<select id="search_in" name="search_in" class="form-control" onchange="search_advance('1')">
										<option value="all" <?php if($search_in == "all"){ echo "selected"; } ?>>All</option>
										<option value="title" <?php if($search_in == "title"){ echo "selected"; } ?>>Title</option>
										<option value="description" <?php if($search_in == "description"){ echo "selected"; } ?>>Description</option>
										<!-- <option value="tag" < ?php if($search_in == "tag"){ echo "selected"; } ?>>Keyword</option>
										 -->
										 <option value="posted_by" <?php if($search_in == "posted_by"){ echo "selected"; } ?>>Post By</option>
									</select>
								</div>
							</div>
						<?php }else{ ?>
							<div class="form-group">
								<label class="control-label col-sm-6 col-lg-4">Type</label>
								<div class="col-sm-6 col-lg-8">
									<?php $search_in_product_main = @$search_in_product_main?>
									<select class="form-control" id="search_in_product_main" name="search_in_product_main" onchange="search_advance('1')">
										<option value="0">All</option>
										<?php if(is_var_array($master_product_main)){ ?>
										<?php foreach($master_product_main as $item){ ?>
											<option value="<?=get_array_value($item,"aid","N/A");?>" <?=($search_in_product_main==get_array_value($item,"aid","0"))?"selected":"";?>><?=get_array_value($item,"name","N/A");?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-6 col-lg-4">Field</label>
								<div class="col-sm-6 col-lg-8">
									<?php $search_in = @$search_in?>
									<select id="search_in" name="search_in" class="form-control" onchange="search_advance('1')">
										<option value="all" <?php if($search_in == "all"){ echo "selected"; } ?>>All</option>
										<option value="title" <?php if($search_in == "title"){ echo "selected"; } ?>>Title</option>
										<option value="author" <?php if($search_in == "author"){ echo "selected"; } ?>>Author</option>
										<option value="isbn" <?php if($search_in == "isbn"){ echo "selected"; } ?>>ISBN</option>
										<option value="content" <?php if($search_in == "content"){ echo "selected"; } ?>>Description</option>
										<option value="publisher" <?php if($search_in == "publisher"){ echo "selected"; } ?>>Publisher</option>
										<option value="tag" <?php if($search_in == "tag"){ echo "selected"; } ?>>Keyword</option>
										<!-- <option value="call_number" <?php if($search_in == "call_number"){ echo "selected"; } ?>>Call Number</option> -->
										<option value="subject" <?php if($search_in == "subject"){ echo "selected"; } ?>>Subject</option>
									</select>
								</div>
							</div>

						<?php } ?>

						<div class="form-group">
							<label class="control-label col-sm-6 col-lg-4">Keyword</label>
							<div class="col-sm-6 col-lg-8">
								<input type="text" size="40" class="form-control" name="keyword" class="mykeyword" id="keyword-2" value="<?=@$keyword?>" onkeypress="isEnterGoTo(event,'search_advance(\'1\')')" />
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-sm-6 col-lg-4">Order By</label>
							<div class="col-sm-6 col-lg-8">
								<?php $sort_by = @$sort_by?>
								<select id="sort_by" name="sort_by" class="form-control" onchange="search_advance('1')">
								<option value="match" <?php if($sort_by == "match"){ echo "selected"; } ?>>Best match</option>
								<option value="date_d" <?php if($sort_by == "date_d"){ echo "selected"; } ?>>Date (Newer)</option>
								<option value="date_a" <?php if($sort_by == "date_a"){ echo "selected"; } ?>>Date (Older)</option>
								<option value="name_a" <?php if($sort_by == "name_a"){ echo "selected"; } ?>>Name (A-Z)</option>
								<option value="name_d" <?php if($sort_by == "name_d"){ echo "selected"; } ?>>Name (Z-A)</option>
								</select>
							</div>
						</div>

						<!-- Button -->
						<div class="form-group">
							<div class="col-xs-12">
								<a class="btn btn-block btn-primary" onclick="search_advance('1');" />Search</a>
							</div>
						</div>
						<!-- End : Button -->
					</form>
				</div>
			</div>

			<!-- Show Popular Search -->
			<?php if(is_var_array($search_history_popular_result)){ ?>
			<div class="boxFocus mt30">
				<h3>Most Popular Search</h3>
					<?php 
						$i=0;
						foreach($search_history_popular_result as $item){ 
							$i++;
					?>
					<div class="col-md-12 a-left">
						<section class="boxContent">
							<h4><a href="<?=site_url('search/'.get_array_value($item,"word","-"))?>"><?=$i.'. '.get_array_value($item,"word","-");?></a></h4>
						</section>
					</div>
				<?php } ?>
			</div>
			<?php } ?>
			<!-- End : Popular Search -->

		</aside>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		
		$("#keyword-2").keyup(function(){
			var txt = $("#keyword-2").val();
			$("#keyword").val(txt)
		});

		$("#keyword").keyup(function(){
			var txt = $("#keyword").val();
			$("#keyword-2").val(txt)
		});

		<?=@$message?>
		<?=@$js_code?>


		
	});

	

</script>
<?php /*
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

 */ ?>

 <?php 
// function draw_item($item){
// 	$txt = '';
// 	$txt .=	'<section class="boxContent">';
// 	if(is_var_array($item)){
// 		$txt .=	'<ul class=""><li><a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'" title="'.removeAllQuote(get_array_value($item,"title","N/A")).'" >'.get_array_value($item,"title_short","N/A") .'</a></li></ul>';
// 	}else{
// 		$txt .=	'&nbsp;';
// 	}
// 	$txt .=	'</section>';
// 	$txt .=	'<section class="imgWrapper">';
// 	if(is_var_array($item)){
// 		$is_license = get_array_value($item,"has_license","0");
// 		if($is_license == '1'){
// 			$txt .= '<div class="mask-ipad-search"></div>';
// 		}
// 		if(get_array_value($item,"product_type_cid","-") != "vdo"){
// 			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),"thumb").'" class="img-responsive"></a>';
// 		$txt .= '<div>'.get_array_value($item,"copy_type_minor_show","").'</div>';
// 		}else{
// 			if(get_array_value($item,"product_main_aid","0") == "5"){
// 				$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img alt="" src="'.get_image(get_array_value($item,"cover_image_thumb_path",""),get_array_value($item,"product_type_cid","")."-thumb").'" class="img-responsive"></a>';
// 				$txt .= '<div><i class="fa fa-video-camera"></i> Multimedia</div>';
// 			}
// 		}
// 	}else{
// 		$txt .=	'&nbsp;';
// 	}
// 	$txt .=	'</section>';
// 	return $txt;
// }
 function draw_item($item){
 	// echo "<pre>";
 	// print_r($item);
 	// echo "</pre>";
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
		if($product_type_cid != 'vdo')
		{
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), "thumb").'?v='.$version.'" ></a>';
		}else{
			$txt .=	'<a href="'.site_url(get_array_value($item,"product_type_cid","none").'-detail/'.get_array_value($item,"aid","")).'"><img src="'.get_image(get_array_value($item,"cover_image_thumb_path",""), "thumb").'?v='.$version.'" ></a>';
		}
	}else{
		$txt .=	'&nbsp;';
	}

	$txt .= '</div>';

	$txt .= '<div class="text-center">';
	if(get_array_value($item,"product_type_cid","-") != "vdo"){
		$txt .= get_array_value($item,"copy_type_minor_show","");
	}else{
		if(get_array_value($item,"product_main_aid","0") == "5"){
			$txt .= '<i class="fa fa-video-camera"></i> Multimediฟ';
		}
	}
	$txt .=	'</div>';	

	$txt .=	'</div>';

	return $txt;
}
?>