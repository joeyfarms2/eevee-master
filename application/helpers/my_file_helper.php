<?php
/**
 * File Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Tisa Pathumwan
 * 
 * 
 */

// ------------------------------------------------------------------------
/****************************************************************/
/* Get content file
/* Added by: Oui
/****************************************************************/
function get_content_file($content_file_name='',$default_file=DEFAULT_CONTENT_VIEW){
	if( !$content_file_name || !isset($content_file_name) || is_blank($content_file_name) ){
		return( VIEW_PATH.$default_file.EXT);
	}else if(is_file(VIEW_PATH.$content_file_name.EXT)){
		return( VIEW_PATH.$content_file_name.EXT );
	}else{
		return( VIEW_PATH.$default_file.EXT);
	}
}

/****************************************************************/
/* Upload file
/* Added by: Oui
/****************************************************************/
function upload_file_and_unzip($controller,$field_image_name,$upload_path,$new_file_name="",$file_allow_type="",$max_size="",$max_width="",$max_height=""){
	//echo print_r($_FILES[$field_image_name])."<hr>";
	if( !is_blank($controller) && !is_blank($field_image_name) && !is_blank($upload_path) ){
		//Start upload file
		//echo ">>".$field_image_name."<BR>";
		if(!is_blank($new_file_name)){
			// $file_type = substr(strrchr($_FILES[$field_image_name]["name"], "."), 0);
			// $file_name = $new_file_name.$file_type;
			$file_name = $new_file_name;
			$_FILES[$field_image_name]["name"]  = $file_name;
		}
		
		$file_type = strtolower(substr(strrchr($_FILES[$field_image_name]["name"], "."), 1));
		$allow_type = CONST_ALLOW_FILE_TYPE_DEFAULT;
		if( !is_blank($file_allow_type) ) $allow_type = $file_allow_type;
		$allow_type_arr_list = preg_split("/\|+/", $allow_type, 0, PREG_SPLIT_NO_EMPTY);
		
		// echo "file_type = $file_type , allow_type = $allow_type<BR />";
		// print_r($allow_type_arr_list);
		// exit(0);
		if( !in_array($file_type,$allow_type_arr_list) ){
			$data["error_msg"] = "This file type [.".$file_type."] was not allowed.";
			return $data;
		}

		if($max_size > 0){
			$size = $_FILES[$field_image_name]["size"];
			if($size > $max_size){
				$data["error_msg"] = "File size must not over ".get_size($max_size);
				return $data;
			}
		}
		

		if(!is_dir($upload_path)){
			mkdir($upload_path);
		}
		
		$config_upload['upload_path'] = $upload_path;
		// if( !is_blank($allow_type) ) $config_upload['allowed_types'] = $allow_type; else $config_upload['allowed_types'] = CONST_ALLOW_FILE_TYPE_DEFAULT;
		$config_upload['allowed_types'] = "*";
		if( !is_blank($max_size) ) $config_upload['max_size']	= $max_size;
		if( !is_blank($max_width) ) $config_upload['max_width']  = $max_width;
		if( !is_blank($max_height) ) $config_upload['max_height']  = $max_height;

		// echo "upload_path = $upload_path , tmp_name = ".$_FILES[$field_image_name]["tmp_name"]."<BR />";
		if($file_type == "zip"){
			require_once("./include/image_toolbox.class.php");
			// create object
			$zip = new ZipArchive() ;
			// open archive
			if ($zip->open($_FILES[$field_image_name]["tmp_name"]) !== TRUE){
				die ('Could not open archive');
			}

			// extract contents to destination directory
			$zip->extractTo($upload_path);
			// close archive
			$zip->close();
		}else{
			$controller->load->library('upload', $config_upload);
			$controller->upload->initialize($config_upload);
			if( ! $controller->upload->do_upload($field_image_name))
			{
				// echo clear_tags($controller->upload->display_errors());
				// exit(0);
				return clear_tags($controller->upload->display_errors());
			}	
			else
			{
				// echo print_r($controller->upload->data());
				// exit(0);
				return $controller->upload->data();
			}
		}

		
	}
}

