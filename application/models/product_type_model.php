<?php
class Product_type_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("product_type");
		$this->model_name = "Product_type_model";
	}
	
	function load_product_types(){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"aid","");
	}
	
	function load_active_product_types(){
		$this->set_where(array("status"=>"1"));
		$this->set_order_by("weight asc");
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
	
	function load_product_type_name_by_aid($aid){
		$this->db->flush_cache();
		$this->load->model($this->model_name,"product_type");
		$this->db->select("name");
		$this->product_type->set_where(array("aid"=>$aid));
		$result  = $this->product_type->load_record(false);
		// echo "<br>sql : ".$this->db->last_query();
		return get_array_value($result,"name","");
	}

}

/* End of file product_type_model.php */
/* Location: ./system/application/model/product_type_model.php */