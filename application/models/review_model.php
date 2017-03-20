<?php
class Review_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("review");

		$this->tbl_user_name = 'user';
	}
	
	function set_join_for_desc($obj=""){
		$this->db->select('review.*, user.username , user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender');
		$this->db->join($this->tbl_user_name.' AS user', 'review.user_aid = user.aid', "left");
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

				$aid = get_array_value($row,"user_aid","");
				$avatar_path = get_array_value($row,"avatar_path","");
				$avatar_path = str_replace("./", "", $avatar_path);
				$avatar_type = get_array_value($row,"avatar_type",".jpg");
				$gender = get_array_value($row,"gender","m");

				$avatar_mode = "tiny";
				$avatar_full = $avatar_path.'/'.$aid.'-'.$avatar_mode.$avatar_type;
				if(!is_file($avatar_full)){
					$avatar_full = THEME_FRONT_PATH.'images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg';
				}else{
					$avatar_full = site_url($avatar_full);
				}
				$row["avatar_mode"] = $avatar_mode;
				$row["avatar_tiny"] = get_user_avatar($row);
				$row["avatar_tiny_path"] = $avatar_full;

				$avatar_mode = "mini";
				$avatar_full = $avatar_path.'/'.$aid.'-'.$avatar_mode.$avatar_type;
				if(!is_file($avatar_full)){
					$avatar_full = THEME_FRONT_PATH.'images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg';
				}else{
					$avatar_full = site_url($avatar_full);
				}
				$row["avatar_mode"] = $avatar_mode;
				$row["avatar_mini"] = get_user_avatar($row);
				$row["avatar_mini_path"] = $avatar_full;

				$avatar_mode = "thumb";
				$avatar_full = $avatar_path.'/'.$aid.'-'.$avatar_mode.$avatar_type;
				if(!is_file($avatar_full)){
					$avatar_full = THEME_FRONT_PATH.'images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg';
				}else{
					$avatar_full = site_url($avatar_full);
				}
				$row["avatar_mode"] = $avatar_mode;
				$row["avatar_thumb"] = get_user_avatar($row);
				$row["avatar_thumb_path"] = $avatar_full;

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

/* End of file review_model.php */
/* Location: ./system/application/model/review_model.php */