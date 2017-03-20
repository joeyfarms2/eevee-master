<?php
class User_domain_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_domain");
	}
	
	function load_master_user_domain(){
		$tmp = array();
		$tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("weight ASC");
		$result = $this->load_records_array(false,"aid","");
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
	}
	
	function load_all_user_domain(){
		$tmp = array();
		$tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("weight ASC");
		$result = $this->load_records_array(false,"aid","name");
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
	}
	
}

?>