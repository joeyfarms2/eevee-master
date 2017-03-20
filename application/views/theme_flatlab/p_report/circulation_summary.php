
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="result-msg-box"></div>
<span style=" font-size:22px;">Circulation Summary</span><br /><br />
<div class="row">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">
                    <form action="" method="post" class="form-horizontal">
  
                        <div class="form-group">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Transaction Year : </label>
                            <div class="col-md-12 col-lg-8">
                                <?php
                                $sql ="select DISTINCT DATE_FORMAT(updated_date,'%Y') as 'year' from shelf_history  order by updated_date desc ";
                                $resultt = mysql_query($sql);
                                ?><select name="year" id="year" style=" padding:5px;"class="form-control" >
                                    <?
                                    while($row = mysql_fetch_array($resultt)){?>
                                    <option value=<?= $row[year] ?> <? if($_REQUEST[year]==$row[year]){?> selected="selected"<? }?>><?= $row[year] ?></option>
                                    <? } ?>  
                                </select>
                            </div>
                            <div> <input type="submit" id="submit" name="submit" value="Submit" class="btn btn-primary"></div>
                        </div>
                        
                    </form>     
                </div>
            </div>
        </section>
    </div>
</div> 
<a class="btn btn-info" target="_blank" href="/admin/circulation_summary/export?year=<?= $_REQUEST[year] ?>">
    Export to Excel
</a>
<div class="panel-body" style=" margin-top:10px; background:#FFF;">
    <div class="adv-table">
        <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">
            <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                <thead>
                     <tr role="row" bgcolor="#efefef">
                        <th class="w20 hcenter">Circulation</th>
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
                    <tr role="row">

                        <td class="w20 hleft">Borrowed (Times)</td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["01"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["02"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["03"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["04"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["05"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["06"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["07"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["08"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["09"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["10"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["11"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['borrowed']["12"],"total","0") ?></td>

                    </tr>
                    <tr role="row" bgcolor="#f2f2f2">
                        <td class="w20 hleft">Returned (Times)</td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["01"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["02"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["03"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["04"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["05"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["06"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["07"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["08"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["09"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["10"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["11"],"total","0") ?></td>
                        <td class="hidden-xs w100 a-right"><?= get_array_value($result_list['returned']["12"],"total","0") ?></td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
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

