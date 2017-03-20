<?php
class Transport_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("transport");
	}

	function load_transports(){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"aid","");
	}
	
}

/* End of file transport_model.php */
/* Location: ./system/application/model/transport_model.php */