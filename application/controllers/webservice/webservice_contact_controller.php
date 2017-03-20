<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/webservice/init_webservice_controller.php");

class Webservice_contact_controller extends Init_webservice_controller {

	function __construct()
	{
		parent::__construct();
		
		$this->contact_msg_model = 'Contact_msg_model';
		$this->contact_topic_model = 'Contact_topic_model';
	}

	function get_contact_topic()
	{
		$this->check_device();

		$this->load->model($this->contact_topic_model,"contact_topic");
		$tmp = array();
		$tmp['status'] = "1";
		$this->contact_topic->set_where($tmp);
		$this->contact_topic->set_order_by("weight ASC");
		$result_list = $this->contact_topic->load_records(false);
		if(is_var_array($result_list)){
			$result = array();
			foreach($result_list as $item){
				// print_r($item);echo "<HR>";
				$obj = array();
				$obj["contact_topic_aid"] = get_array_value($item,"aid","");
				$obj["contact_topic_name"] = get_array_value($item,"name","");
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

	function save_contact()
	{	
		$device = trim($this->input->get_post('device'));
		$device_id = trim($this->input->get_post('device_id'));
		$this->check_device();

		$login_history = $this->check_token(false);
		$user_aid = get_array_value($login_history,"user_aid","");

		$first_name_th = trim($this->input->get_post('first_name_th'));
		$last_name_th = trim($this->input->get_post('last_name_th'));
		$contact_topic_aid = trim($this->input->get_post('contact_topic_aid'));
		$email = trim($this->input->get_post('email'));
		$contact_subject = trim($this->input->get_post('subject'));
		$message = trim($this->input->get_post('message'));
		if(is_blank($first_name_th)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify first_name_th.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($last_name_th)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify last_name_th.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($contact_topic_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify contact_topic_aid.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(!is_number_no_zero($contact_topic_aid)){
			$result_obj = array("status" => 'error',"msg" => 'Incorrect data type : contact_topic_aid must be integer.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($email)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify email.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($contact_subject)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify subject.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		if(is_blank($message)){
			$result_obj = array("status" => 'error',"msg" => 'Parameter missing : Please specify message.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}

		$this->lang->load('mail');

		$this->load->model($this->contact_topic_model,"contact_topic");
		$this->contact_topic->set_where(array("aid"=>$contact_topic_aid));
		$contact_topic_result = $this->contact_topic->load_record(false);
		if(!is_var_array($contact_topic_result)){
			$result_obj = array("status" => 'warning',"msg" => 'No contact topic found.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
		$contact_topic_name = get_array_value($contact_topic_result,"name","N/A");
		$contact_topic_url = get_array_value($contact_topic_result,"url","N/A");
		$contact_topic_email = get_array_value($contact_topic_result,"email", MAIN_CONTACT_EMAIL);

		$subject = $this->lang->line('mail_subject_contact_confirm_to_user');
		$body = $this->lang->line('mail_content_contact_confirm_to_user');

		$subject = str_replace("{name}", trim($first_name_th.' '.$last_name_th) , $subject);

		$body = str_replace("{doc_type}", "&nbsp;" , $body);
		$body = str_replace("{name}", trim($first_name_th.' '.$last_name_th) , $body);
		$body = str_replace("{email}", $email, $body);
		$body = str_replace("{subject}", $contact_subject, $body);
		$body = str_replace("{message}", $message, $body);
		$body = str_replace("{topic_name}", $contact_topic_name, $body);

		// echo "body = $body";

		$data["first_name_th"] = $first_name_th;
		$data["last_name_th"] = $last_name_th;
		$data["contact_topic_aid"] = $contact_topic_aid;
		$data["contact_topic_name"] = $contact_topic_name;
		$data["email"] = $email;
		$data["subject"] = $contact_subject;
		$data["message"] = $message;
		$data["user_aid"] = $user_aid;
		$data["device"] = $device;

		$obj = "";
		$obj["contact_topic_aid"] = $contact_topic_aid;
		$obj["data"] = serialize($data);

		$this->load->model($this->contact_msg_model,"contact");
		$aid = $this->contact->insert_record($obj);
		$data["aid"] = $aid;

		$this->load->library('email');
		$config = $this->get_init_email_config();
		if(is_var_array($config)){ 
			$this->email->initialize($config); 
			$this->email->set_newline("\r\n");
		}
		
		$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
		$this->email->to($email);
		$this->email->bcc($contact_topic_email);

		$this->email->subject($subject);
		$this->email->message($body);
		//echo $this->email->print_debugger();
		$this->log_debug('Webservice : save_contact', '['.$email.'] '.$body);
		if(@$this->email->send()){
			$this->log_status('Webservice : save_contact', 'Email sent success.', $data);
			$result_obj = array("status" => 'success',"msg" => '', "result" => '1');
			echo json_encode($result_obj);
			return "";
		}else{
			$result_obj = array("status" => 'error',"msg" => 'Sorry, The system can not send email right now.<BR>Please try again later or contact administrator to solve the problem.', "result" => '');
			echo json_encode($result_obj);
			return "";
		}
	}

}

?>