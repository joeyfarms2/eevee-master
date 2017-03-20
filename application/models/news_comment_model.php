<?php
class News_comment_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("news_comment");
			$this->tbl_news_comment_user_activity = "news_comment_user_activity";
			$this->tbl_news = 'news';
			$this->tbl_user_name = 'user';
	}
	
	function set_join_for_desc($obj="") {
		$this->db->select($this->tbl_name.'.*, user.username , user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid, activity.user_aid as activity_user_aid, news.title AS news_title');
		$this->db->join($this->tbl_news.' AS news', $this->tbl_name.'.parent_news_aid = news.aid', "left");
		$this->db->join($this->tbl_user_name.' AS user', $this->tbl_name.'.created_by = user.aid', "left");
		
		if (isset($obj['user_aid']) && !is_blank($obj['user_aid'])) {
			$this->db->join($this->tbl_news_comment_user_activity.' AS activity', $this->tbl_name.'.aid = activity.news_comment_aid AND activity.user_aid = "'.$obj['user_aid'].'" and activity.status_wow = "1"', "left");
		}
		else {
			$this->db->join($this->tbl_news_comment_user_activity.' AS activity', $this->tbl_name.'.aid = activity.news_comment_aid AND activity.user_aid = "'.getUserLoginAid($this->user_login_info).'" and activity.status_wow = "1"', "left");
		}
	}

	function fetch_data_with_desc($query)
	{
		$result = array("num_rows" => 0, "results" => "", "html" => "");
		if($query->num_rows() > 0){
			$result["num_rows"] = $query->num_rows();
			$result["html"] = "<ul>";
			$result["results"] = array();
			foreach($query->result_array() as $row){
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}

				$news_title = get_array_value($row,"news_title","");
				$row["news_title_short"] = getShortString($news_title,"70");

				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				$result["html"] .= '<li>'.$row["full_name_th"].'</li>';

				$row["user_info"] = get_user_info($row);

				$user_aid = get_array_value($row,"created_by","");
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
				
				$comment_msg = get_array_value($row,"comment","");
				$row["short_comment"] = getShortString($comment_msg,"300");
				
				$row["created_date_txt"] = get_pretty_date(get_array_value($row, 'created_date', ''));	
				
				$row["is_wowed_by_me"] = false;
				if ($row['activity_user_aid'] > 0) {
					$row["is_wowed_by_me"] = true;
				}
				
				$result["results"][] = $row;
			}
		}
		return $result;
	}
	
	function get_who_comment($news_aid="") {
		$this->set_where(array('parent_news_aid' => $news_aid, 'status' => '1'));
		$this->set_where_not_equal(array('created_by' => getUserLoginAid($this->user_login_info)));
		$this->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$this->set_group_by($this->tbl_name.'.created_by');
		return $this->load_records(true);
	}

	function get_total_wow($aid){
		$this->set_where(array('aid' => $aid));
		$result = $this->load_record(false);
		return get_array_value($result, 'total_wow' , '0');
	}

	function update_total_wow($aid){
		if ($aid > 0) {
			$_sql = 'UPDATE '.$this->get_table_name().' 
				SET total_wow =  (SELECT count(user_aid) FROM '.$this->tbl_news_comment_user_activity.' WHERE news_comment_aid = "'.$aid.'" and status_wow = "1")
				WHERE aid = "'.$aid.'"';
			return $this->db->query($_sql);
		}
		return 0;
	}
	
	function has_commented($news_aid){
		$this->set_where(array(
				'parent_news_aid' => $news_aid,
				'created_by' => getUserLoginAid($this->user_login_info),
				'status' => '1'
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['parent_news_aid'])) {
			return true;
		}
		else {
			return false;
		}
	}
	
}

/* End of file news_comment_model.php */
/* Location: ./system/application/model/news_comment_model.php */