<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class News_init_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();

		$this->news_main_model = "News_main_model";
		$this->news_categpry_model = "News_categpry_model";
		$this->news_model = "News_model";

		$this->load->model($this->news_main_model,"news_main");
		$this->data["master_news_main"] = $this->news_main->load_news_mains();

	}
	
	function index(){
			return "";
	}

}

?>