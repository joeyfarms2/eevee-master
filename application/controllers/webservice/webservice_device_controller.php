<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_device_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->device_register_model = 'Device_register_model';
		$this->user_model = 'User_model';
		$this->user_login_history_model = 'User_login_history_model';
	}
	
	function check_device_register(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();

		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
				
		$this->load->model($this->device_register_model,'device_register');
		$tmp = array();
		$tmp["device"] = $device;
		$tmp["device_id"] = $device_id;
		$this->device_register->set_where($tmp);
		$device_register_result = $this->device_register->load_record(false);
		if(is_var_array($device_register_result)){
			$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'success',"msg" => '', "result" => '0');
			echo json_encode($result_obj);
			return "";
		}
	}

	function register_device(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();

		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$device_token = trim($this->input->get_post('device_token'));
		if(is_blank($device_token)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_token.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$user_aid = 0;
		
		$token = trim($this->input->get_post('token'));
		if(!is_blank($token))
		{
			$login_history = $this->check_token();
			if(is_var_array($login_history))
			{
				$user_aid = get_array_value($login_history,"user_aid","0");
			}
		}
		
		
		
		
		$tmp = array();
		$tmp["device"] = $device;
		$tmp["device_id"] = $device_id;
		$tmp["device_token"] = $device_token;
		$tmp["status"] = "1";
		$tmp["receive_msg"] = "1";
		$tmp["user_owner_aid"] = "1";
		$tmp["user_aid"] = $user_aid;

		$this->load->model($this->device_register_model,"device_register");
		$this->device_register->insert_or_update($tmp);
		$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
		echo json_encode($result_obj);
		return "";
	}

}

?>