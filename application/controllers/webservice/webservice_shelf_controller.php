<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_shelf_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->user_model = 'User_model';
		$this->shelf_model = 'Shelf_model';
		$this->shelf_history_model = 'Shelf_history_model';
		$this->copy_download_model = 'Copy_download_model';
		$this->copy_buyout_model = 'Copy_buyout_model';
		$this->reserve_model = 'Reserve_model';

		$this->view_all_products_model = 'View_all_products_model';
		$this->view_all_product_copies_model = 'View_all_product_copies_model';
	}
	
	function get_mybookshelf(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		$sort_by  = trim($this->input->get_post('sort_by'));

		if(is_var_array($login_history)){


			$user_aid = get_array_value($login_history,"user_aid","0");
			$obj = array();
			$obj["user_aid"] = $user_aid;
			$obj["sort_by"] = $sort_by;
			$this->load->model($this->shelf_model,"shelf");
			$result_list = $this->shelf->get_shelf_detail($obj);
			//echo "<br>sql : ".$this->db->last_query();
			// echo "<pre>";
			// print_r($result_list);
			// echo "</pre>";
			if(!is_var_array($result_list)){
				$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			foreach($result_list as $item){
				//print_r($item);echo "<HR>";
				$biblio_aid = get_array_value($item,"aid","");
				
				$obj = array();
				$obj["type"] = get_array_value($item,"product_type_cid","");
				$obj["title"] = get_array_value($item,"parent_title","");
				$obj["author"] = get_array_value($item,"parent_author","");
				$obj["description"] = get_array_value($item,"parent_description","");
				$obj["cover_image"] = get_array_value($item,"cover_image_ipad","");
				$obj["is_license"] = get_array_value($item,"is_license","");
				$obj["welcome_msg"] = get_array_value($item,"welcome_msg","");
				$obj["product_type_aid"] = get_array_value($item,"product_type_aid","");
				$obj["product_main_aid"] = get_array_value($item,"product_main_aid","");
				$obj["user_aid"] = get_array_value($item,"user_aid","");
				$obj["shelf_created_date"] = get_array_value($item,"created_date","");
				$obj["shelf_created_day"] = get_datetime_pattern("d",get_array_value($item,"created_date",""),"");
				$obj["shelf_created_month"] = get_datetime_pattern("m",get_array_value($item,"created_date",""),"");
				$obj["shelf_created_year"] = get_datetime_pattern("Y",get_array_value($item,"created_date",""),"");
				$obj["shelf_updated_date"] = get_array_value($item,"updated_date","");
				$obj["shelf_updated_day"] = get_datetime_pattern("d",get_array_value($item,"updated_date",""),"");
				$obj["shelf_updated_month"] = get_datetime_pattern("m",get_array_value($item,"updated_date",""),"");
				$obj["shelf_updated_year"] = get_datetime_pattern("Y",get_array_value($item,"updated_date",""),"");
				$obj["shelf_expiration_date"] = get_array_value($item,"expiration_date","");

				$obj["copy_aid"] = get_array_value($item,"copy_aid","");
				$obj["copy_cid"] = get_array_value($item,"copy_cid","");
				$obj["copy_barcode"] = get_array_value($item,"barcode","");
				$obj["copy_upload_path"] = get_array_value($item,"upload_path","");
				$filedoc = "./".get_array_value($item,"upload_path","")."doc";
				$pathdoc = PUBLIC_PATH.get_array_value($item,"upload_path","")."doc";
				if(get_array_value($item,"file_upload","") != ""){
					$obj["copy_file_upload"] = $pathdoc."/".get_array_value($item,"file_upload","");
				}else{
							//$filedoc = "'./".get_array_value($sub_item,"upload_path","")."doc'";
					if(file_exists($filedoc)){
						$objScan = scandir($filedoc);
					        foreach ($objScan as $value) 
					            {
					                if(strlen($value) > 3){
					                	$obj["copy_file_upload"] = $pathdoc."/".$value;
					                }else{
					                	$obj["copy_file_upload"] = "";
					                }
					            }		
					}else{
							$obj["copy_file_upload"] = get_array_value($item,"file_upload","");
						}

				}
				//$obj["copy_file_upload"] = get_array_value($item,"file_upload","");
				$obj["copy_publish_date"] = get_array_value($item,"copy_publish_date","");
				$obj["copy_publish_day"] = get_datetime_pattern("d",get_array_value($item,"copy_publish_date",""),"");
				$obj["copy_publish_month"] = get_datetime_pattern("m",get_array_value($item,"copy_publish_date",""),"");
				$obj["copy_publish_year"] = get_datetime_pattern("Y",get_array_value($item,"copy_publish_date",""),"");
				$obj["copy_updated_date"] = get_array_value($item,"copy_updated_date","");
				$obj["copy_updated_day"] = get_datetime_pattern("d",get_array_value($item,"copy_updated_date",""),"");
				$obj["copy_updated_month"] = get_datetime_pattern("m",get_array_value($item,"copy_updated_date",""),"");
				$obj["copy_updated_year"] = get_datetime_pattern("Y",get_array_value($item,"copy_updated_date",""),"");

				$obj["parent_aid"] = get_array_value($item,"parent_aid","");
				$obj["parent_publish_date"] = get_array_value($item,"parent_publish_date","");
				$obj["parent_publish_day"] = get_datetime_pattern("d",get_array_value($item,"parent_publish_date",""),"");
				$obj["parent_publish_month"] = get_datetime_pattern("m",get_array_value($item,"parent_publish_date",""),"");
				$obj["parent_publish_year"] = get_datetime_pattern("Y",get_array_value($item,"parent_publish_date",""),"");
				$obj["parent_updated_date"] = get_array_value($item,"parent_updated_date","");
				$obj["parent_updated_day"] = get_datetime_pattern("d",get_array_value($item,"parent_updated_date",""),"");
				$obj["parent_updated_month"] = get_datetime_pattern("m",get_array_value($item,"parent_updated_date",""),"");
				$obj["parent_updated_year"] = get_datetime_pattern("Y",get_array_value($item,"parent_updated_date",""),"");

				$result[] = $obj;
			}
			
			// echo "<br>sql : ".$this->db->last_query()."<BR>";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}
	}

	function get_shelf_history(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$obj = array();
			$obj["user_aid"] = $user_aid;
			$this->load->model($this->shelf_history_model,"shelf_history");
			$result_list = $this->shelf_history->get_shelf_history_detail($obj);
			if(!is_var_array($result_list)){
				$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			// foreach($result_list as $item){
			// 	$biblio_aid = get_array_value($item,"aid","");
			// 	$obj = array();
			// 	$obj["product_type_aid"] = get_array_value($item,"product_type_aid","");
			// 	$obj["type"] = get_array_value($item,"product_type_cid","");
			// 	$obj["user_aid"] = get_array_value($item,"user_aid","");
			// 	$obj["parent_aid"] = get_array_value($item,"parent_aid","");
			// 	$obj["title"] = get_array_value($item,"parent_title","");
			// 	$obj["author"] = get_array_value($item,"parent_author","");
			// 	$obj["cover_image"] = get_array_value($item,"cover_image_ipad","");
			// 	$obj["shelf_updated_date"] = get_array_value($item,"updated_date","");

			// 	$result[] = $obj;
			// 	}
				
			}
			
			// echo "<br>sql : ".$this->db->last_query()."<BR>";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result_list);
			echo json_encode($result_obj);
			return "";
		}
		
	
	
	function add_book_to_mybookshelf(){
		$device = trim($this->input->get_post('device'));
		$device_id = trim($this->input->get_post('device_id'));
		$this->check_device();
		
		$login_history = $this->check_token();
		
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";

		$model = $this->get_product_model($product_type_aid);

		$aid = trim($this->input->get_post('copy_id'));
		// echo "aid = ".$aid;
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify copy_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($aid)){
			if(!is_number_no_zero($aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : copy_id must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		$this->load->model($this->view_all_product_copies_model,'view_all_copies');
		$this->view_all_copies->set_where(array("aid"=>$aid, "product_type_aid"=>$product_type_aid, "is_ebook"=>"1", "status"=>"1"));
		$view_all_copies_result = $this->view_all_copies->load_record(false);
		if(!is_var_array($view_all_copies_result)){
			$result_obj = array("status" => 'error',"msg" => 'Data not found : Copy id not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		//check is ebook?
		$parent_aid = get_array_value($view_all_copies_result,"parent_aid","0");
		$is_license = get_array_value($view_all_copies_result,"is_license","0");
		$is_ebook = get_array_value($view_all_copies_result,"is_ebook","0");
		$possession = get_array_value($view_all_copies_result,"possession","0");
		$ebook_concurrence = get_array_value($view_all_copies_result,"ebook_concurrence","0");
		$rental_period = get_array_value($view_all_copies_result,"rental_period","0");
		if($is_ebook != '1'){
			$result_obj = array("status" => 'error',"msg" => 'This copy is not ebook.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$user_aid = get_array_value($login_history,"user_aid","0");
		
		$expiration_date = "";
		if($is_license == "1"){
			// if($ebook_concurrence > 0){
				$this->load->model($this->shelf_model,"shelf");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["copy_aid"] = $aid;
				$tmp["status"] = "1";
				$this->shelf->set_where($tmp);
				$now_on_shelf = $this->shelf->count_records(false);
				if($now_on_shelf >= $ebook_concurrence){
					$result_obj = array("status" => 'error',"msg" => 'This copy reach the max concurrence.', "result" => '');
					echo json_encode($result_obj);
					return "";
				}
			// }
			
			$this->load->model($this->shelf_model,"shelf");
			$tmp = array();
			$tmp["user_aid"] = $user_aid;
			$tmp["status"] = "1";
			$tmp["is_license"] = "1";
			$this->shelf->set_where($tmp);
			$now_on_my_shelf = $this->shelf->count_records(false);
			// echo "now_on_my_shelf = $now_on_my_shelf";
			if($now_on_my_shelf >= CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF){
				$result_obj = array("status" => 'error',"msg" => 'This user reach the max books on shelf.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			$expiration_date = date("Y-m-d",strtotime("+".$rental_period." days"));
		}
		
		$this->load->model($this->shelf_model,"shelf");
		$this->shelf->set_where(array("user_aid"=>$user_aid, "copy_aid"=>$aid, "product_type_aid"=>$product_type_aid));
		$result_list = $this->shelf->load_record(false);
		
		if($result_list){ // not first time load
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["copy_aid"] = $aid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["user_aid"] = $user_aid;
			$tmp["status"] = '1';
			$tmp["is_license"] = $is_license;
			$tmp["is_read"] = '0';
			$tmp["expiration_date"] = $expiration_date;
			$this->load->model($this->shelf_model,"shelf");
			$result = $this->shelf->insert_or_update($tmp);

			$data = array();
			$data["status"] = '0';
			$data["confirm_code"] = NULL;
			$data["expiration_date"] = NULL;
			$data_where = array();
			$data_where["status"] = '1';
			$data_where["user_aid"] = $user_aid;
			$data_where["copy_aid"] = $aid;
			$data_where["product_type_aid"] = $product_type_aid;
			$this->load->model($this->reserve_model,"reserve");
			$this->reserve->set_where($data_where);
			$rs = $this->reserve->update_record($data);

			if($result){
				$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
				echo json_encode($result_obj);
				return "";
			}else{
				$result_obj = array("status" => 'error',"msg" => 'Database error occured : Can not save data, Please try again later or contact administrator.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
		}else{ //first time load
			$tmp = array();
			$tmp["user_aid"] = $user_aid;
			$tmp["copy_aid"] = $aid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["status"] = '1';
			$tmp["is_license"] = $is_license;
			$tmp["is_read"] = '0';
			$tmp["expiration_date"] = $expiration_date;
			$this->load->model($this->shelf_model,"shelf");
			$result = $this->shelf->insert_or_update($tmp);

			$data = array();
			$data["status"] = '0';
			$data["confirm_code"] = NULL;
			$data["expiration_date"] = NULL;
			$data_where = array();
			$data_where["status"] = '1';
			$data_where["user_aid"] = $user_aid;
			$data_where["copy_aid"] = $aid;
			$data_where["product_type_aid"] = $product_type_aid;
			$this->load->model($this->reserve_model,"reserve");
			$this->reserve->set_where($data_where);
			$rs = $this->reserve->update_record($data);

			if($result){
				//Update total download to parent
				$this->db->flush_cache();
				$this->load->model(get_array_value($model,"product_model",""),"main");
				$result = $this->main->increase_total_download_web($parent_aid);	
				
				//Update shelf history
				$this->db->flush_cache();
				$this->load->model($this->shelf_history_model,"shelf_history");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["product_type_cid"] = $product_type_cid;
				$tmp["is_license"] = $is_license;
				$tmp["copy_aid"] = $aid;
				$tmp["parent_aid"] = $parent_aid;
				$tmp["user_aid"] = $user_aid;
				$tmp["status"] = '1';
				$tmp["action"] = 'in';
				$result = $this->shelf_history->insert_record($tmp);

				$this->db->flush_cache();
				$this->load->model(get_array_value($model,"product_model",""),"product");
				$tmp = array();
				$tmp["aid"] = $parent_aid;
				if(!exception_about_status()) $tmp["status"] = "1";
				$this->product->set_where($tmp);
				$parent_result = $this->product->load_record(true);
				$point = $this->update_reward_point($parent_result, $user_aid);

				if($possession == "1"){
					//Update copy buyout
					$this->db->flush_cache();
					$this->load->model($this->copy_buyout_model,"copy_buyout");
					$tmp = array();
					$tmp["product_type_aid"] = $product_type_aid;
					$tmp["product_type_cid"] = $product_type_cid;
					$tmp["copy_aid"] = $aid;
					$tmp["parent_aid"] = $parent_aid;
					$tmp["user_aid"] = $user_aid;
					$tmp["status"] = '1';
					$tmp["price"] = '0';
					$result = $this->copy_buyout->insert_or_update($tmp);
				}

				//Add copy_download for report
				$tmp = array();
				$tmp["order_main_aid"] = '0';
				$tmp["user_aid"] = $user_aid;
				$tmp["copy_aid"] = $aid;
				$tmp["parent_aid"] = $parent_aid;
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["product_type_cid"] = $product_type_cid;
				$tmp["price_cover"] = get_array_value($view_all_copies_result,"price_cover","0");
				$tmp["price_currency"] = get_array_value($view_all_copies_result,"price_currency","0");
				$tmp["price_actual"] = get_array_value($view_all_copies_result,"price_actual","0");
				$tmp["status"] = '1';
				$tmp["device"] = $device;
				$tmp["device_id"] = $device_id;
				$tmp["channel"] = '2';
				$this->load->model($this->copy_download_model,"copy_download");
				$result = $this->copy_download->insert_record($tmp);
				if($result){
					// $this->log_status('Add record to copy_download', 'Add ['.get_array_value($view_all_copies_result,"title","N/A").'] to copy_download of ['.getSessionUserAid().']. Success');
				}else{
					$chk = false;
					// $this->log_error('Add record to copy_download', 'Add ['.get_array_value($view_all_copies_result,"title","N/A").'] to copy_download of ['.getSessionUserAid().']. Fail');
				}
			
				// $this->log_status('Add record to copy_download', getSessionUserCid().' load [copy_aid : '.get_array_value($view_all_copies_result,"aid","-").'] '.get_array_value($view_all_copies_result,"title","-").' to shelf for the first time.');
				$result_obj = array("status" => 'success',"msg" => '', "result" => '1', "point" => $point);
				echo json_encode($result_obj);
				return "";
			}else{
				// $this->log_error('Add record to copy_download', getSessionUserCid().' try to load [issue_aid : '.get_array_value($view_all_copies_result,"aid","-").'] '.get_array_value($view_all_copies_result,"title","-").' to shelf but system error.');
				$result_obj = array("status" => 'error',"msg" => 'Database error occured : Can not save data, Please try again later or contact administrator.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
	}

	function remove_book_from_mybookshelf(){
		$device = trim($this->input->get_post('device'));
		$device_id = trim($this->input->get_post('device_id'));
		$this->check_device();
		
		$login_history = $this->check_token();
		
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";

		$aid = trim($this->input->get_post('copy_id'));
		// echo "aid = ".$aid;
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify copy_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($aid)){
			if(!is_number_no_zero($aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : copy_id must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		$this->load->model($this->view_all_product_copies_model,'view_all_copies');
		$this->view_all_copies->set_where(array("aid"=>$aid, "product_type_aid"=>$product_type_aid, "is_ebook"=>"1", "status"=>"1"));
		$view_all_copies_result = $this->view_all_copies->load_record(false);
		if(!is_var_array($view_all_copies_result)){
			$result_obj = array("status" => 'error',"msg" => 'Data not found : Copy id not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		//check is ebook?
		$parent_aid = get_array_value($view_all_copies_result,"parent_aid","");
		$is_ebook = get_array_value($view_all_copies_result,"is_ebook","0");
		$is_license = get_array_value($view_all_copies_result,"is_license","0");
		$ebook_concurrence = get_array_value($view_all_copies_result,"ebook_concurrence","0");
		if($is_ebook != '1'){
			$result_obj = array("status" => 'error',"msg" => 'This copy is not ebook.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$user_aid = get_array_value($login_history,"user_aid","0");
		
		$this->load->model($this->shelf_model,"shelf");
		$this->shelf->set_where(array("user_aid"=>$user_aid, "copy_aid"=>$aid, "product_type_aid"=>$product_type_aid));
		$result_list = $this->shelf->load_record(false);
		
		if(is_var_array($result_list)){
		
			$this->load->model($this->shelf_model,"shelf");
			$data_where = array();
			$data_where["user_aid"] = $user_aid;
			$data_where["copy_aid"] = $aid;
			$data_where["product_type_aid"] = $product_type_aid;
			$this->shelf->set_where($data_where);
			$rs = $this->shelf->delete_records();

			//Update shelf history
			$this->db->flush_cache();
			$this->load->model($this->shelf_history_model,"shelf_history");
			$data = array();
			$data["user_aid"] = $user_aid;
			$data["copy_aid"] = $aid;
			$data["parent_aid"] = $parent_aid;
			$data["product_type_aid"] = $product_type_aid;
			$data["product_type_cid"] = $product_type_cid;
			$data["status"] = '1';
			$data["action"] = 'de';
			$result = $this->shelf_history->insert_record($data);
			
			if($rs){
				$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
				echo json_encode($result_obj);
				return "";
			}else{
				$result_obj = array("status" => 'error',"msg" => 'Database error occured : Can not save data, Please try again later or contact administrator.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'This copy is not in bookshelf.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function check_book_in_mybookshelf(){
		$device = trim($this->input->get_post('device'));
		$device_id = trim($this->input->get_post('device_id'));
		$this->check_device();
		
		$login_history = $this->check_token();
		
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";

		$aid = trim($this->input->get_post('copy_id'));
		// echo "aid = ".$aid;
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify copy_id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($aid)){
			if(!is_number_no_zero($aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : copy_id must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		$this->load->model($this->view_all_product_copies_model,'view_all_copies');
		$this->view_all_copies->set_where(array("aid"=>$aid, "product_type_aid"=>$product_type_aid, "is_ebook"=>"1", "status"=>"1"));
		$view_all_copies_result = $this->view_all_copies->load_record(false);
		if(!is_var_array($view_all_copies_result)){
			$result_obj = array("status" => 'error',"msg" => 'Data not found : Copy id not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$user_aid = get_array_value($login_history,"user_aid","0");
		
		$this->load->model($this->shelf_model,"shelf");
		$this->shelf->set_where(array("user_aid"=>$user_aid, "copy_aid"=>$aid, "product_type_aid"=>$product_type_aid));
		$result_list = $this->shelf->load_record(false);
		
		if(is_var_array($result_list)){
			$data = array();
			$data["shelf_status"] = "1";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $data);
			echo json_encode($result_obj);
			return "";
		}else{
			$data = array();
			$data["shelf_status"] = "0";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $data);
			echo json_encode($result_obj);
			return "";
		}
	}

	function check_mybookshelf_license(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$obj = array();
			$obj["user_aid"] = $user_aid;
			$obj["is_license"] = "1";
			$this->load->model($this->shelf_model,"shelf");
			$this->shelf->set_where($obj);
			$shelf_result = $this->shelf->load_records(false);

			$qty_used = 0;
			$qty_remaining = 0;
			$qty_allowed = CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF;
			if(is_var_array($shelf_result)){
				$qty_used = count($shelf_result);
			}
			$qty_remaining = $qty_allowed - $qty_used;
			if($qty_remaining <= 0){
				$qty_remaining = 0;
			}

			$result = array();
			$result["qty_allowed"] = $qty_allowed;
			$result["qty_used"] = $qty_used;
			$result["qty_remaining"] = $qty_remaining;

			
			// echo "<br>sql : ".$this->db->last_query()."<BR>";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}
	}

}

?>