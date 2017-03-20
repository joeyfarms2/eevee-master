<?php

require_once(APPPATH."models/product_init_model.php");

class Vdo_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("vdo");
		$this->vdo_field_model = "Vdo_field_model";
		$this->category_model = "Product_category_model";
		$this->view_all_product_copies_with_detail_model = "View_all_product_copies_with_detail_model";
		
		$this->tbl_publisher_name = "publisher";
		$this->tbl_product_main_name = "product_main";
		$this->tbl_product_type_name = "product_type";
		$this->tbl_vdo_copy_name = "vdo_copy";
		$this->tbl_product_category_name = "product_category";
		
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select('vdo.*, publisher.name as publisher_name, publisher.status as publisher_status, product_main.name as product_main_name, product_main.url as product_main_url, product_main.icon as icon, product_type.cid as product_type_cid');
		$this->db->join($this->tbl_publisher_name.' AS publisher', 'vdo.publisher_aid = publisher.aid', "left");
		$this->db->join($this->tbl_product_main_name.' AS product_main', 'vdo.product_main_aid = product_main.aid', "left");
		$this->db->join($this->tbl_product_type_name.' AS product_type', 'vdo.product_type_aid = product_type.aid', "left");
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
	
	function update_parent($aid){
		if(is_blank($aid)){
			return false;
		}
		$_sql ="
			UPDATE ".$this->get_table_name()." SET total_copy = ( 
				SELECT count( aid ) 
				FROM ".$this->tbl_vdo_copy_name." 
				WHERE parent_aid = '".$aid."' 
			)  WHERE aid = '".$aid."' 
		";
		$this->db->query($_sql);

		$CI =& get_instance();
		$CI->load->model($this->vdo_copy_model,"vdo_copy");
		$tmp = array();
		$tmp["product_type_aid"] = '3';
		$tmp["parent_aid"] = $aid;
		$tmp["status"] = "1";
		$CI->vdo_copy->set_where($tmp);
		$copy_result = $CI->vdo_copy->load_records(true);
		$has_ebook = 0;
		$has_license = 0;
		$best_price = 0;
		if(is_var_array($copy_result)){
			foreach ($copy_result as $item) {
				$is_license = get_array_value($item,"is_license","0");
				$type = get_array_value($item,"type","0");
				$possession = get_array_value($item,"possession","0");
				if($type == "1"){
					$has_ebook = 1;
				}
				if($is_license == "1"){
					$has_license = 1;
				}

				$actual_price = get_array_value($item,"actual_price","0");
				// echo "actual_price = $actual_price , ";
				if($best_price <= 0){
					 // echo "best price = $best_price , ";
					$best_price = $actual_price;
				}else{
					 // echo "best price = $best_price , ";
					$best_price = ($actual_price > 0 && $best_price > 0 && $actual_price < $best_price) ? $actual_price : $best_price;
				}
				// echo "best_price = $best_price<br>";
			}
		}

		$_sql ="
			UPDATE ".$this->get_table_name()." SET best_price = '".$best_price."' , has_ebook = '".$has_ebook."' , has_license = '".$has_license."' WHERE aid = '".$aid."' 
		";
		// echo "sql = $_sql";
		$result = $this->db->query($_sql);
		return $result;
	}

	function increase_total_read_web($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_read = total_read+1 WHERE aid = "'.$aid.'"';
		$result = $this->db->query($_sql);
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_read_web = total_read_web+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	function reset_total_read_web($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_read_web = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	
	function increase_total_read_device($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_read = total_read+1 WHERE aid = "'.$aid.'"';
		$result = $this->db->query($_sql);
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_read_device = total_read_device+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	function reset_total_read_device($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_read_device = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	
	function increase_total_view_web($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view = total_view+1 WHERE aid = "'.$aid.'"';
		$result = $this->db->query($_sql);
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view_web = total_view_web+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	function reset_total_view_web($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view_web = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	
	function increase_total_view_device($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view = total_view+1 WHERE aid = "'.$aid.'"';
		$result = $this->db->query($_sql);
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view_device = total_view_device+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	function reset_total_view_device($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view_device = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	
	function increase_total_download_web($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_download = total_download+1 WHERE aid = "'.$aid.'"';
		$result = $this->db->query($_sql);
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_download_web = total_download_web+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
		// $_sql = 'UPDATE '.$this->get_table_name().' SET total_download_web = (SELECT count(*) FROM shelf WHERE shelf.issue_aid = "'.$aid.'") WHERE aid = "'.$aid.'"';
		// $result = $this->db->query($_sql);
		
		// $this->db->flush_cache();
		// $CI =& get_instance();
		// $CI->load->model($this->shelf_model,"shelf");
		// $this->shelf->set_where(array("issue_aid"=>$aid));
		// $result = $CI->shelf->count_records();
		// return $result;
	}
	function reset_total_download_web($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_download_web = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	function increase_total_download_device($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_download = total_download+1 WHERE aid = "'.$aid.'"';
		$result = $this->db->query($_sql);
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_download_device = total_download_device+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
		// $_sql = 'UPDATE '.$this->get_table_name().' SET total_download_web = (SELECT count(*) FROM shelf WHERE shelf.issue_aid = "'.$aid.'") WHERE aid = "'.$aid.'"';
		// $result = $this->db->query($_sql);
		
		// $this->db->flush_cache();
		// $CI =& get_instance();
		// $CI->load->model($this->shelf_model,"shelf");
		// $this->shelf->set_where(array("issue_aid"=>$aid));
		// $result = $CI->shelf->count_records();
		// return $result;
	}
	function reset_total_download_device($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_download_device = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	function reset_review_point($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET review_point = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	
	function increase_total_rental($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_rental = total_rental+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	function reset_total_rental($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_rental = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	
	function load_home_new(){
		$tmp = array();
		$tmp["is_new"] = "1";
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("weight ASC, publish_date DESC");
		$this->set_limit(0,10);
		$result = $this->load_records(true);
		return $result;
	}
	
	function load_home_recommended(){
		$tmp = array();
		$tmp["is_recommended"] = "1";
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("weight ASC, publish_date DESC");
		$this->set_limit(0,10);
		$result = $this->load_records(true);
		return $result;
	}
	
	
	
}

/* End of file vdo_model.php */
/* Location: ./system/application/model/vdo_model.php */