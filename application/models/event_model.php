<?php
class Event_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("event");		

			$this->tbl_event_main_name = "event_main";
			$this->tbl_event_user_activity_join = "event_user_activity_join";
			$this->tbl_user_name = 'user';
	}
	
	function load_by_period($category_aid_arr="", $event_main_aid="", $include_past=FALSE, $period_start="", $period_end="", $user_aid="", $user_role_aid="") {
		if (empty($user_aid)) {
			$user_aid = getUserLoginAid($this->user_login_info);
		}

		if (!empty($user_role_aid)) {
			if ($user_role_aid == '1' || $user_role_aid == '2' || $user_role_aid == '3') {
				$join_type = 'LEFT';
			}
			else {
				$join_type = 'INNER';
			}
		}
		else {
			if(!exception_about_status()) {
				$join_type = 'INNER';
			}
			else {
				$join_type = 'LEFT';
			}
		}

		$sql = "
			SELECT e.*
			FROM
			(
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, 
					`user`.`username`, 
					`user`.`first_name_th`, 
					`user`.`last_name_th`, 
					`user`.`email`, 
					`user`.`avatar_path`, 
					`user`.`avatar_type`, 
					`user`.`gender`, 
					`user`.`department_aid`, 
					`activity_join`.`user_aid` as activity_join_user_aid, 
					`activity_join`.`has_joined` as activity_join_has_joined 
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					LEFT JOIN `user` AS user ON `event`.`created_by` = `user`.`aid` 
					INNER JOIN `event_user_activity_join` AS activity_join ON `event`.`aid` = `activity_join`.`event_aid` AND activity_join.user_aid = '".$user_aid."' AND event.is_public = '0'
					WHERE event.status = '1' ";
		
		if(!is_blank($event_main_aid) && $event_main_aid > 0) {
			$sql .= " AND event.event_main_aid = '".$event_main_aid."'";
		}
		if(is_var_array($category_aid_arr)) {
			$sql .= " AND (";
	   	$i=0;
			foreach($category_aid_arr as $value){
				if($i != 0) $sql .= " OR ";
				$sql .= " category LIKE '%".$value."%'";
				$i++;
			}
			$sql .= " )";
		}
		if ($include_past == TRUE) {
			$sql .= " AND (event.event_start_date >= '".$period_start."'";
			$sql .= " AND event.event_start_date <= '".$period_end."')";
		}
		else {
			$sql .= " AND event.event_start_date >= NOW() ";
		}
		
		$sql .= "
				)
			UNION
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, '', '', '', '', '', '', '', '', '', ''
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					WHERE event.is_public = '1' AND event.status = '1' AND event.event_main_aid = '1'
		";
		if ($include_past == TRUE) {
			$sql .= " AND (event.event_start_date >= '".$period_start."'";
			$sql .= " AND event.event_start_date <= '".$period_end."')";
		}
		else {
			$sql .= " AND event.event_start_date >= NOW() ";
		}
		$sql .= "
				)
			) AS e
			GROUP BY aid
			ORDER BY `event_start_date` ASC, `weight` ASC, `created_date` DESC
			";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		$result = $this->fetch_data_with_desc($query);
		return $result;
	}
	
	function load_home($category_aid_arr="", $event_main_aid="", $count=3, $offset=0, $user_aid="", $user_role_aid=""){
		$tmp = array();
		// $tmp["is_home"] = "1";
		$tmp["status"] = "1";
		if (empty($user_aid)) {
			$user_aid = getUserLoginAid($this->user_login_info);
		}
		// if(!exception_about_status()) $tmp["status"] = "1";

		if (!empty($user_role_aid)) {
			if ($user_role_aid == '1' || $user_role_aid == '2' || $user_role_aid == '3') {
				$join_type = 'LEFT';
			}
			else {
				$join_type = 'INNER';
			}
		}
		else {
			if(!exception_about_status()) {
				$join_type = 'INNER';
			}
			else {
				$join_type = 'LEFT';
			}
		}

		$sql = "
			SELECT e.*
			FROM
			(
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, 
					`user`.`username`, 
					`user`.`first_name_th`, 
					`user`.`last_name_th`, 
					`user`.`email`, 
					`user`.`avatar_path`, 
					`user`.`avatar_type`, 
					`user`.`gender`, 
					`user`.`department_aid`, 
					`activity_join`.`user_aid` as activity_join_user_aid, 
					`activity_join`.`has_joined` as activity_join_has_joined 
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					LEFT JOIN `user` AS user ON `event`.`created_by` = `user`.`aid` 
					INNER JOIN `event_user_activity_join` AS activity_join ON `event`.`aid` = `activity_join`.`event_aid` AND activity_join.user_aid = '".$user_aid."' AND event.is_public = '0'
					WHERE event.status = '1' ";
		
		if(!is_blank($event_main_aid) && $event_main_aid > 0) {
			$sql .= " AND event.event_main_aid = '".$event_main_aid."'";
		}
		if(is_var_array($category_aid_arr)) {
			$sql .= " AND (";
	   	$i=0;
			foreach($category_aid_arr as $value){
				if($i != 0) $sql .= " OR ";
				$sql .= " category LIKE '%".$value."%'";
				$i++;
			}
			$sql .= " )";
		}
		$sql .= " AND event.event_start_date >= NOW() ";
		
		$sql .= "
				)
			UNION
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, '', '', '', '', '', '', '', '', '', ''
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					WHERE event.is_public = '1' AND event.status = '1' AND event.event_main_aid = '1' AND event.event_start_date >= NOW()
				)
			) AS e
			GROUP BY aid
			ORDER BY `event_start_date` ASC, `weight` ASC, `created_date` DESC
			LIMIT ".$offset.", ".$count."
			";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		$result = $this->fetch_data_with_desc($query);
		return $result;
	}
	
	function load_home_my_invitations($category_aid_arr="", $event_main_aid="", $count=3, $offset=0, $user_aid="") {
		$tmp = array();
		// $tmp["is_home"] = "1";
		$tmp["status"] = "1";
		if (empty($user_aid)) {
			$user_aid = getUserLoginAid($this->user_login_info);
		}
		// if(!exception_about_status()) $tmp["status"] = "1";

		$sql = "
			SELECT e.*
			FROM
			(
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, 
					`user`.`username`, 
					`user`.`first_name_th`, 
					`user`.`last_name_th`, 
					`user`.`email`, 
					`user`.`avatar_path`, 
					`user`.`avatar_type`, 
					`user`.`gender`, 
					`user`.`department_aid`, 
					`activity_join`.`user_aid` as activity_join_user_aid, 
					`activity_join`.`has_joined` as activity_join_has_joined 
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					LEFT JOIN `user` AS user ON `event`.`created_by` = `user`.`aid` 
					INNER JOIN `event_user_activity_join` AS activity_join ON `event`.`aid` = `activity_join`.`event_aid` AND activity_join.user_aid = '".$user_aid."' AND event.is_public = '0'
					WHERE event.status = '1' ";
		
		if(!is_blank($event_main_aid) && $event_main_aid > 0) {
			$sql .= " AND event.event_main_aid = '".$event_main_aid."'";
		}
		if(is_var_array($category_aid_arr)) {
			$sql .= " AND (";
	   	$i=0;
			foreach($category_aid_arr as $value){
				if($i != 0) $sql .= " OR ";
				$sql .= " category LIKE '%".$value."%'";
				$i++;
			}
			$sql .= " )";
		}
		$sql .= " AND event.event_start_date >= NOW() ";
		
		$sql .= "
				)
			UNION
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, '', '', '', '', '', '', '', '', '', ''
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					WHERE event.is_public = '1' AND event.status = '1' AND event.event_main_aid = '1' AND event.event_start_date >= NOW()
				)
			) AS e
			GROUP BY aid
			ORDER BY `event_start_date` ASC, `weight` ASC, `created_date` DESC
			LIMIT ".$offset.", ".$count."
			";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		$result = $this->fetch_data_with_desc($query);
		return $result;
	}
	
	function load_widget_calendar($category_aid_arr="", $event_main_aid="", $user_aid=""){
		$tmp = array();
		$this->db->flush_cache();

		if (empty($user_aid)) {
			$user_aid = getUserLoginAid($this->user_login_info);
		}

		$sql = "
			SELECT e.*
			FROM
			(
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, 
					`user`.`username`, 
					`user`.`first_name_th`, 
					`user`.`last_name_th`, 
					`user`.`email`, 
					`user`.`avatar_path`, 
					`user`.`avatar_type`, 
					`user`.`gender`, 
					`user`.`department_aid`, 
					`activity_join`.`user_aid` as activity_join_user_aid, 
					`activity_join`.`has_joined` as activity_join_has_joined 
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					LEFT JOIN `user` AS user ON `event`.`created_by` = `user`.`aid` 
					INNER JOIN `event_user_activity_join` AS activity_join ON `event`.`aid` = `activity_join`.`event_aid` AND activity_join.user_aid = '".$user_aid."' AND event.is_public = '0'
					WHERE event.status = '1' ";
		
		if(!is_blank($event_main_aid) && $event_main_aid > 0) {
			$sql .= " AND event.event_main_aid = '".$event_main_aid."'";
		}
		if(is_var_array($category_aid_arr)) {
			$sql .= " AND (";
	   	$i=0;
			foreach($category_aid_arr as $value){
				if($i != 0) $sql .= " OR ";
				$sql .= " category LIKE '%".$value."%'";
				$i++;
			}
			$sql .= " )";
		}
		$sql .= " AND MONTH(event.event_start_date) = MONTH(NOW()) ";
		$sql .= " AND YEAR(event.event_start_date) = YEAR(NOW()) ";
		
		$sql .= "
				)
			UNION
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, '', '', '', '', '', '', '', '', '', ''
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					WHERE event.is_public = '1' AND event.status = '1' AND event.event_main_aid = '1' AND MONTH(event.event_start_date) = MONTH(NOW()) AND YEAR(event.event_start_date) = YEAR(NOW())
				)
			) AS e
			GROUP BY aid
			ORDER BY `event_start_date` ASC, `weight` ASC, `created_date` DESC
			";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		$result = $this->fetch_data_with_desc($query);
		return $result;
	}
	
	function load_this_month_incoming($category_aid_arr="", $event_main_aid="", $user_aid=""){
		$tmp = array();
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		
		if (empty($user_aid)) {
			$user_aid = getUserLoginAid($this->user_login_info);
		}

		$sql = "
			SELECT e.*
			FROM
			(
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, 
					`user`.`username`, 
					`user`.`first_name_th`, 
					`user`.`last_name_th`, 
					`user`.`email`, 
					`user`.`avatar_path`, 
					`user`.`avatar_type`, 
					`user`.`gender`, 
					`user`.`department_aid`, 
					`activity_join`.`user_aid` as activity_join_user_aid, 
					`activity_join`.`has_joined` as activity_join_has_joined 
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					LEFT JOIN `user` AS user ON `event`.`created_by` = `user`.`aid` 
					INNER JOIN `event_user_activity_join` AS activity_join ON `event`.`aid` = `activity_join`.`event_aid` AND activity_join.user_aid = '".$user_aid."' AND event.is_public = '0'
					WHERE event.status = '1' ";
		
		if(!is_blank($event_main_aid) && $event_main_aid > 0) {
			$sql .= " AND event.event_main_aid = '".$event_main_aid."'";
		}
		if(is_var_array($category_aid_arr)) {
			$sql .= " AND (";
	   	$i=0;
			foreach($category_aid_arr as $value){
				if($i != 0) $sql .= " OR ";
				$sql .= " category LIKE '%".$value."%'";
				$i++;
			}
			$sql .= " )";
		}
		$sql .= " AND DATE(event.event_start_date) >= DATE(NOW()) ";
		$sql .= " AND MONTH(event.event_end_date) = MONTH(NOW()) ";
		
		$sql .= "
				)
			UNION
				(
					SELECT `event`.*, `event_main`.`name` as event_main_name, '', '', '', '', '', '', '', '', '', ''
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					WHERE event.is_public = '1' AND event.status = '1' AND event.event_main_aid = '1' AND DATE(event.event_start_date) >= DATE(NOW()) AND MONTH(event.event_end_date) = MONTH(NOW())
				)
			) AS e
			GROUP BY aid
			ORDER BY `event_start_date` ASC, `weight` ASC, `created_date` DESC
			LIMIT ".$offset.", ".$count."
			";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		$result = $this->fetch_data_with_desc($query);
		return $result;
	}
	
	function load_recommended($event_main_aid="", $offset=0, $count=5, $user_aid=""){
		$tmp = array();
		$tmp["is_recommended"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";

		if (empty($user_aid)) {
			$user_aid = getUserLoginAid($this->user_login_info);
		}
		if(!is_blank($event_main_aid) && $event_main_aid > 0) $tmp["event_main_aid"] = $event_main_aid;
		$this->set_where($tmp);
		$this->set_order_by("weight ASC, event_start_date DESC, created_date DESC");
		// $this->set_order_by("weight");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true, array('user_aid' => $user_aid));
		return $result;
	}
		
	function _load_example_event($event_main_aid="", $event_category_aid="", $offset=0, $count=5, $user_aid=""){
		$tmp = array();
		// $tmp["is_home"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";

		if (empty($user_aid)) {
			$user_aid = getUserLoginAid($this->user_login_info);
		}
		if(!is_blank($event_main_aid) && $event_main_aid > 0) $tmp["event_main_aid"] = $event_main_aid;
		$this->set_where($tmp);
		if(!is_blank($event_category_aid)){
			$tmp = array();
			$tmp["category"] = ",".$event_category_aid.",";
			$this->set_like($tmp);
		}
		$this->set_order_by("weight ASC, event_start_date DESC, created_date DESC");
		$this->set_limit($offset, $count);
		$result = $this->load_records(true, array('user_aid' => $user_aid));
		return $result;
	}
		
	function load_highlight($event_main_aid="", $offset=0, $count=5){
		$tmp = array();
		$tmp["is_highlight"] = "1";
		$tmp["status"] = "1";
		// if(!exception_about_status()) $tmp["status"] = "1";
		if(!is_blank($event_main_aid) && $event_main_aid > 0) $tmp["event_main_aid"] = $event_main_aid;
		$this->set_where($tmp);
		$this->set_order_by("weight ASC, event_start_date DESC, created_date DESC");
		// $this->set_order_by("weight");
		$this->set_limit($offset, $count);
		$result = $this->load_record(true);
		return $result;
	}


	function load_event_detail($event_aid="") {
		$tmp = array();
		$user_aid = getUserLoginAid($this->user_login_info);
		// if(!exception_about_status()) $tmp["status"] = "1";

		$sql = "
					SELECT `event`.*, `event_main`.`name` as event_main_name, 
					`user`.`username`, 
					`user`.`first_name_th`, 
					`user`.`last_name_th`, 
					`user`.`email`, 
					`user`.`avatar_path`, 
					`user`.`avatar_type`, 
					`user`.`gender`, 
					`user`.`department_aid`, 
					`activity_join`.`user_aid` as activity_join_user_aid, 
					`activity_join`.`has_joined` as activity_join_has_joined 
					FROM (`event`) 
					LEFT JOIN `event_main` AS event_main ON `event`.`event_main_aid` = `event_main`.`aid` 
					LEFT JOIN `user` AS user ON `event`.`created_by` = `user`.`aid` 
					LEFT JOIN `event_user_activity_join` AS activity_join ON `event`.`aid` = `activity_join`.`event_aid` AND ( (activity_join.user_aid = '".$user_aid."' AND event.is_public = '0') OR event.is_public = '1')
					WHERE event.status = '1' AND event.aid = '".$event_aid."'";
		// echo $sql; exit;
		$query = $this->db->query($sql);
		$result = $this->fetch_data_with_desc($query);
		return $result;
	}
	
		
	function set_join_for_desc($obj="") {
		$this->db->flush_cache();
		if (isset($obj['user_aid']) && !empty($obj['user_aid'])) {
			$user_aid = $obj['user_aid'];
		}
		else {
			$user_aid = getUserLoginAid($this->user_login_info);
		}

		if (isset($obj['user_role_aid']) && !empty($obj['user_role_aid'])) {
			if ($obj['user_role_aid'] == '1' || $obj['user_role_aid'] == '2' || $obj['user_role_aid'] == '3') {
				$join_type = 'left';
			}
			else {
				$join_type = 'inner';
			}
		}
		else {
			if(!exception_about_status()) {
				$join_type = 'inner';
			}
			else {
				$join_type = 'left';
			}
		}

		if (isset($obj['join_type']) && !empty($obj['join_type'])) {
			$join_type = $obj['join_type'];
		}
		else {
			$join_type = 'inner';
		}

		$this->db->select('event.*, event_main.name as event_main_name, user.username , user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid, activity_join.user_aid as activity_join_user_aid, activity_join.has_joined as activity_join_has_joined');
		$this->db->join($this->tbl_event_main_name.' AS event_main', 'event.event_main_aid = event_main.aid', "left");
		$this->db->join($this->tbl_user_name.' AS user', 'event.created_by = user.aid', "left");
		
		$this->db->join($this->tbl_event_user_activity_join.' AS activity_join', 'event.aid = activity_join.event_aid AND activity_join.user_aid = "'.$user_aid.'"', $join_type);
		
	}

	function fetch_data_with_desc($query)
	{
		$result = array("num_rows" => 0, "results" => "");
		if($query->num_rows() > 0){
			$result = array();
			$result["num_rows"] = $query->num_rows();
			$result["results"] = array();
			foreach($query->result_array() as $row){

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

				$row['event_period_date_txt'] = '';
				$row['event_period_date_txt_2_lines'] = '';
				if (mdate('%Y-%m-%d', strtotime(get_array_value($row,"event_start_date",""))) == mdate('%Y-%m-%d', strtotime(get_array_value($row,"event_end_date","")))) {
					// If same day
					$row['event_period_date_txt'] = mdate('%d %M %Y', strtotime(get_array_value($row,"event_start_date","")));
					$row['event_period_date_txt_2_lines'] = mdate('%d %M %Y', strtotime(get_array_value($row,"event_start_date","")));
					if ( mdate('%H:%i', strtotime(get_array_value($row,"event_start_date",""))) !== mdate('%H:%i', strtotime(get_array_value($row,"event_end_date",""))) ) {
						$row['event_period_date_txt'] .= ' / '. mdate('%H:%i', strtotime(get_array_value($row,"event_start_date",""))) . ' - ' . mdate('%H:%i', strtotime(get_array_value($row,"event_end_date","")));
						$row['event_period_date_txt_2_lines'] .= '<br/>'. mdate('%H:%i', strtotime(get_array_value($row,"event_start_date",""))) . ' - ' . mdate('%H:%i', strtotime(get_array_value($row,"event_end_date","")));
					}
					
				}
				else {
					if ( mdate('%H:%i', strtotime(get_array_value($row,"event_start_date",""))) == '00:00' &&  mdate('%H:%i', strtotime(get_array_value($row,"event_end_date",""))) == '00:00' ) {
						$row['event_period_date_txt'] = mdate('%d %M %Y', strtotime(get_array_value($row,"event_start_date",""))) . ' - ' . mdate('%d %M %Y', strtotime(get_array_value($row,"event_end_date","")));
						$row['event_period_date_txt_2_lines'] = mdate('%d %M %Y', strtotime(get_array_value($row,"event_start_date",""))) . ' to <br/>' . mdate('%d %M %Y', strtotime(get_array_value($row,"event_end_date","")));
					}
					else {
						$row['event_period_date_txt'] = mdate('%d %M %Y  %H:%i', strtotime(get_array_value($row,"event_start_date",""))) . ' - ' . mdate('%d %M %Y  %H:%i', strtotime(get_array_value($row,"event_end_date","")));
						$row['event_period_date_txt_2_lines'] = mdate('%d %M %Y  %H:%i', strtotime(get_array_value($row,"event_start_date",""))) . ' to <br/>' . mdate('%d %M %Y  %H:%i', strtotime(get_array_value($row,"event_end_date","")));
					}
				}

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
				
				$news_title = get_array_value($row,"title","");
				$row["short_title"] = getShortString($news_title,"35");
				$row["short_title_focus"] = getShortString($news_title,"70");
				
				$description = get_array_value($row,"description","");
				$row["short_description"] = getShortString(strip_tags($description),"300");
				$row["short_description_highlight"] = getShortString(strip_tags($description),"150");
				
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");
				
				$row["cover_image_thumb"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-thumb'.get_array_value($row,"cover_image_file_type","");
				$row["cover_image_thumb_sq"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-thumb-sq'.get_array_value($row,"cover_image_file_type","");
				$row["cover_image_big_thumb"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-big-thumb'.get_array_value($row,"cover_image_file_type","") ;
				$row["cover_image_actual"] = get_array_value($row,"upload_path","").get_array_value($row,"cid","").'-actual'.get_array_value($row,"cover_image_file_type","") ;
				
				$event_start_date_txt = get_datetime_pattern("d M Y, H:i",get_array_value($row,"event_start_date",""),"");
				if(is_blank($event_start_date_txt)){
					$event_start_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"created_date",""),"");
				}
				$row["event_start_date_txt"] = $event_start_date_txt;

				$event_end_date_txt = get_datetime_pattern("d M Y, H:i",get_array_value($row,"event_end_date",""),"");
				if(is_blank($event_end_date_txt)){
					$event_end_date_txt = '';
				}
				$row["event_end_date_txt"] = $event_end_date_txt;

				$row['has_passed'] = false;
				if (!empty($row['event_start_date']) && !empty($row['event_end_date'])) {
					if (time() > strtotime($row['event_start_date'])) {
						$row['has_passed'] = true;
					}
				}

				$row["is_invited"] = false;
				$row["has_action"] = false;
				$row["has_joined"] = false;
				$row["has_joined_txt"] = '';
				if ($row['activity_join_user_aid'] > 0) {
					$row["is_invited"] = true;
					if ($row['activity_join_has_joined'] == '1') {
						$row["has_action"] = true;
						$row["has_joined"] = true;
						$row["has_joined_txt"] = 'Going';
						$row["has_joined_txt_long"] = 'You\'re going';
					}
					else if ($row['activity_join_has_joined'] == '0') {
						$row["has_action"] = true;
						$row["has_joined"] = false;
						$row["has_joined_txt"] = 'Not Going';
						$row["has_joined_txt_long"] = 'You\'re not going';
					}
				}

					
				$row['txt_total_join'] = ($row['total_join'] > 0 ? $row['total_join'].' people join this event' : 'No one join this event.');
			
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

	function update_total_join($aid){
		if ($aid > 0) {
			$_sql = 'UPDATE '.$this->get_table_name().' 
				SET total_join =  (SELECT count(user_aid) FROM '.$this->tbl_event_user_activity_join.' WHERE event_aid = "'.$aid.'" AND has_joined = "1")
				WHERE aid = "'.$aid.'"';
			return $this->db->query($_sql);
		}
		return 0;
	}
	
	function get_total_activity($aid){
		$this->set_where(array('aid' => $aid));
		$result = $this->load_record(false);
		$return = array(
				'total_join' => get_array_value($result, 'total_join', 0)
			);
		return $return;
	}

	
}

/* End of file event_model.php */
/* Location: ./system/application/model/event_model.php */