<?php

require_once(APPPATH."models/product_init_model.php");

class Etc_copy_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("etc_copy");

		$this->tbl_etc_name = "etc";
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
		$this->db->select($this->get_table_name().'.*, parent.title as parent_title, parent.author as parent_author, parent.category as parent_category, parent.status as parent_status');
		$this->db->join($this->tbl_etc_name.' AS parent', $this->get_table_name().'.parent_aid = parent.aid', "left");
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
				
				$shelf_status = get_array_value($row,"shelf_status","");
				switch($shelf_status){
					case "1" : $row["shelf_status_name"] = "On shelf"; break;
					case "2" : $row["shelf_status_name"] = "Borrowed"; break;
					case "3" : $row["shelf_status_name"] = "Damage"; break;
					case "4" : $row["shelf_status_name"] = "Lost"; break;
					default : $row["shelf_status_name"] = "N/A"; break;
				}

				$type = get_array_value($row,"type","");
				switch($type){
					case "1" : $row["type_name"] = "Digital"; break;
					case "2" : $row["type_name"] = "Paper"; break;
					default : $row["type_name"] = "N/A"; break;
				}

				$possession = get_array_value($row,"possession","");
				switch($possession){
					case "1" : $row["possession_name"] = "Buy out"; break;
					case "2" : $row["possession_name"] = "Rental"; break;
					default : $row["possession_name"] = "N/A"; break;
				}

				$is_license = get_array_value($row,"is_license","");
				switch($is_license){
					case "1" : $row["is_license_name"] = "Yes"; break;
					default : $row["is_license_name"] = "No"; break;
				}
				
				$result[] = $row;
			}
			$this->db->flush_cache();
		}
		return $result;
	}
}

/* End of file etc_copy_model.php */
/* Location: ./system/application/model/etc_copy_model.php */