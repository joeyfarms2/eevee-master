<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/news/news_init_controller.php");

class News_back_controller extends News_init_controller {

	function __construct(){
		parent::__construct();

		if(CONST_HAS_NEWS != "1"){
			redirect('admin');
		}

		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'news');
		define("thisAdminSubMenu",'general_info');
		@define("folderName",'news/news_back/news');
		
		define("TXT_TITLE",'News management');
		define("TXT_INSERT_TITLE",'News management : Add new news');
		define("TXT_UPDATE_TITLE",'News management : Edit news');
				
		$this->user_model = 'User_model';

		$this->main_model = 'News_model';
		$this->news_main_model = 'News_main_model';
		$this->news_category_model = 'News_category_model';
		$this->news_gallery_model = 'News_gallery_model';
		$this->news_comment_model = 'News_comment_model';
		$this->news_user_activity_model = 'news_user_activity_model';
		$this->news_comment_user_activity_model = 'news_comment_user_activity_model';
		
		$this->event_model = 'Event_model';
		$this->event_main_model = 'Event_main_model';
		$this->event_category_model = 'Event_category_model';
		$this->event_gallery_model = 'Event_gallery_model';	
		
		$this->view_most_comments_model = 'View_most_comments_model';
		
		$this->lang->load('mail');
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show_comments($msg=""){
		@define("thisAction",'show_comments');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/news_comment_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->data["init_adv_search"] = "clear";
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/news_list';
		$this->data["header_title"] = TXT_TITLE;

		$this->load->model($this->news_category_model,"category");
		$this->data["master_news_category"] = $this->category->load_category_by_news_main('1');

		
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/news_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#title').focus();";
		$this->data["parent_detail"] = "";

		$this->session->set_userdata('newsBackDataSearchSession','');

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
			$item_detail['title'] = get_array_value($item_detail, 'draft_title', $item_detail['title']);
			$item_detail['description'] = get_array_value($item_detail, 'draft_description', $item_detail['description']);

			$this->data["item_detail"] = $item_detail;
			$this->data["parent_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"title","");
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific news.');
			$this->data["js_code"] = "$('#title').focus();";
			$this->show();
			return "";
		}
	}
	
