<?php
class Search_history_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("search_history");
	}

	function load_popular($total_record=10){
		$this->db->select("*, count(*) as total_record");
		$this->set_order_by("*total_record DESC");
		$this->db->group_by("word");
		$this->set_limit(0,$total_record);
		return $this->load_records(false);
	}
		
}

/* End of file search_history_model.php */
/* Location: ./system/application/model/search_history_model.php */