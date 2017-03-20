<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Shelf_vdo_front_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = "front";
		define("thisFrontTabMenu",'shelf_vdo');
		// define("thisFrontSubMenu",'');
		@define("folderName",'home/');
		
		$this->shelf_vdo_model = "Shelf_vdo_model";
		$this->shelf_vdo_history_model = "Shelf_vdo_history_model";
	}
	
	function index()
	{
		$this->my_bookshelf();
	}
	
	function my_bookshelf($show_option="shelf",$sort_by="date_d",$page_selected="")
	{
		@define("thisAction","my_bookshelf_vdo");
		@define("thisFrontSubMenu",'my_bookshelf_vdo');
		for_login();
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'MyShelf';
		$this->data["view_the_content"] = $this->default_theme_front . '/shelf/my_bookshelf_vdo';
		
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
		$this->load->model($this->shelf_vdo_model,"shelf_vdo");
		$this->db->start_cache();		
		$this->shelf_vdo->set_where(array("user_aid"=>getSessionUserAid(),"status"=>'1'));
		// $tmp = array("1","2");
		// $this->shelf_vdo->set_and_or_like_by_field("issue.product_main_aid",$tmp,"none");
		$total_record = $this->shelf_vdo->count_records(true);
		$optional["total_record"] = $total_record;
		$optional["page_selected"] = $page_selected;
		$optional["record_per_page"] = $record_per_page;
		$optional["url"] = 'my-bookshelf-vdo'.$url.'/sort-'.$sort_by.'/page-';
		
		$optional = $this->get_pagination_info($optional);
		
		$this->shelf_vdo->set_order_by("");
		if($sort_by == 'date_d'){
			$this->shelf_vdo->set_order_by("shelf_vdo.created_date DESC");
		}else if($sort_by == 'date_a'){
			$this->shelf_vdo->set_order_by("shelf_vdo.created_date ASC");
		}else if($sort_by == 'name_d'){
			$this->shelf_vdo->set_order_by("*title DESC");
		}else if($sort_by == 'name_a'){
			$this->shelf_vdo->set_order_by("*title ASC");
		}

		$this->data["resultList"] = $this->shelf_vdo->load_records(true);		
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
		$this->my_bookshelf();
	}
	
	function delete_my_bookshelf($sid,$product_type_aid,$aid,$show_option="shelf",$sort_by="date_d",$page_selected="")
	{
		if(is_blank($aid)){
			$result_obj = array("status" => "error","msg" => "No copy found.");
			echo json_encode($result_obj);
			return"";
		}
		
		$this->load->model($this->shelf_vdo_model,"shelf_vdo");
		$data_where = array();
		$data_where["product_type_aid"] = $product_type_aid;
		$data_where["parent_aid"] = $aid;
		$data_where["user_aid"] = getSessionUserAid();
		$this->shelf_vdo->set_where($data_where);
		$rs = $this->shelf_vdo->delete_records();
		
		//Update shelf history
		$this->db->flush_cache();
		$this->load->model($this->shelf_vdo_history_model,"shelf_vdo_history");
		$data = array();
		$data["product_type_aid"] = $product_type_aid;
		$data["parent_aid"] = $aid;
		$data["user_aid"] = getSessionUserAid();
		$data["status"] = '1';
		$data["action"] = 'de';
		$result = $this->shelf_vdo_history->insert_record($data);	
		
		$page = "";
		if($page_selected > 1){
			$page = "/page-".$page_selected;
		}
		
		$url = "my-bookshelf-vdo";
		if($show_option == "list"){
			$url .= "-list";
		}
		if($sort_by != ""){
			$url .= "/sort-".$sort_by;
		}
		
		redirect($url.$page);
	}
	
	function ajax_get_badge_my_bookshelf($sid)
	{
		if(is_login()){	
			$this->db->flush_cache();
			$tmp = array();
			$tmp["user_aid"] = getSessionUserAid();
			$tmp["status"] = '1';
			// $tmp["is_read"] = '0';
			$this->load->model($this->shelf_vdo_model,"shelf_vdo");
			$this->shelf_vdo->set_where($tmp);
			$result = $this->shelf_vdo->count_records(false);
			// echo "<br>sql : ".$this->db->last_query()."<br>";
			$result_obj = array("status" => "success","msg" => "","result"=>$result);
			echo json_encode($result_obj);
		}else{
			$result_obj = array("status" => "warning","msg" => "","result"=>"");
			echo json_encode($result_obj);
		}
	}
		
}

?>