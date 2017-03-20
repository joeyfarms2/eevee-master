<?php
$q = urldecode($_GET["q"]);
$sql = "select * from user  where cid  like'%$q%' or username  like'%$q%' or email  like'%$q%' or first_name_th  like'%$q%' or last_name_th  like'%$q%' or contact_number  like'%$q%'  LIMIT 20";
$results = mysql_query($sql);
while ($row = mysql_fetch_array($results)) {
	$id = $row[aid]; // ฟิลที่ต้องการส่งค่ากลับ
	$name = "$row[cid] => $row[first_name_th] $row[last_name_th] => $row[email] "; // ฟิลที่ต้องการแสดงค่า
	// ป้องกันเครื่องหมาย '
	$name = str_replace("'", "'", $name);
	// กำหนดตัวหนาให้กับคำที่มีการพิมพ์
	$display_name = preg_replace("/(" . $q . ")/i", "<b>$1</b>", $name);
	echo "<li onselect=\"this.setText('$name').setValue('$id');\">$display_name</li>";
}
?>
