<?php
//$data_search = "";
// $init_adv_search = @$init_adv_search;
// if($init_adv_search != "clear"){
    //$dataSearchSession = new CI_Session();
    //$data_search = $dataSearchSession->userdata('TopMostPopularCategoriesBackDataSearchSession'); 
// }
//     $borrowing_date_start = "";
//     $borrowing_date_end = "";
$result_list = @$result_list;
// echo "<pre>";
// print_r($result_list);
// echo "</pre>";
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="result-msg-box"></div>
<span style=" font-size:22px;">Top Most Popular Categories</span><br /><br />
<div class="row">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">
                    <form action="" method="post" class="form-horizontal">
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
                        <div class="form-group">
                                    <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Resource Type : </label>
                                    <div class="col-md-12 col-lg-8">

                                        <select name="type" style="padding:5px;" class="right form-control">
                                            <option value="">Select  Resource Type</option>
                                            <?php  $sql="select * from product_main where (aid ='1' OR aid ='2' OR aid ='8')  order by weight asc";
                                                $exe=mysql_query($sql);

                                                while($data=mysql_fetch_array($exe)){
                                            ?>
                                            <option value="<?=$data["aid"] ?>" <? if(get_array_value($data_search,"type","")==$data["aid"]){?> selected="selected" <? }?>><?= $data["name"] ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                    </form>     
                </div>
            </div>
        </section>
    </div>
</div>
<a  class="btn btn-info" target="_blank" href="/admin/top_most_popular_categories/export?borrowing_date_start=<?=get_array_value($data_search,"borrowing_date_start","")?>&borrowing_date_end=<?=get_array_value($data_search,"borrowing_date_end","")?>&type=<?=get_array_value($data_search,"type","")?>">
     Export to Excel
</a>
<div class="panel-body" style=" margin-top:10px; background:#FFF;">
    <div class="adv-table">
        <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">
            <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                <thead>
                   <tr role="row" bgcolor="#efefef">
                        <th class="w10 hcenter">Rk.</th>
                        <th class="hidden-xs w100 a-center">Resource Type</th>
                        <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Category</th>
                        <th class="hidden-xs hidden-sm hidden-md w100 a-center " >Borrowed (Times)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(is_var_array($result_list)){
                    foreach ($result_list as $key=>$item) {
                        $class = ($key%2)? "#f2f2f2" : "#ffffff";
                    ?>
                    <tr role="row" bgcolor="<?= $class ?>">
                        <td class="w10 hcenter"><?= $no ?>.</td>
                        <td class="hidden-xs w100"><?=get_array_value($item,"product_main_name","")?></td>
                        <td class="hidden-xs hidden-sm hidden-md w100 a-left " ><?=get_array_value($item,"name","")?></td>
                        <td class="hidden-xs hidden-sm hidden-md w100 a-center " ><?=get_array_value($item,"total","")?></td>
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
<!--        </section>
    </div>
</div>-->

<script type="text/javascript">
    $(document).ready(function () {

        $(" #borrowing_date_start, #borrowing_date_end").datepicker({
            format: "yyyy-mm-dd",
            todayBtn: true,
            todayHighlight: true,
            autoclose: true
        }).on('changeDate', function (ev) {
        });




<?=@$message ?>
<?=@$js_code ?>
    });
</script>

