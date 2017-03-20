<?php

class Initcontroller extends CI_Controller {
	var $data = "";
	var $user_login_info = "";
	var $user_owner_info = "";
	var $default_theme_front = "";
	var $default_theme_login = "";
	var $default_theme_admin = "";
	
	function __construct()
    {
		parent::__construct();
		@define("thisController",strtolower(get_class($this)));
		$this->precheck();
		$this_url = $this->uri->uri_string();
		if (strpos($this_url, 'webservice') === false && strpos($this_url, 'success') === false && strpos($this_url, 'fail') === false && strpos($this_url, 'status') === false && strpos($this_url, 'save') === false && strpos($this_url, 'ajax') === false && strpos($this_url, '.jpg') === false && strpos($this_url, '.png') === false && strpos($this_url, '.gif') === false && strpos($this_url, '/images/') === false){
			if(thisController != 'login_controller' && thisController != 'registration_controller' && thisController != 'forgot_controller' && thisController != 'activation_controller' && thisController != 'change_password_controller' && thisController != 'welcome_controller' && thisController != 'redeem_front_controller'){
				$this->session->set_userdata('lasted_url',$this_url);
				// echo "Lasted URL : ".$this->session->userdata('lasted_url');
			}
		}else{
			// $this->session->set_userdata('lasted_url','');
		}
		
		$this->banner_model = "Banner_model";

		$this->log_model = "Log_model";

		$this->setting_config_model = "Setting_config_model";

		$this->user_model = "User_model";
		$this->user_role_model = "User_role_model";
		$this->user_section_model = "User_section_model";
		$this->user_department_model = "User_department_model";
		$this->user_owner_model = "User_owner_model";
		$this->user_setting_profile_model = "User_setting_profile_model";
		
		$this->product_main_model = "Product_main_model";
		$this->product_type_model = "Product_type_model";
		$this->product_main_field_model = "Product_main_field_model";
		$this->product_category_model = "Product_category_model";
		$this->publisher_model = "Publisher_model";

		$this->reserve_product_model = "Reserve_product_model";
		
		$this->event_model = "Event_model";
		$this->event_category_model = "Event_category_model";

		$this->comment_model = "Comment_model";

		$this->shelf_model = "Shelf_model";
		$this->shelf_history_model = "Shelf_history_model";
		$this->shelf_vdo_model = "Shelf_vdo_model";
		$this->shelf_vdo_history_model = "Shelf_vdo_history_model";
		$this->copy_download_model = "Copy_download_model";
		
		$this->search_history_model = "Search_history_model";
		$this->search_history_backup_model = "Search_history_backup_model";

		$this->magazine_main_model = "Magazine_main_model";

		$this->book_model = "Book_model";
		$this->magazine_model = "Magazine_model";
		$this->vdo_model = "Vdo_model";
		$this->others_model = "Others_model";

		$this->book_copy_model = "Book_copy_model";
		$this->magazine_copy_model = "Magazine_copy_model";
		$this->vdo_copy_model = "Vdo_copy_model";
		$this->others_copy_model = "Others_copy_model";
		
		$this->book_field_model = "Book_field_model";
		$this->magazine_field_model = "Magazine_field_model";
		$this->vdo_field_model = "Vdo_field_model";
		$this->others_field_model = "Others_field_model";
		
		$this->book_history_model = "Book_history_model";
		$this->magazine_history_model = "Magazine_history_model";
		$this->vdo_history_model = "Vdo_history_model";
		$this->others_history_model = "Others_history_model";
		
		$this->book_ref_product_category_model = "Book_ref_product_category_model";
		$this->magazine_ref_product_category_model = "Magazine_ref_product_category_model";
		$this->vdo_ref_product_category_model = "Vdo_ref_product_category_model";
		$this->others_ref_product_category_model = "Others_ref_product_category_model";
		
		if(CONST_HAS_TRANSACTION == "1"){
			$this->book_ref_user_section_model = "Book_ref_user_section_model";
			$this->magazine_ref_user_section_model = "Magazine_ref_user_section_model";
			$this->vdo_ref_user_section_model = "Vdo_ref_user_section_model";
			$this->others_ref_user_section_model = "Others_ref_user_section_model";
		}
		
		$this->book_tag_model = "Book_tag_model";
		$this->magazine_tag_model = "Magazine_tag_model";
		$this->vdo_tag_model = "Vdo_tag_model";
		$this->others_tag_model = "Others_tag_model";		

		$this->view_all_products = "View_all_products_model";
		$this->view_all_products_with_detail = "View_all_products_with_detail_model";
		$this->view_all_product_copies = "View_all_product_copies_model";
		$this->view_all_product_copies_with_detail = "View_all_product_copies_with_detail_model";
		

		$this->view_all_download_history = "View_all_download_history_model";
		$this->view_all_reserve = "View_all_reserve_model";
		
		//Delete vdo from bookshelf 
		// $this->load->model($this->shelf_model,"shelf");
		// $this->shelf->delete_expired_shelf();
		
		//Delete expired e-book from bookshelf 
		$this->remove_expire_ebook_from_shelf();
		
		//Delete expired search history 
		// $this->remove_expire_search_history();
		
		//get user information
		$user_login_info = "";
		$user_owner_info = "";
		$default_theme_front = "";
		$default_theme_login = "";
		$default_theme_admin = "";
		if(!is_blank(getSessionUserAid())){
			$this->load->model($this->user_model,"user");
			$this->user->set_where(array("aid"=>getSessionUserAid(), "status"=>"1"));
			$user_login_info = $this->user->load_record(true);
			if(!is_var_array($user_login_info)){
				$this->logout();
				redirect('login/status/'.md5('permission'));
				return"";
			}
			
			$user_owner_aid = get_array_value($user_login_info,"user_owner_aid","");
			$user_owner_info = $this->check_user_owner_aid($user_owner_aid,false,"");
			$default_theme_front = get_array_value($user_owner_info,"default_theme_front","");
			$default_theme_admin = get_array_value($user_owner_info,"default_theme_admin","");
			$this->session->set_userdata('userOwnerAidSession', $user_owner_aid);
		
		}
		
		if(!is_blank($default_theme_front)){
			$this->default_theme_front = 'theme'.$default_theme_front;
			$this->default_theme_login = 'theme'.$default_theme_front;
		}else{
			$this->default_theme_front = 'theme'.THEME_FRONT;
			$this->default_theme_login = 'theme'.THEME_LOGIN;
		}

		if(!is_blank($default_theme_admin)){
			$this->default_theme_admin = 'theme'.$default_theme_admin;
		}else{
			$this->default_theme_admin = 'theme'.THEME_ADMIN;
		}
		
		$this->data["user_owner_info"] = $user_owner_info;
		$this->data["user_login_info"] = $user_login_info;
		$this->user_owner_info = $user_owner_info;
		$this->user_login_info = $user_login_info;
		
		//get common language
		// echo "language : ".$this->session->userdata('language');
		// if(is_blank($this->session->userdata('language'))){
			$this->session->set_userdata('language', $this->config->item('language'));
		// }
		
		//load init Data
		$this->load->model($this->product_main_model,"product_main");
		$this->data["master_product_main"] = $this->product_main->load_product_mains();
		
		$this->load->model($this->product_type_model,"product_type");
		$this->data["master_product_type"] = $this->product_type->load_active_product_types();
		
		$this->load->model($this->publisher_model,"publisher");
		$this->publisher->set_order_by("name ASC");
		$this->data["master_publisher"] = $this->publisher->load_publishers();
		
		$this->load->model($this->user_role_model,"user_role");
		$this->data["master_user_role"] = $this->user_role->load_master_user_role();

		$this->load->model($this->banner_model,"banner");
		$this->banner->set_where(array("status" => "1"));
		$this->banner->set_order_by("weight ASC");
		$this->data["banner_result"] = $this->banner->load_records(true);
		//print_r($this->data["banner_result"]);


		$this->data["master_payment_type"] = explode(":",CONST_MASTER_PAYMENT_TYPE);

		if($this->agent->is_mobile()){
			$this->data["browser_type"] = 'mobile';
			$this->data["browser_name"] = strtolower($this->agent->browser());
		}else{
			$this->data["browser_type"] = 'web';
			$this->data["browser_name"] = strtolower($this->agent->browser());
		}

		if(CONST_HAS_BASKET == '1'){
			$payment_type_master = array("point", "paysbuy", "paypal");
			$payment_type = $this->session->userdata('paymentTypeSession');
			if(is_blank($payment_type) || !in_array($payment_type, $payment_type_master)){
				$payment_type = "paysbuy";
				$this->session->set_userdata('paymentTypeSession',$payment_type);
			}
			$this->data["payment_type"] = $payment_type;
			}
		
		if(thisController == "home_controller" || thisController == "login_controller" || thisController == "forgot_controller" || thisController == "registration_controller"){
			$this->load_init_data();
		}

		if(true){
			//load reserve noti
			header('Content-Type: text/html; charset=utf-8');
			$this->load->model($this->reserve_product_model,"reserve_product");
			$tmp = array();
			$tmp["status"][] = "1";
			$tmp["status"][] = "2";
			$this->reserve_product->set_where_in($tmp);
			$this->reserve_product->set_group_by("product_type_aid");
			$this->reserve_product->set_group_by("copy_aid");
			$this->reserve_product->set_group_by("status");
			$reserve_result = $this->reserve_product->load_records(true);
			// echo "<br>sql : ".$this->db->last_query();
			// print_r($reserve_result);
			$reserve_noti_status_1 = "";
			$reserve_noti_status_2_today = "";
			$reserve_noti_status_2_overdue = "";
			if(is_var_array($reserve_result)){
				foreach ($reserve_result as $item) {
					$barcode = get_array_value($item,"barcode","");
					$product_type_aid = get_array_value($item,"product_type_aid","");
					$copy_aid = get_array_value($item,"copy_aid","");
					$title = get_array_value($item,"title","");
					$status = get_array_value($item,"status","");
					$expiration_date = get_datetime_pattern("Y-m-d", get_array_value($item,"expiration_date",""), "");
					// echo "barcode = $barcode<BR>";
					$today = date('Y-m-d');
					if(!is_blank($barcode)){
						if($status == 2 && $expiration_date == $today){
							$reserve_noti_status_2_today[$barcode]["title"] = $title;
							$reserve_noti_status_2_today[$barcode]["barcode"] = $barcode;
							$reserve_noti_status_2_today[$barcode]["product_type_aid"] = $product_type_aid;
							$reserve_noti_status_2_today[$barcode]["copy_aid"] = $copy_aid;
							$reserve_noti_status_2_today[$barcode]["expiration_date"] = $expiration_date;
						}
						if($status == 2 && $expiration_date < $today){
							$reserve_noti_status_2_overdue[$barcode]["title"] = $title;
							$reserve_noti_status_2_overdue[$barcode]["barcode"] = $barcode;
							$reserve_noti_status_2_overdue[$barcode]["product_type_aid"] = $product_type_aid;
							$reserve_noti_status_2_overdue[$barcode]["copy_aid"] = $copy_aid;
							$reserve_noti_status_2_overdue[$barcode]["expiration_date"] = $expiration_date;
						}
						$reserve_noti_status_1[$barcode]["title"] = $title;
						$reserve_noti_status_1[$barcode]["barcode"] = $barcode;
						$reserve_noti_status_1[$barcode]["product_type_aid"] = $product_type_aid;
						$reserve_noti_status_1[$barcode]["copy_aid"] = $copy_aid;
						$reserve_noti_status_1[$barcode]["status"] = $status;
					}
				}

				if(is_var_array($reserve_noti_status_1)){
					foreach ($reserve_noti_status_1 as $item) {
						$barcode = get_array_value($item,"barcode","");
						$product_type_aid = get_array_value($item,"product_type_aid","");
						$copy_aid = get_array_value($item,"copy_aid","");
						$title = get_array_value($item,"title","");
						$status = get_array_value($item,"status","");
						if($status == 2){
							unset($reserve_noti_status_1[$barcode]);
						}
						$model = $this->get_product_model($product_type_aid);
						$model_name = get_array_value($model,"product_copy_model","");
						$this->db->flush_cache();
						$this->db->_reset_select();
						$this->load->model($model_name, $model_name);
						$tmp = array();
						$tmp['aid'] = $copy_aid;
						$this->{$model_name}->set_where($tmp);
						$copy_result = $this->{$model_name}->load_record(false);
						$shelf_status = get_array_value($copy_result,"shelf_status","");
						if($shelf_status != "1"){
							unset($reserve_noti_status_1[$barcode]);
						}
					}
				}
			}
			$this->data["reserve_noti_status_1"] = $reserve_noti_status_1;
			$this->data["reserve_noti_status_2_today"] = $reserve_noti_status_2_today;
			$this->data["reserve_noti_status_2_overdue"] = $reserve_noti_status_2_overdue;
		}

		// $this->log_debug("lasted_url",$this->session->userdata('lasted_url'));
	}
	
