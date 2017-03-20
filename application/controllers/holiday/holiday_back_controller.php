<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");


class Holiday_back_controller extends Project_init_controller {

	function __construct() {
		parent::__construct();
		@define("thisAdminTabMenu",'holiday');
		@define('this_admin_tab_menu','holiday');
		@define('this_admin_sub_menu','holiday');
		
		@define("folderName","holiday/");
		$this->holiday_model = "holiday_model";
		$this->holiday_weekend_model = "holiday_weekend_model";
		for_login();
		define("TXT_INSERT_TITLE",'Holiday Form : Add new holiday');
		define("TXT_UPDATE_TITLE",'Holiday Form : Edit holiday');
	}
	
	public function index() {
		define("thisAction","index");
		$this->show();
	}
	
	function show() {
		@define("thisAction","show");
		@define("thisSubMenu","holiday");
		for_login();
		$this->data["title"] = DEFAULT_TITLE;
		
		$this->load->model($this->holiday_weekend_model,"weekend");
		$this->data["weekend_items"] = $this->weekend->load_holidays();
		
		$this->load->model($this->holiday_model,"holiday");
		$this->data["items"] = $this->holiday->load_holidays();
		
		$this->data["view_the_content"] = $this->default_theme_admin . '/holiday/holiday_list';
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		for_login();
		$this->data["title"] = DEFAULT_TITLE;
		$this->data['view_the_content'] = $this->default_theme_admin . '/holiday/holiday_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		for_login();
		@define("thisAction","add");
		$this->data["command"] = "_insert";
		$this->data["js_code"] = "$('#from_date').focus();";
		$this->data["header_title"] = TXT_INSERT_TITLE;
		
		$this->form();
	}
	
	function edit($aid=""){
		for_login();
		@define("thisAction","edit");
		$this->data['command'] = "_update";
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->holiday_model,"holiday");
		$item_detail = $this->holiday->load_info(array("aid" => $aid));
		
