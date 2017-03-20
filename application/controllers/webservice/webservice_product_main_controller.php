<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_product_main_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->product_topic_main_model = "Product_topic_main_model";
		$this->product_main_model = "Product_main_model";
		$this->product_main_field_model = "Product_main_field_model";
		$this->product_category_model = "Product_category_model";
		$this->product_type_model = "Product_type_model";
		$this->product_type_minor_model = "Product_type_minor_model";
	}
	
	
	function get_product_main_list(){
		$this->check_device();
		
		$this->load->model($this->product_main_model,'product_main');
		$this->product_main->set_where(array("status"=>"1"));
		
		// $order_by = 
	
		$result_list = $this->product_main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				// print_r($item);echo "<HR>";
				$obj = array();
				$obj["product_main_aid"] = get_array_value($item,"aid","");
				$obj["product_main_name"] = get_array_value($item,"name","");
				$obj["product_main_url"] = get_array_value($item,"url","");
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