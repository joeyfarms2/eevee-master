<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/product/product_init_controller.php");

class Product_front_controller extends Product_init_controller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		define("thisFrontTabMenu",'home');
		define("thisFrontSubMenu",'');
		@define("folderName",'product/product_front');
						
		$this->lang->load('product');

		$this->reserve_model = "Reserve_model";
		$this->reserve_product_model = "Reserve_product_model";
		$this->ads_model = "Ads_model";
	}
	
	function index(){
		return "";
	}


	function detail($product_type_cid="", $aid="", $status=""){
		@define("thisAction","detail");
		$this->data["mode"] = "front";
		$this->data["title"] = DEFAULT_TITLE;
		if(!is_blank($status)){
			switch ($status) {
				case 'not-on-shelf':
					$this->data["message"] = set_message_error("Book not found.");
					break;
				case 'file-not-found':
					$this->data["message"] = set_message_error("eBook error found! Please notify E-Library.");
					break;
				case 'not-login':
					$this->data["message"] = set_message_error("Please login.");
					break;
				default:
					break;
			}
		}

		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		$this->data["page_title"] = get_array_value($product_type_detail,"name","N/A").'Detail';
		// $this->data["page_title"] = $this->lang->line('product_pagee_title_detail');
		if($product_type_aid == 3){
			$this->data["view_the_content"] = $this->default_theme_front . '/' . folderName . '/vdo_detail';
		}else{
			$this->data["view_the_content"] = $this->default_theme_front . '/' . folderName . '/product_detail';
		}
		$this->db->flush_cache();
		$model = $this->get_product_model($product_type_aid);
		$this->load->model(get_array_value($model,"product_model",""),"main");
		$result = $this->main->increase_total_view_web($aid);	
		
		$this->db->flush_cache();
		// echo get_array_value($model,"product_model","");
		$this->load->model(get_array_value($model,"product_model",""), "main");
		$this->main->set_where(array("aid"=>$aid));
		if(!exception_about_status()) $this->main->set_where(array("status"=>'1'));
		$item_result = $this->main->load_record(true);
		//echo "<br>sql : ".$this->db->last_query();
		$this_product_main_url = "";
		if(is_var_array($item_result)){
			// print_r($item_result);
			$user_section_aid = getUserLoginSectionAid($this);
			$this->load->model($this->product_category_ref_user_section_model,"ref_user_section");
			$this->ref_user_section->set_where(array("user_section_aid"=>$user_section_aid));
			$ref_user_section_all = $this->ref_user_section->load_records_array(false,"","product_category_aid");

			$category = get_array_value($item_result,"category","");
			$chk = true;
			if(!is_blank($category) && is_var_array($ref_user_section_all)){
				$chk = false;
				foreach ($ref_user_section_all as $item) {
					$cid = ",".$item.",";
					if(!is_blank(strpos($category, $cid))){
						$chk = true;
					}
				}
			}
			if(!$chk){
				redirect('home/status/'.md5('product-not-found'));
				return "";
			}

			$this->db->flush_cache();
			$this->load->model(get_array_value($model,"product_model",""),"main");
			$result = $this->main->increase_total_view_web($aid);	
			
			$this_product_main_url = get_array_value($item_result,"product_main_url","");

			$parent_aid = get_array_value($item_result,"aid","");

			if(is_login() && $product_type_aid == 3){
				
				$this->load->model($this->shelf_vdo_model,"shelf_vdo");
				$tmp = array();
				$tmp["user_aid"] = getSessionUserAid();
				$tmp["parent_aid"] = $parent_aid;
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["status"] = "1";
				$this->shelf_vdo->set_where($tmp);
				$shelf_vdo_result = $this->shelf_vdo->load_record(false);
				$this->data["shelf_vdo_result"] = $shelf_vdo_result;
				// echo "<br>sql : ".$this->db->last_query();
			}
			
			$this->db->flush_cache();
			$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
			$product_copy_result = $this->product_copy->get_product_copy_by_parent_aid($parent_aid);
			
			$product_copy_list = "";

			$digital_list = "";
			$paper_list = "";
			
			if(is_var_array($product_copy_result)){
				foreach($product_copy_result as $item){
					// print_r($item);
					// echo "<hr>";
					$product_copy_aid = get_array_value($item,"aid","0");
					// echo "product_copy_aid = $product_copy_aid <BR>";
					$barcode = get_array_value($item,"barcode","");
					$copy_title = get_array_value($item,"copy_title","");

					$type = get_array_value($item,"type","");
					$is_license = get_array_value($item,"is_license","0");
					$ebook_concurrence = get_array_value($item,"ebook_concurrence","");
					$possession = get_array_value($item,"possession","");

					$digital_price = get_array_value($item,"digital_price","");
					$digital_point = get_array_value($item,"digital_point","");
					$paper_price = get_array_value($item,"paper_price","");
					$paper_point = get_array_value($item,"paper_point","");
					$in_stock = get_array_value($item,"in_stock","");
					$rental_period = get_array_value($item,"rental_period","");
					$rental_fee = get_array_value($item,"rental_fee","");
					$rental_fine_fee = get_array_value($item,"rental_fine_fee","");

					$shelf_status = get_array_value($item,"shelf_status","");
					$shelf_status_name = get_array_value($item,"shelf_status_name","");
					$shelf_name = get_array_value($item,"shelf_name","");

					$item["product_type_cid"] = $product_type_cid;
					$desc = array();

					if(!is_blank($copy_title)){
						$item["copy_title"] = $copy_title;						
					}

					if($type == '1'){ //digital
						//Find max concurrance
						$this->load->model($this->shelf_model,"shelf");
						$this->shelf->set_where(array("copy_aid"=>$product_copy_aid, "product_type_aid"=>$product_type_aid));
						$shelfResult = $this->shelf->load_records(false);
						// print_r($shelfResult);
						$total_available = $ebook_concurrence;
						$is_max_concurrence = '0';
						$is_max_on_myshlef = '0';
						$is_on_myshelf = '0';
						if(is_var_array($shelfResult)){
							// echo "count = ".count($shelfResult)." , ebook_concurrence = $ebook_concurrence <BR>";
							if($is_license == '1' && count($shelfResult) >= $ebook_concurrence){
								// echo "status = is_max_concurrence";
								$total_available = 0;
								$is_max_concurrence = '1';
							}else if($is_license == '1'){
								$total_available = $ebook_concurrence - count($shelfResult);
								// echo "status = ".$total_available;
							}else{
								// echo "status = unlimited";
								$total_available = "unlimited";
							}

							if(is_login()){
								foreach($shelfResult as $subitem){
									$user_aid = get_array_value($subitem,"user_aid","none");
									if($user_aid == getSessionUserAid() && $product_type_aid == get_array_value($subitem,"product_type_aid","")){
										$is_on_myshelf = '1';
									}
								}
							}
						}
						// echo "is_max_concurrence = $is_max_concurrence".count($shelfResult);
						
						if(is_login()){
							//check max on shelf
							$this->load->model($this->shelf_model,"shelf");
							$this->shelf->set_where(array("status"=>'1', "user_aid"=>getSessionUserAid(), "is_license"=>'1'));
							$shelfResult = $this->shelf->load_records(false);
							if(is_var_array($shelfResult)){
								if($is_license == '1' && count($shelfResult) >= CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF){
									$is_max_on_myshlef = '1';
								}
							}
						}

						$this->load->model($this->reserve_model,"reserve");
						$this->reserve->set_where(array("status"=>'1', "copy_aid"=>$product_copy_aid, "product_type_aid"=>$product_type_aid));
						$reserveResult = $this->reserve->load_records(false);
						// echo "<br>sql : ".$this->db->last_query();
						// print_r($reserveResult);
						if(is_var_array($reserveResult)){
							$total_available = 0;
							$is_max_concurrence = '1';
							// foreach ($reserveResult as $reserve) {
							// 	$user_aid = get_array_value($reserve,"user_aid","");
							// 	$confirm_code = get_array_value($reserve,"confirm_code","");
							// 	if($user_aid == getSessionUserAid() && !is_blank($confirm_code)){
							// 		$total_available = 1;
							// 	}
							// }
						}

						$item["total_available"] = $total_available;
						$item["is_max_concurrence"] = $is_max_concurrence;
						$item["is_max_on_myshlef"] = $is_max_on_myshlef;
						$item["is_on_myshelf"] = $is_on_myshelf;
						if($is_on_myshelf){ // book on shelf
							if($is_license){
								$item["button"] = "";
								$desc_txt = $this->lang->line('product_on_shelf_app_only');
								// $desc_txt = '';
								$desc[] = $desc_txt;
								$item["description_list"] = $desc;
							}else{
								$item["button"] = "read";
								$desc_txt = $this->lang->line('product_on_shelf');
								$desc[] = $desc_txt;
								$item["description_list"] = $desc;
							}
							$item["show_price"] = false; 
							$item["price"] = ""; 
							$item["point"] = ""; 


						}else{ //book not on shelf
							if($possession == '1'){ //Buy out
								$total_available = "unlimited";
								if($digital_price <= 0 || $digital_point <= 0){ // free
									$item["button"] = (is_login()) ? "add_to_shelf" : "required_login";
									$item["total_available"] = $total_available; 
									$item["show_price"] = false; 
									$item["price"] = ""; 
									$item["point"] = ""; 
								}else{ //not free
									$item["button"] = "add_to_cart";
									$item["total_available"] = $total_available; 
									$item["show_price"] = true; 
									$item["price"] = $digital_price;
									$item["point"] = $digital_point;
									$remark = "";
									$remark_txt = $this->lang->line('product_for_app_only');
									$remark[] = $remark_txt;
									$item["remark_list"] = $remark;

								}
							}else if($possession == '2'){ //rental
								$remark = "";
								if($rental_fee <= 0){ // free
									$item["button"] = (is_login()) ? "add_to_shelf" : "required_login";
									$item["show_price"] = false;
									$item["price"] = "";
									$item["point"] = "";
								}else{ //not free
									$item["button"] = "add_to_cart";
									$item["show_price"] = true;
									$item["price"] = $rental_fee;
									$item["point"] = $rental_fee;
									$remark_txt = $this->lang->line('product_for_app_only');
									$remark[] = $remark_txt;
								}

								if($is_license){
									if($is_max_concurrence){
										$item["button"] = (is_login()) ? "reserve" : "required_login";
										$this->load->model($this->reserve_model,"reserve");
										$tmp = array();
										$tmp["product_type_aid"] = $product_type_aid;
										$tmp["copy_aid"] = $product_copy_aid;
										$tmp["status"] = '1';
										$this->reserve->set_where($tmp);
										$this->reserve->set_order_by("created_date asc");
										$queue_result = $this->reserve->load_records(false);
										$queue = 0;
										$my_queue = 0;

										if(is_var_array($queue_result)){
											$queue = count($queue_result);
											if(is_login()){
												$i = 1;
												foreach ($queue_result as $q) {
													$q_user_aid = get_array_value($q,"user_aid","0");
													$q_confirm_code = get_array_value($q,"confirm_code","");
													if($q_user_aid == getSessionUserAid()){
														if(!is_blank($q_confirm_code)){
															$item["total_available"] = 1;
															$item["button"] = "add_to_shelf";
														}else{
															$my_queue = $i;
														}
													}
													$i++;
												}
											}
										}
										$item["queue"] = $queue;
										$item["my_queue"] = $my_queue;

										// $this->db->flush_cache();
										// $this->load->model($this->reserve_model,"reserve");
										// $tmp = array();
										// $tmp["user_aid"] = getSessionUserAid();
										// $tmp["product_type_aid"] = $product_type_aid;
										// $tmp["copy_aid"] = $product_copy_aid;
										// $tmp["status"] = '1';
										// $this->reserve->set_where($tmp);
										// $myqueue = $this->reserve->load_record(false);
										// $item["myqueue"] = $myqueue;


										// $remark_txt = $this->lang->line('product_rental_out_of_stock');
										// $remark[] = $remark_txt;
									}else if($is_max_on_myshlef){
										$item["button"] = "";
										$remark_txt = $this->lang->line('product_concurrence_shelf_max');
										$remark_txt = 'Your circulating book shelf is max.';
										$remark[] = $remark_txt;
									}else{
										$remark_txt = $this->lang->line('product_concurrence_prefix').$total_available.$this->lang->line('product_concurrence_middle').$ebook_concurrence.$this->lang->line('product_concurrence_postfix');
										$remark[] = $remark_txt;
									}
									$remark_txt = $this->lang->line('product_for_app_only');
									$remark[] = $remark_txt;
								}else{
									$remark_txt = $this->lang->line('product_unlimited');
									$remark[] = $remark_txt;
								}

								if($rental_period > 0){
									$remark_txt = $this->lang->line('product_concurrence_day_prefix').$rental_period.$this->lang->line('product_concurrence_day_postfix');
									$remark[] = $remark_txt;
								}
								$item["remark_list"] = $remark;
							}
						}
						$digital_list[] = $item;
					}else if ($type == '2') { //paper
						if($possession == '1'){ //Buy out
							$item["button"] = "add_to_cart";
							$item["show_price"] = true;
							$item["price"] = $paper_price; 
							$item["point"] = $paper_point;
							$item["in_stock"] = $in_stock;
							$remark = "";
							if($in_stock <= 5){
								if($in_stock <= 0){
									$item["button"] = "add_to_cart_disabled";
									$remark_txt = $this->lang->line('product_buyout_out_of_stock');
								}else{
									$remark_txt = $this->lang->line('product_buyout_stock_prefix').$in_stock.$this->lang->line('product_buyout_stock_postfix');
								}
								$remark[] = $remark_txt;
								$item["remark_list"] = $remark;
							}
						}else if($possession == '2'){
							$item["button"] = "";
							$item["show_price"] = false;
							$item["price"] = "";
							$item["point"] = "";
							
							$desc_txt = '<div class="col-sm-12 col-md-4 text-left">Barcode : '.$barcode.'</div>';
							$desc_txt .= '<div class="col-sm-12 col-md-2 text-left">'.$shelf_status_name.'</div>';
							$desc_txt .= '<div class="col-sm-12 col-md-5 text-left">'.$shelf_name.'</div>';
							$desc[] = $desc_txt;
							$item["description_list"] = $desc;

							$this->load->model($this->reserve_product_model,"reserve_product");
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["copy_aid"] = $product_copy_aid;
							// $tmp["status"] = "1";
							$data_search = "";
							$data_search["status"][] = "1";
							$data_search["status"][] = "2";
							$this->reserve_product->set_where($tmp);
							$this->reserve_product->set_where_in($data_search);
							$queue_result = $this->reserve_product->load_records(false);
							$queue = 0;
							$my_queue = 0;
							$my_turn = 0;
							$my_expiration_date = 0;
							if(is_var_array($queue_result)){
								$queue = count($queue_result);
								if(is_login()){
									$i = 1;
									foreach ($queue_result as $q) {
										$q_user_aid = get_array_value($q,"user_aid","0");
										$q_confirm_code = get_array_value($q,"confirm_code","");
										$q_status = get_array_value($q,"status","");
										$q_expiration_date = get_datetime_pattern("Y-m-d",get_array_value($q,"expiration_date",""),"");
										if($q_user_aid == getSessionUserAid()){
											if($q_status == "2"){
												$my_turn = 1;
												$my_expiration_date = $q_expiration_date;
											}
											$my_queue = $i;
											break;
										}
										$i++;
									}
								}
							}
							$item["queue"] = $queue;
							$item["my_queue"] = $my_queue;
							$item["my_turn"] = $my_turn;
							$item["my_expiration_date"] = $my_expiration_date;
						}
						$paper_list[] = $item;
					}
					$product_copy_list[] = $item;
				}
			}

			$item_result["product_copy_list"] = $product_copy_list;
			$this->data["item_result"] = $item_result;
			$this->data["digital_list"] = $digital_list;
			$this->data["paper_list"] = $paper_list;
			$this->data["product_type_detail"] = $product_type_detail;
			$this->data["product_type_cid"] = $product_type_cid;
			$this->load->view($this->default_theme_front.'/tpl_detail', $this->data);
		}else{
			redirect('home/status/'.md5('product-not-found'));
		}
	}	
	
	function category_main_page($show_option="", $product_type_cid="", $product_main_url="", $page_selected="")
	{
		$this->category($show_option, $product_type_cid, $product_main_url, "", "", "", $page_selected);
	}
	
	function category_main_sort($show_option="", $product_type_cid="", $product_main_url="", $sort_by="")
	{
		$this->category($show_option, $product_type_cid, $product_main_url, "", "", $sort_by, "");
	}
	
	function category_main_sort_page($show_option="", $product_type_cid="", $product_main_url="", $sort_by="", $page_selected="")
	{
		$this->category($show_option, $product_type_cid, $product_main_url, "", "", $sort_by, $page_selected);
	}
	
	function category_all_page($show_option="", $product_type_cid="", $product_main_url="", $category_url="", $publisher_url="", $page_selected="")
	{
		$this->category($show_option, $product_type_cid, $product_main_url, $category_url, $publisher_url, "", $page_selected);
	}


	
	function category($show_option="", $product_type_cid="", $product_main_url="", $category_url="", $publisher_url="", $sort_by="", $page_selected="")
	{
		@define("thisAction","home");
		$this->data["title"] = DEFAULT_TITLE;

		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");
		$this->data["product_type_aid"] = $product_type_aid;
		$this->data["product_type_cid"] = $product_type_cid;
		$this->data["product_type_detail"] = $product_type_detail;

		$model = $this->get_product_model($product_type_aid);

		if($product_type_aid == 3){
			$this->data["view_the_content"] = $this->default_theme_front . '/home/show_vdo';
		}else{
			$this->data["view_the_content"] = $this->default_theme_front . '/home/show';
		}

		if(is_blank($show_option)){
			$show_option = "shelf";
		}

		// echo "product_type_cid = $product_type_cid, product_main_url = $product_main_url, category_url = $category_url, publisher_url = $publisher_url, = $sort_by, page_selected = $page_selected";

		// exit(0);

		$category_active_class = "active";
		$category_fade_class = "active in";
		$publisher_active_class = "";
		$publisher_fade_class = "";
		
		$sort_by = trim(strtolower($sort_by));
		if($sort_by != "pop_a" && $sort_by != "pop_d" && $sort_by != "date_a" && $sort_by != "date_d" && $sort_by != "name_a" && $sort_by != "name_d" && $sort_by != "author_a" && $sort_by != "author_d" && $sort_by != "category_a" && $sort_by != "category_d" ){
			$sort_by = "date_d";
		}
		
		if($category_url == '0'){
			$category_url = "";
		}
		if($publisher_url == '0'){
			$publisher_url = "";
		}

		$optional = array();
		
		$product_main_aid = "";
		$category_url = urldecode($category_url);
		$product_main_url = urldecode($product_main_url);
		$result = "";
		// echo $product_main_url."<BR>".$category_url."<BR>".$page_selected;
		if(!is_blank($product_main_url)){
			$this->load->model($this->product_main_model,"product_main");
			$this->product_main->set_where(array("url"=>$product_main_url));
			if(!exception_about_status()) $this->product_main->set_where(array("status"=>'1'));
			$result = $this->product_main->load_record(false);
			if(is_var_array($result)){
				$product_main_aid = get_array_value($result,"aid","");
			}
			if(is_blank($product_main_aid)){
				redirect('home/status/'.md5('product-main-not-found'));
				return"";
			}
			
		}
		if(CONST_HAS_ADS == '1'){
			$this->get_ads_by_products_main_aid($product_main_aid);
		}
		
		$this->data["product_main_aid"] = $product_main_aid;
		$this->data["this_product_main_name"] = get_array_value($result,"name","");
		$this->data["this_product_main_url"] = get_array_value($result,"url","");
		
		$this->load->model(get_array_value($model,"product_model",""),"main");

		

		$category_aid = "";
		$category_url = urldecode($category_url);
		$this->data["this_category_url"] = $category_url;
		if(!is_blank($category_url)){
			$category_active_class = "active";
			$category_fade_class = "active in";
			$publisher_active_class = "";
			$publisher_fade_class = "";
			$this->load->model($this->product_category_model,"category");
			$this->category->set_where(array("url"=>$category_url,"product_main_aid"=>$product_main_aid));
			if(!exception_about_status()) $this->category->set_where(array("status"=>'1'));
			$result = $this->category->load_record(false);
			if(is_var_array($result)){
				$category_aid = get_array_value($result,"aid","");
				//$this->data["category_result"] = $result;

				$user_section_aid = getUserLoginSectionAid($this);
				$this->load->model($this->product_category_ref_user_section_model,"ref_user_section");
				$this->ref_user_section->set_where(array("user_section_aid"=>$user_section_aid));
				$ref_user_section_all = $this->ref_user_section->load_records_array(false,"","product_category_aid");
				//print_r($ref_user_section_all);

				if(!is_in_array($category_aid, $ref_user_section_all)){
					redirect('home/status/'.md5('category-not-found'));
					return"";
				}

				$this->data["this_category_parent_aid"] = get_array_value($result,"parent_aid","0");
				$this->data["this_category_aid"] = $category_aid;
				$this->data["this_category_url"] = $category_url;

			}
			if(is_blank($category_aid)){
				redirect('home/status/'.md5('category-not-found'));
				return"";
			}
		}
		
		$publisher_aid = "";
		$publisher_url = urldecode($publisher_url);
		$this->data["this_publisher_url"] = $publisher_url;
		if(!is_blank($publisher_url)){
			$category_active_class = "";
			$category_fade_class = "";
			$publisher_active_class = "active";
			$publisher_fade_class = "active in";
			$this->load->model($this->publisher_model,"publisher");
			$this->publisher->set_where(array("url"=>$publisher_url));
			if(!exception_about_status()) $this->publisher->set_where(array("status"=>'1'));
			$result = $this->publisher->load_record(false);
			if(is_var_array($result)){
				$publisher_aid = get_array_value($result,"aid","");
			}
			if(!is_number_no_zero($publisher_aid)){
				redirect('home/status/'.md5('publisher-not-found'));
				return"";
			}
		}
		
		$this->load->model($this->product_category_model,"product_category");
		$product_category_all = $this->product_category->load_master(true);
		// print_r($product_category_all);

		$user_section_aid = getUserLoginSectionAid($this);
		$this->load->model($this->product_category_ref_user_section_model,"ref_user_section");
		$this->ref_user_section->set_where(array("product_category.product_main_aid"=>$product_main_aid, "user_section_aid"=>$user_section_aid, "product_category.status"=>"1"));
		$this->ref_user_section->set_order_by("product_category.weight asc");
		$ref_user_section_all = $this->ref_user_section->load_records(true);
		//echo "<br>sql : ".$this->db->last_query();
		//print_r($ref_user_section_all);

		//print_r($ref_user_section_all);
		$category_result = "";
		if(is_var_array($ref_user_section_all)){
			foreach ($ref_user_section_all as $item) {
				$product_category_aid = get_array_value($item,"product_category_aid","");
				$parent_aid = get_array_value($item,"parent_aid","0");
				//echo "product_category_aid = $product_category_aid , parent_aid = $parent_aid <BR />";
				// print_r($product_category_all[$product_category_aid]);
				if($parent_aid > 0){
					// echo "product_category_aid = $product_category_aid , parent_aid = $parent_aid <BR />";
					if(is_blank(get_array_value($category_result,$parent_aid,""))){
						$category_result[$parent_aid] = get_array_value($product_category_all,$parent_aid,"");
					}
					$category_result[$parent_aid]["child"][] = get_array_value($product_category_all,$product_category_aid,"");
				}else{
					$category_result[$product_category_aid] = get_array_value($product_category_all,$product_category_aid,"");
					//print_r($category_result);
				}
				// print_r($category_result);
				// echo "<HR>";
			}
		}
		//echo "<br>sql : ".$this->db->last_query();
		// print_r($category_result);
		$this->data["master_category"] = $category_result;


		// $this->load->model($this->product_category_model,"category");
		// $this->data["master_category"] = $this->category->load_category_by_product_main($product_main_aid);
		
		
		$this->db->flush_cache();
		$this->load->model(get_array_value($model,"product_model",""),"main");
		$this->db->start_cache();
		
		if(!is_blank($product_main_aid)){
			$this->main->set_where(array("product_main_aid"=>$product_main_aid));
		}
		if(!is_blank($category_aid)){
			$this->main->set_like(array("category"=>",".$category_aid.","),"both");
		}else{
			// $cat_arr = "";
			// $cat_arr[] = "";
			if(is_var_array($ref_user_section_all)){
				foreach ($ref_user_section_all as $item) {
					$cid = get_array_value($item,"product_category_aid","");
					if(!is_blank($cid)){
						$cat_arr[] = ",".$cid.",";

					}
				}
				//print_r($cat_arr);
				if(is_var_array($cat_arr)){
					$this->main->set_and_or_like_by_field("category", $cat_arr);
				}
			}else{
				$this->main->set_where(array("category"=>""));
			}
		}
		
		// // die();
		if(!is_blank($publisher_aid)){
			$this->main->set_where(array("publisher_aid"=>$publisher_aid),"both");
		}
		if(!exception_about_status()) $this->main->set_where(array("status"=>'1'));
		$optional["total_record"] = $this->main->count_records();
		//echo "<br>sql : ".$this->db->last_query();
		$optional["page_selected"] = $page_selected;
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_FOR_SEARCH;
		$url = 'list-'.$product_type_cid.'/category';
		$url_for_sort = 'list-'.$product_type_cid.'/category';
		$url_for_list = 'list-'.$product_type_cid.'/category';
		$url_for_shelf = 'list-'.$product_type_cid.'/category';
		if(!is_blank($product_main_url)){
			$url .= '/'.$product_main_url;
			$url_for_sort .= '/'.$product_main_url;
			$url_for_list .= '/'.$product_main_url.'-list';
			$url_for_shelf .= '/'.$product_main_url;
			if($show_option == "list"){
				$url .= '-list';
				$url_for_sort .= '-list';
			}
		}
		if(!is_blank($category_url)){
			$url .= '/c-'.$category_url;
			$url_for_sort .= '/c-'.$category_url;
			$url_for_list .= '/c-'.$category_url;
			$url_for_shelf .= '/c-'.$category_url;
		}
		if(!is_blank($publisher_url)){
			$url .= '/p-'.$publisher_url;
			$url_for_sort .= '/p-'.$publisher_url;
			$url_for_list .= '/p-'.$publisher_url;
			$url_for_shelf .= '/p-'.$publisher_url;
		}
		if(!is_blank($sort_by)){
			$url .= '/sort-'.$sort_by;
			$url_for_list .= '/sort-'.$sort_by;
			$url_for_shelf .= '/sort-'.$sort_by;
		}
		if($page_selected > 1){
			$url_for_list .= '/page-'.$page_selected;
			$url_for_shelf .= '/page-'.$page_selected;
		}
		$optional["url"] = $url.'/page-';
		
		$optional = $this->get_pagination_info($optional);
		
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_FOR_SEARCH));
		if($sort_by == 'pop_d'){
			$this->main->set_order_by("total_download DESC");
		}else if($sort_by == 'pop_a'){
			$this->main->set_order_by("total_download ASC");
		}else if($sort_by == 'date_d'){
			$this->main->set_order_by("created_date DESC");
		}else if($sort_by == 'date_a'){
			$this->main->set_order_by("created_date ASC");
		}else if($sort_by == 'name_d'){
			$this->main->set_order_by("title DESC");
		}else if($sort_by == 'name_a'){
			$this->main->set_order_by("title ASC");
		}else if($sort_by == 'author_d'){
			$this->main->set_order_by("author DESC");
		}else if($sort_by == 'author_a'){
			$this->main->set_order_by("author ASC");
		}else if($sort_by == 'category_d'){
			$this->main->set_order_by("*category_sort_name DESC");
		}else if($sort_by == 'category_a'){
			$this->main->set_order_by("*category_sort_name ASC");
		}else{
			$this->main->set_order_by("weight ASC, created_date DESC");
		}
		$this->data["resultList"] = $this->main->load_records(true);
					
		$this->data["sort_by"] = $sort_by;			
		$this->data["url_for_sort"] = $url_for_sort;			
		$this->data["url_for_list"] = $url_for_list;			
		$this->data["url_for_shelf"] = $url_for_shelf;			
		$optional["total_in_page"] = count($this->data["resultList"]);			
		$this->data["optional"] = $optional;			

		$this->data["category_active_class"] = $category_active_class;
		$this->data["category_fade_class"] = $category_fade_class;
		$this->data["publisher_active_class"] = $publisher_active_class;
		$this->data["publisher_fade_class"] = $publisher_fade_class;

		$this->data["show_option"] = $show_option;
		// echo "<br>sql : ".$this->db->last_query();
		// exit(0);
		$this->db->flush_cache();		

		$this->get_relate_content($product_main_aid, $product_type_aid);

		$this->load->view($this->default_theme_front . '/tpl_content', $this->data);
	}

	function category_set($show_option="", $product_type_cid="", $product_main_url="", $category_url="", $publisher_url="", $sort_by="", $page_selected=""){
		@define("thisAction","home");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/home/show_set';

		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");
		$this->data["product_type_aid"] = $product_type_aid;
		$this->data["product_type_cid"] = $product_type_cid;
		$this->data["product_type_detail"] = $product_type_detail;

		$model = $this->get_product_model($product_type_aid);

		if(is_blank($show_option)){
			$show_option = "shelf";
		}

		// echo "product_type_cid = $product_type_cid, product_main_url = $product_main_url, category_url = $category_url, publisher_url = $publisher_url, = $sort_by, page_selected = $page_selected";

		// exit(0);

		$category_active_class = "active";
		$category_fade_class = "active in";
		$publisher_active_class = "";
		$publisher_fade_class = "";
		
		$sort_by = trim(strtolower($sort_by));
		if($sort_by != "pop_a" && $sort_by != "pop_d" && $sort_by != "date_a" && $sort_by != "date_d" && $sort_by != "name_a" && $sort_by != "name_d" && $sort_by != "author_a" && $sort_by != "author_d" && $sort_by != "category_a" && $sort_by != "category_d" ){
			$sort_by = "date_d";
		}
		
		if($category_url == '0'){
			$category_url = "";
		}
		if($publisher_url == '0'){
			$publisher_url = "";
		}

		$optional = array();
		
		$product_main_aid = "";
		$category_url = urldecode($category_url);
		$product_main_url = urldecode($product_main_url);
		$result = "";
		// echo $product_main_url."<BR>".$category_url."<BR>".$page_selected;
		if(!is_blank($product_main_url)){
			$this->load->model($this->product_main_model,"product_main");
			$this->product_main->set_where(array("url"=>$product_main_url));
			if(!exception_about_status()) $this->product_main->set_where(array("status"=>'1'));
			$result = $this->product_main->load_record(false);
			if(is_var_array($result)){
				$product_main_aid = get_array_value($result,"aid","");
			}
			if(is_blank($product_main_aid)){
				redirect('home/status/'.md5('product-main-not-found'));
				return"";
			}
		}
		if(CONST_HAS_ADS == '1'){
			$this->get_ads_by_products_main_aid($product_main_aid);
		}
		
		$this->data["product_main_aid"] = $product_main_aid;
		$this->data["this_product_main_name"] = get_array_value($result,"name","");
		$this->data["this_product_main_url"] = get_array_value($result,"url","");
		
		$this->load->model(get_array_value($model,"product_model",""),"main");

		$category_aid = "";
		$category_url = urldecode($category_url);
		$this->data["this_category_url"] = $category_url;
		if(!is_blank($category_url)){
			$category_active_class = "active";
			$category_fade_class = "active in";
			$publisher_active_class = "";
			$publisher_fade_class = "";
			$this->load->model($this->product_category_model,"category");
			$this->category->set_where(array("url"=>$category_url,"product_main_aid"=>$product_main_aid));
			if(!exception_about_status()) $this->category->set_where(array("status"=>'1'));
			$result = $this->category->load_record(false);
			if(is_var_array($result)){
				$category_aid = get_array_value($result,"aid","");
				$this->data["category_result"] = $result;
			}
			if(is_blank($category_aid)){
				redirect('home/status/'.md5('category-not-found'));
				return"";
			}
		}
		
		$publisher_aid = "";
		$publisher_url = urldecode($publisher_url);
		$this->data["this_publisher_url"] = $publisher_url;
		if(!is_blank($publisher_url)){
			$category_active_class = "";
			$category_fade_class = "";
			$publisher_active_class = "active";
			$publisher_fade_class = "active in";
			$this->load->model($this->publisher_model,"publisher");
			$this->publisher->set_where(array("url"=>$publisher_url));
			if(!exception_about_status()) $this->publisher->set_where(array("status"=>'1'));
			$result = $this->publisher->load_record(false);
			if(is_var_array($result)){
				$publisher_aid = get_array_value($result,"aid","");
			}
			if(!is_number_no_zero($publisher_aid)){
				redirect('home/status/'.md5('publisher-not-found'));
				return"";
			}
		}
		
		$this->load->model($this->product_category_model,"category");
		$this->data["master_category"] = $this->category->load_category_by_product_main($product_main_aid);
		
		$url = 'list-'.$product_type_cid.'/category';
		$url_for_sort = 'list-'.$product_type_cid.'/category';
		$url_for_list = 'list-'.$product_type_cid.'/category';
		$url_for_shelf = 'list-'.$product_type_cid.'/category';
		if(!is_blank($product_main_url)){
			$url .= '/'.$product_main_url;
			$url_for_sort .= '/'.$product_main_url;
			$url_for_list .= '/'.$product_main_url.'-list';
			$url_for_shelf .= '/'.$product_main_url;
			if($show_option == "list"){
				$url .= '-list';
				$url_for_sort .= '-list';
			}
		}
		if(!is_blank($category_url)){
			$url .= '/c-'.$category_url;
			$url_for_sort .= '/c-'.$category_url;
			$url_for_list .= '/c-'.$category_url;
			$url_for_shelf .= '/c-'.$category_url;
		}
		if(!is_blank($publisher_url)){
			$url .= '/p-'.$publisher_url;
			$url_for_sort .= '/p-'.$publisher_url;
			$url_for_list .= '/p-'.$publisher_url;
			$url_for_shelf .= '/p-'.$publisher_url;
		}
		if(!is_blank($sort_by)){
			$url .= '/sort-'.$sort_by;
			$url_for_list .= '/sort-'.$sort_by;
			$url_for_shelf .= '/sort-'.$sort_by;
		}
		if($page_selected > 1){
			$url_for_list .= '/page-'.$page_selected;
			$url_for_shelf .= '/page-'.$page_selected;
		}
		$this->data["url_for_sort"] = $url_for_sort;			
		$this->data["url_for_list"] = $url_for_list;			
		$this->data["url_for_shelf"] = $url_for_shelf;			

		//load popular
		$this->load->model($this->view_all_products_with_detail,"v_all_products");
		$this->v_all_products->set_where(array("product_type_aid"=>$product_type_aid));
		if(!is_blank($product_main_aid)){
			$this->v_all_products->set_where(array("product_main_aid"=>$product_main_aid));
		}
		if(!is_blank($category_aid)){
			$this->v_all_products->set_like(array("category"=>",".$category_aid.","),"both");
		}
		if(!is_blank($publisher_aid)){
			$this->v_all_products->set_where(array("publisher_aid"=>$publisher_aid),"both");
		}
		if(!exception_about_status()) $this->v_all_products->set_where(array("status"=>'1'));
		$this->v_all_products->set_order_by("total_download DESC");
		$this->v_all_products->set_limit(0, 10);
		$popular_list = $this->v_all_products->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		$this->data["popular_list"] = $popular_list;

		//load new
		$this->load->model($this->view_all_products_with_detail,"v_all_products");
		$this->v_all_products->set_where(array("product_type_aid"=>$product_type_aid, "is_new"=>'1'));
		if(!is_blank($product_main_aid)){
			$this->v_all_products->set_where(array("product_main_aid"=>$product_main_aid));
		}
		if(!is_blank($category_aid)){
			$this->v_all_products->set_like(array("category"=>",".$category_aid.","),"both");
		}
		if(!is_blank($publisher_aid)){
			$this->v_all_products->set_where(array("publisher_aid"=>$publisher_aid),"both");
		}
		if(!exception_about_status()) $this->v_all_products->set_where(array("status"=>'1'));
		$this->v_all_products->set_order_by("*RAND()");
		$this->v_all_products->set_limit(0, 20);
		$new_list = $this->v_all_products->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		$this->data["new_list"] = $new_list;

		//load recommended
		$this->load->model($this->view_all_products_with_detail,"v_all_products");
		$this->v_all_products->set_where(array("product_type_aid"=>$product_type_aid, "is_recommended"=>'1'));
		if(!is_blank($product_main_aid)){
			$this->v_all_products->set_where(array("product_main_aid"=>$product_main_aid));
		}
		if(!is_blank($category_aid)){
			$this->v_all_products->set_like(array("category"=>",".$category_aid.","),"both");
		}
		if(!is_blank($publisher_aid)){
			$this->v_all_products->set_where(array("publisher_aid"=>$publisher_aid),"both");
		}
		if(!exception_about_status()) $this->v_all_products->set_where(array("status"=>'1'));
		$this->v_all_products->set_order_by("*RAND()");
		$this->v_all_products->set_limit(0, 10);
		$recommended_list = $this->v_all_products->load_records(true);
		// echo "<br>sql : ".$this->db->last_query();
		$this->data["recommended_list"] = $recommended_list;

		$this->get_relate_content($product_main_aid, $product_type_aid);

		$this->load->view($this->default_theme_front . '/tpl_content', $this->data);
	}
	
	function ajax_add_product_to_shelf($sid="" , $product_type_cid="", $copy_aid=""){
		@define("thisAction","ajax_add_product_to_shelf");
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
			$this->log_notice('Product : Download', getUserLoginNameForLog($this).' try to load [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf but no product found or product status=0.');
			$result_obj = array("status" => "error","msg" => "No product found.");
			echo json_encode($result_obj);
			return"";
		}
		
		$parent_aid = get_array_value($item_result,"parent_aid","0");
		$is_license = get_array_value($item_result,"is_license","0");
		$possession = get_array_value($item_result,"possession","0");
		$ebook_concurrence = get_array_value($item_result,"ebook_concurrence","0");

		$rental_period = get_array_value($item_result,"rental_period","");
		
		$expiration_date = "";
		
		if($is_license == "1"){
			// if($ebook_concurrence > 0){
				$this->load->model($this->shelf_model,"shelf");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["copy_aid"] = $copy_aid;
				$tmp["status"] = "1";
				$this->shelf->set_where($tmp);
				$now_on_shelf = $this->shelf->count_records(false);
				// echo "<br>sql : ".$this->db->last_query();
				// print_r($now_on_shelf);
				// echo "now_on_shelf = $now_on_shelf";
				if($now_on_shelf >= $ebook_concurrence){
					$this->log_error('Product : Download', getUserLoginNameForLog($this).' try to load [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf but it maximum concurrence.');
					$result_obj = array("status" => "error","msg" => "Out of library shelf!");
					echo json_encode($result_obj);
					return"";
				}
			// }
			
			$this->load->model($this->shelf_model,"shelf");
			$tmp = array();
			$tmp["user_aid"] = getSessionUserAid();
			$tmp["status"] = "1";
			$tmp["is_license"] = "1";
			$this->shelf->set_where($tmp);
			$now_on_my_shelf = $this->shelf->count_records(false);
			if($now_on_my_shelf >= CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF){
				$this->log_error('Product : Download', getUserLoginNameForLog($this).' try to load [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf but it maximum license ebook on his/her shlef.');
				$result_obj = array("status" => "error","msg" => "คุณได้ยืมหนังสือครบ ".$now_on_my_shelf." เล่มแล้ว กรุณาลบหนังสือออกจากชั้นหนังสือก่อน");
				echo json_encode($result_obj);
				return"";
			}
			$expiration_date = date("Y-m-d",strtotime("+".$rental_period." days"));
		}
		
		$tmp = array();
		$tmp["copy_aid"] = $copy_aid;
		$tmp["user_aid"] = getSessionUserAid();
		$this->db->flush_cache();
		$this->load->model($this->shelf_model,"shelf");
		$result = $this->shelf->set_where($tmp);
		$result = $this->shelf->load_record(false);
		
		if($result){ // not first time load
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["copy_aid"] = $copy_aid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["user_aid"] = getSessionUserAid();
			$tmp["status"] = '1';
			$tmp["is_license"] = $is_license;
			$tmp["is_read"] = '0';
			$tmp["expiration_date"] = $expiration_date;
			$this->load->model($this->shelf_model,"shelf");
			$result = $this->shelf->insert_or_update($tmp);
			
			if($result){
				$this->log_status('Product : Download', getUserLoginNameForLog($this).' load [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf again.');
				$result_obj = array("status" => "success","msg" => "", "total_download"=>get_array_value($item_result,"total_download","-"));
				echo json_encode($result_obj);
				return"";
			}else{
				$this->log_error('Product : Download', getUserLoginNameForLog($this).' try to load [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf but system error.');
				$result_obj = array("status" => "error","msg" => "Error occured.");
				echo json_encode($result_obj);
				return"";
			}
			
		}else{ //first time load
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["copy_aid"] = $copy_aid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["user_aid"] = getSessionUserAid();
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
			$data_where["user_aid"] = getSessionUserAid();
			$data_where["copy_aid"] = $copy_aid;
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
				$tmp["copy_aid"] = $copy_aid;
				$tmp["parent_aid"] = $parent_aid;
				$tmp["user_aid"] = getSessionUserAid();
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
				$this->update_reward_point($parent_result, getSessionUserAid());
				
				if($possession == "1"){
					//Update copy buyout
					$this->db->flush_cache();
					$this->load->model($this->copy_buyout_model,"copy_buyout");
					$tmp = array();
					$tmp["product_type_aid"] = $product_type_aid;
					$tmp["product_type_cid"] = $product_type_cid;
					$tmp["copy_aid"] = $copy_aid;
					$tmp["parent_aid"] = $parent_aid;
					$tmp["user_aid"] = getSessionUserAid();
					$tmp["status"] = '1';
					$tmp["price"] = '0';
					$result = $this->copy_buyout->insert_or_update($tmp);
				}
				
				//Add copy download for report
				$tmp = array();
				$tmp["order_main_aid"] = '';
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["product_type_cid"] = $product_type_cid;
				$tmp["is_license"] = $is_license;
				$tmp["copy_aid"] = $copy_aid;
				$tmp["parent_aid"] = $parent_aid;
				$tmp["user_aid"] = getSessionUserAid();
				$tmp["price_cover"] = get_array_value($item_result,"sale_price_1","0");
				$tmp["status"] = '1';
				$tmp["channel"] = '1';
				$this->load->model($this->copy_download_model,"copy_download");
				$result2 = $this->copy_download->insert_or_update($tmp);
				if($result2){
					$this->log_status('Product : Download', 'Add/Update ['.get_array_value($item_result,"title","").'] to copy_download of ['.getSessionUserAid().']. Success');
				}else{
					$chk = false;
					$this->log_error('Product : Download', 'Add/Update ['.get_array_value($item_result,"title","").'] to copy_download of ['.getSessionUserAid().']. Fail');
				}
			
				$this->log_status('Product : Download', getUserLoginNameForLog($this).' load [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf for the first time.');
				$result_obj = array("status" => "success","msg" => "", "total_download"=>$result);
				echo json_encode($result_obj);
				return"";
			}else{
				$this->log_error('Product : Download', getUserLoginNameForLog($this).' try to load [copy_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf but system error.');
				$result_obj = array("status" => "error","msg" => "Error occured.");
				echo json_encode($result_obj);
				return"";
			}
			
		}
	}

		function ajax_add_vdo_to_shelf($sid="" , $product_type_cid="", $parent_aid=""){
		@define("thisAction","ajax_add_product_to_shelf");
		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		if(!is_login()){
			$result_obj = array("status" => "error","msg" => "Permission denied.");
			echo json_encode($result_obj);
			return"";
		}

		if(is_blank($parent_aid)){
			$result_obj = array("status" => "error","msg" => "No vdo selected.");
			echo json_encode($result_obj);
			return"";
		}

		$model = $this->get_product_model($product_type_aid);
		$this->db->flush_cache();
		$this->load->model(get_array_value($model,"product_model",""),"product");
		$tmp = array();
		$tmp["aid"] = $parent_aid;
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->product->set_where($tmp);
		$item_result = $this->product->load_record(true);
		if(!is_var_array($item_result)){
			$this->log_notice('Product : Download', getUserLoginNameForLog($this).' try to load [parent_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf but no product found or product status=0.');
			$result_obj = array("status" => "error","msg" => "No product found.");
			echo json_encode($result_obj);
			return"";
		}
				
		$expiration_date = "";
						
		$tmp = array();
		$tmp["product_type_aid"] = $product_type_aid;
		$tmp["product_type_cid"] = $product_type_cid;
		$tmp["parent_aid"] = $parent_aid;
		$tmp["user_aid"] = getSessionUserAid();
		$tmp["status"] = '1';
		$tmp["expiration_date"] = $expiration_date;
		$this->load->model($this->shelf_vdo_model,"shelf_vdo");
		$result = $this->shelf_vdo->insert_or_update($tmp);

		if($result){
			//Update total download to parent
			$this->db->flush_cache();
			$this->load->model(get_array_value($model,"product_model",""),"main");
			$result = $this->main->increase_total_download_web($parent_aid);	
			
			//Update shelf history
			$this->db->flush_cache();
			$this->load->model($this->shelf_vdo_history_model,"shelf_vdo_history");
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["product_type_cid"] = $product_type_cid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["user_aid"] = getSessionUserAid();
			$tmp["status"] = '1';
			$tmp["action"] = 'in';
			$result = $this->shelf_vdo_history->insert_record($tmp);
								
			$this->log_status('Product : Download', getUserLoginNameForLog($this).' load [parent_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf for the first time.');
			$result_obj = array("status" => "success","msg" => "", "total_download"=>$result);
			echo json_encode($result_obj);
			return"";
		}else{
			$this->log_error('Product : Download', getUserLoginNameForLog($this).' try to load [parent_aid : '.get_array_value($item_result,"aid","-").'] '.get_array_value($item_result,"title","-").' to shelf but system error.');
			$result_obj = array("status" => "error","msg" => "Error occured.");
			echo json_encode($result_obj);
			return"";
		}
		
	}
	
	function show_product($product_type_cid="", $copy_aid=""){
		@define("thisAction","show_product");
		$product_type_detail = $this->check_exits_product_type($product_type_cid);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		$this->data["mode"] = "front";
		if(is_blank($copy_aid) && $copy_aid > 0){
			redirect('home/status/'.md5('product-not-found'));
			return"";
		}

		$model = $this->get_product_model($product_type_aid);
		$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
		$tmp = array();
		$tmp["aid"] = $copy_aid;
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->product_copy->set_where($tmp);
		$item_result = $this->product_copy->load_record(false);
		if(!is_var_array($item_result)){
			redirect('home/status/'.md5('product-not-found'));
			return"";
		}
		
		$parent_aid = get_array_value($item_result,"parent_aid","0");
		$is_license = get_array_value($item_result,"is_license","0");
		if($is_license == '1' && CONST_HAS_IPAD_APP == "1"){
			redirect($product_type_cid.'-detail/'.$parent_aid);
			return"";
		}
		
		$this->db->flush_cache();
		if(is_login()){
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["copy_aid"] = $copy_aid;
			$tmp["user_aid"] = getSessionUserAid();
			$tmp["status"] = '1';
			$this->load->model($this->shelf_model,"shelf");
			$this->shelf->set_where($tmp);
			$result = $this->shelf->load_record(true);
			// echo "<br>sql : ".$this->db->last_query()."<br>";
			if($result){
				$this->load->model($this->shelf_model,"shelf");
				$this->db->flush_cache();
				$data_where = array();
				$data_where["product_type_aid"] = $product_type_aid;
				$data_where["copy_aid"] = $copy_aid;
				$data_where["user_aid"] = getSessionUserAid();
				$this->shelf->set_where($data_where);
				$data = array();
				$data['is_read'] = '1';
				$rs = $this->shelf->update_record($data, $data_where);
			}else{
				redirect($product_type_cid.'-detail/'.$parent_aid.'/not-on-shelf');
				return"";
			}
		}
		else //if(CONST_READ_BOOK_ANONYMOUS != '1')
		{
			redirect($product_type_cid.'-detail/'.$parent_aid.'/not-login');
			//redirect('/login');
			return"";
		}
		

		
		$this->load->helper('directory');
		
		$upload_path = get_array_value($item_result,"upload_path","");
		$file_upload = get_array_value($item_result,"file_upload","");
		$use_digital_gen = get_array_value($item_result,"use_digital_gen","");
		$cid = get_array_value($item_result,"cid","");
		// echo "use_digital_gen = $use_digital_gen <BR />";
		$full_path = get_array_value($item_result,"upload_path","")."full";
		$file_path = get_array_value($item_result,"upload_path","")."file";
		$doc_file_path = get_array_value($item_result,"upload_path","")."doc";
		if(strpos($file_upload, '/') === false){
			$file_upload = $file_path.'/'.get_array_value($item_result,"file_upload","");
		}
		$file_name = $file_upload;
		$file_type = substr(strrchr($file_name, "."), 0);

		// echo $file_name."<br/>"; 
		// echo $full_path"<br/>";
		// echo "<br/>";
		// echo "<br/>";
		// echo "<br/>";
		// echo "<br/>";

		$doc_file_upload = $doc_file_path.'/'.get_array_value($item_result,"file_upload","");

		
		if(file_exists("./".$upload_path."app/".$cid.".pdf"))
		{

			$this->data["path_upload"] = $upload_path."app/".$cid.".pdf";
			$this->data["app_path_upload"] = $upload_path."app/";
			$this->data["file_content"] = file_get_contents("./".$upload_path."app/".$cid.".pdf");
			//$this->data["status"] = "full";
			$this->data["type"] = $product_type_aid;
			$this->load->view($this->default_theme_front.'/tpl_reader_pdf', $this->data);
			return"";
		}else if (file_exists("./".$upload_path."full/index.html")) {
				$this->data["mode"] = "front";
				$this->data["title"] = DEFAULT_TITLE;
				$this->data["src"] = site_url($full_path.'/index.html');
				$this->load->model(get_array_value($model,"product_model",""),"main");
				$this->db->flush_cache();
				$this->main->increase_total_read_web($copy_aid);
				$this->update_status_when_read();
				$this->load->view($this->default_theme_front.'/tpl_reader_emag', $this->data);
		}else if(is_file($doc_file_upload)){
					redirect(site_url($doc_file_upload));
					return"";
		}else{
			//echo "error have file : ".".".$upload_path."app/".$cid.".pdf";
			redirect($product_type_cid.'-detail/'.$parent_aid.'/file-not-found');
			return"";
		}
		
		/*
		if($use_digital_gen == "1"){
		
			if($file_type==".pdf"){			
				//echo $file_type; exit();
				//echo $file_upload; exit();
				//$this->data["path_upload"] = $file_upload;
				$this->data["path_upload"] = $upload_path."app/".$cid.".pdf";
				//$this->data["status"] = "full";
				$this->data["type"] = $product_type_aid;
				$this->load->view($this->default_theme_front.'/tpl_reader_pdf', $this->data);
				return"";
			}else{
				$map = directory_map($file_path, 1);
				if(is_var_array($map)){
					$this->db->flush_cache();
					$this->load->model(get_array_value($model,"product_model",""),"main");
					$this->main->increase_total_read_web($copy_aid);
					$this->update_status_when_read();
					$this->data["mode"] = "front";
					$this->data["title"] = DEFAULT_TITLE;
					$this->data["file_path"] = $file_path;
					$this->load->view($this->default_theme_front.'/tpl_reader', $this->data);
					return "";
				}
			}
		}

		if(!is_blank($upload_path)){
			$map = directory_map($full_path, 1);
			echo "<pre>";
			print_r($map);
			echo "</pre>";
			if(is_var_array($map)){
				if (in_array("index.html", $map)) {
					// redirect($full_path.'/index.html#/0/');
					$this->data["mode"] = "front";
					$this->data["title"] = DEFAULT_TITLE;
					$this->data["src"] = site_url($full_path.'/index.html');
					$this->load->model(get_array_value($model,"product_model",""),"main");
					$this->db->flush_cache();
					$this->main->increase_total_read_web($copy_aid);
					$this->update_status_when_read();
					$this->load->view($this->default_theme_front.'/tpl_reader_emag', $this->data);
						
				}else if (in_array("index.pdf", $map)) {
					$this->load->model(get_array_value($model,"product_model",""),"main");
					$this->db->flush_cache();
					$this->main->increase_total_read_web($copy_aid);
					$this->update_status_when_read();
					redirect($full_path.'/index.pdf');
				}else if (in_array("index.txt", $map)) {
					$this->load->model(get_array_value($model,"product_model",""),"main");
					$this->db->flush_cache();
					$this->main->increase_total_read_web($copy_aid);
					$this->update_status_when_read();
					redirect($full_path.'/index.txt');
				}else {
					foreach($map as $item){
						// echo $item;
						$name = substr($item, 0, strrpos($item, "."));
						// echo " has name = $name , ";
						$type = substr(strrchr($item, "."), 1);
						// echo " has type = $type";
						if($name == "index"){
							$this->load->model(get_array_value($model,"product_model",""),"main");
							$this->db->flush_cache();
							$this->main->increase_total_read_web($copy_aid);
							$this->update_status_when_read();
							redirect($full_path.'/index.'.$type);
							return"";
						}
					}
				}
			}else{
				// echo "file_upload: $file_upload";
				if(is_file($file_upload)){
					redirect(site_url($file_upload));
					return"";
				}else{
					redirect($product_type_cid.'-detail/'.$parent_aid.'/file-not-found');
					return"";
				}
			}
		}else{
			redirect($product_type_cid.'-detail/'.$parent_aid.'/file-not-found');
			return"";
		}
		*/
	}
	
	function ajax_get_product_option($sid=""){
		@define("thisAction","ajax_get_product_option");
		$product_type_cid = $this->input->get_post('product_type_cid');
		$parent_aid = $this->input->get_post('parent_aid');
		$product_type_detail = $this->check_exits_product_type($product_type_cid, true);
		$product_type_aid = get_array_value($product_type_detail,"aid","0");

		if(is_blank($parent_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product selected.' );
			echo json_encode($result_obj);
		}

		$model = $this->get_product_model($product_type_aid);
		$this->db->flush_cache();
		$this->load->model(get_array_value($model,"product_model",""),"product");
		$tmp = array();
		$tmp["aid"] = $parent_aid;
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->product->set_where($tmp);
		$item_result = $this->product->load_record(true);
		if(!is_var_array($item_result)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
		}

		$this->load->model(get_array_value($model,"product_copy_model",""),"product_copy");
		$tmp = array();
		$tmp["parent_aid"] = $parent_aid;
		if(!exception_about_status()) $tmp["status"] = "1";
		$this->product_copy->set_where($tmp);
		$this->product_copy->set_order_by("type asc, possession asc");
		$copy_result = $this->product_copy->load_records(true);
		if(!is_var_array($copy_result)){
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
		}

		$paper_result = "";
		$digital_result = "";
		$all_option = 0;
		foreach ($copy_result as $item) {
			$copy_aid = get_array_value($item,"aid","");
			$type = get_array_value($item,"type","");
			$type_name = get_array_value($item,"type_name","");
			$copy_title = get_array_value($item,"copy_title","");
			$possession = get_array_value($item,"possession","");
			$is_license = get_array_value($item,"is_license","");
			$ebook_concurrence = get_array_value($item,"ebook_concurrence","");

			$digital_price = get_array_value($item,"digital_price","");
			$digital_point = get_array_value($item,"digital_point","");
			$paper_price = get_array_value($item,"paper_price","");
			$paper_point = get_array_value($item,"paper_point","");
			$in_stock = get_array_value($item,"in_stock","");
			$rental_period = get_array_value($item,"rental_period","");
			$rental_fee = get_array_value($item,"rental_fee","");
			$rental_fee_point = get_array_value($item,"rental_fee_point","");


			$title_label = $type_name;
			if(!is_blank($copy_title)){
				$title_label .= ' : '.$copy_title;
			}
			$obj = array();
			$obj["parent_aid"] = $parent_aid;
			$obj["copy_aid"] = $copy_aid;
			$obj["product_type_aid"] = $product_type_aid;
			$obj["product_type_cid"] = $product_type_cid;

			switch ($type) {
				case '1':
					if($possession == "1"){ // Digital / Buy out
						if($digital_price > 0 && $digital_point > 0){ // Paid
							$obj["status"] = "1";
							$obj["total_available"] = "unlimit";
							$obj["price"] = $digital_price;
							$obj["point"] = $digital_point;
							$title_label .= ' [THB '.$digital_price.' / Point '.$digital_point.']';
							$obj["price_txt"] = ' THB '.$digital_price.' / Point '.$digital_point.'';
							// echo "title_label = $title_label <BR>";
							$obj["title_label"] = $title_label;
							$obj["copy_result"] = $item;
							$all_option++;
							$digital_result[] = $obj;
						}else{ // Free
							// $obj["price"] = 0;
							// $obj["point"] = 0;
							// $title_label .= ' [Free]';
						}
					}else if($possession == "2"){ // Digital / Rental
						$title_label .= ' for rent';
						if($rental_fee > 0 && $rental_fee_point > 0){ // Paid
							$obj["price"] = $rental_fee;
							$obj["point"] = $rental_fee_point;
							$title_label .= ' [THB '.$rental_fee.' / Point '.$rental_fee_point.']';
							$obj["price_txt"] = ' THB '.$rental_fee.' / Point '.$rental_fee_point.'';

							if($is_license == "1"){
								$this->load->model($this->shelf_model,"shelf");
								$this->shelf->set_where(array("copy_aid"=>$copy_aid));
								$shelf_result = $this->shelf->load_records(false);
								$now_on_shelf = 0;
								if(is_var_array($shelf_result)){
									$now_on_shelf = count($shelf_result);
								}
								$total_available = $ebook_concurrence - $now_on_shelf;
								// echo "now_on_shelf = $now_on_shelf , ebook_concurrence = $ebook_concurrence, total_available = $total_available<BR>";

								if($now_on_shelf >= $ebook_concurrence){
									$title_label .= '<div class="out-of-stock">Out of stock</div>';
									$obj["status"] = "0";
									$obj["total_available"] = 0;
								}else{
									if($total_available=="1"){
										$title_label .= '<div class="copy-left">'.$total_available.' copy left</div>';
									}else{
										$title_label .= '<div class="copy-left">'.$total_available.' copies left</div>';
									}
									$obj["status"] = "1";
									$obj["total_available"] = $total_available;
								}
							}else{
								$obj["status"] = "1";
								$obj["total_available"] = "unlimit";
							}

							// echo "title_label = $title_label <BR>";
							$obj["title_label"] = $title_label;
							$obj["copy_result"] = $item;
							$all_option++;
							$digital_result[] = $obj;
						}else{ // Free
							// $obj["price"] = 0;
							// $obj["point"] = 0;
							// $title_label .= ' [Free]';
						}
					}else{
						break;
					}
					break;
				
				case '2':
					if($possession == "1"){ // Paper / Buy out
						if($paper_price > 0 && $paper_point > 0){ // Paid
							$obj["price"] = $paper_price;
							$obj["point"] = $paper_point;
							$title_label .= ' [THB '.$paper_price.' / Point '.$paper_point.']';
							$obj["price_txt"] = ' THB '.$paper_price.' / Point '.$paper_point.'';

							if(is_number_no_zero($in_stock)){
								if($in_stock <= 5){
									if($in_stock == "1"){
										$title_label .= '<div class="copy-left">'.$in_stock.' copy left</div>';
									}else{
										$title_label .= '<div class="copy-left">'.$in_stock.' copies left</div>';
									}
								}
								$obj["status"] = "1";
								$obj["total_available"] = $in_stock;
							}else{
								$title_label .= '<div class="out-of-stock">Out of stock</div>';
								$obj["status"] = "0";
								$obj["total_available"] = 0;
							}


						}else{ // Free
							// $obj["price"] = 0;
							// $obj["point"] = 0;
							// $title_label .= ' [Free]';
						}
						$obj["title_label"] = $title_label;
						$obj["copy_result"] = $item;
						// echo "title_label = $title_label <BR>";
						$paper_result[] = $obj;
					}
					break;
				default:
					break;
			}
		}

		if(is_var_array($paper_result) || is_var_array($digital_result)){
			$result = array();
			$result["product_detail"] = $item_result;
			$result["all_option"] = $all_option;
			$result["digital_result"] = $digital_result;
			$result["paper_result"] = $paper_result;
			$result_obj = array("status" => 'success',"msg" => '', "result"=>$result );
			echo json_encode($result_obj);
		}else{
			$result_obj = array("status" => 'error',"msg" => 'Error occurred. No product found.' );
			echo json_encode($result_obj);
		}
	}

	function update_status_when_read(){
		return "";
		// $this->load->model($this->user_model,"user");
		// $this->user->add_point_remain(getSessionUserAid(), '5');
	}
	
}

?>