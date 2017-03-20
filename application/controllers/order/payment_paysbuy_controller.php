<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/order/order_save_controller.php");

class Payment_paysbuy_controller extends Order_save_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";

		$this->package_point_model = "Package_point_model";
		$this->order_main_model = "Order_main_model";
		$this->user_model = "User_model";
		$this->point_history_model = "Point_history_model";
		$this->log_paysbuy_model = "Log_paysbuy_model";
		$this->order_receipt_model = "Order_receipt_model";
		$this->setting_running_model = "Setting_running_model";

		$this->lang->load('mail');
	}
	
	function index(){
		return "";
	}
		
	function save_approve_point($result_full="",$order_result=""){
		$order_main_cid = get_array_value($order_result,"cid","");
		$order_main_aid = get_array_value($order_result,"aid","");
		//If success payment
		$this->log_status('Paysbuy Feedback', 'Start!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].', $order_result);
		$chk_status = $this->save_point("paysbuy", $order_result);
		$this->log_status('Paysbuy Feedback', ' End!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].');
		return $chk_status;
	}

	function save_approve_basket($result_full="",$order_result=""){
		$order_main_cid = get_array_value($order_result,"cid","");
		$order_main_aid = get_array_value($order_result,"aid","");
		//If success payment
		$this->log_status('Paysbuy Feedback', 'Start!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].', $order_result);
		$chk_status = $this->save_basket("paysbuy", $order_result);
		$this->log_status('Paysbuy Feedback', ' End!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].');
		return $chk_status;
	}

	function save_back_from_paysbuy_front($what_to_buy){
		@define("thisAction","save_back_from_paysbuy_front");
		$this->data["title"] = DEFAULT_TITLE;
		
		$result_full = $_POST["result"];
		if(is_blank($result_full)){
			switch($what_to_buy){
				case "point" : 
					redirect('order/package-point/status/'.md5('order-not-found'));
					return "";
					break;
				case "basket" : 
					redirect('basket/confirm/status/'.md5('order-not-found'));
					return "";
					break;
				default : 
					redirect('home/status/'.md5('order-not-found'));
					return "";
					break;
			}
		}

		$result = substr($result_full, 0, 2);
		$order_main_cid = substr($result_full, 2);
		$apCode = $_POST["apCode"];
		$amt = $_POST["amt"];
		$fee = $_POST["fee"];
		$method = $_POST["method"];
		$confirm_cs = strtolower(trim(get_array_value($_POST,"confirm_cs","")));
		
		$this->data["order_main_cid"] = $order_main_cid;
		$this->data["result_full"] = $result_full;
		
		switch($what_to_buy){
			case "point" : 
				$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/package_point_paysbuy_status';
				$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
				break;
			case "basket" : 
				$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_paysbuy_status';
				$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
				break;
			default : 
				$this->data["view_the_content"] = $this->default_theme_front . '/home/home';
				$this->load->view($this->default_theme_front.'/tpl_home', $this->data);
				break;
		
		}
	}
	
	function save_back_from_paysbuy_back($what_to_buy){
		@define("thisAction","save_back_from_paysbuy_back");
		$result_full = $_POST["result"];
		$result = substr($result_full, 0, 2);
		$order_main_cid = substr($result_full, 2);
		$apCode = $_POST["apCode"];
		$amt = $_POST["amt"];
		$fee = $_POST["fee"];
		$method = $_POST["method"];
		$confirm_cs = strtolower(trim(get_array_value($_POST,"confirm_cs","")));
		/* status result
			00 = Success
			99 = Fail
			02 = Process
		*/
		//add record to log paysbuy
		$data = array();
		$data["result"] = $result_full;
		$data["apCode"] = $apCode;
		$data["amt"] = $amt;
		$data["fee"] = $fee;
		$data["method"] = $method;
		$data["confirm_cs"] = $confirm_cs;
		$data["controller"] = $what_to_buy;
		$data["action"] = "save_back_from_paysbuy_back";
		$data["owner_user_aid"] = getUserOwnerAid($this);
		$data["owner_detail"] = getUserOwnerDetailForLog($this);
		$this->load->model($this->log_paysbuy_model,"paysbuy");
		$chk = $this->paysbuy->insert_record($data);
		$data["aid"] = $chk;

		$ip = $this->input->ip_address();
		// echo "ip = $ip";
		if($ip != CONST_IP_PAYSBUY && $ip != CONST_IP_PAYSBUY_DEMO){
			//This not come from paysbuy
			echo "This is not from paysbuy!!";
			$this->log_error('Wrong Feedback', 'IP '.$ip.' try to send you a feed back as paysbuy.', $data);
			return"";
		}
		
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$order_result = $this->order_main->load_record(true);
		$log_result = array();
		$log_result["paysbuy_data"] = $data;
		$log_result["order_data"] = $order_result;
		if(!is_var_array($order_result)){
			echo "Order result not found";
			$this->log_error('Paysbuy Feedback', 'Order not found ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.']. Do nothing.', $log_result);
			return"";
		}
		$order_status = get_array_value($order_result,"status","");
		if($order_status == '3'){ //1=New coming, 2=In Process, 3=Approved, 4=Rejected
			$this->log_status('Paysbuy Feedback', 'Order ['.$order_main_cid.'] already approved. Result form paysbuy = ['.$result_full.']. Do nothing.', $log_result);
			return"";
		}
		
		if($order_status == '4'){ //1=New coming, 2=In Process, 3=Approved, 4=Rejected
			echo "Order already rejected";
			$this->log_status('Paysbuy Feedback', 'Order ['.$order_main_cid.'] already rejected. Result form paysbuy = ['.$result_full.']. Do nothing.', $log_result);
			return"";
		}
				
		if($result == "00"){
			if($method == "06"){
				if($confirm_cs == "true"){
					echo "Success";
					switch($what_to_buy){
						case "point" : 
							$status = $this->save_approve_point($result_full,$order_result);
							break;
						case "basket" : 
							$status = $this->save_approve_basket($result_full,$order_result);
							break;
						default : 
							break;
					}
					$order_result["paysbuy_result"] = $status;
					return $order_result;
				}else if($confirm_cs == "false"){
					echo "Fail";
					$this->paysbuy_save_reject($result_full,$order_result);
					$order_result["paysbuy_result"] = "fail";
					return $order_result;
				}else{
					echo "Process";
					$this->paysbuy_save_in_process($result_full,$order_result);
					$order_result["paysbuy_result"] = "process";
					return $order_result;
				}
			}else{
				echo "Success";
				switch($what_to_buy){
					case "point" : 
						$status = $this->save_approve_point($result_full,$order_result);
						break;
					case "basket" : 
						$status = $this->save_approve_basket($result_full,$order_result);
						break;
					default : 
						break;
				}
				$order_result["paysbuy_result"] = $status;
				return $order_result;
			}
		}else if($result == "99"){
			echo "Fail";
			$this->paysbuy_save_reject($result_full,$order_result);
			$order_result["paysbuy_result"] = "fail";
			return $order_result;
		}else if($result == "02"){
			echo "Process";
			$this->paysbuy_save_in_process($result_full,$order_result);
			$order_result["paysbuy_result"] = "process";
			return $order_result;
		}else{
			echo "Error";
			$this->log_error('Paysbuy Feedback', 'Error occurred. Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.']. Do nothing.', $log_result);
			$order_result["paysbuy_result"] = "error";
			return $order_result;
		}
	}
	
	function paysbuy_save_reject($result_full="",$order_result=""){
		$order_main_cid = get_array_value($order_result,"cid","");
		$log_result = array();
		$log_result["paysbuy_data"] = $result_full;
		$log_result["order_data"] = $order_result;
		$this->log_status('Paysbuy Feedback', 'Start!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].', $log_result);

		$this->save_reject("paysbuy", $order_result, $log_result);
	}
	
	function paysbuy_save_in_process($result_full="",$order_result=""){
		//If success payment
		$order_main_cid = get_array_value($order_result,"cid","");
		$log_result = array();
		$log_result["paysbuy_data"] = $result_full;
		$log_result["order_data"] = $order_result;
		$this->log_status('Paysbuy Feedback', 'Start!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].', $log_result);
		
		//update status to order_main
		$data = array();
		$data["status"] = "2"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected		
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$result = $this->order_main->update_record($data);
		if($result){
			$this->log_status('Paysbuy Feedback', ' Step 1. Order ['.$order_main_cid.']. Update status to in-process [status = 2]. Success', $log_result);
		}else{
			$this->log_error('Paysbuy Feedback', ' Step 1. Order ['.$order_main_cid.']. Update status to in-process[status = 2]. Fail', $log_result);
			// $this->order_main->set_trans_rollback();
		}
		$this->log_status('Paysbuy Feedback', ' End!! Order ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.'].', $log_result);
	}
	

}

?>