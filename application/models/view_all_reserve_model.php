<?php
class View_all_reserve_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("view_all_reserve");
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
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y H:i:s",get_array_value($row,"created_date",""),"");
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function update_data(){
		// header('Content-Type: text/html; charset=utf-8');
		$_sql = 'TRUNCATE TABLE view_all_reserve';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, user_aid, copy_aid, parent_aid, product_type_aid, product_type_cid, created_date, created_by, updated_date, updated_by,parent_title, product_main_name, product_main_url, username, first_name_th, last_name_th, email';

		$_sql = '
		INSERT INTO view_all_reserve
		('.$columns.')
		SELECT r.aid, r.user_aid, r.copy_aid, r.parent_aid, r.product_type_aid, r.product_type_cid, r.created_date, r.created_by, r.updated_date, r.updated_by, b.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.username, u.first_name_th, u.last_name_th, u.email
		FROM reserve r
		LEFT JOIN book b ON r.parent_aid = b.aid
		LEFT JOIN product_main pm ON b.product_main_aid = pm.aid
		LEFT JOIN user u ON r.user_aid = u.aid
		WHERE r.product_type_aid = "1"
		UNION
		SELECT r.aid, r.user_aid, r.copy_aid, r.parent_aid, r.product_type_aid, r.product_type_cid, r.created_date, r.created_by, r.updated_date, r.updated_by, m.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.username, u.first_name_th, u.last_name_th, u.email
		FROM reserve r
		LEFT JOIN magazine m ON r.parent_aid = m.aid
		LEFT JOIN product_main pm ON m.product_main_aid = pm.aid
		LEFT JOIN user u ON r.user_aid = u.aid
		WHERE r.product_type_aid = "2"
		';
		echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);
		
	}

	function update_data_save(){
		// header('Content-Type: text/html; charset=utf-8');
		$_sql = 'TRUNCATE TABLE view_all_reserve';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);

		$columns = 'aid, user_aid, copy_aid, parent_aid, product_type_aid, product_type_cid, created_date, created_by, updated_date, updated_by,parent_title, product_main_name, product_main_url, username, first_name_th, last_name_th, email';

		$_sql = '
		INSERT INTO view_all_reserve
		('.$columns.')
		SELECT r.aid, r.user_aid, r.copy_aid, r.parent_aid, r.product_type_aid, r.product_type_cid, r.created_date, r.created_by, r.updated_date, r.updated_by, b.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.username, u.first_name_th, u.last_name_th, u.email
		FROM reserve r
		LEFT JOIN book b ON r.parent_aid = b.aid
		LEFT JOIN product_main pm ON b.product_main_aid = pm.aid
		LEFT JOIN user u ON r.user_aid = u.aid
		WHERE r.product_type_aid = "1"
		UNION
		SELECT r.aid, r.user_aid, r.copy_aid, r.parent_aid, r.product_type_aid, r.product_type_cid, r.created_date, r.created_by, r.updated_date, r.updated_by, m.title as parent_title, pm.name as product_main_name, pm.url as product_main_url, u.username, u.first_name_th, u.last_name_th, u.email
		FROM reserve r
		LEFT JOIN magazine m ON r.parent_aid = m.aid
		LEFT JOIN product_main pm ON m.product_main_aid = pm.aid
		LEFT JOIN user u ON r.user_aid = u.aid
		WHERE r.product_type_aid = "2"
		';
		//echo "sql : $_sql<BR>";
		$query = $this->db->query($_sql);
		
	}

}

/* End of file view_all_reserve_model.php */
/* Location: ./system/application/model/view_all_reserve_model.php */