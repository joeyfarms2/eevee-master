<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Log_back_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		// for_root_admin_or_higher();
		$this->data["mode"] = 'backend';

		define("thisAdminTabMenu",'log');
		define("thisAdminSubMenu",'');
		@define("folderName",'log/');
		
		define("TXT_TITLE",'Log history');
				
		$this->main_model = 'Log_model';

		$this->lang->load('log', $this->session->userdata('language'));
	}
	
	function index()
	{
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show()
	{
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/log_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function ajax_get_main_list($sid)
	{
		@define("thisAction",'ajax_get_main_list');
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('logBackDataSearchSession');		
		// print_r($dsSession);
		
		$search_post_word = $this->getDataFromInput('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->getDataFromInput('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
				
		$created_date_from = $this->getDataFromInput('created_date_from');
		$created_date_to = $this->getDataFromInput('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->main->set_where($this->main->get_table_name().'.created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->main->set_where($this->main->get_table_name().'.created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		
		$search_role = $this->input->get_post('search_role');
		$data_where = "";
		if(is_var_array($search_role))
		foreach($search_role as $item){
			$data_where["*user_role_aid"][] = $item;
			$data_search["search_role"][] = $item;
		}
		$this->main->set_where_in($data_where);
				
		$search_record_per_page = $this->getDataFromInput('search_record_per_page', CONST_DEFAULT_RECORD_PER_PAGE);
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->getDataFromInput('page_selected');
		$optional["record_per_page"] = $search_record_per_page;
		$optional = $this->get_pagination_info($optional);
		$data_search["page_selected"] = get_array_value($optional,"page_selected","");
		$data_search["search_record_per_page"] = get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));
		
		$search_order_by = $this->getDataFromInput('search_order_by');
		$data_search["search_order_by"] = $search_order_by;
		$order_by_option = $this->get_order_by_info($search_order_by,'created_date desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Date',"field_order"=>'created_date',"col_show"=>'created_date_txt',"title_class"=>'w80 hcenter',"result_class"=>'hleft');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Browser',"field_order"=>'browser',"col_show"=>'browser',"title_class"=>'w80 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'IP',"field_order"=>'ip',"col_show"=>'ip',"title_class"=>'w40 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Type',"field_order"=>'type',"col_show"=>'type',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Username',"field_order"=>'created_by',"col_show"=>'email',"title_class"=>'w80 hcenter',"result_class"=>'hleft');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Role',"field_order"=>'user.user_role_aid',"col_show"=>'user_role_name',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Owner',"field_order"=>'owner_detail',"col_show"=>'owner_detail',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Description',"field_order"=>'description',"col_show"=>'description',"title_class"=>'hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('logBackDataSearchSession',$data_search);	
		
		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "sorting" => get_array_value($order_by_option,"sorting",""), "optional"=>$optional, "header_list" => $header_list, "result" => $result_list);
			echo json_encode($result_obj);
			return"";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.');
			echo json_encode($result_obj);
			return"";
		}
	}
	
	function manage_column_detail($result_list)
	{
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		foreach($result_list as $item){
			$result[] = $item;
		}
		return $result;
	}
	
}

?>