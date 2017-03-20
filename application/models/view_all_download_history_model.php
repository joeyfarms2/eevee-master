<?php
class View_all_download_history_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_all_download_history");
	}

	function fetch_data_with_desc($query)
	{
		$result = "";
		// echo "<br>sql : ".$this->db->last_query();
		if($query->num_rows() > 0){
			$this->db->flush_cache();
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;

				$row["user_info_show"] = get_user_info($row);

				$row["updated_date_txt"] = get_datetime_pattern("d/m/Y H:i:s",get_array_value($row,"updated_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function update_data(){
		// header('Content-Type: text/html; charset=utf-8');
		$_sql = 'TRUNCATE TABLE view_all_download_history';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, user_aid, copy_aid, parent_aid, product_type_aid, product_type_cid, is_license, action, updated_date, updated_by, parent_title, product_main_name, product_main_url, first_name_th, last_name_th, email';

		$_sql = '
		INSERT INTO view_all_download_history
		('.$columns.')
		SELECT sh.aid, sh.user_aid, sh.copy_aid, sh.parent_aid, sh.product_type_aid, sh.product_type_cid, sh.is_license, sh.action, sh.updated_date, sh.updated_by, b.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.first_name_th, u.last_name_th, u.email
		FROM shelf_history sh
		LEFT JOIN book b ON sh.parent_aid = b.aid
		LEFT JOIN product_main pm ON b.product_main_aid = pm.aid
		LEFT JOIN user u ON sh.user_aid = u.aid
		WHERE sh.product_type_aid = "1" and action = "in"
		UNION
		SELECT sh.aid, sh.user_aid, sh.copy_aid, sh.parent_aid, sh.product_type_aid, sh.product_type_cid, sh.is_license, sh.action, sh.updated_date, sh.updated_by, m.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.first_name_th, u.last_name_th, u.email
		FROM shelf_history sh
		LEFT JOIN magazine m ON sh.parent_aid = m.aid
		LEFT JOIN product_main pm ON m.product_main_aid = pm.aid
		LEFT JOIN user u ON sh.user_aid = u.aid
		WHERE sh.product_type_aid = "2" and action = "in"
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);
		
	}

	function update_data_save(){
		// header('Content-Type: text/html; charset=utf-8');
		$_sql = 'TRUNCATE TABLE view_all_download_history';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, user_aid, copy_aid, parent_aid, product_type_aid, product_type_cid, is_license, action, updated_date, updated_by, parent_title, product_main_name, product_main_url, first_name_th, last_name_th, email';

		$_sql = '
		INSERT INTO view_all_download_history
		('.$columns.')
		SELECT sh.aid, sh.user_aid, sh.copy_aid, sh.parent_aid, sh.product_type_aid, sh.product_type_cid, sh.is_license, sh.action, sh.updated_date, sh.updated_by, b.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.first_name_th, u.last_name_th, u.email
		FROM shelf_history sh
		LEFT JOIN book b ON sh.parent_aid = b.aid
		LEFT JOIN product_main pm ON b.product_main_aid = pm.aid
		LEFT JOIN user u ON sh.user_aid = u.aid
		WHERE sh.product_type_aid = "1" and action = "in"
		UNION
		SELECT sh.aid, sh.user_aid, sh.copy_aid, sh.parent_aid, sh.product_type_aid, sh.product_type_cid, sh.is_license, sh.action, sh.updated_date, sh.updated_by, m.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.first_name_th, u.last_name_th, u.email
		FROM shelf_history sh
		LEFT JOIN magazine m ON sh.parent_aid = m.aid
		LEFT JOIN product_main pm ON m.product_main_aid = pm.aid
		LEFT JOIN user u ON sh.user_aid = u.aid
		WHERE sh.product_type_aid = "2" and action = "in"
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);
		
	}


}

/* End of file view_all_products_model.php */
/* Location: ./system/application/model/view_all_products_model.php */