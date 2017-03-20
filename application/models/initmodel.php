<?php
class Initmodel extends CI_Model {
	
	var $tbl_name = "";
	var $db_prefix = "";

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->db->cache_delete_all();
		$this->load->helper('date');
		$this->load->helper('array');
		$this->load->helper('common');
		
		$this->load->database();
		$this->db_prefix =  $this->db->dbprefix;
		
	}
	
	function set_table_name($table_name){
		$this->tbl_name = $table_name;
	}
	
	function get_table_name(){
		return $this->db_prefix . $this->tbl_name;
	}
	
	function set_free_result($query){
		if($query) $query->free_result();
	}
			
	function fetch_data($query){
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function fetch_data_with_desc($query){
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$result[] = $row;
			}
		}
		return $result;
	}
	
	function set_join_for_desc($obj=""){
		return "";
	}

	function load_record($fullload=false,$obj=""){
		if($fullload) $this->set_join_for_desc($obj);
		
		$query = $this->db->get($this->tbl_name);
		//echo "<br>sql : ".$this->db->last_query();
		$result =  ($fullload) ? $this->fetch_data_with_desc($query) : $this->fetch_data($query);
		$this->set_free_result($query);
		$results = get_array_value($result,"results","");
		if(is_var_array($results) && count($results) == 1){
			return $results[0];
		}
		if(is_var_array($result) && count($result) == 1){
			return $result[0];
		}else{
			return "";
		}
    }

	function load_records($fullload=false,$obj=""){
		if($fullload) $this->set_join_for_desc($obj);
		$query = $this->db->get($this->tbl_name);
		// echo "<br>sql : ".$this->db->last_query();
		$result =  ($fullload) ? $this->fetch_data_with_desc($query) : $this->fetch_data($query);
		$this->set_free_result($query);
		return $result;
    }
	
	function load_records_by_status($fullload=false,$obj=""){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records($fullload,$obj);
    }
	
	function load_count_records($fullload=false,$obj=""){
		if($fullload) $this->set_join_for_desc($obj);
		$query = $this->db->get($this->tbl_name);
		//echo "<br>sql : ".$this->db->last_query();
		$result =  ($fullload) ? $this->fetch_data_with_desc($query) : $this->fetch_data($query);
		return count($result);
    }
	
	function load_records_array($fullload=false,$f_name_key="",$f_name_value=""){
		$result = $this->load_records($fullload);
		// echo "<br>sql : ".$this->db->last_query();
		$list = "";
		$i=0;
		if(is_var_array($result)){
			foreach($result as $item){
				$i++;
				$key="";
				if(!is_blank($f_name_key)){
					$key = get_array_value($item,$f_name_key,"");
				}else{
					$key = $i;
				}
				if(!is_blank($f_name_value)){
					$list[$key] = get_array_value($item,$f_name_value,"");
				}else{
					$list[$key] = $item;
				}
			}
		}
		return $list;
	}
	
	function load_records_array_by_status($fullload=false,$f_name_key="",$f_name_value=""){
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		return $this->load_records_array($fullload,$f_name_key,$f_name_value);
    }

	function count_records($fullload=false,$obj=""){
		if($fullload) $this->set_join_for_desc($obj);
		$result =   $this->db->count_all_results($this->tbl_name);
		// echo "<br>sql : ".$this->db->last_query();
		return $result;
    }
	
	function set_prefix_for_key($key){
		if(is_blank($key)){
			return "";
		}
		$result = "";
		$keys = explode(",", $key);
		foreach($keys as $item){
			$item = trim($item);
			if(!is_blank($result)) $result .= ', ';
			if(strpos($item, ".") === false && strpos($item, "*") === false){
				$result .=  $this->get_table_name().'.'.$item;
			}else if(strpos($item, "*") === false){
				$result .=  $item;
			}else{
				$result .=  substr($item,1);
			}
		}
		return $result;	
	}
	
	function set_order_by($data=""){
		if( is_var_array($data) ){
			foreach($data as $key => $value){
				$this->db->order_by($this->set_prefix_for_key($key),$value);
			}
		}else if( !is_blank($data) ){
			// $this->db->order_by($data);
			$order_by_option = "";
			if(!strpos($data, " ") === false){
				list($order_by, $order_by_option) = preg_split('/ /', $data, 2);
			}
			if(!is_blank($order_by_option)){
				$arr = explode(",", $order_by);
				foreach($arr as $item){
					$this->db->order_by($this->set_prefix_for_key($item)." ".$order_by_option);
				}
			}else{
				$this->db->order_by($this->set_prefix_for_key($data));
			}
		}
	}

	function set_group_by($data="") {
		if( is_var_array($data) || !is_blank($data)) {
			$this->db->group_by($data);
		}
	}
	
	//return : AND ( tbl_name.tbl_field_1 = 'val1' AND tbl_name.tbl_field_2 = 'val2' )
	function set_where($data,$value=""){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " AND ";
				if(is_var_array($value)){
					$where_str .= " ( ";
					$j=0;
					foreach($value as $txt){
						if($j != 0) $where_str .= " AND ";
						$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($txt)."' ";
						$j++;
					}
					$where_str .= " ) ";
				}else{
					$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($value)."' ";
				}
				$i++;
				//$this->db->where($key,$value);
			}
			$where_str .= " )";
			// echo "<br>where_str = ".htmlentities($where_str)."<HR>";
			$this->db->where($where_str);
		}else if( !is_blank($data) ){
			$this->db->where($data,$value);
		}
	}
	
	//return : AND ( tbl_name.tbl_field_1 = 'val1' OR tbl_name.tbl_field_2 = 'val2' )
	function set_and_or_where($data,$value=""){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " OR ";
				if(is_var_array($value)){
					$where_str .= " ( ";
					$j=0;
					foreach($value as $txt){
						if($j != 0) $where_str .= " OR ";
						$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($txt)."' ";
						$j++;
					}
					$where_str .= " ) ";
				}else{
					$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($value)."' ";
				}
				$i++;
				//$this->db->where($key,$value);
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}else if( !is_blank($data) ){
			$this->db->where($data,$value);
		}
	}
	
	//return : OR ( tbl_name.tbl_field_1 = 'val1' OR tbl_name.tbl_field_2 = 'val2' )
	function set_or_where($data,$value=""){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " OR ";
				if(is_var_array($value)){
					$where_str .= " ( ";
					$j=0;
					foreach($value as $txt){
						if($j != 0) $where_str .= " OR ";
						$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($txt)."' ";
						$j++;
					}
					$where_str .= " ) ";
				}else{
					$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($value)."' ";
				}
				$i++;
				//$this->db->where($key,$value);
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}else if( !is_blank($data) ){
			$this->db->or_where($data,$value);
		}
	}

	//return : OR ( tbl_name.tbl_field_1 = 'val1' AND tbl_name.tbl_field_2 = 'val2' )
	function set_or_and_where($data,$value=""){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " AND ";
				if(is_var_array($value)){
					$where_str .= " ( ";
					$j=0;
					foreach($value as $txt){
						if($j != 0) $where_str .= " AND ";
						$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($txt)."' ";
						$j++;
					}
					$where_str .= " ) ";
				}else{
					$where_str .= " ".$this->set_prefix_for_key($key)." = '".get_text_encode_db($value)."' ";
				}
				$i++;
				//$this->db->where($key,$value);
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}else if( !is_blank($data) ){
			$this->db->or_where($data,$value);
		}
	}

	function set_where_not_equal($data){
		if( is_var_array($data) ){
			foreach($data as $key => $value){
				$this->db->where($this->set_prefix_for_key($key)." != ",$value);
			}
		}else if( !is_blank($data) ){
			$this->db->where($data);
		}
	}

	function set_where_equal_or_greater($data){
		if( is_var_array($data) ){
			foreach($data as $key => $value){
				$this->db->where($this->set_prefix_for_key($key)." >= ",$value);
			}
		}else if( !is_blank($data) ){
			$this->db->where($data, null, false);
		}
	}

	function set_where_in($data){
		if( is_var_array($data) ){
			foreach($data as $key => $value){
				$this->db->where_in($this->set_prefix_for_key($key),$value);
			}
		}
	}
	
	function set_where_not_in($data){
		if( is_var_array($data) ){
			foreach($data as $key => $value){
				$this->db->where_not_in($this->set_prefix_for_key($key),$value);
			}
		}
	}
	
	//return : AND ( tbl_name.tbl_field_1 like '%val1%' AND tbl_name.tbl_field_2 like '%val2%' )
	function set_like($data,$wc_postion="both"){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($key)." like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}
	}
	
	//return : OR ( tbl_name.tbl_field_1 like '%val1%' OR tbl_name.tbl_field_2 like '%val2%' )
	function set_and_or_like($data,$value="",$wc_postion=""){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				// echo "key = $key , value = $value | ";
				if($i != 0) $where_str .= " OR ";
				if(is_var_array($value)){
					$where_str .= " ( ";
					$j=0;
					foreach($value as $txt){
						if($j != 0) $where_str .= " OR ";
						switch($wc_postion){
							case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($txt)."%' "; break;
							case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($txt)."' "; break;
							case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." like '".get_text_encode_db($txt)."%' "; break;
							default : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($txt)."%' "; break;
						}
						$j++;
					}
					$where_str .= " ) ";
				}else{
					switch($wc_postion){
						case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($value)."%' "; break;
						case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($value)."' "; break;
						case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." like '".get_text_encode_db($value)."%' "; break;
						default : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($value)."%' "; break;
					}
				}
				$i++;
				//$this->db->where($key,$value);
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}else if( !is_blank($data) ){
			$this->db->where($data,$value);
		}
	}
	
	//return : OR ( tbl_name.tbl_field_1 like '%val1%' AND tbl_name.tbl_field_2 like '%val2%' )
	function set_or_and_like_group($data,$value="",$wc_postion=""){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " OR ";
				if(is_var_array($value)){
					$where_str .= " ( ";
					$j=0;
					foreach($value as $txt){
						if($j != 0) $where_str .= " AND ";
						switch($wc_postion){
							case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($txt)."%' "; break;
							case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($txt)."' "; break;
							case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." like '".get_text_encode_db($txt)."%' "; break;
							default : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($txt)."%' "; break;
						}
						$j++;
					}
					$where_str .= " ) ";
				}else{
					switch($wc_postion){
						case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($value)."%' "; break;
						case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($value)."' "; break;
						case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." like '".get_text_encode_db($value)."%' "; break;
						default : $where_str .= " ".$this->set_prefix_for_key($key)." like '%".get_text_encode_db($value)."%' "; break;
					}
				}
				$i++;
				//$this->db->where($key,$value);
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}else if( !is_blank($data) ){
			$this->db->where($data,$value);
		}
	}
	
	//return : OR ( tbl_name.tbl_field_1 not like '%val1%' AND tbl_name.tbl_field_2 not like '%val2%' )
	function set_or_and_not_like_group($data,$value="",$wc_postion=""){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " OR ";
				if(is_var_array($value)){
					$where_str .= " ( ";
					$j=0;
					foreach($value as $txt){
						if($j != 0) $where_str .= " AND ";
						switch($wc_postion){
							case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%".get_text_encode_db($txt)."%' "; break;
							case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%".get_text_encode_db($txt)."' "; break;
							case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '".get_text_encode_db($txt)."%' "; break;
							default : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%".get_text_encode_db($txt)."%' "; break;
						}
						$j++;
					}
					$where_str .= " ) ";
				}else{
					switch($wc_postion){
						case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%".get_text_encode_db($value)."%' "; break;
						case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%".get_text_encode_db($value)."' "; break;
						case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '".get_text_encode_db($value)."%' "; break;
						default : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%".get_text_encode_db($value)."%' "; break;
					}
				}
				$i++;
				//$this->db->where($key,$value);
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}else if( !is_blank($data) ){
			$this->db->where($data,$value);
		}
	}
	
	//return : OR ( tbl_name.tbl_field like '%val1%' OR tbl_name.tbl_field like '%val2%' )
	function set_or_like($data,$wc_postion="both"){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " OR ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($key)." like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
		
	//return : OR ( tbl_name.tbl_field like '%val1%' AND tbl_name.tbl_field like '%val2%' )
	function set_or_and_like($data,$wc_postion="both"){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($key)." like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($key)." like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
		
	//return : AND ( tbl_name.tbl_field like '%val1%' AND tbl_name.tbl_field like '%val2%' )
	function set_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}
	}
	
	//return : AND ( tbl_name.tbl_field like '%val1%' OR tbl_name.tbl_field like '%val2%' )
	function set_and_or_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " OR ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}
	}
	
	//return : OR ( tbl_name.tbl_field like '%val1%' OR tbl_name.tbl_field like '%val2%' )
	function set_or_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " OR ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
	
	//return : OR ( tbl_name.tbl_field like '%val1%' AND tbl_name.tbl_field like '%val2%' )
	function set_or_and_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
	
	//return : AND ( tbl_name.tbl_field not like '%val1%' AND tbl_name.tbl_field not like '%val2%' )
	function set_not_like($data,$wc_postion="both"){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}
	}
	
	//return : AND ( tbl_name.tbl_field not like '%val1%' OR tbl_name.tbl_field not like '%val2%' )
	function set_and_or_not_like($data,$wc_postion="both"){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " OR ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}
	}
	
	//return : OR ( tbl_name.tbl_field not like '%val1%' OR tbl_name.tbl_field not like '%val2%' )
	function set_or_not_like($data,$wc_postion="both"){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " OR ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
	
	//return : OR ( tbl_name.tbl_field not like '%val1%' AND tbl_name.tbl_field not like '%val2%' )
	function set_or_and_not_like($data,$wc_postion="both"){
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $key => $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($key)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($key)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
	
	//return : AND ( tbl_name.tbl_field not like '%val1%' AND tbl_name.tbl_field not like '%val2%' )
	function set_not_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}
	}
	
	//return : AND ( tbl_name.tbl_field not like '%val1%' OR tbl_name.tbl_field not like '%val2%' )
	function set_and_or_not_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " OR ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->where($where_str);
		}
	}
	
	//return : OR ( tbl_name.tbl_field not like '%val1%' OR tbl_name.tbl_field not like '%val2%' )
	function set_or_not_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " OR ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
	
	//return : OR ( tbl_name.tbl_field not like '%val1%' AND tbl_name.tbl_field not like '%val2%' )
	function set_or_and_not_like_by_field($field_name,$data,$wc_postion="both"){
		if(is_blank($field_name) || is_blank($data)) return "";
		if( is_var_array($data) ){
			$where_str = "( ";
		   	$i=0;
			foreach($data as $value){
				if($i != 0) $where_str .= " AND ";
				switch($wc_postion){
					case "both" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
					case "left" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value' "; break;
					case "right" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value%' "; break;
					case "none" : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '$value' "; break;
					default : $where_str .= " ".$this->set_prefix_for_key($field_name)." not like '%$value%' "; break;
				}
				$i++;
			}
			$where_str .= " )";
			$this->db->or_where($where_str);
		}
	}
	
	function set_limit($limit_start=0,$no_record=0){
		if( !is_blank($limit_start) && !is_blank($no_record) ){
			$this->db->limit($no_record, $limit_start);
		}else if( !is_blank($no_record) ){
			$this->db->limit($no_record);
		}
	}

	function insert_record($data){
		$created_by = getUserLoginAid($this->user_login_info);
		if(is_number_no_zero(get_array_value($data,"created_by","0"))){
			$created_by = get_array_value($data,"created_by","0");
		}
		$updated_by = getUserLoginAid($this->user_login_info);
		if(is_number_no_zero(get_array_value($data,"updated_by","0"))){
			$updated_by = get_array_value($data,"updated_by","0");
		}

		if($this->db->field_exists('created_date',$this->tbl_name)) $data['created_date'] = get_db_now();
		if($this->db->field_exists('created_by',$this->tbl_name)) $data['created_by'] = $created_by;
		if($this->db->field_exists('updated_date',$this->tbl_name)) $data['updated_date'] = get_db_now();
		if($this->db->field_exists('updated_by',$this->tbl_name)) $data['updated_by'] = $updated_by;
		if($this->db->field_exists('ip',$this->tbl_name)) $data['ip'] = $this->input->ip_address();

        $this->db->insert($this->tbl_name, $data);
		return $this->db->insert_id();
    }
	
	function insert_records($data){
		$chk = false;
		if(is_var_array($data)){
			foreach($data as $item){
				$result = $this->insert_record($item);
				if($result > 0){
					$chk = true;
				}
			}
		}
		return $chk;
    }

	function insert_records_by_field($data_const,$field_name,$data){
		$chk = false;
		if(is_var_array($data_const) && !is_blank($field_name) && is_var_array($data)){
			foreach($data_const as $key => $value){
				foreach($data as $item){
					$tmp = array();
					$tmp[$key] = $value;
					$tmp[$field_name] = $item;
					$result = $this->insert_record($tmp);
					if($result > 0){
						$chk = true;
					}
				}
			}
		}
		return $chk;
    }
	
	function update_record($data){
		if( !is_var_array($data) ) return "";
		
		if($this->db->field_exists('updated_date',$this->tbl_name)) $data['updated_date'] = get_db_now();
		if($this->db->field_exists('updated_by',$this->tbl_name)) $data['updated_by'] = getUserLoginAid($this->user_login_info);
		if($this->db->field_exists('ip',$this->tbl_name)) $data['ip'] = $this->input->ip_address();

		return $this->db->update($this->tbl_name, $data);
		//echo "<br>sql : ".$this->db->last_query();
	}
	
	function insert_or_update($data_insert="",$data_update=""){
		if( !is_var_array($data_insert) ) return "";
		
		if( !is_var_array($data_update) ){
			$data_update = $data_insert;
		}

		$created_by = getUserLoginAid($this->user_login_info);
		if(is_number_no_zero(get_array_value($data_insert,"created_by","0"))){
			$created_by = get_array_value($data_insert,"created_by","0");
		}
		$updated_by = getUserLoginAid($this->user_login_info);
		if(is_number_no_zero(get_array_value($data_insert,"updated_by","0"))){
			$updated_by = get_array_value($data_insert,"updated_by","0");
		}

		if($this->db->field_exists('created_date',$this->tbl_name)) $data_insert['created_date'] = get_db_now();
		if($this->db->field_exists('created_by',$this->tbl_name)) $data_insert['created_by'] = $created_by;
		if($this->db->field_exists('ip',$this->tbl_name)){
			$data_insert['ip'] = $this->input->ip_address();
			$data_update['ip'] = $this->input->ip_address();
		}
		if($this->db->field_exists('updated_date',$this->tbl_name)){
			$data_insert['updated_date'] = get_db_now();
			$data_update['updated_date'] = get_db_now();
		}
		if($this->db->field_exists('updated_by',$this->tbl_name)){
			$data_insert['updated_by'] = $updated_by;
			$data_update['updated_by'] = $updated_by;
		}
		
		$this->db->set($data_insert);
		
		$update = "";
        foreach ($data_update as $key => $value) { 
			$update[] = "{$key} = '{$value}'"; 
		}
		$table_name = $this->get_table_name();
        $sql = $this->db->_insert($table_name, array_keys($this->db->ar_set), array_values($this->db->ar_set))." ON DUPLICATE KEY UPDATE ".implode(', ', $update);
		// echo "SQL : ".$sql;
        $this->db->_reset_write();
        // $this->db->flush_cache();
		
        return $this->db->query($sql);
	}
	
	function delete_records(){
		return $this->db->delete($this->tbl_name);
	}
	
	function set_trans_begin(){
		$this->db->trans_begin();
	}
	
	function set_trans_start(){
		$this->db->trans_start(TRUE);
	}
	
	function set_trans_rollback(){
		$this->db->trans_rollback();
	}
	
	function set_trans_commit(){
		$this->db->trans_commit();
	}

	function set_value($f_name,$f_value){
		if($this->db->field_exists($f_name,$this->tbl_name)){
			$user_aid = getUserLoginAid($this->user_login_info);
			if($user_aid){
				$data = array(
						$f_name	=>	$f_value
					);
				$result = $this->update_record($data);
				// Return result
				if ($result)
					return true;
				else
					return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function load_master($fullload=false){
		$result = "";
		if($this->db->field_exists('user_owner_aid',$this->tbl_name)){
			$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		}
		if($this->db->field_exists('status',$this->tbl_name)){
			if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		}
		$order_by = "";
		if($this->db->field_exists('weight',$this->tbl_name)){
			$order_by .= "weight ASC";
		}
		if($this->db->field_exists('title',$this->tbl_name)){
			if(!is_blank($order_by)){
				$order_by .= " , ";
			}
			$order_by .= "title ASC";
		}
		if($this->db->field_exists('name',$this->tbl_name)){
			if(!is_blank($order_by)){
				$order_by .= " , ";
			}
			$order_by .= "name ASC";
		}
		if(!is_blank($order_by)){
			$this->set_order_by($order_by);
		}
		if($this->db->field_exists('aid',$this->tbl_name)){
			$result = $this->load_records_array($fullload,"aid","");
		}else{
			$result = $this->load_records($fullload);
		}
		// echo "<br>sql : ".$this->db->last_query();
		// print_r($result);
		return $result;
	}
	
	function set_open(){
		$this->db->ar_where[] = "("; 
		return $this;  
	}
	
	function set_close(){
		$this->db->ar_where[] = ")"; 
		return $this;  
	}


}

/* End of file initmodel.php */
/* Location: ./system/application/model/initmodel.php */