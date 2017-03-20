<?php
class Log_access_product_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("log_access_product");
		$this->tbl_view_all_products_with_detail_name = "view_all_products_with_detail";
		$this->tbl_user_name = "user";
	}

	function set_join_for_desc($obj=""){
		$this->db->select('log_access_product.*, all_products.title, user.username , user.first_name_th, user.last_name_th, user.email');
		$this->db->join($this->tbl_view_all_products_with_detail_name.' AS all_products', 'log_access_product.product_type_aid = all_products.product_type_aid AND log_access_product.parent_aid = all_products.aid', "left");
		$this->db->join($this->tbl_user_name.' AS user', 'log_access_product.user_aid = user.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}

				$full_name_th = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				if(is_blank($full_name_th)){
					$full_name_th = '-';
				}
				$row["full_name_th"] = $full_name_th;
				$row["updated_date_txt"] = get_datetime_pattern("d/m/Y H:i",get_array_value($row,"updated_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}

}

/* End of file log_access_product_model.php */
/* Location: ./system/application/model/log_access_product_model.php */