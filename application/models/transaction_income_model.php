<?php
class Transaction_income_model extends Initmodel {

	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("transaction_income");
	}
	
}

/* End of file transaction_income_model.php */
/* Location: ./system/application/model/transaction_income_model.php */