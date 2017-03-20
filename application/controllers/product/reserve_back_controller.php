<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Reserve_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'reservation');
		define("thisAdminSubMenu",'');
		@define("folderName",'product/product_back/reserve');
		
		define("TXT_TITLE",'Reservation management');
		define("TXT_INSERT_TITLE",'Reservation management : Add new reservation');
		define("TXT_UPDATE_TITLE",'Reservation management : Edit reservation');

		$this->lang->load('mail');
	}

	function set_main_model($type="product"){
		switch ($type) {
			case 'digital':
				$this->main_model = 'Reserve_model';
				break;

			default:
				$this->main_model = 'Reserve_product_model';
				break;
		}
	}
	
	function index($type="product"){
		$this->data["init_adv_search"] = "clear";
		$this->show($type,"",true);
	}
	
	function show($type="product", $msg="", $clear=false){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/reserve_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->data["type"] = $type;

		$barcode = $this->input->get('barcode');
		$this->data["barcode"] = $barcode;
		$user_cid = $this->input->get('user_cid');
		$this->data["user_cid"] = $user_cid;

		// echo "barcode = $barcode , user_cid = $user_cid<BR>";
		// echo "clear = $clear<BR>";
		if(!$clear && (!is_blank($barcode) || !is_blank($user_cid))){
			$this->session->set_userdata('reserveBackDataSearchSession','');
			$this->data["init_adv_search"] = "";
			if(!is_blank($barcode)){
				$search_post_word = $barcode;
				// echo "search_post_word = $search_post_word<BR>";
				$data_where["barcode"] = $barcode;
				$data_search["search_in"][] = "barcode";
				$data_search["search_post_word"] = $search_post_word;
			}else if(!is_blank($user_cid)){
				$search_post_word = $user_cid;
				// echo "search_post_word = $search_post_word<BR>";
				$data_where["user.cid"] = $user_cid;
				$data_search["search_in"][] = "user.cid";
				$data_search["search_post_word"] = $search_post_word;
			}
			$this->session->set_userdata('reserveBackDataSearchSession',$data_search);	
		}else{
			$this->data["barcode"] = '';
			$this->data["user_cid"] = '';
		}

		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form($type="product"){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/reserve_form';
		$this->data["type"] = $type;
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add($type="product"){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		// $this->data["js_code"] = "$('#name').focus();";

		$this->session->set_userdata('reserveBackDataSearchSession','');

		$this->form($type);
	}
	
	function edit($type="product", $aid=""){
		@define("thisAction",'edit');
		$this->set_main_model($type);
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$item_detail = $this->main->load_record(true);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"name","");
			$this->form($type);
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific reservation.');
			$this->data["js_code"] = "$('#name').focus();";
			$this->show($type);
			return "";
		}
	}
	
	function save($type="product"){
		@define("thisAction",'save');
		$this->set_main_model($type);
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		
		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		
		$this->load->model($this->main_model,'main');
		
		$name = trim($this->input->get_post('name'));
		$data["name"] = $name;
		$url = $this->input->get_post('url');
		if(is_blank($url)){
			$url = getUrlString($name);
		}
		$data["url"] = $url;
		$data["product_type_aid"] = $this->input->get_post('product_type_aid');
		$data["icon"] = $this->input->get_post('icon');
		$data["weight"] = $this->input->get_post('weight');
		$data["status"] = $this->input->get_post('status');
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form($type);
				return "";
			}
			
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert reservation', '['.$name.'] just added into database.', $data);
				redirect('admin/reservation-'.$type.'/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert reservation', 'Command insert_record() fail. Can not insert '.$name, $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form($type);
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
				$this->form($type);
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Backend : Update reservation',  '['.$name.'] has been updated.', $data);
				if($save_option){
					redirect('admin/reservation-'.$type.'/add');
				}else{
					redirect('admin/reservation-'.$type.'/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update reservation', 'Command update_record() fail. Can not update '.$name.'['.$aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form($type);
				return "";
			}
			
		}else{
			$this->log_error('Backend : Reservation', 'Command not found.', $data);
			redirect('admin/reservation-'.$type.'/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($type="product", $data="",$command=""){
		$this->set_main_model($type);
		$aid = get_array_value($data,"aid","");
		$name = get_array_value($data,"name","");
		$url = get_array_value($data,"url","");

		$this->main->set_and_or_where(array("name"=>$name, "url"=>$url));
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
			$has_url = false;
			foreach($objResult as $item){
				$tmp_name = get_array_value($item,"name","");
				$tmp_url = get_array_value($item,"url","");
				if(!is_blank($name) && $name == $tmp_name){
					if(!is_blank($error_txt) && !$has_name) $error_txt .= '<BR>';
					if(!$has_name) $error_txt .= '"'.$name.'" is used.';
					$js_code .= '$("#name").addClass("error");';
					if(is_blank($obj_name)) $obj_name = "name";
					$has_name = true;
				}
				if(!is_blank($url) && $url == $tmp_url){
					if(!is_blank($error_txt) && !$has_url) $error_txt .= '<BR>';
					if(!$has_url) $error_txt .= '"'.$url.'" is used.';
					$js_code .= '$("#url").addClass("error");';
					if(is_blank($obj_name)) $obj_name = "url";
					$has_url = true;
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
			
	function ajax_set_value($type="product", $sid="", $status=""){
		@define("thisAction",'ajax_set_value');
		$this->set_main_model($type);
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

		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(true);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this reservation.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		$email = get_array_value($objResult,"email","");
		$title = get_array_value($objResult,"title","-");
		$barcode = get_array_value($objResult,"barcode","-");
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Reservation', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$today_date = date("Y-m-d");
			$next_3_day = date("Y-m-d", strtotime('+1 day'));
			switch ($f_value) {
				case '2': //confirm
					$expiration_date = $this->input->get_post('expiration_date');
					$expiration_date = get_datetime_pattern("Y-m-d", $expiration_date, "");
					if(is_blank($expiration_date)){
						$expiration_date = $next_3_day;
					}
					$this->load->model($this->main_model,"main");
					$this->main->set_where(array("aid"=>$aid));
					$cond = array();
					$cond["expiration_date"] = $expiration_date;
					$this->main->update_record($cond);

					$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
					$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Pick up within</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Status</td></tr>";
					$class = "background-color:D1DCF1";
					$product_list .= "<tr style='".$class."'><td style='border-bottom:1px solid #868A9C'>".$barcode."</td><td style='border-bottom:1px solid #868A9C'>".$title."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", $expiration_date, "-") ."</td><td style='border-bottom:1px solid #868A9C'>Approved</td></tr>";
					$product_list .= "</table>";

					$subject = $this->lang->line('mail_subject_reserve_product_confirm');
					$body = $this->lang->line('mail_content_reserve_product_confirm');
					$body = str_replace("{doc_type}", "&nbsp;" , $body);
					$body = str_replace("{email}", $email , $body);
					$body = str_replace("{name}", trim(get_user_info($objResult)) , $body);
					$body = str_replace("{product_list}", $product_list , $body);
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
					$log_arr["subject"] = $subject;
					$log_arr["body"] = $body;
					$this->log_debug('Reservation', '[Send mail to '.$email.'] ', $log_arr);
					// echo $this->email->print_debugger();
					@$this->email->send();
					break;
				
				case '0': //cancel
					$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
					$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Status</td></tr>";
					$class = "background-color:D1DCF1";
					$product_list .= "<tr style='".$class."'><td style='border-bottom:1px solid #868A9C'>".$barcode."</td><td style='border-bottom:1px solid #868A9C'>".$title."</td><td style='border-bottom:1px solid #868A9C'>Cancelled</td></tr>";
					$product_list .= "</table>";

					$subject = $this->lang->line('mail_subject_reserve_product_cancel');
					$body = $this->lang->line('mail_content_reserve_product_cancel');
					$body = str_replace("{doc_type}", "&nbsp;" , $body);
					$body = str_replace("{email}", $email , $body);
					$body = str_replace("{name}", trim(get_user_info($objResult)) , $body);
					$body = str_replace("{product_list}", $product_list , $body);
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
					$log_arr["subject"] = $subject;
					$log_arr["body"] = $body;
					$this->log_debug('Reservation', 'Send mail to ['.$email.'] ', $log_arr);
					// echo $this->email->print_debugger();
					// @$this->email->send();
					break;
			}

			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Reservation', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_delete_one($type="product"){
		@define("thisAction",'ajax_delete_one');
		$this->set_main_model($type);
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
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this reservation.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "name", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "name", $aid);
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete reservation', $this_obj_info.' has been deleted.', $objResult);

			$this->load->model($this->product_main_field_model,"product_main_field");
			$this->product_main_field->set_where(array("product_main_aid"=>$aid));
			$this->product_main_field->delete_records();

			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete reservation', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_get_main_list($type="product", $sid){
		@define("thisAction",'ajax_get_main_list');
		$this->set_main_model($type);
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('reserveBackDataSearchSession');		
		// print_r($dsSession);
		
		$search_post_word = $this->getDataFromInput('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->getDataFromInput('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in)){
			foreach($search_in as $item){
				$data_where[$item] = $search_post_word;
				$data_search["search_in"][] = $item;
			}
		}else{
			$barcode = $this->input->get_post('barcode');
			$user_cid = $this->input->get_post('user_cid');
			if(!is_blank($barcode)){
				$search_post_word = $barcode;
				// echo "search_post_word = $search_post_word<BR>";
				$data_where["barcode"] = $barcode;
				$data_search["search_in"][] = "barcode";
				$data_search["search_post_word"] = $search_post_word;
			}else if(!is_blank($user_cid)){
				$search_post_word = $user_cid;
				// echo "search_post_word = $search_post_word<BR>";
				$data_where["user.cid"] = $user_cid;
				$data_search["search_in"][] = "user.cid";
				$data_search["search_post_word"] = $search_post_word;
			}
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
		$order_by_option = $this->get_order_by_info($search_order_by,'created_date asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));

		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($type, $result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Barcode',"field_order"=>'barcode',"col_show"=>'barcode_action',"title_class"=>'w80 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title_action',"title_class"=>'hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'User',"field_order"=>'user.first_name_th',"col_show"=>'user_action',"title_class"=>'hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Reserved Date',"field_order"=>'created_date',"col_show"=>'created_date_txt',"title_class"=>'w150 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'Item<BR>Status',"field_order"=>'',"col_show"=>'item_status',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');	
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Approval<BR>Status',"field_order"=>'status',"col_show"=>'status_icon',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');	
		$header_list[] = array("sort_able"=>'0',"title_show"=>'Action',"field_order"=>'',"col_show"=>'action',"title_class"=>'w200 hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('reserveBackDataSearchSession',$data_search);	
		
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
	
	function manage_column_detail($type="product", $result_list){
		$this->set_main_model($type);
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$item["barcode_action"] = '<a href="'.site_url('admin/reservation-'.$type.'/show?barcode='.get_array_value($item,"barcode","")).'">'.get_array_value($item,"barcode","-").'</a>';
			$item["title_action"] = '<a href="'.site_url('admin/reservation-'.$type.'/show?barcode='.get_array_value($item,"barcode","")).'">'.get_array_value($item,"title","-").'</a>';
			$item["user_action"] = '<a href="'.site_url('admin/reservation-'.$type.'/show?user_cid='.get_array_value($item,"user_cid","")).'">'.get_array_value($item,"full_name_th","-").'</a>';

			$status = get_array_value($item,"status","0");
			$product_type_aid = get_array_value($item,"product_type_aid","");
			$copy_aid = get_array_value($item,"copy_aid","");
			$model = $this->get_product_model($product_type_aid);
			$model_name = get_array_value($model,"product_copy_model","");
			$this->db->flush_cache();
			$this->db->_reset_select();
			$this->load->model($model_name, $model_name);
			$tmp = array();
			$tmp['aid'] = $copy_aid;
			$this->{$model_name}->set_where($tmp);
			$copy_result = $this->{$model_name}->load_record(true);	
			$shelf_status = get_array_value($copy_result,"shelf_status","");

			$this->load->model($this->main_model,"main");
			$tmp = array();
			$tmp['product_type_aid'] = $product_type_aid;
			$tmp['copy_aid'] = $copy_aid;
			$tmp['status'] = "2";
			$this->main->set_where($tmp);
			$reserve_result = $this->main->load_records(false);
			// echo "<br>sql : ".$this->db->last_query();
			$has_confirm = false;
			if(is_var_array($reserve_result)){
				$has_confirm = true;
			}

			// echo "shelf_status = $shelf_status , status = $status<BR>";
			$item["action"] = '';
			$item["item_status"] = 'On Shelf';
			switch ($shelf_status) {
				case '3':
				case '4':
					if($status == "1" || $status == "2"){
						$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Cancel\' this reservation." onclick="processChangeValue(\' cancel this reservation\', \'admin/reservation-'.$type.'\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-times"></i> Cancel</a>&nbsp;&nbsp;&nbsp;';
					}
					// $item["item_status"] = '<span class="" title="This book is lost or damage."><i class="fa fa-ban"></i> Not available</span>&nbsp;&nbsp;&nbsp;';
					$item["item_status"] = 'Lost/Damage';
					break;
				
				case '2':
					if($status == "1" || $status == "2"){
						$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Cancel\' this reservation." onclick="processChangeValue(\' cancel this reservation\', \'admin/reservation-'.$type.'\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-times"></i> Cancel</a>&nbsp;&nbsp;&nbsp;';
					}
					// $item["item_status"] = '<span class="" title="This book is borrowed."><i class="fa fa-ban"></i> Borrowed</span>&nbsp;&nbsp;&nbsp;';
					$item["item_status"] = 'Borrowed';
					break;
				
				case '1':
					if($status == "1" && !$has_confirm){
						$item["action"] .= '<a class="btn btn-success btn-xs" title="Click to \'Approve\' this reservation." onclick="processChangeReserveValue(\' book this reservation\', \'admin/reservation-'.$type.'\', \''.get_array_value($item,"aid","").'\', \'status=2\')"><i class="fa fa-check"></i> Approve</a>&nbsp;&nbsp;&nbsp;';
					}else if($has_confirm){
						$item["item_status"] = 'Reserved';
					} 

					if($status == "1" || $status == "2"){
						$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Cancel\' this reservation." onclick="processChangeValue(\' cancel this reservation\', \'admin/reservation-'.$type.'\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-times"></i> Cancel</a>&nbsp;&nbsp;&nbsp;';
					}
					break;
			}

			if(is_root_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/reservation-'.$type.'\', \'<strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
			}
			$result[] = $item;
		}
		
		return $result;
	}
	
	function status($type="product", $type="")	{
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
		$this->show($type);
	}

}

?>