<?php
class Product_type_minor_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("product_type_minor");
		$this->tbl_setting_config = "setting_config";
	}
	
	function load_product_type_minors(){
		$this->db->select("product_type_minor.aid, product_type_minor.name, setting_config.opt_2 AS prefix, setting_config.opt_3 AS postfix, setting_config.opt_4 AS len");
		$this->db->join($this->tbl_setting_config, "product_type_minor.setting_config_aid = setting_config.aid", "LEFT");
		$this->set_where(array("product_type_minor.status" => "1"));
		return $this->load_records_array(false,"aid","");
	}
		
}

/* End of file product_type_minor_model.php */
/* Location: ./system/application/model/product_type_minor_model.php */