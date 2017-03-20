<?php
class User_role_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_role");
	}
	
	function load_master_user_role(){
		$my_role_aid = getSessionUserRoleAid();
		if(is_general_admin() || is_owner_admin()){
			$this->set_where($this->get_table_name().".aid >= '".$my_role_aid."'");
		}else if(!is_root_admin()){
			$this->set_where($this->get_table_name().".aid > '".$my_role_aid."'");
		}
		$tmp = array();
		$tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("aid ASC");
		$result = $this->load_records_array(false,"aid","");
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
	}
	
}

?>