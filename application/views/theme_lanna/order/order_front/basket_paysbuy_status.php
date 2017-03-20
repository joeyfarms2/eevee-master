<script type="text/javascript">
$(document).ready(function() {
	setTimeout("check_order_status();",3000);
	<?=@$message?>
	<?=@$js_code?>
} );

function check_order_status(){
	var sid = Math.floor(Math.random()*10000000000);	  
	var full_url = base_url+"basket/confirm/ajax-update-status/"+sid;
	var order_main_cid = '<?=@$order_main_cid?>';
	jQuery.getJSON(full_url, ({order_main_cid:order_main_cid }), 
		function(data){
			jQuery("#result-msg-box").addClass(data.status);
			jQuery("#result-msg-box").html(data.message);
		}
	);
}
</script>
<?php 
?>
<div id="basketListItemsWrap">
	<div id="result-msg-box"><img src="<?=IMAGE_PATH?>icons/loader.gif" />&nbsp;&nbsp;กรุณารอสักครู่ ระบบกำลังอัพเดทข้อมูลการชำระเงิน กรุณาอย่ากดปุ่มใดๆ</div>
</div>