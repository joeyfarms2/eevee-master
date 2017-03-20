<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*****************************************************************************************************************************/
/** Important!!! **************************************************************************************************************/
/** Do not change all values below this line, or some errors may be occured on you system, and term of maintenance will be terminated  **/
/*****************************************************************************************************************************/
define('CONST_MODE','1'); //1:UAT, 2:Develope, default is 1
define('CONST_WEB_STATUS','1'); //1:Normally, 2:Only Login, 3:Only Admin or higher, 4:Only Root Admin

define('CONST_CODENAME','rmutt');

define('BASE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');
define('PUBLIC_PATH', 'http://'.$_SERVER["SERVER_NAME"].'/');

$assign_to_config['base_url'] = 'http://'.$_SERVER["SERVER_NAME"].'/';
$assign_to_config['language'] = CONST_CODENAME.'-english';


/*
|--------------------------------------------------------------------------
| LDAP Login
|--------------------------------------------------------------------------
*/

define('CONST_LDAP_LOGIN', '2'); // 0: Login using our system ,1: Login LDAP server, 2: Login LDAP server soap

/*
|--------------------------------------------------------------------------
| LDAP Login constants
|--------------------------------------------------------------------------
*/
define('CONST_LDAP_AUTHEN', FALSE); // TRUE: Login using LDAP server, FALSE: Login using our system
define('CONST_LDAP_BASE_DN', 'DC=rmutt,DC=ac,DC=th'); // e.g. DC=scbcorp,DC=co,DC=th
define('CONST_LDAP_ACCOUNT_SUFFIX', '@rmutt.ac.th'); // e.g. @scbcorp.co.th
define('CONST_LDAP_DOMAIN_CONTROLLER', '203.158.253.46'); // e.g. scbcorp.co.th

/*****************************************************************************************************************************/

/*
|--------------------------------------------------------------------------
| LDAP Login constants soap
|--------------------------------------------------------------------------
*/
define('CONST_LDAP_AUTHEN_SOAP', TRUE); // TRUE: Login using LDAP server, FALSE: Login using our system

define('CONST_LDAP_AUTHEN_JSON', TRUE); // TRUE: Login using LDAP server, FALSE: Login using our system
define('CONST_LDAP_AUTHEN_JSON_URL', 'http://m.lannapoly.ac.th/e-library/member.php');

/*****************************************************************************************************************************/

/** Editable Zone **/
define('DEFAULT_TITLE', 'RMUTT | Digital Library'); // This value will show in title bar of web browser
define('META_DESCRIPTION', '');
define('META_KEYWORDS', '');

define('ADMIN_EMAIL', 'noreply@mail.rmutt.ac.th'); // This is an email will show in automatic mail sent.
define('ADMIN_EMAIL_NAME', 'ห้องสมุดสื่อทรัพยากรออนไลน์'); // This is name will show in automatic mail sent.
define('ADMIN_EMAIL_SIGNATURE', 'ห้องสมุดสื่อทรัพยากรออนไลน์'); // This is signature will show in automatic mail sent.

define('CONTACT_EMAIL', 'elibrary@mail.rmutt.ac.th'); // This is an email that user can send feedback or ask for your support.
define('MAIN_CONTACT_EMAIL', 'elibrary@mail.rmutt.ac.th'); // This is an email that user can send feedback or ask for your support


define('CONST_MAIN_DOMAIN_EMAIL', '1');
define('MAIN_DOMAIN_EMAIL', 'nia.go.th'); 

define('CONST_FB_LINK', 'https://www.facebook.com/RMUTT.Library/'); // This is an facebook link
define('WEB_URL', '');

define("GOOGLE_API_KEY", ""); // Place your Google API Key
/** End of Editable Zone **/

/***********************************************************************************************************/
/** Important!!!                                                                                                                                                                      **/
/** Do not change all values below this line, or some errors may be occured on you system, and term of maintenance will be terminated  **/
/***********************************************************************************************************/
define('FCK_UPLOAD_PATH', PUBLIC_PATH.'uploads/'.CONST_CODENAME.'/userfiles');
define('UPLOAD_PATH', PUBLIC_PATH.'uploads/'.CONST_CODENAME.'/');

define('THEME_FRONT_PATH', PUBLIC_PATH.'styles/rmutt/');
define('THEME_ADMIN_PATH', PUBLIC_PATH.'styles/flatlab/');

