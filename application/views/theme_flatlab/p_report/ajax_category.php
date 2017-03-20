<?
$sql="select * from product_category where product_main_aid = '$_REQUEST[id]' ";
$exe=mysql_query($sql);
$row=0;
while($data=mysql_fetch_array($exe)){
?>
<div style=" width:33%; float:left;">
 <input  type="checkbox" value="<?=$data[aid]?>" name="cat_[]" /> <?=$data[name]?>
 </div>
<?	
$row++;
}
?>
<input type="hidden" value="<?=$row?>" id="total" name="total" />