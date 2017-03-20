<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");
require_once('./include/tcpdf/tcpdf.php');
class Printbarcode_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		if(CONST_HAS_TRANSACTION != "1"){
			redirect('home');
		}

		define("thisAdminTabMenu",'print');
		define("thisAdminSubMenu",'print');
		@define("folderName",'print/print_back/print');
		
		define("TXT_TITLE",'Print Barcode management');
		define("TXT_INSERT_TITLE",'Print Barcode management : Add new Print Barcode');
		define("TXT_UPDATE_TITLE",'Print Barcode management : Edit Print Barcode');

		define("TXT_TITLE_PB",'Print Barcode in Advance management');
		define("TXT_INSERT_TITLE_PB",'Print Barcode in Advance management : Add new Print Barcode in Advance');
		define("TXT_UPDATE_TITLE_PB",'Print Barcode in Advance management : Edit Print Barcode in Advance');

		define("TXT_TITLE_CARD",'Card management');
		define("TXT_INSERT_TITLE_CARD",'Card management : Add new Card');
		define("TXT_UPDATE_TITLE_CARD",'Card management : Edit Card');
				
		//$this->main_model = 'Transaction_model';		

	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}

	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/print_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "";

		$this->session->set_userdata('transactionBackDataSearchSession','');

		$this->form();
	}

	function print_pdf() {
		define("thisAction",'print_print');
	 	
		$product_main_aid = $this->input->get_post("product_main_aid");
		$print_type = $this->input->get_post("ddl_print_type");
		$choose = $this->input->get_post("choose");
            
                 $data['grid'] ='';
		if($choose == 1){
			$range_from = $this->input->get_post("range_from");
			$range_to = $this->input->get_post("range_to");
		}else{
                    $data['range'] ='';
                    $data['grid'] = $this->input->get_post("range");	
                    foreach ($data['grid'] as $key => $value) {
                            if($value){
                                 $ranges[] = $value;
                            }
                       
                    }
                    

		}
                
                
                $item_start= $this->input->get_post("item_start");
                $item_start=(int)$item_start;
                if(!$item_start){
                    $item_start = 1;
                }
		

		// echo $product_main_aid."<br/>";
		// echo $print_type."<br/>";
		// echo $range_from."<br/>";
		// echo $range_to."<br/>";

		if($product_main_aid == '6'){
			$type = "2";
			$type_minor = "3";
			$product_type_minor_aid	= "2";

			
			//echo $type."<br/>";
			//echo $type_minor."<br/>";
			//echo $product_type_minor_aid."<br/>";

			$this->load->model($this->book_copy_model,"book_copy");
			if($choose == 1){
				$this->db->where('barcode >=', $range_from);
		 		$this->db->where('barcode <=', $range_to);
			}else{
                                $this->db->where_in('barcode',$ranges);
			}
		 	
		 	$this->book_copy->set_where(array("type" => $type , "type_minor" => $type_minor));
			
		 	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		 	//echo "33333333333";
		 	// set document information
		 	$pdf->SetCreator(PDF_CREATOR);
		 	// $pdf->SetAuthor('Nicola Asuni');
		 	switch ($print_type) {
		 		default:
		 		case '1':
		 			$this->book_copy->set_order_by("barcode", "ASC");
		 			$pdf->SetTitle('Barcodes');
		 			$pdf->SetSubject('Barcodes');
		 			$fn_prefix = "barcode_";
		 			break;
		 		case '2':
		 		case '3':
		 			$this->book_copy->set_order_by("barcode", "ASC");
		 			//$this->main->set_order_by("no_1", "ASC");
		 			//$this->main->set_order_by("no_2", "ASC");
					//$this->main->set_order_by("no_3", "ASC");
					//$this->main->set_order_by("no_4", "ASC");
		 			$pdf->SetFont('freeserif', '', 12);
		 			$pdf->SetTitle('หมวดหนังสือ');
		 			$pdf->SetSubject('หมวดหนังสือ');
		 			$fn_prefix = "category_";
		 			break;
		 	}
		 	$result_list = $this->book_copy->load_records(true);
                   
		 	//echo "<br>sql : ".$this->db->last_query()."<br>";
		    //echo "<pre>"; print_r($result_list); echo "</pre>"; exit;
	 	}else if($product_main_aid == '7'){
			$type = "2";
			$type_minor = "4";
			$product_type_minor_aid	= "2";

			//echo $type."<br/>";
			//echo $type_minor."<br/>";
			//echo $product_type_minor_aid."<br/>";

			$this->load->model($this->magazine_copy_model,"magazine_copy");
			if($choose == 1){
				$this->db->where('barcode >=', $range_from);
		 		$this->db->where('barcode <=', $range_to);
			}else{
				$this->db->where_in('barcode',$ranges);
		 		// $this->db->or_where('barcode =', $range4);
		 		// $this->db->or_where('barcode =', $range5);
		 		// $this->db->or_where('barcode =', $range6);
			}

		 	$this->magazine_copy->set_where(array("type" => $type , "type_minor" => $type_minor));
			
		 	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		 	// set document information
		 	$pdf->SetCreator(PDF_CREATOR);
		 	// $pdf->SetAuthor('Nicola Asuni');
		 	switch ($print_type) {
		 		default:
		 		case '1':
		 			$this->magazine_copy->set_order_by("barcode", "ASC");
		 			$pdf->SetTitle('Barcodes');
		 			$pdf->SetSubject('Barcodes');
		 			$fn_prefix = "barcode_";
		 			break;
		 		case '2':
		 		case '3':
		 			$this->magazine_copy->set_order_by("barcode", "ASC");
		 			//$this->main->set_order_by("no_1", "ASC");
		 			//$this->main->set_order_by("no_2", "ASC");
					//$this->main->set_order_by("no_3", "ASC");
					//$this->main->set_order_by("no_4", "ASC");
		 			$pdf->SetFont('freeserif', '', 12);
		 			$pdf->SetTitle('หมวดหนังสือ');
		 			$pdf->SetSubject('หมวดหนังสือ');
		 			$fn_prefix = "category_";
		 			break;
		 	}
		 	$result_list = $this->magazine_copy->load_records(true);
		 	//echo "<br>sql : ".$this->db->last_query()."<br>";
		    //echo "<pre>"; print_r($result_list); echo "</pre>"; exit;
		}
                if($choose == 1){
                    $total_record = count($result_list);
                }else{
                    $total_record = 27;
                    
                    $result_list_tmp = $result_list;
                    foreach($result_list_tmp as $key => $item){
                        $result_list_tmp2[$item['barcode']] = $item;
                    }
//                    echo "<pre>";
//                    print_r($result_list_tmp2);
                    $result_list = '';
                    for($i=0 ; $i <= 26 ; $i++ ){
                        if($data['grid'][$i]){
                          $itemkey= $data['grid'][$i];
                          $result_list[$i] = isset($result_list_tmp2[$itemkey])? $result_list_tmp2[$itemkey]: '';
                        }else{
                            $result_list[$i]['barcode'] = 99;
                        }
                       
                    }
//                    echo "<pre>";
//                    print_r($result_list);
//                    die();
                    
                }
                if($choose == 2){
                    $item_start = 1;
                }
	 	// echo $total_record."<br/>";
	 	// print_r($result_list);
	 	if (!isset($result_list[0])) {
                        $message = "ไม่พบหมายเลข Barcode";
                        echo "<script type='text/javascript'>alert('$message');</script>";
                         echo "<script>window.close();</script>";
	 		//redirect("admin/print/add");
	 		// echo "No record found. Please review your type and range again.";
	 		exit;
	 	}

	 	// remove default header/footer
	 	$pdf->setPrintHeader(false);
	 	$pdf->setPrintFooter(false);

	 	// set default monospaced font
	 	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	 	// set margins
	 	$pdf->SetMargins(0,0,0);
	 	// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	 	// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	 	// set auto page breaks
	 	$pdf->SetAutoPageBreak(TRUE, 0);

	 	// set image scale factor
	 	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	 	// set some language-dependent strings (optional)
	 	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	 	    require_once(dirname(__FILE__).'/lang/eng.php');
	 	    $pdf->setLanguageArray($l);
	 	}

	 	// set a barcode on the page footer
	 	$pdf->setBarcode(date('Y-m-d H:i:s'));

	 	// add a page
	 	// $pdf->AddPage();
		

		
		// define barcode style
		$style = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => 'C',
		    'border' => true,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    //'hmargin' => 50,
		    //'margin' => 10,
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 8,
		    'stretchtext' => 4
		);
		

		switch ($print_type) {
			default:
			case '1':
				$ITEM_WIDTH = 34;
				$ITEMS_PER_LINE = 3;
				$total_line = ceil(($total_record + ($item_start - 1)) / $ITEMS_PER_LINE);
				$items_per_line = $ITEMS_PER_LINE;
				$total_line_per_page = 9;
				break;
			case '2':
				$ITEM_WIDTH = 40;
				$ITEMS_PER_LINE = 3;
				$total_line = ceil(($total_record + ($item_start - 1)) / $ITEMS_PER_LINE);
				$items_per_line = $ITEMS_PER_LINE;
				$total_line_per_page = 9;
				$pdf->SetFont('freeserif', '', 12);
				break;
			case '3':
				$ITEM_WIDTH_1 = 40;
				$ITEM_WIDTH_2 = 40;
				$ITEMS_PER_LINE = 3;
				$total_line = ceil($total_record / $ITEMS_PER_LINE);
				$items_per_line = $ITEMS_PER_LINE;
				$total_line_per_page = 10;
				$pdf->SetFont('freeserif', '', 12);
				break;

		}
		
		$k=0;
                $padding = is_padding();
                $page_top = is_top();
                $padding2 = is_paddingType2();
                
		if ($print_type == "1" | $print_type == "2") {
			for($i=0; $i<$total_line; $i++) {			//row

				for($j=0; $j<$items_per_line; $j++) {   //column
					//$item_id = ($i * $items_per_line) + $j;
                                        $item_ori = (($i * $items_per_line) + $j);
                                        $item_id = (($i * $items_per_line) + $j) - ($item_start-1);
                                        if($item_id < 0) $item_id = 0;
					if (isset($result_list[$item_id])) {
						switch ($print_type) {
							default:
							case '1':
								if (!empty($result_list[$item_id]["barcode"])) {
									if($result_list[$item_id]["barcode"]==99) $result_list[$item_id]["barcode"]='';
									if ($i < $total_line_per_page) $ii = $i;
									else $ii = $i % $total_line_per_page;

									$y = ($ii * $padding) + $page_top;
									
//									if ($j == 0) $pos_start = ($j * $ITEM_WIDTH) + 15;
//									else $pos_start = ($j * $ITEM_WIDTH) + ((($j+1) * 20));
                                                                        if ($j == 0) $pos_start = ($j * $ITEM_WIDTH) + 15;
									else if($j == 1) $pos_start = ($j * $ITEM_WIDTH) + ((($j+1) * 20));
                                                                        else $pos_start = ($j * $ITEM_WIDTH) + ((($j+1) * 23));

									$txt_print = strtoupper($result_list[$item_id]["barcode"]);
									//$txt_print2 = '<div style="width:70px;margin:0px 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>';
									if ($i % $total_line_per_page == 0 && $pos_start == 15) {
										$pdf->AddPage();
									}
									// echo $pos_start."-";
									// echo $y."<br/>";
									// set font
									//$pdf->SetFont('freeserif', '', 12);
									//$pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start+3, $y, "สำนักงาน กสทช.", 0, 1, 0, true, '', true);
									//$pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start, $y, $txt_print2, 0, 1, 0, true, '', true);
									$pdf->Cell(0,0,'', 0, 1);
                                                                         if($item_id == 0 OR $item_start == $item_ori){
                                                                            if($item_start == $item_ori){
                                                                                $txt_print = strtoupper($result_list[1]["barcode"]);
                                                                                $pdf->write1DBarcode($txt_print, 'C128A', $pos_start+14, $y, $ITEM_WIDTH, 19, 2, $style, 'N');  
                                                                            }else if($item_ori < ($item_start-1)){
                                                                             //free space   
                                                                            }
                                                                            else{
                                                                                $txt_print = strtoupper($result_list[0]["barcode"]);
                                                                                $pdf->write1DBarcode($txt_print, 'C128A', $pos_start+14, $y, $ITEM_WIDTH, 19, 2, $style, 'N'); 
                                                                            }
                                                                        }else{ 
                                                                            $pdf->write1DBarcode($txt_print, 'C128A', $pos_start+14, $y, $ITEM_WIDTH, 19, 2, $style, 'N'); 
                                                                        }
									//$pdf->SetXY($pos_start,$y);
            						
            						//$pdf->SetFont('freeserif', '', 12);
									//$txt_print = "<div>".$result_list[$item_id]["no_1"]."</div>";
									//$txt_print = "<div>".$result_list[$item_id]["no_1"]."<br>".$result_list[$item_id]["no_2"]."<br>".$result_list[$item_id]["no_3"]."<br>".$result_list[$item_id]["no_4"]."</div>";
									//$pdf->writeHTMLCell(20, 0, $pos_start, $y+5, $txt_print2, 0, 1, 0, true, '', true);

									$k++;
								}
								break;
							case '2':
								if (!empty($result_list[$item_id]["no_1"]) || !empty($result_list[$item_id]["no_2"]) || !empty($result_list[$item_id]["no_3"])) {

									if ($i < $total_line_per_page) $ii = $i;
									else $ii = $i % $total_line_per_page;

									$y = ($ii * $padding) + is_checktop();
									
//									if ($j == 0) $pos_start = ($j * $ITEM_WIDTH) + 15;
//									else $pos_start = ($j * $ITEM_WIDTH) + ((($j+1) * 20));
                                                                        
                                                                        if ($j == 0) $pos_start = ($j * $ITEM_WIDTH) + 19;
									else if($j == 1) $pos_start = ($j * $ITEM_WIDTH) + ((($j+1) * 21));
                                                                        else $pos_start = ($j * $ITEM_WIDTH) + ((($j+1) * 20));
									// if ($j == 1) $pos_start = ($j * $ITEM_WIDTH) + 5;
									// else $pos_start = ($j * $ITEM_WIDTH) + 5;

									//$txt_print = "<div>".$result_list[$item_id]["no_1"]."</div>";
									$txt_print = "<div style='font-family:helvetica'>".$result_list[$item_id]["no_1"]."<br>".$result_list[$item_id]["no_2"]."<br>".$result_list[$item_id]["no_3"]."<br>".$result_list[$item_id]["no_4"]."</div>";
									if ($i % $total_line_per_page == 0 && $pos_start == 19) {
										$pdf->AddPage();
									}
									//$pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start+15, $y+5, $txt_print, 0, 1, 0, true, '', true);
                                                                         if($item_id == 0 OR $item_start == $item_ori){
                                                                            if($item_start == $item_ori){
                                                                                $txt_print = "<div style='font-family:helvetica'>".$result_list[1]["no_1"]."<br>".$result_list[1]["no_2"]."<br>".$result_list[1]["no_3"]."<br>".$result_list[1]["no_4"]."</div>";
                                                                                $pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start+14, $y+5, $txt_print, 0, 1, 0, true, '', true);  
                                                                            }else if($item_ori < ($item_start-1)){
                                                                             //free space   
                                                                            }
                                                                            else{
                                                                                $txt_print = "<div style='font-family:helvetica'>".$result_list[0]["no_1"]."<br>".$result_list[0]["no_2"]."<br>".$result_list[0]["no_3"]."<br>".$result_list[0]["no_4"]."</div>";
                                                                                $pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start+14, $y+5, $txt_print, 0, 1, 0, true, '', true);
                                                                            }
                                                                        }else{ 
                                                                            $pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start+14, $y+5, $txt_print, 0, 1, 0, true, '', true);
                                                                        }
									$k++;
								}
								break;
						}
					}
					if ($k == ($items_per_line-1)) {
						$pdf->Ln();
					}
				}
			}
		}
		if ($print_type == "3") {
			for($i=0; $i<$total_line; $i++) {
				for($j=0; $j<$items_per_line/2; $j++) {
					$item_id = ($i * $items_per_line/2) + $j;
					if (isset($result_list[$item_id])) {
						switch ($print_type) {
							default:
							case '3':
								// if (!empty($result_list[$item_id]["barcode"])) {
									
									if ($i < $total_line_per_page) $ii = $i;
									else $ii = $i % $total_line_per_page;

									$y = ($ii * 29) + 5;
									
									if ($j == 0) $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2) ) + 5;
									else $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2) ) + 4;

									// Print barcode
									$pdf->SetFont('freeserif', '', 12);
									$txt_print = strtoupper($result_list[$item_id]["barcode"]);
									if ($i % $total_line_per_page == 0 && $pos_start == 5) {
										$pdf->AddPage();
									}
									//$pdf->writeHTMLCell($ITEM_WIDTH_1, 0, $pos_start+3, $y, "สำนักงาน กสทช.", 0, 1, 0, true, '', true);
									//$pdf->writeHTMLCell($ITEM_WIDTH_1, 0, $pos_start+3, $y, "", 0, 1, 0, true, '', true);
									$pdf->write1DBarcode($txt_print, 'C128A', $pos_start+15, $y+15, $ITEM_WIDTH_1, 19, 0.4, $style, 'N');


									// Print side cover
									$pdf->SetFont('freeserif', '', 12);
									//$txt_print = "<div>".$result_list[$item_id]["no_1"]."</div>";
									$txt_print = "<div>".$result_list[$item_id]["no_1"]."<br>".$result_list[$item_id]["no_2"]."<br>".$result_list[$item_id]["no_3"]."<br>".$result_list[$item_id]["no_4"]."</div>";
									$pdf->writeHTMLCell($ITEM_WIDTH_2, 0, $pos_start+70, $y+10, $txt_print, 0, 1, 0, true, '', true);


									$k++;
								// }
								break;
						}
					}
					if ($k == ($items_per_line-1)) {
						$pdf->Ln();
					}
				}
			}
		}
		//Close and output PDF document
		// $pdf->Output($fn_prefix.mdate("%Y%m%d", strtotime()).'.pdf', 'I');
		$pdf->Output('barcode.pdf', 'I');
	 }
	
	function ptint_form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/print_form_after';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function print_barcode_next(){
		@define("thisAction",'print_barcode_next');
		// $this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE_PB;
		$this->data["js_code"] = "";

		$this->session->set_userdata('transactionBackDataSearchSession','');

		$this->ptint_form();
	}

	function print_pdf_next() {
		define("thisAction",'print_pdf_next');
	 	
		$product_main_aid = $this->input->get_post("product_main_aid");
		$print_type = $this->input->get_post("ddl_print_type");
		// $choose = $this->input->get_post("choose");
		$range_from = $this->input->get_post("range_from");
		$range_to = $this->input->get_post("range_to");
		
		// echo $product_main_aid."<br/>";
		// echo $print_type."<br/>";
		// echo $range_from."<br/>";
		// echo $range_to."<br/>";
		 	
		 	// $this->book_copy->set_where(array("type" => $type , "type_minor" => $type_minor));
			
		 	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		 	//echo "33333333333";
		 	// set document information
		 	$pdf->SetCreator(PDF_CREATOR);
		 	
		 			
		 	$pdf->SetTitle('Barcodes');
		 	$pdf->SetSubject('Barcodes');
		 	$fn_prefix = "barcode_";
		 		
	 	if (($product_main_aid == "") || ($range_from == "") || ($range_to == "")){
	 		redirect("admin/print/print-next");
	 		// echo "No record found. Please review your type and range again.";
	 		exit;
	 	}

	 	// remove default header/footer
	 	$pdf->setPrintHeader(false);
	 	$pdf->setPrintFooter(false);

	 	// set default monospaced font
	 	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	 	// set margins
	 	$pdf->SetMargins(0,0,0);
	 	// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	 	// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	 	// set auto page breaks
	 	$pdf->SetAutoPageBreak(TRUE, 0);

	 	// set image scale factor
	 	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	 	// set some language-dependent strings (optional)
	 	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	 	    require_once(dirname(__FILE__).'/lang/eng.php');
	 	    $pdf->setLanguageArray($l);
	 	}

	 	// set a barcode on the page footer
	 	$pdf->setBarcode(date('Y-m-d H:i:s'));

	 	// add a page
	 	// $pdf->AddPage();
	 	$num_bracode = ((int)$range_to - (int)$range_from);
	 	$bracode = (int)$range_from;
	 	// $data_bracode[] = array();
	 	for ($i=0; $i <= $num_bracode ; $i++) { 
	 		$data_bracode[] = $product_main_aid."".str_pad($bracode, 6, "0", STR_PAD_LEFT);
	 		$bracode = $bracode+1;
	 		// echo $data_bracode[$i]."<br/>";
	 	}
		$total_record = count($data_bracode);
		// echo $total_record."<br/>";
		// echo $num_bracode."<br/>";
		// print_r($data_bracode);
		// $input = "Alien";
		// echo str_pad($input, 6, "0", STR_PAD_LEFT);   // produces "__Alien___"                      // produces "Alien     "
		
		// define barcode style
		$style = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => 'C',
		    'border' => true,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    //'hmargin' => 50,
		    //'margin' => 10,
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 8,
		    'stretchtext' => 4
		);
		

		
				$ITEM_WIDTH = 34;
				$ITEMS_PER_LINE = 3;
				$total_line = ceil($total_record / $ITEMS_PER_LINE);
				$items_per_line = $ITEMS_PER_LINE;
				$total_line_per_page = 9;
		
		
		$k=0;
		$y = "";
		$count_number = 0;
		if ($print_type == "1") {
			for($i=0; $i<$total_line; $i++) {			//row

				for($j=0; $j<$items_per_line; $j++) {   //column
					$item_id = ($i * $items_per_line) + $j;
					
								if($count_number < $total_record){
									
									if ($i < $total_line_per_page) $ii = $i;
									else $ii = $i % $total_line_per_page;

									$y = ($ii * 29) + 18;
									
									if ($j == 0) $pos_start = ($j * $ITEM_WIDTH) + 15;
									else $pos_start = ($j * $ITEM_WIDTH) + ((($j+1) * 20));

									$txt_print = strtoupper($data_bracode[$count_number]);
									//$txt_print2 = '<div style="width:70px;margin:0px 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>';
									if ($i % $total_line_per_page == 0 && $pos_start == 15) {
										$pdf->AddPage();
									}
									// echo $pos_start."-";
									// echo $y."<br/>";
									// set font
									//$pdf->SetFont('freeserif', '', 12);
									//$pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start+3, $y, "สำนักงาน กสทช.", 0, 1, 0, true, '', true);
									//$pdf->writeHTMLCell($ITEM_WIDTH, 0, $pos_start, $y, $txt_print2, 0, 1, 0, true, '', true);
									$pdf->Cell(0,0,'', 0, 1);
									$pdf->write1DBarcode($txt_print, 'C128A', $pos_start+16, $y, $ITEM_WIDTH, 19, 2, $style, 'N');
									//$pdf->SetXY($pos_start,$y);
            						
            						//$pdf->SetFont('freeserif', '', 12);
									//$txt_print = "<div>".$result_list[$item_id]["no_1"]."</div>";
									//$txt_print = "<div>".$result_list[$item_id]["no_1"]."<br>".$result_list[$item_id]["no_2"]."<br>".$result_list[$item_id]["no_3"]."<br>".$result_list[$item_id]["no_4"]."</div>";
									//$pdf->writeHTMLCell(20, 0, $pos_start, $y+5, $txt_print2, 0, 1, 0, true, '', true);

									$k++;
								
							
					if ($k == ($items_per_line-1)) {
						$pdf->Ln();
					}
					$count_number++;
					}
				}
			}
		}
		//Close and output PDF document
		// $pdf->Output($fn_prefix.mdate("%Y%m%d", strtotime()).'.pdf', 'I');
		$pdf->Output('barcode_next.pdf', 'I');
	 }

	 function print_card_form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/print_form_card';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function print_card(){
		@define("thisAction",'ptint_card');
		// $this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE_CARD;
		$this->data["js_code"] = "";

		$this->session->set_userdata('transactionBackDataSearchSession','');

		$this->print_card_form();
	}

