<?php
class Test_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("test");
	}
}

/* End of file test.php */
/* Location: ./system/application/model/test.php */