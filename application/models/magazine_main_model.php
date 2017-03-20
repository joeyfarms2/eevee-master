<?php

require_once(APPPATH."models/product_init_model.php");

class Magazine_main_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("magazine_main");
		$this->magazine_model = "Magazine_model";
		$this->magazine_field_model = "Magazine_field_model";
		$this->category_model = "Product_category_model";
		$this->view_all_product_copies_with_detail_model = "View_all_product_copies_with_detail_model";
		
		$this->tbl_publisher_name = "publisher";
		$this->tbl_product_main_name = "product_main";
		$this->tbl_product_type_name = "product_type";
		$this->tbl_magazine_name = "magazine";
		$this->tbl_magazine_copy_name = "magazine_copy";
		$this->tbl_product_category_name = "product_category";
	}

	function update_parent($aid){
		if(is_blank($aid)){
			return false;
		}
		$_sql ="
			UPDATE ".$this->get_table_name()." SET total_issue = ( 
				SELECT count( aid ) 
				FROM ".$this->tbl_magazine_name." 
				WHERE magazine_main_aid = '".$aid."' 
			)  WHERE aid = '".$aid."' 
		";
		$this->db->query($_sql);

		$result = $this->db->query($_sql);
		return $result;
	}

	function get_magazine_main_by_product_main_aid($product_main_aid=""){
		if(!is_number_no_zero($product_main_aid)){
			return "";
		}
		$tmp = array();
		$tmp["user_owner_aid"] = getUserOwnerAid($this->user_owner_info);
		$tmp["product_main_aid"] = $product_main_aid;
		$this->set_where($tmp);
		$this->set_order_by("weight ASC , title ASC");
		$result = $this->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select('magazine_main.*, publisher.name as publisher_name, publisher.status as publisher_status, product_main.name as product_main_name, product_main.url as product_main_url, product_main.icon as icon, product_type.cid as product_type_cid');
		$this->db->join($this->tbl_publisher_name.' AS publisher', 'magazine_main.publisher_aid = publisher.aid', "left");
		$this->db->join($this->tbl_product_main_name.' AS product_main', 'magazine_main.product_main_aid = product_main.aid', "left");
		$this->db->join($this->tbl_product_type_name.' AS product_type', 'magazine_main.product_type_aid = product_type.aid', "left");
	}
	
	function fetch_data_with_desc($query){
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
				
				// $row = $this->manage_result($row,$category_result);
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file magazine_main_model.php */
/* Location: ./system/application/model/magazine_main_model.php */