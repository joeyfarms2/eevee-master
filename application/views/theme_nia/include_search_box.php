<?php 
$master_product_main =  @$master_product_main;
?>
					<script type="text/javascript">
						jQuery(document).ready(function($){

						});
					</script>

				<div class="row">
					<section id="search-box" class="widget search clearfix">
						<form id="frm_search" name="frm_search" class="form-inline" role="form" method="get">
							<div class="">
								<button type="button" class="btn btn-sm search-btn" onclick="search_advance(1)">
									<i class="fa fa-search fa-lg"></i> 
								</button>
								<select class="form-control opt-search hide" id="search_in_product_main" name="search_in_product_main">
									<option value="all">All</option>
									<?php if(is_var_array($master_product_main)){ ?>
									<?php foreach($master_product_main as $item){ ?>
										<option value="<?=get_array_value($item,"aid","N/A");?>"><?=get_array_value($item,"name","N/A");?></option>
									<?php } ?>
									<?php } ?>
								</select>
								<select class="form-control opt-search" id="search_type" name="search_type">
									<option value="marc"> Book/Multimedia</option>
									<option value="news"> News</option>
								</select>
								
								<input type="text" class="form-control search-txt-box" placeholder="Keyword" value="" id="keyword" name="keyword" onkeypress="isEnterGoTo(event,'search_advance(1)')" />
							</div>
						</form>
					</section>
				</div>
