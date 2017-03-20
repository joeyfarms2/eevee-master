<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_type_minor_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'setting');
		define("thisAdminSubMenu",'category');
		@define("folderName",'product/');
		
		define("TXT_TITLE",'Category management');
		define("TXT_INSERT_TITLE",'Category management : Add new category');
		define("TXT_UPDATE_TITLE",'Category management : Edit category');
				
		$this->main_model = 'Product_type_minor_model';
		$this->product_main_model = 'Product_main_model';
				
	}
	
	function index(){
		return "";
	}
	
	function ajax_get_product_type_minor_by_product_main($sid,$product_main_aid){
		$this->load->model($this->product_main_model,'product_main');
		$this->product_main->set_where('aid', $product_main_aid);
		$product_main_result = $this->product_main->load_record(false);

		$is_license = $this->input->get_post("is_license");
		if(!is_owner_admin_or_higher()){
			$is_license = 0;
		}
	
		if(!is_var_array($product_main_result)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Product main result not found.');
			echo json_encode($result_obj);
			return"";
		}
		
		$product_type_aid = get_array_value($product_main_result,"product_type_aid","");
		$this->load->model($this->main_model,'main');
		$this->main->set_where('product_type_aid', $product_type_aid);
		$this->main->set_where('is_license', $is_license);
		$this->main->set_where(array("status"=>"1"));
		if(!is_general_admin_or_higher()){
			$this->main->set_where_not_equal(array("aid"=>"3"));
		}
		$this->main->set_order_by("weight ASC");
		$result_list = $this->main->load_records(false);
		
		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "result" => $result_list);
			echo json_encode($result_obj);
			return"";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.');
			echo json_encode($result_obj);
			return"";
		}
	}
	
}

?>