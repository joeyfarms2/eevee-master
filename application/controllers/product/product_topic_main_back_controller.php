<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_topic_main_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_owner_admin_or_higher();
		$this->data["mode"] = "backend";
		
		if(CONST_USE_PRODUCT_TOPIC != "1"){
			redirect('admin');
		}
		
		define("thisAdminTabMenu",'setting');
		define("thisAdminSubMenu",'topic_main');
		@define("folderName",'product/');
		
		define("TXT_TITLE",'Topic product management');
		define("TXT_INSERT_TITLE",'Topic product management : Add new topic product');
		define("TXT_UPDATE_TITLE",'Topic product management : Edit topic product');
				
		$this->main_model = 'Product_topic_main_model';
		
		$this->load->model($this->main_model,"product_topic_main");
		$this->data["master_product_topic_main"] = $this->product_topic_main->load_product_topic_mains();
		
	}
	
	function index(){
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/product/product_topic_main_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/product/product_topic_main_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#cid').focus();";
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
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific main.');
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
		$data["weight"] = $this->input->get_post('weight');
		$data["status"] = $this->input->get_post('status');
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->form();
				return "";
			}
			
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$this->log_status('Backend : Insert product main topic', '['.$name.'] just added into database.');
				redirect('admin/product-topic-main/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert product main topic', 'Command insert_record() fail. Can not insert '.$name);
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
				if ($aid > 0) $this->log_status('Admin : Update product main topic',  '['.$name.'] has been updated.');
				if($save_option){
					redirect('admin/product-topic-main/add');
				}else{
					redirect('admin/product-topic-main/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update product main topic', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : User', 'Command not found.');
			redirect('admin/product-topic-main/status/'.md5('no-command'));
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
			
	function ajax_set_status($sid="", $status=""){
		if(!is_owner_admin_or_higher()){
			$result_obj = array("status" => 'error',"msg" => 'Permission denied.');
			echo json_encode($result_obj);
			return "";
		}
		$aid = $this->input->get_post('aid_selected');
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Aid is null.' );
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not find this main.' );
			echo json_encode($result_obj);
			return "";
		}
		$this_name = get_array_value($objResult, 'name', $aid);
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->set_status($status);
		
		if ($rs){
			if($status == 1){
				$this->log_status('Backend : Product main topic status', $this_name.'['.$aid.'] was active.');
				$result_obj = array("status" => 'success',"msg" => $this_name.' was active.' );
				echo json_encode($result_obj);
				return "";
			}else{
				$this->log_status('Backend : Product main topic status', $this_name.'['.$aid.'] was inactive.');
				$result_obj = array("status" => 'success',"msg" => $this_name.' was inactive.' );
				echo json_encode($result_obj);
				return "";
			}
		}else{
			$this->log_error('Backend : Product main topic status', 'Function main->set_status() fail. Can not set status to '.$this_name.'['.$aid.'].');
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not save data.' );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_delete_one(){
		if(!is_owner_admin_or_higher()){
			$result_obj = array("status" => 'error',"msg" => 'Permission denied.');
			echo json_encode($result_obj);
			return "";
		}

		$aid = $this->input->post('aid_selected');
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Aid is null.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not find this main.' );
			echo json_encode($result_obj);
			return "";
		}
		$this_name = get_array_value($objResult, "name", $aid);
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete product main topic', $this_name.'['.$aid.'] has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $this_name.' has been deleted.' );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete product main topic', 'Command delete_records() fail. Can not delete '.$this_name.'['.$aid.'].');
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not delete main.' );
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
		
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->input->get_post('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
		
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->main->set_where_in($data_where);
		
		$created_date_from = $this->input->get_post('created_date_from');
		$created_date_to = $this->input->get_post('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->main->set_where($this->main->get_table_name().'.created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->main->set_where($this->main->get_table_name().'.created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}		
		
		$optional = array();
		$optional["total_record"] = $this->main->count_records();
		$optional["page_selected"] = $this->input->get_post('page_selected');
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_PER_PAGE;
		$optional = $this->get_pagination_info($optional);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));			
		
		$search_order_by = $this->input->get_post('search_order_by');
		$order_by_option = $this->get_order_by_info($search_order_by,'weight asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(false);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'cid',"col_show"=>'cid',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		if(is_root_admin_or_higher()){
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Topic Name',"field_order"=>'name',"col_show"=>'name_action',"title_class"=>'w250 hcenter',"result_class"=>'hleft');
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Weight',"field_order"=>'weight',"col_show"=>'weight',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		}else{
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Topic Name',"field_order"=>'name',"col_show"=>'name',"title_class"=>'w250 hcenter',"result_class"=>'hleft');
		}
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'hcenter',"result_class"=>'hleft');
		
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
			$item["name_action"] = '<a href="'.site_url('admin/product-topic-main/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"name","-").'</a>';
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/accept.png" class="button" title="Click to \'Inactive\' this main" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/product-topic-main\', \' inactive '.get_array_value($item,"name","").'\', \'status=0\')">';
			}else{
				$item["status_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/cross.png" class="button" title="Click to \'Active\' this main" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/product-topic-main\', \' active '.get_array_value($item,"name","").'\', \'status=1\')">';
			}
			$item["action"] = '';
			$item["action"] .= '<img src="'.CSS_PATH.'dandelion/images/icons/color/chart_organisation.png" class="button" title="Click to see detail" onclick="processRedirect(\'admin/product-topic/'.get_array_value($item,"aid","").'\')">';
			
			if(is_root_admin_or_higher()){
				$item["action"] .= '&nbsp;<img src="'.CSS_PATH.'dandelion/images/icons/color/bin_closed.png" class="button" title="Click to \'Delete\' this main" onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/product-topic-main\', \''.get_array_value($item,"name","").'\')">';
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