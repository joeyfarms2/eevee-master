<?php
class Holiday_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("holiday");
	}
	
	function load_holidays() {
		$this->db->select("*");
		$this->db->from($this->tbl_name." AS m");
		$this->db->where("user_owner_aid", getSessionOwnerAid());
		$this->db->order_by("m.from_date ASC");
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
		if (isset($obj["aid"])) {
			$this->db->select("*");
			$this->db->from($this->tbl_name." AS m");
			$this->db->where("aid", $obj["aid"]);
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