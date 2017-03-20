<?php

require_once(APPPATH."models/product_init_model.php");

class View_all_product_fields_with_detail_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_all_product_fields_with_detail");

		$this->category_model = "Product_category_model";
	}

	function set_join_for_desc($obj=""){
		$this->db->group_by("aid, product_type_aid");
	}

	function load_count_records_by_search($fullload=false,$obj=""){
		if($fullload) $this->set_join_for_desc($obj);
		$query = $this->db->get($this->tbl_name);
		if($query){
			return $query->num_rows();
		}else{
			return 0;
		}
    }

	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			$CI2 =& get_instance();
			$CI2->load->model($this->category_model,"category");
			$category_result = $CI2->category->load_records_array(false,"aid","");
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				
				$row = $this->manage_result($row,$category_result);
				$result[] = $row;
			}
		}
		return $result;
	}
	
		
}

/* End of file view_all_product_fields_with_detail_model.php */
/* Location: ./system/application/model/view_all_product_fields_with_detail_model.php */