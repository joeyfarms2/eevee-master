<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");

class My_account_controller extends User_init_controller {

	function __construct()
	{
		parent::__construct();
		for_login();
		$this->data["mode"] = 'front';
		define("thisFrontTabMenu",'my_account');
		@define("folderName",'user/');

		$this->user_model = 'User_model';
		$this->product_category_model = 'Product_category_model';
		$this->user_setting_profile_model = 'User_setting_profile_model';
		$this->event_category_model = 'Event_category_model';
		$this->user_department_model = 'User_department_model';

		$this->data["page_title"] = 'MyProfile';
		
	}
	
	function index(){
		$this->form();
	}
	
	function form(){
		@define("thisAction",'form');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/my_account';	

		$this->load->model($this->user_model,'user');
		$this->user->set_where(array("aid"=>getSessionUserAid()));
		$result = $this->user->load_record(false);
		if(is_blank(get_array_value($this->data,"result",""))){
			$this->data["item_detail"] = $result;
		}

		$this->data["user_avatar"] = get_user_avatar($result);
		$this->session->set_userdata("userSession",$result);

		$user_section_aid = get_array_value($result,"user_section_aid","");
		//echo $user_section_aid;
		$this->load->model($this->user_department_model,"user_department");
		$this->user_department->set_and_or_like(array("section"=>$user_section_aid));
		$this->user_department->set_order_by("aid ASC");
		
		$master_department = $this->user_department->load_records();
		//echo "<br>sql : ".$this->db->last_query();
		$this->data["master_department"] = $master_department;
		
		$this->load->model($this->product_category_model,"product_category");
		$tmp = array();
		$tmp["user_owner_aid"] = getUserOwnerAid($this);
		$tmp["status"] = "1";
		$this->product_category->set_where($tmp);
		$this->product_category->set_order_by("main.weight ASC , weight ASC, name ASC");
		$result_list = $this->product_category->load_records(true);
		$product_category_result_list = "";
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$product_main_name = get_array_value($item,"product_main_name","none");
				$product_category_result_list[$product_main_name][] = $item;
			}
		}
		$this->data["product_category_result_list"] = $product_category_result_list;
		
		$this->load->model($this->event_category_model,"event_category");
		$tmp = array();
		$tmp["user_owner_aid"] = getUserOwnerAid($this);
		$tmp["status"] = "1";
		$this->event_category->set_where($tmp);
		$this->event_category->set_order_by("weight ASC, name ASC");
		$result_list = $this->event_category->load_records(true);
		$event_category_result_list = "";
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$product_main_name = "News";
				$event_category_result_list[$product_main_name][] = $item;
			}
		}
		$this->data["event_category_result_list"] = $event_category_result_list;
		
		$this->load->model($this->user_setting_profile_model,"user_setting_profile");
		$tmp = array();
		$tmp["user_aid"] = getUserLoginAid($this);
		$this->user_setting_profile->set_where($tmp);
		$user_setting_profile_list = $this->user_setting_profile->load_records(false);
		$this->data["user_setting_profile_list"] = $user_setting_profile_list;

		


		
		
		$this->load->view($this->default_theme_login.'/tpl_login', $this->data);
	}
	
	function save(){
		@define("thisAction",'save');
		
		$email = $this->input->get_post('email');
		$username = $this->input->get_post('username');
		
		if(is_blank($email)){
			redirect('my-account/status/'.md5('blank'));
		}
		
		//Save setting profile
		$this->load->model($this->user_setting_profile_model,"user_setting_profile");
		$tmp = array();
		$tmp["user_aid"] = getUserLoginAid($this);
		$this->user_setting_profile->set_where($tmp);
		$this->user_setting_profile->delete_records();
		
		$product_category_ignore_list = $this->input->get_post('product_category_ignore_list');
		// echo ($product_category_ignore_list);
		if(!is_blank($product_category_ignore_list)){
			$arr_list = preg_split("/,/", $product_category_ignore_list);
			if(is_var_array($arr_list)){
				foreach($arr_list as $item){
					// echo "Add <br />";
					$this->load->model($this->user_setting_profile_model,"user_setting_profile");
					$data = array();
					$data["user_aid"] = getUserLoginAid($this);
					$data["aid"] = $item;
					$data["type"] = "P";
					$this->user_setting_profile->insert_record($data);
					$this->db->flush_cache();
				}
			}
		}
		
		$event_category_ignore_list = $this->input->get_post('event_category_ignore_list');
		// echo ($event_category_ignore_list);
		if(!is_blank($event_category_ignore_list)){
			$arr_list = preg_split("/,/", $event_category_ignore_list);
			if(is_var_array($arr_list)){
				foreach($arr_list as $item){
					// echo "Add <br />";
					$this->load->model($this->user_setting_profile_model,"user_setting_profile");
					$data = array();
					$data["user_aid"] = getUserLoginAid($this);
					$data["aid"] = $item;
					$data["type"] = "E";
					$this->user_setting_profile->insert_record($data);
					$this->db->flush_cache();
				}
			}
		}
		
		$data = array();
		$data["username"] = $username;
		$data["email"] = $email;
		$data["first_name_th"] = $this->input->get_post('first_name_th');
		$data["last_name_th"] = $this->input->get_post('last_name_th');
		$data["gender"] = $this->input->get_post('gender');
		$data["email"] = $this->input->get_post('email');
		$data["contact_number"] = $this->input->get_post('contact_number');
		$data["note_2"] = $this->input->get_post('note_2');
		$data["note_3"] = $this->input->get_post('note_3');
		$data["note_4"] = $this->input->get_post('note_4');
		$data["position"] = $this->input->get_post('position');
		$data["note_1"] = $this->input->get_post('note_1');
		$data["department_aid"] = $this->input->get_post('department_aid');
		$data["address"] = $this->input->get_post('address');
		$data["display_name"] = $this->input->get_post('display_name');
		
		$txt_error = "";
		
		
		//check Email
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array("email"=>$email, "user_owner_aid"=>getUserOwnerAid($this)));
		$this->user->set_where_not_equal(array("aid"=>getSessionUserAid()));
		$result = $this->user->load_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		if(is_var_array($result))
		{
			$txt_error .= "Email";
			$this->data["js_code"] = '$("#email").focus();';
		}
		
		//check Username
		if(!is_blank($username)){
			$this->load->model($this->user_model,'user');
			$this->user->set_where(array("username"=>$username, "user_owner_aid"=>getUserOwnerAid($this)));
			$this->user->set_where_not_equal(array("aid"=>getSessionUserAid()));
			$result = $this->user->load_records(false);
			// echo "<br>sql : ".$this->db->last_query();
			if(is_var_array($result))
			{
				if(!is_blank($txt_error)) $txt_error .= ", "; 
				$txt_error .= "Username";
				$this->data["js_code"] = '$("#username").focus();';
			}
		}
		
		if(!is_blank($txt_error))
		{
			$this->data["message"] = set_message_error($txt_error.' is used by other.');
			$this->form($data);
			return "";
		}
		
		if( !is_blank(get_array_value($_FILES,"avatar","")) && !is_blank(get_array_value($_FILES["avatar"],"name","")) ){
			$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/avatar/".ceil(getSessionUserAid()/100);
			create_directories($upload_base_path);
		
			//Start upload file
			$upload_path = $upload_base_path;
			$image_name = $_FILES["avatar"]["name"];
			$file_type = substr(strrchr($image_name, "."), 0);
			
			$new_file_name_thumb = getSessionUserAid()."-thumb".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("avatar",$upload_path,$new_file_name_thumb,1500000,CONST_AVATAR_SIZE_WIDTH_THUMB,CONST_AVATAR_SIZE_WIDTH_THUMB,99,1);

			$new_file_name_thumb = getSessionUserAid()."-mini".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("avatar",$upload_path,$new_file_name_thumb,1500000,CONST_AVATAR_SIZE_WIDTH_MINI,CONST_AVATAR_SIZE_WIDTH_MINI,99,1);

			$new_file_name_thumb = getSessionUserAid()."-tiny".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("avatar",$upload_path,$new_file_name_thumb,1500000,CONST_AVATAR_SIZE_WIDTH_TINY,CONST_AVATAR_SIZE_WIDTH_TINY,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$this->log_status('My account', 'Upload image error : '.$result_image_thumb["error_msg"]);
				// $this->data["message"] = set_message_error("Sorry, the system can not save data now. Please try again or contact your administrator.");
				$this->data["message"] = set_message_error($result_image_thumb["error_msg"]);
				$this->form($data);
				return "";
			}else{
				$data["avatar_path"] = $upload_base_path;
				$data["avatar_type"] = $file_type;
			}
		}
		
		$data_where["aid"] = getSessionUserAid();
		$this->user->set_where($data_where);
		$this->user->update_record($data);
		$this->data["message"] = set_message_success('Record has been saved.');
		
		redirect('my-account/status/'.md5('success'));
	}
	
	function status($type="")	{
		switch($type)
		{
			case md5('blank') : 
				$this->data["message"] = set_message_error('Please enter required field.');
				$this->data["js_code"] = '';
				break;
			case md5('success') : 
				$this->data["message"] = set_message_success('Record has been saved.');
				$this->data["js_code"] = '';
				break;
			case md5('password-save-success') : 
				$this->data["message"] = set_message_success('Password has been changed.');
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
		$this->form();
	}
	
}

?>