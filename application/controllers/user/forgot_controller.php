<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");

class Forgot_controller extends User_init_controller {

	function __construct()
	{
		parent::__construct();
		if(is_login()){
			redirect('home/status/'.md5('need-logout'));
			return "";
		}
		$this->data["mode"] = 'front';
		define("thisFrontTabMenu",'forgot');
		define("thisAdminSubMenu",'');
		define("folderName",'user/');
		$this->model = 'User_model';
		
		$this->lang->load('mail');
	}
	
	function index()
	{
		define("thisAction",'index');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'ForgotPassword';
		$this->data["view_the_content"] = $this->default_theme_login . '/user/forgot_password_form';
		$this->data["message"] = set_message_info('Please enter the email address registered.');
		
		$this->load->view($this->default_theme_login.'/tpl_login', $this->data);
	}	
	
	function verify()
	{
		define("thisAction",'verify');
		$user_cid = $this->input->get_post('user_name');
		$user_email = $this->input->get_post('user_email');
		
		$this->session->set_userdata("forgot_email",'');

		// if(is_blank($user_cid) || is_blank($user_email)){
		if(is_blank($user_email)){
			redirect('forgot/status/'.md5('blank'));
		}
		
		require_once('include/securimage/securimage.php');
		$securimage = new Securimage();
		$captcha = $this->input->get_post('captcha_code');
		if ($securimage->check($captcha) == false) {
			$this->session->set_userdata("forgot_email",$user_email);
			redirect('forgot/status/'.md5('capcha'));
		}
		
		$this->load->model($this->model,'user');
		//check username and email
		// $this->user->set_where(array("username"=>$user_cid , "email"=>$user_email));
		$pos = strrpos($user_email, "@");
		if ($pos === false) {
			$this->user->set_where(array("username"=>$user_email));
		}else{
			$this->user->set_where(array("email"=>$user_email));
		}

		$result = $this->user->load_record(false);
		if(is_var_array($result))
		{
			$aid = get_array_value($result,"aid","");
			$username = get_array_value($result,"username","");
			$email = get_array_value($result,"email","");
			$status = get_array_value($result,"status","");
			if($status != "1"){
				$this->session->set_userdata("forgot_email",$user_email);
				redirect('forgot/status/'.md5('status'));
			}
			
			if(is_blank($aid) || is_blank($email)){
				$this->session->set_userdata("forgot_email",$user_email);
				redirect('forgot/status/'.md5('not-found'));
			}else{
				$user_full_name = getUserLoginFullName($result);
				$confirm_code = $this->user->generate_new_password("8");
				//echo "confirm_code : ".$confirm_code;
				
				$data = array(
						"confirm_code"	=>	$confirm_code
					);
				$this->user->set_where(array("aid"=>$aid));
				$result = $this->user->update_record($data);
				
				$subject = $this->lang->line('mail_subject_reset_password');
				$body = $this->lang->line('mail_content_reset_password');
				// $body =	eregi_replace("[\]",'',$body);
				$body = str_replace("{doc_type}", "&nbsp;", $body);
				$body = str_replace("{username}", $email, $body);
				$body = str_replace("{name}", $user_full_name, $body);
				$body = str_replace("{confirm_code}", $confirm_code, $body);
				
				$this->load->library('email');
				$config = $this->get_init_email_config();
				if(is_var_array($config)){ 
					$this->email->initialize($config); 
					$this->email->set_newline("\r\n");
				}
				$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
				$this->email->to($email);
				//$this->email->cc('another@another-example.com');
				//$this->email->bcc('them@their-example.com');

				$this->email->subject($subject);
				$this->email->message($body);
				//echo $this->email->print_debugger();
				// if(@$this->email->send()){
				$this->log_debug('Forgot password email', '['.$email.'] '.$body);
				if(@$this->email->send()){
					$this->log_status('Forgot password request', $email.'['.$aid.'] just submitted forgot password form. Confirm code is '.$confirm_code.'. Email sent success.');
					$this->session->set_userdata("forgot_emai",'');
					redirect('login/status/'.md5('forgot-success'));
				}else{
					$this->log_status('Forgot password request', $email.'['.$aid.'] just submitted forgot password form. Confirm code is '.$confirm_code.'. Email sent fail.');
					$this->session->set_userdata("forgot_email",$user_email);
					redirect('forgot/status/'.md5('mail-fail'));
				}
				
			}
		}else{
			$this->session->set_userdata("forgot_email",$user_email);
			redirect('forgot/status/'.md5('not-found'));
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
			case md5('blank-password') : 
				$this->data["message"] = set_message_error('Please enter the new password.');
				$this->data["js_code"] = '$("#user_email").focus();';
				break;
			case md5('not-found') : 
				$this->data["message"] = set_message_error('Sorry, We did not find this email in the system.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '$("#user_email").focus();';
				break;
			case md5('status') : 
				$this->data["message"] = set_message_error('Sorry, This email was suspended.<BR />Please contact administrator to solve the problem.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '$("#user_email").focus();';
				break;
			case md5('mail-fail') : 
				$this->data["message"] = set_message_error('Sorry, The system can not send email now.<BR />Please try again later or contact administrator to solve the problem.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '$("#user_email").focus();';
				break;
			case md5('capcha') : 
				$this->data["message"] = set_message_error('The characters you enter was wrong.<BR />Please try again.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '$("#captcha_code").focus();';
				break;
			case md5('confirm-code') : 
				$this->data["message"] = set_message_error('Confirmation code is incorrect,<BR />please check the URL from the email again.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again.');
				$this->data["forgot_email"] = $this->session->userdata('forgot_email');
				$this->data["js_code"] = '$("#user_email").focus();';
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'ForgotPassword';
		$this->data["view_the_content"] = $this->default_theme_login . '/user/forgot_password_form';
		$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
	}
	
	function change($username="",$code="")
	{
		define("thisAction",'change');
		if(is_blank($username) || is_blank($code)){
			redirect('forgot/status/'.md5('blank'));
		}
		$this->load->model($this->model,'user');
		
		$pos = strrpos($username, "@");
		if ($pos === false) {
			$this->user->set_where(array("username"=>$username));
		}else{
			$this->user->set_where(array("email"=>$username));
		}
		$result = $this->user->load_record(false);
		if(is_var_array($result))
		{
			$aid = get_array_value($result,"aid","");
			$username = get_array_value($result,"username","");
			$email = get_array_value($result,"email","");
			$status = get_array_value($result,"status","");
			$confirm_code = get_array_value($result,"confirm_code","");
			
			if($confirm_code != $code){
				redirect('forgot/status/'.md5('confirm-code'));
				return "";
			}
				
			$this->data["title"] = DEFAULT_TITLE;

			$this->data["view_the_content"] = $this->default_theme_login . '/user/forgot_password_change_form';
			$this->data["page_title"] = 'ChangePassword';
			$this->data["message"] = set_message_info('Enter you new password.');
			$this->data["aid"] = $aid;
			$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
			
		}else{
			redirect('forgot/status/'.md5('not-found'));
			return "";
		}
		
	}
	
	function change_save()
	{
		define("thisAction",'change_save');
		$aid = $this->input->get_post('aid');
		if(is_blank($aid)){
			redirect('forgot/status/'.md5('not-found'));
			return "";
		}
		$new_password = $this->input->get_post('new_password');
		if(is_blank($new_password)){
			redirect('forgot/status/'.md5('blank-password'));
			return "";
		}
		$this->load->model($this->model,'user');
		$this->user->reset_password($aid,$new_password);
		$data = array(
				"confirm_code"	=>	''
			);
		$this->user->set_where(array("aid"=>$aid));
		$result = $this->user->update_record($data);
		redirect('login/status/'.md5('forgot-change-success'));
	}
	
}

?>