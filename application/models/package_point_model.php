<?php
class Package_point_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("package_point");
	}
	
	function load_package_points(){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(true,"aid","");
	}
	
	function load_package_points_array(){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(true,"aid","name");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				$row["package_point_image"] = get_image(get_array_value($row,"image",""),"package-point","");
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file package_point_model.php */
/* Location: ./system/application/model/package_point_model.php */