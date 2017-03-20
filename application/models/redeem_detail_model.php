<?php
class Redeem_detail_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("redeem_detail");

		$this->tbl_redeem_main_name = "redeem_main";
		$this->tbl_redeem_history_name = "redeem_history";
	}

	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, count(history.aid) as used_total, main.status as redeem_main_status, main.title as redeem_main_title, main.start_date as redeem_main_start_date, main.expired_date as redeem_main_expired_date, main.type as redeem_main_type, main.value as redeem_main_value, main.promotion_set_aid as redeem_main_promotion_set_aid, main.amount as redeem_main_amount, main.limit_per_code as redeem_main_limit_per_code, main.limit_per_user as redeem_main_limit_per_user');
		$this->db->join($this->tbl_redeem_main_name.' AS main', $this->get_table_name().'.redeem_main_aid = main.aid', "left");
		$this->db->join($this->tbl_redeem_history_name.' AS history', $this->get_table_name().'.cid = history.redeem_detail_cid', "left");
		$this->db->group_by($this->get_table_name().'.cid');
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;

				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file redeem_detail_model.php */
/* Location: ./system/application/model/redeem_detail_model.php */