<?php
class Log_product_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("log_product");
		
		$this->tbl_user_role_name = "user_role";
		$this->tbl_user_name = "user";
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select('log_product.*, user.username , user.first_name_th, user.last_name_th, user_role.name as user_role_name, user.email, product_type.cid as product_type_cid, product_type.name as product_type_name');
		$this->db->join($this->tbl_user_name.' AS user', 'log_product.created_by = user.aid', "left");
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
				
				$action = get_array_value($row,"action","");
				switch($action){
					case "I" : $row["action_name"] = "Insert"; break;
					case "U" : $row["action_name"] = "Update"; break;
					case "D" : $row["action_name"] = "Delete"; break;
					default : $row["action_name"] = "N/A"; break;
				
				}
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				
				$created_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"created_date",""),"");
				$row["created_date_txt"] = $created_date_txt;
				
				$result[] = $row;
			}
		}
		return $result;
	}
		
}

/* End of file bblio_history_model.php */
/* Location: ./system/application/model/bblio_history_model.php */