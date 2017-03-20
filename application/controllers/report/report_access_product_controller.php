<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Report_access_product_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		for_owner_admin_or_higher();
		$this->data["mode"] = "backend";
		@define("thisAdminTabMenu",'report');
		@define("folderName","report/access_product/");
		$this->log_access_product_model = "Log_access_product_model";
		
	}
	
	function index()
	{
		$this->access_product_log();
	}
	
	function access_product_log($product_main_url=""){
		@define("thisAction","access_product_log");
		@define('thisAdminSubMenu','report_access_product_log');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/report/access_product/access_product_log_page';
		$this->data["message"] = "";
		
		$data_search = array();
		$data_search["updated_date_from"] = date('Y-m-01');
		$data_search["updated_date_to"] = date('Y-m-d');

		$data_search["product_main_url"] = $product_main_url;
		
		$this->data["data_search"] = $data_search;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function ajax_get_access_product_log_list($product_main_url="", $sid=""){
		if(!is_owner_admin_or_higher()){
			$result_obj = array("status" => "error","msg" => "You don't have authorize to view this report.");
			echo json_encode($result_obj);
			return"";
		}
	
		$this->load->model($this->log_access_product_model,"log_access_product");
		$this->db->start_cache();		
		// $this->log_access_product->set_where(array("action"=>"login"));
		
		$data_search="";
		$data_where = "";
		
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->input->get_post('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->log_access_product->set_or_like($data_where);
		
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->log_access_product->set_where_in($data_where);
		$updated_date_from = $this->input->get_post('updated_date_from');
		$updated_date_to = $this->input->get_post('updated_date_to');
		$data_search["updated_date_from"] = $updated_date_from;
		$data_search["updated_date_to"] = $updated_date_to;
		// echo "updated_date_from = $updated_date_from";
		
		if(!is_blank($updated_date_from)){
			$this->log_access_product->set_where($this->log_access_product->get_table_name().'.updated_date >=', get_datetime_pattern("db_date_format",$updated_date_from,"")." 00:00:00");
		}
		if(!is_blank($updated_date_to)){
			$this->log_access_product->set_where($this->log_access_product->get_table_name().'.updated_date <=', get_datetime_pattern("db_date_format",$updated_date_to,"")." 23:59:59");
		}		

		$this->log_access_product->set_where(array("all_products.product_main_url"=>$product_main_url));
		
		$record_per_page = CONST_DEFAULT_RECORD_PER_PAGE;
		$search_record_per_page = $this->input->get_post('search_record_per_page');
		if(is_blank($search_record_per_page)){
			$search_record_per_page = $record_per_page;
		}
		$total_page = 1;
		
		$total_record = $this->log_access_product->load_count_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		if($total_record > 0){
			$total_page = ceil($total_record/$search_record_per_page);
		}
		
		$page_selected = $this->input->get_post('page_selected');
		if(is_blank($page_selected)  || $page_selected <= 0) $page_selected = 1;
		if($page_selected > $total_page) $page_selected = $total_page;
		$start_record = ($page_selected-1)*$search_record_per_page;
			
		$search_order_by = $this->input->get_post('search_order_by');
		if(is_blank($search_order_by)){
			$order_by = $this->log_access_product->get_table_name().".updated_date";
			$order_by_option = "desc";
		}else{
			list($order_by, $order_by_option) = preg_split("/ /", $search_order_by, 2);
		}
		$data_search["search_order_by"] = $search_order_by;
		$sorting = array("order_by"=>$order_by , "order_by_option"=>$order_by_option);
		
		$this->log_access_product->set_order_by($order_by." ".$order_by_option);
		$this->log_access_product->set_limit($start_record,$search_record_per_page);
		$result_list = $this->log_access_product->load_records(true);
		$result_list = $this->manage_column_access_product_log($product_main_url, $result_list);
		$this->db->flush_cache();
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		
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
		$header_list[] = array("sort_able"=>"1","title_show"=>"Title","field_order"=>"*all_products.title","col_show"=>"title","title_class"=>"w350 hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"1","title_show"=>"User","field_order"=>"*user.first_name_th","col_show"=>"full_name_th","title_class"=>"w200 hcenter","result_class"=>"hleft");
		$header_list[] = array("sort_able"=>"1","title_show"=>"Date access","field_order"=>$this->log_access_product->get_table_name().".updated_date","col_show"=>"updated_date_txt","title_class"=>"w50 hcenter","result_class"=>"hcenter");
		
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
	
	function manage_column_access_product_log($product_main_url="", $result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			
			$result[] = $item;
		}
		
		return $result;
	}
		
	function export_access_product_log($product_main_url=""){
		if(!is_owner_admin_or_higher()){
			echo "You don't have authorize to export this report.";
			return"";
		}
	
		$this->load->model($this->log_access_product_model,"log_access_product");
		$this->db->start_cache();		
		// $this->log_access_product->set_where(array("action"=>"login"));
		
		$data_search="";
		$data_where = "";
		
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->input->get_post('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->log_access_product->set_or_like($data_where);
		
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->log_access_product->set_where_in($data_where);
		$updated_date_from = $this->input->get_post('updated_date_from');
		$updated_date_to = $this->input->get_post('updated_date_to');
		$data_search["updated_date_from"] = $updated_date_from;
		$data_search["updated_date_to"] = $updated_date_to;
		
		if(!is_blank($updated_date_from)){
			$this->log_access_product->set_where($this->log_access_product->get_table_name().'.updated_date >=', get_datetime_pattern("db_date_format",$updated_date_from,"")." 00:00:00");
		}
		if(!is_blank($updated_date_to)){
			$this->log_access_product->set_where($this->log_access_product->get_table_name().'.updated_date <=', get_datetime_pattern("db_date_format",$updated_date_to,"")." 23:59:59");
		}		
		
		$search_order_by = $this->input->get_post('search_order_by');
		if(is_blank($search_order_by)){
			$order_by = $this->log_access_product->get_table_name().".updated_date";
			$order_by_option = "desc";
		}else{
			list($order_by, $order_by_option) = preg_split("/ /", $search_order_by, 2);
		}
		$data_search["search_order_by"] = $search_order_by;
		$sorting = array("order_by"=>$order_by , "order_by_option"=>$order_by_option);

		$this->log_access_product->set_where(array("all_products.product_main_url"=>$product_main_url));
		
		$this->log_access_product->set_order_by($order_by." ".$order_by_option);
		$result_list = $this->log_access_product->load_records(true);
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
			$objPHPExcel->getProperties()->setTitle("Data access List");
			$objPHPExcel->getProperties()->setDescription("Data access list");
			// Set Default Style
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); 
			// Rename Sheet
			$objPHPExcel->getActiveSheet()->setTitle('Data access List');
			// Set column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A1', "Title");
			$objPHPExcel->getActiveSheet()->setCellValue('B1', "User");
			$objPHPExcel->getActiveSheet()->setCellValue('C1', "Date access");
			$sharedStyle1 = new PHPExcel_Style();
			$sharedStyle1->applyFromArray($array_style_summary_title);
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:C1");
			
			$irow = 2;
			foreach($result_list as $item){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$irow, get_array_value($item,"title",""));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$irow, get_array_value($item,"full_name_th",""));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$irow, get_array_value($item,"updated_date_txt",""));
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