<?php
class Master_payment_type_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("master_payment_type");
	}
	
	function load_master_payment_type(){
		$this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(false,"aid","");
	}
	
}

?>