// 	function print_return_card() {
// 		$product_main_aid = $this->input->get_post("product_main_aid");
// 		//$print_type = $this->input->get_post("ddl_print_type");
// 		$choose = $this->input->get_post("choose");
// 		if($choose == 1){
// 			$range_from = $this->input->get_post("range_from");
// 			$range_to = $this->input->get_post("range_to");
// 		}else{
// 			$range1 = $this->input->get_post("range1");
// 			$range2 = $this->input->get_post("range2");
// 			$range3 = $this->input->get_post("range3");
// 			// $range4 = $this->input->get_post("range4");
// 			// $range5 = $this->input->get_post("range5");
// 			// $range6 = $this->input->get_post("range6");
// 		}
		

// 		// echo $product_main_aid."<br/>";
// 		// echo $print_type."<br/>";
// 		// echo $range_from."<br/>";
// 		// echo $range_to."<br/>";

// 		if($product_main_aid == '6'){
// 			$type = "2";
// 			$type_minor = "3";
// 			$product_type_minor_aid	= "2";

			
// 			//echo $type."<br/>";
// 			//echo $type_minor."<br/>";
// 			//echo $product_type_minor_aid."<br/>";

// 			$this->load->model($this->book_copy_model,"book_copy");
// 			if($choose == 1){
// 				$this->db->where('barcode >=', $range_from);
// 		 		$this->db->where('barcode <=', $range_to);
// 			}else{

// 				$this->db->or_where('barcode =', $range1);
// 		 		$this->db->or_where('barcode =', $range2);
// 		 		$this->db->or_where('barcode =', $range3);
// 		 		// $this->db->or_where('barcode =', $range4);
// 		 		// $this->db->or_where('barcode =', $range5);
// 		 		// $this->db->or_where('barcode =', $range6);
// 			}
		 	
// 		 	$this->book_copy->set_where(array("type" => $type , "type_minor" => $type_minor));
			
// 		 	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// 		 	//echo "33333333333";
// 		 	// set document information
// 		 	$pdf->SetCreator(PDF_CREATOR);
// 		 	// $pdf->SetAuthor('Nicola Asuni');
		 	
// 		 	$this->book_copy->set_order_by("barcode", "ASC");
// 		 	$pdf->SetTitle('Barcodes');
// 		 	$pdf->SetSubject('Barcodes');
// 		 	$fn_prefix = "barcode_";
		 		
// 		 	$result_list = $this->book_copy->load_records(true);
// 		 	//echo "<br>sql : ".$this->db->last_query()."<br>";
// 		    //echo "<pre>"; print_r($result_list); echo "</pre>"; exit;
// 	 	}else if($product_main_aid == '7'){
// 			$type = "2";
// 			$type_minor = "4";
// 			$product_type_minor_aid	= "2";

