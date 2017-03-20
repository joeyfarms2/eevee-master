<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Report_dashboard_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		$this->data["mode"] = "backend";
		define("thisAdminTabMenu",'dashboard');
		define("thisAdminSubMenu",'');
		for_staff_or_higher();
		
		define("TXT_TITLE",'Dashboard');

		$this->view_all_download_history_model = "View_all_download_history_model";
		$this->view_all_reserve_model = "View_all_reserve_model";
		
		$this->biblio_model = "Biblio_model";
		$this->biblio_copy_model = "Biblio_copy_model";
		$this->product_main_model = "Product_main_model";
		$this->user_model = "User_model";
		$this->user_login_history_model = "User_login_history_model";
		$this->shelf_history_model = "Shelf_history_model";
		$this->transaction_model = "Transaction_model";
		$this->product_category_model = "Product_category_model";
		$this->event_main_model = "Event_main_model";
		$this->event_model = "Event_model";

	}
	
	function index()
	{
		$this->dashboard();
	}
	
	function dashboard()
	{
		@define("thisAction","dashboard");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/report/dashboard/dashboard';
		$this->data["header_title"] = TXT_TITLE;
		$this->data["message"] = "";
		

		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function ajax_get_data_summary($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result = "";
		$this->db->flush_cache();
		$this->load->model($this->user_model,"user");
		$this->user->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		$this->user->set_where($this->user->get_table_name().".user_role_aid >= '".getSessionUserRoleAid()."'");
		
		if(!is_blank($created_date_from)){
			$this->user->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->user->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		$user_total = $this->user->count_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		$result["user_total"] = $user_total;
		
		$this->db->flush_cache();
		$this->load->model($this->view_all_products,"view_all_products_dashbaord");
		$this->view_all_products_dashbaord->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		$data_where = array();
		$data_where["product_type_aid"] = array("1","2");
		$this->view_all_products_dashbaord->set_where_in($data_where);
		
		if(!is_blank($created_date_from)){
			$this->view_all_products_dashbaord->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->view_all_products_dashbaord->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		$book_total = $this->view_all_products_dashbaord->count_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		$result["book_total"] = $book_total;
				
		$this->db->flush_cache();
		$this->load->model($this->view_all_products,"view_all_products_dashbaord");
		$this->view_all_products_dashbaord->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		$data_where = array();
		$data_where["product_type_aid"] = array("3");
		$this->view_all_products_dashbaord->set_where_in($data_where);
		
		if(!is_blank($created_date_from)){
			$this->view_all_products_dashbaord->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->view_all_products_dashbaord->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		$vdo_total = $this->view_all_products_dashbaord->count_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		$result["vdo_total"] = $vdo_total;
				
		if(!is_blank($created_date_from) || !is_blank($created_date_to)){
			$this->db->flush_cache();
			$this->load->model($this->user_login_history_model,"user_history");
			$this->user_history->set_where(array("user_owner_aid"=>getUserOwnerAid($this), "action"=>'login'));
			
			if(!is_blank($created_date_from)){
				$this->user_history->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->user_history->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$user_history_total = $this->user_history->count_records(false);
			$result["user_history_total"] = $user_history_total;
		}
		
		$this->db->flush_cache();
		$this->load->model($this->shelf_history_model,"shelf_history");
		$this->shelf_history->set_where(array("action"=>'in'));
		
		if(!is_blank($created_date_from)){
			$this->shelf_history->set_where('updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->shelf_history->set_where('updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		$shelf_history_total = $this->shelf_history->count_records(false);
		$result["shelf_history_total"] = $shelf_history_total;

		if(CONST_HAS_EVENT == "1"){
			$this->db->flush_cache();
			$this->load->model($this->event_model,"event");
			$this->event->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
			
			if(!is_blank($created_date_from)){
				$this->event->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->event->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$event_total = $this->event->count_records(false);
			// echo "<br>sql : ".$this->db->last_query();
			$result["event_total"] = $event_total;
		}
		
		if(!is_blank($created_date_from) || !is_blank($created_date_to)){
			$this->db->flush_cache();
			$this->load->model($this->transaction_model,"transaction");
			
			if(!is_blank($created_date_from)){
				$this->transaction->set_where('borrowing_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->transaction->set_where('borrowing_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$transaction_total = $this->transaction->count_records(false);
			$result["transaction_total"] = $transaction_total;
		}
		
		$return = array('result' =>$result);
		echo json_encode($return);
	}
		
	function ajax_get_data_product_book_and_copy_by_product_main($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";
		
		$master_product_main = "";
		$master_product_main_ori = $this->data["master_product_main"];
		if(is_var_array($master_product_main_ori)){
			foreach ($master_product_main_ori as $item) {
				$product_type_aid = get_array_value($item,"product_type_aid","0");
				if($product_type_aid == "1" || $product_type_aid == "2"){
					$master_product_main[] = $item;
				}
			}
		}
		// print_r($master_product_main);
		
		$tmp_result = "";
		$data_total = "";
		$data_total_copy = "";
		$data_total_view = "";
		$data_total_download = "";
		$data_total_rental = "";
		if(is_var_array($master_product_main)){
			$this->load->model($this->view_all_products,"view_all_products_dashboard");
			$data_where = array();
			$data_where["product_type_aid"] = array("1","2");
			$this->view_all_products_dashboard->set_where_in($data_where);
			if(!is_blank($created_date_from)){
				$this->view_all_products_dashboard->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->view_all_products_dashboard->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->view_all_products_dashboard->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$product_main_aid = get_array_value($item,"product_main_aid","0");
					$total_copy = get_array_value($item,"total_copy","0");
					$total_view = get_array_value($item,"total_view","0");
					$total_download = get_array_value($item,"total_download","0");
					$total_rental = get_array_value($item,"total_rental","0");
					if(is_var_array(get_array_value($tmp_result,$product_main_aid,""))){
						$tmp_result[$product_main_aid]["total"] += 1;
						$tmp_result[$product_main_aid]["total_view"] += $total_view;
						$tmp_result[$product_main_aid]["total_download"] += $total_download;
						$tmp_result[$product_main_aid]["total_rental"] += $total_rental;
						$tmp_result[$product_main_aid]["total_copy"] = 0;
					}else{
						$tmp_result[$product_main_aid]["total"] = 1;
						$tmp_result[$product_main_aid]["total_view"] = $total_view;
						$tmp_result[$product_main_aid]["total_download"] = $total_download;
						$tmp_result[$product_main_aid]["total_rental"] = $total_rental;
						$tmp_result[$product_main_aid]["total_copy"] = 0;
					}
				}
			}
			
			$this->load->model($this->view_all_product_copies_with_detail,"view_all_product_copies_dashboard");
			if(!is_blank($created_date_from)){
				$this->view_all_product_copies_dashboard->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->view_all_product_copies_dashboard->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->view_all_product_copies_dashboard->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$product_main_aid = get_array_value($item,"product_main_aid","0");
					// echo "<BR>product_main_aid = ".$product_main_aid;
					if(is_var_array(get_array_value($tmp_result,$product_main_aid,""))){
						$tmp_result[$product_main_aid]["total_copy"] += 1;
					}else{
						$tmp_result[$product_main_aid]["total_copy"] = 1;
					}
				}
			}
			
			foreach($master_product_main as $item){
				$task_title[] = get_array_value($item,"name","N/A");
				$aid = get_array_value($item,"aid","0");
				
				$tmp = get_array_value($tmp_result,$aid,"");
				$data_total[] = (int) get_array_value($tmp,"total",0);
				$data_total_copy[] = (int) get_array_value($tmp,"total_copy",0);
				$data_total_view[] = (int) get_array_value($tmp,"total_view",0);
				$data_total_download[] = (int) get_array_value($tmp,"total_download",0);
				$data_total_rental[] = (int) get_array_value($tmp,"total_rental",0);
				
			}
		}
		
		// print_r($tmp_result);
		if(count($data_total) < 5){
			for ($i=5; $i > count($data_total); $i--) { 
				$task_title[] = "";
				$data_total[] = 0;
				$data_total_copy[] = 0;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Biblio";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;

		$tmp = array();
		$tmp["name"] = "#Copy";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total_copy;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_product_book_by_product_main($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";
		
		$master_product_main = "";
		$master_product_main_ori = $this->data["master_product_main"];
		if(is_var_array($master_product_main_ori)){
			foreach ($master_product_main_ori as $item) {
				$product_type_aid = get_array_value($item,"product_type_aid","0");
				if($product_type_aid == "1" || $product_type_aid == "2"){
					$master_product_main[] = $item;
				}
			}
		}
		// print_r($master_product_main);
		
		$tmp_result = "";
		$data_total = "";
		$data_total_copy = "";
		$data_total_view = "";
		$data_total_download = "";
		$data_total_rental = "";
		if(is_var_array($master_product_main)){
			$this->load->model($this->view_all_products,"view_all_products_dashboard");
			$data_where = array();
			$data_where["product_type_aid"] = array("1","2");
			$this->view_all_products_dashboard->set_where_in($data_where);
			if(!is_blank($created_date_from)){
				$this->view_all_products_dashboard->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->view_all_products_dashboard->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->view_all_products_dashboard->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$product_main_aid = get_array_value($item,"product_main_aid","0");
					$total_copy = get_array_value($item,"total_copy","0");
					$total_view = get_array_value($item,"total_view","0");
					$total_download = get_array_value($item,"total_download","0");
					$total_rental = get_array_value($item,"total_rental","0");
					if(is_var_array(get_array_value($tmp_result,$product_main_aid,""))){
						$tmp_result[$product_main_aid]["total"] += 1;
						$tmp_result[$product_main_aid]["total_view"] += $total_view;
						$tmp_result[$product_main_aid]["total_download"] += $total_download;
						$tmp_result[$product_main_aid]["total_rental"] += $total_rental;
						$tmp_result[$product_main_aid]["total_copy"] = 0;
					}else{
						$tmp_result[$product_main_aid]["total"] = 1;
						$tmp_result[$product_main_aid]["total_view"] = $total_view;
						$tmp_result[$product_main_aid]["total_download"] = $total_download;
						$tmp_result[$product_main_aid]["total_rental"] = $total_rental;
						$tmp_result[$product_main_aid]["total_copy"] = 0;
					}
				}
			}
			
			$this->load->model($this->view_all_product_copies_with_detail,"view_all_product_copies_dashboard");
			if(!is_blank($created_date_from)){
				$this->view_all_product_copies_dashboard->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->view_all_product_copies_dashboard->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->view_all_product_copies_dashboard->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$product_main_aid = get_array_value($item,"product_main_aid","0");
					// echo "<BR>product_main_aid = ".$product_main_aid;
					if(is_var_array(get_array_value($tmp_result,$product_main_aid,""))){
						$tmp_result[$product_main_aid]["total_copy"] += 1;
					}else{
						$tmp_result[$product_main_aid]["total_copy"] = 1;
					}
				}
			}
			
			foreach($master_product_main as $item){
				$task_title[] = get_array_value($item,"name","N/A");
				$aid = get_array_value($item,"aid","0");
				
				$tmp = get_array_value($tmp_result,$aid,"");
				$data_total[] = (int) get_array_value($tmp,"total",0);
				$data_total_copy[] = (int) get_array_value($tmp,"total_copy",0);
				$data_total_view[] = (int) get_array_value($tmp,"total_view",0);
				$data_total_download[] = (int) get_array_value($tmp,"total_download",0);
				$data_total_rental[] = (int) get_array_value($tmp,"total_rental",0);
				
			}
		}
		
		// print_r($tmp_result);
		if(count($data_total) < 10){
			for ($i=10; $i > count($data_total); $i--) { 
				$task_title[] = "";
				$data_total[] = 0;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Biblio";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_user_login_by_device($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";

		$data_total = "";
		$tmp_result = "";

		$this->load->model($this->user_login_history_model,"user_login_history");
		$this->user_login_history->set_where(array("action"=>"login"));
		if(!is_blank($created_date_from)){
			$this->user_login_history->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->user_login_history->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		$result_list = $this->user_login_history->load_records(false);
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$device = get_array_value($item,"device","web");
				if($device == "andriod") $device = "android";
				if(is_var_array(get_array_value($tmp_result,$device,""))){
					$tmp_result[$device]["total"] += 1;
				}else{
					$tmp_result[$device]["total"] = 1;
				}
			}
		}

		if(is_var_array($tmp_result)){
			foreach ($tmp_result as $key => $value) {
				$obj = array();
				$obj["name"] = $key;
				$obj["y"] = $value["total"];
				$data_total[] = $obj;
			}
		}

		$tmp = array();
		$tmp["name"] = "#User login";
		$tmp["type"] = "pie";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
				
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_user_registration_by_device($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";

		$data_total = "";
		$tmp_result = "";

		$this->load->model($this->user_model,"user");
		if(!is_blank($created_date_from)){
			$this->user->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->user->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		$result_list = $this->user->load_records(false);
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$channel = get_array_value($item,"channel","web");
				if($channel == "andriod") $channel = "android";
				if(is_var_array(get_array_value($tmp_result,$channel,""))){
					$tmp_result[$channel]["total"] += 1;
				}else{
					$tmp_result[$channel]["total"] = 1;
				}
			}
		}

		if(is_var_array($tmp_result)){
			foreach ($tmp_result as $key => $value) {
				$obj = array();
				$obj["name"] = $key;
				$obj["y"] = $value["total"];
				$data_total[] = $obj;
			}
		}

		$tmp = array();
		$tmp["name"] = "#User registration";
		$tmp["type"] = "pie";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
				
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
			
	function ajax_get_data_product_book_by_category($sid=""){
		$product_main_aid = $this->input->get_post('c_category_book_product_main_aid');
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		$data_search["c_category_book_product_main_aid"] = $product_main_aid;

		// if($product_main_aid != "1" && $product_main_aid != '2'){
		// 	$return = array('data_search' => $data_search, 'data_chart' => "", 'task_title'=>"");
		// 	echo json_encode($return);
		// 	return "";
		// }
		
		$result_set = "";
		$task_title = "";

		$this->load->model($this->product_category_model,"product_category");
		$master_category = $this->product_category->load_category_by_product_main($product_main_aid);
		$obj = array();
		$obj["aid"] = "0";
		$obj["name"] = "None Cateogry";
		$master_category[] = $obj;

		$tmp_result = "";
		$tmp_result["0"]["total"] = 0;
		$tmp_result["0"]["total_view"] = 0;
		$tmp_result["0"]["total_download"] = 0;
		$tmp_result["0"]["total_rental"] = 0;
		$tmp_result["0"]["total_copy"] = 0;
		$data_total = "";
		$data_total_copy = "";
		$data_total_view = "";
		$data_total_download = "";
		$data_total_rental = "";
		if(is_var_array($master_category)){
			$this->load->model($this->view_all_products,"view_all_products_dashboard");
			$data_where = array();
			$data_where["product_type_aid"] = array("1","2");
			$this->view_all_products_dashboard->set_where_in($data_where);
			if(!is_blank($created_date_from)){
				$this->view_all_products_dashboard->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->view_all_products_dashboard->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->view_all_products_dashboard->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$pmaid = get_array_value($item,"product_main_aid","");
					if($product_main_aid == $pmaid){
						$category = get_array_value($item,"category","");
						$total_copy = get_array_value($item,"total_copy","0");
						$total_view = get_array_value($item,"total_view","0");
						$total_download = get_array_value($item,"total_download","0");
						$total_rental = get_array_value($item,"total_rental","0");
						$arr_category = array_filter(explode(',', $category));
						if(is_var_array($arr_category)){
							foreach ($arr_category as $cate) {
								// echo "cate = $cate<BR>";
								if(is_var_array(get_array_value($tmp_result,$cate,""))){
									$tmp_result[$cate]["total"] += 1;
									$tmp_result[$cate]["total_view"] += $total_view;
									$tmp_result[$cate]["total_download"] += $total_download;
									$tmp_result[$cate]["total_rental"] += $total_rental;
									$tmp_result[$cate]["total_copy"] = 0;
								}else{
									$tmp_result[$cate]["total"] = 1;
									$tmp_result[$cate]["total_view"] = $total_view;
									$tmp_result[$cate]["total_download"] = $total_download;
									$tmp_result[$cate]["total_rental"] = $total_rental;
									$tmp_result[$cate]["total_copy"] = 0;
								}
							}
						}else{
							$tmp_result["0"]["total"] += 1;
							$tmp_result["0"]["total_view"] += $total_view;
							$tmp_result["0"]["total_download"] += $total_download;
							$tmp_result["0"]["total_rental"] += $total_rental;
							$tmp_result["0"]["total_copy"] = 0;
						}
					}
				}
			}
			
			$this->load->model($this->view_all_product_copies_with_detail,"view_all_product_copies_dashboard");
			if(!is_blank($created_date_from)){
				$this->view_all_product_copies_dashboard->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->view_all_product_copies_dashboard->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->view_all_product_copies_dashboard->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$pmaid = get_array_value($item,"product_main_aid","");
					if($product_main_aid == $pmaid){
						$category = get_array_value($item,"category","");
						$arr_category = array_filter(explode(',', $category));
						if(is_var_array($arr_category)){
							foreach ($arr_category as $cate) {
								if(is_var_array(get_array_value($tmp_result,$cate,""))){
									$tmp_result[$cate]["total_copy"] += 1;
								}else{
									$tmp_result[$cate]["total_copy"] = 1;
								}
							}
						}else{
							$tmp_result["0"]["total_copy"] += 1;
						}
					}
				}
			}
			
			foreach($master_category as $item){
				$task_title[] = get_array_value($item,"name","N/A");
				$aid = get_array_value($item,"aid","0");
				
				$tmp = get_array_value($tmp_result,$aid,"");
				$data_total[] = (int) get_array_value($tmp,"total",0);
				$data_total_copy[] = (int) get_array_value($tmp,"total_copy",0);
				$data_total_view[] = (int) get_array_value($tmp,"total_view",0);
				$data_total_download[] = (int) get_array_value($tmp,"total_download",0);
				$data_total_rental[] = (int) get_array_value($tmp,"total_rental",0);
				
			}
		}
		
		// print_r($tmp_result);
		if(count($data_total) < 20){
			for ($i=20; $i > count($data_total); $i--) { 
				$task_title[] = "";
				$data_total[] = 0;
				$data_total_copy[] = 0;
				$data_total_view[] = 0;
				$data_total_download[] = 0;
				$data_total_rental[] = 0;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Biblio";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;

		$tmp = array();
		$tmp["name"] = "#Copy";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total_copy;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_product_vdo_by_category($sid=""){
		$product_main_aid = $this->input->get_post('c_category_vdo_product_main_aid');
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		$data_search["c_category_vdo_product_main_aid"] = $product_main_aid;

		// if($product_main_aid != "1" && $product_main_aid != '2'){
		// 	$return = array('data_search' => $data_search, 'data_chart' => "", 'task_title'=>"");
		// 	echo json_encode($return);
		// 	return "";
		// }
		
		$result_set = "";
		$task_title = "";

		$this->load->model($this->product_category_model,"product_category");
		$master_category = $this->product_category->load_category_by_product_main($product_main_aid);
		$obj = array();
		$obj["aid"] = "0";
		$obj["name"] = "None Category";
		$master_category[] = $obj;
		
		$tmp_result = "";
		$tmp_result["0"]["total"] = 0;
		$data_total = "";
		$data_total_copy = "";
		$data_total_view = "";
		$data_total_download = "";
		$data_total_rental = "";
		if(is_var_array($master_category)){
			$this->load->model($this->view_all_products,"view_all_products_dashboard");
			$data_where = array();
			$data_where["product_type_aid"] = array("3");
			$this->view_all_products_dashboard->set_where_in($data_where);
			if(!is_blank($created_date_from)){
				$this->view_all_products_dashboard->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->view_all_products_dashboard->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->view_all_products_dashboard->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$category = get_array_value($item,"category","");
					$pmaid = get_array_value($item,"product_main_aid","");
					$total_copy = get_array_value($item,"total_copy","0");
					if($product_main_aid == $pmaid){
						$arr_category = array_filter(explode(',', $category));
						if(is_var_array($arr_category)){
							foreach ($arr_category as $cate) {
								// echo "cate = $cate<BR>";
								if(is_var_array(get_array_value($tmp_result,$cate,""))){
									$tmp_result[$cate]["total"] += 1;
								}else{
									$tmp_result[$cate]["total"] = 1;
								}
							}
						}else{
							$tmp_result["0"]["total"] += 1;
						}
					}
				}
			}
			
			foreach($master_category as $item){
				$task_title[] = get_array_value($item,"name","N/A");
				$aid = get_array_value($item,"aid","0");
				
				$tmp = get_array_value($tmp_result,$aid,"");
				$data_total[] = (int) get_array_value($tmp,"total",0);				
			}
		}
		
		// print_r($tmp_result);
		if(count($data_total) < 20){
			for ($i=20; $i > count($data_total); $i--) { 
				$task_title[] = "";
				$data_total[] = 0;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Vdo";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_product_book_popular_download($sid=""){
		$product_main_aid = $this->input->get_post('c_popular_book_product_main_aid');
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		$data_search["c_popular_book_product_main_aid"] = $product_main_aid;
		
		$result_set = "";
		$task_title = "";
		
		$this->db->flush_cache();
		$this->load->model($this->shelf_history_model,"shelf_history");
		$this->db->select('parent_aid, copy_aid, product_type_aid, count(*) as total_download');
		$this->shelf_history->set_where(array("action"=>'in'));
		$this->db->group_by("copy_aid, product_type_aid");
		
		if(!is_blank($created_date_from)){
			$this->shelf_history->set_where('shelf_history.updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->shelf_history->set_where('shelf_history.updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		
		$this->shelf_history->set_limit(0,20);
		$this->shelf_history->set_order_by("*total_download desc");
		$result = $this->shelf_history->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		// print_r($result);
		
		$data_total = "";
		if(is_var_array($result)){
			foreach($result as $item){
				$task_title[] = get_array_value($item,"title","N/A");
				$data_total[] = (int) get_array_value($item,"total_download","0");
			}
		}

		if(count($data_total) < 20){
			for ($i=20; $i > count($data_total); $i--) { 
				$task_title[] = "";
				$data_total[] = 0;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Download";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_search_popular($sid=""){
		$product_main_aid = $this->input->get_post('c_popular_book_product_main_aid');
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		$data_search["c_popular_book_product_main_aid"] = $product_main_aid;
		
		$result_set = "";
		$task_title = "";
		
		$this->db->flush_cache();
		$this->load->model($this->search_history_model,"search_history");
		$this->db->select('aid, word, cond, search_in, count(*) as total_search');
		$this->db->group_by("word");
		
		if(!is_blank($created_date_from)){
			$this->search_history->set_where('search_history.created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->search_history->set_where('search_history.created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		
		$this->search_history->set_limit(0,20);
		$this->search_history->set_order_by("*total_search desc");
		$result = $this->search_history->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		// print_r($result);
		
		$data_total = "";
		if(is_var_array($result)){
			foreach($result as $item){
				$task_title[] = get_array_value($item,"word","N/A");
				$data_total[] = (int) get_array_value($item,"total_search","0");
			}
		}

		if(count($data_total) < 20){
			for ($i=20; $i > count($data_total); $i--) { 
				$task_title[] = "";
				$data_total[] = 0;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Search";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_download_by_product_main($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";

		$data_total = "";
		$tmp_result = "";

		$this->load->model($this->view_all_download_history_model,"download_history_dashboard");
		if(!is_blank($created_date_from)){
			$this->download_history_dashboard->set_where('updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->download_history_dashboard->set_where('updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		$result_list = $this->download_history_dashboard->load_records(false);
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$product_main_name = get_array_value($item,"product_main_name","None");
				if(is_var_array(get_array_value($tmp_result,$product_main_name,""))){
					$tmp_result[$product_main_name]["total"] += 1;
				}else{
					$tmp_result[$product_main_name]["total"] = 1;
				}
			}
		}

		if(is_var_array($tmp_result)){
			foreach ($tmp_result as $key => $value) {
				$obj = array();
				$obj["name"] = $key;
				$obj["y"] = $value["total"];
				$data_total[] = $obj;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Download";
		$tmp["type"] = "pie";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
				
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_popular_reserve($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";
		
		$this->db->flush_cache();
		$this->load->model($this->view_all_reserve_model,"view_all_reserve_dashboard");
		$this->db->select('view_all_reserve.*, count(*) as total_download');
		$this->db->group_by("copy_aid, product_type_aid");
		
		if(!is_blank($created_date_from)){
			$this->view_all_reserve_dashboard->set_where('updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->view_all_reserve_dashboard->set_where('updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		
		$this->view_all_reserve_dashboard->set_limit(0,20);
		$this->view_all_reserve_dashboard->set_order_by("*total_download desc");
		$result = $this->view_all_reserve_dashboard->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		// print_r($result);
		
		$data_total = "";
		if(is_var_array($result)){
			foreach($result as $item){
				$task_title[] = get_array_value($item,"parent_title","N/A");
				$data_total[] = (int) get_array_value($item,"total_download","0");
			}
		}

		if(count($data_total) < 20){
			for ($i=20; $i > count($data_total); $i--) { 
				$task_title[] = "";
				$data_total[] = 0;
			}
		}

		$tmp = array();
		$tmp["name"] = "#Reserve";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	

	function ajax_get_data_event_by_product_main($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";

		$this->load->model($this->event_main_model,"event_main");
		$master_event_main = $this->event_main->load_event_mains();
		// print_r($master_event_main);
		
		$tmp_result = "";
		$data_total = "";
		if(is_var_array($master_event_main)){
			$this->load->model($this->event_model,"event");
			if(!is_blank($created_date_from)){
				$this->event->set_where('created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
			}
			if(!is_blank($created_date_to)){
				$this->event->set_where('created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
			}
			$result_list = $this->event->load_records(false);
			if(is_var_array($result_list)){
				foreach($result_list as $item){
					$event_main_aid = get_array_value($item,"event_main_aid","0");
					if(is_var_array(get_array_value($tmp_result,$event_main_aid,""))){
						$tmp_result[$event_main_aid]["total"] += 1;
					}else{
						$tmp_result[$event_main_aid]["total"] = 1;
					}
				}
			}
			
			foreach($master_event_main as $item){
				$task_title[] = get_array_value($item,"name","N/A");
				$aid = get_array_value($item,"aid","0");
				
				$tmp = get_array_value($tmp_result,$aid,"");
				$data_total[] = (int) get_array_value($tmp,"total",0);				
			}
		}
		
		// print_r($tmp_result);

		$tmp = array();
		$tmp["name"] = "#News";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
				
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_popular_biblio_download($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";
		
		$this->db->flush_cache();
		$this->load->model($this->shelf_history_model,"shelf_history");
		$this->db->select('issue_aid, count(*) as total_download, biblio.title as biblio_title');
		$this->db->join('biblio', 'biblio.aid = shelf_history.issue_aid', "left");
		$this->shelf_history->set_where(array("action"=>'in'));
		$this->db->group_by("issue_aid");
		
		if(!is_blank($created_date_from)){
			$this->shelf_history->set_where('shelf_history.updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->shelf_history->set_where('shelf_history.updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		
		$this->shelf_history->set_limit(0,20);
		$this->shelf_history->set_order_by("*total_download desc");
		$result = $this->shelf_history->load_records(false);
		// print_r($result);
		
		$data_total = "";
		if(is_var_array($result)){
			foreach($result as $item){
				$task_title[] = get_array_value($item,"biblio_title","N/A");
				$data_total[] = (int) get_array_value($item,"total_download","0");
			}
		}

		$tmp = array();
		$tmp["name"] = "#Download";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	function ajax_get_data_popular_biblio_rental($sid=""){
		$created_date_from = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_from'),"");
		$created_date_to = get_datetime_pattern("Y-m-d",$this->input->get_post('created_date_to'),"");
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		$result_set = "";
		$task_title = "";
		
		$this->db->flush_cache();
		$this->load->model($this->transaction_model,"transaction");
		$this->db->select('biblio_aid, count(*) as total_download, biblio.title as biblio_title');
		$this->db->join('biblio', 'biblio.aid = transaction.biblio_aid', "left");
		$this->db->group_by("biblio_aid");
		
		if(!is_blank($created_date_from)){
			$this->transaction->set_where('transaction.updated_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->transaction->set_where('transaction.updated_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		
		$this->transaction->set_limit(0,20);
		$this->transaction->set_order_by("*total_download desc");
		$result = $this->transaction->load_records(false);
		// print_r($result);
		
		$data_total = "";
		if(is_var_array($result)){
			foreach($result as $item){
				$task_title[] = get_array_value($item,"biblio_title","N/A");
				$data_total[] = (int) get_array_value($item,"total_download","0");
			}
		}

		$tmp = array();
		$tmp["name"] = "#Rental";
		$tmp["type"] = "column";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;
		
		$result_set["data_chart"] = $data_chart;
		$result_set["task_title"] = $task_title;
		
		$return = array('data_search' => $data_search, 'data_chart' => get_array_value($result_set,"data_chart",""), 'task_title'=>get_array_value($result_set,"task_title",""));
		echo json_encode($return);
	}
	
	
}

?>