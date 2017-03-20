<?php
class News_upload_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("news_upload");	
		
		$this->news_category_model = "News_category_model";
		
		$this->tbl_news_main_name = "news_main";
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select($this->get_table_name().'.*, news_main.name as news_main_name');
		$this->db->join($this->tbl_news_main_name.' AS news_main', $this->get_table_name().'.news_main_aid = news_main.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			$CI2 =& get_instance();
			$CI2->load->model($this->news_category_model,"category");
			$category_result = $CI2->category->load_records_array(false,"aid","");		
		
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				
				$news_title = get_array_value($row,"title","");
				$row["shot_title"] = getShortString($news_title,"35");
				$row["shot_title_focus"] = getShortString($news_title,"70");
				
				$description = get_array_value($row,"description","");
				$row["shot_description"] = getShortString(strip_tags($description),"300");
				$row["shot_description_highlight"] = getShortString(strip_tags($description),"150");
				
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");
								
				$publish_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"publish_date",""),"");
				if(is_blank($publish_date_txt)){
					$publish_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"created_date",""),"");
				}
				$row["publish_date_txt"] = $publish_date_txt;				
				
				$category = get_array_value($row,"category","");
				$txt = "";
				if(!is_blank($category)){
					$arr = preg_split('/,/', $category, -1, PREG_SPLIT_NO_EMPTY);
					if(is_var_array($arr)){	
						foreach($arr as $item){
							if(!is_blank(trim($item))){
								$category_name = $category_result[$item]["name"];
								$product_main_aid= get_array_value($row,"product_main_aid","");
								$product_main_name = get_array_value($row,"product_main_url","");
								$txt .= ''.$category_result[$item]["name"].', ';
							}
						}
						$txt = substr(trim($txt), 0, -1);
					}
				}
				$row["category_all"] = $txt;
				
				$result[] = $row;
			}
		}
		return $result;
	}
	
	
}

/* End of file news_upload_model.php */
/* Location: ./system/application/model/news_upload_model.php */