<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_magazine_copy_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";

		$this_product_type_aid = '2';
		$this->data["this_product_type_aid"] = $this_product_type_aid;

		define("thisAdminSubMenu",'copy');
		@define("folderName",'product/product_back/magazine');
		
		define("TXT_TITLE",'Magazine management');
		define("TXT_INSERT_TITLE",'Add copy');
		define("TXT_UPDATE_TITLE",'Edit copy');
				
		$this->main_model = 'Magazine_copy_model';
		
		$this->lang->load('product');
		$this->lang->load('product_magazine');	
		$this->load->library('CryptographyAES');		
	}
	
	function check_exits_magazine($parent_aid="",$return_json=false){
		if(!is_number_no_zero($parent_aid)){
			redirect('admin/product-'.$product_main_url.'/magazine/status/'.md5('no-magazine'));
			return"";
		}
		$this->load->model($this->magazine_model,'magazine');
		$this->magazine->set_where(array("aid"=>$parent_aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$parent_detail = $this->magazine->load_record(true);
		if(is_var_array($parent_detail)){
			return $parent_detail;
		}else{
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this magazine.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
			}else{
				redirect('admin/product-'.$product_main_url.'/magazine/status/'.md5('no-magazine'));
				return"";
			}
		}
	}
	
	function index($product_main_url=""){
		$this->data["init_adv_search"] = "clear";
		$this->show($product_main_url);
	}
	
	function show($product_main_url="", $parent_aid="", $msg=""){
		define("thisAdminTabMenu",'product_magazine_'.$product_main_url);
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/magazine_copy_list';
		$this->data["header_title"] = TXT_TITLE;

		$product_main_result = $this->check_exits_product_main_by_url($product_main_url, false);
		$this->data["product_main_result"] = $product_main_result;
		$product_main_aid = get_array_value($product_main_result,"aid","");
		
		$parent_detail = $this->check_exits_magazine($parent_aid, false);
		$this->data["parent_detail"] = $parent_detail;
		$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($parent_detail,"title","");
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form($product_main_url="", $parent_aid=""){
		define("thisAdminTabMenu",'product_magazine_'.$product_main_url);
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/magazine_copy_form';

		$product_main_result = $this->check_exits_product_main_by_url($product_main_url, false);
		$this->data["product_main_result"] = $product_main_result;
		$product_main_aid = get_array_value($product_main_result,"aid","");

		$this->get_data_for_copy_form();
		
		$parent_detail = $this->check_exits_magazine($parent_aid, false);
		
		$this->data["header_title"] .= " for " .get_array_value($parent_detail,"title","");
		$this->data["parent_detail"] = $parent_detail;
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add($product_main_url="", $parent_aid=""){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "";
		$this->form($product_main_url, $parent_aid);
	}
	
	function edit($product_main_url="", $parent_aid="", $aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$copy_item_detail = $this->main->load_record(true);
		if(is_var_array($copy_item_detail)){
			$this->data["copy_item_detail"] = $copy_item_detail;
			if(CONST_HAS_TRANSACTION == "1"){
				$this->load->model($this->magazine_ref_user_section_model,"magazine_ref_user_section");
				$this->magazine_ref_user_section->set_where(array("copy_aid"=>$aid));
				$this->data["product_ref_user_section_result"] = $this->magazine_ref_user_section->load_records_array(false, "user_section_aid", "");
			}
			$this->form($product_main_url, $parent_aid);
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific magazine copy.');
			$this->data["js_code"] = "";
			$this->show($product_main_url, $parent_aid);
			return "";
		}
	}
	
	function save(){
		@define("thisAction",'save');
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		$this->load->model($this->main_model,'main');

		$product_main_aid = $this->input->get_post('product_main_aid');
		$product_main_result = $this->check_exits_product_main_by_aid($product_main_aid, false);
		$product_main_url = get_array_value($product_main_result,"url","");
		
		$parent_aid = trim($this->input->get_post('parent_aid'));
		$data["parent_aid"] = $parent_aid;
		$parent_detail = $this->check_exits_magazine($parent_aid, false);
		$product_type_aid = $this->data["this_product_type_aid"];
		$data["product_type_aid"] = $product_type_aid;
		$title = get_array_value($parent_detail,"title","");


		if(CONST_HAS_IPAD_APP == '1'){
			$data["nonconsume_identifier"] = trim($this->input->get_post('nonconsume_identifier'));
		}
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;

		$today_date = get_datetime_pattern("Y-m-d",$this->input->get_post('publish_date'),date("Y-m-d"));
		$upload_path = "";
		$cid = "";
		$aid = "";
		$barcode = trim($this->input->get_post('barcode'));
		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$aid));
			$itemResult = $this->main->load_record(false);
			if(!is_var_array($itemResult)){
				redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/copy/status/'.md5('no-magazine-copy'));
				return "";
			}
			$cid = get_array_value($itemResult,"cid","");
			if(is_blank($cid)){
				do{
					$cid = trim(random_string('alnum', 12));
				}while( $this->isBookCidExits($cid) );
			}
			
			if(is_blank($barcode)){
				$barcode = get_array_value($itemResult,"barcode","");
			}

			$upload_path = get_array_value($itemResult,"upload_path","");

		}else{ // insert
			$this->data["header_title"] = TXT_INSERT_TITLE;
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isMagazineCidExits($cid) );
			// echo "cid = $cid <BR>";
		}

		if(is_blank($cid)){
			redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/copy/status/'.md5('no-magazine-copy'));
			return "";
		}
		$data["cid"] = trim($cid);

		$data["publish_date"] = get_datetime_pattern("Y-m-d",$this->input->get_post('publish_date'),NULL);
		$data["expired_date"] = get_datetime_pattern("Y-m-d",$this->input->get_post('expired_date'),NULL);
		$data["weight"] = '0';
		$data["status"] = trim($this->input->get_post('status'));

		$data["copy_title"] = trim($this->input->get_post('copy_title'));
		$data["cover_price"] = trim($this->input->get_post('cover_price'));
		$data["source"] = trim($this->input->get_post('source'));
		$data["no_1"] = trim($this->input->get_post('no_1'));
		$data["no_2"] = trim($this->input->get_post('no_2'));
		$data["no_3"] = trim($this->input->get_post('no_3'));
		$data["no_4"] = trim($this->input->get_post('no_4'));
		// $data["note_1"] = trim($this->input->get_post('note_1'));
		// $data["note_2"] = trim($this->input->get_post('note_2'));
		// $data["note_3"] = trim($this->input->get_post('note_3'));

		/* Possession / Price Information */
		$type = $this->input->get_post('type');
		$type_minor = $this->input->get_post('type_minor');
		$is_license = $this->input->get_post('is_license');
		$ebook_concurrence = $this->input->get_post('ebook_concurrence');
		$possession = $this->input->get_post('possession');
		if($command == "_update"){
			$is_license_tmp = get_array_value($itemResult,"is_license","");
			if($is_license_tmp == "1" && !is_general_admin_or_higher()){
				$type = get_array_value($itemResult,"type","");
				$is_license = get_array_value($itemResult,"is_license","");
				$ebook_concurrence = get_array_value($itemResult,"ebook_concurrence","");
				$possession = get_array_value($itemResult,"possession","");
			}
		}

		$digital_price = $this->input->get_post('digital_price');
		$digital_point = $this->input->get_post('digital_point');
		$paper_price = $this->input->get_post('paper_price');
		$paper_point = $this->input->get_post('paper_point');
		$transport_aid = $this->input->get_post('transport_aid');
		$transport_price = $this->input->get_post('transport_price');
		$in_stock = $this->input->get_post('in_stock');

		$rental_period = $this->input->get_post('rental_period');
		$rental_fee = $this->input->get_post('rental_fee');
		$rental_fee_point = $this->input->get_post('rental_fee_point');
		$rental_fine_fee = $this->input->get_post('rental_fine_fee');
		$shelf_status = $this->input->get_post('shelf_status');
		$shelf_name = $this->input->get_post('shelf_name');

		// echo "type = $type , is_license = $is_license , possession = $possession <BR>";
		$type_master = array('1','2');
		$possession_master = array('1','2');
		if(!is_in_array($type, $type_master)){
			redirect('admin/product-'.$product_main_url.'/magazine/status/'.md5('copy-type-incorrect'));
			return "";
		}
		if(!is_in_array($possession, $possession_master)){
			redirect('admin/product-'.$product_main_url.'/magazine/status/'.md5('copy-possession-incorrect'));
			return "";
		}

		$product_type_minor_aid = '0';
		$is_ebook= 0;

		if($type == '1'){ // Digital
			$is_ebook = 1;
			$paper_price = "";
			$paper_point = "";
			$transport_aid = "";
			$transport_price = "";
			$in_stock = "";

			if($is_license == '1'){
				if($possession == '1'){
					redirect('admin/product-'.$product_main_url.'/magazine/status/'.md5('both-buyout-license'));
					return "";
				}
				$product_type_minor_aid = '7';
			}else{
				$is_license = "";
				$ebook_concurrence = "";
				if($possession == '1'){
					if($paper_price > 0 && $paper_point > 0){
						$product_type_minor_aid = '4';						
					}else{
						$product_type_minor_aid = '3';						
					}
				}else{
					$product_type_minor_aid = '5';
				}
			}

		}else if($type == '2'){ // Paper
			$digital_price = "";
			$digital_point = "";
			if($is_license == '1'){
				redirect('admin/product-'.$product_main_url.'/magazine/status/'.md5('both-paper-license'));
				return "";
			}else{
				$is_license = "";
				$ebook_concurrence = "";
				if($possession == '1'){
					$product_type_minor_aid = '1';
				}else{
					$product_type_minor_aid = '2';
				}
			}
		}
		// echo "product_type_minor_aid = $product_type_minor_aid <BR>";
		$data["product_type_minor_aid"] = $product_type_minor_aid;

		$data["type"] = $type;
		$data["type_minor"] = $type_minor;
		$data["possession"] = $possession;
		$data["is_ebook"] = $is_ebook;
		$data["is_license"] = $is_license;
		$data["ebook_concurrence"] = $ebook_concurrence;

		$data["digital_price"] = $digital_price;
		$data["digital_point"] = $digital_point;
		$data["paper_price"] = $paper_price;
		$data["paper_point"] = $paper_point;
		$data["transport_aid"] = $transport_aid;
		$data["transport_price"] = $transport_price;
		$data["in_stock"] = $in_stock;
		$data["rental_period"] = $rental_period;
		$data["rental_fee"] = $rental_fee;
		$data["rental_fee_point"] = $rental_fee_point;
		$data["rental_fine_fee"] = $rental_fine_fee;
		$data["shelf_status"] = $shelf_status;
		$data["shelf_name"] = $shelf_name;

		if(is_blank($barcode)){
			do{
				$this->load->model($this->setting_config_model,'setting_config');		
				$obj = $this->setting_config->get_config_rni_by_product_type_minor_aid($product_type_minor_aid);
				$barcode = trim(get_array_value($obj,"barcode",""));
				$value = trim(get_array_value($obj,"value","0"));
			}while( $this->isMagazineBarcodeExits($barcode, "") );
			$data["barcode"] = $barcode;
			// $data["barcode_no"] = $value;
		}else{
			$data["barcode"] = $barcode;
			if($this->isMagazineBarcodeExits($barcode, $aid)){
				$this->data["message"] = set_message_error("This barcode was used by other.");
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["copy_item_detail"] = $data;
				$this->form($product_main_url, $parent_aid);
				return "";
			}
			// $barcode_no = ltrim($barcode, '0');
			// if(is_int($barcode_no)){
			// 	$data["barcode_no"] = $barcode_no;
			// }
			$data["barcode"] = $barcode;
		}
		// echo "barcode = $barcode <BR>";

		if(is_blank($upload_path)){
			$upload_path = "uploads/".CONST_PROJECT_CODE."/magazine_copy/".get_datetime_pattern("Y",$today_date,"").'/'.get_datetime_pattern("m",$today_date,"").'/'.$barcode.'_'.$cid.'/';
		}
		$upload_base_path = "./".$upload_path;
		create_directories($upload_base_path);
		$data["upload_path"] = $upload_path;
		
		$upload_inc_path = $upload_base_path.'app';
		create_directories($upload_inc_path);
		$upload_inc_path = $upload_base_path.'file';
		create_directories($upload_inc_path);
		$upload_inc_path = $upload_base_path.'ftp';
		create_directories($upload_inc_path);
		$upload_inc_path = $upload_base_path.'bdvdo';
		create_directories($upload_inc_path);
		$upload_inc_path = $upload_base_path.'cover_image';
		create_directories($upload_inc_path);
		$upload_inc_path = $upload_base_path.'full';
		create_directories($upload_inc_path);
		$upload_inc_path = $upload_base_path.'sample/thumb';
		create_directories($upload_inc_path);
		$upload_inc_path = $upload_base_path.'sample/file';
		create_directories($upload_inc_path);

		/*************** Create dummy zip file *************************/
		/*
		$folder_name = $barcode."html";
		$file_full = $upload_base_path."app/".$folder_name.".zip";
		create_empty_folder_to_zip($file_full, $folder_name);

		$folder_name = $barcode."resource";
		$file_full = $upload_base_path."app/".$folder_name.".zip";
		create_empty_folder_to_zip($file_full, $folder_name);
		*/
		/*************************************************************/

		$use_digital_gen = $this->input->get_post('use_digital_gen');
		$data["use_digital_gen"] = $use_digital_gen;

		if( !is_blank(get_array_value($_FILES,"file_upload","")) && !is_blank(get_array_value($_FILES["file_upload"],"name","")) ){
			$upload_inc_path = $upload_base_path.'tmp';
			create_directories($upload_inc_path);
			
			$copy_result["parent_aid"] = $parent_aid;
			$copy_result["product_type_aid"] = $product_type_aid;
			$copy_result["barcode"] = $barcode;
			
			$secret_key = get_secret_key($copy_result); 
			$iv = get_iv($copy_result); 
			
			if(file_exists("./".$upload_path."app"))
            {
                $objScan = scandir("./".$upload_path."app");
                if(sizeof($objScan) > 2)
                {
                	foreach ($objScan as $value) 
                	{
                		// echo "ncrypt_pdf =".strpos($value, "ncrypt_pdf");
//                 		echo "<br />";
//                 		echo "df =".strpos($value, "df");
//                 		echo "<br />";
//                 		echo "cid =".strpos($value, $cid);
//                 		echo "<br />";
                		
                		if(strpos($value, "ncrypt_pdf") == 1 || strpos($value, "df") == 1 || strpos($value, $cid) == 0)
                		{
                			unlink("./".$upload_path."app/".$value);
                		}
                		
                	}
                }
            }
			
			$pdf_file = $_FILES["file_upload"]["tmp_name"];
			$output_file = getcwd()."/".$upload_path."tmp/pdf-%04d.pdf";
			//$string_command = '/usr/local/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sPDFPassword='.PDF_PASSWORD.' -sOutputFile='.$output_file.' '.$pdf_file;
			//$string_command = '/usr/local/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile='.$output_file.' '.$pdf_file;
			//$string_command = 'gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile='.$output_file.' '.$pdf_file;
            $string_command = '/usr/local/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile='.$output_file.' '.$pdf_file;
            
               
            exec($string_command);
            //return "";
			if(self::AES_Encryption(getcwd()."/".$upload_path, $secret_key, $iv))
			{
				deleteDir($tmp_upload_path);
				$objAES = new CryptographyAES();
				
				$destination = "./".$upload_path."app/pdf.pdf";
				
    			if($objAES->Encrypt($pdf_file, $destination, $secret_key, $iv))
    			{
    				if($is_license == 1)
    				{
    					unlink($pdf_file);
    				}
    				else{
    					rename($pdf_file, "./".$upload_path."app/".$cid.".pdf");
    					chmod("./".$upload_path."app/".$cid.".pdf",0644);
    				}
    				//rename($pdf_file, "./".$upload_path."app/".$cid.".pdf");
    			}
			}
		/*
			//Start upload file
			$upload_path = $upload_base_path.'file';
			$file_name = $_FILES["file_upload"]["name"];
			$file_type = substr(strrchr($file_name, "."), 0);
			
			$new_file_name = $cid.$file_type;
			$old_file = "./".$upload_path."/".$new_file_name;

			if(!is_blank($upload_path) && strlen($upload_path) > 8){
				deleteDir($upload_path."/zip_tmp");
				$all_files = glob($upload_path."/*.*");
				if(is_var_array($all_files)){
					foreach ($all_files as $filename) {
						unlink($filename);
					}
				}
			}

			if($use_digital_gen == '1'){
				if($file_type==".pdf"){
					$result_file_upload = upload_file($this,"file_upload",$upload_path,$new_file_name,CONST_ALLOW_FILE_TYPE_DEFAULT,CONST_ALLOW_FILE_SIZE_DEFAULT,'','');
				}else{
					
					$result_file_upload = upload_file_and_generate($this,"file_upload",$upload_path,$new_file_name,CONST_ALLOW_FILE_TYPE_FOR_DIGITAL_GEN,CONST_ALLOW_FILE_SIZE_FOR_DIGITAL_GEN,'','');
				}
			}else{
				$result_file_upload = upload_file($this,"file_upload",$upload_path,$new_file_name,CONST_ALLOW_FILE_TYPE_DEFAULT,CONST_ALLOW_FILE_SIZE_DEFAULT,'','');
			}
			$data["file_upload"] = $new_file_name;
			if ( !is_blank(get_array_value($result_file_upload,"error_msg","")) )
			{
				// echo $result_file_upload["error_msg"];
				$this->log_error('Admin : Product copy', 'Save magazine copy fail => Upload file error : '.$result_file_upload["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_file_upload,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["copy_item_detail"] = $data;
				$this->form($product_main_url, $parent_aid);
				return"";
			}	
		*/
		}		

		if( !is_blank(get_array_value($_FILES,"file_upload_app","")) && !is_blank(get_array_value($_FILES["file_upload_app"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'app';
			$file_name = $_FILES["file_upload_app"]["name"];
			$file_type = substr(strrchr($file_name, "."), 0);
			
			$new_file_name = $file_name;

			if(!is_blank($upload_path) && strlen($upload_path) > 8){
				$all_files = glob($upload_path."/*.*");
				if(is_var_array($all_files)){
					foreach ($all_files as $filename) {
						unlink($filename);
					}
				}
			}

			/*************** Create dummy zip file *************************/
			/*
			$folder_name = $barcode."html";
			$file_full = $upload_base_path."app/".$folder_name.".zip";
			create_empty_folder_to_zip($file_full, $folder_name);

			$folder_name = $barcode."resource";
			$file_full = $upload_base_path."app/".$folder_name.".zip";
			create_empty_folder_to_zip($file_full, $folder_name);
			*/
			/*************************************************************/

			$result_file_upload = upload_file($this,"file_upload_app",$upload_path,$new_file_name,'zip',CONST_ALLOW_FILE_SIZE_DEFAULT,'','');
			if ( !is_blank(get_array_value($result_file_upload,"error_msg","")) )
			{
				// echo $result_file_upload["error_msg"];
				$this->log_error('Admin : Product copy', 'Save magazine copy fail => Upload file app error : '.$result_file_upload["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_file_upload,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["copy_item_detail"] = $data;
				$this->form($product_main_url, $parent_aid);
				return"";
			}	
		}		

		if( !is_blank(get_array_value($_FILES,"file_upload_app_pdf","")) && !is_blank(get_array_value($_FILES["file_upload_app_pdf"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'app';
			$file_name = $_FILES["file_upload_app_pdf"]["name"];
			$file_type = substr(strrchr($file_name, "."), 0);
			
			$new_file_name = $file_name;
			$name_new_file = $barcode."pdf".$file_type;
			//rename( $new_file_name, $name_new_file);

			//rename ($new_file_name , $name_new_file);

			if(!is_blank($upload_path) && strlen($upload_path) > 8){
				$all_files = glob($upload_path."/*.*");
				if(is_var_array($all_files)){
					foreach ($all_files as $filename) {
						unlink($filename);
					}
				}
			}

			/*************** Create dummy zip file *************************/
			/*
			$folder_name = $barcode."html";
			$file_full = $upload_base_path."app/".$folder_name.".zip";
			create_empty_folder_to_zip($file_full, $folder_name);

			$folder_name = $barcode."resource";
			$file_full = $upload_base_path."app/".$folder_name.".zip";
			create_empty_folder_to_zip($file_full, $folder_name);
			*/
			/*************************************************************/
			
			
			$result_file_upload = upload_file($this,"file_upload_app_pdf",$upload_path,$name_new_file,'pdf',CONST_ALLOW_FILE_SIZE_DEFAULT,'','');
			
			$name_new_file_zip = $upload_base_path."app/".$barcode."pdf.zip";
			create_empty_file_to_zip($name_new_file_zip,$name_new_file,$upload_base_path);
						

			if ( !is_blank(get_array_value($result_file_upload,"error_msg","")) )
			{
				// echo $result_file_upload["error_msg"];
				$this->log_error('Admin : Product copy', 'Save magazine copy fail => Upload file app error : '.$result_file_upload["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_file_upload,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["copy_item_detail"] = $data;
				$this->form($product_main_url, $parent_aid);
				return"";
			}	
		}				

		if($command == "_insert"){
			$data["user_owner_aid"] = $user_owner_aid;
			
			$aid = $this->main->insert_record($data);
			if($aid){
				$this->load->model($this->magazine_model,'magazine');
				$this->magazine->update_parent($parent_aid);
				if($type == "2"){
					$this->after_update($parent_aid, $aid);
				}
				$this->log_status('Backend : Insert magazine copy', '[barcode='.$barcode.'] of ['.$title.'] just added into database.', $data);
				$this->log_product_save($product_type_aid, $parent_aid, $title, "U", "Magazine copy [barcode = $barcode] has been added", $data, "copy-add");
				
				//cron_job
				$this->update_all_view_save();
				//end cron_job

				redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/copy/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert magazine copy', 'Command insert_record() fail. Can not insert [barcode='.$barcode.'] of ['.$title.']', $data);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["copy_item_detail"] = $data;
				$this->form($product_main_url, $parent_aid);
				return "";
			}
						
		}else if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->load->model($this->magazine_model,'magazine');
				$this->magazine->update_parent($parent_aid);
				if($type == "2"){
					$this->after_update($parent_aid, $aid);
				}
				$this->data["message"] = set_message_success('Data has been saved.');
				$this->log_status('Admin : Update magazine copy',  '[barcode='.$barcode.'] of ['.$title.'] has been updated.', $data);
				$this->log_product_save($product_type_aid, $parent_aid, $title, "U", "Magazine copy [barcode = $barcode] has been updated", $data, "copy-update");
				
				//cron_job
				$this->update_all_view_save();
				//end cron_job
				
				if($save_option){
					redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/copy/add');
				}else{
					redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/copy/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update magazine copy', 'Command update_record() fail. Can not update [barcode='.$barcode.'] of ['.$title.']', $data);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["copy_item_detail"] = $data;
				$this->form($product_main_url, $parent_aid);
				return "";
			}
			
		}else{
			$this->log_error('Backend : Magazine copy', 'Command not found.', $command);
			redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'copy/status/'.md5('no-command'));
			return "";
		}
	}
		
	function ajax_set_value($sid=""){
		@define("thisAction",'ajax_set_value');
		if(!is_staff_or_higher()){
			$msg = set_message_error('Error occurred. Permission denied.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$aid = $this->input->get_post('aid_selected');
		$f_name = $this->input->get_post('f_name');
		$f_value = $this->input->get_post('f_value');
		if(is_blank($aid) || is_blank($f_name)){
			$msg = set_message_error('Error occurred. Data is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		// echo "f_name = $f_name , f_value = $f_value <BR>";

		$user_owner_aid = getUserOwnerAid($this);
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this magazine.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$parent_aid = get_array_value($objResult, "parent_aid", "");
		
		$parent_detail = $this->check_exits_magazine($parent_aid, false);
		$title = get_array_value($parent_detail,"title","");
		
		$this_obj_info = $title.'[barcode = '.trim(get_array_value($objResult, "barcode", "N/A").'] [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "barcode", $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->load->model($this->magazine_model,'magazine');
			$this->magazine->update_parent($parent_aid);

			$this->log_status('Backend : Magazine copy', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Magazine copy', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Error occurred. Can not save data.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}

	function ajax_delete_one(){
		@define("thisAction",'ajax_delete_one');
		if(!is_owner_admin_or_higher()){
			$msg = set_message_error('Error occurred. Permission denied.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

		$aid = $this->input->post('aid_selected');
		if(is_blank($aid)){
			$msg = set_message_error('Error occurred. Aid is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		$user_owner_aid = getUserOwnerAid($this);
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this magazine copy.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$parent_aid = get_array_value($objResult, "parent_aid", "");
		
		$parent_detail = $this->check_exits_magazine($parent_aid, false);
		$title = get_array_value($parent_detail,"title","");
		
		$this_obj_info = $title.'[barcode = '.trim(get_array_value($objResult, "barcode", "N/A").'] [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "barcode", $aid);

		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->delete_records();
		
		$upload_path = get_array_value($objResult,"upload_path","");
		deleteDir($upload_path);

		
		if ($rs){
			$this->load->model($this->shelf_model,"shelf");
			$this->shelf->set_where(array("product_type_aid"=>$this->data["this_product_type_aid"], "copy_aid"=>$aid));
			$rs = $this->shelf->delete_records();
			
			$this->load->model($this->magazine_model,'magazine');
			$this->magazine->update_parent($parent_aid);

			
			//cron_job
			$this->update_all_view_save();
			//end cron_job
			
			$this->log_status('Backend : Delete magazine copy', $this_obj_info.' has been deleted.', $objResult);
			$this->log_product_save($this->data["this_product_type_aid"], $parent_aid, $title, "U", "Magazine copy [barcode = $this_obj_title] has been deleted", $objResult, "copy-delete");
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete magazine', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Error occurred. Can not delete data.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_get_main_list($sid){
		$parent_aid = $this->input->get_post('parent_aid');
		$parent_detail = $this->check_exits_magazine($parent_aid, false);
		$title = get_array_value($parent_detail,"title","");
		$product_type_main_code = get_array_value($parent_detail,"product_type_main_code","");
		$product_type_sub_code = get_array_value($parent_detail,"product_type_sub_code","");
		$product_type_minor_aid = get_array_value($parent_detail,"product_type_minor_aid","");

		$product_main_aid = $this->input->get_post('product_main_aid');
		$product_main_result = $this->check_exits_product_main_by_aid($product_main_aid, false);
		$product_main_url = get_array_value($product_main_result,"url","");

		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));		
		$this->main->set_where(array("parent_aid"=>$parent_aid));
		$search_post_word = $this->input->get_post('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->input->get_post('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
						
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->main->set_where_in($data_where);
		
		$created_date_from = $this->input->get_post('created_date_from');
		$created_date_to = $this->input->get_post('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->main->set_where($this->main->get_table_name().'.created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->main->set_where($this->main->get_table_name().'.created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}		
		
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->input->get_post('page_selected');
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_PER_PAGE;
		$optional = $this->get_pagination_info($optional);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));			
		
		$search_order_by = $this->input->get_post('search_order_by');
		$order_by_option = $this->get_order_by_info($search_order_by,'barcode ASC');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list, $product_main_url);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);

		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Barcode',"field_order"=>'barcode',"col_show"=>'name_action',"title_class"=>'w80 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Copy type',"field_order"=>'type_minor',"col_show"=>'type_minor_action',"title_class"=>'w50 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Copy title',"field_order"=>'copy_title',"col_show"=>'copy_title',"title_class"=>'w150 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Possession',"field_order"=>'possession',"col_show"=>'possession_name',"title_class"=>'w50 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Is license?',"field_order"=>'is_license',"col_show"=>'is_license_name',"title_class"=>'w50 a-center',"result_class"=>'a-left');

		if(is_general_admin_or_higher()){
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Upload Path',"field_order"=>'upload_path',"col_show"=>'upload_path',"title_class"=>'w350 a-center',"result_class"=>'a-left');
		}

		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 a-center',"result_class"=>'a-center');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'a-center',"result_class"=>'a-left');		
		
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
	
	function manage_column_detail($result_list, $product_main_url="0"){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$parent_aid = get_array_value($item,"parent_aid","0");
			$item["name_action"] = '<a href="'.site_url('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/copy/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"barcode","-").'</a>';
			
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inctive\' this magazine." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"barcode","")).'</strong>\', \'admin/product/magazine-copy\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this magazine." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"barcode","")).'</strong>\', \'admin/product/magazine-copy\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$type_minor = get_array_value($item,"type_minor","0");
			if($type_minor == 1){
				$item["type_minor_action"] = 'E-Book';
			}elseif ($type_minor == 2) {
				$item["type_minor_action"] = 'E-Magazine';
			}elseif ($type_minor == 3) {
				$item["type_minor_action"] = 'Book';
			}elseif ($type_minor == 4) {
				$item["type_minor_action"] = 'Magazine';
			}elseif ($type_minor == 5) {
				$item["type_minor_action"] = 'Knowledge Resources';
			}elseif ($type_minor == 6) {
				$item["type_minor_action"] = 'Book+Media';
			}elseif ($type_minor == 7) {
				$item["type_minor_action"] = 'Media';
			}elseif ($type_minor == 8) {
				$item["type_minor_action"] = 'Information';
			}else{
				$item["type_minor_action"] = 'Multimedia';
			}
			// $type = get_array_value();

			
			$item["action"] = '';
			$product_main_field_aid = get_array_value($item,"product_main_field_aid","0");
			if($product_main_field_aid <= 0 && (CONST_ALLOW_DELETE_PRODUCT != "0" || is_root_admin_or_higher())){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this magazine." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/product/magazine-copy\', \'<strong>'.removeAllQuote(get_array_value($item,"barcode","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
			}
			
			$result[] = $item;
		}
		
		return $result;
	}
	
	function status($product_main_url="", $parent_aid="", $type="")	{
		switch($type)
		{
			case md5('success') : 
				$this->data["message"] = set_message_success('Data has been saved.');
				$this->data["js_code"] = '';
				break;
			case md5('no-command') : 
				$this->data["message"] = set_message_error('Command is unclear. Please try again.');
				$this->data["js_code"] = '';
				break;
			case md5('no-magazine-copy') : 
				$this->data["message"] = set_message_error('Error occurred. Can not find this magazine copy.');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->show($product_main_url, $parent_aid);
	}
	
	function isMagazineBarcodeExits($code, $aid){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("barcode"=>$code));
		if(is_number_no_zero($aid)){
			$this->main->set_where_not_equal(array("aid"=>$aid));
		}
		$total = $this->main->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}
	
	function isMagazineCidExits($cid){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("cid"=>$cid));
		$total = $this->main->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}
	
	function after_update($parent_aid="", $copy_aid=""){
		if(CONST_HAS_TRANSACTION == "1"){
			$this->load->model($this->user_section_model,"user_section");
			$user_section_result = $this->user_section->load_records_array(false,"aid","");
			if(is_var_array($user_section_result)){
				foreach ($user_section_result as $user_section) {
					$obj = array();
					$user_section_aid = get_array_value($user_section,"aid","0");
					$obj["parent_aid"] = $parent_aid;
					$obj["copy_aid"] = $copy_aid;
					$obj["user_section_aid"] = $user_section_aid;
					$obj["product_type_aid"] = $this->data["this_product_type_aid"];
					$obj["user_owner_aid"] = getUserOwnerAid($this);
					$obj["rental_period"] = $this->input->get_post($user_section_aid.'_rental_period');
					$obj["rental_fee"] = $this->input->get_post($user_section_aid.'_rental_fee');
					$obj["rental_fee_point"] = $this->input->get_post($user_section_aid.'_rental_fee_point');
					$obj["rental_fine_fee"] = $this->input->get_post($user_section_aid.'_rental_fine_fee');
					$obj["renew_time"] = $this->input->get_post($user_section_aid.'_renew_time');
					$obj["renew_period"] = $this->input->get_post($user_section_aid.'_renew_period');
					$this->load->model($this->magazine_ref_user_section_model,"magazine_ref_user_section");
					$this->magazine_ref_user_section->insert_or_update($obj);
				}
			}
		}
	}
	
	function ajax_get_popup_list($sid){
		// $parent_aid = $this->input->get_post('parent_aid');
		// $parent_detail = $this->check_exits_book($parent_aid, false);
		// $title = get_array_value($parent_detail,"title","");
		// $product_type_main_code = get_array_value($parent_detail,"product_type_main_code","");
		// $product_type_sub_code = get_array_value($parent_detail,"product_type_sub_code","");
		// $product_type_minor_aid = get_array_value($parent_detail,"product_type_minor_aid","");

		$user_aid = $this->input->get_post('user_aid');

		$product_main_aid = $this->input->get_post('product_main_aid');
		$product_main_result = $this->check_exits_product_main_by_aid($product_main_aid, false);
		$product_main_url = get_array_value($product_main_result,"url","");

		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		// $this->main->set_where(array("is_ebook"=>"0" , "status"=>"1" , "parent.status"=>"1" , "user_owner_aid"=>getUserOwnerAid($this)));		
		$this->main->set_where(array("is_ebook"=>"0" , "user_owner_aid"=>getUserOwnerAid($this)));		

		$search_post_word_product = $this->input->get_post('search_post_word_product');
		$data_search["search_post_word_product"] = $search_post_word_product;
		$search_in_product = $this->input->get_post('search_in_product');
		
		// echo "search_in_product : ".$search_in_product;
		if(!is_blank($search_post_word_product) && is_var_array($search_in_product))
		foreach($search_in_product as $item){
			$data_where[$item] = $search_post_word_product;
			$data_search["search_in_product"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
								
		$search_record_per_page_product = $this->getDataFromInput('search_record_per_page_product', 8);
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->getDataFromInput('page_selected_product');
		$optional["record_per_page"] = $search_record_per_page_product;
		$optional = $this->get_pagination_info($optional);
		$data_search["page_selected_product"] = get_array_value($optional,"page_selected","");
		$data_search["search_record_per_page_product"] = get_array_value($optional,"search_record_per_page", 8);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page", 8));			
		
		$search_order_by_product = $this->getDataFromInput('search_order_by_product');
		$data_search["search_order_by_product"] = $search_order_by_product;
		$order_by_option = $this->get_order_by_info($search_order_by_product,'barcode asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_popup_detail($result_list, $product_main_url, $user_aid);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);

		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Barcode',"field_order"=>'barcode',"col_show"=>'name_action',"title_class"=>'w80 a-center',"result_class"=>'a-left');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'*parent_title',"col_show"=>'parent_title',"title_class"=>'w150 a-center',"result_class"=>'a-left');
		
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
	
	function manage_column_popup_detail($result_list, $product_main_url="0", $user_aid=""){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$parent_aid = get_array_value($item,"parent_aid","0");
			$copy_barcode = get_array_value($item,"barcode","");
			$product_type_aid = get_array_value($item,"product_type_aid","0");
			$item["name_action"] = '<a class="button" onclick="addProductToTransactionByUser(\''.$user_aid.'\', \''.$product_type_aid.'\', \''.$copy_barcode.'\');close_model_box(\'dialog_box\')">'.get_array_value($item,"barcode","-").'</a>';
			
			$result[] = $item;
		}
		
		return $result;
	}
	
}

?>