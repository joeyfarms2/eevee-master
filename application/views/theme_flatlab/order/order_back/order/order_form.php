<?php 
$command = @$command;
$order_main = @$item_detail;
$order_detail = @$order_detail;
$package_point_detail = @$package_point_detail;

?>

<script type="text/javascript" src="<?=JS_PATH?><?=folderName?>/order.js"></script>
<form id="frm_order" name="frm_order" method="POST" action="<?=site_url('admin/order/save')?>" class="cmxform form-horizontal tasi-form" enctype="multipart/form-data">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($order_main,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />

	<div id="result-msg-box"></div>
	
	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>

				<div class="panel-body">

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="status">Payment status</label>
						<div class="col-md-12 col-lg-8">
							<label class="control-label status status-<?=get_array_value($order_main,"status","none")?>">
								<?=get_array_value($order_main,"status_txt","-")?>
							</label>
							<?php $status =  get_array_value($order_main,"status",""); ?>
							<?php /*
							<label class="radio-inline">
								<input type="radio" name="status" id="status_1" value="1" <?php if($status == "1") echo 'checked="checked"';?> disabled='' />New coming
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_2" value="2" <?php if($status == "2") echo 'checked="checked"';?> disabled='' />In process
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_3" value="3" <?php if($status == "3") echo 'checked="checked"';?> disabled='' />Approved
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status_4" value="4" <?php if($status == "4") echo 'checked="checked"';?> disabled='' />Rejected
							</label>
							*/ ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="confirm_status">Confirm status</label>
						<div class="col-md-12 col-lg-8">
							<?php 
								$confirm_status = get_array_value($order_main,"confirm_status","0");
							?>
							<label class="checkbox-inline">
								<input type="checkbox" name="confirm_status" id="confirm_status" value="1" <?php if($confirm_status == "1") echo 'checked="checked"';?> />Confirm payment &amp; Show in report
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="transport_status">Shipping status</label>
						<div class="col-md-12 col-lg-8">
							<?php 
								$need_transport =  get_array_value($order_main,"need_transport","0"); 
								$transport_status =  get_array_value($order_main,"transport_status","0"); 
								$disabled = ($need_transport == '1' && $status == "3") ? "" : "disabled=''";
							?>
							<label class="radio-inline">
								<input type="radio" name="transport_status" id="transport_status_1" value="1" <?php if($transport_status == "1") echo 'checked="checked"';?> <?=$disabled?> />Waiting for shipping
							</label>
							<label class="radio-inline">
								<input type="radio" name="transport_status" id="transport_status_2" value="2" <?php if($transport_status == "2") echo 'checked="checked"';?> <?=$disabled?> />Shipped
							</label>
							<label class="radio-inline">
								<input type="radio" name="transport_status" id="transport_status_3" value="3" <?php if($transport_status == "3") echo 'checked="checked"';?> <?=$disabled?> />Cancelled
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="remark_seller">Remark</label>
						<div class="col-md-12 col-lg-8">
							<textarea class="form-control" type="text" id="remark_seller" name="remark_seller"><?=get_array_value($order_main,"remark_seller","")?></textarea>
						</div>
					</div>
				</div>
			</section>

			<section class="panel">
				<!-- Button -->
				<div class="panel-body">
					<a class="btn btn-primary" onclick="processSubmitOption('frm_order', '0');" />Save & Close</a>
					<a class="btn btn-default" onclick="processRedirect('admin/order/show');" />Cancel</a>
				</div>
				<!-- End : Button -->
			</section>

			<!-- invoice start-->
			<section>
				<div class="panel panel-primary">
					<!--<div class="panel-heading navyblue"> INVOICE</div>-->
					<div class="panel-body">
						<div class="row invoice-list">
							<div class="col-lg-8 col-sm-8">
								<h4>SHIPPING ADDRESS</h4>
								<ul class="unstyled">
									<li>Name : <strong><?=get_array_value($order_main,"buyer_name","-")?></strong></li>
									<li>Email : <?=get_array_value($order_main,"buyer_email","-")?></li>
									<li>Address : <?=get_array_value($order_main,"buyer_address","-")?></li>
									<li>Contact : <?=get_array_value($order_main,"buyer_contact","-")?></li>
									<li>Remark : <?=get_array_value($order_main,"remark","-")?></li>
								</ul>
							</div>
							<div class="col-lg-4 col-sm-4">
								<h4>ORDER INFO</h4>
								<ul class="unstyled">
									<li>Order number : <strong><?=get_array_value($order_main,"cid","")?></strong></li>
									<li>Order date : <?=get_array_value($order_main,"created_date_time_txt","-")?></li>
									<li>Order status : <?=get_array_value($order_main,"status_txt","-")?></li>
									<li>Order type : <?=get_array_value($order_main,"type_txt","-")?></li>
									<li>Payment type : <?=get_array_value($order_main,"payment_type_txt","-")?></li>
								</ul>
							</div>
						</div>
						<table class="table table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Item</th>
									<?php if(is_var_array($order_detail)){ ?><th class="">Title</th><?php } ?>
									<?php if(is_var_array($order_detail)){ ?><th class="a-center">Transport?</th><?php } ?>
									<?php if(is_var_array($order_detail)){ ?><th class="a-center">Unit Cost (<?=get_array_value($order_main,"currency","-")?>)</th><?php } ?>
									<?php if(is_var_array($order_detail)){ ?><th class="a-center">Quantity</th><?php } ?>
									<th class="a-center">Total (<?=get_array_value($order_main,"currency","-")?>)</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$i=0;
									?>
								<?php if(is_var_array($order_detail)){ ?>
									<?php 
										foreach($order_detail as $item){ 
											$i++;
									?>
											<tr>
												<td><?=$i?></td>
												<td><img src="<?=get_array_value($item,"cover_image","-")?>" /></td>
												<td class=""><?=get_array_value($item,"parent_title","-")?></td>
												<td class="a-center"><?=get_array_value($item,"need_transport_txt","-")?></td>
												<td class="a-center"><?=get_array_value($item,"price_per_unit","0")?></td>
												<td class="a-center"><?=get_array_value($item,"unit","0")?></td>
												<td class="a-center"><?=get_array_value($item,"price_total","0")?></td>
											</tr>
									<?php } ?>
								<?php } ?>
								<?php 
									if(is_var_array($package_point_detail)){ 
										$i++;
								?>
										<tr>
											<td><?=$i?></td>
											<td class=""><?=get_array_value($package_point_detail,"name","-")?></td>
											<td class="a-center"><?=get_array_value($package_point_detail,"price","0")?></td>
										</tr>
								<?php } ?>

							</tbody>
						</table>
						<div class="row">
							<div class="col-lg-4 invoice-block pull-right">
								<ul class="unstyled amounts">
									<li><strong>Total (<?=get_array_value($order_main,"currency","-");?>) :</strong> <?=get_array_value($order_main,"actual_total","0");?></li>
								</ul>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4 invoice-block pull-right">
								<ul class="unstyled amounts">
									<li><strong>Redeem (<?=get_array_value($order_main,"currency","-");?>) :</strong> <?=get_array_value($order_main,"redeem_actual_discount","0");?></li>
								</ul>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4 invoice-block pull-right">
								<ul class="unstyled amounts">
									<li><strong>Transport (<?=get_array_value($order_main,"currency","-");?>) :</strong> <?=get_array_value($order_main,"total_transport_fee","0");?></li>
								</ul>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4 invoice-block pull-right">
								<ul class="unstyled amounts">
									<li><strong>Grand Total (<?=get_array_value($order_main,"currency","-");?>) :</strong> <?=get_array_value($order_main,"actual_grand_total","0");?></li>
								</ul>
							</div>
						</div>
						<!--div class="text-center invoice-btn">
							<a class="btn btn-danger btn-lg"><i class="fa fa-check"></i> Submit Invoice </a>
							<a class="btn btn-info btn-lg" onclick="javascript:window.print();"><i class="fa fa-print"></i> Print </a>
						</div-->
					</div>
				</div>
			</section>
			<!-- invoice end-->			
		</div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($){		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>