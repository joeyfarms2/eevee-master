<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");
require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_type_field_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		$this->lang->load('product_type_field');

		define("thisAdminTabMenu",'setting');
		define("thisAdminSubMenu",'product_type_field');
		@define("folderName",'product/product_back/type_field');
		
		define("TXT_TITLE", $this->lang->line('product_type_field_title'));
		define("TXT_INSERT_TITLE", $this->lang->line('product_type_field_insert_title'));
		define("TXT_UPDATE_TITLE", $this->lang->line('product_type_field_update_title'));
				
		$this->main_model = 'Product_type_field_model';
		$this->usmarc_subfield_model = 'Usmarc_subfield_dm_model';
	}
		
	function index($product_type_aid = ""){
		$this->data["init_adv_search"] = "clear";
		$this->show($product_type_aid);
	}
	
	function show($product_type_aid="", $msg=""){
		@define("thisAction",'show');
		for_owner_admin_or_higher();
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/type_field_list';
		$this->data["header_title"] = TXT_TITLE;
		$product_type_result = $this->check_exits_product_type_by_aid($product_type_aid,false);
		$this->data["product_type_result"] = $product_type_result;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form($product_type_aid = ""){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/type_field_form';
		$product_type_result = $this->check_exits_product_type_by_aid($product_type_aid,false);
		$this->data["product_type_result"] = $product_type_result;		
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add($product_type_aid = ""){
		@define("thisAction",'add');
		for_owner_admin_or_higher();
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$product_type_result = $this->check_exits_product_type_by_aid($product_type_aid,false);
		$this->data["product_type_result"] = $product_type_result;		

		$this->session->set_userdata('productTypeFieldBackDataSearchSession','');

		$this->form($product_type_aid);
	}
	
	function edit($product_type_aid = "", $aid=""){
		@define("thisAction",'edit');
		for_owner_admin_or_higher();
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$product_type_result = $this->check_exits_product_type_by_aid($product_type_aid,false);
		$this->data["product_type_result"] = $product_type_result;		

		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$item_detail = $this->main->load_record(false);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->data["header_title"] = TXT_UPDATE_TITLE . " : " .get_array_value($product_type_result,"name","");
			$this->form($product_type_aid);
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific product type field.');
			$this->show($product_type_aid);
			return "";
		}
	}
	
	function save(){
		@define("thisAction",'save');
		for_owner_admin_or_higher();
		$command = $this->input->get_post('command');
		$save_option = $this->input->get_post('save_option');
		
		$user_owner_aid = $this->get_user_owner_aid_by_input();

		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		
		$this->load->model($this->main_model,'main');
		
		$name = trim($this->input->get_post('name'));
		$product_type_aid = trim($this->input->get_post('product_type_aid'));
		$product_type_result = $this->check_exits_product_type_by_aid($product_type_aid,false);

		$data["user_owner_aid"] = $user_owner_aid;
		$data["product_type_aid"] = $product_type_aid;
		$data["product_topic_main_cid"] = $this->input->get_post('product_topic_main_cid');
		$data["tag"] = $this->input->get_post('tag');
		$data["subfield_cd"] = $this->input->get_post('subfield_cd');
		$data["name"] = $name;
		$data["description"] = $this->input->get_post('description');
		$data["is_required"] = $this->input->get_post('is_required');
		$data["input_type"] = $this->input->get_post('input_type');
		$data["status"] = $this->input->get_post('status');
		$data["weight"] = $this->input->get_post('weight');

		if(is_root_admin_or_higher()){
			$data["cid"] = $this->input->get_post('cid');
			$data["fixed_field"] = $this->input->get_post('fixed_field');
		}

		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->data["message"] = set_message_error(get_array_value($data,"error_msg","Duplicate data."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form($product_type_aid);
				return "";
			}
			
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$data["aid"] = $aid;
				$this->log_status('Backend : Insert product type field', '['.$name.'] just added into database.', $data);
				redirect('admin/product-type-field/'.$product_type_aid.'/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert product type field', 'Command insert_record() fail. Can not insert '.$name, $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form($product_type_aid);
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
				$this->form($product_type_aid);
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Backend : Update product type field',  '['.$name.'] has been updated.', $data);

				$tmp = array();
				$tmp["tag"] = $this->input->get_post('tag');
				$tmp["subfield_cd"] = $this->input->get_post('subfield_cd');
				$tmp["name"] = $name;
				$model = $this->get_product_model($product_type_aid);
				$this->load->model(get_array_value($model,"product_field_model",""),"product_field");
				$this->product_field->set_where(array("product_type_field_aid"=>$aid));
				$rs = $this->product_field->update_record($tmp);

				if($save_option){
					redirect('admin/product-type-field/'.$product_type_aid.'/add');
				}else{
					redirect('admin/product-type-field/'.$product_type_aid.'/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update product type field', 'Command update_record() fail. Can not update '.$name.'['.$aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form($product_type_aid);
				return "";
			}
			
		}else{
			$this->log_error('Backend : Product type field', 'Command not found.', $data);
			redirect('admin/product-type-field/'.$product_type_aid.'/status/'.md5('no-command'));
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
			$msg = set_message_error('Error occurred. Can not find this publisher.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : Product type field', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Product type field', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
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
			$msg = set_message_error('Error occurred. Can not find this product type field.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "name", "N/A").' [aid = '.$aid.']');
		$this_obj_title = get_array_value($objResult, "name", $aid);
		
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>$user_owner_aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete product type field', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete product type field', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
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
		$dsSession = $dataSearchSession->userdata('productTypeFieldBackDataSearchSession');		
		// print_r($dsSession);
		
		$product_type_aid = $this->input->get_post('product_type_aid');
		$this->main->set_where(array("product_type_aid"=>$product_type_aid));
		
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
		$order_by_option = $this->get_order_by_info($search_order_by,'weight asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(false);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'cid',"col_show"=>'cid',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Field Name',"field_order"=>'name',"col_show"=>'name_action',"title_class"=>'w250 hcenter',"result_class"=>'hleft');
		if(is_root_admin_or_higher()){
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Cid',"field_order"=>'cid',"col_show"=>'cid',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Fix?',"field_order"=>'fixed_field',"col_show"=>'fixed_field',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		}
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Tag',"field_order"=>'tag',"col_show"=>'tag',"title_class"=>'w100 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Subfield',"field_order"=>'subfield_cd',"col_show"=>'subfield_cd',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Topic',"field_order"=>'product_topic_main_cid',"col_show"=>'product_topic_main_cid',"title_class"=>'w50 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Required?',"field_order"=>'is_required',"col_show"=>'is_required_txt',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Weight',"field_order"=>'weight',"col_show"=>'weight',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		if(is_root_admin_or_higher()){
			$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
		}
		$header_list[] = array("sort_able"=>'0',"title_show"=>'&nbsp;',"field_order"=>'',"col_show"=>'action',"title_class"=>'hcenter',"result_class"=>'hleft');
		
		$this->session->set_userdata('productTypeFieldBackDataSearchSession',$data_search);	
		
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
			$product_type_aid = get_array_value($item,"product_type_aid","");
			$item["name_action"] = '<a href="'.site_url('admin/product-type-field/'.$product_type_aid.'/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"name","-").'</a>';
			$is_required = get_array_value($item,"is_required","0");
			if($is_required == 1){
				$item["is_required_txt"] = "Yes";
			}else{
				$item["is_required_txt"] = "No";
			}
			
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\', \'admin/product-type-field\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this." onclick="processChangeValue(\' active <strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\', \'admin/product-type-field\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$item["action"] = '';
			$fixed_field = get_array_value($item,"fixed_field","0");
			if(is_root_admin_or_higher() || $fixed_field != "1"){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/product-type-field\', \'<strong>'.removeAllQuote(get_array_value($item,"name","")).'</strong>\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
			}
			$result[] = $item;
		}
		
		return $result;
	
	}
	
	function status($product_type_aid="", $type="")	{
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
			case md5('no-product-field-aid') : 
				$this->data["message"] = set_message_error('Error occurred. Can not find this product type.');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->show($product_type_aid);
	}

	function ajax_get_tag_tree($sid=""){
		$this->load->model($this->usmarc_subfield_model,'usmarc_subfield');
		$this->usmarc_subfield->set_order_by("block.block_nmbr ASC, tag.tag ASC, usmarc_subfield_dm.subfield_cd ASC");
		$result_list = $this->usmarc_subfield->load_records(true);
		
		$result_obj = "";
		$block_nmbr_arr = array();
		$tag_arr = array();
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$aid = get_array_value($item,"subfield_cd","");
				$name = get_array_value($item,"subfield_cd","");
				$description = get_array_value($item,"description","");
				$block_nmbr = get_array_value($item,"block_nmbr","0");
				$block_description = get_array_value($item,"block_description","");
				$tag = get_array_value($item,"tag","");
				$tag_description = get_array_value($item,"tag_description","");
				// echo "block nmbr = $block_nmbr , tag = $tag , sub field = $name <BR />";
				$state = "";
				
				// print_r($block_nmbr_arr);
				if(!in_array($block_nmbr, $block_nmbr_arr)){
					// echo "1. <BR />";
					$block_nmbr_arr[] = $block_nmbr;
					$result = array();
					$result["id"] = $block_nmbr;
					$result["parentId"] = "";
					$result["name"] = $block_nmbr." - ".$block_description;
					$result["state"] = $state;
					$result_obj[] = $result;
				}
				
				if(!in_array($tag, $tag_arr)){
					// echo "2. <BR />";
					$tag_arr[] = $tag;
					$result = array();
					$result["id"] = $tag;
					$result["parentId"] = $block_nmbr;
					$result["name"] = $tag." - ".$tag_description;
					$result["state"] = $state;
					$result_obj[] = $result;
				}
				// echo "3. <BR />";
				$result = array();
				$result["id"] = $tag.".".$name;
				$result["parentId"] = $tag;
				$result["name"] = $name." - ".$description;
				$result["state"] = $state;
				$result_obj[] = $result;
			}
		}else{
			$result = array();
			$result["status"] = "warning";
			$result["msg"] = "No recoed found.";
			$result_obj[] = $result;
		}
		echo json_encode($result_obj);
	}
	
	function ajax_get_field_list_by_product_type_aid($sid=""){
		$parent_aid = trim($this->input->get_post('parent_aid'));
		$product_type_aid = trim($this->input->get_post('product_type_aid'));
		$product_type_detail = $this->check_exits_product_type_by_aid($product_type_aid);

		if(is_blank($product_type_aid) || $product_type_aid <= 0){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Product type aid is null.' );
			echo json_encode($result_obj);
			return "";
		}

		$product_field_result = "";
		$model = $this->get_product_model($product_type_aid);
		$this->load->model(get_array_value($model,"product_field_model",""),"product_field");
		if($parent_aid > 0 && $product_type_aid > 0){
			$this->product_field->set_where(array("parent_aid"=>$parent_aid));
			$product_field_result = $this->product_field->load_records(false);
		}
		
		$this->load->model($this->main_model,'main');
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["status"] = "1";
		// $tmp["user_owner_aid"] = getUserOwnerAid($this);
		$this->main->set_where($tmp);
		$this->main->set_order_by("weight ASC, created_date ASC");
		$result_list = $this->main->load_records(true);
		if(is_var_array($result_list)){
			$result_obj = array("status" => 'success', "result" => $result_list, "product_field_result" => $product_field_result);
			echo json_encode($result_obj);
			return"";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.');
			echo json_encode($result_obj);
			return"";
		}
		
	}
	
	
}

?>