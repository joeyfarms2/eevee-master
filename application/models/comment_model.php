<?php
class Comment_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("comment");
		$this->comment_model = "Comment_model";
		
		$this->tbl_issue_name = "issue";
		$this->tbl_user_name = "user";
		
		
	}
	
	function load_home_comment(){
		$tmp = array();
		$tmp["status"] = "1";
		$this->set_where($tmp);
		$this->set_order_by("created_date DESC");
		$this->set_limit(0,6);
		$result = $this->load_records(true);
		return $result;
	}
		
	function set_join_for_desc($obj=""){
		$this->db->select('comment.*,user.aid as user_aid, user.username, user.first_name_th, user.last_name_th, user.display_name, user.avatar_path, user.avatar_type, user.gender, user.email  ');
		$this->db->join($this->tbl_user_name.' AS user', 'comment.created_by = user.aid', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				// echo "parent = ".get_array_value($row,"parent_aid","");
				if(get_array_value($row,"parent_aid","") == 0){
					$this->db->flush_cache();
					$CI =& get_instance();
					$CI->load->model($this->comment_model,"comment");
					$tmp = array();
					// $tmp['issue_aid'] = $issue_aid;
					$tmp['parent_aid'] = get_array_value($row,"aid","");
					if(!exception_about_status()) { $tmp['status'] = '1'; }
					$CI->comment->set_where($tmp);
					$CI->comment->set_order_by('aid ASC');
					$child = $CI->comment->load_records(true);		
					// echo "<br>sql : ".$CI->db->last_query();
					if(is_var_array($child)){
						$row["child"] = $child;
					}
					
				}
				
				
				$tmp = array();
				$tmp["avatar_path"] = get_array_value($row,"avatar_path","");
				$tmp["aid"] = get_array_value($row,"user_aid","");
				$tmp["avatar_type"] = get_array_value($row,"avatar_type","");
				$tmp["gender"] = get_array_value($row,"gender","m");
				$row["avatar_image"] = get_user_avatar($tmp);
				
				$tmp = array();
				$tmp["display_name"] = get_array_value($row,"display_name","");
				$tmp["username"] = get_array_value($row,"username","");
				$tmp["email"] = get_array_value($row,"email","");
				$row["user_info"] = get_user_info($tmp);
				
				$user_name_show = "";
				$row["created_date_txt"] = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"created_date",""),"");
				$row["time_left"] = get_time_left(get_array_value($row,"created_date",""));
				
				$result[] = $row;
			}
		}
		return $result;
	}
	
	
}

/* End of file comment_model.php */
/* Location: ./system/application/model/comment_model.php */