<?php

require_once(APPPATH."models/product_init_model.php");

class Transaction_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->set_table_name("transaction");
		
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
				// switch($row["status"]){
				// 	case "1" : $row["status_name"] = "Active"; break;
				// 	case "0" : $row["status_name"] = "Inactive"; break;
				// 	default : $row["status_name"] = "N/A";	 break;
				// }

				$row["full_name_th"] = trim(get_array_value($row,"first_name_th","")." ".get_array_value($row,"last_name_th",""));

				$row["user_info"] = get_user_info($row);

				$row["created_date_txt"] = get_datetime_pattern("Y-m-d H:i",get_array_value($row,"created_date",""),"");
				$row["borrowing_date_txt"] = get_datetime_pattern("Y-m-d H:i",get_array_value($row,"borrowing_date",""),"");
				$row["borrowing_date_only_txt"] = get_datetime_pattern("Y-m-d",get_array_value($row,"borrowing_date",""),"");
				$row["returning_date_txt"] = get_datetime_pattern("Y-m-d H:i",get_array_value($row,"returning_date",""),"");
				$row["returning_date_only_txt"] = get_datetime_pattern("Y-m-d",get_array_value($row,"returning_date",""),"");
				$row["due_date_txt"] = get_datetime_pattern("Y-m-d",get_array_value($row,"due_date",""),"");


				$result[] = $row;
			}
		}
		return $result;
	}

	function get_transaction_detail($obj=""){
		$_sql = '
						SELECT transaction.*
						FROM transaction 
						WHERE 1 = 1 
		';

		$user_aid = get_array_value($obj,"user_aid","");
		if(is_number_no_zero($user_aid)){
			$_sql .= ' AND transaction.user_aid = "'.$user_aid.'"';
		}

		$copy_aid = get_array_value($obj,"copy_aid","");
		if(is_number_no_zero($copy_aid)){
			$_sql .= ' AND transaction.copy_aid = "'.$copy_aid.'"';
		}

		$product_type_aid = get_array_value($obj,"product_type_aid","");
		if(is_number_no_zero($product_type_aid)){
			$_sql .= ' AND transaction.product_type_aid = "'.$product_type_aid.'"';
		}

		$status = get_array_value($obj,"status","");
		if(!is_blank($status)){
			$_sql .= ' AND transaction.status = "'.$status.'"';
		}

		$sort_by = get_array_value($obj,"sort_by","");
		if($sort_by == 'date_d'){
			$_sql .= ' ORDER BY return_status asc, transaction.due_date DESC';
		}else if($sort_by == 'date_a'){
			$_sql .= ' ORDER BY return_status asc, transaction.due_date ASC';
		}else if($sort_by == 'name_d'){
			$_sql .= ' ORDER BY return_status asc, parent.title DESC';
		}else if($sort_by == 'name_a'){
			$_sql .= ' ORDER BY return_status asc, parent.title ASC';
		}

		$optional = get_array_value($obj,"optional","");
		$record_per_page = get_array_value($optional,"record_per_page","");
		if(is_var_array($optional)){
			// $this->transaction->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",$record_per_page));
			$_sql .= ' LIMIT '.get_array_value($optional,"start_record","0").', '.get_array_value($optional,"search_record_per_page",$record_per_page);
		}

		// echo "sql = $_sql <HR />";
		$query = $this->db->query($_sql);
		$result = $this->fetch_data_with_desc($query);
		// print_r($result);
		$last_result = "";
		if(is_var_array($result)){
			foreach ($result as $item) {
				$product_type_aid = get_array_value($item,"product_type_aid","");
				$parent_aid = get_array_value($item,"parent_aid","");
				$copy_aid = get_array_value($item,"copy_aid","");
				$model = $this->get_product_model($product_type_aid);
				$model_name = get_array_value($model,"product_model","");
				$model_copy_name = get_array_value($model,"product_copy_model","");
				$this->db->flush_cache();
				$this->db->_reset_select();

				$this->load->model($model_copy_name, $model_copy_name);
				$tmp = array();
				$tmp['aid'] = $copy_aid;
				$this->{$model_copy_name}->set_where($tmp);
				$copy_result = $this->{$model_copy_name}->load_record(true);
				$parent_aid = get_array_value($copy_result,"parent_aid","");
				$item["parent_aid"] = get_array_value($copy_result,"parent_aid","");
				$item["copy_cid"] = get_array_value($copy_result,"cid","");
				$item["barcode"] = get_array_value($copy_result,"barcode","");
				$item["upload_path"] = get_array_value($copy_result,"upload_path","");
				$item["file_upload"] = get_array_value($copy_result,"file_upload","");
				$item["copy_publish_date"] = get_array_value($copy_result,"publish_date","");
				$item["copy_created_date"] = get_array_value($copy_result,"created_date","");
				$item["copy_updated_date"] = get_array_value($copy_result,"updated_date","");

				$this->db->flush_cache();
				$this->db->_reset_select();
				$this->load->model($model_name, $model_name);
				$tmp = array();
				$tmp['aid'] = $parent_aid;
				$this->{$model_name}->set_where($tmp);
				$product_result = $this->{$model_name}->load_record(true);
				$item["parent_title"] = get_array_value($product_result,"title","");
				$item["parent_author"] = get_array_value($product_result,"author","");
				$item["parent_publish_date"] = get_array_value($product_result,"publish_date","");
				$item["parent_created_date"] = get_array_value($product_result,"created_date","");
				$item["parent_updated_date"] = get_array_value($product_result,"updated_date","");
				$item["cover_image_small"] = get_array_value($product_result,"cover_image_small","");
				$item["cover_image_thumb"] = get_array_value($product_result,"cover_image_thumb","");
				$item["cover_image_ipad"] = get_array_value($product_result,"cover_image_ipad","");
				$item["product_type_cid"] = get_array_value($product_result,"product_type_cid","");
				$last_result[] = $item;
			}
		}

		return $last_result;
	}

}

/* End of file transaction_model.php */
/* Location: ./system/application/model/transaction_model.php */