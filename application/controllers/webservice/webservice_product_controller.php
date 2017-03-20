<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_product_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->product_topic_main_model = "Product_topic_main_model";
		$this->product_main_model = "Product_main_model";
		$this->product_main_field_model = "Product_main_field_model";
		$this->product_type_model = "Product_type_model";
		$this->product_type_minor_model = "Product_type_minor_model";

		$this->view_all_products_model = 'View_all_products_with_detail_model';
		$this->view_all_product_copies_model = 'View_all_product_copies_model';
		$this->view_all_product_fields_with_detail_model = "View_all_product_fields_with_detail_model";

		$this->search_history_model = "Search_history_model";

		$this->book_model = 'Book_model';
		$this->book_copy_model = 'Book_copy_model';
		$this->magazine_model = 'Magazine_model';
		$this->magazine_copy_model = 'Magazine_copy_model';
		$this->vdo_model = 'Vdo_model';

		$this->reserve_model = "Reserve_model";
	}
	
	
	function get_product_list(){
		// header('Content-Type: text/html; charset=utf-8');
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$product_search_list = "";

		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
				$user_login = $this->check_token();
			//print_r($user_login);
			if(is_var_array($user_login)){
				$user_aid = get_array_value($user_login,"user_aid","0");
				$this->load->model($this->user_model,'user');
				$this->user->set_where(array("aid"=>$user_aid));
				$user_result = $this->user->load_record(true);
				$user_section_aid = get_array_value($user_result,"user_section_aid","0");
			}
		}else{
			$user_section_aid = "0";
		}
		$this->load->model($this->product_category_ref_user_section_model,"ref_user_section");
		$this->ref_user_section->set_where(array("user_section_aid"=>$user_section_aid));
		$ref_user_section_all = $this->ref_user_section->load_records_array(false,"","product_category_aid");
		//print_r($ref_user_section_all);
		//echo "<br>sql : ".$this->db->last_query();
		// $cat_arr = "";
		// $cat_arr[] = "";
		if(is_var_array($ref_user_section_all)){
			foreach ($ref_user_section_all as $cid) {
				if(!is_blank($cid)){
					$cat_arr[] = ",".$cid.",";
				}
			}
		}
		
		$this->db->flush_cache();
	
		$this->load->model($this->view_all_product_fields_with_detail_model,'view_all_products_2');

		$data_insert = "";
		$keyword = trim($this->input->get_post('keyword'));
		$search_in = trim($this->input->get_post('search_in'));
		// echo "keyword = $keyword , search_in = $search_in";
		if(!is_blank($keyword)){
			if(is_blank($search_in)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify search_in.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			$search_in_master = array("all","title","author","isbn","keyword","content","call_number","publisher","tag");
			$search_in_serarch_list = "";
			$search_in_arr = preg_split('/,/', $search_in, -1, PREG_SPLIT_NO_EMPTY);
			if(is_var_array($search_in_arr)){
				foreach($search_in_arr as $item){
					if(!is_blank(trim($item))){
						if(!in_array($item,$search_in_master)){
							$result_obj = array("status" => 'error',"msg" => 'Incorrect data : search_in can not be \''.$item.'\'.', "result" => '');
							echo json_encode($result_obj);
							return "";
						}
						$search_in_serarch_list[] = $item;
					}
				}
			}
			if(!is_var_array($search_in_serarch_list)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify search_in.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}

			// print_r($search_in_serarch_list);
			$search_option = trim($this->input->get_post('search_option'));
			if($search_option != 'and'){
				$search_option = 'or';
			}
			// echo "search_option = $search_option";
			
			// echo "keyword = $keyword";
			$keyword_tmp = $this->convert_keyword_to_array($keyword);
			$keyword_arr = "";
			$keyword_not_arr = "";
			if(is_var_array($keyword_tmp)){
				foreach ($keyword_tmp as $item) {
					$item = trim($item);
					// echo "item = $item <br />";
					if(!is_blank($item)){
						if($item[0] == "-"){
							$keyword_not_arr[] = substr($item, 1);
							$tmp = array();
							$tmp["word"] = substr($item, 1);
							$tmp["cond"] = "NOT";
							$tmp["search_in"] = $search_in;
							$data_insert[] = $tmp;
						}else{
							$keyword_arr[] = $item;						
							$tmp = array();
							$tmp["word"] = $item;
							$tmp["cond"] = "";
							$tmp["search_in"] = $search_in;
							$data_insert[] = $tmp;
						}
					}
				}
			}
			if(is_var_array($data_insert)){
				$this->load->model($this->search_history_model,"search_history");
				$this->search_history->insert_records($data_insert);
			}

			// echo "keyword_arr = ";
			// print_r($keyword_arr);
			// echo "<BR />keyword_not_arr = ";
			// print_r($keyword_not_arr);		
			$this->view_all_products_2->set_open();
			foreach ($search_in_serarch_list as $sitem) {
				switch ($sitem) {
					case "all" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr, "product_tag"=>$keyword_not_arr));
							}
							break;
					case "title" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('245'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('245'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "keyword" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('908'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('908'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "author" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('100','110'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('100','110'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "content" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('520'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('520'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "isbn" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('020'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "publisher" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('260'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
					case "call_number" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('050'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "tag" :
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("product_tag"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("product_tag"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("product_tag"=>$keyword_not_arr));
							}
							break;
				}
			}
			$this->view_all_products_2->set_close();
		}
				
		$product_main_aid = trim($this->input->get_post('product_main_aid'));
		// echo "product_main_aid = ".$product_main_aid;
		if(!is_blank($product_main_aid)){
			if(!is_number_no_zero($product_main_aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : product_main_aid must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("product_main_aid"=>$product_main_aid));
		}

		$type = trim($this->input->get_post('type'));
		if(!is_blank($type)){
			$product_type_obj = $this->check_product_type();
			$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
			$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
			// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";
			if(is_number_no_zero($product_type_aid)){
				$this->view_all_products_2->set_where(array("product_type_aid"=>$product_type_aid));
			}
		}else{
			$this->view_all_products_2->set_where_in(array("product_type_aid"=>array("1","2")));
			
		}
		if($type != "vdo"){
			$this->view_all_products_2->set_where_in(array("has_ebook"=>"1"));
		}

		$category = trim($this->input->get_post('category'));
		// echo "category = ".$category;
		if(!is_blank($category)){
			$category_serarch_list = "";
			$category_arr = preg_split('/,/', $category, -1, PREG_SPLIT_NO_EMPTY);
			if(is_var_array($category_arr)){
				foreach($category_arr as $item){
					if(!is_blank(trim($item))){
						if(is_number_no_zero($item)){
							$category_serarch_list[] = ",".$item.",";
						}
					}
				}
				$this->view_all_products_2->set_and_or_like_by_field("category",$category_serarch_list,"both");
			}
		}
		
		$publisher_aid = trim($this->input->get_post('publisher_aid'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publisher_aid)){
			if(!is_number_no_zero($publisher_aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publisher_aid must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("publisher_aid"=>$publisher_aid));
		}
		
		$publish_date_from = trim($this->input->get_post('publish_date_from'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publish_date_from)){
			$publish_date_from = get_datetime_pattern("db_date_format",$publish_date_from,"");
			if(is_blank($publish_date_from)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publish_date_from must be date.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where('publish_date >=', $publish_date_from);
		}
		
		$publish_date_to = trim($this->input->get_post('publish_date_to'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publish_date_to)){
			$publish_date_to = get_datetime_pattern("db_date_format",$publish_date_to,"");
			if(is_blank($publish_date_to)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publish_date_to must be date.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where('publish_date <=', $publish_date_to);
		}
		
		$aid = trim($this->input->get_post('id'));
		// echo "aid = ".$aid;
		if(!is_blank($aid)){
			if(!is_number_no_zero($aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : id must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("aid"=>$aid));
		}

		$this->view_all_products_2->set_where(array("status"=>"1"));

		//user_section
		if(is_var_array($cat_arr)){
				$this->view_all_products_2->set_and_or_like_by_field("category", $cat_arr);
		}

		
		//Order by
		$order_by_master = array("match","publish_date_asc","publish_date_desc","total_download_desc","total_download_asc","total_view_desc","total_view_asc","total_read_desc","total_read_asc","title_desc","title_asc","author_desc","author_asc","popular","new","recommended");
		$order_by = trim($this->input->get_post('order_by'));
		if(!is_blank($order_by)){
			if(!in_array($order_by,$order_by_master)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data : order_by can not be \''.$order_by.'\'.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			switch($order_by){
				case "match" : $this->view_all_products_2->set_order_by("tag_weight ASC, weight asc, publish_date desc"); break;
				case "publish_date_asc" : $this->view_all_products_2->set_order_by("publish_date asc, weight asc"); break;
				case "publish_date_desc" : $this->view_all_products_2->set_order_by("publish_date desc, weight asc"); break;
				case "total_download_desc" : $this->view_all_products_2->set_order_by("total_download desc, weight asc, publish_date desc"); break;
				case "total_download_asc" : $this->view_all_products_2->set_order_by("total_download asc, weight asc, publish_date desc"); break;
				case "total_view_desc" : $this->view_all_products_2->set_order_by("total_view desc, weight asc, publish_date desc"); break;
				case "total_view_asc" : $this->view_all_products_2->set_order_by("total_view asc, weight asc, publish_date desc"); break;
				case "total_read_desc" : $this->view_all_products_2->set_order_by("total_read desc, weight asc, publish_date desc"); break;
				case "total_read_asc" : $this->view_all_products_2->set_order_by("total_read asc, weight asc, publish_date desc"); break;
				case "title_desc" : $this->view_all_products_2->set_order_by("title desc, weight asc, publish_date desc"); break;
				case "title_asc" : $this->view_all_products_2->set_order_by("title asc, weight asc, publish_date desc"); break;
				case "author_desc" : $this->view_all_products_2->set_order_by("author desc, weight asc, publish_date desc"); break;
				case "author_asc" : $this->view_all_products_2->set_order_by("author asc, weight asc, publish_date desc"); break;
				case "popular" : $this->view_all_products_2->set_order_by("weight asc, total_download desc"); break;
				case "new" : 
					$this->view_all_products_2->set_where(array("is_new"=>"1"));
					$this->view_all_products_2->set_order_by("weight asc, publish_date desc");
					break;
				case "recommended" : 
					$this->view_all_products_2->set_where(array("is_recommended"=>"1"));
					$this->view_all_products_2->set_order_by("weight asc, publish_date desc");
					break;
				default : $this->view_all_products_2->set_order_by("publish_date desc, weight asc");
			}
		}
		
		//Limit
		$limit_start = trim($this->input->get_post('limit_start'));
		$no_record = trim($this->input->get_post('no_record'));
		// echo "limit_start = ".$limit_start;
		if(!is_blank($limit_start)){
			if(!is_number($limit_start)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : limit_start must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(is_blank($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify no_record.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
		}else{
			$limit_start = 0;
		}
		
		// echo "no_record = ".$no_record;
		if(!is_blank($no_record)){
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		
		// echo "limit_start = $limit_start , no_record = $no_record";
		if(is_number_no_zero($no_record)){
			$this->view_all_products_2->set_limit($limit_start, $no_record);
		}
		// else{
		// 	$this->view_all_products_2->set_limit($limit_start, 100);
		// }
		
		$result_list = $this->view_all_products_2->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		// echo "<pre>";
		// print_r($result_list);
		// echo "</pre>";
		
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				//print_r($item);echo "<HR>";
				$parent_aid = get_array_value($item,"aid","");
				$product_type_aid = get_array_value($item,"product_type_aid","");
				
				$obj = array();
				$obj["parent_aid"] = $parent_aid;

				$obj["type"] = get_array_value($item,"product_type_cid","");
				$obj["title"] = get_array_value($item,"title","");
				$obj["author"] = get_array_value($item,"author","");
				$obj["description"] = get_array_value($item,"description","");
				$obj["cover_image"] = get_array_value($item,"cover_image_ipad","");
				$obj["publisher_aid"] = get_array_value($item,"publisher_aid","");
				$obj["publisher_name"] = get_array_value($item,"publisher_name","");
				$obj["product_main_aid"] = get_array_value($item,"product_main_aid","");
				$obj["product_type_aid"] = get_array_value($item,"product_type_aid","");
				
				$obj["publish_date"] = get_array_value($item,"publish_date","");
				$obj["publish_day"] = get_datetime_pattern("d",get_array_value($item,"publish_date",""),"");
				$obj["publish_month"] = get_datetime_pattern("m",get_array_value($item,"publish_date",""),"");
				$obj["publish_year"] = get_datetime_pattern("Y",get_array_value($item,"publish_date",""),"");
				//$obj["has_license"] = get_array_value($item,"has_license","0");
				if($product_type_aid != "3"){
					$obj["has_license"] = get_array_value($item,"has_license","0");
				}
				$obj["review_point"] = get_array_value($item,"review_point","0");

				// $this->load->model($this->view_all_product_fields_model, "view_all_product_fields_2");
				// $tmp = array();
				// $tmp["product_type_aid"] = $product_type_aid;
				// $tmp["parent_aid"] = $parent_aid;
				// $this->view_all_product_fields_2->set_where($tmp);
				// $field_result = $this->view_all_product_fields_2->load_records(false);
				if($product_type_aid == "3"){
					$this->load->model($this->vdo_model,'vdo');
					$this->vdo->set_where(array("aid"=>$parent_aid, "status"=>"1"));
					$item_vdo = $this->vdo->load_record(true);
					
					if(is_var_array($item_vdo)){
						$this->db->flush_cache();
						$this->load->model($this->vdo_model,'vdo');
						$rs = $this->vdo->increase_total_view_device($aid);	
					

						$ext_source = get_array_value($item_vdo,"ext_source","");
						if(preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $ext_source, $matches) ){
							//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
							// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
							$ext_source = $matches[1];
						}
						if(strstr($ext_source, "https://youtu.be/")){
							//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
							// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
							$ext_source = substr(strrchr($ext_source,"/"),1);
						}
						$link_youtube = "http://www.youtube.com/embed/".$ext_source;
						$url = PUBLIC_PATH.get_array_value($item_vdo,"upload_path","")."file/".get_array_value($item_vdo,"uri","");
						//$category_list = get_array_value($item,"category_list","0")
						
						// if(is_var_array($category_list){
						// 	foreach($category_list as $category_item){

						// 	}
						// }
						$obj["copy_file_upload"] = get_array_value($item_vdo,"uri","");
						if(!is_blank($ext_source) && $ext_source != "0"){
								$obj["url"] = "";
								$obj["link_youtube"] = $link_youtube;
						}else{
								$obj["url"] = $url;
								$obj["link_youtube"] = "";
						}

					
					// $obj["url"] = "";
					// $obj["link_youtube"] = "";
					}
				}
				
				

				$this->load->model($this->view_all_product_copies_model, "view_all_product_copies_2");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["parent_aid"] = $parent_aid;
				$tmp["is_ebook"] = "1";
				$tmp["status"] = "1";
				$this->view_all_product_copies_2->set_where($tmp);
				$copy_result = $this->view_all_product_copies_2->load_records(false);
				$child_list = "";
				if(is_var_array($copy_result)){
					foreach($copy_result as $sub_item){
						// echo "<pre>";
						// print_r($sub_item);
						// echo "</pre>";

						$child = array();
						$child["copy_aid"] = get_array_value($sub_item,"aid","");
						$child["copy_cid"] = get_array_value($sub_item,"cid","");
						$child["copy_barcode"] = get_array_value($sub_item,"barcode","");
						$child["copy_nonconsume_identifier"] = get_array_value($sub_item,"nonconsume_identifier","");
						$child["copy_publish_date"] = get_array_value($sub_item,"publish_date","");
						$child["copy_expired_date"] = get_array_value($sub_item,"expired_date","");
						$child["copy_is_ebook_license"] = get_array_value($sub_item,"is_license","0");
						$child["copy_ebook_concurrence"] = get_array_value($sub_item,"ebook_concurrence","");
						$child["copy_upload_path"] = get_array_value($sub_item,"upload_path","");
						$filedoc = "./".get_array_value($sub_item,"upload_path","")."doc";
						$pathdoc = PUBLIC_PATH.get_array_value($sub_item,"upload_path","")."doc";
						if(get_array_value($sub_item,"file_upload","") != ""){
							$child["copy_file_upload"] = $pathdoc."/".get_array_value($sub_item,"file_upload","");
						}else{
							//$filedoc = "'./".get_array_value($sub_item,"upload_path","")."doc'";
							if(file_exists($filedoc)){
								$objScan = scandir($filedoc);
					                foreach ($objScan as $value) 
					                {
					                	if(strlen($value) > 3){
					                		$child["copy_file_upload"] = $pathdoc."/".$value;
					                	}else{
					                		$child["copy_file_upload"] = "";
					                	}
					                }		
							}else{
								$child["copy_file_upload"] = get_array_value($sub_item,"file_upload","");
							}

						}
						
						$child["copy_status"] = get_array_value($sub_item,"status","");
						$child_list[] = $child;
					}
				}
				$obj["copy_list"] = $child_list;
				$result[] = $obj;
			}
		
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}


	function search(){
		// header('Content-Type: text/html; charset=utf-8');
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$product_search_list = "";

		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
				$user_login = $this->check_token();
			//print_r($user_login);
			if(is_var_array($user_login)){
				$user_aid = get_array_value($user_login,"user_aid","0");
				$this->load->model($this->user_model,'user');
				$this->user->set_where(array("aid"=>$user_aid));
				$user_result = $this->user->load_record(true);
				$user_section_aid = get_array_value($user_result,"user_section_aid","0");
			}
		}else{
			$user_section_aid = "0";
		}
		$this->load->model($this->product_category_ref_user_section_model,"ref_user_section");
		$this->ref_user_section->set_where(array("user_section_aid"=>$user_section_aid));
		$ref_user_section_all = $this->ref_user_section->load_records_array(false,"","product_category_aid");
		//print_r($ref_user_section_all);
		//echo "<br>sql : ".$this->db->last_query();
		// $cat_arr = "";
		// $cat_arr[] = "";
		if(is_var_array($ref_user_section_all)){
			foreach ($ref_user_section_all as $cid) {
				if(!is_blank($cid)){
					$cat_arr[] = ",".$cid.",";
				}
			}
		}
		
		$this->db->flush_cache();
	
		$this->load->model($this->view_all_product_fields_with_detail_model,'view_all_products_2');

		$data_insert = "";
		$keyword = trim($this->input->get_post('keyword'));
		$search_in = trim($this->input->get_post('search_in'));
		// echo "keyword = $keyword , search_in = $search_in";
		if(!is_blank($keyword)){
			if(is_blank($search_in)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify search_in.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			$search_in_master = array("all","title","author","isbn","keyword","content","call_number","publisher","tag");
			$search_in_serarch_list = "";
			$search_in_arr = preg_split('/,/', $search_in, -1, PREG_SPLIT_NO_EMPTY);
			if(is_var_array($search_in_arr)){
				foreach($search_in_arr as $item){
					if(!is_blank(trim($item))){
						if(!in_array($item,$search_in_master)){
							$result_obj = array("status" => 'error',"msg" => 'Incorrect data : search_in can not be \''.$item.'\'.', "result" => '');
							echo json_encode($result_obj);
							return "";
						}
						$search_in_serarch_list[] = $item;
					}
				}
			}
			if(!is_var_array($search_in_serarch_list)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify search_in.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}

			// print_r($search_in_serarch_list);
			$search_option = trim($this->input->get_post('search_option'));
			if($search_option != 'and'){
				$search_option = 'or';
			}
			// echo "search_option = $search_option";
			
			// echo "keyword = $keyword";
			$keyword_tmp = $this->convert_keyword_to_array($keyword);
			$keyword_arr = "";
			$keyword_not_arr = "";
			if(is_var_array($keyword_tmp)){
				foreach ($keyword_tmp as $item) {
					$item = trim($item);
					// echo "item = $item <br />";
					if(!is_blank($item)){
						if($item[0] == "-"){
							$keyword_not_arr[] = substr($item, 1);
							$tmp = array();
							$tmp["word"] = substr($item, 1);
							$tmp["cond"] = "NOT";
							$tmp["search_in"] = $search_in;
							$data_insert[] = $tmp;
						}else{
							$keyword_arr[] = $item;						
							$tmp = array();
							$tmp["word"] = $item;
							$tmp["cond"] = "";
							$tmp["search_in"] = $search_in;
							$data_insert[] = $tmp;
						}
					}
				}
			}
			if(is_var_array($data_insert)){
				$this->load->model($this->search_history_model,"search_history");
				$this->search_history->insert_records($data_insert);
			}

			// echo "keyword_arr = ";
			// print_r($keyword_arr);
			// echo "<BR />keyword_not_arr = ";
			// print_r($keyword_not_arr);		
			$this->view_all_products_2->set_open();
			foreach ($search_in_serarch_list as $sitem) {
				switch ($sitem) {
					case "all" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr, "product_tag"=>$keyword_not_arr));
							}
							break;
					case "title" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('245'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('245'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "keyword" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('908'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('908'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "author" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('100','110'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('100','110'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "content" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('520'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('520'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "isbn" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('020'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "publisher" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('260'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
					case "call_number" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('050'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "tag" :
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("product_tag"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("product_tag"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("product_tag"=>$keyword_not_arr));
							}
							break;
				}
			}
			$this->view_all_products_2->set_close();
		}
				
		$product_main_aid = trim($this->input->get_post('product_main_aid'));
		// echo "product_main_aid = ".$product_main_aid;
		if(!is_blank($product_main_aid)){
			if(!is_number_no_zero($product_main_aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : product_main_aid must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("product_main_aid"=>$product_main_aid));
		}

		$type = trim($this->input->get_post('type'));
		if(!is_blank($type)){
			$product_type_obj = $this->check_product_type();
			$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
			$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
			// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";
			if(is_number_no_zero($product_type_aid)){
				$this->view_all_products_2->set_where(array("product_type_aid"=>$product_type_aid));
			}
		}else{
			$this->view_all_products_2->set_where_in(array("product_type_aid"=>array("1","2")));
			
		}
		if($type != "vdo"){
			$this->view_all_products_2->set_where_in(array("has_ebook"=>"1"));
		}

		$category = trim($this->input->get_post('category'));
		// echo "category = ".$category;
		if(!is_blank($category)){
			$category_serarch_list = "";
			$category_arr = preg_split('/,/', $category, -1, PREG_SPLIT_NO_EMPTY);
			if(is_var_array($category_arr)){
				foreach($category_arr as $item){
					if(!is_blank(trim($item))){
						if(is_number_no_zero($item)){
							$category_serarch_list[] = ",".$item.",";
						}
					}
				}
				$this->view_all_products_2->set_and_or_like_by_field("category",$category_serarch_list,"both");
			}
		}
		
		$publisher_aid = trim($this->input->get_post('publisher_aid'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publisher_aid)){
			if(!is_number_no_zero($publisher_aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publisher_aid must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("publisher_aid"=>$publisher_aid));
		}
		
		$publish_date_from = trim($this->input->get_post('publish_date_from'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publish_date_from)){
			$publish_date_from = get_datetime_pattern("db_date_format",$publish_date_from,"");
			if(is_blank($publish_date_from)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publish_date_from must be date.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where('publish_date >=', $publish_date_from);
		}
		
		$publish_date_to = trim($this->input->get_post('publish_date_to'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publish_date_to)){
			$publish_date_to = get_datetime_pattern("db_date_format",$publish_date_to,"");
			if(is_blank($publish_date_to)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publish_date_to must be date.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where('publish_date <=', $publish_date_to);
		}
		
		$aid = trim($this->input->get_post('id'));
		// echo "aid = ".$aid;
		if(!is_blank($aid)){
			if(!is_number_no_zero($aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : id must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("aid"=>$aid));
		}

		$this->view_all_products_2->set_where(array("status"=>"1"));

		//user_section
		if(is_var_array($cat_arr)){
				$this->view_all_products_2->set_and_or_like_by_field("category", $cat_arr);
		}

		
		//Order by
		$order_by_master = array("match","publish_date_asc","publish_date_desc","total_download_desc","total_download_asc","total_view_desc","total_view_asc","total_read_desc","total_read_asc","title_desc","title_asc","author_desc","author_asc","popular","new","recommended");
		$order_by = trim($this->input->get_post('order_by'));
		if(!is_blank($order_by)){
			if(!in_array($order_by,$order_by_master)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data : order_by can not be \''.$order_by.'\'.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			switch($order_by){
				case "match" : $this->view_all_products_2->set_order_by("tag_weight ASC, weight asc, publish_date desc"); break;
				case "publish_date_asc" : $this->view_all_products_2->set_order_by("publish_date asc, weight asc"); break;
				case "publish_date_desc" : $this->view_all_products_2->set_order_by("publish_date desc, weight asc"); break;
				case "total_download_desc" : $this->view_all_products_2->set_order_by("total_download desc, weight asc, publish_date desc"); break;
				case "total_download_asc" : $this->view_all_products_2->set_order_by("total_download asc, weight asc, publish_date desc"); break;
				case "total_view_desc" : $this->view_all_products_2->set_order_by("total_view desc, weight asc, publish_date desc"); break;
				case "total_view_asc" : $this->view_all_products_2->set_order_by("total_view asc, weight asc, publish_date desc"); break;
				case "total_read_desc" : $this->view_all_products_2->set_order_by("total_read desc, weight asc, publish_date desc"); break;
				case "total_read_asc" : $this->view_all_products_2->set_order_by("total_read asc, weight asc, publish_date desc"); break;
				case "title_desc" : $this->view_all_products_2->set_order_by("title desc, weight asc, publish_date desc"); break;
				case "title_asc" : $this->view_all_products_2->set_order_by("title asc, weight asc, publish_date desc"); break;
				case "author_desc" : $this->view_all_products_2->set_order_by("author desc, weight asc, publish_date desc"); break;
				case "author_asc" : $this->view_all_products_2->set_order_by("author asc, weight asc, publish_date desc"); break;
				case "popular" : $this->view_all_products_2->set_order_by("weight asc, total_download desc"); break;
				case "new" : 
					$this->view_all_products_2->set_where(array("is_new"=>"1"));
					$this->view_all_products_2->set_order_by("weight asc, publish_date desc");
					break;
				case "recommended" : 
					$this->view_all_products_2->set_where(array("is_recommended"=>"1"));
					$this->view_all_products_2->set_order_by("weight asc, publish_date desc");
					break;
				default : $this->view_all_products_2->set_order_by("publish_date desc, weight asc");
			}
		}
		
		//Limit
		$limit_start = trim($this->input->get_post('limit_start'));
		$no_record = trim($this->input->get_post('no_record'));
		// echo "limit_start = ".$limit_start;
		if(!is_blank($limit_start)){
			if(!is_number($limit_start)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : limit_start must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(is_blank($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify no_record.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
		}else{
			$limit_start = 0;
		}
		
		// echo "no_record = ".$no_record;
		if(!is_blank($no_record)){
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		
		// echo "limit_start = $limit_start , no_record = $no_record";
		if(is_number_no_zero($no_record)){
			$this->view_all_products_2->set_limit($limit_start, $no_record);
		}
		// else{
		// 	$this->view_all_products_2->set_limit($limit_start, 100);
		// }
		
		$result_list = $this->view_all_products_2->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		// echo "<pre>";
		// print_r($result_list);
		// echo "</pre>";
		
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				//print_r($item);echo "<HR>";
				$parent_aid = get_array_value($item,"aid","");
				$product_type_aid = get_array_value($item,"product_type_aid","");
				
				$obj = array();
				$obj["parent_aid"] = $parent_aid;

				$obj["type"] = get_array_value($item,"product_type_cid","");
				$obj["title"] = get_array_value($item,"title","");
				$obj["author"] = get_array_value($item,"author","");
				$obj["description"] = get_array_value($item,"description","");
				$obj["cover_image"] = get_array_value($item,"cover_image_ipad","");
				$obj["publisher_aid"] = get_array_value($item,"publisher_aid","");
				$obj["publisher_name"] = get_array_value($item,"publisher_name","");
				$obj["product_main_aid"] = get_array_value($item,"product_main_aid","");
				$obj["product_type_aid"] = get_array_value($item,"product_type_aid","");
				
				$obj["publish_date"] = get_array_value($item,"publish_date","");
				$obj["publish_day"] = get_datetime_pattern("d",get_array_value($item,"publish_date",""),"");
				$obj["publish_month"] = get_datetime_pattern("m",get_array_value($item,"publish_date",""),"");
				$obj["publish_year"] = get_datetime_pattern("Y",get_array_value($item,"publish_date",""),"");
				//$obj["has_license"] = get_array_value($item,"has_license","0");
				if($product_type_aid != "3"){
					$obj["has_license"] = get_array_value($item,"has_license","0");
				}
				$obj["review_point"] = get_array_value($item,"review_point","0");

				// $this->load->model($this->view_all_product_fields_model, "view_all_product_fields_2");
				// $tmp = array();
				// $tmp["product_type_aid"] = $product_type_aid;
				// $tmp["parent_aid"] = $parent_aid;
				// $this->view_all_product_fields_2->set_where($tmp);
				// $field_result = $this->view_all_product_fields_2->load_records(false);
				if($product_type_aid == "3"){
					$this->load->model($this->vdo_model,'vdo');
					$this->vdo->set_where(array("aid"=>$parent_aid, "status"=>"1"));
					$item_vdo = $this->vdo->load_record(true);
					
					if(is_var_array($item_vdo)){
						$this->db->flush_cache();
						$this->load->model($this->vdo_model,'vdo');
						$rs = $this->vdo->increase_total_view_device($aid);	
					

						$ext_source = get_array_value($item_vdo,"ext_source","");
						if(preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $ext_source, $matches) ){
							//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
							// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
							$ext_source = $matches[1];
						}
						if(strstr($ext_source, "https://youtu.be/")){
							//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
							// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
							$ext_source = substr(strrchr($ext_source,"/"),1);
						}
						$link_youtube = "http://www.youtube.com/embed/".$ext_source;
						$url = PUBLIC_PATH.get_array_value($item_vdo,"upload_path","")."file/".get_array_value($item_vdo,"uri","");
						//$category_list = get_array_value($item,"category_list","0")
						
						// if(is_var_array($category_list){
						// 	foreach($category_list as $category_item){

						// 	}
						// }
						$obj["copy_file_upload"] = get_array_value($item_vdo,"uri","");
						if(!is_blank($ext_source) && $ext_source != "0"){
								$obj["url"] = "";
								$obj["link_youtube"] = $link_youtube;
						}else{
								$obj["url"] = $url;
								$obj["link_youtube"] = "";
						}

					
					// $obj["url"] = "";
					// $obj["link_youtube"] = "";
					}
				}
				
				

				$this->load->model($this->view_all_product_copies_model, "view_all_product_copies_2");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["parent_aid"] = $parent_aid;
				$tmp["is_ebook"] = "1";
				$tmp["status"] = "1";
				$this->view_all_product_copies_2->set_where($tmp);
				$copy_result = $this->view_all_product_copies_2->load_records(false);
				$child_list = "";
				if(is_var_array($copy_result)){
					foreach($copy_result as $sub_item){
						// echo "<pre>";
						// print_r($sub_item);
						// echo "</pre>";

						$child = array();
						$child["copy_aid"] = get_array_value($sub_item,"aid","");
						$child["copy_cid"] = get_array_value($sub_item,"cid","");
						$child["copy_barcode"] = get_array_value($sub_item,"barcode","");
						$child["copy_nonconsume_identifier"] = get_array_value($sub_item,"nonconsume_identifier","");
						$child["copy_publish_date"] = get_array_value($sub_item,"publish_date","");
						$child["copy_expired_date"] = get_array_value($sub_item,"expired_date","");
						$child["copy_is_ebook_license"] = get_array_value($sub_item,"is_license","0");
						$child["copy_ebook_concurrence"] = get_array_value($sub_item,"ebook_concurrence","");
						$child["copy_upload_path"] = get_array_value($sub_item,"upload_path","");
						$filedoc = "./".get_array_value($sub_item,"upload_path","")."doc";
						$pathdoc = PUBLIC_PATH.get_array_value($sub_item,"upload_path","")."doc";
						if(get_array_value($sub_item,"file_upload","") != ""){
							$child["copy_file_upload"] = $pathdoc."/".get_array_value($sub_item,"file_upload","");
						}else{
							//$filedoc = "'./".get_array_value($sub_item,"upload_path","")."doc'";
							if(file_exists($filedoc)){
								$objScan = scandir($filedoc);
					                foreach ($objScan as $value) 
					                {
					                	if(strlen($value) > 3){
					                		$child["copy_file_upload"] = $pathdoc."/".$value;
					                	}else{
					                		$child["copy_file_upload"] = "";
					                	}
					                }		
							}else{
								$child["copy_file_upload"] = get_array_value($sub_item,"file_upload","");
							}

						}
						
						$child["copy_status"] = get_array_value($sub_item,"status","");
						$child_list[] = $child;
					}
				}
				$obj["copy_list"] = $child_list;
				$result[] = $obj;
			}
		
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}

	

	function search_old()
	{
		// header('Content-Type: text/html; charset=utf-8');
		$this->log_status('request webservice search', trim(urldecode($this->input->get_post('keyword'))), serialize($_REQUEST));
		
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$product_search_list = "";
		
		$this->db->flush_cache();
	
		$this->load->model($this->view_all_product_fields_with_detail_model,'view_all_products_2');

		$data_insert = "";
		$keyword = trim(urldecode($this->input->get_post('keyword')));
		$search_in = trim($this->input->get_post('search_in'));
		// echo "keyword = $keyword , search_in = $search_in";
		if(!is_blank($keyword)){
			if(is_blank($search_in)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify search_in.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
			$search_in_master = array("all","title","author","isbn","keyword","content","call_number","publisher","tag");
			$search_in_serarch_list = "";
			$search_in_arr = preg_split('/,/', $search_in, -1, PREG_SPLIT_NO_EMPTY);
			if(is_var_array($search_in_arr)){
				foreach($search_in_arr as $item){
					if(!is_blank(trim($item))){
						if(!in_array($item,$search_in_master)){
							$result_obj = array("status" => 'error',"msg" => 'Incorrect data : search_in can not be \''.$item.'\'.', "result" => '');
							echo json_encode($result_obj);
							return "";
						}
						$search_in_serarch_list[] = $item;
					}
				}
			}
			if(!is_var_array($search_in_serarch_list)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify search_in.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}

			// print_r($search_in_serarch_list);
			$search_option = trim($this->input->get_post('search_option'));
			if($search_option != 'and'){
				$search_option = 'or';
			}
			// echo "search_option = $search_option";
			
			// echo "keyword = $keyword";
			$keyword_tmp = $this->convert_keyword_to_array($keyword);
			$keyword_arr = "";
			$keyword_not_arr = "";
			if(is_var_array($keyword_tmp)){
				foreach ($keyword_tmp as $item) {
					$item = trim($item);
					// echo "item = $item <br />";
					if(!is_blank($item)){
						if($item[0] == "-"){
							$keyword_not_arr[] = substr($item, 1);
							$tmp = array();
							$tmp["word"] = substr($item, 1);
							$tmp["cond"] = "NOT";
							$tmp["search_in"] = $search_in;
							$data_insert[] = $tmp;
						}else{
							$keyword_arr[] = $item;						
							$tmp = array();
							$tmp["word"] = $item;
							$tmp["cond"] = "";
							$tmp["search_in"] = $search_in;
							$data_insert[] = $tmp;
						}
					}
				}
			}
			if(is_var_array($data_insert)){
				$this->load->model($this->search_history_model,"search_history");
				$this->search_history->insert_records($data_insert);
			}

			// echo "keyword_arr = ";
			// print_r($keyword_arr);
			// echo "<BR />keyword_not_arr = ";
			// print_r($keyword_not_arr);		
			$this->view_all_products_2->set_open();
			foreach ($search_in_serarch_list as $sitem) {
				switch ($sitem) {
					case "all" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr, "product_tag"=>$keyword_not_arr));
							}
							break;
					case "title" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('245'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('245'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "keyword" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('908'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('908'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "author" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('100','110'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('100','110'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "content" :
							if(is_var_array($keyword_arr)){
								if($search_option == "and"){
									$this->view_all_products_2->set_and_or_like_by_field("tag",array('520'));
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_like_by_field("tag",array('520'));
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "isbn" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('020'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "publisher" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('260'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
					case "call_number" :
							$this->view_all_products_2->set_and_or_like_by_field("tag",array('050'));
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("field_data"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("field_data"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
							}
							break;
					case "tag" :
							if(is_var_array($keyword_arr)){
								if($search_option == "or"){
									$this->view_all_products_2->set_and_or_like(array("product_tag"=>$keyword_arr));
								}else{
									$this->view_all_products_2->set_or_and_like_group(array("product_tag"=>$keyword_arr));
								}
							}
							if(is_var_array($keyword_not_arr)){
									$this->view_all_products_2->set_or_and_not_like_group(array("product_tag"=>$keyword_not_arr));
							}
							break;
				}
			}
			$this->view_all_products_2->set_close();
		}
				
		$product_main_aid = trim($this->input->get_post('product_main_aid'));
		// echo "product_main_aid = ".$product_main_aid;
		if(!is_blank($product_main_aid)){
			if(!is_number_no_zero($product_main_aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : product_main_aid must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("product_main_aid"=>$product_main_aid));
		}

		$type = trim($this->input->get_post('type'));
		if(!is_blank($type)){
			$product_type_obj = $this->check_product_type();
			$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
			$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
			// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";
			if(is_number_no_zero($product_type_aid)){
				$this->view_all_products_2->set_where(array("product_type_aid"=>$product_type_aid));
			}
		}

		$category = trim($this->input->get_post('category'));
		// echo "category = ".$category;
		if(!is_blank($category)){
			$category_serarch_list = "";
			$category_arr = preg_split('/,/', $category, -1, PREG_SPLIT_NO_EMPTY);
			if(is_var_array($category_arr)){
				foreach($category_arr as $item){
					if(!is_blank(trim($item))){
						if(is_number_no_zero($item)){
							$category_serarch_list[] = ",".$item.",";
						}
					}
				}
				$this->view_all_products_2->set_and_or_like_by_field("category",$category_serarch_list,"both");
			}
		}
		
		$publisher_aid = trim($this->input->get_post('publisher_aid'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publisher_aid)){
			if(!is_number_no_zero($publisher_aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publisher_aid must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("publisher_aid"=>$publisher_aid));
		}
		
		$publish_date_from = trim($this->input->get_post('publish_date_from'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publish_date_from)){
			$publish_date_from = get_datetime_pattern("db_date_format",$publish_date_from,"");
			if(is_blank($publish_date_from)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publish_date_from must be date.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where('publish_date >=', $publish_date_from);
		}
		
		$publish_date_to = trim($this->input->get_post('publish_date_to'));
		// echo "publish_date_to = $publish_date_to";
		if(!is_blank($publish_date_to)){
			$publish_date_to = get_datetime_pattern("db_date_format",$publish_date_to,"");
			if(is_blank($publish_date_to)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : publish_date_to must be date.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where('publish_date <=', $publish_date_to);
		}
		
		$aid = trim($this->input->get_post('id'));
		// echo "aid = ".$aid;
		if(!is_blank($aid)){
			if(!is_number_no_zero($aid)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : id must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			$this->view_all_products_2->set_where(array("aid"=>$aid));
		}

		$filter_type = $this->get_filter_type();
		switch ($filter_type) {
			case 'digital':
				$this->view_all_products_2->set_where(array("has_ebook"=>"1"));
				break;
			case 'paper':
				$this->view_all_products_2->set_where(array("has_paper"=>"1"));
				break;
		}

		$this->view_all_products_2->set_where(array("status"=>"1"));
		
		//Order by
		$order_by_master = array("match","publish_date_asc","publish_date_desc","total_download_desc","total_download_asc","total_view_desc","total_view_asc","total_read_desc","total_read_asc","title_desc","title_asc","author_desc","author_asc","popular","new","recommended");
		$order_by = trim($this->input->get_post('order_by'));
		if(!is_blank($order_by)){
			if(!in_array($order_by,$order_by_master)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data : order_by can not be \''.$order_by.'\'.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			switch($order_by){
				case "match" : $this->view_all_products_2->set_order_by("tag_weight ASC, weight asc, publish_date desc"); break;
				case "publish_date_asc" : $this->view_all_products_2->set_order_by("publish_date asc, weight asc"); break;
				case "publish_date_desc" : $this->view_all_products_2->set_order_by("publish_date desc, weight asc"); break;
				case "total_download_desc" : $this->view_all_products_2->set_order_by("total_download desc, weight asc, publish_date desc"); break;
				case "total_download_asc" : $this->view_all_products_2->set_order_by("total_download asc, weight asc, publish_date desc"); break;
				case "total_view_desc" : $this->view_all_products_2->set_order_by("total_view desc, weight asc, publish_date desc"); break;
				case "total_view_asc" : $this->view_all_products_2->set_order_by("total_view asc, weight asc, publish_date desc"); break;
				case "total_read_desc" : $this->view_all_products_2->set_order_by("total_read desc, weight asc, publish_date desc"); break;
				case "total_read_asc" : $this->view_all_products_2->set_order_by("total_read asc, weight asc, publish_date desc"); break;
				case "title_desc" : $this->view_all_products_2->set_order_by("title desc, weight asc, publish_date desc"); break;
				case "title_asc" : $this->view_all_products_2->set_order_by("title asc, weight asc, publish_date desc"); break;
				case "author_desc" : $this->view_all_products_2->set_order_by("author desc, weight asc, publish_date desc"); break;
				case "author_asc" : $this->view_all_products_2->set_order_by("author asc, weight asc, publish_date desc"); break;
				case "popular" : 
					// $this->view_all_products_2->set_where(array("is_home"=>"1"));
					$this->view_all_products_2->set_order_by("is_home DESC, weight asc, total_download desc, aid desc"); break;
				case "new" : 
					// $this->view_all_products_2->set_where(array("is_new"=>"1"));
					$this->view_all_products_2->set_order_by("is_new DESC,weight asc, publish_date desc, aid desc");
					break;
				case "recommended" : 
					// $this->view_all_products_2->set_where(array("is_recommended"=>"1"));
					$this->view_all_products_2->set_order_by("is_recommended DESC,weight asc, publish_date desc, aid desc");
					break;
				default : $this->view_all_products_2->set_order_by("publish_date desc, weight asc");
			}
		}
		
		//Limit
		$limit_start = trim($this->input->get_post('limit_start'));
		$no_record = trim($this->input->get_post('no_record'));
		// echo "limit_start = ".$limit_start;
		if(!is_blank($limit_start)){
			if(!is_number($limit_start)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : limit_start must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(is_blank($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify no_record.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
			
		}else{
			$limit_start = 0;
		}
		
		// echo "no_record = ".$no_record;
		if(!is_blank($no_record)){
			if(!is_number_no_zero($no_record)){
				$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : no_record must be integer.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		
		// echo "limit_start = $limit_start , no_record = $no_record";
		if(is_number_no_zero($no_record)){
			$this->view_all_products_2->set_limit($limit_start, $no_record);
		}
		
		$result_list = $this->view_all_products_2->load_records(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				// print_r($item);echo "<HR>";
				$parent_aid = get_array_value($item,"aid","");
				$product_type_aid = get_array_value($item,"product_type_aid","");
				
				$obj = array();
				$obj["parent_aid"] = $parent_aid;

				$obj["type"] = get_array_value($item,"product_type_cid","");
				$obj["title"] = get_array_value($item,"title","");
				$obj["author"] = get_array_value($item,"author","");
				$obj["description"] = get_array_value($item,"description","");
				$obj["cover_image"] = get_array_value($item,"cover_image_ipad","");
				$obj["cover_image_small"] = get_array_value($item,"cover_image_small","");
				$obj["cover_image_thumb"] = get_array_value($item,"cover_image_thumb","");
				$obj["cover_image_cover"] = get_array_value($item,"cover_image_cover","");
				$obj["cover_image_ori"] = get_array_value($item,"cover_image_ori","");
				$obj["publisher_aid"] = get_array_value($item,"publisher_aid","");
				$obj["publisher_name"] = get_array_value($item,"publisher_name","");
				
				$obj["publish_date"] = get_array_value($item,"publish_date","");
				$obj["publish_day"] = get_datetime_pattern("d",get_array_value($item,"publish_date",""),"");
				$obj["publish_month"] = get_datetime_pattern("m",get_array_value($item,"publish_date",""),"");
				$obj["publish_year"] = get_datetime_pattern("Y",get_array_value($item,"publish_date",""),"");
				$obj["has_license"] = get_array_value($item,"has_license","0");

				$obj["has_ebook"] = get_array_value($item,"has_ebook","0");
				$obj["has_paper"] = get_array_value($item,"has_paper","0");

				$obj["review_point"] = get_array_value($item,"review_point","0");

				// $this->load->model($this->view_all_product_fields_model, "view_all_product_fields_2");
				// $tmp = array();
				// $tmp["product_type_aid"] = $product_type_aid;
				// $tmp["parent_aid"] = $parent_aid;
				// $this->view_all_product_fields_2->set_where($tmp);
				// $field_result = $this->view_all_product_fields_2->load_records(false);

				$this->load->model($this->view_all_product_copies_model, "view_all_product_copies_2");
				$tmp = array();
				$tmp["product_type_aid"] = $product_type_aid;
				$tmp["parent_aid"] = $parent_aid;
				// $tmp["is_ebook"] = "1";
				$tmp["status"] = "1";
				$this->view_all_product_copies_2->set_where($tmp);
				$copy_result = $this->view_all_product_copies_2->load_records(true);
				$child_list = "";
				if(is_var_array($copy_result)){
					foreach($copy_result as $sub_item){
						$child = array();
						$type = get_array_value($sub_item,"type","");
						// echo "type = $type<BR>";
						$child["copy_aid"] = get_array_value($sub_item,"aid","");
						$child["copy_cid"] = get_array_value($sub_item,"cid","");
						$child["copy_barcode"] = get_array_value($sub_item,"barcode","");
						$child["copy_nonconsume_identifier"] = get_array_value($sub_item,"nonconsume_identifier","");
						$child["copy_publish_date"] = get_array_value($sub_item,"publish_date","");
						$child["copy_expired_date"] = get_array_value($sub_item,"expired_date","");
						$child["copy_is_ebook_license"] = get_array_value($sub_item,"is_license","0");
						$child["copy_ebook_concurrence"] = get_array_value($sub_item,"ebook_concurrence","");
						$child["copy_upload_path"] = get_array_value($sub_item,"upload_path","");
						$child["copy_file_upload"] = get_array_value($sub_item,"file_upload","");
						$child["copy_percent_discount"] = get_array_value($sub_item,"percent_discount","");
						$child["copy_status"] = get_array_value($sub_item,"status","");
						$child["copy_cover_price"] = get_array_value($sub_item,"cover_price","0");
						$child["copy_price_ios"] = get_array_value($sub_item,"digital_price_ios","0");
						$child["copy_price_android"] = get_array_value($sub_item,"digital_price_android","0");
						$child["copy_type"] = get_array_value($sub_item,"type","0");
						$child["copy_type_name"] = get_array_value($sub_item,"type_name","");
						if($type == "1"){
							$child["copy_price_web"] = get_array_value($sub_item,"digital_price","0");
						}else{
							$child["copy_price_web"] = get_array_value($sub_item,"paper_price","0");
						}
						$child_list[] = $child;
					}
				}
				$obj["copy_list"] = $child_list;
				$result[] = $obj;
			}
			
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result); 
			$this->log_status('response webservice search', 'status = success, keyword = '.trim(urldecode($this->input->get_post('keyword'))));
			echo json_encode($result_obj);
			return "";
		}else{
			
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "msg_th" => 'ไม่พบรายการ', "result" => '');   
			$this->log_status('response webservice search', 'status = warning, keyword = '.trim(urldecode($this->input->get_post('keyword'))), serialize($result_obj));
			echo json_encode($result_obj); 
			return "";
		}
	}



	function get_product_detail(){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = "";
		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
			$login_history = $this->check_token();
		}
		$user_aid = get_array_value($login_history,"user_aid","");
		
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";

		$aid = trim($this->input->get_post('id'));
		// echo "aid = ".$aid;
		if(is_blank($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify id.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_number_no_zero($aid)){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : id must be integer.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		switch ($product_type_aid) {
			case '1':
				$this->get_book_detail($aid);
				break;
			
			case '2':
				$this->get_magazine_detail($aid);
				break;

			case '3':
				$this->get_vdo_detail($aid);
				break;
			
			default:
				$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify type.', "result" => '');
				echo json_encode($result_obj);
				exit(0);
				break;
		}
	}

	function get_book_detail($aid=""){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = "";
		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
			$login_history = $this->check_token();
		}
		$user_aid = get_array_value($login_history,"user_aid","");
		
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";

		$this->load->model($this->book_model,'book');
		$this->book->set_where(array("aid"=>$aid, "status"=>"1"));
		$item = $this->book->load_record(true);
		
		if(is_var_array($item)){
			$this->db->flush_cache();
			$this->load->model($this->book_model,'book');
			$rs = $this->book->increase_total_view_device($aid);	
		
			$result = array();
			// print_r($item);echo "<HR>";
			$parent_aid = get_array_value($item,"aid","");
			
			$result = array();
			$result["parent_aid"] = $parent_aid;
			$result["parent_cid"] = get_array_value($item,"cid","");		
			$result["type"] = get_array_value($item,"product_type_cid","");
			$result["title"] = get_array_value($item,"title","");
			$result["author"] = get_array_value($item,"author","");
			$result["description"] = get_array_value($item,"description","");
			$result["cover_image"] = get_array_value($item,"cover_image_ipad","");
			$result["category_list"] = get_array_value($item,"category_list","");
			$result["publisher_aid"] = get_array_value($item,"publisher_aid","");
			$result["publisher_name"] = get_array_value($item,"publisher_name","");
			$result["product_main_aid"] = get_array_value($item,"product_main_aid","");
			$result["product_type_aid"] = get_array_value($item,"product_type_aid","");

			$result["review_point"] = get_array_value($item,"review_point","0");
			
			$result["publish_date"] = get_array_value($item,"publish_date","");
			$result["publish_day"] = get_datetime_pattern("d",get_array_value($item,"publish_date",""),"");
			$result["publish_month"] = get_datetime_pattern("m",get_array_value($item,"publish_date",""),"");
			$result["publish_year"] = get_datetime_pattern("Y",get_array_value($item,"publish_date",""),"");
			$result["biblio_field_result"] = get_array_value($item,"biblio_field_result","");
			
			$this->load->model($this->book_copy_model, "book_copy");
			$tmp = array();
			$tmp["parent_aid"] = $parent_aid;
			$tmp["status"] = "1";
			$tmp["is_ebook"] = "1";
			$this->book_copy->set_where($tmp);
			$copy_result = $this->book_copy->load_records(true);
			$child_list = "";
			if(is_var_array($copy_result)){
				foreach($copy_result as $item){
					$copy_aid = get_array_value($item,"aid","");
					$child = array();
					$child["copy_aid"] = get_array_value($item,"aid","");
					$child["copy_cid"] = get_array_value($item,"cid","");
					$child["copy_barcode"] = get_array_value($item,"barcode","");
					$child["copy_nonconsume_identifier"] = get_array_value($item,"nonconsume_identifier","");
					$child["copy_publish_date"] = get_array_value($item,"publish_date","");
					$child["copy_expired_date"] = get_array_value($item,"expired_date","");
					$child["copy_is_ebook_license"] = get_array_value($item,"is_license","0");
					$child["copy_ebook_concurrence"] = get_array_value($item,"ebook_concurrence","");
					$child["copy_upload_path"] = get_array_value($item,"upload_path","");
					$filedoc = "./".get_array_value($item,"upload_path","")."doc";
					$pathdoc = PUBLIC_PATH.get_array_value($item,"upload_path","")."doc";
					if(get_array_value($item,"file_upload","") != ""){
						$child["copy_file_upload"] = $pathdoc."/".get_array_value($item,"file_upload","");
					}else{
								//$filedoc = "'./".get_array_value($sub_item,"upload_path","")."doc'";
						if(file_exists($filedoc)){
							$objScan = scandir($filedoc);
						        foreach ($objScan as $value) 
						            {
						                if(strlen($value) > 3){
						                	$child["copy_file_upload"] = $pathdoc."/".$value;
						                }else{
						                	$child["copy_file_upload"] = "";
						                }
						            }		
						}else{
								$child["copy_file_upload"] = get_array_value($item,"file_upload","");
							}

					}
					//$child["copy_file_upload"] = get_array_value($item,"file_upload","");
					$child["copy_status"] = get_array_value($item,"status","");
					$child = $this->get_queue($child, $product_type_aid, $copy_aid, $user_aid);

					$total_queue = get_array_value($child,"total_queue","0");
					$my_queue = get_array_value($child,"my_queue","0");
					
					$is_ebook = get_array_value($item,"is_ebook","0");
					$is_ebook_license = get_array_value($item,"is_license","0");
					$ebook_concurrence = get_array_value($item,"ebook_concurrence","0");
					$rental_period = get_array_value($item,"rental_period","0");
					if($is_ebook){
						$shelf_available = array();
						$is_on_myshelf = 0;
						$is_available = 1;
						$description = "";
						$remain = "";

						if($is_ebook_license == '1'){
							$remain = $ebook_concurrence;
							$this->load->model($this->shelf_model,"shelf");
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["status"] = "1";
							$this->shelf->set_where($tmp);
							$now_on_shelf = $this->shelf->count_records(false);
							$remain = $ebook_concurrence - $now_on_shelf;
							if($now_on_shelf >= $ebook_concurrence || ($total_queue > 0 && $my_queue != "1") ){
								$is_available = 0;
								$remain = 0;
								$description = "Out of library shelf! ";
							}
						}

						if(!is_blank($token)){
							
							if($is_ebook_license == '1'){
								
								$this->load->model($this->shelf_model,"shelf");
								$tmp = array();
								$tmp["user_aid"] = $user_aid;
								$tmp["status"] = "1";
								$tmp["is_license"] = "1";
								$this->shelf->set_where($tmp);
								$now_on_my_shelf = $this->shelf->count_records(false);
								// echo "now_on_my_shelf = $now_on_my_shelf";
								if($now_on_my_shelf >= CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF){
									$is_available = 0;
									$remain = 0;
									$description = "Your circulating book shelf is max.";
								}
								
							}
							
							$this->load->model($this->shelf_model,"shelf");
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["user_aid"] = $user_aid;
							$tmp["status"] = "1";
							$this->shelf->set_where($tmp);
							$now_on_my_shelf = $this->shelf->count_records(false);
							if($now_on_my_shelf > 0){
								$is_available = 0;
								$is_on_myshelf = 1;
								$remain = $remain;
								$description = "Already on your bookshelf.";
							}
							
							
						}else{
							if(is_blank($description)){
								$description = "Please login.";
							}
						}

						$shelf_available["is_available"] = $is_available;
						$shelf_available["is_on_myshelf"] = $is_on_myshelf;
						$shelf_available["remain"] = $remain;
						$shelf_available["rental_period"] = $rental_period;
						$shelf_available["description"] = $description;
						$child["shelf_available"] = $shelf_available;

					}
					$child_list[] = $child;
				}
			}
			$result["copy_list"] = $child_list;
			
		
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}
	
	function get_magazine_detail($aid=""){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = "";
		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
			$login_history = $this->check_token();
		}
		$user_aid = get_array_value($login_history,"user_aid","");
		
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");
		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";

		$this->load->model($this->magazine_model,'magazine');
		$this->magazine->set_where(array("aid"=>$aid, "status"=>"1"));
		$item = $this->magazine->load_record(true);
		
		if(is_var_array($item)){
			$this->db->flush_cache();
			$this->load->model($this->magazine_model,'magazine');
			$rs = $this->magazine->increase_total_view_device($aid);	
		
			$result = array();
			// print_r($item);echo "<HR>";
			$parent_aid = get_array_value($item,"aid","");
			
			$result = array();
			$result["parent_aid"] = $parent_aid;
			$result["parent_cid"] = get_array_value($item,"cid","");		
			$result["type"] = get_array_value($item,"product_type_cid","");
			$result["title"] = get_array_value($item,"title","");
			$result["author"] = get_array_value($item,"author","");
			$result["description"] = get_array_value($item,"description","");
			$result["cover_image"] = get_array_value($item,"cover_image_ipad","");
			$result["category_list"] = get_array_value($item,"category_list","");
			$result["publisher_aid"] = get_array_value($item,"publisher_aid","");
			$result["publisher_name"] = get_array_value($item,"publisher_name","");
			$result["product_main_aid"] = get_array_value($item,"product_main_aid","");
			$result["product_type_aid"] = get_array_value($item,"product_type_aid","");

			$result["review_point"] = get_array_value($item,"review_point","0");
			
			$result["publish_date"] = get_array_value($item,"publish_date","");
			$result["publish_day"] = get_datetime_pattern("d",get_array_value($item,"publish_date",""),"");
			$result["publish_month"] = get_datetime_pattern("m",get_array_value($item,"publish_date",""),"");
			$result["publish_year"] = get_datetime_pattern("Y",get_array_value($item,"publish_date",""),"");
			$result["biblio_field_result"] = get_array_value($item,"biblio_field_result","");
			
			$this->load->model($this->magazine_copy_model, "magazine_copy");
			$tmp = array();
			$tmp["parent_aid"] = $parent_aid;
			$tmp["status"] = "1";
			$tmp["is_ebook"] = "1";
			$this->magazine_copy->set_where($tmp);
			$copy_result = $this->magazine_copy->load_records(true);
			$child_list = "";
			if(is_var_array($copy_result)){
				foreach($copy_result as $item){
					$copy_aid = get_array_value($item,"aid","");
					$child = array();
					$child["copy_aid"] = get_array_value($item,"aid","");
					$child["copy_cid"] = get_array_value($item,"cid","");
					$child["copy_barcode"] = get_array_value($item,"barcode","");
					$child["copy_nonconsume_identifier"] = get_array_value($item,"nonconsume_identifier","");
					$child["copy_publish_date"] = get_array_value($item,"publish_date","");
					$child["copy_expired_date"] = get_array_value($item,"expired_date","");
					$child["copy_is_ebook_license"] = get_array_value($item,"is_license","0");
					$child["copy_ebook_concurrence"] = get_array_value($item,"ebook_concurrence","");
					$child["copy_upload_path"] = get_array_value($item,"upload_path","");
					$child["copy_file_upload"] = get_array_value($item,"file_upload","");
					$child["copy_status"] = get_array_value($item,"status","");
					$child = $this->get_queue($child, $product_type_aid, $copy_aid, $user_aid);

					$total_queue = get_array_value($child,"total_queue","0");
					$my_queue = get_array_value($child,"my_queue","0");
					
					$is_ebook = get_array_value($item,"is_ebook","0");
					$is_ebook_license = get_array_value($item,"is_license","0");
					$ebook_concurrence = get_array_value($item,"ebook_concurrence","0");
					$rental_period = get_array_value($item,"rental_period","0");
					if($is_ebook){
						$shelf_available = array();
						$is_on_myshelf = 0;
						$is_available = 1;
						$description = "";
						$remain = "";

						if($is_ebook_license == '1'){
							$remain = $ebook_concurrence;
							$this->load->model($this->shelf_model,"shelf");
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["status"] = "1";
							$this->shelf->set_where($tmp);
							$now_on_shelf = $this->shelf->count_records(false);
							$remain = $ebook_concurrence - $now_on_shelf;
							if($now_on_shelf >= $ebook_concurrence || ($total_queue > 0 && $my_queue != "1") ){
								$is_available = 0;
								$remain = 0;
								$description = "Out of library shelf! ";
							}
						}

						if(!is_blank($token)){
							
							if($is_ebook_license == '1'){
								
								$this->load->model($this->shelf_model,"shelf");
								$tmp = array();
								$tmp["user_aid"] = $user_aid;
								$tmp["status"] = "1";
								$tmp["is_license"] = "1";
								$this->shelf->set_where($tmp);
								$now_on_my_shelf = $this->shelf->count_records(false);
								// echo "now_on_my_shelf = $now_on_my_shelf";
								if($now_on_my_shelf >= CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF){
									$is_available = 0;
									$remain = 0;
									$description = "Your circulating book shelf is max.";
								}
								
							}
							
							$this->load->model($this->shelf_model,"shelf");
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["user_aid"] = $user_aid;
							$tmp["status"] = "1";
							$this->shelf->set_where($tmp);
							$now_on_my_shelf = $this->shelf->count_records(false);
							if($now_on_my_shelf > 0){
								$is_available = 0;
								$is_on_myshelf = 1;
								$remain = $remain;
								$description = "Already on your bookshelf.";
							}
							
							
						}else{
							if(is_blank($description)){
								$description = "Please login.";
							}
						}

						$shelf_available["is_available"] = $is_available;
						$shelf_available["is_on_myshelf"] = $is_on_myshelf;
						$shelf_available["remain"] = $remain;
						$shelf_available["rental_period"] = $rental_period;
						$shelf_available["description"] = $description;
						$child["shelf_available"] = $shelf_available;

					}
					$child_list[] = $child;
				}
			}
			$result["copy_list"] = $child_list;
			
		
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}

	function get_vdo_detail($aid=""){
		$device = trim($this->input->get_post('device'));
		$this->check_device();
		
		$login_history = "";
		$token = trim($this->input->get_post('token'));
		if(!is_blank($token)){
			$login_history = $this->check_token();
		}
		$user_aid = get_array_value($login_history,"user_aid","");
		
		$product_type_obj = $this->check_product_type();
		$product_type_aid = get_array_value($product_type_obj,"product_type_aid","0");
		$product_type_cid = get_array_value($product_type_obj,"product_type_cid","0");

		// echo "product_type_aid = $product_type_aid , product_type_cid = $product_type_cid ";

		$this->load->model($this->vdo_model,'vdo');
		$this->vdo->set_where(array("aid"=>$aid, "status"=>"1"));
		$item = $this->vdo->load_record(true);
		
		if(is_var_array($item)){
			$this->db->flush_cache();
			$this->load->model($this->vdo_model,'vdo');
			$rs = $this->vdo->increase_total_view_device($aid);	
		
			$result = array();
			//print_r($item);echo "<HR>";
			$parent_aid = get_array_value($item,"aid","");

			$ext_source = get_array_value($item,"ext_source","");
			if(preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $ext_source, $matches) ){
				//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
				// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
				$ext_source = $matches[1];
			}
			if(strstr($ext_source, "https://youtu.be/")){
				//$url = 'https://www.youtube.com/watch?v=u9-kU7gfuFA'
				// preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
				$ext_source = substr(strrchr($ext_source,"/"),1);
			}
			$link_youtube = "http://www.youtube.com/embed/".$ext_source;
			$url = PUBLIC_PATH.get_array_value($item,"upload_path","")."file/".get_array_value($item,"uri","");
			//$category_list = get_array_value($item,"category_list","0")
			
			// if(is_var_array($category_list){
			// 	foreach($category_list as $category_item){

			// 	}
			// }
			$result = array();
			$result["parent_aid"] = $parent_aid;
			$result["parent_cid"] = get_array_value($item,"cid","");		
			$result["type"] = get_array_value($item,"product_type_cid","");
			$result["title"] = get_array_value($item,"title","");
			$result["author"] = get_array_value($item,"author","");
			$result["description"] = get_array_value($item,"description","");
			$result["cover_image"] = get_array_value($item,"cover_image_ipad","");
			if(!is_blank($ext_source) && $ext_source != "0"){
					$result["url"] = "";
					$result["link_youtube"] = $link_youtube;
			}else{
					$result["url"] = $url;
					$result["link_youtube"] = "";
			}

			
			$result["category_list"] = get_array_value($item,"category_list","");
			$result["publisher_aid"] = get_array_value($item,"publisher_aid","");
			$result["publisher_name"] = get_array_value($item,"publisher_name","");
			$result["product_main_aid"] = get_array_value($item,"product_main_aid","");
			$result["product_type_aid"] = get_array_value($item,"product_type_aid","");

			$result["review_point"] = get_array_value($item,"review_point","0");
			
			$result["publish_date"] = get_array_value($item,"publish_date","");
			$result["publish_day"] = get_datetime_pattern("d",get_array_value($item,"publish_date",""),"");
			$result["publish_month"] = get_datetime_pattern("m",get_array_value($item,"publish_date",""),"");
			$result["publish_year"] = get_datetime_pattern("Y",get_array_value($item,"publish_date",""),"");
			//$result["biblio_field_result"] = get_array_value($item,"biblio_field_result","");
			
				
							
			$this->load->model($this->shelf_vdo_model,"shelf_vdo");
			$tmp = array();
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["parent_aid"] = $parent_aid;
			$tmp["user_aid"] = $user_aid;
			$tmp["status"] = "1";
			$this->shelf_vdo->set_where($tmp);
			$now_on_my_shelf = $this->shelf_vdo->count_records(false);
			if($now_on_my_shelf > 0){
							
				$is_on_myshelf = "1";
			}else{
				$is_on_myshelf = "0";
			}			
			$result["is_on_myshelf"] = $is_on_myshelf;			
			
							
							
							
					

			
						
			$result["biblio_field_result"]= "";

			$this->load->model($this->vdo_copy_model, "vdo_copy");
			$tmp = array();
			$tmp["parent_aid"] = $parent_aid;
			$tmp["status"] = "1";
			$tmp["is_ebook"] = "1";
			$this->vdo_copy->set_where($tmp);
			$copy_result = $this->vdo_copy->load_records(true);
			$child_list = "";
			if(is_var_array($copy_result)){
				foreach($copy_result as $item){
					$copy_aid = get_array_value($item,"aid","");
					$child = array();
					$child["copy_aid"] = get_array_value($item,"aid","");
					$child["copy_cid"] = get_array_value($item,"cid","");
					$child["copy_barcode"] = get_array_value($item,"barcode","");
					$child["copy_nonconsume_identifier"] = get_array_value($item,"nonconsume_identifier","");
					$child["copy_publish_date"] = get_array_value($item,"publish_date","");
					$child["copy_expired_date"] = get_array_value($item,"expired_date","");
					$child["copy_is_ebook_license"] = get_array_value($item,"is_license","0");
					$child["copy_ebook_concurrence"] = get_array_value($item,"ebook_concurrence","");
					$child["copy_upload_path"] = get_array_value($item,"upload_path","");
					$child["copy_file_upload"] = get_array_value($item,"file_upload","");
					$child["copy_status"] = get_array_value($item,"status","");
					$child = $this->get_queue($child, $product_type_aid, $copy_aid, $user_aid);

					$total_queue = get_array_value($child,"total_queue","0");
					$my_queue = get_array_value($child,"my_queue","0");
					
					$is_ebook = get_array_value($item,"is_ebook","0");
					$is_ebook_license = get_array_value($item,"is_license","0");
					$ebook_concurrence = get_array_value($item,"ebook_concurrence","0");
					$rental_period = get_array_value($item,"rental_period","0");
					if($is_ebook){
						$shelf_available = array();
						$is_on_myshelf = 0;
						$is_available = 1;
						$description = "";
						$remain = "";

						if($is_ebook_license == '1'){
							$remain = $ebook_concurrence;
							$this->load->model($this->shelf_model,"shelf");
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["status"] = "1";
							$this->shelf->set_where($tmp);
							$now_on_shelf = $this->shelf->count_records(false);
							$remain = $ebook_concurrence - $now_on_shelf;
							if($now_on_shelf >= $ebook_concurrence || ($total_queue > 0 && $my_queue != "1") ){
								$is_available = 0;
								$remain = 0;
								$description = "Out of library shelf! ";
							}
						}

						if(!is_blank($token)){
							
							if($is_ebook_license == '1'){
								
								$this->load->model($this->shelf_model,"shelf");
								$tmp = array();
								$tmp["user_aid"] = $user_aid;
								$tmp["status"] = "1";
								$tmp["is_license"] = "1";
								$this->shelf->set_where($tmp);
								$now_on_my_shelf = $this->shelf->count_records(false);
								// echo "now_on_my_shelf = $now_on_my_shelf";
								if($now_on_my_shelf >= CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF){
									$is_available = 0;
									$remain = 0;
									$description = "Your circulating book shelf is max.";
								}
								
							}
							
							$this->load->model($this->shelf_model,"shelf");
							$tmp = array();
							$tmp["product_type_aid"] = $product_type_aid;
							$tmp["copy_aid"] = $copy_aid;
							$tmp["user_aid"] = $user_aid;
							$tmp["status"] = "1";
							$this->shelf->set_where($tmp);
							$now_on_my_shelf = $this->shelf->count_records(false);
							if($now_on_my_shelf > 0){
								$is_available = 0;
								$is_on_myshelf = 1;
								$remain = $remain;
								$description = "Already on your bookshelf.";
							}
							
							
						}else{
							if(is_blank($description)){
								$description = "Please login.";
							}
						}

						$shelf_available["is_available"] = $is_available;
						$shelf_available["is_on_myshelf"] = $is_on_myshelf;
						$shelf_available["remain"] = $remain;
						$shelf_available["rental_period"] = $rental_period;
						$shelf_available["description"] = $description;
						$child["shelf_available"] = $shelf_available;

					}
					$child_list[] = $child;
				}
			}
			$result["copy_list"] = $child_list;

			
		
			$result_obj = array("status" => 'success',"msg" => '', "result" => $result);
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'warning',"msg" => 'No record found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}

	function convert_keyword_to_array($keyword=""){
		$keyword = trim($keyword);
		// $keyword = "ming    oui=-tisa -  --------------  -- -- -       -    thida ";
		// echo "keyword = $keyword <BR />";
		$keyword = preg_replace('/-/', ' -', $keyword);
		$keyword = preg_replace('/\s\s+/', ' ', $keyword);
		$keyword = preg_replace('/-\s/', '-', $keyword);
		$keyword = preg_replace('/--+/', '-', $keyword);
		$keyword = trim($keyword);
		// echo "keyword = $keyword";

		$keyword_arr = array();
		if(!is_blank($keyword)){
			$keyword_arr = explode(",", $keyword);
			return $keyword_arr;
		}else{
			return "";
		}
	}

	function request_download(){
		$device = trim($this->input->get_post('device'));
		$device_id = trim($this->input->get_post('device_id'));
		$this->check_device();
		
		$login_history = $this->check_token(false);
		
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
		$is_ebook = get_array_value($view_all_copies_result,"is_ebook","0");
		if($is_ebook != '1'){
			$result_obj = array("status" => 'error',"msg" => 'This copy is not ebook.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$parent_aid = get_array_value($view_all_copies_result,"parent_aid","0");
		$digital_file_type = get_array_value($view_all_copies_result,"digital_file_type","");
		$model = $this->get_product_model($product_type_aid);
		$model_name = get_array_value($model,"product_model","");
		$this->db->flush_cache();
		$this->db->_reset_select();
		$this->load->model($model_name, $model_name);
		$tmp = array();
		$tmp['aid'] = $parent_aid;
		$this->{$model_name}->set_where($tmp);
		$parent_detail = $this->{$model_name}->load_record(true);
		
		$user_aid = get_array_value($login_history,"user_aid","0");
		if(is_number_no_zero($user_aid))
		{
			$this->load->model($this->shelf_model,"shelf");
			$tmp =array();
			$tmp["user_aid"] = $user_aid;
			$tmp["product_type_aid"] = $product_type_aid;
			$tmp["copy_aid"] = $aid;
			$this->shelf->set_where($tmp);
			$shelf_detail = $this->shelf->load_record(false);
			if(!is_var_array($shelf_detail)){
				$result_obj = array("status" => 'error',"msg" => 'This copy is not on shelf.', "result" => '');
				echo json_encode($result_obj);
				return "";
			}
		}
	
		$iv = get_array_value($parent_detail,"iv","");
		$upload_path = get_array_value($view_all_copies_result,"upload_path","")."app";
		// echo "upload_path = $upload_path<BR>";
		
		$json = self::gen_json($upload_path);
		
		//if(count($json)>0)
		if(count($json["pages"]) > 0)
        {
        	// $this->load->model($this->request_file_download_model, "request_file_download");
//         	$tmp =array();
//         	$tmp["user_aid"] = $user_aid;
// 			$tmp["product_type_aid"] = $product_type_aid;
// 			$tmp["copy_aid"] = $aid;
// 			$tmp["product_type_aid"] = $product_type_aid;
// 			$tmp["product_type_cid"] = $product_type_cid;
// 			$tmp["parent_aid"] = $parent_aid;
// 			$tmp["device"] = $device;
// 			$tmp["ip"] = $this->input->ip_address();
// 			$tmp["created_by"] = $user_aid;
// 			$tmp["updated_by"] = $user_aid;
// 			
// 			$this->request_file_download->insert_record($tmp);
			
            $result_obj = array("status" => 'success',"msg" => '', "result" => $json);
            echo json_encode($result_obj);
            exit(0);
        }
        else{
            $result_obj = array("status" => 'error',"msg" => 'no files', "result" => '');
            echo json_encode($result_obj);
            exit(0);
        }
	}
	
	public function gen_json($upload_path)
    {
    	$path = getcwd()."/".$upload_path;

        $link_prefix = site_url($upload_path);
                
        $pages = array();
    	$objScan = scandir($path);
        $i = 1;
        $k = 0;
        foreach ($objScan as $value)
        {        
        	//pdf
            if(strpos($value, "ncrypt_pdf") == 1)
            {
                $size = filesize($path."/".$value);
                if($size > 2968)
                {
                    $medias = array();
                    $page = array(
                        'page' => 'page-'.$i,
                        'link' => $link_prefix."/".$value,
                        'size' => $size,
                        'medias' => $medias
                    );
                    $pages[] = $page; 
                    $i++;
                    $k += $size;
                }            
            }
        }
            
        $full_pdf = array();
        if(file_exists($upload_path."/pdf.pdf"))  
        {
            $size = filesize($path."/pdf.pdf");
            $full_pdf = array(
                'link' => $link_prefix."/pdf.pdf",
                'size' => $size
            );
        }
     
        $detail = array('content_type' => 'PDF', 'total_page' => $i-1, 'total_size' => $k); 
        $return_array = array('detail' => $detail, 'pages' => $pages, 'full_pdf' => $full_pdf); 
        return $return_array; 
    }

    function add_book_and_book_copy()
    {

    	$json = $this->input->get_post('json');
    	
    	if(is_var_array($json)){
			foreach($json as $item){
				//echo "ZZZZZZZZZZZZZZZZZZ";
				//var_dump($item);
				//var_dump(json_decode($item));
				$my_item = json_decode($item);
				
				$data = array();
		   		$data["publisher_name"] = $my_item->publisher;
		 		//echo "aid = ".self::check_publisher($data);
		 		//echo "aid 2 = ".$this->check_publisher($data);
		 		//$data["publisher_aid"] = $this->check_publisher($data);

		 		$data["publisher_aid"] = self::check_publisher($data);
		 		
		 		$data["product_type_aid"] = 1;
		 		$data["product_main_aid"] = 3;
		 		$data["status"] = 0;
		 		$data["weight"] = 0;
		 		$data["is_new"] = 0;
		 		$data["is_recommended"] = 0;
		 		$data["is_home"] = 0;
		 		$data["publish_date"] = "";
		 		$data["expired_date"] = "";
		 		$data["bcc_status"] = 1;
		 		$data["cover_image"] = $my_item->images->cover_image; //$this->input->get_post('cover_image');
		 		$data["thumbnail_image"] = $my_item->images->thumbnail_image; //$this->input->get_post('thumbnail_image');
		 		$data["large_image"] = $my_item->images->large_image; //$this->input->get_post('large_image');
		 		$data["category"] = "";
		 		$data["tag"] = "";

		 		$data["title"] = $my_item->title;
		 		$data["author"] = $my_item->author[0]->name;
		 		$data["description"] = $my_item->description;
		 		$data["pages"] = $my_item->pages;
		 		$data["ISBN"] = $my_item->ISBN;

				
		 		//echo "parent_aid = ".$this->add_book_to_db($data);
		 		//$data["parent_aid"] = $this->add_book_to_db($data);

		 		$data["parent_aid"] = self::add_book_to_db($data);
		 		
		 		$data["cover_price"] = $my_item->cover_price;
		 		$data["type"] = 1; // 1=digital, 2=paper
		 		$data["is_license"] = $my_item->is_license;

		 		if($data["is_license"] == 1)
		 		{
					$data["ebook_concurrence"] = 2;
					$data["rental_period"] = 5;
					$data["possession"] = 2; //1=buy out, 2=rental
					$data["type_minor"] = 7; //6=license+free, 7=license+rental
		 		}
		 		else{
		 			$data["ebook_concurrence"] = 0;
		 			$data["rental_period"] = 0;
		 			$data["possession"] = 1; //1=buy out, 2=rental
		 			$data["type_minor"] = 3; //3=free, 4=sale, 5=rental
		 		}

				$data["is_ebook"] = 1;
													
		 		//print_r($data);

		 		self::add_book_copy_to_db($data);
		 		//$this->add_book_copy_to_db($data);
			}
		}


    	/*
   		$data = array();
   		$data["publisher_name"] = $this->input->get_post('publisher_name');
 		//echo "aid = ".self::check_publisher($data);
 		//echo "aid 2 = ".$this->check_publisher($data);
 		$data["publisher_aid"] = $this->check_publisher($data);
 		$data["product_type_aid"] = $this->input->get_post('product_type_aid');
 		$data["product_main_aid"] = $this->input->get_post('product_main_aid');
 		$data["status"] = $this->input->get_post('status');
 		$data["weight"] = $this->input->get_post('weight');
 		$data["is_new"] = $this->input->get_post('is_new');
 		$data["is_recommended"] = $this->input->get_post('is_recommended');
 		$data["is_home"] = $this->input->get_post('is_home');
 		$data["publish_date"] = $this->input->get_post('publish_date');
 		$data["expired_date"] = $this->input->get_post('expired_date');
 		$data["bcc_status"] = $this->input->get_post('bcc_status');
 		$data["cover_image"] = $this->input->get_post('cover_image');
 		$data["thumbnail_image"] = $this->input->get_post('thumbnail_image');
 		$data["large_image"] = $this->input->get_post('large_image');
 		$data["category"] = $this->input->get_post('category');
 		$data["tag"] = $this->input->get_post('tag');

 		echo "parent_aid = ".$this->add_book_to_db($data);
 		$data["parent_aid"] = $this->add_book_to_db($data);
 		*/
    }
     
    function check_publisher($input_data)
    {
    	$name = $input_data['publisher_name'];
		$this->load->model('Publisher_model',"main");
		$this->main->set_where(array("name"=>$name));
		$total = $this->main->count_records(false);
		if($total > 0)
		{
			//echo "abcd";
		}
		else{
			
			$this->load->model('Publisher_model','main');
			
			$cid = ""; //$input_data['cid'];
			$user_owner_aid = $this->get_user_owner_aid_by_input();
			$my_data = array();
			$my_data["user_owner_aid"] = $user_owner_aid;
			$my_data["name"] = $name;
			$url = $this->input->get_post('url');
			if(is_blank($url)){
				$url = $name;
			}
			$my_data["url"] = getUrlString($url);
			
			$my_data["status"] = 1; //$this->input->get_post('status');

			if(is_blank($cid)){
				do{
					$this->load->model('Setting_config_model','setting_config');		
					$obj = $this->setting_config->get_config_rni_by_cid("rn-publisher");
					$cid = trim(get_array_value($obj,"barcode",""));
				}while( self::isPublisherCidExisting($cid) );
			}
			$my_data["cid"] = $cid;

			$aid = $this->main->insert_record($my_data);
			// if($aid > 0){
			// 	$my_data["aid"] = $aid;
			// 	$this->log_status('Backend : Insert publisher', '['.$name.'] just added into database.', $my_data);
			// }else{
			// 	$this->log_error('Backend : Insert publisher', 'Command insert_record() fail. Can not insert '.$name, $my_data);
			// }
		}

		$this->main->set_where(array("name"=>$name));
		$itemResult = $this->main->load_record(false);

		//echo json_encode(array("status"=>"success","aid"=>get_array_value($itemResult,"aid",0)));
		return get_array_value($itemResult,"aid",0);
	
    }

    function isPublisherCidExisting($cid){
		$this->load->model($this->publisher_model,"publisher");
		$this->publisher->set_where(array("cid"=>$cid));
		$total = $this->publisher->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

    function add_book_to_db($input_data)
    {

    	$this->load->model('Book_model','main_book');

    	$product_type_aid = $input_data['product_type_aid'];
		$product_main_aid = $input_data['product_main_aid'];
		$user_owner_aid = $this->get_user_owner_aid_by_input();
				
		$data = array();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["product_main_aid"] = $product_main_aid;
		$data["product_type_aid"] = $product_type_aid;
		$data["publisher_aid"] = $input_data['publisher_aid'];
		$data["status"] = $input_data['status'];
		$data["weight"] = $input_data['weight'];
		$data["is_new"] = $input_data['is_new'];
		$data["is_recommended"] = $input_data['is_recommended'];
		$data["is_home"] = $input_data['is_home'];
		$data["publish_date"] = get_datetime_pattern("db_date_format",$input_data['publish_date'],get_db_now('%Y-%m-%d'));
		$data["expired_date"] = get_datetime_pattern("db_date_format",$input_data['expired_date'],NULL);
		$data["reward_point"] = "";
			
		$data["bcc_status"] = $input_data['bcc_status'];
		$data["cover_image"] = $input_data['cover_image'];
		$data["thumbnail_image"] = $input_data['thumbnail_image'];
		$data["large_image"] = $input_data['large_image'];

		$input_data_field = array();
		$input_data_field["field_1"] =  $input_data['title'];
		$input_data_field["field_2"] =  $input_data['author'];
		$input_data_field["field_3"] =  $input_data['description'];
		$input_data_field["field_4"] =  $input_data['pages'];
		$input_data_field["field_22"] =  $input_data['ISBN'];
				


		$category_list = "";
		$category = $input_data['category'];
		// echo "category : ".$category;
		if(is_var_array($category)){
			$category_list = ",";
			foreach($category as $item){
				$category_list .= $item.',';
			}
			// $category_list = substr($category_list, 1);
		}
		$data["category"] = $category_list;

		//tag
		$all_tag = "";
		$tag = $input_data['tag'];
		// echo "tag : ".$tag;
		if(!is_blank($tag)){
			$tag = explode(",", $tag);
			if(is_var_array($tag)){
				$all_tag = ",";
				foreach($tag as $item){
					$item = trim($item);
					if(!is_blank($item)){
						$all_tag .= $item.',';
					}
				}
				// $all_tag = substr($all_tag, 1);
			}
		}
		if(!is_blank($all_tag)){
			$data["tag"] = $all_tag;
		}
		
		$data["total_view"] = 0;
		$data["total_view_web"] = 0;
		$data["total_view_device"] = 0;
		$data["total_download"] = 0;
		$data["total_download_web"] = 0;
		$data["total_download_device"] = 0;
		$data["total_read"] = 0;
		$data["total_read_web"] = 0;
		$data["total_read_device"] = 0;
		$data["total_rental"] = 0;
		$data["review_point"] = 0;
			
		$aid = $this->main_book->insert_record($data);
		
		if($aid > 0){
			$this->main_book->update_parent($aid);

			//$result = $this->save_category($product_type_aid,$aid,$category);
			//$result = $this->save_tag($product_type_aid,$aid,$tag);
			$book_obj = self::add_marc_field($product_type_aid,$product_main_aid,$aid, $input_data_field);
			$name = get_array_value($book_obj,"title","-");
			$author = get_array_value($book_obj,"author","");
			$data["aid"] = $aid;
			
			

			$cid = get_text_pad($aid,"0",CONST_ZERO_PAD_FOR_PRODUCT);
	
			$data_where = array();
			$data_where["aid"] = $aid;
			$data_new = array();
			$data_new["cid"] = $cid;
			$data_new["title"] = $name;
			$data_new["author"] = $author;
			
			$this->main_book->set_where($data_where);
			$this->main_book->update_record($data_new);
			
			// echo json_encode(array("status"=>"success", "parent_aid"=>$aid));
			// return "";
			return $aid;
		}else{
			// echo json_encode(array("status"=>"error"));
			// return "";
			return 0;
		}
    }

    function add_book_copy_to_db($input_data)
    {
    	$this->load->model('Book_copy_model','main_book_copy');

		$product_main_aid = $input_data['product_main_aid'];
		$product_type_aid = $input_data['product_type_aid'];
		$parent_aid = trim($input_data['parent_aid']);
		$data["parent_aid"] = $parent_aid;

		//$parent_detail = $this->check_exits_book($parent_aid, false);
		$parent_detail = self::check_book_existing($parent_aid, false);
			//echo $this->db->last_query();
			//die();
		$data["product_type_aid"] = $product_type_aid;
		$title = get_array_value($parent_detail,"title","");

		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;

		$today_date = get_datetime_pattern("Y-m-d",$input_data['publish_date'],date("Y-m-d"));
		$upload_path = "";
		$cid = "";
		$aid = "";
		$barcode = "";
			
		// insert
			
		do{
			$cid = trim(random_string('alnum', 12));
		}while( self::isBookCidExisting($cid) );
		
		$data["cid"] = trim($cid);

		$data["publish_date"] = get_datetime_pattern("Y-m-d",$input_data['publish_date'],NULL);
		$data["expired_date"] = get_datetime_pattern("Y-m-d",$input_data['expired_date'],NULL);
		$data["weight"] = '0';
		$data["status"] = 1;

		$data["cover_price"] = trim($input_data['cover_price']);
		$data["source"] = "";
		$data["no_1"] = "";
		$data["no_2"] = "";
		$data["no_3"] = "";
		$data["no_4"] = "";

		/* Possession / Price Information */
		$type = $input_data['type'];
		$type_minor = $input_data['type_minor'];
		$is_license = $input_data['is_license'];
		$ebook_concurrence = $input_data['ebook_concurrence'];
		$possession = $input_data['possession'];
		
		$rental_period = $input_data['rental_period'];

		$rental_fee = 0;
		$rental_fee_point = 0;
		$rental_fine_fee = 0;
		$shelf_status = 1;//1=on shelf, 2=borrowed, 3=damage, 4=lost
		$shelf_name = "";

		$product_type_minor_aid = '0';
		$is_ebook= 0;

		$is_ebook = 1;
		$paper_price = "";
		$paper_point = "";
		$transport_aid = "";
		$transport_price = "";
		$in_stock = "";

		if($is_license == '1')
		{
			$product_type_minor_aid = '7';
		}else{
			$is_license = "";
			$ebook_concurrence = "";
			if($possession == '1'){
				if($paper_price > 0 && $paper_point > 0){
					$product_type_minor_aid = '4';						
				}else{
					$product_type_minor_aid = '3';						
				}
			}else{
				$product_type_minor_aid = '5';
			}
		}

		// echo "product_type_minor_aid = $product_type_minor_aid <BR>";
		$data["product_type_minor_aid"] = $product_type_minor_aid;

		$data["type"] = $type;
		$data["type_minor"] = $type_minor;
		$data["possession"] = $possession;
		$data["is_ebook"] = $is_ebook;
		$data["is_license"] = $is_license;
		$data["ebook_concurrence"] = $ebook_concurrence;

		$data["digital_price"] = "";
		$data["digital_point"] = "";
		$data["paper_price"] = $paper_price;
		$data["paper_point"] = $paper_point;
		$data["transport_aid"] = $transport_aid;
		$data["transport_price"] = $transport_price;
		$data["in_stock"] = $in_stock;
		$data["rental_period"] = $rental_period;
		$data["rental_fee"] = $rental_fee;
		$data["rental_fee_point"] = $rental_fee_point;
		$data["rental_fine_fee"] = $rental_fine_fee;
		$data["shelf_status"] = $shelf_status;
		$data["shelf_name"] = $shelf_name;

		do{
			$this->load->model('Setting_config_model','setting_config');		
			$obj = $this->setting_config->get_config_rni_by_product_type_minor_aid($product_type_minor_aid);
			$barcode = trim(get_array_value($obj,"barcode",""));
			$value = trim(get_array_value($obj,"value","0"));
		}while( self::isBookBarcodeExisting($barcode, "") );
		$data["barcode"] = $barcode;

		$data["user_owner_aid"] = $user_owner_aid;
				
		$aid = $this->main_book_copy->insert_record($data);
	
		if($aid){
			$this->load->model('Book_model','book');
			$this->book->update_parent($parent_aid);
			// echo json_encode(array("status"=>"success", "copy_aid"=>$aid));
			// return "";
		}else{
			// echo json_encode(array("status"=>"error"));
			// return "";
		}
    }

    function add_marc($product_type_aid="",$product_main_aid="",$parent_aid="")
    {
    	$json = $this->input->get_post('json');
    	// echo json_encode(array("result"=>json_encode($json)));
    	// echo json_encode(array("result"=>$json));
    	// return "";

    	$mydata = array();
    	if(is_var_array($json)){

			foreach($json as $item){
				foreach ($item as $key => $value) {
					if($key == "data")
					{
						echo $key." : ".$value[0];
					}
					else{
						echo $key." : ".$value;
					}
					echo "<br />";
				}
		   	}
		}
		
    }

    function add_marc_field($product_type_aid="",$product_main_aid="",$aid="", $data_field){
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
					$field_data = trim($data_field['field_'.$product_main_field_aid]);

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

	function check_book_existing($parent_aid="",$return_json=false){
		// if(!is_number_no_zero($parent_aid)){
		// 	redirect('admin/product-'.$product_main_url.'/book/status/'.md5('no-book'));
		// 	return"";
		// }
		$this->load->model('Book_model','book');
		$this->book->set_where(array("aid"=>$parent_aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$parent_detail = $this->book->load_record(true);
		if(is_var_array($parent_detail)){
			return $parent_detail;
		}else{
			if($return_json){
				$msg = set_message_error('Error occurred. Can not find this book.');
				$result_obj = array("status" => 'error',"msg" => $msg );
				echo json_encode($result_obj);
				return "";
			}else{
				// redirect('admin/product-'.$product_main_url.'/book/status/'.md5('no-book'));
				// return"";
			}
		}
	}

	function isBookCidExisting($cid){
		$this->load->model('Book_copy_model',"main_book");
		$this->main_book->set_where(array("cid"=>$cid));
		$total = $this->main_book->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function isBookBarcodeExisting($code, $aid){
		$this->load->model('Book_copy_model',"main_book_copy");
		$this->main_book_copy->set_where(array("barcode"=>$code));
		if(is_number_no_zero($aid)){
			$this->main_book_copy->set_where_not_equal(array("aid"=>$aid));
		}
		$total = $this->main_book_copy->count_records(false);
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}

	function add_magazine_and_magazine_copy()
	{
		$json = $this->input->get_post('json');
    	
    	if(is_var_array($json)){
			foreach($json as $item){
				
				$my_item = json_decode($item);
				
				$data = array();
		   		$data["publisher_name"] = $my_item->publisher;
		 		//echo "aid = ".self::check_publisher($data);
		 		//echo "aid 2 = ".$this->check_publisher($data);
		 		//$data["publisher_aid"] = $this->check_publisher($data);
		 		$data["publisher_aid"] = self::check_publisher($data);
		 		$data["title"] = $my_item->title;
		 		$data["magazine_main_aid"] = self::add_magazine_main($data);

		 		$data["product_type_aid"] = 2; //Magazine
		 		$data["product_main_aid"] = 4; //emagazines
		 		$data["status"] = 0;
		 		$data["weight"] = 0;
		 		$data["is_new"] = 0;
		 		$data["is_recommended"] = 0;
		 		$data["is_home"] = 0;

		 		$data["volumn"] = $my_item->volume;
				$data["issue"] = $my_item->issue;
				
				if(isset($my_item->issue_else))
				{
					if($my_item->issue_else != NULL)
					{
						$data["desc"] = $my_item->issue_else;
					}
					else{
						$data["desc"] = "";	
					}	
				}
				
				

		 		$data["publish_date"] = "";
		 		$data["expired_date"] = "";
		 		$data["bcc_status"] = 1;
		 		$data["cover_image"] = $my_item->images->cover_image; //$this->input->get_post('cover_image');
		 		$data["thumbnail_image"] = $my_item->images->thumbnail_image; //$this->input->get_post('thumbnail_image');
		 		$data["large_image"] = $my_item->images->large_image; //$this->input->get_post('large_image');
		 		$data["category"] = "";
		 		$data["tag"] = "";

		 		if($my_item->author != NULL)
		 		{
		 			$data["author"] = $my_item->author[0]->name;	
		 		}
		 		
		 		$data["description"] = $my_item->description;
		 		$data["pages"] = $my_item->pages;
		 		$data["ISSN"] = $my_item->ISSN;

		 		$data["parent_aid"] = self::add_magazine_to_db($data);
		 		echo json_encode($data);
		 	// 	$data["cover_price"] = $my_item->cover_price;
		 	// 	$data["type"] = 1; // 1=digital, 2=paper
		 	// 	$data["is_license"] = $my_item->is_license;

		 	// 	if($data["is_license"] == 1)
		 	// 	{
				// 	$data["ebook_concurrence"] = 2;
				// 	$data["rental_period"] = 5;
				// 	$data["possession"] = 2; //1=buy out, 2=rental
				// 	$data["type_minor"] = 7; //6=license+free, 7=license+rental
		 	// 	}
		 	// 	else{
		 	// 		$data["ebook_concurrence"] = 0;
		 	// 		$data["rental_period"] = 0;
		 	// 		$data["possession"] = 1; //1=buy out, 2=rental
		 	// 		$data["type_minor"] = 3; //3=free, 4=sale, 5=rental
		 	// 	}

				// $data["is_ebook"] = 1;
													
		 	// 	//print_r($data);

		 	// 	self::add_book_copy_to_db($data);
		 	// 	//$this->add_book_copy_to_db($data);
			}
		}
	}

	function add_magazine_main($input_data)
	{
		$name = $input_data['title'];
		$publisher_aid = $input_data['publisher_aid'];
		$this->load->model('Magazine_main_model',"mag_main");
		
		

		$this->mag_main->set_where(array("title"=>$name));
		$total = $this->mag_main->count_records(false);
		if($total > 0)
		{
			//echo "abcd";
		}
		else{
			$user_owner_aid = $this->get_user_owner_aid_by_input();
			$my_data = array();
			$my_data["user_owner_aid"] = $user_owner_aid;
			$my_data["product_main_aid"] = 4;
			$my_data["product_type_aid"] = 2;
			$my_data["publisher_aid"] = $publisher_aid;
			$my_data["title"] = $name;
			$my_data["status"] = 1;

			$aid = $this->mag_main->insert_record($my_data);
			// if($aid > 0){
			// 	$my_data["aid"] = $aid;
			// 	$this->log_status('Backend : Insert publisher', '['.$name.'] just added into database.', $my_data);
			// }else{
			// 	$this->log_error('Backend : Insert publisher', 'Command insert_record() fail. Can not insert '.$name, $my_data);
			// }
		}

		$this->mag_main->set_where(array("title"=>$name));
		$itemResult = $this->mag_main->load_record(false);

		//echo json_encode(array("status"=>"success","aid"=>get_array_value($itemResult,"aid",0)));
		return get_array_value($itemResult,"aid",0);
	}

	function add_magazine_to_db($input_data)
    {

    	$this->load->model('Magazine_model','main_mag');

    	$product_type_aid = $input_data['product_type_aid'];
		$product_main_aid = $input_data['product_main_aid'];
		$user_owner_aid = $this->get_user_owner_aid_by_input();
				
		$data = array();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["product_main_aid"] = $product_main_aid;
		$data["product_type_aid"] = $product_type_aid;
		$data["publisher_aid"] = $input_data['publisher_aid'];
		$data["magazine_main_aid"] = $input_data["magazine_main_aid"];

		$data["status"] = $input_data['status'];
		$data["weight"] = $input_data['weight'];
		$data["is_new"] = $input_data['is_new'];
		$data["is_recommended"] = $input_data['is_recommended'];
		$data["is_home"] = $input_data['is_home'];
		$data["publish_date"] = get_datetime_pattern("db_date_format",$input_data['publish_date'],get_db_now('%Y-%m-%d'));
		$data["expired_date"] = get_datetime_pattern("db_date_format",$input_data['expired_date'],NULL);
		$data["reward_point"] = "";
			
		$data["volumn"] = "";
		$data["issue"] = "";
		$data["desc"] = "";

		$data["bcc_status"] = $input_data['bcc_status'];
		$data["cover_image"] = $input_data['cover_image'];
		$data["thumbnail_image"] = $input_data['thumbnail_image'];
		$data["large_image"] = $input_data['large_image'];

		$input_data_field = array();
		$input_data_field["field_5"] =  $input_data['title'];
		$input_data_field["field_6"] =  get_array_value($input_data,"author",""); //$input_data['author'];
		$input_data_field["field_7"] =  $input_data['description'];
		$input_data_field["field_8"] =  $input_data['pages'];
		$input_data_field["field_23"] =  $input_data['ISSN'];
				


		$category_list = "";
		$category = $input_data['category'];
		// echo "category : ".$category;
		if(is_var_array($category)){
			$category_list = ",";
			foreach($category as $item){
				$category_list .= $item.',';
			}
			// $category_list = substr($category_list, 1);
		}
		$data["category"] = $category_list;

		//tag
		$all_tag = "";
		$tag = $input_data['tag'];
		// echo "tag : ".$tag;
		if(!is_blank($tag)){
			$tag = explode(",", $tag);
			if(is_var_array($tag)){
				$all_tag = ",";
				foreach($tag as $item){
					$item = trim($item);
					if(!is_blank($item)){
						$all_tag .= $item.',';
					}
				}
				// $all_tag = substr($all_tag, 1);
			}
		}
		if(!is_blank($all_tag)){
			$data["tag"] = $all_tag;
		}
		
		$data["total_view"] = 0;
		$data["total_view_web"] = 0;
		$data["total_view_device"] = 0;
		$data["total_download"] = 0;
		$data["total_download_web"] = 0;
		$data["total_download_device"] = 0;
		$data["total_read"] = 0;
		$data["total_read_web"] = 0;
		$data["total_read_device"] = 0;
		$data["total_rental"] = 0;
		$data["review_point"] = 0;
			
		
		$aid = $this->main_mag->insert_record($data);
		
		if($aid > 0){
			$this->main_mag->update_parent($aid);

			//$result = $this->save_category($product_type_aid,$aid,$category);
			//$result = $this->save_tag($product_type_aid,$aid,$tag);
			$book_obj = self::add_marc_field($product_type_aid,$product_main_aid,$aid, $input_data_field);
			$name = get_array_value($book_obj,"title","-");
			$author = get_array_value($book_obj,"author","");
			$data["aid"] = $aid;
			
			

			$cid = get_text_pad($aid,"0",CONST_ZERO_PAD_FOR_PRODUCT);
	
			$data_where = array();
			$data_where["aid"] = $aid;
			$data_new = array();
			$data_new["cid"] = $cid;
			$data_new["title"] = $name;
			$data_new["author"] = $author;
			
			$this->main_mag->set_where($data_where);
			$this->main_mag->update_record($data_new);
			
			// echo json_encode(array("status"=>"success", "parent_aid"=>$aid));
			// return "";
			return $aid;
		}else{
			// echo json_encode(array("status"=>"error"));
			// return "";
			return 0;
		}
    }
}

?>