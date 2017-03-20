<?php
class User_section_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_section");
	}
	
	function load_master_user_section(){
		$tmp = array();
		$tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("weight ASC");
		$result = $this->load_records_array(false,"aid","");
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
	}
	
	function load_all_user_section(){
		$tmp = array();
		$tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("weight ASC");
		$result = $this->load_records_array(false,"aid","name");
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
	}

	function clear_default(){
		$_sql = "UPDATE ".$this->get_table_name()." SET is_default = ''";
		return $this->db->query($_sql);
	}
	
}

?>