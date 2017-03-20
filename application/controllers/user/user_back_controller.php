<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");

class User_back_controller extends User_init_controller {

	function __construct()	{
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = 'backend';
		
		define("thisAdminTabMenu",'user');
		define("thisAdminSubMenu",'user');
		@define("folderName",'user/');
		
		define("TXT_TITLE",'User management');
		define("TXT_INSERT_TITLE",'User form : Add new user');
		define("TXT_UPDATE_TITLE",'User form : Edit user');
		
		$this->main_model = 'User_model';
		$this->user_role_model = 'User_role_model';
		$this->user_department_model = 'User_department_model';
		$this->user_section_model = 'User_section_model';
		$this->setting_config_model = 'Setting_config_model';
		$this->event_main_model = 'Event_main_model';
		
		$this->load->model($this->user_role_model,'user_role');
		$this->data["master_user_role"] = $this->user_role->load_master_user_role();

		$this->lang->load('user');
		$this->lang->load('mail');
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show(){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/user/user_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/user/user_form';

		$this->load->model($this->user_department_model, 'department');
		$this->data['departments'] = $this->department->load_records(false);


		$this->load->model($this->user_section_model, 'user_section');
		$this->data['user_section'] = $this->user_section->load_records(false);

		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;

		$this->session->set_userdata('userBackDataSearchSession','');

		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$this->main->set_where($this->main->get_table_name().".user_role_aid >= '".getSessionUserRoleAid()."'");
		$item_detail = $this->main->load_record(false);
		
		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"email","");
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific user.');
			$this->data["js_code"] = '';
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
		
		$activate_code = "";
		$cid = trim($this->input->get_post('cid'));
		$username = trim($this->input->get_post('username'));
		$email = trim($this->input->get_post('email'));
		$password = trim($this->input->get_post('password'));
		$gen_pass = trim($this->input->get_post('gen_pass'));
		$send_activate = trim($this->input->get_post('send_activate'));
		$data["email"] = $email;
		$data["first_name_th"] = $this->input->get_post('first_name_th');
		$data["last_name_th"] = $this->input->get_post('last_name_th');
		$data["display_name"] = $this->input->get_post('display_name');
		$data["address"] = $this->input->get_post('address');
		$data["department_aid"] = $this->input->get_post('department');
		$data["gender"] = $this->input->get_post('gender');
		$data["contact_number"] = $this->input->get_post('contact_number');
		$data["user_role_aid"] = $this->input->get_post('user_role_aid');
		$data["user_section_aid"] = $this->input->get_post('user_section_aid');
		$data["publisher_aid"] = $this->input->get_post('publisher_aid');
		$data["status"] = $this->input->get_post('status');
		$data["position"] = $this->input->get_post('position');
		$data["occupation"] = $this->input->get_post('occupation');
		$data["remark"] = $this->input->get_post('remark');
		$data["note_1"] = $this->input->get_post('note_1');
		$data["note_2"] = $this->input->get_post('note_2');
		$data["note_3"] = $this->input->get_post('note_3');
		$data["note_4"] = $this->input->get_post('note_4');
		$data["birthday"] = get_datetime_pattern("db_date_format",$this->input->get_post('birthday'),NULL);
		$data["registration_date"] = get_datetime_pattern("db_date_format",$this->input->get_post('registration_date'),get_db_now('%Y-%m-%d'));
		$data["expiration_date"] = get_datetime_pattern("db_date_format",$this->input->get_post('expiration_date'),NULL);
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$point_remain = "Not set.";
		if(is_owner_admin_or_higher()){
			$point_remain = $this->input->get_post('point_remain');
			$data["point_remain"] = $point_remain;
		}

		if($command == "_insert"){
			$data["channel"] = "web";
			
			if(is_blank($cid)){
				do{
					$this->load->model($this->setting_config_model,'setting_config');		
					$obj = $this->setting_config->get_config_rni_by_cid("rn-user-1");
					$cid = trim(get_array_value($obj,"barcode",""));
				}while( $this->isUserCidExits($cid) );
			}
			$data["cid"] = $cid;

			if(!is_blank($username)){
				$data["username"] = $username;
			}else{
				$data["username"] = $cid;
			}

			if($this->check_duplicate($data,$command)){
				$this->form();
				return "";
			}

			if($gen_pass == '1' || is_blank($password)){
				$password = $this->main->generate_new_password();
			}
			$data["password"] = $this->main->encryptPassword($password);
			
			$subject = $this->lang->line('mail_subject_new_user_generate');
			$body = $this->lang->line('mail_content_new_user_generate');
			
			$subject_activate = "";
			$body_activate = "";
			
			if(!is_blank($username)){
				$login_type = "Username"; 
				$login_user = $username;
			}else{
				$login_type = "Email"; 
				$login_user = $email;
			}
			
			if($send_activate == '1'){
				$activate_code = $this->main->generate_new_password("8"); 
			}
			$data["activate_code"] = $activate_code;
									
			$this->main->set_trans_start();
			$aid = $this->main->insert_record($data);
			if($aid > 0){			
				$this->log_status('Backend : Insert user', $login_user.' [aid = '.$aid.'] just saved into database with point ['.$point_remain.']. Wating for send email.');

				if(!is_blank($email)){
					// $body = eregi_replace("[\]",'',$body);
					$body = str_replace("{doc_type}", "&nbsp;" , $body);
					$body = str_replace("{name}", trim(get_array_value($data,"email","")) , $body);
					$body = str_replace("{username}", $login_user, $body);
					$body = str_replace("{login_type}", $login_type, $body);
					$body = str_replace("{password}", $password, $body);
					
					$this->load->library('email');
					$config = $this->get_init_email_config();
					if(is_var_array($config)){ 
						$this->email->initialize($config); 
						$this->email->set_newline("\r\n");
					}
					
					// Send message
					$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
					$this->email->to($email);

					$this->email->subject($subject);
					$this->email->message($body);
					//echo $this->email->print_debugger();
					$this->log_debug('User mail : Send password', '['.$email.'] '.$body);
					if(CONST_MODE == 2 || @$this->email->send()){
						$this->log_status('Backend : Insert User', 'Welcome email sent success.'.$login_user.' [aid = '.$aid.'] just been submitted.');
						$this->main->set_trans_commit();
						$this->data["message"] = set_message_success('Data has been saved.');
						
						if($send_activate == '1'){
							$subject = $this->lang->line('mail_subject_new_user_activate');
							$body = $this->lang->line('mail_content_new_user_activate');
							
							// $body = eregi_replace("[\]",'',$body);
							$body = str_replace("{doc_type}", "&nbsp;" , $body);
							$body = str_replace("{name}", trim(get_array_value($data,"email","")) , $body);
							$body = str_replace("{username}", $login_user, $body);
							$body = str_replace("{login_type}", $login_type, $body);
							$body = str_replace("{password}", $password, $body);
							$body = str_replace("{url}", site_url('registration/activate/'.$email.'/'.$activate_code), $body);
							
							$this->load->library('email');
							$config = $this->get_init_email_config();
							if(is_var_array($config)){ 
								$this->email->initialize($config); 
								$this->email->set_newline("\r\n");
							}
							$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
							$this->email->to($email);

							$this->email->subject($subject);
							$this->email->message($body);
							$this->log_debug('User mail : Send activation', '['.$email.'] '.$body);
							if(CONST_MODE == 2 || @$this->email->send()){
								$this->log_status('Backend : Insert user', 'Activation email sent success for '.$login_user.' [aid = '.$aid.']');
							}else{
								$this->log_status('Backend : Insert user', 'Activation email sent fail for '.$login_user.' [aid = '.$aid.']');
							}
						}
					
						if($save_option){
							$this->add();
						}else{
							redirect('admin/user/status/'.md5('success'));
						}
						return "";
						
					}else{
						$this->log_status('Backend : Insert user', 'Welcome email sent fail. '.$login_user.' [aid = '.$aid.'] just been removed.');
						$this->main->set_trans_rollback();
						$this->data["message"] = set_message_error('Sorry, The system can not send email right now.<BR>Please try again later or contact administrator to solve the problem.');
						$this->data["js_code"] = '';
						$this->data["command"] = $command;
						$this->data["item_detail"] = $data;
						$this->form();
						return "";
					}

				}else{
					$this->main->set_trans_commit();
					if($save_option){
						$this->add();
					}else{
						redirect('admin/user/status/'.md5('success'));
					}
					return "";
				}
				
			}else{
				$this->log_error('Backend : Insert user', 'Command insert_record() fail. Can not insert '.$login_user);
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
			$data["cid"] = $cid;
			$data["username"] = $username;
			if($this->check_duplicate($data,$command)){
				$this->form();
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Admin : Update user', $data["email"].' [aid = '.$aid.'] has been updated with point ['.$point_remain.'].');
				if($save_option){
					$this->edit($aid);
				}else{
					redirect('admin/user/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update user', 'Command update_record() fail. Can not update '.$email.' [aid = '.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : User', 'Command not found.');
			redirect('admin/user/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($data="",$command=""){
		$aid = get_array_value($data,"aid","");
		$cid = get_array_value($data,"cid","");
		$user_owner_aid = get_array_value($data,"user_owner_aid","");
		$email = get_array_value($data,"email","");
		$username = get_array_value($data,"username","");
		// $this->main->set_or_where("1=1");
		$cond = "";
		if(!is_blank($cid)){
			$cond["cid"] = $cid;
		}

		if(!is_blank($username)){
			$cond["cid"] = $cid;
		}

		if(!is_blank($email)){
			$cond["email"] = $email;
		}

		if(is_var_array($cond)){
			$this->main->set_or_where($cond);
		}

		$this->main->set_where(array("user_owner_aid"=>$user_owner_aid));

		if(!is_blank($aid)){
			$this->main->set_where_not_equal(array("aid"=>$aid));
		}
		$objResult = $this->main->load_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		// exit;
		if(is_var_array($objResult)){
			$error_txt = "";
			$js_code = "";
			$obj_name = "";
			$has_cid = false;
			$has_email = false;
			$has_username = false;
			foreach($objResult as $item){
				$tmp_cid = get_array_value($item,"cid","");
				$tmp_email = get_array_value($item,"email","");
				$tmp_username = get_array_value($item,"username","");
				if(!is_blank($cid) && $cid == $tmp_cid){
					if(!is_blank($error_txt) && !$has_cid) $error_txt .= '<BR>';
					if(!$has_cid) $error_txt .= 'User Code : "'.$cid.'" is used by other.';
					$js_code .= '$("#cid").addClass("error");';
					if(is_blank($obj_name)) $obj_name = "cid";
					$has_cid = true;
				}
				if(!is_blank($username) && $username == $tmp_username){
					if(!is_blank($error_txt) && !$has_username) $error_txt .= '<BR>';
					if(!$has_username) $error_txt .= 'Username : "'.$username.'" is used by other.';
					$js_code .= '$("#username").addClass("error");';
					if(is_blank($obj_name)) $obj_name = "username";
					$has_username = true;
				}
				if(!is_blank($email) && $email == $tmp_email){
					if(!is_blank($error_txt) && !$has_email) $error_txt .= '<BR>';
					if(!$has_email) $error_txt .= '"'.$email.'" is used by other.';
					$js_code .= '$("#email").addClass("error");';
					if(is_blank($obj_name)) $obj_name = "email";
					$has_email = true;
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
			$msg = set_message_error('Error occurred. Can not find this user.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'email', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : User', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.');
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : User', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.');
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
			$msg = set_message_error('Error occurred. Can not find this user.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, 'email', 'N/A').' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, 'email', $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete user', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete user', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Error occurred. Can not delete data.');
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
		$dsSession = $dataSearchSession->userdata('userBackDataSearchSession');	
		// print_r($dsSession);
		
		$search_post_word = $this->getDataFromInput('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->getDataFromInput('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			// echo "item = $item <BR>";
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

		$search_role = $this->getDataFromInput('search_role');
		$data_where = "";
		if(is_var_array($search_role))
		foreach($search_role as $item){
			$data_where["user_role_aid"][] = $item;
			$data_search["search_role"][] = $item;
		}
		$this->main->set_where_in($data_where);
		$this->main->set_where($this->main->get_table_name().".user_role_aid >= '".getSessionUserRoleAid()."'");
		
		$birthday_from = $this->getDataFromInput('birthday_from');
		$birthday_to = $this->getDataFromInput('birthday_to');
		$data_search["birthday_from"] = $birthday_from;
		$data_search["birthday_to"] = $birthday_to;
		
		if(!is_blank($birthday_from)){
			$this->main->set_where($this->main->get_table_name().'.birthday >=', get_datetime_pattern("db_date_format",$birthday_from,"")." 00:00:00");
		}
		if(!is_blank($birthday_to)){
			$this->main->set_where($this->main->get_table_name().'.birthday <=', get_datetime_pattern("db_date_format",$birthday_to,"")." 23:59:59");
		}		
		
		$expiration_date_from = $this->getDataFromInput('expiration_date_from');
		$expiration_date_to = $this->getDataFromInput('expiration_date_to');
		$data_search["expiration_date_from"] = $expiration_date_from;
		$data_search["expiration_date_to"] = $expiration_date_to;
		
		if(!is_blank($expiration_date_from)){
			$this->main->set_where($this->main->get_table_name().'.expiration_date >=', get_datetime_pattern("db_date_format",$expiration_date_from,"")." 00:00:00");
		}
		if(!is_blank($expiration_date_to)){
			$this->main->set_where($this->main->get_table_name().'.expiration_date <=', get_datetime_pattern("db_date_format",$expiration_date_to,"")." 23:59:59");
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
		$order_by_option = $this->get_order_by_info($search_order_by,'created_date desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		//echo "<br>sql : ".$this->db->last_query()."<br>";

		// echo "<pre>";
		// print_r($result_list);
		// echo "</pre>";

		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		if(is_root_admin_or_higher()){
			$header_list[] = array("sort_able"=>'0',"title_show"=>'<input type="checkbox" name="aid_all" id="aid_all" onclick="changeCheckAll(\'aid_all\',\'aid[]\',false,false)" />',"field_order"=>'',"col_show"=>'checkbox',"title_class"=>'w10 a-center',"result_class"=>'a-center');
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Aid',"field_order"=>'aid',"col_show"=>'aid',"title_class"=>'w30 a-center',"result_class"=>'a-left');
		}
		$header_list[] = array("sort_able"=>'0',"title_show"=>'Avatar',"field_order"=>'',"col_show"=>'avatar_mini',"title_class"=>'hidden-xs w80 a-center',"result_class"=>'hidden-xs a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'cid',"col_show"=>'cid_action',"title_class"=>'w30 a-center',"result_class"=>'a-left');
		if(is_specify_username()){
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Username',"field_order"=>'username',"col_show"=>'username_action',"title_class"=>'w60 a-center',"result_class"=>'a-left');
		}
		$header_list[] = array("sort_able"=>'1',"title_show"=>'E-mail',"field_order"=>'email',"col_show"=>'email_action',"title_class"=>'hidden-xs hidden-sm w250 a-center',"result_class"=>'hidden-xs hidden-sm a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Fullname',"field_order"=>'first_name_th,last_name_th',"col_show"=>'full_name_th',"title_class"=>'hidden-xs hidden-sm  hidden-md w150 a-center',"result_class"=>'hidden-xs hidden-sm  hidden-md a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Role',"field_order"=>'user_role_aid',"col_show"=>'user_role_name',"title_class"=>'hidden-xs hidden-sm hidden-md w60 a-center',"result_class"=>'hidden-xs hidden-sm hidden-md a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'hidden-xs w30 a-center',"result_class"=>'hidden-xs a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Activate?',"field_order"=>'activate_code',"col_show"=>'activation_action',"title_class"=>'hidden-xs w30 a-center',"result_class"=>'hidden-xs a-center');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Joined Date',"field_order"=>'created_date',"col_show"=>'created_date_txt',"title_class"=>'hidden-xs hidden-sm hidden-md w150 a-center',"result_class"=>'hidden-xs hidden-sm hidden-md a-left');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'a-center',"result_class"=>'a-left');

		$this->session->set_userdata('userBackDataSearchSession',$data_search);

		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "sorting" => get_array_value($order_by_option,"sorting",""), "optional"=>$optional, "header_list" => $header_list, "result" => $result_list);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => "No record found.");
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
			$item["cid_action"] = '<a href="'.site_url('admin/user/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"cid"," ").'</a>';
			$item["username_action"] = '<a href="'.site_url('admin/user/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"username"," ").'</a>';
			$item["email_action"] = '<a href="'.site_url('admin/user/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"email"," ").'</a>';
			$item["full_name_th"] = '<a href="'.site_url('admin/user/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"first_name_th"," ").' '.get_array_value($item,"last_name_th"," ").'</a>';

			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this user" onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"email","")).'</strong>\', \'admin/user\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this user" onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"email","")).'</strong>\', \'admin/user\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$activate_code = get_array_value($item,"activate_code","");
			if(!is_blank($activate_code)){
				$item["activation_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this user" onclick="processChangeValue(\' activate <strong>'.removeAllQuote(get_array_value($item,"email","")).'</strong>\', \'admin/user\', \''.get_array_value($item,"aid","").'\', \'activate_code=\')"><i class="fa fa-lock"></i></a>';
			}else{
				$item["activation_action"] = '';
			}

			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this user" onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/user\', \'<strong>'.removeAllQuote(get_array_value($item,"email","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>';
			}
			$item["checkbox"] = '<input type="checkbox" name="aid[]" id="aid_'.get_array_value($item,"aid","").'" value="'.get_array_value($item,"aid","").'" onclick="changeCheckItem(\'aid_all\',\'aid[]\',false,false)" />';
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
				$this->data["message"] = set_message_error('Please try again.');
				$this->data["js_code"] = '';
				break;
		}
		$this->show();
	}

	function export_user(){
		if(!is_owner_admin_or_higher()){
			echo "You don't have authorize to export this report.";
			return"";
		}
	
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->input->get_post('search_in');
		$search_in = preg_split('/,/', $search_in, -1, PREG_SPLIT_NO_EMPTY);
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in)){
			foreach($search_in as $item){
				$data_where[$item] = $search_post_word;
				$data_search["search_in"][] = $item;
			}
			$this->main->set_and_or_like($data_where);
		}
		
		$search_status = $this->input->get_post('search_status');
		$search_status = preg_split('/,/', $search_status, -1, PREG_SPLIT_NO_EMPTY);
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

		$search_role = $this->input->get_post('search_role');
		$search_role = preg_split('/,/', $search_role, -1, PREG_SPLIT_NO_EMPTY);
		$data_where = "";
		if(is_var_array($search_role))
		foreach($search_role as $item){
			$data_where["user_role_aid"][] = $item;
			$data_search["search_role"][] = $item;
		}
		$this->main->set_where_in($data_where);
		$this->main->set_where($this->main->get_table_name().".user_role_aid >= '".getSessionUserRoleAid()."'");
		
		$birthday_from = $this->input->get_post('birthday_from');
		$birthday_to = $this->input->get_post('birthday_to');
		$data_search["birthday_from"] = $birthday_from;
		$data_search["birthday_to"] = $birthday_to;
		
		if(!is_blank($birthday_from)){
			$this->main->set_where($this->main->get_table_name().'.birthday >=', get_datetime_pattern("db_date_format",$birthday_from,"")." 00:00:00");
		}
		if(!is_blank($birthday_to)){
			$this->main->set_where($this->main->get_table_name().'.birthday <=', get_datetime_pattern("db_date_format",$birthday_to,"")." 23:59:59");
		}		
		
		$expiration_date_from = $this->input->get_post('expiration_date_from');
		$expiration_date_to = $this->input->get_post('expiration_date_to');
		$data_search["expiration_date_from"] = $expiration_date_from;
		$data_search["expiration_date_to"] = $expiration_date_to;
		
		if(!is_blank($expiration_date_from)){
			$this->main->set_where($this->main->get_table_name().'.expiration_date >=', get_datetime_pattern("db_date_format",$expiration_date_from,"")." 00:00:00");
		}
		if(!is_blank($expiration_date_to)){
			$this->main->set_where($this->main->get_table_name().'.expiration_date <=', get_datetime_pattern("db_date_format",$expiration_date_to,"")." 23:59:59");
		}
				
		$search_order_by = $this->input->get_post('search_order_by');
		// echo "search_order_by = $search_order_by";
		$data_search["search_order_by"] = $search_order_by;
		$order_by_option = $this->get_order_by_info($search_order_by,'created_date desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));

		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";

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
			$objPHPExcel->getProperties()->setTitle("User List");
			$objPHPExcel->getProperties()->setDescription("User list");
			// Set Default Style
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); 
			// Rename Sheet
			$objPHPExcel->getActiveSheet()->setTitle('User List');
			// Set column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A1', "Email");
			$objPHPExcel->getActiveSheet()->setCellValue('B1', "Firstname");
			$objPHPExcel->getActiveSheet()->setCellValue('C1', "Lastname");
			$objPHPExcel->getActiveSheet()->setCellValue('D1', "Display name");
			$objPHPExcel->getActiveSheet()->setCellValue('E1', "Role");
			$objPHPExcel->getActiveSheet()->setCellValue('F1', "Joined date");
			$objPHPExcel->getActiveSheet()->setCellValue('G1', "Point");
			$sharedStyle1 = new PHPExcel_Style();
			$sharedStyle1->applyFromArray($array_style_summary_title);
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:G1");
			
			$irow = 2;
			foreach($result_list as $item){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$irow, get_array_value($item,"email",""));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$irow, get_array_value($item,"first_name_th",""));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$irow, get_array_value($item,"last_name_th",""));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$irow, get_array_value($item,"display_name",""));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$irow, get_array_value($item,"user_role_name",""));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$irow, get_array_value($item,"registration_date_txt",""));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$irow, get_array_value($item,"point_remain",""));
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
	
	function ajax_get_popup_list($sid){
		@define("thisAction",'ajax_get_main_list');
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));

		// $dataSearchSession = new CI_Session();
		// $dsSession = $dataSearchSession->userdata('userBackDataSearchSession');	
		// print_r($dsSession);
		
		$search_post_word_user = $this->getDataFromInput('search_post_word_user');
		$data_search["search_post_word_user"] = $search_post_word_user;
		$search_in_user = $this->getDataFromInput('search_in_user');
		
		// echo "search_in_user : ".$search_in_user;
		if(!is_blank($search_post_word_user) && is_var_array($search_in_user))
		foreach($search_in_user as $item){
			// echo "item = $item <BR>";
			$data_where[$item] = $search_post_word_user;
			$data_search["search_in_user"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
						
		$search_record_per_page_user = $this->getDataFromInput('search_record_per_page_user', 8);
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->getDataFromInput('page_selected_user');
		$optional["record_per_page"] = $search_record_per_page_user;
		$optional = $this->get_pagination_info($optional);
		$data_search["page_selected_user"] = get_array_value($optional,"page_selected","");
		$data_search["search_record_per_page_user"] = get_array_value($optional,"search_record_per_page",8);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",8));			
		
		$search_order_by_user = $this->getDataFromInput('search_order_by_user');
		$data_search["search_order_by_user"] = $search_order_by_user;
		$order_by_option = $this->get_order_by_info($search_order_by_user,'cid asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_popup_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'cid',"col_show"=>'cid_action',"title_class"=>'w30 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'E-mail',"field_order"=>'email',"col_show"=>'email_action',"title_class"=>'hidden-xs hidden-sm w250 a-center',"result_class"=>'hidden-xs hidden-sm a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Fullname',"field_order"=>'first_name_th,last_name_th',"col_show"=>'full_name_th',"title_class"=>'hidden-xs hidden-sm  hidden-md w150 a-center',"result_class"=>'hidden-xs hidden-sm  hidden-md a-left');

		// $this->session->set_userdata('userBackDataSearchSession',$data_search);

		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "sorting" => get_array_value($order_by_option,"sorting",""), "optional"=>$optional, "header_list" => $header_list, "result" => $result_list);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => "No record found.");
			echo json_encode($result_obj);
			return "";
		}
	}

	function manage_column_popup_detail($result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$item["cid_action"] = '<a href="'.site_url('admin/transaction/user/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"cid"," ").'</a>';
			$item["email_action"] = '<a href="'.site_url('admin/transaction/user/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"email"," ").'</a>';
			$item["full_name_th"] = '<a href="'.site_url('admin/transaction/user/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"first_name_th"," ").' '.get_array_value($item,"last_name_th"," ").'</a>';

			$result[] = $item;
		}
		
		return $result;	
	}

}

?>