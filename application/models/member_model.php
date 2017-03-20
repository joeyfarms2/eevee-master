<?php
class Member_model extends Initmodel {
	
	var $chk_login_session = "0"; // 1= check If login. 0 = not check

	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user");
		// $this->tbl_user_role_name = "user_role";
	}

	function load_members_autocomplete($term="") {
		if (empty($term)) return "";

		$sql = "
				SELECT aid, cid, name, nick_name 
				FROM ".$this->tbl_name." AS member"."
				WHERE
					is_blacklist = '0'
					AND cid LIKE '".$term."%'
					AND (member_type = 'L' OR (member_type = 'A' AND expiration_date > DATE(NOW())))
				ORDER BY cid
			";
		$query = $this->db->query($sql);
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function load_members($start="0", $length="25", $keyword="", $sort_cols="", $arr_filters) {
		$i = 0;
		$sql = "SELECT 
					member.*
				FROM ".$this->tbl_name." AS member"." member
				WHERE cid <> '99999' ";
		// WHERE
		$where_sql = "";
		if (!empty($keyword) || !empty($arr_filters)) {
			$where_sql .= " AND (
						member.cid LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.first_name_th LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.last_name_th LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.nick_name LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.contact_number LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.address LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.email LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.status LIKE '%".mysql_real_escape_string( $keyword )."%' OR
						member.remark LIKE '%".mysql_real_escape_string( $keyword )."%'
			)";
			
			if (isset($arr_filters['is_blacklist'])) {
				$where_sql .= " AND member.is_blacklist = '".$arr_filters['is_blacklist']."'";
			}
			if (isset($arr_filters['is_expired']) && $arr_filters['is_expired'] == '1') {
				$where_sql .= " AND DATE(member.expiration_date) < DATE(NOW())";
			}
			$sql .= $where_sql;
		}
		
		// ORDER BY
		if (is_array($sort_cols) && count($sort_cols) > 0) {
			$sql .= " ORDER BY ";
			foreach ($sort_cols as $col => $dir) {
				if (trim($col) != "") {
					if ($i > 0) $sql .= ", ";
					$sql .= $col. " " . $dir;
					$i++;
				}
			}
		}
		else {
			$sql .= " ORDER BY member.cid DESC ";
		}
		
		$query = $this->db->query($sql);
		$return['num_rows'] = $query->num_rows();
		// LIMIT
		$sql .= " LIMIT ".$start.", ".$length;
		$query = $this->db->query($sql);
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$result[] = $row;
			}
		}
		
		$return['result'] = $result;
		$this->set_free_result($query);
		return $return;
	}
	
	function load_member_info($obj="") {
		if (!empty($obj)) {
			$this->db->select("
				*, 
				IF( DATE_SUB(expiration_date, INTERVAL 30 DAY) < DATE(NOW()), '1', '0' ) AS is_expired_soon, 
				IF( DATE(expiration_date) <= DATE(NOW()), '1', '0' ) AS is_expired", false);
			$this->db->from($this->tbl_name." AS member");
			if (isset($obj['aid'])) {
				$this->db->where('member.aid', $obj['aid']);
			}
			elseif (isset($obj['cid'])) {
				$this->db->where('member.cid', $obj['cid']);
				
			}
			$query = $this->db->get();
			$return = array();
			if($query->num_rows() > 0){
				foreach($query->result_array() as $k=>$row){
					foreach ($row as $k=>$v) {
						$return[$k] = $v;
					}
				}
			}
			
			return $return;
			// return $this->fetch_data($query);
		}
		else {
			return "";
		}
	}
	
	function find_full_member_cid($obj) {
		if (isset($obj['member_cid'])) {
			$this->db->select("*");
			$this->db->from($this->tbl_name." AS member");
			$this->db->where('cid', $obj['member_cid']);
			$query = $this->db->get();
			if($query->num_rows() > 0) {
				foreach($query->result_array() as $row){
					return $row["cid"];
				}
			}
		}

		// if (isset($obj['member_cid']) && strlen(trim($obj['member_cid'])) < 5) {
		if (isset($obj['member_cid'])) {
			$year = date('y');
			$x = 0;
			while($x < 20) {
				$this->db->select("*");
				$this->db->from($this->tbl_name." AS member");
				$this->db->where('cid', $year.str_pad($obj['member_cid'], 3, '0', STR_PAD_LEFT));
				$query = $this->db->get();
				if($query->num_rows() > 0) {
					return $year.str_pad($obj['member_cid'], 3, '0', STR_PAD_LEFT);
				}
				else {
					$year = $year - 1;
					$x++;
				}
			}
		}
		else if (isset($obj['member_name'])) {
			$this->db->select("cid");
			$this->db->from($this->tbl_name." AS member");
			$this->db->like('first_name_th', $obj['member_name']);
			$this->db->or_like('nick_name', $obj['member_name']);
			$query = $this->db->get();
			if($query->num_rows() > 0) {
				foreach($query->result_array() as $row){
					return $row["cid"];
				}
			}
		}
		return "";
	}
	
	function load_latest_cid() {
		$this_year = mdate("%y", time());
		$this->db->select_max('cid');
		$this->db->from($this->tbl_name." AS member");
		$this->db->where("cid <> '99999'");
		// $this->db->where("LEFT(cid, 2) = RIGHT(YEAR(NOW()), 2)");
		$query = $this->db->get();
		$return = array();
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				return $row["cid"];
			}
		}
		else {
			// return $this_year . "000";
			return "0";
		}
	}
	
	function set_expiration_date($obj="") {
		if (isset($obj["aid"])) {
			$this->db->where("aid", $obj["aid"]);
			if (isset($obj["val"])) {
				return $this->db->update($this->tbl_name." AS member", array("expiration_date" => $obj["val"])); 
			}
			else {
				if (isset($obj["use_created_date"])) {
					$_sql = "UPDATE ".$this->tbl_name." AS member"." SET expiration_date = DATE(DATE_ADD(created_date, INTERVAL 1 YEAR)) WHERE  aid= ".$obj["aid"];
					return $this->db->query($_sql);
				}
				else if (isset($obj["use_expiration_date"])) {
					$_sql = "UPDATE ".$this->tbl_name." AS member"." SET expiration_date = IF(expiration_date > NOW(), DATE(DATE_ADD(expiration_date, INTERVAL 1 YEAR)), DATE(DATE_ADD(NOW(), INTERVAL 1 YEAR))) WHERE  aid= ".$obj["aid"];
					return $this->db->query($_sql);
				}
				else {
					$_sql = "UPDATE ".$this->tbl_name." AS member"." SET expiration_date = DATE(DATE_ADD(NOW(), INTERVAL 1 YEAR)) WHERE  aid= ".$obj["aid"];
					return $this->db->query($_sql);				}
			}
		}
	}
	
}

/* End of file user_model.php */
/* Location: ./system/application/model/user_model.php */