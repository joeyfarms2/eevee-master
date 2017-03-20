<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// define('BASE_URL', 'http://'.$_SERVER["SERVER_NAME"].'/');
// define('PUBLIC_PATH', 'http://'.$_SERVER["SERVER_NAME"].'/');

//define('CONST_PROJECT_CODE','rmutt');
define('CONST_PROJECT_CODE','nia');
//define('CONST_PROJECT_CODE','excise');

require_once('config-'.CONST_PROJECT_CODE.'/constants.php');
define('CONST_SHOW_FB', '0'); // 1:Show, 0:Not show

define('VIEW_PATH', APPPATH.'views/');
define('INCLUDE_PATH', PUBLIC_PATH.'include/');
define('CSS_PATH', PUBLIC_PATH.'styles/');
define('JS_PATH', PUBLIC_PATH.'js/');
define('SCRIPT_PATH', PUBLIC_PATH.'scripts/');
define('IMAGE_PATH', PUBLIC_PATH.'images/');
define('DEFAULT_CONTENT_VIEW', 'page_not_set_view');
define('BLANK_CONTENT_VIEW', 'page_blank');

define('IMAGE_NO_IMAGE',IMAGE_PATH.'no-image.png');


/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */