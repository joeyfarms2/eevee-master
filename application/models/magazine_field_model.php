<?php

require_once(APPPATH."models/product_init_model.php");

class Magazine_field_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("magazine_field");
		
		$this->tbl_product_main_field = "product_main_field";
	}
		
	function load_field_by_parent_aid($parent_aid){
		$tmp = array();
		$tmp["user_owner_aid"] = getUserOwnerAid($this->user_owner_info);
		$tmp["parent_aid"] = $parent_aid;
		$this->set_where($tmp);
		$result = $this->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
		
	}
		
	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, product_main_field.cid as product_main_field_cid, product_main_field.product_topic_main_cid, product_main_field.name as product_main_field_name, product_main_field.fixed_field, product_main_field.is_required, product_main_field.input_type');
		$this->db->join($this->tbl_product_main_field.' AS product_main_field', $this->get_table_name().'.product_main_field_aid = product_main_field.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();

			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$row = $this->manage_field_result($row);
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function get_sequence_from_parent_aid($parent_aid=""){
		$this->db->select('MAX(sequence)+1 as sequence');
		$this->set_where(array("parent_aid"=>$parent_aid));
		$result = $this->load_record(false);
		if(is_var_array($result)){
			return get_array_value($result,"sequence","1");
		}else{
			return 1;
		}
		
	}
	
		
}

/* End of file magazine_field_model.php */
/* Location: ./system/application/model/magazine_field_model.php */