// 			//echo $type."<br/>";
// 			//echo $type_minor."<br/>";
// 			//echo $product_type_minor_aid."<br/>";

// 			$this->load->model($this->magazine_copy_model,"magazine_copy");
// 			if($choose == 1){
// 				$this->db->where('barcode >=', $range_from);
// 		 		$this->db->where('barcode <=', $range_to);
// 			}else{
// 				$this->db->or_where('barcode =', $range1);
// 		 		$this->db->or_where('barcode =', $range2);
// 		 		$this->db->or_where('barcode =', $range3);
// 		 		// $this->db->or_where('barcode =', $range4);
// 		 		// $this->db->or_where('barcode =', $range5);
// 		 		// $this->db->or_where('barcode =', $range6);
// 			}

// 		 	$this->magazine_copy->set_where(array("type" => $type , "type_minor" => $type_minor));
			
// 		 	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// 		 	// set document information
// 		 	$pdf->SetCreator(PDF_CREATOR);
// 		 	// $pdf->SetAuthor('Nicola Asuni');
		 	
// 		 	$this->magazine_copy->set_order_by("barcode", "ASC");
// 		 	$pdf->SetTitle('Barcodes');
// 		 	$pdf->SetSubject('Barcodes');
// 		 	$fn_prefix = "barcode_";
		 	
