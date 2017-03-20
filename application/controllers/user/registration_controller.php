<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");

class Registration_controller extends User_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = 'front';
		if(is_login()){
			redirect('home/status/'.md5('need-logout'));
			return "";
		}
		if(CONST_ONLINE_REGIS == '0'){
			redirect('home');
			return "";
		}
		define("thisFrontTabMenu",'registration');
		@define("folderName",'user/');
		
		$this->load->model($this->user_role_model,'user_role');
		$this->data["master_user_role"] = $this->user_role->load_master_user_role();

		$this->data["page_title"] = 'SignUp';

		$this->lang->load('mail');
	}
	
	function index(){
		$this->add();
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->form();
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/registration_form';
		$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
	}
	
	function save(){
		@define("thisAction",'save');
		$command = $this->input->get_post('command');
		$username = "";
		$owner_alias = "";
		$user_owner_aid = "1";
		$activate_code = "";
		$user_role_aid = "5"; //Member
		//$user_section_aid = "1";

		$this->load->model($this->user_section_model,"user_section");
		$this->user_section->set_where(array("status"=>"1", "is_default" =>"1"));
		$user_section_default = $this->user_section->load_record(false);

		$email = trim($this->input->get_post('email'));
		// $user_domain_name = trim($this->input->get_post('user_domain_name'));
		// if(!is_blank($user_domain_name)){
		// 	$master_user_domain = $this->data["master_user_domain"];
		// 	$pos = strrpos($email, "@");
		// 	if ($pos === false) {
		// 		$email .= $user_domain_name;
		// 	}else{
		// 		$domain = substr(strrchr($email, "@"), 1);
		// 		$chk = false;
		// 		foreach ($master_user_domain as $item) {
		// 			$dname = get_array_value($item,"name","");
		// 			if($domain == $dname){
		// 				$chk = true;
		// 			}
		// 		}
		// 		if(!$chk){
		// 			$this->data["message"] = set_message_error('Your email is not be allowed.');
		// 			$this->data["js_code"] = '$("#email").focus();';
		// 			$this->data["command"] = $command;
		// 			$this->data["item_detail"] = $data;
		// 			$this->form();
		// 			return "";
		// 		}
		// 	}
		// }
			$pos = strrpos($email, "@");
			$domain = substr(strrchr($email, "@"), 1);
				if($domain == MAIN_DOMAIN_EMAIL){
					$data["user_section_aid"] = "2";
				}else{
					$data["user_section_aid"] = get_array_value($user_section_default,"aid","0");
				}
				

		$data["email"] = $email;
				
		$this->load->model($this->user_model,'user');
		if(is_specify_username()){
			$username = trim($this->input->get_post('username'));
		}

		
	


		$data["username"] = $username;
		$data["user_role_aid"] = $user_role_aid;
		
		$data["status"] = '1';
		$data["registration_date"] = get_db_now();
		$data["channel"] = "web";
		$data["first_name_th"] = $this->input->get_post('first_name_th');
		$data["last_name_th"] = $this->input->get_post('last_name_th');
		$data["gender"] = $this->input->get_post('gender');
		$data["address"] = $this->input->get_post('address');
		$data["contact_number"] = $this->input->get_post('contact_number');
		$data["note_1"] = $this->input->get_post('note_1');
		$data["note_2"] = $this->input->get_post('note_2');
		$data["note_3"] = $this->input->get_post('note_3');
		$data["note_4"] = $this->input->get_post('note_4');
		$data["position"] = $this->input->get_post('position');
		$data["department_aid"] = $this->input->get_post('department_aid');

		
		
		$cid = trim($this->input->get_post('cid'));
		if(!is_blank($cid)){
			$data["cid"] = $cid;
		}

		$this->session->set_userdata('forgot_email','');

		if(is_blank($email)){
			$this->data["message"] = set_message_error('Please enter your email.');
			$this->data["js_code"] = '$("#email").focus();';
			$this->data["command"] = $command;
			$this->data["item_detail"] = $data;
			$this->form();
			return "";
		}
		
		require_once('include/securimage/securimage.php');
		$securimage = new Securimage();
		$captcha = $this->input->get_post('captcha_code');
		if ($securimage->check($captcha) == false) {
			$this->data["message"] = set_message_error('The characters you enter was wrong. Please try again.');
			$this->data["js_code"] = '$("#captcha_code").focus();';
			$this->data["command"] = $command;
			$this->data["item_detail"] = $data;
			$this->form();
			return "";
		}
		
		$this->user->set_trans_start();
		
		if($command == "_insert"){
			$this->user->set_where(array("email"=>$email));
			$this->user->set_where(array("user_owner_aid"=>$user_owner_aid));
			if(!is_blank($username)){
				$this->user->set_or_where(array("username"=>$username));
			}
			if(!is_blank($cid)){
				$this->user->set_or_where(array("cid"=>$cid));
			}
			$userResult = $this->user->load_records(true);
			if(is_var_array($userResult)){
				$error_txt = "";
				$obj_name = "";
				foreach($userResult as $item){
					$tmp_email = get_array_value($item,"email","");
					$tmp_username = get_array_value($item,"username","");
					$tmp_cid = get_array_value($item,"cid","");
					if(!is_blank($email) && strtolower($email) == strtolower($tmp_email)){
						if(!is_blank($error_txt)) $error_txt .= '<BR>';
						$error_txt .= '"'.$email.'" is used by other.';
						if(is_blank($obj_name)) $obj_name = "email";
					}
					if(!is_blank($username) && strtolower($username) == strtolower($tmp_username)){
						if(!is_blank($error_txt)) $error_txt .= '<BR>';
						$error_txt .= '"'.$username.'" is used by other.';
						if(is_blank($obj_name)) $obj_name = "username";
					}
					if(!is_blank($cid) && strtolower($cid) == strtolower($tmp_cid)){
						if(!is_blank($error_txt)) $error_txt .= '<BR>';
						$error_txt .= '"'.$cid.'" is used by other.';
						if(is_blank($obj_name)) $obj_name = "cid";
					}
				}
				if(!is_blank($error_txt)) {
					$this->data["message"] = set_message_error($error_txt);
					if(!is_blank($obj_name)) $this->data["js_code"] = '$("#'.$obj_name.'").focus();';
					$this->data["command"] = $command;
					$this->data["item_detail"] = $data;
					$this->form();
					return "";
				}
			}
			
			if(is_specify_password()){
				$password = $this->input->get_post('password');
			}else{
				$password = $this->user->generate_new_password();
			}
			$data["password"] = $this->user->encryptPassword($password);
			
			switch (CONST_PASSWORD_TYPE){
				case '2' : $activate_code = $this->user->generate_new_password("8"); $subject = $this->lang->line('mail_subject_new_user_activate'); $body = $this->lang->line('mail_content_new_user_activate'); 
							break;
				case '3' : $subject = $this->lang->line('mail_subject_new_user'); $body = $this->lang->line('mail_content_new_user'); break;
				case '4' : $activate_code = $this->user->generate_new_password("8"); $subject = $this->lang->line('mail_subject_new_user_activate_by_admin'); $body = $this->lang->line('mail_content_new_user_activate_by_admin'); break;
				default : $subject = $this->lang->line('mail_subject_new_user_generate'); $body = $this->lang->line('mail_content_new_user_generate'); break;
			}
			
			switch (CONST_USERNAME_TYPE){
				case '2' : $login_type = "Email"; $login_user = $email; break;
				default : $login_type = "Username"; $login_user = $username; break;
			}
			$data["activate_code"] = $activate_code;
			$data["user_owner_aid"] = $user_owner_aid;
			
			if(is_blank($cid)){
				do{
					$this->load->model($this->setting_config_model,'setting_config');		
					$obj = $this->setting_config->get_config_rni_by_cid("rn-user-1");
					$cid = trim(get_array_value($obj,"barcode",""));
				}while( $this->isUserCidExits($cid) );
				$data["cid"] = $cid;
			}
			
			$aid = $this->user->insert_record($data);
			if($aid > 0){
				$this->log_status('Online registration', $login_user.'['.$aid.'] just saved into database. Wating for send email.');
				// $body = eregi_replace("[\]",'',$body);
				$body = str_replace("{doc_type}", "&nbsp;" , $body);
				$body = str_replace("{name}", trim(get_array_value($data,"email","")) , $body);
				$body = str_replace("{username}", $login_user, $body);
				$body = str_replace("{login_type}", $login_type, $body);
				$body = str_replace("{password}", $password, $body);
				$body = str_replace("{url}", site_url('activation/'.$email.'/'.$activate_code), $body);
				
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
				//echo $this->email->print_debugger();
				$this->log_debug('Online registration email', '['.$email.'] '.$body);
				if(@$this->email->send()){
					$this->log_status('Online registration', 'Welcome email sent success.'.$login_user.'['.$aid.'] just been submitted.');
					$this->user->set_trans_commit();
					redirect('login/status/'.md5('regist-success'));
					return "";
				}else{
					$this->log_status('Online registration', 'Welcome email sent fail. '.$login_user.'['.$aid.'] just been removed.');
					$this->user->set_trans_rollback();
					$this->data["message"] = set_message_error('Sorry, The system can not send email right now.<BR>Please try again later or contact administrator to solve the problem.');
					$this->data["js_code"] = '';
					$this->data["command"] = $command;
					$this->data["item_detail"] = $data;
					$this->form();
					return "";
				}
				
			}else{
				$this->log_error('Online registration', 'Command insert_record() fail. Cant insert '.$login_user);
			}
			
		}else{
			$this->log_error('Online registration', 'Command not found.');
			redirect('registration/status/'.md5('no-command'));
		}
	}
	
	function status($type=""){
		switch($type)
		{
			case md5('blank') : 
				$this->data["message"] = set_message_error('Please enter the email address registered.');
				$this->data["js_code"] = '$("#user_email").focus();';
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
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/registration_form';
		$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
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
	
}

?>