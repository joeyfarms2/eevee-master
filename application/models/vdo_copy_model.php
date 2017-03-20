<?php

require_once(APPPATH."models/product_init_model.php");

class Vdo_copy_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("vdo_copy");

		$this->tbl_vdo_name = "vdo";
		$this->tbl_product_type_name = "product_type";
	}
	
	function get_product_copy_by_parent_aid($parent_aid=0){
		$tmp = array();
		if(!exception_about_status()) $tmp["status"] = "1";
		$tmp["parent_aid"] = $parent_aid;
		$this->set_where($tmp);
		$this->set_order_by("type asc, possession asc, barcode asc");
		$result = $this->load_records(true);
		return $result;
	}
		
	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, product_type.cid as product_type_cid, parent.cid as parent_cid, parent.title as parent_title, parent.author as parent_author, parent.category as parent_category, parent.status as parent_status, parent.upload_path as parent_upload_path, parent.cover_image_file_type as parent_cover_image_file_type');
		$this->db->join($this->tbl_vdo_name.' AS parent', $this->get_table_name().'.parent_aid = parent.aid', "left");
		$this->db->join($this->tbl_product_type_name.' AS product_type', $this->get_table_name().'.product_type_aid = product_type.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo $this->db->last_query()."<br/><br/>";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				
				$row = $this->manage_copy_result($row);

				$result[] = $row;
			}
			$this->db->flush_cache();
		}
		return $result;
	}
	
		
		
}

/* End of file vdo_copy_model.php */
/* Location: ./system/application/model/vdo_copy_model.php */