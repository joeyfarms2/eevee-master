<?php
class Usmarc_subfield_dm_model extends Initmodel {
	
	function __construct()
	{
		// Call the Model constructor
        parent::__construct();
		$this->set_table_name("usmarc_subfield_dm");
		
		$this->tbl_usmarc_tag_name = "usmarc_tag_dm";
		$this->tbl_usmarc_block_name = "usmarc_block_dm";
	}
	
	function set_join_for_desc($obj="")
	{
		$this->db->select('usmarc_subfield_dm.*, tag.description as tag_description, tag.ind1_description, tag.ind2_description, tag.repeatable_flg as tag_repeatable_flg, block.block_nmbr, block.description as block_description');
		$this->db->join($this->tbl_usmarc_tag_name.' AS tag', 'usmarc_subfield_dm.tag = tag.tag', "left");
		$this->db->join($this->tbl_usmarc_block_name.' AS block', 'tag.block_nmbr = block.block_nmbr', "left");
	}
	
	function fetch_data_with_desc($query)
	{
		$result = "";
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$row["num_rows"] = $query->num_rows() ;
				$result[] = $row;
			}
		}
		return $result;
	}
}

/* End of file usmarc_subfield_dm_model.php */
/* Location: ./system/application/model/usmarc_subfield_dm_model.php */