// 		 	$result_list = $this->magazine_copy->load_records(true);
// 		 	//echo "<br>sql : ".$this->db->last_query()."<br>";
// 		    //echo "<pre>"; print_r($result_list); echo "</pre>"; exit;
// 		}
// 	 	$total_record = count($result_list);
// 	 	// echo $total_record."<br/>";
// 	 	// print_r($result_list);
// 	 	if (!isset($result_list[0])) {
// 	 		redirect("admin/print/print-card");
// 	 		// echo "No record found. Please review your type and range again.";
// 	 		exit;
// 	 	}

// 	 	// remove default header/footer
// 	 	$pdf->setPrintHeader(false);
// 	 	$pdf->setPrintFooter(false);

// 	 	// set default monospaced font
// 	 	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// 	 	// set margins
// 	 	$pdf->SetMargins(0,0,0);
// 	 	// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// 	 	// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// 	 	// set auto page breaks
// 	 	$pdf->SetAutoPageBreak(TRUE, 0);

// 	 	// set image scale factor
// 	 	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// 	 	// set some language-dependent strings (optional)
// 	 	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
// 	 	    require_once(dirname(__FILE__).'/lang/eng.php');
// 	 	    $pdf->setLanguageArray($l);
// 	 	}

// 	 	// set a barcode on the page footer
// 	 	$pdf->setBarcode(date('Y-m-d H:i:s'));

// 	 	// add a page
// 	 	// $pdf->AddPage();
		

		
// 		// define barcode style
// 		$style = array(
// 		    'position' => '',
// 		    'align' => 'C',
// 		    'stretch' => false,
// 		    'fitwidth' => true,
// 		    'cellfitalign' => 'C',
// 		    'border' => true,
// 		    'hpadding' => 'auto',
// 		    'vpadding' => 'auto',
// 		    'fgcolor' => array(0,0,0),
// 		    'bgcolor' => array(255,255,255),
// 		    //'hmargin' => 50,
// 		    //'margin' => 10,
// 		    'text' => true,
// 		    'font' => 'cordiaupcb',
// 		    'fontsize' => 11,
// 		    'stretchtext' => 4
// 		);
		

		
// 				// $ITEM_WIDTH = 34;
// 				// $ITEMS_PER_LINE = 3;
// 				// $total_line = ceil($total_record / $ITEMS_PER_LINE);
// 				// $items_per_line = $ITEMS_PER_LINE;
// 				// $total_line_per_page = 9;

