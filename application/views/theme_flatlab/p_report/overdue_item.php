
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="result-msg-box"></div>
<span style=" font-size:22px;">Overdue Item Rating</span><br /><br />
<div class="row">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">

                    <form action="" method="post" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Transaction Year : </label>
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
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
<a target="_blank" class="btn btn-info" href="/admin/overdue_item/export?borrowing_date_start=<?= $_REQUEST[borrowing_date_start] ?>&borrowing_date_end=<?= $_REQUEST[borrowing_date_end]?>">
    Export to Excel
</a> 

<div class="panel-body" style=" margin-top:10px; background:#FFF;">
    <div class="adv-table">
        <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">
            <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                <thead>
                    <tr role="row" bgcolor="#efefef">
                        <th class="hidden-xs w100 a-center">Borrowed (Items)</th>
                        <th class="hidden-xs w100 a-center">Overdue (Items)</th>
                        <th class="hidden-xs w100 a-center">Overdue Rating(%)</th>
            </thead>
            <tbody>
                    <tr role="row">
                        <td class="hidden-xs w100 a-center"><?= get_array_value($result_list,"borrowed","0") ?></td>
                        <td class="hidden-xs w100 a-center"><?= get_array_value($result_list,"overdue","0") ?></td>
                        <td class="hidden-xs hidden-sm hidden-md w100 a-center " ><?= number_format(get_array_value($result_list,"percentage","0"),2) ?></td>
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