	function _save_form($is_ajax=false){
		@define("thisAction",'_save_form');
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		$aid = "";
		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		
		$this->load->model($this->main_model,'main');
		
		$name = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["news_main_aid"] = $this->input->get_post('news_main_aid');
		$data["title"] = trim(strip_tags($this->input->get_post('title')));
		$data["weight"] = $this->input->get_post('weight');
		$data["is_home"] = $this->input->get_post('is_home');
		$data["is_highlight"] = $this->input->get_post('is_highlight');
		$data["is_recommended"] = $this->input->get_post('is_recommended');
		$data["description"] = $this->input->get_post('description');
		$data["ref_link"] = trim(strip_tags($this->input->get_post('ref_link')));
		
		// $data["posted_by"] = $this->input->get_post('posted_by');
		$data["posted_by"] = getUserLoginAid($this->user_login_info);
		
		// $data["posted_email"] = $this->input->get_post('posted_email');
		// $data["posted_ref"] = $this->input->get_post('posted_ref');
		$data["status"] = $this->input->get_post('status');
		$publish_date = get_datetime_pattern("Y-m-d",$this->input->get_post('publish_date'),get_db_now());
		$data["publish_date"] = $publish_date;

		if (!empty($data['description'])) {
			$data['description'] = preg_replace('/(\.\.\/)*uploads\/'.CONST_CODENAME.'\/userfiles/i', FCK_UPLOAD_PATH, $data['description']);
		}

		$data["draft_title"] = $data["title"];
		$data["draft_description"] = $data["description"];
		// Preview option
		if ($save_option == '2') {
			unset($data["title"]);
			unset($data["description"]);
		}
		
		$category_list = "";
		$category = $this->input->get_post('category');
		if(is_var_array($category)){
			$category_list = ",";
			foreach($category as $item){
				$category_list .= $item.',';
			}
		}
		$data["category"] = $category_list;
		
		$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"");
		$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"");
		create_directories($upload_base_path);
		
		$cid = "";
		$return_status = 'success';
		if ($command == "_update") {
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if (!is_var_array($itemResult)) {
				$return_status = 'error';
				$this->data["message"] = set_message_error("News not found.");
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
			else {
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
		
		if ($return_status == 'success' && is_blank($cid)) {
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isNewsCodeExits($cid) );
		}
		$data["cid"] = trim($cid);
		$data["upload_path"] = $upload_base_path_db.'/'.$cid.'/';
		
		if( $return_status == 'success' && !is_blank(get_array_value($_FILES,"cover_image","")) && !is_blank(get_array_value($_FILES["cover_image"],"name","")) ){
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
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_ACTUAL,CONST_NEWS_SIZE_HEIGHT_ACTUAL,99,1);

			$new_file_name_thumb = $cid."-thumb".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_THUMB,CONST_NEWS_SIZE_HEIGHT_THUMB,99,1);

			$new_file_name_thumb = $cid."-mini".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_MINI,CONST_NEWS_SIZE_HEIGHT_MINI,99,1);

			$new_file_name_thumb = $cid."-thumb-sq".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_SQUARE,CONST_NEWS_SIZE_HEIGHT_SQUARE,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$return_status = 'error';
				$this->log_error('Admin : News', 'Add new news fail => Upload image error : '.$result_image_thumb["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_image_thumb,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}	
		}
		
		if ( $return_status == 'success' &&  $command == "_insert" ) {
			// $data["posted_by"] = getUserLoginAid($this);
			
			$aid = $this->main->insert_record($data);
			if ($aid > 0) {
				$this->log_status('Backend : Insert news', '['.$name.'] just added into database.');
				// redirect('admin/news/status/'.md5('success'));
			}
			else {
				$return_status = 'error';
				if ($save_option == '2') {
					$data["title"] = $data["draft_title"];
					$data["description"] = $data["draft_description"];
				}

				$this->log_error('Backend : Insert news', 'Command insert_record() fail. Can not insert '.$name);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
						
		}
		else if ( $return_status == 'success' && $command == "_update" ) {
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			// if($this->check_duplicate($data,$command)){
			// 	$this->form();
			// 	return "";
			// }

			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if ($rs) {
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Admin : Update news',  '['.$name.'] has been updated.');

				// switch ($save_option) {
				// 	default:
				// 	case '0':
				// 		redirect('admin/news/status/'.md5('success'));
				// 		break;
				// 	case '1':
				// 		redirect('admin/news/add');
				// 		break;
				// 	case '2':
				// 		redirect('admin/news/preview/'.$aid.'/'.rand());
				// 		break;
				// }
				// return "";
			}
			else {
				$return_status = 'error';
				if ($save_option == '2') {
					$data["title"] = $data["draft_title"];
					$data["description"] = $data["draft_description"];
				}

				$this->log_error('Backend : Update news', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
			
		}
		else {
			$this->log_error('Backend : News', 'Command not found.');
			$return_status = 'no-command';
			// redirect('admin/news/status/'.md5('no-command'));
			// return "";
		}

		// Return 
		$return = array();
		$return['aid'] = $aid;
		$return['status'] = $return_status;
		$redirect_url = '';
		if ($is_ajax === true) {
			if ($return_status == 'error') {
				$redirect_url = 'admin/news/status/'.md5('error');
			}
		}
		else {
			if ($return_status == 'success') {
				switch ($save_option) {
					default:
					case '0':
					case '1':
						$redirect_url = 'admin/news/status/'.md5('success');
						break;
					// case '1':
					// 	$redirect_url = 'admin/news/add';
					// 	break;
					case '2':
						$redirect_url = 'admin/news/'.$aid.'/preview/'.rand();
						break;
				}
			}
			else if ($return_status == 'error') {
				$return['data'] = $this->data;
			}
			else {
				$redirect_url = 'admin/news/status/'.md5('no-command');
			}
				
		}	
		$return['redirect_url'] = $redirect_url;
		return $return;
	}

	public function save_and_publish() 
	{
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
				$this->load->model($this->user_model, "user");
				$to_emails = $this->user->get_all_staff_emails();
				if (!empty($to_emails)) {
					$this->load->model($this->main_model,'news');
					$this->news->set_where(array("aid" => $return['aid']));
					$rs_news = $this->news->load_record(false);

					if (is_var_array($rs_news)) {
						$subject = $this->lang->line('mail_subject_new_news_publish');
						$body = $this->lang->line('mail_content_new_news_publish');

						// $body = eregi_replace("[\]",'',$body);
						$subject = str_replace("{news_title}", get_array_value($rs_news, 'title', 'New news has been published') , $subject);
						$body = str_replace("{doc_type}", "&nbsp;" , $body);
						$body = str_replace("{news_title}", get_array_value($rs_news, 'title', 'click here to view') , $body);
						$body = str_replace("{news_url}", site_url('news/detail/'.get_array_value($rs_news, 'aid')), $body);


						$cover_image_url = "";
						if (!empty($rs_news['cover_image_file_type'])) {
							$img_cover_image = "<img src='".site_url(get_array_value($rs_news,"upload_path","").get_array_value($rs_news,"cid","").'-actual'.get_array_value($rs_news,"cover_image_file_type",""))."' style='width:100%; max-width:600px; text-align:center;'/>";
							$body = str_replace("{img_cover_image}", $img_cover_image, $body);
						}
						else if (!isset($rs_news['cover_image_file_type']) || !empty($rs_news['cover_image_file_type'])) {
							preg_match("/<img[\w\W]+?\/?>/i", $rs_news['description'], $matches);
							// echo '<pre>'; 
							// print_r($matches);
							// echo '</pre>';
							// exit;
							if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {
								$rs_news['dummy_cover_image'] = $matches[0];
								$img_cover_image = $rs_news['dummy_cover_image'];
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
						//echo $this->email->print_debugger();
						$this->log_debug('News : Send emails to all staff about new news has been published.', '[subject = '.$subject.'] [to_emails = '.$to_emails.'] '.$subject);
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
			echo json_encode(array('status' => 'success', 'preview_url' => site_url('admin/news/'.$return['aid'].'/preview/'.rand()), 'aid' => $return['aid']));
		}
		else {
			echo json_encode(array('status' => 'error', 'msg' => '', 'redirect_url' => $return['redirect_url']));
		}
		return;
	}

	function preview_detail($aid) {
		@define("thisAction","detail");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = '<span class="textStart">News</span><span class="textSub">Detail</span>';
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/news_preview_detail';
				
		$this->db->flush_cache();
		$this->load->model($this->main_model,"news");
		$result = $this->news->increase_total_view($aid);	
		// echo $aid; exit;
		$this->db->flush_cache();
		$this->news->set_where(array("aid"=>$aid));
		$item_result = $this->news->load_record(true);
		
		if(is_var_array($item_result)) {
			$item_result['title'] = get_array_value($item_result, 'draft_title', $item_result['title']);
			$item_result['description'] = get_array_value($item_result, 'draft_description', $item_result['description']);
			$this->data["item_detail"] = $item_result;
			$news_aid = get_array_value($item_result, "aid", "");
			$news_main_aid = get_array_value($item_result, "news_main_aid", "");
			if(!is_blank($news_main_aid)){
				$this->load->model($this->news_main_model,"news_main");
				$this->news_main->set_where(array("aid"=>$news_main_aid,"status"=>"1"));
				$result = $this->news_main->load_record(true);
				if(is_var_array($result)){
					$news_main_aid = get_array_value($result,"aid","");
					$news_main_name = get_array_value($result,"name","");
					$news_main_url = get_array_value($result,"url","");
					$this->data["news_main_result"] = $result;
					$this->data["this_news_main_aid"] = $news_main_aid;
					$this->data["this_news_main_name"] = $news_main_name;
					$this->data["this_news_main_url"] = $news_main_url;
				}
			}
			
			$this->load->model($this->news_gallery_model,"news_gallery");
			$tmp = array();
			$tmp["news_aid"] = $news_aid;
			$tmp["status"] = "1";
			$this->news_gallery->set_where($tmp);
			$this->news_gallery->set_order_by("weight ASC, created_date ASC");
			$this->data["news_gallery_list"] = $this->news_gallery->load_records(true);
			// echo "<br>sql : ".$this->db->last_query();

			// Load data for right sidebar
			$this->load->model($this->event_model,"event");
			$this->load->model($this->view_most_comments_model,"most_comments");
			$tmp_event_list = $this->event->load_home('', '1');
			$this->data['event_list'] = $tmp_event_list['results'];
			$this->data['news_popular_list'] = $this->news->load_popular('1');
			$this->data['news_recommended_list'] = $this->news->load_recommended('1');
			$this->data['news_talk_of_the_town_list'] = $this->news->load_talk_of_the_town('1');
			$this->data['news_top_commenters'] = $this->most_comments->load_top_commenters(0, 3);
			
			$this->load->view($this->default_theme_front.'/tpl_news', $this->data);
		}else{
			redirect('news/status/'.md5('news-not-found'));
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
			$msg = set_message_error('Error occurred. Can not find this news.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : News', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : News', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_set_comment_value($sid="", $status=""){
		@define("thisAction",'ajax_set_comment_value');
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
		$this->load->model($this->news_comment_model,'main');
		$this->main->set_where(array("aid"=>$aid, "news.user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(true);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this comment.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : News comment', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : News comment', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
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
			$msg = set_message_error('Error occurred. Can not find this news.');
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
			$this->log_status('Backend : Delete news', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete news', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
		
	function ajax_delete_comment_one(){
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
		$this->load->model($this->main_model,'news');
		$this->load->model($this->news_comment_model,'main');
		$this->main->set_where(array("aid"=>$aid, "news.user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(true);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this comment.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "title", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "title", $aid);

		$upload_path = get_array_value($objResult,"upload_path","");
		// echo "upload_path = $upload_path";
		deleteDir($upload_path);

		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->delete_records();

		$this->news->update_total_comment(get_array_value($objResult, "parent_news_aid", ""));
		
		if ($rs){
			$this->log_status('Backend : Delete news comment', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete news comment', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
		
	function ajax_get_main_list($sid){
		@define("thisAction",'ajax_get_main_list');
		$this->load->model($this->main_model,'main');
		$this->load->model($this->news_category_model, 'category');
		$tmp_cat = $this->category->load_records(false);
			
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('newsBackDataSearchSession');		
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

		$search_news_category = $this->getDataFromInput('search_news_category');
		$data_where = "";
		if(is_var_array($search_news_category)) {
			
			foreach($search_news_category as $item){
				$data_where[] = $item;
				$data_search["search_news_category"][] = $item;
			}
			if (count($search_news_category) < count($tmp_cat)) {
				$this->main->set_and_or_like_by_field("category", $data_where);
			}
		}
		

		// $news_main_aid = $this->input->get_post('news_main_aid');
		// $this->main->set_where(array("news_main_aid"=>$news_main_aid));

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
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'aid',"col_show"=>'name_action',"title_class"=>'w20 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title_action',"title_class"=>'w200 hcenter',"result_class"=>'hleft');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Main',"field_order"=>'*news_main_name',"col_show"=>'news_main_name',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Published Date',"field_order"=>'publish_date',"col_show"=>'publish_date_txt',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Wow',"field_order"=>'total_wow',"col_show"=>'total_wow',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total View',"field_order"=>'total_view',"col_show"=>'total_view',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Comments',"field_order"=>'total_comment',"col_show"=>'total_comment',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Published?',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		if(CONST_NEWS_MODE == "1"){
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Highlighted?',"field_order"=>'is_highlight',"col_show"=>'is_highlight_action',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Recommended?',"field_order"=>'is_recommended',"col_show"=>'is_recommended_action',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
			// $header_list[] = array("sort_able"=>'1',"title_show"=>'Show in Homepage?',"field_order"=>'is_home',"col_show"=>'is_home_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		}
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'w40 hcenter',"result_class"=>'hcenter');
		
		$this->session->set_userdata('newsBackDataSearchSession',$data_search);	
		
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
			$item['title'] = get_array_value($item, 'draft_title', $item['title']);
			$item['description'] = get_array_value($item, 'draft_description', $item['description']);

			$item["name_action"] = '<a href="'.site_url('admin/news/edit/'.get_array_value($item,"aid","")).'">'.get_text_pad(get_array_value($item,"aid","0"),0,8).'</a>';
			$item["title_action"] = '<a href="'.site_url('admin/news/edit/'.get_array_value($item,"aid","")).'">'.getShortString($item['title'],"70").'</a>';
			
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_highlight = get_array_value($item,"is_highlight","0");
			if($is_highlight == 1){
				$item["is_highlight_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_highlight=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_highlight_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_highlight=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_recommended = get_array_value($item,"is_recommended","0");
			if($is_recommended == 1){
				$item["is_recommended_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_recommended=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_recommended_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_recommended=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_home = get_array_value($item,"is_home","0");
			if($is_home == 1){
				$item["is_home_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_home=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_home_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_home=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this news." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/news\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
				$item["action"] .= '<a class="btn btn-info btn-xs" title="Click to \'View\' all comments." onclick="processViewAllComments(\''.get_array_value($item,"aid","").'\')"><i class="fa fa-comments-o "></i></a>&nbsp;&nbsp;&nbsp;';
			}

			$result[] = $item;
		}
		
		return $result;
	}
	
	function ajax_get_comment_main_list($sid){
		@define("thisAction",'ajax_get_comment_main_list');
		$this->load->model($this->news_comment_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("news.user_owner_aid"=>getUserOwnerAid($this)));

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('newsCommentBackDataSearchSession');		
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

		$search_news_aid = $this->getDataFromInput('search_news_aid');
		$data_search["search_news_aid"] = $search_news_aid;
		if(!is_blank($search_news_aid)){
			$this->main->set_where($this->main->get_table_name().'.parent_news_aid', $search_news_aid);
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
		$result_list = $this->manage_comment_column_detail($result_list['results']);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'News Title',"field_order"=>'news.title',"col_show"=>'news_title_short',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Comment',"field_order"=>'comment',"col_show"=>'comment',"title_class"=>'w200 hcenter',"result_class"=>'hleft');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Main',"field_order"=>'*news_main_name',"col_show"=>'news_main_name',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Commented Date',"field_order"=>'created_date',"col_show"=>'created_date_txt',"title_class"=>'w80 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('newsCommentBackDataSearchSession',$data_search);	
		
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
	
	function manage_comment_column_detail($result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this comment." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news-comment\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this comment." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news-comment\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this comment." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/news-comment\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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

	function isNewsCodeExits($cid){
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