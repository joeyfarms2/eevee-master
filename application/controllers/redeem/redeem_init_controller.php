<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Redeem_init_controller extends Project_init_controller {

	function __construct()	{
		parent::__construct();

		$this->redeem_main_model = "Redeem_main_model";
		$this->redeem_detail_model = "Redeem_detail_model";
		$this->redeem_history_model = "Redeem_history_model";
		$this->point_history_model = "Point_history_model";
	}
	
	function index(){
	}
		
	function isRedeemDetailCidExits($cid){
		$this->load->model($this->redeem_detail_model,"redeem_detail");
		$this->redeem_detail->set_where(array("cid"=>$cid));
		$total = $this->redeem_detail->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function check_exits_redeem_main($redeem_main_aid="",$return_json=false){
		if(is_blank($redeem_main_aid)){
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this redeem.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('redeem-not-found'));
				exit(0);
			}
		}
		$this->load->model($this->redeem_main_model,'redeem_main');
		$this->redeem_main->set_where(array("aid"=>$redeem_main_aid));
		if(!exception_about_status()) $this->redeem_main->set_where(array("status"=>'1'));
		$item_detail = $this->redeem_main->load_record(true);
		if(is_var_array($item_detail)){
			return $item_detail;
		}else{
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this redeem.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('redeem-not-found'));
				exit(0);
			}
		}
	}
	

}

?>