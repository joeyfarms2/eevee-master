<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/redeem/redeem_init_controller.php");

class Redeem_back_controller extends Redeem_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'redeem');
		define("thisAdminSubMenu",'general_info');
		@define("folderName",'redeem/redeem_back/redeem');
		
		define("TXT_TITLE",'Redeem management');
		define("TXT_INSERT_TITLE",'Redeem management : Add new redeem');
		define("TXT_UPDATE_TITLE",'Redeem management : Edit redeem');
				
		$this->main_model = 'Redeem_main_model';		
		$this->lang->load('redeem');
	}
	
	function index(){
		$this->data["init_adv_search"] = "clear";
		$this->show();
	}
	
	function show($msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/redeem_list';
		$this->data["header_title"] = TXT_TITLE;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/redeem_form';
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#title').focus();";

		$this->session->set_userdata('redeemBackDataSearchSession','');

		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$redeem_main_detail = $this->main->load_record(true);

		if(is_var_array($redeem_main_detail)){
			$this->data["redeem_main_detail"] = $redeem_main_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($redeem_main_detail,"title","((no title))");
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific redeem.');
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
		$this->main->set_trans_start();
		
		$title = trim($this->input->get_post('title'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["status"] = $this->input->get_post('status');
		$data["title"] = $title;
		$start_date = get_datetime_pattern("db_date_format",$this->input->get_post('start_date'),get_db_now('%Y-%m-%d'));
		$data["start_date"] = $start_date;
		$data["expired_date"] = get_datetime_pattern("db_date_format",$this->input->get_post('expired_date'),NULL);
		$amount = $this->input->get_post('amount');
		// echo "amount = $amount , limit_per_code = $limit_per_code , limit_per_user = $limit_per_user , code_length = $code_length , code_prefix = $code_prefix , code_postfix = $code_postfix<BR>";

		if($amount <= 0){
			$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Amount can not be 0."));
			$this->data["js_code"] = "";
			$this->data["command"] = $command;
			$this->data["redeem_main_detail"] = $data;
			$this->form();
			return "";
		}
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->main->set_trans_rollback();
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["redeem_main_detail"] = $data;
				$this->form();
				return "";
			}

			$data["type"] = $this->input->get_post('type');
			$data["value"] = $this->input->get_post('value');
			$limit_per_code = $this->input->get_post('limit_per_code');
			$limit_per_user = $this->input->get_post('limit_per_user');
			$code_length = $this->input->get_post('code_length');
			$code_prefix = $this->input->get_post('code_prefix');
			$code_postfix = $this->input->get_post('code_postfix');
			
			$data["amount"] = $amount;
			$data["limit_per_code"] = $limit_per_code;
			$data["limit_per_user"] = $limit_per_user;
			$data["code_length"] = $code_length;
			$data["code_prefix"] = $code_prefix;
			$data["code_postfix"] = $code_postfix;

			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert redeem', 'New redeem just added into database.', $data);

				for ($i=1; $i <= $amount; $i++) { 
					$cid = '';
					// echo $i.'. ';
					if($code_length > 0){
						do{
							if(!is_blank($code_prefix)){
								$cid .= trim($code_prefix);
							}
								$cid .= get_random_text($code_length);
							if(!is_blank($code_postfix)){
								$cid .= trim($code_postfix);
							}
							$cid = strtoupper($cid);
							// echo $cid . ". ";
						}while( $this->isRedeemDetailCidExits($cid) );
						// echo '<BR />';
					}else{
							if(!is_blank($code_prefix)){
								$cid .= trim($code_prefix);
							}

							if(!is_blank($code_postfix)){
								$cid .= trim($code_postfix);
							}
							$cid = strtoupper($cid);

						if($this->isRedeemDetailCidExits($cid)){
							$this->main->set_trans_rollback();
							$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data. This code is already use."));
							$this->data["js_code"] = "$('#code_prefix').focus();";
							$this->data["command"] = $command;
							$this->data["redeem_main_detail"] = $data;
							$this->form();
							return "";
						}
					}

					$detail = array();
					$detail["user_owner_aid"] = $user_owner_aid;
					$detail["redeem_main_aid"] = $aid;
					$detail["cid"] = $cid;
					$this->load->model($this->redeem_detail_model,"redeem_detail");
					$this->redeem_detail->insert_record($detail);
				}
				$this->main->set_trans_commit();
				redirect('admin/redeem/status/'.md5('success'));
			}else{
				$this->main->set_trans_rollback();
				$this->log_error('Backend : Insert redeem', 'Command insert_record() fail. Can not insert new redeem', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["redeem_main_detail"] = $data;
				$this->form();
				return "";
			}
						
		}else if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			if($this->check_duplicate($data,$command)){
				$this->main->set_trans_rollback();
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["redeem_main_detail"] = $data;
				$this->form();
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Backend : Update redeem',  'Redeem has been updated.', $data);
				$this->main->set_trans_commit();
				if($save_option){
					redirect('admin/redeem/add');
				}else{
					redirect('admin/redeem/status/'.md5('success'));
				}
				return "";
			}else{
				$this->main->set_trans_rollback();
				$this->log_error('Backend : Update redeem', 'Command update_record() fail. Can not update redeem['.$aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["redeem_main_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->main->set_trans_rollback();
			$this->log_error('Backend : Redeem', 'Command not found.', $data);
			redirect('admin/redeem/status/'.md5('no-command'));
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
			$msg = set_message_error('Error occurred. Can not find this redeem.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'title', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Redeem', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Redeem', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
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
			$msg = set_message_error('Error occurred. Can not find this redeem.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "title", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "title", $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete redeem', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete redeem', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
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
		$dsSession = $dataSearchSession->userdata('redeemBackDataSearchSession');		
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
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Type',"field_order"=>'type',"col_show"=>'type',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Value',"field_order"=>'value',"col_show"=>'value',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Start Date',"field_order"=>'start_date',"col_show"=>'start_date_show',"title_class"=>'70 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Expiration Date',"field_order"=>'expired_date',"col_show"=>'expired_date_show',"title_class"=>'70 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('redeemBackDataSearchSession',$data_search);	
		
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
			$item["title_action"] = '<a href="'.site_url('admin/redeem/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"title","((no title)))").'</a>';
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this redeem." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/redeem\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this redeem." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\', \'admin/redeem\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$item["action"] = '';			
			if(is_staff_or_higher()){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this redeem." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/redeem\', \'<strong>'.removeAllQuote(get_array_value($item,"title","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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
	
}

?>