<?php
class User_owner_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_owner");		
	}
		
	function set_join_for_desc($obj="")
	{
		
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows();
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				
				$user_owner_info_show = "";
				$alias = trim(get_array_value($row,"alias",""));
				$name = trim(get_array_value($row,"name",""));
				
				if(!is_blank($name)){
					$user_owner_info_show = $name;
				}else{
					$user_owner_info_show = $alias;
				}
				$row["user_owner_info_show"] = $user_owner_info_show;
				
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y H:i",get_array_value($row,"created_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}

	
}

?>