<?php
class Product_category_ref_user_section_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("product_category_ref_user_section");

		$this->tbl_product_category = "product_category";
	}
	
	function load_all_ref_user_section_id_by_category_aid($product_category_aid=0)
	{
		$this->set_where(array("product_category_aid"=>$product_category_aid, "user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"","user_section_aid");
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, product_category.parent_aid, product_category.weight, product_category.status, product_category.product_main_aid');
		$this->db->join($this->tbl_product_category.' AS product_category', $this->get_table_name().'.product_category_aid = product_category.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();

			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				
				$result[] = $row;
			}
		}
		return $result;
	}
	

}

/* End of file product_category_ref_user_section_model.php */
/* Location: ./system/application/model/product_category_ref_user_section_model.php */