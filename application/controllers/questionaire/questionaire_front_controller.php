<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/initcontroller.php");

class Questionaire_front_controller extends Initcontroller {

	function __construct(){
		parent::__construct();
		$this->data["mode"] = "front";
		
		if(CONST_HAS_QUESTIONAIRE != "1"){
			redirect('home');
		}

		define("thisFrontTabMenu",'questionaire');
		define("thisFrontSubMenu",'');
		@define("folderName",'questionaire/');
		
		$this->questionaire_model = 'Questionaire_model';	
		$this->questionaire_question_model = 'Questionaire_question_model';	
		$this->questionaire_question_choice_model = 'Questionaire_question_choice_model';	
		$this->questionaire_category_model = 'Questionaire_category_model';	
		$this->questionaire_user_submit_model = 'Questionaire_user_submit_model';	
		$this->questionaire_user_activity_model = 'Questionaire_user_activity_model';	
		$this->user_model = 'User_model';
		$this->user_department_model = 'User_department_model';
		
		$this->lang->load('questionaire');
		
	}
	
	function index(){
		$this->home();
	}
	
	function home($questionaire_main_url=""){
		@define("thisAction","home");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = '<span class="textStart">Questionnaires</span>';
		$this->data["view_the_content"] = $this->default_theme_front . '/questionaire/questionaire_home';
		
		$this->load->model($this->questionaire_category_model,"questionaire_cat");
		$this->data['questionaire_cat_result'] = $this->questionaire_cat->load_questionaire_categories();

		$this->load->model($this->questionaire_model,"questionaire");

		$this->load->model($this->questionaire_category_model,"questionaire_category");
		$this->questionaire_category->set_where(array("status"=>"1"));
		$this->questionaire_category->set_order_by("weight ASC, name ASC");
		$questionaire_category_list = $this->questionaire_category->load_records(false);
		$questionaire_home_result = "";
		if(is_var_array($questionaire_category_list)){
			foreach($questionaire_category_list as $item){
				$questionaire_category_aid = get_array_value($item,"aid","0");
				$questionaire_list = $this->questionaire->load_home($questionaire_category_aid, 5);
				$item["questionaire_list"] = $questionaire_list['results'];
				$questionaire_home_result[$questionaire_category_aid] = $item;
			}
		}

		// echo '<pre>';
		// print_r($questionaire_home_result);
		// echo '</pre>';
		
		$this->data["questionaire_cat_result"] = $questionaire_category_list;
		$this->data["questionaire_home_result"] = $questionaire_home_result;
				
		$this->load->view($this->default_theme_front . '/tpl_questionaire', $this->data);
	}

	function form($aid){
		@define("thisAction","form");
		$this->data["title"] = DEFAULT_TITLE;
		$this->data["page_title"] = '<span class="textStart">Questionnaires</span>';
		$this->data["view_the_content"] = $this->default_theme_front . '/questionaire/questionaire_form';
				
		$this->db->flush_cache();
		$this->load->model($this->questionaire_model, "questionaire");
		$this->load->model($this->questionaire_question_model, "question");
		$this->load->model($this->questionaire_question_choice_model, "choice");
		$this->load->model($this->questionaire_user_activity_model, "user_activity");

		$this->user_activity->set_where(array('questionaire_aid' => $aid, 'user_aid' => getUserLoginAid($this->user_login_info)));
		$this_user = $this->user_activity->load_record(false);
		if (is_var_array($this_user) && isset($this_user['user_aid']) && ($this_user['user_aid'] > 0)) {
			if ($this_user['has_submitted'] == '1') {
				redirect('questionaire/status/'.md5('already-submitted'));
			}
			else {
				$result = $this->questionaire->increase_total_view($aid);
			}
		}
		else {
			redirect('questionaire/status/'.md5('no-access'));
		}

		$this->questionaire->set_where(array("aid"=>$aid, "user_owner_aid"=>getUserOwnerAid($this)));
		$item_detail = $this->questionaire->load_record(false);

		if(is_var_array($item_detail)){
			$this->data["item_detail"] = $item_detail;

			// Load questions
			$this->question->set_where('questionaire_aid', $item_detail['aid']);
			$this->question->set_order_by('weight ASC');
			$tmp_q = $this->question->load_records(false);
			
			// Load questions' choices
			$this->choice->set_where('questionaire_aid', $item_detail['aid']);
			$this->choice->set_order_by('weight ASC');
			$tmp_c = $this->choice->load_records(false);

			// Pair question-choices into an array
			$arr_q_and_c = array();
			if (is_var_array($tmp_q) && count($tmp_q) > 0) {
				foreach ($tmp_q as $item_q) {
					$arr_q_and_c[$item_q['aid']] = array();
					$arr_q_and_c[$item_q['aid']]['question'] = $item_q;
					$arr_q_and_c[$item_q['aid']]['choices'] = '';
				}
			}
			if (is_var_array($tmp_c) && count($tmp_c) > 0) {
				foreach ($tmp_c as $item_c) {
					if (isset($arr_q_and_c[$item_c['question_aid']]['choices'])) {
						if (!is_var_array($arr_q_and_c[$item_c['question_aid']]['choices'])) {
							$arr_q_and_c[$item_c['question_aid']]['choices'] = array();
						}
						$arr_q_and_c[$item_c['question_aid']]['choices'][] = $item_c;
					}
				}
			}
			// echo '<pre>';
			// print_r ($arr_q_and_c);
			// echo '<pre>';
			// exit;
			$this->data['questions'] = $arr_q_and_c;
			$this->load->view($this->default_theme_front.'/tpl_questionaire', $this->data);
		}
		else {
			redirect('questionaire/status/'.md5('questionaire-not-found'));
		}
	}
	
