<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Home_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		@define("thisFrontTabMenu",'home');
		@define("thisFrontSubMenu",'');
		@define("folderName",'home/');

		$this->data["mode"] = "front";

		$this->news_model = 'News_model';
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
		
		$this->load->model($this->news_main_model,"news_main");
		$this->data["master_news_main"] = $this->news_main->load_news_mains();
		
		$this->package_point_model = "Package_point_model";
		$this->contact_topic_model = "Contact_topic_model";
		$this->contact_msg_model = "Contact_msg_model";
		$this->log_access_product_model = "Log_access_product_model";
	}
	
	function index()
	{
		$this->home();
	}
	
	function home()
	{
		@define("thisAction","home");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'NewReleases';
		$this->data["view_the_content"] = $this->default_theme_front . '/home/home';

		$this->load->model($this->package_point_model,"package_point");
		$this->data["master_package_point"] = $this->package_point->load_package_points();

		$this->get_ads_by_category_aid(array(",3,"));

		//load set of home
		$master_product_main = $this->data["master_product_main"];
		$product_home_result = "";

		$user_section_aid = getUserLoginSectionAid($this);
		$this->load->model($this->product_category_ref_user_section_model,"ref_user_section");
		$this->ref_user_section->set_where(array("user_section_aid"=>$user_section_aid));
		$ref_user_section_all = $this->ref_user_section->load_records_array(false,"","product_category_aid");
		//print_r($ref_user_section_all);
		// $cat_arr = "";
		// $cat_arr[] = "";
		if(is_var_array($ref_user_section_all)){
			foreach ($ref_user_section_all as $cid) {
				if(!is_blank($cid)){
					$cat_arr[] = ",".$cid.",";
				}
			}
		}
		
		$product_main_list = array('4','3','6','7','5');
		if(is_var_array($product_main_list)){
			foreach($product_main_list as $product_main_aid){
				//echo "product_main_aid = ".$product_main_aid;

				$this->load->model($this->product_main_model,"product_main");
				$tmp = "";
				$tmp["aid"] = $product_main_aid;
				$tmp["status"] = "1";
				$this->product_main->set_where($tmp);
				$item = $this->product_main->load_record(true);
				//echo "<br>sql : ".$this->db->last_query();
				$product_type_aid = get_array_value($item,"product_type_aid","");
				$product_type_cid = get_array_value($item,"product_type_cid","");
				$model = $this->get_product_model($product_type_aid);
				//echo "product_type_aid = $product_type_aid , product_main_aid = $product_main_aid , model = ".get_array_value($model,"product_model")."<BR>";
				
				$this->db->flush_cache();
				$this->load->model($this->view_all_products_with_detail,"v_all_products");
				if(is_var_array($cat_arr)){
					$this->v_all_products->set_and_or_like_by_field("category", $cat_arr);
				}
				$this->v_all_products->set_where(array("product_type_aid"=>$product_type_aid, "product_main_aid"=>$product_main_aid, "status"=>'1', "is_home"=>'1'));
				
				
				$this->v_all_products->set_limit(0,6);
				$this->v_all_products->set_order_by("weight ASC, publish_date DESC");
				$result = $this->v_all_products->load_records(true);
				//echo "<br>sql : ".$this->db->last_query();
				$obj = array();
				$obj["product_main_id"] = $product_main_aid;
				$obj["product_main_name"] = get_array_value($item,"name","");
				$obj["product_main_url"] = get_array_value($item,"url","");
				$obj["product_type_aid"] = $product_type_aid;
				$obj["product_type_cid"] = $product_type_cid;
				$obj["result_list"] = $result;
				$product_home_result[] = $obj;
			}
		}

		$this->data["product_home_result"] = $product_home_result;

		$type = array('1','2');
		//load new
		$this->load->model($this->view_all_products_with_detail,"v_all_products");
		$this->v_all_products->set_where(array("status"=>'1'));
		$this->v_all_products->set_where_in(array("product_type_aid"=>$type));
		if(is_var_array($cat_arr)){
					$this->v_all_products->set_and_or_like_by_field("category", $cat_arr);
				}
		$this->v_all_products->set_order_by("total_download desc");
		$this->v_all_products->set_limit(0, 3);
		$new_list = $this->v_all_products->load_records(true);
		// if(is_var_array($new_list)){
		// 	print_r($new_list);
		// 	$rand_list = array_rand($new_list ,1);
			
		// 	$result = array();
		// 	foreach($rand_list as $item) {
		// 		$result[] = get_array_value($new_list,$item,"");
		// 	}
			$this->data["new_list"] = $new_list;
		//}

		//load recommended
		$this->load->model($this->view_all_products_with_detail,"v_all_products");
		$this->v_all_products->set_where(array("status"=>'1', "is_recommended"=>'1'));
		$this->v_all_products->set_where_in(array("product_type_aid"=>$type));
		if(is_var_array($cat_arr)){
			$this->v_all_products->set_and_or_like_by_field("category", $cat_arr);
		}
		$this->v_all_products->set_order_by("*RAND()");
		$this->v_all_products->set_limit(0, 3);
		$recommended_list = $this->v_all_products->load_records(true);
		$this->data["recommended_list"] = $recommended_list;

		if(CONST_HAS_NEWS != 0){
			$this->load->model($this->news_model,"news");
			//$this->load->model($this->view_most_comments_model,"most_comments");
			$tmp_highlight = $this->news->load_home_small('', '1' , 1, 2);
			$this->data['news_highlight_list'] = get_array_value($tmp_highlight, 'results', '');
			//$this->data['news_highlight_list'] = $this->news->load_highlight('1');
			//$this->data['news_recommended_list'] = $this->news->load_recommended('1');
			//$this->data['news_talk_of_the_town_list'] = $this->news->load_talk_of_the_town('1');
			
			
			$tmp_rs = $this->news->load_home_big('', '1' , 0, 1);
			$this->data['latest_news_result'] = get_array_value($tmp_rs, 'results', '');
			
			$this->load->model($this->news_category_model,"news_cat");
			$this->data['news_cat_result'] = $this->news_cat->load_news_categories();
		}

		$this->load->view($this->default_theme_front . '/tpl_home', $this->data);
	}
	
	function status($type="")
	{
		switch($type)
		{
			case md5('permission') : 
				$this->data["message"] = set_message_error("Session expired or unauthorized access!");
				break;
			case md5('product-not-found') : 
				$this->data["message"] = set_message_error("Error occured. Product not found.");
				break;
			case md5('product-expired') : 
				$this->data["message"] = set_message_error("Error occured. Product was expired.");
				break;
			case md5('product-main-not-found') : 
				$this->data["message"] = set_message_error('Error occured. Product main not found.');
				$this->data["js_code"] = '';
				break;
			case md5('product-type-not-found') : 
				$this->data["message"] = set_message_error('Error occured. Product type not found.');
				$this->data["js_code"] = '';
				break;
			case md5('publisher-not-found') : 
				$this->data["message"] = set_message_error("ไม่พบสำนักพิมพ์ที่เรียกดู");
				break;
			case md5('category-not-found') : 
				$this->data["message"] = set_message_error("ไม่พบหมวดหมู่เรียกดู");
				break;
			case md5('no-session') : 
				$this->data["message"] = set_message_error('Session time out. Please login again. <a href="'.site_url('login').'">'.$this->lang->line('user_login').'</a>');
				$this->data["js_code"] = '';
				break;
			case md5('need-logout') : 
				$this->data["message"] = set_message_error('You are still logging in system. Please logout before access this page. <a href="'.site_url('logout').'">'.$this->lang->line('user_logout').'</a>');
				$this->data["js_code"] = '';
				break;
			case md5('user-owner-aid-not-found') : 
				$this->data["message"] = set_message_error('Error occured.');
				$this->data["js_code"] = '';
				break;
			case md5('user-branch-aid-not-found') : 
				$this->data["message"] = set_message_error('Error occured.');
				$this->data["js_code"] = '';
				break;
			case md5('user-owner-aid-expire') : 
				$this->data["message"] = set_message_error('Error occured.');
				$this->data["js_code"] = '';
				break;
			case md5('send-contact-success') : 
				$this->data["message"] = set_message_success('We have now received your mail and will get back to you as soon as possible.');
				$this->data["js_code"] = '';
				break;
				
			default : 
				$this->data["message"] = set_message_error("เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่ หรือติดต่อผู้ดูแลระบบ");
				break;
		}
		$this->home();
	}
		
	function contact()
	{
		@define("thisAction","contact");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/home/contact';
		$this->data["page_name"] = "contact";
		$this->data["page_title"] = 'ContactUs';
		$this->data["message"] = "";
		$this->load->view($this->default_theme_front . '/tpl_full', $this->data);
	}
	
	function contact_form($this_topic_url="")
	{
		@define("thisAction","contact_form");
		$this->lang->load('contact');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/home/contact_form';
		$this->data["page_name"] = "contact_form";
		$this->data["page_title"] = $this->lang->line('contact_front_page_title');

		$this->get_ads_by_category_aid(array(",1,"));

		$this->load->model($this->contact_topic_model,"contact_topic");
		$this->data["master_contact_topic"] = $this->contact_topic->load_master(false);
		$this->data["this_topic_url"] = $this_topic_url;

		$data = '';
		$subject = '';
		$subject_mini='';
		if($this_topic_url == 'book-and-product-inquiry'){
			$type = $this->input->get_post('type');
			$parent_aid = $this->input->get_post('id');
			$barcode = $this->input->get_post('code');
			if($type == 'book'){
				$this->load->model($this->book_model,'main');
				$this->main->set_where(array("aid"=>$parent_aid));
				$parent_detail = $this->main->load_record(false);
				// $model = $this->get_product_model($type);
				// $model_name = get_array_value($model,"product_model","");
				// $this->db->flush_cache();
				// $this->db->_reset_select();
				// $this->load->model($model_name, $model_name);
				// $tmp = array();
				// $tmp["aid"] = $parent_aid;
				// $this->{$model_name}->set_where($tmp);
				// $parent_detail = $this->{$model_name}->load_record(false);
				// echo "<br>sql : ".$this->db->last_query();
				$this->load->model($this->book_copy_model,'book_copy');
				$this->book_copy->set_where(array("barcode"=>$barcode));
				$item_detail = $this->book_copy->load_record(false);
				//echo "<br>sql : ".$this->db->last_query();
				// $book_copy_model = $this->get_product_model('book_copy');
				// $book_copy_model_name = get_array_value($book_copy_model,"book_copy_model","");
				// // $this->db->flush_cache();
				// // $this->db->_reset_select();
				// $this->load->model($book_copy_model_name);
				// $this->book_copy_model->set_where(array("barcode"=>$barcode));
				// $item_detail = $this->book_copy_model->load_record(false);
				// //echo "<br>sql : ".$this->db->last_query();

					if(is_var_array($parent_detail)){

						if(is_var_array($item_detail)){
							
								$subject = 'Reserve ( '.get_array_value($item_detail,"barcode","").' ) '.get_array_value($parent_detail,"title","").' '.get_array_value($item_detail,"copy_title","");
								$data["subject"] = $subject;
							
						}else{
							if(get_array_value($parent_detail,"product_main_aid","") == '5'){

								$subject = 'Online Resource : '.get_array_value($parent_detail,"title","");
								$data["subject"] = $subject;

							}else if(get_array_value($parent_detail,"product_main_aid","") == '8'){

								$subject = 'Online Book : '.get_array_value($parent_detail,"title","");
								$data["subject"] = $subject;

							}
						}
					}
			}else if($type == 'magazine'){
				$this->load->model($this->magazine_model,'magazine');
				$this->magazine->set_where(array("aid"=>$parent_aid));
				$parent_detail = $this->magazine->load_record(false);
				
				$this->load->model($this->magazine_copy_model,'magazine_copy');
				$this->magazine_copy->set_where(array("barcode"=>$barcode));
				$item_detail = $this->magazine_copy->load_record(false);
				

					if(is_var_array($parent_detail)){

						if(is_var_array($item_detail)){
							$subject = 'Reserve ( '.get_array_value($item_detail,"barcode","").' ) '.get_array_value($parent_detail,"title","").' '.get_array_value($item_detail,"copy_title","");
							$data["subject"] = $subject;
						}
					}
			}else{
				$model = $this->get_product_model($type);
				$model_name = get_array_value($model,"product_model","");
				$this->db->flush_cache();
				$this->db->_reset_select();
				$this->load->model($model_name, $model_name);
				$tmp = array();
				$tmp["aid"] = $parent_aid;
				$this->{$model_name}->set_where($tmp);
				$parent_detail = $this->{$model_name}->load_record(false);
				//echo "<br>sql : ".$this->db->last_query();
				
					if(is_var_array($parent_detail)){
						$subject = 'Ask about '.get_array_value($parent_detail,"title","");
						$data["subject"] = $subject;
				
					}
			}
				
		}
		$this->data["item_detail"] = $data;

		$this->load->view($this->default_theme_front . '/tpl_login', $this->data);
	}
	
	function contact_save()
	{
		@define("thisAction","contact_save");
		
		$command = $this->input->get_post('command');

		$this->lang->load('mail');

		$first_name_th = $this->input->get_post('first_name_th');
		$last_name_th = $this->input->get_post('last_name_th');
		$contact_topic_aid = $this->input->get_post('contact_topic_aid');
		$email = $this->input->get_post('email');
		$contact_subject = $this->input->get_post('subject');
		$message = $this->input->get_post('message');

		$this->load->model($this->contact_topic_model,"contact_topic");
		$this->contact_topic->set_where(array("aid"=>$contact_topic_aid));
		$contact_topic_result = $this->contact_topic->load_record(false);
		$contact_topic_name = get_array_value($contact_topic_result,"name","N/A");
		$contact_topic_url = get_array_value($contact_topic_result,"url","N/A");
		$contact_topic_email = get_array_value($contact_topic_result,"email", MAIN_CONTACT_EMAIL);

		require_once('include/securimage/securimage.php');
		$securimage = new Securimage();
		$captcha = $this->input->get_post('captcha_code');
		if ($securimage->check($captcha) == false) {
			$data = '';
			$this->data["message"] = set_message_error('The characters you enter was wrong. Please try again.');
			$this->data["js_code"] = '$("#captcha_code").focus();';
			$this->data["command"] = $command;
			$this->data["item_detail"] = $data;
			$this->contact_form();
			return "";
		}

		$subject = $this->lang->line('mail_subject_contact_confirm_to_user');
		$body = $this->lang->line('mail_content_contact_confirm_to_user');

		$subject = str_replace("{name}", trim($contact_subject.' - '.$first_name_th.' '.$last_name_th) , $subject);

		$body = str_replace("{doc_type}", "&nbsp;" , $body);
		$body = str_replace("{name}", trim($first_name_th.' '.$last_name_th) , $body);
		$body = str_replace("{email}", $email, $body);
		$body = str_replace("{subject}", $contact_subject, $body);
		$body = str_replace("{message}", $message, $body);
		$body = str_replace("{topic_name}", $contact_topic_name, $body);

		// echo "body = $body";

		$data["first_name_th"] = $first_name_th;
		$data["last_name_th"] = $last_name_th;
		$data["contact_topic_aid"] = $contact_topic_aid;
		$data["contact_topic_name"] = $contact_topic_name;
		$data["email"] = $email;
		$data["subject"] = $contact_subject;
		$data["message"] = $message;

		$obj = "";
		$obj["contact_topic_aid"] = $contact_topic_aid;
		$obj["data"] = serialize($data);

		$this->load->model($this->contact_msg_model,"contact");
		$aid = $this->contact->insert_record($obj);
		$data["aid"] = $aid;

		$this->load->model($this->user_model,"user");

		$this->load->library('email');
		$config = $this->get_init_email_config();
		if(is_var_array($config)){ 
			$this->email->initialize($config); 
			$this->email->set_newline("\r\n");
		}


		
		$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
		$this->email->to($email);
		$this->email->bcc($contact_topic_email);

		$this->email->subject($subject);
		$this->email->message($body);
		//echo $this->email->print_debugger();
		$this->log_debug('Contact form email', '['.$email.'] '.$body);
		if(@$this->email->send()){
			$this->log_status('Contact form email', 'Email sent success.', $data);
			$this->user->set_trans_commit();
			redirect('home/status/'.md5('send-contact-success'));
			return "";
		}else{
			$this->log_status('Contact form email', 'Email sent fail.', $data);
			$this->data["message"] = set_message_error('Sorry, The system can not send email right now.<BR>Please try again later or contact administrator to solve the problem.');
			$this->data["js_code"] = '';
			$this->data["command"] = $command;
			$this->data["item_detail"] = $data;
			$this->contact_form($contact_topic_url);
			return "";
		}
	}
	
	function your_library()
	{
		@define("thisAction","your_library");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/' . folderName . '/your_library';
		$this->data["page_name"] = "your_library";
		$this->data["page_title"] = 'YourLibrary';
		$this->data["message"] = "";
		$this->load->view($this->default_theme_front . '/tpl_full', $this->data);
	}
	
	function all_categories()
	{
		@define("thisAction","all_categories");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/' . folderName . '/all_categories';
		$this->data["page_name"] = "your_library";
		$this->data["page_title"] = 'AllCategories';
		$this->data["message"] = "";

		//load all category
		$master_product_main = $this->data["master_product_main"];
		$result_by_product_main = "";
		if(is_var_array($master_product_main)){
			foreach($master_product_main as $item){
				$product_main_aid = get_array_value($item,"aid","0");
				$product_type_aid = get_array_value($item,"product_type_aid","0");
				$product_type_cid = get_array_value($item,"product_type_cid","none");
				$model = $this->get_product_model($product_type_aid);
				// echo "product_type_aid = $product_type_aid , product_main_aid = $product_main_aid , model = ".get_array_value($model,"product_model")."<BR>";
				
				$this->db->flush_cache();

				$this->load->model($this->product_category_model,"category");
				$this->category->set_where(array("product_main_aid"=>$product_main_aid, "status"=>'1'));
				$this->category->set_order_by("name ASC");
				$result = $this->category->load_records(true);

				$obj = array();
				$obj["product_main_id"] = $product_main_aid;
				$obj["product_main_name"] = get_array_value($item,"name","");
				$obj["product_main_url"] = get_array_value($item,"url","");
				$obj["product_type_aid"] = $product_type_aid;
				$obj["product_type_cid"] = $product_type_cid;
				$obj["result_list"] = $result;
				$result_by_product_main[] = $obj;
			}
		}
		$this->data["result_by_product_main"] = $result_by_product_main;
		$this->load->view($this->default_theme_front . '/tpl_full', $this->data);
	}

	function show_data_subscription(){
		$parent_id = $this->input->get_post('id');
		$product_type_aid = $this->input->get_post('type');

		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_name, $model_name);
		$tmp = array();
		$tmp['aid'] = $parent_id;
		// $tmp['status'] = '1';
		$this->{$model_name}->set_where($tmp);
		$result = $this->{$model_name}->load_record(true);
		// print_r($result);
		// exit(0);

		// echo "<br>sql : ".$this->db->last_query();
		if(!is_var_array($result)){
			redirect('home/status/'.md5('product-not-found'));
			return "";
		}

		$ext_source = get_array_value($result,"ext_source","");

		$publish_date = get_datetime_pattern('Y-m-d', get_array_value($result,"publish_date",""), date('Y-m-d'));
		$expired_date = get_datetime_pattern('Y-m-d', get_array_value($result,"expired_date",""), date('Y-m-d', time()+86400));
		$today = date('Y-m-d');
		if($publish_date <= $today && $expired_date > $today){

			if(is_blank($ext_source)){
				redirect('home/status/'.md5('product-not-found'));
				return "";
			}

			$data = array();
			$data["user_aid"] = getSessionUserAid();
			$data["copy_aid"] = '0';
			$data["parent_aid"] = $parent_id;
			$data["product_type_aid"] = "1";
			$data["status"] = '1';
			$this->load->model($this->log_access_product_model,"access");
			$this->access->insert_or_update($data);

			if (strpos($ext_source, '://') === false ){
				$ext_source = 'http://'.$ext_source;
			}

			redirect($ext_source);

		}else{
			redirect('home/status/'.md5('product-expired'));
			return "";
		}
	}
	function show_online_book(){
		$parent_id = $this->input->get_post('id');
		$product_type_aid = $this->input->get_post('type');

		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_name, $model_name);
		$tmp = array();
		$tmp['aid'] = $parent_id;
		// $tmp['status'] = '1';
		$this->{$model_name}->set_where($tmp);
		$result = $this->{$model_name}->load_record(true);
		// print_r($result);
		// exit(0);

		// echo "<br>sql : ".$this->db->last_query();
		if(!is_var_array($result)){
			redirect('home/status/'.md5('product-not-found'));
			return "";
		}

		$ext_source = get_array_value($result,"ext_source","");

		$publish_date = get_datetime_pattern('Y-m-d', get_array_value($result,"publish_date",""), date('Y-m-d'));
		$expired_date = get_datetime_pattern('Y-m-d', get_array_value($result,"expired_date",""), date('Y-m-d', time()+86400));
		$today = date('Y-m-d');
		if($publish_date <= $today && $expired_date > $today){

			if(is_blank($ext_source)){
				redirect('home/status/'.md5('product-not-found'));
				return "";
			}

			$data = array();
			$data["user_aid"] = getSessionUserAid();
			$data["copy_aid"] = '0';
			$data["parent_aid"] = $parent_id;
			$data["product_type_aid"] = "1";
			$data["status"] = '1';
			$this->load->model($this->log_access_product_model,"access");
			$this->access->insert_or_update($data);

			if (strpos($ext_source, '://') === false ){
				$ext_source = 'http://'.$ext_source;
			}

			redirect($ext_source);

		}else{
			redirect('home/status/'.md5('product-expired'));
			return "";
		}
	}
	function privacy_and_policy()
	{
		@define("thisAction","privacy_and_policy");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'Privacy and Data Protection Policy';
		$this->data["view_the_content"] = $this->default_theme_front . '/home/privacy_and_policy';
		$this->load->view($this->default_theme_front . '/tpl_full', $this->data);
	}
	
}

?>