	function precheck(){
		$this_url = $this->uri->uri_string();
		if (strpos($this_url, 'webservice') === false){
			if(CONST_WEB_STATUS == 2){
				if(thisController != 'welcome_controller' && thisController != 'login_controller'){
					for_login();
				}
			}else if(CONST_WEB_STATUS == 3){
				if(thisController != 'welcome_controller' && thisController != 'login_controller'){
					if(!is_general_admin_or_higher()){
						redirect('welcome');
						return"";
					}
				}
			}else if(CONST_WEB_STATUS == 4){
				if(thisController != 'welcome_controller' && thisController != 'login_controller'){
					if(!is_root_admin_or_higher()){
						redirect('welcome');
						return"";
					}
				}
			}
		}
	}

	function log_save($type="", $title="", $description="", $data_arr="",$page="", $controller="", $action="", $flag="", $owner_user_aid="", $owner_detail=""){
		$data="";
		$data["type"] = $type;
		$data["title"] = $title;
		$data["description"] = $description;
		$data["data"] = serialize($data_arr);
		$data["page"] = (!is_blank($page)) ? $page : uri_string();
		$data["controller"] = (!is_blank($controller)) ? $controller : @thisController;
		$data["action"] = (!is_blank($action)) ? $action : @thisAction;
		$data["owner_user_aid"] = (!is_blank($owner_user_aid)) ? $owner_user_aid : getUserOwnerAid($this);
		$data["owner_detail"] = (!is_blank($owner_detail)) ? $owner_detail : getUserOwnerDetailForLog($this);
		$data["flag"] = $flag;
		$data["ip"] = $this->input->ip_address();
		if($this->agent->is_mobile()){
			$data["browser"] = $this->agent->mobile().'/'.$this->agent->browser().' '.$this->agent->version();
		}else{
			$data["browser"] = $this->agent->platform().'/'.$this->agent->browser().' '.$this->agent->version();
		}
		$data["browser_detail"] = $this->agent->agent_string();
		
		$myLog =& get_instance();
		$myLog->load->model($this->log_model,"mylog");
		$myLog->mylog->insert_record($data);
	}
	function log_error($title="", $description="", $data_arr="",$page="", $controller="", $action="", $flag="", $owner_user_aid="", $owner_detail=""){
		$this->log_save("error", $title, $description, $data_arr, $page, $controller, $action, $flag, $owner_user_aid, $owner_detail);
	}
	function log_warning($title="", $description="", $data_arr="",$page="", $controller="", $action="", $flag="", $owner_user_aid="", $owner_detail=""){
		$this->log_save("warning", $title, $description, $data_arr, $page, $controller, $action, $flag, $owner_user_aid, $owner_detail);
	}
	function log_notice($title="", $description="", $data_arr="",$page="", $controller="", $action="", $flag="", $owner_user_aid="", $owner_detail=""){
		$this->log_save("notice", $title, $description, $data_arr, $page, $controller, $action, $flag, $owner_user_aid, $owner_detail);
	}
	function log_debug($title="", $description="", $data_arr="",$page="", $controller="", $action="", $flag="", $owner_user_aid="", $owner_detail=""){
		$this->log_save("debug", $title, $description, $data_arr, $page, $controller, $action, $flag, $owner_user_aid, $owner_detail);
	}
	function log_status($title="", $description="", $data_arr="",$page="", $controller="", $action="", $flag="", $owner_user_aid="", $owner_detail=""){
		$this->log_save("status", $title, $description, $data_arr, $page, $controller, $action, $flag, $owner_user_aid, $owner_detail);
	}

