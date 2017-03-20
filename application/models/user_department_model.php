<?php
class User_department_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_department");
	}

	function load_department(){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"aid","");
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}
		
}

/* End of file user_department_model.php */
/* Location: ./system/application/model/user_department_model.php */