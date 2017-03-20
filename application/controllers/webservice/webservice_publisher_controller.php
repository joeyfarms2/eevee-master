<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_publisher_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->publisher_model = "Publisher_model";
	}
	
	
	function get_publisher_list(){
		$this->check_device();
		
		$this->load->model($this->publisher_model,'publisher');
		$this->publisher->set_where(array("status"=>"1"));
		
		// $order_by = 
	
		$result_list = $this->publisher->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				// print_r($item);echo "<HR>";
				$obj = array();
				$obj["publisher_aid"] = get_array_value($item,"aid","");
				$obj["publisher_name"] = get_array_value($item,"name","");
				$result[] = $obj;
			}
		
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		
	}
	
		
}

?>