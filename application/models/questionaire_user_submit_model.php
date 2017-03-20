<?php
class Questionaire_user_submit_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
			$this->set_table_name("questionaire_user_submit");
			$this->tbl_questionaire_question = 'questionaire_question';
			$this->tbl_questionaire_question_choice = 'questionaire_question_choice';
			$this->tbl_user_name = 'user';
	}

	function load_export_data($questionaire_aid=""){
		if (empty($questionaire_aid)) return "";
		// if(!exception_about_status()) $tmp["status"] = "1";

		$this->db->select('
				question.title AS question_title,
				question.question_type,
				question.weight,
				user_submit.*, 
				user.username , 
				user.first_name_th, 
				user.last_name_th
			');
		$this->db->from($this->tbl_name.' AS user_submit');
		$this->db->join($this->tbl_questionaire_question.' AS question', 'user_submit.question_aid = question.aid', "left");
		$this->db->join($this->tbl_user_name.' AS user', 'user_submit.user_aid = user.aid', "left");
		$this->db->where('question.questionaire_aid', $questionaire_aid, false);
		$this->set_group_by("user_submit.questionaire_aid, user_submit.question_aid, user_submit.user_aid");
		$this->set_order_by("user.first_name_th ASC, user.last_name_th ASC, question.weight ASC");
		$query = $this->db->get();
		// return $this->fetch_data_with_desc($query);

		$result = "";
		if($query->num_rows() > 0) {
			$result = array();
			foreach($query->result_array() as $row) {
				$row['full_name'] = get_array_value($row, 'first_name_th').' '.get_array_value($row, 'last_name_th');
				if ($row['question_type'] == 'rdo' || $row['question_type'] == 'chk') {
					if (!empty($row['answer']))
						$tmp_answer = @unserialize($row['answer']);
						if (is_var_array($tmp_answer)) {
							$row['answer'] = implode(', ', $tmp_answer);
						}
				}
				$result[] = $row;
			}
		}
		return $result;
	}
		
}

/* End of file questionaire_user_submit_model.php */
/* Location: ./system/application/model/questionaire_user_submit_model.php */