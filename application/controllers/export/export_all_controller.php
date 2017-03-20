<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Export_all_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		for_owner_admin_or_higher();
		$this->data["mode"] = "backend";
		@define("thisAdminTabMenu",'report');
		@define("folderName","export/all/");
		$this->user_history_model = "User_login_history_model";
		
		$this->user_model = "User_model";
		
	}
	
	function index()
	{
		$this->show();
	}
	
	function show(){
		@define("thisAction","user_log");
		@define('thisAdminSubMenu','export_all');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . 'all_page';
		$this->data["message"] = "";
		
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
			
	function export_all($sid=""){
		if(!is_owner_admin_or_higher()){
			echo "You don't have authorize to export this report.";
			return"";
		}
	
		$this->load->model($this->user_history_model,"user_history");
		$this->db->start_cache();

		$this->load->model($this->view_all_products_with_detail,"view_all_products_with_detail_for_export");
		$tmp = array();
		$tmp["product_type_aid"] = array("1", "2");
		$this->view_all_products_with_detail_for_export->set_where_in($tmp);
		$this->view_all_products_with_detail_for_export->set_order_by('total_download desc, product_type_aid asc , product_main_aid asc , aid asc');
		// $this->view_all_products_with_detail_for_export->set_limit(0,1000);
		$result_list = $this->view_all_products_with_detail_for_export->load_records(true);
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

			$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
			$cacheSettings = array( ' memoryCacheSize ' => '256MB');
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

			$objPHPExcel = new PHPExcel();
			// Set properties
			$objPHPExcel->getProperties()->setCreator(ADMIN_EMAIL_NAME);
			$objPHPExcel->getProperties()->setLastModifiedBy(ADMIN_EMAIL_NAME);
			$objPHPExcel->getProperties()->setTitle("All Book List");
			$objPHPExcel->getProperties()->setDescription("All book list");
			// Set Default Style
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); 
			// Rename Sheet
			$objPHPExcel->getActiveSheet()->setTitle('All book list');
			// Set column width
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A1', "Type");
			$objPHPExcel->getActiveSheet()->setCellValue('B1', "Title");
			$objPHPExcel->getActiveSheet()->setCellValue('C1', "#Copy");
			$objPHPExcel->getActiveSheet()->setCellValue('D1', "Has E-Book");
			$objPHPExcel->getActiveSheet()->setCellValue('E1', "Author");
			$objPHPExcel->getActiveSheet()->setCellValue('F1', "Publisher Name");
			$objPHPExcel->getActiveSheet()->setCellValue('G1', "Category");
			$objPHPExcel->getActiveSheet()->setCellValue('H1', "Total Download");
			$sharedStyle1 = new PHPExcel_Style();
			$sharedStyle1->applyFromArray($array_style_summary_title);
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:H1");
			
			$irow = 2;
			foreach($result_list as $item){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$irow, get_array_value($item,"product_main_name",""));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$irow, get_array_value($item,"title",""));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$irow, get_array_value($item,"total_copy",""));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$irow, get_array_value($item,"has_ebook",""));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$irow, get_array_value($item,"author",""));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$irow, get_array_value($item,"publisher_name",""));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$irow, get_array_value($item,"category_link",""));
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$irow, get_array_value($item,"total_download",""));
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