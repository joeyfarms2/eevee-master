<?php
class View_all_product_tags_with_detail_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_all_product_tags_with_detail");
	}

	function set_join_for_desc($obj=""){
		$this->db->group_by("aid, product_type_aid");
	}

	function load_count_records_by_search($fullload=false,$obj=""){
		if($fullload) $this->set_join_for_desc($obj);
		$query = $this->db->get($this->tbl_name);
		if($query){
			return $query->num_rows();
		}else{
			return 0;
		}
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

				switch($row["product_type_aid"]){
					case "1" : $row["product_type_cid"] = "book"; break;
					case "2" : $row["product_type_cid"] = "magazine"; break;
					case "3" : $row["product_type_cid"] = "vdo"; break;
					case "4" : $row["product_type_cid"] = "etc"; break;
					default : $row["product_type_cid"] = "N/A";	 break;
				}

				$title = get_array_value($row,"title","N/A");
				$row["title_short"] = getShortString($title, CONST_TITLE_SHORT_CHAR);

				$upload_path = get_array_value($row,"upload_path","");
				$cover_image_file_type = get_array_value($row,"cover_image_file_type","");

				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-thumb'.$cover_image_file_type;
				$image = get_image($image_path,"thumb");
				$row["cover_image_thumb_path"] = $image_path;
				$row["cover_image_thumb"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-cover'.$cover_image_file_type;
				$image = get_image($image_path, "detail");
				$row["cover_image_detail_path"] = $image_path;
				$row["cover_image_detail"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-related'.$cover_image_file_type;
				$image = get_image($image_path,"related");
				$row["cover_image_related_path"] = $image_path;
				$row["cover_image_related"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-small'.$cover_image_file_type;
				$image = get_image($image_path,"small");
				$row["cover_image_small_path"] = $image_path;
				$row["cover_image_small"] = $image;
				
				$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-ori'.$cover_image_file_type;
				$image = get_image($image_path,"","off");
				$row["cover_image_ipad_path"] = $image_path;
				$row["cover_image_ipad"] = $image;
							
				$created_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"created_date",""),"");
				$row["created_date_txt"] = $created_date_txt;
				$updated_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"updated_date",""),"");
				$row["updated_date_txt"] = $updated_date_txt;
				
				$publish_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"publish_date",""),"");
				$row["publish_date_txt"] = $publish_date_txt;
				
				$publish_date_short_txt = get_datetime_pattern("dmy_EN_SHORT",get_array_value($row,"publish_date",""),"");
				$row["publish_date_short_txt"] = $publish_date_short_txt;

				$result[] = $row;
			}
		}
		return $result;
	}
	
		
}

/* End of file view_all_product_tags_with_detail_model.php */
/* Location: ./system/application/model/view_all_product_tags_with_detail_model.php */