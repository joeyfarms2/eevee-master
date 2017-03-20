<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/user/user_init_controller.php");

class Change_password_controller extends User_init_controller {

	function __construct()
	{
		parent::__construct();
		for_login();
		$this->data["mode"] = 'front';
		define("thisFrontTabMenu",'change_password');
		define("thisAdminSubMenu",'');
		define("folderName",'user/');
		$this->user_model = 'User_model';

		$this->data["page_title"] = '<span class="textStart">Change</span><span class="textSub">Password</span>';

	}
	
	function index()
	{
		$this->form();
	}	
		
	function form()
	{
		@define("thisAction",'form');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/change_password_form';		
		$this->data["message"] = '';
		$this->load->view($this->default_theme_login.'/tpl_login', $this->data);
	}
		
	function save()
	{
		@define("thisAction",'save');
		
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array("aid"=>getSessionUserAid()));
		$result = $this->user->load_record(false);
		if(!is_var_array($result)){
			redirect('change-password/status/'.md5('not-found'));
			return "";
		}
		$pass = get_array_value($result,"password","");
		$old_pass = $this->input->get_post('old_password');
		$new_pass = $this->input->get_post('new_password_2');
		
		if($this->user->encryptPassword($old_pass) != $pass){
			redirect('change-password/status/'.md5('not-match'));
		}
		
		$data_where["aid"] = getSessionUserAid();
		$data_update["password"] = $this->user->encryptPassword($new_pass);
		$this->user->set_where($data_where);
		$result = $this->user->update_record($data_update);
		if($result){
			$this->log_status('Change password', getUserLoginEmail($this).'['.getSessionUserAid().'] just change password.', $result);
			// redirect('change-password/status/'.md5('success'));
			redirect('my-account/status/'.md5('password-save-success'));
		}
		else {
			$this->log_status('Change password', getUserLoginEmail($this).'['.getSessionUserAid().'] fail to change password.');
			$this->log_debug('Change password', 'Command update_record() failed for '.getUserLoginEmail($this).'['.getSessionUserAid().']', $result);
			redirect('change-password/status/'.md5('fail'));
		}
		
	}
	
	function status($type)
	{
		switch($type)
		{
			case md5('success') : 
				$this->data["message"] = set_message_success('Password has been changed.');
				$this->data["js_code"] = '';
				break;
			case md5('fail') : 
				$this->data["message"] = set_message_success('Sorry, Some error occured.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				break;
			case md5('not-match') : 
				$this->data["message"] = set_message_error('The old password is incorrect.');
				$this->data["js_code"] = '$("#old_password").focus();';
				break;
			case md5('not-found') : 
				$this->data["message"] = set_message_error('Sorry, We did not find this account in the system.');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again.');
				$this->data["js_code"] = '$("#old_password").focus();';
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_login . '/user/change_password_form';		
		$this->load->view($this->default_theme_login.'/tpl_login',$this->data);
	}
	
}

?>