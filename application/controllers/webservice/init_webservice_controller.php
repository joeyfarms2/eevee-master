<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Init_webservice_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = "web_service";
		$this->user_login_history_model = "User_login_history_model";
		$this->reserve_model = "Reserve_model";
		
	}
	
	function check_device(){
		$device_master = array("ios","android","andriod");
		$device = $this->input->get_post('device'); // ios , android
		if(is_blank($device) || !in_array($device,$device_master)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device.', "result" => '');
			echo json_encode($result_obj);
			exit(0);
		}
		
		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			exit(0);
		}
	}
	
	function check_product_type(){
		$product_type_master = array("book","magazine","vdo");
		$type = $this->input->get_post('type'); // ios , andriod
		// echo "type = ".$type;
		if(is_blank($type) || !in_array($type,$product_type_master)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify type.', "result" => '');
			echo json_encode($result_obj);
			exit(0);
		}
		$product_type_obj = array();
		$product_type_obj["product_type_cid"] = $type;
		switch ($type) {
			case 'book':
				$product_type_obj["product_type_aid"] = "1";
				return $product_type_obj;
				break;
			case 'magazine':
				$product_type_obj["product_type_aid"] = "2";
				return $product_type_obj;
				break;
			case 'vdo':
				$product_type_obj["product_type_aid"] = "3";
				return $product_type_obj;
				break;
			default:
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify type.', "result" => '');
				echo json_encode($result_obj);
				exit(0);
				break;
		}
	}
	
	function check_token($required=true){
		$login_history = "";
		
		$device_id = trim($this->input->get_post('device_id'));
		if(is_blank($device_id)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify device_id.', "result" => '');
			echo json_encode($result_obj);
			exit(0);
		}
		
		$token = trim($this->input->get_post('token'));
		if(is_blank($token) && $required){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify token.', "result" => '');
			echo json_encode($result_obj);
			exit(0);
		}
		
		if(!is_blank($token)){
			$this->load->model($this->user_login_history_model,"user_login_history");
			$this->user_login_history->set_where(array("token"=>$token));
			$login_history = $this->user_login_history->load_record(true);
			if(!is_var_array($login_history)){
				$result_obj = array("status" => 'error',"msg" => 'Session time out : Do not find this token. Please try again later.', "result" => '0');
				echo json_encode($result_obj);
				exit(0);
			}
		}
		/*		
		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->set_where(array("token"=>$token, "status"=>'1'));
		$login_history = $this->user_login_history->load_record(false);
		if(!is_var_array($login_history)){
			$result_obj = array("status" => 'error',"msg" => 'Session time out : Do not find this token. Please try again later.', "result" => '0');
			echo json_encode($result_obj);
			exit(0);
		}
		*/
		return $login_history;
	}
	
	function check_parent_product(){
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");

		$parent_aid = trim($this->input->get_post('id'));
		// echo "parent_aid = ".$parent_aid;
		if(is_blank($parent_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify id.', "result" => '');
			echo json_encode($result_obj);
			exit(0);
		}
		if(!is_number_no_zero($parent_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : id must be integer.', "result" => '');
			echo json_encode($result_obj);
			exit(0);
		}

		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_name, $model_name);
		$where = array();
		$where["product_type_aid"] = $product_type_aid;
		$where["aid"] = $parent_aid;
		$this->{$model_name}->set_where($where);
		$result = $this->{$model_name}->load_record(true);

		if(!is_var_array($result)){
			$result_obj = array("status" => 'error',"msg" => 'Data not found : Do not find this product.', "result" => '0');
			echo json_encode($result_obj);
			exit(0);
		}
		return $result;
	}

	function get_queue($result_obj, $product_type_aid, $copy_aid, $user_aid){
		$this->load->model($this->reserve_model,"reserve");
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["copy_aid"] = $copy_aid;
		$tmp["status"] = '1';
		$this->reserve->set_where($tmp);
		$this->reserve->set_order_by("created_date asc");
		$queue_result = $this->reserve->load_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		// print_r($queue_result);
		// echo "<HR>";
		$queue = 0;
		$my_queue = 0;

		if(is_var_array($queue_result)){
			$queue = count($queue_result);
			if($user_aid > 0){
				$i = 1;
				foreach ($queue_result as $q) {
					$q_user_aid = get_array_value($q,"user_aid","0");
					// echo "q_user_aid = $q_user_aid, user_aid = $user_aid<BR>";
					if($q_user_aid == $user_aid){
						$my_queue = $i;
					}
					$i++;
				}
			}
		}

		$result_obj["total_queue"] = $queue;
		$result_obj["my_queue"] = $my_queue;
		return $result_obj;
	}
		
}

?>