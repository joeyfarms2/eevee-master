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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Export Excel</title>
</head>
<body style="font-size:12px; color:#333; font-family:Tahoma; margin:0; padding:0;">
		
						<table width="510" border="1" cellspacing="1" cellpadding="0" style=" margin-top:10px;  font-size:12px; font-weight:bold;">
							<tr>
                            	<th colspan="2" align="center">
                              Export Excel  Not Borrow Items (<?=$_REQUEST[borrowing_date_start]?> - <?=$_REQUEST[borrowing_date_end]?>)
                                </th>
                            </tr>
                            
                            
                           <tr role="row">
                                <th class="w10 hcenter">No.</th>
                                <th class="hidden-xs w500 a-center">Title of Copy</th>
                            </tr>
                            
                            <? 
							
							$now = date("Y-m-d");
							if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]!=""){
								$word1 = "and borrowing_date >='$_REQUEST[borrowing_date_start]' and borrowing_date<='$_REQUEST[borrowing_date_end]'";
								$word2 = "and updated_date >='$_REQUEST[borrowing_date_start]' and updated_date<='$_REQUEST[borrowing_date_end]'";
								
							}else if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]==""){
								$word1 = "and borrowing_date like '%$_REQUEST[borrowing_date_start]%' ";
								$word2 = "and updated_date like '%$_REQUEST[borrowing_date_start]%' ";
							}
							
							if($_REQUEST[type]==3 or $_REQUEST[type]==7){
								 $sql="select aid,title from magazine where status = '1' and product_main_aid = '$_REQUEST[type]'  ";
							}else{
								 $sql="select aid,title from book where status = '1' and product_main_aid = '$_REQUEST[type]' ";	
							}
							 $exe=mysql_query($sql);
							 $row=0;
							 while($datab=mysql_fetch_array($exe)){
								 if($_REQUEST[type]=="6"){ //หนังสือเล่ม
										 $sqlz="select * from transaction   where  1 and product_type_aid = '1' $word1 and  parent_aid = '$datab[aid]' ";
										$exex=mysql_query($sqlz);	
										}else if($_REQUEST[type]=="7"){ //magazine  เล่ม
											
										 $sqlz="select * from transaction   where  1 and product_type_aid = '2' $word1 and  parent_aid = '$datab[aid]' ";
										$exex=mysql_query($sqlz);	
										}else if($_REQUEST[type]=="3"){ //e magazine
											
										 $sqlx="select * from shelf_history where  1 and product_type_aid = '2' $word2 and action = 'in' and parent_aid = '$datab[aid]'  ";
											$exex=mysql_query($sqlx);
										}else{ //หนังสืออีเล็ก
											$sqlx="select * from shelf_history where  1 and product_type_aid = '1' $word2 and action = 'in' and parent_aid = '$datab[aid]'  ";
											$exex=mysql_query($sqlx);
											
										}
								
								 if(mysql_num_rows($exex)==0){
									 $row++;
                            ?>
                            
                            
                            <tr role="row">
                                <th class="w10 hcenter"><?=$row?>.</th>
                                <th class="hidden-xs w500 a-left"><?=$datab[title]?></th>                        
                            </tr>
                                    <?
								 }
							}
									

									
									
?>
                            
                            
                            
                            
                            </thead>
<tbody>
</table>
  <br />
  
 
  </body>
</html>
