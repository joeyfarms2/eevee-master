<?php
class Product_init_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
	}

	function get_product_model($product_type_aid=""){
		switch ($product_type_aid) {
			case '1':
			case 'book':
				$product_model = $this->book_model;
				$product_copy_model = $this->book_copy_model;
				$product_field_model = $this->book_field_model;
				$product_history_model = $this->book_history_model;
				$product_ref_product_category_model = $this->book_ref_product_category_model;
				$product_tag_model = $this->book_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			case '2':
			case 'magazine':
				$product_model = $this->magazine_model;
				$product_copy_model = $this->magazine_copy_model;
				$product_field_model = $this->magazine_field_model;
				$product_history_model = $this->magazine_history_model;
				$product_ref_product_category_model = $this->magazine_ref_product_category_model;
				$product_tag_model = $this->magazine_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			case '3':
			case 'vdo':
				$product_model = $this->vdo_model;
				$product_copy_model = $this->vdo_copy_model;
				$product_field_model = $this->vdo_field_model;
				$product_history_model = $this->vdo_history_model;
				$product_ref_product_category_model = $this->vdo_ref_product_category_model;
				$product_tag_model = $this->vdo_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			case '4':
			case 'others':
				$product_model = $this->others_model;
				$product_copy_model = $this->others_copy_model;
				$product_field_model = $this->others_field_model;
				$product_history_model = $this->others_history_model;
				$product_ref_product_category_model = $this->others_ref_product_category_model;
				$product_tag_model = $this->others_tag_model;
				$model["product_model"] = $product_model;
				$model["product_copy_model"] = $product_copy_model;
				$model["product_field_model"] = $product_field_model;
				$model["product_history_model"] = $product_history_model;
				$model["product_ref_product_category_model"] = $product_ref_product_category_model;
				$model["product_tag_model"] = $product_tag_model;
				return $model;
				break;
			default:
				return "";
				break;
		}
	}
	
	function add_copy_stock_remain($copy_aid="0",$unit=0){
		// if($copy_aid <= 0 || $unit <= 0){
		// 	return "";
		// }
		
		$_sql = "UPDATE ". $this->get_table_name() ." SET in_stock = (in_stock+".$unit.") WHERE aid = '".$copy_aid."'";
		return $this->db->query($_sql);
	}

	function reduce_copy_stock_remain($copy_aid="0",$unit=0){
		// if($copy_aid <= 0 || $unit <= 0){
		// 	return "";
		// }
		
		$_sql = "UPDATE ". $this->get_table_name() ." SET in_stock = (in_stock-".$unit.") WHERE aid = '".$copy_aid."'";
		return $this->db->query($_sql);
	}

	function manage_result($row,$category_result){
		if(is_var_array($row)){
			// print_r($row);
				
			$aid = get_array_value($row,"aid","");
			$product_type_aid = get_array_value($row,"product_type_aid","");

			$model = $this->get_product_model($product_type_aid);
			$model_field_name = get_array_value($model,"product_field_model","");
			$model_copy_name = get_array_value($model,"product_copy_model","");
			$this->db->flush_cache();
			$this->db->_reset_select();

			$CI =& get_instance();
			$CI->load->model($model_field_name, $model_field_name);
			$tmp = array();
			$tmp['aid'] = $aid;
			$field_result = $CI->{$model_field_name}->load_field_by_parent_aid($aid);
			
			$row["biblio_field_result"] = $field_result;

			$title = get_array_value($row,"title","");
			$title_by_cid ="";
			
			$author = get_array_value($row,"author","");
			$author_by_cid ="";

			$ext_source_by_cid ="";
			$welcome_msg_by_cid ="";
			$description_by_cid ="";
			$total_page_by_cid ="";

			if(is_var_array($field_result)){
				foreach($field_result as $item){
					$parent_aid = get_array_value($item,"parent_aid","");
					$product_main_field_aid = get_array_value($item,"product_main_field_aid","");
					$product_main_field_cid = get_array_value($item,"product_main_field_cid","");
					$tag = get_array_value($item,"tag","");
					$ind1_cd = get_array_value($item,"ind1_cd","");
					$ind2_cd = get_array_value($item,"ind2_cd","");
					$subfield_cd = get_array_value($item,"subfield_cd","");
					$field_data = get_array_value($item,"field_data","");

					// echo "product_main_field_cid = $product_main_field_cid, field_data = $field_data <BR>";
					// echo "description_by_cid = $description_by_cid<BR>";
					
					$title_by_cid = "";
					if($product_main_field_cid == 'title'){
						$title_by_cid = $field_data;
					}

					$author_by_cid = "";
					if($product_main_field_cid == 'author'){
						$author_by_cid = $field_data;
					}
					
					$ext_source_by_cid = "";
					if($product_main_field_cid == 'ext-source'){
						$ext_source_by_cid = $field_data;
						$row["ext_source"] = $ext_source_by_cid;
					}
					
					$welcome_msg_by_cid = "";
					if($product_main_field_cid == 'welcome-msg'){
						$welcome_msg_by_cid = $field_data;
						$row["welcome_msg"] = $welcome_msg_by_cid;
					}
					
					$description_by_cid = "";
					if($product_main_field_cid == 'description'){
						$description_by_cid = $field_data;
						$row["description"] = $description_by_cid;
					}
					
					$total_page_by_cid = "";
					if($product_main_field_cid == 'total_page'){
						$total_page_by_cid = $field_data;
						$row["total_page"] = $total_page_by_cid;
					}
					
					$location_by_cid = "";
					if($product_main_field_cid == 'location'){
						$location_by_cid = $field_data;
						$row["location"] = $location_by_cid;
					}
					
					$text = "";
					if($product_main_field_cid == 'no_1'){
						$text = $field_data;
						$row["no_1"] = $text;
					}
					
					$text = "";
					if($product_main_field_cid == 'no_2'){
						$text = $field_data;
						$row["no_2"] = $text;
					}
					
					$text = "";
					if($product_main_field_cid == 'no_3'){
						$text = $field_data;
						$row["no_3"] = $text;
					}
					
				}
			}

			if(is_blank($title)){
					$title = $title_by_cid;
			}
			// echo "title = $title <BR>";
			$row["title"] = $title;
			$row["title_short"] = getShortString($title, CONST_TITLE_SHORT_CHAR);

			if(is_blank($author)){
					$author = $author_by_cid;
			}
			$row["author"] = $author;

			$no_1 = get_array_value($row,"no_1","");
			$no_2 = get_array_value($row,"no_2","");
			$no_3 = get_array_value($row,"no_3","");
			$call_number = "";
			if(!is_blank($no_1)){
				$call_number = trim($no_1);
			}
			if(!is_blank($no_2)){
				$call_number = trim($call_number." ".trim($no_2));
			}
			if(!is_blank($no_3)){
				$call_number = trim($call_number." ".trim($no_3));
			}
			$row["call_number"] = $call_number;

			$product_main_aid = get_array_value($row,"product_main_aid","");
			$product_type_aid = get_array_value($row,"product_type_aid","");
			$product_type_cid = get_array_value($row,"product_type_cid","");
			$product_type_minor_aid = get_array_value($row,"product_type_minor_aid","");
			
			$product_type_main_code = '';
			$product_type_sub_code = '';
			switch($product_type_minor_aid){
				case "1" : 
							$product_type_main_code = "ebook";
							$product_type_sub_code = "ebook-free";
							break;
				case "2" : 
							$product_type_main_code = "ebook";
							$product_type_sub_code = "ebook-sale";
							break;
				case "3" : 
							$product_type_main_code = "ebook";
							$product_type_sub_code = "ebook-license";
							break;
				case "4" : 
							$product_type_main_code = "vdo";
							$product_type_sub_code = "vdo-free";
							break;
				case "5" : 
							$product_type_main_code = "vdo";
							$product_type_sub_code = "vdo-sale";
							break;
				case "6" : 
							$product_type_main_code = "vdo";
							$product_type_sub_code = "vdo-license";
							break;
				case "7" : 
							$product_type_main_code = "book";
							$product_type_sub_code = "book";
							break;
				case "8" : 
							$product_type_main_code = "cd-dvd";
							$product_type_sub_code = "cd-dvd-cd";
							break;
				case "9" : 
							$product_type_main_code = "cd-dvd";
							$product_type_sub_code = "cd-dvd-ct";
							break;
				case "10" : 
							$product_type_main_code = "cd-dvd";
							$product_type_sub_code = "cd-dvd-vc";
							break;
			}
			
			$row["product_type_main_code"] = $product_type_main_code;
			$row["product_type_sub_code"] = $product_type_sub_code;
			
			$upload_path = get_array_value($row,"upload_path","");
			$cover_image_file_type = get_array_value($row,"cover_image_file_type","");
			$class = ($product_type_aid == '3') ? "vdo-" : "";

			// echo "upload_path = $upload_path , cover_image_file_type = $cover_image_file_type<BR />";

			$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-ori'.$cover_image_file_type;
			$image = get_image($image_path,$class."ori");
			$row["cover_image_ori_path"] = $image_path;
			$row["cover_image_ori"] = $image;
			
			$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-thumb'.$cover_image_file_type;
			$image = get_image($image_path,$class."thumb");
			$row["cover_image_thumb_path"] = $image_path;
			$row["cover_image_thumb"] = $image;
			
			$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-cover'.$cover_image_file_type;
			$image = get_image($image_path, $class."detail");
			$row["cover_image_detail_path"] = $image_path;
			$row["cover_image_detail"] = $image;
			
			$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-related'.$cover_image_file_type;
			$image = get_image($image_path,$class."related");
			$row["cover_image_related_path"] = $image_path;
			$row["cover_image_related"] = $image;
			
			$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-small'.$cover_image_file_type;
			$image = get_image($image_path,$class."small");
			$row["cover_image_small_path"] = $image_path;
			$row["cover_image_small"] = $image;
			
			$image_path = $upload_path.'cover_image/'.get_array_value($row,"cid","").'-ori'.$cover_image_file_type;
			$image = get_image($image_path,"","off");
			$row["cover_image_ipad_path"] = $image_path;
			$row["cover_image_ipad"] = $image;
						
			$created_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"created_date",""),"");
			$row["created_date_txt"] = $created_date_txt;
			$updated_date_txt = get_datetime_pattern("dmyhis_EN_SHORT",get_array_value($row,"updated_date",""),"");
			$row["updated_date_txt"] = $updated_date_txt;
			
			$publish_date_txt = get_datetime_pattern("dmy_EN_LONG",get_array_value($row,"publish_date",""),"");
			$row["publish_date_txt"] = $publish_date_txt;
			
			$publish_date_short_txt = get_datetime_pattern("dmy_EN_SHORT",get_array_value($row,"publish_date",""),"");
			$row["publish_date_short_txt"] = $publish_date_short_txt;

			$has_ebook = get_array_value($row,"has_ebook","0");
			if($has_ebook == 1){
				$row["has_ebook_show"] = '<i class="fa fa-check"></i>';
			}else{
				$row["has_ebook_show"] = '<i class="fa fa-ban"></i>';
			}

			$has_paper = get_array_value($row,"has_paper","0");
			if($has_paper == 1){
				$row["has_paper_show"] = '<i class="fa fa-check"></i>';
			}else{
				$row["has_paper_show"] = '<i class="fa fa-ban"></i>';
			}

			$category = get_array_value($row,"category","");
			$category_list = "";
			$txt = "";
			if(!is_blank($category)){
				$arr = preg_split('/,/', $category, -1, PREG_SPLIT_NO_EMPTY);
				if(is_var_array($arr)){	
					foreach($arr as $item){
						if(!is_blank(trim($item))){
							$category_obj = get_array_value($category_result,$item,"");
							$category_name = get_array_value($category_obj,"name","");
							$category_url = get_array_value($category_obj,"url","");
							$product_main_aid= get_array_value($row,"product_main_aid","");
							$product_main_name = get_array_value($row,"product_main_url","");
							$txt .= '<a href="'.site_url('list-'.$product_type_cid.'/category/'.$product_main_name.'/c-'.$category_url).'">'.$category_name.'</a>, ';
							$category_list[] = $category_name;
						}
					}
					$txt = substr(trim($txt), 0, -1);
				}
			}
			$row["category_list"] = $category_list;
			$row["category_link"] = $txt;
			
			$tag = get_array_value($row,"tag","");
			if(!is_blank($tag)){
				$tag = substr($tag,1);
				$row["tag"] = $tag;
			}

			$CI =& get_instance();
			$CI->load->model($model_copy_name, $model_copy_name);
			$tmp = array();
			$tmp['parent_aid'] = $aid;
			if(!exception_about_status()) $tmp["status"] = "1";
			$CI->{$model_copy_name}->set_where($tmp);
			$copy_result = $CI->{$model_copy_name}->load_records(false);
			if(is_var_array($copy_result)){
				$row["copy_list"] = $copy_result;
				$copy_result_show = array();
				foreach ($copy_result as $item) {
					$type_minor = get_array_value($item,"type_minor","0");
					$tmp_key = "t_".$type_minor;
					$type_minor = get_array_value($item,"type_minor","0");
					if (array_key_exists($tmp_key, $copy_result_show)) {
						$copy_result_show[$tmp_key] += 1;
					}else{
						$copy_result_show[$tmp_key] = 1;
					}
				}

				$row["copy_type_minor_show"] = '';
				foreach ($copy_result_show as $key => $value) {
					$type_minor = str_replace("t_", "", $key);
					// echo "type_minor = $type_minor";
					switch ($type_minor) {
						case '1':
							if(!is_blank($row["copy_type_minor_show"])){
								$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
							}
							$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> E-Book';
							break;
						case '2':
							if(!is_blank($row["copy_type_minor_show"])){
								$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
							}
							$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> E-Magazine';
							break;
						case '3':
							if(!is_blank($row["copy_type_minor_show"])){
								$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
							}
							$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> Book';
							break;
						case '4':
							if(!is_blank($row["copy_type_minor_show"])){
								$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
							}
							$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> Magzine';
							break;
						case '5':
							if(!is_blank($row["copy_type_minor_show"])){
								$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
							}
							$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> Online Book';
							break;
						case '6':
							if(!is_blank($row["copy_type_minor_show"])){
								$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
							}
							$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i><i class="fa fa-dot-circle-o"></i> Book+CD';
							break;
						case '7':
							if(!is_blank($row["copy_type_minor_show"])){
								$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
							}
							$row["copy_type_minor_show"] .= '<i class="fa fa-dot-circle-o"></i> Media';
							break;
					}
					if($value > 1){
						$row["copy_type_minor_show"] .= ' ('.$value.')';
					}
				}
			}

			return $row;
		}
	}
	
	function manage_copy_result($row){
		$type = get_array_value($row,"type","0");
		$possession = get_array_value($row,"possession","0");
		$is_license = get_array_value($row,"is_license","0");
		$is_ebook = get_array_value($row,"is_ebook","0");
		$ebook_concurrence = get_array_value($row,"ebook_concurrence","0");
		$digital_price = get_array_value($row,"digital_price","0");
		$digital_point = get_array_value($row,"digital_point","0");
		$paper_price = get_array_value($row,"paper_price","0");
		$paper_point = get_array_value($row,"paper_point","0");
		$in_stock = get_array_value($row,"in_stock","0");
		$rental_period = get_array_value($row,"rental_period","0");
		$rental_fee = get_array_value($row,"rental_fee","0");
		$rental_fee_point = get_array_value($row,"rental_fee_point","0");
		$rental_fine_fee = get_array_value($row,"rental_fine_fee","0");

		$product_type_name = get_array_value($row,"product_type_name","");
		$copy_title = get_array_value($row,"copy_title","");

		$title_label = ($type == "2") ? "Paper" : "Digital";
		if(!is_blank($copy_title)){
			$title_label .= ' : '.$copy_title;
		}

		$shelf_status = get_array_value($row,"shelf_status","");
		switch($shelf_status){
			case "1" : $row["shelf_status_name"] = "On shelf"; break;
			case "2" : $row["shelf_status_name"] = "Borrowed"; break;
			case "3" : $row["shelf_status_name"] = "Damage"; break;
			case "4" : $row["shelf_status_name"] = "Lost"; break;
			default : $row["shelf_status_name"] = "N/A"; break;
		}

		switch($type){
			case "1" : $row["type_name"] = "Digital"; break;
			case "2" : $row["type_name"] = "Paper"; break;
			default : $row["type_name"] = "N/A"; break;
		}

		switch($possession){
			case "1" : 
				$row["possession_name"] = "Buy out";
				if($type == "1"){
					$row["actual_price"] = $digital_price;
					$row["actual_point"] = $digital_point;
				}else if($type == "2"){
					$row["actual_price"] = $paper_price;
					$row["actual_point"] = $paper_point;
				}
				break;
			case "2" : 
				$row["possession_name"] = "Rental";
				$title_label .= ' for rent';
				$row["actual_price"] = $rental_fee;
				$row["actual_point"] = $rental_fee_point;
				break;
			default : 
				$row["possession_name"] = "N/A"; 
				break;
		}

		switch($is_license){
			case "1" : $row["is_license_name"] = "Yes"; break;
			default : $row["is_license_name"] = "No"; break;
		}

		// echo "title_label = $title_label";
		$row["title_label"] = $title_label;
		$upload_path = get_array_value($row,"parent_upload_path","");
		$cover_image_file_type = get_array_value($row,"parent_cover_image_file_type","");

		$image_path = $upload_path.'cover_image/'.get_array_value($row,"parent_cid","").'-thumb'.$cover_image_file_type;
		// echo "image_path = $image_path <BR>";
		$image = get_image($image_path,"thumb");
		$row["cover_image_thumb_path"] = $image_path;
		$row["cover_image_thumb"] = $image;
		
		$image_path = $upload_path.'cover_image/'.get_array_value($row,"parent_cid","").'-cover'.$cover_image_file_type;
		$image = get_image($image_path, "detail");
		$row["cover_image_detail_path"] = $image_path;
		$row["cover_image_detail"] = $image;
		
		$image_path = $upload_path.'cover_image/'.get_array_value($row,"parent_cid","").'-related'.$cover_image_file_type;
		$image = get_image($image_path,"related");
		$row["cover_image_related_path"] = $image_path;
		$row["cover_image_related"] = $image;
		
		$image_path = $upload_path.'cover_image/'.get_array_value($row,"parent_cid","").'-small'.$cover_image_file_type;
		$image = get_image($image_path,"small");
		$row["cover_image_small_path"] = $image_path;
		$row["cover_image_small"] = $image;
		
		$image_path = $upload_path.'cover_image/'.get_array_value($row,"parent_cid","").'-ori'.$cover_image_file_type;
		$image = get_image($image_path,"","off");
		$row["cover_image_ipad_path"] = $image_path;
		$row["cover_image_ipad"] = $image;
		
		$type_minor = get_array_value($row,"type_minor","0");
		$row["copy_type_minor_show"] = '';
		switch ($type_minor) {
			case '1':
				if(!is_blank($row["copy_type_minor_show"])){
					$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
				}
				$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> E-Book';
				break;
			case '2':
				if(!is_blank($row["copy_type_minor_show"])){
					$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
				}
				$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> E-Magazine';
				break;
			case '3':
				if(!is_blank($row["copy_type_minor_show"])){
					$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
				}
				$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> Book';
				break;
			case '4':
				if(!is_blank($row["copy_type_minor_show"])){
					$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
				}
				$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> Magzine';
				break;
			case '5':
				if(!is_blank($row["copy_type_minor_show"])){
					$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
				}
				$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i> Online Book';
				break;
			case '6':
				if(!is_blank($row["copy_type_minor_show"])){
					$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
				}
				$row["copy_type_minor_show"] .= '<i class="fa fa-book"></i><i class="fa fa-dot-circle-o"></i> Book+CD';
				break;
			case '7':
				if(!is_blank($row["copy_type_minor_show"])){
					$row["copy_type_minor_show"] .= '&nbsp;/&nbsp;';
				}
				$row["copy_type_minor_show"] .= '<i class="fa fa-dot-circle-o"></i> Media';
				break;
		}

		return($row);
	}
	
	function manage_field_result($row){
		$input_type = get_array_value($row,"input_type","textbox");
		if($input_type != "textbox" && $input_type != "textarea"){
			$input_type = "textbox";
		}
		
		$product_topic_main_cid = get_array_value($row,"product_topic_main_cid","");
		if(is_number_no_zero($product_topic_main_cid)){
			$input_type = "textbox_topic";
		}
		$row["input_type"] = $input_type;

		$product_main_field_name = get_array_value($row,"product_main_field_name","");
		$name = get_array_value($row,"name","");
		if(is_blank($product_main_field_name)){
			$row["product_main_field_name"] = $name;
		}

		$tag = get_array_value($row,"tag","");
		$field_data = get_array_value($row,"field_data","");
		switch ($tag) {
			case '100':
				$field_data_link = "";
				$data = explode(",", $field_data);
				if(is_var_array($data)){
					foreach($data as $item){
						$item = trim($item);
						if(!is_blank($item)){
							if(!is_blank($field_data_link)){
								$field_data_link .= ' , ';
							}
							$field_data_link .= '<a href="'.site_url('search/'.$item.'?search_in=author').'">'.$item.'</a>';
						}
					}
				}
				$row["field_data_link"] = $field_data_link;
				break;
			
			case '650':
			case '700':
				$field_data_link = "";
				$data = explode(",", $field_data);
				if(is_var_array($data)){
					foreach($data as $item){
						$item = trim($item);
						if(!is_blank($item)){
							if(!is_blank($field_data_link)){
								$field_data_link .= ' , ';
							}
							$field_data_link .= '<a href="'.site_url('search/'.$item.'/option-or').'">'.$item.'</a>';
						}
					}
				}
				$row["field_data_link"] = $field_data_link;
				break;
			
			default:
				$row["field_data_link"] = makeClickableLinks($field_data, true);
				break;
		}
		return($row);
	}
	
	function update_review_point($parent_aid="0"){
		$CI =& get_instance();
		$CI->load->model("review_model", "review");
		$CI->db->select("count(*) as review_total , ROUND(AVG(point)) as review_avg");
		$tmp = array();
		$tmp["parent_aid"] = $parent_aid;
		$tmp["status"] = '1';
		$CI->review->set_where($tmp);
		$CI->db->group_by("parent_aid");
		$result = $CI->review->load_record(false);

		if(is_var_array($result)){
			$review_point = get_array_value($result,"review_avg","0");
			$_sql = "UPDATE ". $this->get_table_name() ." SET review_point = '".$review_point."' WHERE aid = '".$parent_aid."'";
			return $this->db->query($_sql);
		}
	}

}

/* End of file product_init_model.php */
/* Location: ./system/application/model/product_init_model.php */