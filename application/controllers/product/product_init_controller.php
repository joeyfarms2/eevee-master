<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Product_init_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();

		$this->copy_buyout_model = "Copy_buyout_model";
		$this->log_product_model = "Log_product_model";
		$this->user_section_model = "User_section_model";

		if(CONST_HAS_BASKET == '1'){
			$this->transport_model = "Transport_model";
			$this->load->model($this->transport_model,"transport");
			$this->data["master_transport"] = $this->transport->load_transports();

		}
		$this->lang->load('mail');
		
		$this->load->library('CryptographyAES');
	}
	
	function index(){
			return "";
	}

	function check_exits_product_type($product_type_cid="", $return_json=false){
		if(is_blank($product_type_cid)){
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product type.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-type-not-found'));
				exit(0);
			}
		}
		$this->load->model($this->product_type_model,'product_type');
		$this->product_type->set_where(array("cid"=>$product_type_cid));
		if(!exception_about_status()) $this->product_type->set_where(array("status"=>'1'));
		$item_detail = $this->product_type->load_record(true);
		if(is_var_array($item_detail)){
			return $item_detail;
		}else{
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product type.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-type-not-found'));
				exit(0);
			}
		}
	}
	
	function check_exits_product_type_by_aid($product_type_aid="", $return_json=false){
		if(is_blank($product_type_aid)){
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product type.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-type-not-found'));
				exit(0);
			}
		}
		$this->load->model($this->product_type_model,'product_type');
		$this->product_type->set_where(array("aid"=>$product_type_aid));
		if(!exception_about_status()) $this->product_type->set_where(array("status"=>'1'));
		$item_detail = $this->product_type->load_record(true);
		if(is_var_array($item_detail)){
			return $item_detail;
		}else{
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product type.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-type-not-found'));
				exit(0);
			}
		}
	}
	
	function check_exits_product_main_by_aid($product_main_aid="", $return_json=false){
		if(is_blank($product_main_aid)){
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product main.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-main-not-found'));
				exit(0);
			}
		}
		$this->load->model($this->product_main_model,'product_main');
		$this->product_main->set_where(array("aid"=>$product_main_aid));
		if(!exception_about_status()) $this->product_main->set_where(array("status"=>'1'));
		$item_detail = $this->product_main->load_record(true);
		if(is_var_array($item_detail)){
			return $item_detail;
		}else{
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product main.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-main-not-found'));
				exit(0);
			}
		}
	}
	
	function check_exits_product_main_by_url($product_main_url="", $return_json=false){
		if(is_blank($product_main_url)){
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product main.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-main-not-found'));
				exit(0);
			}
		}
		$this->load->model($this->product_main_model,'product_main');
		$this->product_main->set_where(array("url"=>$product_main_url));
		if(!exception_about_status()) $this->product_main->set_where(array("status"=>'1'));
		$item_detail = $this->product_main->load_record(true);
		if(is_var_array($item_detail)){
			return $item_detail;
		}else{
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this product main.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				exit(0);
			}else{
				redirect('home/status/'.md5('product-main-not-found'));
				exit(0);
			}
		}
	}

	function check_exits_magazine_main($product_main_url="", $magazine_main_aid="",$return_json=false,$mode="backend"){
		if(!is_number_no_zero($magazine_main_aid)){
			if($return_json){
				$msg_txt = 'Error occurred. Can not find this magazine.';
				$msg = set_message_error($msg_txt);
				$result_obj = array("status" => 'error',"msg" => $msg, "msg_txt" => $msg_txt );
				echo json_encode($result_obj);
				exit(0);
			}else{
				if($mode == "frontend"){
					redirect('home/status/'.md5('product-not-found'));
					return"";
				}else{
					redirect('admin/product-'.$product_main_url.'/magazine-main/status/'.md5('no-magazine'));
					return"";
				}
			}
		}
		$this->load->model($this->magazine_main_model,'magazine_main');
		$this->magazine_main->set_where(array("aid"=>$magazine_main_aid, "user_owner_aid"=>getUserOwnerAid($this)));
		if(!exception_about_status()) $this->magazine_main->set_where(array("status"=>'1'));
		$magazine_main_detail = $this->magazine_main->load_record(true);
		if(is_var_array($magazine_main_detail)){
			return $magazine_main_detail;
		}else{
			if($return_json){
				$msg_txt = 'Error occurred. Can not find this magazine.';
				$msg = set_message_error($msg_txt);
				$result_obj = array("status" => 'error',"msg" => $msg, "msg_txt" => $msg_txt );
				echo json_encode($result_obj);
				exit(0);
			}else{
				if($mode == "frontend"){
					redirect('home/status/'.md5('product-not-found'));
					return"";
				}else{
					redirect('admin/product-'.$product_main_url.'/magazine-main/status/'.md5('no-magazine'));
					return"";
				}
			}
		}
	}
		
	function save_field($product_type_aid="",$product_main_aid="",$aid=""){
		for_staff_or_higher();
		$model = $this->get_product_model($product_type_aid);
		$copy_model = get_array_value($model,"product_copy_model","");
		$field_model = get_array_value($model,"product_field_model","");

		$result_obj = "";
		if($product_type_aid > 0 && $product_main_aid > 0 && $aid >0){
			$this->load->model($this->product_main_field_model,"product_main_field");
			$this->product_main_field->set_where(array("product_main_aid"=>$product_main_aid, "status"=>"1"));
			$product_main_field_result = $this->product_main_field->load_records(false);
			$no_1 = "";
			$no_2 = "";
			$no_3 = "";
			$total_page = 0;
			
			if(is_var_array($product_main_field_result)){
				$i=0;
				foreach($product_main_field_result as $result){
					$i++;
					$product_main_field_aid = get_array_value($result,"aid","");
					$cid = get_array_value($result,"cid","");
					$tag = get_array_value($result,"tag","");
					$field_data = trim($this->input->get_post('field_'.$product_main_field_aid));

					if($cid == 'title'){
						if(!is_blank($field_data)){
							$result_obj["title"] = $field_data;
						}
					}
					if($cid == 'author'){
						if(!is_blank($field_data)){
							$result_obj["author"] = $field_data;
						}
					}
					if($cid == 'no_1'){
						if(!is_blank($field_data)){
							$no_1 = $field_data;
						}
					}
					if($cid == 'no_2'){
						if(!is_blank($field_data)){
							$no_2 = $field_data;
						}
					}
					if($cid == 'no_3'){
						if(!is_blank($field_data)){
							$no_3 = $field_data;
						}
					}
					if($cid == 'total_page'){
						if(!is_blank($field_data)){
							$total_page = $field_data;
							$point = ceil($total_page/CONST_REWARD_POINT);
						}
					}

					$this->load->model($field_model,"field");
					$data = array();
					$data["parent_aid"] = $aid;
					$data["sequence"] = $this->field->get_sequence_from_parent_aid($aid);
					$data["product_type_aid"] = $product_type_aid;
					$data["product_main_field_aid"] = $product_main_field_aid;
					$data["user_owner_aid"] = $this->get_user_owner_aid_by_input();
					$data["tag"] = $tag;
					$data["subfield_cd"] = get_array_value($result,"subfield_cd","");
					$data["name"] = get_array_value($result,"name","");
					$data["ind1_cd"] = "";
					$data["ind2_cd"] = "";
					$data["field_data"] = $field_data;
					$this->load->model($field_model,"field");
					$result = $this->field->insert_record($data);
				}
			}


			$this->load->model($copy_model,"copy");
			$this->copy->set_where(array('parent_aid' => $aid));
			$copy_result = $this->copy->load_records(false);
			if(is_var_array($copy_result)){
				foreach ($copy_result as $item) {
					$call_number = "";
					$copy_aid = get_array_value($item,"aid","");
					$no_4 = get_array_value($item,"no_4","");
					if(!is_blank($no_1)){
						$call_number = trim($no_1);
					}
					if(!is_blank($no_2)){
						$call_number = trim($call_number." ".trim($no_2));
					}
					if(!is_blank($no_3)){
						$call_number = trim($call_number." ".trim($no_3));
					}
					if(!is_blank($no_4)){
						$call_number = trim($call_number." ".trim($no_4));
					}
					$tmp = array();
					$tmp["no_1"] = $no_1;
					$tmp["no_2"] = $no_2;
					$tmp["no_3"] = $no_3;
					$tmp["no_4"] = $no_4;
					$tmp["call_number"] = $call_number;
					$this->load->model($copy_model,"copy");
					$this->copy->set_where(array('aid' => $copy_aid));
					$rs = $this->copy->update_record($tmp);
				}
			}
		}
		return $result_obj;
	}
	
	function save_category($product_type_aid="",$aid="",$category=""){
		for_staff_or_higher();
		$model = $this->get_product_model($product_type_aid);
		$copy_model = get_array_value($model,"product_copy_model","");
		$ref_product_category_model = get_array_value($model,"product_ref_product_category_model","");

		if(is_blank($category)){
			return "";
		}

		if(is_var_array($category)){
			foreach($category as $item){
				$item = trim($item);
				if(!is_blank($item)){
					$this->load->model($ref_product_category_model, "ref_category");
					$data = array();
					$data["parent_aid"] = $aid;
					$data["product_category_aid"] = $item;
					$data["product_type_aid"] = $product_type_aid;
					$data["user_owner_aid"] = $this->get_user_owner_aid_by_input();
					$this->ref_category->insert_or_update($data);
				}
			}
		}
	}
	
	function save_tag($product_type_aid="",$aid="",$tag=""){
		for_staff_or_higher();
		$model = $this->get_product_model($product_type_aid);
		$copy_model = get_array_value($model,"product_copy_model","");
		$tag_model = get_array_value($model,"product_tag_model","");

		if(is_blank($tag)){
			return "";
		}

		if(is_var_array($tag)){
			foreach($tag as $item){
				$item = trim($item);
				if(!is_blank($item)){
					$this->load->model($tag_model, "tag");
					$data = array();
					$data["parent_aid"] = $aid;
					$data["product_type_aid"] = $product_type_aid;
					$data["user_owner_aid"] = $this->get_user_owner_aid_by_input();
					$data["tag"] = $item;
					$this->tag->insert_or_update($data);					
				}
			}
		}
	}
	
	function upload_cover_image($aid, $data, $path){
		//echo "upload image <br />";
		$cover_image_path = $path;
		$upload_base_path = "./".$path;
		create_directories($upload_base_path);
		
		$cid = get_text_pad($aid,"0",CONST_ZERO_PAD_FOR_PRODUCT);
		$cover_image_path = $cover_image_path.'/'.$cid.'/';
		
		$file_type = "";
		if( !is_blank(get_array_value($_FILES,"cover_image","")) && !is_blank(get_array_value($_FILES["cover_image"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'/'.$cid.'/cover_image';
			$image_name = $_FILES["cover_image"]["name"];
			$file_type = substr(strrchr($image_name, "."), 0);
			
			$new_file_name_thumb = $cid."-ori".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,0,1024,99,1);

			$new_file_name_thumb = $cid."-thumb".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,0,250,99,1);

			$new_file_name_thumb = $cid."-cover".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,0,450,99,1);

			$new_file_name_thumb = $cid."-related".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,0,200,99,1);

			$new_file_name_thumb = $cid."-ipad".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,0,824,99,1);

			$new_file_name_thumb = $cid."-small".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,0,150,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$data["error_msg"] = $result_image_thumb["error_msg"];
				// $this->log_status('Admin : Book', 'Save book fail => Upload image error : '.$result_image_thumb["error_msg"]);
			}	
			$data["save_image"] = "1";
			$data["upload_path"] = $cover_image_path;
			$data["cover_image_file_type"] = $file_type;
		}		
		return $data;
	}

	function upload_cover_image_vdo($aid, $data, $path){
		$cover_image_path = $path;
		$upload_base_path = "./".$path;
		create_directories($upload_base_path);
		
		$cid = get_text_pad($aid,"0",CONST_ZERO_PAD_FOR_PRODUCT);
		$cover_image_path = $cover_image_path.'/'.$cid.'/';
		
		$file_type = "";
		if( !is_blank(get_array_value($_FILES,"cover_image","")) && !is_blank(get_array_value($_FILES["cover_image"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'/'.$cid.'/cover_image';
			$image_name = $_FILES["cover_image"]["name"];
			$file_type = substr(strrchr($image_name, "."), 0);
			
			$new_file_name_thumb = $cid."-ori".$file_type;
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
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,260,0,99,1);

			$new_file_name_thumb = $cid."-cover".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,450,0,99,1);

			$new_file_name_thumb = $cid."-related".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,180,0,99,1);

			$new_file_name_thumb = $cid."-ipad".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,0,824,99,1);

			$new_file_name_thumb = $cid."-small".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_IMAGE,150,0,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$data["error_msg"] = $result_image_thumb["error_msg"];
				// $this->log_status('Admin : Book', 'Save book fail => Upload image error : '.$result_image_thumb["error_msg"]);
			}	
			$data["save_image"] = "1";
			$data["upload_path"] = $cover_image_path;
			$data["cover_image_file_type"] = $file_type;
		}		
		return $data;
	}

	function upload_parent_file($aid, $data, $path){
		$upload_path = $path;
		$upload_base_path = "./".$path;
		create_directories($upload_base_path);
		
		$cid = get_text_pad($aid,"0",CONST_ZERO_PAD_FOR_PRODUCT);
		$product_upload_path = $upload_path.'/'.$cid.'/';
		

		if( !is_blank(get_array_value($_FILES,"file_upload","")) && !is_blank(get_array_value($_FILES["file_upload"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'/'.$cid.'/file';
			$file_name = $_FILES["file_upload"]["name"];
			$file_type = substr(strrchr($file_name, "."), 0);
			
			$new_file_name = $cid.$file_type;
			$old_file = "./".$upload_path."/".$new_file_name;

			if(!is_blank($upload_path) && strlen($upload_path) > 8){
				$all_files = glob($upload_path."/*.*");
				if(is_var_array($all_files)){
					foreach ($all_files as $filename) {
						unlink($filename);
					}
				}
			}

			$result_file_upload = upload_file($this,"file_upload",$upload_path,$new_file_name,CONST_ALLOW_FILE_TYPE_FOR_VDO,CONST_ALLOW_FILE_SIZE_FOR_VDO,'','');
			$data["file_upload"] = $new_file_name;
			if ( !is_blank(get_array_value($result_file_upload,"error_msg","")) )
			{
				// echo $result_file_upload["error_msg"];
				// $this->log_error('Admin : Product', 'Save vdo fail => Upload file error : '.$result_file_upload["error_msg"]);
				$data["error_msg"] = get_array_value($result_file_upload,"error_msg","");
			}
			$data["upload_path"] = $product_upload_path;
			$data["uri"] = $new_file_name;
		}	
		return $data;
	}

	function log_product_save($product_type_aid="", $parent_aid="", $title="",$action="", $description="", $data_arr="", $flag="", $status="", $user_aid="", $user_owner_aid="", $user_owner_detail=""){
		$data="";
		$data["user_aid"] = (!is_blank($user_aid)) ? $user_aid : getUserLoginAid($this);
		$data["product_type_aid"] = $product_type_aid;
		$data["parent_aid"] = $parent_aid;
		$data["title"] = $title;
		$data["action"] = $action;
		$data["description"] = $description;
		$data["data"] = serialize($data_arr);
		$data["flag"] = $flag;
		$data["status"] = (!is_blank($status)) ? $status : '1';
		$data["user_owner_aid"] = (!is_blank($user_owner_aid)) ? $user_owner_aid : getUserOwnerAid($this);
		$data["user_owner_detail"] = (!is_blank($user_owner_detail)) ? $user_owner_detail : getUserOwnerDetailForLog($this);
		$data["ip"] = $this->input->ip_address();
		if($this->agent->is_mobile()){
			$data["browser"] = $this->agent->mobile().'/'.$this->agent->browser().' '.$this->agent->version();
		}else{
			$data["browser"] = $this->agent->platform().'/'.$this->agent->browser().' '.$this->agent->version();
		}
		$data["browser_detail"] = $this->agent->agent_string();
		
		$log_product =& get_instance();
		$log_product->load->model($this->log_product_model,"log_product");
		$log_product->log_product->insert_record($data);
	}

	function update_all_parents(){
		echo "Start update all parent.";
		$this->load->model($this->book_model,"book");
		$all = $this->book->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$result = $this->book->update_parent($aid);
				echo "Update '$title' : $result <BR>";
			}
			echo "<HR>";
		}

		$this->load->model($this->magazine_model,"magazine");
		$all = $this->magazine->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$result = $this->magazine->update_parent($aid);
				echo "Update '$title' : $result <BR>";
			}
			echo "<HR>";
		}

		$this->load->model($this->vdo_model,"vdo");
		$all = $this->vdo->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$result = $this->vdo->update_parent($aid);
				echo "Update '$title' : $result <BR>";
			}
			echo "<HR>";
		}

		$this->load->model($this->others_model,"others");
		$all = $this->others->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$result = $this->others->update_parent($aid);
				echo "Update '$title' : $result <BR>";
			}
			echo "<HR>";
		}
	}

	function update_all_reward_point(){
		echo "Start update reward point.";
		$this->load->model($this->book_model,"book");
		$all = $this->book->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$this->load->model($this->book_field_model,"book_field");
				$this->book_field->set_where(array("parent_aid"=>$aid, "tag"=>"300"));
				$field = $this->book_field->load_record(false);
				$page = get_array_value($field,"field_data","0");
				$point = ceil($page/CONST_REWARD_POINT);
				echo "'$title' has $page page(s) : point = $point <BR>";
				// echo "Update '$title' : $result <BR>";

				$data = array();
				$data["reward_point"] = $point;
				$this->load->model($this->book_model,"book");
				$data_where = array();
				$data_where["aid"] = $aid;
				$this->book->set_where($data_where);
				$this->book->update_record($data,$data_where);
			}
			echo "<HR>";
		}

		$this->load->model($this->magazine_model,"magazine");
		$all = $this->magazine->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$this->load->model($this->magazine_field_model,"magazine_field");
				$this->magazine_field->set_where(array("parent_aid"=>$aid, "tag"=>"300"));
				$field = $this->magazine_field->load_record(false);
				$page = get_array_value($field,"field_data","0");
				$point = ceil($page/CONST_REWARD_POINT);
				echo "'$title' has $page page(s) : point = $point <BR>";
				// echo "Update '$title' : $result <BR>";

				$data = array();
				$data["reward_point"] = $point;
				$this->load->model($this->magazine_model,"magazine");
				$data_where = array();
				$data_where["aid"] = $aid;
				$this->magazine->set_where($data_where);
				$this->magazine->update_record($data,$data_where);
			}
			echo "<HR>";
		}

		$this->load->model($this->vdo_model,"vdo");
		$all = $this->vdo->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$this->load->model($this->vdo_field_model,"vdo_field");
				$this->vdo_field->set_where(array("parent_aid"=>$aid, "tag"=>"300"));
				$field = $this->vdo_field->load_record(false);
				$page = get_array_value($field,"field_data","0");
				$point = ceil($page/CONST_REWARD_POINT);
				echo "'$title' has $page page(s) : point = $point <BR>";
				// echo "Update '$title' : $result <BR>";

				$data = array();
				$data["reward_point"] = $point;
				$this->load->model($this->vdo_model,"vdo");
				$data_where = array();
				$data_where["aid"] = $aid;
				$this->vdo->set_where($data_where);
				$this->vdo->update_record($data,$data_where);
			}
			echo "<HR>";
		}

		$this->load->model($this->others_model,"others");
		$all = $this->others->load_records(false);
		if(is_var_array($all)){
			foreach ($all as $item) {
				$aid = get_array_value($item,"aid","0");
				$title = get_array_value($item,"title","N/A");
				$this->load->model($this->others_field_model,"others_field");
				$this->others_field->set_where(array("parent_aid"=>$aid, "tag"=>"300"));
				$field = $this->others_field->load_record(false);
				$page = get_array_value($field,"field_data","0");
				$point = ceil($page/CONST_REWARD_POINT);
				echo "'$title' has $page page(s) : point = $point <BR>";
				// echo "Update '$title' : $result <BR>";

				$data = array();
				$data["reward_point"] = $point;
				$this->load->model($this->others_model,"others");
				$data_where = array();
				$data_where["aid"] = $aid;
				$this->others->set_where($data_where);
				$this->others->update_record($data,$data_where);
			}
			echo "<HR>";
		}
	}

	function update_all_product_view(){
		$this->load->model($this->view_all_products,"vap_for_view");
		$this->vap_for_view->update_data();
	}


	function update_all_view_save(){

		$this->load->model($this->view_all_products,"vap_for_view");
		$this->vap_for_view->update_data_save();

		$this->load->model($this->view_all_download_history,"var_for_view");
		$this->var_for_view->update_data_save();

		$this->load->model($this->view_all_reserve,"vad_for_view");
		$this->vad_for_view->update_data_save();
		
		// $this->load->model($this->view_all_products_model,"vap_for_view");
		// $this->vap_for_view->update_data_save();

		// $this->load->model($this->view_all_reserve_model,"var_for_view");
		// $this->var_for_view->update_data_save();

		// $this->load->model($this->view_all_download_history_model,"vad_for_view");
		// $this->vad_for_view->update_data_save();
	}


	function get_data_for_copy_form(){
		if(CONST_HAS_TRANSACTION == "1"){
			$this->load->model($this->user_section_model,"user_section");
			$this->data["master_user_section"] = $this->user_section->load_records_array(false, "aid", "");
		}
	}

	function ajax_save_reserve_product($sid="" , $product_type_cid="", $copy_aid="", $type="digital"){
		@define("thisAction","ajax_save_reserve_product");
		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		if(!is_login()){
			$result_obj = array("status" => "error","msg" => "Permission denied.");
			echo json_encode($result_obj);
			return"";
		}

		if(is_blank($copy_aid)){
			$result_obj = array("status" => "error","msg" => "No copy selected.");
			echo json_encode($result_obj);
			return"";
		}

		$model = $this->get_product_model($product_type_aid);
		$this->db->flush_cache();
		$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
		$tmp = array();
		$tmp["aid"] = $copy_aid;
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->product_copy->set_where($tmp);
		$item_result = $this->product_copy->load_record(true);
		if(!is_var_array($item_result)){
			$this->log_notice('Product : Reserve ['.$type.']', getUserLoginNameForLog($this).' try to reserve [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' but no product found or product status=0.');
			$result_obj = array("status" => "error","msg" => "No product found.");
			echo json_encode($result_obj);
			return"";
		}

		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["copy_aid"] = $copy_aid;
		$tmp["user_aid"] = getSessionUserAid();
		$tmp["status"] = '1';
		if($type == "product"){
			$this->load->model($this->reserve_product_model,"reserve");
		}else{
			$this->load->model($this->reserve_model,"reserve");
		}
		$this->reserve->set_where($tmp);
		$queue_result = $this->reserve->load_record(true);
		if(is_var_array($queue_result)){
			$result_obj = array("status" => "warning","msg" => "You already reserve this book.");
			echo json_encode($result_obj);
			return"";
		}else{
			$parent_title = get_array_value($item_result,"parent_title","");
			$copy_title = get_array_value($item_result,"copy_title","");
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["copy_aid"] = $copy_aid;
			$tmp["parent_aid"] = get_array_value($item_result,"parent_aid","0");
			$tmp["title"] = trim($parent_title." ".$copy_title);
			$tmp["barcode"] = get_array_value($item_result,"barcode","0");
			$tmp["user_aid"] = getSessionUserAid();
			$tmp["status"] = '1';
			$this->db->flush_cache();
			if($type == "product"){
				$this->load->model($this->reserve_product_model,"reserve");
			}else{
				$this->load->model($this->reserve_model,"reserve");
			}
			$aid = $this->reserve->insert_record($tmp);
			// echo "<br>sql : ".$this->db->last_query()."<br>";
			// echo "aid = $aid";
			// if($aid > 0){
				$this->log_status('Product : Reserve ['.$type.']', 'Success to add ['.get_array_value($item_result,"title","").'] in queue for ['.getSessionUserAid().'].', $tmp);

				$product_list = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
				$product_list .= "<tr><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Code</td><td style='border-bottom:1px solid #868A9C; font-weight:bold'>Title</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Request Date</td><td style='border-bottom:1px solid #868A9C; font-weight:bold' width='20%'>Status</td></tr>";
				$class = "background-color:D1DCF1";
				$product_list .= "<tr style='".$class."'><td style='border-bottom:1px solid #868A9C'>".get_array_value($item_result,"barcode","0")."</td><td style='border-bottom:1px solid #868A9C'>".trim($parent_title." ".$copy_title)."</td><td style='border-bottom:1px solid #868A9C'>".get_datetime_pattern("dmy_EN_SHORT", date('Y-m-d'), "-") ."</td><td style='border-bottom:1px solid #868A9C'>Pending for Approval</td></tr>";
				$product_list .= "</table>";

				$email = getUserLoginEmail($this);
				$subject = $this->lang->line('mail_subject_reserve_product_request');
				$body = $this->lang->line('mail_content_reserve_product_request');
				$body = str_replace("{doc_type}", "&nbsp;" , $body);
				$body = str_replace("{email}", $email , $body);
				$body = str_replace("{name}", trim(getUserLoginFullName($this)) , $body);
				$body = str_replace("{product_list}", $product_list , $body);
				$this->load->library('email');
				$config = $this->get_init_email_config();
				if(is_var_array($config)){ 
					$this->email->initialize($config); 
					$this->email->set_newline("\r\n");
				}
				
				// Send message
				$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
				$this->email->to($email);
				$this->email->bcc(''); 

				$this->email->subject($subject);
				$this->email->message($body);
				$log_arr["subject"] = $subject;
				$log_arr["body"] = $body;
				$this->log_debug('Reservation', 'Send mail to ['.$email.'] ', $log_arr);
				// echo $this->email->print_debugger();
				@$this->email->send();

				$result_obj = array("status" => "success","msg" => "");
				echo json_encode($result_obj);
				return"";
			// }else{
			// 	$this->log_error('Product : Reserve', 'Fail to add ['.get_array_value($item_result,"title","").'] in queue for ['.getSessionUserAid().'].', $tmp);
			// 	$result_obj = array("status" => "error","msg" => "Error occured.");
			// 	echo json_encode($result_obj);
			// 	return"";
			// }
		}
	}
	
	function ajax_cancel_reserve_product($sid="" , $product_type_cid="", $copy_aid="", $type="digital"){
		@define("thisAction","ajax_cancel_reserve_product");
		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		if(!is_login()){
			$result_obj = array("status" => "error","msg" => "Permission denied.");
			echo json_encode($result_obj);
			return"";
		}

		if(is_blank($copy_aid)){
			$result_obj = array("status" => "error","msg" => "No copy selected.");
			echo json_encode($result_obj);
			return"";
		}

		$model = $this->get_product_model($product_type_aid);
		$this->db->flush_cache();
		$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
		$tmp = array();
		$tmp["aid"] = $copy_aid;
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->product_copy->set_where($tmp);
		$item_result = $this->product_copy->load_record(true);
		if(!is_var_array($item_result)){
			$this->log_notice('Product : Cancel Reserve ['.$type.']', getUserLoginNameForLog($this).' try to cancel reserve [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' but no product found or product status=0.');
			$result_obj = array("status" => "error","msg" => "No product found.");
			echo json_encode($result_obj);
			return"";
		}

		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["copy_aid"] = $copy_aid;
		$tmp["user_aid"] = getSessionUserAid();
		$tmp["status"] = "1";
		if($type == "product"){
			$this->load->model($this->reserve_product_model,"reserve");
		}else{
			$this->load->model($this->reserve_model,"reserve");
		}
		$this->reserve->set_where($tmp);
		$queue_result = $this->reserve->load_record(true);
		if(!is_var_array($queue_result)){
			$result_obj = array("status" => "warning","msg" => "You did not reserve this book.");
			echo json_encode($result_obj);
			return"";
		}else{
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["copy_aid"] = $copy_aid;
			$tmp["status"] = "1";
			$tmp["user_aid"] = getSessionUserAid();
			$this->db->flush_cache();
			if($type == "product"){
				$this->load->model($this->reserve_product_model,"reserve");
			}else{
				$this->load->model($this->reserve_model,"reserve");
			}
			$this->reserve->set_where($tmp);
			$cond = array();
			$cond["status"] = "0";
			$result = $this->reserve->update_record($cond);
			// echo "<br>sql : ".$this->db->last_query()."<br>";
			// echo "aid = $aid";
			// if($aid > 0){
				/*
				$this->log_status('Product : Cancel Reserve ['.$type.']', 'Success to cencel ['.get_array_value($item_result,"title","").'] from queue for ['.getSessionUserAid().'].', $tmp);
				$email = getUserLoginEmail($this);
				$subject = $this->lang->line('mail_subject_reserve_product_cancel');
				$body = $this->lang->line('mail_content_reserve_product_cancel');
				$body = str_replace("{doc_type}", "&nbsp;" , $body);
				$body = str_replace("{email}", $email , $body);
				$body = str_replace("{name}", trim(getUserLoginFullName($this)) , $body);
				$body = str_replace("{title}", trim($parent_title." ".$copy_title) , $body);
				$this->load->library('email');
				$config = $this->get_init_email_config();
				if(is_var_array($config)){ 
					$this->email->initialize($config);
					$this->email->set_newline("\r\n");
				 }
				
				// Send message
				$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
				$this->email->to($email);
				// $this->email->bcc(''); 

				$this->email->subject($subject);
				$this->email->message($body);
				$log_arr["subject"] = $subject;
				$log_arr["body"] = $body;
				$this->log_debug('Reservation', 'Send mail to ['.$email.'] ', $log_arr);
				// echo $this->email->print_debugger();
				@$this->email->send();
				*/
				$result_obj = array("status" => "success","msg" => "");
				echo json_encode($result_obj);
				return"";
			// }else{
			// 	$this->log_error('Product : Reserve', 'Fail to add ['.get_array_value($item_result,"title","").'] in queue for ['.getSessionUserAid().'].', $tmp);
			// 	$result_obj = array("status" => "error","msg" => "Error occured.");
			// 	echo json_encode($result_obj);
			// 	return"";
			// }
		}
	}
	
	function ajax_generate_file($sid=""){
		
		$product_type_aid = $this->input->get_post('product_type_aid');
		$copy_aid = $this->input->get_post('copy_aid');
		if(is_blank($product_type_aid)){
			$msg = ('Error occurred. Product type aid is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		if(is_blank($copy_aid)){
			$msg = ('Error occurred. Copy aid is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_copy_name, $model_copy_name);
		$tmp = array();
		$tmp['aid'] = $copy_aid;
		$this->{$model_copy_name}->set_where($tmp);
		$copy_result = $this->{$model_copy_name}->load_record(true);

		if(!is_var_array($copy_result)){
			$msg = ('Error occurred. Copy not found.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		$upload_path = get_array_value($copy_result,"upload_path","");
		// echo $upload_path."<BR>";
		if(is_var_array(glob($upload_path."ftp/*.*")))
		{
			$tmp_upload_path = $upload_path."app";

			foreach (glob($upload_path."ftp/*.*") as $ftpfilename)
			{
				//echo "ftpfilename = $ftpfilename <br>";
				$digital_file_name = basename(strtolower($ftpfilename));
				$digital_file_type = strtolower(substr(strrchr($ftpfilename, "."), 1));
				//echo "digital_file_name = $digital_file_name <br>";
				//echo "digital_file_type = $digital_file_type <br>";
 
				$tmp_upload_path = $upload_path."tmp/";
				deleteDir($tmp_upload_path);
				if(!is_dir($tmp_upload_path)){
					mkdir($tmp_upload_path);
				}
				$gen_secret_key = get_secret_key($copy_result); 
				$gen_iv = get_iv($copy_result); 
				//echo "<BR>gen_secret_key = $gen_secret_key<BR>gen_iv = $gen_iv<BR>product_type_aid = $product_type_aid<BR>";

				$secret_key = get_array_value($copy_result,"secret_key", $gen_secret_key);
				$iv = get_array_value($copy_result,"iv", $gen_iv);
				//echo "<BR>secret_key = $secret_key<BR>iv = $iv<BR>";
				//echo "absolute path =". getcwd(). "<br />";
				
				$pdf_file = getcwd()."/".$ftpfilename;
				$output_file = getcwd()."/".$upload_path."tmp/pdf-%04d.pdf";
				//$string_command = '/usr/local/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sPDFPassword='.PDF_PASSWORD.' -sOutputFile='.$output_file.' '.$pdf_file;
				//$string_command = '/usr/local/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile='.$output_file.' '.$pdf_file;
				$string_command = '/usr/local/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile='.$output_file.' '.$pdf_file;
				//$string_command = 'gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -sOutputFile='.$output_file.' '.$pdf_file;
               
               	exec($string_command);
				if(self::AES_Encryption(getcwd()."/".$upload_path, $secret_key, $iv))
                {
                	// $this->db->flush_cache();
// 					$this->db->_reset_select();
// 					$this->load->model($model_copy_name, $model_copy_name);
// 					$tmp = array();
// 					$tmp['aid'] = $copy_aid;
// 					$this->{$model_copy_name}->set_where($tmp);
// 					$obj = array();
// 					//$obj["digital_file_type"] = $digital_file_type;
// 					$result = $this->{$model_copy_name}->update_record($obj);
					
					
					deleteDir($tmp_upload_path);
					//
					//echo "1. <hr />";
					$objAES = new CryptographyAES();
					//echo "2. <hr />";
					$destination = "./".$upload_path."app/pdf.pdf";
					//echo "destination : ".$destination;
					//echo "<br />";
					//echo "pdf_file : ".$pdf_file;
					//echo "<br />";
					//echo "secret_key : ".$secret_key;
					//echo "<br />";
					//echo "iv : ".$iv;
    				if($objAES->Encrypt($pdf_file, $destination, $secret_key, $iv))
    				{
    					//echo "3. <hr />";
    					$cid = get_array_value($copy_result,"cid","");
    					$is_license = get_array_value($copy_result,"is_license",0);
    					if($is_license == 1)
    					{
    						unlink($pdf_file);
    					}
    					else{
    						rename($pdf_file, "./".$upload_path."app/".$cid.".pdf");
    						chmod("./".$upload_path."app/".$cid.".pdf",0644);
    					}
    					//rename($pdf_file, "./".$upload_path."app/".$cid.".pdf");
    				}
    				//echo "4. <hr />";
                }
			}
			$msg = ('All files are encyted.');
			$result_obj = array("status" => 'success',"msg" => $msg);
			echo json_encode($result_obj);
			return "";

		}else{
			$msg = ('File not found.<BR><BR>Please upload file to this path : '. $upload_path . ' then hit button again.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}

	}
	
	
	function ajax_generate_update_file($sid=""){
		$product_type_aid = $this->input->get_post('product_type_aid');
		$copy_aid = $this->input->get_post('copy_aid');
		if(is_blank($product_type_aid)){
			$msg = ('Error occurred. Product type aid is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		if(is_blank($copy_aid)){
			$msg = ('Error occurred. Copy aid is null.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_copy_name, $model_copy_name);
		$tmp = array();
		$tmp['aid'] = $copy_aid;
		$this->{$model_copy_name}->set_where($tmp);
		$copy_result = $this->{$model_copy_name}->load_record(true);

		if(!is_var_array($copy_result)){
			$msg = ('Error occurred. Copy not found.');
			$result_obj = array("status" => 'error',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		}
		
		$upload_path = get_array_value($copy_result,"upload_path","");
		
		// if(is_var_array(glob($upload_path."file/*.*")))
// 		{
			// $tmp_upload_path = $upload_path."app";
// 			if(is_var_array(glob($tmp_upload_path."/*.*")))
// 			{
// 				foreach (glob($tmp_upload_path."/*.*") as $filename)
// 				{
// 					// echo "filename = $filename<BR>";
// 					unlink($filename);
// 				}
// 			}
			
			$gen_secret_key = get_secret_key($copy_result); 
			$gen_iv = get_iv($copy_result); 

			$secret_key = get_array_value($copy_result,"secret_key", $gen_secret_key);
			$iv = get_array_value($copy_result,"iv", $gen_iv);
				
			$objAES = new CryptographyAES();
			$objScan = scandir("./".$upload_path."file/");
			
			foreach ($objScan as $value) 
    		{
    			if(strpos($value, "df-") == 1)
    			{
    				if(file_exists("./".$upload_path."file/".$value))
    				{
    					$destination = "./".$upload_path."app/encrypt_".$value;   //$path.str_replace("decrypt","encrypt",$value);
    					if($objAES->Encrypt("./".$upload_path."file/".$value, $destination, $secret_key, $iv))
    					{
    						unlink("./".$upload_path."file/".$value);  
    						//echo "success<br />";
    					}
    					else{
    						//echo "encrypt error!<br />";
    					}
    				}
    				else{
    					//echo "no file!<br />";
    				}
    			}
    			else if(strpos($value, "df") == 1) 
    			{
    				if(file_exists("./".$upload_path."file/".$value))
    				{
    					$destination = "./".$upload_path."app/pdf.pdf";   //$path.str_replace("decrypt","encrypt",$value);
    					if($objAES->Encrypt("./".$upload_path."file/".$value, $destination, $secret_key, $iv))
    					{
    						//unlink("./".$upload_path."file/".$value);   
    						//echo "success<br />";
    						$cid = get_array_value($copy_result,"cid","");
    						$is_license = get_array_value($copy_result,"is_license",0);
    						if($is_license == 1)
    						{
    							unlink("./".$upload_path."file/".$value);
    						}
    						else{
    							rename("./".$upload_path."file/".$value, "./".$upload_path."app/".$cid.".pdf");
    						}
    					}
    					else{
    						//echo "encrypt error!<br />";
    					}
    				}
    				else{
    					//echo "no file!<br />";
    				}
    			}
    			else{
    				//echo "not pdf!<br />";
    			}
    		}
			
			$msg = ('All files are encyted.');
			$result_obj = array("status" => 'success',"msg" => $msg );
			echo json_encode($result_obj);
			return "";
		// }
// 		else{
// 			$msg = ('File not found.<BR><BR>Please upload file to this path : '. $upload_path . ' then hit button again.');
// 			$result_obj = array("status" => 'error',"msg" => $msg );
// 			echo json_encode($result_obj);
// 			return "";
// 		}
	}
	
	public function AES_Encryption($mypath, $secret_key, $iv)
        { 
            $path = $mypath."tmp/";
            if(file_exists($path))
            {
                $objScan = scandir($path);
            	$objAES = new CryptographyAES();
                foreach ($objScan as $value) 
                {
                    /* pdf */
                    if(strpos($value, "df-") == 1)
                    {
                        $source = $path.$value;
                        $destination = $mypath."app/encrypt_".$value;
                    
                        
                        if($objAES->Encrypt($source, $destination, $secret_key, $iv))
                        {
                            //echo "Create ".$destination." is Complete!<br />";
                            //unlink($source);
                        }
                        
                        if(!is_blank($source)) 
                    	{
                        	unlink($source);
                    	}
                    }
                    else if(strpos($value, "df") == 1)
                    {
                    	if(file_exists($mypath."file/".$value))
    					{
    						$destination = $mypath."app/pdf.pdf";   
    						if($objAES->Encrypt($mypath."file/".$value, $destination, $secret_key, $iv))
    						{
    							//unlink("./".$upload_path."file/".$value);   
    							//echo "success<br />";
    						}
    						else{
    							//echo "encrypt error!<br />";
    						}
    					}
    					else{
    						//echo "no file!<br />";
    					}
                    }
                    

                }
                return true;
            }
            else{
                return false;
            }

        }
}

?>