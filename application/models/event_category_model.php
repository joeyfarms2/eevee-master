<?php
class Event_category_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("event_category");
		
		$this->tbl_event_main_name = "event_main";
	}
	
	function load_event_categories(){
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(false,"aid","");
	}
	
	function load_event_categories_array(){
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(false,"aid","name");
	}

	function load_category_by_event_main($event_main_aid){
		$tmp = array();
		$tmp["event_main_aid"] = $event_main_aid;
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("weight ASC , name ASC");
		$result = $this->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
	}
	
	function set_join_for_desc($obj="")
	{
		$this->db->select($this->get_table_name().'.*, main.name as event_main_name');
		$this->db->join($this->tbl_event_main_name.' AS main', $this->get_table_name().'.event_main_aid = main.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y H:i",get_array_value($row,"created_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

?>