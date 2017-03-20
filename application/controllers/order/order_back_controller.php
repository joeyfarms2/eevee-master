<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Order_back_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();
		for_owner_admin_or_higher();
		$this->data["mode"] = "backend";

		if(CONST_HAS_BASKET != '1' && CONST_HAS_POINT != '1'){
			redirect('admin');
		}
		
		define("thisAdminTabMenu",'order');
		define("thisAdminSubMenu",'order');
		@define("folderName",'order/order_back/order');
		
		define("TXT_TITLE",'Order management');
		define("TXT_INSERT_TITLE",'Order management : Add new order');
		define("TXT_UPDATE_TITLE",'Order management : Edit order');
				
		$this->main_model = 'Order_main_model';

		$this->order_detail_model = "Order_detail_model";
		$this->package_point_model = "Package_point_model";
				
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/'. folderName . '/order_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/'. folderName . '/order_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;

		$this->session->set_userdata('orderBackDataSearchSession','');

		$this->data["js_code"] = "";
		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$item_detail = $this->main->load_record(true);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"cid","");

			$package_point_aid = get_array_value($item_detail,"package_aid","0");
			$package_point_detail = "";
			if($package_point_aid > 0){
				$this->load->model($this->package_point_model,"package_point");
				$this->package_point->set_where(array("aid"=>$package_point_aid));
				$package_point_detail = $this->package_point->load_record(true);
			}
			$this->data["package_point_detail"] = $package_point_detail;

			$order_detail = "";
			$this->load->model($this->order_detail_model,"order_detail");
			$this->order_detail->set_where(array("order_main_aid"=>$aid));
			$order_detail = $this->order_detail->load_records(true);
			$this->data["order_detail"] = $order_detail;

			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific order.');
			$this->data["js_code"] = "";
			$this->show();
			return "";
		}
	}
	
	function save(){
		@define("thisAction",'save');
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		
		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		
		$this->load->model($this->main_model,'main');
		
		$name = trim($this->input->get_post('name'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["transport_status"] = $this->input->get_post('transport_status');
		$data["remark_seller"] = $this->input->get_post('remark_seller');
		$data["confirm_status"] = $this->input->get_post('confirm_status');
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->form();
				return "";
			}
			
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$this->log_status('Backend : Insert order', '['.$name.'] just added into database.');
				redirect('admin/order/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert order', 'Command insert_record() fail. Can not insert '.$name);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
						
		}else if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			if($this->check_duplicate($data,$command)){
				$this->form();
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Admin : Update order',  '['.$name.'] has been updated.');
				if($save_option){
					redirect('admin/order/add');
				}else{
					redirect('admin/order/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update order', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : User', 'Command not found.');
			redirect('admin/order/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($data="",$command=""){
		return false;
	}
			
	function ajax_set_value($sid=""){
		@define("thisAction",'ajax_set_value');
		if(!is_staff_or_higher()){
			$msg = set_message_error('Error occurred. Permission denied.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$aid = $this->input->get_post('aid_selected');
		$f_name = $this->input->get_post('f_name');
		$f_value = $this->input->get_post('f_value');
		if(is_blank($aid) || is_blank($f_name)){
			$msg = set_message_error('Error occurred. Data is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		// echo "f_name = $f_name , f_value = $f_value <BR>";

		$user_owner_aid = getUserOwnerAid($this);
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this order.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'cid', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Order', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.');
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Order', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.');
			$msg = set_message_error('Error occurred. Can not save data.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_delete_one(){
		@define("thisAction",'ajax_delete_one');
		if(!is_owner_admin_or_higher()){
			$msg = set_message_error('Error occurred. Permission denied.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$aid = $this->input->post('aid_selected');
		if(is_blank($aid)){
			$msg = set_message_error('Error occurred. Aid is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		$user_owner_aid = getUserOwnerAid($this);
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this order.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, 'cid', 'N/A').' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, 'cid', $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete order', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete order', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Error occurred. Can not delete data.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_get_main_list($sid){
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('orderBackDataSearchSession');	
		// print_r($dsSession);
		
		$search_post_word = $this->getDataFromInput('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->getDataFromInput('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
		
		$search_status = $this->getDataFromInput('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->main->set_where_in($data_where);
		
		$created_date_from = $this->getDataFromInput('created_date_from');
		$created_date_to = $this->getDataFromInput('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->main->set_where($this->main->get_table_name().'.created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->main->set_where($this->main->get_table_name().'.created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}

		$search_need_transport = $this->getDataFromInput('search_need_transport');
		if($search_need_transport == '1'){
			$this->main->set_where(array("need_transport"=>"1"));
		}

		$search_type = $this->getDataFromInput('search_type');
		$data_where = "";
		if(is_var_array($search_type))
		foreach($search_type as $item){
			$data_where["type"][] = $item;
			$data_search["search_type"][] = $item;
		}
		$this->main->set_where_in($data_where);

		$search_record_per_page = $this->getDataFromInput('search_record_per_page', CONST_DEFAULT_RECORD_PER_PAGE);
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->getDataFromInput('page_selected');
		$optional["record_per_page"] = $search_record_per_page;
		$optional = $this->get_pagination_info($optional);
		$data_search["page_selected"] = get_array_value($optional,"page_selected","");
		$data_search["search_record_per_page"] = get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));			
		
		$search_order_by = $this->getDataFromInput('search_order_by');
		$data_search["search_order_by"] = $search_order_by;
		$order_by_option = $this->get_order_by_info($search_order_by,'created_date desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'cid',"col_show"=>'cid_action',"title_class"=>'w80 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Type',"field_order"=>'type',"col_show"=>'type_txt',"title_class"=>'w80 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Transport?',"field_order"=>'need_transport',"col_show"=>'need_transport_action',"title_class"=>'w30 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Transport status',"field_order"=>'transport_status',"col_show"=>'transport_status_txt',"title_class"=>'w30 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Payment status',"field_order"=>'status',"col_show"=>'status_txt',"title_class"=>'w80 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Confirm status',"field_order"=>'confirm_status',"col_show"=>'confirm_status_txt',"title_class"=>'w30 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total price',"field_order"=>'actual_grand_total',"col_show"=>'actual_grand_total_show_with_currency',"title_class"=>'w120 a-center',"result_class"=>'a-right');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Buyer name',"field_order"=>'buyer_name',"col_show"=>'buyer_name',"title_class"=>'hidden-xs w200 a-center',"result_class"=>'hidden-xs a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Buyer email',"field_order"=>'buyer_email',"col_show"=>'buyer_email',"title_class"=>'hidden-xs w150 a-center',"result_class"=>'hidden-xs a-center');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Buyer contact',"field_order"=>'buyer_contact',"col_show"=>'buyer_contact',"title_class"=>'hidden-xs w150 a-center',"result_class"=>'hidden-xs a-center');
		// $header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'a-center',"result_class"=>'hleft');

		$this->session->set_userdata('orderBackDataSearchSession',$data_search);

		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "sorting" => get_array_value($order_by_option,"sorting",""), "optional"=>$optional, "header_list" => $header_list, "result" => $result_list);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.');
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function manage_column_detail($result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$item["cid_action"] = '<a href="'.site_url('admin/order/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"cid","-").'</a>';

			$need_transport = get_array_value($item,"need_transport","0");
			if($need_transport == 1){
				$item["need_transport_action"] = '<i class="fa fa-check"></i>';
			}else{
				$item["need_transport_action"] = '';
			}

			$item["action"] = '';
			if(is_root_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this order" onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/order\', \'<strong>'.removeAllQuote(get_array_value($item,"cid","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>';
			}
			
			$result[] = $item;
		}
		
		return $result;
	
	}
	
	function status($type="")	{
		switch($type)
		{
			case md5('success') : 
				$this->data["message"] = set_message_success('Data has been saved.');
				$this->data["js_code"] = '';
				break;
			case md5('no-command') : 
				$this->data["message"] = set_message_error('Command is unclear. Please try again.');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->show();
	}

}

?>