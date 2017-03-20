<?php
class Product_main_field_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("product_main_field");
	}
	
	function set_join_for_desc($obj=""){

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
				
				$input_type = get_array_value($row,"input_type","textbox");
				if($input_type != "textbox" && $input_type != "textarea"){
					$input_type = "textbox";
				}
				
				$product_topic_main_cid = get_array_value($row,"product_topic_main_cid","");
				if(is_number_no_zero($product_topic_main_cid)){
					$input_type = "textbox_topic";
				}
				$row["input_type"] = $input_type;

				$tag = get_array_value($row,"tag","");
				$row["tag"] = $tag;
				
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file product_main_field_model.php */
/* Location: ./system/application/model/product_main_field_model.php */