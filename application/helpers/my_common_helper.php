<?php 
/****************************************************************/
/* Show web page title
/* Added by: Oui
/****************************************************************/
function show_title($title='') {
	if( !$title || !isset($title) || is_blank($title) ) echo DEFAULT_TITLE;
	else echo $title;
}

function get_language_line($controller, $name, $init_word=""){
	$result = $controller->lang->line($name);
	if(is_blank($result)){
		return $init_word;
	}else{
		return $result;
	}
}

/****************************************************************/
/* Manage Message
/* Added by: Oui
/****************************************************************/
function set_message($status="", $msg="", $sub_header="", $main_header="", $show_close_buttom=true, $box_id="") {
	$show = '';
	$show .= '<div class="alert alert-'.$status.' ';
	if(!is_blank($main_header)){
		$show .= 'alert-block ';
	}
	$show .= ' fade in">';
	if($show_close_buttom){
		$show .= '<button data-dismiss="alert" class="close close-sm" type="button">';
		$show .= '<i class="fa fa-times"></i>';
		$show .= '</button>';
	}
	if(!is_blank($main_header)){
		$show .= '<h4>';
		$show .= '<i class="fa fa-ok-sign"></i>';
		$show .= getTextQuote($main_header);
		$show .= '</h4>';
	}
	if(!is_blank($sub_header)){
		$show .= '<strong>'.getTextQuote($sub_header).'</strong>&nbsp;';
	}
	$show .= '<p>'.getTextQuote($msg).'</p>';
	$show .= '</div>';

	if(is_blank($box_id)){
		$box_id = "result-msg-box";
	}
	$txt = '';
	$txt = "$('#".$box_id."').removeClass('hidden');";
	$txt .= "$('#".$box_id."').html('".$show."');";
	return $txt;
}
function set_message_error($msg="", $sub_header="", $main_header="", $show_close_buttom=true, $box_id="") {
	return set_message("danger", $msg, $sub_header, $main_header, $show_close_buttom, $box_id);
}
function set_message_warning($msg="", $sub_header="", $main_header="", $show_close_buttom=true, $box_id="") {
	return set_message("warning", $msg, $sub_header, $main_header, $show_close_buttom, $box_id);
}
function set_message_success($msg="", $sub_header="", $main_header="", $show_close_buttom=true, $box_id="") {
	return set_message("success", $msg, $sub_header, $main_header, $show_close_buttom, $box_id);
}
function set_message_info($msg="", $sub_header="", $main_header="", $show_close_buttom=true, $box_id="") {
	return set_message("info", $msg, $sub_header, $main_header, $show_close_buttom, $box_id);
}

/****************************************************************/
/* Manage Show image, If not find file show default image.
/* Added by: Oui
/****************************************************************/
function get_image_old($full_file_name="",$class="",$default_image="") {
	if(strtolower($default_image) == "blank"){
		$default_image = PUBLIC_PATH."images/blank.gif";
	}else if(strtolower($default_image) == "off"){
		$default_image = "";
	}else if(is_blank($default_image)){
		if(is_blank($class)){
			$default_image = THEME_FRONT_PATH."images/cover/no-image.png";
		}else{
			$default_image = THEME_FRONT_PATH."images/cover/no-image-".$class.".png";
		}
	}
	
	if(!is_blank($full_file_name)){
		if(is_file($full_file_name)){
			return PUBLIC_PATH.$full_file_name;
		}
	}
	return $default_image;
}

/****************************************************************/
/* Manage Show image, If not find file show default image.
/* Added by: Air
/****************************************************************/
function get_image($full_file_name="",$class="",$default_image="") {
	if(!is_blank($full_file_name)){
		if(is_file($full_file_name)){
			return PUBLIC_PATH.$full_file_name;
		}
	}
	
	if(strtolower($default_image) == "blank"){
		$default_image = PUBLIC_PATH."images/blank.gif";
	}else if(strtolower($default_image) == "off"){
		$default_image = "";
	}else if(is_blank($default_image)){
		if(is_blank($class)){
			$default_image = THEME_FRONT_PATH."images/cover/no-image.png";
		}else{
			$default_image = THEME_FRONT_PATH."images/cover/no-image-".$class.".png";
		}
	}
	
	
	return $default_image;
}

