<?php
class Etc_field_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("etc_field");
		
		$this->tbl_product_type_field = "product_type_field";
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
		$this->db->select($this->get_table_name().'.*, product_type_field.cid as product_type_field_cid, product_type_field.product_topic_main_cid, product_type_field.name as product_type_field_name, product_type_field.fixed_field, product_type_field.is_required, product_type_field.input_type');
		$this->db->join($this->tbl_product_type_field.' AS product_type_field', $this->get_table_name().'.product_type_field_aid = product_type_field.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();

			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				
				$input_type = get_array_value($row,"input_type","textbox");
				if($input_type != "textbox" && $input_type != "textarea"){
					$input_type = "textbox";
				}
				
				$product_topic_main_cid = get_array_value($row,"product_topic_main_cid","");
				if(is_number_no_zero($product_topic_main_cid)){
					$input_type = "textbox_topic";
				}
				$row["input_type"] = $input_type;

				$product_type_field_name = get_array_value($row,"product_type_field_name","");
				$name = get_array_value($row,"name","");
				if(is_blank($product_type_field_name)){
					$row["product_type_field_name"] = $name;
				}
				
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

/* End of file etc_field_model.php */
/* Location: ./system/application/model/etc_field_model.php */