	function get_pagination_info($optional){
		$record_per_page = get_array_value($optional,"record_per_page",CONST_DEFAULT_RECORD_PER_PAGE);
		$search_record_per_page = $this->input->get_post('search_record_per_page');
		if(is_blank($search_record_per_page)){
			$search_record_per_page = $record_per_page;
		}
		$total_page = 1;
		
		$total_record = get_array_value($optional,"total_record","0");
		if($total_record > 0){
			$total_page = ceil($total_record/$search_record_per_page);
		}
		
		$page_selected = get_array_value($optional,"page_selected","0");
		// echo $page_selected;
		if($page_selected == '-1') $page_selected = $total_page;
		else{
			if(is_blank($page_selected)  || $page_selected <= 0) $page_selected = 1;
			if($page_selected > $total_page) $page_selected = $total_page;
		}
		$optional["start_record"] = ($page_selected-1)*$search_record_per_page;
		$optional["search_record_per_page"] = $search_record_per_page;
		$optional["page_selected"] = $page_selected;
		$optional["total_page"] = $total_page;
		return $optional;
	}
	
	function get_order_by_info($search_order_by="",$init_order_by="aid desc"){
		if(is_blank($search_order_by)){
			$search_order_by = $init_order_by;
		}
		list($order_by, $order_by_option) = preg_split("/ /", $search_order_by, 2);
		$order_by_txt = '';
		$orders = explode(",", $order_by);
		foreach($orders as $item){
			$item = trim($item);
			if(!is_blank($order_by_txt)) $order_by_txt .= ', ';
			$order_by_txt .= $item." ".$order_by_option;
		}
		$result["order_by_txt"] = $order_by_txt;
		$result["sorting"] = array("order_by"=>$order_by , "order_by_option"=>$order_by_option);
		return $result;
	}

