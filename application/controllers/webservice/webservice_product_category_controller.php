<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_product_category_controller extends Init_webservice_controller {

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
	
	
	function get_product_category_list(){
		$this->check_device();

		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
				$user_login = $this->check_token();
			//print_r($user_login);
			if(is_var_array($user_login)){
				$user_aid = get_array_value($user_login,"user_aid","0");
				$this->load->model($this->user_model,'user');
				$this->user->set_where(array("aid"=>$user_aid));
				$user_result = $this->user->load_record(true);
				$user_section_aid = get_array_value($user_result,"user_section_aid","0");
			}
		}else{
			$user_section_aid = "0";
		}
		$this->load->model($this->product_category_ref_user_section_model,"ref_user_section");
		$this->ref_user_section->set_where(array("user_section_aid"=>$user_section_aid));
		$ref_user_section_all = $this->ref_user_section->load_records_array(false,"","product_category_aid");
		//print_r($ref_user_section_all);
		//echo "<br>sql : ".$this->db->last_query();
		//$cat_arr = "";
		//$cat_arr[] = "";
		if(is_var_array($ref_user_section_all)){
			foreach ($ref_user_section_all as $cid) {
				if(!is_blank($cid)){
					$cat_arr[] = $cid;
				}
			}
		}
				
		$this->load->model($this->product_category_model,'product_category');
		$this->product_category->set_where(array("status"=>"1"));

		//print_r($cat_arr);
		//user_section
		if(is_var_array($cat_arr)){
				$this->product_category->set_and_or_where(array("aid"=>$cat_arr));
		}
		
		
		$product_main_aid = $this->input->get_post('product_main_aid');
		// echo "product_main_aid = ".$product_main_aid;
		if(is_blank($product_main_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify product_main_aid.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		if( !is_number_no_zero($product_main_aid) ){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : product_main_aid must be integer.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		$this->product_category->set_where(array("product_main_aid"=>$product_main_aid));
		
		// $order_by = 
		$this->product_category->set_order_by("weight ASC, name ASC");
		$result_list = $this->product_category->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				// print_r($item);echo "<HR>";
				$obj = array();
				$obj["product_main_aid"] = get_array_value($item,"product_main_aid","");
				$obj["product_main_name"] = get_array_value($item,"product_main_name","");
				$obj["product_category_aid"] = get_array_value($item,"aid","");
				$obj["product_category_name"] = get_array_value($item,"name","");
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