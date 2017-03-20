<?php
class Master_Member_Type_model extends Initmodel {
	

	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("master_member_type");
	}
	
	function load_member_types() {
		// $this->db->flush_cache();
		$this->db->select("*");
		$this->db->from($this->tbl_name." AS m");
		$this->db->order_by("m.aid ASC");
		$query = $this->db->get();
		$return = array();
		
		if($query->num_rows() > 0){
			foreach($query->result_array() as $k=>$row){
				$return[] = $row;
			}
		}
		return $return;
	}
	
	function load_info($obj="") {
		$return = "";
		if (isset($obj["aid"]) || isset($obj["cid"])) {
			$this->db->select("*");
			$this->db->from($this->tbl_name." AS m");
			if (isset($obj['aid'])) {
				$this->db->where("aid", $obj["aid"]);
			}
			else if (isset($obj['cid'])) {
				$this->db->where("cid", $obj["cid"]);
			}
			$query = $this->db->get();
			$return = array();
			
			if($query->num_rows() > 0){
				foreach($query->result_array() as $k=>$row){
					$return = $row;
				}
			}
		}
		return $return;
	}
	
}

/* End of file user_model.php */
/* Location: ./system/application/model/user_model.php */