<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");
require_once(APPPATH."libraries/adLDAP/adLDAP.php");

class Login_controller extends User_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = 'front';
		@define("thisFrontTabMenu",'login');
		@define("folderName",'user/');
		
		$this->model = 'User_model';
		$this->user_login_history_model = 'User_login_history_model';
		
		$this->lang->load('mail');
	}
	
	function index()
	{
		$this->login();
	}
	
	function login()
	{
		@define("thisAction",'login');
		$user_aid = getSessionUserAid();
		$this->load->model($this->model,'user');
		$hash = $this->user->get_login_hash($user_aid);
		// $this->data["page_title"] = $this->lang->line('user_login_header');
		$this->data["page_title"] = 'Login';
		// if($user_aid != "" && getSessionUserHash() != "" && getSessionUserHash() == $hash){
		if($user_aid != ""){
			if(is_general_admin_or_higher()){
				redirect('home');
			}else if(is_staff_or_higher()){
				redirect('home');
			}else{
				redirect('home');
			}
		}else {
			$user_cookie =get_cookie('cuser'.CONST_HASH_KEY);
			$pass_cookie =get_cookie('cpass'.CONST_HASH_KEY);
			$owner_cookie =get_cookie('cowner'.CONST_HASH_KEY);
			$hash_cookie =get_cookie('chash'.CONST_HASH_KEY);

			if(CONST_LDAP_AUTHEN_SOAP !== TRUE && !is_blank($user_cookie) && !is_blank($pass_cookie)){
				$this->check($user_cookie,$pass_cookie,$owner_cookie,"1",$hash_cookie);
			}elseif(CONST_LDAP_AUTHEN !== TRUE && !is_blank($user_cookie) && !is_blank($pass_cookie)){
				$this->check($user_cookie,$pass_cookie,$owner_cookie,"1",$hash_cookie);
			}else{
				$this->data["title"] = DEFAULT_TITLE;
				$this->data["view_the_content"] = $this->default_theme_login . '/user/login_form';
				$this->data["message"] = '';
				if(is_web_service()){
					$this->data["js_code"] = '$("#owner_alias").focus();';
				}else{
					$this->data["js_code"] = '$("#user_name").focus();';
				}
				$this->load->view($this->default_theme_login.'/tpl_login', $this->data);
			}
		}
	}
	
	function verify()
	{
		@define("thisAction",'verify');
		$user_cid = $this->input->get_post('user_name');
		$user_pass = $this->input->get_post('user_password');
		$owner_alias = $this->input->get_post('owner_alias');
		$remember = $this->input->get_post('remember');
		

		if(is_blank($user_cid) || is_blank($user_pass)){
			redirect('login/status/'.md5('blank'));
		}
		
		if (CONST_LDAP_AUTHEN !== TRUE) {
			$this->load->model($this->model,'user');
			$user_pass = $this->user->encryptPassword($user_pass);
		}
		$this->check($user_cid,$user_pass,$owner_alias,$remember,"");
		
	}
	
	function check($user_cid,$user_pass,$owner_alias,$remember,$hash)
	{

		// echo "<br/>user_cid".$user_cid;
		// echo "<br/>user_pass".$user_pass;
		// echo "<br/>owner_alias".$owner_alias;
		// echo "<br/>remember".$remember;
		// echo "<br/>hash".$hash;
		//echo "string";

		$username = $user_cid;
		$owner_result = "";
		if(CONST_IS_WEB_SERVICE == "1"){ // If is web service
			if(!is_blank($owner_alias)){
				$this->load->model($this->user_owner_model,'user_owner');
				$this->user_owner->set_where(array("alias"=>$owner_alias));
				$owner_result = $this->user_owner->load_record(false);
				if(!is_var_array($owner_result)){
					switch (thisAction) {
						case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$owner_alias.' login with incorrect owner.'); break;
						case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$owner_alias.' login with incorrect owner.'); break;
						default : $this->log_status('Login from "Unwhere"', 'Login failed. '.$owner_alias.' login with incorrect owner.'); break;
					}
					redirect('login/status/'.md5('owner-notfound'));
				}

				//check status
				if(get_array_value($owner_result,"status","") != '1'){
					switch (thisAction) {
						case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$owner_alias.' is an inactive owner.'); break;
						case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$owner_alias.' is an inactive owner.'); break;
						default : $this->log_status('Login from "Unwhere"', 'Login failed. '.$owner_alias.' is an inactive owner.'); break;
					}
					redirect('login/status/'.md5('owner-status'));
				}

				//check expiration date
				$expiration_date = get_datetime_pattern("ymd",get_array_value($owner_result,"expiration_date",""),"");
				// echo "<br>expiration_date = ".$expiration_date;
				if(!is_blank($expiration_date)){
					$today = date("ymd");
					// echo "<br>today = ".$today;
					if($expiration_date < $today){
						switch (thisAction) {
							case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$owner_alias.' is expired.'); break;
							case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$owner_alias.' is expired.'); break;
							default : $this->log_status('Login from "Unwhere"', 'Login failed. '.$owner_alias.' is expired.'); break;
						}
						redirect('login/status/'.md5('owner-expire'));
					}
				}
				$user_owner_aid = get_array_value($owner_result,"aid","1");
			}else{
				$user_owner_aid = 1;
			}
		}else{
			$user_owner_aid = "";
		}
	
		$this->load->model($this->model,'user');
		if(!is_blank($user_owner_aid)){
			$this->user->set_where(array("user_owner_aid"=> $user_owner_aid));
		}
		//check username
		if(CONST_LOGIN_BY_DOMAIN == "1"){
			$pos = strrpos($user_cid, "@");
			$master_user_domain = $this->data["master_user_domain"];
			$user_domain_name = $this->input->get_post('user_domain_name');
			if ($pos === false) {
				$user_cid .= $user_domain_name;
			}
			$this->user->set_where(array("email"=>$user_cid));
		}else{
			$pos = strrpos($user_cid, "@");
			if ($pos === false) {
				$this->user->set_where(array("username"=>$user_cid));
			}else{
				$this->user->set_where(array("email"=>$user_cid));
			}
		}
		$this->user->set_limit(0,1);
		$result = $this->user->load_record(false);
		/*
		$new_user_aid = '';
		if (!is_var_array($result) && CONST_LDAP_AUTHEN == TRUE) {
			$data_user = array();
			$pos = strrpos($user_cid, "@");
			if ($pos !== false) {
				$data_user["email"] = $user_cid;
			}
			$data_user["username"] = $username;
			$data_user["first_name_th"] = $username;
			$data_user["user_role_aid"] = '5';
			$new_user_aid = $this->user->insert_record($data_user);
			if ($new_user_aid > 0) {
				$this->log_status('Auto insert new username ['.$username.'] into database.', 'Access', 'User aid = '.$new_user_aid);
				$this->user->set_where(array("aid"=>$new_user_aid));
				$result = $this->user->load_record(false);
			}
			else {
				$this->log_status('Fail auto insert new username ['.$username.'] into database.', 'Access', '');
			}
		}
*/
		if ( 
				(is_var_array($result)) ||
				(!is_var_array($result))
		) {
			/*
			if (is_var_array($result)) {
				$this->session->set_userdata('username',$user_cid);
				$this->user_login_info = $result;
				// echo "id = ".getUserLoginAid($this)."<BR>";

				$this->log_status('Found user ['.$user_cid.']', 'Access', '');
			}
			*/

			// Authen with LDAP Server
			if ( CONST_LDAP_AUTHEN == TRUE && 
				( 
					(is_var_array($result) && $result['password'] == "") ||
					(!is_var_array($result))
				)
			) {
				$pos = strrpos($username, "@");
				if ($pos !== false) {
					$username = substr($username, 0, $pos);
				}

				$this->log_status('Trying to connect LDAP server...', 'Access', '['.$user_cid.'] is trying to authenticate with LDAP server...');

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
			            // exit();
			            $this->log_status('Cannot initiate LDAP instance', 'Access', $e);
			           	redirect('login/status/'.md5('failed-to-connect-adldap'));
			        }

					$ip_address = $_SERVER['REMOTE_ADDR'];
				/*
				$adldap->close();
				$adldap->set_ad_username($username);
				$adldap->set_ad_password($user_pass);
				$conn = $adldap->connect();
				$this->log_status('Connection status = '.$conn, 'Access', 'Connection status = '.$conn);
				*/

					$ldap_msg = "";
					$authen_status = false;
					// if($conn == 0) {
					try {
						$authUser = $adldap->authenticate($username, $user_pass);
					}
					catch (adLDAPException $e) {
					   // echo $e; 
					   // exit();
					   $this->log_status('Failed to authenticate', 'Access', $e);
					  	redirect('login/status/'.md5('password'));
					}

					if ($authUser !== true){
						$ldap_msg = $adldap->getLastError();
						$this->log_status('Failed to connect LDAP Server, cannot authenticate user "'.$username.'"', 'Access', 'Cannot connect LDAP Server. '.$ldap_msg.' ['.$username.'].');
						redirect('login/status/'.md5('password'));
						// redirect('login/status/'.md5('failed-to-connect-adldap'));
					}
					else {
						$authen_status = true;
						$this->log_status('LDAP authen success', 'Access', '['.$username.'] passed LDAP authentication successfully.');
					}

					if ($authen_status == true && !is_var_array($result)) {
						$obj_user = $adldap->user()->infoCollection($username, array('*'));
						$this->user->set_where(array("email"=>$obj_user->mail));
						$this->user->set_limit(0,1);
						$result = $this->user->load_record(true);
					}

					if ($authen_status == true && !is_var_array($result)) {
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
						if ($pos !== false) {
							$data_user["email"] = $obj_user->mail; // $user_cid;
						}
						$data_user["username"] = $username;
						$data_user["first_name_th"] = $obj_user->displayName; // $obj_user->firstname; // $username;
						// $data_user["last_name_th"] =  $obj_user->displayName; // $obj_user->surname; // $username;
						$data_user["display_name"] =  $obj_user->displayName;
						$data_user["user_role_aid"] = '5';
						$this->load->model($this->setting_config_model,'setting_config');	
						do {
							$obj = $this->setting_config->get_config_rni_by_cid("rn-user-1");
							$cid = trim(get_array_value($obj,"barcode",""));
						}while( $this->isUserCidExits($cid) );
						$data_user["cid"] = $cid;
						
						$new_user_aid = $this->user->insert_record($data_user);
						if ($new_user_aid > 0) {
							$this->log_status('Auto insert new username ['.$username.'] into database.', 'Access', 'User aid = '.$new_user_aid);
							$this->user->set_where(array("aid"=>$new_user_aid));
							$result = $this->user->load_record(true);
						}
						else {
							$this->log_status('Failed to auto insert new username ['.$username.'] into our database.', 'Access', '');
						}
					}

					if (is_var_array($result)) {
						$this->session->set_userdata('username',$user_cid);
						$this->user_login_info = $result;
						// echo "id = ".getUserLoginAid($this)."<BR>";

						$this->log_status('Found user ['.$user_cid.']', 'Access', '');
					}


			} else {

				if (CONST_LDAP_AUTHEN == TRUE && $result['password'] !== "") {
					$user_pass = $this->user->encryptPassword($user_pass);
				}

				// check password
				if(get_array_value($result,"password","") != $user_pass){
					switch (thisAction) {
						case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' login with incorrect password.'); break;
						case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' login with incorrect password.'); break;
						default : $this->log_status('Login from "Unwhere"', 'Login failed. '.$user_cid.' login with incorrect password.'); break;
					}
					redirect('login/status/'.md5('password'));
				}
				
				//check status
				if(get_array_value($result,"status","") != '1'){
					switch (thisAction) {
						case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' is an inactive user.'); break;
						case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' is an inactive user.'); break;
						default : $this->log_status('Login from "Unwhere"', 'Login failed. '.$user_cid.' is an inactive user.'); break;
					}
					redirect('login/status/'.md5('status'));
				}

				//check expiration date
				$expiration_date = get_datetime_pattern("ymd",get_array_value($result,"expiration_date",""),"");
				// echo "<br>expiration_date = ".$expiration_date;
				if(!is_blank($expiration_date)){
					$today = date("ymd");
					// echo "<br>today = ".$today;
					if($expiration_date < $today){
						switch (thisAction) {
							case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' is expired.'); break;
							case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' is expired.'); break;
							default : $this->log_status('Login from "No where"', 'Login failed. '.$user_cid.' is expired.'); break;
						}
						redirect('login/status/'.md5('expire'));
					}
				}
				
				//check activation
				if(!is_blank($result["activate_code"])){
					switch (thisAction) {
						case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' is not activate.'); break;
						case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' is not activate.'); break;
						default : $this->log_status('Login from "No where"', 'Login failed. '.$user_cid.' is not activate.'); break;
					}
					switch(CONST_PASSWORD_TYPE){
						case "4" : redirect('login/status/'.md5('wait-for-activate')); break;
						default : redirect('activation/'.get_array_value($result,"email","0")); break;
					}

				}
			}

			// Authen with LDAP Server
			// if ( CONST_LDAP_AUTHEN_SOAP == TRUE && 
			// 	( 
			// 		(is_var_array($result) && $result['password'] == "") ||
			// 		(!is_var_array($result))
			// 	)
			// ) {
				

			// 		//header('Content-Type: text/html; charset=utf-8');

			// 		$client = new SoapClient("http://172.20.0.48/adrmutt/adrmutt.asmx?wsdl",

			// 			array(
						
			// 			"trace"      => 1,     // enable trace to view what is happening
						
			// 			"exceptions" => 0,     // disable exceptions
						
			// 			"cache_wsdl" => 0)         // disable any caching on the wsdl, encase you alter the wsdl server
						
			// 			);

			// 		$params = array(
					
			// 					'apikey' => "vo6iydknris,[6[zk",
			// 					'userName' => $user_cid,
			// 					'passWord' => $user_pass
								
			// 		);
			// 		$data1 = $client->GetADProperties($params);

			// 		print_r($data1);
					
			// 		$firstName = $data1->GetADPropertiesResult->firstName;
			// 		$lastName = $data1->GetADPropertiesResult->lastName;

			// 		$department = $data1->GetADPropertiesResult->department;

			// 		$telephonenumber = $data1->GetADPropertiesResult->telephonenumber;

			// 		$mail = $data1->GetADPropertiesResult->mail;

			// 		echo "<pre>";
			// 		print_r($data1);
			// 		echo "</pre>";
			// 		echo "<br/>firstName = ".$firstName;
			// 		echo "<br/>lastName = ".$lastName;
			// 		echo "<br/>department = ".$department;
			// 		echo "<br/>telephonenumber = ".$telephonenumber;
			// 		echo "<br/>mail = ".$mail;
			// 		//dir();


			// } else {

			// 	if (CONST_LDAP_AUTHEN_SOAP == TRUE && $result['password'] !== "") {
			// 		$user_pass = $this->user->encryptPassword($user_pass);
			// 	}

			// 	// check password
			// 	if(get_array_value($result,"password","") != $user_pass){
			// 		switch (thisAction) {
			// 			case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' login with incorrect password.'); break;
			// 			case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' login with incorrect password.'); break;
			// 			default : $this->log_status('Login from "Unwhere"', 'Login failed. '.$user_cid.' login with incorrect password.'); break;
			// 		}
			// 		redirect('login/status/'.md5('password'));
			// 	}
				
			// 	//check status
			// 	if(get_array_value($result,"status","") != '1'){
			// 		switch (thisAction) {
			// 			case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' is an inactive user.'); break;
			// 			case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' is an inactive user.'); break;
			// 			default : $this->log_status('Login from "Unwhere"', 'Login failed. '.$user_cid.' is an inactive user.'); break;
			// 		}
			// 		redirect('login/status/'.md5('status'));
			// 	}

			// 	//check expiration date
			// 	$expiration_date = get_datetime_pattern("ymd",get_array_value($result,"expiration_date",""),"");
			// 	// echo "<br>expiration_date = ".$expiration_date;
			// 	if(!is_blank($expiration_date)){
			// 		$today = date("ymd");
			// 		// echo "<br>today = ".$today;
			// 		if($expiration_date < $today){
			// 			switch (thisAction) {
			// 				case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' is expired.'); break;
			// 				case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' is expired.'); break;
			// 				default : $this->log_status('Login from "No where"', 'Login failed. '.$user_cid.' is expired.'); break;
			// 			}
			// 			redirect('login/status/'.md5('expire'));
			// 		}
			// 	}
				
			// 	//check activation
			// 	if(!is_blank($result["activate_code"])){
			// 		switch (thisAction) {
			// 			case "verify" : $this->log_status('Login from "Login Page"', 'Login failed. '.$user_cid.' is not activate.'); break;
			// 			case "login" : $this->log_status('Login from "Cookie"', 'Login failed. '.$user_cid.' is not activate.'); break;
			// 			default : $this->log_status('Login from "No where"', 'Login failed. '.$user_cid.' is not activate.'); break;
			// 		}
			// 		switch(CONST_PASSWORD_TYPE){
			// 			case "4" : redirect('login/status/'.md5('wait-for-activate')); break;
			// 			default : redirect('activation/'.get_array_value($result,"email","0")); break;
			// 		}

			// 	}
			// }




			$this->log_status('Start checking some conditions', 'Access', '['.$user_cid.'] Start checking some conditions after authentication.');
			
			// Check is first login
			$is_first_login = (is_blank($result['last_login'])) ? 1 : 0;
			$is_first_login_txt = ($is_first_login==1) ? " for the first time" : "";

			//update lasted login datetime
			$login_hash = md5(mktime().$result["aid"]);
			$result["login_hash"] = $login_hash;
			$chk = $this->user->update_last_login($result);
			$this->session->set_userdata('userSession',$result);
			
			//if check Remember Me, save data to cookie
			if($remember == "1"){
				$cookie_user = array(
					   'name'   => 'cuser'.CONST_HASH_KEY,
					   'value'  => $user_cid,
					   'expire' => '604800'
				   );
				set_cookie($cookie_user); 
				
				$cookie_pass = array(
					   'name'   => 'cpass'.CONST_HASH_KEY,
					   'value'  => $user_pass,
					   'expire' => '604800'
				   );
				set_cookie($cookie_pass); 
				
				$cookie_owner = array(
					   'name'   => 'cowner'.CONST_HASH_KEY,
					   'value'  => $owner_alias,
					   'expire' => '604800'
				   );
				set_cookie($cookie_owner); 
				
				$cookie_hash = array(
					   'name'   => 'chash'.CONST_HASH_KEY,
					   'value'  => $login_hash,
					   'expire' => '604800'
				   );
				set_cookie($cookie_hash); 
			}else{
				delete_cookie('cuser'.CONST_HASH_KEY);
				delete_cookie('cpass'.CONST_HASH_KEY);
				delete_cookie('cowner'.CONST_HASH_KEY);
				delete_cookie('chash'.CONST_HASH_KEY);
			}
			
			switch (thisAction) {
				case "verify" : $this->log_status('Login from "Login Page"', $user_cid.' just login'.$is_first_login_txt.'. remember : ['.$remember.']'); break;
				case "login" : $this->log_status('Login from "Cookie"', $user_cid.' just login'.$is_first_login_txt.'. remember : ['.$remember.']'); break;
				default : $this->log_status('Login from "Unwhere"', $user_cid.' just login'.$is_first_login_txt.'. remember : ['.$remember.']'); break;
			}
			
			$token = $this->save_user_login_history("login", get_array_value($result,"aid","0"), "web", "", "login-form", "");
			
			$lasted_url = $this->session->userdata('lasted_url');
			if(false && $is_first_login){
				redirect('change-password');
			}else if(!is_blank($lasted_url)){
				redirect($lasted_url);
			}else if(is_staff_or_higher()){
				redirect('home');
			}else{
				redirect('home');
			}
			
		}
		else{
			$this->log_status('User not found ['.$user_cid.'] on our database. Please create this user via backend system.', 'Access', '');
			redirect('login/status/'.md5('username'));
		}
		redirect('login/status');	
	}
	
	function status($type="")
	{
		// $this->data["page_title"] = $this->lang->line('user_login_header');
		$this->data["page_title"] = '<span class="textStart">Log</span><span class="textSub">in</span>';
		switch($type)
		{
			case md5('blank') : 
				$this->data["message"] = set_message_error('Please enter username and password.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('owner-notfound') : 
				$this->data["message"] = set_message_error('Shop alias is incorrect. Please try again.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('owner-status') : 
				$this->data["message"] = set_message_error('Shop alias was suspended. Please contact administrator to solve the problem.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('owner-expire') : 
				$this->data["message"] = set_message_error('Shop alias was expired. Please contact administrator to solve the problem.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('username') : 
				$this->data["message"] = set_message_error('Username or password is incorrect. Please try again.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('password') : 
				$this->data["message"] = set_message_error('Username or password is incorrect. Please try again.');
				$this->data["username"] = $this->session->userdata('username');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('forgot-success') : 
				$this->data["message"] = set_message_success('Our email was sent to your mailbox.<BR>If you have not got the email in your Inbox, please verify the email in your junk box or spam folder.');
				$this->data["js_code"] = '';
				break;
			case md5('status') : 
				$this->data["message"] = set_message_error('This user was suspended. Please contact administrator to solve the problem.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('permission') : 
				$this->data["message"] = set_message_error('This page was abandoned or you do not have permission to access.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('expire') : 
				$this->data["message"] = set_message_error('This user was expired. Please contact administrator to solve the problem.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('doublelogin') : 
				$this->data["message"] = set_message_error('This user is logging in system already.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('forgot-change-success') : 
				$this->data["message"] = set_message_success('Password has been changed.<BR>Please Login again with your new password.');
				$this->data["js_code"] = '';
				break;
			case md5('regist-success') : 
				switch(CONST_PASSWORD_TYPE){
					case "2" : $this->data["message"] = set_message_success('Registration almost complete.<BR>Please check your email to activate this account.<BR>Also check your junk mail or spam folder if you do not receive it.'); break;
					case "3" : $this->data["message"] = set_message_success('Registration complete.<BR>You can login now.'); break;
					case "4" : $this->data["message"] = set_message_success('Registration complete.<BR>Please wait for activation process. You will get confirmation email again when activation complete.'); break;
					default : $this->data["message"] = set_message_success('Registration complete.<BR>Please check your email for the password.<BR>Also check your junk mail or spam folder if you do not receive it.'); break;
				}
				$this->data["js_code"] = '';
				break;
			case md5('no-session') : 
				$this->data["message"] = set_message_success('Session time out. Please login again.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('wait-for-activate') : 
				$this->data["message"] = set_message_error('Your account still in activation process. Please be patiant or contact your admin.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('activation-already') : 
				$this->data["message"] = set_message_success('Your account has been activate. You can login now.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('activation-success') : 
				$this->data["message"] = set_message_success('Your account has been activate. You can login now.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('activation-resend-success') : 
				$this->data["message"] = set_message_success('Our email was sent to your mailbox.<BR>If you have not got the email in your Inbox, please verify the email in your junk box or spam folder.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('need-login') : 
				$this->data["message"] = set_message_error('You need to login before process.');
				$this->data["js_code"] = '$("#user_name").focus();';
				break;
			case md5('failed-to-connect-adldap') : 
				$this->data["message"] = set_message_error('Failed to connect to LDAP Server. Please try again or contact your administrator.');
				$this->data["js_code"] = "$('#user_name').focus();";
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again.');
				$this->data["js_code"] = '';
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/login_form';
		$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
	}
	
}

?>