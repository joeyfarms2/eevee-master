<?php 
	$master_category = @$master_category;
	$master_product_main = @$master_product_main;
	$master_publisher_by_product_main = @$master_publisher_by_product_main;
	$this_product_main_name = @$this_product_main_name;
	$this_product_main_url = @$this_product_main_url;
	$this_category_name = @$this_category_name;
	$this_publisher_name = @$this_publisher_name;
?>
					<?php include_once('include_search_box.php'); ?>
					
					<?php if(is_var_array($master_product_main)){ ?>
                	<aside class="widget shadow-box clearfix">
						<div class="inner-box">
							<h5 class="widget-title"><span>เมนูหลัก</span></h5>
							<div class="entry-box">                            
								<ul class="full-expand older-post">
									<?php 
										foreach($master_product_main as $item){ 
											$selected = "";
											if($this_product_main_name == get_array_value($item,"name","-")){
												$selected = " class='selected' ";
											}
									?>
									<li>
										<article class="clearfix">
										<?php
											echo '<a href="'.site_url('category/'.get_array_value($item,"url","-")).'" '.$selected.'>'.get_array_value($item,"name","-").'</a><BR>';
										?>
										</article>
									</li>
									<?php } ?>									
								</ul><!--end:older-post-->
								<div class="clear"></div>
							</div><!--end:entry-box-->
						</div><!--inner-box-->
                    </aside><!--end:widget-->
					<?php } ?>

					<?php if(is_var_array($master_category)){ ?>
                	<aside class="widget shadow-box clearfix">
						<div class="inner-box">
							<h5 class="widget-title"><span>หมวดหมู่</span></h5>
							<div class="entry-box">                            
								<ul class="full-expand older-post">
									<?php 
										foreach($master_category as $item){ 
											$selected = "";
											if($this_category_name == get_array_value($item,"name","-")){
												$selected = " class='selected' ";
											}
									?>
									<li>
										<article class="clearfix">
										<?php
											echo '<a href="'.site_url('category/'.$this_product_main_url.'/c-'.get_array_value($item,"name","-")).'" '.$selected.'>'.get_array_value($item,"name","-");
											if(get_array_value($item,"total","0") > 0) echo ' ('.get_array_value($item,"total","0").')';
											echo '</a>';
										?>
										</article>
									</li>
									<?php } ?>									
								</ul><!--end:older-post-->
								<div class="clear"></div>
							</div><!--end:entry-box-->
						</div><!--inner-box-->
                    </aside><!--end:widget-->
					<?php } ?>
