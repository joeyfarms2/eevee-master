<?php
class News_user_activity_like_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("news_user_activity_like");
			$this->tbl_user_name = 'user';
	}

	function set_join_for_desc($obj="") {
		$this->db->select($this->tbl_name.'.*, user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid');
		$this->db->join($this->tbl_user_name.' AS user', $this->tbl_name.'.user_aid = user.aid', "left");
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$result = array();
			$result["num_rows"] = $query->num_rows();
			$result["html"] = "";
			$result["results"] = array();
			foreach($query->result_array() as $row){
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				$result["html"] .= (!empty($result["html"]) ? '<br/>' : '').$row["full_name_th"];
				$result["results"][] = $row;
			}
		}
		return $result;
	}

	function do_like($news_aid){
		$done = $this->insert_or_update(array(
				'news_aid' => $news_aid,
				'user_aid' => getUserLoginAid($this->user_login_info)
			)
		);
		return $done;
	}

	function do_unlike($news_aid){
		$this->set_where(array(
				'news_aid' => $news_aid,
				'user_aid' => getUserLoginAid($this->user_login_info)
			)
		);
		$done = $this->delete_records();
		return $done;
	}

	function has_liked($news_aid){
		$this->set_where(array(
				'news_aid' => $news_aid,
				'user_aid' => getUserLoginAid($this->user_login_info)
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