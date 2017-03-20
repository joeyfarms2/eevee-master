<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class Event_upload_back_controller extends Initcontroller {

	function __construct(){
		parent::__construct();
		
		if(CONST_HAS_EVENT != "1"){
			redirect('admin');
		}

		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'event');
		define("thisAdminSubMenu",'event_upload');
		@define("folderName",'event/');
		
		define("TXT_TITLE",'Event upload management');
		define("TXT_INSERT_TITLE",'Add new event upload');
		define("TXT_UPDATE_TITLE",'Edit event upload');
				
		$this->main_model = 'Event_upload_model';
		$this->event_model = 'Event_model';
		$this->event_main_model = 'Event_main_model';
		$this->event_category_model = 'Event_category_model';

		$this->load->model($this->event_main_model,"event_main");
		$this->data["master_event_main"] = $this->event_main->load_event_mains();

	}
	
	function index(){
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/event/event_upload_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function save(){
		@define("thisAction",'save');
		$command = $this->input->get_post('command');
		
		$event_upload_aid = $this->input->get_post('aid');
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$event_upload_aid,"status"=>"1"));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			redirect('admin/event-upload/status/'.md5('no-event-upload'));
		}
		
		$data["event_upload_aid"] = get_array_value($objResult,"aid","0");
		$data["user_owner_aid"] = get_array_value($objResult,"user_owner_aid","1");
		$data["title"] = get_array_value($objResult,"title","");
		$data["weight"] = "0";
		$data["event_main_aid"] = get_array_value($objResult,"event_main_aid","");
		$data["description"] = get_array_value($objResult,"description","");
		$data["ref_link"] = get_array_value($objResult,"ref_link","");
		$data["posted_by"] = get_array_value($objResult,"posted_by","");
		$data["posted_email"] = get_array_value($objResult,"posted_email","");
		$data["posted_ref"] = get_array_value($objResult,"posted_ref","");
		$data["in_home"] = "0";
		$data["is_highlight"] = "0";
		$data["is_recommended"] = "0";
		$data["status"] = "0";
		$data["publish_date"] = get_datetime_pattern("Y-m-d",get_array_value($objResult,"user_owner_aid",""),get_db_now());
		$data["category"] = get_array_value($objResult,"category","");
		$data["upload_path"] = "";
		
		$cid = "";		
		if(is_blank($cid)){
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isEventCodeExits($cid) );
		}
		$data["cid"] = trim($cid);
		
		$this->load->model($this->event_model,"event");
		$aid = $this->event->insert_record($data);
		if($aid > 0){
			$this->load->model($this->main_model,'main');
			$data = "";
			$data["status"] = "2";
			$data_where["aid"] = $event_upload_aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
		
			$this->log_status('Backend : Insert event fron event upload', '[event aid='.$aid.'] just added into database.');
			redirect('admin/event/edit/'.$aid);
		}else{
			$this->log_error('Backend : Insert event', 'Command insert_record() fail. Can not insert.');
			redirect('admin/event-upload/status/'.md5('insert-error'));
			return "";
		}

	}
	
	function ajax_set_status($sid="", $status=""){
		if(!is_staff_or_higher()){
			$result_obj = array("status" => 'error',"msg" => 'Permission denied.');
			echo json_encode($result_obj);
			return "";
		}
		$aid = $this->input->get_post('aid_selected');
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Aid is null.' );
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not find this event.' );
			echo json_encode($result_obj);
			return "";
		}
		$this_name = get_array_value($objResult, "title", $aid);
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->set_status($status);
		
		if ($rs){
			if($status == 1){
				$this->log_status('Backend : Event status', $this_name.'['.$aid.'] was active.');
				$result_obj = array("status" => 'success',"msg" => $this_name.' was active.' );
				echo json_encode($result_obj);
				return "";
			}else{
				$this->log_status('Backend : Event status', $this_name.'['.$aid.'] was inactive.');
				$result_obj = array("status" => 'success',"msg" => $this_name.' was inactive.' );
				echo json_encode($result_obj);
				return "";
			}
		}else{
			$this->log_error('Backend : Event status', 'Function main->set_status() fail. Can not set status to '.$this_name.'['.$aid.'].');
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not save data.' );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_get_main_list($sid){
		$this->load->model($this->main_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		if(is_staff()){
			$this->main->set_where(array("event_main_aid"=>getUserLoginPublisherAid($this)));
		}
		
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

		$search_event_main = $this->input->get_post('search_event_main');
		$data_where = "";
		if(is_var_array($search_event_main))
		foreach($search_event_main as $item){
			$data_where["event_main_aid"][] = $item;
			$data_search["search_event_main"][] = $item;
		}
		$this->main->set_where_in($data_where);

		$this->main->set_where(array("status"=>"1"));
		/*
		$search_status = $this->input->get_post('search_status');
		$data_where = "";
		if(is_var_array($search_status))
		foreach($search_status as $item){
			$data_where["status"][] = $item;
			$data_search["search_status"][] = $item;
		}
		$this->main->set_where_in($data_where);
		
		$search_option = $this->input->get_post('search_option');
		$data_where = "";
		if(is_var_array($search_option))
		foreach($search_option as $item){
			$data_where[$item] = "1";
			$data_search["search_option"][] = $item;
		}
		// echo print_r($data_where);
		$this->main->set_where($data_where);
		*/
		
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
		$optional["total_record"] = $this->main->count_records();
		$optional["page_selected"] = $this->input->get_post('page_selected');
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_PER_PAGE;
		$optional = $this->get_pagination_info($optional);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));			
		
		$search_order_by = $this->input->get_post('search_order_by');
		$order_by_option = $this->get_order_by_info($search_order_by,'created_date desc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'aid',"col_show"=>'name_action',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'title',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Provice',"field_order"=>'*event_main_name',"col_show"=>'event_main_name',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Posted by',"field_order"=>'email',"col_show"=>'posted_email',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Created Date',"field_order"=>'created_date',"col_show"=>'created_date_txt',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
				
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
			$item["name_action"] = '<a href="'.site_url('admin/event-upload/detail/'.get_array_value($item,"aid","")).'">'.get_text_pad(get_array_value($item,"aid","0"),0,8).'</a>';
			
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/accept.png" class="button" title="Click to \'Inctive\' this event" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/event\', \' inactive '.get_array_value($item,"title","").'\', \'status=0\')">';
			}else{
				$item["status_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/cross.png" class="button" title="Click to \'Active\' this event" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/event\', \' active '.get_array_value($item,"title","").'\', \'status=1\')">';
			}
			
			$status = get_array_value($item,"is_highlight","0");
			if($status == 1){
				$item['is_highlight_action'] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/accept.png" class="button" title="Click to remove highlight form this event" onclick="processChangeHighlight(\''.get_array_value($item,"aid","").'\', \''.removeAllQuote(get_array_value($item,"title","")).'\', 0)">';
			}else{
				$item['is_highlight_action'] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/cross.png" class="button" title="Click to set highlight this event" onclick="processChangeHighlight(\''.get_array_value($item,"aid","").'\', \''.removeAllQuote(get_array_value($item,"title","")).'\', 1)">';
			}
			
			$status = get_array_value($item,"is_recommended","0");
			if($status == 1){
				$item['is_recommended_action'] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/accept.png" class="button" title="Click to remove recommended form this event" onclick="processChangeRecommended(\''.get_array_value($item,"aid","").'\', \''.removeAllQuote(get_array_value($item,"title","")).'\', 0)">';
			}else{
				$item['is_recommended_action'] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/cross.png" class="button" title="Click to set recommended this event" onclick="processChangeRecommended(\''.get_array_value($item,"aid","").'\', \''.removeAllQuote(get_array_value($item,"title","")).'\', 1)">';
			}
			
			$in_home = get_array_value($item,"in_home","0");
			if($in_home == 1){
				$item["in_home_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/accept.png" class="button" title="Click to \'Inctive\' this event" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/event\', \' inactive '.get_array_value($item,"title","").'\', \'in_home=0\')">';
			}else{
				$item["in_home_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/cross.png" class="button" title="Click to \'Active\' this event" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/event\', \' active '.get_array_value($item,"title","").'\', \'in_home=1\')">';
			}
			
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<img src="'.CSS_PATH.'dandelion/images/icons/color/bin_closed.png" class="button" title="Click to \'Delete\' this event" onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/event\', \''.get_array_value($item,"title","").'\')">';
			}
			$item["checkbox"] = '<input type="checkbox" name="aid[]" id="aid_'.get_array_value($item,"aid","").'" value="'.get_array_value($item,"aid","").'" onclick="changeCheckItem(\'aid_all\',\'aid[]\',false,false)" />';
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
			case md5('no-event-upload') : 
				$this->data["message"] = set_message_error('No event found.');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->show();
	}

	function detail($aid=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/event/event_upload_detail';
		$this->data["header_title"] = TXT_TITLE;
	
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid,"status"=>"1"));
		$objResult = $this->main->load_record(true);
		if(!is_var_array($objResult)){
			redirect('admin/event-upload/status/'.md5('no-event-upload'));
		}
		
		$this->data["item_detail"] = $objResult;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function isEventCodeExits($cid){
		$this->load->model($this->event_model,"event");
		$this->event->set_where(array("cid"=>$cid));
		$total = $this->event->count_records();
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}
	
	
}

?>