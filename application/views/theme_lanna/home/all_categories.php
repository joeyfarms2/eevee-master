<?php
	$result_by_product_main = @$result_by_product_main;
?>
<section id="message-box">
	<div class="container">
		<div id="result-msg-box" class="hidden" ></div>
	</div>
</section>

<section id="projects">
	<div class="container mt15 category-box">
		<?php 
		if(is_var_array($result_by_product_main)){
			foreach($result_by_product_main as $item){
				$result_list = get_array_value($item,"result_list","");
		?>	
			<!-- call to action -->
			<div class="mt10 mb20">
				<div class="row">
					<div class="col-md-12">
						<h2><?=get_array_value($item,"product_main_name","N/A")?></h2>
					</div>
				</div>
				<div class="row">
					<?php if(is_var_array($result_list)){ ?>
						<?php 
								foreach($result_list as $sub_item){ 
									$product_type_cid = get_array_value($item,"product_type_cid","");
									$product_main_url = get_array_value($item,"product_main_url","");
									$url = get_array_value($sub_item,"url","");
							?>
							<div class="col-xs-4 mb20">
								<a href="<?=site_url('list-'.$product_type_cid.'/category/'.$product_main_url.'/c-'.$url)?>"><?=get_array_value($sub_item,"name","N/A");?></a>
							</div>
						<?php }?>
					<?php } ?>
				</div>
			</div>
			<!-- call to action -->
		<?php } ?>
		<?php } ?>

	</div>
</section>
<script type="text/javascript">
	jQuery(document).ready(function($){
		<?=@$message?>
		<?=@$js_code?>
	});
</script>

