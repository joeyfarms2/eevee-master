<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Package_point_front_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		define("thisFrontTabMenu",'package_point');
		define("thisFrontSubMenu",'');
		@define("folderName","order/order_front/");
		$this->data["page_title"] = '<span class="textStart">Buy</span><span class="textSub">Point</span>';

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
		$this->show();
	}
	
	function show(){
		@define("thisAction","show");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/package_point_list';
		
		$this->load->model($this->package_point_model,"package_point");
		$this->data["master_package_point"] = $this->package_point->load_package_points();

		$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
	}
	
	function buy_package_point($aid=""){
		@define("thisAction","buy_package_point");
		if(!is_login()){
			redirect('login');
		}

		if(is_blank($aid) || $aid <= 0){
			$this->data["message"] = set_message_error("No package found.");
			$this->show();
			return "";
		}
		
		$this->load->model($this->package_point_model,"package_point");
		$this->package_point->set_where(array("aid"=>$aid,"status"=>"1"));
		$package_point_result = $this->package_point->load_record(false);
		if(!is_var_array($package_point_result)){
			$this->data["message"] = set_message_error("No package found.");
			$this->show();
			return "";
		}

		$this->data["title"] = DEFAULT_TITLE;

		$master_payment_type = explode(":",CONST_MASTER_PAYMENT_TYPE_POINT);
		$payment_type = $this->session->userdata('paymentTypeSession');
		if(is_blank($payment_type) || !in_array($payment_type, $master_payment_type)){
			$payment_type = $master_payment_type[0];
		}

		switch ($payment_type) {
			case 'paysbuy':
				$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/package_point_paysbuy_form';
				break;
			case 'paypal':
				$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/package_point_paypal_form';
				break;
			
			default:
				break;
		}

		$this->load->model($this->setting_running_model,"running");
		$cid = $this->running->get_latest_invoice_by_year(date('Y'));

		$data = array();
		$data["cid"] = $cid;
		$data["user_aid"] = getSessionUserAid();
		$data["user_owner_aid"] = getUserOwnerAid($this);
		$data["payment_type"] = $payment_type;
		$data["actual_unit"] = get_array_value($package_point_result,"point","0");
		$data["actual_total"] = get_array_value($package_point_result,"price","0");
		$data["actual_grand_total"] = get_array_value($package_point_result,"price","0");
		$data["currency"] = "Baht";
		$data["need_transport"] = "0";
		$data["transport_status"] = "0";
		$data["transport_code"] = "";
		$data["status"] = "1"; //1=New coming, 2=In Process, 3=Approved, 4=Rejected
		$data["confirm_status"] = "0";
		$data["type"] = "1"; //1=Buy Point
		$data["package_aid"] = get_array_value($package_point_result,"aid","0");
		$data["buyer_name"] = getUserLoginFullName($this);
		$data["buyer_email"] = getUserLoginEmail($this);
		$data["buyer_contact"] = getUserContactNumber($this);
		$data["buyer_address"] = getUserAddress($this);
		$data["remark"] = "";
		$data["channel"] = "1"; //1=Website

		$this->load->model($this->order_main_model,"order_main");
		$aid = $this->order_main->insert_record($data);
		if($aid){			
			$this->log_status('Order : Package point', 'Order '.$cid.' just coming. Status is waiting for payment.', $data);
			
			$this->load->model($this->order_main_model,"order_main");
			$this->order_main->set_where(array("aid"=>$aid));
			$this->data["order_result"] = $this->order_main->load_record(true);
			
			$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
			
		}else{
			$this->log_error('Order : Package point', 'Error occure. insert_record()', $data);
			$this->data["message"] = set_message_error("Error occured.");
			$this->show();
			return "";
		}
	}
	
	function status($type="",$order_main_cid=""){
		
		switch($type)
		{
			case md5('success') : 
				$this->data["status"] = 'success';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_success("การสั่งซื้อเสร็จสมบูรณ์ เลขที่อ้างอิงคือ ".$order_main_cid."<BR>แต้มได้ถูกเพิ่มให้แล้ว");
				break;
			case md5('approve-fail') : 
				$this->data["status"] = 'error';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("การสั่งซื้อเกิดข้อผิดพลาด เลขที่อ้างอิงคือ ".$order_main_cid."<BR>กรุณาติดต่อผู้ดูแลระบบ");
				break;
			case md5('order-not-found') : 
				$this->data["status"] = 'error';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("ไม่พบใบสั่งซื้อ");
				break;
			case md5('fail') : 
				$this->data["status"] = 'error';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("การชำระเงินถูกปฎิเสธ เลขที่อ้างอิงคือ ".$order_main_cid."<BR>การสั่งซื้อถูกยกเลิก");
				break;
			case md5('pending') : 
				$this->data["status"] = 'info';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("การชำระเงินอยู่ระหว่างการรออนุมัติ เลขที่อ้างอิงคือ ".$order_main_cid."<BR>การสั่งซื้อถูกยกเลิก");
				break;
			case md5('process') : 
				$this->data["status"] = 'info';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_success("ใบสั่งซื้ออยู่ระหว่างการรอชำระเงิน เลขที่อ้างอิงคือ ".$order_main_cid);
				break;
			case md5('error') : 
				$this->data["status"] = 'info';
				$this->data["order_main_cid"] = $order_main_cid;
				$this->data["message"] = set_message_error("เกิดข้อผิดพลาดไม่สามารถทำรายการ");
				break;
			default : 
				$this->data["message"] = set_message_error("กรุณาลองใหม่อีกครั้ง");
				break;
		}
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/package_point_paysbuy_status';
		$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
	}
	
	function redeem_point_status($type="", $redeem_code=""){
		
		switch($type)
		{
			case md5('success') : 
				$this->data["status"] = 'success';
				$this->data["redeem_code"] = "";
				$this->data["message"] = set_message_success("Point added success.", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
			case md5('need-login') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("Please login before redeem code.", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
			case md5('blank') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("Please enter redeem code.", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
			case md5('code-not-found') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("This code is not found.", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
			case md5('not-point-code') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("This code is not for point redeem.", "", "", true, "result-msg-redeem-box");
				break;
			case md5('code-inactive') : 
			case md5('code-expired') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("This promotion is cancelled or expired.", "", "", true, "result-msg-redeem-box");
				break;
			case md5('code-early') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("This promotion is not start yet. Please come back later.", "", "", true, "result-msg-redeem-box");
				break;
			case md5('code-run-out') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("This code is used or reach maximum limitation.", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
			case md5('code-run-out-for-user') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("You already reach maximum limitation for this promotion.", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
			case md5('code-used') : 
				$this->data["status"] = 'error';
				$this->data["redeem_code"] = $redeem_code;
				$this->data["message"] = set_message_error("This code is already used.", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
			default : 
				$this->data["message"] = set_message_error("กรุณาลองใหม่อีกครั้ง", "", "", true, "result-msg-redeem-box");
				$this->data["js_code"] = '$("#redeem_code").focus();';
				break;
		}
		$this->show();
	}
	
	function ajax_update_status($sid=""){
		$order_main_cid = $this->input->get_post('order_main_cid');
		$this->load->model($this->order_main_model,"order_main");
		$this->order_main->set_where(array("cid"=>$order_main_cid));
		$order_result = $this->order_main->load_record(true);
		if(!is_var_array($order_result)){
			$this->log_error('Payment Feedback', 'Order not found ['.$order_main_cid.']. Do nothing.');
			$msg = set_message_error('ไม่พบใบสั่งซื้อ');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return"";
		}
		
		$order_status = get_array_value($order_result,"status","");
		switch ($order_status){
			case "1" : 
				$msg = set_message_success('ใบสั่งซื้ออยู่ระหว่างการรอชำระเงิน เลขที่อ้างอิงคือ '.$order_main_cid);
				$result_obj = array("status" => 'success',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			case "2" : 
				$msg = set_message_success('ใบสั่งซื้ออยู่ระหว่างการรอชำระเงิน เลขที่อ้างอิงคือ '.$order_main_cid);
				$result_obj = array("status" => 'success',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			case "3" : 
				$msg = set_message_success('การสั่งซื้อเสร็จสมบูรณ์ เลขที่อ้างอิงคือ '.$order_main_cid.'<BR>แต้มได้ถูกเพิ่มให้แล้ว');
				$result_obj = array("status" => 'success',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			case "4" : 
				$msg = set_message_error('การชำระเงินถูกปฎิเสธ เลขที่อ้างอิงคือ '.$order_main_cid.'<BR>การสั่งซื้อถูกยกเลิก');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
			default : 
				$msg = set_message_error('ไม่พบใบสั่งซื้อ');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
				break;
		}
	}
}

?>