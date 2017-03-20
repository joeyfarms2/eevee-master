<?php
/**
 * Permission Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Tisa Pathumwan
 * 
 * 
 */

// ------------------------------------------------------------------------

function getUserSession(){
	$userSession = new CI_Session();
	return $userSession->userdata('userSession');
}
function getSessionUserAid(){
	$obj = getUserSession();
	return get_array_value($obj,"aid","");
}
function getSessionUserRoleAid(){
	$obj = getUserSession();
	return get_array_value($obj,"user_role_aid","");
}
function getSessionLastedUrl(){
	$objSession = new CI_Session();
	return $objSession->userdata('lasted_url');
}

function getSessionOwnerAid(){
	$obj = getUserSession();
	return get_array_value($obj,"user_owner_aid","");
}

function get_user_avatar($obj){
	$avatar_path = get_array_value($obj,"avatar_path","");
	$avatar_path = str_replace("./", "", $avatar_path);
	$aid = get_array_value($obj,"user_aid","");
	if(!is_number_no_zero($aid)){
		$aid = get_array_value($obj,"aid","");
	}
	$avatar_type = get_array_value($obj,"avatar_type",".jpg");
	$avatar_mode = get_array_value($obj,"avatar_mode","thumb");
	$gender = get_array_value($obj,"gender","m");
	$avatar_full = $avatar_path.'/'.$aid.'-'.$avatar_mode.$avatar_type;
	// echo "avatar_full = $avatar_full <BR>";
	
	if(is_blank($avatar_path) || is_blank($aid)){
		return img(array('src'=>THEME_FRONT_PATH.'images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg', 'class'=>'avatar'));
	}
	if(is_file($avatar_full)){
		return img(array('src'=>$avatar_path.'/'.$aid.'-'.$avatar_mode.$avatar_type, 'class'=>'avatar'));
	}else{
		return img(array('src'=>THEME_FRONT_PATH.'images/avatar/avatar-'.$avatar_mode.'-'.$gender.'.jpg', 'class'=>'avatar'));
	}
}

function get_user_info($obj){
	$full_name_en = trim(get_array_value($obj,"first_name_en","")." ".get_array_value($obj,"last_name_en",""));
	$full_name_th = trim(get_array_value($obj,"first_name_th","")." ".get_array_value($obj,"last_name_th",""));
	$display_name = trim(get_array_value($obj,"display_name",""));
	$nickname = trim(get_array_value($obj,"nickname",""));
	$username = trim(get_array_value($obj,"username",""));
	$email = trim(get_array_value($obj,"email","N/A"));
	 if(!is_blank($full_name_en)){
		return $full_name_en;
	}else if(!is_blank($full_name_th)){
		return $full_name_th;
	}else if(!is_blank($display_name)){
		return $display_name;
	}else if(!is_blank($username)){
		return $username;
	}elseif(!is_blank($nickname)){
		return $nickname;
	}else {
		return $email;
	}
}