// 				$ITEM_WIDTH_1 = 42;
// 				$ITEM_WIDTH_2 = 17;
// 				$ITEMS_PER_LINE = 3;
// 				$total_line = ceil($total_record / $ITEMS_PER_LINE);
// 				$items_per_line = $ITEMS_PER_LINE;
// 				$total_line_per_page = 9;
// 				$pdf->SetFont('cordiaupcb', '', 11);
		
			
		
// 		$k=0;
// 		for($i=0; $i<$total_line; $i++) {
// 				for($j=0; $j<$items_per_line; $j++) {
// 					$item_id = ($i * $items_per_line) + $j;
// 					if (isset($result_list[$item_id])) {
					
// 								// if (!empty($result_list[$item_id]["barcode"])) {
									
// 									if ($i < $total_line_per_page) $ii = $i;
// 									else $ii = $i % $total_line_per_page;

// 									$y = ($ii * 29) + 5;
									
// 									// if ($j == 0) $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2) ) + 5;
// 									// else $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2) ) + 4;
// 									if ($j == 0) $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2)) +  5;
// 									else $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2)) + ((($j+1) * 4));

// 									// Print barcode
// 									$pdf->SetFont('cordiaupcb', '', 11);
// 									$txt_title = $result_list[$item_id]["parent_title"];
									
// 									//echo $txt."<br/>";
// 									$txt_strlen = strlen($txt_title);
									
// 									//	$txt_print_title = "<div>".$txt_title."</div>";
// 									$text[1] =  substr($txt_title,0,1);
// 									$text[$txt_strlen] =  substr($txt_title,($txt_strlen-1),$txt_strlen);
	                				
// 	                                $txt="";
// 	                                $language = "";
// 	                				$txt_title_cut =iconv_substr($txt_title,0,40,"UTF-8");
// 									//$txt =  ceil((strlen($txt_title)/80));
// 									if(chack_data($text[1]) && chack_data($text[$txt_strlen])){
// 										$language = "EN";
// 										$txt =  ceil((strlen($txt_title)/33));
// 										//echo $txt_strlen.", EN";
// 									}elseif(chack_data($text[1]) || chack_data($text[$txt_strlen])){
// 										$txt =  ceil((strlen($txt_title)/68));
// 										$language = "ENTH";
// 										//echo $txt_strlen.", EN TH ,";
// 									}else{
// 										$txt =  ceil((strlen($txt_title)/84));
// 										$language = "TH";
// 										//echo $txt_strlen.", TH ,";
// 									}
// 									//echo $txt."<br/>";
// 									$txt_print_title = "<div >".$txt_title_cut."</div>";
									
// 									$txt_print_author = "<div>".$result_list[$item_id]["parent_author"]."<div>";
// 									if ($i % $total_line_per_page == 0 && $pos_start == 5) {
// 										$pdf->AddPage();
// 									}
									
// 										$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+10, "Title: ", 0, 1, 0, true, '', true);
// 										$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+10, "$txt_print_title", 0, 1, 0, true, '', true);
									
									
// 									if($language == "EN"){
// 										if($txt == 1){
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
									

// 										}elseif ($txt == 2) {
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
										
// 											# code...
// 										}elseif ($txt == 3) {
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
										
// 											# code...
// 										}//else{

// 										//}
// 									}elseif($language == "TH"){
// 										if($txt == 1){
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
									

// 										}elseif ($txt == 2) {
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
										
// 											# code...
// 										}elseif ($txt == 3) {
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
										
// 											# code...
// 										}//else{

// 										//}
// 									}else{
// 										if($txt == 1){
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
									

// 										}elseif ($txt == 2) {
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
										
// 											# code...
// 										}elseif ($txt == 3) {
// 											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
// 										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
										
// 											# code...
// 										 }//else{

// 										// }
// 									}
// 									// if(chack_data($text[1])){
// 	        //         					//echo substr($data,0,$count_array). "<br>";
// 	        //         					if($txt < 50){
// 									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
// 									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
								
// 									// 	}elseif ($txt > 50 && $txt < 100){
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
									
// 									// 	}else{
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
									

// 									// 	}
	                					
// 									// }else{
// 									// 	if($txt < 90){
// 									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
// 									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
								
// 									// 	}elseif ($txt > 89 && $txt < 150){
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
									
// 									// 	}else{
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
// 									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
									

// 									// 	}
// 									// }

									
									

// 									$k++;
						
// 					}
// 					if ($k == ($items_per_line-1)) {
// 						$pdf->Ln();
// 					}
// 				}
// 			}

		
// 		//Close and output PDF document
// 		// $pdf->Output($fn_prefix.mdate("%Y%m%d", strtotime()).'.pdf', 'I');
// 		$pdf->Output('card.pdf', 'I');
// 	 }
	
	

