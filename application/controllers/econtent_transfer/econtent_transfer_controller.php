<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Econtent_transfer_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		if(CONST_HAS_ECONTENT_TRANSFER != "1"){
			redirect('home');
		}

		define("thisAdminTabMenu",'econtent_transfer');
		define("thisAdminSubMenu",'econtent_transfer');
		@define("folderName",'econtent_transfer/econtent_transfer_back');
		
		define("TXT_TITLE",'E-Content Transfer management');
		define("TXT_INSERT_TITLE",'E-Content Transfer management : Add new e-content');
		define("TXT_UPDATE_TITLE",'E-Content Transfer management : Edit e-content');
				
		// $this->main_model = 'Ads_model';		
		// $this->ads_category_model = 'Ads_category_model';		

		// $this->load->model($this->ads_category_model,"category");
		// $this->data["master_ads_category"] = $this->category->load_all_category();
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
		
	}

	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/econtent';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
}

?>