define('THEME_ADMIN', '_flatlab');
define('THEME_FRONT', '_rmutt');
define('THEME_LOGIN', '_rmutt');

define('CONST_DETAIL_PATH','detail/');
define('CONST_STATIC_PAGE_PATH','info/');

/*
|--------------------------------------------------------------------------
| Module
|--------------------------------------------------------------------------
*/
define('CONST_HAS_IPAD_APP', '1'); // 1:Has ipad app, 0:DONT has ipad app, default is 0
define('CONST_MARC_STATUS','1'); //1:Use MARC, 0:NOT use MARC, default is 0
define('CONST_USE_TRANSACTION', '0'); // 1:Use, 0:Is NOT use, default is 0
define('CONST_USE_PRODUCT_TOPIC', '0'); // 1:Use, 0:Is NOT use, default is 0
define('CONST_ALLOW_DELETE_PRODUCT', '1'); // 1:Allow, 0:NOT Allow, default is 1

/*
|--------------------------------------------------------------------------
| Login Module
|--------------------------------------------------------------------------
*/
define('CONST_IS_WEB_SERVICE', '2'); // 1:Is web service, 2:Is NOT web service, default is 2
define('CONST_ONLINE_REGIS','1'); // 1:Has online register, 0:DONT have online register, default is 0
define('CONST_USERNAME_TYPE','1'); // 1:Has Username Field, 2:DONT have Username Field: default is 1
define('CONST_PASSWORD_TYPE','2'); // 1:Generate by system, 2:Specify by user with activate mail, 3:Specify by user w/o activate mail, 4:Specify by user and need activate by admin, default is 1
define('CONST_EMAIL_REQUIRED','1'); // 1:Email required, 2:Email is optional, default is 1
define('CONST_READ_BOOK_ANONYMOUS','1'); // 0:NOT Allow, 1:Allow, default is 0
define('CONST_LOGIN_BY_DOMAIN','0'); // 0:NOT use domain, 1:Use domain, default is 0

define('CONST_HASH_KEY', 'B00kDo5e'); //Do not change this value. Once you change this all of exists user can not be login with current password anymore.
define('CONST_MIN_LENGTH_USERNAME', '4');
define('CONST_MAX_LENGTH_USERNAME', '13');
define('CONST_MIN_LENGTH_PASSWORD', '4');
define('CONST_MAX_LENGTH_PASSWORD', '13');

define('CONST_MAX_LOGIN_DEVICE', '9999');
/***** End : Login Module *****/

define('CONST_SHOW_FOOTER_POWER_BY','1'); // 1: Show, 0:NOT show, default is 1

define('CONST_AVATAR_SIZE_WIDTH_TINY','29'); // in pixel
define('CONST_AVATAR_SIZE_HEIGHT_TINY','29'); // in pixel
define('CONST_AVATAR_SIZE_WIDTH_MINI','90'); // in pixel
define('CONST_AVATAR_SIZE_HEIGHT_MINI','90'); // in pixel
define('CONST_AVATAR_SIZE_WIDTH_THUMB','152'); // in pixel
define('CONST_AVATAR_SIZE_HEIGHT_THUMB','152'); // in pixel

/*
|-------------------------------------------------------------------------
| Default setting 
|--------------------------------------------------------------------------
*/
define('CONST_DEFAULT_RECORD_PER_PAGE','25');
define('CONST_DEFAULT_RECORD_FOR_COMMENT','50');
define('CONST_DEFAULT_RECORD_FOR_SEARCH','12');
define('CONST_DEFAULT_RECORD_FOR_BOOKSHELF','12');
define('CONST_DEFAULT_RECORD_FOR_MY_BOOKSHELF','16');
define('CONST_DEFAULT_RECORD_FOR_EVENT','16');

define('CONST_DEFAULT_DATE_PATTERN','M d, Y');
define('CONST_DEFAULT_DATETIME_PATTERN','M d, Y H:i');

define('CONST_DEFAULT_EBOOK_RENTAL_PERIOD','7');
define('CONST_DEFAULT_EBOOK_RENTAL_ALLOW_ON_SHELF','3');
define('CONST_DEFAULT_EBOOK_LICENSE_CONCURRENCE','2');