/****************************************************************/
/* Upload file
/* Added by: Oui
/****************************************************************/
function upload_file_and_generate($controller,$field_image_name,$upload_path,$new_file_name="",$file_allow_type="",$max_size="",$max_width="",$max_height=""){
	//echo print_r($_FILES[$field_image_name])."<hr>";
	if( !is_blank($controller) && !is_blank($field_image_name) && !is_blank($upload_path) ){
		//Start upload file
		//echo ">>".$field_image_name."<BR>";
		if(!is_blank($new_file_name)){
			// $file_type = substr(strrchr($_FILES[$field_image_name]["name"], "."), 0);
			// $file_name = $new_file_name.$file_type;
			$file_name = $new_file_name;
			$_FILES[$field_image_name]["name"]  = $file_name;
		}
		
		$file_type = strtolower(substr(strrchr($_FILES[$field_image_name]["name"], "."), 1));
		$allow_type = CONST_ALLOW_FILE_TYPE_DEFAULT;
		if( !is_blank($file_allow_type) ) $allow_type = $file_allow_type;
		$allow_type_arr_list = preg_split("/\|+/", $allow_type, 0, PREG_SPLIT_NO_EMPTY);
		
		// echo "file_type = $file_type , allow_type = $allow_type<BR />";
		// print_r($allow_type_arr_list);
		// exit(0);
		if( !in_array($file_type,$allow_type_arr_list) ){
			$data["error_msg"] = "This file type [.".$file_type."] was not allowed.";
			return $data;
		}

		if($max_size > 0){
			$size = $_FILES[$field_image_name]["size"];
			if($size > $max_size){
				$data["error_msg"] = "File size must not over ".get_size($max_size);
				return $data;
			}
		}
		

		if(!is_dir($upload_path)){
			mkdir($upload_path);
		}
		
		$config_upload['upload_path'] = $upload_path;
		// if( !is_blank($allow_type) ) $config_upload['allowed_types'] = $allow_type; else $config_upload['allowed_types'] = CONST_ALLOW_FILE_TYPE_DEFAULT;
		$config_upload['allowed_types'] = "*";
		if( !is_blank($max_size) ) $config_upload['max_size']	= $max_size;
		if( !is_blank($max_width) ) $config_upload['max_width']  = $max_width;
		if( !is_blank($max_height) ) $config_upload['max_height']  = $max_height;

		// echo "upload_path = $upload_path , tmp_name = ".$_FILES[$field_image_name]["tmp_name"]."<BR />";
		if($file_type == "zip"){
			// create object
			$zip = new ZipArchive() ;
			// open archive
			if ($zip->open($_FILES[$field_image_name]["tmp_name"]) !== TRUE){
				die ('Could not open archive');
			}

			$tmp_upload_path = $upload_path."/tmp";
			if(!is_dir($tmp_upload_path)){
				mkdir($tmp_upload_path);
			}
			// extract contents to destination directory
			$zip->extractTo($tmp_upload_path);
			// close archive
			$zip->close();

			generate_image($upload_path, $tmp_upload_path);

			// echo "tmp_upload_path = $tmp_upload_path";
		}else if($file_type == "pdf"){
			$tmp_upload_path = $upload_path."/tmp";
			if(!is_dir($tmp_upload_path)){
				mkdir($tmp_upload_path);
			}

			$name_to = $tmp_upload_path."/page.jpg"; // Output File
			exec('convert -density 500 "'.$_FILES[$field_image_name]["tmp_name"].'" -colorspace RGB -quality 100 -resize 1024 "'.$name_to.'"', $output, $return_var);
			if($return_var == 0){ //if exec successfuly converted pdf to jpg
				generate_image($upload_path, $tmp_upload_path);
				return "Success";
			}else{
				return "Conversion failed.<br />".$output;
			}
		}else{
			$controller->load->library('upload', $config_upload);
			$controller->upload->initialize($config_upload);
			if( ! $controller->upload->do_upload($field_image_name))
			{
				// echo clear_tags($controller->upload->display_errors());
				// exit(0);
				return clear_tags($controller->upload->display_errors());
			}	
			else
			{
				// echo print_r($controller->upload->data());
				// exit(0);
				return $controller->upload->data();
			}
		}

		
	}
}