	function get_init_email_config(){
		$config = "";
		// $config['useragent']           = "CodeIgniter";
  //       $config['mailpath']            = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
		$config['protocol'] = 'smtp';
		//$config['smtp_host'] = '27.254.144.144';
		$config['smtp_host'] = 'www.nialib.com';
		$config['smtp_user'] = 'noreply@nialib.com';
		$config['smtp_pass'] = 'cIv1ny5Xi';
		$config['smtp_port'] = '25';
		$config['smtp_timeout'] = '25';
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['newline']  = "\r\n";
		$config['wordwrap'] = TRUE;
		return $config;
	}

	function ajax_change_language($lang='') {
		switch(strtolower($lang)){
			case "en" : $this->session->set_userdata('language', 'english'); break;
			case "th" : $this->session->set_userdata('language', 'thai'); break;
			default : $this->session->set_userdata('language', $this->config->item('language'));
		}
	}
	
	function check_user_owner_aid($user_owner_aid="",$redirect=false,$url=""){
		$chk = true;
		$user_owner_info = "";
		if(is_blank($user_owner_aid)){
			$chk = false;
		}else{
			$this->load->model($this->user_owner_model,"user_owner");
			$this->user_owner->set_where(array("aid"=>$user_owner_aid, "status"=>"1"));
			$user_owner_info = $this->user_owner->load_record(true);
			if(!is_var_array($user_owner_info)){
				$chk = false;
			}
			
			// $expiration_date = get_array_value($user_owner_info,"expiration_date","");
			// if(!is_blank($expiration_date) && $expiration_date < $today){
				// redirect('home/status/'.md5('user-owner-aid-expire'));
				// return"";
			// }
			
		}
		
		if(!$chk){
			if($redirect){
				if(!is_blank($url)){
					redirect($url);
					return"";
				}else{
					redirect('home/status/'.md5('user-owner-aid-not-found'));
					return"";
				}
			}else{
				return "";
			}
		}else{
			return $user_owner_info;
		}
	}
		
