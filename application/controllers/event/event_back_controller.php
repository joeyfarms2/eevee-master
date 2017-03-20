<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/event/event_init_controller.php");

class Event_back_controller extends Event_init_controller {

	function __construct(){
		parent::__construct();

		if(CONST_HAS_EVENT != "1"){
			redirect('admin');
		}

		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'event');
		define("thisAdminSubMenu",'event');
		@define("folderName",'event/event_back/event');
		
		define("TXT_TITLE",'Event management');
		define("TXT_INSERT_TITLE",'Event management : Add new event');
		define("TXT_UPDATE_TITLE",'Event management : Edit event');
				
		$this->main_model = 'Event_model';	
		$this->user_model = 'User_model';	
		$this->user_department_model = 'User_department_model';	
		$this->event_category_model = 'Event_category_model';	
		$this->event_user_activity_join_model = 'Event_user_activity_join_model';	

		$this->lang->load('mail');
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/event_list';
		$this->data["header_title"] = TXT_TITLE;

		$this->load->model($this->event_category_model,"category");
		$this->data["master_event_category"] = $this->category->load_category_by_event_main('1');
		
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/event_form';

		// Load all departments and users for invitation section
		$this->load->model($this->user_department_model, 'dept');
		$this->dept->set_where('status', '1');
		$this->dept->set_order_by('name ASC');
		$rs_dept = $this->dept->load_records(false);

		$tmp_dept = '';
		if (is_var_array($rs_dept)) {
			$tmp_dept = array();
			foreach ($rs_dept as $item_dept) {
				if (!isset($tmp_dept[$item_dept['aid']])) {
					$tmp_dept[$item_dept['aid']] = array();
					$tmp_dept[$item_dept['aid']]['aid'] = $item_dept['aid'];
					$tmp_dept[$item_dept['aid']]['name'] = $item_dept['name'];
					$tmp_dept[$item_dept['aid']]['staff'] = array();
				}
			}
		}

		$this->load->model($this->user_model, 'user');
		$this->user->set_where('user_role_aid >', '2');
		$this->user->set_where('status', '1');
		$this->user->set_order_by(array('first_name_th' => 'ASC', 'last_name_th' => 'ASC'));
		$rs_user = $this->user->load_records(false);
		if (is_var_array($rs_user)) {
			foreach ($rs_user as $item_user) {
				if (isset($tmp_dept[$item_user['department_aid']])) {
					$this_user = array();
					$this_user['aid'] = $item_user['aid'];
					$this_user['email'] = trim($item_user['email']);
					$this_user['full_name'] = trim($item_user['first_name_th'].' '.$item_user['last_name_th']);
					array_push( $tmp_dept[$item_user['department_aid']]['staff'], $this_user );
				}
			}
		}

		$this->data["rs_dept"] = $tmp_dept;
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#title').focus();";

