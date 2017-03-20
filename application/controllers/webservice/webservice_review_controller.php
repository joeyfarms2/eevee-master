<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_review_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();

		$this->review_model = "Review_model";
		$this->review_history_model = "Review_history_model";

		if(CONST_HAS_REVIEW != '1'){
			$result_obj = array("status" => 'error',"msg" => 'Review system not available.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function get_review_list(){
		$this->check_device();

		$product_result = $this->check_parent_product();
		$product_type_aid = get_array_value($product_result,"product_type_aid","0");
		$product_type_cid = get_array_value($product_result,"product_type_cid","0");
		$parent_aid = get_array_value($product_result,"aid","0");
		$review_point = get_array_value($product_result,"review_point","0");

		$login_history = "";
		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
			$login_history = $this->check_token();
		}
		$user_aid = get_array_value($login_history,"user_aid","0");

		$this->load->model($this->review_model,'review');
		$this->db->start_cache();
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["parent_aid"] = $parent_aid;
		$tmp["status"] = '1';
		$this->review->set_where($tmp);
		$total_record = $this->review->count_records(true);

		//Order by
		$order_by_master = array("review_date_asc","review_date_desc","review_point_asc","review_point_desc");
		$order_by = trim($this->input->get_post('order_by'));
		if(!is_blank($order_by)){
			if(!in_array($order_by,$order_by_master)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data : order_by can not be \''.$order_by.'\'.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			switch($order_by){
				case "review_date_asc" : $this->review->set_order_by("created_date asc"); break;
				case "review_date_desc" : $this->review->set_order_by("created_date desc"); break;
				case "review_point_asc" : $this->review->set_order_by("point asc, created_date desc"); break;
				case "review_point_desc" : $this->review->set_order_by("point desc, created_date desc"); break;
				default : $this->review->set_order_by("created_date desc");
			}
		}
		
		//Limit
		$limit_start = trim($this->input->get_post('limit_start'));
		$no_record = trim($this->input->get_post('no_record'));
		// echo "limit_start = ".$limit_start;
		if(!is_blank($limit_start)){
			if(!is_number($limit_start)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : limit_start must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(is_blank($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify no_record.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
		}else{
			$limit_start = 0;
		}
		
		// echo "no_record = ".$no_record;
		if(!is_blank($no_record)){
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		
		// echo "limit_start = $limit_start , no_record = $no_record";
		if(is_number_no_zero($no_record)){
			$this->review->set_limit($limit_start, $no_record);
		}

		$result_list = $this->review->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				// print_r($item);echo "<HR>";

				$obj = array();
				$obj["review_point"] = get_array_value($item,"point","");
				$obj["review_description"] = get_array_value($item,"description","");
				$obj["review_name"] = get_array_value($item,"user_info","");
				$obj["review_avatar"] = get_array_value($item,"avatar_mini_path","");
				if($user_aid > 0 && $user_aid == get_array_value($item,"user_aid","")){
					$obj["is_my_review"] = "1";
				}else{
					$obj["is_my_review"] = "0";
				}
				$result[] = $obj;
			}

			$result_obj = array("status" => 'success',"msg" => '', "total_record" => $total_record, "review_point_summary" => $review_point, "result" => $result);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "total_record" => '0', "review_point_summary" => '0', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}

	function add_review(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		$user_aid = get_array_value($login_history,"user_aid","");
		// echo "user_aid = ".$user_aid;

		$product_result = $this->check_parent_product();
		$product_type_aid = get_array_value($product_result,"product_type_aid","0");
		$product_type_cid = get_array_value($product_result,"product_type_cid","0");
		$parent_aid = get_array_value($product_result,"aid","0");
		$review_point = get_array_value($product_result,"review_point","0");

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
		if(is_blank($description)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify description.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$data = array();
		$data["user_aid"] = $user_aid;
		$data["product_type_aid"] = $product_type_aid;
		$data["parent_aid"] = $parent_aid;
		$data["status"] = '1';
		$data["point"] = $point;
		$data["description"] = $description;
		$data["channel"] = $device;

		$this->load->model($this->review_model,"review");
		$this->review->insert_or_update($data);

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


	
}

?>