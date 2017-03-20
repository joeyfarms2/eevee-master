<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Report_news_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		for_owner_admin_or_higher();
		$this->data["mode"] = "backend";
		@define("thisAdminTabMenu",'report');
		@define("folderName","report/news/");
		$this->user_model = 'User_model';

		$this->main_model = 'News_model';
		$this->news_main_model = 'News_main_model';
		$this->news_category_model = 'News_category_model';
		$this->news_gallery_model = 'News_gallery_model';
		$this->news_comment_model = 'News_comment_model';
		$this->news_user_activity_model = 'news_user_activity_model';
		$this->news_comment_user_activity_model = 'news_comment_user_activity_model';
		
		$this->event_model = 'Event_model';
		$this->event_main_model = 'Event_main_model';
		$this->event_category_model = 'Event_category_model';
		$this->event_gallery_model = 'Event_gallery_model';	
		
		$this->view_most_comments_model = 'View_most_comments_model';
		
	}
	
	function index()
	{
		$this->news_log();
	}
	
	function news_log(){
		@define("thisAction","news_log");
		@define('thisAdminSubMenu','report_news_log');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/report/news/news_log_page';
		$this->data["message"] = "";
		
		$data_search = array();
		$data_search["created_date_from"] = date('Y-m-01');
		$data_search["created_date_to"] = date('Y-m-d');
		
		$this->data["data_search"] = $data_search;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function ajax_get_news_log_list($sid=""){
		if(!is_owner_admin_or_higher()){
			$result_obj = array("status" => "error","msg" => "You don't have authorize to view this report.");
			echo json_encode($result_obj);
			return"";
		}
	
		$this->load->model($this->main_model,'main');
		$this->load->model($this->news_category_model, 'category');
		$tmp_cat = $this->category->load_records(false);
			
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('newsBackDataSearchSession');		
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
		
		$search_status = $this->getDataFromInput('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->main->set_where_in($data_where);
		
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

		$search_news_category = $this->getDataFromInput('search_news_category');
		$data_where = "";
		if(is_var_array($search_news_category)) {
			
			foreach($search_news_category as $item){
				$data_where[] = $item;
				$data_search["search_news_category"][] = $item;
			}
			if (count($search_news_category) < count($tmp_cat)) {
				$this->main->set_and_or_like_by_field("category", $data_where);
			}
		}
		

		// $news_main_aid = $this->input->get_post('news_main_aid');
		// $this->main->set_where(array("news_main_aid"=>$news_main_aid));

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
		$order_by_option = $this->get_order_by_info($search_order_by,'aid desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_news_log(true);
		// echo "<pre>";
		// print_r($result_list);
		// echo "</pre>";
		//echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_news_log($result_list['results']);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'aid',"col_show"=>'name_action',"title_class"=>'w20 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title',"title_class"=>'w200 hcenter',"result_class"=>'hleft');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Main',"field_order"=>'*news_main_name',"col_show"=>'news_main_name',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Published Date',"field_order"=>'publish_date',"col_show"=>'publish_date_txt',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Wow',"field_order"=>'total_wow',"col_show"=>'total_wow',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Cheer',"field_order"=>'total_cheer',"col_show"=>'total_cheer',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Thanks',"field_order"=>'total_thanks',"col_show"=>'total_thanks',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total View',"field_order"=>'total_view',"col_show"=>'total_view',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Total Comments',"field_order"=>'total_comment',"col_show"=>'total_comment',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Published?',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		// if(CONST_NEWS_MODE == "1"){
		// 	$header_list[] = array("sort_able"=>'1',"title_show"=>'Highlighted?',"field_order"=>'is_highlight',"col_show"=>'is_highlight_action',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		// 	$header_list[] = array("sort_able"=>'1',"title_show"=>'Recommended?',"field_order"=>'is_recommended',"col_show"=>'is_recommended_action',"title_class"=>'w20 hcenter',"result_class"=>'hcenter');
		// 	// $header_list[] = array("sort_able"=>'1',"title_show"=>'Show in Homepage?',"field_order"=>'is_home',"col_show"=>'is_home_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		// }
		//$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'w40 hcenter',"result_class"=>'hcenter');
		
		$this->session->set_userdata('newsBackDataSearchSession',$data_search);	
		
		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "sorting" => get_array_value($order_by_option,"sorting",""), "optional"=>$optional, "header_list" => $header_list, "result" => $result_list);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.');
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function manage_column_news_log($result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$item['title'] = get_array_value($item, 'draft_title', $item['title']);
			$item['description'] = get_array_value($item, 'draft_description', $item['description']);

			$item["name_action"] = '<a href="'.site_url('admin/news/edit/'.get_array_value($item,"aid","")).'">'.get_text_pad(get_array_value($item,"aid","0"),0,8).'</a>';
			$item["title_action"] = '<a href="'.site_url('admin/news/edit/'.get_array_value($item,"aid","")).'">'.getShortString($item['title'],"70").'</a>';
			
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_highlight = get_array_value($item,"is_highlight","0");
			if($is_highlight == 1){
				$item["is_highlight_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_highlight=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_highlight_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_highlight=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_recommended = get_array_value($item,"is_recommended","0");
			if($is_recommended == 1){
				$item["is_recommended_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_recommended=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_recommended_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_recommended=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$is_home = get_array_value($item,"is_home","0");
			if($is_home == 1){
				$item["is_home_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this news." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_home=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["is_home_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this news." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/news\', \''.get_array_value($item,"aid","").'\', \'is_home=1\')"><i class="fa fa-ban"></i></a>';
			}
			
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this news." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/news\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
				$item["action"] .= '<a class="btn btn-info btn-xs" title="Click to \'View\' all comments." onclick="processViewAllComments(\''.get_array_value($item,"aid","").'\')"><i class="fa fa-comments-o "></i></a>&nbsp;&nbsp;&nbsp;';
			}

			$result[] = $item;
		}
		
		return $result;
	}
	
	function export_news_log(){
		if(!is_owner_admin_or_higher()){
			echo "You don't have authorize to export this report.";
			return"";
		}
	
		$this->load->model($this->main_model,'main');
		$this->load->model($this->news_category_model, 'category');
		$tmp_cat = $this->category->load_records(false);
			
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('newsBackDataSearchSession');		
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
		
		$search_status = $this->getDataFromInput('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->main->set_where_in($data_where);
		
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

		$search_news_category = $this->getDataFromInput('search_news_category');
		$data_where = "";
		if(is_var_array($search_news_category)) {
			
			foreach($search_news_category as $item){
				$data_where[] = $item;
				$data_search["search_news_category"][] = $item;
			}
			if (count($search_news_category) < count($tmp_cat)) {
				$this->main->set_and_or_like_by_field("category", $data_where);
			}
		}
		

		// $news_main_aid = $this->input->get_post('news_main_aid');
		// $this->main->set_where(array("news_main_aid"=>$news_main_aid));
		$search_order_by = $this->getDataFromInput('search_order_by');
		$data_search["search_order_by"] = $search_order_by;
		$order_by_option = $this->get_order_by_info($search_order_by,'aid desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_news_log(true);
		$result_list = $this->manage_column_news_log($result_list['results']);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		// print_r($result_list);
		if(is_var_array($result_list)){
			$this->load->library('PHPExcel');
			$title_column_color = 'C9DCE6';
			$array_style_summary_title = array(
				'font' => array('bold' => true), 
				'alignment' => array('
					wrap' => true,
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'borders' => array(
					'allborders'     => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array('rgb' => $title_column_color)
				)
			);

			$objPHPExcel = new PHPExcel();
			// Set properties
			$objPHPExcel->getProperties()->setCreator(ADMIN_EMAIL_NAME);
			$objPHPExcel->getProperties()->setLastModifiedBy(ADMIN_EMAIL_NAME);
			$objPHPExcel->getProperties()->setTitle("News List");
			$objPHPExcel->getProperties()->setDescription("News list");
			// Set Default Style
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); 
			// Rename Sheet
			$objPHPExcel->getActiveSheet()->setTitle('News List');
			// Set column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(80);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);

			$objPHPExcel->getActiveSheet()->setCellValue('A1', "Title");
			$objPHPExcel->getActiveSheet()->setCellValue('B1', "Published Date");
			$objPHPExcel->getActiveSheet()->setCellValue('C1', "Total Wow");
			$objPHPExcel->getActiveSheet()->setCellValue('D1', "Total Cheer");
			$objPHPExcel->getActiveSheet()->setCellValue('E1', "Total Thanks");
			$objPHPExcel->getActiveSheet()->setCellValue('F1', "Total View");
			$objPHPExcel->getActiveSheet()->setCellValue('G1', "Total Comments");





			$sharedStyle1 = new PHPExcel_Style();
			$sharedStyle1->applyFromArray($array_style_summary_title);
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:G1");
			
			$irow = 2;
			foreach($result_list as $item){
				//print_r($item);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$irow, get_array_value($item,"title",""));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$irow, get_array_value($item,"publish_date",""));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$irow, get_array_value($item,"total_wow",""));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$irow, get_array_value($item,"total_cheer",""));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$irow, get_array_value($item,"total_thanks",""));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$irow, get_array_value($item,"total_view",""));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$irow, get_array_value($item,"total_comment",""));
				$irow++;
			}
			
			$filename ="download_export_".date("ymdHis").".xls";
			// echo "$filename";
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$filename);
			header('Cache-Control: max-age=0');
			header('Pragma: no-cache');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit();
		}else{
			echo "No record found.";
			return"";
		}
	}
		
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */