<?php
class Reserve_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("reserve");

	}

	function delete_expired_code(){
		$_sql = "
			UPDATE reserve SET status = '0', confirm_code = NULL, expiration_date = NULL WHERE expiration_date IS NOT NULL AND expiration_date != '0000-00-00' AND expiration_date <= '".date('Y-m-d H:i:s')."'	
		";
		// echo "$_sql";
		return $this->db->query($_sql);

	}
	
}

/* End of file reserve_model.php */
/* Location: ./system/application/model/reserve_model.php */