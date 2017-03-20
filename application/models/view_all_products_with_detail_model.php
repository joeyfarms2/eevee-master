<?php
class View_all_products_with_detail_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_all_products_with_detail");
		$this->category_model = "Product_category_model";
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			$CI2 =& get_instance();
			$CI2->load->model($this->category_model,"category");
			$category_result = $CI2->category->load_records_array(false,"aid","");

			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
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

				$category = get_array_value($row,"category","");
				$category_list = "";
				$txt = "";
				if(!is_blank($category)){
					$arr = preg_split('/,/', $category, -1, PREG_SPLIT_NO_EMPTY);
					if(is_var_array($arr)){	
						foreach($arr as $item){
							if(!is_blank(trim($item))){
								$category_obj = get_array_value($category_result,$item,"");
								$category_name = get_array_value($category_obj,"name","");
								$category_url = get_array_value($category_obj,"url","");
								$product_main_aid= get_array_value($row,"product_main_aid","");
								$product_main_name = get_array_value($row,"product_main_url","");
								$txt .= ''.$category_name.', ';
								$category_list[] = $category_name;
							}
						}
						$txt = substr(trim($txt), 0, -1);
					}
				}
				$row["category_list"] = $category_list;
				$row["category_link"] = $txt;

				$result[] = $row;
			}
		}
		return $result;
	}
	
}

/* End of file view_all_products_with_detail_model.php */
/* Location: ./system/application/model/view_all_products_with_detail_model.php */