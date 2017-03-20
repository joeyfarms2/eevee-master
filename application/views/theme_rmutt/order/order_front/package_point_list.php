<?php
$master_package_point = @$master_package_point;
$payment_type = @$payment_type;
?>

<div id="result-msg-box" class="hidden" ></div>		

<form id="frm_point" name="frm_point" class="clearfix frm-class" method="POST" action="" novalidate="novalidate">
<section id="content" class="mt30 mb30">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-10" id="">	
				<p>Buy books with points for reading on Book Friend iPad app. You can buy points to save your money.
				Choose the point package you prefer.</p>

				<p>" ซื้อหนังสือเพื่ออ่านบน Book Friend iPad app ด้วยแต้ม ประหยัดกว่าการซื้อแบบปกติ เลือกแพคเกจแต้มได้ตามใจชอบได้ที่นี่ "</p>

				<p>ช่องทางการชำระเงิน<BR />
				มั่นใจในการชำระเงิน เพื่อซื้อหนังสือหรือแต้ม ให้คุณชำระได้หลายช่องทาง<BR />
				- เซเว่น อีเลฟเว่น (7-11) หรือ counter service (เงินสด)<BR />
				- Tesco Lotus (เงินสด)<BR />
				- จ่ายผ่านบัตรเครดิตธนาคารต่างๆ<BR />
				- Paysbuy account
				</p>
				
				<div class="col-xs-12 a-center mt20 mb20 package-point-header">
					<img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/package-point-header.png" />
				</div>

				<div class="row package-point-list">
					<?php if(is_var_array($master_package_point)){ ?>
						<?php foreach($master_package_point as $item){ ?>
							<div class="fleft"><img src="<?=get_array_value($item,"package_point_image","")?>" class="button" onclick="processRedirect('order/package-point/confirm/package-<?=get_array_value($item,"aid","0")?>')" /></div>
						<?php } ?>
					<?php } ?>
				</div>

				<div class="row a-center mt20 mb20 package-point-header">
					<div class="download-app">Download Bookdose app to your device.</div>
					<a href="#" target="_blank"><img class="mr30" src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/button-apple-store.png" /></a>
					<a href="#" target="_blank"><img src="<?=CSS_PATH?><?=CONST_CODENAME?>/images/background/button-play-store.png" /></a>
				</div>
			</div>
			<div class="col-xs-12 col-md-2 om-summary" id="">
				<div>
					<div class="pt10 pb20">
							<h4>Payment method</h4>
							<select class="form-control" id="point_payment_type" name="point_payment_type" onchange="change_payment_type(this.value, 'point')">
								<option value="paysbuy" <?=($payment_type == "paysbuy")?"selected":"";?>>Pay by Paysbuy</option>
								<option value="paypal" <?=($payment_type == "paypal")?"selected":"";?>>Pay by PayPal</option>
							</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
</form>
<script type="text/javascript">
$(document).ready(function() {
	<?=@$message?>
	<?=@$js_code?>
} );
</script>