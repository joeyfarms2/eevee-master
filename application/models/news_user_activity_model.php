<?php
class News_user_activity_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("news_user_activity");
			$this->tbl_user_name = 'user';
			$this->tbl_news = 'news';
	}

	function set_join_for_desc($obj="") {
		$this->db->select($this->tbl_name.'.*, user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid');
		$this->db->join($this->tbl_user_name.' AS user', $this->tbl_name.'.user_aid = user.aid', "left");
	}

	function fetch_data_with_desc($query)
	{
		$result = array("num_rows" => 0, "results" => "", "html" => "");
		if($query->num_rows() > 0){
			$result = array();
			$result["num_rows"] = $query->num_rows();
			$result["html"] = "<ul>";
			$result["results"] = array();
			foreach($query->result_array() as $row){
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				$result["html"] .= '<li>'.$row["full_name_th"].'</li>';
				$result["results"][] = $row;
			}
		}
		return $result;
	}

	function do_wow($news_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '1',
				'status_cheer' => '0',
				'status_thanks' => '0'
			)
		);
		return $done;
	}

	function do_unwow($news_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '0',
				'status_cheer' => '0',
				'status_thanks' => '0'
			)
		);
		return $done;
	}

	function do_cheer($news_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '0',
				'status_cheer' => '1',
				'status_thanks' => '0'
			)
		);
		return $done;
	}

	function do_uncheer($news_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '0',
				'status_cheer' => '0',
				'status_thanks' => '0'
			)
		);
		return $done;
	}

	function do_thanks($news_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '0',
				'status_cheer' => '0',
				'status_thanks' => '1'
			)
		);
		return $done;
	}

	function do_unthanks($news_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '0',
				'status_cheer' => '0',
				'status_thanks' => '0'
			)
		);
		return $done;
	}
/*
	function update_all_total($news_aid){
		if ($news_aid > 0) {
			$_sql = 'UPDATE '.$this->tbl_news.'
				SET total_wow =  (SELECT count(user_aid) FROM '.$this->tbl_name.' WHERE news_aid = "'.$news_aid.'" and status_wow = "1")
				WHERE aid = "'.$news_aid.'"';
			$this->db->query($_sql);

			$_sql = 'UPDATE '.$this->tbl_news.'
				SET total_cheer =  (SELECT count(user_aid) FROM '.$this->tbl_name.' WHERE news_aid = "'.$news_aid.'" and status_cheer = "1")
				WHERE aid = "'.$news_aid.'"';
			$this->db->query($_sql);
			
			$_sql = 'UPDATE '.$this->tbl_news.'
				SET total_thanks =  (SELECT count(user_aid) FROM '.$this->tbl_name.' WHERE news_aid = "'.$news_aid.'" and status_thanks = "1")
				WHERE aid = "'.$news_aid.'"';
			$this->db->query($_sql);

		}
	}
*/
	function has_activity($news_aid, $user_aid=""){
		$this->set_where(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info))
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['news_aid'])) {
			$return = array(
					'has_wowed' => ($result['status_wow'] == '1' ? true : false),
					'has_cheered' => ($result['status_cheer'] == '1' ? true : false),
					'has_thanked' => ($result['status_thanks'] == '1' ? true : false)
				);
		}
		else {
			$return = array(
					'has_wowed' => false,
					'has_cheered' => false,
					'has_thanked' => false
				);
		}
		return $return;
	}
	
	function has_wowed($news_aid, $user_aid=""){
		$this->set_where(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '1'
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['news_aid'])) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function has_cheered($news_aid, $user_aid=""){
		$this->set_where(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_cheer' => '1'
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['news_aid'])) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function has_thanked($news_aid, $user_aid=""){
		$this->set_where(array(
				'news_aid' => $news_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_thanks' => '1'
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['news_aid'])) {
			return true;
		}
		else {
			return false;
		}
	}
	
}

/* End of file news_user_activity_model.php */
/* Location: ./system/application/model/news_user_activity_model.php */