/****************************************************************/
/* Navi Page
/* Added by: Oui
/****************************************************************/
function getPagination($optional) {

	$zero_show = get_array_value($optional,"zero_show","1");
	if(!is_var_array($optional)){
		return '<div class="comment-pagination clearfix" id="tbldata_paginate">&nbsp;</div>';
	}
	$total_page = get_array_value($optional,"total_page","0");
	if($total_page <= 1 && $zero_show == 0){
		return "";
	}
	if($total_page <= 1){
		return '<div class="comment-pagination clearfix" id="tbldata_paginate">&nbsp;</div>';
	}
	$search_record_per_page = get_array_value($optional,"search_record_per_page","0");
	$total_in_page = get_array_value($optional,"total_in_page","0");
	$page_selected = get_array_value($optional,"page_selected","0");
	$url = get_array_value($optional,"url","");
	$onclick = get_array_value($optional,"onclick","");
	
	$end_page = $page_selected+2;
	if($end_page > $total_page) $end_page = $total_page;
	
	$start_page = $page_selected-2;
	if($start_page <= 0) $start_page = 1;
	
	$txt = '';
	$txt .= '<div class="comment-pagination clearfix">';
	
	if($page_selected > 1){
		$this_onclick = str_replace("page_selected",1,$onclick);
		$txt .= '<a class="button page-numbers';
		if(!is_blank($url)){
			$txt .= '" onclick="processRedirect(\''.$url.'1\')">First</a>';
		}else if(!is_blank($this_onclick)){
			$txt .= '" onclick="'.$this_onclick.'">First</a>';
		}
		
		$this_onclick = str_replace("page_selected",($page_selected-1),$onclick);
		$txt .= '<a class="button page-numbers';
		if(!is_blank($url)){
			$txt .= '" onclick="processRedirect(\''.$url.($page_selected-1).'\')">Previous</a>';
		}else if(!is_blank($this_onclick)){
			$txt .= '" onclick="'.$this_onclick.'">Previous</a>';
		}
		
		
	}
	
	for($i=$start_page; $i<=$end_page; $i++){
		$this_onclick = str_replace("page_selected",$i,$onclick);
		$txt .= '<a class="button page-numbers ';
		if($i==$page_selected){
			$txt .= 'current';
		}else{
			$txt .= '';
		}
		if(!is_blank($url)){
			$txt .= '" onclick="processRedirect(\''.$url.$i.'\')">'.$i.'</a>';
		}else if(!is_blank($this_onclick)){
			$txt .= '" onclick="'.$this_onclick.'">'.$i.'</a>';
		}
	}
	
	if($page_selected < $total_page){
		$this_onclick = str_replace("page_selected",($page_selected+1),$onclick);
		$txt .= '<a class="button page-numbers';
		if(!is_blank($url)){
			$txt .= '" onclick="processRedirect(\''.$url.($page_selected+1).'\')">Next</a>';
		}else if(!is_blank($this_onclick)){
			$txt .= '" onclick="'.$this_onclick.'">Next</a>';
		}
	
		$this_onclick = str_replace("page_selected",$total_page,$onclick);
		$txt .= '<a class="button page-numbers';
		if(!is_blank($url)){
			$txt .= '" onclick="processRedirect(\''.$url.$total_page.'\')">Last</a>';
		}else if(!is_blank($this_onclick)){
			$txt .= '" onclick="'.$this_onclick.'">Last</a>';
		}
	}
	
	$txt .= '</div>';
	return $txt;
}

/****************************************************************/
/* Send mail
/* Added by: Oui
/****************************************************************/
function send_mail($to, $subject, $detail, $from_mail="", $from_name="") {
	if(isBlank($to) || isBlank($subject) || isBlank($detail)) return false;
	
	if(isBlank($from_mail)) $from_mail = ADMIN_EMAIL;
	if(isBlank($from_name)) $from_name = ADMIN_EMAIL_NAME;

	// Additional headers
	$headers = "";
	$headers .= "From: ".$from_name." <".$from_mail.">" . "\r\n";
	//$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
	$header_ = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n";  	
			
	$flag =  mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=',$detail,$header_ . $headers);
	
	return $flag;
}

/****************************************************************/
/* Get Mac Address
/* Added by: Oui / 06 Aug 2010
/****************************************************************/
function getMacAddress() {
	$output = Array();
	exec( 'netstat -r', $output );
	for( $a = 0, $b = &count( $output ); $a < $b; $a++ ) {
		if( preg_match( "/(?i)([a-z0-9]{2} ){6}/", $output[$a] ) == true ) {
			$macaddress = &$output[$a];
			// echo "full macaddress : ".$macaddress."<br>";
			$macaddress = substr($macaddress,7,17);
			$macaddress = str_replace(" ",":",$macaddress);
			// echo "macaddress : ".$macaddress."<br>";
			// $uniquekey = &md5( $macaddress );
			// $output[$a] = &preg_replace( "/(?i)([^a-z0-9]*?)([a-z0-9]{2} ){6}/i", "\\1 {$uniquekey} ", $output[$a] );
			// $output[$a] = &explode( " {$uniquekey} ", $output[$a] );
			// $uniquekey = Array( trim( $output[$a][0] ), trim( $output[$a][1] ) );
			// $macaddress = &str_replace( $uniquekey, "", $macaddress );
			return trim( $macaddress );
		}
		// echo "<hr>";
	}
	return 'not-found';
}

function cmpbyweight($a, $b) {
  return strnatcmp($a["weight"], $b["weight"]);
}

function cmpbyparentaid($a, $b) {
  return strnatcmp($a["parent_aid"], $b["parent_aid"]);
}

function get_price_format($val, $digit=2){
	return number_format($val, $digit);
}

function get_price($item){
	if(!is_var_array($item)){
		return "";
	}
	$price = 0;
	$result = array();
	
	$item_sale_price = 0 + get_array_value($item,"sale_price","0");
	$item_special_price = 0 + get_array_value($item,"special_price","0");
	
	if($item_special_price > 0 && $item_special_price < $item_sale_price){
		$result["price"] = $item_special_price;
		$result["price_show"] = '<span class="price-cut">'.get_price_format($item_sale_price).'</span> <span class="price-special">'.get_price_format($item_special_price).'</span>';
	}else{
		$result["price"] = $item_sale_price;
		$result["price_show"] = '<span class="price-normal">'.get_price_format($item_sale_price).'</span>';
	}
	return $result;
}

function get_secret_key($data){
	$parent_aid = get_array_value($data,"parent_aid","0");
	$product_type_aid = get_array_value($data,"product_type_aid","0");
	
	$sum = $product_type_aid + $parent_aid;
	
	if($product_type_aid == 1)
	{
		$sum .= "book";
	}
	else if($product_type_aid == 2)
	{
		$sum .= "magazine";
	}
	else{
		$sum .= "";
	}
	
   	$gen_secret_key = hash('sha256', $sum);
 	$gen_secret_key = substr($gen_secret_key, 0, 32); 
 	return $gen_secret_key;
}

function get_iv($data){
	$barcode = get_array_value($data,"barcode","0");
	$gen_iv = md5("bookdose".$barcode);
	$gen_iv = substr($gen_iv, 0, 16); 
	return $gen_iv;
}

?>