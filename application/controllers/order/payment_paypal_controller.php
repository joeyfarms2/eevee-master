<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/order/order_save_controller.php");

class Payment_paypal_controller extends Order_save_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";

		$this->package_point_model = "Package_point_model";
		$this->order_main_model = "Order_main_model";
		$this->user_model = "User_model";
		$this->point_history_model = "Point_history_model";
		$this->log_paypal_model = "Log_paypal_model";
		$this->order_receipt_model = "Order_receipt_model";
		$this->setting_running_model = "Setting_running_model";

		$this->lang->load('mail');
	}
	
	function index(){
		return "";
	}
	
	function save_approve_point($txn_id="",$order_result=""){
		$order_main_cid = get_array_value($order_result,"cid","");
		//If success payment
		$this->log_status('Paypal Feedback', 'Start!! Order ['.$order_main_cid.']. Paypal txn_id = ['.$txn_id.'].', $order_result);
		$chk_status = $this->save_point("paypal", $order_result);
		$this->log_status('Paypal Feedback', ' End!! Order ['.$order_main_cid.']. Paypal txn_id = ['.$txn_id.'].');
		return $chk_status;
	}

	function save_approve_basket($txn_id="",$order_result=""){
		$order_main_cid = get_array_value($order_result,"cid","");
		//If success payment
		$this->log_status('Paypal Feedback', 'Start!! Order ['.$order_main_cid.']. Paypal txn_id = ['.$txn_id.'].', $order_result);
		$chk_status = $this->save_basket("paypal", $order_result);
		$this->log_status('Paypal Feedback', ' End!! Order ['.$order_main_cid.']. Paypal txn_id = ['.$txn_id.'].');
		return $chk_status;
	}

	function save_back_from_paypal_front($what_to_buy){
		@define("thisAction","save_back_from_paypal_front");
		$this->data["title"] = DEFAULT_TITLE;
		
		if(CONST_USE_PAYPAL_SANDBOX == "1") {
         $pp_hostname = "www.sandbox.paypal.com";
	   } else {
         $pp_hostname = "www.paypal.com";
	   }

		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-synch';
		 
		$tx_token = $_GET['tx'];
		$auth_token = "bDNnga79RV3OmX7ODf009M3oc7_hWUlOcIefTyw3aL4YVOUZDClfNaJIGjy";
		$req .= "&tx=$tx_token&at=$auth_token";
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://$pp_hostname/cgi-bin/webscr");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		//set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
		//if your server does not bundled with default verisign certificates.
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: $pp_hostname"));
		$res = curl_exec($ch);
		curl_close($ch);

		if (!$res) {
		    //HTTP ERROR
		} 
		else {
		     // parse the data
		    $lines = explode("\n", $res);
		    $pdt_return = array();
		    if (strcmp ($lines[0], "SUCCESS") == 0) {
	      	for ($i=1; $i<count($lines);$i++) {
	      		if (isset($lines[$i]) && (strpos($lines[$i], "=") != FALSE) ) {
			        list($key,$val) = explode("=", $lines[$i]);
			        $pdt_return[urldecode($key)] = urldecode($val);
			     	}
			   }
				$payer_email = $pdt_return['payer_email'];
				$first_name = $pdt_return['first_name'];
				$last_name = $pdt_return['last_name'];
				$mc_gross = $pdt_return['mc_gross'];
				$mc_currency = $pdt_return['mc_currency'];
				$payer_id = $pdt_return['payer_id'];
				$payment_date = $pdt_return['payment_date'];
				$payment_status = $pdt_return['payment_status'];
				$txn_id = $pdt_return['txn_id'];
				$receiver_email = $pdt_return['receiver_email'];
				$order_main_cid = $pdt_return['custom'];

				$this->data["order_main_cid"] = $order_main_cid;

				// check the payment_status is Completed
				// check that txn_id has not been previously processed
				// check that receiver_email is your Primary PayPal email
				// check that payment_amount/payment_currency are correct
				// process payment

				// echo "<pre>"; print_r($pdt_return); echo "</pre>"; exit;
				$is_completed = false;
				if ($payment_status == "Completed") {
               if ($receiver_email == CONST_EMAIL_FOR_PAYPAL) {
                  if ($mc_currency == "THB") {
                  	if (!empty($order_main_cid) && $order_main_cid != "-") {
                  		$is_completed = true;
                  		$this->load->model($this->order_main_model,"order_main");
								$this->order_main->set_where(array("cid"=>$order_main_cid));
								$this->order_main->update_record(array(
									'status' => '3'
									)
								);

								$this->log_status('Paypal PDT Success', '"'.$order_main_cid.'"" was successfully processed (status = 3). The paypal txn_id = '.$txn_id);

								switch($what_to_buy) {
									case "point" : 
										$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/package_point_paypal_status';
										$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
										break;
									case "basket" : 
										$this->data["view_the_content"] = $this->default_theme_front . '/'. folderName .'/basket_paypal_status';
										$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
										break;
									default : 
										$this->data["view_the_content"] = $this->default_theme_front . '/home/home';
										$this->load->view($this->default_theme_front.'/tpl_home', $this->data);
										break;
								}
                  	}
                  	else {
                  		$this->log_error('Paypal PDT Error', 'Error: $order_main_cid is blank or invalid, the value is "'.$order_main_cid.'"');
                  	}
                  }
                  else {
                  	 $this->log_error('Paypal PDT Error', 'Error: Incorrect mc_currency (payment currency) from Paypal, "'.$mc_currency.'" instead of "THB" (order_main.cid = '.$order_main_cid.')');
                  }
              	}
              	else {
              		$this->log_error('Paypal PDT Error', 'Error: Incorrect receiver_email from Paypal, "'.$receiver_email.'" instead of "'.CONST_EMAIL_FOR_PAYPAL.'" (order_main.cid = '.$order_main_cid.')');
              	}

              	if ($is_completed != true) {
              		switch($what_to_buy){
							case "point" : 
								redirect('order/package-point/status/'.md5('fail'));
								return "";
								break;
							case "basket" : 
								redirect('basket/confirm/status/'.md5('fail'));
								return "";
								break;
							default : 
								redirect('home/status/'.md5('fail'));
								return "";
								break;
						}
              	}
           	}
           	else if ($payment_status == "Pending") {
           		$this->log_error('Paypal PDT Error', 'Error: Incorrect payment_status from Paypal PDT, "'.$payment_status.'" instead of "Completed". Pending reason response is '.$pdt_return["pending_reason"].' (order_main.cid = '.$order_main_cid.')');

           		switch($what_to_buy){
						case "point" : 
							redirect('order/package-point/status/'.md5('pending'));
							return "";
							break;
						case "basket" : 
							redirect('basket/confirm/status/'.md5('pending'));
							return "";
							break;
						default : 
							redirect('home/status/'.md5('pending'));
							return "";
							break;
					}
           	}
           	else {
           		$this->log_error('Paypal PDT Error', 'Error: Incorrect payment_status from Paypal PDT, "'.$payment_status.'" instead of "Completed"');

           		switch($what_to_buy){
						case "point" : 
							redirect('order/package-point/status/'.md5('fail'));
							return "";
							break;
						case "basket" : 
							redirect('basket/confirm/status/'.md5('fail'));
							return "";
							break;
						default : 
							redirect('home/status/'.md5('fail'));
							return "";
							break;
					}
           	}
		    }
		    else if (strcmp ($lines[0], "FAIL") == 0) {
	        	// log for manual investigation
	        	$this->log_error('Paypal PDT Error', 'Paypal IPN returns FAIL');
		    	
				switch($what_to_buy){
					case "point" : 
						redirect('order/package-point/status/'.md5('fail'));
						return "";
						break;
					case "basket" : 
						redirect('basket/confirm/status/'.md5('fail'));
						return "";
						break;
					default : 
						redirect('home/status/'.md5('fail'));
						return "";
						break;
				}
		    }
		    else {
		    	// log for manual investigation
	        	$this->log_error('Paypal PDT Error', 'Unknown PDT return result "'.$res.'"');
		    	
				switch($what_to_buy){
					case "point" : 
						redirect('order/package-point/status/'.md5('fail'));
						return "";
						break;
					case "basket" : 
						redirect('basket/confirm/status/'.md5('fail'));
						return "";
						break;
					default : 
						redirect('home/status/'.md5('fail'));
						return "";
						break;
				}
		    }
		}
	}
	
   function save_back_from_paypal_back($what_to_buy) {
     @define("thisAction","save_back_from_paypal_back");
     // Read POST data
     // reading posted data directly from $_POST causes serialization
     // issues with array data in POST. Reading raw POST data from input stream instead.
     $raw_post_data = file_get_contents('php://input');
     $raw_post_array = explode('&', $raw_post_data);
     $ipn_return = array();
     foreach ($raw_post_array as $keyval) {
         $keyval = explode ('=', $keyval);
         if (count($keyval) == 2)
            $ipn_return[$keyval[0]] = urldecode($keyval[1]);
     }
     // read the post from PayPal system and add 'cmd'
     $req = 'cmd=_notify-validate';
     if(function_exists('get_magic_quotes_gpc')) {
         $get_magic_quotes_exists = true;
     }
     foreach ($ipn_return as $key => $value) {
         if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                 $value = urlencode(stripslashes($value));
         } else {
                 $value = urlencode($value);
         }
         $req .= "&$key=$value";
     }

     // Post IPN data back to PayPal to validate the IPN data is genuine
     // Without this step anyone can fake IPN data

     if(CONST_USE_PAYPAL_SANDBOX == "1") {
         $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
     } else {
         $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
     }

     $ch = curl_init($paypal_url);
     if ($ch == FALSE) {
         return FALSE;
     }

     curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
     curl_setopt($ch, CURLOPT_POST, 1);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
     curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

     if(DEBUG == true) {
         curl_setopt($ch, CURLOPT_HEADER, 1);
         curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
     }

     // CONFIG: Optional proxy configuration
     //curl_setopt($ch, CURLOPT_PROXY, $proxy);
     //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);

     // Set TCP timeout to 30 seconds
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

     // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
     // of the certificate as shown below. Ensure the file is readable by the webserver.
     // This is mandatory for some environments.

     //$cert = __DIR__ . "./cacert.pem";
     //curl_setopt($ch, CURLOPT_CAINFO, $cert);

     $res = curl_exec($ch);
     if (curl_errno($ch) != 0) { // cURL error
         if(DEBUG == true) {        
             error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
         }
         $this->log_error("Paypal Notification Error", date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch));
         curl_close($ch);
         exit;

     } else {
         // Log the entire HTTP response if debug is switched on.
         if(DEBUG == true) {
             error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
             error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
         }
         $this->log_status("Paypal IPN Notification", date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req");
         $this->log_status("Paypal IPN Notification", date('[Y-m-d H:i e] '). "HTTP response of validation request: $res");

         $this->log_status('Paypal IPN original response', $res);

         if (CONST_USE_PAYPAL_SANDBOX==0) { 
             // Split response headers and payload
             list($headers, $res) = explode("\r\n\r\n", $res, 2);
         }
         else {
             // Response from SANDBOX
            // $res = str_replace("\n\r", "\n", $res);
            // list($header1, $header2, $res) = explode("\n\n", $res, 3);
            $tmp = explode("\r\n\r\n", $res);
            $res = end($tmp);
         }
         curl_close($ch);
     }

     // Inspect IPN validation result and act accordingly

     if (strcmp ($res, "VERIFIED") == 0) {
         // check whether the payment_status is Completed
         // check that txn_id has not been previously processed
         // check that receiver_email is your PayPal email
         // check that payment_amount/payment_currency are correct
         // process payment and mark item as paid.

         $this->load->model($this->log_paypal_model,"paypal");

         // assign posted variables to local variables
         $payer_email = $_POST['payer_email'];
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$mc_gross = $_POST['mc_gross'];
			$mc_currency = $_POST['mc_currency'];
			$payer_id = $_POST['payer_id'];
			$payment_date = $_POST['payment_date'];
			$payment_status = $_POST['payment_status'];
			$txn_id = $_POST['txn_id'];
			$receiver_email = $_POST['receiver_email'];
			$order_main_cid = $_POST['custom'];

			$data = array();
			$data["payer_email"] = $payer_email;
			$data["first_name"] = $first_name;
			$data["last_name"] = $last_name;
			$data["mc_gross"] = $mc_gross;
			$data["mc_currency"] = $mc_currency;
			$data["payer_id"] = $payer_id;
			$data["payment_date"] = $payment_date;
			$data["payment_status"] = $payment_status;
			$data["txn_id"] = $txn_id;
			$data["controller"] = $what_to_buy;
			$data["action"] = "save_back_from_paypal_back";
			$data["owner_user_aid"] = getUserOwnerAid($this);
			$data["owner_detail"] = getUserOwnerDetailForLog($this);
			$chk = $this->paypal->insert_record($data);

         if (!empty($order_main_cid) && $order_main_cid != "-") {
             $this->load->model($this->order_main_model,"order_main");
             $this->order_main->set_where(array("cid" => $order_main_cid));
             $order_result = $this->order_main->load_record(true);

				if(!is_var_array($order_result)){
					echo "Order result not found";
					$this->log_error('Paysbuy Feedback', 'Order not found ['.$order_main_cid.']. Result form paysbuy = ['.$result_full.']. Do nothing.', $log_result);
					return"";
				}
				$order_status = get_array_value($order_result,"status","");
				if($order_status == '3'){ //1=New coming, 2=In Process, 3=Approved, 4=Rejected
					$this->log_status('Paysbuy Feedback', 'Order ['.$order_main_cid.'] already approved. Result form paysbuy = ['.$result_full.']. Do nothing.', $log_result);
					return"";
				}
				
				if($order_status == '4'){ //1=New coming, 2=In Process, 3=Approved, 4=Rejected
					echo "Order already rejected";
					$this->log_status('Paysbuy Feedback', 'Order ['.$order_main_cid.'] already rejected. Result form paysbuy = ['.$result_full.']. Do nothing.', $log_result);
					return"";
				}
				
             if ($payment_status == "Completed") {
                 if ($receiver_email == CONST_EMAIL_FOR_PAYPAL) {
                     if ($mc_gross == $order_result["actual_grand_total"]) {
                         if ($mc_currency == "THB") {
                             	// Update status on user_waiting_paypal
                             	$this->order_main->set_where(array("cid" => $order_main_cid));
                             	$this->order_main->update_record(array(
                                 	"status" => "3"
                             		)
                             	);

                             	$this->log_status('Paypal IPN Success', '"'.$order_main_cid.'"" was successfully processed (status = 3). The paypal txn_id = '.$txn_id);

                             	switch($what_to_buy){
											case "point" : 
												$status = $this->save_approve_point($txn_id, $order_result);
												break;
											case "basket" : 
												$status = $this->save_approve_basket($txn_id, $order_result);
												break;
											default : 
												break;
										}
										$order_result["paypal_result"] = $status;
										return $order_result;
                         }
                         else {
                             $this->log_error('Paypal IPN Error', 'Error: Incorrect mc_currency (payment currency) from Paypal, "'.$mc_currency.'" instead of "THB" (order_main.cid = '.$order_main_cid.')');
                         }
                     }
                     else {
                         $this->log_error('Paypal IPN Error', 'Error: Incorrect mc_gross (payment amount) from Paypal, "'.$mc_gross.'" instead of "'.$order_result["actual_grand_total"].'" (order_main.cid = '.$order_main_cid.')');
                     }
                 }
                 else {
                     $this->log_error('Paypal IPN Error', 'Error: Incorrect receiver_email from Paypal, "'.$receiver_email.'" instead of "'.CONST_EMAIL_FOR_PAYPAL.'" (order_main.cid = '.$order_main_cid.')');
                 }
             }
             else {
                 $this->log_error('Paypal IPN Error', 'Error: Incorrect payment_status from Paypal, "'.$payment_status.'" instead of "Completed". Pending reason response is '.$_POST["pending_reason"].' (order_main.cid = '.$order_main_cid.')');
             }
         }
         else {
             $this->log_error('Paypal IPN Error', 'Error: Payment has been processed successfully but No data in field "custom" ($order_main_cid) returned back from Paypal, so we do not know this payment belongs to which order, mc_gross (payment amount) = "'.$mc_gross.'" (txn_id = "'.$txn_id.'")');
         }

         if(DEBUG == true) {
             error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
         }
         $this->paypal_save_reject($txn_id, $order_result);
			$order_result["paypal_result"] = "fail";
			return $order_result;
     } 
     else if (strcmp ($res, "INVALID") == 0) {
         // log for manual investigation
         // Add business logic here which deals with invalid IPN messages
         $this->log_error('Paypal IPN Error', 'Paypal IPN returns INVALID');

         if(DEBUG == true) {
             error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
         }
         $this->paypal_save_reject($txn_id, $order_result);
			$order_result["paypal_result"] = "fail";
			return $order_result;
     }
     else {
         $this->log_error('Paypal IPN Error', 'Unknown IPN Result "'.$res.'"');

         if(DEBUG == true) {
             error_log(date('[Y-m-d H:i e] '). $res." IPN: $req" . PHP_EOL, 3, LOG_FILE);
         }
         $this->paypal_save_reject($txn_id, $order_result);
			$order_result["paypal_result"] = "fail";
			return $order_result;
     }
   }

	function paypal_save_reject($txn_id="",$order_result=""){
		$order_main_cid = get_array_value($order_result,"cid","");
		$log_result = array();
		$log_result["txn_id"] = $txn_id;
		$log_result["order_data"] = $order_result;
		$this->log_status('Paypal Feedback', 'Start!! Order ['.$order_main_cid.']. Paypal txn_id = ['.$txn_id.'].', $log_result);
		
		$this->save_reject("paysbuy", $order_result, $log_result);
	}

}

?>