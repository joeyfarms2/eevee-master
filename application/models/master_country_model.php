<?php
class Master_country_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("master_country");
	}
	
	function load_master_country(){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"aid","");
	}
	
}

?>