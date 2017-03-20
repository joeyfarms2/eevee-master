<?php
class Copy_download_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("copy_download");
		$this->tbl_issue_name = "issue";
		$this->tbl_publisher_name = "publisher";
		
	}
	
	function set_join_for_desc($obj=""){

	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");				
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function set_join_for_desc_by_issue()
	{
		$this->db->select('copy_download.*, issue.name');
		$this->db->join($this->tbl_issue_name.' AS issue', $this->get_table_name().'.issue_aid = issue.aid', "left");
	}
	function load_count_records_by_issue()
	{
		$this->set_join_for_desc_by_issue();
		$this->db->select('SUM(copy_download.issue_price) AS total_issue_price, count(*) as total_download');
		$this->db->group_by($this->get_table_name().'.issue_aid');
		$query = $this->db->get($this->tbl_name);
		//echo "<br>sql : ".$this->db->last_query();
		$result =  $this->fetch_data($query);
		return count($result);
	}
	function load_records_all_by_issue()
	{
		$this->set_join_for_desc_by_issue();
		$query = $this->db->get($this->tbl_name);
		//echo "<br>sql : ".$this->db->last_query();
		$result =  $this->fetch_data_with_desc_by_issue($query);
		return $result;
	
	}
	function load_records_by_issue()
	{
		$this->set_join_for_desc_by_issue();
		$this->db->select('SUM(copy_download.issue_price) AS total_issue_price, count(*) as total_download');
		$this->db->group_by($this->get_table_name().'.issue_aid');
		$query = $this->db->get($this->tbl_name);
		//echo "<br>sql : ".$this->db->last_query();
		$result =  $this->fetch_data_with_desc_by_issue($query);
		return $result;
	
	}
	function fetch_data_with_desc_by_issue($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");
				
				$full_issue_name = get_array_value($row,"name","");
				$row["full_issue_name"] = $full_issue_name;
				$result[] = $row;
			}
		}
		return $result;
	}

	function set_join_for_desc_by_publisher()
	{
		$this->db->select('copy_download.*, publisher.name as publisher_name, publisher.aid as publisher_aid');
		$this->db->join($this->tbl_issue_name.' AS issue', $this->get_table_name().'.issue_aid = issue.aid', "left");
		$this->db->join($this->tbl_publisher_name.' AS publisher', 'issue.publisher_aid = publisher.aid', "left");
	}
	function load_count_records_by_publisher()
	{
		$this->db->select('SUM(copy_download.issue_price) AS total_issue_price, count(*) as total_download');
		$this->db->group_by('issue.publisher_aid');
		$this->set_join_for_desc_by_publisher();
		$query = $this->db->get($this->tbl_name);
		// echo "<br>sql : ".$this->db->last_query();
		$result =  $this->fetch_data($query);
		return count($result);
	}
	function load_records_all_by_publisher()
	{
		// echo "start_record = $start_record , search_record_per_page = $search_record_per_page <BR>";
		$this->set_join_for_desc_by_publisher();
		$query = $this->db->get($this->tbl_name);
		// echo "<hr>sql : ".$this->db->last_query();
		$result =  $this->fetch_data_with_desc_by_publisher($query);
		return $result;
	}
	function load_records_by_publisher()
	{
		$this->db->select('SUM(copy_download.issue_price) AS total_issue_price, count(*) as total_download');
		$this->db->group_by('issue.publisher_aid');
		$query = $this->db->get($this->tbl_name);
		// echo "<hr>sql : ".$this->db->last_query();
		$result =  $this->fetch_data_with_desc_by_publisher($query);
		return $result;
	}
	function fetch_data_with_desc_by_publisher($query)
	{
		$result = "";
		$all_type_currency = array();
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$row["created_date_txt"] = get_datetime_pattern("d/m/Y",get_array_value($row,"created_date",""),"");
				$result[] = $row;
				// echo "<hr>";
			}
		}
		return $result;
	}

	
	
}

/* End of file copy_download_model.php */
/* Location: ./system/application/model/copy_download_model.php */