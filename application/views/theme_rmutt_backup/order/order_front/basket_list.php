<div id="result-msg-box" class="hidden" ></div>		

<form id="frm_basket" name="frm_basket" class="clearfix frm-class" method="POST" action="" novalidate="novalidate">
<section id="content" class="mt30 mb30">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-9" id="b_page_item_list">
				<div class="row basket-item"></div>
			</div>
			<div class="col-xs-12 col-md-3 om-summary" id="b_page_summary_zone">
				<div>
					<div class="">
							<div>Total unit : <span id="b_page_unit_total">-</span></div>
							<h4 class="mt10">Grand Total : <h4>
							<h3 class=""><strong id="b_page_price">-</strong></h3>

							<h4>Choose payment method</h4>
							<select class="form-control" id="b_page_payment_type" name="b_page_payment_type" onchange="change_payment_type(this.value, 'page')">
								<option value="point">Pay by Point</option>
								<option value="paysbuy">Pay by Paysbuy</option>
								<option value="paypal">Pay by PayPal</option>
							</select>
					</div>
				</div>
				<div class="w100p mt20 mb10 om-button fleft">
					<a href="<?=site_url('basket/confirm')?>" id="om_button_checkout" class="btn btn-block btn-lg btn-danger">
					<i class="fa fa-check-square-o"></i>
					   Check out
					</a>&nbsp;
				</div>
			</div>

		</div>
	</div>

</section>
</form>
<script type="text/javascript">
$(document).ready(function() {
	show_basket_list();
	<?=@$message?>
	<?=@$js_code?>
} );
</script>