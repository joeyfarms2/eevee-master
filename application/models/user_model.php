<?php
class User_model extends Initmodel {
	
	var $chk_login_session = "0"; // 1= check If login. 0 = not check

	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("user");
		$this->tbl_user_role_name = "user_role";
		$this->tbl_user_department = "user_department";
	}
	
	function generate_new_password($length = 8)
	{
		$c = '9a1bc8defghijkl2mn0op3qr5st4uvwx2yz3A4B60CDEF95GHIJ6KLMNOPQ7R7STUV1WXYZ08123456789';   
		$password = '';
		for ($i = 0; $i < $length; $i++) 
			{       
				$password .= $c[(rand() % strlen($c))];       
		}
		return $password;
	}
	
	function encryptPassword($password)
	{
		if( is_blank($password) ) return "";
		return md5($password.CONST_HASH_KEY);
	}
	
	function set_join_for_desc($obj="")
	{
		$this->db->select('user.*, user_role.name AS user_role_name, dept.name AS department_name');
		$this->db->join($this->tbl_user_role_name.' AS user_role', 'user.user_role_aid = user_role.aid', "left");
		$this->db->join($this->tbl_user_department.' AS dept', 'user.department_aid = dept.aid', "left");
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows();
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				switch($row["gender"]){
					case "f" : $row["gender_name"] = "Female"; break;
					case "m" : $row["gender_name"] = "Male"; break;
					default : $row["gender_name"] = "N/A";	 break;
				}
				
				$user_info_show = "";
				$first_name_th = trim(get_array_value($row,"first_name_th",""));
				$last_name_th = trim(get_array_value($row,"last_name_th",""));
				$user_name = trim(get_array_value($row,"username",""));
				$display_name = trim(get_array_value($row,"display_name",""));
				$email = trim(get_array_value($row,"email",""));
				
				if(!is_blank($display_name)){
					$user_info_show = $display_name;
				}else if(!is_blank($first_name_th)){
					if(preg_match('/^([A-Za-z]+)$/',$last_name_th)){
							$last_name = ucfirst(substr($last_name_th,0,1));
						if(!is_blank($last_name)){
							$last_name .= '.';
						}
					}else{
							$last_name = $last_name_th;
						if(!is_blank($last_name)){
							$last_name .= '.';
						}
					}
					
					$user_info_show = trim($first_name_th.' '.$last_name);
				}else if(!is_blank($user_name)){
					$user_info_show = $user_name;
				}else{
					$user_info_show = $email;
				}
				$row["user_info_show"] = $user_info_show;
				
				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y H:i",get_array_value($row,"created_date",""),"");
				$row["registration_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"registration_date",""),"");
				$row["expiration_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"expiration_date",""),"");
				$row["birthday_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"birthday",""),"");
				$row["avatar_mode"] = "thumb";
				$row["avatar"] = get_user_avatar($row);
				$row["avatar_mode"] = "mini";
				$row["avatar_mini"] = get_user_avatar($row);
				$row["avatar_mode"] = "tiny";
				$row["avatar_tiny"] = get_user_avatar($row);
				$result[] = $row;
			}
		}
		return $result;
	}

	function update_last_login($data)
	{
		if( get_array_value($data,"aid","") == "") return false;
		
		$tmp_data['last_login'] = get_db_now();
		$tmp_data['is_login'] = 1;
		$tmp_data['login_hash'] = get_array_value($data,"login_hash","");

		$this->db->where("aid", $data["aid"]);
		return $this->db->update($this->tbl_name, $tmp_data);
	}
	
	function confirm_activation($data)
	{
		if( get_array_value($data,"aid","") == "") return false;
		$tmp_data['activate_code'] = '';
		$this->db->where("aid", $data["aid"]);
		return $this->db->update($this->tbl_name, $tmp_data);
	}
	
	function reset_password($aid,$new_password)
	{
		$data = array(
				'password'	=>	$this->encryptPassword($new_password)
			);
		$this->set_where(array("aid"=>$aid));
		$result = $this->update_record($data);
		// Return result
		if ($result === 1)
			return true;
		else
			return false;
	}
	
	function get_login_hash($aid)
	{
		if(is_blank($aid) || $aid <= 0) return "";
		$this->db->select("login_hash");
		$this->set_where(array("aid"=>$aid));
		$result = $this->load_record(false);
		if(is_var_array($result)){
			return get_array_value($result,"login_hash");
		}else{
			return "";
		}
	}
	
	function set_logout()
	{
		$user_aid = getSessionUserAid();
		if($user_aid){
			$data = array(
					'is_login'	=>	0,
					'login_hash'	=>	""
				);
			$this->set_where(array("aid"=>$user_aid));
			$result = $this->update_record($data);
			// Return result
			if ($result === 1)
				return true;
			else
				return false;
		}else{
			return false;
		}
	}
	
	function get_user_point_remain($user_aid=""){
		if(is_blank($user_aid)){
			return 0;
		}
		
		$this->db->select("user.point_remain");
		$this->set_where(array("aid"=>$user_aid, "status"=>"1"));
		$result = $this->load_record(false);
		if(is_var_array($result)){
			return get_array_value($result,"point_remain","0");
		}else{
			return 0;
		}
		
	}
	
	function add_point_remain($user_aid="",$point=0){
		// if($user_aid <= 0 || $point <= 0){
		// 	return "";
		// }
		
		$_sql = "UPDATE user SET point_remain = (point_remain+".$point.") WHERE aid = '".$user_aid."'";
		return $this->db->query($_sql);
	}

	function reduce_point_remain($user_aid="",$point=0){
		// if($user_aid <= 0 || $point <= 0){
		// 	return "";
		// }
		
		$_sql = "UPDATE user SET point_remain = (point_remain-".$point.") WHERE aid = '".$user_aid."'";
		return $this->db->query($_sql);
	}

	function get_all_staff_emails(){
		$_sql = "SELECT GROUP_CONCAT(email) AS list_email FROM user WHERE user_role_aid > 2";
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

	function get_staff_emails($list_user_aid){
		$_sql = "SELECT GROUP_CONCAT(email) AS list_email FROM user WHERE aid IN (".$list_user_aid.")";
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

}

?>