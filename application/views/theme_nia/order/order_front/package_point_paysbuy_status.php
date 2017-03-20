<script type="text/javascript">
$(document).ready(function() {
	setTimeout("check_order_status();",3000);
	<?=@$message?>
	<?=@$js_code?>
} );

function check_order_status(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"order/package-point/ajax-update-status/"+sid;
	var order_main_cid = '<?=@$order_main_cid?>';
	var result_full = '<?=@$result_full?>';
	jQuery.getJSON(full_url, ({order_main_cid:order_main_cid, result_full:result_full }), 
		function(data){
			show_result_box(data);
		}
	);
}
</script>
<?php 
$order_main_cid = @$order_main_cid;
?>

<div id="result-msg-box" class=" mt30 mb30"><img src="<?=IMAGE_PATH?>icons/loader.gif" />&nbsp;&nbsp;กรุณารอสักครู่ ระบบกำลังอัพเดทข้อมูลการชำระเงิน กรุณาอย่ากดปุ่มใดๆ</div></div>
<?php if(CONST_MODE == "2"){ ?>
<div class="develope-mode">This block just for develop mode only!! must be comment this form on UAT.
	<BR>
		Box 1 : Result : Code 00 : success ex. 00INV-xxxxxxxxx<BR>
		Box 1 : Result : Code 09 : fail ex. 00INV-xxxxxxxxx<BR>
		Box 1 : Result : Code 02 : on process ex. 00INV-xxxxxxxxx<BR>
		Box 2 : amt : Amount ex. 300, 600<BR>
		Box 3 : method : Code 01 :<BR>
	<BR>
	<form name="frm_back" id="frm_back" method="post" action="<?=site_url('paysbuy/save-point-back-from-paysbuy-back')?>">
	<input type="text" Name="result" value="00<?=$order_main_cid?>"/>
	<input Type="hidden" Name="apCode" value="000000"/>
	<input Type="text" Name="amt" value=""/>
	<input Type="hidden" Name="fee" value="0"/>
	<input Type="text" Name="method" value="01"/>
	&nbsp;&nbsp;
	<input type="submit" class="btn btn-sm btn-danger" value="Test pay fail"/>
	</form>

	</div>
<?php } ?>