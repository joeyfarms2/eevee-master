<?php

require_once(APPPATH."models/product_init_model.php");

class Shelf_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("shelf");

	}
	
	function set_join_for_desc($obj=""){

	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();			

			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				switch($row["status"]){
					case "1" : $row["status_name"] = "Active"; break;
					case "0" : $row["status_name"] = "Inactive"; break;
					default : $row["status_name"] = "N/A";	 break;
				}
				$product_type_aid = get_array_value($row,"product_type_aid","");
				$parent_aid = get_array_value($row,"parent_aid","");

				$model = $this->get_product_model($product_type_aid);
				$model_name = get_array_value($model,"product_model","");
				$this->db->flush_cache();
				$this->db->_reset_select();
				$this->load->model($model_name, $model_name);
				$tmp = array();
				$tmp['aid'] = $parent_aid;
				$this->{$model_name}->set_where($tmp);
				$product_result = $this->{$model_name}->load_record(true);
				// print_r($product_result);
				// echo "<br>sql : ".$this->db->last_query()."<BR>";
				$title = get_array_value($product_result,"title","N/A");
				$row["title"] = $title;
				$row["welcome_msg"] = get_array_value($product_result,"welcome_msg","");
				//$row["product_main_aid"] = get_array_value($product_result,"product_main_aid","");
				$row["cover_image_small"] = get_array_value($product_result,"cover_image_small","");
				$row["cover_image_thumb"] = get_array_value($product_result,"cover_image_thumb","");
				$row["cover_image_ipad"] = get_array_value($product_result,"cover_image_ipad","");

				$row["cover_image"] = get_array_value($product_result,"cover_image","");
				$row["large_image"] = get_array_value($product_result,"large_image","");
				$row["thumbnail_image"] = get_array_value($product_result,"thumbnail_image","");

				$row["parent_publish_date_txt"] = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"parent_publish_date",""),"");

				$result[] = $row;
			}
		}
		return $result;
	}
	
	function is_buy($user_aid="",$copy_aid=""){
		$this->set_where(array("user_aid"=>$user_aid, "copy_aid"=>$copy_aid));
		$result = $this->load_record(false);
		if(is_var_array($result)){
			return true;
		}else{
			return false;
		}
	}
	
	function delete_expired_shelf(){

		$_sql = "
			INSERT INTO shelf_history (user_aid, copy_aid, parent_aid, product_type_aid, product_type_cid, is_license, action, status, updated_date, updated_by)
			SELECT user_aid, copy_aid, parent_aid, product_type_aid, product_type_cid, is_license, 'de', '1', '".get_db_now()."', '1'
			FROM shelf WHERE expiration_date IS NOT NULL AND expiration_date != '0000-00-00' AND expiration_date <= '".date('Y-m-d')."'	
		";
		// echo "$_sql";
		$q = $this->db->query($_sql);

		$_sql = "
			DELETE FROM shelf WHERE expiration_date IS NOT NULL AND expiration_date != '0000-00-00' AND expiration_date <= '".date('Y-m-d')."'	
		";
		// echo "$_sql";
		return $this->db->query($_sql);
	}

	function get_shelf_detail($obj=""){
		$_sql = '
						SELECT shelf.user_aid, 
						shelf.copy_aid, 
						shelf.product_type_aid, 
						shelf.product_type_cid, 
						shelf.parent_aid, 
						shelf.status, 
						shelf.is_license, 
						shelf.is_read, 
						shelf.expiration_date, 
						shelf.ip, 
						shelf.created_date, 
						shelf.created_by, 
						shelf.updated_date, 
						shelf.updated_by, 
						parent.title AS parent_title, 
						parent.author AS parent_author, 
						parent.publish_date as parent_publish_date, 
						parent.created_date as parent_created_date, 
						parent.updated_date as parent_updated_date, 
						copy.cid as copy_cid, 
						copy.barcode, 
						copy.upload_path, 
						copy.file_upload, 
						copy.publish_date as copy_publish_date, 
						copy.created_date as copy_created_date, 
						copy.updated_date as copy_updated_date 
						FROM shelf 
						INNER JOIN 
						(SELECT aid as parent_aid, author, product_type_aid, title, publish_date, created_date, updated_date 
						FROM book 
						UNION 
						SELECT aid as parent_aid, author, product_type_aid, title, publish_date, created_date, updated_date 
						FROM magazine ) parent ON shelf.parent_aid = parent.parent_aid AND shelf.product_type_aid = parent.product_type_aid

						INNER JOIN 
						(SELECT aid, cid, product_type_aid, barcode, upload_path, file_upload, publish_date, created_date, updated_date 
						FROM book_copy 
						UNION 
						SELECT aid, cid, product_type_aid, barcode, upload_path, file_upload, publish_date, created_date, updated_date 
						FROM magazine_copy ) copy ON shelf.copy_aid = copy.aid AND shelf.product_type_aid = copy.product_type_aid

						WHERE 1 = 1 
		';

		$user_aid = get_array_value($obj,"user_aid","");
		if(is_number_no_zero($user_aid)){
			$_sql .= ' AND shelf.user_aid = "'.$user_aid.'"';
		}

		$copy_aid = get_array_value($obj,"copy_aid","");
		if(is_number_no_zero($copy_aid)){
			$_sql .= ' AND shelf.copy_aid = "'.$copy_aid.'"';
		}

		$product_type_aid = get_array_value($obj,"product_type_aid","");
		if(is_number_no_zero($product_type_aid)){
			$_sql .= ' AND shelf.product_type_aid = "'.$product_type_aid.'"';
		}

		$status = get_array_value($obj,"status","");
		if(!is_blank($status)){
			$_sql .= ' AND shelf.status = "'.$status.'"';
		}

		$sort_by = get_array_value($obj,"sort_by","");
		if($sort_by == 'date_d'){
			$_sql .= ' ORDER BY shelf.created_date DESC';
		}else if($sort_by == 'date_a'){
			$_sql .= ' ORDER BY shelf.created_date ASC';
		}else if($sort_by == 'name_d'){
			$_sql .= ' ORDER BY parent.title DESC';
		}else if($sort_by == 'name_a'){
			$_sql .= ' ORDER BY parent.title ASC';
		}else{
			$_sql .= ' ORDER BY shelf.created_date DESC';
		}

		$optional = get_array_value($obj,"optional","");
		$record_per_page = get_array_value($optional,"record_per_page","");
		if(is_var_array($optional)){
			// $this->shelf->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",$record_per_page));
			$_sql .= ' LIMIT '.get_array_value($optional,"start_record","0").', '.get_array_value($optional,"search_record_per_page",$record_per_page);
		}

		// echo "sql = $_sql <HR />";
		$query = $this->db->query($_sql);
		$result = $this->fetch_data_with_desc($query);
		//print_r($result);
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
				$this->load->model($model_copy_name, $model_copy_name);
				$tmp = array();
				$tmp['aid'] = $copy_aid;
				$this->{$model_copy_name}->set_where($tmp);
				$copy_result = $this->{$model_copy_name}->load_record(true);
				$item["copy_cid"] = get_array_value($copy_result,"cid","");
				$item["barcode"] = get_array_value($copy_result,"barcode","");
				$item["upload_path"] = get_array_value($copy_result,"upload_path","");
				$item["file_upload"] = get_array_value($copy_result,"file_upload","");
				$item["copy_publish_date"] = get_array_value($copy_result,"publish_date","");
				$item["copy_created_date"] = get_array_value($copy_result,"created_date","");
				$item["copy_updated_date"] = get_array_value($copy_result,"updated_date","");
				$last_result[] = $item;
			}
		}

		return $last_result;
	}


}

/* End of file shelf_model.php */
/* Location: ./system/application/model/shelf_model.php */