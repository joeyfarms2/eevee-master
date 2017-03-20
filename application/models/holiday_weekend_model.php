<?php
class Holiday_weekend_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("holiday_weekend");
	}
	
	function load_holidays() {
		$this->db->flush_cache();
		$this->db->select("*");
		$this->db->from($this->tbl_name." AS m");
		$this->db->where("user_owner_aid", getSessionOwnerAid());
		$query = $this->db->get();
		$return = array();
		
		if($query->num_rows() > 0){
			foreach($query->result_array() as $k=>$row){
				$return = $row;
			}
		}
		return $return;
	}
	
	
}

/* End of file user_model.php */
/* Location: ./system/application/model/user_model.php */