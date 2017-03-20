<?php
class Event_gallery_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("event_gallery");
		
		$this->tbl_event_name = "event";
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, event.upload_path');
		$this->db->join($this->tbl_event_name.' AS event', $this->get_table_name().'.event_aid = event.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				
				$upload_path = get_array_value($row,"upload_path","");
				$image_path = $upload_path.'galleries/original/'.get_array_value($row,"file_name","");
				$row["image_original"] = $image_path;
				
				$image_path = $upload_path.'galleries/thumb/'.get_array_value($row,"file_name","");
				$row["image_thumb"] = $image_path;
								
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file event_galley_model.php */
/* Location: ./system/application/model/event_galley_model.php */