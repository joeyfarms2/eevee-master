<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_news_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->user_model = 'User_model';
		$this->news_model = 'News_model';
		$this->news_main_model = 'News_main_model';
		$this->news_category_model = 'News_category_model';
		$this->news_gallery_model = 'News_gallery_model';
		$this->news_comment_model = 'News_comment_model';
		$this->news_user_activity_model = 'news_user_activity_model';
		$this->news_comment_user_activity_model = 'news_comment_user_activity_model';
		
		$this->view_most_comments_model = 'View_most_comments_model';
	}

	function get_all_categories() {

		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_category_model,"news_cat");
		$this->news_cat->set_where(array('status' => '1'));
		$rs = $this->news_cat->load_records(false);
		$num_rows = count($rs);
		$results = $rs;
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['news_main_aid']);
			}
		}
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'result' => ($num_rows > 0 ? $results : '')
			);
		echo json_encode($result_obj);
		return "";
	}

	function get_news() {
		$category_aid = $this->input->get_post('category_aid');
		$page = $this->input->get_post('page');
		$total_load = $this->input->get_post('total_load');
		$total_records_in_this_cat = $this->input->get_post('total_records_in_this_cat');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$arr_category_aid = "";
		if(!is_blank($category_aid)){
			if(!is_number_no_zero($category_aid) && strtolower($category_aid) != 'all'){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : category_aid must be integer or leave blank to load all categories.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
			if (strtolower($category_aid) == 'all') $category_aid = '';
			else $arr_category_aid = array(','.$category_aid.',');
		}
		if(!is_blank($page)){
			if(!is_number_no_zero($page)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : page must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$page = 1;
		}
		if(!is_blank($total_load)){
			if(!is_number_no_zero($total_load)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : total_load must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$total_load = 10;
		}

		
		// $this->load->model($this->news_model, "news");
		// $data_insert = "";
		$keyword = $this->input->get_post('keyword');
		//$keyword = array($keyword);
		// $search_in = "title";
		// // echo "keyword = $keyword , search_in = $search_in";
		// if(!is_blank($keyword)){
		// 	if(is_blank($search_in)){
		// 		$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify search_in.', "result" => '');
		// 		echo json_encode($result_obj);
		// 		return "";
		// 	}

		// 	// print_r($search_in_serarch_list);
		// 	$search_option = trim($this->input->get_post('search_option'));
		// 	if($search_option != 'and'){
		// 		$search_option = 'or';
		// 	}
		// 	// echo "search_option = $search_option";
			
		// 	//$data_insert = "";
		// 	$keyword = trim(urldecode($keyword));
		// 	// echo "keyword = $keyword";
		// 	$keyword_tmp = $this->convert_keyword_to_array($keyword);
		// 	$keyword_arr = "";
		// 	$keyword_not_arr = "";
		// 	if(is_var_array($keyword_tmp)){
		// 		foreach ($keyword_tmp as $item) {
		// 			$item = trim($item);
		// 			// echo "item = $item <br />";
		// 			if(!is_blank($item)){
		// 				if($item[0] == "-"){
		// 					$keyword_not_arr[] = substr($item, 1);
		// 					// $tmp = array();
		// 					// $tmp["word"] = substr($item, 1);
		// 					// $tmp["cond"] = "NOT";
		// 					// $tmp["search_in"] = "news";
		// 					// $data_insert[] = $tmp;
		// 				}else{
		// 					$keyword_arr[] = $item;						
		// 					// $tmp = array();
		// 					// $tmp["word"] = $item;
		// 					// $tmp["cond"] = "";
		// 					// $tmp["search_in"] = "news";
		// 					// $data_insert[] = $tmp;
		// 				}
		// 			}
		// 		}
		// 	}
		// 	// if(is_var_array($data_insert)){
		// 	// 	$this->load->model($this->search_history_model,"search_history");
		// 	// 	$this->search_history->insert_records($data_insert);
		// 	// }

			
		// 	// $this->db->flush_cache();
		// 	// $this->load->model($this->news_model,'news');
		// 	// $this->db->start_cache();
		// 	// $this->news->set_where(array("status"=>"1"));
		// 	$this->news->set_open();
		// 	switch($search_in){
		// 		case "title" :
		// 				if(is_var_array($keyword_arr)){
		// 					if($search_option == "or"){
		// 						$this->news->set_and_or_like(array("title"=>$keyword_arr));
		// 					}else{
		// 						$this->news->set_or_and_like_group(array("title"=>$keyword_arr));
		// 					}
		// 				}
		// 				if(is_var_array($keyword_not_arr)){
		// 						$this->news->set_or_and_not_like_group(array("title"=>$keyword_not_arr));
		// 				}
		// 				break;
				
				
		// 		}
		// 	// //}
		// 	$this->news->set_close();
		// }


		$this->load->model($this->news_model, "news");
		$this->load->model($this->news_user_activity_model, "user_activity");

		if(!is_blank($total_records_in_this_cat)){
			if(!is_number_no_zero($total_records_in_this_cat)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : total_records_in_this_cat must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		if ($page == '1' || is_blank($total_records_in_this_cat)) {
			$total_records_in_this_cat = 0;
			$tmp_rs = $this->news->load_home($arr_category_aid, '1',0,0,$keyword);
			if (isset($tmp_rs['num_rows'])) {
				$total_records_in_this_cat = $tmp_rs['num_rows'];
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/



		/*--- Step 3: Take action ---*/
		$next_page = '';
		$tmp_rs = $this->news->load_home($arr_category_aid, '1' , (($page-1)*$total_load), $total_load,$keyword);
		$num_rows = get_array_value($tmp_rs, 'num_rows', 0);
		$results = get_array_value($tmp_rs, 'results', '');
		if ($total_records_in_this_cat > ($page * $total_load)) {
			$next_page = $page+1;
		}
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				$news_aid = $item['aid'];
				$has_activity = $this->user_activity->has_activity($news_aid, $user_aid);
				//print_r($has_activity);
				$total = $this->news->get_total_activity($news_aid);
				$results[$k]['has_wowed'] = $has_activity['has_wowed'];
				$results[$k]['has_cheered'] = $has_activity['has_cheered'];
				$results[$k]['has_thanked'] = $has_activity['has_thanked'];
				$results[$k]['total_wow'] = $total['total_wow'];
				$results[$k]['total_cheer'] = $total['total_cheer'];
				$results[$k]['total_thanks'] = $total['total_thanks'];
				
				$results[$k]['description'] = strip_tags($results[$k]['description'], '<a><img><video><br>');

				unset($results[$k]['news_main_aid']);
				unset($results[$k]['news_main_name']);
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
				unset($results[$k]['short_description']);
				unset($results[$k]['very_short_description']);
			}
		}
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'total_records_in_this_cat' => $total_records_in_this_cat,
				'next_page' => $next_page,
				'result' => ($num_rows > 0 ? $results : '')
			);
		echo json_encode($result_obj);
		return "";
	}
	
	function get_news_detail() {
		$news_aid = $this->input->get_post('news_aid');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,"news");
		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->news->set_where(array('aid' => $news_aid));
		$result = $this->news->load_record(true);

		$has_activity = $this->user_activity->has_activity($news_aid, $user_aid);
		$total = $this->news->get_total_activity($news_aid);

		$result_obj = array(
				'status' => (is_var_array($result) ? 'success' : 'warning'),
				'msg' => (is_var_array($result) ? '' : 'No record found.'),
				"has_wowed" => $has_activity['has_wowed'],
				"has_cheered" => $has_activity['has_cheered'],
				"has_thanked" => $has_activity['has_thanked'],
				"total_wow" => $total['total_wow'],
				"total_cheer" => $total['total_cheer'],
				"total_thanks" => $total['total_thanks'],
				'result' => '',
			);
		if (is_var_array($result)) {
			$result['description'] = strip_tags($result['description'], '<a><img><video><br>');

			unset($result['news_main_aid']);
			unset($result['news_main_name']);
			unset($result['avatar_tiny']);
			unset($result['avatar_mini']);
			unset($result['user_info']);
			unset($result['short_description']);
			unset($result['very_short_description']);
			$result_obj['result'] = $result;
		}
		echo json_encode($result_obj);
		return "";
	}
	
	function get_who_wow_news() {
		$news_aid = $this->input->get_post('news_aid');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->user_activity->set_where(array('news_aid' => $news_aid, 'status_wow' => '1'));
		$this->user_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$result = $this->user_activity->load_records(true);
		$result_obj = array(
					'status' => (is_var_array($result['results']) ? 'success' : 'warning'),
					'msg' => (is_var_array($result['results']) ? '' : 'No record found.'),
					'total_records' => 0,
					'result' => ''
				);
		if (is_var_array($result['results'])) {
			$rs = $result['results'];
			foreach ($rs as $key => $value) {
				unset($rs[$key]['news_aid']);
				unset($rs[$key]['status_wow']);
				unset($rs[$key]['status_cheer']);
				unset($rs[$key]['status_thanks']);
				unset($rs[$key]['department_aid']);
				unset($rs[$key]['created_date']);
				unset($rs[$key]['created_by']);
				unset($rs[$key]['updated_date']);
				unset($rs[$key]['updated_by']);
			}
			$result_obj['total_records'] = count($rs);
			$result_obj['result'] = $rs;
		}
		echo json_encode($result_obj);
		return "";
	}
	
	function get_who_cheer_news() {
		$news_aid = $this->input->get_post('news_aid');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->user_activity->set_where(array('news_aid' => $news_aid, 'status_cheer' => '1'));
		$this->user_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$result = $this->user_activity->load_records(true);
		$result_obj = array(
					'status' => (is_var_array($result['results']) ? 'success' : 'warning'),
					'msg' => (is_var_array($result['results']) ? '' : 'No record found.'),
					'total_records' => 0,
					'result' => ''
				);
		if (is_var_array($result['results'])) {
			$rs = $result['results'];
			foreach ($rs as $key => $value) {
				unset($rs[$key]['news_aid']);
				unset($rs[$key]['status_wow']);
				unset($rs[$key]['status_cheer']);
				unset($rs[$key]['status_thanks']);
				unset($rs[$key]['department_aid']);
				unset($rs[$key]['created_date']);
				unset($rs[$key]['created_by']);
				unset($rs[$key]['updated_date']);
				unset($rs[$key]['updated_by']);
			}
			$result_obj['total_records'] = count($rs);
			$result_obj['result'] = $rs;
		}
		echo json_encode($result_obj);
		return "";
	}
	
	function get_who_thanks_news() {
		$news_aid = $this->input->get_post('news_aid');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_user_activity_model,'user_activity');
		$this->user_activity->set_where(array('news_aid' => $news_aid, 'status_thanks' => '1'));
		$this->user_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$result = $this->user_activity->load_records(true);
		$result_obj = array(
					'status' => (is_var_array($result['results']) ? 'success' : 'warning'),
					'msg' => (is_var_array($result['results']) ? '' : 'No record found.'),
					'total_records' => 0,
					'result' => ''
				);
		if (is_var_array($result['results'])) {
			$rs = $result['results'];
			foreach ($rs as $key => $value) {
				unset($rs[$key]['news_aid']);
				unset($rs[$key]['status_wow']);
				unset($rs[$key]['status_cheer']);
				unset($rs[$key]['status_thanks']);
				unset($rs[$key]['department_aid']);
				unset($rs[$key]['created_date']);
				unset($rs[$key]['created_by']);
				unset($rs[$key]['updated_date']);
				unset($rs[$key]['updated_by']);
			}
			$result_obj['total_records'] = count($rs);
			$result_obj['result'] = $rs;
		}
		echo json_encode($result_obj);
		return "";
	}
	
	function get_who_comment_news() {
		$news_aid = $this->input->get_post('news_aid');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_comment_model,'news_comment');
		$has_commented = $this->news_comment->has_commented($news_aid);
		$result = $this->news_comment->get_who_comment($news_aid);
		
		$result_obj = array(
					'status' => (is_var_array($result['results']) ? 'success' : 'warning'),
					'msg' => (is_var_array($result['results']) ? '' : 'No record found.'),
					'total_records' => 0,
					'result' => ''
				);
		if (is_var_array($result['results'])) {
			$tmp = array();
			$rs = $result['results'];
			foreach ($rs as $key => $value) {
				$tmp[$key]['user_aid'] = $value['user_aid'];
				$tmp[$key]['username'] = $value['username'];
				$tmp[$key]['first_name_th'] = $value['first_name_th'];
				$tmp[$key]['last_name_th'] = $value['last_name_th'];
				$tmp[$key]['full_name_th'] = $value['full_name_th'];
				$tmp[$key]['email'] = $value['email'];
				$tmp[$key]['avatar_path'] = $value['avatar_path'];
				$tmp[$key]['avatar_type'] = $value['avatar_type'];
				$tmp[$key]['avatar_tiny_path'] = $value['avatar_tiny_path'];
				$tmp[$key]['avatar_tiny'] = $value['avatar_tiny'];
				$tmp[$key]['avatar_mini_path'] = $value['avatar_mini_path'];
				$tmp[$key]['avatar_mini'] = $value['avatar_mini'];
				$tmp[$key]['comment'] = $value['comment'];
			}
			$result_obj['total_records'] = count($rs);
			$result_obj['result'] = $tmp;
		}
		echo json_encode($result_obj);
		return "";
	}
	
	function get_who_wow_comment() {
		$comment_aid = $this->input->get_post('comment_aid');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($comment_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify comment_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($comment_aid)){
			if(!is_number_no_zero($comment_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : comment_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_comment_user_activity_model,'user_comment_activity');
		$this->user_comment_activity->set_where(array('news_comment_aid' => $comment_aid, 'status_wow' => '1'));
		$this->user_comment_activity->set_order_by(array('user.first_name_th' => 'ASC', 'user.last_name_th' => 'ASC'));
		$result = $this->user_comment_activity->load_records(true);

		$result_obj = array(
					'status' => (is_var_array($result['results']) ? 'success' : 'warning'),
					'msg' => (is_var_array($result['results']) ? '' : 'No record found.'),
					'total_records' => 0,
					'result' => ''
				);
		if (is_var_array($result['results'])) {
			$rs = $result['results'];
			foreach ($rs as $key => $value) {
				unset($rs[$key]['news_comment_aid']);
				unset($rs[$key]['status_wow']);
				unset($rs[$key]['department_aid']);
				unset($rs[$key]['created_date']);
				unset($rs[$key]['created_by']);
				unset($rs[$key]['updated_date']);
				unset($rs[$key]['updated_by']);
			}
			$result_obj['total_records'] = count($rs);
			$result_obj['result'] = $rs;
		}
		echo json_encode($result_obj);
		return "";
	}
	
	
	/**
	 * เรียกดูรายการสำหรับภาพประกอบข่าวในรูปแบบ gallery
	 */
	function get_news_photo_gallery() {
		$news_aid = trim($this->input->get('news_aid'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();

		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
	

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');
		$user_owner_aid = get_array_value($this_user, 'user_owner_aid', '');

		$this->load->model($this->news_gallery_model,'news_gallery');
		$this->load->model($this->news_model,'news');
		$this->news->set_where(array('aid' => $news_aid));
		$this_news = $this->news->load_record(false);
		if (!is_var_array($this_news)) {
			$result_obj = array('status' => 'error','msg' => 'News not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_gallery_model,'news_gallery');
		$this->news_gallery->set_where(array('news_aid' => $news_aid));
		
		
		$rs = $this->news_gallery->load_records(true, array('news_aid' => $news_aid));
		
		
			
		$num_rows = count($rs);
		//$results = get_array_value($rs, 'results', 0);
		// if ($num_rows > 0) {
// 			foreach ($results as $k=>$item) {
// 				unset($results[$k]['avatar_tiny']);
// 				unset($results[$k]['avatar_mini']);
// 				unset($results[$k]['user_info']); 
// 				unset($results[$k]['news_title']);
// 				unset($results[$k]['news_title_short']);
// 			}
// 		}
		
		//print_r($result);
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'result' => ($num_rows > 0 ? $rs : ''),
			);
		echo json_encode($result_obj);
		return "";
	}
	
	
	function add_news_photo_gallery() {
		$title = trim(strip_tags($this->input->get_post('title')));
		$weight = trim($this->input->get_post('weight'));
		$news_aid = trim($this->input->get_post('news_aid'));
		$ori_img_base64_string = trim($this->input->get_post('ori_img_base64_string'));
		 //$thumb_img_base64_string = trim($this->input->get_post('thumb_img_base64_string'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();

		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if( !is_blank(get_array_value($_FILES,"image_name","")) && !is_blank(get_array_value($_FILES["image_name"],"name","")) ){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify file image_name.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');
		$user_owner_aid = get_array_value($this_user, 'user_owner_aid', '');

		$this->load->model($this->news_gallery_model,'news_gallery');
		$this->load->model($this->news_model,'news');
		$this->news->set_where(array('aid' => $news_aid));
		$this_news = $this->news->load_record(false);
		if (!is_var_array($this_news)) {
			$result_obj = array('status' => 'error','msg' => 'News not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}


		/*--- Step 3: Take action ---*/
		// 3.1 Check permission
		$has_authorized = false;
		if ($user_role_aid >= 1 && $user_role_aid <= 5) {
			$has_authorized = true;
		}
		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}


		// 3.2 Prepare insert data
		$data = array();
		$data['user_owner_aid'] = $user_owner_aid;
		$data['news_aid'] = $news_aid;
		$data['title'] = $title;
		$data["status"] = '1';
		$data['weight'] = $weight;
		$data["created_by"] = $user_aid;
		$data["updated_by"] = $user_aid;

		
		// Prepare upload base path
		$upload_base_path = "./".get_array_value($this_news, 'upload_path', '');
		$upload_base_path_db = "./".get_array_value($this_news, 'upload_path', '');
		create_directories($upload_base_path);
		create_directories($upload_base_path."galleries");
		$gallery_upload_path = $upload_base_path."galleries/";


		// // 3.3 Process action
		// $new_file_name_thumb = "";
		
		// if( !is_blank(get_array_value($_FILES,"image_name","")) && !is_blank(get_array_value($_FILES["image_name"],"name","")) ){
			
		// 	//Start upload file
		// 	$image_name = $_FILES["image_name"]["name"];
		// 	$file_type = substr(strrchr($image_name, "."), 0);
			
		// 	$new_file_name_thumb = $news_aid.date('YdmHis').get_random_text(4).$file_type;
		// 	$upload_path = $upload_base_path_db."/original";
		// 	create_directories("./".$upload_path);
		// 	$old_file = "./".$upload_path."/".$new_file_name_thumb;
		// 	if(is_file($old_file)){
		// 		unlink($old_file);	
		// 	}
		// 	$result_image_thumb = upload_image($image_name,$upload_path,$new_file_name_thumb,0,0,800,99,1);

		// 	$upload_path = $upload_base_path_db."/thumb";
		// 	create_directories("./".$upload_path);
		// 	$old_file = "./".$upload_path."/".$new_file_name_thumb;
		// 	if(is_file($old_file)){
		// 		unlink($old_file);	
		// 	}
		// 	$result_image_thumb = upload_image($image_name,$upload_path,$new_file_name_thumb,0,0,150,99,1);
			
		// 	if ( !is_blank(get_array_value($result_image_thumb,"error_msg","")) )
		// 	{
		// 		// echo $result_image_thumb["error_msg"];
		// 		$this->log_status('Admin : Issue', 'Save issue fail => Upload image error : '.$result_image_thumb["error_msg"]);
		// 	}	
		// 	$data["file_name"] = $new_file_name_thumb;
		// 	if (is_blank($data['title'])) {
		// 		$data['title'] = $new_file_name_thumb;
		// 	}
		// }




		// 3.3 Process action
		
		if (!empty($ori_img_base64_string)) {
			$imgdata = base64_decode($ori_img_base64_string);
			$f = finfo_open();
			$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
			$file_ext = '';

			switch ($mime_type) {
				default:
				case 'image/jpeg':
				case 'image/pjpeg':
					$file_ext = '.jpg';
					break;
				case 'image/bmp':
				case 'image/x-windows-bmp':
					$file_ext = '.bmp';
					break;
				case 'image/gif':
					$file_ext = '.gif';
					break;
				case 'image/png':
				case 'image/x-png':
					$file_ext = '.png';
					break;
				case 'image/tiff':
					$file_ext = '.tiff';
					break;
			}
			$new_file_name_thumb = $news_aid.date('YdmHis').get_random_text(4).$file_ext;
			$data['file_name'] = $new_file_name_thumb;

			$upload_path = $gallery_upload_path."original";
			create_directories("./".$upload_path);
			$output_file = "./".$upload_path."/".$new_file_name_thumb;
			if(is_file($output_file)){
				unlink($output_file);	
			}
			$imgDecoded_big = base64_decode($ori_img_base64_string);

			// Requires string image as parm, returns image resource
			$im_big = imagecreatefromstring($imgDecoded_big);

			// Get width and height of original image resource
			$origWidth_big = imagesx($im_big);
			$origHeight_big = imagesy($im_big);
			
			if ($origHeight_big <= 800) {
				$Width_big = $origWidth_big;
				$Height_big = $origHeight_big;
			}else{
				$Width_big = ($origWidth_big * 800)/$origHeight_big;
				$Height_big = 800;
			}
			

			// Create new destination image resource for new 24 x 24 image
			$imNew_big = imagecreatetruecolor($Width_big, $Height_big);

			// Re-sample image to smaller size and display
			imagecopyresampled($imNew_big, $im_big, 0, 0, 0, 0, $Width_big, $Height_big, $origWidth_big, $origHeight_big);
			imagejpeg($imNew_big, $output_file);
			imagedestroy($im_big);
			imagedestroy($imNew_big);
			//$result_image_thumb = upload_image("image_name",$upload_path,$new_file_name_thumb,0,0,800,99,1);
			// $ifp = fopen($output_file, "wb"); 
			// fwrite($ifp, base64_decode($ori_img_base64_string)); 
			// fclose($ifp);

			$upload_path_thumb = $gallery_upload_path."thumb";
			create_directories("./".$upload_path_thumb);
			$output_file_thumb = "./".$upload_path_thumb."/".$new_file_name_thumb;
			if(is_file($output_file_thumb)){
				unlink($output_file_thumb);	
			}

			// //$result_image_thumb = upload_image($ori_img_base64_string,$upload_path_thumb,$new_file_name_thumb,0,0,150,99,1);
			// $ifp_thumb = fopen($output_file_thumb, "wb"); 
			// fwrite($ifp_thumb, base64_decode($ori_img_base64_string)); 
			// fclose($ifp_thumb);
			//$percent = 0.5;

			$imgDecoded = base64_decode($ori_img_base64_string);

			// Requires string image as parm, returns image resource
			$im = imagecreatefromstring($imgDecoded);

			// Get width and height of original image resource
			$origWidth = imagesx($im);
			$origHeight = imagesy($im);
			// if($origWidth > $origHeight){
			// 	$thumb_Height = ($origHeight * 220)/$origWidth;
			// 	$thumb_Width = 220;
			// }else

			if ($origHeight <= 150) {
				$thumb_Width = $origWidth;
			 	$thumb_Height = $origWidth;
			}else{
				$thumb_Width = ($origWidth * 150)/$origHeight;
			 	$thumb_Height = 150;
			}
			// if ($origWidth < $origHeight) {
			// 	$thumb_Width = ($origWidth * 150)/$origHeight;
			// 	$thumb_Height = 150;
			// }else{
			// 	$thumb_Width = $origWidth;
			// 	$thumb_Height = $origWidth;
			// }
			
			// Create new destination image resource for new 24 x 24 image
			$imNew = imagecreatetruecolor($thumb_Width, $thumb_Height);

			// Re-sample image to smaller size and display
			imagecopyresampled($imNew, $im, 0, 0, 0, 0, $thumb_Width, $thumb_Height, $origWidth, $origHeight);
			imagejpeg($imNew , $output_file_thumb);
			imagedestroy($im);
			imagedestroy($imNew);

			// $output_file = $gallery_upload_path.$data["cid"].'-actual'.$file_ext;
			// $ifp = fopen($output_file, "wb"); 
			// fwrite($ifp, base64_decode($cover_img_base64_string)); 
			// fclose($ifp); 
		}
		
		
		$news_gallery_aid = $this->news_gallery->insert_record($data);

		if ($news_gallery_aid > 0) {
			$this->news_gallery->set_where('aid', $news_gallery_aid);
			$rs = $this->news_gallery->load_record(false);
			unset($rs['user_owner_aid']);
			unset($rs['description']);
			$rs['original_full_path'] = "";
			$rs['thumb_full_path'] = "";
			if (!empty($rs['file_name'])) {
				$rs['original_full_path'] = $gallery_upload_path."original/".$rs['file_name'];
				$rs['thumb_full_path'] = $gallery_upload_path."thumb/".$rs['file_name'];
			}
			$result_obj = array(
					'status' => 'success',
					'msg' => '',
					'result' => $rs
				);
		}
		else {
			$result_obj = array(
					'status' => 'error',
					'msg' => 'Not able to insert this photo into database. Please try again or contact bookdose administrator.', 
					'result' => ''
				);
		}
		echo json_encode($result_obj);
		return "";
	}



	function delete_news_photo_gallery() {
		$photo_aid = trim($this->input->get_post('photo_aid'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$user_role_aid = get_array_value($login_history,"user_role_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($photo_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify photo_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($photo_aid)){
			if(!is_number_no_zero($photo_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : photo_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');

		$this->load->model($this->news_gallery_model,'news_gallery');
		$this->news_gallery->set_where(array('aid' => $photo_aid));
		$this_photo = $this->news_gallery->load_record(false);
		if (!is_var_array($this_photo)) {
			$result_obj = array('status' => 'error','msg' => 'Photo not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 3: Take action ---*/
		// 3.1 Check permission
		$has_authorized = false;
		if ($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3) {
			$has_authorized = true;
		}

		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		// 3.2 Process action
		$this_obj_info = trim(get_array_value($this_photo, "file_name", "N/A").' [aid = '.$aid.']');
		$this_obj_file_name = get_array_value($this_photo, "file_name", $aid);
		$this_obj_title = (get_array_value($this_photo,"title")!="" ? get_array_value($this_photo,"title") : get_array_value($this_photo,"file_name", $aid));
		$upload_path = get_array_value($this_photo,"upload_path","");
		
		$old_file = "./".$upload_path."galleries/original/".$this_obj_file_name;
		if(is_file($old_file)){
			unlink($old_file);
		}
		$old_file = "./".$upload_path."galleries/thumb/".$this_obj_file_name;
		if(is_file($old_file)){
			unlink($old_file);
		}

		$this->news_gallery->set_where(array('aid' => $photo_aid));
		$rs = $this->news_gallery->delete_records();

		if ($rs) {
			$result_obj = array(
				'status' => 'success',
				'msg' => ''
			);
		}
		else {
			$result_obj = array(
				'status' => 'warning',
				'msg' => 'No record deleted.'
			);
		}
		echo json_encode($result_obj);
		return "";
	}

	function add_news() {
		$title = trim(strip_tags($this->input->get_post('title')));
		$category = trim($this->input->get_post('category'));
		$description = trim($this->input->get_post('description'));
		$news_main_aid = trim($this->input->get_post('news_main_aid'));
		$cover_img_base64_string = trim($this->input->get_post('cover_img_base64_string'));
		//$video_file = $this->input->get_post('video_file');
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();

		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$user_role_aid = get_array_value($login_history,"user_role_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($title)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify title.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($category)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify category. Use comma as a separator, e.g. 1,2,3', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if( !is_blank(get_array_value($_FILES,"video_file","")) && !is_blank(get_array_value($_FILES["video_file"],"name","")) ){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify file video_file.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');
		$user_owner_aid = get_array_value($this_user, 'user_owner_aid', '');

		/*--- Step 3: Take action ---*/
		// 3.1 Check permission
		$has_authorized = false;
		if ($user_role_aid >= 1 && $user_role_aid <= 5) {
			$has_authorized = true;
		}

		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$arr_category_aid = array();
		$arr_category_aid = explode(',', $category);
		if (count($arr_category_aid) > 0) {
			$txt_category = ','.implode(',', $arr_category_aid).',';
		}
		else {
			// Incorrect format for category parameter
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify category. Use comma as a separator, e.g. 1,2,3', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}


		// 3.2 Prepare insert data
		$data = array();
		$data['user_owner_aid'] = $user_owner_aid;
		$data['news_main_aid'] = (isset($news_main_aid) && is_number_no_zero($news_main_aid) ? $news_main_aid : '1');
		$data['is_home'] = '1';
		$data['title'] = $title;
		$data['draft_title'] = $title;
		$data['category'] = $txt_category;
		$data['description'] = $description;
		$data['draft_description'] = $description;
		$data["publish_date"] = get_db_now();
		$data["status"] = '1';
		$data["posted_by"] = $user_aid;
		$data["created_by"] = $user_aid;
		$data["updated_by"] = $user_aid;

		// Generate cid
		do {
			$cid = trim(random_string('alnum', 12));
		}
		while( $this->isNewsCodeExits($cid) );
		$data["cid"] = trim($cid);
		
		// Prepare upload base path
		$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$data["publish_date"],"").'/'.get_datetime_pattern("m",$data["publish_date"],"");
		$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$data["publish_date"],"").'/'.get_datetime_pattern("m",$data["publish_date"],"");
		create_directories($upload_base_path);
		create_directories($upload_base_path."/".$data["cid"]);
		$data["upload_path"] = $upload_base_path_db.'/'.$data["cid"].'/';
		

		// 3.3 Process action
		if (!empty($cover_img_base64_string)) {
			$imgdata = base64_decode($cover_img_base64_string);
			$f = finfo_open();
			$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
			$file_ext = '';

			switch ($mime_type) {
				default:
				case 'image/jpeg':
				case 'image/pjpeg':
					$file_ext = '.jpg';
					break;
				case 'image/bmp':
				case 'image/x-windows-bmp':
					$file_ext = '.bmp';
					break;
				case 'image/gif':
					$file_ext = '.gif';
					break;
				case 'image/png':
				case 'image/x-png':
					$file_ext = '.png';
					break;
				case 'image/tiff':
					$file_ext = '.tiff';
					break;
			}
			$data["cover_image_file_type"] = $file_ext;
			$output_file = $data["upload_path"].$data["cid"].'-actual'.$file_ext;
			$ifp = fopen($output_file, "wb"); 
			fwrite($ifp, base64_decode($cover_img_base64_string)); 
			fclose($ifp); 
		}


		if (isset($_FILES['video_file']) && is_var_array($_FILES['video_file']) && !empty($_FILES['video_file'])) {
			try {
			    // Undefined | Multiple Files | $_FILES Corruption Attack
			    // If this request falls under any of them, treat it invalid.
			    if (
			        !isset($_FILES['video_file']['error']) ||
			        is_array($_FILES['video_file']['error'])
			    ) {
			    		$result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - Invalid parameters.', 'result' => '');
						echo json_encode($result_obj);
						return "";
			    }

			    // Check $_FILES['upfile']['error'] value.
			    switch ($_FILES['video_file']['error']) {
			        case UPLOAD_ERR_OK:
			            break;
			        case UPLOAD_ERR_NO_FILE:
			            $result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - No file sent.', 'result' => '');
							echo json_encode($result_obj);
							return "";
			        case UPLOAD_ERR_INI_SIZE:
			        case UPLOAD_ERR_FORM_SIZE:
			        		$result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - Exceeded filesize limit.', 'result' => '');
							echo json_encode($result_obj);
							return "";
			        default:
			        		$result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - Unknown errors.', 'result' => '');
							echo json_encode($result_obj);
							return "";
			    }

			    // You should also check filesize here. 
			    if ($_FILES['video_file']['size'] > 10000000) {
			    		$result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - Exceeded filesize limit.', 'result' => '');
						echo json_encode($result_obj);
						return "";
			    }

			    // DO NOT TRUST $_FILES['video_file']['mime'] VALUE !!
			    // Check MIME Type by yourself.
			    $finfo = new finfo(FILEINFO_MIME_TYPE);
			    if (false === $file_ext = array_search(
			        $finfo->file($_FILES['video_file']['tmp_name']),
			        array(
			            'mp4' => 'video/mp4',
			            'ogg' => 'video/ogg',
			            'ogg' => 'video/ogg',
			            'webm' => 'video/webm',
			            'mp3' => 'audio/mp3',
			            'wma' => 'audio/wma'
			        ),
			        true
			    )) {
			    		$result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - Invalid file format.', 'result' => '');
						echo json_encode($result_obj);
						return "";
			    }

			    // You should name it uniquely.
			    // DO NOT USE $_FILES['video_file']['name'] WITHOUT ANY VALIDATION !!
			    // On this example, obtain safe unique name from its binary data.
			    // $data["video_file_type"] = $file_ext;
				 $data["video_file_path"] = $data["upload_path"].'vdo_'.$data["cid"].$file_ext;

			    if (!move_uploaded_file( $_FILES['video_file']['tmp_name'], $data["video_file_path"])) {
		    		$result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - Failed to move uploaded file.', 'result' => '');
					echo json_encode($result_obj);
					return "";
			    }

			    // echo 'File is uploaded successfully.';
			} 
			catch (RuntimeException $e) {
			    // echo $e->getMessage();
				$result_obj = array('status' => 'error','msg' => '$_FILES["video_file"] Error - '.$e->getMessage(), 'result' => '');
						echo json_encode($result_obj);
						return "";

			}
		}


		$this->load->model($this->news_model,'news');
		$news_aid = $this->news->insert_record($data);

		if ($news_aid > 0) {
			$this->news->set_where('aid', $news_aid);
			$rs = $this->news->load_record(false);
			$rs['cover_image_full_path'] = "";
			if (!empty($rs['cover_image_file_type'])) {
				$rs['cover_image_full_path'] = $rs['upload_path'].$rs['cid'].'-actual'.$rs['cover_image_file_type'];
			}
			$result_obj = array(
					'status' => 'success',
					'msg' => '',
					'result' => $rs
				);
		}
		else {
			$result_obj = array(
					'status' => 'error',
					'msg' => 'Not able to insert this news into database. Please try again or contact bookdose administrator.', 
					'result' => ''
				);
		}
		echo json_encode($result_obj);
		return "";
	}

	function update_news() {
		$news_aid = trim($this->input->get_post('news_aid'));
		$title = trim(strip_tags($this->input->get_post('title')));
		$category = trim($this->input->get_post('category'));
		$description = trim($this->input->get_post('description'));
		$news_main_aid = trim($this->input->get_post('news_main_aid'));
		$cover_img_base64_string = trim($this->input->get_post('cover_img_base64_string'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();

		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$user_role_aid = get_array_value($login_history,"user_role_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		if(is_blank($title)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify title.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($category)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify category. Use comma as a separator, e.g. 1,2,3', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');
		$user_owner_aid = get_array_value($this_user, 'user_owner_aid', '');

		$this->load->model($this->news_model,'news');
		$this->news->set_where(array('aid' => $news_aid));
		$this_news = $this->news->load_record(false);
		if (!is_var_array($this_news)) {
			$result_obj = array('status' => 'error','msg' => 'News not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 3: Take action ---*/
		// 3.1 Check permission
		$has_authorized = false;
		if ($user_role_aid >= 1 && $user_role_aid <= 5) {
			$has_authorized = true;
		}

		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$arr_category_aid = array();
		$arr_category_aid = explode(',', $category);
		if (count($arr_category_aid) > 0) {
			$txt_category = ','.implode(',', $arr_category_aid).',';
		}
		else {
			// Incorrect format for category parameter
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify category. Use comma as a separator, e.g. 1,2,3', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		// 3.2 Prepare update data
		$data = array();
		$data['user_owner_aid'] = $user_owner_aid;
		$data['news_main_aid'] = (isset($news_main_aid) && is_number_no_zero($news_main_aid) ? $news_main_aid : '1');
		$data['is_home'] = '1';
		$data['title'] = $title;
		$data['draft_title'] = $title;
		$data['category'] = $txt_category;
		$data['description'] = $description;
		$data['draft_description'] = $description;
		$data["publish_date"] = get_db_now();
		$data["status"] = '1';
		$data["posted_by"] = $user_aid;

		// Generate cid
		if (is_blank(get_array_value($this_news, 'cid'))) {
			do {
				$cid = trim(random_string('alnum', 12));
			}
			while( $this->isNewsCodeExits($cid) );
			$data["cid"] = trim($cid);
		}
		if (is_blank(get_array_value($this_news, 'upload_path'))) {
			// Prepare upload base path
			$upload_base_path = "./uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$data["publish_date"],"").'/'.get_datetime_pattern("m",$data["publish_date"],"");
			$upload_base_path_db = "uploads/".CONST_PROJECT_CODE."/news/".get_datetime_pattern("Y",$data["publish_date"],"").'/'.get_datetime_pattern("m",$data["publish_date"],"");
			create_directories($upload_base_path);
			create_directories($upload_base_path."/".$data["cid"]);
			$data["upload_path"] = $upload_base_path_db.'/'.$data["cid"].'/';
		}
			
		// 3.3 Process action
		if (!empty($cover_img_base64_string)) {
			$imgdata = base64_decode($cover_img_base64_string);
			$f = finfo_open();
			$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
			$file_ext = '';

			switch ($mime_type) {
				default:
				case 'image/jpeg':
				case 'image/pjpeg':
					$file_ext = '.jpg';
					break;
				case 'image/bmp':
				case 'image/x-windows-bmp':
					$file_ext = '.bmp';
					break;
				case 'image/gif':
					$file_ext = '.gif';
					break;
				case 'image/png':
				case 'image/x-png':
					$file_ext = '.png';
					break;
				case 'image/tiff':
					$file_ext = '.tiff';
					break;
			}
			$data["cover_image_file_type"] = $file_ext;
			$output_file = $data["upload_path"].$data["cid"].'-actual'.$file_ext;
			$ifp = fopen($output_file, "wb"); 
			fwrite($ifp, base64_decode($cover_img_base64_string)); 
			fclose($ifp); 
		}

		$this->news->set_where('aid', $news_aid);
		$this->news->update_record($data);

		if ($news_aid > 0) {
			$this->news->set_where('aid', $news_aid);
			$rs = $this->news->load_record(false);
			$rs['cover_image_full_path'] = "";
			if (!empty($rs['cover_image_file_type'])) {
				$rs['cover_image_full_path'] = $rs['upload_path'].$rs['cid'].'-actual'.$rs['cover_image_file_type'];
			}
			$result_obj = array(
					'status' => 'success',
					'msg' => '',
					'result' => $rs
				);
		}
		else {
			$result_obj = array(
					'status' => 'error',
					'msg' => 'Not able to update this news. Please try again or contact bookdose administrator.', 
					'result' => ''
				);
		}
		echo json_encode($result_obj);
		return "";
	}

	function delete_news() {
		$news_aid = trim($this->input->get_post('news_aid'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$user_role_aid = get_array_value($login_history,"user_role_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');

		$this->load->model($this->news_model,'news');
		$this->news->set_where(array('aid' => $news_aid));
		$this_news = $this->news->load_record(false);
		if (!is_var_array($this_news)) {
			$result_obj = array('status' => 'error','msg' => 'News not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 3: Take action ---*/
		// 3.1 Check permission
		$has_authorized = false;
		if ($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3) {
			$has_authorized = true;
		}

		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		// 3.2 Process action
		$this->news->set_where(array('aid' => $news_aid));
		$rs = $this->news->delete_records();

		if ($rs) {
			$result_obj = array(
				'status' => 'success',
				'msg' => ''
			);
		}
		else {
			$result_obj = array(
				'status' => 'warning',
				'msg' => 'No record deleted.'
			);
		}
		echo json_encode($result_obj);
		return "";
	}

	function wow_news() {
		$news_aid = trim($this->input->get_post('news_aid'));
		$status = trim($this->input->get_post('status'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_user_activity_model,'user_activity');
		if ($status == '0') {
			$this->user_activity->do_unwow($news_aid, $user_aid);
		}
		else {
			$this->user_activity->do_wow($news_aid, $user_aid);
		}
		$this->news->update_all_total($news_aid);

		$has_activity = $this->user_activity->has_activity($news_aid, $user_aid);
		$total = $this->news->get_total_activity($news_aid);
		$result_obj = array(
				'status' => 'success',
				'msg' => '', 
				'result' => '', 
				"has_wowed" => $has_activity['has_wowed'],
				"has_cheered" => $has_activity['has_cheered'],
				"has_thanked" => $has_activity['has_thanked'],
				"total_wow" => $total['total_wow'],
				"total_cheer" => $total['total_cheer'],
				"total_thanks" => $total['total_thanks']
			);
		echo json_encode($result_obj);
		return "";
	}

	function cheer_news() {
		$news_aid = trim($this->input->get_post('news_aid'));
		$status = trim($this->input->get_post('status'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_user_activity_model,'user_activity');
		if ($status == '0') {
			$this->user_activity->do_uncheer($news_aid, $user_aid);
		}
		else {
			$this->user_activity->do_cheer($news_aid, $user_aid);
		}
		$this->news->update_all_total($news_aid);

		$has_activity = $this->user_activity->has_activity($news_aid, $user_aid);
		$total = $this->news->get_total_activity($news_aid);
		$result_obj = array(
				'status' => 'success',
				'msg' => '', 
				'result' => '', 
				"has_wowed" => $has_activity['has_wowed'],
				"has_cheered" => $has_activity['has_cheered'],
				"has_thanked" => $has_activity['has_thanked'],
				"total_wow" => $total['total_wow'],
				"total_cheer" => $total['total_cheer'],
				"total_thanks" => $total['total_thanks']
			);
		echo json_encode($result_obj);
		return "";
	}

	function thanks_news() {
		$news_aid = trim($this->input->get_post('news_aid'));
		$status = trim($this->input->get_post('status'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_user_activity_model,'user_activity');
		if ($status == '0') {
			$this->user_activity->do_unthanks($news_aid, $user_aid);
		}
		else {
			$this->user_activity->do_thanks($news_aid, $user_aid);
		}
		$this->news->update_all_total($news_aid);

		$has_activity = $this->user_activity->has_activity($news_aid, $user_aid);
		$total = $this->news->get_total_activity($news_aid);
		$result_obj = array(
				'status' => 'success',
				'msg' => '', 
				'result' => '', 
				"has_wowed" => $has_activity['has_wowed'],
				"has_cheered" => $has_activity['has_cheered'],
				"has_thanked" => $has_activity['has_thanked'],
				"total_wow" => $total['total_wow'],
				"total_cheer" => $total['total_cheer'],
				"total_thanks" => $total['total_thanks']
			);
		echo json_encode($result_obj);
		return "";
	}

	function set_news_status() {
		$news_aid = trim($this->input->get_post('news_aid'));
		$status = trim($this->input->get_post('status'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$user_role_aid = get_array_value($login_history,"user_role_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');

		$this->load->model($this->news_model,'news');
		$this->news->set_where(array('aid' => $news_aid));
		$this_comment = $this->news->load_record(false);
		if (!is_var_array($this_comment)) {
			$result_obj = array('status' => 'error','msg' => 'News not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 3: Take action ---*/
		// 3.1 Check permission
		$has_authorized = false;
		if ($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3) {
			$has_authorized = true;
		}

		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		// 3.2 Process action
		if ($status == '1') {
			$data_update = array('status' => '1');
		}
		else {
			$data_update = array('status' => '0');
		}
		$this->news->set_where(array('aid' => $news_aid));
		$this->news->update_record($data_update);

		$this->news->set_where(array('aid' => $news_aid));
		$this_news = $this->news->load_record(false);
		$result_obj = array(
			'status' => 'success',
			'msg' => '',
			'result' => '',
			'news_status' => get_array_value($this_news, 'status', '0')
		);
		echo json_encode($result_obj);
		return "";
	}

	function get_comments() {
		$news_aid = trim($this->input->get_post('news_aid'));
		$order_by = trim($this->input->get_post('order_by'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		switch ($order_by) {
			default:
			case 'created_date_asc': 
				$order_by = 'ASC';
				break;
			
			case 'created_date_desc': 
				$order_by = 'DESC';
				break;
		}

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_comment_model,'comment');
		$this->comment->set_where(array('parent_news_aid' => $news_aid, 'parent_comment_aid' => 0));
		if(!exception_about_status()) $this->comment->set_where(array("status"=>'1'));
		$this->comment->set_order_by(array('created_date' => $order_by));
		$rs = $this->comment->load_records(true, array('user_aid' => $user_aid));
		$num_rows = get_array_value($rs, 'num_rows', 0);
		$results = get_array_value($rs, 'results', 0);
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
				unset($results[$k]['news_title']);
				unset($results[$k]['news_title_short']);
			}
		}
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'result' => ($num_rows > 0 ? $results : ''),
			);
		echo json_encode($result_obj);
		return "";
	}
	
	function add_comment() {
		$news_aid = trim($this->input->get_post('news_aid'));
		$comment = trim($this->input->get_post('comment'));
		$parent_comment_aid = trim($this->input->get_post('parent_comment_aid'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($news_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify news_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($news_aid)){
			if(!is_number_no_zero($news_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : news_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		if(is_blank($comment)){
			$result_obj = array('status' => 'error','msg' => 'Comment cannot be empty.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($parent_comment_aid)){
			if(!is_number_no_zero($parent_comment_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : parent_comment_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$parent_comment_aid = 0;
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_comment_model,'comment');
		$comment_aid = $this->comment->insert_record(
			array(
					'comment' => strip_tags($comment, '<br>'),
					'parent_news_aid' => $news_aid,
					'parent_comment_aid' => $parent_comment_aid,
					'created_by' => $user_aid,
					'updated_by' => $user_aid
				)
			);

		// Update total comment
		$this->news->update_total_comment($news_aid);
		$total = $this->news->get_total_activity($news_aid);
		
		if ($comment_aid > 0) {
			$this->comment->set_where(array('aid' => $comment_aid));
			$result = $this->comment->load_record(true);
			if (is_var_array($result)) {
				unset($result['avatar_tiny']);
				unset($result['avatar_mini']);
				unset($result['user_info']);
				unset($result['news_title']);
				unset($result['news_title_short']);
			}
			$result_obj = array(
				'status' => 'success',
				'msg' => '',
				'total_comment' => $total['total_comment'],
				'result' => (is_var_array($result) ? $result : '')
			);
		}
		else {
			$result_obj = array(
				'status' => 'error',
				'msg' => 'Unable to insert this record into the database because of some technical issues. Please try again.',
				'result' => ''
			);
		}
		echo json_encode($result_obj);
		return "";
	}

	function delete_comment() {
		$comment_aid = trim($this->input->get_post('comment_aid'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$user_role_aid = get_array_value($login_history,"user_role_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($comment_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify comment_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($comment_aid)){
			if(!is_number_no_zero($comment_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : comment_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_comment_model,'comment');
		$this->comment->set_where(array('aid' => $comment_aid));
		$this_comment = $this->comment->load_record(false);
		if (!is_var_array($this_comment)) {
			$result_obj = array('status' => 'error','msg' => 'Comment not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 3: Take action ---*/
		$news_aid = get_array_value($this_comment, 'parent_news_aid', '');
		$comment_user_aid = get_array_value($this_comment, 'created_by', '');

		// 3.1 Check permission
		$has_authorized = false;
		if ($comment_user_aid == $user_aid) {
			$has_authorized = true;
		}
		else if ($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3) {
			$has_authorized = true;
		}

		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		// 3.2 Process action
		$this->comment->set_where(array('aid' => $comment_aid));
		$rs = $this->comment->delete_records();

		// Update total comment
		$this->news->update_total_comment($news_aid);
		$total = $this->news->get_total_activity($news_aid);
		
		if ($rs) {
			$result_obj = array(
				'status' => 'success',
				'msg' => '',
				'total_comment' => $total['total_comment']
			);
		}
		else {
			$result_obj = array(
				'status' => 'warning',
				'msg' => 'No record deleted.',
				'total_comment' => $total['total_comment']
			);
		}
		echo json_encode($result_obj);
		return "";
	}

	function wow_comment() {
		$comment_aid = trim($this->input->get_post('comment_aid'));
		$status = trim($this->input->get_post('status'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($comment_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify comment_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($comment_aid)){
			if(!is_number_no_zero($comment_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : comment_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_comment_model,'comment');
		$this->load->model($this->news_comment_user_activity_model,'user_comment_activity');
		if ($status == '0') {
			$this->user_comment_activity->do_unwow($comment_aid, $user_aid);
		}
		else {
			$this->user_comment_activity->do_wow($comment_aid, $user_aid);
		}
		$this->comment->update_total_wow($comment_aid);

		$has_wowed = $this->user_comment_activity->has_wowed($comment_aid, $user_aid);
		$total_wow = $this->comment->get_total_wow($comment_aid);
		$result_obj = array(
				'status' => 'success',
				'msg' => '', 
				'result' => '', 
				"has_wowed" => $has_wowed,
				"total_wow" => $total_wow
			);
		echo json_encode($result_obj);
		return "";
	}

	function set_comment_status() {
		$comment_aid = trim($this->input->get_post('comment_aid'));
		$status = trim($this->input->get_post('status'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
			$user_role_aid = get_array_value($login_history,"user_role_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($comment_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify comment_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($comment_aid)){
			if(!is_number_no_zero($comment_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : comment_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		$this->load->model($this->user_model,'user');
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		if (!is_var_array($this_user)) {
			$result_obj = array('status' => 'error','msg' => 'User not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		$user_role_aid = get_array_value($this_user, 'user_role_aid', '');

		$this->load->model($this->news_model,'news');
		$this->load->model($this->news_comment_model,'comment');
		$this->comment->set_where(array('aid' => $comment_aid));
		$this_comment = $this->comment->load_record(false);
		if (!is_var_array($this_comment)) {
			$result_obj = array('status' => 'error','msg' => 'Comment not found.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 3: Take action ---*/
		$news_aid = get_array_value($this_comment, 'parent_news_aid', '');
		$comment_user_aid = get_array_value($this_comment, 'created_by', '');

		// 3.1 Check permission
		$has_authorized = false;
		if ($comment_user_aid == $user_aid) {
			$has_authorized = true;
		}
		else if ($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3) {
			$has_authorized = true;
		}

		if ($has_authorized !== true) {
			$result_obj = array('status' => 'error','msg' => 'Permission denied.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		// 3.2 Process action
		if ($status == '1') {
			$data_update = array('status' => '1');
		}
		else {
			$data_update = array('status' => '0');
		}
		$this->comment->set_where(array('aid' => $comment_aid));
		$this->comment->update_record($data_update);

		// Update total comment
		$this->news->update_total_comment($news_aid);
		$total = $this->news->get_total_activity($news_aid);

		$this->comment->set_where(array('aid' => $comment_aid));
		$this_comment = $this->comment->load_record(false);
		$result_obj = array(
			'status' => 'success',
			'msg' => '',
			'result' => '',
			'comment_status' => get_array_value($this_comment, 'status', '0'),
			'total_comment' => $total['total_comment']
		);
		echo json_encode($result_obj);
		return "";
	}

	function get_list_top_commenters() {
		$offset = trim($this->input->get_post('offset'));
		$total_load = trim($this->input->get_post('total_load'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($offset)){
			if(!is_number_no_zero($offset)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : offset must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$offset = 0;
		}
		if(!is_blank($total_load)){
			if(!is_number_no_zero($total_load)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : total_load must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$total_load = 3;
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->view_most_comments_model,'most_comments');
		$rs = $this->most_comments->load_top_commenters($offset, $total_load);
		$num_rows = get_array_value($rs, 'num_rows', 0);
		$results = get_array_value($rs, 'results', 0);
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
			}
		}
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'result' => ($num_rows > 0 ? $results : ''),
			);
		echo json_encode($result_obj);
		return "";
	}
	
	function get_list_recommended() {
		$offset = trim($this->input->get_post('offset'));
		$total_load = trim($this->input->get_post('total_load'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($offset)){
			if(!is_number_no_zero($offset)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : offset must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$offset = 0;
		}
		if(!is_blank($total_load)){
			if(!is_number_no_zero($total_load)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : total_load must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$total_load = 3;
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,'news');
		$rs = $this->news->load_recommended('1', $offset, $total_load);
		$num_rows = get_array_value($rs, 'num_rows', 0);
		$results = get_array_value($rs, 'results', 0);
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['news_main_aid']);
				unset($results[$k]['news_main_name']);
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
				unset($results[$k]['short_description']);
				unset($results[$k]['very_short_description']);
			}
		}
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'result' => ($num_rows > 0 ? $results : ''),
			);
		echo json_encode($result_obj);
		return "";
	}
	
	function get_list_talk_of_the_town() {
		$offset = trim($this->input->get_post('offset'));
		$total_load = trim($this->input->get_post('total_load'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($offset)){
			if(!is_number_no_zero($offset)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : offset must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$offset = 0;
		}
		if(!is_blank($total_load)){
			if(!is_number_no_zero($total_load)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : total_load must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$total_load = 3;
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,'news');
		$rs = $this->news->load_talk_of_the_town('1', $offset, $total_load);
		$num_rows = get_array_value($rs, 'num_rows', 0);
		$results = get_array_value($rs, 'results', 0);
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['news_main_aid']);
				unset($results[$k]['news_main_name']);
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
				unset($results[$k]['short_description']);
				unset($results[$k]['very_short_description']);
			}
		}
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'result' => ($num_rows > 0 ? $results : ''),
			);
		echo json_encode($result_obj);
		return "";
	}
	
	function get_list_popular() {
		$offset = trim($this->input->get_post('offset'));
		$total_load = trim($this->input->get_post('total_load'));
		
		/*--- Step 1: Verify parameters ---*/
		$this->check_device();
		
		$user_aid = "";
		$login_history = $this->check_token();
		if(is_var_array($login_history)){
			$user_aid = get_array_value($login_history,"user_aid","0");
		}
		if(is_blank($user_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Invalid token.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($offset)){
			if(!is_number_no_zero($offset)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : offset must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$offset = 0;
		}
		if(!is_blank($total_load)){
			if(!is_number_no_zero($total_load)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : total_load must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		else {
			$total_load = 3;
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		

		/*--- Step 3: Take action ---*/
		$this->load->model($this->news_model,'news');
		$rs = $this->news->load_popular('1', $offset, $total_load);
		$num_rows = get_array_value($rs, 'num_rows', 0);
		$results = get_array_value($rs, 'results', 0);
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['news_main_aid']);
				unset($results[$k]['news_main_name']);
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
				unset($results[$k]['short_description']);
				unset($results[$k]['very_short_description']);
			}
		}
		$result_obj = array(
				'status' => ($num_rows > 0 ? 'success' : 'warning'),
				'msg' => ($num_rows > 0 ? '' : 'No record found.'),
				'total_records' => $num_rows,
				'result' => ($num_rows > 0 ? $results : ''),
			);
		echo json_encode($result_obj);
		return "";
	}

	function isNewsCodeExits($cid){
		$this->load->model($this->news_model, "main");
		$this->main->set_where(array("cid"=>$cid));
		$total = $this->main->count_records();
		if($total> 0){
			return true;
		}else{
			return false;
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

	
}

?>