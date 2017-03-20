<?php
class Order_receipt_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("order_receipt");
	}
	
}

/* End of file order_receipt_model.php */
/* Location: ./system/application/model/order_receipt_model.php */