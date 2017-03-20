<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Report_top_reader_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		for_owner_admin_or_higher();
		$this->data["mode"] = "backend";
		@define("thisAdminTabMenu",'report');
		@define("folderName","report/top_reader/");
		$this->view_all_download_history_model = "View_all_download_history_model";
		
	}
	
	function index()
	{
		$this->top_reader_log();
	}
	
	function top_reader_log(){
		@define("thisAction","top_reader_log");
		@define('thisAdminSubMenu','report_top_reader_log');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/report/top_reader/top_reader_log_page';
		$this->data["message"] = "";
		
		$data_search = array();
		$data_search["created_date_from"] = date('Y-m-01');
		$data_search["created_date_to"] = date('Y-m-d');
		
		$this->data["data_search"] = $data_search;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function ajax_get_top_reader_log_list($sid=""){
		if(!is_owner_admin_or_higher()){
			$result_obj = array("status" => "error","msg" => "You don't have authorize to view this report.");
			echo json_encode($result_obj);
			return"";
		}
	
		// $this->load->model($this->view_all_download_history_model,"user_download_report");
		// $this->db->start_cache();

		$this->load->model($this->view_all_download_history,"user_download_report");
		$this->db->start_cache();		
		$this->db->select('view_all_download_history.*, count(*) as total_download');
		$this->db->group_by("user_aid");
		
		
		// $this->user_download_report->set_where(array("action"=>"login"));
		
		$data_search="";
		$data_where = "";
		
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;

		$search_limit = $this->input->get_post('search_limit');
		$data_search["search_limit"] = $search_limit;


		$search_in = $this->input->get_post('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->user_download_report->set_or_like($data_where);
		
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->user_download_report->set_where_in($data_where);
		$created_date_from = $this->input->get_post('created_date_from');
		$created_date_to = $this->input->get_post('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->user_download_report->set_where($this->user_download_report->get_table_name().'.updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->user_download_report->set_where($this->user_download_report->get_table_name().'.updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}		
		
		$record_per_page = CONST_DEFAULT_RECORD_PER_PAGE;
		$search_record_per_page = $this->input->get_post('search_record_per_page');
		if(is_blank($search_record_per_page)){
			$search_record_per_page = $record_per_page;
		}
		$total_page = 1;
		
		$total_record = $this->user_download_report->count_records(true);
		if($total_record > 0){
			$total_page = ceil($total_record/$search_record_per_page);
		}
		
		$page_selected = $this->input->get_post('page_selected');
		if(is_blank($page_selected)  || $page_selected <= 0) $page_selected = 1;
		if($page_selected > $total_page) $page_selected = $total_page;
		$start_record = ($page_selected-1)*$search_record_per_page;

		$search_order_by = $this->input->get_post('search_order_by');
		if(is_blank($search_order_by)){
			$order_by = "*total_download";
			$order_by_option = "desc";
		}else{
			list($order_by, $order_by_option) = preg_split("/ /", $search_order_by, 2);
		}
		$data_search["search_order_by"] = $search_order_by;
		$sorting = array("order_by"=>$order_by , "order_by_option"=>$order_by_option);

		$this->user_download_report->set_order_by($order_by." ".$order_by_option);
		$this->user_download_report->set_limit(0,$search_limit);
		$result_list = $this->user_download_report->load_records(true);
		$result_list = $this->manage_column_top_reader_log($result_list);
		$this->db->flush_cache();
		//echo "<br>sql : ".$this->db->last_query()."<br>";
		
		$optional = "";
		$optional["search_record_per_page"] = $search_record_per_page;
		$optional["total_record"] = $total_record;
		$optional["page_selected"] = $page_selected;
		$optional["total_page"] = $total_page;
		$optional["start_record"] = $start_record;
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		if(is_root_admin_or_higher()){
		$header_list[] = array("sort_able"=>"1","title_show"=>"Aid","field_order"=>"aid","col_show"=>"aid","title_class"=>"w30 hcenter","result_class"=>"hleft");
		}
		$header_list[] = array("sort_able"=>"1","title_show"=>"User","field_order"=>"*first_name_th","col_show"=>"user_info_show","title_class"=>"w350 hcenter","result_class"=>"hleft");
		//$header_list[] = array("sort_able"=>"1","title_show"=>"Type","field_order"=>"*product_main_name","col_show"=>"product_main_name","title_class"=>"w100 hcenter","result_class"=>"hleft");
		//$header_list[] = array("sort_able"=>"1","title_show"=>"Title","field_order"=>"*parent_title","col_show"=>"parent_title","title_class"=>"w350 hcenter","result_class"=>"hleft");
		//$header_list[] = array("sort_able"=>"1","title_show"=>"Downloaded Date","field_order"=>"updated_date","col_show"=>"updated_date_txt","title_class"=>"hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"1","title_show"=>"#Download","field_order"=>"*total_download","col_show"=>"total_download","title_class"=>"w50 hcenter","result_class"=>"hcenter");
		
		if(is_var_array($result_list)){
			$result_obj = array("status" => "success", "sorting" => $sorting, "optional"=>$optional, "header_list" => $header_list, "result" => $result_list);
			echo json_encode($result_obj);
			return"";
		}else{
			$result_obj = array("status" => "warning","msg" => "No record found.");
			echo json_encode($result_obj);
			return"";
		}
	}
	
	function manage_column_top_reader_log($result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			
			$result[] = $item;
		}
		
		return $result;
	}
	
	function export_top_reader_log(){
		if(!is_owner_admin_or_higher()){
			echo "You don't have authorize to export this report.";
			return"";
		}
	
		$this->load->model($this->view_all_download_history,"user_download_report");
		$this->db->start_cache();		
		$this->db->select('view_all_download_history.*, count(*) as total_download');
		$this->db->group_by("user_aid");
		
		
		// $this->user_download_report->set_where(array("action"=>"login"));
		
		$data_search="";
		$data_where = "";
		
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;

		$search_limit = $this->input->get_post('search_limit');
		$data_search["search_limit"] = $search_limit;
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->user_download_report->set_or_like($data_where);
		
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->user_download_report->set_where_in($data_where);
		$created_date_from = $this->input->get_post('created_date_from');
		$created_date_to = $this->input->get_post('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->user_download_report->set_where($this->user_download_report->get_table_name().'.updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->user_download_report->set_where($this->user_download_report->get_table_name().'.updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}		
		
		$search_order_by = $this->input->get_post('search_order_by');
		if(is_blank($search_order_by)){
			$order_by = "*total_download";
			$order_by_option = "desc";
		}else{
			list($order_by, $order_by_option) = preg_split("/ /", $search_order_by, 2);
		}
		$data_search["search_order_by"] = $search_order_by;
		$sorting = array("order_by"=>$order_by , "order_by_option"=>$order_by_option);

		$this->user_download_report->set_order_by($order_by." ".$order_by_option);
		$this->user_download_report->set_limit(0,$search_limit);
		$result_list = $this->user_download_report->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
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
			$objPHPExcel->getProperties()->setTitle("User download List");
			$objPHPExcel->getProperties()->setDescription("User download list");
			// Set Default Style
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); 
			// Rename Sheet
			$objPHPExcel->getActiveSheet()->setTitle('User Top Reader List');
			// Set column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			// $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
			// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A1', "User");
			$objPHPExcel->getActiveSheet()->setCellValue('B1', "#Download");
			// $objPHPExcel->getActiveSheet()->setCellValue('C1', "Title");
			// $objPHPExcel->getActiveSheet()->setCellValue('D1', "Downloaded Date");
			$sharedStyle1 = new PHPExcel_Style();
			$sharedStyle1->applyFromArray($array_style_summary_title);
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:B1");
			
			$irow = 2;
			foreach($result_list as $item){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$irow, get_array_value($item,"user_info_show",""));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$irow, get_array_value($item,"total_download",""));
				// $objPHPExcel->getActiveSheet()->setCellValue('C'.$irow, get_array_value($item,"parent_title",""));
				// $objPHPExcel->getActiveSheet()->setCellValue('D'.$irow, get_array_value($item,"updated_date_txt",""));
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