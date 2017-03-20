<?php
class Reserve_product_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("reserve_product");
		
		$this->tbl_user_name = 'user';
	}

	function set_join_for_desc($obj="") {
		$this->db->select($this->tbl_name.'.*, user.cid as user_cid, user.first_name_th, user.last_name_th, user.email, user.avatar_path, user.avatar_type, user.gender, user.department_aid, user.contact_number');
		$this->db->join($this->tbl_user_name.' AS user', $this->tbl_name.'.user_aid = user.aid', "left");
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "3" : 
						$row["status_name"] = "Delivered";
						$row["status_icon"] = '<i class="fa fa-big fa-check-circle status-3" title="Delivered"></i>';
						break;
					case "2" : 
						$expiration_date = get_datetime_pattern("Y-m-d",get_array_value($row,"expiration_date",""),"");
						$today = date("Y-m-d");
						if($expiration_date == $today){
							$row["status_icon"] = '<i class="fa fa-big fa-bell status-3" title="Confirm reservation : Expire within today."></i>';
						}else if($expiration_date > $today){
							$row["status_icon"] = '<i class="fa fa-big fa-bell status-1" title="Approved : Expire on '.$expiration_date.'"></i>';
						}else{
							$row["status_icon"] = '<i class="fa fa-big fa-bell status-4" title="Overdue reservation : Expire on '.$expiration_date.'"></i>';
						}
						$row["status_name"] = "Approved";
						break;
					case "1" : 
						$row["status_name"] = "Reserved";
						$row["status_icon"] = '<i class="fa fa-big fa-clock-o status-0" title="On queue"></i>';
						break;
					case "0" : 
						$row["status_name"] = "Canceled";
						$row["status_icon"] = '<i class="fa fa-big fa-times-circle status-4" title="Canceled"></i>';
						break;
					default : 
						$row["status_name"] = "N/A";
						$row["status_icon"] = "N/A";
						break;
				}

				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));

				$row["user_info"] = get_user_info($row);

				$row["created_date_txt"] = get_datetime_pattern("Y-m-d H:i",get_array_value($row,"created_date",""),"");
				$row["created_date_only_txt"] = get_datetime_pattern("Y-m-d",get_array_value($row,"created_date",""),"");
				$row["updated_date_txt"] = get_datetime_pattern("Y-m-d H:i",get_array_value($row,"updated_date",""),"");
				$row["updated_date_only_txt"] = get_datetime_pattern("Y-m-d",get_array_value($row,"updated_date",""),"");
				$row["expiration_date_txt"] = get_datetime_pattern("Y-m-d",get_array_value($row,"expiration_date",""),"");

				$result[] = $row;
			}
		}
		return $result;
	}

}

/* End of file reserve_product_model.php */
/* Location: ./system/application/model/reserve_product_model.php */