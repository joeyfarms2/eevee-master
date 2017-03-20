<?php
class Product_main_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("product_main");

		$this->tbl_product_type_name = "product_type";
	}
	
	function load_product_mains(){
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(true,"aid","");
	}
	
	function load_product_mains_array(){
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(false,"aid","name");
	}

	function set_join_for_desc($obj="")
	{
		$this->db->select($this->get_table_name().'.*, type.cid as product_type_cid, type.name as product_type_name, type.icon as product_type_icon');
		$this->db->join($this->tbl_product_type_name.' AS type', $this->get_table_name().'.product_type_aid = type.aid', "left");
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