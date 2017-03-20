<?php
class Questionaire_user_activity_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("questionaire_user_activity");
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

	function get_emails($questionaire_aid="") {
		if (!is_numeric($questionaire_aid)) return '';
		if (empty($questionaire_aid)) return '';

		$_sql = "SELECT GROUP_CONCAT(user.email) AS list_email FROM questionaire_user_activity LEFT JOIN user ON questionaire_user_activity.user_aid = user.aid WHERE questionaire_user_activity.questionaire_aid = ".$questionaire_aid;
		$query = $this->db->query($_sql);
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			$rs =  $query->result_array();
			if (is_var_array($rs) && isset($rs[0]['list_email'])) {
				if (!empty($rs[0]['list_email'])) {
					return $rs[0]['list_email'];
				}
			}
		}
		return '';
	}

	function has_submitted($questionaire_aid, $user_aid=""){
		$this->set_where(array(
				'questionaire_aid' => $questionaire_aid,
				'user_aid' => (!is_blank($user_aid) ? $user_aid : getUserLoginAid($this->user_login_info)),
				'has_submitted' => '1'
			)
		);
		$this->set_limit(0,1);
		$result = $this->load_record(false);
		if (is_var_array($result) && isset($result['questionaire_aid'])) {
			return true;
		}
		else {
			return false;
		}
	}
}

/* End of file questionaire_user_activity_model.php */
/* Location: ./system/application/model/questionaire_user_activity_model.php */