define('CONST_ZERO_PAD','5');
define('CONST_ZERO_PAD_FOR_PRODUCT','6');
define('CONST_INVOICE_PREFIX','INV-');
define('CONST_RECEIPT_PREFIX','RCP-');

define('CONST_TITLE_SHORT_CHAR','50');

//define('CONST_ALLOW_FILE_TYPE_DEFAULT','pdf|doc|docx|csv|xls|xlsx|pps|ppt|pptx|mov|mp3|mp4|mpeg|flv|wma|jpg|jpeg|gif|png');
define('CONST_ALLOW_FILE_TYPE_DEFAULT','doc|docx|csv|xls|xlsx|pps|ppt|pptx');
define('CONST_ALLOW_FILE_SIZE_DEFAULT','52428800');
define('CONST_ALLOW_FILE_TYPE_FOR_DIGITAL_GEN','zip|pdf');
define('CONST_ALLOW_FILE_SIZE_FOR_DIGITAL_GEN','52428800');
define('CONST_ALLOW_FILE_TYPE_FOR_IMAGE','jpg|jpeg|gif|png');
define('CONST_ALLOW_FILE_SIZE_FOR_IMAGE','5242880');
define('CONST_ALLOW_FILE_TYPE_FOR_VDO','mov|mp3|mp4|mpeg|flv|wma');
define('CONST_ALLOW_FILE_SIZE_FOR_VDO','52428800');
define('CONST_ALLOW_FILE_TYPE_FOR_COMMENT','jpg|jpeg|gif|png');
define('CONST_ALLOW_FILE_SIZE_FOR_COMMENT','1048576');

define('CONST_CATEGORY_MODE','1'); //1: Simple (1 Level), 2: 2 Levels, 3:Multi Levels(Not available now)
define('CONST_CATEGORY_ACCESS_MODE','2'); //1: Access all, 2: Access by user level

/*
|--------------------------------------------------------------------------
| Review Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_REVIEW','1'); // 1:Use review system, 0:DONT use review system, default is 0
define('CONST_REVIEW_MAX_POINT','5');
define('CONST_DEFAULT_RECORD_FOR_REVIEW','10');
define('CONST_DEFAULT_DATE_PATTERN_FOR_REVIEW','M d, Y H:i');

/*
|--------------------------------------------------------------------------
| News Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_NEWS','1'); // 1:Use news system, 0:DONT news event system, default is 0
define('CONST_NEWS_MODE','1'); // 1:Full mode, 2:Simple mode, default is 1
define('CONST_NEWS_SIZE_WIDTH_SQUARE','120'); // in pixel
define('CONST_NEWS_SIZE_HEIGHT_SQUARE','120'); // in pixel
define('CONST_NEWS_SIZE_WIDTH_MINI','120'); // in pixel
define('CONST_NEWS_SIZE_HEIGHT_MINI','0'); // in pixel
define('CONST_NEWS_SIZE_WIDTH_THUMB','400'); // in pixel
define('CONST_NEWS_SIZE_HEIGHT_THUMB','0'); // in pixel
define('CONST_NEWS_SIZE_WIDTH_ACTUAL','800'); // in pixel
define('CONST_NEWS_SIZE_HEIGHT_ACTUAL','0'); // in pixel
define('CONST_ALLOW_FILE_TYPE_FOR_NEWS_IMAGE','jpg|jpeg|gif|png');
define('CONST_ALLOW_FILE_SIZE_FOR_NEWS_IMAGE','5242880');
define('CONST_NEWS_DEFAULT_CHECKED_SHOW_IN_HOME','1');

/*
|--------------------------------------------------------------------------
| Event Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_EVENT','0'); // 1:Use event system, 0:DONT use event system, default is 0
define('CONST_EVENT_MODE','1'); // 1:Full mode, 2:Simple mode, default is 1
define('CONST_EVENT_SIZE_WIDTH_SQUARE','120'); // in pixel
define('CONST_EVENT_SIZE_HEIGHT_SQUARE','120'); // in pixel
define('CONST_EVENT_SIZE_WIDTH_MINI','120'); // in pixel
define('CONST_EVENT_SIZE_HEIGHT_MINI','0'); // in pixel
define('CONST_EVENT_SIZE_WIDTH_THUMB','400'); // in pixel
define('CONST_EVENT_SIZE_HEIGHT_THUMB','0'); // in pixel
define('CONST_EVENT_SIZE_WIDTH_ACTUAL','800'); // in pixel
define('CONST_EVENT_SIZE_HEIGHT_ACTUAL','0'); // in pixel
define('CONST_ALLOW_FILE_TYPE_FOR_EVENT_IMAGE','jpg|jpeg|gif|png');
define('CONST_ALLOW_FILE_SIZE_FOR_EVENT_IMAGE','5242880');
define('CONST_EVENT_DEFAULT_CHECKED_SHOW_IN_HOME','0');

/*
|--------------------------------------------------------------------------
| Questionaire Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_QUESTIONAIRE','0'); // 1:Use questionaire system, 0:DONT use questionaire system, default is 0

/*
|--------------------------------------------------------------------------
| Ads Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_ADS','1'); // 1:Use ads system, 0:DONT use ads system, default is 0
define('CONST_ADS_MODE','1'); // 1:Full mode, 2:Simple mode, default is 1

/*
|--------------------------------------------------------------------------
| Ads Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_ECONTENT_TRANSFER','1'); // 1:Use ECONTENT_TRANSFER system, 0:DONT use ECONTENT_TRANSFER system, default is 0
define('CONST_ECONTENT_TRANSFER_MODE','1'); // 1:Full mode, 2:Simple mode, default is 1

/*
|--------------------------------------------------------------------------
| Redeem Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_REDEEM','0'); // 1:Use ads system, 0:DONT use ads system, default is 0
define('CONST_MASTER_REDEEM_TYPE',''); // point , cash , discount
define('CONST_REDEEM_DEFAULT_LIMIT_PER_CODE','1');
define('CONST_REDEEM_DEFAULT_LIMIT_PER_USER','1');
define('CONST_REDEEM_DEFAULT_CODE_LENGTH','4');

/*
|--------------------------------------------------------------------------
| Cart Setting 
|--------------------------------------------------------------------------
*/
define('CONST_HAS_POINT', '0'); // 1:Use point, 0:DONT use point, default is 0
define('CONST_MASTER_PAYMENT_TYPE_POINT', '');

