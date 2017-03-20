<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Order_save_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		define("thisFrontTabMenu",'package_point');
		define("thisFrontSubMenu",'');
		@define("folderName","order/order_front/");
		$this->data["page_title"] = "Buy point";

		$this->package_point_model = "Package_point_model";
		$this->order_main_model = "Order_main_model";
		$this->order_detail_model = "Order_detail_model";
		$this->user_model = "User_model";
		$this->point_history_model = "Point_history_model";
		$this->log_paysbuy_model = "Log_paysbuy_model";
		$this->order_receipt_model = "Order_receipt_model";
		$this->setting_running_model = "Setting_running_model";
		$this->copy_buyout_model = "Copy_buyout_model";
		$this->copy_download_model = "Copy_download_model";

		$this->lang->load('mail');
	}
	
	function index(){
		$this->show();
	}
	
	function save_point($payment_type="", $order_result=""){
		$chk_status = "";
		$order_main_cid = get_array_value($order_result,"cid","");
		$order_main_aid = get_array_value($order_result,"aid","");

		if(is_blank($order_main_cid)){
			$this->log_status('Payment Feedback ['.$payment_type.']', ' Step 1. Order is null. Do nothing.');
			return 'approve-fail';
		}

		//Step 1. update status to order_main
		$data = array();
		$data["status"] = "3"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected		
		$this->load->model($this->order_main_model,"order_main");
		//$this->order_main->set_trans_begin();
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$result = $this->order_main->update_record($data);
		if($result){
			$this->log_status('Payment Feedback ['.$payment_type.']', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved [status = 3]. Success');
			
			//Step 2. add remain point
			$this->load->model($this->user_model,"user");
			$result = $this->user->add_point_remain(get_array_value($order_result,"user_aid",""),get_array_value($order_result,"all_unit_total","0"));
			if($result){
				$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 2. Order ['.$order_main_cid.']. Add point ['.get_array_value($order_result,"all_unit_total","0").'] to user ['.get_array_value($order_result,"user_aid","").']. Success');
				
				//Step 3. add record to point history
				$data = array();
				$data["user_aid"] = get_array_value($order_result,"user_aid","");
				$data["order_aid"] = get_array_value($order_result,"aid","");
				$data["point_type"] = "1"; //1 = Receive, 2 Pay
				$data["point"] = get_array_value($order_result,"all_unit_total","0");
				$data["status"] = "1"; //1=Active, 2=Inactive
				$this->load->model($this->point_history_model,"point_history");
				$result = $this->point_history->insert_record($data);
				
				if($result){
					$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 3. Order ['.$order_main_cid.']. Add point history. Success', $data);
					
					//Step 4. add receipt
					$this->load->model($this->order_receipt_model,"order_receipt");
					$this->order_receipt->set_where(array("order_main_aid"=>$order_main_aid));
					$result = $this->order_receipt->load_records(false);
					if(is_var_array($result)){
						$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 3. Order ['.$order_main_cid.']. has receipt already. DO not add new receipt');
						$chk_status = "success";
						//$this->order_main->set_trans_rollback();
					}else{
						$this->load->model($this->setting_running_model,"running");
						$cid = $this->running->get_latest_receipt_by_year(date('Y'));
						$data = array();
						$data["cid"] = $cid;
						$data["order_main_aid"] = $order_main_aid;
						$data["status"] = "1";
						$data["type"] = "1"; //1 = Buy point, 2 Buy book
						$data["remark"] = "";
						$data["channel"] = "1"; //1=web, 2=ipad
						$this->load->model($this->order_receipt_model,"order_receipt");
						$aid = $this->order_receipt->insert_record($data);
						if($aid>0){
							$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 4. Order ['.$order_main_cid.']. Add receipt. Success', $data);
							$chk_status = "success";
							//$this->order_main->set_trans_commit();
							$vat = 0;
							$total_before_vat = 0;
							
							$price_after_vat = get_array_value($order_result,"actual_grand_total","0");
							$price_before_vat = round(($price_after_vat/1.07),2);
							$total_before_vat += $price_before_vat;
							$vat += ($price_after_vat-$price_before_vat);
							
							$order_table = "";
							$order_table .= '
							<table cellspacing="0" cellpadding="0" border="0" width="100%" class="item-box">
							<tr>
							<td class="header">No.</td>
							<td class="header">Item</td>
							<td class="header hright" width="100">Price (Baht)</td>
							</tr>
							';
							$order_table .= '
							<tr>
							<td>1.</td>
							<td>'.get_array_value($order_result,"package_name","N/A").'</td>
							<td class="hright">'.$price_before_vat.'</td>
							</tr>
							';
							$order_table .= '
							<tr>
							<td colspan="2" class="hright">Total (Baht)</td>
							<td class="hright">'.$total_before_vat.'</td>
							</tr>
							<tr>
							<td colspan="2" class="hright">Vat (7%)</td>
							<td class="hright">'.$vat.'</td>
							</tr>
							<tr>
							<td colspan="2" class="hright">Grand total (Baht)</td>
							<td class="hright">'.get_array_value($order_result,"actual_grand_total_show","").'</td>
							</tr>
							</table>
							';
							
							$body = $this->lang->line('mail_content_confirm_basket');
							// $body = eregi_replace("[\]",'',$body);
							$body = str_replace("{doc_type}", "Receipt (ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ)" , $body);
							$body = str_replace("{name}", get_array_value($order_result,"buyer_name","") , $body);
							$body = str_replace("{email}", get_array_value($order_result,"buyer_email",""), $body);
							$body = str_replace("{address}", get_array_value($order_result,"buyer_address",""), $body);
							$body = str_replace("{order_aid}", $cid, $body);
							$body = str_replace("{order_table}", $order_table, $body);
							$body = str_replace("{date}", date('Y/m/d'), $body);
							$body = str_replace("{total}", get_array_value($order_result,"actual_grand_total","0"), $body);
							$body = str_replace("{method}", "Paysbuy", $body);
							$body = str_replace("{remark}", "", $body);
							
							$subject = $this->lang->line('mail_subject_confirm_basket');
							// $subject = eregi_replace("[\]",'',$subject);
							$subject = str_replace("{doc_type}", "Receipt (ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ)" , $subject);
							$subject = str_replace("{order_aid}", $cid, $subject);

							$this->load->library('email');
							$config['mailtype'] = 'html';
							$config['charset'] = 'utf-8';
							$config['wordwrap'] = TRUE;

							$this->email->initialize($config);
							$this->email->set_newline("\r\n");	
							$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
							$this->email->to(get_array_value($order_result,"buyer_email",""));
							$this->email->bcc(MAIN_CONTACT_EMAIL);

							$this->email->subject($subject);
							$this->email->message($body);
							//echo $this->email->print_debugger();
							if(@$this->email->send()){
								$this->log_status('Payment Feedback ['.$payment_type.']', 'Receipt no. ['.$cid.'] : Email sent to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'].');
								$this->log_debug('Payment Feedback ['.$payment_type.'] : Email', 'Send mail to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'] success ['.$body.']');
							}else{
								$this->log_status('Payment Feedback ['.$payment_type.']', 'Receipt no. ['.$cid.'] : Fail to sent email to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'].');
								$this->log_debug('Payment Feedback ['.$payment_type.'] : Email', 'Send mail to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'] fail ['.$body.']');
							}
							
						}else{
							$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 4. Order ['.$order_main_cid.']. Add receipt. Fail');
							//$this->order_main->set_trans_rollback();
							$chk_status = "approve-fail";
						}
					}
				}else{
					$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 3. Order ['.$order_main_cid.']. Add point history. Fail');
					//$this->order_main->set_trans_rollback();
					$chk_status = "approve-fail";
				}
			}else{
				$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 2. Order ['.$order_main_cid.']. Add point['.get_array_value($order_result,"point","0").'] to user['.get_array_value($order_result,"user_aid","").']. Fail');
				//$this->order_main->set_trans_rollback();
				$chk_status = "approve-fail";
			}
		}else{
			$this->log_error('Payment Feedback ['.$payment_type.']', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved[status = 3]. Fail');
			//$this->order_main->set_trans_rollback();
			$chk_status = "approve-fail";
		}

		return $chk_status;
	}

	function _save_basket($payment_type="", $order_result=""){
		$chk_status = "";
		$order_main_cid = get_array_value($order_result,"cid","");
		$order_main_aid = get_array_value($order_result,"aid","");

		if(is_blank($order_main_cid)){
			$this->log_status('Payment Feedback ['.$payment_type.']', ' Step 1. Order is null. Do nothing.');
			return 'approve-fail';
		}

		//Step 1. update status to order_main
		$data = array();
		$data["status"] = "3"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected		
		$this->load->model($this->order_main_model,"order_main");
		//$this->order_main->set_trans_start();
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$result = $this->order_main->update_record($data);


		if($result){
			$this->log_status('Payment Feedback ['.$payment_type.']', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved [status = 3]. Success');
			
			//Step 2. add book to shelf
			$this->load->model($this->order_detail_model,"order_detail");
			$this->order_detail->set_where(array("order_main_aid"=>$order_main_aid));
			$result_detail = $this->order_detail->load_records(false);
			if(is_var_array($result_detail)){
				// $this->log_status('Payment Feedback ['.$payment_type.']', 'Step 2. Order ['.$order_main_cid.']. Add point['.get_array_value($order_result,"point","0").'] to user['.get_array_value($order_result,"user_aid","").']. Success');
				$chk = true;
				$i=0;
				$vat = 0;
				$total_before_vat = 0;
				$order_table = "";
				$order_table .= '
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="item-box">
				<tr>
				<td class="header">No.</td>
				<td class="header">Item</td>
				<td class="header">Unit</td>
				<td class="header hright" width="100">Price (Baht)</td>
				</tr>
				';
				foreach($result_detail as $item){
					$i++;
					$copy_aid = get_array_value($item,"copy_aid","");
					$product_type_aid = get_array_value($item,"product_type_aid","");
					$product_type_cid = get_array_value($item,"product_type_cid","");
					$tmp = array();
					$tmp["product_type_aid"] = $product_type_aid;
					$tmp["copy_aid"] = $copy_aid;
					$product_copy_detail = $this->get_product_copy_detail($tmp);
					// print_r($product_copy_detail);
					$is_ebook = get_array_value($product_copy_detail,"is_ebook","");
					$is_license = get_array_value($product_copy_detail,"is_license","");
					$rental_period = get_array_value($product_copy_detail,"rental_period","");
					$possession = get_array_value($product_copy_detail,"possession","0");
					$expiration_date = "";

					if($is_license){
						$expiration_date = date("Y-m-d",strtotime("+".$rental_period." days"));
					}

					$unit = get_array_value($item,"unit","0");
					$need_transport = get_array_value($item,"need_transport","0");
					$price_after_vat = get_array_value($item,"price_total","0");
					$price_before_vat = round(($price_after_vat/1.07),2);
					$total_before_vat += $price_before_vat;
					$vat += ($price_after_vat-$price_before_vat);
					if($need_transport != '1'){
						//Add book to shelf

						// require_once(APPPATH."controllers/shelf/shelf_function_controller.php");
						// $shelf_function = new Shelf_function_controller();
						$tmp = array();
						$tmp["user_aid"] = get_array_value($item,"user_aid","");
						$tmp["copy_aid"] = get_array_value($item,"copy_aid","");
						$tmp["product_type_aid"] = get_array_value($item,"product_type_aid","");
						$tmp["product_type_cid"] = get_array_value($item,"product_type_cid","");
						$tmp["parent_aid"] = get_array_value($item,"parent_aid","");
						$tmp["is_license"] = $is_license;
						$tmp["expiration_date"] = $expiration_date;
						$tmp["status"] = '1';
						$tmp["is_read"] = '0';
						$this->load->model($this->shelf_model,"shelf");
						// $result = $shelf_function->add_product_to_shelf($tmp);
						$result = $this->shelf->insert_or_update($tmp);
						if($result){
							$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 2. Add ['.get_array_value($item,"parent_title","").'] to shelf of ['.get_array_value($item,"user_aid","").']. Success');
						}else{
							$chk = false;
							$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 2. Add ['.get_array_value($item,"parent_title","").'] to shelf of ['.get_array_value($item,"user_aid","").']. Fail');
						}
					}
					// Add copy download for report
					$tmp = array();
					$tmp["order_main_aid"] = $order_main_aid;
					$tmp["product_type_aid"] = get_array_value($item,"product_type_aid","");
					$tmp["product_type_cid"] = get_array_value($item,"product_type_cid","");
					$tmp["copy_aid"] = get_array_value($item,"copy_aid","");
					$tmp["user_aid"] = get_array_value($item,"user_aid","");
					$tmp["price_cover"] = "0";
					$tmp["price_currency"] = get_array_value($item,"currency","");
					$tmp["price_actual"] = get_array_value($item,"price_total","");
					$tmp["status"] = '1';
					$tmp["channel"] = '1';
					$this->load->model($this->copy_download_model,"copy_download");
					$result = $this->copy_download->insert_or_update($tmp);
					if($result){
						$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 2.2 Add ['.get_array_value($item,"parent_title","").'] to copy_download of ['.get_array_value($item,"user_aid","").']. Success');
					}else{
						$chk = false;
						$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 2.2 Add ['.get_array_value($item,"parent_title","").'] to copy_download of ['.get_array_value($item,"user_aid","").']. Fail');
					}
					
					$order_table .= '
					<tr>
					<td>'.$i.'.</td>
					<td>'.get_array_value($item,"parent_title","N/A").'</td>
					<td class="hright">'.$unit.'</td>
					<td class="hright">'.$price_before_vat.'</td>
					</tr>
					';
				}
				$order_table .= '
				<tr>
				<td colspan="3" class="hright">Total (Baht)</td>
				<td class="hright">'.$total_before_vat.'</td>
				</tr>
				<tr>
				<td colspan="3" class="hright">Vat (7%)</td>
				<td class="hright">'.$vat.'</td>
				</tr>
				<tr>
				<td colspan="3" class="hright">Grand total (Baht)</td>
				<td class="hright">'.get_array_value($order_result,"actual_grand_total_show","").'</td>
				</tr>
				</table>
				';
				
				if($chk){
					$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 2. All book saved to shelf of ['.get_array_value($item,"user_aid","").']. Success');
					//Step 3. add receipt
					$this->load->model($this->order_receipt_model,"order_receipt");
					$this->order_receipt->set_where(array("order_main_aid"=>$order_main_aid));
					$result = $this->order_receipt->load_records(false);
					if(is_var_array($result)){
						$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 3. Order ['.$order_main_cid.']. has receipt already. DO not add new receipt');
						//$this->order_main->set_trans_rollback();
						$chk_status = "success";
					}else{
						$this->load->model($this->setting_running_model,"running");
						$cid = $this->running->get_latest_receipt_by_year(date('Y'));
						$data = array();
						$data["cid"] = $cid;
						$data["order_main_aid"] = $order_main_aid;
						$data["status"] = "1";
						$data["type"] = "2"; //1 = Buy point, 2 Buy book
						$data["remark"] = "";
						$data["channel"] = "1"; //1=web, 2=ipad
						$this->load->model($this->order_receipt_model,"order_receipt");
						$aid = $this->order_receipt->insert_record($data);
						if($aid>0){
							$this->log_status('Payment Feedback ['.$payment_type.']', 'Step 3. Order ['.$order_main_cid.']. Add receipt. Success');
							//$this->order_main->set_trans_commit();
							$chk_status = "success";
							
							//Update total download to issue
							$this->db->flush_cache();
							$this->load->model($this->issue_model,"issue");
							$result = $this->issue->increase_total_download($aid);
							
							$body = $this->lang->line('mail_content_confirm_basket');
							$body = eregi_replace("[\]",'',$body);
							$body = str_replace("{doc_type}", "Receipt (ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ)" , $body);
							$body = str_replace("{name}", get_array_value($order_result,"buyer_name","") , $body);
							$body = str_replace("{email}", get_array_value($order_result,"buyer_email",""), $body);
							$body = str_replace("{address}", get_array_value($order_result,"buyer_address",""), $body);
							$body = str_replace("{order_aid}", $cid, $body);
							$body = str_replace("{order_table}", $order_table, $body);
							$body = str_replace("{date}", date('Y/m/d'), $body);
							$body = str_replace("{total}", get_array_value($order_result,"actual_grand_total","0"), $body);
							$body = str_replace("{method}", "Paysbuy", $body);
							$body = str_replace("{remark}", "หนังสือได้เข้าสู่ <a href='".site_url('my-bookshelf')."'>My bookshelf</a> แล้ว", $body);
							
							$subject = $this->lang->line('mail_subject_confirm_basket');
							$subject = eregi_replace("[\]",'',$subject);
							$subject = str_replace("{doc_type}", "Receipt (ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ)" , $subject);
							$subject = str_replace("{order_aid}", $cid, $subject);

							$this->load->library('email');
							$config = $this->get_init_email_config();
							if(is_var_array($config)){ 
								$this->email->initialize($config);
								$this->email->set_newline("\r\n");
							 }
							$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
							$this->email->to(get_array_value($order_result,"buyer_email",""));
							$this->email->bcc(MAIN_CONTACT_EMAIL);

							$this->email->subject($subject);
							$this->email->message($body);
							//echo $this->email->print_debugger();
							if(@$this->email->send()){
								$this->log_status('Payment Feedback ['.$payment_type.']', 'Receipt no. ['.$cid.'] : Email sent to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'].');
								$this->log_debug('Payment Feedback ['.$payment_type.'] : Email', 'Send mail to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'] success ['.$body.']');
							}else{
								$this->log_status('Payment Feedback ['.$payment_type.']', 'Receipt no. ['.$cid.'] : Fail to sent email to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'].');
								$this->log_debug('Payment Feedback ['.$payment_type.'] : Email', 'Send mail to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'] fail ['.$body.']');
							}
							
						}else{
							$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 3. Order ['.$order_main_cid.']. Add receipt. Fail');
							//$this->order_main->set_trans_rollback();
							$chk_status = "approve-fail";
						}
					}
				}else{
					$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 2. Some books can not add to shelf of ['.get_array_value($item,"user_aid","").']. Fail');
					//$this->order_main->set_trans_rollback();
					$chk_status = "approve-fail";
				}
			}else{
				$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 2. Order ['.$order_main_cid.']. Add book to shelf for user['.get_array_value($order_result,"user_aid","").']. Fail : Not found any book detail.');
				//$this->order_main->set_trans_rollback();
				$chk_status = "approve-fail";
			}
		}else{
			$this->log_error('Payment Feedback ['.$payment_type.']', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved[status = 3]. Fail');
			//$this->order_main->set_trans_rollback();
			$chk_status = "approve-fail";
		}
		return $chk_status;
	}

	function save_basket($payment_type="", $order_result=""){
		$chk_status = "";
		$order_main_cid = get_array_value($order_result,"cid","");
		$order_main_aid = get_array_value($order_result,"aid","");
		// echo "order_main_aid = $order_main_aid , order_main_cid = $order_main_cid";

		if(is_blank($order_main_cid)){
			$this->log_status('Payment Feedback ['.$payment_type.']', ' Step 1. Order is null. Do nothing.');
			return 'approve-fail';
		}

		$need_transport = get_array_value($order_result,"need_transport","0");
		// echo "need_transport = $need_transport";

		//Step 1. update status to order_main
		$data = array();
		$data["status"] = "3"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected	
		if($need_transport == '1'){
			$data["transport_status"] = "1";
		}else{
			$data["transport_status"] = "0";
		}
		$this->load->model($this->order_main_model,"order_main");
		//$this->order_main->set_trans_begin();
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$result = $this->order_main->update_record($data);

		if($result){
			$this->log_status('Payment Feedback ['.$payment_type.']', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved [status = 3]. Success', $data);
			
			//Step 2. load order detail
			$this->load->model($this->order_detail_model,"order_detail");
			$this->order_detail->set_where(array("order_main_aid"=>$order_main_aid));
			$result_detail = $this->order_detail->load_records(false);
			// print_r($result_detail); echo "<HR>";
			if(is_var_array($result_detail)){
				$chk = true;
				$i=0;
				$vat = 0;
				$total_before_vat = 0;

				foreach($result_detail as $item){
					$product_type_aid = get_array_value($item,"product_type_aid","");
					$product_type_cid = get_array_value($item,"product_type_cid","");
					$copy_aid = get_array_value($item,"copy_aid","");
					$user_aid = get_array_value($item,"user_aid","");
					$unit = get_array_value($item,"unit","0");
					$i++;

					$tmp = array();
					$tmp["product_type_aid"] = $product_type_aid;
					$tmp["copy_aid"] = $copy_aid;
					$product_copy_detail = $this->get_product_copy_detail($tmp);
					// print_r($product_copy_detail); echo "<HR>";

					$is_ebook = get_array_value($product_copy_detail,"is_ebook","0");
					// echo "is_ebook = $is_ebook";
					if($is_ebook == '1'){
						//Add to bookshelf
						$parent_aid = get_array_value($product_copy_detail,"parent_aid","0");
						$is_license = get_array_value($product_copy_detail,"is_license","0");
						$possession = get_array_value($product_copy_detail,"possession","0");
						$ebook_concurrence = get_array_value($product_copy_detail,"ebook_concurrence","0");

						$rental_period = get_array_value($product_copy_detail,"rental_period","");

						$model = $this->get_product_model($product_type_aid);
						
						$expiration_date = "";
						if($is_license == "1"){
							$expiration_date = date("Y-m-d",strtotime("+".$rental_period." days"));

						}

						$tmp = array();
						$tmp["copy_aid"] = $copy_aid;
						$tmp["user_aid"] = $user_aid;
						$this->db->flush_cache();
						$this->load->model($this->shelf_model,"shelf");
						$result = $this->shelf->set_where($tmp);
						$result = $this->shelf->load_record(false);

						if($result){ // not first time load
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["product_type_cid"] = $product_type_cid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["parent_aid"] = $parent_aid;
							$tmp["user_aid"] = $user_aid;
							$tmp["status"] = '1';
							$tmp["is_license"] = $is_license;
							$tmp["is_read"] = '0';
							$tmp["expiration_date"] = $expiration_date;
							$this->load->model($this->shelf_model,"shelf");
							$result = $this->shelf->insert_or_update($tmp);
							
							if($result){
								//$this->order_main->set_trans_commit();
								$chk_status = "success";
							}else{
								//$this->order_main->set_trans_rollback();
								$chk_status = "approve-fail";
							}
							
						}else{ //first time load
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["product_type_cid"] = $product_type_cid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["parent_aid"] = $parent_aid;
							$tmp["user_aid"] = $user_aid;
							$tmp["status"] = '1';
							$tmp["is_license"] = $is_license;
							$tmp["is_read"] = '0';
							$tmp["expiration_date"] = $expiration_date;
							$this->load->model($this->shelf_model,"shelf");
							$result = $this->shelf->insert_or_update($tmp);
							
							if($result){
								//Update total download to parent
								$this->db->flush_cache();
								$this->load->model(get_array_value($model,"product_model",""),"main");
								$result = $this->main->increase_total_download_web($parent_aid);	
								
								//Update shelf history
								$this->db->flush_cache();
								$this->load->model($this->shelf_history_model,"shelf_history");
								$tmp = array();
								$tmp["product_type_aid"] = $product_type_aid;
								$tmp["product_type_cid"] = $product_type_cid;
								$tmp["is_license"] = $is_license;
								$tmp["copy_aid"] = $copy_aid;
								$tmp["parent_aid"] = $parent_aid;
								$tmp["user_aid"] = $user_aid;
								$tmp["status"] = '1';
								$tmp["action"] = 'in';
								$result = $this->shelf_history->insert_record($tmp);	
								
								if($possession == "1"){
									//Update copy buyout
									$this->db->flush_cache();
									$this->load->model($this->copy_buyout_model,"copy_buyout");
									$tmp = array();
									$tmp["product_type_aid"] = $product_type_aid;
									$tmp["product_type_cid"] = $product_type_cid;
									$tmp["copy_aid"] = $copy_aid;
									$tmp["parent_aid"] = $parent_aid;
									$tmp["user_aid"] = $user_aid;
									$tmp["status"] = '1';
									$tmp["price"] = '0';
									$result = $this->copy_buyout->insert_or_update($tmp);
								}
								
								//Add copy download for report
								$tmp = array();
								$tmp["order_main_aid"] = $order_main_aid;
								$tmp["product_type_aid"] = $product_type_aid;
								$tmp["product_type_cid"] = $product_type_cid;
								$tmp["is_license"] = $is_license;
								$tmp["copy_aid"] = $copy_aid;
								$tmp["parent_aid"] = $parent_aid;
								$tmp["user_aid"] = $user_aid;
								$tmp["price_cover"] = get_array_value($product_copy_detail,"sale_price_1","0");
								$tmp["status"] = '1';
								$tmp["channel"] = '1';
								$this->load->model($this->copy_download_model,"copy_download");
								$result2 = $this->copy_download->insert_or_update($tmp);
								if($result2){
									$this->log_status('Product : Download', 'Add/Update ['.get_array_value($product_copy_detail,"title","").'] to copy_download of ['.$user_aid.']. Success');
								}else{
									$chk = false;
									$this->log_error('Product : Download', 'Add/Update ['.get_array_value($product_copy_detail,"title","").'] to copy_download of ['.$user_aid.']. Fail');
								}							
								$chk_status = "success";
							}else{
								$chk_status = "approve-fail";
							}							
						}
					}
				}
			}else{
				$this->log_error('Payment Feedback ['.$payment_type.']', 'Step 2. Order ['.$order_main_cid.']. Add book to shelf for user['.get_array_value($order_result,"user_aid","").']. Fail : Not found any book detail.');
				//$this->order_main->set_trans_rollback();
				$chk_status = "approve-fail";
			}
		}else{
			$this->log_error('Payment Feedback ['.$payment_type.']', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved [status = 3]. Fail');
			//$this->order_main->set_trans_rollback();
			$chk_status = "approve-fail";
		}
		return $chk_status;
	}

	function save_reject($payment_type="", $order_result="", $log_result=""){
		$order_main_cid = get_array_value($order_result,"cid","");
		$order_main_aid = get_array_value($order_result,"aid","");
		// echo "order_main_aid = $order_main_aid , order_main_cid = $order_main_cid";

		if(is_blank($order_main_cid)){
			$this->log_status('Payment Feedback ['.$payment_type.']', ' Step 1. Order is null. Do nothing.');
			return 'approve-fail';
		}

		$need_transport = get_array_value($order_result,"need_transport","0");
		// echo "need_transport = $need_transport";

		//update status to order_main
		$data = array();
		$data["status"] = "4"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected		
		if($need_transport == '1'){
			$data["transport_status"] = "3";
			//Step 2. load order detail
			$this->load->model($this->order_detail_model,"order_detail");
			$this->order_detail->set_where(array("order_main_aid"=>$order_main_aid));
			$result_detail = $this->order_detail->load_records(false);
			// print_r($result_detail); echo "<HR>";
			if(is_var_array($result_detail)){
				foreach ($result_detail as $item) {
					$item_need_transport = get_array_value($item,"need_transport","0");
					if($item_need_transport == '1'){
						$unit = get_array_value($item,"unit","0");
						// $this*********************

					}
				}
			}


		}else{
			$data["transport_status"] = "0";
		}
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$result = $this->order_main->update_record($data);
		if($result){
			$this->log_status('Payment feedback', ' Step 1. Order ['.$order_main_cid.']. Update status to reject[status = 4]. Success', $log_result);
		}else{
			$this->log_error('Payment feedback', ' Step 1. Order ['.$order_main_cid.']. Update status to reject[status = 4]. Fail', $log_result);
			// $this->order_main->set_trans_rollback();
		}
		$this->log_status('Payment feedback', ' End!! Order ['.$order_main_cid.'].', $log_result);
	}

}

?>