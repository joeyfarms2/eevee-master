<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Check_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = "web_service";
		
		define("thisFrontTabMenu",'');
		define("thisFrontSubMenu",'');
		@define("folderName",'web_service/check');
		
		$this->shelf_model = "Shelf_model";
		$this->view_all_products_model = "View_all_products_model";
		$this->view_all_download_history_model = "View_all_download_history_model";
		$this->view_all_reserve_model = "View_all_reserve_model";
		$this->transaction_model = "Transaction_model";

		$this->reserve_model = "Reserve_model";

		$this->lang->load('mail');
	}
	
	function index()
	{
		return "";
	}

	function cron_hourly()
	{
		header('Content-Type: text/html; charset=utf-8');
		echo "Start.....<BR>";
		
		$this->load->model($this->view_all_products_model,"vap_for_view");
		$this->vap_for_view->update_data();

		$this->load->model($this->view_all_reserve_model,"var_for_view");
		$this->var_for_view->update_data();

		$this->load->model($this->view_all_download_history_model,"vad_for_view");
		$this->vad_for_view->update_data();

		echo "Step 1 : Delete expired book from shelf......";
		$this->load->model($this->shelf_model,"shelf");
		$this->shelf->delete_expired_shelf();
		echo "Done<BR>";

		echo "Step 2 : Remove expired reserve......";
		$this->load->model($this->reserve_model,"reserve");
		$this->reserve->delete_expired_code();
		echo "Done<BR>";

		echo "Step 3 : Get reserve list......<BR>";
		$this->load->model($this->reserve_model,"reserve");
		$this->reserve->set_where(array("status"=>'1'));
		$this->reserve->set_order_by("copy_aid asc, created_date asc");
		$reserve_result = $this->reserve->load_records(false);
		if(!is_var_array($reserve_result)){
			echo "No reserve list......Do nothing.";
		}else{
			$reserve_arr = array();
			foreach ($reserve_result as $item) {
				$user_aid = get_array_value($item,"user_aid","0");
				$copy_aid = get_array_value($item,"copy_aid","0");
				$product_type_aid = get_array_value($item,"product_type_aid","0");
				// echo "user_aid = $user_aid, copy_aid = $copy_aid, product_type_aid = $product_type_aid<BR>";
				if($user_aid > 0 && $copy_aid > 0 && $product_type_aid > 0){
					$data = array();
					$data["user_aid"] = $user_aid;
					$data["confirm_code"] = get_array_value($item,"confirm_code","");
					$reserve_arr[$product_type_aid.":".$copy_aid][] = $data;
				}
			}
			// print_r($reserve_arr);
			if(is_var_array($reserve_arr)){

				foreach ($reserve_arr as $key => $user_list) {
					echo "<HR>";

					list($product_type_aid, $copy_aid) = explode(':', $key);
					
					$model = $this->get_product_model($product_type_aid);
					$model_copy_name = get_array_value($model,"product_copy_model","");
					$this->load->model($model_copy_name, $model_copy_name);

					$tmp = array();
					$tmp['aid'] = $copy_aid;
					$this->{$model_copy_name}->set_where($tmp);
					$copy_result = $this->{$model_copy_name}->load_record(true);

					$copy_status = get_array_value($copy_result,"status","0");
					$parent_status = get_array_value($copy_result,"parent_status","0");
					$parent_title = get_array_value($copy_result,"parent_title","N/A");
					$parent_aid = get_array_value($copy_result,"parent_aid","0");

					$ebook_concurrence = get_array_value($copy_result,"ebook_concurrence","0");
					$is_license = get_array_value($copy_result,"is_license","0");
					$is_ebook = get_array_value($copy_result,"is_ebook","0");
					echo "$parent_title [product_type_aid = $product_type_aid, copy_aid = $copy_aid, parent_aid = $parent_aid] has ".count($user_list)." queue(s)......";

					if($copy_status == '0' || $parent_status == '0' || $is_ebook == '0' || $is_license == '0' || $ebook_concurrence == '0'){
						echo "Book out of conditions. Do nothing.<BR>";
						continue;
					}
					// print_r($copy_result);

					$this->load->model($this->shelf_model,"shelf");
					$this->shelf->set_where(array("product_type_aid"=>$product_type_aid, "copy_aid"=>$copy_aid));
					$shelf_result = $this->shelf->load_records(false);
					$on_shelf = 0;
					if(is_var_array($shelf_result)){
						$on_shelf = count($shelf_result);
					}
					// echo "<br>sql : ".$this->db->last_query();
					$available = $ebook_concurrence - $on_shelf;
					if($available <= 0){
						echo "Max concurrence. Do nothing.<BR>";
						continue;
					}
					echo "<BR>on shelf = $on_shelf , ebook_concurrence = $ebook_concurrence , available = $available";

					$i=0;
					if(is_var_array($user_list)){
						foreach ($user_list as $obj) {
							$user_aid = get_array_value($obj,"user_aid","0");
							$confirm_code = get_array_value($obj,"confirm_code","");
							$i++;
							if($i <= $available){
								echo "<BR>$i. user_aid = $user_aid ";
								if(!is_blank($confirm_code)){
									echo ": already send confirm code.....Do nothing.";
								}else{
									$confirm_code = trim(random_string('alnum', 12));
									echo ": start send mail , confirm_code = $confirm_code , expired = ".date('Y-m-d H:i:s', strtotime("+1 day"));

									$data = array();
									$data["confirm_code"] = $confirm_code;
									$data["expiration_date"] = date('Y-m-d H:i:s', strtotime("+1 day"));
									$data_where = array();
									$data_where["user_aid"] = $user_aid;
									$data_where["product_type_aid"] = $product_type_aid;
									$data_where["copy_aid"] = $copy_aid;
									$data_where["status"] = "1";
									$this->load->model($this->reserve_model,"reserve");
									$this->reserve->set_where($data_where);
									$r = $this->reserve->update_record($data);
									print_r($data);
									echo "<HR>";
									print_r($data_where);

									$this->load->model($this->user_model,"user");
									$this->user->set_where(array("aid"=>$user_aid));
									$user_result = $this->user->load_record(false);
									if($user_result){
										$email = get_array_value($user_result,"email","");
										if(!is_blank($email)){
											$this->lang->load('mail');											
											$subject = $this->lang->line('mail_subject_reserve_confirm');
											$body = $this->lang->line('mail_content_reserve_confirm');
											
											// $email = "asitgets@gmail.com";
									
											$body = str_replace("{doc_type}", "&nbsp;" , $body);
											$body = str_replace("{name}", trim(get_user_info($user_result)) , $body);
											$body = str_replace("{email}", trim($email) , $body);
											$body = str_replace("{title}", trim($parent_title) , $body);
											$body = str_replace("{url_confirm}", site_url('reservation-confirm/'.$user_aid.'-'.$copy_aid.'-'.$product_type_aid.'-'.$confirm_code), $body);
											$body = str_replace("{url_cancel}", site_url('reservation-cancel/'.$user_aid.'-'.$copy_aid.'-'.$product_type_aid.'-'.$confirm_code), $body);

											$this->load->library('email');
											$config = $this->get_init_email_config();
											if(is_var_array($config)){ 
												$this->email->initialize($config); 
												$this->email->set_newline("\r\n");
											}
											
											// Send message
											$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
											$this->email->to($email);
											//$this->email->bcc('asitgets@gmail.com,yaowaluk_tarn@bookdose.com'); 

											$this->email->subject($subject);
											$this->email->message($body);
											// echo $this->email->print_debugger();
											echo $this->email->send();
										}
									}

								}
							}else{
								break;
							}
						}
					}
				}
			}
		}
	}

	function cron_mail_daily(){
		header('Content-Type: text/html; charset=utf-8');
		echo "cron_mail_daily <BR>";

		$today_date = date("Y-m-d");
		$tomorrow = date("Y-m-d", time()+86400); 
		$day_3_ago = date("Y-m-d", time()-259200); 
		echo "Today is ".$today_date."<BR>";
		echo "Tomorrow is ".$tomorrow."<BR>";
		echo "3 Days ago is ".$day_3_ago."<BR>";
		echo "<HR>";

		$this->load->model($this->transaction_model,"transaction");
		$tmp = array();
		$tmp["return_status"] = "0";
		$tmp["due_date"] = $tomorrow;
		$this->transaction->set_where($tmp);
		$this->transaction->set_order_by("user_aid ASC, barcode ASC");
		$reminder_result = $this->transaction->load_records(true);
		echo "Start : Due Date Reminder : <BR>";
		if(is_var_array($reminder_result)){
			$user_aid_current = "";
			$user_email_current = "";
			$user_name_current = "";
			$product_list = "";
			$i = 0;
			foreach ($reminder_result as $item) {
				$i++;
				$barcode = get_array_value($item,"barcode","-");
				$title = get_array_value($item,"title","-");
				$borrowing_date = get_array_value($item,"borrowing_date","-");
				$due_date = get_array_value($item,"due_date","-");
				$user_aid = get_array_value($item,"user_aid","-");
				$full_name_th = get_array_value($item,"full_name_th","-");
				$email = get_array_value($item,"email","-");
				echo "Barcode : ".$barcode.", Title : ".$title.", Email : ".$email."<BR>";
				if($user_aid_current != $user_aid){
					if(!is_blank($product_list)){
						$product_list .= "</table>";
						//Send mail
						$subject = $this->lang->line('mail_subject_transaction_reminder');
						$body = $this->lang->line('mail_content_transaction_reminder');
						$body = str_replace("{doc_type}", "&nbsp;" , $body);
						$body = str_replace("{email}", $user_email_current , $body);
						$body = str_replace("{name}", $user_name_current , $body);
						$body = str_replace("{date}", get_datetime_pattern("dmy_EN_SHORT", $due_date, "-") , $body);
						$body = str_replace("{product_list}", $product_list , $body);
						echo $body;
						$this->load->library('email');
						$config = $this->get_init_email_config();
						if(is_var_array($config)){ 
							$this->email->initialize($config); 
							$this->email->set_newline("\r\n");
						}
						
						// Send message
						$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
						$this->email->to($user_email_current);
						$this->email->bcc('');
						
						$this->email->subject($subject);
						$this->email->message($body);
						// echo $this->email->print_debugger();
						echo $this->email->send();

						$user_aid_current = $user_aid;
						$user_name_current = $full_name_th;
						$user_email_current = $email;
						$i = 0;
						$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
						$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Check Out Date</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Due Date</td></tr>";
					}else{
						$user_aid_current = $user_aid;
						$user_name_current = $full_name_th;
						$user_email_current = $email;
						$i = 0;
						$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
						$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Check Out Date</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Due Date</td></tr>";
					}
				}

				$class = ($i%2 == "0") ? "background-color:D1DCF1" : "";
				$product_list .= "<tr style='".$class."'><td style='border-bottom:1px solid #868A9C'>".$barcode."</td><td style='border-bottom:1px solid #868A9C'>".$title."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", $borrowing_date, "-") ."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", $due_date, "-") ."</td></tr>";
			}

			if(!is_blank($product_list)){
				$product_list .= "</table>";
				//Send mail
				$subject = $this->lang->line('mail_subject_transaction_reminder');
				$body = $this->lang->line('mail_content_transaction_reminder');
				$body = str_replace("{doc_type}", "&nbsp;" , $body);
				$body = str_replace("{product_list}", $product_list , $body);
				$body = str_replace("{email}", $email , $body);
				$body = str_replace("{name}", $full_name_th , $body);
				echo $body;
				$this->load->library('email');
				$config = $this->get_init_email_config();
				if(is_var_array($config)){ 
					$this->email->initialize($config); 
					$this->email->set_newline("\r\n");
				}
				
				// Send message
				$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
				$this->email->to($email);
				$this->email->bcc('');

				$this->email->subject($subject);
				$this->email->message($body);
				// echo $this->email->print_debugger();
				echo $this->email->send();
			}

		}
		echo " End : Due Date Reminder<BR>";
		echo "<HR>";

		$this->load->model($this->transaction_model,"transaction");
		$tmp = array();
		$tmp["return_status"] = "0";
		$tmp["due_date"] = $day_3_ago;
		$this->transaction->set_where($tmp);
		$this->transaction->set_order_by("user_aid ASC, barcode ASC");
		$overdue_result = $this->transaction->load_records(true);
		echo "Start : Overdue Notice : <BR>";
		if(is_var_array($overdue_result)){
			$user_aid_current = "";
			$user_email_current = "";
			$user_name_current = "";
			$product_list = "";
			$i = 0;
			foreach ($overdue_result as $item) {
				$i++;
				$barcode = get_array_value($item,"barcode","-");
				$title = get_array_value($item,"title","-");
				$borrowing_date = get_array_value($item,"borrowing_date","-");
				$due_date = get_array_value($item,"due_date","-");
				$user_aid = get_array_value($item,"user_aid","-");
				$full_name_th = get_array_value($item,"full_name_th","-");
				$email = get_array_value($item,"email","-");
				echo "Barcode : ".$barcode.", Title : ".$title.", Email : ".$email."<BR>";
				if($user_aid_current != $user_aid){
					if(!is_blank($product_list)){
						$product_list .= "</table>";
						//Send mail
						$subject = $this->lang->line('mail_subject_transaction_overdue');
						$body = $this->lang->line('mail_content_transaction_overdue');
						$body = str_replace("{doc_type}", "&nbsp;" , $body);
						$body = str_replace("{email}", $user_email_current , $body);
						$body = str_replace("{name}", $user_name_current , $body);
						$body = str_replace("{product_list}", $product_list , $body);
						echo $body;
						$this->load->library('email');
						$config = $this->get_init_email_config();
						if(is_var_array($config)){ 
							$this->email->initialize($config); 
							$this->email->set_newline("\r\n");
						}
						
						// Send message
						$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
						$this->email->to($user_email_current);
						$this->email->bcc('');
						
						$this->email->subject($subject);
						$this->email->message($body);
						// echo $this->email->print_debugger();
						echo $this->email->send();
						$user_aid_current = $user_aid;
						$user_name_current = $full_name_th;
						$user_email_current = $email;
						$i = 0;
						$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
						$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Check Out Date</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Due Date</td></tr>";
					}else{
						$user_aid_current = $user_aid;
						$user_name_current = $full_name_th;
						$user_email_current = $email;
						$i = 0;
						$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
						$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Check Out Date</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Due Date</td></tr>";
					}
				}

				$class = ($i%2 == "0") ? "background-color:D1DCF1" : "";
				$product_list .= "<tr style='".$class."'><td style='border-bottom:1px solid #868A9C'>".$barcode."</td><td style='border-bottom:1px solid #868A9C'>".$title."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", $borrowing_date, "-") ."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", $due_date, "-") ."</td></tr>";
			}

			if(!is_blank($product_list)){
				$product_list .= "</table>";
				//Send mail
				$subject = $this->lang->line('mail_subject_transaction_overdue');
				$body = $this->lang->line('mail_content_transaction_overdue');
				$body = str_replace("{doc_type}", "&nbsp;" , $body);
				$body = str_replace("{product_list}", $product_list , $body);
				$body = str_replace("{email}", $email , $body);
				$body = str_replace("{name}", $full_name_th , $body);
				echo $body;
				$this->load->library('email');
				$config = $this->get_init_email_config();
				if(is_var_array($config)){ 
					$this->email->initialize($config); 
					$this->email->set_newline("\r\n");
				}
				
				// Send message
				$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
				$this->email->to($email);
				$this->email->bcc('');
				
				$this->email->subject($subject);
				$this->email->message($body);
				// echo $this->email->print_debugger();
				echo $this->email->send();
			}

		}else{
			echo "No Overdue Notice<BR>";
		}
		echo " End : Overdue Notice<BR>";
	}

	function update_all_parent(){
		header('Content-Type: text/html; charset=utf-8');
		$this->load->model($this->book_model,"book");
		$result = $this->book->load_records(false);
		if(is_var_array($result)){
			foreach ($result as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","0");
				if($aid > 0){
					echo $title.' start update parent .... ';
					echo $this->book->update_parent($aid);
					echo '<BR />';
				}
			}
		}
		echo "<HR />";

		$this->load->model($this->magazine_model,"magazine");
		$result = $this->magazine->load_records(false);
		if(is_var_array($result)){
			foreach ($result as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","0");
				if($aid > 0){
					echo $title.' start update parent .... ';
					echo $this->magazine->update_parent($aid);
					echo '<BR />';
				}
			}
		}
		echo "<HR />";
		/*
		$this->load->model($this->vdo_model,"vdo");
		$result = $this->vdo->load_records(false);
		if(is_var_array($result)){
			foreach ($result as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","0");
				if($aid > 0){
					echo $title.' start update parent .... ';
					echo $this->vdo->update_parent($aid);
					echo '<BR />';
				}
			}
		}
		*/
		echo "<HR />";
		$this->load->model($this->others_model,"others");
		$result = $this->others->load_records(false);
		if(is_var_array($result)){
			foreach ($result as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","0");
				if($aid > 0){
					echo $title.' start update parent .... ';
					echo $this->others->update_parent($aid);
					echo '<BR />';
				}
			}
		}
	}

	function update_view_download(){
		$this->load->model($this->view_all_download_history_model,"vad_for_view");
		$this->vad_for_view->update_data();
	}

	function update_view_reserve(){
		$this->load->model($this->view_all_reserve_model,"var_for_view");
		$this->var_for_view->update_data();
	}

	function check_test(){
		$this->load->model($this->view_all_products_model,"vap_for_view");
		$this->vap_for_view->update_data();
	}


	function migrate_book_pttep(){
		header('Content-Type: text/html; charset=utf-8');
		$this->load->library('PHPExcel');
		//  Include PHPExcel_IOFactory
		// include 'PHPExcel/IOFactory.php';

		$inputFileName = './tmp/5.csv';
		echo "inputFileName = $inputFileName<BR/>";
		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		echo "highestRow = $highestRow, highestColumn = $highestColumn<BR>";

		//  Loop through each row of the worksheet in turn
		for ($row = 2; $row <= $highestRow; $row++){ 
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			// print_r($rowData[0]);
			if(is_var_array($rowData[0])){
				$rowData = $rowData[0];
				$isbn = trim(get_array_value($rowData, "1", ""));
				$call_no_a = trim(get_array_value($rowData, "2", ""));
				$call_no_b1 = trim(get_array_value($rowData, "3", ""));
				$call_no_b2 = trim(get_array_value($rowData, "4", ""));
				$call_no_b3 = trim(get_array_value($rowData, "5", ""));
				$author_personal = trim(get_array_value($rowData, "6", ""));
				$author_corporate = trim(get_array_value($rowData, "7", ""));
				$title_245_a = trim(get_array_value($rowData, "8", ""));;
				$title_245_b = trim(get_array_value($rowData, "9", ""));
				$title_245_c = trim(get_array_value($rowData, "10", ""));
				$title_246_a = trim(get_array_value($rowData, "11", ""));
				$title_246_b = trim(get_array_value($rowData, "12", ""));
				$editions = trim(get_array_value($rowData, "13", ""));
				$place_of_pub = trim(get_array_value($rowData, "14", ""));
				$publisher = trim(get_array_value($rowData, "15", ""));
				$date_of_pub = trim(get_array_value($rowData, "16", ""));
				$page_300_a  = trim(get_array_value($rowData, "17", ""));
				$page_300_b = trim(get_array_value($rowData, "18", ""));
				$page_300_c = trim(get_array_value($rowData, "19", ""));
				$price = trim(get_array_value($rowData, "20", ""));
				$series = trim(get_array_value($rowData, "21", ""));
				$series_volume = trim(get_array_value($rowData, "22", ""));
				$general_note = trim(get_array_value($rowData, "23", ""));
				$description_504_a = trim(get_array_value($rowData, "24", ""));
				$description_520_a = trim(get_array_value($rowData, "25", ""));
				$subject_650_a = trim(get_array_value($rowData, "26", ""));
				$subject_650_a1 = trim(get_array_value($rowData, "27", ""));
				$subject_650_a2 = trim(get_array_value($rowData, "28", ""));
				$added_personal_name_700_a = trim(get_array_value($rowData, "29", ""));
				$added_personal_name_700_a1 = trim(get_array_value($rowData, "30", ""));
				$added_personal_name_700_a2 = trim(get_array_value($rowData, "31", ""));
				$added_personal_name_700_a3 = trim(get_array_value($rowData, "32", ""));
				$added_personal_name_710_a = trim(get_array_value($rowData, "33", ""));
				$barcode = trim(get_array_value($rowData, "34", ""));
				$product_type_aid = "1";
				$product_type_minor_aid = "2";
				// echo "barcode = $barcode<BR>";
				// echo "publisher_name = $publisher_name<BR>";
				 echo "title_245_a = $title_245_a   ";
				// echo "total_page = $total_page<BR>";
				// echo "book_weight = $book_weight<BR>";
				// echo "book_size = $book_size<BR>";
				// echo "is_ebook = $is_ebook<BR>";
				// echo "alias = $alias<BR>";
				// echo "cover_price = $cover_price<BR>";
				// exit();
				
				//find category
				// $product_category_aid = "";
				// $category = "";
				// $this->load->model($this->product_category_model,"product_category");
				// $this->product_category->set_where(array("name"=>$category_name));
				// $category_result = $this->product_category->load_record(false);
				// if(is_var_array($category_result)){
				// 	$category = ",".get_array_value($category_result,"aid","").",";
				// 	$product_category_aid = get_array_value($category_result,"aid","");
				// }else{
				// 	$tmp = array();
				// 	$tmp["user_owner_aid"] = "1";
				// 	$tmp["product_main_aid"] = "1";
				// 	$tmp["weight"] = "0";
				// 	$tmp["status"] = "1";
				// 	$tmp["name"] = $category_name;
				// 	$tmp["url"] = getUrlString($category_name);
				// 	$tmp_aid = $this->product_category->insert_record($tmp);
				// 	$category = ",".$tmp_aid.",";
				// 	$product_category_aid = $tmp_aid;
				// }

				//find publisher
				$publisher_aid = "";
				$this->load->model($this->publisher_model,"publisher");
				$this->publisher->set_where(array("name"=>$publisher));
				$publisher_result = $this->publisher->load_record(false);
				if(is_var_array($publisher_result)){
					$publisher_aid = get_array_value($publisher_result,"aid","");
				}else{
					$tmp = array();
					$tmp["user_owner_aid"] = "1";
					// $tmp["weight"] = "0";
					$tmp["status"] = "1";
					$tmp["name"] = $publisher;
					$tmp["url"] = getUrlString($publisher);
					$tmp_cid = "";
					if(is_blank($tmp_cid)){
						do{
							$this->load->model("Setting_config_model",'setting_config');		
							$obj = $this->setting_config->get_config_rni_by_cid("rn-publisher");
							$tmp_cid = trim(get_array_value($obj,"barcode",""));
						}while( $this->isPublisherCidExits($tmp_cid) );
					}
					$tmp["cid"] = $tmp_cid;
					$tmp["contact_name"] = "";
					$tmp["contact_number"] = "";
					$tmp["email"] = "";
					$tmp["logo"] = "";
					$tmp["remark"] = "";
					$tmp_aid = $this->publisher->insert_record($tmp);
					$publisher_aid = $tmp_aid;
				}


				// $product_type_minor_aid = "2";
				// echo "title = $title<BR>";
				// $percent_discount = $discount;
				// if($discount > 0 && $discount < 1){
				// 	$percent_discount = $discount*100;
				// }

				$data = array();
				$data["user_owner_aid"] = "1";
				$data["product_main_aid"] = "6";
				$data["product_type_aid"] = $product_type_aid;
				$data["publisher_aid"] = $publisher_aid;
				$data["title"] = $title_245_a;
				$data["author"] = $author_personal;
				$data["status"] = "1";
				$data["weight"] = "0";
				$data["is_new"] = "0";
				$data["is_recommended"] = "0";
				$data["is_home"] = "0";
				$data["publish_date"] = NULL;
				$data["expired_date"] = NULL;
				$data["category"] = "";
				$data["uri"] = NULL;
				$data["tag"] = NULL;
				$data["total_copy"] = "0";
				$data["total_view"] = "0";
				$data["total_view_web"] = "0";
				$data["total_view_device"] = "0";
				$data["total_download"] = "0";
				$data["total_download_web"] = "0";
				$data["total_download_device"] = "0";
				$data["total_read"] = "0";
				$data["total_read_web"] = "0";
				$data["total_read_device"] = "0";
				$data["total_rental"] = "0";
				$data["best_price"] = "0";
				$data["has_ebook"] = "0";
				$data["has_license"] = "0";
				$data["reward_point"] = "0";
				$data["review_point"] = "0";
				// print_r($data);
				// exit();

				$this->load->model("Book_model","book");
				$parent_aid = $this->book->insert_record($data);
				$parent_cid = get_text_pad($parent_aid,"0",CONST_ZERO_PAD_FOR_PRODUCT);
				$parent_upload_path = "uploads/".CONST_PROJECT_CODE."/book/".ceil($parent_aid/100)."/".$parent_cid."/";
				$data = array();
				$data["cid"] = $parent_cid;
				$data["upload_path"] = $parent_upload_path;
				$this->book->set_where(array("aid"=>$parent_aid));
				$this->book->update_record($data);

				$copy_cid = "";
				do{
					$copy_cid = trim(random_string('alnum', 12));
				}while( $this->isBookCidExits($copy_cid) );
				if(is_blank($barcode)){
					do{
						$this->load->model("Setting_config_model",'setting_config');		
						$obj = $this->setting_config->get_config_rni_by_product_type_minor_aid($product_type_minor_aid);
						$barcode = trim(get_array_value($obj,"barcode",""));
						$value = trim(get_array_value($obj,"value","0"));
					}while( $this->isBookBarcodeExits($barcode, "") );
				}

				//save field
				$this->load->model("Book_field_model","book_field");
				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "1";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "58";
				$data["name"] = "Title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $title_245_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "2";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "59";
				$data["name"] = "Author";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "100";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $author_personal;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "3";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "60";
				$data["name"] = "Description";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "520";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $description_520_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "4";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "61";
				$data["name"] = "Total Pages";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "300";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $page_300_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "5";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "63";
				$data["name"] = "Call No. 1: Classification No.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $call_no_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "6";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "64";
				$data["name"] = "ISBN";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "020";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $isbn;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "7";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "65";
				$data["name"] = "Edition";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "250";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $editions;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "8";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "66";
				$data["name"] = "Subject";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $subject_650_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "9";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "67";
				$data["name"] = "Added Author";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "700";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_700_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "10";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "68";
				$data["name"] = "Link";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "856";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "6";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "11";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "69";
				$data["name"] = "Place of publication";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "260";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $place_of_pub;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "12";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "70";
				$data["name"] = "Publisher";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "260";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $publisher;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "13";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "71";
				$data["name"] = "Date of pub";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "260";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "c";
				$data["field_data"] = $date_of_pub;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "14";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "78";
				$data["name"] = "Author Corporate Name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "110";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $author_corporate;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "15";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "79";
				$data["name"] = "Price";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "350";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $price;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "16";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "80";
				$data["name"] = "Series name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "490";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $series;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "17";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "81";
				$data["name"] = "Series Vol.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "490";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "v";
				$data["field_data"] = $series_volume;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "18";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "83";
				$data["name"] = "Call No. 2:  Author ID";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $call_no_b1;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "19";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "93";
				$data["name"] = "Call No. 4:  Item/Issue No.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $call_no_b3;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "20";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "94";
				$data["name"] = "Call No. 3: Publish Year";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $call_no_b2;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "21";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "95";
				$data["name"] = "Remainder of title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $title_245_b;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "22";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "96";
				$data["name"] = "Statement of responsibility, etc.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "c";
				$data["field_data"] = $title_245_c;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "23";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "97";
				$data["name"] = "Subject";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $subject_650_a1;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "24";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "98";
				$data["name"] = "General subdivision";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "x";
				$data["field_data"] = $subject_650_a2;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "25";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "99";
				$data["name"] = "Geographic subdivision";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "z";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "26";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "100";
				$data["name"] = "Personal name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "700";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_700_a1;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "27";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "101";
				$data["name"] = "Corporate name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "710";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_710_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "28";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "102";
				$data["name"] = "Other physical details";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "300";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $page_300_b;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "29";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "103";
				$data["name"] = "c - Dimensions";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "300";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "c";
				$data["field_data"] = $page_300_c;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "30";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "104";
				$data["name"] = "a - General note";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "500";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "31";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "116";
				$data["name"] = "Corporate name or jurisdiction name as entry element";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "610";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "32";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "117";
				$data["name"] = "General subdivision";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "610";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "x";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "33";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "121";
				$data["name"] = "Bibliography, etc. note";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "504";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "34";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "122";
				$data["name"] = "Title proper/short title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "246";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $title_246_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "35";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "123";
				$data["name"] = "Personal name 3";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "700";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_700_a2;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "36";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "124";
				$data["name"] = "Subordinate unit";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "610";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);


				//save category
				// $data = array();
				// $data["parent_aid"] = $parent_aid;
				// $data["product_category_aid"] = $product_category_aid;
				// $data["product_type_aid"] = $product_type_aid;
				// $data["user_owner_aid"] = "1";
				// $this->load->model("Book_ref_product_category_model","book_ref_product_category");
				// $this->book_ref_product_category->insert_record($data);

				$copy_upload_path = $parent_upload_path."book_copy/".$barcode."_".$copy_cid."/";

				//save book copy
				$data = array();
				$data["cid"] = $copy_cid;
				$data["barcode"] = $barcode;
				$data["nonconsume_identifier"] = NULL;
				$data["user_owner_aid"] = "1";
				$data["parent_aid"] = $parent_aid;
				$data["product_type_aid"] = $product_type_aid;
				$data["product_type_minor_aid"] = $product_type_minor_aid;
				$data["copy_title"] = "";
				$data["publish_date"] = NULL;
				$data["expired_date"] = NULL;
				$data["weight"] = "0";
				$data["upload_path"] = $copy_upload_path;
				$data["file_upload"] = "";
				// $data["digital_file_type"] = "";
				$data["use_digital_gen"] = "";
				// $data["device_support"] = "";
				$data["status"] = "1";
				$data["no_1"] = $call_no_a;
				$data["no_2"] = $call_no_b1;
				$data["no_3"] = $call_no_b2;
				$data["no_4"] = $call_no_b3;
				$no_call_number = "";
				if($call_no_a != ""){
					$no_call_number .= $call_no_a." ";
				}
				if($call_no_b1 != ""){
					$no_call_number .= $call_no_b1." ";
				}
				if($call_no_b2 != ""){
					$no_call_number .= $call_no_b2." ";
				}
				if($call_no_b3 != ""){
					$no_call_number .= $call_no_b3;
				}
				echo $no_call_number."<br/>";
				$data["call_number"] = $no_call_number;
				$data["cover_price"] = $price;
				$data["source"] = "";
				$data["note_1"] = "";
				$data["note_2"] = "";
				$data["note_3"] = "";
				$data["type"] = "2";
				$data["type_minor"] = "3";
				$data["possession"] = "2";
				$data["is_license"] = "0";
				$data["is_ebook"] = "0";
				$data["ebook_concurrence"] = "0";
				// $data["percent_discount"] = $percent_discount;
				$data["digital_price"] = "0";
				// $data["digital_price_ios"] = NULL;
				// $data["digital_price_android"] = NULL;
				$data["digital_point"] = "0";
				$data["paper_price"] = "0";
				$data["paper_point"] = "0";
				$data["in_stock"] = "0";
				$data["rental_period"] = "7";
				$data["rental_fee"] = "0";
				$data["rental_fee_point"] = "0";
				$data["rental_fine_fee"] = "0";
				$data["shelf_status"] = "1";
				$data["shelf_name"] = NULL;
				$data["transport_aid"] = "0";
				$data["transport_price"] = "0";
				$this->load->model("Book_copy_model","book_copy");
				$this->book_copy->insert_record($data);

				echo "<HR>";
				// exit();
			}
			//  Insert row data array into your database of choice here
		}
	}
		function migrate_media_pttep(){
		header('Content-Type: text/html; charset=utf-8');
		$this->load->library('PHPExcel');
		//  Include PHPExcel_IOFactory
		// include 'PHPExcel/IOFactory.php';

		$inputFileName = './tmp/2.csv';
		echo "inputFileName = $inputFileName<BR/>";
		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		echo "highestRow = $highestRow, highestColumn = $highestColumn<BR>";

		//  Loop through each row of the worksheet in turn
		for ($row = 2; $row <= $highestRow; $row++){ 
			//  Read a row of data into an array
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			// print_r($rowData[0]);
			if(is_var_array($rowData[0])){
				$rowData = $rowData[0];
				$isbn = trim(get_array_value($rowData, "1", ""));
				$call_no_a = trim(get_array_value($rowData, "2", ""));
				$call_no_b1 = trim(get_array_value($rowData, "3", ""));
				$call_no_b2 = trim(get_array_value($rowData, "4", ""));
				$call_no_b3 = trim(get_array_value($rowData, "5", ""));
				$author_personal = trim(get_array_value($rowData, "6", ""));
				$author_corporate = trim(get_array_value($rowData, "7", ""));
				$title_245_a = trim(get_array_value($rowData, "8", ""));;
				$title_245_b = trim(get_array_value($rowData, "9", ""));
				$title_245_c = trim(get_array_value($rowData, "10", ""));
				$title_246_a = trim(get_array_value($rowData, "11", ""));
				$title_246_b = trim(get_array_value($rowData, "12", ""));
				$editions = trim(get_array_value($rowData, "13", ""));
				$place_of_pub = trim(get_array_value($rowData, "14", ""));
				$publisher = trim(get_array_value($rowData, "15", ""));
				$date_of_pub = trim(get_array_value($rowData, "16", ""));
				$page_300_a  = trim(get_array_value($rowData, "17", ""));
				$page_300_b = trim(get_array_value($rowData, "18", ""));
				$page_300_c = trim(get_array_value($rowData, "19", ""));
				$price = trim(get_array_value($rowData, "20", ""));
				$series = trim(get_array_value($rowData, "21", ""));
				$series_volume = trim(get_array_value($rowData, "22", ""));
				$general_note = trim(get_array_value($rowData, "23", ""));
				$description_504_a = trim(get_array_value($rowData, "24", ""));
				$description_520_a = trim(get_array_value($rowData, "25", ""));
				$subject_650_a = trim(get_array_value($rowData, "26", ""));
				$subject_650_a1 = trim(get_array_value($rowData, "27", ""));
				$subject_650_a2 = trim(get_array_value($rowData, "28", ""));
				$added_personal_name_700_a = trim(get_array_value($rowData, "29", ""));
				$added_personal_name_700_a1 = trim(get_array_value($rowData, "30", ""));
				$added_personal_name_700_a2 = trim(get_array_value($rowData, "31", ""));
				$added_personal_name_700_a3 = trim(get_array_value($rowData, "32", ""));
				$added_personal_name_710_a = trim(get_array_value($rowData, "33", ""));
				$barcode = trim(get_array_value($rowData, "34", ""));
				$product_type_aid = "1";
				$product_type_minor_aid = "2";
				// echo "barcode = $barcode<BR>";
				// echo "publisher_name = $publisher_name<BR>";
				 echo "title_245_a = $title_245_a   ";
				// echo "total_page = $total_page<BR>";
				// echo "book_weight = $book_weight<BR>";
				// echo "book_size = $book_size<BR>";
				// echo "is_ebook = $is_ebook<BR>";
				// echo "alias = $alias<BR>";
				// echo "cover_price = $cover_price<BR>";
				// exit();
				
				//find category
				// $product_category_aid = "";
				// $category = "";
				// $this->load->model($this->product_category_model,"product_category");
				// $this->product_category->set_where(array("name"=>$category_name));
				// $category_result = $this->product_category->load_record(false);
				// if(is_var_array($category_result)){
				// 	$category = ",".get_array_value($category_result,"aid","").",";
				// 	$product_category_aid = get_array_value($category_result,"aid","");
				// }else{
				// 	$tmp = array();
				// 	$tmp["user_owner_aid"] = "1";
				// 	$tmp["product_main_aid"] = "1";
				// 	$tmp["weight"] = "0";
				// 	$tmp["status"] = "1";
				// 	$tmp["name"] = $category_name;
				// 	$tmp["url"] = getUrlString($category_name);
				// 	$tmp_aid = $this->product_category->insert_record($tmp);
				// 	$category = ",".$tmp_aid.",";
				// 	$product_category_aid = $tmp_aid;
				// }

				//find publisher
				$publisher_aid = "";
				$this->load->model($this->publisher_model,"publisher");
				$this->publisher->set_where(array("name"=>$publisher));
				$publisher_result = $this->publisher->load_record(false);
				if(is_var_array($publisher_result)){
					$publisher_aid = get_array_value($publisher_result,"aid","");
				}else{
					$tmp = array();
					$tmp["user_owner_aid"] = "1";
					// $tmp["weight"] = "0";
					$tmp["status"] = "1";
					$tmp["name"] = $publisher;
					$tmp["url"] = getUrlString($publisher);
					$tmp_cid = "";
					if(is_blank($tmp_cid)){
						do{
							$this->load->model("Setting_config_model",'setting_config');		
							$obj = $this->setting_config->get_config_rni_by_cid("rn-publisher");
							$tmp_cid = trim(get_array_value($obj,"barcode",""));
						}while( $this->isPublisherCidExits($tmp_cid) );
					}
					$tmp["cid"] = $tmp_cid;
					$tmp["contact_name"] = "";
					$tmp["contact_number"] = "";
					$tmp["email"] = "";
					$tmp["logo"] = "";
					$tmp["remark"] = "";
					$tmp_aid = $this->publisher->insert_record($tmp);
					$publisher_aid = $tmp_aid;
				}


				// $product_type_minor_aid = "2";
				// echo "title = $title<BR>";
				// $percent_discount = $discount;
				// if($discount > 0 && $discount < 1){
				// 	$percent_discount = $discount*100;
				// }

				$data = array();
				$data["user_owner_aid"] = "1";
				$data["product_main_aid"] = "6";
				$data["product_type_aid"] = $product_type_aid;
				$data["publisher_aid"] = $publisher_aid;
				$data["title"] = $title_245_a;
				$data["author"] = $author_personal;
				$data["status"] = "1";
				$data["weight"] = "0";
				$data["is_new"] = "0";
				$data["is_recommended"] = "0";
				$data["is_home"] = "0";
				$data["publish_date"] = NULL;
				$data["expired_date"] = NULL;
				$data["category"] = "";
				$data["uri"] = NULL;
				$data["tag"] = NULL;
				$data["total_copy"] = "0";
				$data["total_view"] = "0";
				$data["total_view_web"] = "0";
				$data["total_view_device"] = "0";
				$data["total_download"] = "0";
				$data["total_download_web"] = "0";
				$data["total_download_device"] = "0";
				$data["total_read"] = "0";
				$data["total_read_web"] = "0";
				$data["total_read_device"] = "0";
				$data["total_rental"] = "0";
				$data["best_price"] = "0";
				$data["has_ebook"] = "0";
				$data["has_license"] = "0";
				$data["reward_point"] = "0";
				$data["review_point"] = "0";
				// print_r($data);
				// exit();

				$this->load->model("Book_model","book");
				$parent_aid = $this->book->insert_record($data);
				$parent_cid = get_text_pad($parent_aid,"0",CONST_ZERO_PAD_FOR_PRODUCT);
				$parent_upload_path = "uploads/".CONST_PROJECT_CODE."/book/".ceil($parent_aid/100)."/".$parent_cid."/";
				$data = array();
				$data["cid"] = $parent_cid;
				$data["upload_path"] = $parent_upload_path;
				$this->book->set_where(array("aid"=>$parent_aid));
				$this->book->update_record($data);

				$copy_cid = "";
				do{
					$copy_cid = trim(random_string('alnum', 12));
				}while( $this->isBookCidExits($copy_cid) );
				if(is_blank($barcode)){
					do{
						$this->load->model("Setting_config_model",'setting_config');		
						$obj = $this->setting_config->get_config_rni_by_product_type_minor_aid($product_type_minor_aid);
						$barcode = trim(get_array_value($obj,"barcode",""));
						$value = trim(get_array_value($obj,"value","0"));
					}while( $this->isBookBarcodeExits($barcode, "") );
				}

				//save field
				//save field
				$this->load->model("Book_field_model","book_field");
				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "1";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "112";
				$data["name"] = "Title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $title_245_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "2";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "113";
				$data["name"] = "Author";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "100";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $author_personal;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "3";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "114";
				$data["name"] = "Description";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "520";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $description_520_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "4";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "115";
				$data["name"] = "Series statement";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "490";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $page_300_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "5";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "118";
				$data["name"] = "Remainder of title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $call_no_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "6";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "119";
				$data["name"] = "Corporate name or jurisdiction name as entry element";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "110";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $isbn;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "7";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "120";
				$data["name"] = "Subject";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $editions;
				$this->book_field->insert_record($data);

				/************************************/
				$this->load->model("Book_field_model","book_field");
				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "1";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "58";
				$data["name"] = "Title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $title_245_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "2";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "59";
				$data["name"] = "Author";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "100";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $author_personal;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "3";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "60";
				$data["name"] = "Description";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "520";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $description_520_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "4";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "61";
				$data["name"] = "Total Pages";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "300";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $page_300_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "5";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "63";
				$data["name"] = "Call No. 1: Classification No.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $call_no_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "6";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "64";
				$data["name"] = "ISBN";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "020";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $isbn;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "7";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "65";
				$data["name"] = "Edition";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "250";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $editions;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "8";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "66";
				$data["name"] = "Subject";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $subject_650_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "9";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "67";
				$data["name"] = "Added Author";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "700";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_700_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "10";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "68";
				$data["name"] = "Link";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "856";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "6";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "11";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "69";
				$data["name"] = "Place of publication";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "260";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $place_of_pub;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "12";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "70";
				$data["name"] = "Publisher";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "260";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $publisher;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "13";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "71";
				$data["name"] = "Date of pub";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "260";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "c";
				$data["field_data"] = $date_of_pub;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "14";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "78";
				$data["name"] = "Author Corporate Name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "110";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $author_corporate;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "15";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "79";
				$data["name"] = "Price";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "350";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $price;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "16";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "80";
				$data["name"] = "Series name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "490";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $series;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "17";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "81";
				$data["name"] = "Series Vol.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "490";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "v";
				$data["field_data"] = $series_volume;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "18";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "83";
				$data["name"] = "Call No. 2:  Author ID";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $call_no_b1;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "19";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "93";
				$data["name"] = "Call No. 4:  Item/Issue No.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $call_no_b3;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "20";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "94";
				$data["name"] = "Call No. 3: Publish Year";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "050";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $call_no_b2;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "21";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "95";
				$data["name"] = "Remainder of title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $title_245_b;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "22";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "96";
				$data["name"] = "Statement of responsibility, etc.";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "245";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "c";
				$data["field_data"] = $title_245_c;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "23";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "97";
				$data["name"] = "Subject";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $subject_650_a1;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "24";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "98";
				$data["name"] = "General subdivision";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "x";
				$data["field_data"] = $subject_650_a2;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "25";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "99";
				$data["name"] = "Geographic subdivision";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "650";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "z";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "26";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "100";
				$data["name"] = "Personal name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "700";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_700_a1;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "27";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "101";
				$data["name"] = "Corporate name";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "710";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_710_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "28";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "102";
				$data["name"] = "Other physical details";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "300";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = $page_300_b;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "29";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "103";
				$data["name"] = "c - Dimensions";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "300";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "c";
				$data["field_data"] = $page_300_c;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "30";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "104";
				$data["name"] = "a - General note";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "500";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "31";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "116";
				$data["name"] = "Corporate name or jurisdiction name as entry element";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "610";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "32";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "117";
				$data["name"] = "General subdivision";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "610";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "x";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "33";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "121";
				$data["name"] = "Bibliography, etc. note";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "504";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "34";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "122";
				$data["name"] = "Title proper/short title";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "246";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $title_246_a;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "35";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "123";
				$data["name"] = "Personal name 3";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "700";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "a";
				$data["field_data"] = $added_personal_name_700_a2;
				$this->book_field->insert_record($data);

				$data = array();
				$data["parent_aid"] = $parent_aid;
				$data["sequence"] = "36";
				$data["product_type_aid"] = $product_type_aid;
				$data["product_main_field_aid"] = "124";
				$data["name"] = "Subordinate unit";
				$data["user_owner_aid"] = "1";
				$data["tag"] = "610";
				$data["ind1_cd"] = "";
				$data["ind2_cd"] = "";
				$data["subfield_cd"] = "b";
				$data["field_data"] = "";
				$this->book_field->insert_record($data);


				//save category
				// $data = array();
				// $data["parent_aid"] = $parent_aid;
				// $data["product_category_aid"] = $product_category_aid;
				// $data["product_type_aid"] = $product_type_aid;
				// $data["user_owner_aid"] = "1";
				// $this->load->model("Book_ref_product_category_model","book_ref_product_category");
				// $this->book_ref_product_category->insert_record($data);

				$copy_upload_path = $parent_upload_path."book_copy/".$barcode."_".$copy_cid."/";

				//save book copy
				$data = array();
				$data["cid"] = $copy_cid;
				$data["barcode"] = $barcode;
				$data["nonconsume_identifier"] = NULL;
				$data["user_owner_aid"] = "1";
				$data["parent_aid"] = $parent_aid;
				$data["product_type_aid"] = $product_type_aid;
				$data["product_type_minor_aid"] = $product_type_minor_aid;
				$data["copy_title"] = "";
				$data["publish_date"] = NULL;
				$data["expired_date"] = NULL;
				$data["weight"] = "0";
				$data["upload_path"] = $copy_upload_path;
				$data["file_upload"] = "";
				// $data["digital_file_type"] = "";
				$data["use_digital_gen"] = "";
				// $data["device_support"] = "";
				$data["status"] = "1";
				$data["no_1"] = $call_no_a;
				$data["no_2"] = $call_no_b1;
				$data["no_3"] = $call_no_b2;
				$data["no_4"] = $call_no_b3;
				$no_call_number = "";
				if($call_no_a != ""){
					$no_call_number .= $call_no_a." ";
				}
				if($call_no_b1 != ""){
					$no_call_number .= $call_no_b1." ";
				}
				if($call_no_b2 != ""){
					$no_call_number .= $call_no_b2." ";
				}
				if($call_no_b3 != ""){
					$no_call_number .= $call_no_b3;
				}
				echo $no_call_number."<br/>";
				$data["call_number"] = $no_call_number;
				$data["cover_price"] = $price;
				$data["source"] = "";
				$data["note_1"] = "";
				$data["note_2"] = "";
				$data["note_3"] = "";
				$data["type"] = "2";
				$data["type_minor"] = "3";
				$data["possession"] = "2";
				$data["is_license"] = "0";
				$data["is_ebook"] = "0";
				$data["ebook_concurrence"] = "0";
				// $data["percent_discount"] = $percent_discount;
				$data["digital_price"] = "0";
				// $data["digital_price_ios"] = NULL;
				// $data["digital_price_android"] = NULL;
				$data["digital_point"] = "0";
				$data["paper_price"] = "0";
				$data["paper_point"] = "0";
				$data["in_stock"] = "0";
				$data["rental_period"] = "7";
				$data["rental_fee"] = "0";
				$data["rental_fee_point"] = "0";
				$data["rental_fine_fee"] = "0";
				$data["shelf_status"] = "1";
				$data["shelf_name"] = NULL;
				$data["transport_aid"] = "0";
				$data["transport_price"] = "0";
				$this->load->model("Book_copy_model","book_copy");
				$this->book_copy->insert_record($data);

				echo "<HR>";
				// exit();
			}
			//  Insert row data array into your database of choice here
		}
	}
	function isPublisherCidExits($cid){
		$this->load->model($this->publisher_model,"publisher");
		$this->publisher->set_where(array("cid"=>$cid));
		$total = $this->publisher->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function isBookCidExits($cid){
		$this->load->model($this->book_model,"book");
		$this->book->set_where(array("cid"=>$cid));
		$total = $this->book->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function isBookBarcodeExits($code, $aid){
		$this->load->model($this->book_model,"book");
		$this->book->set_where(array("barcode"=>$code));
		if(is_number_no_zero($aid)){
			$this->maibookn->set_where_not_equal(array("aid"=>$aid));
		}
		$total = $this->book->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

}

?>