	function get_user_owner_aid_by_input(){
		$user_owner_aid = $this->input->get_post('user_owner_aid');
		if(!is_blank($user_owner_aid)){
			return $user_owner_aid;
		}else{
			return getUserOwnerAid($this);
		}
	}
		
	function load_init_data(){
	}
	
	function remove_expire_ebook_from_shelf(){
		$this->load->model($this->shelf_model,"shelf");
		// $tmp = array();
		// $tmp["expiration_date"] = date('Y-m-d');
		// $this->shelf->set_where($this->shelf->get_table_name().'.expiration_date NOT NULL', NULL);
		// $this->shelf->set_where($this->shelf->get_table_name().'.expiration_date <=', date('Y-m-d'));
		$this->shelf->delete_expired_shelf();
		/*
		// @define("thisAction","remove_expire_ebook_from_shelf");
		// echo "Start. <BR />";
		$this->load->model($this->shelf_model,"shelf");
		$data = array("","NULL","0000-00-00");
		$this->shelf->set_not_like_by_field("shelf.expiration_date",$data,"none");
		$this->shelf->set_where("shelf.expiration_date <= ", date('Y-m-d'));
		$result_list = $this->shelf->load_records(true);
		// echo "SQL : ".$this->db->last_query()."<BR />";
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$issue_aid = get_array_value($item,"issue_aid","");
				$user_aid = get_array_value($item,"user_aid","");
				// $status = get_array_value($item,"status","");
				// $is_book_license = get_array_value($item,"is_book_license","");
				$expiration_date = get_array_value($item,"expiration_date","");
				$full_issue_name = get_array_value($item,"full_issue_name","");
				$userinfo = get_user_info($item);
				// echo "expiration_date = ".$expiration_date."<BR />";
				$txt = 'Auto delete expiration ebook from shelf. Remove '.$full_issue_name.' [issue_aid = '.$issue_aid.'] from '.$userinfo.'\'s bookshelf [user_aid = '.$user_aid.'] ';
				// echo $txt."<BR />";
				$data = array();
				$data["user_aid"] = $user_aid;
				$data["issue_aid"] = $issue_aid;
				$data["action"] = "ad";
				$data["status"] = "1";
				$this->db->flush_cache();
				$this->load->model($this->shelf_history_model,"shelf_history");
				$this->shelf_history->insert_record($data);
				// echo "SQL : ".$this->db->last_query()."<BR />";
				
				$data = array();
				$data["user_aid"] = $user_aid;
				$data["issue_aid"] = $issue_aid;
				$this->db->flush_cache();
				$this->load->model($this->shelf,"shelf");
				$this->shelf->set_where($data);
				$this->shelf->delete_records();
				// echo "SQL : ".$this->db->last_query()."<BR />";
				
				$this->db->flush_cache();
				$this->log_status('Web service', $txt);
				// echo "SQL : ".$this->db->last_query()."<BR />";
			}
		}
		*/
	}
	
