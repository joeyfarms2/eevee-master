<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Review_front_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";

		$this->main_model = "Review_model";
		$this->review_history_model = "Review_history_model";
	}
	
	function index(){
		return "";
	}

	function ajax_get_main_list($sid){
		@define("thisAction",'ajax_get_main_list');
		$parent_aid = $this->input->get_post('parent_aid');
		$product_type_aid = $this->input->get_post('product_type_aid');

		$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid, true);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		if(is_blank($parent_aid)){
			$result_obj = array("status" => "error","msg" => "No product selected.");
			echo json_encode($result_obj);
			return"";
		}

		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";

		$data = array();
		$data["product_type_aid"] = $product_type_aid;
		$data["parent_aid"] = $parent_aid;
		if(!exception_about_status()) $data["status"] = '1';
		$this->load->model($this->main_model,"main");
		$this->main->set_where($data);

		$search_record_per_page = $this->getDataFromInput('search_record_per_page', CONST_DEFAULT_RECORD_FOR_REVIEW);
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->getDataFromInput('page_selected');
		$optional["record_per_page"] = $search_record_per_page;
		$optional = $this->get_pagination_info($optional);
		$data_search["page_selected"] = get_array_value($optional,"page_selected","");
		$data_search["search_record_per_page"] = get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_FOR_REVIEW);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_FOR_REVIEW));
		
		$search_order_by = $this->getDataFromInput('search_order_by');
		$data_search["search_order_by"] = $search_order_by;
		$order_by_option = $this->get_order_by_info($search_order_by,'created_date desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);

		$my_review = "";
		if(is_login() && is_var_array($result_list)){
			foreach ($result_list as $item) {
				if(getSessionUserAid() == get_array_value($item,"user_aid","0")){
					$my_review = $item;
					break;
				}
			}
		}

		$is_admin = 0;
		if(is_staff_or_higher()){
			$is_admin = 1;
		}
				
		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "sorting" => get_array_value($order_by_option,"sorting",""), "optional"=>$optional, "result" => $result_list, "my_review"=>$my_review, "is_admin"=>$is_admin);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.');
			echo json_encode($result_obj);
			return "";
		}
	}

	function ajax_save_review($sid=""){
		@define("thisAction","ajax_save_review");

		$parent_aid = $this->input->get_post('parent_aid');
		$product_type_aid = $this->input->get_post('product_type_aid');

		$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid, true);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		if(!is_login()){
			$result_obj = array("status" => "error","msg" => "Permission denied.");
			echo json_encode($result_obj);
			return"";
		}

		if(is_blank($parent_aid)){
			$result_obj = array("status" => "error","msg" => "No product selected.");
			echo json_encode($result_obj);
			return"";
		}

		$point = trim($this->input->get_post('point'));
		// echo "point = ".$point;
		if(is_blank($point)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify point.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_number_no_zero($point)){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : point must be integer.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if($point > CONST_REVIEW_MAX_POINT){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data value : point must not over '.CONST_REVIEW_MAX_POINT.'.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$description = trim(clearHTMLtoSave($this->input->get_post('description')));
		// echo "description = ".$description;
		// if(is_blank($description)){
		// 	$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify comment.', "result" => '');
		// 	echo json_encode($result_obj);
		// 	return "";
		// }

		$data = array();
		$data["user_aid"] = getSessionUserAid();
		$data["product_type_aid"] = $product_type_aid;
		$data["parent_aid"] = $parent_aid;
		$data["status"] = '1';
		$data["point"] = $point;
		$data["description"] = $description;
		$data["channel"] = "web";

		$this->load->model($this->main_model,"main");
		$this->main->insert_or_update($data);
		$this->load->model($this->review_history_model,"review_history");
		$this->review_history->insert_record($data);

		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_name, $model_name);
		$result = $this->{$model_name}->update_review_point($parent_aid);
		$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
		echo json_encode($result_obj);
		return "";
	}

	function ajax_set_value($sid=""){
		@define("thisAction",'ajax_set_value');

		if(!is_staff_or_higher()){
			$msg = set_message_error('Error occurred. Permission denied.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		$parent_aid = $this->input->get_post('parent_aid');
		$product_type_aid = $this->input->get_post('product_type_aid');
		$user_aid = $this->input->get_post('user_aid');
		$status = $this->input->get_post('status');

		$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid, true);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		if(is_blank($parent_aid) || is_blank($user_aid)){
			$result_obj = array("status" => "error","msg" => "Data is null.");
			echo json_encode($result_obj);
			return"";
		}

		$data_where = array();
		$data_where["user_aid"] = $user_aid;
		$data_where["product_type_aid"] = $product_type_aid;
		$data_where["parent_aid"] = $parent_aid;
		$data = array();
		$data["status"] = $status;

		$this->load->model($this->main_model,"main");
		$this->main->set_where($data_where);
		$rs = $this->main->update_record($data);
		// echo "<br>sql : ".$this->db->last_query();

		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_name, $model_name);
		$result = $this->{$model_name}->update_review_point($parent_aid);
		$result_obj = array("status" => 'success',"msg" => '', "result" => '1' );
		echo json_encode($result_obj);
		return "";
	}
			
}

?>