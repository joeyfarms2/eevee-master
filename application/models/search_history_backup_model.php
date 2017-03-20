<?php
class Search_history_backup_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("search_history_backup");

			$this->tbl_search_history_name = "search_history";
	}

	function backup_search_history(){
		$expired_search_history_month = $this->config->item('expired_search_history_month');
		if(!is_number_no_zero($expired_search_history_month)){
			$expiration_date = date("Y-m-d 23:59:59",strtotime("-".$expired_search_history_month." month"));
			$_sql = "
				INSERT INTO ".$this->get_table_name()." (word, cond, search_in, ip, created_date, created_by) SELECT word, cond, search_in, ip, created_date, created_by FROM ".$this->tbl_search_history_name." WHERE created_date <= '".$expiration_date."'
			";
			// echo "sql = $_sql <BR />";
			$resut = $this->db->query($_sql);

			$_sql = "
				DELETE FROM ".$this->tbl_search_history_name." WHERE created_date <= '".$expiration_date."'
			";
			// echo "sql = $_sql <BR />";
			$result = $this->db->query($_sql);
		}
	}
		
}

/* End of file search_history__backup_model.php */
/* Location: ./system/application/model/search_history__backup_model.php */