	function remove_expire_search_history(){
		// @define("thisAction","remove_expire_search_history");
		// echo "Start. <BR />";
		$this->load->model($this->search_history_backup_model,"search_history_backup");
		$result_list = $this->search_history_backup->backup_search_history();
		// echo "SQL : ".$this->db->last_query()."<BR />";
	}
	
	function getDataFromInput($field_name="", $init_value="", $input_type="get_post", $session_obj=""){
		/** input_type : get_post , get , post , session : default is 'get_post' **/
		switch ($input_type) {
			case 'get':
				$value = $this->input->get($field_name);
				break;
			case 'post':
				$value = $this->input->post($field_name);
				break;
			case 'session':
				$value = get_array_value($session_obj,$field_name,"");
				break;
			default:
				$value = $this->input->get_post($field_name);
				break;
		}
		// echo "value = $value";
		if(is_blank($value)){
			$value = $init_value;
		}
		return $value;
	}

	function ajax_clear_session($sid=""){
		$session_name = $this->input->get_post("session_name");
		if(!is_blank($session_name)){
			$this->session->set_userdata($session_name,'');			
		}
	}

	function logout(){
		@define("thisAction",'logout');
		$this->load->model($this->model,'user');
		$user_pass = $this->user->set_logout();
		
		$this->log_status('Logout', getUserLoginEmail($this).'['.getSessionUserAid().'] just log out.');
		$this->session->set_userdata('userSession','');
		$this->session->sess_destroy();
		
		delete_cookie('cuser'.CONST_HASH_KEY);
		delete_cookie('cpass'.CONST_HASH_KEY);
		delete_cookie('cowner'.CONST_HASH_KEY);
		delete_cookie('chash'.CONST_HASH_KEY);
		
		redirect('home');
	}

