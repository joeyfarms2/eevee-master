
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="result-msg-box"></div>
<span style=" font-size:22px;">Cataloging Summary</span><br /><br />
<div class="row">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">
                    <form action="" method="post"  class="form-horizontal">
                       
                        <div class="form-group">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Transaction Year : </label>
                            <div class="col-md-12 col-lg-8">
                                <?php
                                /*if($_REQUEST[year]==""){
                                $_REQUEST[year]=date("Y");
                                }*/
                                $sql ="select DISTINCT DATE_FORMAT(updated_date,'%Y') as 'year' from shelf_history  order by updated_date desc ";
                                $resultt = mysql_query($sql);
                                ?><select name="year" id="year" style=" padding:5px;" class="form-control">

                                    <?php
                                    while($row = mysql_fetch_array($resultt)){?>
                                    <option value=<?= $row[year] ?> <? if($_REQUEST[year]==$row[year]){?> selected="selected"<? }?>><?= $row[year] ?></option>
                                    <? } ?>  
                                </select>
                            </div>
                            <div> <input type="submit" id="submit" name="submit" value="Submit" class="btn btn-primary"></div>

                        </div>
<!--                        <div class="form-group">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Month : </label>
                            <div class="col-md-12 col-lg-8">
                                <div class="input-group date form_datetime-adv" data-date="">
                                    <span class="input-group-addon" >From</span>
                                    <input class="form-control" type="text" id="borrowing_date_start" name="month_start" value="<?= $_REQUEST[borrowing_date_start] ?>"  />

                                    <span class="input-group-addon">To</span>
                                    <input class="form-control" type="text" id="borrowing_date_end" name="month_stop" value="<?= $_REQUEST[borrowing_date_end] ?>"  />

                                    <div class="input-group-btn">
                                        <button class="btn btn-danger" type="button" onclick="clearValue('borrowing_date_start');clearValue('borrowing_date_end');">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        <!--                        <div class="form-group" style=" margin-top:20px;">
                                                    <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Month : </label>
                        
                                                    <div class="col-md-12 col-lg-8">
                        
                                                        <select name="month_start" id="month_start" style=" padding:5px;">
                                                            <option value=""> กรุณาเลือกเดือน </option>
                                                            <option value="01" <? if($_REQUEST[month_start]=="01"){?> selected="selected"<? } ?>>มกราคม</option>
                                                            <option value="02" <? if($_REQUEST[month_start]=="02"){?> selected="selected"<? } ?>>กุมภาพันธ์</option>
                                                            <option value="03" <? if($_REQUEST[month_start]=="03"){?> selected="selected"<? } ?>>มีนาคม</option>
                                                            <option value="04" <? if($_REQUEST[month_start]=="04"){?> selected="selected"<? } ?>>เมษายน</option>
                                                            <option value="05" <? if($_REQUEST[month_start]=="05"){?> selected="selected"<? } ?>>พฤษภาคม</option>
                                                            <option value="06" <? if($_REQUEST[month_start]=="06"){?> selected="selected"<? } ?>>มิถุนายน</option>
                                                            <option value="07" <? if($_REQUEST[month_start]=="07"){?> selected="selected"<? } ?>>กรกฎาคม</option>
                                                            <option value="08" <? if($_REQUEST[month_start]=="08"){?> selected="selected"<? } ?>>สิงหาคม</option>
                                                            <option value="09" <? if($_REQUEST[month_start]=="09"){?> selected="selected"<? } ?>>กันยายน</option>
                                                            <option value="10" <? if($_REQUEST[month_start]=="10"){?> selected="selected"<? } ?>>ตุลาคม</option>
                                                            <option value="11" <? if($_REQUEST[month_start]=="11"){?> selected="selected"<? } ?>>พฤศจิกายน</option>
                                                            <option value="12" <? if($_REQUEST[month_start]=="12"){?> selected="selected"<? } ?>>ธันวาคม</option>
                                                        </select> TO
                                                        <select name="month_stop" id="month_stop" style=" padding:5px;">
                                                            <option value=""> กรุณาเลือกเดือน </option>
                                                            <option value="01" <? if($_REQUEST[month_stop]=="01"){?> selected="selected"<? } ?>>มกราคม</option>
                                                            <option value="02" <? if($_REQUEST[month_stop]=="02"){?> selected="selected"<? } ?>>กุมภาพันธ์</option>
                                                            <option value="03" <? if($_REQUEST[month_stop]=="03"){?> selected="selected"<? } ?>>มีนาคม</option>
                                                            <option value="04" <? if($_REQUEST[month_stop]=="04"){?> selected="selected"<? } ?>>เมษายน</option>
                                                            <option value="05" <? if($_REQUEST[month_stop]=="05"){?> selected="selected"<? } ?>>พฤษภาคม</option>
                                                            <option value="06" <? if($_REQUEST[month_stop]=="06"){?> selected="selected"<? } ?>>มิถุนายน</option>
                                                            <option value="07" <? if($_REQUEST[month_stop]=="07"){?> selected="selected"<? } ?>>กรกฎาคม</option>
                                                            <option value="08" <? if($_REQUEST[month_stop]=="08"){?> selected="selected"<? } ?>>สิงหาคม</option>
                                                            <option value="09" <? if($_REQUEST[month_stop]=="09"){?> selected="selected"<? } ?>>กันยายน</option>
                                                            <option value="10" <? if($_REQUEST[month_stop]=="10"){?> selected="selected"<? } ?>>ตุลาคม</option>
                                                            <option value="11" <? if($_REQUEST[month_stop]=="11"){?> selected="selected"<? } ?>>พฤศจิกายน</option>
                                                            <option value="12" <? if($_REQUEST[month_stop]=="12"){?> selected="selected"<? } ?>>ธันวาคม</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <br><br> -->
                        <!--            <div class="form-group" style=" margin-top:20px;">
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
<a target="_blank"  class="btn btn-info" href="/admin/cataloging_summary/export?year=<?= $_REQUEST[year] ?>">
    Export to Excel
</a>  

<div class="panel-body" style=" margin-top:10px; background:#FFF;">
    <div class="adv-table">
        <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">
            <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                <thead>


                   <tr role="row" bgcolor="#efefef">
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
                </thead>
                <tbody>
                <?php
                 foreach ($master_product_main as $key => $value) {
                  $class = ($key%2)? "#f2f2f2" : "#ffffff";
                ?>
                    <tr role="row" bgcolor="<?= $class ?>">
                        <td class="w10 hleft" ><?=  get_array_value($value,"name","") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["01"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["02"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["03"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["04"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["05"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["06"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["07"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["08"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["09"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["10"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["11"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right" ><?= get_array_value($result_list[$key]["12"],"total","0") ?></td>
                      
                    </tr>

        <?php }?>

               
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
            format: "mm",
            viewMode: "months",
            minViewMode: "months"
        }).on('changeDate', function (ev) {
        });




<?= @$message ?>
<?= @$js_code ?>
    });
</script>

