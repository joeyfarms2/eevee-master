<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if($_SERVER["SERVER_NAME"] == "bookdose-elibrary")
{
	$db['default']['username'] = 'root';
 	$db['default']['database'] = 'bd_lanna_new';
 	$db['default']['password'] = '1234';
}
else{
	$db['default']['username'] = 'belibs_lanna';
 	$db['default']['database'] = 'belibs_lanna';
 	$db['default']['password'] = '64PyG9tke';	
}
 

 


/* End of file database.php */
/* Location: ./application/config/config-nbtc/database.php */