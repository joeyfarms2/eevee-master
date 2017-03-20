<?php
class User_login_social_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user_login_social");
	}
	
}

?>