// }

	function print_return_card() {
		$product_main_aid = $this->input->get_post("product_main_aid");
		//$print_type = $this->input->get_post("ddl_print_type");
		$choose = $this->input->get_post("choose");
		if($choose == 1){
			$range_from = $this->input->get_post("range_from");
			$range_to = $this->input->get_post("range_to");
		}else{
//			$range1 = $this->input->get_post("range1");
//			$range2 = $this->input->get_post("range2");
//			$range3 = $this->input->get_post("range3");
			// $range4 = $this->input->get_post("range4");
			// $range5 = $this->input->get_post("range5");
			// $range6 = $this->input->get_post("range6");
                    $data['range'] ='';
                    $data['grid'] = $this->input->get_post("range");	
                    foreach ($data['grid'] as $key => $value) {
                            if($value){
                                 $ranges[] = $value;
                            }
                       
                    }
		}
		$item_start= $this->input->get_post("item_start");
                $item_start=(int)$item_start;
                if(!$item_start){
                    $item_start = 1;
                }

		// echo $product_main_aid."<br/>";
		// echo $print_type."<br/>";
		// echo $range_from."<br/>";
		// echo $range_to."<br/>";

		if($product_main_aid == '6'){
			$type = "2";
			$type_minor = "3";
			$product_type_minor_aid	= "2";

			
			//echo $type."<br/>";
			//echo $type_minor."<br/>";
			//echo $product_type_minor_aid."<br/>";

			$this->load->model($this->book_copy_model,"book_copy");
			if($choose == 1){
				$this->db->where('barcode >=', $range_from);
		 		$this->db->where('barcode <=', $range_to);
			}else{
                                $this->db->where_in('barcode',$ranges);
//				$this->db->or_where('barcode =', $range1);
//		 		$this->db->or_where('barcode =', $range2);
//		 		$this->db->or_where('barcode =', $range3);
		 		// $this->db->or_where('barcode =', $range4);
		 		// $this->db->or_where('barcode =', $range5);
		 		// $this->db->or_where('barcode =', $range6);
			}
		 	
		 	$this->book_copy->set_where(array("type" => $type , "type_minor" => $type_minor));
			
		 	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		 	//echo "33333333333";
		 	// set document information
		 	$pdf->SetCreator(PDF_CREATOR);
		 	// $pdf->SetAuthor('Nicola Asuni');
		 	
		 	$this->book_copy->set_order_by("barcode", "ASC");
		 	$pdf->SetTitle('Barcodes');
		 	$pdf->SetSubject('Barcodes');
		 	$fn_prefix = "barcode_";
		 		
		 	$result_list = $this->book_copy->load_records(true);
		 	//echo "<br>sql : ".$this->db->last_query()."<br>";
		    //echo "<pre>"; print_r($result_list); echo "</pre>"; exit;
	 	}else if($product_main_aid == '7'){
			$type = "2";
			$type_minor = "4";
			$product_type_minor_aid	= "2";

			//echo $type."<br/>";
			//echo $type_minor."<br/>";
			//echo $product_type_minor_aid."<br/>";

			$this->load->model($this->magazine_copy_model,"magazine_copy");
			if($choose == 1){
				$this->db->where('barcode >=', $range_from);
		 		$this->db->where('barcode <=', $range_to);
			}else{
                            $this->db->where_in('barcode',$ranges);
//				$this->db->or_where('barcode =', $range1);
//		 		$this->db->or_where('barcode =', $range2);
//		 		$this->db->or_where('barcode =', $range3);
		 		// $this->db->or_where('barcode =', $range4);
		 		// $this->db->or_where('barcode =', $range5);
		 		// $this->db->or_where('barcode =', $range6);
			}

		 	$this->magazine_copy->set_where(array("type" => $type , "type_minor" => $type_minor));
			
		 	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		 	// set document information
		 	$pdf->SetCreator(PDF_CREATOR);
		 	// $pdf->SetAuthor('Nicola Asuni');
		 	
		 	$this->magazine_copy->set_order_by("barcode", "ASC");
		 	$pdf->SetTitle('Barcodes');
		 	$pdf->SetSubject('Barcodes');
		 	$fn_prefix = "barcode_";
		 	
		 	$result_list = $this->magazine_copy->load_records(true);
		 	//echo "<br>sql : ".$this->db->last_query()."<br>";
		    //echo "<pre>"; print_r($result_list); echo "</pre>"; exit;
		}
//	 	$total_record = count($result_list);
                if($choose == 1){
                    $total_record = count($result_list);
                }else{
                    $total_record = 27;
                    
                    $result_list_tmp = $result_list;
                    foreach($result_list_tmp as $key => $item){
                        $result_list_tmp2[$item['barcode']] = $item;
                    }
//                    echo "<pre>";
//                    print_r($result_list_tmp2);
                    $result_list = '';
                    for($i=0 ; $i <= 26 ; $i++ ){
                        if($data['grid'][$i]){
                          $itemkey= $data['grid'][$i];
                          $result_list[$i] = isset($result_list_tmp2[$itemkey])? $result_list_tmp2[$itemkey]: '';
                        }else{
                            $result_list[$i]['barcode'] = 99;
                        }
                       
                    }
//                    echo "<pre>";
//                    print_r($result_list);
//                    die();
                    
                }
                if($choose == 2){
                    $item_start = 1;
                }
	 	// echo $total_record."<br/>";
	 	// print_r($result_list);
	 	if (!isset($result_list[0])) {
                        $message = "ไม่พบหมายเลข Barcode";
                        echo "<script type='text/javascript'>alert('$message');</script>";
                         echo "<script>window.close();</script>";
	 		//redirect("admin/print/add");
	 		// echo "No record found. Please review your type and range again.";
	 		exit;
	 	}
	 	// remove default header/footer
	 	$pdf->setPrintHeader(false);
	 	$pdf->setPrintFooter(false);

	 	// set default monospaced font
	 	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	 	// set margins
	 	$pdf->SetMargins(0,0,0);
	 	// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	 	// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	 	// set auto page breaks
	 	$pdf->SetAutoPageBreak(TRUE, 0);

	 	// set image scale factor
	 	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	 	// set some language-dependent strings (optional)
	 	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	 	    require_once(dirname(__FILE__).'/lang/eng.php');
	 	    $pdf->setLanguageArray($l);
	 	}

	 	// set a barcode on the page footer
	 	$pdf->setBarcode(date('Y-m-d H:i:s'));

	 	// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
	 	$pdf->SetFont('freeserif', '', 9 ,'',true);

	 	// add a page
	 	$pdf->AddPage();
		

		
		// define barcode style
		$style = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => 'C',
		    'border' => true,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    //'hmargin' => 50,
		    //'margin' => 10,
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 8,
		    'stretchtext' => 4
		);

		
				// $ITEM_WIDTH = 34;
				// $ITEMS_PER_LINE = 3;
				// $total_line = ceil($total_record / $ITEMS_PER_LINE);
				// $items_per_line = $ITEMS_PER_LINE;
				// $total_line_per_page = 9;

				$ITEM_WIDTH_1 = 42;
				$ITEM_WIDTH_2 = 17;
				$ITEMS_PER_LINE = 3;
				$total_line = ceil(($total_record + ($item_start - 1)) / $ITEMS_PER_LINE);
				$items_per_line = $ITEMS_PER_LINE;
				$total_line_per_page = 9;
				$pdf->SetFont('freeserif', '', 9);
		
			
		
		$k=0;
		for($i=0; $i<$total_line; $i++) {
				for($j=0; $j<$items_per_line; $j++) {
					//$item_id = ($i * $items_per_line) + $j - $item_start;
                                        $item_ori = (($i * $items_per_line) + $j);
                                        $item_id = (($i * $items_per_line) + $j) - ($item_start-1);
                                        if($item_id < 0) $item_id = 0;
					if (isset($result_list[$item_id])) {
					
								// if (!empty($result_list[$item_id]["barcode"])) {
									if($result_list[$item_id]["barcode"]==99) $result_list[$item_id]["barcode"]='';
									if ($i < $total_line_per_page) $ii = $i;
									else $ii = $i % $total_line_per_page;

									$y = ($ii * is_padding()) + 14;
//									if ($j == 0) $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2)) + 10;
//									else $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2)) + ((($j+1) * 4));
                                                                        if ($j == 0) $pos_start = 10;
									else if($j == 1) $pos_start =  ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2)) + ((($j+1) * 5));
                                                                        else $pos_start = ($j * ($ITEM_WIDTH_1 + $ITEM_WIDTH_2)) + ((($j+1) * 4));

									// Print barcode
									$pdf->SetFont('freeserif', '', 9);
                                                                        if(isset($result_list[$item_id]["parent_title"])){
                                                                            $txt_title = $result_list[$item_id]["parent_title"];
                                                                        } else{
                                                                            $txt_title = '';
                                                                        }
									
									$txt_strlen = strlen($txt_title);
									
									//	$txt_print_title = "<div>".$txt_title."</div>";
									$text[1] =  substr($txt_title,0,1);
									$text[$txt_strlen] =  substr($txt_title,($txt_strlen-1),$txt_strlen);
	                				
                                                                        $txt="";
                                                                        $language = "";
                                                                        $txt_title_cut =iconv_substr($txt_title,0,25,"UTF-8");
									
                                                                        if($txt_strlen > 25) $text_cut = "...";
                                                                        else $text_cut = "";
									$txt_print_title = "<div >".$txt_title_cut.$text_cut."</div>";
									if(isset($result_list[$item_id]["parent_author"])){
                                                                            $txt_print_author = "<div>".$result_list[$item_id]["parent_author"]."<div>";
                                                                        }else{
                                                                            $txt_print_author = "";
                                                                        }
									
									if ($i % $total_line_per_page == 0 && $pos_start == 10 && ($item_id+$item_start) > 26) {
										$pdf->AddPage();
									}
                                                                        $barCode = $result_list[$item_id]["barcode"];
                                                                        if($result_list[$item_id]["barcode"] != ''){
									
                                                                        if($item_id == 0 OR $item_start == $item_ori){
                                                                            if($item_start == $item_ori){
                                                                                $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+5, $barCode , 0, 1, 0, true, '', true);

                                                                                $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+10, "Title: ", 0, 1, 0, true, '', true);
										$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+21, $y+10, "$txt_print_title", 0, 1, 0, true, '', true);
                                                                                $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
										$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+24, $y+15, "$txt_print_author", 0, 1, 0, true, '', true); 
                                                                                }else if($item_ori < ($item_start-1)){
                                                                                 //free space   
                                                                                }else{
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+5, $barCode , 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+10, "Title: ", 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+21, $y+10, "$txt_print_title", 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+24, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
                                                                                }
                                                                            }else{ 
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+5, $barCode , 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+10, "Title: ", 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+21, $y+10, "$txt_print_title", 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
                                                                                    $pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+24, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
                                                                            }
                                                                        }

										
									
									
