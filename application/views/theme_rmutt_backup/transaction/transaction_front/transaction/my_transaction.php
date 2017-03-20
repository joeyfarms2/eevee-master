<?php
	$resultList = @$resultList;
	$optional = @$optional;
	
	$show_option = @$show_option;
	$sort_by = @$sort_by;

	// print_r($resultList);
?>	
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