		if(is_var_array($item_detail)){
			// if (!empty($item_detail["from_date"]) && $item_detail["from_date"] != "0000-00-00") {
			// 	$item_detail["from_date"] = mdate("%d/%m/%Y", strtotime($item_detail["from_date"]));	
			// }
			// if (!empty($item_detail["to_date"]) && $item_detail["to_date"] != "0000-00-00") {
			// 	$item_detail["to_date"] = mdate("%d/%m/%Y", strtotime($item_detail["to_date"]));	
			// }
			// $item_detail["to_date"]
			// $item_detail["from_date"]
			$this->data["item_detail"] = $item_detail;
			$this->form();
		}else{
			$this->data["message"] = set_message_error("ไม่พบรายการวันหยุดนี้");
			$this->form();
			return "";
		}
	}
	
	function save(){
		for_login();
		@define("thisAction","save");
		@define("thisSubMenu","holiday");
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		
		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		
		$this->load->model($this->holiday_model,"holiday");		
		$from_date = $this->input->get_post('from_date');
		$to_date = $this->input->get_post('to_date');
		$data['from_date'] = $from_date;
		$data['to_date'] = $to_date;
		// echo $from_date;
		// echo $to_date;

		// $data['from_date'] = null;
		// if (!empty($from_date) && $from_date != '00/00/0000') {
		// 	$arr = explode('/', $from_date);
		// 	$data['from_date'] = $arr[2].'-'.$arr[0].'-'.$arr[1];
		// }
		// $data['to_date'] = null;
		// if (!empty($to_date) && $to_date != '00/00/0000') {
		// 	$arr = explode('/', $to_date);
		// 	$data['to_date'] = $arr[2].'-'.$arr[0].'-'.$arr[1];
		// }
		if (empty($data['to_date'])) $data['to_date'] = $data['from_date'];
		$data["remark"] = $this->input->get_post('remark');
		
		if($command == "_insert"){
			$data["user_owner_aid"] = getUserOwnerAid($this);
			$aid = $this->holiday->insert_record($data);
			//echo "<br>sql : ".$this->db->last_query()."<br>";

			if ($aid > 0){
				
				$this->log_status('Holiday : เพิ่มวันหยุด ['.$data["from_date"].' - '.$data["to_date"].'] '. ' เรียบร้อย');
				$this->data["message"] = "Your annual holiday(s) has been saved successfully.";
				$this->data["js_code"] = "";
				$this->data["command"] = "";
				$this->data["item_detail"] = "";
				redirect('admin/holiday');
			}
			else {
				$this->log_status('Holiday : Error ระบบไม่สามารถเพิ่มวันหยุด  ['.$data["from_date"].' - '.$data["to_date"].'] '.' เข้าสู่ระบบได้');
				$this->data["message"] = "Oops! Your annual holiday(s) could not be saved at the moment. Please refresh this page and try again.";
				$this->data["js_code"] = "$('#from_date').focus();";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
		}
		else if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data_where["aid"] = $aid;
			$this->holiday->set_where($data_where);
			$rs = $this->holiday->update_record($data);
			if($rs){
				$this->log_status('Holiday : บันทึกวันหยุด ['.$data["from_date"].' - '.$data["to_date"].'] เรียบร้อย');
				$this->data["message"] = "Your annual holiday(s) has been saved successfully.";
				redirect('admin/holiday');
			}else{
				$this->log_status('Holiday : Error ระบบไม่สามารถบันทึกวันหยุด ['.$data["from_date"].' - '.$data["to_date"].'] เข้าสู่ระบบได้');
				$this->data["message"] = "Oops! Your annual holiday(s) could not be saved at the moment. Please refresh this page and try again.";
				$this->data["js_code"] = "$('#from_date').focus();";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
		}
		$this->edit($aid);

	}

	function ajax_get_main_list($sid){
		$this->load->model($this->holiday_model,'main');
		$this->db->start_cache();		
		$data_search="";
		$data_where = "";
		
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		
		$search_post_word = trim($this->input->get_post('search_post_word'));
		$data_search["search_post_word"] = $search_post_word;
		$this->main->set_like(array("remark" => $search_post_word));
		
		$created_date_from = $this->input->get_post('created_date_from');
		$created_date_to = $this->input->get_post('created_date_to');
		$data_search["created_date_from"] = $created_date_from;
		$data_search["created_date_to"] = $created_date_to;
		
		if(!is_blank($created_date_from)){
			$this->main->set_where($this->main->get_table_name().'.from_date >=', get_datetime_pattern("db_date_format",$created_date_from,""));
		}
		if(!is_blank($created_date_to)){
			$this->main->set_where($this->main->get_table_name().'.to_date <=', get_datetime_pattern("db_date_format",$created_date_to,""));
		}		
		
		$optional = array();
		$optional["total_record"] = $this->main->count_records();
		$optional["page_selected"] = $this->input->get_post('page_selected');
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_PER_PAGE;
		$optional = $this->get_pagination_info($optional);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));			
		
		$search_order_by = $this->input->get_post('search_order_by');
		$order_by_option = $this->get_order_by_info($search_order_by,'from_date DESC');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'From Date',"field_order"=>'from_date',"col_show"=>'txt_from_date',"title_class"=>'w100 hleft',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'To Date',"field_order"=>'to_date',"col_show"=>'txt_to_date',"title_class"=>'w100 hleft',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Remark',"field_order"=>'remark',"col_show"=>'remark',"title_class"=>'w200 hleft',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'hcenter',"result_class"=>'hleft');		
		
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
			$item["txt_from_date"] = (!empty($item["from_date"]) && $item["from_date"] != "0000-00-00" ? "<a href='".site_url("admin/holiday/edit/".$item["aid"])."'>".$item["from_date"]."</a>" : "");
			$item["txt_to_date"] = (!empty($item["to_date"]) && $item["to_date"] != "0000-00-00" && ($item["to_date"] != $item["from_date"]) ? $item["to_date"] : "");
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				//$item["action"] .= '<img src="'.CSS_PATH.'dandelion/images/icons/color/bin_closed.png" class="button" title="Click to \'Delete\' this magazine" onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/holiday\', \''.get_array_value($item,"remark","").'\')">';
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this holiday." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/holiday\', \''.get_array_value($item,"remark","").'\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->show();
	}
	
	function ajax_save_weekend_holidays() {
		$data['sun'] = ($this->input->get_post('sun') == 'true' ? '1' : '0');
		$data['mon'] = ($this->input->get_post('mon') == 'true' ? '1' : '0');
		$data['tue'] = ($this->input->get_post('tue') == 'true' ? '1' : '0');
		$data['wed'] = ($this->input->get_post('wed') == 'true' ? '1' : '0');
		$data['thu'] = ($this->input->get_post('thu') == 'true' ? '1' : '0');
		$data['fri'] = ($this->input->get_post('fri') == 'true' ? '1' : '0');
		$data['sat'] = ($this->input->get_post('sat') == 'true' ? '1' : '0');
		$data['user_owner_aid'] = getSessionOwnerAid();
		
		$this->load->model($this->holiday_weekend_model,"holiday");
		$return = array("status" => "error", "msg" => "Oops! Your weekly holiday(s) could not be saved at the moment. Please refresh this page and try again.");
		if (getSessionOwnerAid() > 0) {
			$data_update = $data;
			unset($data_update['user_org_aid']);
			$result = $this->holiday->insert_or_update($data, $data_update);
			if ($result) {
				$return = array("status" => "success", "msg" => "Your weekly holiday(s) has been saved successfully.");
			}
		}
		echo get_json_encode($return);
	}

	function ajax_delete_one(){
		if(!is_owner_admin_or_higher()){
			$result_obj = array("status" => 'error',"msg" => 'Permission denied.');
			echo json_encode($result_obj);
			return "";
		}

		$aid = $this->input->post('aid_selected');
		// $aid = 1;
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Aid is null.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->holiday_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(true);
		if(!is_var_array($objResult)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not find this issue.' );
			echo json_encode($result_obj);
			return "";
		}
		$this_name = get_array_value($objResult, "remark", $aid)." (".get_array_value($objResult, "from_date", "")." - ".get_array_value($objResult, "to_date", "").")";
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->delete_records();
		
		if (true){
			$this->log_status('Backend : Delete holiday', $this_name.'['.$aid.'] has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $this_name.' has been deleted.' );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete holiday', 'Command delete_records() fail. Can not delete '.$this_name.'['.$aid.'].');
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not delete issue.' );
			echo json_encode($result_obj);
			return "";
		}
	}
	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */