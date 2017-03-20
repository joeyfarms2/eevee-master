<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if($_SERVER["SERVER_NAME"] == "bookdose-elibrary")
{
	$db['default']['username'] = 'root';
 	$db['default']['database'] = 'bd_eevee';
 	$db['default']['password'] = '1234';
}
else{
	$db['default']['username'] = 'bdcontent_uat';
 	$db['default']['database'] = 'bdcontent_uat';
 	$db['default']['password'] = 'Ero2Q40zL';	
}
 

 


/* End of file database.php */
/* Location: ./application/config/config-nbtc/database.php */