<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_type_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_root_admin_or_higher();
		$this->data["mode"] = "backend";
		
		$this->lang->load('product_type');
		
		define("thisAdminTabMenu",'setting');
		define("thisAdminSubMenu",'product_type');
		@define("folderName",'product/product_back/type');
		
		define("TXT_TITLE", $this->lang->line('product_type_title'));
		define("TXT_INSERT_TITLE", $this->lang->line('product_type_insert_title'));
		define("TXT_UPDATE_TITLE", $this->lang->line('product_type_update_title'));
				
		$this->main_model = 'Product_type_model';
				
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/type_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/type_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		for_root_admin_or_higher();
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#cid').focus();";

		$this->session->set_userdata('productTypeBackDataSearchSession','');

		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));

		$item_detail = $this->main->load_record(false);
		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"name","");
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific product type.');
			$this->data["js_code"] = "$('#cid').focus();";
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
		$data["cid"] = $this->input->get_post('cid');
		$data["user_owner_aid"] = $user_owner_aid;
		$data["name"] = $name;
		$data["icon"] = $this->input->get_post('icon');
		$data["description"] = $this->input->get_post('description');
		$data["status"] = $this->input->get_post('status');
		$data["weight"] = $this->input->get_post('weight');
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert product type', '['.$name.'] just added into database.', $data);
				redirect('admin/product-type/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert product type', 'Command insert_record() fail. Can not insert '.$name, $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
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
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Backend : Update product type',  '['.$name.'] has been updated.', $data);
				if($save_option){
					redirect('admin/product-type/add');
				}else{
					redirect('admin/product-type/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update product type', 'Command update_record() fail. Can not update '.$name.'['.$aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : Product type', 'Command not found.', $data);
			redirect('admin/product-type/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($data="",$command=""){
		$aid = get_array_value($data,"aid","");
		$cid = get_array_value($data,"cid","");
		$name = get_array_value($data,"name","");
		$user_owner_aid = get_array_value($data,"user_owner_aid","");

		$this->main->set_where(array("user_owner_aid"=>$user_owner_aid));
		$this->main->set_and_or_where(array("cid"=>$cid , "name"=>$name));
		if(!is_blank($aid)){
			$this->main->set_where_not_equal(array("aid"=>$aid));
		}
		$objResult = $this->main->load_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		if(is_var_array($objResult)){
			$error_txt = "";
			$js_code = "";
			$obj_name = "";
			$has_name = false;
			$has_cid = false;
			foreach($objResult as $item){
				$tmp_name = get_array_value($item,"name","");
				$tmp_cid = get_array_value($item,"cid","");
				if(!is_blank($name) && $name == $tmp_name){
					if(!is_blank($error_txt) && !$has_name) $error_txt .= '<BR>';
					if(!$has_name) $error_txt .= '"'.$name.'" is used.';
					$js_code .= '$("#name").addClass("error");$("#name").focus();';
					if(is_blank($obj_name)) $obj_name = "name";
					$has_name = true;
				}
				if(!is_blank($cid) && $cid == $tmp_cid){
					if(!is_blank($error_txt) && !$has_cid) $error_txt .= '<BR>';
					if(!$has_cid) $error_txt .= '"'.$cid.'" is used.';
					$js_code .= '$("#cid").addClass("error");$("#cid").focus();';
					if(is_blank($obj_name)) $obj_name = "cid";
					$has_cid = true;
				}
			}
			if(!is_blank($error_txt)) {
				$this->data["message"] = set_message_error($error_txt);
				if(!is_blank($obj_name)) $this->data["js_code"] = $js_code.'$("#'.$obj_name.'").focus();';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				return true;
			}
		}
		return false;
	}
			
	function ajax_set_value($sid="", $status=""){
		@define("thisAction",'ajax_set_value');
		if(!is_owner_admin_or_higher()){
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
			$msg = set_message_error('Error occurred. Can not find this product type.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Product type', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Product type', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_delete_one(){
		@define("thisAction",'ajax_delete_one');
		if(!is_root_admin_or_higher()){
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
			$msg = set_message_error('Error occurred. Can not find this product type.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "name", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "name", $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete product type', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete product type', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_get_main_list($sid){
		@define("thisAction",'ajax_get_main_list');
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		if(!is_root_admin_or_higher()){
			$this->main->set_where(array("status"=>"1"));
		}

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('productTypeBackDataSearchSession');		
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
		$order_by_option = $this->get_order_by_info($search_order_by,'weight asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(false);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'cid',"col_show"=>'cid',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		if(is_root_admin_or_higher()){
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Product Type Name',"field_order"=>'name',"col_show"=>'name_action',"title_class"=>'w250 hcenter',"result_class"=>'hleft');
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Weight',"field_order"=>'weight',"col_show"=>'weight',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		}else{
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Product Type Name',"field_order"=>'name',"col_show"=>'name',"title_class"=>'w250 hcenter',"result_class"=>'hleft');
		}
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('productTypeBackDataSearchSession',$data_search);	
		
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
			$item["name_action"] = '<a href="'.site_url('admin/product-type/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"name","-").'</a>';
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\', \'admin/product-type\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\', \'admin/product-type\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$item["action"] = '';
			
			if(is_root_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/product-type\', \'<strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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
			case md5('no-product-type-aid') : 
				$this->data["message"] = set_message_error('Error occurred. Can not find this product type.');
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