	function submit_form() {
		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		$this->load->model($this->questionaire_model, 'questionaire');
		$this->load->model($this->questionaire_user_activity_model, 'user_activity');
		$this->load->model($this->questionaire_user_submit_model, 'user_submit');

		$questionaire_aid = $this->input->get_post('questionaire_aid');

		$this->user_activity->set_where(array('questionaire_aid' => $questionaire_aid, 'user_aid' => getUserLoginAid($this->user_login_info)));
		$this_user = $this->user_activity->load_record(false);
		if (is_var_array($this_user) && isset($this_user['user_aid']) && ($this_user['user_aid'] > 0)) {
			if ($this_user['has_submitted'] == '1') {
				redirect('questionaire/status/'.md5('already-submitted'));
			}
		}
		else {
			redirect('questionaire/status/'.md5('no-access'));
		}


		$return_insert = array();
		$tmp_ans = array();
		$post_ans = $_POST;
		foreach ($post_ans as $key => $value) {
			$tmp = explode('_', $key, 2);
			if ($tmp[0] == 'ans') {
				$tmp_ans[$tmp[1]] = $post_ans[$key];
			}
		}

		foreach ($tmp_ans as $key => $value) {
			$data = array();
			$data['questionaire_aid'] = $questionaire_aid;
			$data['question_aid'] = $key;
			$data['user_aid'] = getUserLoginAid($this->user_login_info);
			if (is_var_array($value)) {
				$data['answer'] = serialize($value);
			}
			else {
				$data['answer'] = trim($value);
			}
			$return_insert[] = $this->user_submit->insert_record($data);
		}
		$this->user_activity->set_where(array('questionaire_aid' => $questionaire_aid, 'user_aid' => getUserLoginAid($this->user_login_info)));
		$this->user_activity->update_record(array('has_submitted' => '1'));

		$this->questionaire->update_total_submit($questionaire_aid);
		// echo '<pre>';
		// print_r($return_insert);
		// echo '</pre>';
		// exit;

		// if (in_array(false, $return_insert)) {
			redirect('questionaire/status/'.md5('submit-success'));
		// }
		// else {
		// 	redirect('questionaire/status/'.md5('submit-error').'/'.$questionaire_aid);
		// }
	}
	
	function status($type="", $param="")	{
		switch($type)
		{
			case md5('success') : 
				$this->data["message"] = set_message_success('Data has been saved.');
				$this->data["js_code"] = '';
				break;
			case md5('submit-success') : 
				$this->data["message"] = set_message_success('<h4>Thank you!</h4>Your response has been recorded.');
				$this->data["js_code"] = '$("#main_content").remove();';
				break;
			case md5('submit-error') : 
				$this->data["message"] = set_message_error('Oops! It looks like something went wrong while saving your data. Please <a href="'.site_url('questionaire/form/'.$param).'">click here to go back to the form</a> and then try again.');
				$this->data["js_code"] = '';
				break;
			case md5('already-submitted') : 
				$this->data["message"] = set_message_error('Oops! It looks like you have already submitted this questionnaire. Please ignore this invitation or <a href="'.site_url('questionaire').'">click here to view all questionnaires</a>.');
				$this->data["js_code"] = '$("#main_content").remove();';
				break;
			case md5('no-access') : 
				$this->data["message"] = set_message_error('Oops! It looks like you does not have authorization to view this questionnaire. Please contact your questionnaire administrator or <a href="'.site_url('questionaire').'">click here to view all questionnaires</a>.');
				$this->data["js_code"] = '$("#main_content").remove();';
				break;
			case md5('no-command') : 
				$this->data["message"] = set_message_error('Command is unclear. Please try again.');
				$this->data["js_code"] = '';
				break;
			case md5('cat-not-found') : 
				$this->data["message"] = set_message_error('Category not found.');
				$this->data["js_code"] = '';
				break;
			case md5('questionaire-not-found') : 
				$this->data["message"] = set_message_error('Questionnaire not found.');
				$this->data["js_code"] = '';
				break;
			default : 
				$this->data["message"] = set_message_error('Please try again');
				$this->data["js_code"] = '';
				break;
		}
		$this->home();
	}

}
?>