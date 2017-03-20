<?php
class Setting_config_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("setting_config");
		
		$this->tbl_product_type_minor = "product_type_minor";
	}
	
	function get_config_rni_by_cid($cid){
		$this->set_trans_start();
		$this->set_where(array("cid"=>$cid));
		$config_result = $this->load_record(false);
		if(!is_var_array($config_result)){
			// set_error_handler($this->my_error_handler);
			return "";
		}else{
			$_sql = 'UPDATE '.$this->get_table_name().' SET value = value+1 WHERE cid = "'.$cid.'"';
			$result = $this->db->query($_sql);
			if($result){
				$this->set_trans_commit();
				$value = get_array_value($config_result,"value","");
				$opt_1 = get_array_value($config_result,"opt_1","");
				$opt_2 = get_array_value($config_result,"opt_2","");
				$opt_3 = get_array_value($config_result,"opt_3","");
				$opt_4 = get_array_value($config_result,"opt_4","");
				$opt_5 = get_array_value($config_result,"opt_5","");
				$obj = array();
				$obj["value"] = $value;
				if($opt_4 > 0){
					$obj["barcode"] = $opt_2.get_text_pad($value,"0",$opt_4).$opt_3;
				}else{
					$obj["barcode"] = $opt_2.$value.$opt_3;
				}
				return $obj;
			}else{
				$this->set_trans_rollback();
				return "";
			}
		}
	}
	
	function get_config_rni_by_product_type_minor_aid($product_type_minor_aid){
		$this->set_trans_start();
		// echo "product_type_minor_aid = $product_type_minor_aid";
		
		$this->db->select($this->get_table_name().'.*');
		$this->db->join($this->tbl_product_type_minor.' AS product_type_minor', $this->get_table_name().'.aid = product_type_minor.setting_config_aid', "left");
		$this->set_where(array("product_type_minor.aid"=>$product_type_minor_aid));
		$config_result = $this->load_record(false);
		// echo "<br>sql : ".$this->db->last_query()."<BR />";
		// print_r($config_result);
		
		if(!is_var_array($config_result)){
			// set_error_handler($this->my_error_handler);
			return "";
		}else{
			$cid = get_array_value($config_result,"cid","");
			$_sql = 'UPDATE '.$this->get_table_name().' SET value = value+1 WHERE cid = "'.$cid.'"';
			$result = $this->db->query($_sql);
			if($result){
				$this->set_trans_commit();
				$value = get_array_value($config_result,"value","");
				$opt_1 = get_array_value($config_result,"opt_1","");
				$opt_2 = get_array_value($config_result,"opt_2","");
				$opt_3 = get_array_value($config_result,"opt_3","");
				$opt_4 = get_array_value($config_result,"opt_4","");
				$opt_5 = get_array_value($config_result,"opt_5","");
				$obj = array();
				$obj["value"] = $value;
				$obj["barcode"] = $opt_2.get_text_pad($value,"0",$opt_4).$opt_3;
				return $obj;
			}else{
				$this->set_trans_rollback();
				return "";
			}
		}
	}
	
	
}

/* End of file setting_running_model.php */
/* Location: ./system/application/model/setting_running_model.php */