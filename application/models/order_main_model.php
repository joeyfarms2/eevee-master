<?php
class Order_main_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("order_main");
		
		$this->tbl_package_point_name = "package_point";
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select('order_main.*, package_point.name as package_name, package_point.point as package_point, package_point.price as package_price');
		$this->db->join($this->tbl_package_point_name.' AS package_point', 'order_main.package_aid = package_point.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$row["actual_unit_show"] = get_price_format(get_array_value($row,"actual_unit","0"));				
				$row["actual_grand_total_show"] = get_price_format(get_array_value($row,"actual_grand_total","0"));				
				$row["actual_grand_total_show_with_currency"] = trim(get_price_format(get_array_value($row,"actual_grand_total","0")). " " . get_array_value($row,"currency",""));				
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");	
				$row["created_date_time_txt"] = get_datetime_pattern("d/m/Y H:i:s",get_array_value($row,"created_date",""),"");	
				
				$type = get_array_value($row,"type","");
				switch($type){
					case "1" : $row["type_txt"] = "Buy Point"; break;
					case "2" : $row["type_txt"] = "Buy Book"; break;
					default : $row["type_txt"] = "N/A"; break;
				}
				
				$status = get_array_value($row,"status","");
				switch($status){
					case "1" : $row["status_txt"] = "New coming"; break;
					case "2" : $row["status_txt"] = "In Process"; break;
					case "3" : $row["status_txt"] = "Approved"; break;
					case "4" : $row["status_txt"] = "Rejected"; break;
					case "5" : $row["status_txt"] = "Cancelled"; break;
					default : $row["status_txt"] = "N/A"; break;
				}
				
				$transport_status = get_array_value($row,"transport_status","");
				switch($transport_status){
					case "0" : $row["transport_status_txt"] = "-"; break;
					case "1" : $row["transport_status_txt"] = "Waiting shipping"; break;
					case "2" : $row["transport_status_txt"] = "Shipped"; break;
					case "3" : $row["transport_status_txt"] = "Cancelled"; break;
					default : $row["transport_status_txt"] = "-"; break;
				}
				
				$payment_type = get_array_value($row,"payment_type","");
				switch($payment_type){
					case "paysbuy" : $row["payment_type_txt"] = "Pay by Paysbuy"; break;
					case "paypal" : $row["payment_type_txt"] = "Pay by Paypal"; break;
					case "point" : $row["payment_type_txt"] = "Pay by point"; break;
					case "ios" : $row["payment_type_txt"] = "Pay by IOS"; break;
					case "android" : $row["payment_type_txt"] = "Pay by Andriod"; break;
					default : $row["payment_type_txt"] = "ไม่พบข้อมูล"; break;
				}

				$confirm_status = get_array_value($row,"confirm_status","0");
				$confirm_status_txt = '';
				if($confirm_status == '1'){
					$confirm_status_txt = '<i class="fa fa-check"></i>';
				}
				$row["confirm_status_txt"] = $confirm_status_txt;

				$result[] = $row;
			}
		}
		return $result;
	}
	
	
}

/* End of file order_main_model.php */
/* Location: ./system/application/model/order_main_model.php */