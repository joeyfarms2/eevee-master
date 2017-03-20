<script type="text/javascript">
$(document).ready(function() {
	<?=@$message?>
	<?=@$js_code?>
	// jQuery("#frm_basket").submit();
} );
</script>
<?php 
$order_result = @$order_result;
?>
<div id="basketListItemsWrap">
<form name="frm_basket" id="frm_basket" method="post" action="<?=CONST_URL_PAYSBUY_PAYNOW?>">
<input type="Hidden" Name="psb" value="psb"/>
<input Type="Hidden" Name="biz" value="<?=CONST_EMAIL_FOR_PAYSBUY?>"/>
<input Type="Hidden" Name="inv" value="<?=get_array_value($order_result,"cid","-")?>"/>
<input Type="Hidden" Name="itm" value="Digital book from Bookdose.com"/>
<input Type="Hidden" Name="amt" value="<?=get_array_value($order_result,"actual_grand_total","0")?>"/>
<input Type="Hidden" Name="currencyCode" value="764"/>
<input Type="Hidden" Name="postURL" value="<?=site_url('paysbuy/save-basket-back-from-paysbuy-front')?>"/>
<input Type="Hidden" Name="reqURL" value="<?=site_url('paysbuy/save-basket-back-from-paysbuy-back')?>"/>
<input Type="Hidden" Name="opt_name" value="<?=get_array_value($order_result,"buyer_name","")?>"/>
<input Type="Hidden" Name="opt_email" value="<?=get_array_value($order_result,"buyer_email","")?>"/>
<input Type="Hidden" Name="opt_mobile" value="<?=get_array_value($order_result,"buyer_contact","")?>"/>
<input Type="Hidden" Name="opt_address" value="<?=get_array_value($order_result,"buyer_address","")?>"/>
<div id="result-msg-box" class=" mt30 mb30"><img src="<?=IMAGE_PATH?>icons/loader.gif" />&nbsp;&nbsp;กรุณารอสักครู่ ระบบกำลังนำท่านไปยัง&nbsp;&nbsp;<img src="<?=IMAGE_PATH?>payment/paysbuy-m.gif" /></div></div>
</form>
<?php /*
<BR><BR>
<form name="frm_back" id="frm_back" method="post" action="<?=site_url('paysbuy/save-basket-back-from-paysbuy-front')?>">
<input type="text" Name="result" value="00<?=get_array_value($order_result,"cid","-")?>"/>
<input Type="text" Name="apCode" value="000000"/>
<input Type="text" Name="amt" value="<?=get_array_value($order_result,"actual_grand_total","0")?>"/>
<input Type="text" Name="fee" value="0"/>
<input Type="text" Name="method" value="01"/>
<input type="submit" class="btn big" value="จ่ายเงินสำเร็จ!!"/></p>
</form>
*/?>
</div>