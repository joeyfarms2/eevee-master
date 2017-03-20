					<aside class="widget shadow-box clearfix">
						<div class="inner-box">
							<h5 class="widget-title"><span>ตัวเลือกการแสดงผล</span> </h5>
							<form id="frm_search" name="frm_search" class="wpcf7-form" method="post">
								<input type="hidden" name="page_selected" id="page_selected" value="<?=@$page_selected?>" />
								<p class="">
									การแสดง<BR />
									<?php $show_option = @$show_option?>
									<input type="radio" id="show_option_shelf" name="show_option" value="shelf" onclick="show_bookshelf()" checked />
									<label for="show_option_shelf">Shelf</label>
									<input type="radio" id="show_option_list" name="show_option" value="list" onclick="show_bookshelf()" <?=($show_option == "list") ? "checked" : ""?> />
									<label for="show_option_list">List</label>
								</p>
								<p class="">
									เรียงลำดับตาม<BR />
									<?php $sort_by = @$sort_by?>
									<select id="sort_by" name="sort_by" class="wpcf7-select" onchange="show_bookshelf()">
										<option value="date_d" <?php if($sort_by == "date_d"){ echo "selected"; } ?>>วันที่ (ใหม่ ไป เก่า)</option>
										<option value="date_a" <?php if($sort_by == "date_a"){ echo "selected"; } ?>>วันที่ (เก่า ไป ใหม่)</option>
										<option value="name_a" <?php if($sort_by == "name_a"){ echo "selected"; } ?>>ชื่อ (ก-ฮ)</option>
										<option value="name_d" <?php if($sort_by == "name_d"){ echo "selected"; } ?>>ชื่อ (ฮ-ก)</option>
									</select>
								</p>
							</form>
						</div><!--inner-box-->
                    </aside><!--end:widget-->
					
