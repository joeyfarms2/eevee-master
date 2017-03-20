<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_topic_back_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		for_owner_admin_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'setting');
		define("thisAdminSubMenu",'topic');
		@define("folderName",'product/');
		
		define("TXT_TITLE",'Topic management');
		define("TXT_INSERT_TITLE",'Topic management : Add new topic');
		define("TXT_UPDATE_TITLE",'Topic management : Edit topic');
				
		$this->main_model = 'Product_topic_model';
		$this->product_topic_main_model = 'Product_topic_main_model';
		
	}
	
	function index(){
		$this->show();
	}
	
	function show($topic_main_aid="", $msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/product/product_topic_list';
		$this->data["header_title"] = TXT_TITLE;
		
		if(!is_number_no_zero($topic_main_aid)){
			redirect('admin/product-topic-main/status/'.md5('no-topic-main-aid'));
			return "";
		}
		
		$this->load->model($this->product_topic_main_model,'product_topic_main');
		$this->product_topic_main->set_where(array("aid"=>$topic_main_aid));
		$topic_main_result = $this->product_topic_main->load_record(false);
		if(!is_var_array($topic_main_result)){
			redirect('admin/product-topic-main/status/'.md5('no-topic-main-aid'));
			return "";
		}
		$this->data["topic_main_result"] = $topic_main_result;
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form(){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/product/product_topic_form';
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this)));
		$master_product_topic = $this->main->load_records(true);
		$this->data["master_product_topic"] = $master_product_topic;
		
		
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
	}
	
	function add(){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "$('#name').focus();";
		$this->form();
	}
	
	function edit($aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$item_detail = $this->main->load_record(false);
		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;
			$this->form();
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific topic.');
			$this->data["js_code"] = "$('#name').focus();";
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
		
		$name = trim($this->input->get_post('name'));
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["name"] = $name;
		$data["parent_aid"] = $this->input->get_post('parent_aid');
		$data["weight"] = $this->input->get_post('weight');
		$data["status"] = $this->input->get_post('status');
		
		if($command == "_insert"){
			if($this->check_duplicate($data,$command)){
				$this->form();
				return "";
			}
			
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$this->log_status('Backend : Insert product topic', '['.$name.'] just added into database.');
				redirect('admin/product-topic/status/'.md5('success'));
			}else{
				$this->log_error('Backend : Insert product topic', 'Command insert_record() fail. Can not insert '.$name);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
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
				$this->form();
				return "";
			}
			
			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if($rs){
				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Admin : Update product topic',  '['.$name.'] has been updated.');
				if($save_option){
					redirect('admin/product-topic/add');
				}else{
					redirect('admin/product-topic/status/'.md5('success'));
				}
				return "";
			}else{
				$this->log_error('Backend : Update product topic', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form();
				return "";
			}
			
		}else{
			$this->log_error('Backend : Product topic', 'Command not found.');
			redirect('admin/product-topic/status/'.md5('no-command'));
			return "";
		}
	}
	
	function check_duplicate($data="",$command=""){
		return false;
		$aid = get_array_value($data,"aid","");
		$name = get_array_value($data,"name","");
		$user_owner_aid = get_array_value($data,"user_owner_aid","");
		
		$this->main->set_where(array("name"=>$name, "user_owner_aid"=>$user_owner_aid));
		if(!is_blank($aid)){
			$this->main->set_where_not_equal(array("aid"=>$aid));
		}
		$objResult = $this->main->load_records(false);
		// echo "<br>sql : ".$this->db->last_query();
		if(is_var_array($objResult)){
			$this->data["message"] = set_message_error('"'.$name.'" is used.');
			$this->data["js_code"] = '$("#name").addClass("error");$("#name").focus();';
			$this->data["command"] = $command;
			$this->data["item_detail"] = $data;
			return true;
		}
		return false;
	}
			
	function ajax_set_status($sid="", $status=""){
		if(!is_owner_admin_or_higher()){
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
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not find this topic.' );
			echo json_encode($result_obj);
			return "";
		}
		$this_name = get_array_value($objResult, 'name', $aid);
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->set_status($status);
		
		if ($rs){
			if($status == 1){
				$this->log_status('Backend : Product topic status', $this_name.'['.$aid.'] was active.');
				$result_obj = array("status" => 'success',"msg" => $this_name.' was active.' );
				echo json_encode($result_obj);
				return "";
			}else{
				$this->log_status('Backend : Product topic status', $this_name.'['.$aid.'] was inactive.');
				$result_obj = array("status" => 'success',"msg" => $this_name.' was inactive.' );
				echo json_encode($result_obj);
				return "";
			}
		}else{
			$this->log_error('Backend : Product topic status', 'Function main->set_status() fail. Can not set status to '.$this_name.'['.$aid.'].');
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not save data.' );
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function ajax_delete_one(){
		if(!is_owner_admin_or_higher()){
			$result_obj = array("status" => 'error',"msg" => 'Permission denied.');
			echo json_encode($result_obj);
			return "";
		}

		$aid = $this->input->post('aid_selected');
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Aid is null.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not find this topic.' );
			echo json_encode($result_obj);
			return "";
		}
		$this_name = get_array_value($objResult, "name", $aid);
		$topic_main_aid = get_array_value($objResult, "topic_main_aid", $aid);
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->delete_records();
		
		$this->main->set_where(array("parent_aid"=>$aid));
		$rs = $this->main->delete_records();
		
		if ($rs){
			$this->log_status('Backend : Delete product topic', $this_name.'['.$aid.'] has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $this_name.' has been deleted.', 'topic_main_aid' => $topic_main_aid );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete product topic', 'Command delete_records() fail. Can not delete '.$this_name.'['.$aid.'].');
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not delete topic.', 'topic_main_aid' => $topic_main_aid );
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
		$optional["total_record"] = $this->main->count_records();
		$optional["page_selected"] = $this->input->get_post('page_selected');
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_PER_PAGE;
		$optional = $this->get_pagination_info($optional);
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_PAGE));			
		
		$search_order_by = $this->input->get_post('search_order_by');
		$order_by_option = $this->get_order_by_info($search_order_by,'weight asc');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Topic Name',"field_order"=>'name',"col_show"=>'name_action',"title_class"=>'w250 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Weight',"field_order"=>'weight',"col_show"=>'weight',"title_class"=>'w50 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Status',"field_order"=>'status',"col_show"=>'status_action',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
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
			$item["name_action"] = '<a href="'.site_url('admin/product-topic/edit/'.get_array_value($item,"aid","")).'">'.get_array_value($item,"name","-").'</a>';
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/accept.png" class="button" title="Click to \'Inctive\' this topic" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/product-topic\', \' inactive '.get_array_value($item,"name","").'\', \'status=0\')">';
			}else{
				$item["status_action"] = '<img src="'.CSS_PATH.'dandelion/images/icons/color/cross.png" class="button" title="Click to \'Active\' this topic" onclick="processChangeValue(\''.get_array_value($item,"aid","").'\', \'admin/product-topic\', \' active '.get_array_value($item,"name","").'\', \'status=1\')">';
			}
			$item["action"] = '';
			if(is_owner_admin_or_higher()){
				$item["action"] .= '<img src="'.CSS_PATH.'dandelion/images/icons/color/bin_closed.png" class="button" title="Click to \'Delete\' this topic" onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/product-topic\', \''.get_array_value($item,"name","").'\')">';
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

	function ajax_get_topic_tree_by_cid($sid="", $topic_main_cid=""){
		if(is_blank($topic_main_cid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Topic main cid is null.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->product_topic_main_model,'product_topic_main');
		$this->product_topic_main->set_where(array("cid"=>$topic_main_cid));
		$topic_main_result = $this->product_topic_main->load_record(false);
		if(!is_var_array($topic_main_result)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Topic main not found.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$topic_main_aid = get_array_value($topic_main_result,"aid","");
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this), "topic_main_aid" => $topic_main_aid));
		$result_list = $this->main->load_records(true);
		
		$result_obj = "";
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$aid = get_array_value($item,"aid","");
				$name = get_array_value($item,"name","");
				$parent_aid = get_array_value($item,"parent_aid","0");
				$state = "";
				
				$result = array();
				$result["id"] = $aid;
				$result["parentId"] = $parent_aid;
				$result["name"] = $name;
				$result["state"] = $state;
				$result_obj[] = $result;
			}
		}
		echo json_encode($result_obj);
	}
	
	function ajax_get_topic_tree($sid="", $topic_main_aid=""){
		if(is_blank($topic_main_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Topic main cid is null.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->product_topic_main_model,'product_topic_main');
		$this->product_topic_main->set_where(array("aid"=>$topic_main_aid));
		$topic_main_result = $this->product_topic_main->load_record(false);
		if(!is_var_array($topic_main_result)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Topic main not found.' );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("user_owner_aid"=>getUserOwnerAid($this), "topic_main_aid" => $topic_main_aid));
		$result_list = $this->main->load_records(true);
		
		$result_obj = "";
		if(is_var_array($result_list)){
			foreach($result_list as $item){
				$aid = get_array_value($item,"aid","");
				$name = get_array_value($item,"name","");
				$parent_aid = get_array_value($item,"parent_aid","0");
				
				$state = "";
				// if($parent_aid == 0){
					// $state = "closed";
				// }
				
				$result = array();
				$result["id"] = $aid;
				$result["parentId"] = $parent_aid;
				$result["name"] = $name;
				$result["state"] = $state;
				$result_obj[] = $result;
			}
		}else{
			$data = array();
			$user_owner_aid = $this->get_user_owner_aid_by_input();
			$data["user_owner_aid"] = $user_owner_aid;
			$data["topic_main_aid"] = $topic_main_aid;
			$data["name"] = "New Topic";
			$data["parent_aid"] = "0";
			$data["weight"] = "0";
			$data["status"] = "1";
			$aid = $this->main->insert_record($data);
			if($aid > 0){
				$result["id"] = $aid;
				$result["parentId"] = get_array_value($data,"parent_aid","0");
				$result["name"] = get_array_value($data,"name","New Topic");
				$result_obj[] = $result;
			}
			
		}
		echo json_encode($result_obj);
	}
	
	function ajax_save_topic_tree_parent_aid($sid=""){
		$this->load->model($this->main_model,'main');
		$data["parent_aid"] = $this->input->post('parent_aid');
		$data_where["aid"] = $this->input->post('aid');
		$this->main->set_where($data_where);
		$rs = $this->main->update_record($data, $data_where);
	}
	
	function ajax_save_topic_tree_name($sid=""){
		$this->load->model($this->main_model,'main');
		$data["name"] = $this->input->post('name');
		$data_where["aid"] = $this->input->post('aid');
		$this->main->set_where($data_where);
		$rs = $this->main->update_record($data, $data_where);
	}
	
	function ajax_add_topic_tree($sid=""){
		$this->load->model($this->main_model,'main');
		$data["parent_aid"] = $this->input->post('parent_aid');
		$data["topic_main_aid"] = $this->input->post('topic_main_aid');
		$data["user_owner_aid"] = getUserOwnerAid($this);
		$data["status"] = '1';
		$data["weight"] = '0';
		$data["name"] = "New Topic";
		$rs = $this->main->insert_record($data);
		$result_obj = array("status" => 'success',"aid" => $rs );
		echo json_encode($result_obj);
		return "";
	}
	
	function exists($rows, $parentId){
		if(is_var_array($rows)){
			for($i=0; $i < count($rows); $i++){
				if ($rows[$i]["id"] == $parentId) return true;
			}
		}
        return false;
    }
    
	function convert($rows){		
		header('Content-type: application/json; charset=utf-8');
		$nodes = array();
		// get the top level nodes
		for($i=0; $i<count($rows); $i++){
			$row = $rows[$i];
			if (!$this->exists($rows, $row["parentId"])){
				$arr = array();
				$arr["id"] = $row["id"];
				$arr["text"] = $row["name"];
				array_push($nodes, $arr);
			}
		}
		
		$toDo = array();
		for($i=0; $i<count($nodes); $i++){
			array_push($toDo, $nodes[$i]);
		}
		while(count($toDo) > 0){
			$node = array_shift($toDo);    // the parent node
			// print_r($node);
			// get the children nodes
			for($i=0; $i<count($rows); $i++){
				$row = $rows[$i];
				if ($row["parentId"] == $node["id"]){
					$arr = array();
					$arr["id"] = $row["id"];
					$arr["text"] = $row["name"];
					
					// print_r($node);
					if (array_key_exists('children', $node)) {
						array_push($node["children"], $arr);
					}else{
						$node["children"][] = $arr;
					}
					array_push($toDo, $arr);
				}
			}
		}
		return $nodes;
	}
	
	
}

?>