function generate_image($upload_path, $tmp_upload_path){
	require_once("./include/image_toolbox.class.php");
	$i=0;
	if(is_var_array(glob($tmp_upload_path."/*.*"))){
		foreach (glob($tmp_upload_path."/*.*") as $filename){
			// echo "filename = $filename <br>";
			$file_type = strtolower(substr(strrchr($filename, "."), 1));
			// echo "file_type = $file_type <br>";
			// echo "upload_path = $upload_path <br>";

			if($file_type == "jpg" || $file_type == "jpeg"){
				$i++;
				$file_info = getimagesize($filename);
				$file_width = $file_info[0];
				$file_height = $file_info[1];

				$width = "";
				$height = "120";
				
				if($file_width < $width) $width = $file_width;
				if($file_height < $height) $height = $file_height;
				$img_name = get_text_pad($i, '0', 4)."-thumb.jpg";
				// echo "img_name = $img_name <br>";
				$ITB_thumb = new Image_Toolbox($filename);
				$ITB_thumb->newOutputSize($width, $height, 1);
				$result = $ITB_thumb->save($upload_path."/".$img_name, $file_type, 99);

				$width = "";
				$height = "1024";
				
				if($file_width < $width) $width = $file_width;
				if($file_height < $height) $height = $file_height;
				$img_name = get_text_pad($i, '0', 4)."-large.jpg";
				$ITB_thumb = new Image_Toolbox($filename);
				$ITB_thumb->newOutputSize($width, $height, 1);
				$result = $ITB_thumb->save($upload_path."/".$img_name, $file_type, 99);

			}
			// echo "file_type = $file_type <BR>";
		}
	}
	deleteDir($tmp_upload_path);	
}

/****************************************************************/
/* Upload file
/* Added by: Oui
/****************************************************************/
function upload_file($controller,$field_image_name,$upload_path,$new_file_name="",$file_allow_type="",$max_size="",$max_width="",$max_height=""){
	//echo print_r($_FILES[$field_image_name])."<hr>";
	if( !is_blank($controller) && !is_blank($field_image_name) && !is_blank($upload_path) ){
		//Start upload file
		//echo ">>".$field_image_name."<BR>";
		if(!is_blank($new_file_name)){
			// $file_type = substr(strrchr($_FILES[$field_image_name]["name"], "."), 0);
			// $file_name = $new_file_name.$file_type;
			$file_name = $new_file_name;
			$_FILES[$field_image_name]["name"]  = $file_name;
		}
		
		$file_type = strtolower(substr(strrchr($_FILES[$field_image_name]["name"], "."), 1));
		$allow_type = CONST_ALLOW_FILE_TYPE_DEFAULT;
		if( !is_blank($file_allow_type) ) $allow_type = $file_allow_type;
		$allow_type_arr_list = preg_split("/\|+/", $allow_type, 0, PREG_SPLIT_NO_EMPTY);
		
		// echo "file_type = $file_type , allow_type = $allow_type<BR />";
		// print_r($allow_type_arr_list);
		// exit(0);
		if( !in_array($file_type,$allow_type_arr_list) ){
			$data["error_msg"] = "This file type [.".$file_type."] was not allowed.";
			return $data;
		}

		if($max_size > 0){
			$size = $_FILES[$field_image_name]["size"];
			if($size > $max_size){
				$data["error_msg"] = "ขนาดไฟล์ต้องไม่เกิน ".get_size($max_size);
				return $data;
			}
		}
		

		if(!is_dir($upload_path)){
			mkdir($upload_path);
		}
		
		$config_upload['upload_path'] = $upload_path;
		// if( !is_blank($allow_type) ) $config_upload['allowed_types'] = $allow_type; else $config_upload['allowed_types'] = CONST_ALLOW_FILE_TYPE_DEFAULT;
		$config_upload['allowed_types'] = "*";
		if( !is_blank($max_size) ) $config_upload['max_size']	= $max_size;
		if( !is_blank($max_width) ) $config_upload['max_width']  = $max_width;
		if( !is_blank($max_height) ) $config_upload['max_height']  = $max_height;
		
		$controller->load->library('upload', $config_upload);
		$controller->upload->initialize($config_upload);
		if( ! $controller->upload->do_upload($field_image_name))
		{
			// echo clear_tags($controller->upload->display_errors());
			// exit(0);
			return clear_tags($controller->upload->display_errors());
		}	
		else
		{
			// echo print_r($controller->upload->data());
			// exit(0);
			return $controller->upload->data();
		}
	}
}

