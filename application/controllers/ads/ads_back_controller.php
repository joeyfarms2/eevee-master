<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Ads_back_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		if(CONST_HAS_ADS != "1"){
			redirect('home');
		}

		define("thisAdminTabMenu",'ads');
		define("thisAdminSubMenu",'ads');
		@define("folderName",'ads/ads_back/ads');
		
		define("TXT_TITLE",'Ads management');
		define("TXT_INSERT_TITLE",'Ads management : Add new ads');
		define("TXT_UPDATE_TITLE",'Ads management : Edit ads');
				
		$this->main_model = 'Ads_model';		
		$this->ads_category_model = 'Ads_category_model';		

		$this->load->model($this->ads_category_model,"category");
		$this->data["master_ads_category"] = $this->category->load_all_category();
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/ads_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/ads_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#title').focus();";

		$this->session->set_userdata('adsBackDataSearchSession','');

		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$item_detail = $this->main->load_record(true);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($item_detail,"title","((no title))");
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific ads.');
			$this->data["js_code"] = "$('#title').focus();";
			$this->show();
			return "";
		}
	}
	
	function save(){
		@define("thisAction",'save');
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');

		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		
		$this->load->model($this->main_model,'main');
		
		$title = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["title"] = $title;
		$data["weight"] = $this->input->get_post('weight');
		$data["is_highlight"] = $this->input->get_post('is_highlight');
		$data["is_new"] = $this->input->get_post('is_new');
		$data["in_home"] = $this->input->get_post('in_home');
		$publish_date = get_datetime_pattern("db_date_format",$this->input->get_post('publish_date'),get_db_now('%Y-%m-%d'));
		$data["publish_date"] = $publish_date;
		$data["expired_date"] = get_datetime_pattern("db_date_format",$this->input->get_post('expired_date'),NULL);
		$data["ref_link"] = $this->input->get_post('ref_link');
		$data["target"] = $this->input->get_post('target');
		$data["description"] = $this->input->get_post('description');
		$data["status"] = $this->input->get_post('status');

		$category_list = "";
		$category = $this->input->get_post('category');
		// echo "category : ".$category;
		if(is_var_array($category)){
			$category_list = ",";
			foreach($category as $item){
				$category_list .= $item.',';
			}
			// $category_list = substr($category_list, 1);
		}
		$data["category"] = $category_list;
		$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/ads/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"");
		$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/ads/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"");
		create_directories($upload_base_path);
		
		$cid = "";
		if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if(!is_var_array($itemResult)){
				$this->data["message"] = set_message_error("Ads not found.");
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}else{
				$cid = trim(get_array_value($itemResult,"cid",""));
				$path = './'.trim(get_array_value($itemResult,"upload_path",""));
				if($path != './' && $path != $upload_base_path.'/'.$cid.'/'){
					// echo "Change Path";
					if(is_dir($path)){
						echo $path." is found .. Start move";
						rename($path,$upload_base_path.'/'.$cid);
					}else{
						// echo $path." not found";
					}
				}else{
					// echo "Do Not Change Path";
				}
			}
		}
		
		if(is_blank($cid)){
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isAdsCodeExits($cid) );
		}
		$data["cid"] = trim($cid);
		$data["upload_path"] = $upload_base_path_db.'/'.$cid.'/';
		
		if( !is_blank(get_array_value($_FILES,"cover_image","")) && !is_blank(get_array_value($_FILES["cover_image"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'/'.$cid;
			$image_name = $_FILES["cover_image"]["name"];
			$file_type = substr(strrchr($image_name, "."), 0);
			
			$data["cover_image_file_type"] = $file_type;
			
			$new_file_name_thumb = $cid."-actual".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,800,0,99,1);

			$new_file_name_thumb = $cid."-thumb".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,270,0,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$this->log_error('Admin : Ads', 'Add new ads fail => Upload image error : '.$result_image_thumb["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_image_thumb,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}	
		}		
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			$data["total_view"] = '0';
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert ads', 'New ads just added into database.', $data);
				redirect('admin/ads/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert ads', 'Command insert_record() fail. Can not insert new ads', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
						
		}else if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			if($this->check_duplicate($data,$command)){
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Backend : Update ads',  'Ads has been updated.', $data);
				if($save_option){
					redirect('admin/ads/add');
				}else{
					redirect('admin/ads/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update ads', 'Command update_record() fail. Can not update ads['.$aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : Ads', 'Command not found.', $data);
			redirect('admin/ads/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($data="",$command=""){
		return false;
	}
			
	function ajax_set_value($sid="", $status=""){
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
			$msg = set_message_error('Error occurred. Can not find this ads.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'title', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Ads', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Ads', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_delete_one(){
		@define("thisAction",'ajax_delete_one');
		if(!is_staff_or_higher()){
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
			$msg = set_message_error('Error occurred. Can not find this ads.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "title", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "title", $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete ads', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete ads', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_get_main_list($sid){
		@define("thisAction",'ajax_get_main_list');
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));

		$dataSearchSession = new CI_Session();
		$dsSession = $dataSearchSession->userdata('adsBackDataSearchSession');		
		// print_r($dsSession);
		
		$search_post_word = $this->getDataFromInput('search_post_word');
		$data_search["search_post_word"] = $search_post_word;
		$search_in = $this->getDataFromInput('search_in');
		
		// echo "search_in : ".$search_in;
		if(!is_blank($search_post_word) && is_var_array($search_in))
		foreach($search_in as $item){
			$data_where[$item] = $search_post_word;
			$data_search["search_in"][] = $item;
		}
		$this->main->set_and_or_like($data_where);
		
		$search_status = $this->getDataFromInput('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->main->set_where_in($data_where);
		
		$created_date_from = $this->getDataFromInput('created_date_from');
		$created_date_to = $this->getDataFromInput('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->main->set_where($this->main->get_table_name().'.created_date >=', get_datetime_pattern("db_date_format",$created_date_from,"")." 00:00:00");
		}
		if(!is_blank($created_date_to)){
			$this->main->set_where($this->main->get_table_name().'.created_date <=', get_datetime_pattern("db_date_format",$created_date_to,"")." 23:59:59");
		}
		
		$search_record_per_page = $this->getDataFromInput('search_record_per_page', CONST_DEFAULT_RECORD_PER_PAGE);
		$optional = array();
		$optional["total_record"] = $this->main->count_records(true);
		$optional["page_selected"] = $this->getDataFromInput('page_selected');
		$optional["record_per_page"] = $search_record_per_page;
		$optional = $this->get_pagination_info($optional);
		$data_search["page_selected"] = get_array_value($optional,"page_selected","");
		$data_search["search_record_per_page"] = get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));
		
		$search_order_by = $this->getDataFromInput('search_order_by');
		$data_search["search_order_by"] = $search_order_by;
		$order_by_option = $this->get_order_by_info($search_order_by,'aid desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title_action',"title_class"=>'w250 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Weight',"field_order"=>'weight',"col_show"=>'weight',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('adsBackDataSearchSession',$data_search);	
		
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
	
	function manage_column_detail($result_list){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$item["title_action"] = '<a href="'.site_url('admin/ads/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title","((no title)))").'</a>';
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this ads." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/ads\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this ads." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/ads\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$item["action"] = '';			
			if(is_staff_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this ads." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/ads\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
			}

			$result[] = $item;
		}
		
		return $result;
	}
	
	function status($type="")	{
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
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->show();
	}
	
	function isAdsCodeExits($cid){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("cid"=>$cid));
		$total = $this->main->count_records();
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

}

?>