<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_event_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->event_model = 'Event_model';
		$this->event_main_model = 'Event_main_model';
		$this->event_category_model = 'Event_category_model';
		$this->event_gallery_model = 'Event_gallery_model';
		$this->event_user_activity_join_model = 'Event_user_activity_join_model';
		$this->user_model = 'User_model';
		
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
		$this->load->model($this->event_category_model,"event_cat");
		$this->event_cat->set_where(array('status' => '1'));
		$rs = $this->event_cat->load_records(false);
		$num_rows = count($rs);
		$results = $rs;
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['event_main_aid']);
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

	function get_incoming_events() {
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

		$this->load->model($this->user_model,"user");
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		$user_role_aid = '';
		if (is_var_array($this_user)) {
			$user_role_aid = get_array_value($this_user, 'user_role_aid', '0');
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
		$this->load->model($this->event_model,"event");
		if(!is_blank($total_records_in_this_cat)){
			if(!is_number_no_zero($total_records_in_this_cat)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : total_records_in_this_cat must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		if ($page == '1' || is_blank($total_records_in_this_cat)) {
			$total_records_in_this_cat = 0;
			$tmp_rs = $this->event->load_home($arr_category_aid, '1', 3, 0, $user_aid, $user_role_aid);
			if (isset($tmp_rs['num_rows'])) {
				$total_records_in_this_cat = $tmp_rs['num_rows'];
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/
		


		/*--- Step 3: Take action ---*/
		$next_page = '';
		$tmp_rs = $this->event->load_home($arr_category_aid, '1' , $total_load, (($page-1)*$total_load), $user_aid, $user_role_aid);
		$num_rows = get_array_value($tmp_rs, 'num_rows', 0);
		$results = get_array_value($tmp_rs, 'results', '');
		if ($total_records_in_this_cat > ($page * $total_load)) {
			$next_page = $page+1;
		}
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['event_main_aid']);
				unset($results[$k]['event_main_name']);
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
				unset($results[$k]['total_view']);
				unset($results[$k]['total_comment']);
				unset($results[$k]['total_wow']);
				unset($results[$k]['total_cheer']);
				unset($results[$k]['total_thanks']);
				// unset($results[$k]['short_description']);
				// unset($results[$k]['very_short_description']);
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
	
	function get_all_events_by_period() {
		$category_aid = $this->input->get_post('category_aid');
		$period = $this->input->get_post('period');
		$period_start = $this->input->get_post('period_start');
		$period_end = $this->input->get_post('period_end');
		
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

		$this->load->model($this->user_model,"user");
		$this->user->set_where(array('aid' => $user_aid));
		$this_user = $this->user->load_record(false);
		$user_role_aid = '';
		if (is_var_array($this_user)) {
			$user_role_aid = get_array_value($this_user, 'user_role_aid', '0');
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
		if(!is_blank($period)){
			switch ($period) {
				default:
				case 'this_month':
					$date = new DateTime(); 
					// $date->modify('+2 month');
					$period_start = $date->modify('first day of this month')->format('Y-m-d 00:00:00');
					$period_end = $date->modify('last day of this month')->format('Y-m-d 23:59:59');
					break;
				case 'this_week':
					// Start with Monday, if wanna start with Sunday use $date->modify('this week')->modify('-1 day')
					$date = new DateTime(); 
					$period_start = $date->modify('this week')->format('Y-m-d 00:00:00').'<br><br/>';
					$period_end =  $date->modify('+6 days')->format('Y-m-d 23:59:59').'<br><br/>';
					break;
				case 'today':
					$date = new DateTime(); 
					$period_start = $date->format('Y-m-d 00:00:00').'<br><br/>';
					$period_end =  $date->format('Y-m-d 23:59:59').'<br><br/>';
					break;
			}
		}
		else {
			if(is_blank($period_start)){
				$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify period_start in date format. (e.g. \'2014-12-01\')', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
			$period_start = trim($period_start).' 00:00:00';
			if(is_blank($period_end)){
				$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify period_end in date format. (e.g. \'2014-12-30\')', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
			$period_end = trim($period_end).' 23:59:59';
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->event_model,"event");
		$rs = $this->event->load_by_period($arr_category_aid, '1', TRUE, $period_start, $period_end, $user_aid, $user_role_aid);
		$num_rows = get_array_value($rs, 'num_rows', 0);
		$results = get_array_value($rs, 'results', '');
		if ($num_rows > 0) {
			foreach ($results as $k=>$item) {
				unset($results[$k]['event_main_name']);
				unset($results[$k]['avatar_tiny']);
				unset($results[$k]['avatar_mini']);
				unset($results[$k]['user_info']);
				// unset($results[$k]['short_description']);
				// unset($results[$k]['very_short_description']);
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
	
	function get_event_detail() {
		$event_aid = $this->input->get_post('event_aid');
		
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
		if(is_blank($event_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify event_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($event_aid)){
			if(!is_number_no_zero($event_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : event_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->event_model,"event");
		$this->event->set_where(array('aid' => $event_aid));
		$result = $this->event->load_record(true);
		$result_obj = array(
					'status' => (is_var_array($result) ? 'success' : 'warning'),
					'msg' => (is_var_array($result) ? '' : 'No record found.'),
					'result' => ''
				);
		if (is_var_array($result)) {
			unset($result['event_main_aid']);
			unset($result['event_main_name']);
			unset($result['avatar_tiny']);
			unset($result['avatar_mini']);
			unset($result['user_info']);
			unset($result['activity_join_user_aid']);
			unset($result['activity_join_has_joined']);
			unset($result['status_name']);
			unset($result['short_description']);
			unset($result['very_short_description']);
			$result_obj['result'] = $result;
		}
		echo json_encode($result_obj);
		return "";
	}

	function add_event() {

	}

	function update_event() {

	}

	function delete_event() {
		$event_aid = trim($this->input->get_post('event_aid'));
		
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
		if(is_blank($event_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify event_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($event_aid)){
			if(!is_number_no_zero($event_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : event_aid must be integer.', 'result' => '');
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

		$this->load->model($this->event_model,'event');
		$this->event->set_where(array('aid' => $event_aid));
		$rs = $this->event->load_record(false);
		if (!is_var_array($rs)) {
			$result_obj = array('status' => 'error','msg' => 'Event not found.', 'result' => '');
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
		$this->event->set_where(array('aid' => $event_aid));
		$rs = $this->event->delete_records();

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

	function accept_invitation() {
		$event_aid = trim($this->input->get_post('event_aid'));
		$join_status = trim($this->input->get_post('join_status'));
		
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
		if(is_blank($event_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify event_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($event_aid)){
			if(!is_number_no_zero($event_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : event_aid must be integer.', 'result' => '');
				echo json_encode($result_obj);
				return "";
			}
		}
		if(trim($join_status) != '0' && trim($join_status) != '1'){
			$result_obj = array('status' => 'error','msg' => 'Incorrect data : join_status can be either 0 or 1', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}

		/*--- Step 2: Initialize/Pre-define values (if any) ---*/


		/*--- Step 3: Take action ---*/
		$this->load->model($this->event_model,'event');
		$this->load->model($this->event_user_activity_join_model,'user_activity');
		$this->user_activity->do_join($event_aid, $user_aid, $join_status);
		$this->event->update_total_join($event_aid);

		$has_joined = $this->user_activity->has_joined($event_aid, $user_aid);
		$total = $this->event->get_total_activity($event_aid);
		$result_obj = array(
				'status' => 'success',
				'msg' => '', 
				'result' => '', 
				"has_joined" => $has_joined,
				"total_joins" => $total['total_join']
			);
		echo json_encode($result_obj);
		return "";
	}

	function set_event_status() {
		$event_aid = trim($this->input->get_post('event_aid'));
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
		if(is_blank($event_aid)){
			$result_obj = array('status' => 'error','msg' => 'Parameter missing : Please specify event_aid.', 'result' => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_blank($event_aid)){
			if(!is_number_no_zero($event_aid)){
				$result_obj = array('status' => 'error','msg' => 'Incorrect data type : event_aid must be integer.', 'result' => '');
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

		$this->load->model($this->event_model,'event');
		$this->event->set_where(array('aid' => $event_aid));
		$this_comment = $this->event->load_record(false);
		if (!is_var_array($this_comment)) {
			$result_obj = array('status' => 'error','msg' => 'Event not found.', 'result' => '');
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
		$this->event->set_where(array('aid' => $event_aid));
		$this->event->update_record($data_update);

		$this->event->set_where(array('aid' => $event_aid));
		$rs = $this->event->load_record(false);
		$result_obj = array(
			'status' => 'success',
			'msg' => '',
			'result' => '',
			'event_status' => get_array_value($rs, 'status', '0')
		);
		echo json_encode($result_obj);
		return "";
	}

}

?>