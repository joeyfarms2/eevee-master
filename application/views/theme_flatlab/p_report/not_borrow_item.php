<?php

/*$category="";
for($i=0;$i<=$_REQUEST[total];$i++){
if($_REQUEST[cat_][$i]!=""){

$category = $_REQUEST[cat_][$i];
if($word_category==""){
$word_category="and  category like ('%,$category,%')";
$data_export="&cat_[]=$category";
}else{
$word_category= $word_category." or  category like ('%,$category,%')";
$data_export="$data_export&cat_[]=$category";
}

}
}*/
// echo $word_category;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="result-msg-box"></div>
<span style=" font-size:22px;">Not Borrow Items</span><br /><br />
<div class="row">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">			
                    <form action="" method="post">
                        <div class="form-group" style=" margin-top:20px; float:left; width:100%;">
                                                                <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Transaction Date : </label>

                            <div class="col-md-12 col-lg-8">
                                <div class="input-group date form_datetime-adv" data-date="">
                                    <span class="input-group-addon">From</span>
                                    <input class="form-control" type="text" id="borrowing_date_start" name="borrowing_date_start" value="<?= $_REQUEST[borrowing_date_start] ?>"  />

                                    <span class="input-group-addon">To</span>
                                    <input class="form-control" type="text" id="borrowing_date_end" name="borrowing_date_end" value="<?= $_REQUEST[borrowing_date_end] ?>"  />

                                    <div class="input-group-btn">
                                        <button class="btn btn-danger" type="button" onclick="clearValue('borrowing_date_start');clearValue('borrowing_date_end');">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div> <input type="submit" id="submit" name="submit" value="Submit" class="btn btn-primary"></div>

                        </div>


                        <br><br> 
                        <div class="form-group" style=" margin-top:20px; float:left; width:100%;">
                            <!--<label class="col-md-12 col-lg-2 control-label">Resource Type</label>-->
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Resource Type : </label>

                            <div class="col-md-12 col-lg-8">

                                <select name="type" style="padding:5px;" onchange="category(this.value)" class="right form-control">
                                    <!-- <option value="">Select  Resource Type</option> -->
                                    <? $sql="select * from product_main where (aid ='1' OR aid ='2' OR aid ='8')  order by weight asc";
                                    $exe=mysql_query($sql);
                                    while($data=mysql_fetch_array($exe)){
                                    ?>
                                    <option value="<?= $data[aid] ?>" <? if($_REQUEST[type]==$data[aid]){?> selected="selected" <? }?>><?= $data[name] ?></option>
                                    <? }?>
                                </select>
                            </div>
                        </div>

                        <br><br> 

                    </form>     
                </div>
            </div>
        </section>
    </div>
</div>
<a target="_blank" class="btn btn-info" href="/admin/not_borrow_item/export?borrowing_date_start=<?= $_REQUEST[borrowing_date_start] ?>&borrowing_date_end=<?= $_REQUEST[borrowing_date_end] ?>&type=<?= $_REQUEST[type] ?>">
    Export to Excel
</a>       
        <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper" style="margin-top:20px">
            <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                <thead>

                     <tr role="row" bgcolor="#efefef">
                        <th class="w10 hcenter">No.</th>
                        <th class="w10 hcenter">Barcode</th>
                        <th class="w10 hcenter">Call No.</th>
                        <th class="hidden-xs w100 a-center">Title of Copy</th>
                        <th class="w10 hcenter">Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($result_list as $key => $value) {
                        if($key%2){
                            $class = "#f2f2f2";
                        }else{
                            $class = "#ffffff";
                        }
                    ?>
                    <tr role="row"  bgcolor="<?= $class ?>">
                        <td class="w10 hcenter"><?= $key+1 ?>.</td>
                        <td class="w10 hcenter"><?= get_array_value($value,"barcode","-") ?></td>
                        <td class="w10 hcenter"><?= get_array_value($value,"call_number","-") ?> </td>
                        <td class="hidden-xs w100 a-left"><?= get_array_value($value,"parent_title","") ?> <?= get_array_value($value,"copy_title","") ?></td>       
                        <td class="w10 hcenter"><?= get_array_value($value,"category_name","-") ?></td>
                    </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    function category(id) {

        var ajaxRequest;

        try {

            ajaxRequest = new XMLHttpRequest();
        } catch (e) {

            try {
                ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {

                    alert("Your browser broke!");
                    return false;
                }
            }
        }
        ajaxRequest.onreadystatechange = function () {
            if (ajaxRequest.readyState == 4)
            {
                var showarea = 'category_area';
                var area = document.getElementById(showarea);
                area.innerHTML = ajaxRequest.responseText;
            }
        }

        ajaxRequest.open("GET", "/admin/top_most_popular_item/ajax?id=" + id, true);
        ajaxRequest.send(null);

    }
    $(document).ready(function () {

        $(" #borrowing_date_start, #borrowing_date_end").datepicker({
            format: "yyyy-mm-dd",
            todayBtn: true,
            todayHighlight: true,
            autoclose: true
        }).on('changeDate', function (ev) {
        });




<?= @$message ?>
<?= @$js_code ?>
    });
</script>

