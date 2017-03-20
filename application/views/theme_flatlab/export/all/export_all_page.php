<script type="text/javascript" src="<?=JS_PATH?>report/export_all/export_all.js"></script>

<form id="frm_report" name="frm_report" method="POST" action="" class="form-horizontal tasi-form">

	<div class="row">
		<div class="col-xs-12 m-bot15">
			<a onclick="exportToExcel()" class="btn btn-info">
				<i class="fa fa-save"></i> Export to Excel
			</a>
		</div>
	</div>

	<div id="result-msg-box"></div>

	
</form>
<script type="text/javascript">
$(document).ready(function() {
		
	<?=@$message?>
	<?=@$js_code?>
	
} );
</script>
