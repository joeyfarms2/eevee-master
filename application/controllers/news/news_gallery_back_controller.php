<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class News_gallery_back_controller extends Initcontroller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "backend";
		
		if(CONST_HAS_EVENT != "1"){
			redirect('admin');
		}

		for_staff_or_higher();
		$this->data["mode"] = "backend";
		
		define("thisAdminTabMenu",'news');
		define("thisAdminSubMenu",'gallery');
		@define("folderName",'news/news_back/news');
		
		define("TXT_TITLE",'News management');
		define("TXT_INSERT_TITLE",'News management : Add photo gallery');
		define("TXT_UPDATE_TITLE",'News management : Edit photo gallery');
				
		$this->main_model = 'News_gallery_model';
		$this->news_model = 'News_model';
		
		$this->lang->load('news');
		
	}
	
	function check_exits_news($news_aid="",$return_json=false){
		if(!is_number_no_zero($news_aid)){
			redirect('admin/news/status/'.md5('no-news'));
			return"";
		}
		$this->load->model($this->news_model,'news');
		$this->news->set_where(array("aid"=>$news_aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$item_detail = $this->news->load_record(true);
		if(is_var_array($item_detail)){
			return $item_detail;
		}else{
			if($return_json){
				$result_obj = array("status" => 'error',"msg" => 'Cannot find the specific news.');
				echo json_encode($result_obj);
				return "";
			}else{
				redirect('admin/news/status/'.md5('no-news'));
				return"";
			}
		}
	}
	
	function index(){
		$this->show();
	}
	
	function show($news_aid="", $msg=""){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/news_gallery_list';
		$this->data["header_title"] = TXT_TITLE;
		
		$parent_detail = $this->check_exits_news($news_aid, false);
		$this->data["parent_detail"] = $parent_detail;
		$this->data["header_title"] = TXT_UPDATE_TITLE . " of " .get_array_value($parent_detail,"title","");
		$this->load->view($this->default_theme_admin.'/tpl_admin', $this->data);
	}
	
	function form($news_aid=""){
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_admin . '/' . folderName . '/news_gallery_form';	
		
		$parent_detail = $this->check_exits_news($news_aid, false);
		
		$this->data["header_title"] .= " of " .get_array_value($parent_detail,"title","");
		$this->data["parent_detail"] = $parent_detail;
		$this->load->view($this->default_theme_admin.'/tpl_admin',$this->data);
		
	}
	
	function add($news_aid=""){
		@define("thisAction",'add');
		$this->data["command"] = '_insert';
		$this->data["header_title"] = TXT_INSERT_TITLE;
		$this->data["js_code"] = "";
		$this->form($news_aid);
	}
	
	function edit($news_aid="", $aid=""){
		@define("thisAction",'edit');
		$this->data["command"] = '_update';
		$this->data["header_title"] = TXT_UPDATE_TITLE;
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$gallery_item_detail = $this->main->load_record(true);
		if(is_var_array($gallery_item_detail)){
			$this->data["gallery_item_detail"] = $gallery_item_detail;
			$this->form($news_aid);
		}else{
			$this->data["message"] = set_message_error('Cannot find the specific news gallery.');
			$this->data["js_code"] = "";
			$this->show($news_aid);
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
		
		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$news_aid = trim($this->input->get_post('news_aid'));
		$cid = trim($this->input->get_post('cid'));
		$item_detail = $this->check_exits_news($news_aid, false);
		
		$data["news_aid"] = $news_aid;
		$title = trim($this->input->get_post('title'));
		$data["title"] = $title;
		// $data["description"] = trim($this->input->get_post('description'));
		$data["status"] = trim($this->input->get_post('status'));
		$data["weight"] = trim($this->input->get_post('weight'));
				
		$upload_path = get_array_value($item_detail,"upload_path","");
		
		$upload_base_path = "./".$upload_path."galleries";
		$upload_base_path_db = $upload_path."galleries";
		create_directories($upload_base_path);
		
		if($command == "_update"){
			$aid = $this->input->get_post('aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if(!is_var_array($itemResult)){
				$this->data["message"] = set_message_error("This photo gallery not found.");
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				$this->form($news_aid);
				return "";
			}
		}
		
		// echo '<pre>';
		// print_r($_FILES);
		// echo '</pre>';
		// exit;
		
		if (is_var_array($_FILES)) {
			foreach ($_FILES['image_name']['name'] as $_k => $_file) {
				$new_file_name_thumb = "";
				// if( !is_blank(get_array_value($_file,"image_name","")) && !is_blank(get_array_value($_file["image_name"],"name","")) ){
				if( is_var_array($_FILES['image_name']) && !is_blank($_FILES['image_name']['name'][$_k]) ){
					if($command == "_update"){
						$file_name = get_array_value($itemResult,"file_name","");
						$upload_path = get_array_value($item_detail,"upload_path","");
						
						$old_file = "./".$upload_path."galleries/original/".$file_name;
						if(is_file($old_file)){
							unlink($old_file);
						}
						$old_file = "./".$upload_path."galleries/thumb/".$file_name;
						if(is_file($old_file)){
							unlink($old_file);
						}
					}
					
					//Start upload file
					$image_name = $_FILES['image_name']['name'][$_k]; // $_file["image_name"]["name"][$_k];
					$file_type = substr(strrchr($image_name, "."), 0); // ".jpg"
					
					$new_file_name_thumb = $news_aid.date('YdmHis').get_random_text(4).$file_type;
					$upload_path = $upload_base_path_db."/original";
					create_directories("./".$upload_path);
					$old_file = "./".$upload_path."/".$new_file_name_thumb;
					if(is_file($old_file)){
						unlink($old_file);	
					}
					$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,0,0,800,99,1,$_k);

					$upload_path = $upload_base_path_db."/thumb";
					create_directories("./".$upload_path);
					$old_file = "./".$upload_path."/".$new_file_name_thumb;
					if(is_file($old_file)){
						unlink($old_file);	
					}
					$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,0,0,150,99,1,$_k);
					
					if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
					{
						// echo $result_image_thumb["error_msg"];
						$this->log_status('Admin : Issue', 'Save issue fail => Upload image error : '.$result_image_thumb["error_msg"]);
						// $this->data["message"] = set_message_error("Sorry, the system can not save data now. Please try again or contact your administrator.");
						// $this->data["js_code"] = "$('#vol').focus();";
						// $this->data["command"] = $command;
						// $this->data["item_detail"] = $data;
						// $this->form();
					}	
					$data["file_name"] = $new_file_name_thumb;
				}		
				
				if($command == "_insert"){
					$data["title"] = $_FILES['image_name']['name'][$_k]; // get_array_value($_file,"image_name","");
					$data["status"] = '1';
					$data["weight"] = $_k;
					$data["user_owner_aid"] = $user_owner_aid;
					$aid = $this->main->insert_record($data);
					if($aid){
						$this->log_status('Backend : Insert news gallery', '[title='.$title.'][file ='.$new_file_name_thumb.'] just added into database.');
						// redirect('admin/news/edit/'.$news_aid.'/gallery/status/'.md5('success'));
					}else{
						$this->log_error('Backend : Insert news gallery', 'Command insert_record() fail. Can not insert '.$barcode);
						$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
						$this->data["js_code"] = '';
						$this->data["command"] = $command;
						$this->data["item_detail"] = $data;
						$this->form($news_aid);
						return "";
					}
								
				}else if($command == "_update"){
					$aid = $this->input->get_post('aid');
					$data_where["aid"] = $aid;
					$this->main->set_where($data_where);
					$rs = $this->main->update_record($data, $data_where);
					if($rs){
						$this->data["message"] = set_message_success('Data has been saved.');
						$this->log_status('Backend : Insert news gallery', '[title='.$title.'][file ='.$new_file_name_thumb.'] just added into database.');
						// if($save_option){
						// 	redirect('admin/news/edit/'.$news_aid.'/gallery/add');
						// }else{
						// 	redirect('admin/news/edit/'.$news_aid.'/gallery/status/'.md5('success'));
						// }
						// return "";
					}else{
						$this->log_error('Backend : Update news gallery', 'Command update_record() fail. Can not update [barcode='.$barcode.']');
						$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
						$this->data["js_code"] = '';
						$this->data["command"] = $command;
						$this->data["item_detail"] = $data;
						$this->form($news_aid);
						return "";
					}
					
				}
				// else{
				// 	$this->log_error('Backend : News', 'Command not found.');
				// 	redirect('admin/news/edit/'.$news_aid.'gallery/status/'.md5('no-command'));
				// 	return "";
				// }


			}
		}
		else{
			$this->log_error('Backend : News', 'Command not found.');
			redirect('admin/news/edit/'.$news_aid.'gallery/status/'.md5('no-command'));
			return "";
		}


		// Complete and Redirect 
		if($command == "_insert") {
			redirect('admin/news/edit/'.$news_aid.'/gallery/status/'.md5('success'));
			return "";
		}
		else if($command == "_update") {
			if($save_option){
				redirect('admin/news/edit/'.$news_aid.'/gallery/add');
			}else{
				redirect('admin/news/edit/'.$news_aid.'/gallery/status/'.md5('success'));
			}
			return "";
		}
		else {
			$this->log_error('Backend : News', 'Command not found.');
			redirect('admin/news/edit/'.$news_aid.'gallery/status/'.md5('no-command'));
			return "";
		}

	}
		
	function ajax_set_value($status=""){
		if(!is_staff_or_higher()){
			$result_obj = array("status" => 'error',"msg" => 'Permission denied.');
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

		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(false);
		if(!is_var_array($objResult)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. Can not find this news gallery.' );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = get_array_value($objResult, 'file_name', $aid).' [aid = '.$aid.']';
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->set_value($f_name, $f_value);
		
		if ($rs){
			$this->log_status('Backend : News gallery', 'Set value : "'.$f_name.'" has been set to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_success('Data has been saved.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : News gallery', 'Set value : Function main->set_value() fail. Can not set "'.$f_name.'" to "'.$f_value.'" for '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
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
			$result_obj = array("status" => 'error',"msg" => set_message_error('Error occurred. Aid is null.') );
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->main_model,'main');
		$this->main->set_where(array("aid"=>$aid));
		$objResult = $this->main->load_record(true);
		if(!is_var_array($objResult)){
			$result_obj = array("status" => 'error',"msg" => set_message_error('Error occurred. Can not find this news gallery.') );
			echo json_encode($result_obj);
			return "";
		}
		$this_obj_info = trim(get_array_value($objResult, "file_name", "N/A").' [aid = '.$aid.']');
		$this_obj_file_name = get_array_value($objResult, "file_name", $aid);
		$this_obj_title = (get_array_value($objResult,"title")!="" ? get_array_value($objResult,"title") : get_array_value($objResult,"file_name", $aid));
		$upload_path = get_array_value($objResult,"upload_path","");
		
		$old_file = "./".$upload_path."galleries/original/".$this_obj_file_name;
		if(is_file($old_file)){
			unlink($old_file);
		}
		$old_file = "./".$upload_path."galleries/thumb/".$this_obj_file_name;
		if(is_file($old_file)){
			unlink($old_file);
		}
		
		$this->main->set_where(array("aid"=>$aid));
		$rs = $this->main->delete_records();

		if ($rs){
			$this->log_status('Backend : Delete news gallery', $this_obj_info.' has been deleted.', $objResult);
			$msg = set_message_success($this_obj_title.' has been deleted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}else{
			$this->log_error('Backend : Delete news gallery', 'Command delete_records() fail. Can not delete '.$this_obj_info.'.', $objResult);
			$msg = set_message_error('Sorry, the system can not save data now. Please try again or contact your administrator.');
			$result_obj = array("status" => 'error',"msg" => $msg );
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
		
		$news_aid = $this->input->get_post('news_aid');
		$this->main->set_where(array("news_aid"=>$news_aid));
		
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
		
		$search_news_main = $this->input->get_post('search_news_main');
		$data_where = "";
		if(is_var_array($search_news_main))
		foreach($search_news_main as $item){
			$data_where["news_main_aid"][] = $item;
			$data_search["search_news_main"][] = $item;
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
		$order_by_option = $this->get_order_by_info($search_order_by,'weight ASC');
		$this->main->set_order_by(get_array_value($order_by_option,"order_by_txt",""));
		
		$result_list = $this->main->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		$result_list = $this->manage_column_detail($result_list);
		$this->db->flush_cache();
		$optional["total_in_page"] = count($result_list);
		
		$header_list = array();
		// $header_list[] = array("sort_able"=>'1',"title_show"=>'Code',"field_order"=>'aid',"col_show"=>'cid',"title_class"=>'w30 hcenter',"result_class"=>'hleft');
		$header_list[] = array("sort_able"=>'0',"title_show"=>'Image',"field_order"=>'image_thumb',"col_show"=>'image_thumb',"title_class"=>'w150 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Title',"field_order"=>'title',"col_show"=>'name_action',"title_class"=>'w250 hcenter',"result_class"=>'hcenter');
		$header_list[] = array("sort_able"=>'1',"title_show"=>'Weight',"field_order"=>'weight',"col_show"=>'weight',"title_class"=>'w30 hcenter',"result_class"=>'hcenter');
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
			$news_aid = get_array_value($item,"news_aid","0");
			$file_title_name = (get_array_value($item,"title")!="" ? get_array_value($item,"title") : get_array_value($item,"file_name","Untitled"));
			$item["name_action"] = '<a href="'.site_url('admin/news/edit/'.$news_aid.'/gallery/edit/'.get_array_value($item,"aid","")).'">'.$file_title_name.'</a>';
			
			$item['image_original'] =  '<img src="'.get_image(get_array_value($item,"image_original",""),"small").'" />';
			$item['image_thumb'] =  '<img src="'.get_image(get_array_value($item,"image_thumb",""),"small").'" />';
			
			$status = get_array_value($item,"status","0");
			if($status == 1){
				$item["status_action"] = '<a class="btn btn-success btn-xs" title="Click to \'Inactive\' this photo." onclick="processChangeValue(\' inactive <strong>'.removeAllQuote($file_title_name).'</strong>\', \'admin/news-gallery\', \''.get_array_value($item,"aid","").'\', \'status=0\')"><i class="fa fa-check"></i></a>';
			}else{
				$item["status_action"] = '<a class="btn btn-danger btn-xs" title="Click to \'Active\' this photo." onclick="processChangeValue(\' active <strong>'.removeAllQuote($file_title_name).'</strong>\', \'admin/news-gallery\', \''.get_array_value($item,"aid","").'\', \'status=1\')"><i class="fa fa-ban"></i></a>';
			}

			$item["action"] = '';
			$item["action"] .= '<a class="btn btn-danger btn-xs" title="Click to \'Delete\' this photo." onclick="processDelete(\''.get_array_value($item,"aid","").'\', \'admin/news-gallery\', \'<strong>'.removeAllQuote($file_title_name).'</strong>\')"><i class="fa fa-trash-o "></i></a>';
			
			$result[] = $item;
		}
		
		return $result;
	
	}
	
	function status($news_aid="", $type="")	{
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
		$this->show($news_aid);
	}
	
	function isNewsBarcodeExits($code){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("barcode"=>$code));
		$total = $this->main->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}
	
	function isNewsCidExits($cid){
		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("cid"=>$cid));
		$total = $this->main->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}
	
	
}

?>