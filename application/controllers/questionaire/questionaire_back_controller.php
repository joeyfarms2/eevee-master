<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Questionaire_back_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();

		if(CONST_HAS_QUESTIONAIRE != "1"){
			redirect('admin');
		}

		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'questionaire');
		define("thisAdminSubMenu",'questionaire');
		@define("folderName",'questionaire/questionaire_back/questionaire');
		
		define("TXT_TITLE",'Questionnaire management');
		define("TXT_INSERT_TITLE",'Questionnaire management : Add new questionnaire');
		define("TXT_UPDATE_TITLE",'Questionnaire management : Edit questionnaire');
				
		$this->questionaire_model = 'Questionaire_model';	
		$this->questionaire_question_model = 'Questionaire_question_model';	
		$this->questionaire_question_model = 'Questionaire_question_model';	
		$this->questionaire_question_choice_model = 'Questionaire_question_choice_model';	
		$this->questionaire_category_model = 'Questionaire_category_model';	
		$this->questionaire_user_activity_model = 'Questionaire_user_activity_model';	
		$this->questionaire_user_submit_model = 'Questionaire_user_submit_model';	
		$this->user_model = 'User_model';
		$this->user_department_model = 'User_department_model';

		$this->lang->load('mail');
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/questionaire_list';
		$this->data["header_title"] = TXT_TITLE;

		$this->load->model($this->questionaire_category_model,"category");
		$this->data["master_questionaire_category"] = $this->category->load_questionaire_categories();
		
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/questionaire_form';

		// Load all categories
		$this->load->model($this->questionaire_category_model,"category");
		$this->data["master_questionaire_category"] = $this->category->load_questionaire_categories();
		
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

		$this->session->set_userdata('questionaireBackDataSearchSession','');

		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->questionaire_model,'main');
		$this->load->model($this->questionaire_question_model, 'question');
		$this->load->model($this->questionaire_question_choice_model, 'choice');
		$this->load->model($this->questionaire_user_activity_model, 'invi');

		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$item_detail = $this->main->load_record(false);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"title","");

			// Load questions
			$this->question->set_where('questionaire_aid', $item_detail['aid']);
			$this->question->set_order_by('weight ASC');
			$tmp_q = $this->question->load_records(false);
			
			// Load questions' choices
			$this->choice->set_where('questionaire_aid', $item_detail['aid']);
			$this->choice->set_order_by('weight ASC');
			$tmp_c = $this->choice->load_records(false);

			// Pair question-choices into an array
			$arr_q_and_c = array();
			if (is_var_array($tmp_q) && count($tmp_q) > 0) {
				foreach ($tmp_q as $item_q) {
					$arr_q_and_c[$item_q['aid']] = array();
					$arr_q_and_c[$item_q['aid']]['question'] = $item_q;
					$arr_q_and_c[$item_q['aid']]['choices'] = '';
				}
			}
			if (is_var_array($tmp_c) && count($tmp_c) > 0) {
				foreach ($tmp_c as $item_c) {
					if (isset($arr_q_and_c[$item_c['question_aid']]['choices'])) {
						if (!is_var_array($arr_q_and_c[$item_c['question_aid']]['choices'])) {
							$arr_q_and_c[$item_c['question_aid']]['choices'] = array();
						}
						$arr_q_and_c[$item_c['question_aid']]['choices'][] = $item_c;
					}
				}
			}
			$this->data['questions'] = $arr_q_and_c;

			// echo '<pre>';
			// print_r($arr_q_and_c);
			// echo '</pre>';
			// exit;

			// Load invitations
			$this->invi->set_where('questionaire_aid', $item_detail['aid']);
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
			$this->data["message"] = set_message_error('Cannot find the specific questionnaire.');
			$this->data["js_code"] = "$('#title').focus();";
			$this->show();
			return "";
		}
	}
	
	function _save_form($is_ajax=false) {
		@define("thisAction",'_save_form');
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		
		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}

		$this->load->model($this->questionaire_model,'main');
		$this->load->model($this->questionaire_question_model, 'question');
		$this->load->model($this->questionaire_question_choice_model, 'choice');
		$this->load->model($this->questionaire_user_activity_model, 'invi');
		
		$name = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();

		$data["user_owner_aid"] = $user_owner_aid;
		$data["title"] = $this->input->get_post('title');
		$data["weight"] = $this->input->get_post('weight');
		$data["description"] = trim(strip_tags($this->input->get_post('description')));
		$data["status"] = $this->input->get_post('status');
		$data["publish_date"] = get_db_now();
		$data["expiry_date"] = get_datetime_pattern("db_datetime_format", $this->input->get_post('expiry_date'), NULL);

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
		
		$cid = "";
		$return_status = 'success';
		if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if(!is_var_array($itemResult)){
				$return_status = 'error';
				$this->data["message"] = set_message_error("Questionnaire not found.");
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
		}
		
		if($return_status == 'success' && is_blank($cid)){
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isQuestionaireCodeExits($cid) );
		}
		$data["cid"] = trim($cid);
		
		if($return_status == 'success' && $command == "_insert"){
			$data["posted_by"] = getUserLoginAid($this->user_login_info);
			// if($this->check_duplicate($data,$command)){
			// 	$this->form();
			// 	return "";
			// }
			

			// Insert questionnaire
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$questionaire_aid = $aid;

				// Insert questions
				$q_weight = $this->input->get_post('q_weight');
				$q_title = $this->input->get_post('q_title');
				$q_type = $this->input->get_post('q_type');
				$rdo_choice = $this->input->get_post('rdo_choice');
				$chk_choice = $this->input->get_post('chk_choice');
				if (is_var_array($q_weight) && count($q_weight) > 0) {
					for ($i=0; $i<count($q_weight) ; $i++) {
						$tmp_title = trim(strip_tags($q_title[$i]));
						if (!empty($tmp_title)) {
							$data_q = array();
							$data_q['questionaire_aid'] = $questionaire_aid;
							$data_q['title'] = trim(strip_tags($q_title[$i]));
							$data_q['weight'] = trim(strip_tags($q_weight[$i]));
							$data_q['question_type'] = strtolower(trim(strip_tags($q_type[$i])));
							$data_q['user_owner_aid'] = $user_owner_aid;
							$data_q['posted_by'] = getUserLoginAid($this->user_login_info);
							$question_aid = $this->question->insert_record($data_q);

							// Insert choices (if any)
							if ($data_q['question_type'] == 'rdo' && $question_aid > 0) {
								if (isset($rdo_choice[$i]) && count($rdo_choice[$i]) > 0) {
									$choices = $rdo_choice[$i];
									foreach ($choices as $k=>$choice_title) {
										$data_c = array();
										$data_c['questionaire_aid'] = $questionaire_aid;
										$data_c['question_aid'] = $question_aid;
										$data_c['title'] = $choice_title;
										$data_c['weight'] = $k;
										$data_c['user_owner_aid'] = $user_owner_aid;
										$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
										$this->choice->insert_record($data_c);
									}
								}
							}
							else if ($data_q['question_type'] == 'chk' && $question_aid > 0) {
								if (isset($chk_choice[$i]) && count($chk_choice[$i]) > 0) {
									$choices = $chk_choice[$i];
									foreach ($choices as $k=>$choice_title) {
										$data_c = array();
										$data_c['questionaire_aid'] = $questionaire_aid;
										$data_c['question_aid'] = $question_aid;
										$data_c['title'] = $choice_title;
										$data_c['weight'] = $k;
										$data_c['user_owner_aid'] = $user_owner_aid;
										$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
										$this->choice->insert_record($data_c);
									}
								}
							}

						}
					}
				}


				// Insert invitations
				if (is_var_array($list_invi_user) && count($list_invi_user) > 0) {
					foreach ($list_invi_user as $staff_user_aid) {
						$this->invi->insert_record(array(
									'questionaire_aid' => $aid,
									'user_aid' => $staff_user_aid,
									'has_submitted' => NULL
								)
							);
					}
				}

				$this->log_status('Backend : Insert questionnaire', '['.$name.'] just added into database.');
				// redirect('admin/questionaire/status/'.md5('success'));
			}
			else {
				$return_status = 'error';
				$this->log_error('Backend : Insert questionnaire', 'Command insert_record() fail. Can not insert '.$name);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
						
		}
		else if($return_status == 'success' && $command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			// if($this->check_duplicate($data,$command)){
			// 	$this->form();
			// 	return "";
			// }
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);


			// Delete and Re-insert questions
			$questionaire_aid = $aid;
			$this->choice->set_where(array('questionaire_aid' => $questionaire_aid));
			$this->choice->delete_records();

			$q_aid = $this->input->get_post('q_aid');
			$q_weight = $this->input->get_post('q_weight');
			$q_title = $this->input->get_post('q_title');
			$q_type = $this->input->get_post('q_type');
			$rdo_choice = $this->input->get_post('rdo_choice');
			$chk_choice = $this->input->get_post('chk_choice');
			$current_q_aid = array();
			if (is_var_array($q_weight) && count($q_weight) > 0) {
				foreach ($q_weight as $i => $item) {
					$tmp_title = trim(strip_tags($q_title[$i]));
					if (!empty($tmp_title)) {
						$data_q = array();
						$data_q['questionaire_aid'] = $questionaire_aid;
						$data_q['title'] = trim(strip_tags($q_title[$i]));
						$data_q['weight'] = trim(strip_tags($q_weight[$i]));
						$data_q['question_type'] = strtolower(trim(strip_tags($q_type[$i])));
						$data_q['user_owner_aid'] = $user_owner_aid;
						$data_q['posted_by'] = getUserLoginAid($this->user_login_info);
						if ($q_aid[$i] > 0) {
							$this->question->set_where(array('aid' => $q_aid[$i]));
							$this->question->update_record($data_q);
							$question_aid = $q_aid[$i];
						}
						else {
							$question_aid = $this->question->insert_record($data_q);
						}
						$current_q_aid[] = $question_aid;

						// Insert choices (if any)
						if ($data_q['question_type'] == 'rdo' && $question_aid > 0) {
							if (isset($rdo_choice[$i]) && !empty($rdo_choice[$i])) {
								$choices = $rdo_choice[$i];
								foreach ($choices as $k=>$choice_title) {
									$data_c = array();
									$data_c['questionaire_aid'] = $questionaire_aid;
									$data_c['question_aid'] = $question_aid;
									$data_c['title'] = $choice_title;
									$data_c['weight'] = $k;
									$data_c['user_owner_aid'] = $user_owner_aid;
									$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
									$this->choice->insert_record($data_c);
								}
							}
						}
						else if ($data_q['question_type'] == 'chk' && $question_aid > 0) {
							if (isset($chk_choice[$i]) && !empty($chk_choice[$i])) {
								$choices = $chk_choice[$i];
								foreach ($choices as $k=>$choice_title) {
									$data_c = array();
									$data_c['questionaire_aid'] = $questionaire_aid;
									$data_c['question_aid'] = $question_aid;
									$data_c['title'] = $choice_title;
									$data_c['weight'] = $k;
									$data_c['user_owner_aid'] = $user_owner_aid;
									$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
									$this->choice->insert_record($data_c);
								}
							}
						}

					}

				}
			}

			// Delete the remaining which not exist in the latest one
			if (count($current_q_aid) > 0) {
				$this->question->set_where(array('questionaire_aid' => $questionaire_aid));
				$this->question->set_where_not_in(array('aid' => $current_q_aid));
				$this->question->delete_records();
			}


			// Insert / Delete invitations 
			$this->invi->set_where('questionaire_aid', $aid);
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
									'questionaire_aid' => $aid,
									'user_aid' => $staff_user_aid,
									'has_submitted' => NULL
								)
							);
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
						$this->invi->set_where(array('questionaire_aid' => $aid, 'user_aid' => $cur_staff_user_aid));
						$this->invi->delete_records();
					}
				}
			}
			$this->main->update_total_submit($aid);


			if($rs) {
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Admin : Update questionnaire',  '['.$name.'] has been updated.');
				// if($save_option){
				// 	redirect('admin/questionaire/add');
				// }else{
				// 	redirect('admin/questionaire/status/'.md5('success'));
				// }
				// return "";
			}
			else {
				$return_status = 'error';
				$this->log_error('Backend : Update questionnaire', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
			
		}
		else {
			$return_status = 'no-command';
			$this->log_error('Backend : User', 'Command not found.');
			// redirect('admin/questionaire/status/'.md5('no-command'));
			// return "";
		}


		// Return 
		$return = array();
		$return['aid'] = $aid;
		$return['status'] = $return_status;
		$redirect_url = '';
		if ($is_ajax === true) {
			if ($return_status == 'error') {
				$redirect_url = 'admin/questionaire/status/'.md5('error');
			}
		}
		else {
			if ($return_status == 'success') {
				switch ($save_option) {
					default:
					case '0':
					case '1':
						$redirect_url = 'admin/questionaire/status/'.md5('success');
						break;
					// case '1':
					// 	$redirect_url = 'admin/questionaire/add';
					// 	break;
					case '2':
						$redirect_url = 'admin/questionaire/'.$aid.'/preview/'.rand();
						break;
				}
			}
			else if ($return_status == 'error') {
				$return['data'] = $this->data;
			}
			else {
				$redirect_url = 'admin/questionaire/status/'.md5('no-command');
			}
				
		}	
		$return['redirect_url'] = $redirect_url;
		return $return;

	}

	public function save_and_publish() {
		@define("thisAction",'save_and_publish');
		$command = $this->input->get_post('command');
		$email_notify = $this->input->get_post('email_notify');

		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		$return = $this->_save_form(false);

		if (!empty($return['redirect_url'])) {

			if($email_notify == "1"){
				// If checkbox notify has been checked, send email to everyone
				$this->load->model($this->questionaire_user_activity_model, "activity");
				$to_emails = $this->activity->get_emails($return['aid']);
				if (!empty($to_emails)) {
					$this->load->model($this->questionaire_model, 'questionaire');
					$this->questionaire->set_where(array("aid" => $return['aid']));
					$rs_news = $this->questionaire->load_record(false);

					if (is_var_array($rs_news)) {
						$subject = $this->lang->line('mail_subject_new_questionaire_publish');
						$body = $this->lang->line('mail_content_new_questionaire_publish');

						// $body = eregi_replace("[\]",'',$body);
						$subject = str_replace("{questionaire_title}", get_array_value($rs_news, 'title', 'New questionnaire has been published') , $subject);
						$body = str_replace("{doc_type}", "&nbsp;" , $body);
						$body = str_replace("{questionaire_title}", get_array_value($rs_news, 'title', 'click here to view') , $body);
						$body = str_replace("{questionaire_url}", site_url('questionaire/form/'.get_array_value($rs_news, 'aid')), $body);

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
						//echo $this->email->print_debugger();
						$this->log_debug('Questionnaire : Send emails to all staff about new questionnaire has been published.', '[subject = '.$subject.'] [to_emails = '.$to_emails.'] '.$subject);
					}

					
				}
			}
			redirect($return['redirect_url']);
		}
		else {
			$this->data = get_array_value($return, 'data');
			$this->form();
		}
	}

	public function ajax_save_preview()
	{
		$return = $this->_save_form(true);
		if ($return['status'] == 'success' && $return['aid'] > 0) {
			echo json_encode(array('status' => 'success', 'preview_url' => site_url('admin/questionaire/'.$return['aid'].'/preview/'.rand()), 'aid' => $return['aid']));
		}
		else {
			echo json_encode(array('status' => 'error', 'msg' => '', 'redirect_url' => $return['redirect_url']));
		}
		return;
	}

	function preview_detail($aid) {
		@define("thisAction","form");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = '<span class="textStart">Questionnaires</span>';
		$this->data["view_the_content"] = $this->default_theme_front . '/questionaire/questionaire_form';
				
		$this->db->flush_cache();
		$this->load->model($this->questionaire_model, "questionaire");
		$this->load->model($this->questionaire_question_model, "question");
		$this->load->model($this->questionaire_question_choice_model, "choice");
		$result = $this->questionaire->increase_total_view($aid);	
		

		$this->questionaire->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$item_detail = $this->questionaire->load_record(false);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;

			// Load questions
			$this->question->set_where('questionaire_aid', $item_detail['aid']);
			$this->question->set_order_by('weight ASC');
			$tmp_q = $this->question->load_records(false);
			
			// Load questions' choices
			$this->choice->set_where('questionaire_aid', $item_detail['aid']);
			$this->choice->set_order_by('weight ASC');
			$tmp_c = $this->choice->load_records(false);

			// Pair question-choices into an array
			$arr_q_and_c = array();
			if (is_var_array($tmp_q) && count($tmp_q) > 0) {
				foreach ($tmp_q as $item_q) {
					$arr_q_and_c[$item_q['aid']] = array();
					$arr_q_and_c[$item_q['aid']]['question'] = $item_q;
					$arr_q_and_c[$item_q['aid']]['choices'] = '';
				}
			}
			if (is_var_array($tmp_c) && count($tmp_c) > 0) {
				foreach ($tmp_c as $item_c) {
					if (isset($arr_q_and_c[$item_c['question_aid']]['choices'])) {
						if (!is_var_array($arr_q_and_c[$item_c['question_aid']]['choices'])) {
							$arr_q_and_c[$item_c['question_aid']]['choices'] = array();
						}
						$arr_q_and_c[$item_c['question_aid']]['choices'][] = $item_c;
					}
				}
			}
			// echo '<pre>';
			// print_r ($arr_q_and_c);
			// echo '<pre>';
			// exit;
			$this->data['js_code'] = '$("#btn_submit").prop("disabled", true); $("#btn_submit").addClass("disabled");';
			$this->data['questions'] = $arr_q_and_c;
			$this->load->view($this->default_theme_front.'/tpl_questionaire', $this->data);
		}
		else {
			redirect('questionaire/status/'.md5('questionaire-not-found'));
		}
	}

	function __save(){
		@define("thisAction",'save');
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		
		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit;
		
		$this->load->model($this->questionaire_model,'main');
		$this->load->model($this->questionaire_question_model, 'question');
		$this->load->model($this->questionaire_question_choice_model, 'choice');
		$this->load->model($this->questionaire_user_activity_model, 'invi');
		
		$name = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();

		$data["user_owner_aid"] = $user_owner_aid;
		$data["title"] = $this->input->get_post('title');
		$data["weight"] = $this->input->get_post('weight');
		$data["description"] = trim(strip_tags($this->input->get_post('description')));
		$data["status"] = $this->input->get_post('status');
		$data["publish_date"] = get_db_now();
		$data["expiry_date"] = get_datetime_pattern("db_datetime_format", $this->input->get_post('expiry_date'), NULL);

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
		
		$cid = "";
		if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if(!is_var_array($itemResult)){
				$this->data["message"] = set_message_error("Questionnaire not found.");
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
		}
		
		if(is_blank($cid)){
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isQuestionaireCodeExits($cid) );
		}
		$data["cid"] = trim($cid);
		
		if($command == "_insert"){
			$data["posted_by"] = getUserLoginAid($this->user_login_info);
			// if($this->check_duplicate($data,$command)){
			// 	$this->form();
			// 	return "";
			// }
			

			// Insert questionnaire
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$questionaire_aid = $aid;

				// Insert questions
				$q_weight = $this->input->get_post('q_weight');
				$q_title = $this->input->get_post('q_title');
				$q_type = $this->input->get_post('q_type');
				$rdo_choice = $this->input->get_post('rdo_choice');
				$chk_choice = $this->input->get_post('chk_choice');
				if (is_var_array($q_weight) && count($q_weight) > 0) {
					for ($i=0; $i<=count($q_weight) ; $i++) {
						$tmp_title = trim(strip_tags($q_title[$i]));
						if (!empty($tmp_title)) {
							$data_q = array();
							$data_q['questionaire_aid'] = $questionaire_aid;
							$data_q['title'] = trim(strip_tags($q_title[$i]));
							$data_q['weight'] = trim(strip_tags($q_weight[$i]));
							$data_q['question_type'] = strtolower(trim(strip_tags($q_type[$i])));
							$data_q['user_owner_aid'] = $user_owner_aid;
							$data_q['posted_by'] = getUserLoginAid($this->user_login_info);
							$question_aid = $this->question->insert_record($data_q);

							// Insert choices (if any)
							if ($data_q['question_type'] == 'rdo' && $question_aid > 0) {
								if (isset($rdo_choice[$i]) && count($rdo_choice[$i]) > 0) {
									$choices = $rdo_choice[$i];
									foreach ($choices as $k=>$choice_title) {
										$data_c = array();
										$data_c['questionaire_aid'] = $questionaire_aid;
										$data_c['question_aid'] = $question_aid;
										$data_c['title'] = $choice_title;
										$data_c['weight'] = $k;
										$data_c['user_owner_aid'] = $user_owner_aid;
										$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
										$this->choice->insert_record($data_c);
									}
								}
							}
							else if ($data_q['question_type'] == 'chk' && $question_aid > 0) {
								if (isset($chk_choice[$i]) && count($chk_choice[$i]) > 0) {
									$choices = $chk_choice[$i];
									foreach ($choices as $k=>$choice_title) {
										$data_c = array();
										$data_c['questionaire_aid'] = $questionaire_aid;
										$data_c['question_aid'] = $question_aid;
										$data_c['title'] = $choice_title;
										$data_c['weight'] = $k;
										$data_c['user_owner_aid'] = $user_owner_aid;
										$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
										$this->choice->insert_record($data_c);
									}
								}
							}

						}
					}
				}


				// Insert invitations
				if (is_var_array($list_invi_user) && count($list_invi_user) > 0) {
					foreach ($list_invi_user as $staff_user_aid) {
						$this->invi->insert_record(array(
									'questionaire_aid' => $aid,
									'user_aid' => $staff_user_aid,
									'has_submitted' => NULL
								)
							);
					}
				}

				$this->log_status('Backend : Insert questionnaire', '['.$name.'] just added into database.');
				redirect('admin/questionaire/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert questionnaire', 'Command insert_record() fail. Can not insert '.$name);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
						
		}
		else if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			// if($this->check_duplicate($data,$command)){
			// 	$this->form();
			// 	return "";
			// }
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);


			// Delete and Re-insert questions
			$questionaire_aid = $aid;
			$this->choice->set_where(array('questionaire_aid' => $questionaire_aid));
			$this->choice->delete_records();

			$q_aid = $this->input->get_post('q_aid');
			$q_weight = $this->input->get_post('q_weight');
			$q_title = $this->input->get_post('q_title');
			$q_type = $this->input->get_post('q_type');
			$rdo_choice = $this->input->get_post('rdo_choice');
			$chk_choice = $this->input->get_post('chk_choice');
			$current_q_aid = array();
			if (is_var_array($q_weight) && count($q_weight) > 0) {
				foreach ($q_weight as $i => $item) {
					$tmp_title = trim(strip_tags($q_title[$i]));
					if (!empty($tmp_title)) {
						$data_q = array();
						$data_q['questionaire_aid'] = $questionaire_aid;
						$data_q['title'] = trim(strip_tags($q_title[$i]));
						$data_q['weight'] = trim(strip_tags($q_weight[$i]));
						$data_q['question_type'] = strtolower(trim(strip_tags($q_type[$i])));
						$data_q['user_owner_aid'] = $user_owner_aid;
						$data_q['posted_by'] = getUserLoginAid($this->user_login_info);
						if ($q_aid[$i] > 0) {
							$this->question->set_where(array('aid' => $q_aid[$i]));
							$this->question->update_record($data_q);
							$question_aid = $q_aid[$i];
						}
						else {
							$question_aid = $this->question->insert_record($data_q);
						}
						$current_q_aid[] = $question_aid;

						// Insert choices (if any)
						if ($data_q['question_type'] == 'rdo' && $question_aid > 0) {
							if (isset($rdo_choice[$i]) && count($rdo_choice[$i]) > 1) {
								$choices = $rdo_choice[$i];
								foreach ($choices as $k=>$choice_title) {
									$data_c = array();
									$data_c['questionaire_aid'] = $questionaire_aid;
									$data_c['question_aid'] = $question_aid;
									$data_c['title'] = $choice_title;
									$data_c['weight'] = $k;
									$data_c['user_owner_aid'] = $user_owner_aid;
									$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
									$this->choice->insert_record($data_c);
								}
							}
						}
						else if ($data_q['question_type'] == 'chk' && $question_aid > 0) {
							if (isset($chk_choice[$i]) && count($chk_choice[$i]) > 0) {
								$choices = $chk_choice[$i];
								foreach ($choices as $k=>$choice_title) {
									$data_c = array();
									$data_c['questionaire_aid'] = $questionaire_aid;
									$data_c['question_aid'] = $question_aid;
									$data_c['title'] = $choice_title;
									$data_c['weight'] = $k;
									$data_c['user_owner_aid'] = $user_owner_aid;
									$data_c['posted_by'] = getUserLoginAid($this->user_login_info);
									$this->choice->insert_record($data_c);
								}
							}
						}

					}

				}
			}

			// Delete the remaining which not exist in the latest one
			if (count($current_q_aid) > 0) {
				$this->question->set_where(array('questionaire_aid' => $questionaire_aid));
				$this->question->set_where_not_in(array('aid' => $current_q_aid));
				$this->question->delete_records();
			}


			// Insert / Delete invitations 
			$this->invi->set_where('questionaire_aid', $aid);
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
									'questionaire_aid' => $aid,
									'user_aid' => $staff_user_aid,
									'has_submitted' => NULL
								)
							);
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
						$this->invi->set_where(array('questionaire_aid' => $aid, 'user_aid' => $cur_staff_user_aid));
						$this->invi->delete_records();
					}
				}
			}
			$this->main->update_total_submit($aid);


			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Admin : Update questionnaire',  '['.$name.'] has been updated.');
				if($save_option){
					redirect('admin/questionaire/add');
				}else{
					redirect('admin/questionaire/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update questionnaire', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : User', 'Command not found.');
			redirect('admin/questionaire/status/'.md5('no-command'));
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
		$this->load->model($this->questionaire_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this questionnaire.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Questionnaire', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Questionnaire', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
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
		$this->load->model($this->questionaire_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this questionnaire.');
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
			$this->log_status('Backend : Delete questionnaire', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete questionnaire', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
		
	function ajax_get_main_list($sid){
		@define("thisAction",'ajax_get_main_list');
		$this->load->model($this->questionaire_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('questionaireBackDataSearchSession');		
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

		$search_questionaire_category = $this->getDataFromInput('search_questionaire_category');
		$data_where = "";
		if(is_var_array($search_questionaire_category))
		foreach($search_questionaire_category as $item){
			$data_where[] = $item;
			$data_search["search_questionaire_category"][] = $item;
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
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list['results']);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'aid',"col_show"=>'name_action',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title_action',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Expiration Date',"field_order"=>'expiry_date',"col_show"=>'expiry_date_txt',"title_class"=>'w80 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Created Date',"field_order"=>'created_date',"col_show"=>'created_date_txt',"title_class"=>'w80 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Submit',"field_order"=>'total_submit',"col_show"=>'total_submit',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Published?',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('questionaireBackDataSearchSession',$data_search);	
		
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
			$item["name_action"] = '<a href="'.site_url('admin/questionaire/edit/'.get_array_value($item,"aid","")).'">'.get_text_pad(get_array_value($item,"aid","0"),0,8).'</a>';
			$item["title_action"] = '<a href="'.site_url('admin/questionaire/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title","&mdash;").'</a>';

			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this questionnaire." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this questionnaire." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_highlight = get_array_value($item,"is_highlight","0");
			if($is_highlight == 1){
				$item["is_highlight_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this questionnaire." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'is_highlight=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_highlight_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this questionnaire." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'is_highlight=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_recommended = get_array_value($item,"is_recommended","0");
			if($is_recommended == 1){
				$item["is_recommended_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this questionnaire." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'is_recommended=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_recommended_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this questionnaire." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'is_recommended=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_home = get_array_value($item,"is_home","0");
			if($is_home == 1){
				$item["is_home_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this questionnaire." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'is_home=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_home_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this questionnaire." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/questionaire\', \''.get_array_value($item,"aid","").'\', \'is_home=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this questionnaire." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/questionaire\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';

				if (get_array_value($item, 'total_submit', 0) > 0) {
					$item["action"] .= '<a class="btn btn-info btn-xs" title="Click to \'Export\' this questionnaire." href="'.site_url('admin/questionaire/'.get_array_value($item,"aid","").'/export-excel').'"><i class="fa fa-save"></i></a>&nbsp;&nbsp;&nbsp;';
				}
			}

			$result[] = $item;
		}
		
		return $result;
	}

	function export_excel($questionaire_aid) {
		if ($questionaire_aid > 0) {
			$this->load->model($this->questionaire_user_submit_model, 'user_submit');
			$result_list = $this->user_submit->load_export_data($questionaire_aid);
			if(is_var_array($result_list)){

				// echo '<pre>';
				// print_r($result_list);
				// echo '</pre>';
				// exit;

				$this->load->library('PHPExcel');
				$title_column_color = '8FE0FF';
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
				$objPHPExcel->getProperties()->setTitle("User download List");
				$objPHPExcel->getProperties()->setDescription("User download list");
				// Set Default Style
				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
				$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); 
				// Rename Sheet
				$objPHPExcel->getActiveSheet()->setTitle('User download List');
				// Set column width
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				
				$objPHPExcel->getActiveSheet()->setCellValue('A1', "#");
				$objPHPExcel->getActiveSheet()->setCellValue('B1', "User");
				$objPHPExcel->getActiveSheet()->setCellValue('C1', "Question");
				$objPHPExcel->getActiveSheet()->setCellValue('D1', "Answer");
				$objPHPExcel->getActiveSheet()->setCellValue('E1', "Answered Date");
				$sharedStyle1 = new PHPExcel_Style();
				$sharedStyle1->applyFromArray($array_style_summary_title);
				$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:E1");
				
				$irow = 2;
				foreach($result_list as $item){
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$irow, get_array_value($item,"weight",""));
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$irow, get_array_value($item,"full_name",""));
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$irow, get_array_value($item,"question_title",""));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$irow, get_array_value($item,"answer",""));
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$irow, get_array_value($item,"created_date",""));
					$irow++;
				}
				
				$filename ="questionaire_".date("ymd_His").".xls";
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
		else {
			
		}
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

	function isQuestionaireCodeExits($cid){
		$this->load->model($this->questionaire_model,"main");
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