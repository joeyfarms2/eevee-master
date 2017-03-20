<?

header("Content-type: text/html; charset=utf-8");

ob_start();
session_start();

$datenow = date("Y_m_d_h_i_s");

$strExcelFileName="EXPORT_$datenow.xls";

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=\"$strExcelFileName\""); 
header("Pragma: no-cache"); 
header("Expires: 0"); 


##
function month($t) {
$tm=array('มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม ','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม');
    $t=ereg_replace ('[^0-9]','',$t);
    $m=$tm[($t)-1];
return $m;
}

function month_eng_d($t) {
$tm=array('Jan','Feb','Mar','Apr','May','Jun','Jul ','Aug','Seb','Oct','Nov','Dec');
    $t=ereg_replace ('[^0-9]','',$t);
    $m=$tm[($t)-1];
return $m;
}
$category="";
 for($i=0;$i<=$_REQUEST[total];$i++){
		 if($_REQUEST[cat_][$i]!=""){
			 
			 $category = $_REQUEST[cat_][$i];
			 if($word_category==""){
				 $word_category="and  category like ('%,$category,%')";
			 }else{
				 $word_category= $word_category." or  category like ('%,$category,%')";
			 }
			
		 }
	 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Export Excel</title>
</head>
<body style="font-size:12px; color:#333; font-family:Tahoma; margin:0; padding:0;">
		
<table width="1500" border="1" cellspacing="1" cellpadding="0" style=" margin-top:10px;  font-size:12px; font-weight:bold;">
							<tr>
                            	<th colspan="4" align="center">
                              Export Excel   Top Most Popular Items (<?=$_REQUEST[borrowing_date_start]?> - <?=$_REQUEST[borrowing_date_end]?>)
                                </th>
                            </tr>
                             <tr role="row">
                                <th class="w10 hcenter">No.</th>
                                <th class="w10 hcenter">Barcode</th>
                                <th class="hidden-xs w100 a-center">Title of Copy</th>
                                <th class="hidden-xs w100 a-center">Resource Type</th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Category</th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Borrowed (Times)</th>
                            </tr>
                            <? 
								if($_REQUEST[top]!=""){
								$limit = $_REQUEST[top];
							}else{
							   $limit=5;	
							}
							
							$now = date("Y-m-d");
							if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]!=""){
								$word1 = "and borrowing_date >='$_REQUEST[borrowing_date_start]' and borrowing_date<='$_REQUEST[borrowing_date_end]'";
								$word2 = "and updated_date >='$_REQUEST[borrowing_date_start]' and updated_date<='$_REQUEST[borrowing_date_end]'";
								
							}else if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]==""){
								$word1 = "and borrowing_date like '%$_REQUEST[borrowing_date_start]%' ";
								$word2 = "and updated_date like '%$_REQUEST[borrowing_date_start]%' ";
							}
							
							
							   $sqlt="select * from product_main where aid = '$_REQUEST[type]'  ";
							   $exet=mysql_query($sqlt);
							   $datat=mysql_fetch_array($exet);
							
					 			
									
									if($_REQUEST[type]=="2" or  $_REQUEST[type]=="4"){ //แม็กกาซีน
									    $sqlb="select aid from magazine where 1 $word_category and status = '1' ";
									}else{
										 $sqlb="select aid from book where 1 $word_category and status = '1' ";
									}
									$exeb=mysql_query($sqlb);
									$no=0;
									while($datab=mysql_fetch_array($exeb)){
									
									$total=0;
									if($_REQUEST[type]=="1"){ //หนังสือเล่ม
										
										 $sqlz="select * from transaction   where  1 and product_type_aid = '1' $word1 and  parent_aid = '$datab[aid]' ";
										$exez=mysql_query($sqlz);
										$num=mysql_num_rows($exez);
										
										}else if($_REQUEST[type]=="2"){ //magazine  เล่ม
											
										 $sqlz="select * from transaction   where  1 and product_type_aid = '2' $word1 and  parent_aid = '$datab[aid]' ";
										$exez=mysql_query($sqlz);
										$num=mysql_num_rows($exez);
											
											
										}else if($_REQUEST[type]=="4"){ //e magazine
											
										 $sqlx="select * from shelf_history where  1 and product_type_aid = '2' $word2 and action = 'in' and parent_aid = '$datab[aid]'  ";
											$exex=mysql_query($sqlx);
											$num=mysql_num_rows($exex);
											
											
										}else{ //หนังสืออีเล็ก
											$sqlx="select * from shelf_history where  1 and product_type_aid = '1' $word2 and action = 'in' and parent_aid = '$datab[aid]'  ";
											$exex=mysql_query($sqlx);
											$num=mysql_num_rows($exex);
											
										}
										 
										 $total=$total+$num;
										 
									$array_data[$no] = "$total.$datab[aid]"; //จำนวน/ชื่อหนังสือ
									$no++;
									}
									
									
                          
									
							//
							rsort($array_data);
							//print_r($array_data);
							$count = $_REQUEST[top];
							
							$count2 = count($array_data);
							if($count2<$count){
								$count=$count2;
							}
							
							$row=0;
							for($i=0;$i<$count;$i++) {
								
								 $array = $array_data[$i];
								 $data_array2 = explode (".","$array");
								 
								if($_REQUEST[type]=="2" or  $_REQUEST[type]=="4"){
									$table = "magazine";
									$table_copy = "magazine_copy";
								}else{
									$table = "book";
									$table_copy = "book_copy";
								}
								$sqltitle = "SELECT title,category from $table  where aid = '$data_array2[1]'    ";		 
								$exetitle=mysql_query($sqltitle);
								$datatitle=mysql_fetch_array($exetitle);
								
								$sqlbarcode = "select barcode from $table_copy where parent_aid = '$data_array2[1]' ";
								$exebarcode = mysql_query($sqlbarcode);
								$barcode = mysql_fetch_array($exebarcode);
								
								$row++;
                            ?>
                            
                            
                            <tr role="row">
                                <th class="w10 hcenter"><?=$row?>.</th>
                                <th class="w10 hcenter"> 
                                <?=$barcode[barcode]?>
                                </th>
                                <th class="hidden-xs w100 a-left"><?=$datatitle[title]?></th>
                                <th class="hidden-xs w100 a-center"><?=$datat[name]?></th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-left " >
                                <?
								$str=strlen($datatitle[category]);
								$newcat = substr($datatitle[category], 1, $str-2);
								$sqlcat="select * from product_category where aid in ($newcat)";
								$execat=mysql_query($sqlcat);
								$no=0;
								while($datacat=mysql_fetch_array($execat)){
									if($no!=0){
										echo ",";
									}
									echo " $datacat[name] ";
									$no++;
								}
								?>
                                </th>
                                 <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=$data_array2[0]?></th>
                            </tr>
                                    <?
							}?>
  </table>
  <br />
  
 
  </body>
</html>
