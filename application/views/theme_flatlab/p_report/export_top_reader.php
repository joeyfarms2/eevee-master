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
                    Export Excel  Top Readers (<?= $_REQUEST[borrowing_date_start] ?> - <?= $_REQUEST[borrowing_date_end] ?>)
                </th>
            </tr>
            <tr role="row">
                <th class="w10 hcenter">Rk.</th>
                <th class="hidden-xs w100 a-center">Member ID</th>
                <th class="hidden-xs w100 a-center">Full Name</th>
                <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Total of ReadingItems</th>


            </tr>
            <?php
            if ($_REQUEST[top] != "") {
                $limit = $_REQUEST[top];
            } else {
                $limit = 5;
            }
            if ($_REQUEST[borrowing_date_start] != "" and $_REQUEST[borrowing_date_end] != "") {
                $data = "and borrowing_date >='$_REQUEST[borrowing_date_start]' and borrowing_date<='$_REQUEST[borrowing_date_end]'";
            } else if ($_REQUEST[borrowing_date_start] != "" and $_REQUEST[borrowing_date_end] == "") {
                $data = "and borrowing_date like '%$_REQUEST[borrowing_date_start]%' ";
            }
            $sql = "SELECT transaction.user_aid , COUNT(transaction.user_aid) AS theCount ,user.cid, user.first_name_th, user.last_name_th from transaction  INNER JOIN user ON transaction.user_aid=user.aid where 1 $data  GROUP BY  transaction.user_aid  ORDER BY theCount DESC limit $limit ";
            $exe = mysql_query($sql);
            $row = 0;
            while ($datax = mysql_fetch_array($exe)) {
                $no++;
                if ($no % 2) {
                    $class = "#f2f2f2";
                } else {
                    $class = "#ffffff";
                }
                ?>


                <tr role="row">
                    <th class="w10 hcenter"><?= $no ?>.</th>
                    <th class="hidden-xs w100 a-center"><?= $datau[cid] ?></th>
                    <th class="hidden-xs w100 a-center"><?= $datau[first_name_th] ?> <?= $datau[last_name_th] ?></th>
                    <th class="hidden-xs hidden-sm hidden-md w100 a-center " ><?= $u[0] ?></th>


                </tr>
            <?php } ?>

        </table>
        <br />


    </body>
</html>
