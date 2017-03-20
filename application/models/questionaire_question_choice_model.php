<?php
class Questionaire_question_choice_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("questionaire_question_choice");	
			$this->tbl_questionaire_user_activity = "questionaire_user_activity";
			$this->tbl_user_name = 'user';
	}
	
	
}

/* End of file questionaire_question_choice_model.php */
/* Location: ./system/application/model/questionaire_question_choice_model.php */