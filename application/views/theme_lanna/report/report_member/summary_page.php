<script type="text/javascript">
$(document).ready(function() {
	jQuery("#created_date_from").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd",
		dateISO:"true"
	});
	jQuery("#created_date_to").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd",
		dateISO:"true"
	});
		
		var validator = jQuery("#frm_report").validate({
			rules: {
				created_date_from:{
					required: true
				},
				created_date_to:{
					required: true,
					greaterThanOrEqual: "#created_date_from"
				}
			},
			messages: {
				created_date_from: {
					required: "กรุณาระบุวันที่",
				},
				created_date_to: {
					required: "กรุณาระบุวันที่",
					greaterThanOrEqual: "วันที่ต้องมากกว่าวันเริ่มต้น"
				}
			}
		});
	
	showSearchResult();
	
	<?=@$message?>
	<?=@$js_code?>
	
} );
</script>

<!--  Main Stylesheet -->
<link rel="stylesheet" type="text/css" href="<?=CSS_PATH?>admin/core/table.css" media="screen" />

<link href="<?=CSS_PATH?>report.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=JS_PATH?>report/report_member/summary.js"></script>
<?php 
$data_search = @$data_search;
$search_in = get_array_value($data_search,"search_in","");
$search_status = get_array_value($data_search,"search_status","");
$page_selected = get_array_value($data_search,"page_selected","");
$search_order_by = get_array_value($data_search,"search_order_by","");

?>

                    <section class="shadow-box clearfix">
						<div class="inner-box">
							<section class="element-box clearfix">
								<h3 class="element-title">Purchase History</h3>
								
								<form id="frm_report" name="frm_report" class="clearfix frm-class" method="POST" data-role="validator" novalidate="novalidate">  
									<input type="hidden" id="aid_selected" name="aid_selected" />
									<input type="hidden" id="page_selected" name="page_selected" value="<?=$page_selected?>" />
									<input type="hidden" id="search_order_by" name="search_order_by" value="<?=$search_order_by?>" />
									<div class="comment-left">
										<p class="input-block fleft">
											<label class="required" for="created_date_from">Report from</label>
											<input type="text" id="created_date_from" name="created_date_from" placeholder="" onchange="showSearchResult();" class="k-textbox required dateISO" value="<?=get_array_value($data_search,"created_date_from","")?>" />
										</p>
										<p class="input-block fleft">                                                
											<label class="required" for="created_date_to">Report to</label>
											<input type="text" id="created_date_to" name="created_date_to" placeholder="" onchange="showSearchResult();" class="k-textbox required dateISO" value="<?=get_array_value($data_search,"created_date_to","")?>" />
										</p>
									</div>
									<div class="clear"></div>
									<p class="frm-button">         
										<a class="small-button button" onClick="processSubmit('frm_login')">Show report</a> 
									</p>                        
								</form>
							</section>
							
							<section class="element-box clearfix">
								<div id="result-msg-box" class="hidden" ></div>
								<div class="dataTables_wrapper" id="tbldata_wrapper"></div>
							</section>
							
						</div><!--inner-box-->
                    </section><!--end:shadow-box-->