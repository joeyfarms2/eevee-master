<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class Event_upload_front_controller extends Initcontroller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		
		if(CONST_HAS_EVENT != "1"){
			redirect('home');
		}

		define("thisFrontTabMenu",'event_upload');
		define("thisFrontSubMenu",'');
		@define("folderName",'event/');

		for_login();
				
		$this->main_model = 'Event_upload_model';
		$this->event_main_model = 'Event_main_model';
		$this->event_category_model = 'Event_category_model';
		
		$this->load->model($this->event_main_model,"event_main");
		$this->data["master_event_main"] = $this->event_main->load_event_mains();
		
		$this->load->model($this->event_category_model,"event_category");
		$this->data["master_event_category"] = $this->event_category->load_event_categories();
		
		
	}
	
	function index(){
		$this->form();
	}
	
	function form(){
		@define("thisAction","home");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/event/event_upload_form';
		$this->load->view($this->default_theme_front . '/tpl_content.php', $this->data);
	}
	
	function save(){
		@define("thisAction",'save');
		$save_option = $this->input->get_post('save_option');
		
		$this->load->model($this->main_model,'main');
		
		$title = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["event_main_aid"] = $this->input->get_post('event_main_aid');
		$data["title"] = $title;
		$publish_date = get_datetime_pattern("Y-m-d",$this->input->get_post('publish_date'),get_db_now());
		$data["publish_date"] = $publish_date;
		$data["description"] = $this->input->get_post('description');
		$data["ref_link"] = $this->input->get_post('ref_link');
		$data["status"] = "1";
		// $data["event_upload_aid"] = "0";
		
		$posted_by = getUserLoginAid($this);
		$posted_email = $this->input->get_post('email');
		$posted_ref = $this->input->get_post('name');
		$data["posted_by"] = $posted_by;
		$data["posted_email"] = $posted_email;
		$data["posted_ref"] = $posted_ref;
		
		$category_list = "";
		$category = $this->input->get_post('event_category_aid');
		// echo "category : ".$category;
		if(is_var_array($category)){
			$category_list = ",";
			foreach($category as $item){
				$category_list .= $item.',';
			}
			// $category_list = substr($category_list, 1);
		}
		$data["category"] = $category_list;
		
		
		$cid = date('YmdHis').get_random_text(4);
		$data["cid"] = $cid;
		
		$attach_file_name = "N/A";
		$file_name = "N/A";
		if( !is_blank(get_array_value($_FILES,"file_attach","")) && !is_blank(get_array_value($_FILES["file_attach"],"name","")) ){
			$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/event_upload/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"").'/'.$cid.'/';
			$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/event_upload/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"").'/'.$cid.'/';
			create_directories($upload_base_path);
			
			$data["upload_path"] = $upload_base_path_db;
		
			//Start upload file
			$upload_path = $upload_base_path;
			$file_name = $_FILES["file_attach"]["name"];
			$file_type = substr(strrchr($file_name, "."), 0);
			
			do{
				$new_file_name = trim(random_string('alnum', 16)).$file_type;
			}while( is_file("./".$upload_path."/".$new_file_name) );
			
			// echo "new_file_name = $new_file_name <BR>";

			$result_file = upload_file($this,"file_attach",$upload_path,$new_file_name);
			if ( !is_blank(get_array_value($result_file,"error_msg","")) )
			{
				// echo $result_file["error_msg"];
				$this->log_status('Event Upload : Insert', 'Upload file error : '.$result_file["error_msg"]);
				// $this->data["message"] = set_message_error("Sorry, the system can not save data now. Please try again or contact your administrator.");
				$this->data["message"] = set_message_error($result_file["error_msg"]);
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}else{
				$data["file_name"] = $new_file_name;
			}
		}

		$aid = $this->main->insert_record($data);
		if($aid > 0){
			$this->log_status('Event Upload : Insert', '[title='.$title.'][upload by=('.$posted_by.')('.$posted_email.')'.$posted_ref.'] just added into database by.');
			redirect('event/upload/status/'.md5('success'));
		}else{
			$this->log_error('Event Upload : Insert', 'Command insert_record() fail. Can not insert [title='.$title.'][upload by=('.$posted_by.')('.$posted_email.')'.$posted_ref.']');
			$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
			$this->data["js_code"] = '';
			$this->data["command"] = $command;
			$this->data["item_detail"] = $data;
			$this->form();
			return "";
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
		$this->form();
	}

	
}

?>