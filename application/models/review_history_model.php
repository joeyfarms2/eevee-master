<?php
class Review_history_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("review_history");

		$this->tbl_user_name = 'user';
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select('review_history.*, user.username , user.first_name_th, user.last_name_th, user.email');
		$this->db->join($this->tbl_user_name.' AS user', 'review_history.user_aid = user.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows();

				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));

				$row["user_info"] = get_user_info($row);
				$row["avatar_mode"] = "tiny";
				$row["avatar_tiny"] = getUserLoginAvatar($row);
				$row["avatar_mode"] = "mini";
				$row["avatar_mini"] = getUserLoginAvatar($row);
				$row["avatar_mode"] = "thumb";
				$row["avatar_thumb"] = getUserLoginAvatar($row);
				
				$row["description"] = getHTMLtoShow(get_array_value($row,"description",""));

				$created_date_txt = get_datetime_pattern("dmyhis_EN_SHORT", get_array_value($row,"created_date",""), "");
				$row["created_date_txt"] = $created_date_txt;
				
				$created_date_txt = get_datetime_pattern(CONST_DEFAULT_DATE_PATTERN_FOR_REVIEW, get_array_value($row,"created_date",""), "");
				$row["created_date_show"] = $created_date_txt;
				
				
				$result[] = $row;
			}
		}
		return $result;
	}
	
	
	
}

/* End of file review_history_model.php */
/* Location: ./system/application/model/review_history_model.php */