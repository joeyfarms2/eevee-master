<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class News_front_controller extends Initcontroller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		
		if(CONST_HAS_NEWS != "1"){
			redirect('home');
		}

		define("thisFrontTabMenu",'news');
		define("thisFrontSubMenu",'');
		@define("folderName",'news/');
				
		$this->news_model = 'News_model';
		$this->news_main_model = 'News_main_model';
		$this->news_category_model = 'News_category_model';
		$this->news_gallery_model = 'News_gallery_model';
		$this->news_comment_model = 'News_comment_model';
		$this->news_user_activity_model = 'news_user_activity_model';
		$this->news_comment_user_activity_model = 'news_comment_user_activity_model';
		
		// $this->event_model = 'Event_model';
		// $this->event_main_model = 'Event_main_model';
		// $this->event_category_model = 'Event_category_model';
		// $this->event_gallery_model = 'Event_gallery_model';
		
		$this->view_most_comments_model = 'View_most_comments_model';

		$this->user_model = 'user_model';
		
		$this->load->model($this->news_main_model,"news_main");
		$this->data["master_news_main"] = $this->news_main->load_news_mains();
		
		$this->load->model($this->news_model,"main");
		$this->data["recommendedResult"] = $this->main->load_recommended("1");

		$this->lang->load('news');
		
	}
	
	function index(){
		$this->home();
	}
	
	function home($news_main_url=""){
		@define("thisAction","home");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'News';
		$this->data["view_the_content"] = $this->default_theme_front . '/news/news_home';
		
		$news_main_aid = "";
		$news_main_url = urldecode($news_main_url);
		// echo $news_main_url."<BR>".$category_name."<BR>".$page_selected;

		// if(!is_blank($news_main_url) && $news_main_url != 'all'){
			$this->load->model($this->news_category_model,"news_cat");
			$this->data['news_cat_result'] = $this->news_cat->load_news_categories();
			// echo '<pre>';
			// print_r($this->data['news_cat_result']);
			// echo '</pre>';
			// exit;

			$this->load->model($this->news_model,"news");
			$this->news->set_where(array('news_main_aid' => '1'));
			$rs_news = $this->news->load_records(true);

			$this->data['total_items'] = get_array_value($rs_news, 'num_rows', 0);
			// $this->data['news_result'] = $this->news->load_home('', '1');
			// echo '<pre>'; print_r($rs_news); echo '</pre>'; exit;

			// Load data for right sidebar
			//$this->load->model($this->event_model,"event");
			$this->load->model($this->view_most_comments_model,"most_comments");
			//$tmp_event_list = $this->event->load_home_my_invitations('', '1');
			//$this->data['event_list'] = $tmp_event_list['results'];
			$this->data['news_popular_list'] = $this->news->load_popular('1');
			$this->data['news_recommended_list'] = $this->news->load_recommended('1');
			$this->data['news_talk_of_the_town_list'] = $this->news->load_talk_of_the_town('1');
			$this->data['news_top_commenters'] = $this->most_comments->load_top_commenters(0, 3);
		// }
		
		
		// $this->load->model($this->news_model,"main");
		$this->data["news_highlight_list"] = $this->news->load_highlight($news_main_aid);
		
		$this->load->model($this->news_category_model,"news_category");
		$this->news_category->set_where(array("status"=>"1"));
		$this->news_category->set_order_by(" weight ASC, created_date ASC");
		$news_category_list = $this->news_category->load_records(false);
		$news_home_result = "";
		if(is_var_array($news_category_list)){
			foreach($news_category_list as $item){
				$news_category_aid = get_array_value($item,"aid","0");
				$news_list = $this->news->load_example_news($news_main_aid, $news_category_aid);
				$item["news_list"] = $news_list;
				$news_home_result[] = $item;
			}
		}
		
		$this->data["news_home_result"] = $news_home_result;
		$this->load->view($this->default_theme_front . '/tpl_news', $this->data);
	}
	
	function ajax_load_news_feed() {
		$total_load = 10;
		$html = '';
		$next_page = '';
		$page = $this->input->get_post('page');
		$total_items = $this->input->get_post('total_items');
		$category_aid = $this->input->get_post('category_aid');

		$this->load->model($this->news_model,"news");
		$arr_category_aid = "";
		if (!empty($category_aid) && $category_aid != "all") {
			$arr_category_aid = array(','.$category_aid.',');
		}

		if ($page == '1' || is_blank($total_items)) {
			$total_items = 0;
			$tmp_rs = $this->news->load_home($arr_category_aid, '1');
			if (isset($tmp_rs['num_rows'])) {
				$total_items = $tmp_rs['num_rows'];
			}
		}
		$tmp_rs = $this->news->load_home($arr_category_aid, '1' , (($page-1)*$total_load), $total_load);
		$this->data['news_result'] = get_array_value($tmp_rs, 'results', '');

		if ($total_items > ($page * $total_load)) {
			$next_page = $page+1;
		}
		echo json_encode( array(
				'status' => 'success',
				'html' => $this->load->view($this->default_theme_front . '/news/include_news_feed', $this->data, TRUE),
				'total_items' => $total_items,
				'next_page' => $next_page
			)
		);
	}
	
	function status($type="")	{
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
			case md5('cat-not-found') : 
				$this->data["message"] = set_message_error('ไม่พบหมวดหมู่เรียกดู');
				$this->data["js_code"] = '';
				break;
			case md5('news-not-found') : 
				$this->data["message"] = set_message_error('ไม่พบข่าวที่เรียกดู');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->home();
	}

	function category_main_page($news_main_url="",$page_selected="")
	{
		$this->category($news_main_url,"","",$page_selected);
	}
	
	function category_main_sort($news_main_url="",$sort_by="")
	{
		$this->category($news_main_url,"",$sort_by,"");
	}
	
	function category_main_sort_page($news_main_url="",$sort_by="",$page_selected="")
	{
		$this->category($news_main_url,"",$sort_by,$page_selected);
	}
	
	function category_all_page($news_main_url="",$category_name="",$page_selected="")
	{
		$this->category($news_main_url,$category_name,"",$page_selected);
	}
	
	function category($news_main_url="",$category_name="",$sort_by="",$page_selected="")
	{
		@define("thisAction","home");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["view_the_content"] = $this->default_theme_front . '/news/news_list';
		
		$sort_by = trim(strtolower($sort_by));
		if($sort_by != "pop_a" && $sort_by != "pop_d"  && $sort_by != "date_a" && $sort_by != "date_d"  && $sort_by != "name_a" && $sort_by != "name_d" ){
			$sort_by = "date_d";
		}
		// echo "news_main_url = $news_main_url , category_name = $category_name , sort_by = $sort_by , page_selected = $page_selected";
		if(is_blank($category_name) || $category_name == '0' ||$category_name == 'all' ){
			$category_name = "";
		}
		
		$optional = array();
		
		$news_main_aid = "";
		$category_name = urldecode($category_name);
		$news_main_url = urldecode($news_main_url);
		// echo $news_main_url."<BR>".$category_name."<BR>".$page_selected;
		if(!is_blank($news_main_url)){
			$this->load->model($this->news_main_model,"news_main");
			$this->news_main->set_where(array("url"=>$news_main_url,"status"=>"1"));
			$result = $this->news_main->load_record(false);
			if(is_var_array($result)){
				$news_main_aid = get_array_value($result,"aid","");
				$news_main_name = get_array_value($result,"name","");
				$news_main_url = get_array_value($result,"url","");
				$this->data["news_main_result"] = $result;
				$this->data["this_news_main_aid"] = $news_main_aid;
				$this->data["this_news_main_name"] = $news_main_name;
				$this->data["this_news_main_url"] = $news_main_url;
			}
		}
		
		
		$category_aid = "";
		$category_name = urldecode($category_name);
		$this->data["this_category_name"] = $category_name;
		if(!is_blank($category_name)){
			$this->load->model($this->news_category_model,"category");
			$this->category->set_where(array("name"=>$category_name,"status"=>"1"));
			$result = $this->category->load_record(false);
			if(is_var_array($result)){
				$category_aid = get_array_value($result,"aid","");
				$this->data["news_category_result"] = $result;
			}
			if(is_blank($category_aid)){
				$this->status(md5('cat-not-found'));
				return"";
			}
		}
		
		$this->db->flush_cache();
		$this->load->model($this->news_model,"main");
		$this->db->start_cache();
		
		if(!is_blank($news_main_aid)){
			$this->main->set_where(array("news_main_aid"=>$news_main_aid));
		}
		if(!is_blank($category_aid)){
			$this->main->set_wow(array("category"=>",".$category_aid.","),"both");
		}
		if(!exception_about_status()) $this->main->set_where(array("status"=>'1'));
		$optional["total_record"] = $this->main->count_records();
		$optional["page_selected"] = $page_selected;
		$optional["record_per_page"] = CONST_DEFAULT_RECORD_PER_EVENT;
		$url = 'news/category';
		$url_for_sort = 'category';
		if(!is_blank($news_main_url)){
			$url .= '/'.$news_main_url;
			$url_for_sort .= '/'.$news_main_url;
		}
		if(!is_blank($category_name)){
			$url .= '/c-'.$category_name;
			$url_for_sort .= '/c-'.$category_name;
		}
		if(!is_blank($sort_by)){
			$url .= '/sort-'.$sort_by;
		}
		$optional["url"] = $url.'/page-';
		
		$optional = $this->get_pagination_info($optional);
		
		$this->main->set_limit(get_array_value($optional,"start_record","0"),get_array_value($optional,"search_record_per_page",CONST_DEFAULT_RECORD_PER_EVENT));
		if($sort_by == 'pop_d'){
			$this->main->set_order_by("total_download DESC");
		}else if($sort_by == 'pop_a'){
			$this->main->set_order_by("total_download ASC");
		}else if($sort_by == 'date_d'){
			$this->main->set_order_by("created_date DESC");
		}else if($sort_by == 'date_a'){
			$this->main->set_order_by("created_date ASC");
		}else if($sort_by == 'name_d'){
			$this->main->set_order_by("main.name DESC");
		}else if($sort_by == 'name_a'){
			$this->main->set_order_by("main.name ASC");
		}else{
			$this->main->set_order_by("weight ASC, created_date DESC");
		}
		$this->data["resultList"] = $this->main->load_records(true);			
		$this->data["sort_by"] = $sort_by;			
		$this->data["url_for_sort"] = $url_for_sort;			
		$optional["total_in_page"] = count($this->data["resultList"]);			
		$this->data["optional"] = $optional;			
		// echo "<br>sql : ".$this->db->last_query();
		// exit(0);
		$this->db->flush_cache();		
		$this->load->view($this->default_theme_front . '/tpl_news', $this->data);
	}
	
	function detail($aid){
		@define("thisAction","detail");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = 'NewsDetail';
		$this->data["view_the_content"] = $this->default_theme_front . '/news/news_detail';

		$this->load->model($this->news_category_model,"news_cat");
		$this->data['news_cat_result'] = $this->news_cat->load_news_categories();
				
		$this->db->flush_cache();
		$this->load->model($this->news_model,"news");
		$result = $this->news->increase_total_view($aid);	
		// echo $aid; exit;
		$this->db->flush_cache();
		$this->news->set_where(array("aid"=>$aid));
		if(!exception_about_status()) $this->news->set_where(array("status"=>'1'));
		$item_result = $this->news->load_record(true);
		
		if(is_var_array($item_result)) {
			$this->data["item_detail"] = $item_result;
			$news_aid = get_array_value($item_result, "aid", "");
			$news_main_aid = get_array_value($item_result, "news_main_aid", "");
			if(!is_blank($news_main_aid)){
				$this->load->model($this->news_main_model,"news_main");
				$this->news_main->set_where(array("aid"=>$news_main_aid,"status"=>"1"));
				$result = $this->news_main->load_record(true);
				if(is_var_array($result)){
					$news_main_aid = get_array_value($result,"aid","");
					$news_main_name = get_array_value($result,"name","");
					$news_main_url = get_array_value($result,"url","");
					$this->data["news_main_result"] = $result;
					$this->data["this_news_main_aid"] = $news_main_aid;
					$this->data["this_news_main_name"] = $news_main_name;
					$this->data["this_news_main_url"] = $news_main_url;
				}
			}
			
			$this->load->model($this->news_gallery_model,"news_gallery");
			$tmp = array();
			$tmp["news_aid"] = $news_aid;
			$tmp["status"] = "1";
			$this->news_gallery->set_where($tmp);
			$this->news_gallery->set_order_by("weight ASC, created_date ASC");
			$this->data["news_gallery_list"] = $this->news_gallery->load_records(true);
			// echo "<br>sql : ".$this->db->last_query(); exit;

			// Load data for right sidebar
			//$this->load->model($this->event_model,"event");
			$this->load->model($this->view_most_comments_model,"most_comments");
			//$tmp_event_list = $this->event->load_home_my_invitations('', '1');
			//$this->data['event_list'] = $tmp_event_list['results'];
			$this->data['news_popular_list'] = $this->news->load_popular('1');
			$this->data['news_recommended_list'] = $this->news->load_recommended('1');
			$this->data['news_talk_of_the_town_list'] = $this->news->load_talk_of_the_town('1');
			$this->data['news_top_commenters'] = $this->most_comments->load_top_commenters(0, 3);
			
			$this->load->view($this->default_theme_front.'/tpl_news', $this->data);
		}else{
			redirect('news/status/'.md5('news-not-found'));
		}
	}

	private function load_panel_comment($parent_news_aid="", $limit=3, $order_by="DESC") {
		$arr_return = array( 'status' => 'error', 'msg' => '' );
		if (is_blank($parent_news_aid)) {
			$arr_return['msg'] = 'Incomplete input data';
			echo json_encode($arr_return);
			return;
		}
		$this->load->model($this->news_comment_model,'comment');
		$this->comment->set_where(array('parent_news_aid' => $parent_news_aid, 'parent_comment_aid' => 0));
		if(!exception_about_status()) $this->comment->set_where(array("status"=>'1'));
		$this->comment->set_order_by(array('created_date' => $order_by));
		$rs_comments = $this->comment->load_records(true);
		
		$html ='';
		if (!empty($limit) && $limit > 0) { // If limit = 0; means load all comments
			if ($rs_comments['num_rows'] > $limit) {
				$more_val = get_array_value($rs_comments, 'num_rows') - $limit;
				$html .= '
					<div class="panel-view-more-comments">
			    		<i class="fa fa-comment-o"></i> <a href="javascript:void(0);" class="view-more-comments">View '.$more_val.' more comment'.($more_val > 1 ? 's' : '').'</a>
			    	</div>';
		   }
	   }

	   if (is_var_array($rs_comments['results'])) {
			$rs_comments['results'] = @array_reverse($rs_comments['results']);
			if (!empty($limit) && $limit > 0) { // If limit = 0; means load all comments
				$rs_comments['results'] = array_slice($rs_comments['results'], -$limit, $limit);
			}
		   
		   $html .= '<div class="panel-comments">';
			foreach ($rs_comments['results'] as $item) {
				$html .= '
					<div class="media box-comment'.($item['status'] == '1' ? '' : ' is-hidden').'" data-comment-aid="'.$item['aid'].'">
				      <a href="#" class="pull-left">
				        '.get_array_value($item, "avatar_tiny", "").'
				      </a>
				      <div class="media-body">
				        <div class="media-heading">
				        	<div class="line-comment">
				        		<strong>'.$item['full_name_th'].'</strong>
				        		'.makeClickableLinks(nl2br(get_array_value($item, 'comment', ''))).'
				        	</div>
				        	<div class="line-datetime clearfix">
				        		'.get_pretty_date(get_array_value($item, 'created_date', '')).'
				        		 · 
				        		<a href="javascript:void(0);" class="action-icon-wow-comment mrs '.(get_array_value($item, 'is_wowed_by_me', false) == true ? 'wowed' : '').'">'.(get_array_value($item, 'is_wowed_by_me', false) == true ? 'Unwow' : 'Wow!').'</a>';

        			
        				// Thumbs-up icon with total wows
        				$html .= '
				        		<div class="panel-comment-total-wow inline-block'.($item['total_wow'] > 0 ? '' : ' hidden').'">
				        			<a href="javascript:void(0);" class="mrs who-wow-this-comment" data-toggle="tooltip" data-original-title=""><i class="fa fa-thumbs-o-up"></i> <span class="total-wow-comment">'.$item['total_wow'].'</span></a>
				        		</div>';
        			
				   
				   // Delete link. If this is my comment, show delete comment link
				   if (is_owner_admin_or_higher() || ($item['created_by'] == getUserLoginAid($this->user_login_info))) {
				   	$html .= '<span class="panel-delete-comment hidden"> · <a href="javascript:void(0);" class="text-danger">Delete</a></span>';
					}
				   
				   // Hide/Unhide link for admin
				   if (is_owner_admin_or_higher()) {
				   	$html .= '<span class="panel-hide-comment hidden"> · <a href="javascript:void(0);" class="text-muted" data-status="'.$item['status'].'">'.($item['status'] == '1' ? 'Hide' : 'Unhide').'</a></span>';
					}
				   
				   $html .= '
				        	</div>
				        </div>
				      </div>
			    	</div>';
			}
		}
		else {
			$html .= '<div class="pam">No comment for this article.</div>';
		}
		$html .= '</div>';
		$arr_return = array( 
				'status' => 'success', 
				'html_panel_comment' => $html 
			);
		return $arr_return;
	}

	private function load_panel_actions($news_aid) {
		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_user_activity_model,'user_activity');

		$has_activity = $this->user_activity->has_activity($news_aid);
		$total = $this->news->get_total_activity($news_aid);

		$html = '<ul class="row">';
		// Wow!
		if ($has_activity['has_wowed'] == true) {
			$html .= '<li class="col-xs-3 action-btn has-clicked">';
		}
		else {
			$html .= '<li class="col-xs-3 action-btn">';
		}
		$html .= '<a href="javascript:void(0);" class="action-icon-wow"><span class="hidden-xs">Wow!</span><span class="visible-xs">&nbsp;</span></a>';
		$html .= ' <span class="badge who-wow" data-toggle="tooltip" data-original-title="">'.$total['total_wow'].'</span>';
		$html .= '</li>';

		// Cheer!
		
		if ($has_activity['has_cheered'] == true) {
			$html .= '<li class="col-xs-3 action-btn has-clicked">';
		}
		else {
			$html .= '<li class="col-xs-3 action-btn">';
		}
		$html .= '<a href="javascript:void(0);" class="action-icon-cheer"><span class="hidden-xs">Cheer!</span><span class="visible-xs">&nbsp;</span></a>';
		$html .= ' <span class="badge who-cheer" data-toggle="tooltip" data-original-title=""">'.$total['total_cheer'].'</span>';
		$html .= '</li>';
		

		// Thanks
		if ($has_activity['has_thanked'] == true) {
			$html .= '<li class="col-xs-3 action-btn has-clicked">';
		}
		else {
			$html .= '<li class="col-xs-3 action-btn">';
		}
		$html .= '<a href="javascript:void(0);" class="action-icon-thanks"><span class="hidden-xs">Thanks</span><span class="visible-xs">&nbsp;</span></a>';
		$html .= ' <span class="badge who-thanks" data-toggle="tooltip" data-original-title=""">'.$total['total_thanks'].'</span>';
		$html .= '</li>';

		// Comment
		$html .= '<li class="col-xs-3 action-btn last">';
		$html .= '<a href="javascript:void(0);" class="action-icon-comment"><span class="hidden-xs">Comment</span><span class="visible-xs">&nbsp;</span></a>';
		$html .= ' <span class="badge who-comment" data-toggle="tooltip" data-original-title=""">'.$total['total_comment'].'</span>';
		$html .= '</li>';

		$html .= '</ul>';

		$arr_return = array( 
				'status' => 'success', 
				'html_panel_actions' => $html
			);
		return $arr_return;
	}
	
	private function load_panel_activity_wow_comment($comment_aid) {
		$this->load->model($this->news_comment_model,'comment');
		$this->load->model($this->news_comment_user_activity_model,'user_comment_activity');

		if ( $this->user_comment_activity->has_wowed($comment_aid) ) $has_wowed = true;
		else $has_wowed = false;

		$total_wow = $this->comment->get_total_wow($comment_aid);
		$total_wow_exclude_me = $total_wow - 1;
		$html = '';
		if ($total_wow > 0) {
			$html .= '<a href="javascript:void(0);" class="mrs who-wow-this-comment" data-toggle="tooltip" data-original-title=""><i class="fa fa-thumbs-o-up"></i> <span class="total-wow-comment">'.$total_wow.'</span></a>';
		}

		$arr_return = array( 
				'status' => 'success', 
				'has_wowed' => $has_wowed, 
				'total_wow' => $total_wow, 
				'html_panel_comment_total_wow' => $html, 
				'new_txt_action' => ($has_wowed == true ? 'Unwow' : 'Wow!')
			);
		return $arr_return;
	}
	
	function ajax_load_panel_comment_and_activity() {
		$news_aid = $this->input->get_post('parent_news_aid');
		$limit = $this->input->get_post('limit');
		$order_by = $this->input->get_post('order_by');

		$arr_return = array( 'status' => 'error', 'msg' => '' );
		if (is_blank($news_aid)) {
			$arr_return['msg'] = 'Incomplete input data';
			echo json_encode($arr_return);
			return;
		}
		if (is_blank($limit)) $limit = 3;
		if (is_blank($order_by)) $order_by = "DESC";
		$panel_actions = $this->load_panel_actions($news_aid);
		$panel_comment = $this->load_panel_comment($news_aid, $limit, $order_by);
		$arr_return =  $panel_actions;
		$arr_return['html_panel_comment'] = get_array_value($panel_comment, 'html_panel_comment', '');
		echo json_encode( $arr_return );
	}

	function ajax_load_view_all_comments() {
		$news_aid = $this->input->get_post('news_aid');
		$arr_return = array( 'status' => 'error', 'msg' => '' );
		if (is_blank($news_aid)) {
			$arr_return['msg'] = 'Incomplete input data';
			echo json_encode($arr_return);
			return;
		}
		$arr_return = $this->load_panel_comment($news_aid, 0);
		echo json_encode( $arr_return );
	}

	function ajax_add_comment() {
		$comment = trim($this->input->get_post('comment'));
		$parent_news_aid = trim($this->input->get_post('parent_news_aid'));
		$parent_comment_aid = trim($this->input->get_post('parent_comment_aid'));
		$has_view_all_link = trim($this->input->get_post('has_view_all_link'));
		$limit = 3;
		if (empty($has_view_all_link)) $limit = 0;

		$arr_return = array( 'status' => 'error', 'msg' => '' );
		if (is_blank($parent_news_aid) || is_blank($comment)) {
			$arr_return['msg'] = 'Incomplete input data';
			echo json_encode($arr_return);
			return;
		}
		if (is_blank($parent_comment_aid)) $parent_comment_aid = 0;

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_comment_model,'comment');
		$this->comment->insert_record(
			array(
					'comment' => strip_tags($comment, '<br>'),
					'parent_news_aid' => $parent_news_aid,
					'parent_comment_aid' => $parent_comment_aid
				)
			);

		// Update total comment
		$this->news->update_total_comment($parent_news_aid);

		$panel_actions = $this->load_panel_actions($parent_news_aid);
		$panel_comment = $this->load_panel_comment($parent_news_aid, $limit);
		$arr_return =  $panel_actions;
		$arr_return['html_panel_comment'] = get_array_value($panel_comment, 'html_panel_comment', '');
		echo json_encode( $arr_return );
		// echo json_encode($this->load_panel_comment($parent_news_aid, $limit));
		return;
	}

	function ajax_delete_comment() {
		$comment_aid = trim($this->input->get_post('comment_aid'));
		$parent_news_aid = trim($this->input->get_post('parent_news_aid'));
		$has_view_all_link = trim($this->input->get_post('has_view_all_link'));
		$limit = 3;
		if (empty($has_view_all_link)) $limit = 0;

		$arr_return = array( 'status' => 'error', 'msg' => '' );
		if (is_blank($comment_aid)) {
			$arr_return['msg'] = 'Incomplete input data';
			echo json_encode($arr_return);
			return;
		}

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_comment_model,'comment');
		$this->load->model($this->news_comment_user_activity_model,'user_comment_activity');
		// Delete this comment
		$this->comment->set_where(array('aid' => $comment_aid));
		$this->comment->delete_records();
		// Delete all wows of this comment
		$this->user_comment_activity->set_where(array('news_comment_aid' => $comment_aid));
		$this->user_comment_activity->delete_records();

		// Update total comment
		$this->news->update_total_comment($parent_news_aid);

		$panel_actions = $this->load_panel_actions($parent_news_aid);
		$panel_comment = $this->load_panel_comment($parent_news_aid, $limit);
		$arr_return =  $panel_actions;
		$arr_return['html_panel_comment'] = get_array_value($panel_comment, 'html_panel_comment', '');
		echo json_encode( $arr_return );
		// echo json_encode($this->load_panel_comment($parent_news_aid, $limit));
		return;
	}

	function ajax_hide_comment() {
		$comment_aid = trim($this->input->get_post('comment_aid'));
		$parent_news_aid = trim($this->input->get_post('parent_news_aid'));
		$status = trim($this->input->get_post('status'));
		$has_view_all_link = trim($this->input->get_post('has_view_all_link'));
		$limit = 3;
		if (empty($has_view_all_link)) $limit = 0;

		$arr_return = array( 'status' => 'error', 'msg' => '' );
		if (is_blank($comment_aid)) {
			$arr_return['msg'] = 'Incomplete input data';
			echo json_encode($arr_return);
			return;
		}

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_comment_model,'comment');
		// Delete this comment
		$this->comment->set_where(array('aid' => $comment_aid));
		if ($status == '1') {
			$this->comment->update_record(array('status' => '0'));
		}
		else {
			$this->comment->update_record(array('status' => '1'));
		}

		// Update total comment
		$this->news->update_total_comment($parent_news_aid);

		$panel_actions = $this->load_panel_actions($parent_news_aid);
		$panel_comment = $this->load_panel_comment($parent_news_aid, $limit);
		$arr_return =  $panel_actions;
		$arr_return['html_panel_comment'] = get_array_value($panel_comment, 'html_panel_comment', '');
		echo json_encode( $arr_return );
		// echo json_encode($this->load_panel_comment($parent_news_aid, $limit));
		return;
	}

	function ajax_wow() {
		$news_aid = $this->input->get_post('news_aid');
		$status = $this->input->get_post('status');

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->load->model($this->user_model,"user");
		
		if ($status == '1') {
			$this->user_activity->do_wow($news_aid);
			if (is_login()) {
				$this->user->add_point_remain(getUserLoginAid($this->user_login_info), 1);
			}
		}
		else {
			$this->user_activity->do_unwow($news_aid);
			if (is_login()) {
				$this->user->reduce_point_remain(getUserLoginAid($this->user_login_info), 1);
			}
		}
		$this->news->update_all_total($news_aid);
		echo json_encode($this->load_panel_actions($news_aid));
		return;
	}

	function ajax_cheer() {
		$news_aid = $this->input->get_post('news_aid');
		$status = $this->input->get_post('status');

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_user_activity_model,'user_activity');
		
		if ($status == '1') {
			$this->user_activity->do_cheer($news_aid);
		}
		else {
			$this->user_activity->do_uncheer($news_aid);
		}
		$this->news->update_all_total($news_aid);
		echo json_encode($this->load_panel_actions($news_aid));
		return;
	}

	function ajax_thanks() {
		$news_aid = $this->input->get_post('news_aid');
		$status = $this->input->get_post('status');

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_user_activity_model,'user_activity');
		
		if ($status == '1') {
			$this->user_activity->do_thanks($news_aid);
		}
		else {
			$this->user_activity->do_unthanks($news_aid);
		}
		$this->news->update_all_total($news_aid);
		echo json_encode($this->load_panel_actions($news_aid));
		return;
	}

	function ajax_wow_comment() {
		$comment_aid = $this->input->get_post('comment_aid');
		$status = $this->input->get_post('status');

		$this->load->model($this->news_comment_model,'comment');
		$this->load->model($this->news_comment_user_activity_model,'user_comment_activity');
		
		if ($status == '1') {
			$this->user_comment_activity->do_wow($comment_aid);
		}
		else {
			$this->user_comment_activity->do_unwow($comment_aid);
		}
		$this->comment->update_total_wow($comment_aid);
		echo json_encode($this->load_panel_activity_wow_comment($comment_aid));
		return;
	}

	function ajax_load_who_wow() {
		$news_aid = $this->input->get_post('news_aid');
		$include_me = $this->input->get_post('include_me');
		$arr_return = array( 'status' => 'error', 'msg' => '' );

		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->user_activity->set_where(array('news_aid' => $news_aid, 'status_wow' => '1'));
		if ($include_me == '1') {
			$this->user_activity->set_where_not_equal(array('user_aid' => getUserLoginAid($this->user_login_info)));
		}
		$this->user_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$tmp_rs = $this->user_activity->load_records(true);

		if ($tmp_rs['num_rows'] > 0 || $include_me == '1') {
			$html = '<div class="text-left">';
			$html .= $tmp_rs['html'];
			if ($include_me == '1') {
				$html .= (empty($tmp_rs['html']) ? '<ul><li>You</li></ul>' : '<li>You</li></ul>');
			}
			$html .= '</div>';
			$arr_return = array( 'status' => 'success', 'html' => $html );
		}
		echo json_encode($arr_return);
		return;
	}

	function ajax_load_who_cheer() {
		$news_aid = $this->input->get_post('news_aid');
		$include_me = $this->input->get_post('include_me');
		$arr_return = array( 'status' => 'error', 'msg' => '' );

		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->user_activity->set_where(array('news_aid' => $news_aid, 'status_cheer' => '1'));
		if ($include_me == '1') {
			$this->user_activity->set_where_not_equal(array('user_aid' => getUserLoginAid($this->user_login_info)));
		}
		$this->user_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$tmp_rs = $this->user_activity->load_records(true);

		if ($tmp_rs['num_rows'] > 0 || $include_me == '1') {
			$html = '<div class="text-left">';
			$html .= $tmp_rs['html'];
			if ($include_me == '1') {
				$html .= (empty($tmp_rs['html']) ? '<ul><li>You</li></ul>' : '<li>You</li></ul>');
			}
			$html .= '</div>';
			$arr_return = array( 'status' => 'success', 'html' => $html );
		}
		echo json_encode($arr_return);
		return;
	}

	function ajax_load_who_thanks() {
		$news_aid = $this->input->get_post('news_aid');
		$include_me = $this->input->get_post('include_me');
		$arr_return = array( 'status' => 'error', 'msg' => '' );

		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->user_activity->set_where(array('news_aid' => $news_aid, 'status_thanks' => '1'));
		if ($include_me == '1') {
			$this->user_activity->set_where_not_equal(array('user_aid' => getUserLoginAid($this->user_login_info)));
		}
		$this->user_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$tmp_rs = $this->user_activity->load_records(true);

		if ($tmp_rs['num_rows'] > 0 || $include_me == '1') {
			$html = '<div class="text-left">';
			$html .= $tmp_rs['html'];
			if ($include_me == '1') {
				$html .= (empty($tmp_rs['html']) ? '<ul><li>You</li></ul>' : '<li>You</li></ul>');
			}
			$html .= '</div>';
			$arr_return = array( 'status' => 'success', 'html' => $html );
		}
		echo json_encode($arr_return);
		return;
	}

	function ajax_load_who_comment() {
		$news_aid = $this->input->get_post('news_aid');
		$include_me = $this->input->get_post('include_me');
		$arr_return = array( 'status' => 'error', 'msg' => '' );

		$this->load->model($this->news_comment_model,'news_comment');
		$has_commented = $this->news_comment->has_commented($news_aid);
		$tmp_rs = $this->news_comment->get_who_comment($news_aid);

		if ($tmp_rs['num_rows'] > 0 || $has_commented == true) {
			$html = '<div class="text-left">';
			$html .= $tmp_rs['html'];
			if ($has_commented == true) {
				$html .= (empty($tmp_rs['html']) ? '<ul><li>You</li></ul>' : '<li>You</li></ul>');
			}
			$html .= '</div>';
			$arr_return = array( 'status' => 'success', 'html' => $html );
		}
		echo json_encode($arr_return);
		return;
	}

	function ajax_load_who_wow_this_comment() {
		$news_comment_aid = $this->input->get_post('comment_aid');
		$arr_return = array( 'status' => 'error', 'msg' => '' );

		$this->load->model($this->news_comment_user_activity_model,'user_comment_activity');
		$this->user_comment_activity->set_where(array('news_comment_aid' => $news_comment_aid, 'status_wow' => '1'));
		$this->user_comment_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$tmp_rs = $this->user_comment_activity->load_records(true);

		if ($tmp_rs['num_rows'] > 0) {
			$arr_return = array( 'status' => 'success', 'html' => '<div class="text-left">'.$tmp_rs['html'].'</div>' );
		}
		echo json_encode($arr_return);
		return;
	}


	function isNewsCodeExits($cid){
		$this->load->model($this->news_model,"news");
		$this->news->set_where(array("cid"=>$cid));
		$total = $this->news->count_records();
		if($total> 0){
			return true;
		}else{
			return false;
		}
	}
	
	function _save_form($is_ajax=false){
		@define("thisAction",'_save_form');
		$command = $this->input->get_post('command');
		$aid = "";
		// if($command == "_update"){
		// 	$this->data["header_title"] = TXT_UPDATE_TITLE;
		// }else{
		// 	$this->data["header_title"] = TXT_INSERT_TITLE;
		// }
		
		$this->load->model($this->news_model,'`');
		$this->load->model($this->news_gallery_model,'news_gallery');
		
		$name = trim($this->input->get_post('title'));
		$data['ref_link2_url'] = trim($this->input->get_post('ref_link2_url'));
		$data['ref_link2_image_url'] = trim($this->input->get_post('ref_link2_image_url'));
		$data['ref_link2_title'] = trim($this->input->get_post('ref_link2_title'));
		$data['ref_link2_desc'] = trim($this->input->get_post('ref_link2_desc'));

		$user_owner_aid = $this->get_user_owner_aid_by_input();
		$data["user_owner_aid"] = $user_owner_aid;
		$data["title"] = trim(strip_tags($this->input->get_post('title')));
		$data["is_home"] = '1';
		// $data["is_highlight"] = $this->input->get_post('is_highlight');
		// $data["is_recommended"] = $this->input->get_post('is_recommended');
		$data["description"] = $this->input->get_post('description');
		$data["ref_link"] = trim(strip_tags($this->input->get_post('ref_link')));
		
		// $data["posted_by"] = $this->input->get_post('posted_by');
		$data["posted_by"] = getUserLoginAid($this->user_login_info);
		
		// $data["posted_email"] = $this->input->get_post('posted_email');
		// $data["posted_ref"] = $this->input->get_post('posted_ref');
		$data["news_main_aid"] = '1';
		$data["status"] = '1';

		if (!empty($data['description'])) {
			$data['description'] = preg_replace('/(\.\.\/)*uploads\/'.CONST_CODENAME.'\/userfiles/i', FCK_UPLOAD_PATH, $data['description']);
		}

		$data["draft_title"] = $data["title"];
		$data["draft_description"] = $data["description"];
		
		$category_list = "";
		$category = $this->input->get_post('category');
		if(is_var_array($category)){
			$category_list = ",";
			foreach($category as $item){
				$category_list .= $item.',';
			}
		}
		$data["category"] = $category_list;
		
		$publish_date = get_db_now();
		$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"");
		$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$publish_date,"").'/'.get_datetime_pattern("m",$publish_date,"");
		create_directories($upload_base_path);
		
		$cid = "";
		$return_status = 'success';
		if ($command == "_update") {
			$aid = $this->input->get_post('news_aid');
			$data["aid"] = $aid;
			$this->main->set_where(array("aid"=>$data["aid"]));
			$itemResult = $this->main->load_record(false);
			if (!is_var_array($itemResult)) {
				$return_status = 'error';
				$this->log_error('Frontend : News', 'News aid = '.$aid.' not found.');
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
			}
			else {
				$cid = trim(get_array_value($itemResult,"cid",""));
				$path = './'.trim(get_array_value($itemResult,"upload_path",""));
				if($path != './' && $path != $upload_base_path.'/'.$cid.'/'){
					// echo "Change Path";
					if(is_dir($path)){
						echo $path." is found .. Start move";
						rename($path,$upload_base_path.'/'.$cid);
					}else{
						// echo $path." not found";
					}
				}else{
					// echo "Do Not Change Path";
				}
			}
		}
		
		if ($return_status == 'success' && is_blank($cid)) {
			do{
				$cid = trim(random_string('alnum', 12));
			}while( $this->isNewsCodeExits($cid) );
		}
		$data["cid"] = trim($cid);
		
		
		if( $return_status == 'success' && !is_blank(get_array_value($_FILES,"cover_image","")) && !is_blank(get_array_value($_FILES["cover_image"],"name","")) ){
			//Start upload file
			$upload_path = $upload_base_path.'/'.$cid;
			$image_name = $_FILES["cover_image"]["name"];
			$file_type = substr(strrchr($image_name, "."), 0);
			
			$data["cover_image_file_type"] = $file_type;
			
			$new_file_name_thumb = $cid."-actual".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_ACTUAL,CONST_NEWS_SIZE_HEIGHT_ACTUAL,99,1);

			$new_file_name_thumb = $cid."-thumb".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_THUMB,CONST_NEWS_SIZE_HEIGHT_THUMB,99,1);

			$new_file_name_thumb = $cid."-mini".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_MINI,CONST_NEWS_SIZE_HEIGHT_MINI,99,1);

			$new_file_name_thumb = $cid."-thumb-sq".$file_type;
			$old_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($old_file)){
				unlink($old_file);	
			}
			$result_image_thumb = upload_image("cover_image",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_SQUARE,CONST_NEWS_SIZE_HEIGHT_SQUARE,99,1);

			if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
			{
				// echo $result_image_thumb["error_msg"];
				$return_status = 'error';
				$this->log_error('Frontend : News', 'Add new news fail => Upload image error : '.$result_image_thumb["error_msg"]);
				$this->data["message"] = set_message_error(get_array_value($result_image_thumb,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
				$this->data["js_code"] = "";
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}	
		}
		
		if ( $return_status == 'success' &&  $command == "_insert" ) {
			// $data["posted_by"] = getUserLoginAid($this);
			$data["publish_date"] = get_db_now();
			$data["upload_path"] = $upload_base_path_db.'/'.$cid.'/';
			$news_upload_base_path_db = $data["upload_path"];
			$aid = $this->main->insert_record($data);
			if ($aid > 0) {
				$this->log_status('Frontend : Insert news', '['.$name.'] just added into database.');
				// redirect('admin/news/status/'.md5('success'));
			}
			else {
				$return_status = 'error';

				$this->log_error('Frontend : Insert news', 'Command insert_record() fail. Can not insert '.$name);
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
						
		}
		else if ( $return_status == 'success' && $command == "_update" ) {
			$aid = $this->input->get_post('news_aid');
			$data["aid"] = $aid;

			// if($this->check_duplicate($data,$command)){
			// 	$this->form();
			// 	return "";
			// }

			$data_where["aid"] = $aid;
			$this->main->set_where($data_where);
			$rs = $this->main->update_record($data, $data_where);
			if ($rs) {
				$path = trim(get_array_value($itemResult,"upload_path",""));
				$this->main->set_where($data_where);
				$rs2 = $this->main->load_record(false);
				// $news_upload_base_path_db = $rs2["upload_path"];
				$news_upload_base_path_db = $path;

				$this->data["message"] = set_message_success('Data has been saved.');
				if ($aid > 0) $this->log_status('Frontend : Update news',  '['.$name.'] has been updated.');
			}
			else {
				$return_status = 'error';

				$this->log_error('Frontend : Update news', 'Command update_record() fail. Can not update '.$name.'['.$aid.']');
				$this->data["message"] = set_message_error('Sorry, The system can not insert data right now.<BR>Please try again later or contact administrator to solve the problem.');
				$this->data["js_code"] = '';
				$this->data["command"] = $command;
				$this->data["item_detail"] = $data;
				// $this->form();
				// return "";
			}
			
		}
		else {
			$this->log_error('Frontend : News', 'Command not found.');
			$return_status = 'no-command';
			// redirect('admin/news/status/'.md5('no-command'));
			// return "";
		}

		// Gallery 
		if ($return_status == 'success') {
			$news_aid = $aid;

			if (is_var_array($_FILES['image_name'])) {
				$no_cover_image = true;
				foreach ($_FILES['image_name']['name'] as $_k => $_file) {
					$data_gallery = array();
					$new_file_name_thumb = "";
					
					// Gallery & Cover image
					if( is_var_array($_FILES['image_name']) && !is_blank($_FILES['image_name']['name'][$_k]) ){
						
						//Start upload file
						$image_name = $_FILES['image_name']['name'][$_k]; // $_file["image_name"]["name"][$_k];
						$file_type = substr(strrchr($image_name, "."), 0); // ".jpg"
						
						$gallery_upload_base_path = "./".$news_upload_base_path_db."galleries";
						$gallery_upload_base_path = $news_upload_base_path_db."galleries";
						create_directories($gallery_upload_base_path);

						$new_file_name_thumb = $news_aid.date('YdmHis').get_random_text(4).$file_type;
						$upload_path = $gallery_upload_base_path."/original";
						create_directories("./".$upload_path);

						$old_file = "./".$upload_path."/".$new_file_name_thumb;
						if(is_file($old_file)){
							unlink($old_file);	
						}
						$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,0,0,800,99,1,$_k);

						$upload_path = $gallery_upload_base_path."/thumb";
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
						}	
						$data_gallery["file_name"] = $new_file_name_thumb;

						$data_gallery["news_aid"] = $news_aid;
						$data_gallery["title"] = $_FILES['image_name']['name'][$_k]; // get_array_value($_file,"image_name","");
						$data_gallery["status"] = '1';
						$data_gallery["weight"] = '0'; // $_k;
						$data_gallery["user_owner_aid"] = $user_owner_aid;
						$news_gallery_aid = $this->news_gallery->insert_record($data_gallery);
						if($news_gallery_aid){
							$this->log_status('Frontend : Insert news gallery', '[title='.$data_gallery['title'].'][file ='.$new_file_name_thumb.'] just added into database.');
							// redirect('admin/news/edit/'.$news_aid.'/gallery/status/'.md5('success'));
						}else{
							$this->log_error('Frontend : Insert news gallery', 'Command insert_record() fail. Can not insert news gallery.');
						}


						// Auto upload cover image
						// if (!empty($data['ref_link2_image_url'])) {
							if ($no_cover_image == true && $command == "_insert") {
								$no_cover_image = false;
								$image_name = $_FILES["image_name"]["name"][$_k];
								$file_type = substr(strrchr($image_name, "."), 0);
								
								$data_cover_image["cover_image_file_type"] = $file_type;
								
								$upload_path = $news_upload_base_path_db;
								$new_file_name_thumb = $cid."-actual".$file_type;
								$old_file = "./".$upload_path."/".$new_file_name_thumb;
								if(is_file($old_file)){
									unlink($old_file);	
								}
								$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_ACTUAL,CONST_NEWS_SIZE_HEIGHT_ACTUAL,99,1,$_k);

								$new_file_name_thumb = $cid."-thumb".$file_type;
								$old_file = "./".$upload_path."/".$new_file_name_thumb;
								if(is_file($old_file)){
									unlink($old_file);	
								}
								$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_THUMB,CONST_NEWS_SIZE_HEIGHT_THUMB,99,1,$_k);

								$new_file_name_thumb = $cid."-mini".$file_type;
								$old_file = "./".$upload_path."/".$new_file_name_thumb;
								if(is_file($old_file)){
									unlink($old_file);	
								}
								$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_MINI,CONST_NEWS_SIZE_HEIGHT_MINI,99,1,$_k);

								$new_file_name_thumb = $cid."-thumb-sq".$file_type;
								$old_file = "./".$upload_path."/".$new_file_name_thumb;
								if(is_file($old_file)){
									unlink($old_file);	
								}
								$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE,CONST_NEWS_SIZE_WIDTH_SQUARE,CONST_NEWS_SIZE_HEIGHT_SQUARE,99,1,$_k);

								if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
								{
									// echo $result_image_thumb["error_msg"];
									$return_status = 'error';
									$this->log_error('Frontend : News', 'Add new news fail => Upload cover image error : '.$result_image_thumb["error_msg"]);
									$this->data["message"] = set_message_error(get_array_value($result_image_thumb,"error_msg","Sorry, the system can not save data now. Please try again or contact your administrator."));
									$this->data["js_code"] = "";
									$this->data["command"] = $command;
									$this->data["item_detail"] = $data;
									// $this->form();
									// return "";
								}
								else {
									$data_where["aid"] = $news_aid;
									$this->main->set_where($data_where);
									$rs = $this->main->update_record($data_cover_image, $data_where);
								}

							}
						// }

					}
					
					
					


				}
			}

		}

		// Return 
		$return = array();
		$return['aid'] = $aid;
		$return['status'] = $return_status;
		$return['redirect_url'] = ($command="_update" ? site_url('news/detail/'.$aid) : site_url('news'));
		return $return;
	}

	function ajax_delete_one_gallery_photo() {
		$aid = $this->input->get_post('news_gallery_aid');

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_gallery_model,'news_gallery');
		$this->news_gallery->set_where(array('aid' => $aid));
		$rs = $this->news_gallery->load_record(true);
		
		if (is_var_array($rs) && isset($rs['aid'])) {
			$file_name = $rs['file_name'];
			$file_thumb = "./".$rs['upload_path']."galleries/thumb/".$file_name;
			if(is_file($file_name)){
				unlink($file_name);	
			}
			$file_original = "./".$rs['upload_path']."galleries/original/".$file_name;
			if(is_file($file_original)){
				unlink($file_original);	
			}

			$this->news_gallery->set_where(array('aid' => $aid));
			$this->news_gallery->delete_records();
			// $this->news->delete_records();
			$return = array('status' => 'success', 'msg' => '');
		}
		else {
			$return = array('status' => 'error', 'msg' => 'Oops! It looks like you have no authorize to delete photos in this news.');
		}
		echo json_encode($return);
		return;
	}

	function ajax_delete_one() {
		$news_aid = $this->input->get_post('news_aid');

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_gallery_model,"news_gallery");
		$this->load->model($this->news_comment_model,"news_comment");
		$this->load->model($this->news_user_activity_model,"news_user_activity");

		$this->news->set_where(array('aid' => $news_aid, 'created_by' => getUserLoginAid($this->user_login_info)));
		$rs = $this->news->load_record(false);
		
		if (is_var_array($rs) && isset($rs['aid'])) {
			$this->news_gallery->set_where(array('news_aid' => $news_aid));
			$this->news_gallery->delete_records();

			$this->news_comment->set_where(array('parent_news_aid' => $news_aid));
			$this->news_comment->delete_records();

			$this->news_user_activity->set_where(array('news_aid' => $news_aid));
			$this->news_user_activity->delete_records();

			$this->news->set_where(array('aid' => $news_aid, 'created_by' => getUserLoginAid($this->user_login_info)));
			$this->news->delete_records();
			// $this->news->update_record(array('status' => '0'));

			$return = array('status' => 'success', 'msg' => '');
		}
		else {
			$return = array('status' => 'error', 'msg' => 'Oops! It looks like you have no authorize to delete this news');
		}
		echo json_encode($return);
		return;
	}

	function ajax_edit_one() {
		$news_aid = $this->input->get_post('news_aid');

		$this->load->model($this->news_model,'news');
		$this->news->set_where(array('aid' => $news_aid, 'created_by' => getUserLoginAid($this->user_login_info)));
		$rs = $this->news->load_record(false);
		
		if (is_var_array($rs) && isset($rs['aid'])) {
			$this->load->model($this->news_gallery_model,"news_gallery");
			$tmp = array();
			$tmp["news_aid"] = $news_aid;
			$tmp["status"] = "1";
			$this->news_gallery->set_where($tmp);
			$this->news_gallery->set_order_by("weight ASC, created_date ASC");
			$news_gallery_list = $this->news_gallery->load_records(true);
			if (!is_var_array($news_gallery_list)) {
				$news_gallery_list = array();
				$num_gallery = 0;
			}
			else {
				$num_gallery = count($news_gallery_list);
			}

			$return = array('status' => 'success', 'msg' => '', 'result' => $rs, 'num_gallery' => $num_gallery, 'news_gallery_list' => $news_gallery_list);
		}
		else {
			$return = array('status' => 'error', 'msg' => 'Oops! It looks like you have no authorize to edit this news');
		}
		echo json_encode($return);
		return;
	}

	public function ajax_set_cover_image() {
		
	}

	public function ajax_extract_url_process() {
		// if(isset($_POST["url"]))
		// {
		    $get_url = $_POST["url"]; 
		        
	        //Include PHP HTML DOM parser (requires PHP 5 +)
	        include_once("simple_html_dom.php");
	        
	        //get URL content
	        $get_content = new simple_html_dom();
			$get_content->load_file( $get_url ); //put url or filename in place of xxx

	        // $get_content = file_get_html($get_url); 
	        
	        //Get Page Title 
	        $page_title = $get_content->find("meta[property='og:title']", 0)->content;
	        if (empty($page_title) || is_null($page_title)) {
		        foreach($get_content->find('title') as $element) 
		        {
		            $page_title = $element->plaintext;
		        }
	        }
	        
	        //Get Body Text
	        $page_body = $get_content->find("meta[property='og:description']", 0)->content;
	        if (empty($page_body) || is_null($page_body)) {
	        	$page_body = $get_content->find("meta[name='description']", 0)->content;
		        if (empty($page_body) || is_null($page_body)) {
		        	foreach($get_content->find('meta[description]') as $element) 
			        {
			            $page_body = trim($element->plaintext);
			            $pos=strpos($page_body, ' ', 500); //Find the numeric position to substract
			            $page_body = substr($page_body,0,$pos ); //shorten text to 200 chars
			        }
		        }
	        }
	    
	        $image_urls = array();
	        
	        //get all images URLs in the content
	        foreach($get_content->find('img') as $element) 
	        {
                /* check image URL is valid and name isn't blank.gif/blank.png etc..
                you can also use other methods to check if image really exist */
                // if(!preg_match('/blank.(.*)/i', $element->src) && filter_var($element->src, FILTER_VALIDATE_URL))
                if(!preg_match('/blank.(.*)/i', $element->src) && filter_var($element->src, FILTER_VALIDATE_URL))
                {
                    if (!in_array($element->src, $image_urls))
                    	$image_urls[] =  $element->src;
                }
	        }
	        
	        //prepare for JSON 
	        $output = array('title'=>$page_title, 'images'=>$image_urls, 'content'=> $page_body);
	        echo json_encode($output); //output JSON data
		// }
	}

	public function save_and_publish() 
	{
		@define("thisAction",'save_and_publish');
		$command = $this->input->get_post('command');
		$return = $this->_save_form(false);

		redirect($return['redirect_url']);
	}

}

?>