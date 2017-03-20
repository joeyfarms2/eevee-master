<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Test_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "backend";
		define('thisAdminTabMenu','test');
		define("thisAdminSubMenu",'');

		$this->test_model = "Test_model";
		// for_root_admin();
	}
	
	function index(){
		return"";
	}

	function phpinfo() {
		phpinfo();
		//echo get_image("", "", "http://pttepelibrary.e-bookstudio.com/uploads/pttep/magazine/4/000365/cover_image/000365-cover.jpg");
	}

	function test_time_ago(){
		$strDate = "2014-10-06 11:59:35";
		echo "Date = $strDate<BR />";
		$today = date("YmdHis");
		echo "today = $today <BR />";
		echo "result :: ".get_pretty_date($strDate);
	}
	
	function test_send_mail(){
			$this->lang->load('mail');
	
			// $subject = $this->lang->line('mail_subject_new_user_activate');
			// $body = $this->lang->line('mail_content_new_user_activate');
			
			$subject = $this->lang->line('mail_subject_new_user_generate');
			$body = $this->lang->line('mail_content_new_user_generate');
			
			// $subject = $this->lang->line('mail_subject_new_user_activate');
			// $body = $this->lang->line('mail_content_new_user_activate');
			
			// $subject = $this->lang->line('mail_subject_reset_password');
			// $body = $this->lang->line('mail_content_reset_password');
			
			// $subject = $this->lang->line('mail_subject_meeting_invite_new');
			// $body = $this->lang->line('mail_content_meeting_invite_new');
			
			//$subject = $this->lang->line('mail_subject_reserve_fail');
			//$body = $this->lang->line('mail_content_reserve_fail');
			
			$to_emails = "yaowaluk_tarn@bookdose.com,yaowaluk@bookdose.com";
	
			// $body = eregi_replace("[\]",'',$body);
			// $body = str_replace("{doc_type}", "&nbsp;", $body);
			// $body = str_replace("{date_start}", "&nbsp;" , $body);
			// $body = str_replace("{date_full}", "&nbsp;", $body);
			// $body = str_replace("{title}", "&nbsp;", $body);
			// $body = str_replace("{description}", "&nbsp;", $body);
			// $body = str_replace("{attendee_all}", "&nbsp;", $body);
			// $body = str_replace("{agenda_list}", "&nbsp;", $body);
			
			echo "Subject : $subject <HR>";
			echo "$body <BR>";
			// return "";
			
			$this->load->library('email');
			$config = $this->get_init_email_config();
			if(is_var_array($config)){ 
				$this->email->initialize($config); 
				$this->email->set_newline("\r\n");
			}
			
			// Send message
			$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
			$this->email->to($to_emails);
			$this->email->bcc('');

			$this->email->subject($subject);
			$this->email->message($body);
			echo $this->email->send();
			//echo $this->email->print_debugger();	
			$debug_msg = $this->email->print_debugger();
			echo $debug_msg;
			$this->log_debug('Test : Send test email.', '[subject = '.$subject.'] [to_emails = '.$to_emails.'] --- '.$debug_msg);
			exit;
	}
	
	function test_simple_mail()
	{
	 		$to      = 'yaowaluk@bookdose.com';
	  		$subject = 'the subject';
	  		$message = 'hello';
	  		$headers = 'From: yaowaluk_tarn@bookdose.com';

	  		//$chack = mail($to, $subject, $message, $headers);
	  		$check = mail($to, $subject, $message , $headers);

	  		if($check){
	   			echo $check;
	   			echo 'yes';
	  		}else{
	   			echo $check;
	   			echo 'no';
	  		}
	 }

	function test_string(){
		echo "Test string : <BR />";
		echo "1. is_blank('') = " . is_blank('') ."<BR />"; 
		echo "1. is_blank(' ') = " . is_blank(' ') ."<BR />"; 
		echo "1. is_blank(0) = " . is_blank(0) ."<BR />"; 
		echo "1. is_blank('0') = " . is_blank('0') ."<BR />"; 
		echo "1. is_blank(1) = " . is_blank(1) ."<BR />"; 
		echo "1. is_blank('1') = " . is_blank('1') ."<BR />"; 
		echo "1. is_blank('true') = " . is_blank('true') ."<BR />"; 
		echo "1. is_blank(true) = " . is_blank(true) ."<BR />"; 
		echo "1. is_blank('false') = " . is_blank('false') ."<BR />"; 
		echo "1. is_blank(false) = " . is_blank(false) ."<BR />"; 
		echo "1. is_blank('NULL') = " . is_blank('NULL') ."<BR />"; 
		echo "1. is_blank(NULL) = " . is_blank(NULL) ."<BR />"; 
		echo "<HR />";
		echo "1. is_number('') = " . is_number('') ."<BR />"; 
		echo "1. is_number(' ') = " . is_number(' ') ."<BR />"; 
		echo "1. is_number(0) = " . is_number(0) ."<BR />"; 
		echo "1. is_number('0') = " . is_number('0') ."<BR />"; 
		echo "1. is_number(1) = " . is_number(1) ."<BR />"; 
		echo "1. is_number('1') = " . is_number('1') ."<BR />"; 
		echo "1. is_number('true') = " . is_number('true') ."<BR />"; 
		echo "1. is_number(true) = " . is_number(true) ."<BR />"; 
		echo "1. is_number('false') = " . is_number('false') ."<BR />"; 
		echo "1. is_number(false) = " . is_number(false) ."<BR />"; 
		echo "1. is_number('NULL') = " . is_number('NULL') ."<BR />"; 
		echo "1. is_number(NULL) = " . is_number(NULL) ."<BR />"; 
		echo "<HR />";
		echo "1. is_number_no_zero('') = " . is_number_no_zero('') ."<BR />"; 
		echo "1. is_number_no_zero(' ') = " . is_number_no_zero(' ') ."<BR />"; 
		echo "1. is_number_no_zero(0) = " . is_number_no_zero(0) ."<BR />"; 
		echo "1. is_number_no_zero('0') = " . is_number_no_zero('0') ."<BR />"; 
		echo "1. is_number_no_zero(1) = " . is_number_no_zero(1) ."<BR />"; 
		echo "1. is_number_no_zero('1') = " . is_number_no_zero('1') ."<BR />"; 
		echo "1. is_number_no_zero('true') = " . is_number_no_zero('true') ."<BR />"; 
		echo "1. is_number_no_zero(true) = " . is_number_no_zero(true) ."<BR />"; 
		echo "1. is_number_no_zero('false') = " . is_number_no_zero('false') ."<BR />"; 
		echo "1. is_number_no_zero(false) = " . is_number_no_zero(false) ."<BR />"; 
		echo "1. is_number_no_zero('NULL') = " . is_number_no_zero('NULL') ."<BR />"; 
		echo "1. is_number_no_zero(NULL) = " . is_number_no_zero(NULL) ."<BR />"; 
	}

	function test_replace(){
		/*
		$keyword = "-ming    oui=-tisa -  --------------  -- -- -       -    thida ";
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/-/', ' -', $keyword);
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/\s\s+/', ' ', $keyword);
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/-\s/', '-', $keyword);
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/--+/', '-', $keyword);
		echo "keyword = '$keyword' <BR />";
		$keyword = trim($keyword);
		echo "keyword = '$keyword'";
		*/

		$keyword = "2014-05-20 14:57 2014/05/20 14:57";
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/\//', '', $keyword);
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/-/', '', $keyword);
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/ /', '', $keyword);
		echo "keyword = '$keyword' <BR />";
		$keyword = preg_replace('/:/', '', $keyword);
		$keyword = trim($keyword);
		echo "keyword = '$keyword'";
	}
	
	function test_image(){
		echo "get_image('') : ".get_image('')."<BR />";
		echo "get_image('','small') : ".get_image('','small')."<BR />";
		echo "get_image('','','off') : ".get_image('','','off')."<BR />";
		echo "get_image('','','blank') : ".get_image('','','blank')."<BR />";
	}
	
	function created_folder_for_biblio(){
		$this->load->model("Biblio_model","biblio");
		$this->biblio->set_order_by("aid asc");
		$result = $this->biblio->load_records(false);
		if(is_var_array($result)){
			foreach ($result as $key => $value) {
				$path = './'.get_array_value($value,"upload_path","")."cover_image";
				echo "path = $path <BR>";
				create_directories($path);
			}
		}
	}

	function created_zip_for_book(){
		$this->load->model("Book_copy_model","book_copy");
		$this->book_copy->set_order_by("aid asc");
		$result = $this->book_copy->load_records(false);
		if(is_var_array($result)){
			foreach ($result as $key => $value) {
				$barcode = get_array_value($value,"barcode","");
				$path = './'.get_array_value($value,"upload_path","").'app';

				$folder_name = $barcode."html";
				$file_full = $path."/".$folder_name.".zip";
				if(is_file($file_full)){
					// echo "File already exits.";
				}else{
					echo "path = $path <BR>";
					echo "folder_name = $folder_name<BR>";
					echo "file = $file_full<BR>";
					echo "Need to add file.";
					create_directories($path);
					create_empty_folder_to_zip($file_full, $folder_name);
					echo "<HR>";
				}

				$folder_name = $barcode."resource";
				$file_full = $path."/".$folder_name.".zip";
				if(is_file($file_full)){
					// echo "File already exits.";
				}else{
					echo "path = $path <BR>";
					echo "folder_name = $folder_name<BR>";
					echo "file = $file_full<BR>";
					echo "Need to add file.";
					create_directories($path);
					create_empty_folder_to_zip($file_full, $folder_name);
					echo "<HR>";
				}
				// create_directories($path);
			}
		}
	}

	function test_pdf_to_image(){
		echo "Start <BR>"; 
		// echo 'file pdf : <a href="'.site_url($file_pdf).'">'.$file_pdf.'</a>';

		// $file_pdf = "uploads/test/atm-test.pdf[1]";
		// $imagick = new Imagick();
		// $imagick->readImage($file_pdf);
		// $imagick->writeImage('uploads/test/output.jpg');
		// echo 'Success';

		exec("'gs' '-dNOPAUSE' '-sDEVICE=jpeg' '-dUseCIEColor' '-dTextAlphaBits=4' '-dGraphicsAlphaBits=4' '-o$exportPath' '-r$res' '-dJPEGQ=$quality' '$pdf'",$output);
	}

	function test_views(){
		$this->load->model($this->view_all_products,"v");
		$result = $this->v->load_records(true);
		print_r($result);
	}

	function test_paypal() {
		$res = "HTTP/1.1 200 OK
						Date: Wed, 27 Aug 2014 09:00:24 GMT
						Server: Apache
						X-Frame-Options: SAMEORIGIN
						Set-Cookie: c9MWDuvPtT9GIMyPc3jwol1VSlO=0lZ5IeQ9Jopzj6z4wdYUs2gSaKoiEDqTlOutJfFG6X-fs82cWAoqUJCmnM9Bu2JGGVP7_g9XPgZsr7FG_UtxfzMTAU9veNCxGNEMmHX8aptlHPH7ycCZTyRBdamGwOxqEHVexuDx4dd7LmmTyclwKA5S7XC9l5vQEGgabfbUHWp2IXGyfzVTLGZW4gwwl45jhCOhA48wWHrF6SQRjYg1cBcNuu693QDKfY5EZj3y1n8RqjdG_5BziNk1RgZDuL5fOJq6Mc9tY3VZfyu-qrkL3DO8JgbH7BpeooS3TKv3_Yh-IIBjyufoflUBjXY-u-KrQzGehacuP1NIHAracWSd6FUB1zO42lJNnVr6FX1Lrmeg3-O-b5OTTkq0rsvChVU6k994FSZPGLOiGGXB52gbiwviAdMVX1C3bua5qOCeG9ACNxaNFpiybdzO8YW; domain=.paypal.com; path=/; Secure; HttpOnly
						Set-Cookie: cookie_check=yes; expires=Sat, 24-Aug-2024 09:00:24 GMT; domain=.paypal.com; path=/; Secure; HttpOnly
						Set-Cookie: navcmd=_notify-validate; domain=.paypal.com; path=/; Secure; HttpOnly
						Set-Cookie: navlns=0.0; expires=Fri, 26-Aug-2016 09:00:24 GMT; domain=.paypal.com; path=/; Secure; HttpOnly
						Set-Cookie: Apache=10.72.109.11.1409130024562405; path=/; expires=Fri, 19-Aug-44 09:00:24 GMT
						Connection: close
						Set-Cookie: X-PP-SILOVER=name%3DSANDBOX3.WEB.1%26silo_version%3D880%26app%3Dslingshot%26TIME%3D681508179; domain=.paypal.com; path=/; Secure; HttpOnly
						Set-Cookie: X-PP-SILOVER=; Expires=Thu, 01 Jan 1970 00:00:01 GMT
						Set-Cookie: Apache=10.72.128.11.1409130024548635; path=/; expires=Fri, 19-Aug-44 09:00:24 GMT
						Vary: Accept-Encoding
						Strict-Transport-Security: max-age=14400
						Transfer-Encoding: chunked
						Content-Type: text/html; charset=UTF-8

						VERIFIED";

		// Split response headers and payload
		// list($headers, $res) = explode("\r\n\r\n", $res, 2);

		// Response from SANDBOX
		$tmp = explode("\r\n\r\n", $res);
		echo "<pre>";
		print_r($tmp);
		echo end($tmp);
		echo "</pre>";
		exit;

		$res = str_replace("\n\r", "\n", $res);
        list($header1, $header2, $res) = explode("\n\n", $res, 3);

         echo "<pre>";
         print_r($header1);
         print_r($header2);

         // print_r($headers);
         print_r($res);
         echo "</pre>";
	}

	function test_convert_img_2_base64() {
		$full_path = 'uploads/eastwater/news/2014/11/eP1uD5sWHXFh/eP1uD5sWHXFh-thumb.jpg';
		if (is_file($full_path)) {
			$file_content = file_get_contents($full_path);
        	$base64_string = base64_encode($file_content);
        	// echo $base64_string;
        	// exit;
		}

		
		$imgdata = base64_decode($base64_string);
		$f = finfo_open();
		$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
		$file_ext = '';

		switch ($mime_type) {
			default:
			case 'image/jpeg':
			case 'image/pjpeg':
				$file_ext = '.jpg';
				break;
			case 'image/bmp':
			case 'image/x-windows-bmp':
				$file_ext = '.bmp';
				break;
			case 'image/gif':
				$file_ext = '.gif';
				break;
			case 'image/png':
			case 'image/x-png':
				$file_ext = '.png';
				break;
			case 'image/tiff':
				$file_ext = '.tiff';
				break;
		}
		$output_file = 'uploads/eastwater/news/2014/11/eP1uD5sWHXFh/test-app'.$file_ext;
		$ifp = fopen($output_file, "wb"); 
		fwrite($ifp, base64_decode($base64_string)); 
		fclose($ifp); 
		exit;

		// $output_file = 'uploads/eastwater/news/2014/11/eP1uD5sWHXFh/test.jpg';
		// $ifp = fopen($output_file, "wb"); 
		// fwrite($ifp, base64_decode($base64_string)); 
		// fclose($ifp); 

		// return $output_file; 

		exit;
	}

	function test_ci_insert(){
		echo "Hello";
		$this->load->model($this->test_model,"test");

		$data = array();
		$data["name"] = "test";
		$data["status"] = "1";

		$this->test->insert_record($data);

		// print_r($result);
	}
	
	function test_ci_load(){
		echo "Hello<BR>";
		$this->load->model($this->test_model,"test");
		$tmp = array();
		$tmp["status"] = "1";
		$this->test->set_where($tmp);
		// $this->test->set_where(array("status"=>"1"));
		$result = $this->test->load_records();
		if(is_var_array($result)){
			foreach ($result as $key => $value) {
				echo ">>".$value["name"]."<BR>";
			}
		}else{
			echo "No record.";
		}

		// print_r($result);
	}
	
	function test_ci_fullload(){
		echo $test["1111"];
		echo "Hello<BR>";
		$this->load->model($this->product_main_model,"product_main");
		$tmp = array();
		$tmp["status"] = "1";
		$result = $this->product_main->load_records(true);
		if(is_var_array($result)){
			foreach ($result as $key => $item) {
				echo "product main = ".$item["name"]." , product type = ".get_array_value($item,"ffffff","")." , status = ".get_array_value($item,"status_name","")."<BR>";
			}
		}else{
			echo "No record.";
		}

		// print_r($result);
	}
	
}

?>