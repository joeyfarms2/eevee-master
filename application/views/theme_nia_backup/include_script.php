	<!-- third party plugins  -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/easing/jquery.easing.1.3.js"></script>
	<!-- carousel -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/owl.carousel/owl-carousel/owl.carousel.min.js"></script>
	<!-- pop up -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/magnific-popup/jquery.magnific-popup.min.js"></script>
	<!-- flex slider -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/flexslider/jquery.flexslider-min.js"></script>
	<!-- isotope -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/isotope/jquery.isotope.min.js"></script>
	<!-- form -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/neko-contact-ajax-plugin/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/neko-contact-ajax-plugin/js/jquery.validate.min.js"></script>
	<!-- parallax -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/parallax/js/jquery.stellar.min.js"></script>
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/parallax/js/jquery.localscroll-1.2.7-min.js"></script>
	<!-- camera slider -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/camera/camera.min.js"></script>
	<script src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/layerslider/layerslider/js/layerslider.transitions.js" type="text/javascript"></script>
	<script src="<?=CSS_PATH?><?=CONST_CODENAME?>/js-plugin/layerslider/layerslider/js/layerslider.kreaturamedia.jquery.js" type="text/javascript"></script>

	<!-- Custom  -->
	<script type="text/javascript" src="<?=CSS_PATH?><?=CONST_CODENAME?>/js/custom.js"></script>

<!-- ui-dialog for alert or confirm -->
<div class="modal fade" id="dialog_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-header"><!-- Title goes here...--></h4>
			</div>
			<div class="modal-body" id="modal-msg">
			<!-- Body goes here...-->
			</div>
			<div class="modal-footer" id="modal-button">
			<!-- Button goes here...-->
			</div>
		</div>
	</div>
</div>

<!-- ui-dialog for choose product to cart -->
<div class="modal fade order-modal" id="choose_product_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-header">
					<i class="fa fa-book"></i>
					Choose option
				</h4>
			</div>
			<div class="modal-body">
			<!-- Body goes here...-->
				<form class="form-horizontal" role="form">
					<div class="form-group">
						<div class="col-sm-12 col-md-3" id="cp_cover_image"></div>
						<div class="col-sm-12 col-md-9">
							<h3 id="cp_title">Title</h3>
							<div id="cp_digital" class="om om-digital">
								<h4>Digital edition</h4>
							</div>
							<div id="cp_paper" class="om om-paper">
								<h4>Paper edition</h4>
							</div>
							<div class="om-button">
								<h4 class="price">Price : <strong id="cp_price">-</strong></h4>
								<a id="om_button_add" class="btn btn-lg btn-primary" onclick="add_to_basket('1')" disabled>
								<i class="fa fa-shopping-cart"></i>
								   Add to shopping cart
								</a>&nbsp;
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- ui-dialog show cart detail -->
<div class="modal fade order-modal" id="basket_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-header">
					<i class="fa fa-shopping-cart"></i>
					Shopping cart
				</h4>
			</div>
			<div class="modal-body">
			<!-- Body goes here...-->
				<form class="form-horizontal" role="form">
					<div class="form-group">
						<div id="b_list_zone" class="col-xs-12 col-sm-8 hide">
							<div id="b_item_list"></div>
							<div class="col-xs-12 om-button fleft hide" id="b_button_zone">
								<a id="om_button_manage" class="btn btn-small btn-warning mb10" href="<?=site_url('my-cart')?>">
								
								   Manage cart
								</a>&nbsp;
								<a id="om_button_continue" class="btn btn-small btn-info mb10" data-dismiss="modal" aria-hidden="true">
								
								   Continue shopping
								</a>&nbsp;
							</div>
						</div>

						<div id="b_summary_zone" class="col-xs-12 col-sm-4 om-summary a-center fleft hide">
							<div>
								<div class="">
										<div>Total unit : <span id="b_unit_total">-</span></div>
										<h4 class="mt10">Grand Total : <h4>
										<h3 class=""><strong id="b_price">-</strong></h3>

										<h4>Payment method</h4>
										<select class="form-control" id="b_payment_type" name="b_payment_type" onchange="change_payment_type(this.value, 'modal')">
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
				</form>
			</div>
		</div>
	</div>
</div>

<!--<script> (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-55847637-1', 'auto'); ga('send', 'pageview');
</script>-->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-89457097-1', 'auto');
  ga('send', 'pageview');

</script>