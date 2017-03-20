 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."controllers/project_".CONST_PROJECT_CODE."/project_init_controller.php");

class Event_init_controller extends Project_init_controller {

	function __construct(){
		parent::__construct();

		$this->event_main_model = "Event_main_model";
		$this->event_categpry_model = "Event_categpry_model";
		$this->event_model = "Event_model";

		$this->load->model($this->event_main_model,"event_main");
		$this->data["master_event_main"] = $this->event_main->load_event_mains();

	}
	
	function index(){
			return "";
	}

}

?>