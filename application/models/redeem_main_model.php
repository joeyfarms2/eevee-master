<?php
class Redeem_main_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("redeem_main");
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
				$start_date_txt = get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($row,"start_date",""), "");
				$row["start_date_show"] = $start_date_txt;
				$expired_date_txt = get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN, get_array_value($row,"expired_date",""), "");
				$row["expired_date_show"] = $expired_date_txt;
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file redeem_main_model.php */
/* Location: ./system/application/model/redeem_main_model.php */