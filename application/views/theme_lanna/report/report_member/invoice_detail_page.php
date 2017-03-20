<script type="text/javascript">
$(document).ready(function() {
	
	<?=@$message?>
	<?=@$js_code?>
	
} );
</script>
<link href="<?=CSS_PATH?>basket.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=JS_PATH?>report/report_member/invoice_detail.js"></script>
<?php 
$order_main_result = @$order_main_result;
$order_detail_list = @$order_detail_list;
?>
<div id="basketListItemsWrap">

	<div class="">
		<div class="space hidden"></div>
		<div id="result-msg-box" class="hidden" ></div>

			<?php if(is_var_array($order_main_result)){ ?>
				<table class="cblock" width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="5" class="hright invoice-header"><h2><?=get_array_value($order_main_result,"cid","N/A")?></h2></td>
					</tr>
					<tr class="title">
						<td class="title" width="40">ลำดับ</td>
						<td class="title" width="155">&nbsp;</td>
						<td class="title">สินค้า</td>
						<td class="title hright" width="120">ราคา (<?=get_array_value($order_main_result,"price_classifier","")?>)</td>
						<td class="title" width="10">&nbsp;</td>
					</tr>
				<?php
				if(is_var_array($order_detail_list)){
					$i = 0;
					foreach($order_detail_list as $item){
						$i++;
						$detail = get_array_value($item,"product_detail","");
				?>
					<tr> 
						<td><?=$i?>.</td>
						<td><img src="<?=get_array_value($item,"issue_img","")?>" /></td>
						<td><?=get_array_value($item,"issue_fullname","N/A")?></td>
						<td class="hright"><?=get_array_value($item,"issue_price_full","-")?></td>
						<td class="hcenter">&nbsp;</td>
					</tr>
				<?php
					}
				}else{
				?>
					<tr> 
						<td>1.</td>
						<td>&nbsp;</td>
						<td><?=get_array_value($order_main_result,"package_name","N/A")?></td>
						<td class="hright"><?=get_array_value($order_main_result,"all_price_total_show","-")?></td>
						<td class="hcenter">&nbsp;</td>
					</tr>
				<?php } ?>
				
					<tr class="title hright hidden">
					<td colspan="3" class="hright">ราคารวม (<?=get_array_value($order_main_result,"price_classifier","")?>)</td>
					<td colspan=""><?=get_array_value($order_main_result,"all_price_total_show","")?></td>
					<td class="">&nbsp;</td>
					</tr>
					
					<tr class="title hright">
					<td colspan="4" class="hright"><?=get_array_value($order_main_result,"payment_type_txt","")?> (<?=get_array_value($order_main_result,"status_txt","")?>)</td>
					<td class="">&nbsp;</td>
					</tr>
					
					<?php $transport_type = get_array_value($order_main_result,"transport_type",""); ?>
					<tr class="title hright hidden">
					<td colspan="3" class="hright">ค่าจัดส่ง
					<?php if($transport_type == "normal"){ echo "(ลงทะเบียน)"; }else{ echo "EMS"; } ?>

					</td>
					<td colspan="3"><?=get_array_value($order_main_result,"transport_fee_show","")?></td>
					<td class="">&nbsp;</td>
					</tr>
					
					<tr class="title hright">
					<td colspan="3" class="hright summary">รวมทั้งสิ้น (<?=get_array_value($order_main_result,"price_classifier","")?>)</td>
					<td colspan="" class="hright summary"><?=get_array_value($order_main_result,"actual_grand_total_show","0")?></td>
					<td class="">&nbsp;</td>
					</tr>
					
					<tr class="title hright">
					<td colspan="4" class="hright">
						<p class="frm-button">         
							<a class="small-button button" onClick="processRedirect('purchase-history/summary')">Back</a> 
						</p> 
					</td>
					<td class="">&nbsp;</td>
					</tr>
				</table>
			<?php }else{ ?>		
			
			<?php } ?>
		
</div>