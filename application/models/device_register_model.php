<?php
class Device_register_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("device_register");		
	}
	
	function set_join_for_desc($obj=""){
		return "";
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
					default : $row["device_name"] = "N/A"; break;
				
				}
								
				$result[] = $row;
			}
		}
		return $result;
	}
	
	
}

?>