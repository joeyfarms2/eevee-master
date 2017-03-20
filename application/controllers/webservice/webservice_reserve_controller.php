<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_reserve_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->reserve_model = 'Reserve_model';
		$this->user_model = 'User_model';
		$this->view_all_product_copies_model = 'View_all_product_copies_model';

	}
	
	function reserve_product(){
		$this->check_device();

		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		$user_aid = get_array_value($login_history,"user_aid","");
		// echo "user_aid = ".$user_aid;

		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";
		$copy_aid = trim($this->input->get_post('copy_aid'));
		// echo "copy_aid = ".$copy_aid;
		if(is_blank($copy_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify copy_aid.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_number_no_zero($copy_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : id must be integer.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->view_all_product_copies_model,'view_all_copies');
		$this->view_all_copies->set_where(array("aid"=>$copy_aid, "product_type_aid"=>$product_type_aid, "is_ebook"=>"1", "status"=>"1"));
		$view_all_copies_result = $this->view_all_copies->load_record(false);
		if(!is_var_array($view_all_copies_result)){
			$result_obj = array("status" => 'error',"msg" => 'Data not found : Copy id not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		$parent_aid = get_array_value($view_all_copies_result,"parent_aid","");

		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["copy_aid"] = $copy_aid;
		$tmp["user_aid"] = $user_aid;
		$tmp["status"] = '1';
		$this->load->model($this->reserve_model,"reserve");
		$this->reserve->set_where($tmp);
		$queue_result = $this->reserve->load_record(true);
		if(is_var_array($queue_result)){
			$result_obj = array("status" => 'warning',"msg" => 'You already reserve this book.', "result" => '');
			$result_obj = $this->get_queue($result_obj, $product_type_aid, $copy_aid, $user_aid);
			echo json_encode($result_obj);
			return "";
		}else{
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["copy_aid"] = $copy_aid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["user_aid"] = $user_aid;
			$tmp["status"] = '1';
			$this->db->flush_cache();
			$this->load->model($this->reserve_model,"reserve");
			$aid = $this->reserve->insert_record($tmp);
			$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
			$result_obj = $this->get_queue($result_obj, $product_type_aid, $copy_aid, $user_aid);
			echo json_encode($result_obj);
			return "";
		}
	}

	function get_my_reserve_list(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$obj = array();
			$obj["user_aid"] = $user_aid;
			$obj["status"] = "1";
			$this->load->model($this->reserve_model,"reserve");
			$this->reserve->set_where($obj);
			$result = $this->reserve->load_records(false);
			
			if(!is_var_array($result)){
				$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}

			$result_list = "";
			foreach ($result as $item) {
				// print_r($item); echo "<HR>";
				$product_type_aid = get_array_value($item,"product_type_aid","");
				$product_type_cid = get_array_value($item,"product_type_cid","");
				$copy_aid = get_array_value($item,"copy_aid","");
				$parent_aid = get_array_value($item,"parent_aid","");
				$confirm_code = get_array_value($item,"confirm_code","");
				$model = $this->get_product_model($product_type_aid);
				$model_name = get_array_value($model,"product_model","");
				$this->db->flush_cache();
				$this->db->_reset_select();
				$this->load->model($model_name, $model_name);
				$tmp = array();
				$tmp['aid'] = $parent_aid;
				$this->{$model_name}->set_where($tmp);
				$product = $this->{$model_name}->load_record(true);
				// print_r($product); echo "<HR>";
				
				$product_main_aid = get_array_value($product,"product_main_aid","");
				$aid = get_array_value($product,"aid","");
				$obj = array();
				$obj["user_aid"] = $user_aid;
				$obj["type"] = $product_type_cid;
				$obj["parent_aid"] = $parent_aid;
				$obj["copy_aid"] = $copy_aid;
				$obj["title"] = get_array_value($product,"title","");
				$obj["author"] = get_array_value($product,"author","");
				$obj["cover_image"] = get_array_value($product,"cover_image_ipad","");
				$obj = $this->get_queue($obj, $product_type_aid, $copy_aid, $user_aid);
				if($confirm_code!=""){
					$obj["status"] = 1;	
					$obj["confirm_code"]=$confirm_code;
				}else{
					$obj["status"] = 0;
					$obj["confirm_code"]=0;
				}
				$obj['product_type_aid']=$product_type_aid;
				
				
				$result_list[] = $obj;
			}
			
			// echo "<br>sql : ".$this->db->last_query()."<BR>";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result_list);
			echo json_encode($result_obj);
			return "";
		}
	}
	function get_noti_reserve_list(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$obj = array();
			$obj["user_aid"] = $user_aid;
			$obj["status"] = "1";
			$this->load->model($this->reserve_model,"reserve");
			$this->reserve->set_where($obj);
			$result = $this->reserve->load_records(false);
			$sql="select * from reserve where user_aid= '$user_aid' and status = '1' and confirm_code!=''  ";
			$exe=mysql_query($sql);
			$rows=mysql_num_rows($exe);
			


			$result_list = "";
			$obj['rows']=$rows;
			$result_list[] = $obj;
			
			
			// echo "<br>sql : ".$this->db->last_query()."<BR>";
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result_list);
			echo json_encode($result_obj);
			return "";
		}
	}

	function cancel_my_reserve(){
		$this->check_device();

		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = $this->check_token();
		$user_aid = get_array_value($login_history,"user_aid","");
		// echo "user_aid = ".$user_aid;

		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";
		$copy_aid = trim($this->input->get_post('copy_aid'));
		// echo "copy_aid = ".$copy_aid;
		if(is_blank($copy_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify copy_aid.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_number_no_zero($copy_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : id must be integer.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$this->load->model($this->view_all_product_copies_model,'view_all_copies');
		$this->view_all_copies->set_where(array("aid"=>$copy_aid, "product_type_aid"=>$product_type_aid, "is_ebook"=>"1", "status"=>"1"));
		$view_all_copies_result = $this->view_all_copies->load_record(false);
		if(!is_var_array($view_all_copies_result)){
			$result_obj = array("status" => 'error',"msg" => 'Data not found : Copy id not found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		$parent_aid = get_array_value($view_all_copies_result,"parent_aid","");

		$data_where = array();
		$data_where["product_type_aid"] = $product_type_aid;
		$data_where["copy_aid"] = $copy_aid;
		$data_where["user_aid"] = $user_aid;
		$data = array();
		$data["status"] = '0';
		$this->load->model($this->reserve_model,"reserve");
		$this->reserve->set_where($data_where);
		$result = $this->reserve->update_record($data);
		$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
		echo json_encode($result_obj);
		return "";
	}
	
	
	
	function confirm_my_reserve(){
			
		$this->check_device();
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		$login_history = $this->check_token();
		$user_aid = get_array_value($login_history,"user_aid","");
		$copy_aid = trim($this->input->get_post('copy_aid'));
		$product_type_aid = trim($this->input->get_post('product_type_aid'));
		$confirm_code = trim($this->input->get_post('confirm_code'));
		
		$result = $this->check_reservation_code($user_aid, $copy_aid, $product_type_aid, $confirm_code);
		$reserve_result = get_array_value($result,"reserve_result","");
		$user_result = get_array_value($result,"user_result","");
		$copy_result = get_array_value($result,"copy_result","");

		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		// print_r($model);
		// return "";

		$product_type_cid = get_array_value($copy_result,"product_type_cid","");
		$copy_status = get_array_value($copy_result,"status","0");
		$parent_status = get_array_value($copy_result,"parent_status","0");
		$parent_title = get_array_value($copy_result,"parent_title","N/A");
		$parent_aid = get_array_value($copy_result,"parent_aid","0");

		$ebook_concurrence = get_array_value($copy_result,"ebook_concurrence","0");
		$is_license = get_array_value($copy_result,"is_license","0");
		$is_ebook = get_array_value($copy_result,"is_ebook","0");
		$possession = get_array_value($copy_result,"possession","0");
		$rental_period = get_array_value($copy_result,"rental_period","0");

		$this->load->model($this->shelf_model,"shelf");
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["copy_aid"] = $copy_aid;
		$tmp["status"] = "1";
		$this->shelf->set_where($tmp);
		$now_on_shelf = $this->shelf->count_records(false);
		//echo $now_on_shelf;
		if($now_on_shelf >= $ebook_concurrence){
			$data = array();
			$data["status"] = '0';
			$data["confirm_code"] = NULL;
			$data["expiration_date"] = NULL;
			$data_where = array();
			$data_where["status"] = '1';
			$data_where["user_aid"] = $user_aid;
			$data_where["copy_aid"] = $copy_aid;
			$data_where["product_type_aid"] = $product_type_aid;
			$this->load->model($this->reserve_model,"reserve");
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data);
			
			$result_obj = array("status" => 'error',"msg" => 'already-in-bookshelf.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$expiration_date = "";
		$this->load->model($this->shelf_model,"shelf");
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["copy_aid"] = $copy_aid;
		$tmp["status"] = "1";
		$this->shelf->set_where($tmp);
		$now_on_shelf = $this->shelf->count_records(false);
		if($now_on_shelf >= $ebook_concurrence){
			$result_obj = array("status" => 'error',"msg" => 'max-concurrence.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		
		$this->load->model($this->shelf_model,"shelf");
		$tmp = array();
		$tmp["user_aid"] = $user_aid;
		$tmp["status"] = "1";
		$tmp["is_license"] = "1";
		$this->shelf->set_where($tmp);
		$now_on_my_shelf = $this->shelf->count_records(false);
		// echo "now_on_my_shelf = $now_on_my_shelf";
		if($now_on_my_shelf >= CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF){
			$result_obj = array("status" => 'error',"msg" => 'max-shelf.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		$expiration_date = date("Y-m-d",strtotime("+".$rental_period." days"));

		$this->load->model($this->shelf_model,"shelf");
		$this->shelf->set_where(array("user_aid"=>$user_aid, "copy_aid"=>$copy_aid, "product_type_aid"=>$product_type_aid));
		$result_list = $this->shelf->load_record(false);
		
		if($result_list){ // not first time load
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["copy_aid"] = $copy_aid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["user_aid"] = $user_aid;
			$tmp["status"] = '1';
			$tmp["is_license"] = $is_license;
			$tmp["is_read"] = '0';
			$tmp["expiration_date"] = $expiration_date;
			$this->load->model($this->shelf_model,"shelf");
			$result = $this->shelf->insert_or_update($tmp);
			
			if($result){
				$result_obj = array("status" => 'success',"msg" => 'success-confirm.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}else{

				$result_obj = array("status" => 'error',"msg" => 'error-db.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
		}else{ //first time load
			$tmp = array();
			$tmp["user_aid"] = $user_aid;
			$tmp["copy_aid"] = $copy_aid;
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
			$data_where["copy_aid"] = $copy_aid;
			$data_where["product_type_aid"] = $product_type_aid;
			$this->load->model($this->reserve_model,"reserve");
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data);

			if($result){
				//Update total download to parent
				$this->db->flush_cache();
				$this->load->model($model_name, $model_name);
				$result = $this->{$model_name}->increase_total_download_web($parent_aid);	
				
				//Update shelf history
				$this->db->flush_cache();
				$this->load->model($this->shelf_history_model,"shelf_history");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["product_type_cid"] = $product_type_cid;
				$tmp["is_license"] = $is_license;
				$tmp["copy_aid"] = $copy_aid;
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
				$this->update_reward_point($parent_result, $user_aid);

				if($possession == "1"){
					//Update copy buyout
					$this->db->flush_cache();
					$this->load->model($this->copy_buyout_model,"copy_buyout");
					$tmp = array();
					$tmp["product_type_aid"] = $product_type_aid;
					$tmp["product_type_cid"] = $product_type_cid;
					$tmp["copy_aid"] = $copy_aid;
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
				$tmp["copy_aid"] = $copy_aid;
				$tmp["parent_aid"] = $parent_aid;
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["product_type_cid"] = $product_type_cid;
				$tmp["price_cover"] = get_array_value($copy_result,"price_cover","0");
				$tmp["price_currency"] = get_array_value($copy_result,"price_currency","0");
				$tmp["price_actual"] = get_array_value($copy_result,"price_actual","0");
				$tmp["status"] = '1';
				$tmp["channel"] = '2';
				$this->load->model($this->copy_download_model,"copy_download");
				$result = $this->copy_download->insert_record($tmp);			

				$email = get_array_value($user_result,"email","");
				/*if(!is_blank($email)){
					$this->lang->load('mail');											
					$subject = $this->lang->line('mail_subject_reserve_success');
					$body = $this->lang->line('mail_content_reserve_success');
					
					// $email = "asitgets@gmail.com";
			
					$body = str_replace("{doc_type}", "&nbsp;" , $body);
					$body = str_replace("{name}", trim(get_user_info($user_result)) , $body);
					$body = str_replace("{email}", trim($email) , $body);

					$this->load->library('email');
					$config = $this->get_init_email_config();
					if(is_var_array($config)){ 
						$this->email->initialize($config); 
						$this->email->set_newline("\r\n");
					}
					
					// Send message
					$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
					$this->email->to($email);
					$this->email->bcc('asitgets@gmail.com'); 

					$this->email->subject($subject);
					$this->email->message($body);
					// echo $this->email->print_debugger();
					echo $this->email->send();
				}*/

				$result_obj = array("status" => 'success',"msg" => 'success-confirm.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}else{
				$result_obj = array("status" => 'error',"msg" => 'error-db.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
	}
	function check_reservation_code($user_aid=0, $copy_aid=0, $product_type_aid=0, $confirm_code=""){
		// echo "user_aid = $user_aid, copy_aid = $copy_aid, product_type_aid = $product_type_aid, confirm_code = $confirm_code";
		if($user_aid <= 0 || $copy_aid <= 0 || $product_type_aid <= 0 || is_blank($confirm_code) ){
			$result_obj = array("status" => 'error',"msg" => 'data-not-found.', "result" => '');
				echo json_encode($result_obj);
				return "";
		}

		$this->load->model($this->user_model,"user");
		$this->user->set_where(array("aid"=>$user_aid, "status"=>'1'));
		$user_result = $this->user->load_record(false);
		
		if(!is_var_array($user_result)){
			$result_obj = array("status" => 'error',"msg" => 'data-not-found..', "result" => '');
				echo json_encode($result_obj);
				return "";
		}

		$this->load->model($this->reserve_model,"main");
		$this->main->set_where(array("user_aid"=>$user_aid, "copy_aid"=>$copy_aid, "product_type_aid"=>$product_type_aid, "status"=>'1', "confirm_code" => $confirm_code));
		$reserve_result = $this->main->load_record(false);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		// print_r($reserve_result);
		if(!is_var_array($reserve_result)){

			$email = get_array_value($reserve_result,"email","");

			$result_obj = array("status" => 'error',"msg" => 'wrong-url.', "result" => '');
				echo json_encode($result_obj);
				return "";
		}
		$model = $this->get_product_model($product_type_aid);
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$this->load->model($model_copy_name, $model_copy_name);

		$tmp = array();
		$tmp['aid'] = $copy_aid;
		$this->{$model_copy_name}->set_where($tmp);
		$copy_result = $this->{$model_copy_name}->load_record(true);
		// echo "<br>sql : ".$this->db->last_query()."<br>";
		// print_r($copy_result);

		$copy_status = get_array_value($copy_result,"status","0");
		$parent_status = get_array_value($copy_result,"parent_status","0");
		$parent_title = get_array_value($copy_result,"parent_title","N/A");
		$parent_aid = get_array_value($copy_result,"parent_aid","0");

		$ebook_concurrence = get_array_value($copy_result,"ebook_concurrence","0");
		$is_license = get_array_value($copy_result,"is_license","0");
		$is_ebook = get_array_value($copy_result,"is_ebook","0");
		// echo "copy_status = $copy_status , parent_status = $parent_status";

		if($copy_status == '0' || $parent_status == '0' || $is_ebook == '0' || $is_license == '0' || $ebook_concurrence == '0'){
			$result_obj = array("status" => 'error',"msg" => 'data-not-found.', "result" => '');
				echo json_encode($result_obj);
				return "";
		}
		// print_r($copy_result);


		$result = array();
		$result["reserve_result"] = $reserve_result;
		$result["user_result"] = $user_result;
		$result["copy_result"] = $copy_result;
		// echo "<pre>";
		// print_r($result);
		// echo "</pre>";
		return $result;
	}


}

?>