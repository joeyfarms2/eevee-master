<?php
require_once(APPPATH."models/product_init_model.php");

class View_all_product_copies_with_detail_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_all_product_copies_with_detail");
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
				$row = $this->manage_copy_result($row);
				$result[] = $row;
			}
		}
		return $result;
	}

}

/* End of file view_all_product_copies_with_detail_model.php */
/* Location: ./system/application/model/view_all_product_copies_with_detail_model.php */