define('CONST_HAS_BASKET', '0'); // 1:Use basket system, 0:DONT use basket system, default is 0
define('CONST_MASTER_PAYMENT_TYPE', '');

define('CONST_HAS_REWARD_POINT', '1'); // 0:Do not have reward, 1:Have reward, default is 0
define('CONST_REWARD_POINT', '10'); // No. of page per 1 point

define('CONST_USE_PAYSBUY','0');
define('CONST_EMAIL_FOR_PAYSBUY','');
define('CONST_URL_PAYSBUY_PAYNOW','');
define('CONST_IP_PAYSBUY','');
define('CONST_IP_PAYSBUY_DEMO','');

define('CONST_USE_PAYPAL','0');
define('CONST_USE_PAYPAL_SANDBOX','0');
define('CONST_URL_PAYPAL_PAYNOW_SANDBOX','');
define('CONST_URL_PAYPAL_PAYNOW','');
define('CONST_EMAIL_FOR_PAYPAL','');
define('CONST_PAYPAL_API_USERID', '');
define('CONST_PAYPAL_API_PASSWORD', '');
define('CONST_PAYPAL_API_SIGNATURE', '');
define('CONST_PAYPAL_APP_ID', ''); // Sandbox App ID

define('CONST_BASKET_EMPTY','<p>Your basket is empty</p>');

/*
|--------------------------------------------------------------------------
| Transaction Module
|--------------------------------------------------------------------------
*/
define('CONST_HAS_TRANSACTION', '1'); // 1:Use transaction system, 0:DONT use transaction system, default is 0
define('CONST_MAX_ALLOW_BORROWING_BOOK', '3');
define('CONST_MAX_ALLOW_BORROWING_CD', '3');
define('CONST_DEFAULT_BORROWING_PERIOD', '7');
define('CONST_DEFAULT_FINE_FEE', '5');
define('CONST_DEFAULT_RENTAL_FEE', '0');
define('CONST_MAX_BORROWING_RENEW', '1');
define('CONST_HAS_PRINT', '1'); // 1:Use PRINT system, 0:DONT use PRINT system, default is 0
/***** End : Transaction Module *****/

/* End of file constants.php */
/* Location: ./application/config/constants.php */