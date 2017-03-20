<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* is_match_role
*
* @access	public
* @param	string	current_role
* @param array,string	match_role
* @return	bool
* @author		Tisa Pathumwan
 */
if ( ! function_exists('is_match_role'))
{
	function is_match_role($current_role, $match_role){
	
		if(is_array($match_role)){
		
			for($i=0; $i<count($match_role); $i++){
				if($current_role == $match_role[$i]){
					return true;
				}
			}
			
			//show_error('Sorry, You can not access this page.');
			redirect('home/status/'.md5('permission'));
			
		}else{
		
			if($current_role == $match_role){
				return true;
			}else{
				//show_error('Sorry, You can not access this page.');
				redirect('home/status/'.md5('permission'));
			}
		
		}//else
	}
}
// ------------------------------------------------------------------------


/* End of file permission_helper.php */
/* Location: ./system/helpers/permission_helper.php */