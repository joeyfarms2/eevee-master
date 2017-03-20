<?php
class News_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("news");		

		$this->tbl_news_main_name = "news_main";
		$this->tbl_news_user_activity = "news_user_activity";
		$this->tbl_news_comment = "news_comment";
		$this->tbl_user_name = 'user';
	}
	
	function load_home($category_aid_arr="", $news_main_aid="", $offset=0, $count=0,$keyword="") {
		$tmp = array();
		$tmp["is_home"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		if(is_var_array($category_aid_arr)){
			$this->set_and_or_like_by_field("category",$category_aid_arr,"both");
		}
		if(!is_blank($keyword)){
			$this->set_and_or_like(array("title"=>$keyword));
		}
		
		$this->set_order_by("is_highlight DESC ,weight ASC, publish_date DESC, created_date DESC");
		if ($count > 0) {
			$this->set_limit($offset, $count);
		}
		$result = $this->load_records(true);
		//echo "<br>sql : ".$this->db->last_query()."<br>";
		return $result;
	}


	function load_home_big($category_aid_arr="", $news_main_aid="", $offset=0, $count=0,$keyword="") {
		$tmp = array();
		$tmp["is_home"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		if(is_var_array($category_aid_arr)){
			$this->set_and_or_like_by_field("category",$category_aid_arr,"both");
		}
		if(!is_blank($keyword)){
			$this->set_and_or_like(array("title"=>$keyword));
		}
		
		$this->set_order_by("weight ASC, publish_date DESC, created_date DESC");
		if ($count > 0) {
			$this->set_limit($offset, $count);
		}
		$result = $this->load_records(true);
		//echo "<br>sql : ".$this->db->last_query()."<br>";
		return $result;
	}

	function load_home_small($category_aid_arr="", $news_main_aid="", $offset=0, $count=0,$keyword="") {
		$tmp = array();
		$tmp["is_home"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		if(is_var_array($category_aid_arr)){
			$this->set_and_or_like_by_field("category",$category_aid_arr,"both");
		}
		if(!is_blank($keyword)){
			$this->set_and_or_like(array("title"=>$keyword));
		}
		
		$this->set_order_by("weight ASC, publish_date DESC, created_date DESC");
		if ($count > 0) {
			$this->set_limit($offset, $count);
		}
		$result = $this->load_records(true);
		//echo "<br>sql : ".$this->db->last_query()."<br>";
		return $result;
	}
	
	function load_recommended($news_main_aid="", $offset=0, $count=3) {
		$tmp = array();
		$tmp["is_recommended"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		$this->set_order_by("weight ASC, publish_date DESC, created_date DESC");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true);
		return $result;
	}
		
	function load_popular($news_main_aid="", $offset=0, $count=3) {
		$tmp = array();
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		$this->set_order_by("total_wow DESC, weight ASC, publish_date DESC, created_date DESC");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true);
		return $result;
	}
		
	function load_talk_of_the_town($news_main_aid="", $offset=0, $count=3) {
		$tmp = array();
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		$this->set_order_by("total_comment DESC, weight ASC, publish_date DESC, created_date DESC");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true);
		return $result;
	}
		
	function load_example_news($news_main_aid="", $news_category_aid="", $offset=0, $count=5) {
		$tmp = array();
		$tmp["is_home"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		if(!is_blank($news_category_aid)){
			$tmp = array();
			$tmp["category"] = ",".$news_category_aid.",";
			$this->set_like($tmp);
		}
		$this->set_order_by("weight ASC, publish_date DESC, created_date DESC");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true);
		return $result;
	}
		
	function load_highlight($news_main_aid="", $offset=0, $count=5) {
		$tmp = array();
		$tmp["is_highlight"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($news_main_aid) && $news_main_aid > 0) $tmp["news_main_aid"] = $news_main_aid;
		$this->set_where($tmp);
		$this->set_order_by("weight ASC, publish_date DESC, created_date DESC");
		// $this->set_order_by("weight");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true);
		return $result;

	}
		
	function set_join_for_desc($obj="") {
		$this->db->select('news.*, news_main.name as news_main_name, user.username , user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid');
		$this->db->join($this->tbl_news_main_name.' AS news_main', 'news.news_main_aid = news_main.aid', "left");
		$this->db->join($this->tbl_user_name.' AS user', 'news.posted_by = user.aid', "left");
	}

	function fetch_data_with_desc($query)
	{
		$result = array("num_rows" => 0, "results" => "");
		if($query->num_rows() > 0){
			$result = array();
			$result["num_rows"] = $query->num_rows();
			$result["results"] = array();
			foreach($query->result_array() as $row){


				//get first image from description and store in dummy_cover_image
				if (!isset($row['cover_image_file_type']) || !empty($row['cover_image_file_type'])) {
					preg_match("/<img[\w\W]+?\/?>/i", $row['description'], $matches);
					// echo '<pre>'; 
					// print_r($matches);
					// echo '</pre>';
					// exit;
					if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {
						$row['dummy_cover_image'] = $matches[0];
					}
				}

				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}

				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));

				$row["user_info"] = get_user_info($row);

				$user_aid = get_array_value($row,"posted_by","");
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
				
				$news_title = get_array_value($row,"title","");
				$row["short_title"] = getShortString($news_title,"40");
				$row["short_title_focus"] = getShortString($news_title,"70");
				
				$description = get_array_value($row,"description","");
				$row["short_description"] = getShortString(strip_tags($description),"300");
				$row["short_description_highlight"] = getShortString(strip_tags($description),"150");
				$row["very_short_description"] = getShortString(strip_tags($description),"70");
				
				
				
				$row["cover_image_thumb"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-thumb'.get_array_value($row,"cover_image_file_type","");
				$row["cover_image_thumb_sq"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-thumb-sq'.get_array_value($row,"cover_image_file_type","");
				$row["cover_image_big_thumb"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-big-thumb'.get_array_value($row,"cover_image_file_type","") ;
				$row["cover_image_actual"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-actual'.get_array_value($row,"cover_image_file_type","") ;
				
				$publish_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"publish_date",""),"");
				if(is_blank($publish_date_txt)){
					$publish_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"created_date",""),"");
				}
				$row["publish_date_txt"] = $publish_date_txt;	
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");

				// $row["is_wowed_by_me"] = false;
				// if ($row['activity_user_aid'] > 0) {
				// 	$row["is_wowed_by_me"] = true;
				// }
			
				$result["results"][] = $row;
			}
		}
		return $result;
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
    
	function increase_total_view($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view = total_view+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}

	function reset_total_view($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}
	
	function update_total_comment($aid){
		if ($aid > 0) {
			$_sql = 'UPDATE '.$this->get_table_name().' 
				SET total_comment =  (SELECT count(aid) FROM '.$this->tbl_news_comment.' WHERE parent_news_aid = "'.$aid.'" and status = "1")
				WHERE aid = "'.$aid.'"';
			return $this->db->query($_sql);
		}
		return 0;
	}
	
	function get_total_activity($aid){
		$this->set_where(array('aid' => $aid));
		$result = $this->load_record(false);
		$return = array(
				'total_wow' => get_array_value($result, 'total_wow', 0),
				'total_cheer' => get_array_value($result, 'total_cheer', 0),
				'total_thanks' => get_array_value($result, 'total_thanks', 0),
				'total_comment' => get_array_value($result, 'total_comment', 0),
				'total_view' => get_array_value($result, 'total_view', 0)
			);
		return $return;
	}

	function update_all_total($aid) {
		$this->update_total_wow($aid);
		$this->update_total_cheer($aid);
		$this->update_total_thanks($aid);
	}

	function update_total_wow($aid){
		if ($aid > 0) {
			$_sql = 'UPDATE '.$this->get_table_name().' 
				SET total_wow =  (SELECT count(user_aid) FROM '.$this->tbl_news_user_activity.' WHERE news_aid = "'.$aid.'" and status_wow = "1")
				WHERE aid = "'.$aid.'"';
			return $this->db->query($_sql);
		}
		return 0;
	}
	
	function update_total_cheer($aid){
		if ($aid > 0) {
			$_sql = 'UPDATE '.$this->get_table_name().' 
				SET total_cheer =  (SELECT count(user_aid) FROM '.$this->tbl_news_user_activity.' WHERE news_aid = "'.$aid.'" and status_cheer = "1")
				WHERE aid = "'.$aid.'"';
			return $this->db->query($_sql);
		}
		return 0;
	}
	
	function update_total_thanks($aid){
		if ($aid > 0) {
			$_sql = 'UPDATE '.$this->get_table_name().' 
				SET total_thanks =  (SELECT count(user_aid) FROM '.$this->tbl_news_user_activity.' WHERE news_aid = "'.$aid.'" and status_thanks = "1")
				WHERE aid = "'.$aid.'"';
			return $this->db->query($_sql);
		}
		return 0;
	}

	function load_news_log($obj="") {
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		// if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		// $this->set_order_by("main.weight ASC, ".$this->get_table_name().".weight ASC");
		$result = $this->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		return $result;	
	}
	
}

/* End of file news_model.php */
/* Location: ./system/application/model/news_model.php */