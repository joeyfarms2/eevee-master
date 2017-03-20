<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/redeem/redeem_init_controller.php");

class Redeem_front_controller extends Redeem_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		define("thisFrontTabMenu",'home');
		define("thisFrontSubMenu",'');
		@define("folderName",'redeem/redeem_front');
						
		$this->lang->load('redeem');
	}
	
	function index(){
		return "";
	}

	function redeem_point(){
		if(!is_login()){
			redirect('login/status/'.md5('need-login'));
			return "";
		}

		$redeem_code = $this->input->get_post('redeem_code');
		// echo "redeem_code = $redeem_code<BR />";
		if(is_blank($redeem_code)){
			redirect('order/package-point/redeem-point-status/'.md5('blank'));
			return "";
		}

		$result = $this->check_redeem_code($redeem_code, getSessionUserAid(), "point");
		$status = get_array_value($result,"status","");
		if($status == "error"){
			$error_code = get_array_value($result,"error_code","");
			redirect('order/package-point/redeem-point-status/'.md5($error_code).'/'.$redeem_code);
			return "";
		}

		$redeem_result = get_array_value($result,"redeem_result","");
		if(!is_var_array($redeem_result)){
			redirect('order/package-point/redeem-point-status/'.md5('blank').'/'.$redeem_code);
			return "";
		}

		$redeem_main_aid = get_array_value($redeem_result,"redeem_main_aid","");
		$redeem_main_value = get_array_value($redeem_result,"redeem_main_value","0");
		
		//Step 1. add point to user
		$this->load->model($this->user_model,"user");
		$result = $this->user->add_point_remain(getSessionUserAid(), $redeem_main_value);
		if($result){
			$this->log_status('Redeem point', 'Step 1. Add point ['.$redeem_main_value.'] to user ['.getSessionUserAid().']. Success');

			//Step 2. add record to point history
			$data = array();
			$data["user_aid"] = getSessionUserAid();
			$data["order_aid"] = "0";
			$data["point_type"] = "1"; //1 = Receive, 2 Pay
			$data["point"] = $redeem_main_value;
			$data["status"] = "1"; //1=Active, 2=Inactive
			$data["redeem_code"] = $redeem_code;
			$this->load->model($this->point_history_model,"point_history");
			$result = $this->point_history->insert_record($data);
			if($result){
				$this->log_status('Redeem point', 'Step 2. Add point history. Success', $data);
			}

			$today_date = date('Y-m-d');
			//Step 3. add record to redeem history
			$data = array();
			$data["redeem_main_aid"] = $redeem_main_aid;
			$data["redeem_detail_cid"] = $redeem_code;
			$data["order_main_aid"] = "0";
			$data["status"] = "1"; //1=Active, 2=Inactive
			$data["user_aid"] = getSessionUserAid();
			$data["redeem_date"] = $today_date;
			$this->load->model($this->redeem_history_model,"redeem_history");
			$result = $this->redeem_history->insert_record($data);
			if($result){
				$this->log_status('Redeem point', 'Step 3. Add redeem history. Success', $data);
			}
		}
		redirect('order/package-point/redeem-point-status/'.md5('success').'/'.$redeem_code);
		return "";
	}
	
}

?>