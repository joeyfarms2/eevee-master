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
?>

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
						<input type="text" size="40" class="form-control" name="keyword" id="keyword" value="<?=@$keyword?>" onkeypress="isEnterGoTo(event,'search_advance(\'1\')')" >
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