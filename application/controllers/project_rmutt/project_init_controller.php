<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class Project_init_controller extends Initcontroller {

	function __construct()
	{
		parent::__construct();

		$this->product_category_ref_user_section_model = "Product_category_ref_user_section_model";
		
		$this->ads_model = "Ads_model";

		// echo "controllers = ".thisController;
		switch (thisController) {
			case 'login_controller':
			case 'forgot_controller':
				$this->load->model($this->ads_model,"ads");
				$arr_aid = array();
				$arr_aid[] = ",5,";
				$this->data["ads_list"] = $this->ads->load_ads_by_arr_aid($arr_aid);
				// echo "<br>sql : ".$this->db->last_query();
				break;
			
			case 'registration_controller':
				$this->load->model($this->ads_model,"ads");
				$arr_aid = array();
				$arr_aid[] = ",6,";
				$this->data["ads_list"] = $this->ads->load_ads_by_arr_aid($arr_aid);
				// echo "<br>sql : ".$this->db->last_query();
				break;
			
			case 'my_account_controller':
			case 'change_password_controller':
				$this->load->model($this->ads_model,"ads");
				$arr_aid = array();
				$arr_aid[] = ",7,";
				$this->data["ads_list"] = $this->ads->load_ads_by_arr_aid($arr_aid);
				// echo "<br>sql : ".$this->db->last_query();
				break;
			
			default:
				# code...
				break;
		}
	}
	
	function index()
	{
		return "";
	}

	function get_ads_by_category_aid($arr_aid){
		if(is_var_array($arr_aid)){
			$this->load->model($this->ads_model,"ads");
			$this->data["ads_list"] = $this->ads->load_ads_by_arr_aid($arr_aid);
			// echo "<br>sql : ".$this->db->last_query();
		}
	}
		
	function get_ads_by_products_main_aid($product_main_aid){
		switch ($product_main_aid) {
			case '1':
			case '3':
			case '4':
				$this->load->model($this->ads_model,"ads");
				$arr_aid = array();
				$arr_aid[] = ",4,";
				$this->data["ads_list"] = $this->ads->load_ads_by_arr_aid($arr_aid);
				// echo "<br>sql : ".$this->db->last_query();
				break;
			case '2':
				$this->load->model($this->ads_model,"ads");
				$arr_aid = array();
				$arr_aid[] = ",8,";
				$this->data["ads_list"] = $this->ads->load_ads_by_arr_aid($arr_aid);
				// echo "<br>sql : ".$this->db->last_query();
				break;
			case '5':
				$this->load->model($this->ads_model,"ads");
				$arr_aid = array();
				$arr_aid[] = ",2,";
				$this->data["ads_list"] = $this->ads->load_ads_by_arr_aid($arr_aid);
				// echo "<br>sql : ".$this->db->last_query();
				break;
		}
	}

	function get_relate_content($product_main_aid="", $product_type_aid=""){
		//load new
		$this->load->model($this->view_all_products_with_detail,"v_all_products");
		$this->v_all_products->set_where(array("status"=>'1', "is_new"=>'1'));
		if(!is_blank($product_main_aid)){
			$this->v_all_products->set_where(array("product_main_aid"=>$product_main_aid));
		}
		$this->v_all_products->set_order_by("*RAND()");
		$this->v_all_products->set_limit(0, 2);
		$new_list = $this->v_all_products->load_records(true);
		$this->data["new_list"] = $new_list;

		//load recommended
		$this->load->model($this->view_all_products_with_detail,"v_all_products");
		$this->v_all_products->set_where(array("status"=>'1', "is_recommended"=>'1'));
		if(!is_blank($product_main_aid)){
			$this->v_all_products->set_where(array("product_main_aid"=>$product_main_aid));
		}
		$this->v_all_products->set_order_by("*RAND()");
		$this->v_all_products->set_limit(0, 2);
		$recommended_list = $this->v_all_products->load_records(true);
		$this->data["recommended_list"] = $recommended_list;
	}
		
}

?>