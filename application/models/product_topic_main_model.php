<?php
class Product_topic_main_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("product_topic_main");
	}
	
	function load_product_topic_mains(){
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(false,"aid","");
	}
	
	function load_product_topic_mains_array(){
		$this->set_where(array("user_owner_aid"=>getUserOwnerAid($this->user_owner_info)));
		if(!exception_about_status()) $this->set_where(array("status"=>"1"));
		$this->set_order_by("weight ASC");
		return $this->load_records_array(false,"aid","name");
	}
	
}

/* End of file product_topic_main_model.php */
/* Location: ./system/application/model/product_topic_main_model.php */