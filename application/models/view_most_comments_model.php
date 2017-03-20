<?php
class View_most_comments_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_most_comments");
	}

	function load_top_commenters($offset=0, $count=3) {
		$this->set_limit($offset, $count);
		$result = $this->load_records(true);
		return $result;
	}
	
	function fetch_data_with_desc($query)
	{
		$result = array('num_rows' => 0, 'results' => '');
		if($query->num_rows() > 0){
			$result = array();
			$result["num_rows"] = $query->num_rows();
			$result["results"] = array();
			foreach($query->result_array() as $row){
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));

				$row["user_info"] = get_user_info($row);

				$user_aid = get_array_value($row,"aid","");
				$avatar_path = get_array_value($row,"avatar_path","");
				$avatar_path = str_replace("./", "", $avatar_path);
				$avatar_type = get_array_value($row,"avatar_type",".jpg");
				$gender = get_array_value($row,"gender","m");

				$avatar_mode = "tiny";
				$avatar_full = $avatar_path.'/'.$user_aid.'-'.$avatar_mode.$avatar_type;
				if(!file_exists($avatar_full)){
					$avatar_full = THEME_FRONT_PATH.'images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg';
				}else{
					$avatar_full = site_url($avatar_full);
				}
				$row["user_aid"] = $user_aid;
				$row["avatar_mode"] = $avatar_mode;
				$row["avatar_tiny_path"] = $avatar_full;
				$row["avatar_tiny"] = get_user_avatar($row);

				$avatar_mode = "mini";
				$avatar_full = $avatar_path.'/'.$user_aid.'-'.$avatar_mode.$avatar_type;
				if(!file_exists($avatar_full)){
					$avatar_full = THEME_FRONT_PATH.'images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg';
				}else{
					$avatar_full = site_url($avatar_full);
				}
				$row["user_aid"] = $user_aid;
				$row["avatar_mode"] = $avatar_mode;
				$row["avatar_mini_path"] = $avatar_full;
				$row["avatar_mini"] = get_user_avatar($row);
				
				$result["results"][] = $row;
			}
		}
		return $result;
	}
	
		
}

/* End of file view_most_comments_model.php */
/* Location: ./system/application/model/view_most_comments_model.php */