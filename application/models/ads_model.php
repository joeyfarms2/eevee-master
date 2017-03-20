<?php
class Ads_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("ads");
	}

	function load_ads_by_arr_aid($arr_aid=""){
		if(!is_var_array($arr_aid)){
			return "";
		}
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_and_or_like_by_field("category",$arr_aid,"both");
		$this->set_order_by("weight asc, created_date asc");
		return $this->load_records(true);
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

				$upload_path = get_array_value($row,"upload_path","");
				$cover_image_file_type = get_array_value($row,"cover_image_file_type","");

				$image_path = $upload_path.''.get_array_value($row,"cid","").'-actual'.$cover_image_file_type;
				$image = get_image($image_path,"actual", "off");
				$row["cover_image_actual_path"] = $image_path;
				$row["cover_image_actual"] = $image;
			
				$image_path = $upload_path.''.get_array_value($row,"cid","").'-thumb'.$cover_image_file_type;
				$image = get_image($image_path,"thumb", "off");
				$row["cover_image_thumb_path"] = $image_path;
				$row["cover_image_thumb"] = $image;
			
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y H:i",get_array_value($row,"created_date",""),"");

				$ref_link = get_array_value($row,"ref_link","");
				if(!is_blank($ref_link) && strpos($ref_link, '://') === false ){
					$ref_link = 'http://'.$ref_link;
				}
				$row["ref_link"] = $ref_link;
				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file ads_model.php */
/* Location: ./system/application/model/ads_model.php */