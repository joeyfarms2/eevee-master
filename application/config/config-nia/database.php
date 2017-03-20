<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if($_SERVER["SERVER_NAME"] == "bookdose-elibrary")
{
	$db['default']['username'] = 'root';
 	$db['default']['database'] = 'bd_nia_new';
 	$db['default']['password'] = '1234';
	
}
else if($_SERVER["SERVER_NAME"] == "eevee")
{
	$db['default']['username'] = 'root';
 	$db['default']['database'] = 'eevee';
 	$db['default']['password'] = 'root';
}
else if($_SERVER["SERVER_NAME"] == "eevee.local")
{
    $db['default']['username'] = 'root';
    $db['default']['database'] = 'eevee';
    $db['default']['password'] = '';
}
else{
	// $db['default']['username'] = 'nialib_belibs';
 // 	$db['default']['database'] = 'nialib_belibs';
 // 	$db['default']['password'] = 'N8f0NfQkg';		
	$db['default']['username'] = 'bdcontent_uat';
 	$db['default']['database'] = 'bdcontent_uat';
 	$db['default']['password'] = 'Ero2Q40zL';
}
 

 


/* End of file database.php */
/* Location: ./application/config/config-nbtc/database.php */