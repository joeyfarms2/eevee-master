<?php
class Log_paypal_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("log_paypal");
	}
	
}

/* End of file log_paypal_model.php */
/* Location: ./system/application/model/log_paypal_model.php */