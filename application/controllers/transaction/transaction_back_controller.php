<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Transaction_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		if(CONST_HAS_TRANSACTION != "1"){
			redirect('home');
		}

		define("thisAdminTabMenu",'transaction');
		
		@define("folderName",'transaction/transaction_back/transaction');
		
		define("TXT_TITLE",'Transaction management');
		define("TXT_INSERT_TITLE",'Transaction management : Add new transaction');
		define("TXT_UPDATE_TITLE",'Transaction management : Edit transaction');
				
		$this->main_model = 'Transaction_model';		
		$this->reserve_product_model = 'Reserve_product_model';	
		
		$this->holiday_weekend_model = 'Holiday_weekend_model';
		$this->holiday_model = 'Holiday_model';	

	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		define("thisAdminSubMenu",'transaction');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/transaction_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/transaction_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		define("thisAdminSubMenu",'borrow');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "";

		$this->session->set_userdata('transactionBackDataSearchSession','');

		$this->form();
	}
	
	function user($user_aid=""){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';

		$this->load->model($this->user_model,"user");
		$tmp = array();
		$tmp["aid"] = $user_aid;
		$this->user->set_where($tmp);
		$user_result = $this->user->load_record(true);
		$this->data["user_result"] = $user_result;

		$this->data["header_title"] = get_array_value($user_result,"full_name_th",get_array_value($user_result,"email",get_array_value($user_result,"cid","N/A")));
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
			$this->data["header_title"] = TXT_UPDATE_TITLE;
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific transaction.');
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
		
		$title = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["title"] = $title;
		$data["weight"] = $this->input->get_post('weight');
		$data["is_highlight"] = $this->input->get_post('is_highlight');
		$data["is_new"] = $this->input->get_post('is_new');
		$data["in_home"] = $this->input->get_post('in_home');
		$publish_date = get_datetime_pattern("db_date_format",$this->input->get_post('publish_date'),get_db_now('%Y-%m-%d'));
		$data["publish_date"] = $publish_date;
		$data["expired_date"] = get_datetime_pattern("db_date_format",$this->input->get_post('expired_date'),NULL);
		$data["ref_link"] = $this->input->get_post('ref_link');
		$data["target"] = $this->input->get_post('target');
		$data["description"] = $this->input->get_post('description');
		$data["status"] = $this->input->get_post('status');
		
		$cid = "";
		if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if(!is_var_array($itemResult)){
				$this->data["message"] = set_message_error("Ads not found.");
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}else{
				$cid = trim(get_array_value($itemResult,"cid",""));
				$path = './'.trim(get_array_value($itemResult,"upload_path",""));
				if($path != './' && $path != $upload_base_path.'/'.$cid.'/'){
					// echo "Change Path";
					if(is_dir($path)){
						echo $path." is found .. Start move";
						rename($path,$upload_base_path.'/'.$cid);
					}else{
						// echo $path." not found";
					}
				}else{
					// echo "Do Not Change Path";
				}
			}
		}
		
		if(is_blank($cid)){
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isAdsCodeExits($cid) );
		}
		$data["cid"] = trim($cid);
		$data["upload_path"] = $upload_base_path_db.'/'.$cid.'/';
		
		if( !is_blank(get_array_value($_FILES,"cover_image","")) && !is_blank(get_array_value($_FILES["cover_image"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'/'.$cid;
			$image_name = $_FILES["cover_image"]["name"];
			$file_type = substr(strrchr($image_name, "."), 0);
			
			$data["cover_image_file_type"] = $file_type;
			
			$new_file_name_thumb = $cid."-actual".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,800,0,99,1);

			$new_file_name_thumb = $cid."-thumb".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,270,0,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$this->log_error('Admin : Ads', 'Add new transaction fail => Upload image error : '.$result_image_thumb["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_image_thumb,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}	
		}		
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			$data["total_view"] = '0';
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert transaction', 'New transaction just added into database.', $data);
				redirect('admin/transaction/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert transaction', 'Command insert_record() fail. Can not insert new transaction', $data);
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
				if ($aid > 0) $this->log_status('Backend : Update transaction',  'Ads has been updated.', $data);
				if($save_option){
					redirect('admin/transaction/add');
				}else{
					redirect('admin/transaction/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update transaction', 'Command update_record() fail. Can not update transaction['.$aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : Ads', 'Command not found.', $data);
			redirect('admin/transaction/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($data="",$command=""){
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
			$msg = set_message_error('Error occurred. Can not find this transaction.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'title', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Ads', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Ads', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_delete_one(){
		@define("thisAction",'ajax_delete_one');
		if(!is_staff_or_higher()){
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
			$msg = set_message_error('Error occurred. Can not find this transaction.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "title", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "title", $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete transaction', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete transaction', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
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
		$dsSession = $dataSearchSession->userdata('transactionBackDataSearchSession');		
		// print_r($dsSession);
		
		$mode = $this->getDataFromInput('mode');
		// echo "mode = $mode <BR>";

		$user_aid = $this->getDataFromInput('user_aid');
		if(is_number_no_zero($user_aid)){
			$this->main->set_where(array("user_aid"=>$user_aid, "return_status"=>"0"));
		}

		$search_post_word = $this->getDataFromInput('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->getDataFromInput('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && !is_blank($search_in) && !is_var_array($search_in)){
			$search_in = explode(",", $search_in);
		}
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
		
		$search_status = $this->getDataFromInput('search_status');
		if(!is_blank($search_status) && !is_var_array($search_status)){
			$search_status = explode(",", $search_status);
		}
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
		
		$borrowing_date_from = $this->getDataFromInput('borrowing_date_from');
		$borrowing_date_to = $this->getDataFromInput('borrowing_date_to');
		$data_search["borrowing_date_from"] = $borrowing_date_from;
		$data_search["borrowing_date_to"] = $borrowing_date_to;
		
		if(!is_blank($borrowing_date_from)){
			$this->main->set_where($this->main->get_table_name().'.borrowing_date >=', get_datetime_pattern("db_date_format",$borrowing_date_from,"")." 00:00:00");
		}
		if(!is_blank($borrowing_date_to)){
			$this->main->set_where($this->main->get_table_name().'.borrowing_date <=', get_datetime_pattern("db_date_format",$borrowing_date_to,"")." 23:59:59");
		}
		
		$due_date_from = $this->getDataFromInput('due_date_from');
		$due_date_to = $this->getDataFromInput('due_date_to');
		$data_search["due_date_from"] = $due_date_from;
		$data_search["due_date_to"] = $due_date_to;
		
		if(!is_blank($due_date_from)){
			$this->main->set_where($this->main->get_table_name().'.due_date >=', get_datetime_pattern("db_date_format",$due_date_from,"")." 00:00:00");
		}
		if(!is_blank($due_date_to)){
			$this->main->set_where($this->main->get_table_name().'.due_date <=', get_datetime_pattern("db_date_format",$due_date_to,"")." 23:59:59");
		}
		
		$search_option = $this->getDataFromInput('search_option');
		$data_search["search_option"] = $search_option;
		switch ($search_option) {
			case '1':
				$this->main->set_where(array("return_status"=>"1"));
				break;
			case '2':
				$this->main->set_where(array("return_status"=>"0"));
				break;
			case '3':
				$this->main->set_where(array("return_status"=>"0"));
				$this->main->set_where($this->main->get_table_name().'.due_date >', date('Y-m-d')." 23:59:59");
				break;
			case '4':
				$this->main->set_where(array("return_status"=>"0"));
				$this->main->set_where($this->main->get_table_name().'.due_date <=', date('Y-m-d')." 23:59:59");
				break;
		}

		$search_record_per_page = $this->getDataFromInput('search_record_per_page', CONST_DEFAULT_RECORD_PER_PAGE);
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->getDataFromInput('page_selected');
		$optional["record_per_page"] = $search_record_per_page;
		$optional = $this->get_pagination_info($optional);
		$data_search["page_selected"] = get_array_value($optional,"page_selected","");
		$data_search["search_record_per_page"] = get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE);
		if($mode != "1"){ // not export mode
			$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));
		}
		
		$search_order_by = $this->getDataFromInput('search_order_by');
		$data_search["search_order_by"] = $search_order_by;
		$order_by_option = $this->get_order_by_info($search_order_by,'due_date asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list, $mode);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);

		if($mode == "1"){ //export mode
			$this->export($result_list);
			return "";
		}
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Barcode',"field_order"=>'barcode',"col_show"=>'barcode',"title_class"=>'w80 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title_action',"title_class"=>'w250 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Borrowing Date',"field_order"=>'borrowing_date',"col_show"=>'borrowing_date_only_txt',"title_class"=>'w100 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Due Date',"field_order"=>'due_date',"col_show"=>'due_date_txt',"title_class"=>'w100 a-center',"result_class"=>'a-center cls_due_date');
		switch ($mode) {
			case '1': //export
					
				break;
			
			case '2': // trasaction form mode
				break;
			
			default:
				 $header_list[] = array("sort_able"=>'1',"title_show"=>'Returning Date',"field_order"=>'returning_date',"col_show"=>'returning_date_only_txt',"title_class"=>'w100 a-center',"result_class"=>'a-center cls_returning_date');
				 $header_list[] = array("sort_able"=>'1',"title_show"=>'User',"field_order"=>'user.first_name_th',"col_show"=>'full_name_th',"title_class"=>'w250 a-center',"result_class"=>'a-left');
				 break;
		}
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'a-center',"result_class"=>'a-left');
		
		$this->session->set_userdata('transactionBackDataSearchSession',$data_search);

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
	
	function manage_column_detail($result_list, $mode){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			// $item["title_action"] = '<a href="'.site_url('admin/transaction/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title","((no title)))").'</a>';
			$item["title_action"] = ''.get_array_value($item,"title","((no title)))").'';
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this transaction." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/transaction\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this transaction." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/transaction\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$today_date = date('Y-m-d');
			$due_date = get_datetime_pattern("db_date_format",get_array_value($item,"due_date",""),"");
			$returning_date = get_datetime_pattern("db_date_format",get_array_value($item,"returning_date",""),"");
			$return_status = get_array_value($item,"return_status","0");

			$item["action"] = '';			
			if(is_staff_or_higher()){
				switch ($mode) {
					case '1': //export
						break;
					
					case '2': // trasaction form mode
						$item["action"] .= '<a class="btn btn-primary btn-xs" title="Click to \'Return\' this book." onclick="addProductToTransactionByUser(\''.get_array_value($item,"user_aid","").'\', \''.get_array_value($item,"product_type_aid","").'\', \''.get_array_value($item,"barcode","").'\')"><i class="fa fa-reply "></i></a>&nbsp;&nbsp;&nbsp;';
						break;
					
					default:
						$item["action"] .= '<a class="btn btn-primary btn-xs" title="" onclick="processRedirect(\'admin/transaction/user/'.get_array_value($item,"user_aid","").'\')"><i class="fa fa-search "></i></a>&nbsp;&nbsp;&nbsp;';
						if($today_date > $due_date && $return_status == '0' ){
							$item["action"] .= '<a class="btn btn-danger btn-xs" title="Send overdue notification email" onclick="sendOverdueEmail(\''.get_array_value($item,"aid","").'\')"><i class="fa fa-envelope-o "></i></a>&nbsp;&nbsp;&nbsp;';
						}
						break;
				}

				// $item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this transaction." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/transaction\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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
	
	function isAdsCodeExits($cid){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("cid"=>$cid));
		$total = $this->main->count_records();
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function export($result_list=""){
		if(is_var_array($result_list)){
			$this->load->library('PHPExcel');
			$title_column_color = 'C9DCE6';
			$array_style_summary_title = array(
				'font' => array('bold' => true), 
				'alignment' => array('
					wrap' => true,
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'borders' => array(
					'allborders'     => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array('rgb' => $title_column_color)
				)
			);

			$objPHPExcel = new PHPExcel();
			// Set properties
			$objPHPExcel->getProperties()->setCreator(ADMIN_EMAIL_NAME);
			$objPHPExcel->getProperties()->setLastModifiedBy(ADMIN_EMAIL_NAME);
			$objPHPExcel->getProperties()->setTitle("Circulation List");
			$objPHPExcel->getProperties()->setDescription("Circulation List");
			// Set Default Style
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); 
			// Rename Sheet
			$objPHPExcel->getActiveSheet()->setTitle('Circulation List');
			// Set column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		
			$objPHPExcel->getActiveSheet()->setCellValue('A1', "Barcode");
			$objPHPExcel->getActiveSheet()->setCellValue('B1', "Title");
			$objPHPExcel->getActiveSheet()->setCellValue('C1', "Member ID");
			$objPHPExcel->getActiveSheet()->setCellValue('D1', "Member name");
			$objPHPExcel->getActiveSheet()->setCellValue('E1', "Borrowing date");
			$objPHPExcel->getActiveSheet()->setCellValue('F1', "Due date");
			$objPHPExcel->getActiveSheet()->setCellValue('G1', "Returning date");
			$sharedStyle1 = new PHPExcel_Style();
			$sharedStyle1->applyFromArray($array_style_summary_title);
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:G1");

			$irow = 2;
			foreach($result_list as $item){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$irow, get_array_value($item,"barcode",""));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$irow, get_array_value($item,"title",""));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$irow, get_array_value($item,"user_cid",""));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$irow, get_array_value($item,"full_name_th",""));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$irow, get_array_value($item,"borrowing_date_txt",""));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$irow, get_array_value($item,"due_date_txt",""));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$irow, get_array_value($item,"returning_date_txt",""));
				$irow++;
			}
			
			$filename ="download_export_".date("ymdHis").".xls";
			// echo "$filename";
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$filename);
			header('Cache-Control: max-age=0');
			header('Pragma: no-cache');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit();
		}else{
			echo "No record found.";
			return"";
		}
	}

	function ajax_get_transaction_list_by_user($sid=""){
		$user_aid = $this->input->get_post('user_aid');
		if(is_blank($user_aid)){
			$msg = 'Error occurred. Data is null.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->user_model,"user");
		$tmp = array();
		$tmp["aid"] = $user_aid;
		$this->user->set_where($tmp);
		$user_result = $this->user->load_record();
		if(!is_var_array($user_result)){
			$msg = 'Error occurred. User not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$transactionByUser = $this->session->userdata('transactionByUser_'.$user_aid);
		// print_r($transactionByUser);
		if(!is_var_array($transactionByUser)){
			$summary = array();
			$summary["total_fee"] = 0;
			$result_obj = array("status" => 'warning',"msg" => '&nbsp;',"summary" => $summary);
			echo json_encode($result_obj);
			return "";
		}

		$total_fee = 0;
		foreach ($transactionByUser as $key => $obj) {
			$total_fee += get_array_value($obj,"fee","0");
		}

		$summary = array();
		$summary["total_fee"] = $total_fee;

		$result_obj = array("status" => 'success',"msg" => '',"result" => $transactionByUser,"summary" => $summary);
		echo json_encode($result_obj);
		return "";
	}

	function ajax_add_product_to_transaction_by_user($sid=""){
		if(!is_staff_or_higher()){
			$msg = 'Error occurred. Permission denied.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$user_aid = $this->input->get_post('user_aid');
		$product_type_aid = $this->input->get_post('product_type_aid');
		$barcode = $this->input->get_post('barcode');
		if(is_blank($user_aid) || is_blank($product_type_aid) || is_blank($barcode)){
			$msg = 'Error occurred. Data is null.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->user_model,"user");
		$tmp = array();
		$tmp["aid"] = $user_aid;
		$this->user->set_where($tmp);
		$user_result = $this->user->load_record();
		if(!is_var_array($user_result)){
			$msg = 'Error occurred. User not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$user_section_aid = get_array_value($user_result,"user_section_aid","0");
		$this->load->model($this->user_section_model,"user_section");
		$this->user_section->set_where(array("aid"=>$user_section_aid));
		$user_section_result = $this->user_section->load_record(false);
		if(!is_var_array($user_section_result)){
			$msg = 'Error occurred. User section not found.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid, true);
		$model = $this->get_product_model($product_type_aid);
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$model_product_ref_user_section_name = get_array_value($model,"product_ref_user_section_model","");
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_copy_name, $model_copy_name);
		$tmp = array();
		$tmp['barcode'] = $barcode;
		$this->{$model_copy_name}->set_where($tmp);
		$product_copy_result = $this->{$model_copy_name}->load_record(true);
		if(!is_var_array($product_copy_result)){
			$msg = 'Error occurred. Book not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$copy_aid = get_array_value($product_copy_result,"aid","0");

		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_product_ref_user_section_name, $model_product_ref_user_section_name);
		$tmp = array();
		$tmp['copy_aid'] = $copy_aid;
		$tmp['user_section_aid'] = $user_section_aid;
		$this->{$model_product_ref_user_section_name}->set_where($tmp);
		$product_ref_user_section_result = $this->{$model_product_ref_user_section_name}->load_record(true);
		// if(!is_var_array($product_ref_user_section_result)){
		// 	$msg = 'Error occurred. Book not founded.';
		// 	$result_obj = array("status" => 'error',"msg" => $msg );
		// 	echo json_encode($result_obj);
		// 	return "";
		// }

		$status = get_array_value($product_copy_result,"status","0");
		// if($status != "1"){
		// 	$msg = 'This book was inactive. Can not borrow.';
		// 	$result_obj = array("status" => 'error',"msg" => $msg );
		// 	echo json_encode($result_obj);
		// 	return "";
		// }

		$is_ebook = get_array_value($product_copy_result,"is_ebook","0");
		if($is_ebook == "1"){
			$msg = 'This book is ebook. Can not borrow.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$shelf_status = get_array_value($product_copy_result,"shelf_status","0");
		// if($shelf_status == "2"){
		// 	$msg = 'This book was borrowed by other. Can not borrow.';
		// 	$result_obj = array("status" => 'error',"msg" => $msg );
		// 	echo json_encode($result_obj);
		// 	return "";
		// }
		// if($shelf_status == "3"){
		// 	$msg = 'This book was damaged. Can not borrow.';
		// 	$result_obj = array("status" => 'error',"msg" => $msg );
		// 	echo json_encode($result_obj);
		// 	return "";
		// }
		// if($shelf_status == "4"){
		// 	$msg = 'This book was lost. Can not borrow.';
		// 	$result_obj = array("status" => 'error',"msg" => $msg );
		// 	echo json_encode($result_obj);
		// 	return "";
		// }
		$this->load->model($this->main_model,"main");
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["copy_aid"] = $copy_aid;
		$tmp["status"] = "1";
		$tmp["return_status"] = "0";
		$this->main->set_where($tmp);
		$transaction_result = $this->main->load_record(false);
		$already_borrow = false;
		$get_reserve = false;
		$transaction_aid = 0;
		if(is_var_array($transaction_result)){
			$transaction_aid = get_array_value($transaction_result,"aid","0");
			$transaction_user_aid = get_array_value($transaction_result,"user_aid","0");
			if($transaction_user_aid != $user_aid){
				// $msg = 'This book was borrowed by other. Can not borrow.';
				// $result_obj = array("status" => 'error',"msg" => $msg );
				// echo json_encode($result_obj);
				// return "";
				$this->load->model($this->reserve_product_model,"reserve_product");
				$tmp_r = array();
				$tmp_r["user_aid"] = $user_aid;
				$tmp_r["product_type_aid"] = $product_type_aid;
				$tmp_r["copy_aid"] = $copy_aid;
				$this->reserve_product->set_where($tmp_r);
				$tmp_r = array();
				$tmp_r["status"][] = "1";
				$tmp_r["status"][] = "2";
				$this->reserve_product->set_where_in($tmp_r);
				$reserve_result = $this->reserve_product->load_records(false);
				// echo "<br>sql : ".$this->db->last_query();
				if(is_var_array($reserve_result)){
					$msg = 'This book was borrowed by other and you already make reservation.';
					$result_obj = array("status" => 'error',"msg" => $msg );
					echo json_encode($result_obj);
					return "";
				}
				$get_reserve = true;
			}else{
				$already_borrow = true;
			}
		}

		if(!$already_borrow){
			$this->load->model($this->reserve_product_model,"reserve_product");
			$tmp_r = array();
			$tmp_r["product_type_aid"] = $product_type_aid;
			$tmp_r["copy_aid"] = $copy_aid;
			$this->reserve_product->set_where($tmp_r);
			$tmp_r = array();
			$tmp_r["status"][] = "1";
			$tmp_r["status"][] = "2";
			$this->reserve_product->set_where_in($tmp_r);
			$reserve_result = $this->reserve_product->load_records(false);
			if(is_var_array($reserve_result)){
				$my_turn = false;
				$other_turn = false;
				$my_reserve = false;
				$other_reserve = false;
				foreach ($reserve_result as $reserve_item) {
					$reserve_user_aid = get_array_value($reserve_item,"user_aid","");
					$reserve_status = get_array_value($reserve_item,"status","");
					if($reserve_status == "2" && $user_aid == $reserve_user_aid){
						$my_turn = true;
					}
					if($reserve_status == "2" && $user_aid != $reserve_user_aid){
						$other_turn = true;
					}
					if($user_aid == $reserve_user_aid){
						$my_reserve = true;
					}
					if($user_aid != $reserve_user_aid){
						$other_reserve = true;
					}
				}
				if(!$my_turn && $other_turn && $my_reserve){
					$msg = 'This book was reserved by other and you already make reservation.';
					$result_obj = array("status" => 'error',"msg" => $msg );
					echo json_encode($result_obj);
					return "";
				}
				if(!$my_turn && $my_reserve && $other_reserve){
					$msg = 'This book was reserved by other and you already make reservation.';
					$result_obj = array("status" => 'error',"msg" => $msg );
					echo json_encode($result_obj);
					return "";
				}
				if( ($other_turn || $other_reserve) && !$my_reserve ){
					$get_reserve = true;
				}
			}
		}

		$transactionByUser = $this->session->userdata('transactionByUser_'.$user_aid);
		$key = $product_type_aid."_".$copy_aid;

		$obj = array();
		$obj["product_type_aid"] = $product_type_aid;
		$obj["copy_aid"] = $copy_aid;
		$obj["barcode"] = get_array_value($product_copy_result,"barcode","");
		$obj["title"] = trim(get_array_value($product_copy_result,"parent_title","")." ".get_array_value($product_copy_result,"copy_title",""));

		$rental_period = get_array_value($user_section_result,"default_rental_period","0");
		$rental_fee = get_array_value($user_section_result,"default_rental_fee","0");
		$rental_fee_point = get_array_value($user_section_result,"default_rental_fee_point","0");
		$rental_fine_fee = get_array_value($user_section_result,"default_rental_fine_fee","0");
		$renew_time = get_array_value($user_section_result,"default_renew_time","0");
		$renew_period = get_array_value($user_section_result,"default_renew_period","0");
		if(is_var_array($product_ref_user_section_result)){
			$rental_period = get_array_value($product_ref_user_section_result,"rental_period","0");
			$rental_fee = get_array_value($product_ref_user_section_result,"rental_fee","0");
			$rental_fee_point = get_array_value($product_ref_user_section_result,"rental_fee_point","0");
			$rental_fine_fee = get_array_value($product_ref_user_section_result,"rental_fine_fee","0");
			$renew_time = get_array_value($product_ref_user_section_result,"renew_time","0");
			$renew_period = get_array_value($product_ref_user_section_result,"renew_period","0");
		}

		$fee = 0;
		if($get_reserve){
			$obj["type"] = "reserve";
		}else if($already_borrow){
			$obj["type"] = "return";
			$due_date = get_array_value($transaction_result,"due_date","");
			$obj["due_date"] = $due_date;
			$diff = get_diff_date(date('Y-m-d'), $due_date);
			if($diff < 0){
				$diff = 0;
			}
			$fee = $diff*$rental_fine_fee;
		}else{
			$obj["type"] = "borrow";
			if($rental_period > 1){

				// $due_date_day_items = date("w", strtotime('+'.$rental_period.' days'));
				//$rental_period_day = $rental_period;
				// echo $due_date_day_items."xxxxxxxxx<br/>";
				while ($this->chack_holiday_transaction($rental_period) > 0) {
					# code...
					
					//echo "rental_period_day = ".$rental_period."<br/>";
					// $this->chack_holiday_transaction($rental_period);
					$rental_period = $rental_period+1;
				}
		

				
				$obj["due_date"] = date("Y-m-d", strtotime('+'.$rental_period.' days'));
			}else{
				$obj["due_date"] = date("Y-m-d", strtotime('+1 day'));
			}
			$fee = $rental_fee;
		}
		$obj["transaction_aid"] = $transaction_aid;
		$obj["fee"] = $fee;
		$transactionByUser[$key] = $obj;
		$this->session->set_userdata('transactionByUser_'.$user_aid,$transactionByUser);
		// print_r($transactionByUser);
		$result_obj = array("status" => 'success',"msg" => '' );
		echo json_encode($result_obj);
		return "";
	}

	function ajax_remove_product_to_transaction_by_user($sid=""){
		if(!is_staff_or_higher()){
			$msg = 'Error occurred. Permission denied.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$user_aid = $this->input->get_post('user_aid');
		$product_type_aid = $this->input->get_post('product_type_aid');
		$barcode = $this->input->get_post('barcode');
		if(is_blank($user_aid) || is_blank($product_type_aid) || is_blank($barcode)){
			$msg = 'Error occurred. Data is null.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->user_model,"user");
		$tmp = array();
		$tmp["aid"] = $user_aid;
		$this->user->set_where($tmp);
		$user_result = $this->user->load_record();
		if(!is_var_array($user_result)){
			$msg = 'Error occurred. User not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid, true);
		$model = $this->get_product_model($product_type_aid);
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_copy_name, $model_copy_name);
		$tmp = array();
		$tmp['barcode'] = $barcode;
		$this->{$model_copy_name}->set_where($tmp);
		$product_copy_result = $this->{$model_copy_name}->load_record(true);
		if(!is_var_array($product_copy_result)){
			$msg = 'Error occurred. Book not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$copy_aid = get_array_value($product_copy_result,"aid","0");

		$transactionByUser = $this->session->userdata('transactionByUser_'.$user_aid);
		// print_r($transactionByUser);
		if(is_var_array($transactionByUser)){
			$key = $product_type_aid."_".$copy_aid;
			// echo "key = $key";
			unset($transactionByUser[$key]);
			$this->session->set_userdata('transactionByUser_'.$user_aid,$transactionByUser);
		}
		$result_obj = array("status" => 'success',"msg" => '' );
		echo json_encode($result_obj);
		return "";
	}

	function ajax_clear_product_to_transaction_by_user($sid=""){
		if(!is_staff_or_higher()){
			$msg = 'Error occurred. Permission denied.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$user_aid = $this->input->get_post('user_aid');
		$this->session->set_userdata('transactionByUser_'.$user_aid,"");
		$result_obj = array("status" => 'success',"msg" => '' );
		echo json_encode($result_obj);
		return "";
	}

	function ajax_change_due_date_product_to_transaction_by_user($sid=""){
		if(!is_staff_or_higher()){
			$msg = 'Error occurred. Permission denied.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$user_aid = $this->input->get_post('user_aid');
		$product_type_aid = $this->input->get_post('product_type_aid');
		$barcode = $this->input->get_post('barcode');
		if(is_blank($user_aid) || is_blank($product_type_aid) || is_blank($barcode)){
			$msg = 'Error occurred. Data is null.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->user_model,"user");
		$tmp = array();
		$tmp["aid"] = $user_aid;
		$this->user->set_where($tmp);
		$user_result = $this->user->load_record();
		if(!is_var_array($user_result)){
			$msg = 'Error occurred. User not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid, true);
		$model = $this->get_product_model($product_type_aid);
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_copy_name, $model_copy_name);
		$tmp = array();
		$tmp['barcode'] = $barcode;
		$this->{$model_copy_name}->set_where($tmp);
		$product_copy_result = $this->{$model_copy_name}->load_record(true);
		if(!is_var_array($product_copy_result)){
			$msg = 'Error occurred. Book not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$copy_aid = get_array_value($product_copy_result,"aid","0");

		$transactionByUser = $this->session->userdata('transactionByUser_'.$user_aid);
		// print_r($transactionByUser);
		if(is_var_array($transactionByUser)){
			$key = $product_type_aid."_".$copy_aid;
			// echo "key = $key";
			$obj = $transactionByUser[$key];
			$obj["due_date"] = $this->input->get_post('new_due_date');
			$transactionByUser[$key] = $obj;
			$this->session->set_userdata('transactionByUser_'.$user_aid,$transactionByUser);
		}
		$result_obj = array("status" => 'success',"msg" => '' );
		echo json_encode($result_obj);
		return "";
	}

	function ajax_save_product_transaction_by_user($sid=""){
		$user_aid = $this->input->get_post('user_aid');
		if(is_blank($user_aid)){
			$msg = 'Error occurred. Data is null.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->user_model,"user");
		$tmp = array();
		$tmp["aid"] = $user_aid;
		$this->user->set_where($tmp);
		$user_result = $this->user->load_record();
		if(!is_var_array($user_result)){
			$msg = 'Error occurred. User not founded.';
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$transactionByUser = $this->session->userdata('transactionByUser_'.$user_aid);
		// print_r($transactionByUser);
		if(!is_var_array($transactionByUser)){
			$result_obj = array("status" => 'warning',"msg" => '&nbsp;');
			echo json_encode($result_obj);
			return "";
		}

		$tmp_transactionByUser = $transactionByUser;
		foreach ($transactionByUser as $key => $obj) {
			$type = get_array_value($obj,"type","");
			$product_type_aid = get_array_value($obj,"product_type_aid","0");
			$copy_aid = get_array_value($obj,"copy_aid","0");
			$title = get_array_value($obj,"title","");
			$barcode = get_array_value($obj,"barcode","");
			$due_date = get_array_value($obj,"due_date","");
			$fee = get_array_value($obj,"fee","0");
			$transaction_aid = get_array_value($obj,"transaction_aid","0");

			$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid, true);
			$model = $this->get_product_model($product_type_aid);
			$model_copy_name = get_array_value($model,"product_copy_model","");
			$model_product_ref_user_section_name = get_array_value($model,"product_ref_user_section_model","");
			$model_name = get_array_value($model,"product_model","");
			$this->db->flush_cache();
			$this->db->_reset_select();
			$this->load->model($model_copy_name, $model_copy_name);
			$tmp = array();
			$tmp['barcode'] = $barcode;
			$this->{$model_copy_name}->set_where($tmp);
			$product_copy_result = $this->{$model_copy_name}->load_record(true);
			if(!is_var_array($product_copy_result)){
				$msg = 'Error occurred. Book not founded.';
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
			}

			$copy_aid = get_array_value($product_copy_result,"aid","0");

			$status = get_array_value($product_copy_result,"status","0");
			// if($status != "1"){
			// 	$msg = 'This book was inactive. Can not borrow.';
			// 	$result_obj = array("status" => 'error',"msg" => $msg );
			// 	echo json_encode($result_obj);
			// 	return "";
			// }
			
			// $sql="SELECT * FROM `holiday` WHERE to_date >= '$due_date' and from_date <= '$due_date'  ";
			// $exe=mysql_query($sql);
			// $data=mysql_fetch_array($exe);
			// $count = mysql_num_rows($exe);
			
			
			
			//     if($count>0){
			// 		$due_date = strtotime($data[to_date]);
   //   		  	    $due_date = date("Y-m-d", strtotime("+1 days",$due_date));
			// 	}
				/*$msg = "$due_date";
				$result_obj = array("status" => 'error',"msg" => $msg );
			 	echo json_encode($result_obj);
			 	return "";*/
				

			$is_ebook = get_array_value($product_copy_result,"is_ebook","0");
			if($is_ebook == "1"){
				$msg = 'This book is ebook. Can not borrow.';
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
			}

			$key = $product_type_aid."_".$copy_aid;

			if($type == "borrow"){
				
				
		// 		$sql="select user_section_aid from user where aid = '$user_aid' ";
		// $exe=mysql_query($sql);
		// $datax=mysql_fetch_array($exe);
		
		//  $sql="select * from user_section where aid = '$datax[user_section_aid]'   ";
		// $exe=mysql_query($sql);
		// $data=mysql_fetch_array($exe);
		
		// $sql="select * from transaction where  return_status = '0' and user_aid = '$user_aid'    ";
		// $exe=mysql_query($sql);
		// $row=mysql_num_rows($exe);
		
		// if($row>=$data[default_rental_book_period]){
		//     $msg = 'You borrow books overdue.';
		// 	$result_obj = array("status" => 'error',"msg" => $msg );
		// 	echo json_encode($result_obj);
		// 	return "";
		// }
				
				
				$obj = array();
				$obj["user_owner_aid"] = getUserOwnerAid($this);
				$obj["user_aid"] = $user_aid;
				$obj["product_main_aid"] = get_array_value($product_copy_result,"product_main_aid","");
				$obj["product_type_aid"] = $product_type_aid;
				$obj["parent_aid"] = get_array_value($product_copy_result,"parent_aid","");
				$obj["copy_aid"] = $copy_aid;
				$obj["status"] = '1';
				$obj["borrowing_period"] = '';
				$obj["title"] = $title;
				$obj["barcode"] = $barcode;
				$obj["borrowing_date"] = get_db_now();
				$obj["due_date"] = $due_date;
				$obj["returning_date"] = '';
				$obj["return_status"] = '0';
				$obj["total_renew"] = '0';
				$obj["last_renewal_date"] = '';
				$obj["remark"] = '';
				$obj["pre_paid"] = $fee;
				$obj["post_paid"] = '0';
				$obj["damage_lost_paid"] = '0';

				$this->load->model($this->main_model,"main");
				$this->main->insert_record($obj);

				$this->db->flush_cache();
				$this->db->_reset_select();
				$this->load->model($model_copy_name, $model_copy_name);
				$tmp = array();
				$tmp['shelf_status'] = "2";
				$this->{$model_copy_name}->set_where(array("aid"=>$copy_aid));
				$product_copy_result = $this->{$model_copy_name}->update_record($tmp);

				$this->db->flush_cache();
				$this->db->_reset_select();
				$this->load->model($this->reserve_product_model,"reserve_product");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["copy_aid"] = $copy_aid;
				$tmp["user_aid"] = $user_aid;
				$this->reserve_product->set_where($tmp);
				$tmp = array();
				$tmp["status"][] = "1";
				$tmp["status"][] = "2";
				$this->reserve_product->set_where_in($tmp);
				$cond = array();
				$cond["status"] = "3";
				$reserve_result = $this->reserve_product->update_record($cond);

				unset($tmp_transactionByUser[$key]);

			}else if($type == "return"){
				if($transaction_aid > 0){
					$this->load->model($this->main_model,"main");
					$obj = array();
					$obj["returning_date"] = get_db_now();
					$obj["return_status"] = '1';
					$obj["post_paid"] = $fee;
					$this->main->set_where(array("aid"=>$transaction_aid));
					$this->main->update_record($obj);

					$this->db->flush_cache();
					$this->db->_reset_select();
					$this->load->model($model_copy_name, $model_copy_name);
					$tmp = array();
					$tmp['barcode'] = $barcode;
					$this->{$model_copy_name}->set_where($tmp);
					$this->{$model_copy_name}->update_record(array("shelf_status"=>"1"));

					unset($tmp_transactionByUser[$key]);

				}

			}else if($type == "reserve"){
				$obj = array();
				$obj["product_type_aid"] = $product_type_aid;
				$obj["product_type_cid"] = get_array_value($product_type_detail,"cid","");
				$obj["copy_aid"] = $copy_aid;
				$obj["parent_aid"] = get_array_value($product_copy_result,"parent_aid","");
				$obj["title"] = $title;
				$obj["barcode"] = $barcode;
				$obj["user_aid"] = $user_aid;
				$obj["status"] = '1';
				$this->db->flush_cache();
				$this->load->model($this->reserve_product_model,"reserve_product");
				$aid = $this->reserve_product->insert_record($obj);

				unset($tmp_transactionByUser[$key]);
			}

		}

		$this->session->set_userdata('transactionByUser_'.$user_aid,$tmp_transactionByUser);
		$result_obj = array("status" => 'success',"msg" => '');
		echo json_encode($result_obj);
		return "";
	}

	function ajax_send_mail_overdue($sid=""){
		@define("thisAction",'ajax_send_mail_overdue');
		if(!is_staff_or_higher()){
			$msg = ('Error occurred. Permission denied.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$aid = $this->input->get_post('aid_selected');
		if(is_blank($aid)){
			$msg = ('Error occurred. Data is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$user_owner_aid = getUserOwnerAid($this);
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(true);
		if(!is_var_array($objResult)){
			$msg = ('Error occurred. Can not find this transaction.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'title', $aid).' [aid = '.$aid.']';
		$return_status = get_array_value($objResult,"return_status","");
		if($return_status == "1"){
			$msg = ('Can not send overdue email. This transaction was returned.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$today_date = date('Y-m-d');
		$due_date = get_datetime_pattern("db_date_format",get_array_value($objResult,"due_date",""),"");
		$returning_date = get_datetime_pattern("db_date_format",get_array_value($objResult,"returning_date",""),"");
		$return_status = get_array_value($objResult,"return_status","0");

		if($today_date > $due_date){
			$barcode = get_array_value($objResult,"barcode","-");
			$title = get_array_value($objResult,"title","-");
			$borrowing_date = get_array_value($objResult,"borrowing_date","-");
			$due_date = get_array_value($objResult,"due_date","-");
			$user_aid = get_array_value($objResult,"user_aid","-");
			$full_name_th = get_array_value($objResult,"full_name_th","-");
			$email = get_array_value($objResult,"email","-");

			$product_list = '';
			$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
			$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Check Out Date</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Due Date</td></tr>";
			$class = '';
			$product_list .= "<tr style='".$class."'><td style='border-bottom:1px solid #868A9C'>".$barcode."</td><td style='border-bottom:1px solid #868A9C'>".$title."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", $borrowing_date, "-") ."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", $due_date, "-") ."</td></tr>";
			$product_list .= "</table>";

			$subject = $this->lang->line('mail_subject_transaction_overdue');
			$body = $this->lang->line('mail_content_transaction_overdue');
			$body = str_replace("{doc_type}", "&nbsp;" , $body);
			$body = str_replace("{email}", $email , $body);
			$body = str_replace("{name}", $full_name_th , $body);
			$body = str_replace("{product_list}", $product_list , $body);
			// echo $body;
			$this->load->library('email');
			$config = $this->get_init_email_config();
			if(is_var_array($config)){ 
				$this->email->initialize($config); 
				$this->email->set_newline("\r\n");
			}
			
			// Send message
			$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
			$this->email->to($email);
			$this->email->bcc(''); 

			$this->email->subject($subject);
			$this->email->message($body);
			// echo $this->email->print_debugger();
			@$this->email->send();
			$this->log_status('Backend : Ads', 'Overdue email has been sent to "'.$email.'" for '.$this_obj_info.'.', $objResult);
			$msg = ('('.$barcode.') '.$title.'<BR>Overdue Notice was sent successfully.');
			$result_obj = array("status" => 'Message',"msg" => $msg );
			echo json_encode($result_obj);
			return "";


		}else{
			$msg = ('Can not send overdue email. This transaction is not overdue.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function chack_holiday_transaction($rental_period){

				$rental_period = $rental_period;
				//echo "due_date_day_items = ".$due_date_day_items;
				
				if($this->chack_due_date_day_transaction($rental_period) > 0){
					//echo "aaaaa = 1";
					return 1;

				}else{


					//$due_date_weekend_items = date("Y-m-d", strtotime('+'.$rental_period.' days'));

					if($this->chack_due_date_weekend_transaction($rental_period) > 0){

							return 1;

					}else{

						$chack_due_date_day_transaction = $this->chack_due_date_day_transaction($rental_period);
						//echo $chack_due_date_day_transaction;
						return $chack_due_date_day_transaction;
					}


				}

				
				
	}

	function chack_due_date_day_transaction($rental_period){

		//echo "rental_period = ".$rental_period."<br/>";	
		$this->load->model($this->holiday_weekend_model,"weekend");
		$weekend_items= $this->weekend->load_holidays();

		$due_date_day_items = date("w", strtotime('+'.$rental_period.' days'));
		//echo "due_date_day_items = ".$due_date_day_items."<br/>";		
				 
					if ($due_date_day_items == "0") {

						$sun = get_array_value($weekend_items,"sun","");
						if($sun == "1"){
							return 1;
						}else{
							return 0;
						}
					}
					if ($due_date_day_items == "1") {
						$mon = get_array_value($weekend_items,"mon","");
						if($mon == "1"){
							return 1;
						}else{
							return 0;
						}
					}
					if ($due_date_day_items == "2") {
						$tue = get_array_value($weekend_items,"tue","");
						if($tue == "1"){
							return 1;
						}else{
							return 0;
						}
					}
					if ($due_date_day_items == "3") {
						$wed = get_array_value($weekend_items,"wed","");
						if($wed == "1"){
							return 1;
						}else{
							return 0;
						}
					}
					if ($due_date_day_items == "4") {
						$thu = get_array_value($weekend_items,"thu","");
						if($thu == "1"){
							return 1;
						}else{
							return 0;
						}
					}
					if ($due_date_day_items == "5") {
						$fri = get_array_value($weekend_items,"fri","");
						if($fri == "1"){
							return 1;
						}else{
							return 0;
						}
					}
					if ($due_date_day_items == "6") {
						$sat = get_array_value($weekend_items,"sat","");
						if($sat == "1"){
							return 1;
						}else{
							return 0;
						}
					}
					
	} 

	function chack_due_date_weekend_transaction($rental_period){

		//echo "chack_due_date_weekend_transaction<br/>";
		$due_date_weekend_items = date("Y-m-d", strtotime('+'.$rental_period.' days'));
		$this->load->model($this->holiday_model,"holiday");
		$day_items = $this->holiday->load_holidays();
		//echo "due_date_weekend_items =".$due_date_weekend_items."<br/>";
		$num_day_items= $this->holiday->count_records(true);
		$num = 0;
			if(is_var_array($day_items)){	
				foreach($day_items as $day){
					$num = $num+1;
					//echo "num = ".$num."<br/>";
					//echo "num_day_items = ".$num_day_items."<br/>";
					$from_date = get_array_value($day,"from_date","");
					$to_date = get_array_value($day,"to_date","");

					if($from_date !=  $to_date){
						//echo "string = 1<br/>";
						if($this->chack_from_date_transaction($from_date , $to_date , $due_date_weekend_items) > 0){
							return 1;
						}else{
							if ($num_day_items == $num){
								return 0;
							}
						}

					}else{
						//echo "string = 2<br/>";
						if(get_array_value($day,"from_date","") == $due_date_weekend_items){
							//echo "string = 1<br/>";
							return 1;
						}else{
							if ($num_day_items == $num){
								return 0;
							}
						}
					}
				}

			}
					
	} 

	function chack_from_date_transaction($from_date , $to_date , $due_date_weekend_items){

			if($from_date <= $due_date_weekend_items &&  $to_date >= $due_date_weekend_items){
				//$num_day = (($to_date - $due_date_weekend_items)/  ( 60 * 60 * 24 )) + 1;
				return 1;

			}else{
				return 0;
			}
			// $from_date = strtotime($from_date);
			// $to_date = strtotime($to_date);
			// $num_day = (($to_date - $from_date)/  ( 60 * 60 * 24 )) + 1;
			// // $num_date_day = ($from_date/  ( 60 * 60 * 24 )) + 1;
			// echo "from_date = ".$from_date."<br/>";
			// echo "to_date = ".$to_date."<br/>";
			// echo "num_day = ".$num_day."<br/>";
			// for ($i=0; $i < $num_day ; $i++) { 
			// 	$rental_period_update = $rental_period+$i;
			// 	$due_day_items = date("Y-m-d", strtotime('+'.$rental_period_update.' days'));
			// 	if($due_date_weekend_items == $due_day_items){
			// 		echo "string = 1<br/>";
			// 		return 1;
			// 	}
			// }

	}

}

?>