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
	<div class="container mt15">
	<?php if($search_clear == "clear"){ ?>
		<header class="entry-header">
			<h4 class="feature-title">Advanced search</h4>
		</header>
	<?php }else{ ?>
		<header>
			<h5><span class="search-title">Results the word <span class="txt-red">"<?=@$keyword?>"</span> : <?=get_array_value($optional,"total_record","0")?> result(s) found.</span></h5>
		</header>
		<?php if(is_var_array($resultList)){ ?>
		<section class="mt30">
			<div class="row search-item">
			<?php foreach($resultList as $item){ 
				// print_r($item);
			?>
				<div class="box-generic box-news">
					<div class="news-content clearfix">
						<a href="<?=site_url('news/detail/'.get_array_value($item,"aid","0"))?>"><h3 class="txt-blue"><?=get_array_value($item,"title","N/A")?></h3></a>
						<p><?=get_array_value($item,"short_description","")?></p>
					</div>
				</div>
			<?php } ?>									
			</div>
			<?=getPagination($optional);?>
		</section>
		<?php } ?>
	<?php } ?>
	</div><!--container-->
</section><!--end:feature-widget-->
