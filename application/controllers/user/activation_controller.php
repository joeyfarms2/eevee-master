<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");

class Activation_controller extends User_init_controller {

	function __construct()
	{
		parent::__construct();
		if(is_login()){
			redirect('home/status/'.md5('need-logout'));
			return "";
		}
		$this->data["mode"] = 'front';
		define("thisFrontTabMenu",'activation');
		define("thisAdminSubMenu",'');
		define("folderName",'user/');
		$this->model = 'User_model';
		
		$this->lang->load('mail');
		
	}
	
	function index($email="",$code="")
	{
		define("thisAction",'index');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["email"] = $email;
		
		$result = $this->check($email);
		if(is_blank($code)){
			$this->data["message"] = set_message_error('Please activate your account from your mailbox to confirm your registration. If you have not got any activation email, please click <a class="button" onclick="processSubmit(\'frm_actvation\')">here</a> to re-send activation email again.');
			$this->form();
			return "";
		}
		
		$activate_code = get_array_value($result,"activate_code","");
		$aid = get_array_value($result,"aid","");
		if($code == $activate_code){
			$this->load->model($this->model,"user");
			$this->user->confirm_activation($result);
			$this->log_status('User activation', $email.'['.$aid.'] just been activated.');
			redirect('login/status/'.md5('activation-success'));
		}else{
			$this->data["message"] = set_message_error('Your activation code is incorrect. Please check link from your email again or Click <a class="button" onclick="processSubmit(\'frm_actvation\')">here</a> to re-send activation email again.');
			$this->form();
			return "";
		}
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/activation_form';
		$this->load->view($this->default_theme_login.'/tpl_login', $this->data);
	}
	
	function check($email=""){
		$result = "";
		if(is_blank($email)){
			redirect('login');
			return "";
		}
		
		$this->load->model($this->model,"user");
		$this->user->set_where(array("email"=>$email));
		$result = $this->user->load_record(false);
		if(is_var_array($result)){
			$activate_code = get_array_value($result,"activate_code","");
			if(is_blank($activate_code)){
				redirect('login/status/'.md5('activation-already'));
				return "";
			}
			
			$status = get_array_value($result,"status","");
			if($status != '1'){
				redirect('login/status/'.md5('status'));
				return "";
			}
			
			$expiration_date = get_datetime_pattern("ymd",get_array_value($result,"expiration_date",""),"");
			if(!is_blank($expiration_date)){
				$today = date("ymd");
				if($expiration_date < $today){
					redirect('login/status/'.md5('exprie'));
					return "";
				}
			}
			
			return $result;
		}else{
			redirect('activation/status/'.md5('not-found'));
		}
	}
	
	function verify()
	{
		define("thisAction",'verify');
		$email = $this->input->post('email');
		
		// require_once('include/securimage/securimage.php');
		// $securimage = new Securimage();
		// $captcha = $this->input->get_post('captcha_code');
		// if ($securimage->check($captcha) == false) {
			// $this->session->set_userdata("forgot_email",$user_email);
			// redirect('forgot/status/'.md5('capcha'));
		// }
		
		$result = $this->check($email);

		$user_full_name = get_user_full_name($result);
		$activate_code = get_array_value($result,"activate_code","");
		$username = get_array_value($result,"username","");
		$aid = get_array_value($result,"aid","");
		//echo "activate_code : ".$activate_code;
		
		switch (CONST_USERNAME_TYPE){
			case '2' : $login_type = "Email"; $login_user = $email; break;
			default : $login_type = "Username"; $login_user = $username; break;
		}
		
		$subject = $this->lang->line('mail_subject_new_user_activate');
		$body = $this->lang->line('mail_content_new_user_activate');
				
		// $body = eregi_replace("[\]",'',$body);
		$body = str_replace("{doc_type}", "&nbsp;" , $body);
		$body = str_replace("{name}", trim($email) , $body);
		$body = str_replace("{username}", $login_user, $body);
		$body = str_replace("{login_type}", $login_type, $body);
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
		$this->log_debug('Resend activation email', '['.$email.'] '.$body);
		if(CONST_MODE == 2 || @$this->email->send()){
			$this->log_status('Resend activation email', 'Activation email re-sent success to '.$login_user.'['.$aid.'].');
			redirect('login/status/'.md5('activation-resend-success'));
			return "";
		}else{
			$this->log_status('Resend activation', 'Activation email re-sent fail to '.$login_user.'['.$aid.'].');
			$this->data["message"] = set_message_error('Sorry, The system can not send email right now.<BR>Please try again later or contact administrator to solve the problem.');
			$this->data["js_code"] = '';
			$this->form();
			return "";
		}
			
	}
	
	function status($type="")
	{
		switch($type)
		{
			case md5('blank') : 
				$this->data["message"] = set_message_error('Please enter the email address registered.');
				$this->data["js_code"] = '$("#user_email").focus();';
				break;
			case md5('not-found') : 
				$this->data["message"] = set_message_error('Sorry, We did not find this account in the system.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '';
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/activation_form';
		$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
	}
	
}

?>