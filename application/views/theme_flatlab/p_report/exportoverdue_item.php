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
                            	<th colspan="3" align="center">
                              Export Excel  Overdue Items Rating (<?=$_REQUEST[borrowing_date_start]?> - <?=$_REQUEST[borrowing_date_end]?>)
                                </th>
                            </tr>
                             <tr role="row">

                                <th class="hidden-xs w100 a-center">Borrowed (Items)</th>
                                <th class="hidden-xs w100 a-center">Overdue (Items)</th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Overdue Rating (%)</th>

                            </tr>
                                <? 
							

							
							if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]!=""){
								$data = "and borrowing_date >='$_REQUEST[borrowing_date_start]' and borrowing_date<='$_REQUEST[borrowing_date_end]'";
							}else if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]==""){
								$data = "and borrowing_date like '%$_REQUEST[borrowing_date_start]%' ";
							}
							
					 			$sql = "SELECT user_aid  from transaction where 1 $data  ";		 
								$datarow=mysql_query($sql);
								$num_all=mysql_num_rows($datarow);
								$row=0;
								
								$sql2 = "SELECT user_aid  from transaction where 1 $data and return_status = '0' ";		 
								$dataoverdue=mysql_query($sql2);
								$num_overdue=mysql_num_rows($dataoverdue);
								
									

									?>
                            
                            
                            <tr role="row">
                                <th class="hidden-xs w100 a-center"><?=$num_all?></th>
                                <th class="hidden-xs w100 a-center"><?=$num_overdue?></th>
                                <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=number_format(($num_overdue*100)/$num_all,2)?></th>


                            </tr>
                          

  </table>
  <br />
  
 
  </body>
</html>
