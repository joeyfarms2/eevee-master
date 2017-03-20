<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");

class Facebook_controller extends User_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = 'front';
		define("thisFrontTabMenu",'login');
		@define("folderName",'user/');
		
		$this->user_login_social_model = 'User_login_social_model';
		
		$this->lang->load('mail');
		
	}
	
	function index()
	{
		$this->fb_check();
	}
	
	function ajax_fb_check($sid="")
	{
		@define("thisAction",'fb_check');
		$fb = $this->input->get_post('fb_response');
		// print_r($fb);

		$id = get_array_value($fb,"id","");
		$email = get_array_value($fb,"email","");
		$name = get_array_value($fb,"name","");
		$first_name = get_array_value($fb,"first_name","");
		$last_name = get_array_value($fb,"last_name","");
		$gender = get_array_value($fb,"gender","");
		$link = get_array_value($fb,"link","");
		$locale = get_array_value($fb,"locale","");
		$timezone = get_array_value($fb,"timezone","");
		$updated_time = get_array_value($fb,"updated_time","");
		$verified = get_array_value($fb,"verified","");

		$social = array();
		$social["id"] = $id;
		$social["email"] = $email;
		$social["name"] = $name;
		$social["first_name"] = $first_name;
		$social["last_name"] = $last_name;
		$social["gender"] = $gender;
		$social["link"] = $link;
		$social["locale"] = $locale;
		$social["timezone"] = $timezone;
		$social["updated_time"] = $updated_time;
		$social["verified"] = $verified;
		// $social["data"] = serialize($fb);

		$user_result = "";
		$user_aid = "";
		$user_cid = "";
		$this->load->model($this->user_login_social_model,"social");
		$this->load->model($this->user_login_social_model,"social");
		$data = "";
		$data["id"] = $id;
		$this->social->set_where($data);
		$social_result = $this->social->load_record(false);
		if(is_var_array($social_result)){
			// check login
			$user_aid = get_array_value($social_result,"user_aid","");
			$user_result = $this->check_user($user_aid,"");
			$user_aid = get_array_value($user_result,"aid","");
			$user_cid = get_array_value($user_result,"cid","");
		}else{
			if(!is_blank($email)){
				$user_result = $this->check_user("", $email);
				$user_aid = get_array_value($user_result,"aid","");
				$user_cid = get_array_value($user_result,"cid","");
			}
			if(!is_var_array($user_result)){
				$user_cid = "";
				do{
					$this->load->model($this->setting_config_model,'setting_config');		
					$obj = $this->setting_config->get_config_rni_by_cid("rn-user-1");
					$user_cid = trim(get_array_value($obj,"barcode",""));
				}while( $this->isUserCidExits($user_cid) );


				$this->load->model($this->user_section_model,"user_section");
				$this->user_section->set_where(array("status"=>"1", "is_default" =>"1"));
				$user_section_default = $this->user_section->load_record(false);

				$user_result = array();
				$user_result["user_owner_aid"] = "1";
				$user_result["status"] = "1";
				$user_result["user_role_aid"] = "5";
				$user_result["user_section_aid"] = get_array_value($user_section_default,"aid","0");
				$user_result["email"] = $email;

				$user_result["cid"] = $user_cid;
				$user_result["username"] = $user_cid;
				$user_result["password"] = "";

				$user_result["first_name_th"] = $first_name;
				$user_result["last_name_th"] = $last_name;
				$user_result["gender"] = ($gender == "female") ? "f" : "m";
				$user_result["registration_date"] = get_db_now('%Y-%m-%d');
				$user_result["expiration_date"] = "";
				$user_result["point_remain"] = "0";
				$user_result["channel"] = "web";

				$this->load->model($this->user_model,"user");
				$user_aid = $this->user->insert_record($user_result);
				if($user_aid > 0){
					$this->log_status('Login from Facebook', $user_cid.' just add to database.',$user_result);
				}else{
					$this->log_error('Login from Facebook', 'Command insert_record() fail. Can not insert '.$user_cid.'.', $user_result);
					$msg = set_message_error('Error occurred. Can not delete data.');
					$result_obj = array("status" => 'error',"msg" => $msg );
					echo json_encode($result_obj);
					return "";
				}
			}
		}

		//update lasted login datetime
		$login_hash = md5(mktime().$user_aid);
		$user_result["login_hash"] = $login_hash;
		$chk = $this->user->update_last_login($user_result);
		$this->session->set_userdata('userSession',$user_result);
		$this->log_status('Login from Facebook', $user_cid.' just login.',$user_result);

		$social["social_type"] = "facebook";
		$social["user_aid"] = $user_aid;
		$social["avatar_path"] = "https://graph.facebook.com/".$id."/picture";
		$social["avatar_path_large"] = "https://graph.facebook.com/".$id."/picture?type=large";
		$this->load->model($this->user_login_social_model,"social");
		$this->social->insert_record($social);
		$this->log_status('Login from Facebook', 'Login success.', $social);

		$token = $this->save_user_login_history("login", $user_aid, "web", "", "facebook", "");
		$lasted_url = $this->session->userdata('lasted_url');
		if(false && $is_first_login){
			$msg = "processRedirect('change-password')";
		}else if(!is_blank($lasted_url)){
			$msg = "processRedirect('".$lasted_url."')";
		}else if(is_staff_or_higher()){
			$msg = "processRedirect('admin/dashboard')";
		}else{
			$msg = "processRedirect('home')";
		}
		$result_obj = array("status" => 'success',"msg" => $msg );
		echo json_encode($result_obj);
		return "";
	}

	function check_user($user_aid="", $email=""){
		$fb = $this->input->get_post('fb_response');
		// print_r($fb);

		$id = get_array_value($fb,"id","");
		$email = get_array_value($fb,"email","");
		$name = get_array_value($fb,"name","");
		$first_name = get_array_value($fb,"first_name","");
		$last_name = get_array_value($fb,"last_name","");
		$gender = get_array_value($fb,"gender","");
		$link = get_array_value($fb,"link","");
		$locale = get_array_value($fb,"locale","");
		$timezone = get_array_value($fb,"timezone","");
		$updated_time = get_array_value($fb,"updated_time","");
		$verified = get_array_value($fb,"verified","");

		$social = array();
		$social["id"] = $id;
		$social["email"] = $email;
		$social["name"] = $name;
		$social["first_name"] = $first_name;
		$social["last_name"] = $last_name;
		$social["gender"] = $gender;
		$social["link"] = $link;
		$social["locale"] = $locale;
		$social["timezone"] = $timezone;
		$social["updated_time"] = $updated_time;
		$social["verified"] = $verified;

		if(is_blank($user_aid) && is_blank($email)){
			$msg = set_message_error('Error occure.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			exit(0);
		}

		$data = "";
		if(!is_blank($user_aid)){
			$data["aid"] = $user_aid;
		}
		if(!is_blank($email)){
			$data["email"] = $email;
		}

		$this->load->model($this->user_model,"user");
		$data = "";
		$data["email"] = $email;
		$this->user->set_where($data);
		$result = $this->user->load_record(false);
		if(is_var_array($result)){
			$user_aid = get_array_value($result,"aid","");
			$user_cid = get_array_value($result,"cid","");
			//check status
			if(get_array_value($result,"status","") != '1'){
				$this->log_status('Facebook Login', 'Login failed. '.$user_cid.' is an inactive user.', $result);
				$msg = set_message_error('This user was suspended. Please contact administrator to solve the problem.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}

			//check expiration date
			$expiration_date = get_datetime_pattern("ymd",get_array_value($result,"expiration_date",""),"");
			// echo "<br>expiration_date = ".$expiration_date;
			if(!is_blank($expiration_date)){
				$today = date("ymd");
				// echo "<br>today = ".$today;
				if($expiration_date < $today){
					$this->log_status('Facebook Login', 'Login failed. '.$user_cid.' is expired.', $result);
					$msg = set_message_error('This user was expired. Please contact administrator to solve the problem.');
					$result_obj = array("status" => 'error',"msg" => $msg );
					echo json_encode($result_obj);
					exit(0);
				}
			}

			return $result;
			
		}else{
			return "";
		}

	}
	
	function status($type="")
	{
		switch($type)
		{
			case md5('blank') : 
				$this->data["message"] = set_message_error('Please try again.');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again.');
				$this->data["js_code"] = '';
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/facebook_form';
		$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
	}
	
}

?>