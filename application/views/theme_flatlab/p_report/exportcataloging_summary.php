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
		
<table width="1500" border="1" cellspacing="1" cellpadding="0" style=" margin-top:10px;  font-size:12px; font-weight:bold;">
							<tr>
                            	<th colspan="13" align="center">
                              Export Excel  Cataloging Summary Year <?=$_REQUEST[type]?>
                                </th>
                            </tr>
                             <tr role="row">
 							<th class="w10 hcenter">Resource Type</th>
                                <th class="hidden-xs w100 a-center">Jan</th>
                                <th class="hidden-xs w100 a-center">Feb</th>
                                <th class="hidden-xs w100 a-center">Mar</th>
                                <th class="hidden-xs w100 a-center">Apr</th>
                                <th class="hidden-xs w100 a-center">May</th>
                                <th class="hidden-xs w100 a-center">Jun</th>
                                <th class="hidden-xs w100 a-center">Jul</th>
                                <th class="hidden-xs w100 a-center">Aug</th>
                                <th class="hidden-xs w100 a-center">Sep</th>
                                <th class="hidden-xs w100 a-center">Oct</th>
                                <th class="hidden-xs w100 a-center">Nov</th>
                                <th class="hidden-xs w100 a-center">Dec</th>
                            </tr>
                           <?
							if($_REQUEST[year]!=""){
							$sql="select * from product_main where product_type_aid !='3' order by weight asc ";
							$exe=mysql_query($sql);
							}
							while($data=mysql_fetch_array($exe)){
							?>
                            <tr role="row">
                                <th class="w10 hleft"><?=$data[name]?></th>
                                <? 
									for($i=1;$i<=12;$i++){
										if($i==1 or $i==2 or $i==3 or $i==4 or $i==5 or $i==6 or $i==7 or $i==8 or $i==9){$i="0$i";}
										
										if($data[aid]=="7" or  $data[aid]=="3"){ //แม็กกาซีน
									    $sqlb="select aid from magazine where 1 and product_main_aid = '$data[aid]' and status = '1' ";
									}else{
										 $sqlb="select aid from book where 1 and product_main_aid = '$data[aid]' and status = '1' ";
									}
									$exeb=mysql_query($sqlb);
									$no=0;
									$total=0;
									while($datab=mysql_fetch_array($exeb)){
										if($id==""){
											$id=$datab[aid];
										}else{
											$id=$id.",".$datab[aid];	
										}
									}
									
									
									if($data[aid]=="6"){ //หนังสือเล่ม
										
										 $sqlz="select parent_aid from transaction   where  1 and product_type_aid = '1'  and  parent_aid in ($id) and updated_date like '%$_REQUEST[year]-$i-%'  ";
										$exez=mysql_query($sqlz);
										$num=mysql_num_rows($exez);
										
										}else if($data[aid]=="7"){ //magazine  เล่ม
											
										 $sqlz="select parent_aid from transaction   where  1 and product_type_aid = '2' and  parent_aid in ($id) and updated_date like '%$_REQUEST[year]-$i-%' ";
										$exez=mysql_query($sqlz);
										$num=mysql_num_rows($exez);
											
											
										}else if($data[aid]=="3"){ //e magazine
											
										 $sqlx="select parent_aid from shelf_history where  1 and product_type_aid = '2'  and action = 'in' and  parent_aid in ($id) and updated_date like '%$_REQUEST[year]-$i-%'  ";
											$exex=mysql_query($sqlx);
											$num=mysql_num_rows($exex);

										}else{ //หนังสืออีเล็ก
											$sqlx="select parent_aid from shelf_history where  1 and product_type_aid = '1'  and action = 'in' and  parent_aid in ($id) and updated_date like '%$_REQUEST[year]-$i-%'  ";
											$exex=mysql_query($sqlx);
											$num=mysql_num_rows($exex);
											
										}
										 $total=$total+$num;
										$no++;
									
								?>
                                <th class="hidden-xs w100 a-right"><?=$total?></th>
                                <? }?>
                            </tr>
                            <? }?>

  </table>
  <br />
  
 
  </body>
</html>
