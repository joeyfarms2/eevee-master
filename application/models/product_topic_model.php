<?php
class Product_topic_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("product_topic");
		$this->tbl_product_main_name = "product_main";
	}
	
	function load_all_topic_name()
	{
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"aid","name");
	}
	
	function load_all_topic()
	{
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"aid","");
	}
	
	function load_topic_by_product_main($product_main_aid){
		$tmp = array();
		$tmp["product_main_aid"] = $product_main_aid;
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->set_where($tmp);
		$result = $this->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
		
	}
	
	function set_join_for_desc($obj="")
	{

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
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y H:i",get_array_value($row,"created_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file product_main_model.php */
/* Location: ./system/application/model/product_main_model.php */