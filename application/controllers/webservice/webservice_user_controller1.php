<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");
// require_once(APPPATH."libraries/adLDAP/adLDAP.php");

class Webservice_user_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->user_model = 'User_model';
		$this->user_domain_model = 'User_domain_model';
		$this->setting_config_model = 'Setting_config_model';
		$this->user_login_history_model = 'User_login_history_model';

		$this->lang->load('mail');
	}
	
	
	function create_user(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		

		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		if(CONST_ONLINE_REGIS != '1'){
			$result_obj = array("status" => 'error',"msg" => 'Permission denied : Online registration is now closed.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$email = trim($this->input->get_post('email'));
		// echo "email = ".$email;
		if(is_blank($email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify valid email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		if(CONST_LOGIN_BY_DOMAIN == '1'){
			$domain = substr(strrchr($email, "@"), 1);
			// echo "domain = $domain";
			$this->load->model($this->user_domain_model,"domain");
			$master_user_domain = $this->domain->load_all_user_domain();
			// print_r($master_user_domain);
			if(!in_array($domain, $master_user_domain)){
				$result_obj = array("status" => 'error',"msg" => 'Permission denied : Your email domain is not authorized to access the requested resource.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		
		$password = trim($this->input->get_post('password'));
		if(is_blank($password)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify password.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(strlen($password) < CONST_MIN_LENGTH_PASSWORD || strlen($password) > CONST_MAX_LENGTH_PASSWORD){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data : password must be '.CONST_MIN_LENGTH_PASSWORD.' - '.CONST_MAX_LENGTH_PASSWORD.' charactors.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$username = trim($this->input->get_post('username'));
		if(!is_blank($username)){
			if(CONST_USERNAME_TYPE == "2"){
				$result_obj = array("status" => 'error',"msg" => 'Permission denied : Your email domain is not authorized to access the requested resource.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}

			if(strlen($username) < CONST_MIN_LENGTH_USERNAME || strlen($username) > CONST_MAX_LENGTH_USERNAME){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data : username must be '.CONST_MIN_LENGTH_USERNAME.' - '.CONST_MAX_LENGTH_USERNAME.' charactors.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		
		$version = trim($this->input->get_post('version'));
		
		$this->load->model($this->user_model,'user');
		$tmp = array();
		$tmp["email"] = $email;
		$this->user->set_where($tmp);
		$user_result = $this->user->load_records(false);
		if(is_var_array($user_result)){
			$result_obj = array("status" => 'error',"msg" => 'Data duplicate : This email is used by another', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		if(!is_blank($username)){
			$this->load->model($this->user_model,'user');
			$tmp = array();
			$tmp["username"] = $username;
			$this->user->set_where($tmp);
			$user_result = $this->user->load_records(false);
			if(is_var_array($user_result)){
				$result_obj = array("status" => 'error',"msg" => 'Data duplicate : This username is used by another', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		$this->load->model($this->user_section_model,"user_section");
		$this->user_section->set_where(array("status"=>"1", "is_default" =>"1"));
		$user_section_default = $this->user_section->load_record(false);

		// $user_section_aid; = get_array_value($user_section_default,"aid","0");

		if(CONST_MAIN_DOMAIN_EMAIL == 1){
			$pos = strrpos($email, "@");
			$domain = substr(strrchr($email, "@"), 1);
				if($domain == MAIN_DOMAIN_EMAIL){
					$user_section_aid = "2";
				}else{
					$user_section_aid = get_array_value($user_section_default,"aid","0");
				}

		}else{
				$user_section_aid = get_array_value($user_section_default,"aid","0");
		}

		$first_name_th = $this->input->get_post('first_name');
		if(is_blank($first_name_th)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify firstname.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		
		$last_name_th = $this->input->get_post('last_name');
		if(is_blank($last_name_th)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify lastname.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		
		$gender = $this->input->get_post('gender');
		if(is_blank($gender)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify gender.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		
		$contact_number = $this->input->get_post('contact_number');
		if(is_blank($contact_number)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify contact number.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		

		$address = $this->input->get_post('address');
		if(is_blank($address)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify address.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		
		
		// $note_3 = $this->input->get_post('company_address');
		// if(is_blank($note_3)){
		// 	$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify Company Address.', "result" => '');
		// 	echo json_encode($result_obj);
		// 	return "";
		// }
		// $data["note_3"] = $note_3;
		
		if($user_section_aid > 1 ){
			$note_4 = $this->input->get_post('company_phone');
			if(is_blank($note_4)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify Company Phone Number.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			
			$position = $this->input->get_post('position');
			if(is_blank($position)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify position.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			
			$department_aid = $this->input->get_post('department_aid');
			if(is_blank($department_aid)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify department_aid.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
		}else{
			$note_2 = $this->input->get_post('company_name');
			if(is_blank($note_2)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify Company Name.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		
		
		}


		
			
		$this->user->set_trans_start();

		$data = array();
		$data["user_owner_aid"] = "1";
		$data["status"] = "1";
		$data["point_remain"] = "0";
		$data["user_role_aid"] = "5"; //member
		$data["user_section_aid"] = $user_section_aid;
		$data["channel"] = $device;
		$data["registration_date"] = get_db_now();
		$data["first_name_th"] = $first_name_th;
		$data["last_name_th"] = $last_name_th;
		$data["gender"] = $gender;
		$data["contact_number"] = $contact_number;
		$data["address"] = $address;
		
		if($user_section_aid > 1){
			$data["note_4"] = $note_4;
			$data["position"] = $position;
			$data["department_aid"] = $department_aid;
		}else{
			$data["note_2"] = $note_2;
		}

		$data["email"] = $email;
		$data["password"] = $this->user->encryptPassword($password); 
		if(!is_blank($username)){
			$data["username"] = $username;
		}

		$activate_code = "";
		switch (CONST_PASSWORD_TYPE){
			case '2' : $activate_code = $this->user->generate_new_password("8"); $subject = $this->lang->line('mail_subject_new_user_activate'); $body = $this->lang->line('mail_content_new_user_activate'); 
						break;
			case '3' : $subject = $this->lang->line('mail_subject_new_user'); $body = $this->lang->line('mail_content_new_user'); break;
			case '4' : $activate_code = $this->user->generate_new_password("8"); $subject = $this->lang->line('mail_subject_new_user_activate_by_admin'); $body = $this->lang->line('mail_content_new_user_activate_by_admin'); break;
			default : $subject = $this->lang->line('mail_subject_new_user_generate'); $body = $this->lang->line('mail_content_new_user_generate'); break;
		}
		$data["activate_code"] = $activate_code;

		switch (CONST_USERNAME_TYPE){
			case '2' : $login_type = "Email"; $login_user = $email; break;
			default : $login_type = "Username"; $login_user = $username; break;
		}

		do{
			$this->load->model($this->setting_config_model,'setting_config');		
			$obj = $this->setting_config->get_config_rni_by_cid("rn-user-1");
			$cid = trim(get_array_value($obj,"barcode",""));
		}while( $this->isUserCidExits($cid) );
		$data["cid"] = $cid;
		
		$user_aid = $this->user->insert_record($data);
		if($user_aid > 0){

			// $body = eregi_replace("[\]",'',$body);
			$body = str_replace("{doc_type}", "&nbsp;" , $body);
			$body = str_replace("{name}", trim(get_array_value($data,"email","")) , $body);
			$body = str_replace("{username}", $login_user, $body);
			$body = str_replace("{login_type}", $login_type, $body);
			$body = str_replace("{password}", $password, $body);
			$body = str_replace("{url}", site_url('activation/'.$email.'/'.$activate_code), $body);
			
			$this->load->library('email');
			$config = $this->get_init_email_config();
			if(is_var_array($config)){ $this->email->initialize($config); }

			$this->email->set_newline("\r\n");
			
			$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
			$this->email->to($email);

			$this->email->subject($subject);
			$this->email->message($body);
			//echo $this->email->print_debugger();
			$this->log_debug('Web service registration email', '['.$email.'] '.$body);
			if(@$this->email->send()){
				$this->log_status('Web service registration', 'Welcome email sent success.'.$login_user.'['.$user_aid.'] just been submitted.');
				$this->user->set_trans_commit();
				$result_obj = array("status" => 'success',"msg" => '', "result" => $user_aid);
				echo json_encode($result_obj);
				return "";
			}else{
				$this->log_status('Web service registration', 'Welcome email sent fail. '.$login_user.'['.$user_aid.'] just been removed.');
				$this->user->set_trans_rollback();
				$result_obj = array("status" => 'error',"msg" => 'Sorry, The system can not send email right now.<BR>Please try again later or contact administrator to solve the problem.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}else{
			$result_obj = array("status" => 'error',"msg" => 'Database error : Can not create user. Please try again later.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}	
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
	
	function login(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$username = trim($this->input->get_post('username'));
		// echo "username = ".$username;
		if(is_blank($username)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify username.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$password = trim($this->input->get_post('password'));
		if(is_blank($password)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify password.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->user_model,'user');




		//check username
		$pos = strrpos($username, "@");
		if ($pos === false) {
			$this->user->set_where(array("username"=>$username));
		}else{
			$this->user->set_where(array("email"=>$username));
		}
		$this->user->set_limit(0,1);
		$user_result = $this->user->load_record(true);
		/*
		$new_user_aid = '';
		if (!is_var_array($user_result) && CONST_LDAP_AUTHEN == TRUE) {
			$data_user = array();
			$pos = strrpos($username, "@");
			if ($pos !== false) {
				$data_user["email"] = $username;
			}
			$data_user["username"] = $username;
			$data_user["first_name_th"] = $username;
			$data_user["user_role_aid"] = '5';
			$new_user_aid = $this->user->insert_record($data_user);
			if ($new_user_aid > 0) {
				$this->log_status('[APP]: Auto insert new username ['.$username.'] into database.', 'Access', 'User aid = '.$new_user_aid);
				$this->user->set_where(array("aid"=>$new_user_aid));
				$user_result = $this->user->load_record(true);
			}
			else {
				$this->log_status('[APP]: Fail auto insert new username ['.$username.'] into database.', 'Access', '');
			}
		}
*/
		if(!is_var_array($user_result) && CONST_LDAP_LOGIN < 1){
			$result_obj = array("status" => 'error',"msg" => 'User not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		// Authen with LDAP Server
		if ( CONST_LDAP_AUTHEN == TRUE && 
				( 
					(is_var_array($user_result) && $user_result['password'] == "") ||
					(!is_var_array($user_result))
				)
			) {
			$pos = strrpos($username, "@");
			if ($pos !== false) {
				$username = substr($username, 0, $pos);
			}

			$this->log_status('[APP]: Trying to connect LDAP server...', 'Access', '['.$username.'] is trying to authenticate with LDAP server...');

			$arr_domain = explode('.', substr($username, 1));
				$base_dn = '';
				if (is_array($arr_domain)) {
					// $domain_controller = 'Ewbkdc1';
					foreach ($arr_domain as $key => $value) {
						if ($key > 0) $base_dn .= ',';
						$base_dn .= 'DC='.$value;
					}
				}

				$adldap_param = array(
					'base_dn' => CONST_LDAP_BASE_DN, // (!empty($base_dn) ? $base_dn : CONST_LDAP_BASE_DN),
					'account_suffix' => CONST_LDAP_ACCOUNT_SUFFIX, // (!empty($user_domain_name) ? $user_domain_name : CONST_LDAP_ACCOUNT_SUFFIX),
					'domain_controllers' => array( CONST_LDAP_DOMAIN_CONTROLLER )
				);

				/*
				echo '<pre>';
				echo $base_dn;
				print_r($adldap_param);
				echo '</pre>';
				exit;
				*/

				//$this->log_status('Start LDAP command = ', 'Access', '');
				
				// $adldap = new adLDAP($adldap_param);
				try {
			    // $adldap = new adLDAP();
			    $adldap = new adLDAP($adldap_param);
	        }
	        catch (adLDAPException $e) {
	            // echo $e; 
	            $result_obj = array("status" => 'error',"msg" => 'Failed to connect LDAP Server.', "result" => '');
				echo json_encode($result_obj);
				return "";
	            // exit();   
	        }
			
			// $this->log_status('[APP]: Start LDAP command = ', 'Access', '');
			
			/*
			$adldap = new adLDAP($adldap_param);
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$adldap->close();
			$adldap->set_ad_username($username);
			$adldap->set_ad_password($password);
			$conn = $adldap->connect();
			*/

			// $this->log_status('[APP]: Connection status = '.$conn, 'Access', 'Connection status = '.$conn);

			$ip_address = $_SERVER['REMOTE_ADDR'];
			$ldap_msg = "";
			$authen_status = false;
			// if($conn == 0) {
			$authUser = $adldap->authenticate($username, $password);
			if ($authUser !== true){
				$ldap_msg = $adldap->getLastError();
				$this->log_status('[APP]: Failed to connect LDAP Server, cannot authenticate user "'.$username.'"', 'Access', 'Cannot connect LDAP Server. '.$ldap_msg.' ['.$username.'].');
				$result_obj = array("status" => 'error',"msg" => $ldap_msg, "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			else {
				$authen_status = true;
				$this->log_status('[APP]: LDAP authen success', 'Access', '['.$username.'] passed LDAP authentication successfully.');
			}

			if ($authen_status == true && !is_var_array($user_result)) {
				$obj_user = $adldap->user()->infoCollection($username, array('*'));
				$this->user->set_where(array("email"=>$obj_user->mail));
				$this->user->set_limit(0,1);
				$user_result = $this->user->load_record(true);
			}

			if ($authen_status == true && !is_var_array($user_result)) {
				$obj_user = $adldap->user()->infoCollection($username, array('*'));
				// $obj_user_2 = $adldap->user()->info($username);
				/*
				echo 'hello<br>';
				echo '<pre>';
				print_r($obj_user);
				echo '</pre>';
				exit;
				*/
				$this->log_status('LDAP user object', 'Access', 'user object = '.serialize($obj_user));
				$this->log_status('LDAP user display name', 'Access', 'user object = '.$obj_user->displayName);
				$this->log_status('LDAP user first name', 'Access', 'user object = '.$obj_user->firstname);
				$this->log_status('LDAP user last name', 'Access', 'user object = '.$obj_user->surname);
				$this->log_status('LDAP user email', 'Access', 'user object = '.$obj_user->mail);
				// $this->log_status('LDAP user last name', 'Access', 'user object = '.$obj_user->surname);

				$new_user_aid = '';
				$data_user = array();
				$pos = strrpos($username, "@");
				// if ($pos !== false) {
					$data_user["email"] = $obj_user->mail; // $user_cid;
				// }
				$data_user["username"] = $username;
				$data_user["first_name_th"] = $obj_user->displayName; // $obj_user->firstname; // $username;
				// $data_user["last_name_th"] =  $obj_user->displayName; // $obj_user->surname; // $username;
				$data_user["display_name"] =  $obj_user->displayName;
				$data_user["user_role_aid"] = '5';
				$new_user_aid = $this->user->insert_record($data_user);
				if ($new_user_aid > 0) {
					$this->log_status('[APP]: Auto insert new username ['.$username.'] into database.', 'Access', 'User aid = '.$new_user_aid);
					$this->user->set_where(array("aid"=>$new_user_aid));
					$user_result = $this->user->load_record(true);
				}
				else {
					$this->log_status('[APP]: Failed to auto insert new username ['.$username.'] into our database.', 'Access', '');
					$result_obj = array("status" => 'error',"msg" => 'Failed to auto insert new username ['.$username.'] into our database.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
			}

		}
		else if (is_var_array($user_result)) {
			// check password
			if(get_array_value($user_result,"password","") != $this->user->encryptPassword($password)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect password.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			//check status
			if(get_array_value($user_result,"status","") != '1'){
				$result_obj = array("status" => 'error',"msg" => 'This user is an inactive user.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			if(!is_blank(get_array_value($user_result,"activate_code",""))){
				$result_obj = array("status" => 'error',"msg" => 'This user nedd to activate.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			//check expiration date
			$expiration_date = get_datetime_pattern("ymd",get_array_value($user_result,"expiration_date",""),"");
			// echo "<br>expiration_date = ".$expiration_date;
			if(!is_blank($expiration_date)){
				$today = date("ymd");
				// echo "<br>today = ".$today;
				if($expiration_date < $today){
					$result_obj = array("status" => 'error',"msg" => 'This user is expired.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
			}
		}
		else {
			$result_obj = array("status" => 'error',"msg" => 'User not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		



		
		
		// print_r($user_result);
		// echo "<hr>";
		
		$avatar_path = get_array_value($user_result,"avatar_path","");
		$avatar_path = str_replace("./", "", $avatar_path);
		$aid = get_array_value($user_result,"aid","");
		$user_owner_aid = get_array_value($user_result,"user_owner_aid","");
		$avatar_type = get_array_value($user_result,"avatar_type",".jpg");
		// $avatar_mode = get_array_value($user_result,"avatar_mode","thumb");
		$avatar_thumb_mode = "thumb";
		$avatar_mini_mode = "mini";
		$avatar_tiny_mode = "tiny";
		$gender = get_array_value($user_result,"gender","m");
		$avatar_thumb_full = $avatar_path.'/'.$aid.'-'.$avatar_thumb_mode.$avatar_type;
		$avatar_mini_full = $avatar_path.'/'.$aid.'-'.$avatar_mini_mode.$avatar_type;
		$avatar_tiny_full = $avatar_path.'/'.$aid.'-'.$avatar_tiny_mode.$avatar_type;

		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->set_where(array("user_aid"=>$aid, "status"=>'1'));
		$this->user_login_history->set_order_by("aid DESC");
		$arr_history = $this->user_login_history->load_records(false);
		if(is_var_array($arr_history)){
			$i = 0;
			foreach ($arr_history as $item) {
				$login_device = get_array_value($item,"device","");
				if($login_device == "ios" || $login_device == "andriod"){
					$i++;
					if($i >= CONST_MAX_LOGIN_DEVICE){
						// print_r($item);
						$tmp = array();
						$tmp["status"] = "0";
						$data_where = array();
						$data_where["aid"] = get_array_value($item,"aid","0");
						$this->user_login_history->set_where($data_where);
						$this->user_login_history->update_record($tmp);
					}
				}
			}
		}
		
		if(is_blank($avatar_path) || is_blank($aid)){
			$user_thumb_avatar = site_url('images/avatar/avatar-'.$avatar_thumb_mode.'-'.$gender.'.jpg');
			$user_mini_avatar = site_url('images/avatar/avatar-'.$avatar_mini_full.'-'.$gender.'.jpg');
			$user_tiny_avatar = site_url('images/avatar/avatar-'.$avatar_tiny_full.'-'.$gender.'.jpg');
		}
		if(is_file($avatar_thumb_full)){
			$user_thumb_avatar = site_url($avatar_path.'/'.$aid.'-'.$avatar_thumb_mode.$avatar_type);
		}else{
			$user_thumb_avatar = site_url('images/avatar/avatar-'.$avatar_thumb_mode.'-'.$gender.'.jpg');
		}
		if(is_file($avatar_mini_full)){
			$user_mini_avatar = site_url($avatar_path.'/'.$aid.'-'.$avatar_mini_mode.$avatar_type);
		}else{
			$user_mini_avatar = site_url('images/avatar/avatar-'.$avatar_mini_mode.'-'.$gender.'.jpg');
		}
		if(is_file($avatar_tiny_full)){
			$user_tiny_avatar = site_url($avatar_path.'/'.$aid.'-'.$avatar_tiny_mode.$avatar_type);
		}else{
			$user_tiny_avatar = site_url('images/avatar/avatar-'.$avatar_tiny_mode.'-'.$gender.'.jpg');
		}
		
		$token = md5(get_array_value($user_result,"aid","0").date('YmdHis').get_random_text(8));
		// echo "token = $token";
		
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->logout_by_device_id($device_id);
		
		$data = array();
		$data["user_aid"] = $aid;
		$data["device"] = $device;
		$data["device_id"] = $device_id;
		$data["user_owner_aid"] = $user_owner_aid;
		$data["action"] = "login";
		$data["token"] = $token;
		$data["status"] = "1";
		if($this->agent->is_mobile()){
			$data["browser"] = $this->agent->mobile().'/'.$this->agent->browser().' '.$this->agent->version();
		}else{
			$data["browser"] = $this->agent->platform().'/'.$this->agent->browser().' '.$this->agent->version();
		}
		$data["browser_detail"] = $this->agent->agent_string();
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->insert_record($data);
		
		
		$result = array();
		$result["aid"] = get_array_value($user_result,"aid");
		$result["user_section_aid"] = get_array_value($user_result,"user_section_aid");
		$result["user_role_aid"] = get_array_value($user_result,"user_role_aid");
		$result["username"] = get_array_value($user_result,"username");
		$result["first_name"] = get_array_value($user_result,"first_name_th");
		$result["last_name"] = get_array_value($user_result,"last_name_th");
		$result["department_name"] = get_array_value($user_result,"department_name");
		$result["avatar_thumb"] = $user_thumb_avatar;
		$result["avatar_mini"] = $user_mini_avatar;
		$result["avatar_tiny"] = $user_tiny_avatar;
		$result["token"] = $token;
		
		$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
		echo json_encode($result_obj);
		return "";
	}

	function login_ad(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$username = trim($this->input->get_post('username'));
		// echo "username = ".$username;
		if(is_blank($username)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify username.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$password = trim($this->input->get_post('password'));
		if(is_blank($password)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify password.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->user_model,'user');


	
		//check username
		$pos = strrpos($username, "@");
		if ($pos === false) {
			$this->user->set_where(array("username"=>$username));
		}else{
			$this->user->set_where(array("email"=>$username));
		}
		$this->user->set_limit(0,1);
		$user_result = $this->user->load_record(true);

		// echo "<pre>";
		// print_r($user_result);
		// echo "</pre>";
		/*
		$new_user_aid = '';
		if (!is_var_array($user_result) && CONST_LDAP_AUTHEN == TRUE) {
			$data_user = array();
			$pos = strrpos($username, "@");
			if ($pos !== false) {
				$data_user["email"] = $username;
			}
			$data_user["username"] = $username;
			$data_user["first_name_th"] = $username;
			$data_user["user_role_aid"] = '5';
			$new_user_aid = $this->user->insert_record($data_user);
			if ($new_user_aid > 0) {
				$this->log_status('[APP]: Auto insert new username ['.$username.'] into database.', 'Access', 'User aid = '.$new_user_aid);
				$this->user->set_where(array("aid"=>$new_user_aid));
				$user_result = $this->user->load_record(true);
			}
			else {
				$this->log_status('[APP]: Fail auto insert new username ['.$username.'] into database.', 'Access', '');
			}
		}
*/
		if(!is_var_array($user_result) && CONST_LDAP_LOGIN < 1){
			$result_obj = array("status" => 'error',"msg" => 'User not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		// Authen with LDAP Server
		if ( CONST_LDAP_AUTHEN == TRUE && 
				( 
					(is_var_array($user_result) && $user_result['password'] == "") ||
					(!is_var_array($user_result))
				)
			) {
			$pos = strrpos($username, "@");
			if ($pos !== false) {
				$username = substr($username, 0, $pos);
			}

			$this->log_status('[APP]: Trying to connect LDAP server...', 'Access', '['.$username.'] is trying to authenticate with LDAP server...');

			$arr_domain = explode('.', substr($username, 1));
				$base_dn = '';
				if (is_array($arr_domain)) {
					// $domain_controller = 'Ewbkdc1';
					foreach ($arr_domain as $key => $value) {
						if ($key > 0) $base_dn .= ',';
						$base_dn .= 'DC='.$value;
					}
				}

				$adldap_param = array(
					'base_dn' => CONST_LDAP_BASE_DN, // (!empty($base_dn) ? $base_dn : CONST_LDAP_BASE_DN),
					'account_suffix' => CONST_LDAP_ACCOUNT_SUFFIX, // (!empty($user_domain_name) ? $user_domain_name : CONST_LDAP_ACCOUNT_SUFFIX),
					'domain_controllers' => array( CONST_LDAP_DOMAIN_CONTROLLER )
				);

				/*
				echo '<pre>';
				echo $base_dn;
				print_r($adldap_param);
				echo '</pre>';
				exit;
				*/

				//$this->log_status('Start LDAP command = ', 'Access', '');
				
				// $adldap = new adLDAP($adldap_param);
				try {
			    // $adldap = new adLDAP();
			    $adldap = new adLDAP($adldap_param);
	        }
	        catch (adLDAPException $e) {
	            // echo $e; 
	            $result_obj = array("status" => 'error',"msg" => 'Failed to connect LDAP Server.', "result" => '');
				echo json_encode($result_obj);
				return "";
	            // exit();   
	        }
			
			// $this->log_status('[APP]: Start LDAP command = ', 'Access', '');
			
			/*
			$adldap = new adLDAP($adldap_param);
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$adldap->close();
			$adldap->set_ad_username($username);
			$adldap->set_ad_password($password);
			$conn = $adldap->connect();
			*/

			// $this->log_status('[APP]: Connection status = '.$conn, 'Access', 'Connection status = '.$conn);

			$ip_address = $_SERVER['REMOTE_ADDR'];
			$ldap_msg = "";
			$authen_status = false;
			// if($conn == 0) {
			$authUser = $adldap->authenticate($username, $password);
			if ($authUser !== true){
				$ldap_msg = $adldap->getLastError();
				$this->log_status('[APP]: Failed to connect LDAP Server, cannot authenticate user "'.$username.'"', 'Access', 'Cannot connect LDAP Server. '.$ldap_msg.' ['.$username.'].');
				$result_obj = array("status" => 'error',"msg" => $ldap_msg, "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			else {
				$authen_status = true;
				$this->log_status('[APP]: LDAP authen success', 'Access', '['.$username.'] passed LDAP authentication successfully.');
			}

			if ($authen_status == true && !is_var_array($user_result)) {
				$obj_user = $adldap->user()->infoCollection($username, array('*'));
				$this->user->set_where(array("email"=>$obj_user->mail));
				$this->user->set_limit(0,1);
				$user_result = $this->user->load_record(true);
			}

			if ($authen_status == true && !is_var_array($user_result)) {
				$obj_user = $adldap->user()->infoCollection($username, array('*'));
				// $obj_user_2 = $adldap->user()->info($username);
				/*
				echo 'hello<br>';
				echo '<pre>';
				print_r($obj_user);
				echo '</pre>';
				exit;
				*/
				$this->log_status('LDAP user object', 'Access', 'user object = '.serialize($obj_user));
				$this->log_status('LDAP user display name', 'Access', 'user object = '.$obj_user->displayName);
				$this->log_status('LDAP user first name', 'Access', 'user object = '.$obj_user->firstname);
				$this->log_status('LDAP user last name', 'Access', 'user object = '.$obj_user->surname);
				$this->log_status('LDAP user email', 'Access', 'user object = '.$obj_user->mail);
				// $this->log_status('LDAP user last name', 'Access', 'user object = '.$obj_user->surname);

				$new_user_aid = '';
				$data_user = array();
				$pos = strrpos($username, "@");
				// if ($pos !== false) {
					$data_user["email"] = $obj_user->mail; // $user_cid;
				// }
				$data_user["username"] = $username;
				$data_user["first_name_th"] = $obj_user->displayName; // $obj_user->firstname; // $username;
				// $data_user["last_name_th"] =  $obj_user->displayName; // $obj_user->surname; // $username;
				$data_user["display_name"] =  $obj_user->displayName;
				$data_user["user_role_aid"] = '5';
				$new_user_aid = $this->user->insert_record($data_user);
				if ($new_user_aid > 0) {
					$this->log_status('[APP]: Auto insert new username ['.$username.'] into database.', 'Access', 'User aid = '.$new_user_aid);
					$this->user->set_where(array("aid"=>$new_user_aid));
					$user_result = $this->user->load_record(true);
				}
				else {
					$this->log_status('[APP]: Failed to auto insert new username ['.$username.'] into our database.', 'Access', '');
					$result_obj = array("status" => 'error',"msg" => 'Failed to auto insert new username ['.$username.'] into our database.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
			}

		}elseif( CONST_LDAP_AUTHEN_SOAP == TRUE && ((is_var_array($user_result) && $user_result['password'] == "") || (!is_var_array($user_result)))) { 
					
					$user_result = $this->chack_soap_status($username,$password);

			}
		else if (is_var_array($user_result)) {
			// check password
			if(get_array_value($user_result,"password","") != $this->user->encryptPassword($password)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect password.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			//check status
			if(get_array_value($user_result,"status","") != '1'){
				$result_obj = array("status" => 'error',"msg" => 'This user is an inactive user.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			if(!is_blank(get_array_value($user_result,"activate_code",""))){
				$result_obj = array("status" => 'error',"msg" => 'This user nedd to activate.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			//check expiration date
			$expiration_date = get_datetime_pattern("ymd",get_array_value($user_result,"expiration_date",""),"");
			// echo "<br>expiration_date = ".$expiration_date;
			if(!is_blank($expiration_date)){
				$today = date("ymd");
				// echo "<br>today = ".$today;
				if($expiration_date < $today){
					$result_obj = array("status" => 'error',"msg" => 'This user is expired.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
			}
		}
		else {
			$result_obj = array("status" => 'error',"msg" => 'User not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		

		$avatar_path = get_array_value($user_result,"avatar_path","");
		$avatar_path = str_replace("./", "", $avatar_path);
		$aid = get_array_value($user_result,"aid","");
		$user_owner_aid = get_array_value($user_result,"user_owner_aid","");
		$avatar_type = get_array_value($user_result,"avatar_type",".jpg");
		// $avatar_mode = get_array_value($user_result,"avatar_mode","thumb");
		$avatar_thumb_mode = "thumb";
		$avatar_mini_mode = "mini";
		$avatar_tiny_mode = "tiny";
		$gender = get_array_value($user_result,"gender","m");
		$avatar_thumb_full = $avatar_path.'/'.$aid.'-'.$avatar_thumb_mode.$avatar_type;
		$avatar_mini_full = $avatar_path.'/'.$aid.'-'.$avatar_mini_mode.$avatar_type;
		$avatar_tiny_full = $avatar_path.'/'.$aid.'-'.$avatar_tiny_mode.$avatar_type;

		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->set_where(array("user_aid"=>$aid, "status"=>'1'));
		$this->user_login_history->set_order_by("aid DESC");
		$arr_history = $this->user_login_history->load_records(false);
		if(is_var_array($arr_history)){
			$i = 0;
			foreach ($arr_history as $item) {
				$login_device = get_array_value($item,"device","");
				if($login_device == "ios" || $login_device == "andriod"){
					$i++;
					if($i >= CONST_MAX_LOGIN_DEVICE){
						// print_r($item);
						$tmp = array();
						$tmp["status"] = "0";
						$data_where = array();
						$data_where["aid"] = get_array_value($item,"aid","0");
						$this->user_login_history->set_where($data_where);
						$this->user_login_history->update_record($tmp);
					}
				}
			}
		}
		
		if(is_blank($avatar_path) || is_blank($aid)){
			$user_thumb_avatar = site_url('images/avatar/avatar-'.$avatar_thumb_mode.'-'.$gender.'.jpg');
			$user_mini_avatar = site_url('images/avatar/avatar-'.$avatar_mini_full.'-'.$gender.'.jpg');
			$user_tiny_avatar = site_url('images/avatar/avatar-'.$avatar_tiny_full.'-'.$gender.'.jpg');
		}
		if(is_file($avatar_thumb_full)){
			$user_thumb_avatar = site_url($avatar_path.'/'.$aid.'-'.$avatar_thumb_mode.$avatar_type);
		}else{
			$user_thumb_avatar = site_url('images/avatar/avatar-'.$avatar_thumb_mode.'-'.$gender.'.jpg');
		}
		if(is_file($avatar_mini_full)){
			$user_mini_avatar = site_url($avatar_path.'/'.$aid.'-'.$avatar_mini_mode.$avatar_type);
		}else{
			$user_mini_avatar = site_url('images/avatar/avatar-'.$avatar_mini_mode.'-'.$gender.'.jpg');
		}
		if(is_file($avatar_tiny_full)){
			$user_tiny_avatar = site_url($avatar_path.'/'.$aid.'-'.$avatar_tiny_mode.$avatar_type);
		}else{
			$user_tiny_avatar = site_url('images/avatar/avatar-'.$avatar_tiny_mode.'-'.$gender.'.jpg');
		}
		
		$token = md5(get_array_value($user_result,"aid","0").date('YmdHis').get_random_text(8));
		// echo "token = $token";
		
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->logout_by_device_id($device_id);
		
		$data = array();
		$data["user_aid"] = $aid;
		$data["device"] = $device;
		$data["device_id"] = $device_id;
		$data["user_owner_aid"] = $user_owner_aid;
		$data["action"] = "login";
		$data["token"] = $token;
		$data["status"] = "1";
		if($this->agent->is_mobile()){
			$data["browser"] = $this->agent->mobile().'/'.$this->agent->browser().' '.$this->agent->version();
		}else{
			$data["browser"] = $this->agent->platform().'/'.$this->agent->browser().' '.$this->agent->version();
		}
		$data["browser_detail"] = $this->agent->agent_string();
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->insert_record($data);

		// print_r($user_result);
		// echo "<hr>";
		if((get_array_value($user_result,"department_aid","") == "0" || get_array_value($user_result,"department_aid","") == NULL) && get_array_value($user_result,"user_section_aid","") > "1"){

						$edit_user = "Yes";
					}else{
						$edit_user = "No";
					}	
		
		
		
		$result = array();
		$result["aid"] = get_array_value($user_result,"aid");
		$result["cid"] = get_array_value($user_result,"cid");
		$result["username"] = get_array_value($user_result,"username");
		$result["first_name"] = get_array_value($user_result,"first_name_th");
		$result["last_name"] = get_array_value($user_result,"last_name_th");
		$result["gender"] = get_array_value($user_result,"gender");
		$result["email"] = get_array_value($user_result,"email");
		$result["contact_number"] = get_array_value($user_result,"contact_number");
		$result["user_section_aid"] = get_array_value($user_result,"user_section_aid");
		$result["user_role_aid"] = get_array_value($user_result,"user_role_aid");
		$result["department_aid"] = get_array_value($user_result,"department_aid");
		$result["position"] = get_array_value($user_result,"position");
		$result["degree"] = get_array_value($user_result,"note_1");
		$result["company_name"] = get_array_value($user_result,"note_2");
		$result["company_address"] = get_array_value($user_result,"note_3");
		$result["company_phone"] = get_array_value($user_result,"note_4");
		$result["edit_user"] = $edit_user;
		$result["avatar_thumb"] = $user_thumb_avatar;
		$result["avatar_mini"] = $user_mini_avatar;
		$result["avatar_tiny"] = $user_tiny_avatar;
		$result["token"] = $token;
		
		$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
		echo json_encode($result_obj);
		return "";
	}
		
	function logout(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$token = trim($this->input->get_post('token'));
		if(is_blank($token)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify token.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->user_login_history_model,"user_login_history");
		$result = $this->user_login_history->logout_by_device_id($device_id);
		if($result){
			$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'error',"msg" => 'Database error : Can not logout. Please try again later.', "result" => '0');
			echo json_encode($result_obj);
			return "";
		}
	}

	function get_mypoint(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$obj = array();
			$obj["aid"] = $user_aid;
			$this->load->model($this->user_model,"user");
			$this->user->set_where($obj);
			$result = $this->user->load_record(false);
			$point = get_array_value($result,"point_remain","0");
			
			if(!is_var_array($result)){
				$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			// echo "<br>sql : ".$this->db->last_query()."<BR>";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $point);
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function request_login_social() {
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		
		
		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		
		$email = trim($this->input->get_post('email'));
		// echo "email = ".$email;
		if(is_blank($email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify valid email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		// "email = ".$email;
		$token = $this->generateToken();
		$_SESSION['_email'] = $email;
		$result = array();
		$result["token"] = $token;
		$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
		echo json_encode($result_obj);
		return "";
	}
	
	function login_social(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$social_type = trim($this->input->get_post('social_type'));
		$this->check_social_type();
		
		$email = trim($this->input->get_post('email'));
	 //echo "email = ".$email;
		if(is_blank($email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify valid email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		/*if($email !== $_SESSION['_email']) {
			$result_obj = array("status" => 'error',"msg" => 'Invalid email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}*/

		$token = trim($this->input->get_post('token'));
		if(is_blank($token)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify token.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		// if($token !== $this->session->userdata('_token')) {
		/*if($token !== $_SESSION['_token']) {
			$result_obj = array("status" => 'error',"msg" => 'Invalid token.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}*/

		$id = trim($this->input->get_post('id'));
		if(is_blank($id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}		
		$name = trim($this->input->get_post('name'));
		$first_name = trim($this->input->get_post('first_name'));
		$last_name = trim($this->input->get_post('last_name'));
		$gender = trim($this->input->get_post('gender'));
		$link = trim($this->input->get_post('link'));
		$locale = trim($this->input->get_post('locale'));
		$timezone = trim($this->input->get_post('timezone'));
		$updated_time = trim($this->input->get_post('updated_time'));
		$verified = trim($this->input->get_post('verified'));

		$this->load->model($this->user_model,'user');
		$this->user->set_where(array("email"=>$email));
		
		$user_result = $this->user->load_record(true);

		$this->load->model($this->user_section_model,"user_section");
		$this->user_section->set_where(array("status"=>"1", "is_default" =>"1"));
		$user_section_default = $this->user_section->load_record(false);
		
		if(!is_var_array($user_result)){
			//create new user
			$user_cid = "";
			do{
				$this->load->model($this->setting_config_model,'setting_config');		
				$obj = $this->setting_config->get_config_rni_by_cid("rn-user-1");
				$user_cid = trim(get_array_value($obj,"barcode",""));
			}while( $this->isUserCidExits($user_cid) );

			

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
			$user_result["channel"] = $device;

			$this->load->model($this->user_model,"user");
			$user_aid = $this->user->insert_record($user_result);
			if($user_aid > 0){
				$user_result["aid"] = $user_aid;
			}else{
				$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not insert data.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}else{
			$user_aid = get_array_value($user_result,"aid","");
			if(get_array_value($user_result,"status","") != '1'){
				$result_obj = array("status" => 'error',"msg" => 'This user is an inactive user.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			if(!is_blank(get_array_value($user_result,"activate_code",""))){
				$result_obj = array("status" => 'error',"msg" => 'This user need to activate.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			$expiration_date = get_datetime_pattern("ymd",get_array_value($user_result,"expiration_date",""),"");
			// echo "<br>expiration_date = ".$expiration_date;
			if(!is_blank($expiration_date)){
				$today = date("ymd");
				// echo "<br>today = ".$today;
				if($expiration_date < $today){
					$result_obj = array("status" => 'error',"msg" => 'This user is expired.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
			}
		}
		
		// print_r($user_result);
		// echo "<hr>";
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
		$social["social_type"] = $social_type;
		$social["user_aid"] = $user_aid;
		if($social_type == 'facebook'){
			$social["avatar_path"] = "https://graph.facebook.com/".$id."/picture";
			$social["avatar_path_large"] = "https://graph.facebook.com/".$id."/picture?type=large";
		}
		$this->load->model($this->user_login_social_model,"social");
		$this->social->insert_record($social);

		$avatar_path = get_array_value($user_result,"avatar_path","");
		$avatar_path = str_replace("./", "", $avatar_path);
		$aid = get_array_value($user_result,"aid","");
		$user_owner_aid = get_array_value($user_result,"user_owner_aid","");
		$avatar_type = get_array_value($user_result,"avatar_type",".jpg");
		$avatar_mode = get_array_value($user_result,"avatar_mode","thumb");
		$gender = get_array_value($user_result,"gender","m");
		$avatar_full = $avatar_path.'/'.$aid.'-'.$avatar_mode.$avatar_type;

		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->set_where(array("user_aid"=>$aid, "status"=>'1'));
		$this->user_login_history->set_order_by("aid DESC");
		$arr_history = $this->user_login_history->load_records(false);
		if(is_var_array($arr_history)){
			$i = 0;
			foreach ($arr_history as $item) {
				$login_device = get_array_value($item,"device","");
				if($login_device == "ios" || $login_device == "andriod"){
					$i++;
					if($i >= CONST_MAX_LOGIN_DEVICE){
						 //print_r($item);
						$tmp = array();
						$tmp["status"] = "0";
						$data_where = array();
						$data_where["aid"] = get_array_value($item,"aid","0");
						$this->user_login_history->set_where($data_where);
						$this->user_login_history->update_record($tmp);
					}
				}
			}
		}
		
		if(is_blank($avatar_path) || is_blank($aid)){
			$user_avatar = site_url('images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg');
		}
		if(is_file($avatar_full)){
			$user_avatar = site_url($avatar_path.'/'.$aid.'-'.$avatar_mode.$avatar_type);
		}else{
			$user_avatar = site_url('images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg');
		}
		
		$token = md5(get_array_value($user_result,"aid","0").date('YmdHis').get_random_text(8));
		// echo "token = $token";
		
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->logout_by_device_id($device_id);
		
		$data = array();
		$data["user_aid"] = $aid;
		$data["device"] = $device;
		$data["device_id"] = $device_id;
		$data["user_owner_aid"] = $user_owner_aid;
		$data["action"] = "login";
		$data["token"] = $token;
		$data["status"] = "1";
		if($this->agent->is_mobile()){
			$data["browser"] = $this->agent->mobile().'/'.$this->agent->browser().' '.$this->agent->version();
		}else{
			$data["browser"] = $this->agent->platform().'/'.$this->agent->browser().' '.$this->agent->version();
		}
		$data["browser_detail"] = $this->agent->agent_string();
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->insert_record($data);
				
		$result = array();
		$result["aid"] = get_array_value($user_result,"aid");
		$result["avatar"] = $user_avatar;
		$result["first_name"] = $first_name;
		$result["last_name"] = $last_name;
		$result["token"] = $token;
		
		$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
		echo json_encode($result_obj);
		return "";
	}

	function edit_my_profile()
	{

			$device = trim($this->input->get_post('device'));
			$this->check_device();
			//echo $device."<br/>";

			$device_id = trim($this->input->get_post('device_id'));
			if(is_blank($device_id)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			//echo $device_id."<br/>";

			$token = trim($this->input->get_post('token'));
			//$this->check_token();
			if(is_blank($token)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify token.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			

			$this->load->model($this->user_login_history_model,"user_login_history");
			
			$this->user_login_history->set_where(array("token"=>$token));
			$user_history_result = $this->user_login_history->load_record(true);
			//print_r($user_history_result);

			if(!is_var_array($user_history_result)){
				$result_obj = array("status" => 'error',"msg" => 'User not found.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}

			$user_aid = get_array_value($user_history_result,"user_aid","");

			$this->load->model($this->user_model,"user");
			$this->user->set_where(array("aid"=>$user_aid));
			$user_result = $this->user->load_record(true);
			//print_r($user_result);
			if(!is_var_array($user_result)){
				$result_obj = array("status" => 'error',"msg" => 'User not found.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}

			$cid = trim($this->input->get_post('cid'));
			if(is_blank($cid)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify cid.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$first_name = trim($this->input->get_post('first_name'));
			if(is_blank($first_name)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify firstname.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$last_name = trim($this->input->get_post('last_name'));
			if(is_blank($last_name)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify lastname.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$gender = trim($this->input->get_post('gender'));
			if(is_blank($gender)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify gender.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$email = trim($this->input->get_post('email'));
			if(is_blank($email)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify email.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify valid email.', "result" => '');
			echo json_encode($result_obj);
			return "";
			}
			$contact_number = trim($this->input->get_post('contact_number'));
			if(is_blank($contact_number)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify firstname.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$data = array();
			if(get_array_value($user_result,"user_section_aid") > "1"){
				$department_aid = trim($this->input->get_post('department_aid'));
				if(is_blank($department_aid)){
					$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify contact number.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
				if(get_array_value($user_result,"user_section_aid") > "2"){
					$degree = trim($this->input->get_post('degree'));
					if(is_blank($degree)){
						$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify degree.', "result" => '');
						echo json_encode($result_obj);
						return "";
					}

					$data["note_1"] = $degree;
				}
				$data["department_aid"] = $department_aid;
					
			}
			if(get_array_value($user_result,"user_section_aid") < "2"){
				$position = trim($this->input->get_post('position'));
				if(is_blank($position)){
					$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify position.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
				$company_name = trim($this->input->get_post('company_name'));
				if(is_blank($company_name)){
					$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify company name.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}

				$company_address = trim($this->input->get_post('company_address'));
				if(is_blank($company_address)){
					$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify company address.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
				$company_phone = trim($this->input->get_post('company_phone'));
				if(is_blank($company_phone)){
					$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify company phone.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}

						$data["position"] = $position;
						$data["note_2"] = $company_name;
						$data["note_3"] = $company_address;
						$data["note_4"] = $company_phone;
			}



		$data["cid"] = $cid;
		$data["first_name_th"] = $first_name;
		$data["last_name_th"] = $last_name;
		$data["gender"] = $gender;
		$data["email"] = $email;
		$data["contact_number"] = $contact_number;
		$data["user_section_aid"] = get_array_value($user_result,"user_section_aid");
		$data["user_role_aid"] = get_array_value($user_result,"user_role_aid");



		$data_where["aid"] = $user_aid;
		$this->load->model($this->user_model,"user");
		$this->user->set_where($data_where);
		$rs  = $this->user->update_record($data, $data_where);
		if ($user_aid > 0) {
				$result_obj = array("status" => 'success',"msg" => '', "result" => '');
				echo json_encode($result_obj);
				return "";
		}
		else {
			$result_obj = array("status" => 'error',"msg" => 'Failed edit user not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

			
			
				
	}

	function chack_soap_status($user_cid="",$user_pass="")
	{

			//header('Content-Type: text/html; charset=utf-8');

			$client = new SoapClient("http://172.20.0.48/adrmutt/adrmutt.asmx?wsdl",

							array(
							
							"trace"      => 1,     // enable trace to view what is happening
							
							"exceptions" => 0,     // disable exceptions
							
							"cache_wsdl" => 0)         // disable any caching on the wsdl, encase you alter the wsdl server
							
							);

						$params = array(
						
									'apikey' => "vo6iydknris,[6[zk",
									'userName' => $user_cid,
									'passWord' => $user_pass
									
						);
						$data_chack = $client->CheckValid($params);

						//print_r($data_chack);
						
						$Valid = $data_chack->CheckValidResult;

						if($Valid == "true"){

							$this->load->model($this->user_model,'user');
							
							$this->user->set_where(array("username"=>$user_cid));
							
							$result_user = $this->user->load_record(false);

								if(!is_var_array($result_user)){
									
									$data_user_ad = $client->GetADProperties($params);

									$user_ad = $data_user_ad->GetADPropertiesResult;

									$ad_firstName = $user_ad->firstName;

									$ad_lastName = $user_ad->lastName;

									$ad_department = $user_ad->department;

									$ad_telephonenumber = $user_ad->telephonenumber;

									$ad_mail = $user_ad->mail;

									$ad_Description = $user_ad->Description;

									if(preg_match("/^[0-9]+$/", $user_cid)){
											
											$ldap_new_user_aid = '';
											$ldap_data_user = array();
											$ldap_data_user["cid"] = $ad_Description;
											$ldap_data_user["username"] = $user_cid;
											$ldap_data_user["first_name_th"] = $ad_firstName; // $obj_user->firstname; // $username;
											$ldap_data_user["last_name_th"] =  $ad_lastName; // $obj_user->surname; // $username;
											$ldap_data_user["user_section_aid"] = '3';
											$ldap_data_user["user_role_aid"] = '5';
											$ldap_data_user["email"] = $ad_mail;
											$ldap_data_user["contact_number"] = $ad_telephonenumber;
											$ldap_data_user["registration_date"] = get_db_now();

											
											$ldap_new_user_aid = $this->user->insert_record($ldap_data_user);
											if ($ldap_new_user_aid > 0) {
												$this->log_status('[APP]: Auto insert new username ['.$user_cid.'] into database.', 'Access', 'User aid = '.$ldap_new_user_aid);
												$this->user->set_where(array("aid"=>$ldap_new_user_aid));
												$result = $this->user->load_record(true);

											}else{
												$this->log_status('[APP]: Failed to auto insert new username ['.$user_cid.'] into our database.', 'Access', '');
												$result_obj = array("status" => 'error',"msg" => 'Failed to auto insert new username ['.$user_cid.'] into our database.', "result" => '');
												echo json_encode($result_obj);
												return "";
												
											}
										
									}else{

											$ldap_new_user_aid = '';
											$ldap_data_user = array();
											$ldap_data_user["cid"] = $ad_Description;
											$ldap_data_user["username"] = $user_cid;
											$ldap_data_user["first_name_th"] = $ad_firstName; // $obj_user->firstname; // $username;
											$ldap_data_user["last_name_th"] =  $ad_lastName; // $obj_user->surname; // $username;
											$ldap_data_user["user_section_aid"] = '2';
											$ldap_data_user["user_role_aid"] = '5';
											$ldap_data_user["email"] = $ad_mail;
											$ldap_data_user["contact_number"] = $ad_telephonenumber;
											$ldap_data_user["registration_date"] = get_db_now();
											//$ldap_data_user["activate_code"] = $this->user->generate_new_password("8");

											$ldap_new_user_aid = $this->user->insert_record($ldap_data_user);
											if ($ldap_new_user_aid > 0) {
												$this->log_status('[APP]: Auto insert new username ['.$user_cid.'] into database.', 'Access', 'User aid = '.$ldap_new_user_aid);
												$this->user->set_where(array("aid"=>$ldap_new_user_aid));
												$result = $this->user->load_record(true);

											}else{
												$this->log_status('[APP]: Failed to auto insert new username ['.$user_cid.'] into our database.', 'Access', '');
												$result_obj = array("status" => 'error',"msg" => 'Failed to auto insert new username ['.$user_cid.'] into our database.', "result" => '');
												echo json_encode($result_obj);
												return "";

											}

										
									}
									
								}else{ 


									$result = $result_user;


								}
								

						}else{ 
								
								$this->log_status('Failed to connect AD Server, cannot authenticate user "'.$user_cid.'"', 'Access', 'Cannot connect AD Server.  ['.$user_cid.'].');
								$result_obj = array("status" => 'error',"msg" => 'User not found. Failed to connect AD Server.', "result" => '');
								echo json_encode($result_obj);
								return "";


								//redirect('login/status/'.md5('password'));


							}


			return $result;			
		}

	function check_bookdose(){
  
		$version = trim($this->input->get_post('version'));
		if($version == "1.0.0"){
		   $result_obj = array("status" => 'success',"msg" => '', "result" => 'true');
		   echo json_encode($result_obj);
		   return "";
		}else{
		   $result_obj = array("status" => 'success',"msg" => '', "result" => 'false');
		   echo json_encode($result_obj);
		   return "";
		}
	}
	
}

?>