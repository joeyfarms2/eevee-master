<?php
//$data_search = "";
// $init_adv_search = @$init_adv_search;
// if($init_adv_search != "clear"){
    // $dataSearchSession = new CI_Session();
    // $data_search = $dataSearchSession->userdata('TopMostPopularItemBackDataSearchSession'); 
// }
//     $borrowing_date_start = "";
//     $borrowing_date_end = "";
// $result_list = @$result_list;
// $cat_ = get_array_value($data_search,"cat_","");
// $type = get_array_value($data_search,"type","");
// print_r($_REQUEST['total']);
// echo $_REQUEST['total'];
$category="";
for($i=0;$i<=$_REQUEST['total'];$i++){
    if($cat_[$i]!=""){
        $category = $cat_[$i];
        // echo $cat_[$i]."<br/>";
        // echo $category."<br/>";
    }
}

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="result-msg-box"></div>
<span style=" font-size:22px;">Top Most Popular Items</span><br /><br />

<div class="row">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">
                    <form action="" method="get" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Transaction Date : </label>
                            <div class="col-md-12 col-lg-8">
                                <div class="input-group date form_datetime-adv" data-date="">
                                    <span class="input-group-addon" >From</span>
                                    <input class="form-control" type="text" id="borrowing_date_start" name="borrowing_date_start" value="<?=get_array_value($data_search,"borrowing_date_start","")?>"  />

                                    <span class="input-group-addon">To</span>
                                    <input class="form-control" type="text" id="borrowing_date_end" name="borrowing_date_end" value="<?=get_array_value($data_search,"borrowing_date_end","")?>"  />

                                    <div class="input-group-btn">
                                        <button class="btn btn-danger" type="button" onclick="clearValue('borrowing_date_start');clearValue('borrowing_date_end');">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div> <input type="submit" id="submit" name="submit" value="Submit" class="btn btn-primary"></div>
                        </div>

                        <div class="form-group" style=" margin-top:20px; float:left; width:100%;">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Resource Type : </label>
                            <div class="col-md-12 col-lg-8">

                                <select name="type" style="padding:5px;" onchange="category(this.value)" class="right form-control">
                                    <!--<option value="">Select  Resource Type</option>-->
                                    <?php $sql="select * from product_main where (aid ='1' OR aid ='2' OR aid ='8') order by weight asc";
                                    $exe=mysql_query($sql);
                                    while($data=mysql_fetch_array($exe)){
                                    ?>
                                    <option value="<?= $data['aid'] ?>" <? if(get_array_value($data_search,"type","")==$data["aid"]){?> selected="selected" <? }?>><?= $data["name"] ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>

                        
                        <div class="form-group" style=" margin-top:20px; float:left; width:100%;">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Category : </label>
                            <div class="col-md-12 col-lg-8" >
                                <div id="category_area">
                                    <?php
                                    if($type==""){
                                       $type = 1;
                                    }
                                    if($type!=""){

                                    $sql="select * from product_category where product_main_aid = '".$type."' ";
                                    $exe=mysql_query($sql);
                                    $row=0;
                                    while($data=mysql_fetch_array($exe)){

                                    $chk="";
                                    for($i=0;$i<=$_REQUEST["total"];$i++){
                                    $category = $cat_[$i];
                                    if($cat_[$i]!=""){
                                    if($category==$data["aid"]){
                                    $chk="act";
                                    }
                                    }
                                    }

                                    ?>
                                    <div style=" width:33%; float:left;">
                                        <input  type="checkbox" value="<?= $data['aid'] ?>" name="cat_[]" <? if($chk=="act"){?> checked="checked" <? }?> /> <?= $data["name"] ?>
                                    </div>
                                    <?  
                                    $row++;
                                    }
                                    }
                                    ?>
                                    <input type="hidden" value="<?= $row ?>" id="total" name="total" />
                                </div>


                            </div>
                        </div>


                        <br><br> 
                         <div class="form-group">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Top : </label>
                            <div class="col-md-12 col-lg-8">

                                <select name="top" style="padding:5px;" class="right form-control">
                                    <option  value="5" <? if(get_array_value($data_search,"top","")==5){?> selected <? }?>>5</option>
                                    <option  value="10" <? if(get_array_value($data_search,"top","")==10){?> selected <? }?>>10</option>
                                    <option  value="20" <? if(get_array_value($data_search,"top","")==20){?> selected <? }?>>20</option>
                                    <option   value="50" <? if(get_array_value($data_search,"top","")==50){?> selected <? }?>>50</option>
                                    <option  value="100" <? if(get_array_value($data_search,"top","")==100){?> selected <? }?>>100</option>
                                </select>
                            </div>
                        </div>
<!--                        <div class="form-group" style=" margin-top:20px; float:left; width:100%;">
                            <div class="col-lg-offset-2 col-lg-8">
                                <input type="submit" id="submit" name="submit" value="Search" class="btn btn-primary">
                                <input type="button" value="Clear" class="btn btn-default" onClick="window.location = '';">

                            </div>
                        </div>-->

                    </form>     
                </div>
            </div>
        </section>
    </div>
</div>
<a  class="btn btn-info" target="_blank" href="/admin/top_most_popular_item/export?borrowing_date_start=<?=get_array_value($data_search,"borrowing_date_start","")?>&borrowing_date_end=<?=get_array_value($data_search,"borrowing_date_end","")?>&type=<?=$type?>&top=<?=get_array_value($data_search,"top","")?>">
    Export to Excel
</a>  

<div class="panel-body" style=" margin-top:10px; background:#FFF;">
    <div class="adv-table">
        <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">
            <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                <thead>


                   <tr role="row" bgcolor="#efefef">
                        <th class="w10 hcenter">No.</th>
                        <th class="w10 hcenter">Barcode</th>
                        <th class="w10 hcenter">Call No.</th>
                        <th class="hidden-xs w100 a-center">Title of Copy</th>
                        <th class="hidden-xs w100 a-center">Resource Type</th>
                        <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Category</th>
                        <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Borrowed (Times)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                   if (is_var_array($result_list)) {
                    foreach ($result_list as $key=>$item) {
                        $class = ($key%2)? "#f2f2f2" : "#ffffff";
                    ?>
                    <tr role="row" bgcolor="<?= $class ?>">
                        <td class="w10 hcenter"><?= $no ?>.</td>
                        <td class="w10 hcenter">
                            <?= $item->barcode ?>
                        </td>
                        <td class="w10 hcenter"><?= $item->call_number  ?></td>
                        <td class="hidden-xs w100 a-left"><?= $item->title  ?></td>
                        <td class="hidden-xs w100 "><?= $item->product_main_name ?></td>
                        <td class="hidden-xs hidden-sm hidden-md w100 a-left " ><?= $item->category_name ?>
                        </td>
                        <td class="hidden-xs hidden-sm hidden-md w100 a-center " ><?= $item->total?></td>
                    </tr>
                     <?php
                    $no++;
                    }
                }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!--            </section>
                </div>
        </div>-->

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

