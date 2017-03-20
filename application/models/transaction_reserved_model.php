<?php
class Transaction_reserved_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("transaction_reserved");
		$this->tbl_book = "book";
		$this->tbl_book_issue = "book_issue";
		$this->tbl_member = "member";
	}
	
	function load_transactions($obj="") {
		$this->db->select("
			trans.*
		", false);
		$this->db->from($this->tbl_name." AS trans");
		
		if(isset($obj["is_borrowed"])) {
			$this->db->where("trans.is_borrowed", $obj["is_borrowed"]);
		}
		
		if(isset($obj["member_cid"]) && !empty($obj["member_cid"])) {
			$this->db->where("trans.member_cid", $obj["member_cid"]);
		}
		
		if(isset($obj["order_by"])) {
			$this->db->order_by($obj["order_by"]);
		}
		else {
			$this->db->order_by("trans.aid", "DESC");
		}
		$query = $this->db->get();
		$return = array();
		
		if($query->num_rows() > 0){
			foreach($query->result_array() as $k=>$row){
				$return[] = $row;
			}
		}
		return $return;
	}
	
	function load_transaction_info($obj="") {
		$return = array();
		if (isset($obj["aid"])) {
			$this->db->select("*");
			$this->db->from($this->tbl_name);
			$this->db->where("aid", $obj["aid"]);
			$query = $this->db->get();
						
			if($query->num_rows() > 0){
				foreach($query->result_array() as $k=>$row){
					$return = $row;
				}
			}
		}
		return $return;
	}
	
	function delete_transaction($obj="") {
		if (isset($obj["aid"])) {
			$this->db->where("aid", $obj["aid"]);
			return $this->db->delete($this->tbl_name);
		}
		return false;
	}
	
}

/* End of file user_model.php */
/* Location: ./system/application/model/user_model.php */