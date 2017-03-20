<?php
class Publisher_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("publisher");
		$this->model_name = "Publisher_model";
	}
	
	function load_publishers(){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array(false,"aid","");
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
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");
				$logo_image = get_image(get_array_value($row,"logo",""));
				if(!is_blank($logo_image)){
					$row["logo_image"] = '<img src="'.$logo_image.'" />';
				}else{
					// $row["logo_image"] = get_array_value($row,"name","-");
					$row["logo_image"] = '';
				}
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function load_publisher_name_by_aid($aid){
		$this->db->flush_cache();
		$this->load->model($this->model_name,"publisher");
		$this->db->select("name");
		$this->publisher->set_where(array("aid"=>$aid));
		$result  = $this->publisher->load_record(false);
		// echo "<br>sql : ".$this->db->last_query();
		return get_array_value($result,"name","");
	}

}

/* End of file publisher_model.php */
/* Location: ./system/application/model/publisher_model.php */