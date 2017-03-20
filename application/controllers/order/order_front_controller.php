<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class Order_controller extends Initcontroller {

	function __construct()	{
		parent::__construct();
		for_general_admin_or_higher();
		$this->data["mode"] = "backend";
		define('thisAdminTabMenu','order');
		define('thisFrontTabMenu','order');
		@define("folderName","order/");
		$this->order_main_model = "Order_main_model";
		$this->order_detail_model = "Order_detail_model";
		$this->master_status_order_model = "Master_status_order_model";
		$this->bank_account_model = "Setting_bank_account_model";
		
		$this->load->model($this->master_status_order_model,"status_order");
		$this->data["master_status_order"] = $this->status_order->load_all_status_orders();
	}
	
	function index(){
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction","show");
		for_general_admin_or_higher();
		$this->data["name"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = "/order/order_list";
		$this->data["header_name"] = "Order Management";
		$this->data["message"] = $this->get_init_message_status($msg);
		$this->load->view('tpl_admin', $this->data);
	}
	
	function form(){
		for_general_admin_or_higher();
		$this->data["name"] = DEFAULT_TITLE;
		$this->data['view_the_content'] = "order/order_form";
		$this->load->view('tpl_admin',$this->data);
	}
	
	function add(){
		for_general_admin_or_higher();
		@define("thisAction","add");
		$this->data['command'] = "_insert";
		$this->data["header_name"] = "Order Management : Add New Order";
		$this->form();
	}
	
	function edit($aid=""){
		for_general_admin_or_higher();
		@define("thisAction","edit");
		$this->data['command'] = "_update";
		$this->data["header_name"] = "Order Management : Edit Order";
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("aid"=>$aid));
		$item_detail = $this->order_main->load_record(true);
		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			
			$this->load->model($this->order_detail_model,"order_detail");
			$this->order_detail->set_where(array("order_main_aid"=>$aid));
			$item_detail = $this->order_detail->load_record(true);
			$this->data["order_detail"] = $item_detail;
			$this->form();
		}else{
			$this->data["message"] = set_message_error("Cannot find the specific order.");
			$this->data["js_code"] = "$('#name').focus();";
			$this->show();
			return "";
		}
	}
	
	function save(){
		for_general_admin_or_higher();
		@define("thisAction","save");
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		$aid = "";
		
		if($command == "_update"){
			$this->data["header_name"] = "Order Management : Edit Order";
		}else{
			$this->data["header_name"] = "Order Management : Add New Order";
		}
		
		$data["cid"] = $this->input->get_post('cid');
		$data["user_aid"] = $this->input->get_post('name');
		$data["total_unit"] = $this->input->get_post('is_new');
		$data["total_price"] = $this->input->get_post('is_hot');
		$data["transport_type"] = $this->input->get_post('order_detail_aid');
		$data["transport_fee"] = $this->input->get_post('order_main_aid');
		$data["buyer_name"] = $this->input->get_post('cost_price');
		$data["buyer_address"] = $this->input->get_post('sale_price');
		$data["buyer_contact"] = $this->input->get_post('special_price');
		$data["buyer_email"] = $this->input->get_post('unit');
		$data["remark"] = $this->input->get_post('weight');
		$data["status"] = $this->input->get_post('status');
		
		$this->load->model($this->order_main_model,"order_main");
		
		//In update mode, order aid must be exits.
		if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->order_main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->order_main->load_record(false);
			if(!is_var_array($itemResult)){
				$this->data["message"] = set_message_error("This order is not exits anymore.");
				$this->data["js_code"] = "$('#name').focus();";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
		}
		
		if($command == "_insert"){
			$aid = $this->order_main->insert_record($data);
			if ($aid > 0){
				$this->log_status('Admin : Order', 'Add new order ['.$data["name"].'] success.');
				$this->data["message"] = set_message_success("Data has been saved.");
				if($save_option){
					$this->add();
				}else{
					redirect('admin/order/S'.md5('success'));
				}
				return "";
			}else{
				$this->log_status('Admin : Order', 'Add new order ['.$data["buyer_name"].'] fail => sql insert error');
				$this->data["message"] = set_message_error("Sorry, the system can not save data now. Please try again or contact your administrator.");
				$this->data["js_code"] = "$('#name').focus();";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else if($command == "_update"){
			$data_where["aid"] = $aid;
			$this->order_main->set_where($data_where);
			$rs = $this->order_main->update_record($data, $data_where);
			if($rs){
				$this->log_status('Admin : Order', 'Update order ['.$data["name"].'] success.');
				$this->data["message"] = set_message_success("Data has been saved.");
				if($save_option){
					$this->edit($aid);
				}else{
					redirect('admin/order/S'.md5('success'));
				}
				return "";
			}else{
				$this->log_status('Admin : Order', 'Update order ['.$data["name"].'] fail => sql update error.');
				$this->data["message"] = set_message_error("Sorry, the system can not save data now. Please try again or contact your administrator.");
				$this->data["js_code"] = "$('#name').focus();";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
		}
		$this->edit($aid);
	}
	
	function delete(){
		for_root_admin_or_higher();
		@define("thisAction","delete");
		$aid = $this->input->get_post('aid_selected');
		$this_name = "";

		$this->load->model($this->order_model,"order");
		$this->order->set_where(array("aid"=>$aid));
		$itemResult = $this->order->load_records(true);
		if(is_var_array($itemResult)){
			$this_name = get_array_value($itemResult[0], 'name', $aid);
		}
		$this->order->set_where(array("aid"=>$aid));
		$rs = $this->order->delete_records();
		if($rs){
			$this->log_status('Admin', 'Delete order ['.$this_name.'].');
			redirect('admin/order');
		}
	}

	function deletelist(){
		for_root_admin_or_higher();
		@define("thisAction","deletelist");
		$order_aid = $this->input->get_post('order_aid');

		$this->load->model($this->order_model,"order");
		$this->order->set_where_in(array("order_aid"=>$order_aid));
		$this->order->delete_records();
		if (!is_blank($order_aid)) $this->log_status('Admin', 'Delete order aid = '.$order_aid.'.');
		redirect('admin/order');
	}

	function ajex_set_status(){
		$aid = $this->input->get_post('aid_selected');
		$status = $this->input->get_post('status');

		$this->load->model($this->order_model,"order");
		$this->order->set_where(array("aid"=>$aid));
		$itemResult = $this->order->load_records(true);
		if(!is_var_array($itemResult)){
			echo "Error occured : Can not find this order.";
		}
		$this_name = get_array_value($itemResult[0], 'name', $aid);
		
		$this->order->set_where(array("aid"=>$aid));
		$rs = $this->order->set_status($status);
		
		if ($rs){
			if($status == 1){
				$this->log_status('Admin : Order', 'Active order ['.$this_name.'].');
				echo "success";
				return "";
			}else{
				$this->log_status('Admin : Order', 'Inctive order ['.$this_name.'].');
				echo "success";
				return "";
			}
		}else{
			echo "Error occured : Can not save data.";
		}
	}
	
	function ajex_delete_one(){
		$aid = $this->input->get_post('aid_selected');

		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("aid"=>$aid));
		$itemResult = $this->order_main->load_records(true);
		if(!is_var_array($itemResult)){
			echo "Error occured : Can not find this order.";
		}
		$this_name = get_array_value($itemResult[0], 'aid', $aid);
		
		$this->order_main->set_where(array("aid"=>$aid));
		$rs = $this->order_main->delete_records();
		
		$this->load->model($this->order_detail_model,"order_detail");
		$this->order_detail->set_where(array("order_main_aid"=>$aid));
		$rs = $this->order_detail->delete_records();
		
		if ($rs){
			$this->log_status('Admin : Order', 'Delete order no. ['.$this_name.'].');
			echo "success";
			return "";
		}else{
			echo "Error occured : Can not delete data.";
		}
	}
	
	function ajax_get_order_list($sid){
		$this->load->model($this->order_main_model,"order_main");
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->input->get_post('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->order_main->set_or_like($data_where);
		
		$search_order_detail = $this->input->get_post('search_order_detail');
		$data_where = "";
		if(is_var_array($search_order_detail))
		foreach($search_order_detail as $item){
			$data_where["order_main_aid"][] = $item;
			$data_search["search_order_detail"][] = $item;
		}
		$this->order_main->set_where_in($data_where);
				
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->order_main->set_where_in($data_where);
		
		$search_option = $this->input->get_post('search_option');
		$data_where = "";
		if(is_var_array($search_option))
		foreach($search_option as $item){
			$data_where[$item] = "1";
			$data_search["search_option"][] = $item;
		}
		// echo print_r($data_where);
		$this->order_main->set_where($data_where);
		
		$created_date_from = $this->input->get_post('created_date_from');
		$created_date_to = $this->input->get_post('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->order_main->set_where('created_date >=', get_datetime_pattern("db_format",$created_date_from,""));
		}
		if(!is_blank($created_date_to)){
			$this->order_main->set_where('created_date <=', get_datetime_pattern("db_format",$created_date_to,""));
		}		
		
		$record_per_page = CONST_DEFAULT_RECORD_PER_PAGE;
		$search_record_per_page = $this->input->get_post('search_record_per_page');
		if(is_blank($search_record_per_page)){
			$search_record_per_page = $record_per_page;
		}
		$total_page = 1;
		
		$total_record = $this->order_main->count_records();
		if($total_record > 0){
			$total_page = ceil($total_record/$search_record_per_page);
		}
		
		$page_selected = $this->input->get_post('page_selected');
		if(is_blank($page_selected)  || $page_selected <= 0) $page_selected = 1;
		if($page_selected > $total_page) $page_selected = $total_page;
		$start_record = ($page_selected-1)*$search_record_per_page;
		
		$search_order_by = $this->input->get_post('search_order_by');
		if(is_blank($search_order_by)){
			$order_by = "aid";
			$order_by_option = "desc";
		}else{
			list($order_by, $order_by_option) = split(" ", $search_order_by, 2);
		}
		$data_search["search_order_by"] = $search_order_by;
		$sorting = array("order_by"=>$order_by , "order_by_option"=>$order_by_option);
		
		$this->order_main->set_order_by($order_by." ".$order_by_option);
		$this->order_main->set_limit($start_record,$search_record_per_page);
		$result_list = $this->order_main->load_records(true);
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		
		$optional = "";
		$optional["search_record_per_page"] = $search_record_per_page;
		$optional["total_record"] = $total_record;
		$optional["page_selected"] = $page_selected;
		$optional["total_page"] = $total_page;
		$optional["start_record"] = $start_record;
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>"1","title_show"=>"Order no.","field_order"=>"aid","col_show"=>"cid","name_class"=>"w30 hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"1","title_show"=>"Buyer Name","field_order"=>"buyer_name","col_show"=>"buyer_name","name_class"=>"w60 hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"1","title_show"=>"Buyer Contact","field_order"=>"buyer_contact","col_show"=>"buyer_contact","name_class"=>"w50 hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"1","title_show"=>"Total Price","field_order"=>"all_price_summary_change","col_show"=>"all_price_summary_change_show","name_class"=>"w100 hcenter","result_class"=>"hright");
		$header_list[] = array("sort_able"=>"1","title_show"=>"Order Date","field_order"=>"created_date","col_show"=>"created_date_txt","name_class"=>"w50 hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"1","title_show"=>"Status","field_order"=>"status","col_show"=>"status_show","name_class"=>"w60 hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"0","title_show"=>"&nbsp;","field_order"=>"","col_show"=>"action","name_class"=>"w30 hcenter","result_class"=>"hleft");
		
		if(is_var_array($result_list)){
			$result_obj = array("status" => "success", "sorting" => $sorting, "optional"=>$optional, "header_list" => $header_list, "result" => $result_list);
			echo json_encode($result_obj);
			return"";
		}else{
			$result_obj = array("status" => "warning","msg" => "No record found.");
			echo json_encode($result_obj);
			return"";
		}
	}
	
	function manage_column_detail($result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$item['cid'] =  '<a href="'.site_url('admin/order/detail/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"aid","-").'</a>';
			$order_status = get_array_value($item,"status","0");
			$item['status_show'] = '<div class="order_status" style="background-color:'.get_array_value($item,"status_color").'">'.get_array_value($item,"status_name","-").'</div>';
			
			$item['all_price_summary_show'] = get_price_format(get_array_value($item,"all_price_summary","0")).'.-';
			$item['all_price_summary_change_show'] = get_price_format(get_array_value($item,"all_price_summary_change","0")).'.-';
			$item['action'] = '&nbsp;<img src="'.ICON_PATH.'delete.png" class="button" name="Click to \'Delete\' this order" onclick="processDelete(\''.get_array_value($item,"aid","").'\', \''.get_array_value($item,"name","").'\')">';
			$result[] = $item;
		}
		
		return $result;
		
	
	}
	
	function detail($aid){
		@define("thisAction","detail");
		$this->data["name"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = "/order/order_detail";
		
		$this->db->flush_cache();
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("aid"=>$aid));
		$item_result = $this->order_main->load_record(true);
		if(is_var_array($item_result)){
			$this->data["item_detail"] = $item_result;
			
			$this->db->flush_cache();
			$this->load->model($this->order_detail_model,"order_detail");
			$this->order_detail->set_where(array("order_main_aid"=>$aid));
			$this->data["order_detail"] = $this->order_detail->load_records(true);
			
			$this->load->view('tpl_admin', $this->data);
		}else{
			$this->show('data-notfound');
		}
		
	
	
	}	
	
	function ajax_get_order_detail($sid="",$order_main_aid="")	{		
		echo json_encode($this->get_order_detail($order_main_aid));
	}
	
	function ajax_recal_order_detail($sid="",$order_main_aid="")	{		

		$unit = $this->input->get_post('unit');
		// print_r($unit);
		// echo "<BR>";
		if(!is_var_array($unit)){
			echo json_encode(set_array_message_error(set_message_error('Unit is null.','result-msg-box-sub')));
			return "";
		}
		
		$price = $this->input->get_post('price');
		// print_r($price);
		// echo "<BR>";
		if(!is_var_array($price)){
			echo json_encode(set_array_message_error(set_message_error('Price is null.','result-msg-box-sub')));
			return "";
		}
		
		$unit_change = $this->input->get_post('unit_change');
		// print_r($unit_change);
		// echo "<BR>";
		if(!is_var_array($unit_change)){
			echo json_encode(set_array_message_error(set_message_error('Unit change is null.','result-msg-box-sub')));
			return "";
		}
		
		$remark = $this->input->get_post('remark');
		// print_r($remark);
		// echo "<BR>";
		if(count($unit) != count($price) && count($unit) != count($unit_change) && count($unit) != count($remark)){
			echo json_encode(set_array_message_error(set_message_error('Remark and Unit does not match.','result-msg-box-sub')));
			return "";
		}
		
		$arr = array();
		$i=0;
		foreach($unit as $item){
			$tmp = split(",", $item);
			$product_aid = get_array_value($tmp,"0","0");
			$product_unit = get_array_value($tmp,"1","0");
			if(is_number_no_zero($product_aid) && is_number_no_zero($product_unit)){
				$tmp2 = array();
				$tmp2["aid"] = $product_aid;
				$tmp2["unit"] = $product_unit;
				$tmp2["price"] = $price[$i];
				$tmp2["unit_change"] = $unit_change[$i];
				$tmp2["remark"] = $remark[$i];
				$arr[] = $tmp2;
			}
			$i++;
		}
		
		if(!is_var_array($arr)){
			echo json_encode(set_array_message_error(set_message_error('Array is null.','result-msg-box-sub')));
			return "";
		}
		
		$order_main_aid = $this->input->get_post('order_main_aid');
		$transport_type_change = $this->input->get_post('transport_type_change');
		$transport_fee_change = $this->input->get_post('transport_fee_change');
		$remark_change = $this->input->get_post('remark_change');
		$package_code = $this->input->get_post('package_code');
		
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("aid"=>$order_main_aid));
		$result = $this->order_main->load_record(false);
		
		if(!is_var_array($result)){
			echo json_encode(set_array_message_error(set_message_error('Order main does not found.','result-msg-box-sub')));
			return "";
		}
		
		$this->load->model($this->order_detail_model,"order_detail");

		$is_change = false;
		$all_unit_total_change = 0;
		$all_price_total_change = 0;
		$all_price_summary_change = 0;
		foreach($arr as $item){
			$aid = get_array_value($item,"aid","");
			$unit = get_array_value($item,"unit","");
			$price = get_array_value($item,"price","");
			$unit_change = get_array_value($item,"unit_change","");
			$remark = get_array_value($item,"remark","");
			
			
			$data = array();
			$data["remark"] = $remark;
			if($unit != $unit_change){
				$is_change = true;
				$data["is_change"] = "1";
				if($unit_change == 0 && ( is_blank($remark) || $remark == CONST_DEFAULT_MSG_OUT_OF_STOCK) ){
					$data["remark"] = CONST_DEFAULT_MSG_OUT_OF_STOCK_ALL;
				}
				if($unit_change > 0 && ( is_blank($remark) || $remark == CONST_DEFAULT_MSG_OUT_OF_STOCK_ALL) ){
					$data["remark"] = CONST_DEFAULT_MSG_OUT_OF_STOCK;
				}
				
			}else{
				$data["is_change"] = "0";
				if($remark == CONST_DEFAULT_MSG_OUT_OF_STOCK_ALL || $remark == CONST_DEFAULT_MSG_OUT_OF_STOCK){
					$data["remark"] = '';
				}
			}

			$data["product_unit_change"] = $unit_change;
			$data["product_price_total_change"] = $unit_change*$price;
			
			$all_unit_total_change += $unit;
			$all_price_total_change += ($unit_change*$price);
			
			$this->db->flush_cache();
			$data_where = array();
			$data_where["order_main_aid"] = $order_main_aid;
			$data_where["product_aid"] = $aid;
			$this->order_detail->set_where($data_where);
			$rs = $this->order_detail->update_record($data, $data_where);
			// echo "<br>sql : ".$this->db->last_query()."<br>";
		
		}
		$all_price_summary_change = $all_price_total_change + $transport_fee_change;
		
		$data_where = array();
		$data = array();
		$data["all_unit_total_change"] = $all_unit_total_change;
		$data["all_price_total_change"] = $all_price_total_change;
		$data["all_price_summary_change"] = $all_price_summary_change;
		$data["transport_type_change"] = $transport_type_change;
		$data["transport_fee_change"] = $transport_fee_change;
		$data["remark_change"] = $remark_change;
		$data["package_code"] = $package_code;
		$data["is_change"] = ($is_change) ? "1" : "0";
		$status = $this->input->get_post('status');
		$data["status"] = $status;
		
		$this->db->flush_cache();
		$data_where["aid"] = $order_main_aid;
		$this->order_main->set_where($data_where);
		$rs = $this->order_main->update_record($data, $data_where);
		// echo "<br>sql : ".$this->db->last_query()."<br>";

		echo json_encode(set_array_message_success(''));
	}
	
	function get_order_detail($order_main_aid="")	{
		if(is_blank($order_main_aid)){
			return "";
		}
		
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("aid"=>$order_main_aid));
		$order_main_result = $this->order_main->load_record(true);
		
		if(!is_var_array($order_main_result)){
			return "";
		}
		
		$this->load->model($this->order_detail_model,"order_detail");
		$this->order_detail->set_where(array("order_main_aid"=>$order_main_aid));
		$order_main_result["order_detail_result"] = $this->order_detail->load_records(true);

		$this->load->model($this->master_status_order_model,"status_order");
		$order_main_result["master_status_order"] = $this->status_order->load_records(false);

		return $order_main_result;
	}
	
	function ajax_send_email_order($sid=""){
		$order_main_aid = $this->input->get_post('order_main_aid');
		if(is_blank($order_main_aid)){
			echo json_encode(set_array_message_error(set_message_error('Order main aid is null.','')));
			return "";
		}
		
		$body = "";
		$subject = "";
		$result = $this->get_order_detail($order_main_aid);
		$order_detail = get_array_value($result,"order_detail_result","");
		$order_status = get_array_value($result,"status","");
		$order_status_name = get_array_value($result,"status_name","");
		$buyer_email = get_array_value($result,"buyer_email","");
		$buyer_name = get_array_value($result,"buyer_name","");
		$package_code = get_array_value($result,"package_code","");
		
		if(!is_var_array($result) || !is_var_array($order_detail) || is_blank($order_status)){
			echo json_encode(set_array_message_error(set_message_error('Order not found','')));
			return "";
		}
		
		if(is_blank($buyer_email)){
			echo json_encode(set_array_message_error(set_message_error('Email not found.','')));
			return "";
		}
		
		switch ($order_status){
			case "2" : $body = $this->_set_mail_confirm_order($result);
							$subject = MAIL_SUBJECT_CONFIRM_ORDER;
							break;
			case "4" : $body = MAIL_CONTENT_CONFIRM_TRANSFER;
							$subject = MAIL_SUBJECT_CONFIRM_TRANSFER;
							break;
			case "5" : $body = MAIL_CONTENT_CONFIRM_DELIVERY;
							$subject = MAIL_SUBJECT_CONFIRM_DELIVERY;
							break;
			case "7" : $body = MAIL_CONTENT_CONFIRM_CANCEL;
							$subject = MAIL_SUBJECT_CONFIRM_CANCEL;
							break;
			default : $body = ""; break;
		}
		// echo $body;
		
		if(is_blank($body)){
			echo json_encode(set_array_message_error(set_message_error('No email found for this step.','')));
			return "";
		}
		
		$body = eregi_replace("[\]",'',$body);
		$body = str_replace("{order_aid}", $order_main_aid, $body);
		$body = str_replace("{name}", $buyer_name, $body);
		
		if($status == "5" && !is_blank($package_code)){
			$body = str_replace("{package_code}", '<tr><td>เลขที่พัศดุของคุณคือ '.$package_code.'</td></tr>', $body);
		}else{
			$body = str_replace("{package_code}", '', $body);
		}
		
		$subject = eregi_replace("[\]",'',$subject);
		$subject = str_replace("{order_aid}", $order_main_aid, $subject);

		$this->load->library('email');
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;

		$this->email->initialize($config); 
		$this->email->set_newline("\r\n");
		$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
		$this->email->to($buyer_email.','.ADMIN_EMAIL);

		$this->email->subject($subject);
		$this->email->message($body);
		// echo $this->email->print_debugger();
		if(@$this->email->send()){
			$this->log_status('Order confirm', 'Order no. ['.$order_main_aid.'] : Status = '.$order_status_name.' : Email sent to admin and ['.$buyer_name.'],['.$buyer_email.'].');
			echo json_encode(set_array_message_success(set_message_success('Email sent.','')));
		}else{
			$this->log_status('Order confirm', 'Order no. ['.$order_main_aid.'] : Status = '.$order_status_name.' : Fail to sent email to admin and ['.$buyer_name.'],['.$buyer_email.'].');
			echo json_encode(set_array_message_error(set_message_error('Can not send email right now. Please contact administrator.','')));
			return "";
		}
	}
	
	
	function _set_mail_confirm_order($result){
		$order_detail = get_array_value($result,"order_detail_result","");
		
		$this->load->model($this->bank_account_model,"bank_account");
		$master_bank_account = $this->bank_account->load_records_by_status(true);
	
		$txt = MAIL_HEADER;
		$txt .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
		$txt .= '<tr><td>สวัสดีค่ะ <strong>คุณ'.get_array_value($result,"buyer_name","").'</strong></td></tr>';
		$txt .= '<tr><td>&nbsp;</td></tr>';
		$txt .= '<tr><td>ขอบคุณที่ทำการสั่งสินค้าจาก www.cotton4quilt.com ค่ะ</td></tr>';
		$txt .= '<tr><td>&nbsp;</td></tr>';
		
		$txt .= '<tr><td align="center" class="header">รายการสั่งซื้อ</td></tr>';
		$txt .= '<tr><td>';
		$txt .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
		$txt .= '<tr valign="top">';
		$txt .= '<td width="40%"><strong>เลขที่ใบสั่งซื้อ : </strong>'.get_array_value($result,"aid","");
		$txt .= '<BR><strong>วันที่สั่งซื้อ : </strong>'.get_array_value($result,"created_date_txt","");
		$txt .= '<BR><strong>สถานะใบสั่งซื้อ : </strong>'.get_array_value($result,"status_name","");
		$txt .= '<BR><strong>หมายเหตุจากลูกค้า : </strong>'.get_array_value($result,"remark","");
		$txt .= '</td>';
		$txt .= '<td width="60%"><strong>ชื่อลูกค้า : </strong>'.get_array_value($result,"buyer_name","");
		$txt .= '<BR><strong>เบอร์ติดต่อ : </strong>'.get_array_value($result,"buyer_contact","");
		$txt .= '<BR><strong>อีเมลล์ : </strong>'.get_array_value($result,"buyer_email","");
		$txt .= '<BR><strong>ที่อยู่จัดส่งสินค้า : </strong>'.get_array_value($result,"buyer_address","");
		$txt .= '</td>';
		$txt .= '</tr>';
		
		$txt .= '</table>';
		$txt .= '</td></tr>';
		
		$txt .= '<tr><td>&nbsp;</td></tr>';
		
		$txt .= '<tr><td>';
		$txt .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
		$txt .= '<tr>';
		$txt .= '<td class="linedown" width="30"><strong>ลำดับ</strong></td>';
		$txt .= '<td class="linedown" width="50">&nbsp;</td>';
		$txt .= '<td class="linedown" width=""><strong>สินค้า</strong></td>';
		$txt .= '<td class="linedown hright" width="80"><strong>ราคา</strong></td>';
		$txt .= '<td class="linedown hright" width="80"><strong>จำนวนที่สั่ง</strong></td>';
		$txt .= '<td class="linedown hright" width="80"><strong>จำนวนจัดส่ง</strong></td>';
		$txt .= '<td class="linedown hright" width="60"><strong>ราคารวม</strong></td>';
		$txt .= '<td class="linedown" width=""><strong>หมายเหตุ</strong></td>';
		$txt .= '</tr>';
		$i=0;
		foreach($order_detail as $item){
			$i++;
			$txt .= '<tr>';
			$txt .= '<td class="linedown">'.$i.'.</td>';
			$txt .= '<td class="linedown"><img src="'.get_array_value($item,"product_img","").'" /></td>';
			$txt .= '<td class="linedown">'.get_array_value($item,"product_fullname","").'</td>';
			$txt .= '<td class="linedown hright">'.get_array_value($item,"product_price_show","").'</td>';
			$txt .= '<td class="linedown hright">'.get_array_value($item,"product_unit_show","").'</td>';
			$txt .= '<td class="linedown hright">'.get_array_value($item,"product_unit_change_show","").'</td>';
			$txt .= '<td class="linedown hright">'.get_array_value($item,"product_price_total_change_show","").'</td>';
			$txt .= '<td class="linedown">'.get_array_value($item,"remark","").'</td>';
			$txt .= '</tr>';
		}
		
		$txt .= '<tr>';
		$txt .= '<td class="linedown hright" colspan="6">รวม</td>';
		$txt .= '<td class="linedown hright">'.get_array_value($result,"all_price_total_change_show","").'</td>';
		$txt .= '<td class="linedown" colspan="">&nbsp;</td>';
		$txt .= '</tr>';
		
		$txt .= '<tr>';
		$txt .= '<td class="linedown hright" colspan="6">ค่าจัดส่ง ('.get_array_value($result,"transport_type_change_show","").')</td>';
		$txt .= '<td class="linedown hright">'.get_array_value($result,"transport_fee_change","").'</td>';
		$txt .= '<td class="linedown" colspan="">&nbsp;</td>';
		$txt .= '</tr>';
		
		$txt .= '<tr>';
		$txt .= '<td class="linedown hright" colspan="6"><strong>รวมทั้งสิ้น</strong></td>';
		$txt .= '<td class="linedown hright"><strong>'.get_array_value($result,"all_price_summary_change_show","").'</strong></td>';
		$txt .= '<td class="linedown" colspan="">&nbsp;</td>';
		$txt .= '</tr>';
		
		$txt .= '</table>';
		$txt .= '</td></tr>';
		
		
		if(!is_blank(get_array_value($result,"remark_change",""))){
		$txt .= '<tr><td><font color="red">หมายเหตุ : '.get_array_value($result,"remark_change","").'</font></td></tr>';
		}
		$txt .= '<tr><td>&nbsp;</td></tr>';
		$txt .= '<tr><td>กรุณาโอนเงินจำนวน <strong>'.get_array_value($result,"all_price_summary_change_show","").'</strong> บาท มาที่</td></tr>';
		
		if(is_var_array($master_bank_account)) {
			foreach($master_bank_account as $item){
				$txt .= '<tr><td><font color="#082E98">';
				$txt .= '<span class=""><strong>ธนาคาร'.get_array_value($item,"bank_name","").'</strong></span>&nbsp;&nbsp;';
				$txt .= '<span class="">เลขที่ <strong>'.get_array_value($item,"number","").'</strong></span><BR>';
				$txt .= '<span class="">ชื่อบัญชี <strong>'.get_array_value($item,"name","").'</strong></span><BR>';
				$txt .= '<span class="">สาขา <strong>'.get_array_value($item,"branch","").'</strong></span><BR>';
				$txt .= '</font></td></tr>';
			}
		}
		
		$txt .= '<tr><td>&nbsp;</td></tr>';
		$txt .= '<tr><td>หลังจากชำระเงินเรียบร้อยแล้ว ให้เก็บหลักฐานการชำระเงินไว้ด้วยนะคะ แล้วสามารถเลือกแจ้งโอนได้ตามช่องทางใดทางหนึ่งตามนี้ค่ะ</td></tr>';
		$txt .= '<tr><td><strong>1. แจ้งโอนผ่านเวปไซต์</strong> '.anchor("inform", "คลิกที่นี่", array('class' => '')).'</td></tr>';
		$txt .= '<tr><td><strong>2. แจ้งโอนผ่านโทรศัพท์ หรือ SMS</strong> ได้ที่ 087-917-0505  (หญิง) หรือ 081-494-1960  (หน่อย)<BR>';
		$txt .= 'กรุณาระบุ เลขที่ใบสั่งซื้อ จำนวนเงินที่โอน วันและเวลาโดยประมาณด้วยนะคะ เพื่อความรวดเร็วในการตรวจสอบค่ะ<BR>';
		$txt .= '<font color="red">กรณีแจ้งโอนทางโทรศัพท์ จะรับแจ้งในช่วงเวลา 9 โมงเช้า ถึง 6 โมงเย็นเท่านั้นค่ะ</font></td></tr>';
		$txt .= '<tr><td><strong>3. แจ้งโอนผ่านอีเมลล์</strong> <a href="mailto:cotton4quilt@gmail.com">cotton4quilt@gmail.com</a><BR>';
		$txt .= 'กรุณาระบุ ชื่อ-นามสกุล เลขที่ใบสั่งซื้อ จำนวนเงินที่โอน วันและเวลาโดยประมาณด้วยนะคะ เพื่อความรวดเร็วในการตรวจสอบค่ะ</a></td></tr>';
		
		$txt .= '<tr><td>&nbsp;</td></tr>';
		$txt .= '<tr><td>หากมีปัญหาเกิดขึ้นระหว่างการใช้งาน กรุณาติดต่อ <a href="mailto:cotton4quilt@gmail.com">cotton4quilt@gmail.com</a> ค่ะ</td></tr>';
		$txt .= '<tr><td>ขอบคุณค่ะ<BR>www.cotton4quilt.com</td></tr>';
		$txt .= '</table>';
		
		$txt .= MAIL_FOOTER;
		return $txt;
	}
	
	
}

?>