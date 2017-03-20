<?php

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
                              Export Excel   Top Most Popular Categories (<?=$_REQUEST[borrowing_date_start]?> - <?=$_REQUEST[borrowing_date_end]?>)
                                </th>
                            </tr>
                             <tr role="row">
                                <th class="w10 hcenter">Rk.</th>
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
							
					 			$sql = "SELECT aid,name from product_category  where product_main_aid = '$_REQUEST[type]' and status = '1'     ";		 
								$exe=mysql_query($sql);
								while($data=mysql_fetch_array($exe)){
									$no++;
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
										 
									
									}
									$array_data[$no] = "$total.$datat[name].$data[name]";
									
                            }
									
								
							rsort($array_data);
							$count = count($array_data);
							for($i=0;$i<$count;$i++) {
								
								$array = $array_data[$i];
								 $data_array = explode (".","$array");
								 $row++;
                            ?>
                            
                            
                            <tr role="row">
                                <th class="w10 hcenter"><?=$row?>.</th>
                                <th class="hidden-xs w100 a-center"><?=$data_array[1];?></th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-left " ><?=$data_array[2];?></th>
                                 <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=$data_array[0];?></th>
                            </tr>
                                    <?
							}?>
  </table>
  <br />
  
 
  </body>
</html>
