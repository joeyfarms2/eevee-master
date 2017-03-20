<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Search_front_controller extends Project_init_controller {

	function __construct()
	{
		parent::__construct();
		define("thisFrontTabMenu",'search');
		define("thisFrontSubMenu",'');
		@define("folderName",'home/');
		$this->data["mode"] = "front";
		$this->news_model = "News_model";
				
		$this->search_history_model = "Search_history_model";
		$this->view_all_product_fields_with_detail_model = "View_all_product_fields_with_detail_model";
		$this->view_all_product_tag_with_detail_model = "View_all_product_tag_with_detail_model";

		$this->load->model($this->search_history_model,"search_history");
		$this->data["search_history_popular_result"] = $this->search_history->load_popular(10);

	}
	
	function index($keyword="",$search_option="and",$page_selected=1)
	{
		$search_type = $this->input->get_post('search_type');
		// echo "search_type = $search_type";
		$this->data["search_type"] = $search_type;
		if($search_type == "event"){
			$this->search_event($keyword,$search_option,$page_selected);
		}else if($search_type == "news"){
			$this->search_news($keyword,$search_option,$page_selected);
		}else{
			$this->search_marc($keyword,$search_option,$page_selected);
		}
	}
	
	function search_news($keyword="",$search_option="and",$page_selected=1){
		@define("thisAction","search_news");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/search/search_news_result';
		$this->data["page_title"] = 'SearchNewsResult';
		$this->data["message"] = "";

		if(is_blank($keyword)){
			$this->data["search_clear"] = "clear";			
			$this->load->view($this->default_theme_front.'/tpl_search', $this->data);
			return"";
		}
		
		$page = $this->input->get_post("page_selected");
		if(is_number_no_zero($page)){
			$page_selected = $page;
		}
		// echo "<BR />page = $page , page_selected = $page_selected";
		
		if($search_option != 'or'){
			$search_option = 'and';
		}
		// echo "<BR />search_option = $search_option";
		
		$search_in_product_main = $this->input->get_post("search_in_product_main");
		
		$search_in = $this->input->get_post("search_in");
		$search_in_master = array("all","title","description","posted_by");
		// echo "device = ".$device;
		if(is_blank($search_in) || !in_array($search_in,$search_in_master)){
			$search_in = "all";
		}
		// echo "<BR />search_in = $search_in";

		$sort_by = trim(strtolower($this->input->get_post('sort_by')));
		if($sort_by != "match" && $sort_by != "date_a" && $sort_by != "date_d"  && $sort_by != "name_a" && $sort_by != "name_d" ){
			$sort_by = "match";
		}

		$data_insert = "";
		$keyword = trim(urldecode($keyword));
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
						$tmp["search_in"] = "news";
						$data_insert[] = $tmp;
					}else{
						$keyword_arr[] = $item;						
						$tmp = array();
						$tmp["word"] = $item;
						$tmp["cond"] = "";
						$tmp["search_in"] = "news";
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

		$this->db->flush_cache();
		$this->load->model($this->news_model,'news');
		$this->db->start_cache();
		$this->news->set_where(array("status"=>"1"));

		switch($search_in){
			case "all" :
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->news->set_and_or_like(array("title"=>$keyword_arr, "description"=>$keyword_arr, "user.username"=>$keyword_arr, "user.first_name_th"=>$keyword_arr, "user.last_name_th"=>$keyword_arr, "user.email"=>$keyword_arr));
						}else{
							$this->news->set_or_and_like_group(array("title"=>$keyword_arr, "description"=>$keyword_arr, "user.username"=>$keyword_arr, "user.first_name_th"=>$keyword_arr, "user.last_name_th"=>$keyword_arr, "user.email"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->news->set_or_and_not_like_group(array("title"=>$keyword_not_arr, "description"=>$keyword_not_arr, "user.username"=>$keyword_not_arr, "user.first_name_th"=>$keyword_not_arr, "user.last_name_th"=>$keyword_not_arr, "user.email"=>$keyword_not_arr));
					}
					
					break;
			case "title" :
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->news->set_and_or_like(array("title"=>$keyword_arr));
						}else{
							$this->news->set_or_and_like_group(array("title"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->news->set_or_and_not_like_group(array("title"=>$keyword_not_arr));
					}
					break;
			case "description" :
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->news->set_and_or_like(array("description"=>$keyword_arr));
						}else{
							$this->news->set_or_and_like_group(array("description"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->news->set_or_and_not_like_group(array("description"=>$keyword_not_arr));
					}
					break;
			case "posted_by" :
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->news->set_and_or_like(array("user.username"=>$keyword_arr, "user.first_name_th"=>$keyword_arr, "user.last_name_th"=>$keyword_arr, "user.email"=>$keyword_arr));
						}else{
							$this->news->set_or_and_like_group(array("user.username"=>$keyword_arr, "user.first_name_th"=>$keyword_arr, "user.last_name_th"=>$keyword_arr, "user.email"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->news->set_or_and_not_like_group(array("user.username"=>$keyword_not_arr, "user.first_name_th"=>$keyword_not_arr, "user.last_name_th"=>$keyword_not_arr, "user.email"=>$keyword_not_arr));
					}
					break;
			case "tag" :
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->news->set_and_or_like(array("tag"=>$keyword_arr));
						}else{
							$this->news->set_or_and_like_group(array("tag"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->news->set_or_and_not_like_group(array("tag"=>$keyword_not_arr));
					}
					break;
		}

		$optional["total_record"] = $this->news->load_count_records_by_search(true);
		// echo "<br>sql : ".$this->db->last_query()."<BR>";
		$optional["page_selected"] = $page_selected;
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_FOR_SEARCH;
		$url = 'search/'.$keyword.'/option-'.$search_option;
		// $optional["url"] = $url.'/page-';
		$optional["onclick"] = "search_advance('page_selected')";
		
		$optional = $this->get_pagination_info($optional);
		$this->news->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_FOR_SEARCH));
		// $this->news->set_order_by("weight ASC, publish_date DESC");
		
		if($sort_by == 'match'){
			$this->news->set_order_by("weight ASC, publish_date DESC");
		}else if($sort_by == 'date_d'){
			$this->news->set_order_by("publish_date DESC, weight ASC, publish_date DESC");
		}else if($sort_by == 'date_a'){
			$this->news->set_order_by("publish_date ASC, weight ASC, publish_date DESC");
		}else if($sort_by == 'name_d'){
			$this->news->set_order_by("title DESC, weight ASC, publish_date DESC");
		}else if($sort_by == 'name_a'){
			$this->news->set_order_by("title ASC, weight ASC, publish_date DESC");
		}else{
			$this->news->set_order_by("weight ASC, publish_date DESC");
		}

		$resultList = $this->news->load_records(true);
		$resultList = get_array_value($resultList,"results","");
		$total_in_page = 0;
		if(is_var_array($resultList)){
			$total_in_page = count($resultList);
		}
		$this->data["resultList"] = $resultList;
		$optional["total_in_page"] = $total_in_page;			
		$this->data["optional"] = $optional;			
		
		$this->data["sort_by"] = $sort_by;	
		$this->data["search_in"] = $search_in;			
		$this->data["keyword"] = $keyword;			
		$this->data["search_option"] = $search_option;			
		$this->data["page_selected"] = $page_selected;			
		// echo "<br>sql : ".$this->db->last_query();
		$this->load->view($this->default_theme_front.'/tpl_search', $this->data);
	}
	
	function search_marc($keyword="",$search_option="and",$page_selected=1){
		@define("thisAction","search_marc");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/search/search_result';
		$this->data["page_title"] = 'SearchResult';
		$this->data["message"] = "";

		if(is_blank($keyword)){
			$this->data["search_clear"] = "clear";			
			$this->load->view($this->default_theme_front.'/tpl_search', $this->data);
			return"";
		}
		
		$page = $this->input->get_post("page_selected");
		if(is_number_no_zero($page)){
			$page_selected = $page;
		}
		// echo "<BR />page = $page , page_selected = $page_selected";
		
		if($search_option != 'or'){
			$search_option = 'and';
		}
		// echo "<BR />search_option = $search_option";
		
		$search_in_product_main = $this->input->get_post("search_in_product_main");
		
		$search_in = $this->input->get_post("search_in");
		$search_in_master = array("all","title","author","isbn","keyword","content","call_number","publisher","tag","subject");
		// echo "device = ".$device;
		if(is_blank($search_in) || !in_array($search_in,$search_in_master)){
			$search_in = "all";
		}
		// echo "<BR />search_in = $search_in";

		$sort_by = trim(strtolower($this->input->get_post('sort_by')));
		if($sort_by != "match" && $sort_by != "pop_a" && $sort_by != "pop_d"  && $sort_by != "date_a" && $sort_by != "date_d"  && $sort_by != "name_a" && $sort_by != "name_d" ){
			$sort_by = "match";
		}

		$data_insert = "";
		$keyword = trim(urldecode($keyword));
		//echo "keyword = $keyword";
		$keyword_tmp = $this->convert_keyword_to_array($keyword);
		$keyword_arr = "";
		$keyword_not_arr = "";
		if(is_var_array($keyword_tmp)){
			foreach ($keyword_tmp as $item) {

				$item = trim($item);
				
				//echo "item = $item<br/>";	
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

		$this->db->flush_cache();
		$this->load->model($this->view_all_product_fields_with_detail_model,'product');
		$this->db->start_cache();
		$this->product->set_where(array("status"=>"1"));

		if(is_number_no_zero($search_in_product_main)){
			$this->product->set_where(array("product_main_aid"=>$search_in_product_main));
		}

		switch($search_in){
			case "all" :
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr, "product_tag"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr, "product_tag"=>$keyword_not_arr));
					}
					
					break;
			case "title" :
					$this->product->set_where(array("tag"=>"245"));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
					}
					break;
			case "keyword" :
					$this->product->set_where(array("tag"=>"908"));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
					}
					break;
			case "author" :
					$this->product->set_and_or_like_by_field("tag",array('100','110'));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
					}
					break;
			case "content" :
					$this->product->set_and_or_like_by_field("tag",array('520'));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
					}
					break;
			case "isbn" :
					$this->product->set_and_or_like_by_field("tag",array('020'));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
					}
					break;
			case "publisher" :
					//$this->product->set_where(array("tag"=>"245"));
					$this->product->set_and_or_like_by_field("tag",array('260'));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("publisher_name"=>$keyword_arr,"field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("publisher_name"=>$keyword_arr,"field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("publisher_name"=>$keyword_not_arr,"field_data"=>$keyword_not_arr));
					}
					break;
			case "call_number" :
					$this->product->set_and_or_like_by_field("tag",array('050'));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("field_data"=>$keyword_not_arr));
					}
					break;
			case "tag" :
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("product_tag"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("product_tag"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
							$this->product->set_or_and_not_like_group(array("product_tag"=>$keyword_not_arr));
					}
					break;

			case "subject" :
					$this->product->set_and_or_like_by_field("tag",array('650'));
					if(is_var_array($keyword_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_arr));
						}
					}
					if(is_var_array($keyword_not_arr)){
						if($search_option == "or"){
							$this->product->set_and_or_like(array("field_data"=>$keyword_not_arr));
						}else{
							$this->product->set_or_and_like_group(array("field_data"=>$keyword_not_arr));
						}
							
					}
					break;

		}

		$optional["total_record"] = $this->product->load_count_records_by_search(true);
		//echo "<br>sql : ".$this->db->last_query()."<BR>";
		$optional["page_selected"] = $page_selected;
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_FOR_SEARCH;
		$url = 'search/'.$keyword.'/option-'.$search_option;
		// $optional["url"] = $url.'/page-';
		$optional["onclick"] = "search_advance('page_selected')";
		
		$optional = $this->get_pagination_info($optional);
		$this->product->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_FOR_SEARCH));
		// $this->product->set_order_by("weight ASC, publish_date DESC");
		
		if($sort_by == 'match'){
			$this->product->set_order_by("tag_weight ASC, weight ASC, publish_date DESC");
		}else if($sort_by == 'pop_d'){
			$this->product->set_order_by("total_download DESC, weight ASC, publish_date DESC");
		}else if($sort_by == 'pop_a'){
			$this->product->set_order_by("total_download ASC, weight ASC, publish_date DESC");
		}else if($sort_by == 'date_d'){
			$this->product->set_order_by("publish_date DESC, weight ASC, publish_date DESC");
		}else if($sort_by == 'date_a'){
			$this->product->set_order_by("publish_date ASC, weight ASC, publish_date DESC");
		}else if($sort_by == 'name_d'){
			$this->product->set_order_by("title DESC, weight ASC, publish_date DESC");
		}else if($sort_by == 'name_a'){
			$this->product->set_order_by("title ASC, weight ASC, publish_date DESC");
		}else{
			$this->product->set_order_by("weight ASC, publish_date DESC");
		}

		$resultList = $this->product->load_records(true);
		$this->data["resultList"] = $resultList;
		// echo "<br>sql : ".$this->db->last_query();	
		//print_r($resultList);	
		
		$optional["total_in_page"] = count($this->data["resultList"]);			
		$this->data["optional"] = $optional;			
		
		$this->data["sort_by"] = $sort_by;	
		$this->data["search_in"] = $search_in;			
		$this->data["search_in_product_main"] = $search_in_product_main;			
		$this->data["keyword"] = $keyword;			
		$this->data["search_option"] = $search_option;			
		$this->data["page_selected"] = $page_selected;			
		// echo "<br>sql : ".$this->db->last_query();
		$this->load->view($this->default_theme_front.'/tpl_search', $this->data);
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
			$keyword_arr = explode(" ", $keyword);
			return $keyword_arr;
		}else{
			return "";
		}
	
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */