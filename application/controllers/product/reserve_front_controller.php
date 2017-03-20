<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Reserve_front_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		define("thisFrontTabMenu",'home');
		define("thisFrontSubMenu",'');
		@define("folderName",'product/product_front');
		
		$this->main_model = "Reserve_model";
	}
	
	function index(){
		return "";
	}

	function confirm($user_aid=0, $copy_aid=0, $product_type_aid=0, $confirm_code=""){
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
			$this->load->model($this->main_model,"reserve");
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data);
			redirect('reservation/status/'.md5('already-in-bookshelf'));
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
			redirect('reservation/status/'.md5('max-concurrence'));
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
			redirect('reservation/status/'.md5('max-shelf'));
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
				redirect('reservation/status/'.md5('success-confirm'));
				return "";
			}else{
				redirect('reservation/status/'.md5('error-db'));
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
			$this->load->model($this->main_model,"reserve");
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
				if(!is_blank($email)){
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
				}

				redirect('reservation/status/'.md5('success-confirm'));
				return "";
			}else{
				redirect('reservation/status/'.md5('error-db'));
				return "";
			}
		}
	}

	function cancel($user_aid=0, $copy_aid=0, $product_type_aid=0, $confirm_code=""){
		$result = $this->check_reservation_code($user_aid, $copy_aid, $product_type_aid, $confirm_code);
		$reserve_result = get_array_value($result,"reserve_result","");
		$user_result = get_array_value($result,"user_result","");

		$data = array();
		$data["status"] = '0';
		$data["confirm_code"] = NULL;
		$data["expiration_date"] = NULL;
		$data_where = array();
		$data_where["user_aid"] = $user_aid;
		$data_where["copy_aid"] = $copy_aid;
		$data_where["product_type_aid"] = $product_type_aid;
		$data_where["confirm_code"] = $confirm_code;
		$this->load->model($this->main_model,"main");
		$this->main->set_where($data_where);
		$rs = $this->main->update_record($data);
		if($rs){
			$this->log_status('Reservation', get_user_info($user_result).' [aid = '.$user_aid.'] just cancel reservation.', $reserve_result);
			redirect('reservation/status/'.md5('success-cancel'));
		}else{
			$this->log_error('Reservation', 'Command update_record() fail. Can not cancel reservation.', $reserve_result);
			redirect('reservation/status/'.md5('error-db'));
		}

	}

	function check_reservation_code($user_aid=0, $copy_aid=0, $product_type_aid=0, $confirm_code=""){
		// echo "user_aid = $user_aid, copy_aid = $copy_aid, product_type_aid = $product_type_aid, confirm_code = $confirm_code";
		if($user_aid <= 0 || $copy_aid <= 0 || $product_type_aid <= 0 || is_blank($confirm_code) ){
			redirect('reservation/status/'.md5('data-not-found'));
			exit(0);
		}

		$this->load->model($this->user_model,"user");
		$this->user->set_where(array("aid"=>$user_aid, "status"=>'1'));
		$user_result = $this->user->load_record(false);
		if(!is_var_array($user_result)){
			redirect('reservation/status/'.md5('data-not-found'));
			exit(0);
		}

		$this->load->model($this->main_model,"main");
		$this->main->set_where(array("user_aid"=>$user_aid, "copy_aid"=>$copy_aid, "product_type_aid"=>$product_type_aid, "status"=>'1', "confirm_code" => $confirm_code));
		$reserve_result = $this->main->load_record(false);
		if(!is_var_array($reserve_result)){

			$email = get_array_value($user_result,"email","");
			if(!is_blank($email)){
				$this->lang->load('mail');											
				$subject = $this->lang->line('mail_subject_reserve_fail');
				$body = $this->lang->line('mail_content_reserve_fail');
				
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
			}

			redirect('reservation/status/'.md5('wrong-url'));
			exit(0);
		}
		$model = $this->get_product_model($product_type_aid);
		$model_copy_name = get_array_value($model,"product_copy_model","");
		$this->load->model($model_copy_name, $model_copy_name);

		$tmp = array();
		$tmp['aid'] = $copy_aid;
		$this->{$model_copy_name}->set_where($tmp);
		$copy_result = $this->{$model_copy_name}->load_record(true);

		$copy_status = get_array_value($copy_result,"status","0");
		$parent_status = get_array_value($copy_result,"parent_status","0");
		$parent_title = get_array_value($copy_result,"parent_title","N/A");
		$parent_aid = get_array_value($copy_result,"parent_aid","0");

		$ebook_concurrence = get_array_value($copy_result,"ebook_concurrence","0");
		$is_license = get_array_value($copy_result,"is_license","0");
		$is_ebook = get_array_value($copy_result,"is_ebook","0");
		// echo "copy_status = $copy_status , parent_status = $parent_status";

		if($copy_status == '0' || $parent_status == '0' || $is_ebook == '0' || $is_license == '0' || $ebook_concurrence == '0'){
			redirect('reservation/status/'.md5('data-not-found'));
			exit(0);
		}
		// print_r($copy_result);


		$result = array();
		$result["reserve_result"] = $reserve_result;
		$result["user_result"] = $user_result;
		$result["copy_result"] = $copy_result;

		return $result;
	}

	function show(){
		@define("thisAction",'show');
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/' . folderName . '/reserve_status';
		$this->load->view($this->default_theme_front.'/tpl_blank', $this->data);
	}

	function status($type="")	{
		switch($type)
		{
			case md5('success-confirm') : 
				$this->data["message_show"] = '<img src="'.THEME_FRONT_PATH.'images/background/reserve-success.jpg" /><BR /><BR /><p><strong>Congratulations</strong><BR /><BR />This e-book/e-magazine has been already added into <a href="'.site_url('my-bookshelf').'">your personal book shelf</a>.<BR /><BR /><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a><BR /><BR />Happy Reading!</p>';
				$this->data["js_code"] = '';
				break;
			case md5('success-cancel') : 
				$this->data["message_show"] = '<p><strong>Congratulations</strong><BR /><BR />Cancellation is completed.<BR /><BR /><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a><BR /><BR />Happy Reading!</p>';
				$this->data["js_code"] = '';
				break;
			case md5('no-command') : 
				$this->data["message_show"] = '<p><strong>Oops!</strong><BR /><BR />Command is unclear. Please try again.</p>';
				$this->data["js_code"] = '';
				break;
			case md5('max-shelf') : 
				$this->data["message_show"] = '<p><strong>Oops!</strong><BR /><BR />Your personal shelf dues to exceed limit, please remove some items before making confirmation.<BR /><BR /><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a><BR /><BR />Happy Reading!</p>';
				$this->data["js_code"] = '';
				break;
			case md5('max-concurrence') : 
				$this->data["message_show"] = '<p><strong>Oops!</strong><BR /><BR />This circulating E-Books/E-Magazines dues to exceed limit.<BR /><BR /><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a><BR /><BR />Happy Reading!</p>';
				$this->data["js_code"] = '';
				break;
			case md5('wrong-url') : 
				$this->data["message_show"] = '<img src="'.THEME_FRONT_PATH.'images/background/reserve-fail.jpg" /><BR /><BR /><p><strong>Oops!</strong><BR /><BR />You did not confirm reservation within 24 hours or this book was added into <a href="'.site_url('my-bookshelf').'">your personal book shelf</a>. <BR /><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a><BR /><BR />Happy Reading!</p>';
				$this->data["js_code"] = '';
				break;
			case md5('data-not-found') : 
				$this->data["message_show"] = '<p><strong>Oops!</strong><BR /><BR />Data not found. Please try again later or contact administrator.</p>';
				$this->data["js_code"] = '';
				break;
			case md5('already-in-bookshelf') : 
				$this->data["message_show"] = '<p><strong>Congratulations</strong><BR /><BR />This circulating E-Books/E-Magazines is already in <a href="'.site_url('my-bookshelf').'">your personal book shelf</a>.<BR /><BR /><a href="'.site_url('my-bookshelf').'">Go to My Shelf</a><BR /><BR />Happy Reading!</p>';
				$this->data["js_code"] = '';
				break;
			case md5('error-db') : 
				$this->data["message_show"] = '<p><strong>Oops!</strong><BR /><BR />Database error occured : Can not save data. Please try again later or contact administrator.</p>';
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message_show"] = '<p><strong>Oops!</strong><BR /><BR />Something went wrong. Please try again later or contact administrator.</p>';
				$this->data["js_code"] = '';
				break;
		}
		$this->show();
	}

}

?>