function getUserLoginInfo($obj){
	if (is_a($obj, 'Initcontroller')){
		return $obj->user_login_info;
	}else if(is_var_array($obj)){
		return $obj;
	}else{
		return "";
	}
}
function getUserLoginAid($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"aid","");
}
function getUserLoginCid($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"cid","");
}
function getUserLoginUsername($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"username","");
}
function getUserLoginPassword($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"password","");
}
function getUserLoginEmail($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"email","");
}
function getUserLoginFirstNameTh($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"first_name_th","");
}
function getUserLoginLastNameTh($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"last_name_th","");
}
function getUserContactNumber($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"contact_number","");
}
function getUserAddress($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"address","");
}
function getUserLoginStatus($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"status","");
}
function getUserLoginUserOwnerAid($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"user_owner_aid","");
}
function getUserLoginRoleAid($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"user_role_aid","");
}
function getUserLoginRoleName($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"user_role_name","");
}
function getUserLoginPublisherAid($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"publisher_aid","");
}
function getUserLoginHash($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"login_hash","");
}
function getUserLoginLastLogin($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"last_login","");
}
function getUserLoginAvatar($obj){
	return get_user_avatar(getUserLoginInfo($obj));
}
function getUserLoginFullName($obj){
	return get_user_info(getUserLoginInfo($obj));
}
function getUserLoginNameForLog($obj){
	return get_user_info(getUserLoginInfo($obj)).' [aid='.getUserLoginAid($obj).']';
}
function getUserLoginSectionAid($obj){
	$obj = getUserLoginInfo($obj);
	return get_array_value($obj,"user_section_aid","0");
}
/* get user owner login information */
function getUserOwnerInfo($obj){
	if (is_a($obj, 'Initcontroller')){
		return $obj->user_owner_info;
	}else if(is_var_array($obj)){
		return $obj;
	}else{
		return "";
	}
}
function getUserOwnerAid($obj){
	$obj = getUserOwnerInfo($obj);
	return get_array_value($obj,"aid","1");
}
function getUserOwnerAlias($obj){
	$obj = getUserOwnerInfo($obj);
	return get_array_value($obj,"alias","");
}
function getUserOwnerStatus($obj){
	$obj = getUserOwnerInfo($obj);
	return get_array_value($obj,"stauts","");
}
function getUserOwnerType($obj){
	$obj = getUserOwnerInfo($obj);
	return get_array_value($obj,"type","");
}
function getUserOwnerName($obj){
	$obj = getUserOwnerInfo($obj);
	$name = get_array_value($obj,"name","");
	$alias = get_array_value($obj,"alias","N/A");
	if(!is_blank($name)){
		return $name;
	}else{
		return $alias;
	}
}
function getUserOwnerDetailForLog($obj){
	$name = getUserOwnerName($obj);
	$aid = getUserOwnerAid($obj);
	if(!is_blank($aid)){
		return "[".$name."] aid = ".$aid."";
	}else{
		return "N/A";
	}
}

function is_login(){
	$user_aid = getSessionUserAid();
	if($user_aid != "") return true;
	else return false;
}

function is_root_admin(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 1) return true;
	else return false;
}
function is_general_admin(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 2) return true;
	else return false;
}
function is_owner_admin(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 3) return true;
	else return false;
}
function is_staff(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 4) return true;
	else return false;
}
function is_member(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 5) return true;
	else return false;
}
function is_publisher(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 6) return true;
	else return false;
}

function is_root_admin_or_higher(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 1) return true;
	else return false;
}
function is_general_admin_or_higher(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 1 || $user_role_aid == 2) return true;
	else return false;
}
function is_owner_admin_or_higher(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3) return true;
	else return false;
}
function is_staff_or_higher(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3 || $user_role_aid == 4) return true;
	else return false;
}
function is_member_or_higher(){
	$user_role_aid = getSessionUserRoleAid();
	if($user_role_aid == 1 || $user_role_aid == 2 || $user_role_aid == 3 || $user_role_aid == 4 || $user_role_aid == 5 || $user_role_aid == 6) return true;
	else return false;
}

function for_login($hash=""){
	$user_aid = getSessionUserAid();
	// if($user_aid != "" && getSessionUserHash() != "" && getSessionUserHash() == $hash) return true;
	if($user_aid != "" ) return true;
	else redirect('login');
}
function for_root_admin(){
	$role_can_access = array("1");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_root_admin_or_higher(){
	$role_can_access = array("1");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_general_admin(){
	$role_can_access = array("2");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_general_admin_or_higher(){
	$role_can_access = array("1","2");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_owner_admin(){
	$role_can_access = array("3");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_owner_admin_or_higher(){
	$role_can_access = array("1","2","3");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_staff(){
	$role_can_access = array("4");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_staff_or_higher(){
	$role_can_access = array("1","2","3","4");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_member(){
	$role_can_access = array("5");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_member_or_higher(){
	$role_can_access = array("1","2","3","4","5","6");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_publisher(){
	$user = getUserSession();
	$role_can_access = array("6");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}
function for_publisher_or_higher(){
	$user = getUserSession();
	$role_can_access = array("1","2","3","4","6");
	return is_match_role(getSessionUserRoleAid(),$role_can_access);
}

function exception_about_status(){
	return is_owner_admin_or_higher();
}

function is_specify_password(){
	if(CONST_PASSWORD_TYPE == 2 || CONST_PASSWORD_TYPE == 3 || CONST_PASSWORD_TYPE == 4){
		return true;
	}else{
		return false;
	}
}

function is_specify_username(){
	if(CONST_USERNAME_TYPE == 2){
		return false;
	}else{
		return true;
	}
}

function is_web_service(){
	if(CONST_IS_WEB_SERVICE == 1){
		return true;
	}else{
		return false;
	}
}

?>