<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Basket_front_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		@define("folderName","order/order_front/");
		$this->data["page_title"] = "My Shopping Cart";
		
		$this->lang->load('mail');
		$this->lang->load('order');

		$this->setting_running_model = "Setting_running_model";
		$this->order_main_model = "Order_main_model";
		$this->order_detail_model = "Order_detail_model";

		$this->point_history_model = "Point_history_model";
		$this->redeem_history_model = "redeem_history_model";
		
	}
	
	function index(){
		$this->show();
	}
	
	function show(){
		@define("thisAction","show");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_list';
		$this->data["message"] = "";		
		$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
	}
	
	function ajax_get_basket_badge($sid){
		$basket = $this->get_basket();
		$result_obj = array("status" => 'success',"msg" => '', "result"=>get_array_value($basket,"unit_grand_total",""));
		echo json_encode($result_obj);
		return "";
	}
	
	function ajax_get_basket($sid=""){
		$result = $this->get_basket();
		if(is_var_array($result)){
			$result_obj = array("status" => 'success',"msg" => '', "result"=>$result );
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'error',"msg" => 'Your shopping cart is empty.' );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function get_basket(){
		// $this->session->set_userdata('basketSession','');
		$s_product = $this->session->userdata('basketSession');
		$all = "";
		$master_transport = $this->data["master_transport"];
		$transport_arr = "";
		$total_transport_fee = 0;
		if(is_var_array($master_transport)){
			foreach ($master_transport as $item) {
				$transport_aid = get_array_value($item,"aid","0");
				$transport_fee = get_array_value($item,"transport_fee","0");
				$free_price_over = get_array_value($item,"free_price_over","0");
				$free_unit_over = get_array_value($item,"free_unit_over","0");
				$title = get_array_value($item,"title","0");
				$transport_arr[$transport_aid]["title"] = $title;
				$transport_arr[$transport_aid]["transport_fee"] = $transport_fee;
				$transport_arr[$transport_aid]["free_price_over"] = $free_price_over;
				$transport_arr[$transport_aid]["free_unit_over"] = $free_unit_over;
				$transport_arr[$transport_aid]["now_price"] = 0;
			}
		}
		// print_r($transport_arr);
		
		$master_payment_type = explode(":",CONST_MASTER_PAYMENT_TYPE);
		$payment_type = $this->session->userdata('paymentTypeSession');
		if(is_blank($payment_type) || !in_array($payment_type, $master_payment_type)){
			$payment_type = $master_payment_type[0];
			$this->session->set_userdata('paymentTypeSession',$payment_type);
		}

		$actual_classifier = "";		
		$unit_grand_total = 0;
		$actual_total = 0;
		$actual_grand_total = 0;
		$after_redeem_grand_total = 0;
		$redeem_actual_discount = 0;
		$price_total = 0;
		$point_total = 0;
		$price_grand_total = 0;
		$point_grand_total = 0;
		$actual_transport_total = 0;

		$has_paper = 0;
		$has_ebook = 0;
		$has_change = 0;
		
		$redeem_main_aid = "";
		$redeem_code = "";
		$redeem_object = "";
		$redeem_type = "";
		$redeem_value = "";
		if(CONST_HAS_REDEEM == "1"){
			$redeem_code = $this->session->userdata('redeemCodeSession');
			if(!is_blank($redeem_code)){
				$redeem_object = $this->check_redeem_code($redeem_code, getSessionUserAid(), "cart");
				$status = get_array_value($redeem_object,"status","");
				$msg = get_array_value($redeem_object,"msg","");
				$redeem_result = get_array_value($redeem_object,"redeem_result","");
				if($status == "error"){
					$has_change = 1;
					$redeem_object["status"] = "error";
					$redeem_object["message"] = set_message_error($msg, "", "", false, "result-msg-redeem-box");
					$redeem_object["js_code"] = '$("#b_redeem_code").addClass("error");';
					$this->session->set_userdata('redeemCodeSession','');
				}else{
					$redeem_object["status"] = "success";
					$redeem_object["message"] = set_message_success($msg, "", "", false, "result-msg-redeem-box");
					$redeem_object["js_code"] = '$("#b_redeem_code").addClass("success");';
					$redeem_object["redeem_result"] = $redeem_result;
					$redeem_main_aid = get_array_value($redeem_result,"redeem_main_aid","");
					$redeem_type = get_array_value($redeem_result,"redeem_main_type","");
					$redeem_value = get_array_value($redeem_result,"redeem_main_value","0");
				}
			}
		}

		$item_list = array();
		if(is_var_array($s_product)){
			// print_r($s_product);
			foreach($s_product as $item){
				$copy_aid = get_array_value($item,"aid","");
				$product_type_aid = get_array_value($item,"pta","");
				$unit = get_array_value($item,"unit","1");

				$model = $this->get_product_model($product_type_aid);
				$model_copy_name = get_array_value($model,"product_copy_model","");
				$this->db->flush_cache();
				$this->db->_reset_select();
				$this->load->model($model_copy_name, $model_copy_name);
				$tmp = array();
				$tmp = array();
				$tmp["aid"] = $copy_aid;
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["status"] = "1";
				$tmp["parent.status"] = "1";
				$this->{$model_copy_name}->set_where($tmp);
				$copy_detail = $this->{$model_copy_name}->load_record(true);

				// $this->db->_reset_select();
				// $this->load->model($this->view_all_product_copies_with_detail,"vpc");
				// $tmp = array();
				// $tmp["aid"] = $copy_aid;
				// $tmp["product_type_aid"] = $product_type_aid;
				// $tmp["status"] = "1";
				// $tmp["parent_status"] = "1";
				// $this->vpc->set_where($tmp);
				// $copy_detail = $this->vpc->load_record(true);
				// print_r($copy_detail);
				$need_transport = 0;
				if(is_var_array($copy_detail)){
					$price_per_item = 0;
					$point_per_item = 0;
					$stock_status = "";
					$is_ebook = get_array_value($copy_detail,"is_ebook","0");
					if($is_ebook == "1"){
						$transport_aid = 0;
						$transport_price = 0;
						$unit = 1;
						$this->db->_reset_select();
						$this->load->model($this->copy_buyout_model,"copy_buyout");
						$tmp_s = array();
						$tmp_s["copy_aid"] = $copy_aid;
						$tmp_s["product_type_aid"] = $product_type_aid;
						$tmp_s["user_aid"] = getSessionUserAid();
						$this->copy_buyout->set_where($tmp_s);
						$copy_buyout_result = $this->copy_buyout->load_records(false);
						if(is_var_array($copy_buyout_result)){
							$has_change = 1;
							$unit = 0;
							$stock_status = "You already buy this book.";
							$price_per_item = "0";
							$point_per_item = "0";
						}else{
							$is_license = get_array_value($copy_detail,"is_license","0");
							$ebook_concurrence = get_array_value($copy_detail,"ebook_concurrence","0");
							if($is_license == "1"){
								$this->db->_reset_select();
								$this->load->model($this->shelf_model,"shelf");
								$tmp_s = array();
								$tmp_s["copy_aid"] = $copy_aid;
								$this->shelf->set_where($tmp_s);
								$shelf_result = $this->shelf->load_records(false);
								$used_total = 0;
								if(is_var_array($shelf_result)){
									$used_total = count($shelf_result);
								}
								$available_total = $ebook_concurrence - $used_total;
								if($available_total == 0){
									$has_change = 1;
									$unit = 0;
									$stock_status = "Out of stock.";
								}

							}else{
								$price_per_item = get_array_value($copy_detail,"actual_price","0");
								$point_per_item = get_array_value($copy_detail,"actual_point","0");
							}
						}
					}else{
						$need_transport = 1;
						$in_stock = get_array_value($copy_detail,"in_stock","0");
						$transport_aid = get_array_value($copy_detail,"transport_aid","0");
						$transport_price = get_array_value($copy_detail,"transport_price","0");
						// echo "in_stock = $in_stock , unit = $unit <BR>";
						if($in_stock <= 0){
							$has_change = 1;
							$unit = 0;
							$stock_status = "Out of stock.";
						}else if($in_stock < $unit){
							$has_change = 1;
							$unit = $in_stock;
							if($unit == "1"){
								$stock_status = "Only ".$unit." copy left.";
							}else{
								$stock_status = "Only ".$unit." copies left.";
							}
						}
						$price_per_item = get_array_value($copy_detail,"actual_price","0");
						$point_per_item = get_array_value($copy_detail,"actual_point","0");
					}

					$item["is_ebook"] = $is_ebook;
					$item["need_transport"] = $need_transport;
					$item["product_type_cid"] = get_array_value($copy_detail,"product_type_cid","");
					$item["parent_aid"] = get_array_value($copy_detail,"parent_aid","");
					$item["parent_title"] = get_array_value($copy_detail,"parent_title","");
					$item["cover_image_small"] = get_array_value($copy_detail,"cover_image_small","");

					$item["price_per_item"] = $price_per_item;
					$item["point_per_item"] = $point_per_item;
					$price_total_per_item = ($price_per_item*$unit);
					$point_total_per_item = ($point_per_item*$unit);
					$item["price_total_per_item"] = $price_total_per_item;
					$item["point_total_per_item"] = $point_total_per_item;
					$price_total += $price_total_per_item;
					$point_total += $point_total_per_item;
					$unit_grand_total += $unit;

					$actual_per_item = 0;
					$actual_total_per_item = 0;
					$title_label = get_array_value($copy_detail,"title_label","");
					switch($payment_type){
						case "paysbuy" : 
							$payment_type_txt = "Paysbuy";
							$actual_classifier = "Baht"; 
							$actual_per_item = $price_per_item;
							$actual_total_per_item = $price_total_per_item;
							$actual_total = $price_total;
							$title_label .= ' [THB '.$price_per_item.']';
							if($transport_aid > 0){
								$transport_arr[$transport_aid]["now_price"] += $actual_total_per_item;
							}else{
								$total_transport_fee += $transport_price;
							}
							break;
						case "paypal" : 
							$payment_type_txt = "Paypal";
							$actual_classifier = "Baht"; 
							$actual_per_item = $price_per_item;
							$actual_total_per_item = $price_total_per_item;
							$actual_total = $price_total;
							$title_label .= ' [THB '.$price_per_item.']';
							if($transport_aid > 0){
								$transport_arr[$transport_aid]["now_price"] += $actual_total_per_item;
							}else{
								$total_transport_fee += $transport_price;
							}
							break;
						case "point" : 
							$payment_type_txt = "Point";
							$actual_classifier = "Point"; 
							$actual_per_item = $point_per_item;
							$actual_total_per_item = $point_total_per_item;
							$actual_total = $point_total;
							$title_label .= ' [Point '.$point_per_item.']';
							if($transport_aid > 0){
								$transport_arr[$transport_aid]["now_price"] += $actual_total_per_item;
							}else{
								$total_transport_fee += $transport_price;
							}
							break;
						default : 
							break;
					}

					$item["unit"] = $unit;
					$item["stock_status"] = $stock_status;
					$item["title_label"] = $title_label;
					$item["actual_per_item"] = $actual_per_item;
					$item["actual_total_per_item"] = $actual_total_per_item;
					$item_list[] = $item;
					if($unit > 0 && $is_ebook == '1'){
						$has_ebook = 1;
					}else{
						$has_paper = 1;
					}
				}
			}

			$price_grand_total = $price_total;
			$point_grand_total = $point_total;
			$actual_grand_total = $actual_total;
			$after_redeem_grand_total = $actual_total;

			// echo "redeem_type = $redeem_type , redeem_value = $redeem_value <BR />";

			if(!is_blank($redeem_type) && $redeem_value > 0){
				switch ($redeem_type) {
					case 'cash':
						$price_grand_total = $price_total - $redeem_value;
						$point_grand_total = $point_total - $redeem_value;
						$actual_grand_total = $actual_total - $redeem_value;
						$after_redeem_grand_total = $actual_total - $redeem_value;
						$redeem_actual_discount = $redeem_value;
						break;
					
					case 'discount':
						$price_grand_total = $price_total * ((100-$redeem_value)/100);
						$point_grand_total = $point_total * ((100-$redeem_value)/100);
						$actual_grand_total = $actual_total * ((100-$redeem_value)/100);
						$after_redeem_grand_total = $actual_total * ((100-$redeem_value)/100);
						$redeem_actual_discount = $actual_total * ($redeem_value/100);
						break;
					
					default:
						# code...
						break;
				}
			}

			// echo "total_transport_fee = $total_transport_fee <BR>";
			// print_r($transport_arr);
			$actual_grand_total += $total_transport_fee;
			if(is_var_array($transport_arr)){
				foreach ($transport_arr as $item) {
					$now_price = get_array_value($item,"now_price","0");
					$free_price_over = get_array_value($item,"free_price_over","0");
					$transport_fee = get_array_value($item,"transport_fee","0");
					if($now_price > 0 && $now_price < $free_price_over){
						$actual_grand_total += $transport_fee;
						$total_transport_fee += $transport_fee;
					}
				}
			}

			$all["has_paper"] = $has_paper;
			$all["has_ebook"] = $has_ebook;
			$all["has_change"] = $has_change;

			$all["payment_type"] = $payment_type;
			$all["payment_type_txt"] = $payment_type_txt;
			$all["price_total"] = $price_total;
			$all["point_total"] = $point_total;
			$all["price_grand_total"] = $price_grand_total;
			$all["point_grand_total"] = $point_grand_total;
			$all["unit_grand_total"] = $unit_grand_total;
			$all["actual_classifier"] = $actual_classifier;
			$all["actual_per_item"] = $actual_per_item;
			$all["actual_total_per_item"] = $actual_total_per_item;
			$all["actual_total"] = $actual_total;
			$all["actual_grand_total"] = $actual_grand_total;
			$all["after_redeem_grand_total"] = $after_redeem_grand_total;
			$all["redeem_actual_discount"] = $redeem_actual_discount;
			$all["item_list"] = $item_list;
			$all["redeem_main_aid"] = $redeem_main_aid;
			$all["redeem_code"] = $redeem_code;
			$all["redeem_object"] = $redeem_object;
			$all["total_transport_fee"] = $total_transport_fee;
			return $all;
		}else{
			return "";
		}
	}

	function ajax_add_basket($sid=""){
		$s_product = $this->session->userdata('basketSession');

		$product_type_cid = $this->input->get_post('product_type_cid');
		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		$copy_aid = $this->input->get_post('copy_aid');
		$unit = $this->input->get_post('unit');

		if($unit <= 0) $unit = 1;
		
		if($copy_aid <= 0){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$model = $this->get_product_model($product_type_aid);
		$this->db->flush_cache();
		$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
		$tmp = array();
		$tmp["aid"] = $copy_aid;
		$tmp["status"] = "1";
		$tmp["parent.status"] = "1";
		$this->product_copy->set_where($tmp);
		$item_result = $this->product_copy->load_record(true);
		if(!is_var_array($item_result)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$is_ebook = get_array_value($item_result,"is_ebook","0");
		if($is_ebook == "1"){
			$unit = 1;
		}
		$data = "";
		if(is_var_array($s_product)){
			if(array_key_exists('k_'.$product_type_cid.'_'.$copy_aid, $s_product)){
				$data = $s_product['k_'.$product_type_cid.'_'.$copy_aid];
				$data["aid"] = $copy_aid;
				$data["pta"] = $product_type_aid;
				if($is_ebook == "1"){
					$data["unit"] = $unit;
				}else{
					$data["unit"] = $unit + get_array_value($data,"unit","0");
				}
				$s_product['k_'.$product_type_cid.'_'.$copy_aid] = $data;
			}else{
				$data["aid"] = $copy_aid;
				$data["pta"] = $product_type_aid;
				$data["unit"] = $unit;
				$s_product['k_'.$product_type_cid.'_'.$copy_aid] = $data;
			}
		}else{
			$data["aid"] = $copy_aid;
			$data["pta"] = $product_type_aid;
			$data["unit"] = $unit;
			$s_product['k_'.$product_type_cid.'_'.$copy_aid] = $data;
		}
		$this->session->set_userdata('basketSession',$s_product);
		$result_obj = array("status" => 'success',"msg" => '' );
		echo json_encode($result_obj);
		return "";
	}
	
	function ajax_clear_basket($sid=""){
		$this->session->set_userdata('basketSession',"");
		$result_obj = array("status" => 'success',"msg" => '');
		echo json_encode($result_obj);
		return "";
	}
	
	function ajax_remove_basket($sid=""){
		$s_product = $this->session->userdata('basketSession');
		
		$product_type_cid = $this->input->get_post('product_type_cid');
		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		$copy_aid = $this->input->get_post('copy_aid');		
		if($copy_aid <= 0){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
			return "";
		}
		
		if(is_var_array($s_product)){
			unset($s_product['k_'.$product_type_cid.'_'.$copy_aid]);
		}
		$this->session->set_userdata('basketSession',$s_product);
		$result_obj = array("status" => 'success',"msg" => '');
		echo json_encode($result_obj);
		return "";
	}
	
	function ajax_refresh_basket($sid=""){
		$s_product = $this->session->userdata('basketSession');

		$all_item = $this->input->get_post('all_item');
		if(!is_var_array($all_item)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No item found.' );
			echo json_encode($result_obj);
			return "";
		}

		foreach($all_item as $item){
			$product_type_cid = get_array_value($item,"product_type_cid","");
			$product_type_detail = $this->check_exits_product_type($product_type_cid);
			$product_type_aid = get_array_value($product_type_detail,"aid","0");
			$copy_aid = get_array_value($item,"copy_aid","");
			$unit = get_array_value($item,"unit","0");

			// echo "<BR>product_type_aid = $product_type_aid , copy_aid = $copy_aid , unit = $unit <BR>";
			// if($unit <= 0) $unit = 1;
			if($unit <= 0){
				if(is_var_array($s_product)){
					if(array_key_exists('k_'.$product_type_cid.'_'.$copy_aid, $s_product)){
						unset($s_product['k_'.$product_type_cid.'_'.$copy_aid]);
					}
				}
				$this->session->set_userdata('basketSession',$s_product);
				$result_obj = array("status" => 'success',"msg" => '' );
				echo json_encode($result_obj);
				return "";
			}
			
			if($copy_aid <= 0){
				$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
				echo json_encode($result_obj);
				return "";
			}

			$model = $this->get_product_model($product_type_aid);
			$this->db->flush_cache();
			$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
			$tmp = array();
			$tmp["aid"] = $copy_aid;
			$tmp["status"] = "1";
			$tmp["parent.status"] = "1";
			$this->product_copy->set_where($tmp);
			$item_result = $this->product_copy->load_record(true);
			if(!is_var_array($item_result)){
				$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
				echo json_encode($result_obj);
				return "";
			}

			$data = "";
			if(is_var_array($s_product)){
				if(array_key_exists('k_'.$product_type_cid.'_'.$copy_aid, $s_product)){
					$data = $s_product['k_'.$product_type_cid.'_'.$copy_aid];
					$data["aid"] = $copy_aid;
					$data["pta"] = $product_type_aid;
					$data["unit"] = $unit;
					$s_product['k_'.$product_type_cid.'_'.$copy_aid] = $data;
				}
			}
		}

		$this->session->set_userdata('basketSession',$s_product);
		$result_obj = array("status" => 'success',"msg" => '' );
		echo json_encode($result_obj);
		return "";
	}
	
	function ajax_update_basket($sid=""){
		$s_product = $this->session->userdata('basketSession');

		$product_type_cid = $this->input->get_post('product_type_cid');
		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		$copy_aid = $this->input->get_post('copy_aid');
		$unit = $this->input->get_post('unit');

		// if($unit <= 0) $unit = 1;
		if($unit <= 0){
			if(is_var_array($s_product)){
				if(array_key_exists('k_'.$product_type_cid.'_'.$copy_aid, $s_product)){
					unset($s_product['k_'.$product_type_cid.'_'.$copy_aid]);
				}
			}
			$this->session->set_userdata('basketSession',$s_product);
			$result_obj = array("status" => 'success',"msg" => '' );
			echo json_encode($result_obj);
			return "";
		}
		
		if($copy_aid <= 0){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$model = $this->get_product_model($product_type_aid);
		$this->db->flush_cache();
		$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
		$tmp = array();
		$tmp["aid"] = $copy_aid;
		$tmp["status"] = "1";
		$tmp["parent.status"] = "1";
		$this->product_copy->set_where($tmp);
		$item_result = $this->product_copy->load_record(true);
		if(!is_var_array($item_result)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
			return "";
		}
		
		// $is_ebook = get_array_value($item_result,"is_ebook","0");
		// if($is_ebook == "1"){
		// 	$unit = 1;
		// }else{
		// 	$in_stock = get_array_value($item_result,"in_stock","0");
		// 	if($in_stock < $unit){
		// 		$unit = $in_stock;
		// 	}	
		// }

		$data = "";
		if(is_var_array($s_product)){
			if(array_key_exists('k_'.$product_type_cid.'_'.$copy_aid, $s_product)){
				$data = $s_product['k_'.$product_type_cid.'_'.$copy_aid];
				$data["aid"] = $copy_aid;
				$data["pta"] = $product_type_aid;
				$data["unit"] = $unit;
				$s_product['k_'.$product_type_cid.'_'.$copy_aid] = $data;
			}
		}
		$this->session->set_userdata('basketSession',$s_product);
		$result_obj = array("status" => 'success',"msg" => '' );
		echo json_encode($result_obj);
		return "";
	}
	
	function ajax_change_payment_type($sid=""){
		$payment_type = $this->input->get_post('payment_type');
		$this->session->set_userdata('paymentTypeSession', $payment_type);
		$payment_type = $this->session->userdata('paymentTypeSession');		
		$result_obj = array("status" => 'success',"msg" => '');
		echo json_encode($result_obj);
		return "";
	}

	function ajax_add_redeem($sid=""){
		$redeem_code = $this->input->get_post('redeem_code');
		$this->session->set_userdata('redeemCodeSession', $redeem_code);
		$redeem_code = $this->session->userdata('redeemCodeSession');	
		// echo "redeem_code = $redeem_code";
		$result_obj = array("status" => 'success',"msg" => '');
		echo json_encode($result_obj);
		return "";
	}

	function confirm($buyer_detail=""){
		@define("thisAction","intro");
		$this->data["title"] = DEFAULT_TITLE;		
		$s_product = $this->session->userdata('basketSession');
		if(!is_var_array($s_product)){
			redirect('my-cart');
		}

		if(!is_login()){
			redirect('login');
		}

		$basket = $this->get_basket();
		// print_r($basket);
		$has_change = get_array_value($basket,"has_change","");
		$has_paper = get_array_value($basket,"has_paper","");
		$has_ebook = get_array_value($basket,"has_ebook","");
		// echo "has_change = $has_change , has_paper = $has_paper , has_ebook = $has_ebook<BR/>";

		if($has_change){
			redirect('my-cart/status/'.md5('has-change'));
			return"";
		}

		if($has_paper){
			if(is_var_array($buyer_detail)){
				$this->data["buyer_detail"] = $buyer_detail;
			}else if(is_var_array($this->session->userdata('buyerDetailSession'))){
				$this->data["buyer_detail"] = $this->session->userdata('buyerDetailSession');
			}else{
				$aid = getSessionUserAid();
				$this->load->model($this->user_model,"user");
				$this->user->set_where(array("aid"=>$aid));
				$user = $this->user->load_record(false);
				$tmp["buyer_email"] = get_array_value($user,"email","");
				$tmp["buyer_name"] = trim(get_array_value($user,"first_name_th","")." ".get_array_value($user,"last_name_th",""));
				$tmp["buyer_contact"] = get_array_value($user,"contact_number","");
				$tmp["buyer_address"] = get_array_value($user,"address","");
				$this->data["buyer_detail"] = $tmp;
			}

			$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_confirm_address';
			$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
		}else{
			$this->confirm_save();
			return;
		}


		// $this->data["basket_result"] = $this->get_basket();
		// $point_remain = 0;
		// $point_remain = $this->user->get_user_point_remain(getSessionUserAid());
		// $this->data["point_remain"] = $point_remain;
	}

	function confirm_save(){
		@define("thisAction","intro");
		$this->data["title"] = DEFAULT_TITLE;		
		$s_product = $this->session->userdata('basketSession');
		if(!is_var_array($s_product)){
			redirect('my-cart');
		}

		if(!is_login()){
			redirect('login');
		}

		$basket = $this->get_basket();
		// print_r($basket);
		$has_change = get_array_value($basket,"has_change","");
		$has_paper = get_array_value($basket,"has_paper","");
		$has_ebook = get_array_value($basket,"has_ebook","");
		$product_list = get_array_value($basket,"item_list","");
		// echo "has_change = $has_change , has_paper = $has_paper , has_ebook = $has_ebook<BR/>";
		// print_r($product_list);
		// exit(0);

		if($has_change){
			redirect('my-cart/status/'.md5('has-change'));
			return"";
		}

		$buyer_email = $this->input->get_post('buyer_email');
		if(is_blank($buyer_email)){
			$buyer_email = getUserLoginEmail($this);
		}
		$buyer_name = $this->input->get_post('buyer_name');
		if(is_blank($buyer_name)){
			$buyer_name = getUserLoginFullName($this);
		}
		$buyer_contact = $this->input->get_post('buyer_contact');
		if(is_blank($buyer_contact)){
			$buyer_contact = getUserContactNumber($this);
		}
		$buyer_address = $this->input->get_post('buyer_address');
		if(is_blank($buyer_address)){
			$buyer_address = getUserAddress($this);
		}
		$save_profile = $this->input->get_post('save_profile');
		$remark = $this->input->get_post('remark');
		// echo "buyer_email = $buyer_email , save_profile = $save_profile <BR/>";

		if($save_profile == "1" && !is_blank(getSessionUserAid())){
			$data = array();
			$data["first_name_th"] = $buyer_name;
			$data["last_name_th"] = '';
			$data["contact_number"] = $buyer_contact;
			$data["address"] = $buyer_address;
			
			$this->load->model($this->user_model,"user");
			$this->user->set_where(array("aid"=>getSessionUserAid()));
			$rs = $this->user->update_record($data);
			
			//check Email
			$this->load->model($this->user_model,'user');
			$this->user->set_where(array("email"=>$buyer_email));
			$this->user->set_where_not_equal(array("aid"=>getSessionUserAid()));
			$result = $this->user->load_records(false);
			if(is_var_array($result))
			{
				$this->log_status('Basket confirm save', 'Email duplicate do not save to profile ['.$buyer_name.'],['.$buyer_email.'].', $data);
			}else{
				$data = array();
				$data["email"] = $buyer_email;
				
				$this->load->model($this->user_model,"user");
				$this->user->set_where(array("aid"=>getSessionUserAid()));
				$rs = $this->user->update_record($data);
				$this->log_status('Basket confirm save', 'Email save to profile ['.$buyer_name.'],['.$buyer_email.'].', $data);
			}
		}

		$buyer_detail = array();
		$buyer_detail["buyer_email"] = $buyer_email;
		$buyer_detail["buyer_name"] = $buyer_name;
		$buyer_detail["buyer_contact"] = $buyer_contact;
		$buyer_detail["buyer_address"] = $buyer_address;
		$buyer_detail["save_profile"] = $save_profile;
		$buyer_detail["remark"] = $remark;
		
		$this->session->set_userdata('buyerDetailSession',$buyer_detail);

		$master_payment_type = $this->data["master_payment_type"];
		$payment_type = $this->session->userdata('paymentTypeSession');
		if(is_blank($payment_type) || !in_array($payment_type, $master_payment_type)){
			$payment_type = $master_payment_type[0];
		}
		// echo "payment_type = $payment_type <BR/>";
		$currency = "";

		if($payment_type == "point"){
			$currency = "Point";
			$point_remain = 0;
			$point_remain = $this->user->get_user_point_remain(getSessionUserAid());
			$actual_grand_total = get_array_value($basket,"actual_grand_total","0");

			if($point_remain < $actual_grand_total ){
				redirect('my-cart/status/'.md5('lack-point'));
				return"";
			}
		}

		switch ($payment_type) {
			case 'paysbuy':
				$currency = "Baht";
				$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_paysbuy_form';
				break;
			case 'paypal':
				$currency = "Baht";
				$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_paypal_form';
				break;				
			case 'point':
				$currency = "Point";
				$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_point_form';
				break;				
			default:
				break;
		}

		$redeem_main_aid = get_array_value($basket,"redeem_main_aid","");
		$redeem_code = get_array_value($basket,"redeem_code","");

		$this->load->model($this->setting_running_model,"running");
		$cid = $this->running->get_latest_invoice_by_year(date('Y'));
		$order_main = array();
		$order_main["cid"] = $cid;
		$order_main["user_owner_aid"] = getUserOwnerAid($this);
		$order_main["user_aid"] = getSessionUserAid();
		$order_main["payment_type"] = $payment_type;
		$order_main["actual_unit"] = get_array_value($basket,"unit_grand_total","0");
		$order_main["actual_total"] = get_array_value($basket,"actual_total","0");
		$order_main["redeem_code"] = get_array_value($basket,"redeem_code","");
		$order_main["redeem_actual_discount"] = get_array_value($basket,"redeem_actual_discount","0");
		$order_main["after_redeem_grand_total"] = get_array_value($basket,"after_redeem_grand_total","0");
		$order_main["total_transport_fee"] = get_array_value($basket,"total_transport_fee","0");
		$order_main["actual_grand_total"] = get_array_value($basket,"actual_grand_total","0");
		$order_main["currency"] = $currency;
		$order_main["need_transport"] = $has_paper;
		
		$order_main["status"] = "1";
		$order_main["type"] = "2"; //1 = Buy Point, 2 = Buy Product
		$order_main["package_aid"] = "0";
		
		$order_main["buyer_name"] = $buyer_name;
		$order_main["buyer_email"] = $buyer_email;
		$order_main["buyer_contact"] = $buyer_contact;
		$order_main["buyer_address"] = $buyer_address;
		$order_main["remark"] = $remark;
		$order_main["channel"] = "1";
		
		$this->load->model($this->order_main_model,"order_main");
		$this->load->model($this->order_detail_model,"order_detail");				
		$this->order_main->set_trans_start();
		
		$aid = $this->order_main->insert_record($order_main);
		if(!$aid){
			$this->log_error('Basket confirm', 'Failed saving data to DB by ['.$buyer_name.'] and order_main_aid = ['.$aid.'].');
			$this->order_main->set_trans_rollback();
			$this->data["message"] = set_message_error(CONST_DEFAULT_MSG_ERROR_CONFIRM);
			$this->confirm($tmp);
			return "";
		}
		
		$chk = true;
		foreach($product_list as $item){
			$order_detail = array();
			$order_detail["order_main_aid"] = $aid;
			$order_detail["user_aid"] = getSessionUserAid();
			$order_detail["product_type_aid"] = get_array_value($item,"pta","");
			$order_detail["product_type_cid"] = get_array_value($item,"product_type_cid","");
			$order_detail["copy_aid"] = get_array_value($item,"aid","");
			$order_detail["parent_aid"] = get_array_value($item,"parent_aid","");
			$order_detail["parent_title"] = get_array_value($item,"parent_title","");
			$order_detail["cover_image"] = get_array_value($item,"cover_image_small","");
			$order_detail["need_transport"] = get_array_value($item,"need_transport","");
			$order_detail["unit"] = get_array_value($item,"unit","");

			$order_detail["price_per_unit"] = get_array_value($item,"actual_per_item","");
			$order_detail["price_total"] = get_array_value($item,"actual_total_per_item","");
			$order_detail["currency"] = $currency;
			
			$order_detail["status"] = "1";
			$order_detail["remark"] = "";
			
			$this->order_detail->insert_record($order_detail);
		}
		
		if(!$chk){
			$this->log_error('Basket confirm', 'Failed saving data to DB by ['.$buyer_name.'] and order_main_aid = ['.$aid.'].');
			$this->order_main->set_trans_rollback();
			$this->data["message"] = set_message_error(CONST_DEFAULT_MSG_ERROR_CONFIRM);
			$this->confirm($tmp);
			return "";
		}

		if(!is_blank($redeem_code)){
			$today_date = date('Y-m-d');
			$data = array();
			$data["redeem_main_aid"] = $redeem_main_aid;
			$data["redeem_detail_cid"] = $redeem_code;
			$data["order_main_aid"] = $aid;
			$data["status"] = "1"; //1=Active, 2=Inactive
			$data["user_aid"] = getSessionUserAid();
			$data["redeem_date"] = $today_date;
			$this->load->model($this->redeem_history_model,"redeem_history");
			$result = $this->redeem_history->insert_record($data);
			$this->session->set_userdata('redeemCodeSession','');
		}
		
		$this->order_main->set_trans_commit();
		$this->log_status('Basket confirm', 'Order aid = ['.$aid.']['.$cid.'] just coming. Status is waiting for payment.', $order_main);
		
		if($payment_type == "point"){
			$point_remain = 0;
			$point_remain = $this->user->get_user_point_remain(getSessionUserAid());
			$actual_grand_total = get_array_value($basket,"actual_grand_total","0");

			if($point_remain < $actual_grand_total ){
				redirect('my-cart/status/'.md5('lack-point'));
				return"";
			}

			$point_remain -= $actual_grand_total;
			$point_history = array();
			$point_history["user_aid"] = getSessionUserAid();
			$point_history["order_aid"] = $aid;
			$point_history["point_type"] = "2";
			$point_history["point"] = $actual_grand_total;
			$point_history["status"] = "1";

			$this->load->model($this->point_history_model, "point_history");
			$this->point_history->insert_record($point_history);

			$this->load->model($this->user_model, "user");
			$this->user->reduce_point_remain(getSessionUserAid(), $actual_grand_total);

			//update status to order_main
			$data = array();
			$data["status"] = "3"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected
			$data["transport_status"] = $has_paper;
			$this->load->model($this->order_main_model,"order_main");
			$this->order_main->set_where(array("aid"=>$aid));
			$result = $this->order_main->update_record($data);
		}

		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("aid"=>$aid));
		$this->data["order_result"] = $this->order_main->load_record(true);
		
		$this->session->set_userdata('basketSession','');
		$this->session->set_userdata('buyerDetailSession','');
		$this->session->set_userdata('transportTypeSession','');
		$this->session->set_userdata('paymentTypeSession','');

		$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
	}
	
	function confirm_status($type="",$order_main_cid=""){
		
		switch($type)
		{
			case md5('success') : 
				$this->data["status"] = 'success';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_success("การสั่งซื้อเสร็จสมบูรณ์ เลขที่อ้างอิงคือ ".$order_main_cid."<BR>หนังสือได้เข้าสู่ <a href='".BASE_URL."my-bookshelf'>My Bookshelf</a> แล้ว");
				break;
			case md5('order-not-found') : 
				$this->data["status"] = 'approve-fail';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("การสั่งซื้อเกิดข้อผิดพลาด เลขที่อ้างอิงคือ ".$order_main_cid."<BR>กรุณาติดต่อผู้ดูแลระบบ");
				break;
			case md5('approve-fail') : 
				$this->data["status"] = 'approve-fail';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("การสั่งซื้อเกิดข้อผิดพลาด เลขที่อ้างอิงคือ ".$order_main_cid."<BR>กรุณาติดต่อผู้ดูแลระบบ");
				break;
			case md5('fail') : 
				$this->data["status"] = 'error';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("การชำระเงินถูกปฎิเสธ เลขที่อ้างอิงคือ ".$order_main_cid."<BR>การสั่งซื้อถูกยกเลิก");
				break;
			case md5('process') : 
				$this->data["status"] = 'info';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_success("ใบสั่งซื้ออยู่ระหว่างการรอชำระเงิน เลขที่อ้างอิงคือ ".$order_main_cid);
				break;
			case md5('error') : 
				$this->data["status"] = 'info';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("เกิดข้อผิดพลาดไม่สามารถทำรายการ");
				break;
			default : 
				$this->data["message"] = set_message_error("กรุณาลองใหม่อีกครั้ง");
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_status';
		$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
	}
	
	function status($type="",$order_main_cid=""){
		
		switch($type)
		{
			case md5('has-change') : 
				$this->data["status"] = 'info';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error($this->lang->line('order_message_has_change'));
				break;
			case md5('lack-point') : 
				$this->data["status"] = 'info';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error($this->lang->line('order_message_lack_point'));
				break;
			default : 
				$this->data["message"] = set_message_error("กรุณาลองใหม่อีกครั้ง");
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_list';
		$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
	}
	
	function paysbuy_save_approve($result_full="",$order_result=""){
		//If success payment
		$order_main_cid = get_array_value($order_result,"cid","");
		$order_main_aid = get_array_value($order_result,"copy_aid","");
		$this->log_status('Paysbuy Feedback', 'Start!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].');
		
		$chk_status = "";
		
		$this->order_main->set_trans_start();
		//Step 1. update status to order_main
		$data = array();
		$data["status"] = "3"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected		
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$result = $this->order_main->update_record($data);
		if($result){
			$this->log_status('Paysbuy Feedback', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved[status = 3]. Success');
			
			//Step 2. add book to shelf
			$this->load->model($this->order_detail_model,"order_detail");
			$this->order_detail->set_where(array("order_main_aid"=>$order_main_aid));
			$result_detail = $this->order_detail->load_records(false);
			if(is_var_array($result_detail)){
				// $this->log_status('Paysbuy Feedback', 'Step 2. Order ['.$order_main_cid.']. Add point['.get_array_value($order_result,"point","0").'] to user['.get_array_value($order_result,"user_aid","").']. Success');
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
				<td class="header hright" width="100">Price (Baht)</td>
				</tr>
				';
				foreach($result_detail as $item){
					$i++;
					$issue_price_after_vat = get_array_value($item,"issue_price","0");
					$issue_price_before_vat = round(($issue_price_after_vat/1.07),2);
					$total_before_vat += $issue_price_before_vat;
					$vat += ($issue_price_after_vat-$issue_price_before_vat);
					//Add book to shelf
					$tmp = array();
					$tmp["issue_aid"] = get_array_value($item,"issue_aid","");
					$tmp["user_aid"] = get_array_value($item,"user_aid","");
					$tmp["status"] = '1';
					$tmp["is_read"] = '0';
					$this->load->model($this->shelf_model,"shelf");
					$result = $this->shelf->insert_or_update($tmp);
					if($result){
						$this->log_status('Paysbuy Feedback', 'Step 2. Add ['.get_array_value($item,"issue_fullname","").'] to shelf of ['.get_array_value($item,"user_aid","").']. Success');
					}else{
						$chk = false;
						$this->log_error('Paysbuy Feedback', 'Step 2. Add ['.get_array_value($item,"issue_fullname","").'] to shelf of ['.get_array_value($item,"user_aid","").']. Fail');
					}
					
					//Add issue download for report
					$tmp = array();
					$tmp["order_main_aid"] = $order_main_aid;
					$tmp["issue_aid"] = get_array_value($item,"issue_aid","");
					$tmp["user_aid"] = get_array_value($item,"user_aid","");
					$tmp["issue_price"] = get_array_value($item,"issue_price","");
					$tmp["status"] = '1';
					$tmp["channel"] = '1';
					$this->load->model($this->issue_download_model,"issue_download");
					$result = $this->issue_download->insert_or_update($tmp);
					if($result){
						$this->log_status('Paysbuy Feedback', 'Step 2.2 Add ['.get_array_value($item,"issue_fullname","").'] to issue_download of ['.get_array_value($item,"user_aid","").']. Success');
					}else{
						$chk = false;
						$this->log_error('Paysbuy Feedback', 'Step 2.2 Add ['.get_array_value($item,"issue_fullname","").'] to issue_download of ['.get_array_value($item,"user_aid","").']. Fail');
					}
					
					$order_table .= '
					<tr>
					<td>'.$i.'.</td>
					<td>'.get_array_value($item,"issue_fullname","N/A").'</td>
					<td class="hright">'.$issue_price_before_vat.'</td>
					</tr>
					';
				}
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
				
				if($chk){
					$this->log_status('Paysbuy Feedback', 'Step 2. All book saved to shelf of ['.get_array_value($item,"user_aid","").']. Success');
					//Step 3. add receipt
					$this->load->model($this->order_receipt_model,"order_receipt");
					$this->order_receipt->set_where(array("order_main_aid"=>$order_main_aid));
					$result = $this->order_receipt->load_records(false);
					if(is_var_array($result)){
						$this->log_status('Paysbuy Feedback', 'Step 3. Order ['.$order_main_cid.']. has receipt already. DO not add new receipt');
						$chk_status = "success";
						$this->order_main->set_trans_rollback();
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
							$this->log_status('Paysbuy Feedback', 'Step 3. Order ['.$order_main_cid.']. Add receipt. Success');
							$chk_status = "success";
							$this->order_main->set_trans_commit();
							
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
								$this->log_status('Paysbuy Feedback', 'Receipt no. ['.$cid.'] : Email sent to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'].');
								$this->log_debug('Paysbuy Feedback : Email', 'Send mail to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'] success ['.$body.']');
							}else{
								$this->log_status('Paysbuy Feedback', 'Receipt no. ['.$cid.'] : Fail to sent email to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'].');
								$this->log_debug('Paysbuy Feedback : Email', 'Send mail to ['.get_array_value($order_result,"buyer_name","").'],['.get_array_value($order_result,"buyer_email","").'] fail ['.$body.']');
							}
							
						}else{
							$this->log_error('Paysbuy Feedback', 'Step 3. Order ['.$order_main_cid.']. Add receipt. Fail');
							$chk_status = "approve-fail";
							$this->order_main->set_trans_rollback();
						}
					}
				}else{
					$this->log_error('Paysbuy Feedback', 'Step 2. Some books can not add to shelf of ['.get_array_value($item,"user_aid","").']. Fail');
					$this->order_main->set_trans_rollback();
					$chk_status = "approve-fail";
				}
			}else{
				$this->log_error('Paysbuy Feedback', 'Step 2. Order ['.$order_main_cid.']. Add book to shelf for user['.get_array_value($order_result,"user_aid","").']. Fail : Not found any book detail.');
				$this->order_main->set_trans_rollback();
				$chk_status = "approve-fail";
			}
		}else{
			$this->log_error('Paysbuy Feedback', ' Step 1. Order ['.$order_main_cid.']. Update status to apporved[status = 3]. Fail');
			$this->order_main->set_trans_rollback();
			$chk_status = "approve-fail";
		}
		$this->log_status('Paysbuy Feedback', ' End!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].');
		return $chk_status;
	}
	
	function ajax_update_status($sid=""){
		$order_main_cid = $this->input->get_post('order_main_cid');
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$order_result = $this->order_main->load_record(true);
		if(!is_var_array($order_result)){
			$this->log_error('Payment Feedback', 'Order not found ['.$order_main_cid.']. Do nothing.');
			$msg = set_message_error('ไม่พบใบสั่งซื้อ');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return"";
		}
		
		$order_status = get_array_value($order_result,"status","");
		switch ($order_status){
			case "1" : 
				$msg = set_message_success('ใบสั่งซื้ออยู่ระหว่างการรอชำระเงิน เลขที่อ้างอิงคือ '.$order_main_cid);
				$result_obj = array("status" => 'success',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			case "2" : 
				$msg = set_message_success('ใบสั่งซื้ออยู่ระหว่างการรอชำระเงิน เลขที่อ้างอิงคือ '.$order_main_cid);
				$result_obj = array("status" => 'success',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			case "3" : 
				$msg = set_message_success('การสั่งซื้อเสร็จสมบูรณ์ เลขที่อ้างอิงคือ '.$order_main_cid.'');
				$result_obj = array("status" => 'success',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			case "4" : 
				$msg = set_message_error('การชำระเงินถูกปฎิเสธ เลขที่อ้างอิงคือ '.$order_main_cid.'<BR>การสั่งซื้อถูกยกเลิก');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			default : 
				$msg = set_message_error('ไม่พบใบสั่งซื้อ');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
		}
	}


}

?>