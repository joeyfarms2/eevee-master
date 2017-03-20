<?php
class Event_user_activity_join_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("event_user_activity_join");
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

	function do_join($event_aid, $user_aid="", $has_joined='1'){
		$done = $this->insert_or_update(array(
				'event_aid' => $event_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'has_joined' => $has_joined
			)
		);
		return $done;
	}

	function has_joined($event_aid, $user_aid=""){
		$this->set_where(array(
				'event_aid' => $event_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'has_joined' => '1'
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['event_aid'])) {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	function insert_invitation($obj="") {
		$insert_query = $this->db->insert_string($this->tbl_name, $obj);
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO', $insert_query);
		$this->db->query($insert_query);
	}
	*/
}

/* End of file event_user_activity_model.php */
/* Location: ./system/application/model/event_user_activity_model.php */