/****************************************************************/
/* Upload image
/* Added by: Oui
/****************************************************************/
function upload_image($field_image_name,$upload_path,$new_file_name="",$max_file_size=150000,$width=9999,$height=9999,$qulity=99,$ratio=0){
	//echo print_r($_FILES[$field_image_name])."<hr>";
	require_once("./include/image_toolbox.class.php");
	if( !is_blank($field_image_name) && !is_blank($upload_path) ){
		
		create_directories($upload_path);

		$file_info = getimagesize($_FILES[$field_image_name]['tmp_name']);
		$file_width = $file_info[0];
		$file_height = $file_info[1];
		
		if($file_width < $width) $width = $file_width;
		if($file_height < $height) $height = $file_height;

		$file_type = substr(strrchr($_FILES[$field_image_name]["name"], "."), 1);
		if( strtolower($file_type) != "jpg" && strtolower($file_type) != "jpeg" && strtolower($file_type) != "gif" && strtolower($file_type) != "png" ){
			$data["error_msg"] = "รูปจะต้องเป็น JPG, JPEG, GIF หรือ PNG เท่านั้น.";
			return $data;
		}
		
		if($max_file_size > 0){
			$size = $_FILES[$field_image_name]["size"];
			if($size > $max_file_size){
				$data["error_msg"] = "ขนาดรูปต้องไม่เกิน ".get_size($max_file_size);
				return $data;
			}
		}
		
		$ITB_thumb = new Image_Toolbox($_FILES[$field_image_name]['tmp_name']);
		$ITB_thumb->newOutputSize($width, $height, $ratio);
		$result = $ITB_thumb->save($upload_path."/".$new_file_name, $file_type, $qulity);

		if($result){
			$data["file_name"] = $new_file_name;
			$data["error_msg"] = "";
			return $data;
		}else{
			$data["error_msg"] = "error";
			return $result;
		}

	}

}

function upload_sample_gallery($field_name,$upload_path,$article_aid){
	//echo print_r($_FILES[$field_name])."<hr>";
	require_once("./include/image_toolbox.class.php");
	if( !is_blank($field_name) && !is_blank($upload_path) ){
		
		if(!is_dir($upload_path)){
			mkdir($upload_path);
		}
		
		$file_type = strtolower(substr(strrchr($_FILES[$field_name]["name"], "."), 1));
		if( $file_type != "jpg" && $file_type != "gif" && $file_type != "png" && $file_type != "zip" ){
			$data["error_msg"] = "Only JPG, GIF, PNG or ZIP be allowed.";
			return $data;
		}
		
		$image_list = array();
		$image_result_list = array();
		$tmp_path = "./uploads/tmp/".$article_aid;
		if($file_type == "zip"){
			ob_start();
			if(!is_dir($tmp_path)){
				mkdir($tmp_path);
			}

			if(is_var_array(glob($tmp_path."/*.*"))){
				foreach (glob($tmp_path."/*.*") as $filename){
					unlink($filename);
				}
			}
			
			// create object
			$zip = new ZipArchive() ;
			// open archive
			if ($zip->open($_FILES[$field_name]["tmp_name"]) !== TRUE){
				die ('Could not open archive');
			}
			// extract contents to destination directory
			$zip->extractTo($tmp_path);
			// close archive
			$zip->close();
			
			unset($data);
			if(is_var_array(glob($tmp_path."/*.*"))){
				foreach (glob($tmp_path."/*.*") as $filename){
					//echo "$filename size " . basename($filename) . "<br>";
					$data["filename"] = basename($filename);
					$data["file"] = $filename;
					$image_list[] = $data;
				}
			}
			
		}else{
			unset($data);
			$data["filename"] = $_FILES[$field_name]["name"];
			$data["file"] = $_FILES[$field_name]['tmp_name'];
			$image_list[] = $data;
		}
		
		if( is_var_array($image_list) ){
			foreach($image_list as $item){
				$file_name = get_array_value($item,"filename","");
				$file = get_array_value($item,"file","");
				unset($data);
								
				do{
					$file_type = strtolower(substr(strrchr($file_name, "."), 0));
					$file_name_new = random_string('alnum', 8);
					//echo "-".$upload_path."/".$file_name_new.$file_type."<br>";
				}while( is_file($upload_path."/".$file_name_new.$file_type) );
				
				$file_type = strtolower(substr(strrchr($file_name, "."), 1));
				if( $file_type == "jpg" || $file_type == "gif" || $file_type == "png" ){
				
					$file_info = getimagesize($file);
					$file_width = $file_info[0];
					$file_height['height'] = $file_info[1];
					
					$width = 0;
					$height = 120;
					$ratio = 1;
					$qulity = 90;
					if($file_width < $width) $width = $file_width;
					if($file_height < $height) $height = $file_height;
					$ITB_thumb = new Image_Toolbox($file);
					$ITB_thumb->newOutputSize($width, $height, $ratio);
					$result_thumb = $ITB_thumb->save($upload_path."/thumb/".$file_name_new.".".$file_type, $file_type, $qulity);
					if($result_thumb){
						$data["file_name_thumb"] = $file_name_new.".".$file_type;
					}else{
						$image_result_list["error_msg"] = "error";
						return $image_result_list;
					}
					
					$width = 0;
					$height = 1024;
					$ratio = 1;
					$qulity = 100;
					if($file_width < $width) $width = $file_width;
					if($file_height < $height) $height = $file_height;
					$ITB_ori = new Image_Toolbox($file);
					$ITB_ori->newOutputSize($width, $height, $ratio);
					$result_ori = $ITB_ori->save($upload_path."/".$file_name_new.".".$file_type, $file_type, $qulity);
					if($result_ori){
						$data["file_name_ori"] = $file_name_new.".".$file_type;
					}else{
						$image_result_list["error_msg"] = "error";
						return $image_result_list;
					}
					
					$image_result_list["name_list"][] = $data;
	
					//echo "<br>$field_name : $file_name = $file_name_new$file_type : width = $width : height = $height;";
				}
			}//foreach
		}//if( is_var_array($image_list) )
		
		if(is_dir($tmp_path)){
			if(is_var_array(glob($tmp_path."/*.*"))){
				foreach (glob($tmp_path."/*.*") as $filename){
					unlink($filename);
				}
			}
		}
		
		return $image_result_list;
	}
}