		$this->session->set_userdata('eventBackDataSearchSession','');

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
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"title","");

			$this->load->model($this->event_user_activity_join_model, 'invi');
			$this->invi->set_where('event_aid', $item_detail['aid']);
			$tmp_rs = $this->invi->load_records(false);
			$cur_invi_staff = array();
			if (is_var_array($tmp_rs) && count($tmp_rs) > 0) {
				foreach ($tmp_rs as $invi) {
					$cur_invi_staff[] = $invi['user_aid'];
				}
			}
			$this->data["cur_invi_staff"] = $cur_invi_staff;
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific event.');
			$this->data["js_code"] = "$('#title').focus();";
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
		$this->load->model($this->event_user_activity_join_model, 'invi');
		
		$name = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["event_main_aid"] = $this->input->get_post('event_main_aid');
		$data["title"] = $this->input->get_post('title');
		$data["location"] = $this->input->get_post('location');
		$data["weight"] = $this->input->get_post('weight');
		$data["is_public"] = $this->input->get_post('is_public');
		$data["is_home"] = $this->input->get_post('is_home');
		$data["is_highlight"] = $this->input->get_post('is_highlight');
		$data["is_recommended"] = $this->input->get_post('is_recommended');
		$data["description"] = trim($this->input->get_post('description'));
		if ($data["description"] == '<br>' || $data["description"] == '<br/>') 
			$data["description"] = '';
		
		if (!empty($data['description'])) {
			$data['description'] = preg_replace('/(\.\.\/)*uploads/i', PUBLIC_PATH.'uploads', $data['description']);
		}

		$data["ref_link"] = $this->input->get_post('ref_link');
		// $data["posted_by"] = $this->input->get_post('posted_by');
		// $data["posted_email"] = $this->input->get_post('posted_email');
		// $data["posted_ref"] = $this->input->get_post('posted_ref');
		$data["status"] = $this->input->get_post('status');
		$data["is_all_day"] = $this->input->get_post('is_all_day');
		if ($data["is_all_day"] == '1') {
			$event_start_date = get_datetime_pattern("db_datetime_format", $this->input->get_post('event_start_date').'00:00:00', get_db_now());
			$data["event_start_date"] = $event_start_date;
			$event_end_date = get_datetime_pattern("db_datetime_format", $this->input->get_post('event_end_date').'00:00:00', get_db_now());
			$data["event_end_date"] = $event_end_date;
		}
		else {
			$event_start_date = get_datetime_pattern("db_datetime_format", $this->input->get_post('event_start_date').' '.$this->input->get_post('event_start_time').':00', get_db_now());
			$data["event_start_date"] = $event_start_date;
			$event_end_date = get_datetime_pattern("db_datetime_format", $this->input->get_post('event_end_date').' '.$this->input->get_post('event_end_time').':00', get_db_now());
			$data["event_end_date"] = $event_end_date;
		}

		$category_list = "";
		$category = $this->input->get_post('category');
		// echo "category : ".$category;
		if(is_var_array($category)){
			$category_list = ",";
			foreach($category as $item){
				$category_list .= $item.',';
			}
		}
		$data["category"] = $category_list;

		$list_invi_user = $this->input->get_post('list_invi_user');
		
		$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/event/".get_datetime_pattern("Y",$event_start_date,"").'/'.get_datetime_pattern("m",$event_start_date,"");
		$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/event/".get_datetime_pattern("Y",$event_start_date,"").'/'.get_datetime_pattern("m",$event_start_date,"");
		create_directories($upload_base_path);
		
		$cid = "";
		if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if(!is_var_array($itemResult)){
				$this->data["message"] = set_message_error("Event not found.");
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
			}while( $this->isEventCodeExits($cid) );
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
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_EVENT_IMAGE,CONST_EVENT_SIZE_WIDTH_ACTUAL,CONST_EVENT_SIZE_HEIGHT_ACTUAL,99,1);

			$new_file_name_thumb = $cid."-thumb".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_EVENT_IMAGE,CONST_EVENT_SIZE_WIDTH_THUMB,CONST_EVENT_SIZE_HEIGHT_THUMB,99,1);

			$new_file_name_thumb = $cid."-mini".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_EVENT_IMAGE,CONST_EVENT_SIZE_WIDTH_MINI,CONST_EVENT_SIZE_HEIGHT_MINI,99,1);

			$new_file_name_thumb = $cid."-thumb-sq".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_EVENT_IMAGE,CONST_EVENT_SIZE_WIDTH_SQUARE,CONST_EVENT_SIZE_HEIGHT_SQUARE,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$this->log_error('Admin : Event', 'Add new event fail => Upload image error : '.$result_image_thumb["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_image_thumb,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}	
		}		
		
		if($command == "_insert"){
			$data["posted_by"] = getUserLoginAid($this);
			if($this->check_duplicate($data,$command)){
				$this->form();
				return "";
			}
			
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				// Insert / Delete invitations
				if (is_var_array($list_invi_user) && count($list_invi_user) > 0) {
					foreach ($list_invi_user as $staff_user_aid) {
						$this->invi->insert_record(array(
									'event_aid' => $aid,
									'user_aid' => $staff_user_aid,
									'has_joined' => NULL
								)
							);
					}
				}
				

				$list_user_aid = implode(',', $list_invi_user);
				// If checkbox notify has been checked, send email to everyone
				$this->load->model($this->user_model, "user");
				$to_emails = $this->user->get_staff_emails($list_user_aid);
				if (!empty($to_emails)) {
					$this->main->set_where(array("aid" => $aid));
					$rs_event = $this->main->load_record(false);

					if (is_var_array($rs_event)) {
						$subject = $this->lang->line('mail_subject_new_event_publish');
						$body = $this->lang->line('mail_content_new_event_publish');

						// $body = eregi_replace("[\]",'',$body);
						$subject = str_replace("{event_title}", get_array_value($rs_event, 'title', 'New event has been published') , $subject);
						$body = str_replace("{doc_type}", "&nbsp;" , $body);
						$body = str_replace("{event_title}", get_array_value($rs_event, 'title', 'click here to view') , $body);
						$body = str_replace("{event_url}", site_url('event/detail/'.get_array_value($rs_event, 'aid')), $body);


						$cover_image_url = "";
						if (!empty($rs_event['cover_image_file_type'])) {
							$img_cover_image = "<img src='".site_url(get_array_value($rs_event,"upload_path","").get_array_value($rs_event,"cid","").'-actual'.get_array_value($rs_event,"cover_image_file_type",""))."' style='width:100%; max-width:600px; text-align:center;'/>";
							$body = str_replace("{img_cover_image}", $img_cover_image, $body);
						}
						else if (!isset($rs_event['cover_image_file_type']) || !empty($rs_event['cover_image_file_type'])) {
							preg_match("/<img[\w\W]+?\/?>/i", $rs_event['description'], $matches);
							// echo '<pre>'; 
							// print_r($matches);
							// echo '</pre>';
							// exit;
							if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {
								$rs_event['dummy_cover_image'] = $matches[0];
								$img_cover_image = $rs_event['dummy_cover_image'];
								$body = str_replace("{img_cover_image}", $img_cover_image, $body);
								// $body = str_replace("<img", "<img style='width:100%; max-width:600px; text-align:center;' ", $body);
							}
							else {
								$body = str_replace("{img_cover_image}", "", $body);
							}
						}
						else {
							$body = str_replace("{img_cover_image}", "", $body);
						}


						$this->load->library('email');
						$config = $this->get_init_email_config();
						if(is_var_array($config)){ 
							$this->email->initialize($config); 
							$this->email->set_newline("\r\n");
						}

						$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
						$this->email->to('');
						$this->email->bcc($to_emails);
						$this->email->subject($subject);
						$this->email->message($body);
						$this->email->send();
						$debug_msg = $this->email->print_debugger();
						$this->log_debug('Event : Send emails to invited staff about new event has been published.', '[subject = '.$subject.'] [to_emails = '.$to_emails.'] --- '.$debug_msg);
					}
				}



				$this->log_status('Backend : Insert event', '['.$name.'] just added into database.');
				redirect('admin/event/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert event', 'Command insert_record() fail. Can not insert '.$name);
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

			// Insert / Delete invitations 
			$this->invi->set_where('event_aid', $aid);
			$tmp_rs = $this->invi->load_records(false);

			$cur_invi_staff = array();
			$new_invi_staff = array();
			if (is_var_array($tmp_rs) && count($tmp_rs) > 0) {
				foreach ($tmp_rs as $invi) {
					$cur_invi_staff[] = $invi['user_aid'];
				}
			}
			// Insert invitation for new invited staff
			if (is_var_array($list_invi_user) && count($list_invi_user) > 0) {
				foreach ($list_invi_user as $staff_user_aid) {
					$new_invi_staff[] = $staff_user_aid;
					$need_insert = false;
					if (empty($cur_invi_staff)) 
						$need_insert = true;
					else if (count($cur_invi_staff) > 0 && !in_array($staff_user_aid, $cur_invi_staff)) 
						$need_insert = true;

					if ($need_insert == true) {
						$this->invi->insert_record(array(
									'event_aid' => $aid,
									'user_aid' => $staff_user_aid,
									'has_joined' => NULL
								)
							);
					}
				}

				
				$list_user_aid = implode(',', $list_invi_user);
				// If checkbox notify has been checked, send email to everyone
				$this->load->model($this->user_model, "user");
				$to_emails = $this->user->get_staff_emails($list_user_aid);
				if (!empty($to_emails)) {
					$this->main->set_where(array("aid" => $aid));
					$rs_event = $this->main->load_record(false);

					if (is_var_array($rs_event)) {
						$subject = $this->lang->line('mail_subject_new_event_publish');
						$body = $this->lang->line('mail_content_new_event_publish');

						// $body = eregi_replace("[\]",'',$body);
						$subject = str_replace("{event_title}", get_array_value($rs_event, 'title', 'New event has been published') , $subject);
						$body = str_replace("{doc_type}", "&nbsp;" , $body);
						$body = str_replace("{event_title}", get_array_value($rs_event, 'title', 'click here to view') , $body);
						$body = str_replace("{event_url}", site_url('event/detail/'.get_array_value($rs_event, 'aid')), $body);


						$cover_image_url = "";
						if (!empty($rs_event['cover_image_file_type'])) {
							$img_cover_image = "<img src='".site_url(get_array_value($rs_event,"upload_path","").get_array_value($rs_event,"cid","").'-actual'.get_array_value($rs_event,"cover_image_file_type",""))."' style='width:100%; max-width:600px; text-align:center;'/>";
							$body = str_replace("{img_cover_image}", $img_cover_image, $body);
						}
						else if (!isset($rs_event['cover_image_file_type']) || !empty($rs_event['cover_image_file_type'])) {
							preg_match("/<img[\w\W]+?\/?>/i", $rs_event['description'], $matches);
							// echo '<pre>'; 
							// print_r($matches);
							// echo '</pre>';
							// exit;
							if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {
								$rs_event['dummy_cover_image'] = $matches[0];
								$img_cover_image = $rs_event['dummy_cover_image'];
								$body = str_replace("{img_cover_image}", $img_cover_image, $body);
								// $body = str_replace("<img", "<img style='width:100%; max-width:600px; text-align:center;' ", $body);
							}
							else {
								$body = str_replace("{img_cover_image}", "", $body);
							}
						}
						else {
							$body = str_replace("{img_cover_image}", "", $body);
						}


						$this->load->library('email');
						$config = $this->get_init_email_config();
						if(is_var_array($config)){ 
							$this->email->initialize($config); 
							$this->email->set_newline("\r\n");
						}

						$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
						$this->email->to('');
						$this->email->bcc($to_emails);
						$this->email->subject($subject);
						$this->email->message($body);
						$this->email->send();
						$debug_msg = $this->email->print_debugger();
						$this->log_debug('Event : Send emails to invited staff about new event has been published.', '[subject = '.$subject.'] [to_emails = '.$to_emails.'] --- '.$debug_msg);
					}
				}



			}
			// Delete invitation for current staff who have been removed from this update
			if (is_var_array($cur_invi_staff) && count($cur_invi_staff) > 0) {
				foreach ($cur_invi_staff as $cur_staff_user_aid) {
					$need_delete = false;
					if (empty($new_invi_staff)) 
						$need_delete = true;
					if (count($new_invi_staff) > 0 && !in_array($cur_staff_user_aid, $new_invi_staff)) 
						$need_delete = true;

					if ($need_delete == true) {
						$this->invi->set_where(array('event_aid' => $aid, 'user_aid' => $cur_staff_user_aid));
						$this->invi->delete_records();
					}
				}
			}
			$this->main->update_total_join($aid);


			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Admin : Update event',  '['.$name.'] has been updated.');
				if($save_option){
					redirect('admin/event/add');
				}else{
					redirect('admin/event/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update event', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : User', 'Command not found.');
			redirect('admin/event/status/'.md5('no-command'));
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
			$msg = set_message_error('Error occurred. Can not find this event.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Event', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Event', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
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
			$msg = set_message_error('Error occurred. Can not find this event.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "title", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "title", $aid);

		$upload_path = get_array_value($objResult,"upload_path","");
		// echo "upload_path = $upload_path";
		deleteDir($upload_path);

		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete event', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete event', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
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
		$dsSession = $dataSearchSession->userdata('eventBackDataSearchSession');		
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

		$search_event_category = $this->getDataFromInput('search_event_category');
		$data_where = "";
		if(is_var_array($search_event_category))
		foreach($search_event_category as $item){
			$data_where[] = $item;
			$data_search["search_event_category"][] = $item;
		}
		$this->main->set_and_or_like_by_field("category", $data_where);
		
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
		
		$result_list = $this->main->load_records(true, array('join_type' => 'left'));
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list['results']);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'aid',"col_show"=>'name_action',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title_action',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Main',"field_order"=>'*event_main_name',"col_show"=>'event_main_name',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Event Date',"field_order"=>'event_start_date',"col_show"=>'event_period_date_txt_2_lines',"title_class"=>'w80 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Location',"field_order"=>'location',"col_show"=>'location',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Join',"field_order"=>'total_join',"col_show"=>'total_join',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		// if(CONST_EVENT_MODE == "1"){
		// 	$header_list[] = array("sort_able"=>'1',"title_show"=>'Highlight?',"field_order"=>'is_highlight',"col_show"=>'is_highlight_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		// 	$header_list[] = array("sort_able"=>'1',"title_show"=>'Recommened?',"field_order"=>'is_recommended',"col_show"=>'is_recommended_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		// 	$header_list[] = array("sort_able"=>'1',"title_show"=>'In home?',"field_order"=>'is_home',"col_show"=>'is_home_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		// }
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('eventBackDataSearchSession',$data_search);	
		
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
			$item["name_action"] = '<a href="'.site_url('admin/event/edit/'.get_array_value($item,"aid","")).'">'.get_text_pad(get_array_value($item,"aid","0"),0,8).'</a>';
			$item["title_action"] = '<a href="'.site_url('admin/event/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title","&mdash;").'</a>';

			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this event." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this event." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_highlight = get_array_value($item,"is_highlight","0");
			if($is_highlight == 1){
				$item["is_highlight_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this event." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'is_highlight=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_highlight_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this event." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'is_highlight=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_recommended = get_array_value($item,"is_recommended","0");
			if($is_recommended == 1){
				$item["is_recommended_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this event." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'is_recommended=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_recommended_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this event." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'is_recommended=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_home = get_array_value($item,"is_home","0");
			if($is_home == 1){
				$item["is_home_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this event." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'is_home=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_home_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this event." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/event\', \''.get_array_value($item,"aid","").'\', \'is_home=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this event." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/event\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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

	function isEventCodeExits($cid){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("cid"=>$cid));
		$total = $this->main->count_records();
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}
	
	
}

?>