<?php
class News_comment_user_activity_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("news_comment_user_activity");
			$this->tbl_user_name = 'user';
	}
	
	function set_join_for_desc($obj="") {
		$this->db->select($this->tbl_name.'.*, user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid');
		$this->db->join($this->tbl_user_name.' AS user', $this->tbl_name.'.user_aid = user.aid', "left");
	}

	function fetch_data_with_desc($query)
	{
		$found_you = false;
		$result = "";
		if($query->num_rows() > 0){
			$result = array();
			$result["num_rows"] = $query->num_rows();
			$result["html"] = "";
			$result["results"] = array();
			foreach($query->result_array() as $row){
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				if ($row['user_aid'] == getUserLoginAid($this->user_login_info)) {
					$found_you = true;
				}
				else {
					$result["html"] .= (!empty($result["html"]) ? '<br/>' : '').$row["full_name_th"];
				}
				$result["results"][] = $row;
			}
			if ($found_you) {
				$result["html"] .= (!empty($result["html"]) ? '<br/>' : '').'You';
			}
		}
		return $result;
	}

	function do_wow($news_comment_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_comment_aid' => $news_comment_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '1'
			)
		);
		return $done;
	}

	function do_unwow($news_comment_aid, $user_aid=""){
		$done = $this->insert_or_update(array(
				'news_comment_aid' => $news_comment_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '0'
			)
		);
		return $done;
	}

	function has_wowed($news_comment_aid, $user_aid=""){
		$this->set_where(array(
				'news_comment_aid' => $news_comment_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'status_wow' => '1'
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['news_comment_aid'])) {
			return true;
		}
		else {
			return false;
		}
	}
	
}

/* End of file news_user_activity_model.php */
/* Location: ./system/application/model/news_user_activity_model.php */