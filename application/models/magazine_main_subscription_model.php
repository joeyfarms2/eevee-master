<?php
class Magazine_main_subscription_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("magazine_main_subscription");
		
		$this->tbl_magazine_main = "magazine_main";
	}
		
	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, magazine_main.title as magazine_main_title');
		$this->db->join($this->tbl_magazine_main.' AS magazine_main', $this->get_table_name().'.magazine_main_aid = magazine_main.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();

			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;				
				$result[] = $row;
			}
		}
		return $result;
	}	
		
}

/* End of file magazine_main_subscription_model.php */
/* Location: ./system/application/model/magazine_main_subscription_model.php */