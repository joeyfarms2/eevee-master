<?php
class User_setting_profile_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_setting_profile");
	}
	
}

?>