//									if($language == "EN"){
//										if($txt == 1){
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
//									
//
//										}elseif ($txt == 2) {
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
//										
//											# code...
//										}elseif ($txt == 3) {
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
//										
//											# code...
//										}//else{
//
//										//}
//									}elseif($language == "TH"){
//										if($txt == 1){
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
//									
//
//										}elseif ($txt == 2) {
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
//										
//											# code...
//										}elseif ($txt == 3) {
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
//										
//											# code...
//										}//else{
//
//										//}
//									}else{
//										if($txt == 1){
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
//									
//
//										}elseif ($txt == 2) {
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
//										
//											# code...
//										}elseif ($txt == 3) {
//											$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
//										 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
//										
//											# code...
//										 }//else{
//
//										// }
//									}
									// if(chack_data($text[1])){
	        //         					//echo substr($data,0,$count_array). "<br>";
	        //         					if($txt < 50){
									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
								
									// 	}elseif ($txt > 50 && $txt < 100){
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
									
									// 	}else{
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
									

									// 	}
	                					
									// }else{
									// 	if($txt < 90){
									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+15, "Author: ", 0, 1, 0, true, '', true);
									// 	$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+15, "$txt_print_author", 0, 1, 0, true, '', true);
								
									// 	}elseif ($txt > 89 && $txt < 150){
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+19, "Author: ", 0, 1, 0, true, '', true);
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+19, "$txt_print_author", 0, 1, 0, true, '', true);
									
									// 	}else{
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+13, $y+24, "Author: ", 0, 1, 0, true, '', true);
									// 		$pdf->writeHTMLCell($ITEM_WIDTH_1, 0 , $pos_start+23, $y+24, "$txt_print_author", 0, 1, 0, true, '', true);
									

									// 	}
									// }

									
									

									$k++;
						
					}
					if ($k == ($items_per_line-1)) {
						$pdf->Ln();
					}
				}
			}

		
		//Close and output PDF document
		// $pdf->Output($fn_prefix.mdate("%Y%m%d", strtotime()).'.pdf', 'I');
		$pdf->Output('card.pdf', 'I');
	 }
	
	

}

?>