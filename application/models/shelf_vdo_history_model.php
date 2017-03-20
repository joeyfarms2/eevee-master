<?php
class Shelf_vdo_history_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("shelf_vdo_history");
		$this->tbl_shelf_vdo_name = "shelf_vdo";
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
				$result[] = $row;
			}
		}
		return $result;
	}
	function get_shelf_history_detail($obj=""){

		$user_aid = get_array_value($obj,"user_aid","");
		$sql = 'SELECT * FROM shelf_vdo_history WHERE user_aid = "'.$user_aid.'" AND action  = "in" GROUP BY parent_aid, product_type_aid ORDER BY updated_date DESC';
		$result = mysql_query($sql);
		$myresult = array();
		while($row = mysql_fetch_array($result))
			{
				$s_1 = 'SELECT * FROM shelf_vdo_history WHERE user_aid = "'.$row["user_aid"].'"  AND parent_aid = "'.$row["parent_aid"].'" AND product_type_aid = "'.$row["product_type_aid"].'" ';
				$re_1 = mysql_query($s_1);
				$count_1 = ceil(mysql_num_rows($re_1)/2.); 
			
			
			
				$sqlx="SELECT * FROM  shelf_vdo WHERE user_aid = '".$row['user_aid']."' and parent_aid = '".$row['parent_aid']."' and product_type_aid = '".$row['product_type_aid']."' ";
				$exex=mysql_query($sqlx);

				
				 	$sqlbook="SELECT * FROM vdo WHERE aid = '".$row['parent_aid']."' ";
				 	$exebook=mysql_query($sqlbook);
				 	$databook=mysql_fetch_array($exebook);
				
				
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
					//$obj["copy_aid"] = $row['copy_aid'];
					$obj["type"] = $row['product_type_cid'];
					$obj["total_borrow"] = $count_1;
					$obj["copy_file_upload"] = "";
					$myresult[] = $obj;
				
				}else{
					//no book in shelf
					$sqlb = "SELECT * FROM shelf_vdo_history WHERE user_aid = '".$row['user_aid']."' AND parent_aid = '".$row['parent_aid']."' AND product_type_aid = '".$row['product_type_aid']."' AND action  NOT LIKE 'in' ORDER BY  updated_date DESC ";

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
					//$obj["copy_aid"] = $row['copy_aid'];
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