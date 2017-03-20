<?php
class Order_detail_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("order_detail");
	}
	
	function set_join_for_desc($obj=""){
		// $this->db->select('product.*, product_main.name as product_main_name, product_type.name as product_type_name, product_type.status as product_type_status');
		// $this->db->join($this->tbl_product_main_name.' AS product_main', 'product.product_main_aid = product_main.aid', "left");
		// $this->db->join($this->tbl_product_type_name.' AS product_type', 'product.product_type_aid = product_type.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");

				$need_transport = get_array_value($row,"need_transport","0");
				$need_transport_txt = '';
				if($need_transport == '1'){
					$need_transport_txt = '<i class="fa fa-check"></i>';
				}
				$row["need_transport_txt"] = $need_transport_txt;
				
				
				
				// if(is_blank(get_array_value($row,"product_unit_change",""))){
					// $row["product_unit_change"] = get_array_value($row,"product_unit","");
				// }
				// $row["product_unit_change_show"] = get_price_format(get_array_value($row,"product_unit_change",""));
				
				// if(is_blank(get_array_value($row,"product_price_total_change",""))){
					// $row["product_price_total_change"] = get_array_value($row,"product_price_total","");
				// }
				// $row["product_price_total_change_show"] = get_price_format(get_array_value($row,"product_price_total_change",""));
				
				$result[] = $row;
			}
		}
		return $result;
	}

}

/* End of file order_detail_model.php */
/* Location: ./system/application/model/order_detail_model.php */