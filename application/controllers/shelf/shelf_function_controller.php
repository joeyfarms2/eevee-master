<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// require_once(APPPATH."controllers/initcontroller.php");

class Shelf_function_controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->shelf_model = "Shelf_model";
		$this->shelf_history_model = "Shelf_history_model";
	}
	
	function add_product_to_shelf($obj)
	{
		
		

	}
	
		
}

?>