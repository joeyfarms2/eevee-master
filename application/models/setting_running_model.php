<?php
class Setting_running_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("setting_running");
	}
	
	function get_latest_invoice_by_year($year=""){
		if(!is_number_no_zero($year)){
			return "";
		}
		$this->set_trans_start();
		$this->db->select('invoice');
		$this->set_where(array("year"=>$year));
		$running_result = $this->load_record(false);
		if(!is_var_array($running_result)){
			$data = array();
			$data["year"] = $year;
			$data["invoice"] = "2";
			$data["receipt"] = "1";
			$this->insert_record($data);
			$this->set_trans_commit();
			return CONST_INVOICE_PREFIX.$year.get_text_pad("1","0",CONST_ZERO_PAD);
		}else{
			$_sql = 'UPDATE '.$this->get_table_name().' SET invoice = invoice+1 WHERE year = "'.$year.'"';
			$result = $this->db->query($_sql);
			if($result){
				$this->set_trans_commit();
				return CONST_INVOICE_PREFIX.$year.get_text_pad(get_array_value($running_result,"invoice","0"),"0",CONST_ZERO_PAD);
			}else{
				$this->set_trans_rollback();
				return "";
			}
		}
	}
	
	function get_latest_receipt_by_year($year=""){
		if(!is_number_no_zero($year)){
			return "";
		}
		$this->set_trans_start();
		$this->db->select('receipt');
		$this->set_where(array("year"=>$year));
		$running_result = $this->load_record(false);
		if(!is_var_array($running_result)){
			$data = array();
			$data["year"] = $year;
			$data["invoice"] = "1";
			$data["receipt"] = "2";
			$this->insert_record($data);
			$this->set_trans_commit();
			return CONST_INVOICE_PREFIX.$year.get_text_pad("1","0",CONST_ZERO_PAD);
		}else{
			$_sql = 'UPDATE '.$this->get_table_name().' SET receipt = receipt+1 WHERE year = "'.$year.'"';
			$result = $this->db->query($_sql);
			if($result){
				$this->set_trans_commit();
				return CONST_RECEIPT_PREFIX.$year.get_text_pad(get_array_value($running_result,"receipt","0"),"0",CONST_ZERO_PAD);
			}else{
				$this->set_trans_rollback();
				return "";
			}
		}
	}
	
	
}

/* End of file setting_running_model.php */
/* Location: ./system/application/model/setting_running_model.php */