<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class User_init_controller extends Project_init_controller {

	function __construct()	{
		parent::__construct();

		$this->user_login_history_model = "User_login_history_model";
		$this->user_section_model = "User_section_model";

		if(CONST_LOGIN_BY_DOMAIN == "1"){
			$this->user_domain_model = 'User_domain_model';
			$this->load->model($this->user_domain_model,"user_domain");
			$this->data["master_user_domain"] = $this->user_domain->load_master_user_domain();
		}

		$this->load->model($this->user_section_model,"user_section");
		$this->data["master_user_section"] = $this->user_section->load_master();

		$this->load->model($this->user_department_model,"user_department");
		$master_department = $this->user_department->load_records();
		//echo "<br>sql : ".$this->db->last_query();
		$this->data["master_user_department"] = $master_department;

	}
	
	function index(){
	}
		
	function isUserCidExits($cid){
		$this->load->model($this->user_model,"user");
		$this->user->set_where(array("cid"=>$cid));
		$total = $this->user->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function save_user_login_history($action, $user_aid, $device, $device_id, $flag, $description){
		/****** Start : Save log for user_login_history ******/
		$token = md5($user_aid.date('YmdHis').get_random_text(8));
		$data = array();
		$data["action"] = "login";
		$data["user_aid"] = $user_aid;
		$data["device"] = "web";
		$data["device_id"] = "";
		$data["flag"] = $flag;
		$data["description"] = $description;
		$data["user_owner_aid"] = "1";
		$data["status"] = "1";
		$data["token"] = $token;
		if($this->agent->is_mobile()){
			$data["browser"] = $this->agent->mobile().'/'.$this->agent->browser().' '.$this->agent->version();
		}else{
			$data["browser"] = $this->agent->platform().'/'.$this->agent->browser().' '.$this->agent->version();
		}
		$data["browser_detail"] = $this->agent->agent_string();
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->insert_record($data);
		return $token;
		/****** End : Save log for user_login_history ******/
	}

	function check_user_registration($info_obj=""){
		if(!is_var_array($info_obj)){
			$result["status"] = "error";
			$result["message"] = "Parameter missing : Data is null.";
			return $result;
		}

		$email = get_array_value($info_obj, "email","");
		$username = get_array_value($info_obj, "username","");
		$password = get_array_value($info_obj, "password","");
		$title_th = get_array_value($info_obj, "title_th","");
		$first_name_th = get_array_value($info_obj, "first_name_th","");
		$last_name_th = get_array_value($info_obj, "last_name_th","");
		$title_en = get_array_value($info_obj, "title_en","");
		$first_name_en = get_array_value($info_obj, "first_name_en","");
		$last_name_en = get_array_value($info_obj, "last_name_en","");
		$nick_name = get_array_value($info_obj, "nick_name","");
		$display_name = get_array_value($info_obj, "display_name","");
		$avartar_path = get_array_value($info_obj, "avartar_path","");
		$avatar_type = get_array_value($info_obj, "avatar_type","");
		$birthday = get_array_value($info_obj, "birthday","");
		$gender = get_array_value($info_obj, "gender","");
		$address = get_array_value($info_obj, "address","");
		$country_aid = get_array_value($info_obj, "country_aid","");
		$contact_number = get_array_value($info_obj, "contact_number","");
		$user_owner_aid = get_array_value($info_obj, "user_owner_aid","");
		$interest = get_array_value($info_obj, "interest","");
		$device = get_array_value($info_obj, "device","");
		$user_role_aid = get_array_value($info_obj, "user_role_aid","");
		$publisher_aid = get_array_value($info_obj, "publisher_aid","");
		$department = get_array_value($info_obj, "department","");
		$position = get_array_value($info_obj, "position","");
		$occupation = get_array_value($info_obj, "occupation","");
		$note_1 = get_array_value($info_obj, "note_1","");
		$note_2 = get_array_value($info_obj, "note_2","");
		$note_3 = get_array_value($info_obj, "note_3","");
		$note_4 = get_array_value($info_obj, "note_4","");
		$status = get_array_value($info_obj, "status","");
		$point_remain = get_array_value($info_obj, "point_remain","");
		$registration_date = get_array_value($info_obj, "registration_date","");
		$expiration_date = get_array_value($info_obj, "expiration_date","");
		$last_login = get_array_value($info_obj, "last_login","");
		$is_login = get_array_value($info_obj, "is_login","");
		$login_hash = get_array_value($info_obj, "login_hash","");
		$activate_code = get_array_value($info_obj, "activate_code","");
		$confirm_code = get_array_value($info_obj, "country_aid","");
		$is_blacklist = get_array_value($info_obj, "is_blacklist","");
		$remark = get_array_value($info_obj, "remark","");
		$channel = get_array_value($info_obj, "channel","");

		$data["email"] = get_array_value($info_obj, "email","");
		$data["username"] = get_array_value($info_obj, "username","");
		$data["password"] = get_array_value($info_obj, "password","");
		$data["user_role_aid"] = get_array_value($info_obj, "user_role_aid","");
		$data["user_owner_aid"] = get_array_value($info_obj, "user_owner_aid","");
		$data["status"] = get_array_value($info_obj, "status","");

		$data["point_remain"] = get_array_value($info_obj, "point_remain","0");
		$data["registration_date"] = get_array_value($info_obj, "registration_date","");
		$data["expiration_date"] = get_array_value($info_obj, "expiration_date","");
		$data["last_login"] = get_array_value($info_obj, "last_login","");
		$data["is_login"] = get_array_value($info_obj, "is_login","");
		$data["login_hash"] = get_array_value($info_obj, "login_hash","");
		$data["activate_code"] = get_array_value($info_obj, "activate_code","");
		$data["confirm_code"] = get_array_value($info_obj, "country_aid","");

		$data["title_th"] = get_array_value($info_obj, "title_th","");
		$data["first_name_th"] = get_array_value($info_obj, "first_name_th","");
		$data["last_name_th"] = get_array_value($info_obj, "last_name_th","");
		$data["title_en"] = get_array_value($info_obj, "title_en","");
		$data["first_name_en"] = get_array_value($info_obj, "first_name_en","");
		$data["last_name_en"] = get_array_value($info_obj, "last_name_en","");
		$data["nick_name"] = get_array_value($info_obj, "nick_name","");
		$data["display_name"] = get_array_value($info_obj, "display_name","");
		$data["avartar_path"] = get_array_value($info_obj, "avartar_path","");
		$data["avatar_type"] = get_array_value($info_obj, "avatar_type","");
		$data["birthday"] = get_array_value($info_obj, "birthday","");
		$data["gender"] = get_array_value($info_obj, "gender","m");
		$data["address"] = get_array_value($info_obj, "address","");
		$data["country_aid"] = get_array_value($info_obj, "country_aid","");
		$data["contact_number"] = get_array_value($info_obj, "contact_number","");
		$data["interest"] = get_array_value($info_obj, "interest","");
		$data["device"] = get_array_value($info_obj, "device","");
		$data["publisher_aid"] = get_array_value($info_obj, "publisher_aid","0");
		$data["department"] = get_array_value($info_obj, "department","");
		$data["position"] = get_array_value($info_obj, "position","");
		$data["occupation"] = get_array_value($info_obj, "occupation","");
		$data["note_1"] = get_array_value($info_obj, "note_1","");
		$data["note_2"] = get_array_value($info_obj, "note_2","");
		$data["note_3"] = get_array_value($info_obj, "note_3","");
		$data["note_4"] = get_array_value($info_obj, "note_4","");
		$data["is_blacklist"] = get_array_value($info_obj, "is_blacklist","0");
		$data["remark"] = get_array_value($info_obj, "remark","");
		$data["channel"] = get_array_value($info_obj, "channel","none");
	}
		
	
}

?>