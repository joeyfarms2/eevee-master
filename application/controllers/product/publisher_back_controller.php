<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Publisher_back_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'setting');
		define("thisAdminSubMenu",'publisher');
		@define("folderName",'product/product_back/publisher');
		
		define("TXT_TITLE",'Publisher management');
		define("TXT_INSERT_TITLE",'Publisher management : Add new publisher');
		define("TXT_UPDATE_TITLE",'Publisher management : Edit publisher');
				
		$this->main_model = 'Publisher_model';
		
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/publisher_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/publisher_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#name').focus();";

		$this->session->set_userdata('productPublisherBackDataSearchSession','');

		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$item_detail = $this->main->load_record(true);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"name","");
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific publisher.');
			$this->data["js_code"] = "$('#name').focus();";
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
		
		$cid = trim($this->input->get_post('cid'));
		$name = trim($this->input->get_post('name'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["name"] = $this->input->get_post('name');
		$url = $this->input->get_post('url');
		if(is_blank($url)){
			$url = $name;
		}
		$data["url"] = getUrlString($url);
		$data["contact_name"] = $this->input->get_post('contact_name');
		$data["contact_number"] = $this->input->get_post('contact_number');
		$data["email"] = $this->input->get_post('email');
		$data["remark"] = $this->input->get_post('remark');
		$data["status"] = $this->input->get_post('status');
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}

			if(is_blank($cid)){
				do{
					$this->load->model($this->setting_config_model,'setting_config');		
					$obj = $this->setting_config->get_config_rni_by_cid("rn-publisher");
					$cid = trim(get_array_value($obj,"barcode",""));
				}while( $this->isPublisherCidExits($cid) );
			}
			$data["cid"] = $cid;

			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert publisher', '['.$name.'] just added into database.', $data);
			
				$data_result = $this->upload_logo_for_publisher($aid);
				$upload_status = get_array_value($data_result,"upload_status","error");
				if($upload_status == "success"){
					$data_upload = array();
					$data_upload["logo"] = get_array_value($data_result,"logo","");
					$data_where["aid"] = $aid;
					$this->main->set_where($data_where);
					$rs = $this->main->update_record($data_upload, $data_where);
				}
			
				redirect('admin/publisher/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert publisher', 'Command insert_record() fail. Can not insert '.$name, $data);
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
			$data["cid"] = $cid;
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
			
				$data_result = $this->upload_logo_for_publisher($aid);
				$upload_status = get_array_value($data_result,"upload_status","error");
				if($upload_status == "success"){
					$data_upload = array();
					$data_upload["logo"] = get_array_value($data_result,"logo","");
					$data_where["aid"] = $aid;
					$this->main->set_where($data_where);
					$rs = $this->main->update_record($data_upload, $data_where);
				}
			
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Backend : Update publisher',  '['.$name.'] has been updated.', $data);
				if($save_option){
					redirect('admin/publisher/add');
				}else{
					redirect('admin/publisher/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update publisher', 'Command update_record() fail. Can not update '.$name.'['.$aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : Publisher', 'Command not found.', $data);
			redirect('admin/publisher/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($data="",$command=""){
		$aid = get_array_value($data,"aid","");
		$name = get_array_value($data,"name","");
		$url = get_array_value($data,"url","");
		$user_owner_aid = get_array_value($data,"user_owner_aid","");

		$this->main->set_where(array("user_owner_aid"=>$user_owner_aid));
		$this->main->set_and_or_where(array("name"=>$name, "url"=>$url));
		if(!is_blank($aid)){
			$this->main->set_where_not_equal(array("aid"=>$aid));
		}
		$objResult = $this->main->load_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		if(is_var_array($objResult)){
			$this->data["message"] = set_message_error('"'.$name.'" is used.');
			$this->data["js_code"] = '$("#name").addClass("error");$("#name").focus();';
			$this->data["command"] = $command;
			$this->data["item_detail"] = $data;
			return true;
		}
		return false;
	}
			
	function ajax_set_value($sid="", $status=""){
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
			$msg = set_message_error('Error occurred. Can not find this publisher.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Publisher', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Publisher', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
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
			$msg = set_message_error('Error occurred. Can not find this publisher.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "name", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "name", $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete publisher', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete publisher', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
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

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('productPublisherBackDataSearchSession');		
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
		$order_by_option = $this->get_order_by_info($search_order_by,'aid desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'cid',"col_show"=>'cid',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Publisher Name',"field_order"=>'name',"col_show"=>'name_action',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Publisher Url',"field_order"=>'url',"col_show"=>'url',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Contact Name',"field_order"=>'contact_name',"col_show"=>'contact_name',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Contact Number',"field_order"=>'contact_number',"col_show"=>'contact_number',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Contact Email',"field_order"=>'email',"col_show"=>'email',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'hcenter',"result_class"=>'hleft');		
		
		$this->session->set_userdata('productPublisherBackDataSearchSession',$data_search);	
		
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
			$item["name_action"] = '<a href="'.site_url('admin/publisher/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"name","-").'</a>';
			$item["contact_number"] = get_array_value($item,"contact_number","-");
			$item["email"] = get_array_value($item,"email","-");
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\', \'admin/publisher\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\', \'admin/publisher\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$item["action"] = '';			
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/publisher\', \'<strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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

	function upload_logo_for_publisher($publisher_aid=""){
		$data_result = array();
		if(is_blank($publisher_aid)){
			$data_result["upload_status"] = "error";
			$data_result["message"] = set_message_error('Do not found publisher aid.');
			return $data_result;
		}
		
		if( !is_blank(get_array_value($_FILES,"logo","")) && !is_blank(get_array_value($_FILES["logo"],"name","")) ){
			$this->load->model($this->main_model,"main");
			$this->main->set_where(array("aid"=>$publisher_aid));
			$itemResult = $this->main->load_record(false);
			$created_date = get_array_value($itemResult,"created_date","");
			if(is_blank($created_date)){
				$data_result["upload_status"] = "error";
				$data_result["message"] = set_message_error('Do not found created date.');
				return $data_result;
			}
			
			$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/publisher/".get_datetime_pattern("Y",$created_date,date('Y')).'/'.get_datetime_pattern("m",$created_date,date('m'));
			$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/publisher/".get_datetime_pattern("Y",$created_date,date('Y')).'/'.get_datetime_pattern("m",$created_date,date('m'));
			create_directories($upload_base_path);
			
			// echo $upload_base_path;exit(0);
		
			//Start upload file
			$upload_path = $upload_base_path;
			$image_name = $_FILES["logo"]["name"];
			$file_type = substr(strrchr($image_name, "."), 0);
			
			$new_file_name_thumb = $publisher_aid."-logo".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("logo",$upload_path,$new_file_name_thumb,0,999,999,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$this->log_status('Backend : Publisher', 'Upload image error : '.$result_image_thumb["error_msg"]);
				$data_result["upload_status"] = "error";
				$data_result["message"] = $result_image_thumb["error_msg"];
			}else{
				$data_result["upload_status"] = "success";
				$data_result["logo"] = $upload_base_path_db.'/'.$new_file_name_thumb;
			}
			return $data_result;
		}
	}

	function isPublisherCidExits($cid){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("cid"=>$cid));
		$total = $this->main->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function add_if_not_exist()
	{
		// echo "<pre>";
		// print_r($_REQUEST);
		// echo "</pre>";
		$name = $this->input->get_post('name');
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("name"=>$name));
		$total = $this->main->count_records(false);
		if($total > 0)
		{
			//echo "abcd";
		}
		else{
			//echo "1234";
			$this->load->model($this->main_model,'main');
			
			$cid = trim($this->input->get_post('cid'));
			$user_owner_aid = $this->get_user_owner_aid_by_input();
			$data["user_owner_aid"] = $user_owner_aid;
			$data["name"] = $name;
			$url = $this->input->get_post('url');
			if(is_blank($url)){
				$url = $name;
			}
			$data["url"] = getUrlString($url);
			
			$data["status"] = 1; //$this->input->get_post('status');
			//echo "5678";
			if(is_blank($cid)){
				do{
					$this->load->model($this->setting_config_model,'setting_config');		
					$obj = $this->setting_config->get_config_rni_by_cid("rn-publisher");
					$cid = trim(get_array_value($obj,"barcode",""));
				}while( $this->isPublisherCidExits($cid) );
			}
			//echo "9876";
			$data["cid"] = $cid;

			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert publisher', '['.$name.'] just added into database.', $data);
			}else{
				$this->log_error('Backend : Insert publisher', 'Command insert_record() fail. Can not insert '.$name, $data);
			}
		}
		//echo "5432";
		//$this->load->model($this->main_model,"main");
		$this->main->set_where(array("name"=>$name));
		$itemResult = $this->main->load_record(false);

		echo json_encode(array("status"=>"success","aid"=>get_array_value($itemResult,"aid",0)));
		return "";
	}
}

?>