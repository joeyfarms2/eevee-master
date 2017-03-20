<?php
class Redeem_history_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("redeem_history");

		$this->tbl_redeem_main_name = "redeem_main";
	}

	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, redeem_main.status as redeem_main_status, redeem_main.title as redeem_main_title, redeem_main.start_date as redeem_main_start_date, redeem_main.expired_date as redeem_main_expired_date, redeem_main.type as redeem_main_type, redeem_main.value as redeem_main_value, redeem_main.promotion_set_aid as redeem_main_promotion_set_aid, redeem_main.amount as redeem_main_amount, redeem_main.limit_per_code as redeem_main_limit_per_code, redeem_main.limit_per_user as redeem_main_limit_per_user, redeem_main.code_length as redeem_main_code_length');
		$this->db->join($this->tbl_redeem_main_name.' AS redeem_main', $this->get_table_name().'.redeem_main_aid = redeem_main.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}

				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file redeem_history_model.php */
/* Location: ./system/application/model/redeem_history_model.php */