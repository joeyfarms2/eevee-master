<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_magazine_field_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "backend";
		for_staff_or_higher();
		
		if(CONST_MARC_STATUS != "1"){
			redirect('admin');
		}

		$this_product_type_aid = '2';
		$this->data["this_product_type_aid"] = $this_product_type_aid;

		define("thisAdminSubMenu",'marc');
		@define("folderName",'product/product_back/magazine');
		
		define("TXT_TITLE",'Magazine management');
		define("TXT_INSERT_TITLE",'Add Field');
		define("TXT_UPDATE_TITLE",'Edit Field');
				
		$this->main_model = 'Magazine_field_model';
		
		$this->lang->load('product');
		$this->lang->load('product_magazine');				
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
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/magazine_field_list';
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
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/magazine_field_form';

		$product_main_result = $this->check_exits_product_main_by_url($product_main_url, false);
		$this->data["product_main_result"] = $product_main_result;
		$product_main_aid = get_array_value($product_main_result,"aid","");
		
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
	
	function edit($product_main_url="", $parent_aid="", $sequence=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("parent_aid"=>$parent_aid, "sequence"=>$sequence));
		$field_item_detail = $this->main->load_record(true);
		if(is_var_array($field_item_detail)){
			$this->data["field_item_detail"] = $field_item_detail;
			$this->form($product_main_url, $parent_aid);
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific magazine field.');
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

		if($command == "_update"){
			$this->data["header_title"] = TXT_UPDATE_TITLE;
		}else{
			$this->data["header_title"] = TXT_INSERT_TITLE;
		}
		
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$name = trim($this->input->get_post('name'));
		$sequence= $this->input->get_post('sequence');
		$product_main_field_aid = $this->input->get_post('product_main_field_aid');
				
		$data["ind1_cd"] = $this->input->get_post('ind1_cd');
		$data["ind2_cd"] = $this->input->get_post('ind2_cd');
		$field_data = trim($this->input->get_post('field_data'));
		$data["field_data"] = $field_data;
		
		if($command == "_insert"){
			$data["user_owner_aid"] = $user_owner_aid;
			$data["name"] = $name;
			$data["tag"] = get_text_pad($this->input->get_post('tag'),'0',3);
			$data["subfield_cd"] = $this->input->get_post('subfield_cd');
			$sequence = $this->main->get_sequence_from_parent_aid($parent_aid);
			$data["sequence"] = $sequence;
			
			$data["parent_aid"] = $parent_aid;
			$data["product_main_field_aid"] = "0";
			
			$aid = $this->main->insert_record($data);
			// if($aid){
				$this->log_status('Backend : Insert magazine field', '[name='.$name.'][sequence='.$sequence.'][field_data='.$field_data.'] of ['.$title.']['.$parent_aid.'] just added into database.', $data);
				$this->log_product_save($product_type_aid, $parent_aid, $title, "U", "Field [$name] has been added", $data, "field-add");
				redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/field/status/'.md5('success'));
			// }else{
				// $this->log_error('Backend : Insert magazine', 'Command insert_record() fail. Can not insert '.$name);
				// $this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				// $this->data["js_code"] = '';
				// $this->data["command"] = $command;
				// $this->data["field_item_detail"] = $data;
				// $this->form($product_main_url, $parent_aid);
				// return "";
			// }
						
		}else if($command == "_update"){
			$data_where["parent_aid"] = $parent_aid;
			$data_where["sequence"] = $sequence;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				$this->log_status('Admin : Update magazine field',  '[name='.$name.'][sequence='.$sequence.'][field_data='.$field_data.'] of ['.$title.']['.$parent_aid.'] has been updated.', $data);
				$this->log_product_save($product_type_aid, $parent_aid, $title, "U", "Field [$name] has been updated", $data, "field-update");
				if($save_option){
					redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/field/add');
				}else{
					redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/field/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update magazine field', 'Command update_record() fail. Can not update [name='.$name.'][sequence='.$sequence.'][field_data='.$field_data.'] of ['.$title.']['.$parent_aid.']', $data);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["field_item_detail"] = $data;
				$this->form($product_main_url, $parent_aid);
				return "";
			}
			
		}else{
			$this->log_error('Backend : Magazine field', 'Command not found.');
			redirect('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'field/status/'.md5('no-command'));
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

		$parent_aid = $this->input->post('parent_aid');
		$parent_detail = $this->check_exits_magazine($parent_aid, false);
		$title = get_array_value($parent_detail,"title","");
		
		$sequence = $this->input->post('sequence');
		if(is_blank($parent_aid) || is_blank($sequence)){
			$msg = set_message_error('Error occurred. Aid is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("parent_aid"=>$parent_aid , "sequence"=>$sequence));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$msg = set_message_error('Error occurred. Can not find this magazine field.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$this_name = get_array_value($objResult, "name", "");
		$field_data = get_array_value($objResult, "field_data", "");
		
		$this->main->set_where(array("parent_aid"=>$parent_aid , "sequence"=>$sequence));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete field', '[name='.$this_name.'][sequence='.$sequence.'][field_data='.$field_data.'] of ['.$title.']['.$parent_aid.'] has been deleted.', $objResult);
			$this->log_product_save($this->data["this_product_type_aid"], $parent_aid, $title, "U", "Field [$this_name] has been deleted", $objResult, "field-delete");
			$msg = set_message_success($this_name.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
		}else{
			$this->log_error('Backend : Delete field', 'Command delete_records() fail. Can not delete [name='.$this_name.'][sequence='.$sequence.'][field_data='.$field_data.'] of ['.$title.']['.$parent_aid.'].', $objResult);
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
		
		$search_product_main = $this->input->get_post('search_product_main');
		$data_where = "";
		if(is_var_array($search_product_main))
		foreach($search_product_main as $item){
			$data_where["product_main_aid"][] = $item;
			$data_search["search_product_main"][] = $item;
		}
		$this->main->set_where_in($data_where);
				
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
		$order_by_option = $this->get_order_by_info($search_order_by,'product_main_field.weight asc, product_main_field.created_date ASC');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list, $product_main_url);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);

		$header_list = array();
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'aid',"col_show"=>'cid',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Tag',"field_order"=>'tag',"col_show"=>'tag',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Sub Field',"field_order"=>'subfield_cd',"col_show"=>'subfield_cd',"title_class"=>'w100 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Filed Name',"field_order"=>'name',"col_show"=>'name_action',"title_class"=>'w200 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Ind 1',"field_order"=>'ind1_cd',"col_show"=>'ind1_cd',"title_class"=>'w100 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Ind 2',"field_order"=>'ind2_cd',"col_show"=>'ind2_cd',"title_class"=>'w100 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Data',"field_order"=>'field_data',"col_show"=>'field_data',"title_class"=>'w300 hcenter',"result_class"=>'hleft');
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
	
	function manage_column_detail($result_list, $product_main_url="0"){
		$result = "";
		if(!is_var_array($result_list)){
			return "";
		}
		
		foreach($result_list as $item){
			$parent_aid = get_array_value($item,"parent_aid","0");
			$item["name_action"] = '<a href="'.site_url('admin/product-'.$product_main_url.'/magazine/edit/'.$parent_aid.'/field/edit/'.get_array_value($item,"sequence","")).'">'.get_array_value($item,"product_main_field_name","-").'</a>';
			// $item["cid"] = get_text_pad(get_array_value($item,"aid","0"));
			$item["action"] = '';

			$product_main_field_aid = get_array_value($item,"product_main_field_aid","0");
			if($product_main_field_aid <= 0){
				$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this field" onclick="processDeleteField(\''.get_array_value($item,"parent_aid","").'\', \''.get_array_value($item,"sequence","").'\', \'admin/product/magazine-field\', \''.get_array_value($item,"name","").'\')"><i class="fa fa-trash-o "></i></a>&nbsp;&nbsp;&nbsp;';
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
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->show($product_main_url, $parent_aid);
	}
	
	
}

?>