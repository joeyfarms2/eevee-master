<?php 
$command = @$command;
$item_detail = @$item_detail;
?>


<script type="text/javascript" src="<?=JS_PATH?>holiday/holiday.js"></script>
<form id="frm_holiday" name="frm_holiday" method="POST" action="<?=site_url('admin/holiday/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />
	
	<div id="result-msg-box"></div>

	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
			</section>
		</div>
	</div>			
			

	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="from_date">From Date</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="from_date" name="from_date" value="<?=get_array_value($item_detail, "from_date","")?>" />
							</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="to_date">To Date</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="to_date" name="to_date" value="<?=get_array_value($item_detail, "to_date","")?>" />
							</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="remark">Remark</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="remark" name="remark" value="<?=get_array_value($item_detail, "remark","")?>" />
							</div>
					</div>


					<section class="panel">
						<!-- Button -->
						<div class="panel-body">
							<a class="btn btn-primary" onclick="processSubmitOption('frm_holiday', '0');" />Save & Close</a>
							<a class="btn btn-default" onclick="processRedirect('admin/holiday');" />Cancel</a>
						</div>
						<!-- End : Button -->
					</section>
				</div>
			</section>
		</div>
	</div>
</form>

<script type="text/javascript">
	jQuery(document).ready(function($){
	
		$("#from_date, #to_date").datepicker({
			// changeMonth: true,
			// changeYear: true,
			// dateFormat: "dd/mm/yy",
			// dateISO:"true"

			format: "yyyy-mm-dd",
			todayBtn: true,
			todayHighlight: true,
			autoclose: true
		});
		
		
		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>