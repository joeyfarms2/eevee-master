<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/order/order_save_controller.php");

class Payment_point_controller extends Order_save_controller {

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
	
	function confirm_save_with_point(){
		$order_main_cid = $this->input->get_post("order_cid");
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$order_result = $this->order_main->load_record(true);
		if(is_var_array($order_result)){
			$status = get_array_value($order_result,"status","0");
			if($status == '3'){
				$this->log_status('Buy with point', 'Start!! Order ['.$order_main_cid.'].', $order_result);
				$chk_status = $this->save_basket("point", $order_result);
				$this->log_status('Buy with point', ' End!! Order ['.$order_main_cid.'].');
				redirect('basket/confirm/status/'.md5('success').'/'.$order_main_cid);
			}else{
				redirect('basket/confirm/status/'.md5('fail').'/'.$order_main_cid);
			}
		}else{
			redirect('basket/confirm/status/'.md5('order-not-found').'/'.$order_main_cid);
		}

	}
	

}

?>