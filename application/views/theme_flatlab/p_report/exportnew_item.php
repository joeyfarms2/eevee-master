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
		
						<table width="810" border="1" cellspacing="1" cellpadding="0" style=" margin-top:10px;  font-size:12px; font-weight:bold;">
							<tr>
                            	<th colspan="5" align="center">
                              Export Excel  New Resources (<?=$_REQUEST[borrowing_date_start]?> - <?=$_REQUEST[borrowing_date_end]?>)
                                </th>
                            </tr>
                            
                            
                           <tr role="row">
                                <th class="w10 hcenter">No.</th>
                                <th class="hidden-xs w500 a-center">Title</th>
                                <th class="hidden-xs w100 a-center">Author</th>
                                <th class="hidden-xs w100 a-center">ISBN</th>
                                <th class="hidden-xs w100 a-center">Category</th>
                            </tr>
                            
                           <? 
							
							$now = date("Y-m-d");
							if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]!=""){
								$word1 = "and created_date >='$_REQUEST[borrowing_date_start]' and created_date<='$_REQUEST[borrowing_date_end]'";

								
							}else if($_REQUEST[borrowing_date_start]!="" and $_REQUEST[borrowing_date_end]==""){
								$word1 = "and created_date like '%$_REQUEST[borrowing_date_start]%' ";

							}
							
							if($_REQUEST[type]==3 or $_REQUEST[type]==7){
								$table = "magazine";	
								$field="book_field";
							}else{
							    $table = "book";	
								//$field="book_field";
							}
								 $sql="select * from $table where  product_main_aid = '$_REQUEST[type]'  $word1 order by  category asc  ";
							
							 $exe=mysql_query($sql);
							 $row=0;
							 while($datab=mysql_fetch_array($exe)){
								 $row++;
								 
								 $sqlx="select field_data from $field where  	product_main_field_aid = '14' and parent_aid = '$datab[aid]' ";
								 $exex=mysql_query($sqlx);
								 $datax=mysql_fetch_array($exex);
								 
                            ?>
                            
                            
                            <tr role="row">
                                <th class="w10 hcenter"><?=$row?>.</th>
                                <th class="hidden-xs w500 a-left"><?=$datab[title]?></th>  
                                <th class="hidden-xs w100 a-left"><?=$datab[author]?></th>  
                                <th class="hidden-xs w100 a-left"><?=$datax[field_data]?></th>  
                                <th class="hidden-xs w100 a-left">
                                	<?
									
									$count = strlen($datab[category]);
									$cat = substr("$datab[category]", 0, $count-1);
									$cat = substr("$cat", 1);
									//$count = strlen($datab[category]);
									
                                    $sqlc="select * from product_category where product_main_aid = '$datab[product_main_aid]' and aid in ($cat) ";
									$exec=mysql_query($sqlc);
									while($datac=mysql_fetch_array($exec)){
										
										echo "$datac[name] ";
										
									}
                                    ?>
                                </th>                        
                            </tr>
                                    <?
							}
									

									
									
?>
                            
                            
                            
                            
                            </thead>
<tbody>
</table>
  <br />
  
 
  </body>
</html>
