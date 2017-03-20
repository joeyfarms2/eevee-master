<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Transaction_front_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = "front";
		define("thisFrontTabMenu",'transaction');
		@define("folderName",'transaction/transaction_front/transaction');
		
		$this->transaction_model = "transaction_model";
	}
	
	function index()
	{
		$this->my_transaction();
	}
	
	function my_transaction($show_option="shelf",$sort_by="date_d",$page_selected="")
	{
		@define("thisAction","my_transaction");
		@define("thisFrontSubMenu",'my_transaction');
		for_login();
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'MyShelf';
		$this->data["view_the_content"] = $this->default_theme_front . '/' . folderName . '/my_transaction';
		
		// echo "show_option = $show_option <BR />";
		if($show_option != "shelf" && $show_option != "list"){
			$show_option = "shelf";
		}
		// echo "show_option = $show_option <BR />";
		
		$sort_by = trim(strtolower($sort_by));
		if($sort_by != "date_a" && $sort_by != "date_d"  && $sort_by != "name_a" && $sort_by != "name_d" ){
			$sort_by = "date_d";
		}
		// echo "sort_by = $sort_by <BR />";
		$page_selected = str_replace("page-", "", $page_selected);
		// echo "page_selected = $page_selected <BR />";
		
		$url = "";
		$record_per_page = CONST_DEFAULT_RECORD_FOR_MY_BOOKSHELF;
		if($show_option == "list"){
			$url = "-list";
			$record_per_page = 20;
		}
		
		$optional = array();
		$this->load->model($this->transaction_model,"transaction");
		$this->db->start_cache();		
		$this->transaction->set_where(array("user_aid"=>getSessionUserAid()));
		// $tmp = array("1","2");
		// $this->transaction->set_and_or_like_by_field("issue.product_main_aid",$tmp,"none");
		$total_record = $this->transaction->count_records(true);
		$optional["total_record"] = $total_record;
		$optional["page_selected"] = $page_selected;
		$optional["record_per_page"] = $record_per_page;
		$optional["url"] = 'my-transaction'.$url.'/sort-'.$sort_by.'/page-';
		
		$optional = $this->get_pagination_info($optional);
		
		$obj = "";
		$obj["user_aid"] = getSessionUserAid();
		$obj["status"] = "1";
		$obj["sort_by"] = $sort_by;
		$obj["optional"] = $optional;
		$this->data["resultList"] = $this->transaction->get_transaction_detail($obj);		
		$optional["total_in_page"] = count($this->data["resultList"]);			
		$this->db->flush_cache();		
		
		$this->data["optional"] = $optional;			
		$this->data["show_option"] = $show_option;			
		$this->data["sort_by"] = $sort_by;			
		// echo "<br>sql : ".$this->db->last_query();
		// exit(0);
		$this->db->flush_cache();		
		
		$this->load->view('theme'.THEME_FRONT.'/tpl_bookshelf', $this->data);
	}
	
	function status($type="")
	{
		switch($type)
		{
			case md5('permission') : 
				$this->data["message"] = set_message_error("หน้านี้ได้ถูกยกเลิกไปแล้ว หรือคุณไม่มีสิทธิ์เรียกดู");
				break;
				
			default : 
				$this->data["message"] = set_message_error("เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่ หรือติดต่อผู้ดูแลระบบ");
				break;
		}
		$this->my_transaction();
	}
			
}

?>