<?php      
Class CryptographyAES
{
	function Encrypt($source, $destination, $key, $iv)	{
		if (extension_loaded('mcrypt') === true)
		{
			//echo "have mcrypt<br />";
			/* | */
			/* V */
			/* problem? */
            $content = file_get_contents($source);   
            $encryptedSource = self::AESEncrypt($content,$key,$iv);
            //echo "encryptedSource : ".$encryptedSource."<br />";
            if (file_put_contents($destination,$encryptedSource, LOCK_EX) !== false)
            {
            	//echo "file_put_contents<br />";
				return true;
            }
            //echo "error file_put_contents<br />";
            return false;
		}
		//echo "error mcrypt<br />";
		return false;
	}

	function Decrypt($source, $destination, $key, $iv) {
		if (extension_loaded('mcrypt') === true)
		{
            $decryptedSource=self::AESDecrypt($source,$key,$iv);	
            if (file_put_contents($destination,$decryptedSource, LOCK_EX) !== false)
            {
				return true;
            }
            return false;	
		}
		return false;
	}


	function EncryptText($text, $key, $iv)	{
		if (extension_loaded('mcrypt') === true)
		{       
        	$encryptedTest = self::AESEncrypt($text,$key,$iv);
            return $encryptedTest;        
		}
		//return "ABC";
	}

	function DecryptText($text, $key, $iv) {
		if (extension_loaded('mcrypt') === true)
		{
            $decryptedText = self::AESDecrypt($text,$key,$iv);
			return $decryptedText;	
                    	
		}
		//return "XYZ";
	}




/*AES - START*/
function fnEncrypt($sValue, $sSecretKey, $iv) {
    //global $iv;
    return rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, $sValue, MCRYPT_MODE_CBC, $iv)), "\0\3");
}

function fnDecrypt($sValue, $sSecretKey, $iv) {
    //global $iv;
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_CBC, $iv), "\0\3");
}
/*AES - END*/




	/*
	 Apply tripleDES algorthim for encryption, append "___EOT" to encrypted file ,
	 so that we can remove it while decrpytion also padding 0's
	 */
	function TripleDesEncrypt($buffer,$key,$iv) {
		$cipher = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
		$buffer.='___EOT';
		// get the amount of bytes to pad
		$extra = 8 - (strlen($buffer) % 8);
	 	// add the zero padding
		if($extra > 0) {
			for($i = 0; $i < $extra; $i++) {
				$buffer .= '_';
			}
		}
	    mcrypt_generic_init($cipher, $key, $iv);
		$result = mcrypt_generic($cipher, $buffer);
		mcrypt_generic_deinit($cipher);
		return base64_encode($result);
	}

	/*
	 Apply tripleDES algorthim for decryption, remove "___EOT" from encrypted file ,
	 so that we can get the real data.
	 */
	function TripleDesDecrypt($buffer,$key,$iv) {
	
		   $buffer= base64_decode($buffer);
		   $cipher = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
		   mcrypt_generic_init($cipher, $key, $iv);
		   $result = mdecrypt_generic($cipher,$buffer);
	        $result=substr($result,0,strpos($result,'___EOT'));
	   	   mcrypt_generic_deinit($cipher);
	 	  return $result;
	}
	
	
	/*AES - START*/
	/*
	 Apply tripleDES algorthim for encryption, append "___EOT" to encrypted file ,
	 so that we can remove it while decrpytion also padding 0's
	 */
	function AESEncrypt($buffer,$key,$iv) {
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'ecb', '');
		$buffer.='___EOT';
		//$buffer.='\0';
		// get the amount of bytes to pad
		$extra = 16 - (strlen($buffer) % 16);
	 	// add the zero padding
		if($extra > 0) {
			for($i = 0; $i < $extra; $i++) {
				$buffer .= '_';
			}
		}
	    mcrypt_generic_init($cipher, $key, $iv);
		$result = mcrypt_generic($cipher, $buffer);
		mcrypt_generic_deinit($cipher);
		return base64_encode($result);
	}

	/*
	 Apply tripleDES algorthim for decryption, remove "___EOT" from encrypted file ,
	 so that we can get the real data.
	 */
	function AESDecrypt($buffer,$key,$iv) {
	
		   $buffer= base64_decode($buffer);
		   //echo $buffer."<br />";
		   $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'ecb', '');
		   mcrypt_generic_init($cipher, $key, $iv);
		   $result = mdecrypt_generic($cipher,$buffer);
	        $result=substr($result,0,strpos($result,'___EOT'));
	   	   mcrypt_generic_deinit($cipher);
	 	  return $result;
	}


	/*AES - END*/

	
	
	
}




/*//get current path
$current_path= getcwd();
$obj = new Cryptography();
$obj->Encrypt($current_path."\\test.docx",$current_path."\\encryption\\test.docx");
$obj->Decrypt($current_path."\\encryption\\test.docx",$current_path."\\decryption\\test.docx");*/
?>