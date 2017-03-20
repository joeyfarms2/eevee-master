<?php
require_once(APPPATH."models/product_init_model.php");

class Shelf_history_model extends Product_init_model {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("shelf_history");
		$this->tbl_shelf_name = "shelf";
	}
	
	function set_join_for_desc($obj=""){
		
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			
			foreach($query->result_array() as $row){
				// print_r($row);
				$row["num_rows"] = $query->num_rows() ;
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
				// echo "<br>sql : ".$this->db->last_query()."<BR>";
				// print_r($product_result); echo "<HR>";
				$title = get_array_value($product_result,"title","N/A");
				$row["title"] = $title;
				$row["welcome_msg"] = get_array_value($product_result,"welcome_msg","");
				$row["cover_image_small"] = get_array_value($product_result,"cover_image_small","");
				$row["cover_image_thumb"] = get_array_value($product_result,"cover_image_thumb","");
				$row["cover_image_ipad"] = get_array_value($product_result,"cover_image_ipad","");
				$row["product_main_aid"] = get_array_value($product_result,"product_main_aid","");
				$row["product_main_cid"] = get_array_value($product_result,"product_main_cid","");

				$row["parent_publish_date_txt"] = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"parent_publish_date",""),"");

				$result[] = $row;
			}
		}
		return $result;
	}

	function get_shelf_history_detail($obj=""){

		$user_aid = get_array_value($obj,"user_aid","");
		$sql = 'SELECT * FROM shelf_history WHERE user_aid = "'.$user_aid.'" AND action  = "in" GROUP BY copy_aid, parent_aid, product_type_aid ORDER BY updated_date DESC';
		$result = mysql_query($sql);
		$myresult = array();
		while($row = mysql_fetch_array($result))
			{
				$s_1 = 'SELECT * FROM shelf_history WHERE user_aid = "'.$row["user_aid"].'" AND copy_aid = "'.$row["copy_aid"].'" AND parent_aid = "'.$row["parent_aid"].'" AND product_type_aid = "'.$row["product_type_aid"].'" ';
				$re_1 = mysql_query($s_1);
				$count_1 = ceil(mysql_num_rows($re_1)/2.); 
			
			
			
				$sqlx="SELECT * FROM  shelf WHERE user_aid = '".$row['user_aid']."' and parent_aid = '".$row['parent_aid']."' and product_type_aid = '".$row['product_type_aid']."' ";
				$exex=mysql_query($sqlx);

				if($row["product_type_aid"]==1){
				 	$sqlbook="SELECT * FROM book WHERE aid = '".$row['parent_aid']."' ";
				 	$exebook=mysql_query($sqlbook);
				 	$databook=mysql_fetch_array($exebook);
				}else{
				 	$sqlbook="SELECT * FROM magazine WHERE aid = '".$row['parent_aid']."' ";
				 	$exebook=mysql_query($sqlbook);
				 	$databook=mysql_fetch_array($exebook);
				}
				
				if(mysql_num_rows($exex)>0)
				{
					//has book in shelf
					//$obj["msg"] = "success";
					$obj["status"] = "1";
					$obj["title"] = $databook['title'];
					$obj["cover_image_ori"] = "http://".$_SERVER['HTTP_HOST']."/".$databook['upload_path']."cover_image/".$databook['cid']."-ori".$databook['cover_image_file_type'];
					$obj["cover_image_cover"] = "http://".$_SERVER['HTTP_HOST']."/".$databook['upload_path']."cover_image/".$databook['cid']."-cover".$databook['cover_image_file_type'];
					$obj["cover_image_thumb"] = "http://".$_SERVER['HTTP_HOST']."/".$databook['upload_path']."cover_image/".$databook['cid']."-thumb".$databook['cover_image_file_type'];
					
					$obj["borrow"] = $row['updated_date'];
					$obj["return"] = "";
					$obj["status"] = "on shelf";
					$obj["parent_aid"] = $row['parent_aid'];
					$obj["copy_aid"] = $row['copy_aid'];
					$obj["type"] = $row['product_type_cid'];
					$obj["total_borrow"] = $count_1;
					$obj["copy_file_upload"] = "";
					$myresult[] = $obj;
				
				}else{
					//no book in shelf
					$sqlb = "SELECT * FROM shelf_history WHERE user_aid = '".$row['user_aid']."' AND parent_aid = '".$row['parent_aid']."' AND product_type_aid = '".$row['product_type_aid']."' AND action  NOT LIKE 'in' ORDER BY  updated_date DESC ";

					$exeb=mysql_query($sqlb);
					$datab=mysql_fetch_array($exeb);
					
					// $sqlbook="SELECT * FROM book WHERE aid = '".$datab['parent_aid']."' ";
// 					$exebook=mysql_query($sqlbook);
// 					$databook=mysql_fetch_array($exebook);
				
					//$obj["msg"] = "success";
					$obj["status"] = "2";
					$obj["title"] = $databook['title'];
					$obj["cover_image_ori"] = "http://".$_SERVER['HTTP_HOST']."/".$databook['upload_path']."cover_image/".$databook['cid']."-ori".$databook['cover_image_file_type'];
					$obj["cover_image_cover"] = "http://".$_SERVER['HTTP_HOST']."/".$databook['upload_path']."cover_image/".$databook['cid']."-cover".$databook['cover_image_file_type'];
					$obj["cover_image_thumb"] = "http://".$_SERVER['HTTP_HOST']."/".$databook['upload_path']."cover_image/".$databook['cid']."-thumb".$databook['cover_image_file_type'];
					
					$obj["borrow"] = $row['updated_date'];
					if (is_var_array($datab)){
						$obj["return"] = $datab['updated_date'];
					}else{
						$obj["return"] = "";
					}
					
					$obj["status"] = "return";
					$obj["parent_aid"] = $row['parent_aid'];
					$obj["copy_aid"] = $row['copy_aid'];
					$obj["type"] = $row['product_type_cid'];
					$obj["total_borrow"] = $count_1;
					$obj["copy_file_upload"] = "";
					$myresult[] = $obj;
				}
			}
		
		
		//return $obj;
		return $myresult;
	}
}

/* End of file shelf_history_model.php */
/* Location: ./system/application/model/shelf_history_model.php */