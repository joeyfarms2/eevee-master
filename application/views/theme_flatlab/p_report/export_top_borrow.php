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
                            	<th colspan="4" align="center">
                              Export Excel  Top Borrower (<?=$_REQUEST[borrowing_date_start]?> - <?=$_REQUEST[borrowing_date_end]?>)
                                </th>
                            </tr>
                             <tr role="row">
                                <th class="w10 hcenter">Rk.</th>
                                <th class="hidden-xs w100 a-center">Member ID</th>
                                <th class="hidden-xs w100 a-center">Full Name</th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Borrowing (Times)</th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Borrowed (Times)</th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Returned (Times)</th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Overdue (Times)</th>
 
                                
                            </tr>
                            <?
							if($_REQUEST[top]!=""){
								$limit = $_REQUEST[top];
							}else{
							   $limit=5;	
							}
							$now = date("Y-m-d");
							if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]!=""){
								$data = "and borrowing_date >='$_REQUEST[borrowing_date_start]' and borrowing_date<='$_REQUEST[borrowing_date_end]'";
							}else if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]==""){
								$data = "and borrowing_date like '%$_REQUEST[borrowing_date_start]%' ";
							}
							
					 			$sql = "SELECT parent_aid,user_aid , COUNT(user_aid) AS theCount from transaction where 1 $data  GROUP BY  user_aid  ORDER BY theCount DESC limit $limit ";		 
								$exe=mysql_query($sql);
								while($datax=mysql_fetch_array($exe)){
									 
									$sqlu="select * from user where aid = '$datax[user_aid]' ";
									$exeu=mysql_query($sqlu);
									$datau=mysql_fetch_array($exeu);
									$no++;
									
									$sql1="SELECT user_aid  from transaction where 1 $data  and return_status = '0' and user_aid = '$datax[user_aid]'  ";
									$exe1=mysql_query($sql1);
									$num1=mysql_num_rows($exe1);
									
									$sql2="SELECT user_aid  from transaction where 1 $data  and return_status = '1' and user_aid = '$datax[user_aid]'  ";
									$exe2=mysql_query($sql2);
									$num2=mysql_num_rows($exe2);
									
									$sql3="SELECT user_aid  from transaction where 1 $data  and return_status = '0' and due_date<'$now' and user_aid = '$datax[user_aid]'  ";
									$exe3=mysql_query($sql3);
									$num3=mysql_num_rows($exe3);
									
									
									?>
                            
                            
                            <tr role="row">
                                <th class="w10 hcenter"><?=$no?>.</th>
                                <th class="hidden-xs w100 a-center"><?=$datau[cid]?></th>
                                <th class="hidden-xs w100 a-center"><?=$datau[first_name_th]?> <?=$datau[last_name_th]?></th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=$num1?></th>
                                 <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=$datax[theCount]?></th>
                                  <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=$num2?></th>
                                   <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=$num3?></th>
                            </tr>
                            <? }?>

  </table>
  <br />
  
 
  </body>
</html>
