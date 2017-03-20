<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Topread_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		if(CONST_HAS_TRANSACTION != "1"){
			redirect('home');
		}
		
		@define("folderName",'p_report');
		define("thisAdminTabMenu",'p_report');
		define("TXT_TITLE",'Transaction management');
		define("TXT_INSERT_TITLE",'Transaction management : Add new transaction');
		define("TXT_UPDATE_TITLE",'Transaction management : Edit transaction');
        $this->transaction_model = "Transaction_model";
        $this->product_main_model = "Product_main_model";
        $this->user_model = "User_model";
        $this->product_category_model = "Product_category_model";
        $this->book_model = "Book_model";
        $this->book_copy_model = "Book_copy_model";
        $this->magazine_model = "Magazine_model";
        $this->magazine_copy_model = "Magazine_copy_model";
        $this->shelf_history_model = "Shelf_history_model";
        $this->magazine_field_model = "Magazine_field_model";
        $this->book_field_model = "Book_field_model";
        $this->view_transaction_model = "View_transaction_model";
        
        
    }

    function create_table_view() {
        $db_drop = "DROP VIEW IF EXISTS `view_transaction`";
        $qs2 = $this->db->query($db_drop);
        $this->db->flush_cache();
        $sql2 ="CREATE VIEW view_transaction AS (SELECT `transaction`.`user_aid` , `transaction`.`parent_aid` , `user`.`cid` , `user`.`first_name_th` , `user`.`last_name_th` , `transaction`.`borrowing_date` FROM (`transaction`) INNER JOIN `user` ON `user`.`aid` = `transaction`.`user_aid` GROUP BY `transaction`.`user_aid` , `transaction`.`parent_aid` )";
        $qs2 = $this->db->query($sql2);
        $this->db->flush_cache();
    }
    function get_book() {

        $data_search="";

        $top=$this->input->get_post('top');
        if ( $top ) {
            $limit =  $top;
        } else {
            $limit = 5;
        }
       

        $created_date_from=$this->input->get_post('borrowing_date_start');
        $created_date_to=$this->input->get_post('borrowing_date_end');
        $data_search["borrowing_date_start"] = $created_date_from;
        $data_search["borrowing_date_end"] = $created_date_to;
        $data_search["top"] = $top;

        $this->session->set_userdata('TopReaderBackDataSearchSession',$data_search);
       
        $this->load->model($this->view_transaction_model, "transaction");
        $this->db->select('view_transaction.user_aid , COUNT(view_transaction.user_aid) AS theCount ,user.cid, user.first_name_th, user.last_name_th');
        $this->db->join('user', 'user.aid = view_transaction.user_aid', "INNER");
        if (!is_blank($created_date_from)) {
            $this->transaction->set_where('view_transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
        }
        if (!is_blank($created_date_to)) {
            $this->transaction->set_where('view_transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
        }
        $this->db->group_by("view_transaction.user_aid");
        $this->transaction->set_limit(0, $limit);
        $this->transaction->set_order_by("*theCount desc");
        $result_list = $this->transaction->load_records(false);
        // echo $this->db->last_query();
        $this->db->flush_cache();

        

        return $result_list;
    }
	function book(){
        @define("thisAction","p_report");
        @define('thisAdminSubMenu','top_reader');
		$this->data["title"] = DEFAULT_TITLE;
        //$this->data["init_adv_search"] = "clear";
        $this->create_table_view();
        $result_list = $this->get_book();
        $this->data["result_list"] = $result_list;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/top_reader';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	function exportbook(){
        $result_list = $this->get_book();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
					wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);

            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Rk.");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Member ID");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Full Name");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Total Item");

            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:H1");

            $irow = 2;
            $no = 1;
            foreach ($result_list as $item) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $irow, get_array_value($item, "cid", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($item, "first_name_th", "no name") . get_array_value($item, "last_name_th", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($item, "theCount", ""));
                $irow++;
                $no++;
            }

            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
    }
    function get_topborrow() {
        $top=$this->input->get_post('top');
        if ( $top ) {
            $limit =  $top;
        } else {
            $limit = 5;
        }
        $created_date_from=$this->input->get_post('borrowing_date_start');
        $created_date_to=$this->input->get_post('borrowing_date_end');

        $data_search["borrowing_date_start"] = $created_date_from;
        $data_search["borrowing_date_end"] = $created_date_to;
        $data_search["top"] = $top;

        $this->session->set_userdata('TopborrowBackDataSearchSession',$data_search);

        $this->load->model($this->transaction_model, "transaction");
        $this->db->select('transaction.user_aid , '
                . 'COUNT(transaction.user_aid) AS theCount ,'
                . 'SUM(CASE WHEN return_status = 0 THEN 1 ELSE 0 END) AS borrowing ,'
                . 'SUM(CASE WHEN return_status = 1 THEN 1 ELSE 0 END) AS borrowed ,'
                . 'SUM(CASE WHEN (return_status = 0 AND due_date < NOW()) THEN 1 ELSE 0 END) AS returned ,'
                . 'user.cid, user.first_name_th, user.last_name_th');
        $this->db->join('user', 'user.aid = transaction.user_aid', "INNER");
        if (!is_blank($created_date_from)) {
            $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
        }
        if (!is_blank($created_date_to)) {
            $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
        }
        $this->db->group_by("transaction.user_aid");
        $this->transaction->set_limit(0, $limit);
        $this->transaction->set_order_by("*theCount desc");
        $result_list = $this->transaction->load_records(false);
        #echo $this->db->last_query();
        return $result_list;
    }
	function topborrow(){
		@define("thisAction","p_report");
        @define('thisAdminSubMenu','top_borrow');
		$this->data["title"] = DEFAULT_TITLE;
        $result_list = $this->get_topborrow();
        $this->data["result_list"] = $result_list;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/top_borrow';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	function exporttopborrow(){
        $result_list = $this->get_topborrow();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
					wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);

            $objPHPExcel->getActiveSheet()->setCellValue('A1', "No.");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Barcode");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Title of copy");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('E1', "Category");
            $objPHPExcel->getActiveSheet()->setCellValue('F1', "Borrowed (Times)");

            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:H1");

            $irow = 2;
            $no = 1;
            foreach ($result_list as $item) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $irow, get_array_value($item, "barcode", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($item, "title", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($item, "product_main_name", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, get_array_value($item, "category_name", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow, get_array_value($item, "total", ""));
                $irow++;
                $no++;
            }

            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
    }
    function create_table_popular_categories() {
        $db_drop = "DROP TABLE IF EXISTS `view_popular_categories`";
        $qs2 = $this->db->query($db_drop);
        $this->db->flush_cache();
        $sql2 ="CREATE TABLE `view_popular_categories` (`aid` int(10) unsigned NOT NULL auto_increment,`product_main_name` char(100),`name` char(100),`total` int(10) ,PRIMARY KEY  (`aid`)) ";
        $qs2 = $this->db->query($sql2);
        $this->db->flush_cache();
    }
    function insert_data_popular_categories($product_main_name='',$name="",$total="") {
     
        $sql2 ="INSERT INTO `view_popular_categories` (`product_main_name`,`name`,`total`) VALUES ( '$product_main_name', '$name', '$total')";
        $qs2 = $this->db->query($sql2);
        $this->db->flush_cache();
    }
    function get_top_most_popular_categories() {
        
        $created_date_from=$this->input->get_post('borrowing_date_start');
        $created_date_to=$this->input->get_post('borrowing_date_end');
        $product_main_aid = $this->input->get_post('type');

        $data_search["borrowing_date_start"] = $created_date_from;
        $data_search["borrowing_date_end"] = $created_date_to;
        $data_search["type"] = $product_main_aid;

        $data_array = array();
        if($product_main_aid ){
            
        $this->create_table_popular_categories();
        $this->load->model($this->product_category_model, "product_category");
        $master_category = $this->product_category->load_category_by_product_main($product_main_aid);
        $this->db->flush_cache();
        if (is_var_array($master_category)) {
            foreach ($master_category as $row) {
                $cate_id = get_array_value($row, "aid", "");
                $total = 0;
                if ($product_main_aid == "7") { //แม็กกาซีน
                    $this->load->model($this->magazine_model, "magazine");
                    $this->db->select('aid');
                    $this->magazine->set_where('status', 1);
                    $this->magazine->set_like(array("category" => "," . get_array_value($row, "aid", "") . ","), "both");
                    $result = $this->magazine->load_records(false);
                } else {
                    $this->load->model($this->book_model, "book");
                    $this->db->select('aid');
                    $this->book->set_where('status', 1);
                    $this->book->set_like(array("category" => "," . get_array_value($row, "aid", "") . ","), "both");
                    $result = $this->book->load_records(false);
                    #echo $this->db->last_query();
                }
                $cate_ids = array();
                foreach ($result as $key => $item1) {
                    $cate_ids[] = $item1["aid"];
                }
                if (is_var_array($result)) {
                    $this->db->flush_cache();
                    $this->load->model($this->transaction_model, "transaction");
                    if ($product_main_aid == "6" OR $product_main_aid == "7") { //หนังสือเล่ม
                        if ($product_main_aid == "6") { //หนังสือเล่ม
                            $this->transaction->set_where('product_type_aid ', 1);
                        } else { //magazine  เล่ม
                            $this->transaction->set_where('product_type_aid ', 2);
                        }
                        $this->db->where_in('parent_aid', $cate_ids);

                        if (!is_blank($created_date_from)) {
                            $this->transaction->set_where('borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                        }
                        if (!is_blank($created_date_to)) {
                            $this->transaction->set_where('borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                        }
                        $num = $this->transaction->count_records(false);
                        #echo $this->db->last_query(); 
                    } else {
                        $this->db->flush_cache();
                        $this->load->model($this->shelf_history_model, "shelf_history");
                        $this->shelf_history->set_where('product_type_aid ', 1);
                        $this->shelf_history->set_where(array("action" => 'in'));
                        $this->db->where_in('parent_aid', $cate_ids);
                        $num = $this->shelf_history->count_records(false);
                    }
                }
                $this->insert_data_popular_categories(get_array_value($row, "product_main_name", ""),get_array_value($row, "name", ""),$num);
            }
        }
            $_sql= "SELECT * FROM (`view_popular_categories`) ORDER BY total desc";
            $qs = $this->db->query($_sql);
            $result_list = $qs->result_array();
            return $result_list;
          }else{
            return false;
          }
    }
	function top_most_popular_categories(){

		@define("thisAction","p_report");
        @define('thisAdminSubMenu','top_most_popular_categories');
        $this->data["title"] = DEFAULT_TITLE;
        
        $this->data["result_list"] = $this->get_top_most_popular_categories();
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/top_most_popular_categories';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
    function export_top_most_popular_categories() {

        $result_list = $this->get_top_most_popular_categories();

        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
					wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);

            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Rk.");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Category");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Borrowed(Times)");
            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:H1");
            $irow = 2;
            $no = 1;
            foreach ($result_list as $item) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($item, "product_main_name", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($item, "name", "no name"));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($item, "total", ""));

                $irow++;
                $no++;
            }
            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
    }
    function get_top_most_popular_item() {
        $product_main_aid = $this->input->get_post('type');
        $created_date_from=$this->input->get_post('borrowing_date_start');
        $created_date_to=$this->input->get_post('borrowing_date_end');

        $top=$this->input->get_post('top');


        if ( $top ) {
            $limit =  $top;
        } else {
            $limit = 5;
        }

        $data_search["type"] = $product_main_aid;
        $data_search["borrowing_date_start"] = $created_date_from;
        $data_search["borrowing_date_end"] = $created_date_to;
        $data_search["top"] = $top;

        $this->session->set_userdata('TopMostPopularItemBackDataSearchSession',$data_search);
        if(! $product_main_aid) $product_main_aid= 7;

        $data_where['category'] =  $this->input->get_post('cat_');
        $data_array = array();
        $this->load->model($this->product_category_model, "product_category");
        $master_category = $this->product_category->load_category_by_product_main($product_main_aid);
        $this->db->flush_cache();
        $data_where_cate = '';
        if($created_date_from!=""){
                $word1 = " AND borrowing_date >='$created_date_from' ";
           }else{
            $word1 = '';
           }
           if($created_date_to!=""){
                    $word2 = " AND borrowing_date <= '$created_date_to' ";
           }else{
            $word2 = '';
           }
        if ($product_main_aid == "7") { //แม็กกาซีน

             if($data_where['category']){
                $data_where_cate = "AND ( ";
                $i = 0;
                $num_row = count($data_where['category'])-1;
                foreach ( $data_where['category'] as $value) {
                    if($i < $num_row){
                        $data_where_cate .= "magazine.category like '%$value%' OR ";
                    }else{
                        $data_where_cate .= "magazine.category like '%$value%' ";
                    }
                    $i++;
                }
                $data_where_cate .= ") ";
            }
            $where_product_main = " AND magazine.product_main_aid = ".$product_main_aid;
            $where_product_type = " AND transaction.product_type_aid = 2";
            $_sql = 'SELECT magazine.product_main_aid, product_main.name as product_main_name, `magazine`.`title`, `transaction`.`barcode`, COUNT(magazine.aid) as total ,magazine.category ,magazine_copy.call_number '
                    . 'FROM (`magazine_copy`) '
                    . 'LEFT JOIN `magazine` ON `magazine`.`aid` = `magazine_copy`.`parent_aid` '
                    . 'LEFT JOIN `transaction` ON `transaction`.`copy_aid` = `magazine_copy`.`aid` '
                    . 'LEFT JOIN `product_main` ON `product_main`.`aid` =  `magazine`.product_main_aid '
                    . 'WHERE 1 '.$word1.$word2.$where_product_type.' AND `magazine`.`status` = 1 '.$data_where_cate.$where_product_main.' GROUP BY `magazine`.`aid` '
                    . 'ORDER BY `total` desc '
                    . 'LIMIT '.$limit;
                    $qs = $this->db->query($_sql);
                    $result = $qs->result();
 
        } else {
     
             if($data_where['category']){
                $data_where_cate = "AND ( ";
                $i = 0;
                $num_row = count($data_where['category'])-1;
                foreach ( $data_where['category'] as $value) {
                    if($i < $num_row){
                        $data_where_cate .= "book.category like '%$value%' OR ";
                    }else{
                        $data_where_cate .= "book.category like '%$value%' ";
                    }
                    $i++;
                }
                $data_where_cate .= ") ";
            }
            $this->load->model($this->book_model, "book");
            $where_product_type = "AND transaction.product_type_aid = 1";
            if ($product_main_aid == "6") {
                $where_product_main = " AND book.product_main_aid = ".$product_main_aid;
            }else{
                $where_product_main = " AND book.product_main_aid = ".$product_main_aid;
            }
            $_sql = 'SELECT book.product_main_aid, product_main.name as product_main_name, `book`.`title`, `transaction`.`barcode`, COUNT(book.aid) as total ,book.category ,book_copy.call_number '
                    . 'FROM (`book_copy`) '
                    . 'LEFT JOIN `book` ON `book`.`aid` = `book_copy`.`parent_aid` '
                    . 'LEFT JOIN `transaction` ON `transaction`.`copy_aid` = `book_copy`.`aid` '
                    . 'LEFT JOIN `product_main` ON `product_main`.`aid` =  `book`.product_main_aid '
                    . 'WHERE 1 '.$word1.$word2.$where_product_type.' AND `book`.`status` = 1 '.$data_where_cate.$where_product_main.' GROUP BY `book`.`aid` '
                    . 'ORDER BY `total` desc '
                    . 'LIMIT '.$limit;
                    $qs = $this->db->query($_sql);
                    $result = $qs->result();
           
        }

        $data  = array();
        foreach ($result as $key => $value) {
            $data[$key] = $value;
            $data[$key]->category_name= $this->get_category_name($value->category);
        }
       
        return $data;
    }
    function get_category_name($category= false) {
            $cate= explode(",", $category);
            $data_merge = '';
            $this->load->model($this->product_category_model, "product_category");
            foreach ($cate as  $value) {
                if( $value ){
                    $this->product_category->set_where('aid', $value);
                    $rs=  $this->product_category->load_record();
                     if(is_var_array($rs)){
                        if($data_merge) $data_merge = $data_merge .','. get_array_value($rs,"name","");
                        else $data_merge  = get_array_value($rs,"name","");
                     }
                }
           }

           return $data_merge;
    }
	function top_most_popular_item(){

		@define("thisAction","p_report");
        @define('thisAdminSubMenu','top_most_popular_item');
		$this->data["title"] = DEFAULT_TITLE;
        $result_list = $this->get_top_most_popular_item();
        $this->data["result_list"] = $result_list;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/top_most_popular_item';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	function export_top_most_popular_item(){
		$result_list = $this->get_top_most_popular_item();

        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);


            $objPHPExcel->getActiveSheet()->setCellValue('A1', "No.");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Barcode");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Call No.");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Title of Copy");
            $objPHPExcel->getActiveSheet()->setCellValue('E1', "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('F1', "Category");
            $objPHPExcel->getActiveSheet()->setCellValue('G1', "Borrowed(Times)");
            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:H1");
            $irow = 2;
            $no = 1;
            foreach ($result_list as $key=>$item) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, $item->barcode);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, $item->call_number);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, $item->title);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, $item->product_main_name);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow, $item->category_name);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $irow, $item->total);

                $irow++;
                $no++;
            }
            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
	}
	function get_cataloging_summary(){
        $created_date_from=$this->input->get_post('borrowing_date_start');
        $created_date_to=$this->input->get_post('borrowing_date_end');

        $year=$this->input->get_post('year');
        if ( ! $year ) $year= date("Y");
        $master_product_main_ori = $this->data["master_product_main"];
        if(is_var_array($master_product_main_ori)){
            foreach ($master_product_main_ori as $item) {
                $product_type_aid = get_array_value($item,"product_type_aid","0");
                if($product_type_aid == "1" || $product_type_aid == "2"){
                    $master_product_main[] = $item;
                }
            }
        }
        if(is_var_array($master_product_main)){
            $_sql= "SELECT DATE_FORMAT(view_all_product_copies_with_detail.created_date,'%m') as m  , COUNT(aid) as total ,product_main_aid FROM (`view_all_product_copies_with_detail`) WHERE ( view_all_product_copies_with_detail.created_date like '%$year%' ) group by DATE_FORMAT(view_all_product_copies_with_detail.created_date,'%m') , view_all_product_copies_with_detail.product_main_aid";
            $qs = $this->db->query($_sql);
            $result_list = $qs->result_array();
            $data  = array();
            foreach ($result_list as  $key=>$item) {
               $data[get_array_value($item,"product_main_aid","")][get_array_value($item,"m","")]['total'] = get_array_value($item,"total","");
            }
            return $data;
        }
    }
	function cataloging_summary(){
		@define("thisAction","p_report");
        @define('thisAdminSubMenu','cataloging_summary');
		$this->data["title"] = DEFAULT_TITLE;
        $result_list = $this->get_cataloging_summary();
        $this->data["result_list"] =  $result_list;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/cataloging_summary';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	function exportcataloging_summary(){
         $master_product_main_ori = $this->data["master_product_main"];
		 $result_list = $this->get_cataloging_summary();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);

            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Jan");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Feb");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Mar");
            $objPHPExcel->getActiveSheet()->setCellValue('E1', "Apr");
            $objPHPExcel->getActiveSheet()->setCellValue('F1', "May");
            $objPHPExcel->getActiveSheet()->setCellValue('G1', "Jun");
            $objPHPExcel->getActiveSheet()->setCellValue('H1', "Jul");
            $objPHPExcel->getActiveSheet()->setCellValue('I1', "Aug");
            $objPHPExcel->getActiveSheet()->setCellValue('J1', "Sep");
            $objPHPExcel->getActiveSheet()->setCellValue('K1', "Oct");
            $objPHPExcel->getActiveSheet()->setCellValue('L1', "Nov");
            $objPHPExcel->getActiveSheet()->setCellValue('M1', "Dec");
            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:M1");
            $irow = 2;
            $no = 1;
            foreach ($master_product_main_ori as $key=>$item) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, get_array_value($item, "name", ""));
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($result_list[$key]["01"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($result_list[$key]["02"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($result_list[$key]["03"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, get_array_value($result_list[$key]["04"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow, get_array_value($result_list[$key]["05"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $irow, get_array_value($result_list[$key]["06"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $irow, get_array_value($result_list[$key]["07"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $irow, get_array_value($result_list[$key]["08"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $irow, get_array_value($result_list[$key]["09"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $irow, get_array_value($result_list[$key]["10"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $irow, get_array_value($result_list[$key]["11"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $irow, get_array_value($result_list[$key]["12"],"total","0"));

                $irow++;
                $no++;
            }
             
            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
	}
    function get_circulation_summary(){
            $year=$this->input->get_post('year');
            if ( ! $year ) $year= date("Y");
            $data['borrowed']  = '';
            $data['returned']  = ''; 
            $data_where = array();
            $_sql= "SELECT DATE_FORMAT(transaction.borrowing_date,'%m') as m  , COUNT(aid) as total FROM (`transaction`) WHERE ( transaction.borrowing_date like '%$year%' ) group by DATE_FORMAT(transaction.borrowing_date,'%m')";
            $qs = $this->db->query($_sql);
            $result_list = $qs->result_array();
            if(is_var_array($result_list)){
                foreach ($result_list as  $key=>$item) {
                    $data['borrowed'][get_array_value($item,"m","")]['total'] = get_array_value($item,"total","");
                }
            }
            $this->db->flush_cache();
            $_sql2= "SELECT DATE_FORMAT(transaction.returning_date,'%m') as m  , COUNT(aid) as total FROM (`transaction`) WHERE ( transaction.returning_date like '%$year%' ) AND return_status = '1' group by DATE_FORMAT(transaction.returning_date,'%m')";
            $qs2 = $this->db->query($_sql2);
            $result_list2 = $qs2->result_array();
            if(is_var_array($result_list2)){
                foreach ($result_list2 as  $key=>$item) {
                    $data['returned'][get_array_value($item,"m","")]['total'] = get_array_value($item,"total","");
                }
            }
            return $data;
    }
	function circulation_summary(){
		@define("thisAction","p_report");
        @define('thisAdminSubMenu','circulation_summary');
		$this->data["title"] = DEFAULT_TITLE;
        $this->data["result_list"] = $this->get_circulation_summary();
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/circulation_summary';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	function exportcirculation_summary(){
        $year=$this->input->get_post('year');
        if ( ! $year ) $year= date("Y");
        $result_list = $this->get_circulation_summary();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
             $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Report: Circulation Summary | Transaction:  ".$year);

            $objPHPExcel->getActiveSheet()->setCellValue('A2', "Circulation");
            $objPHPExcel->getActiveSheet()->setCellValue('B2', "Jan");
            $objPHPExcel->getActiveSheet()->setCellValue('C2', "Feb");
            $objPHPExcel->getActiveSheet()->setCellValue('D2', "Mar");
            $objPHPExcel->getActiveSheet()->setCellValue('E2', "Apr");
            $objPHPExcel->getActiveSheet()->setCellValue('F2', "May");
            $objPHPExcel->getActiveSheet()->setCellValue('G2', "Jun");
            $objPHPExcel->getActiveSheet()->setCellValue('H2', "Jul");
            $objPHPExcel->getActiveSheet()->setCellValue('I2', "Aug");
            $objPHPExcel->getActiveSheet()->setCellValue('J2', "Sep");
            $objPHPExcel->getActiveSheet()->setCellValue('K2', "Oct");
            $objPHPExcel->getActiveSheet()->setCellValue('L2', "Nov");
            $objPHPExcel->getActiveSheet()->setCellValue('M2', "Dec");
            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            // $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:M1");
            $irow = 3;
            $irow2 = 4;
            $no = 1;

            // foreach ($master_product_main_ori as $key=>$item) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, 'Borrowed (Times)');
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($result_list['borrowed']["01"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($result_list['borrowed']["02"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($result_list['borrowed']["03"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, get_array_value($result_list['borrowed']["04"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow, get_array_value($result_list['borrowed']["05"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $irow, get_array_value($result_list['borrowed']["06"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $irow, get_array_value($result_list['borrowed']["07"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $irow, get_array_value($result_list['borrowed']["08"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $irow, get_array_value($result_list['borrowed']["09"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $irow, get_array_value($result_list['borrowed']["10"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $irow, get_array_value($result_list['borrowed']["11"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $irow, get_array_value($result_list['borrowed']["12"],"total","0"));

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow2, 'Returned (Times)');
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow2, get_array_value($result_list['returned']["01"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow2, get_array_value($result_list['returned']["02"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow2, get_array_value($result_list['returned']["03"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow2, get_array_value($result_list['returned']["04"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow2, get_array_value($result_list['returned']["05"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $irow2, get_array_value($result_list['returned']["06"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $irow2, get_array_value($result_list['returned']["07"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $irow2, get_array_value($result_list['returned']["08"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $irow2, get_array_value($result_list['returned']["09"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $irow2, get_array_value($result_list['returned']["10"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $irow2, get_array_value($result_list['returned']["11"],"total","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $irow2, get_array_value($result_list['returned']["12"],"total","0"));

            //     $irow++;
            //     $no++;
            // }
             

            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
	}	
    function get_overdue_item(){
        $data['borrowed']  = '';
        $data['overdue']  = ''; 
        $data['percentage']  = ''; 
        $year=$this->input->get_post('year');
       
        $created_date_from = $this->input->get_post('borrowing_date_start');
        $created_date_to = $this->input->get_post('borrowing_date_end');
        $this->load->model($this->transaction_model, "transaction");
        $this->db->select('COUNT(transaction.user_aid) AS theCount');
        if (!is_blank($created_date_from)) {
            $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
        }
        if (!is_blank($created_date_to)) {
            $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
        }

        $result_list = $this->transaction->load_records(false);
      
        if(is_var_array($result_list))  $data['borrowed']  = $result_list[0]['theCount'];

        $this->db->flush_cache();
        $this->db->select('COUNT(transaction.user_aid) AS theCount');
        if (!is_blank($created_date_from)) {
            $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
        }
        if (!is_blank($created_date_to)) {
            $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
        }
        $this->transaction->set_where('transaction.return_status', '0');
        $result_list = $this->transaction->load_records(false);
        if(is_var_array($result_list))  $data['overdue']  = $result_list[0]['theCount'];
         $data['percentage'] = ($data['overdue'] / $data['borrowed']) * 100 ;
        return $data;
    }
	function overdue_item(){
		@define("thisAction","p_report");
        @define('thisAdminSubMenu','overdue_item');
		$this->data["title"] = DEFAULT_TITLE;
        $this->data["result_list"] = $this->get_overdue_item();
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/overdue_item';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	function exportoverdue_item(){
        $created_date_from = $this->input->get_post('borrowing_date_start');
        $created_date_to = $this->input->get_post('borrowing_date_end');
        $date_from = explode("-", $created_date_from);
        $date_from = $date_from[2].'/'. $date_from[1]. '/'. $date_from[0];
        $date_to = explode("-", $created_date_to);
        $date_to = $date_to[2].'/'. $date_to[1]. '/'. $date_to[0];

		$result_list = $this->get_overdue_item();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Report: Overdue Items Rating | Transaction:  ".$date_from. " - " .$date_to);

            $objPHPExcel->getActiveSheet()->setCellValue('A2', "Borrowed (Items)");
            $objPHPExcel->getActiveSheet()->setCellValue('B2', "Overdue (Items)");
            $objPHPExcel->getActiveSheet()->setCellValue('C2', "Overdue Rating(%)");

            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            // $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:M1");
            $irow = 3;
            $no = 1;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, get_array_value($result_list,"borrowed","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($result_list,"overdue","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($result_list,"percentage","0"));

            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
	}
    function get_member_usage(){
        $data = array();
        $created_date_from = $this->input->get_post('borrowing_date_start');
        $created_date_to =$this->input->get_post('borrowing_date_end');
        $aid = $this->input->get_post('aid');
        $type = $this->input->get_post('type');
        $word = $this->input->get_post('word');
        $data['borrowing']  = '';
        $data['overdue']  = '';
        $data['borrowed']  = '';

        $this->load->model($this->product_main_model, "product_main");
        $this->db->where_in('aid', array(1,2,8));
        $this->product_main->set_order_by("name asc");
        $result_list = $this->product_main->load_records(false);
        #echo "sql : ".$this->db->last_query();
        $data['product_main_time'] = $result_list;
                
        $this->db->flush_cache();
        $this->load->model($this->product_main_model, "product_main");
        $this->db->where_not_in('aid', array(5));
        $this->product_main->set_order_by("name asc");
        $result_list = $this->product_main->load_records(false);
        #echo "sql : ".$this->db->last_query();
        $data['product_main_item'] = $result_list;
                
        $this->db->flush_cache();


        if($aid){
            $this->load->model($this->user_model,'user');
            $this->user->set_where("user.aid",$aid);
            $user_detail = $this->user->load_record(true);

            if(is_var_array($user_detail)){
                $data['user_detail'] = $user_detail;
            }else{
               $data['user_detail'] = '';
            }
            //echo "sql:".$this->db->last_query();
            if($type == 1){
                $this->load->model($this->transaction_model, "transaction");
                $this->db->select('COUNT(transaction.user_aid) AS theCount');
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->db->where_in('product_type_aid', array(1,2));
                $this->transaction->set_where(array("return_status"=>"1"));
                $this->db->where('transaction.returning_date IS NOT NULL');
                $result_list = $this->transaction->load_records(false);
                #echo $this->db->last_query();

                if(is_var_array($result_list)) $data['circulation']['borrowed']  = $result_list[0]['theCount'];

                $this->db->flush_cache();
                $this->db->select('COUNT(transaction.user_aid) AS theCount');
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->transaction->set_where(array("return_status"=>"0"));
                $this->transaction->set_where('transaction.due_date <', date('Y-m-d'));
                $this->db->where_in('product_type_aid', array(1,2));
                $result_list = $this->transaction->load_records(false);
                #echo $this->db->last_query();
                if(is_var_array($result_list))  $data['circulation']['overdue']  = $result_list[0]['theCount'];

                $this->db->flush_cache();

                $this->db->select('COUNT(transaction.user_aid) AS theCount');
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->transaction->set_where(array("return_status"=>"0"));
                $this->transaction->set_where('transaction.due_date >=', date('Y-m-d'));
                $this->db->where_in('product_type_aid', array(1,2));
                $result_list = $this->transaction->load_records(false);
                if(is_var_array($result_list))  $data['circulation']['borrowing']  = $result_list[0]['theCount'];

                $this->db->flush_cache();

                
                $this->db->select('COUNT(transaction.aid) as total , main.product_main_aid, transaction.aid , transaction.copy_aid, transaction.parent_aid ,transaction.user_aid');
                $this->db->join('book_copy AS copy','copy.aid = transaction.copy_aid', "left");
                $this->db->join('book AS main','main.aid = copy.parent_aid', "left");
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->db->where_in('main.product_main_aid', array(1,8));
                $this->db->group_by("main.product_main_aid");
                $result_list = $this->transaction->load_records(false);
                #echo "sql : ".$this->db->last_query();
                $data_transaction = array();
                foreach ($result_list as $key => $value) {
                    $data_transaction[get_array_value($value,"product_main_aid","0")]['total'] = get_array_value($value,"total","0");
                }

                $this->db->flush_cache();

                $this->db->select('COUNT(transaction.aid) as total , main.product_main_aid, transaction.aid , transaction.copy_aid, transaction.parent_aid ,transaction.user_aid');
                $this->db->join('magazine_copy AS copy','copy.aid = transaction.copy_aid', "left");
                $this->db->join('magazine AS main','main.aid = copy.parent_aid', "left");
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->db->where_in('main.product_main_aid', array(2));
                $this->db->group_by("main.product_main_aid");
                $result_list = $this->transaction->load_records(false);
               # echo "sql : ".$this->db->last_query();
                foreach ($result_list as $key => $value) {
                    $data_transaction[get_array_value($value,"product_main_aid","0")]['total'] = get_array_value($value,"total","0");
                }
                $data['transaction_time'] = $data_transaction;


                $this->db->flush_cache();

                $this->db->select('COUNT(DISTINCT transaction.copy_aid) as total , main.product_main_aid, transaction.aid , transaction.copy_aid, transaction.parent_aid ,transaction.user_aid');
                $this->db->join('book_copy AS copy','copy.aid = transaction.copy_aid', "left");
                $this->db->join('book AS main','main.aid = copy.parent_aid', "left");
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                //$this->db->where_in('main.product_main_aid', array(1,2,5,6,8,9));
                $this->db->where_in('main.product_main_aid', array(1,8));
                $this->db->group_by("main.product_main_aid");
                $result_list = $this->transaction->load_records(false);
                #echo "sql : ".$this->db->last_query();
                $data_transaction = array();
                foreach ($result_list as $key => $value) {
                    $data_transaction[get_array_value($value,"product_main_aid","0")]['total'] = get_array_value($value,"total","0");
                }

                $this->db->flush_cache();

                $this->db->select('COUNT(DISTINCT transaction.copy_aid) as total , main.product_main_aid, transaction.aid , transaction.copy_aid, transaction.parent_aid ,transaction.user_aid');
                $this->db->join('magazine_copy AS copy','copy.aid = transaction.copy_aid', "left");
                $this->db->join('magazine AS main','main.aid = copy.parent_aid', "left");
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                // $this->db->where_in('main.product_main_aid', array(3,7));
                 $this->db->where_in('main.product_main_aid', array(2));
                $this->db->group_by("main.product_main_aid");
                $result_list = $this->transaction->load_records(false);
                #echo "sql : ".$this->db->last_query();
                foreach ($result_list as $key => $value) {
                    $data_transaction[get_array_value($value,"product_main_aid","0")]['total'] = get_array_value($value,"total","0");
                }
                $data['transaction_item'] = $data_transaction;

            }
            else if($type == 2){
                $this->load->model($this->transaction_model, "transaction");
                $this->db->select('aid,copy_aid,product_type_aid');
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->db->where_in('product_type_aid', array(1,2));
                $this->transaction->set_where(array("return_status"=>"1"));
                $this->db->where('transaction.returning_date IS NOT NULL');
                $result_list = $this->transaction->load_records(false);
                $this->db->last_query();
                $this->db->flush_cache();
                $data['borrowed'] = '';
                if(is_var_array($result_list)){
                    foreach ($result_list as $key => $value) {
                        if(get_array_value($value,"product_type_aid","0") == 1){
                            $this->db->select('main.title as book_title,copy.barcode,copy.call_number,copy.copy_title,main.category,p_main.name as product_main_name ,borrowing_date,due_date,return_status,returning_date ');
                            $this->db->join('book_copy AS copy','copy.aid = transaction.copy_aid', "left");
                            $this->db->join('book AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->transaction->set_where("transaction.aid",get_array_value($value,"aid","0"));
                            $result_list1 = $this->transaction->load_records(false);
                            if(is_var_array($result_list1)){
                                $data['borrowed'][get_array_value($value,"aid","0")]= $result_list1[0];
                                $data['borrowed'][get_array_value($value,"aid","0")]['category_name']= $this->get_category_name(get_array_value($result_list1[0],"category","0"));
                                
                            }
                            $this->db->flush_cache();
                            
                        }else{
                            $this->db->select('main.title as book_title,copy.barcode,call_number,copy.copy_title,main.category,p_main.name as product_main_name,borrowing_date,due_date,return_status,returning_date ');
                            $this->db->join('magazine_copy AS copy','copy.aid = transaction.copy_aid', "left");
                            $this->db->join('magazine AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->transaction->set_where("transaction.aid",get_array_value($value,"aid","0"));
                          
                            $result_list1 = $this->transaction->load_records(false);
                            if(is_var_array($result_list1)){
                                $data['borrowed'][get_array_value($value,"aid","0")]= $result_list1[0];
                                $data['borrowed'][get_array_value($value,"aid","0")]['category_name']= $this->get_category_name(get_array_value($result_list1[0],"category","0"));
                                
                            }
                            $this->db->flush_cache();
                            
                        }
                        
                    }

                }  
             
                $this->db->select('aid,copy_aid,product_type_aid');
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->transaction->set_where(array("return_status"=>"0"));
                $this->transaction->set_where('transaction.due_date <', date('Y-m-d'));
                $this->db->where_in('product_type_aid', array(1,2));
                $result_list = $this->transaction->load_records(false);
                #echo $this->db->last_query();
                $this->db->flush_cache();
                $data['overdue'] = '';
                if(is_var_array($result_list)){
                    foreach ($result_list as $key => $value) {
                        if(get_array_value($value,"product_type_aid","0") == 1){
                            $this->db->select('main.title as book_title,copy.barcode,copy.call_number,copy.copy_title,main.category,p_main.name as product_main_name ,borrowing_date,due_date,return_status,returning_date ');
                            $this->db->join('book_copy AS copy','copy.aid = transaction.copy_aid', "left");
                            $this->db->join('book AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->transaction->set_where("transaction.aid",get_array_value($value,"aid","0"));
                            $result_list1 = $this->transaction->load_records(false);
                            #echo $this->db->last_query();
                            if(is_var_array($result_list1)){
                                $data['overdue'][get_array_value($value,"aid","0")]= $result_list1[0];
                                $data['overdue'][get_array_value($value,"aid","0")]['category_name']= $this->get_category_name(get_array_value($result_list1[0],"category","0"));
                            }
                            $this->db->flush_cache();
                            
                        }else{
                            $this->db->select('main.title as book_title,copy.barcode,call_number,copy.copy_title,main.category,p_main.name as product_main_name,borrowing_date,due_date,return_status,returning_date ');
                            $this->db->join('magazine_copy AS copy','copy.aid = transaction.copy_aid', "left");
                            $this->db->join('magazine AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->transaction->set_where("transaction.aid",get_array_value($value,"aid","0"));
                            $result_list1 = $this->transaction->load_records(false);
                            if(is_var_array($result_list1)){
                                $data['overdue'][get_array_value($value,"aid","0")]= $result_list1[0];
                                $data['overdue'][get_array_value($value,"aid","0")]['category_name']= $this->get_category_name(get_array_value($result_list1[0],"category","0"));
                                
                            } 

                            $this->db->flush_cache();
                            
                        }
                        
                    }

                }

                $this->db->select('aid,copy_aid,product_type_aid');
                if (!is_blank($created_date_from)) {
                    $this->transaction->set_where('transaction.borrowing_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->transaction->set_where('transaction.borrowing_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->transaction->set_where("user_aid",$aid);
                $this->transaction->set_where(array("return_status"=>"0"));
                $this->transaction->set_where('transaction.due_date >=', date('Y-m-d'));
                $this->db->where_in('product_type_aid', array(1,2));
                $result_list = $this->transaction->load_records(false);
                $this->db->last_query();
                $this->db->flush_cache();
                $data['borrowing'] = '';
                if(is_var_array($result_list)){
                    foreach ($result_list as $key => $value) {
                        
                        if(get_array_value($value,"product_type_aid","0") == 1){
                            $this->db->select('main.title as book_title,copy.barcode,copy.call_number,copy.copy_title,main.category,p_main.name as product_main_name ,borrowing_date,due_date,return_status,returning_date ');
                            $this->db->join('book_copy AS copy','copy.aid = transaction.copy_aid', "left");
                            $this->db->join('book AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->transaction->set_where("transaction.aid",get_array_value($value,"aid","0"));
                            $result_list1 = $this->transaction->load_records(false);
                            if(is_var_array($result_list1)){
                                $data['borrowing'][get_array_value($value,"aid","0")]= $result_list1[0];
                                $data['borrowing'][get_array_value($value,"aid","0")]['category_name']= $this->get_category_name(get_array_value($result_list1[0],"category","0"));
                                
                            } 
                            $this->db->flush_cache();
                            
                        }else{
                            $this->db->select('main.title as book_title,copy.barcode,copy.call_number,copy.copy_title,main.category,p_main.name as product_main_name,borrowing_date,due_date,return_status,returning_date ');
                            $this->db->join('magazine_copy AS copy','copy.aid = transaction.copy_aid', "left");
                            $this->db->join('magazine AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->transaction->set_where("transaction.aid",get_array_value($value,"aid","0"));
                            $result_list1 = $this->transaction->load_records(false);
                            if(is_var_array($result_list1)){
                                $data['borrowing'][get_array_value($value,"aid","0")]= $result_list1[0];
                                $data['borrowing'][get_array_value($value,"aid","0")]['category_name']= $this->get_category_name(get_array_value($result_list1[0],"category","0"));
                                
                            } 
                            $this->db->flush_cache();
                            
                        }
                        
                    }

                }

            }else if($type == 3){
                $data['downloaded'] = '';
                $this->load->model($this->shelf_history_model, "shelf_history");
                if (!is_blank($created_date_from)) {
                    $this->shelf_history->set_where('shelf_history.updated_date >=', get_datetime_pattern("db_date_format", $created_date_from, "") . " 00:00:00");
                }
                if (!is_blank($created_date_to)) {
                    $this->shelf_history->set_where('shelf_history.updated_date <=', get_datetime_pattern("db_date_format", $created_date_to, "") . " 23:59:59");
                }
                $this->shelf_history->set_where('user_aid ', $aid);
                $this->shelf_history->set_where(array("action" => 'in'));
                $this->db->where_in('product_type_aid', array(1,2));
                $result_list = $this->shelf_history->load_records(false);
                #echo $this->db->last_query();
                $this->db->flush_cache();

                if(is_var_array($result_list)){
                    foreach ($result_list as $key => $value) {
                        if(get_array_value($value,"product_type_aid","0") == 1){
                            $this->db->select('user.email ,main.title as book_title,copy.barcode,copy.call_number,copy.copy_title,main.category,p_main.name as product_main_name,shelf_history.updated_date ');
                            $this->db->join('user','user.aid = shelf_history.user_aid', "left");
                            $this->db->join('book_copy AS copy','copy.aid = shelf_history.copy_aid', "left");
                            $this->db->join('book AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->shelf_history->set_where("shelf_history.aid",get_array_value($value,"aid","0"));
                            $result_list1 = $this->shelf_history->load_records(false);
                            #echo $this->db->last_query();
                           
                            if(is_var_array($result_list1)){
                                $data['downloaded'][get_array_value($value,"aid","0")]= $result_list1[0];
                            }
                            $this->db->flush_cache();
                        }else{
                            $this->db->select('user.email ,main.title as book_title,copy.barcode,call_number,copy.copy_title,main.category,p_main.name as product_main_name,  shelf_history.updated_date ');
                            $this->db->join('user','user.aid = shelf_history.user_aid', "left");
                            $this->db->join('magazine_copy AS copy','copy.aid = shelf_history.copy_aid', "left");
                            $this->db->join('magazine AS main','main.aid = copy.parent_aid', "left");
                            $this->db->join('product_main AS p_main','p_main.aid = main.product_main_aid', "left");
                            $this->shelf_history->set_where("shelf_history.aid",get_array_value($value,"aid","0"));
                            $result_list1 = $this->shelf_history->load_records(false);
                            #echo $this->db->last_query();
                            if(is_var_array($result_list1)){
                                $data['downloaded'][get_array_value($value,"aid","0")]= $result_list1[0];
                                
                            } 
                            $this->db->flush_cache();
                            
                        }
                        
                    }

                }
            }

             
        }
        return $data;
    }
	function member_usage(){
		@define("thisAction","p_report");
        @define('thisAdminSubMenu','member_usage');
		$this->data["title"] = DEFAULT_TITLE;
        $this->data["result_list"]  = $this->get_member_usage();
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/member_usage';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}

    function exportmember_usage_item(){
      $type = $this->input->get_post('type');
      if( $type == 1){
        //
      }else if( $type == 2){
        $this->exportmember_usage_circulation();
      }else if( $type == 3){
        $this->exportmember_usage_download();
      }
    }
    function dateDiff($strDate1, $strDate2) {
        return (strtotime($strDate2) - strtotime($strDate1)) / ( 60 * 60 * 24 );  // 1 day = 60*60*24
    }
    function exportmember_usage_circulation(){
        $result_list  = $this->get_member_usage();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);

            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            
            // $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:M1");

            $user_detail = $result_list['user_detail'];
            $gender = (get_array_value($user_detail,"gender","0")=="f")? "Female":"Male";
            $status = (get_array_value($user_detail,"status","0")=="1")? "Active":"Inactive";

            $objPHPExcel->getActiveSheet()->setCellValue('A1', "User Code:  ".get_array_value($user_detail,"cid","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Gender:  ". $gender);
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Status:  ". $status);
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Point:  ".get_array_value($user_detail,"point_remain","0"));

            $objPHPExcel->getActiveSheet()->setCellValue('A2', "Email:  ".get_array_value($user_detail,"email","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('B2', "Department:  ".get_array_value($user_detail,"department_name","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('C2', "User role:  ".get_array_value($user_detail,"user_role_name","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('D2', "Last Login:  ".get_array_value($user_detail,"last_login","0"));

            $objPHPExcel->getActiveSheet()->setCellValue('A3', "Tel:  ".get_array_value($user_detail,"contact_number","0"));
         
            $borrowing = $result_list['borrowing'];
            $overdue= $result_list["overdue"];
            $borrowed= $result_list["borrowed"];

            $objPHPExcel->getActiveSheet()->setCellValue('A5', "borrowing");
            $objPHPExcel->getActiveSheet()->setCellValue('A6', "No");
            $objPHPExcel->getActiveSheet()->setCellValue('B6', "Barcode");
            $objPHPExcel->getActiveSheet()->setCellValue('C6', "Title of Copy");
            $objPHPExcel->getActiveSheet()->setCellValue('D6', "Category");
            $objPHPExcel->getActiveSheet()->setCellValue('E6', "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('F6', "Borrowed Date");
            $objPHPExcel->getActiveSheet()->setCellValue('G6', "Due Date");
            $objPHPExcel->getActiveSheet()->setCellValue('H6', "Delayed Days");
            $irow = 7;
            $no = 1;

             foreach ($borrowing as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($value,"Barcode","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($value,"book_title","") . get_array_value($value,"copy_title",""));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($value,"category_name","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, get_array_value($value,"product_main_name","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow, date("d/m/Y", strtotime(get_array_value($value,"borrowing_date","0"))));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $irow, date("d/m/Y", strtotime(get_array_value($value,"due_date","0"))));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $irow, $this->dateDiff(get_array_value($value,"due_date","0"),date("Y-m-d")));
                $no ++ ;
                $irow ++ ;
            }
            
            $h_irow2 =  $irow + 1;
            $h_irow22 = $irow + 2;
            $irow2 =  $irow + 3;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $h_irow2, "borrowing");
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $h_irow22, "No");
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $h_irow22, "Barcode");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $h_irow22, "Title of Copy");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $h_irow22, "Category");
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $h_irow22, "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $h_irow22, "Borrowed Date");
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $h_irow22, "Due Date");
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $h_irow22, "Delayed Days");

            foreach ($overdue as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow2, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow2, get_array_value($value,"Barcode","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow2, get_array_value($value,"book_title","").get_array_value($value,"copy_title",""));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow2, get_array_value($value,"category_name","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow2, get_array_value($value,"product_main_name","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow2, date("d/m/Y", strtotime(get_array_value($value,"borrowing_date","0"))));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $irow2, date("d/m/Y", strtotime(get_array_value($value,"due_date","0"))));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $irow2, $this->dateDiff(get_array_value($value,"due_date","0"),date("Y-m-d")));
                $no ++ ;
                $irow2 ++ ;
            }

            $h_irow3 =  $irow2 + 1;
            $h_irow33 =  $irow2 + 2;
            $irow3 =  $irow2 + 3;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $h_irow3, "borrowing");
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $h_irow33, "No");
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $h_irow33, "Barcode");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $h_irow33, "Title of Copy");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $h_irow33, "Category");
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $h_irow33, "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $h_irow33, "Borrowed Date");
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $h_irow33, "Due Date");
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $h_irow33, "Returned Date");
            $no = 1;
            foreach ($borrowed as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow3, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow3, get_array_value($value,"Barcode","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow3, get_array_value($value,"book_title","") . get_array_value($value,"copy_title",""));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow3, get_array_value($value,"category_name","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow3, get_array_value($value,"product_main_name","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $irow3, date("d/m/Y", strtotime(get_array_value($value,"borrowing_date","0"))));
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $irow3, date("d/m/Y", strtotime(get_array_value($value,"due_date","0"))));
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $irow3, get_array_value($value,"returning_date","0"));
                $no ++ ;
                $irow3 ++ ;
            }

            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
    }
    function exportmember_usage_download(){
        $result_list  = $this->get_member_usage();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);

            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $user_detail = $result_list['user_detail'];
            //$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:M1");
            $gender = (get_array_value($user_detail,"gender","0")=="f")? "Female":"Male";
            $status = (get_array_value($user_detail,"status","0")=="1")? "Active":"Inactive";
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "User Code:  ".get_array_value($user_detail,"cid","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Gender:  " . $gender );
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Status:  ". $status);
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Point:  ".get_array_value($user_detail,"point_remain","0"));

            $objPHPExcel->getActiveSheet()->setCellValue('A2', "Email:  ".get_array_value($user_detail,"email","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('B2', "Department:  ".get_array_value($user_detail,"department_name","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('C2', "User role:  ".get_array_value($user_detail,"user_role_name","0"));
            $objPHPExcel->getActiveSheet()->setCellValue('D2', "Last Login:  ".get_array_value($user_detail,"last_login","0"));

            $objPHPExcel->getActiveSheet()->setCellValue('A3', "Tel:  ".get_array_value($user_detail,"contact_number","0"));
           

            $downloaded = $result_list['downloaded'];
            $objPHPExcel->getActiveSheet()->setCellValue('A5', "Download");
            $objPHPExcel->getActiveSheet()->setCellValue('A6', "No");
            $objPHPExcel->getActiveSheet()->setCellValue('B6', "User");
            $objPHPExcel->getActiveSheet()->setCellValue('C6', "Resource Type");
            $objPHPExcel->getActiveSheet()->setCellValue('D6', "Title");
            $objPHPExcel->getActiveSheet()->setCellValue('E6', "Downloaded Date");
     
            $irow = 7;
            $no = 1; 
             foreach ($downloaded as $key => $value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($value,"email","0"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($value,"product_main_name","") );
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($value,"book_title",""). get_array_value($value,"copy_title","") );
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, date("d/m/Y H:i:s", strtotime(get_array_value($value,"updated_date","0"))));
                $no ++ ;
                $irow ++ ;
            }
           

            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
    }

	function getdatauser(){
		@define("thisAction",'show');
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/getdata';
		$this->load->view($this->default_theme_admin . '/' . folderName . '/getdata', $this->data);
	}
	function get_not_borrow_item() {
        $this->load->model($this->transaction_model, "transaction");
        $product_main_aid = ($this->input->get_post('type'))? $this->input->get_post('type'):7;
        $borrowing_date_from =$this->input->get_post('borrowing_date_start');
        $borrowing_date_to = $this->input->get_post('borrowing_date_end');
        $data_array = array();

        if(!is_blank($borrowing_date_from)){
            $this->transaction->set_where($this->transaction->get_table_name().'.borrowing_date >=', get_datetime_pattern("db_date_format",$borrowing_date_from,"")." 00:00:00");
        }
        if(!is_blank($borrowing_date_to)){
            $this->transaction->set_where($this->transaction->get_table_name().'.borrowing_date <=', get_datetime_pattern("db_date_format",$borrowing_date_to,"")." 23:59:59");
        }
        if ($product_main_aid == "7") { 
            $this->transaction->set_where('product_type_aid', 2);

        }else{
            $this->transaction->set_where('product_type_aid', 1);
        }
        $result_list = $this->transaction->load_records(false);
        foreach ($result_list as $key => $value) {
            if($ids){
                $ids = $ids .','. $value['copy_aid'];
            }else{
                $ids = $value;
            }
        }
        $ignore = explode(",", $ids);
         if ($product_main_aid == "7") { 

                $this->load->model($this->magazine_copy_model, "magazine_copy");
                $this->magazine_copy->set_where('magazine_copy.status', 1);
                $this->db->where_not_in('magazine_copy.aid', $ignore);
                $this->magazine_copy->set_where("product_main_aid",$product_main_aid);
                $result = $this->magazine_copy->load_records(true);
                #echo "<br>sql : ".$this->db->last_query()."<br>";
         }else{
                $this->load->model($this->book_copy_model, "book_copy");
                $this->book_copy->set_where('book_copy.status', 1);
                $this->db->where_not_in('book_copy.aid', $ignore);
                $this->book_copy->set_where("product_main_aid",$product_main_aid);
                $result = $this->book_copy->load_records(true);
                #echo "<br>sql : ".$this->db->last_query()."<br>";
        }

        $data_list  = array();
        foreach ($result as $key => $value) {
            $data_list[$key] = $value;
            $data_list[$key]['category_name']= $this->get_category_name(get_array_value($value,"parent_category","0"));
        }

        return $data_list;
    }
	function not_borrow_item(){
		@define("thisAction","p_report");
        @define('thisAdminSubMenu','not_borrow_item');
		$this->data["title"] = DEFAULT_TITLE;
        $this->data["result_list"] = $this->get_not_borrow_item();
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/not_borrow_item';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	function exportnot_borrow_item(){
		$result_list = $this->get_not_borrow_item();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

            $objPHPExcel->getActiveSheet()->setCellValue('A1', "No.");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Barcode");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Call No.");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Title of Copy");
            $objPHPExcel->getActiveSheet()->setCellValue('E1', "Category");
            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:E1");
            $irow = 2;
            $no = 1;
            foreach ($result_list as $key=>$value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($value,"barcode","-"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($value,"call_number","-"));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($value,"parent_title","").' '.get_array_value($value,"copy_title",""));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, get_array_value($value,"category_name","-"));
                $irow++;
                $no++;
            }
            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
	}	
	function get_new_item() {
        $this->load->model($this->transaction_model, "transaction");

        $product_main_aid = $this->input->get_post('type');
        $borrowing_date_from = $this->input->get_post('borrowing_date_start');
        $borrowing_date_to = $this->input->get_post('borrowing_date_end');
        $data_array = array();
        if($product_main_aid){
            if ($product_main_aid == "7" OR $product_main_aid == "3") { 
                    $this->load->model($this->magazine_copy_model, "magazine_copy");
                    $this->magazine_copy->set_where('magazine_copy.status', 1);
                    $this->db->where_not_in('magazine_copy.aid', $ignore);
                    $this->magazine_copy->set_where("product_main_aid",$product_main_aid);
                    if(!is_blank($borrowing_date_from)){
                        $this->magazine_copy->set_where($this->magazine_copy->get_table_name().'.created_date >=', get_datetime_pattern("db_date_format",$borrowing_date_from,"")." 00:00:00");
                    }
                    if(!is_blank($borrowing_date_to)){
                        $this->magazine_copy->set_where($this->magazine_copy->get_table_name().'.created_date <=', get_datetime_pattern("db_date_format",$borrowing_date_to,"")." 23:59:59");
                    }
                    $this->magazine_copy->set_order_by("created_date desc");
                    $result = $this->magazine_copy->load_records(true);
                    #echo "<br>sql : ".$this->db->last_query()."<br>";
             }else{
                    $this->load->model($this->book_copy_model, "book_copy");
                    $this->book_copy->set_where('book_copy.status', 1);
                    $this->db->where_not_in('book_copy.aid', $ignore);
                    $this->book_copy->set_where("product_main_aid",$product_main_aid);
                     if(!is_blank($borrowing_date_from)){
                        $this->book_copy->set_where($this->book_copy->get_table_name().'.created_date >=', get_datetime_pattern("db_date_format",$borrowing_date_from,"")." 00:00:00");
                    }
                    if(!is_blank($borrowing_date_to)){
                        $this->book_copy->set_where($this->book_copy->get_table_name().'.created_date <=', get_datetime_pattern("db_date_format",$borrowing_date_to,"")." 23:59:59");
                    }
                    $this->book_copy->set_order_by("created_date desc");
                    $result = $this->book_copy->load_records(true);
                    #echo "<br>sql : ".$this->db->last_query()."<br>";
            }

            $data_list  = array();
            foreach ($result as $key => $value) {
                $data_list[$key] = $value;
                $data_list[$key]['category_name']= $this->get_category_name(get_array_value($value,"parent_category","0"));
            }

            return $data_list;
        }else{
            return false;   
        }
    }
	function new_item(){
		@define("thisAction","p_report");
        @define('thisAdminSubMenu','new_item');
		$this->data["title"] = DEFAULT_TITLE;
        $this->data["result_list"] = $this->get_new_item();
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/new_item';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
    function exportnew_item(){
        $result_list = $this->get_new_item();
        if (is_var_array($result_list)) {
            $this->load->library('PHPExcel');
            $title_column_color = 'C9DCE6';
            $array_style_summary_title = array(
                'font' => array('bold' => true),
                'alignment' => array('
                    wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => $title_column_color)
                )
            );

            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $objPHPExcel = new PHPExcel();
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

            $objPHPExcel->getActiveSheet()->setCellValue('A1', "No.");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "Barcode");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Call No.");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Title of Copy");
            $objPHPExcel->getActiveSheet()->setCellValue('E1', "Category");
            $sharedStyle1 = new PHPExcel_Style();
            $sharedStyle1->applyFromArray($array_style_summary_title);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:E1");
            $irow = 2;
            $no = 1;
            foreach ($result_list as $key=>$value) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $irow, $no);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $irow, get_array_value($value,"barcode","-"));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $irow, get_array_value($value,"call_number","-"));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $irow, get_array_value($value,"parent_title","").' '.get_array_value($value,"copy_title",""));
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $irow, get_array_value($value,"category_name","-"));
                $irow++;
                $no++;
            }
            $filename = "download_export_" . date("ymdHis") . ".xls";
            // echo "$filename";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Cache-Control: max-age=0');
            header('Pragma: no-cache');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        } else {
            echo "No record found.";
            return"";
        }
    }   

	
	function ajax_category(){
		define("thisAdminSubMenu",'top_most_popular_item');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/ajax_category';
		$this->load->view($this->default_theme_admin . '/' . folderName . '/ajax_category', $this->data);
	}
	
	

}

?>