	function get_product_model($product_type_aid=""){
		switch ($product_type_aid) {
			case '1':
			case 'book':
				$product_model = $this->book_model;
				$product_copy_model = $this->book_copy_model;
				$product_field_model = $this->book_field_model;
				$product_history_model = $this->book_history_model;
				$product_ref_product_category_model = $this->book_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
					$product_ref_user_section_model = $this->book_ref_user_section_model;
				}
				$product_tag_model = $this->book_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
					$model["product_ref_user_section_model"] = $product_ref_user_section_model;
				}
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			case '2':
			case 'magazine':
				$product_model = $this->magazine_model;
				$product_copy_model = $this->magazine_copy_model;
				$product_field_model = $this->magazine_field_model;
				$product_history_model = $this->magazine_history_model;
				$product_ref_product_category_model = $this->magazine_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
					$product_ref_user_section_model = $this->magazine_ref_user_section_model;
				}
				$product_tag_model = $this->magazine_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
					$model["product_ref_user_section_model"] = $product_ref_user_section_model;
				}
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			case '3':
			case 'vdo':
				$product_model = $this->vdo_model;
				$product_copy_model = $this->vdo_copy_model;
				$product_field_model = $this->vdo_field_model;
				$product_history_model = $this->vdo_history_model;
				$product_ref_product_category_model = $this->vdo_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
					$product_ref_user_section_model = $this->vdo_ref_user_section_model;
				}
				$product_tag_model = $this->vdo_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
					$model["product_ref_user_section_model"] = $product_ref_user_section_model;
				}
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			case '4':
			case 'others':
				$product_model = $this->others_model;
				$product_copy_model = $this->others_copy_model;
				$product_field_model = $this->others_field_model;
				$product_history_model = $this->others_history_model;
				$product_ref_product_category_model = $this->others_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
				$product_ref_user_section_model = $this->others_ref_user_section_model;
				}
				$product_tag_model = $this->others_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				if(CONST_HAS_TRANSACTION == "1"){
					$model["product_ref_user_section_model"] = $product_ref_user_section_model;
				}
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			default:
				return "";
				break;
		}
	}

	function get_product_copy_detail($cond){
		$product_type_aid = get_array_value($cond,"product_type_aid","");
		$copy_aid = get_array_value($cond,"copy_aid","");
		$status = get_array_value($cond,"status","");

		// echo "product_type_aid = $product_type_aid <BR>";

		$model = $this->get_product_model($product_type_aid);
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_copy_name, $model_copy_name);
		$tmp = array();
		if(!is_blank($copy_aid)){
			$tmp["aid"] = $copy_aid;
		}
		if(!is_blank($status)){
			$tmp["status"] = $status;
		}
		$this->{$model_copy_name}->set_where($tmp);
		$copy_detail = $this->{$model_copy_name}->load_record(false);
		// echo "<br>sql : ".$this->db->last_query();
		return $copy_detail;
	}
	
	function update_reward_point($product_result, $user_aid){
		$product_type_aid = get_array_value($product_result,"product_type_aid","0");
		$parent_aid = get_array_value($product_result,"aid","0");
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["parent_aid"] = $parent_aid;
		$tmp["user_aid"] = $user_aid;
		$this->load->model($this->copy_download_model,"copy_download");
		$this->copy_download->set_where($tmp);
		$result = $this->copy_download->load_records(false);
		if(!is_var_array($result)){
			$point = get_array_value($product_result,"reward_point","0");
			$this->load->model($this->user_model,"user");
			$this->user->add_point_remain($user_aid, $point);
			return $point;
		}
		return 0;
	}

	function check_redeem_code($redeem_code="" , $user_aid="", $type=""){
		$result = "";
		if(is_blank($redeem_code)){
			$result = array();
			$result["status"] = "error";
			$result["error_code"] = "blank";
			$result["msg"] = "Data is null.";
			return $result;
		}

		if($type != "point" && $type != "cart"){
			$result = array();
			$result["status"] = "error";
			$result["error_code"] = "blank";
			$result["msg"] = "Type is wrong.";
			return $result;
		}

		$this->load->model("Redeem_detail_model","redeem_detail");
		$this->redeem_detail->set_where(array("cid"=>$redeem_code));
		$redeem_result = $this->redeem_detail->load_record(true);
		if(!is_var_array($redeem_result)){
			$result = array();
			$result["status"] = "error";
			$result["error_code"] = "code-not-found";
			$result["msg"] = "Redeem code not found.";
			return $result;
		}

		$redeem_main_aid = get_array_value($redeem_result,"redeem_main_aid","");
		$redeem_date = get_array_value($redeem_result,"redeem_date","");
		$redeem_main_status = get_array_value($redeem_result,"redeem_main_status","");
		$redeem_main_title = get_array_value($redeem_result,"redeem_main_title","");
		$redeem_main_start_date = get_array_value($redeem_result,"redeem_main_start_date","");
		$redeem_main_expired_date = get_array_value($redeem_result,"redeem_main_expired_date","");
		$redeem_main_type = get_array_value($redeem_result,"redeem_main_type","");
		$redeem_main_value = get_array_value($redeem_result,"redeem_main_value","0");
		$redeem_main_promotion_set_aid = get_array_value($redeem_result,"redeem_main_promotion_set_aid","");
		$redeem_main_amount = get_array_value($redeem_result,"redeem_main_amount","");
		$redeem_main_limit_per_code = get_array_value($redeem_result,"redeem_main_limit_per_code","");
		$redeem_main_limit_per_user = get_array_value($redeem_result,"redeem_main_limit_per_user","");
		if($type == "point"){
			if($redeem_main_type != "point"){
				$result = array();
				$result["status"] = "error";
				$result["error_code"] = "not-point-code";
				$result["msg"] = "This code is not for point redeem.";
				return $result;
			}
		}else if($type == "cart"){
			if($redeem_main_type != "discount" && $redeem_main_type != "cash"){
				$result = array();
				$result["status"] = "error";
				$result["error_code"] = "not-cart-code";
				$result["msg"] = "This code is not for cart redeem.";
				return $result;
			}
		}


		if($redeem_main_status != '1'){
			$result = array();
			$result["status"] = "error";
			$result["error_code"] = "code-inactive";
			$result["msg"] = "Redeem code not found.";
			return $result;
		}

		$today_date = date('Y-m-d');
		// echo "today_date = $today_date, redeem_main_start_date = $redeem_main_start_date , redeem_main_expired_date = $redeem_main_expired_date<BR />";
		if(!is_blank($redeem_main_start_date) && $redeem_main_start_date > $today_date){
			$result = array();
			$result["status"] = "error";
			$result["error_code"] = "code-early";
			$result["msg"] = "Redeem code not found.";
			return $result;
		}

		if(!is_blank($redeem_main_expired_date) && $redeem_main_expired_date < $today_date){
			$result = array();
			$result["status"] = "error";
			$result["error_code"] = "code-expired";
			$result["msg"] = "Redeem code was expired.";
			return $result;
		}

		$this->load->model("Redeem_history_model","redeem_history");
		$this->redeem_history->set_where(array("redeem_main_aid"=>$redeem_main_aid, "redeem_detail_cid"=>$redeem_code));
		$redeem_history_result = $this->redeem_history->load_records(false);
		// print_r($redeem_history_result);

		if(is_var_array($redeem_history_result)){
			$used_total = count($redeem_history_result);
			// echo "used_total = $used_total , redeem_main_limit_per_code = $redeem_main_limit_per_code<BR />";
			if($used_total >= $redeem_main_limit_per_code){
				$result = array();
				$result["status"] = "error";
				$result["error_code"] = "code-run-out";
				$result["msg"] = "Redeem code reach maximun limitation.";
				return $result;
			}
		}

		$this->load->model("Redeem_history_model","redeem_history");
		$this->redeem_history->set_where(array("redeem_main_aid"=>$redeem_main_aid));
		$redeem_history_result = $this->redeem_history->load_records(false);
		// print_r($redeem_history_result);
		if(is_var_array($redeem_history_result)){
			$my_used_total = 0;
			foreach ($redeem_history_result as $item) {
				$tmp_user_aid = get_array_value($item,"user_aid","0");
				if($tmp_user_aid == getSessionUserAid()){
					$my_used_total++;
				}
			}
			// echo "my_used_total = $my_used_total , redeem_main_limit_per_user = $redeem_main_limit_per_user<BR />";
			if($redeem_main_limit_per_user > 0 && $my_used_total >= $redeem_main_limit_per_user){
			$result = array();
			$result["status"] = "error";
			$result["error_code"] = "code-run-out-for-user";
			$result["msg"] = "You alrady use this promotion.";
			return $result;
			}
		}

		$result = array();
		$result["status"] = "success";
		$result["msg"] = get_array_value($redeem_result,"redeem_main_title","You can use this code.");
		$result["redeem_result"] = $redeem_result;
		return $result;
	}
	function generateToken() {
		$token = hash('sha256', uniqid(rand(), true));
		// $this->session->set_userdata('_token', $token);
		$_SESSION['_token'] = $token;
		return $token;
	}

	function verifyToken($token="") {
		// if ($token !== $this->session->userdata('_token')) {
		if ($token !== $_SESSION['_token']) {
			// echo "token = $token , session = ".$this->session->userdata('_token');
			// echo "token = $token , session = ".$_SESSION['_token'];
			die("Invalid token");
		}
		return true;
	}

	function isAjaxRequest() {
		if( !(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ) {
			die('Invalid request');
		}
		return true;
	}



}

?>