/****************************************************************/
/* Get file size
/* Added by: Oui
/****************************************************************/
function get_size($size){
	//echo "size = ".$size."<br>";
	if($size<1024){
		return $size." Byte";
	}else if($size < 1024000){
		return round($size/1024)." KB";
	}else{
		return number_format(round($size/1024/1024,1))." MB";
	}
}

/****************************************************************/
/* Get file type
/* Added by: Oui
/****************************************************************/
function get_file_type($str){
	//echo "str = ".$str."<br>";
	$result = "";
	if(!is_blank($str)){
		$result = ".".str_replace("|", ", .", $str);
	}
	return $result;
}

/****************************************************************/
/* Create directories
/* Added by: Oui
/****************************************************************/
function create_directories($full_path){
	if(!is_dir($full_path)){
		$tmp = explode("/", $full_path);
		$path = "";
		foreach($tmp as $item){
			if($item == '.'){
				$path = '.';
			}else if(!is_blank($item)){
				$path .= '/'.$item;
				if(!is_dir($path)){
					mkdir($path, 0777, true);
					// chown($path,"bookdose");
					chmod($path,0777);
				}else{
					// chmod($path, 0777);
				}
			}
		}
	}
}

/****************************************************************/
/* Delete directories
/* Added by: Oui
/****************************************************************/
function deleteDir($dir){
	// echo "dir = $dir <br />";
	if(substr($dir, strlen($dir)-1, 1) != '/' && substr($dir, strlen($dir)-1, 1) != '.' && substr($dir, strlen($dir)-1, 1) != ''){
		$dir .= '/';
	}

	if(is_blank($dir) || strlen($dir) < 8){
		return false;
	}

	if(is_dir($dir)){
		if($handle = opendir($dir)){
			while ($obj = readdir($handle)){
				if($obj != '.' && $obj != '..'){
					if(is_dir($dir.$obj)){
						if(!deleteDir($dir.$obj)){
							return false;
						}
					}
					else if(is_file($dir.$obj)){
						if(!unlink($dir.$obj)){
							return false;
						}
					}
				}
			}

			closedir($handle);

			if(!@rmdir($dir)){
				return false;
			}
			return true;
		}
	}
	return false;
}

/****************************************************************/
/* Create zip
/* Added by: Oui
/****************************************************************/
function create_empty_folder_to_zip($full_path, $folder_name){
	$zip = new ZipArchive;
	if ($zip -> open($full_path, ZipArchive::CREATE) === TRUE){
		if(is_var_array($folder_name)){
			foreach($folder_name as $item){
				$zip->addEmptyDir($item);
			}
		}else if(!is_blank($folder_name)){
			$zip->addEmptyDir($folder_name);
		}
		$zip->close();
	}else{
		echo 'failed';
		return false;
	}
}


?>