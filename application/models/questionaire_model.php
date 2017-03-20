<?php
class Questionaire_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("questionaire");		
			$this->tbl_questionaire_question = "questionaire_question";
			$this->tbl_questionaire_question_choice = "questionaire_question_choice";
			$this->tbl_questionaire_user_activity = "questionaire_user_activity";
			$this->tbl_questionaire_user_submit = "questionaire_user_submit";
			$this->tbl_user_name = 'user';
	}
	
	function load_home($category_aid="", $count=3, $offset=0){
		$tmp = array();
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";

		$this->db->select('questionaire.*, user.username , user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid, activity.user_aid as activity_user_aid, activity.has_submitted as activity_has_submitted');
		$this->db->from($this->tbl_name);
		$this->db->join($this->tbl_user_name.' AS user', 'questionaire.created_by = user.aid', "left");
		$this->db->join($this->tbl_questionaire_user_activity.' AS activity', 'questionaire.aid = activity.questionaire_aid AND activity.user_aid = "'.getUserLoginAid($this->user_login_info).'"', "right");

		$this->set_where($tmp);

		if(!empty($category_aid)){
			// $this->set_and_or_like_by_field("category",$category_aid_arr,"both");
			$this->db->like("category", ','.$category_aid.',', 'both');
		}
		$this->db->where('(questionaire.expiry_date >=', 'DATE(now())', false);
		$this->db->or_where('questionaire.expiry_date IS NULL', ')', false);
		$this->set_order_by("weight ASC, publish_date DESC, created_date DESC");
		$this->set_limit($offset, $count);
		$query = $this->db->get();
		return $this->fetch_data_with_desc($query);
	}
	
	function load_example_questionaire($questionaire_main_aid="", $questionaire_category_aid="", $offset=0, $count=5){
		$tmp = array();
		$tmp["is_home"] = "1";
		if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($questionaire_main_aid) && $questionaire_main_aid > 0) $tmp["questionaire_main_aid"] = $questionaire_main_aid;
		$this->set_where($tmp);
		if(!is_blank($questionaire_category_aid)){
			$tmp = array();
			$tmp["category"] = ",".$questionaire_category_aid.",";
			$this->set_like($tmp);
		}
		$this->set_order_by("weight ASC, questionaire_start_date DESC, created_date DESC");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true);
		return $result;
	}
		
	function set_join_for_desc($obj=""){
		$this->db->select('questionaire.*, user.username , user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid, activity.user_aid as activity_user_aid, activity.has_submitted as activity_has_submitted');
		$this->db->join($this->tbl_user_name.' AS user', 'questionaire.created_by = user.aid', "left");
		
		if(!exception_about_status()) {
			$this->db->join($this->tbl_questionaire_user_activity.' AS activity', 'questionaire.aid = activity.questionaire_aid AND activity.user_aid = "'.getUserLoginAid($this->user_login_info).'"', "left");
		}
		else {
			$this->db->join($this->tbl_questionaire_user_activity.' AS activity', 'questionaire.aid = activity.questionaire_aid AND activity.user_aid = "'.getUserLoginAid($this->user_login_info).'"', "left");
		}
	}

	function fetch_data_with_desc($query)
	{
		$result = array("num_rows" => 0, "results" => "");
		if($query->num_rows() > 0){
			$result = array();
			$result["num_rows"] = $query->num_rows();
			$result["results"] = array();
			foreach($query->result_array() as $row){
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}

				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
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
				$row["created_date_txt"] = get_datetime_pattern("d M Y",get_array_value($row,"created_date",""),"");
				$row["publish_date_txt"] = get_datetime_pattern("d M Y",get_array_value($row,"publish_date",""),"");
				$row["expiry_date_txt"] = get_datetime_pattern("d M Y",get_array_value($row,"expiry_date",""),"-");

				$result["results"][] = $row;
			}
		}
		return $result;
	}
	
	function increase_total_view($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view = total_view+1 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}

	function reset_total_view($aid){
		$_sql = 'UPDATE '.$this->get_table_name().' SET total_view = 0 WHERE aid = "'.$aid.'"';
		return $this->db->query($_sql);
	}

	function update_total_submit($aid){
		if ($aid > 0) {
			$_sql = 'UPDATE '.$this->get_table_name().' 
				SET total_submit =  (SELECT count(user_aid) FROM '.$this->tbl_questionaire_user_activity.' WHERE questionaire_aid = "'.$aid.'" AND has_submitted = "1")
				WHERE aid = "'.$aid.'"';
			return $this->db->query($_sql);
		}
		return 0;
	}
	

	
}

/* End of file questionaire_model.php */
/* Location: ./system/application/model/questionaire_model.php */