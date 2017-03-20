<?php
class Log_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("log");
		
		$this->tbl_user_role_name = "user_role";
		$this->tbl_user_name = "user";
	}
	
	function set_join_for_desc($obj="")
	{
		$this->db->select('log.*, user.username , user.first_name_th, user.last_name_th, user_role.name as user_role_name, user.email');
		$this->db->join($this->tbl_user_name.' AS user', 'log.created_by = user.aid', "left");
		$this->db->join($this->tbl_user_role_name.' AS user_role', 'user.user_role_aid = user_role.aid', "left");
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$row["user_role_name"] = get_array_value($row,"user_role_name","");
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y H:i",get_array_value($row,"created_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}

	
}

?>