<?php
class User_login_history_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_login_history");		
		
		$this->tbl_user_role_name = "user_role";
		$this->tbl_user_name = "user";
	}
	
	function logout_by_device_id($device_id=""){
		if(is_number_no_zero($device_id)){
			$_sql = 'UPDATE '.$this->get_table_name().' SET status = 0 WHERE device_id = "'.$device_id.'"';
			return $this->db->query($_sql);
		}
	}
	
	function get_user_by_token($token=""){
		$result = "";
		if(!is_blank($token)){
			$data = array();
			$data["token"] = $token;
			$data["status"] = "1";
			$this->set_where($data);
			$user_login_history_result = $this->load_record(false);
			if(is_var_array($user_login_history_result)){
				$user_aid = get_array_value($user_login_history_result,"user_aid","");
				// echo "user_aid = $user_aid";
			}
			
			
		}
		return $result;
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select('user_login_history.*, user.username , user.first_name_th, user.last_name_th, user_role.aid as user_role_aid, user_role.name as user_role_name, user.email');
		$this->db->join($this->tbl_user_name.' AS user', 'user_login_history.user_aid = user.aid', "left");
		$this->db->join($this->tbl_user_role_name.' AS user_role', 'user.user_role_aid = user_role.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				
				$device = get_array_value($row,"device","");
				switch($device){
					case "web" : $row["device_name"] = "Website"; break;
					case "ios" : $row["device_name"] = "IOS"; break;
					case "android" : $row["device_name"] = "Android"; break;
					case "andriod" : $row["device_name"] = "Android"; break;
					default : $row["device_name"] = "N/A"; break;
				
				}
				
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				$row["user_info_show"] = get_user_info($row);
				
				$created_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"created_date",""),"");
				$row["created_date_txt"] = $created_date_txt;
				
				$updated_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"updated_date",""),"");
				
				$status = get_array_value($row,"status","0");
				if($status == "0"){
					$row["updated_date_txt"] = $updated_date_txt;
				}else{
					$row["updated_date_txt"] = "-";
				}
				
				$result[] = $row;
			}
		}
		return $result;
	}
	
	
}

?>