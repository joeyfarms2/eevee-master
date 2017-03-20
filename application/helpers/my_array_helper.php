<?php
/**
 * Array Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Tisa Pathumwan
 * 
 * 
 * 
 * 
 * 
 */

// ------------------------------------------------------------------------

function is_var_array($arr){
	if(isset($arr) && is_array($arr) && count($arr)>0 ){
		return true;
	}else{
		return false;
	}
}

function get_array_value($arr, $key="", $init_val=""){
	if(is_var_array($arr) && $key != "" && array_key_exists($key, $arr) && !is_blank($arr[$key])){
		return $arr[$key];
	}else{
		if($init_val != ""){
			return $init_val;
		}else{
			return "";
		}
	}
}

function get_array_by_key_with_value($array, $key="",$value=""){
	if(!is_var_array($array) || is_blank($key)){
		return "";
	}
	$result = "";
	foreach($array as $item){
		$value_by_key = get_array_value($item,$key,"");
		// echo "value_by_key = $value_by_key <BR>";
		if($value_by_key == $value){
			$result[] = $item;
		}
	}
	return $result;
}

function is_in_array($val, $arr){
	if(is_var_array($arr)){
		return in_array($val,$arr);
	}else{
		return false;
	}
}
function chack_data($data){

		$abc = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
    	$all = count($abc);
    			for($k=0;$k < $all; $k++){
					if($abc[$k] == $data){
						//echo "555555555555";
						return true;
					}

				}
				return false;


	}
        
function is_padding(){
                $u_agent= $_SERVER['HTTP_USER_AGENT'] . "\n\n";
                if(preg_match('/Firefox/i',$u_agent))
                {
                    $padding = 31;
                }elseif(preg_match('/Edge/i',$u_agent))
                {
                    $padding = 31;
                }
                else{
                     $padding = 30;
                }
                return $padding;
}

function is_paddingType2(){
                $u_agent= $_SERVER['HTTP_USER_AGENT'] . "\n\n";
                if(preg_match('/Firefox/i',$u_agent))
                {
                    $padding = 28;
                }elseif(preg_match('/Edge/i',$u_agent))
                {
                    $padding = 28;
                }
                else{
                     $padding = 27;
                }
                return $padding;
}

function is_top(){
                $u_agent= $_SERVER['HTTP_USER_AGENT'] . "\n\n";
                if(preg_match('/Firefox/i',$u_agent))
                {
                    $page_top = 18;
                }
                elseif(preg_match('/Edge/i',$u_agent))
                {
                    $page_top = 18;
                } else{
                    $page_top = 20;
                }
                return $page_top;
}

function is_checktop(){
                $u_agent= $_SERVER['HTTP_USER_AGENT'] . "\n\n";
                if(preg_match('/Firefox/i',$u_agent))
                {
                    $page_top = 14;
                }
                elseif(preg_match('/Edge/i',$u_agent))
                {
                    $page_top = 14;
                } else{
                    $page_top = 15;
                }
                return $page_top;
}

?>
