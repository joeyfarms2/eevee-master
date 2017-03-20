<?php
function DateDiff($strDate1, $strDate2) {
    return (strtotime($strDate2) - strtotime($strDate1)) / ( 60 * 60 * 24 );  // 1 day = 60*60*24
}
if($_REQUEST[type]==""){
$_REQUEST[type]=1;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="result-msg-box"></div>
<span style=" font-size:22px;">Member Usage Activities</span><br /><br />

<div class="row">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">       

                    <form action="" method="post">
                        <div class="form-group" style=" margin-top:20px;">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Member : </label>

                            <div class="col-md-12 col-lg-8">

                                <input class="form-control" type="text" id="word" name="word" value="<?= $_REQUEST[word] ?>"  />
                                <input name="aid" type="hidden" id="aid" value="<?= $_REQUEST[aid] ?>" />

                            </div>
                        </div>
                        <br><br> 
                        <div class="form-group" style=" margin-top:20px;">
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
                        <div class="form-group" style=" margin-top:20px;">
                            <label class="col-md-12 col-lg-2 control-label" style=" text-align:right; font-weight:normal;">Activity Type : </label>

                            <div class="col-md-12 col-lg-8">

                                <input id="type" type="radio"  value="1" name="type" <? if($_REQUEST[type]==1){?> checked <? }?>> Summary &nbsp;
                                       <input id="type" type="radio"  value="2" name="type" <? if($_REQUEST[type]==2){?> checked <? }?>> Circulation &nbsp;
                                       <input id="type" type="radio"  value="3" name="type" <? if($_REQUEST[type]==3){?> checked <? }?>> Download 

                            </div>
                        </div>
                        <br><br> 

                    </form>     
                </div>
            </div>
        </section>
    </div>
</div>
<a  target="_blank" href="/admin/member_usage/export?borrowing_date_start=<?= $_REQUEST[borrowing_date_start] ?>&borrowing_date_end=<?= $_REQUEST[borrowing_date_end] ?>&aid=<?= $_REQUEST[aid] ?>&type=<?= $_REQUEST[type] ?>">
    <input id="submit" class="btn btn-primary" type="button" value="Export" name="submit">
</a>

<?php
if($_REQUEST[aid]!=""){
    // echo "<pre>";
    // var_dump($result_list);
$user_detail = $result_list['user_detail'];
?>
<div class="row" style="margin-top:20px;">
    <div class="col-xs-12">
        <section class="panel">
            <div id="adv-area" class="panel-body" >
                <div class="form-group">
                    <div class="col-md-12 mt20">
                        <section class="panel">
                            <header class="panel-heading">
                                <?= @$header_title ?>
                            </header>
                            <div class="panel panel-primary" >
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="col-lg-3 col-sm-3">
                                            <ul class="unstyled">
                                                <li>User Code : <strong><?= get_array_value($user_detail,"cid","0") ?></strong></li>
                                                <li>Email : <strong><?= get_array_value($user_detail,"email","0") ?></strong></li>
                                                <li>Tel : <strong><?= get_array_value($user_detail,"contact_number","-") ?></strong></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-3 col-sm-3">
                                            <ul class="unstyled">
                                                <li>Gender : <strong><?= (get_array_value($user_detail,"gender","0")=="f")? "Female":"Male" ?></strong></li>
                                                <li>Department : <strong><?= get_array_value($user_detail,"department_name","-") ?></strong></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-3 col-sm-3">
                                            <ul class="unstyled">
                                                <li>Status : <strong><?= (get_array_value($user_detail,"status","0")=="1")? "Active":"Inactive" ?></strong></li>
                                                <li>User role : <strong><?= get_array_value($user_detail,"user_role_name","0") ?></strong></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-3 col-sm-3">
                                            <ul class="unstyled">
                                                <li>Point : <strong><?= get_array_value($user_detail,"point_remain","0") ?></strong></li>
                                                <li>Last Login : <strong><?= date("d/m/Y H:i:s", strtotime(get_array_value($user_detail,"last_login","0"))); ?></strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
}
// echo "<pre>";
// var_dump($result_list[];);
?>

<?php if($_REQUEST[type]==1){
$circulation = $result_list["circulation"];
    ?>
        <div class="panel panel-primary" style="margin-top:20px;">
            <div class="panel-body">
                <div class="form-group">
                    <div class="col-lg-3 col-sm-3">
                        <ul class="unstyled">
                            <table border="0" cellpadding="1" cellspacing="1" style=" border:1px solid #FFF; " width="90%">
                                <tr bgcolor="#0099FF" >
                                    <td  style=" padding:10px; color:#FFF;">
                                        Circulation
                                    </td>
                                    <td align="right" style=" padding:10px; color:#FFF;">
                                        Times
                                    </td>
                                </tr>
                                <tr bgcolor="#D7E6FD" >
                                    <td style=" padding:10px;">Borrowing</td>
                                    <td style=" padding:10px;" align="right"><?= get_array_value($circulation,"borrowing","0") ?></td>
                                </tr>
                                <tr bgcolor="#F4F4F4">
                                    <td style=" padding:10px;">Overdue</td>
                                    <td style=" padding:10px;" align="right"><?= get_array_value($circulation,"overdue","0") ?></td>
                                </tr>
                                <tr bgcolor="#D7E6FD">
                                    <td style=" padding:10px;">Borrowed</td>
                                    <td style=" padding:10px;" align="right"><?= get_array_value($circulation,"borrowed","0") ?></td>
                                </tr>

                            </table>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-sm-3">
                        <ul class="unstyled">

                            <table border="0" cellpadding="1" cellspacing="1" style=" border:1px solid #FFF; " width="90%">
                                <tr bgcolor="#0099FF" >
                                    <td  style=" padding:10px; color:#FFF;">
                                        Resource Type
                                    </td>
                                    <td align="right" style=" padding:10px; color:#FFF;">
                                        Times
                                    </td>
                                </tr>
                                <?php
                                $product_main_time = $result_list['product_main_time'];
                                $transaction_time= $result_list['transaction_time'];
                                foreach ($product_main_time as $key => $value) {
                                    if($no%2==1){
                                    $color="#D7E6FD";
                                    }else{
                                    $color="#f4f4f4";
                                    }

                                ?>

                                <tr bgcolor="<?= $color ?>" >
                                    <td style=" padding:10px;"><?= get_array_value($value,"name","0") ?></td>
                                    <td style=" padding:10px;" align="right"><?= get_array_value($transaction_time[get_array_value($value,"aid","0")],"total","0") ?></td>
                                </tr>
                                <?php }?>

                            </table>

                        </ul>
                    </div>
                    <div class="col-lg-3 col-sm-3">
                        <ul class="unstyled">
                            <table border="0" cellpadding="1" cellspacing="1" style=" border:1px solid #FFF; " width="90%">
                                <tr bgcolor="#0099FF" >
                                    <td  style=" padding:10px; color:#FFF;">
                                        Resource Type
                                    </td>
                                    <td align="right" style=" padding:10px; color:#FFF;">
                                        Items
                                    </td>
                                </tr>
                            <?php
                                $product_main_item = $result_list['product_main_item'];
                                $transaction_item= $result_list['transaction_item'];
                                foreach ($product_main_time as $key => $value) {
                                    if($no%2==1){
                                    $color="#D7E6FD";
                                    }else{
                                    $color="#f4f4f4";
                                    }

                                ?>

                                <tr bgcolor="<?= $color ?>" >
                                    <td style=" padding:10px;"><?= get_array_value($value,"name","0") ?></td>
                                    <td style=" padding:10px;" align="right"><?= get_array_value($transaction_item[get_array_value($value,"aid","0")],"total","0") ?></td>
                                </tr>
                                <?php
                                 }
                                 ?>

                            </table>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
<?php 
} else if($_REQUEST[type]==2){?>
            <div class="panel panel-primary" style="margin-top:20px;">
            <div class="panel-body">
                <div class="form-group">
            <?php
           
                $borrowing = $result_list['borrowing'];
                $overdue= $result_list["overdue"];
                $borrowed= $result_list["borrowed"];
            ?>
            <div class="panel-body" style=" margin-top:10px; background:#FFF;">
                <div class="adv-table">
                    <span><h4>Circulation :</h4></span>
                    <br>
                    <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">
                        <span><h4>Borrowing</h4></span>
                        <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                            <thead>
                               <tr role="row" bgcolor="#efefef">
                                    <th class="w20 hcenter">No</th>
                                    <th class="hidden-xs w100 a-center">Barcode</th>
                                    <th class="hidden-xs w100 a-center">Call No.</th>
                                    <th class="hidden-xs w100 a-center">Title of Copy</th>
                                    <th class="hidden-xs w100 a-center">Category</th>
                                    <th class="hidden-xs w100 a-center">Resource Type</th>
                                    <th class="hidden-xs w100 a-center">Borrowed Date</th>
                                    <th class="hidden-xs w100 a-center">Due Date</th>
                                    <th class="hidden-xs w100 a-center">Days Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                 $no= 0;
                                foreach ($borrowing as $key => $value) {
                                if($no%2==1){
                                $color="#f2f2f2";
                                }else{
                                $color="#fff";
                                }
                                $no++;
                                ?>
                                <tr role="row" bgcolor="<?= $color ?>">
                                    <td class="w20 hcenter"><?= $no ?>.</td>
                                    <td class="hidden-xs w100 a-center"><?= get_array_value($value,"barcode","0") ?></td>
                                    <td class="w20 hcenter"><?= get_array_value($value,"call_number","0") ?></td>
                                    <td class="hidden-xs w100 "><?= get_array_value($value,"book_title","") ?> <?= get_array_value($value,"copy_title","") ?></td>
                                    <td class="w20 hcenter"><?= get_array_value($value,"category_name","-") ?></td>
                                    <td class="hidden-xs w100 a-center"><?= get_array_value($value,"product_main_name","0") ?></td>
                                    <td class="hidden-xs w100 a-center"><?= date("d/m/Y", strtotime(get_array_value($value,"borrowing_date","0"))); ?></td>
                                    <td class="hidden-xs w100 a-center"><?= date("d/m/Y", strtotime(get_array_value($value,"due_date","0"))); ?></td>
                                    <td class="hidden-xs w100 a-center"><?= DateDiff(get_array_value($value,"due_date","0"),date("Y-m-d")) ?></td>
                                </tr>
                                <?php
                                }
                                ?>
                            
                            </tbody>
                        </table>
                        <span><h4>Overdue</h4></span>
                        <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                            <thead>

                                 <tr role="row" bgcolor="#efefef">
                                    <th class="w20 hcenter">No</th>
                                    <th class="hidden-xs w100 a-center">Barcode</th>
                                    <th class="hidden-xs w100 a-center">Call No.</th>
                                    <th class="hidden-xs w100 a-center">Title of Copy</th>
                                    <th class="hidden-xs w100 a-center">Category</th>
                                    <th class="hidden-xs w100 a-center">Resource Type</th>
                                    <th class="hidden-xs w100 a-center">Borrowed Date</th>
                                    <th class="hidden-xs w100 a-center">Due Date</th>
                                    <th class="hidden-xs w100 a-center">Delayed Days</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php
                               $no= 0;
                                foreach ($overdue as $key => $value) {
                                if($no%2==1){
                                $color="#f2f2f2";
                                }else{
                                $color="#fff";
                                }
                                $no++;
                                ?>
                                <tr role="row" bgcolor="<?= $color ?>">
                                    <td class="w20 hcenter"><?= $no ?>.</td>
                                    <td class="hidden-xs w100 a-center"><?= get_array_value($value,"barcode","0") ?></td>
                                    <td class="w20 hcenter"><?= get_array_value($value,"call_number","0") ?></td>
                                    <td class="hidden-xs w100 "><?= get_array_value($value,"book_title","") ?> <?= get_array_value($value,"copy_title","") ?></td>
                                    <td class="w20 hcenter"><?= get_array_value($value,"category_name","-") ?></td>
                                    <td class="hidden-xs w100 a-center"><?= get_array_value($value,"product_main_name","0") ?></td>
                                    <td class="hidden-xs w100 a-center"><?= date("d/m/Y", strtotime(get_array_value($value,"borrowing_date","0"))); ?></td>
                                    <td class="hidden-xs w100 a-center"><?= date("d/m/Y", strtotime(get_array_value($value,"due_date","0"))); ?></td>
                                    <td class="hidden-xs w100 a-center"><?= DateDiff(get_array_value($value,"due_date","0"),date("Y-m-d")) ?></td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <span><h4>Borrowed</h4></span>
                        <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                            <thead>

                                <tr role="row" bgcolor="#efefef">
                                    <th class="w20 hcenter">No</th>
                                    <th class="hidden-xs w100 a-center">Barcode</th>
                                    <th class="hidden-xs w100 a-center">Call No.</th>
                                    <th class="hidden-xs w100 a-center">Title of Copy</th>
                                    <th class="hidden-xs w100 a-center">Category</th>
                                    <th class="hidden-xs w100 a-center">Resource Type</th>
                                    <th class="hidden-xs w100 a-center">Borrowed Date</th>
                                    <th class="hidden-xs w100 a-center">Due Date</th>
                                    <th class="hidden-xs w100 a-center">Returned Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no= 0;
                                foreach ($borrowed as $key => $value) {
                                if($no%2==1){
                                $color="#f2f2f2";
                                }else{
                                $color="#fff";
                                }
                                $no++;
                                ?>
                                <tr role="row" bgcolor="<?= $color ?>">
                                    <td class="w20 hcenter"><?= $no ?>.</td>
                                    <td class="hidden-xs w100 a-center"><?= get_array_value($value,"barcode","0") ?></td>
                                    <td class="w20 hcenter"><?= get_array_value($value,"call_number","0") ?></td>
                                    <td class="hidden-xs w100 "><?= get_array_value($value,"book_title","") ?> <?= get_array_value($value,"copy_title","") ?></td>
                                    <td class="w20 hcenter"><?= get_array_value($value,"category_name","-") ?></td>
                                    <td class="hidden-xs w100 a-center"><?= get_array_value($value,"product_main_name","0") ?></td>
                                    <td class="hidden-xs w100 a-center"><?= date("d/m/Y", strtotime(get_array_value($value,"borrowing_date","0"))); ?></td>
                                    <td class="hidden-xs w100 a-center"><?= date("d/m/Y", strtotime(get_array_value($value,"due_date","0"))); ?></td>
                                    <td class="hidden-xs w100 a-center"><?= get_array_value($value,"returning_date","0") ?></td>
                                </tr>
                                <?php
                                }
                                ?>
                           
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>
            <?php 
            } else if($_REQUEST[type]==3){
            $downloaded = $result_list['downloaded'];
            ?>
            <div class="panel panel-primary" style="margin-top:20px;">
            <div class="panel-body">
            <div class="form-group">
            <div class="panel-body" style=" margin-top:10px; background:#FFF;">
                <div class="adv-table">
                    <div role="grid" class="dataTables_wrapper form-inline" id="tbldata_wrapper">
                        <span><h4>Download:</h4></span>

                        <table id="tbldata" class="display table table-bordered table-striped dataTable" cellspacing="0" cellpadding="0" border="0">
                            <thead>
                                 <tr role="row" bgcolor="#efefef">
                                    <th class="w20 hcenter">No</th>
                                    <th class="hidden-xs w100 a-center">User</th>
                                    <th class="hidden-xs w100 a-center">Resource Type</th>
                                    <th class="hidden-xs w100 a-center">Title</th>
                                    <th class="hidden-xs w100 a-center">Downloaded Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no= 0;
                                foreach ($downloaded as $key => $value) {
                                    if(get_array_value($value,"barcode","") != ""){
                                

                                    if($no%2==1){
                                    $color="#f2f2f2";
                                    }else{
                                    $color="#fff";
                                    }
                                    $no++;
                                    ?>
                                    <tr role="row" bgcolor="<?= $color ?>">
                                        <td class="w20 hcenter"><?= $no ?>.</td>
                                        <td class="hidden-xs w100 a-center"><?= get_array_value($value,"email","0") ?></td>
                                        <td class="hidden-xs w100 a-center"><?= get_array_value($value,"product_main_name","0") ?></td>
                                        <td class="hidden-xs w100 a-left"><?= get_array_value($value,"book_title","") ?> <?= get_array_value($value,"copy_title","") ?></td>
                                        <td class="hidden-xs w100 a-center"><?= date("d/m/Y H:i:s", strtotime(get_array_value($value,"updated_date","0"))); ?></td>
                                    </tr>
                                <?php
                                    }
                                }
                                
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
          </div>
    </div>
</div>
            <?php }?>



   

<style type="text/css">
    .autocomplete_list * {
        font-size: 13px;
    }

    .autocomplete_list b {
        font-weight: bold;
    }

    .autocomplete_list { 
        background: #f2f2f2; display: block;
        border: 1px solid gray;
        text-align: left; z-index: 200; width:300px; 

    }

    .autocomplete_list, .autocomplete_list ol, .autocomplete_list li { 
        list-style-type: none; 
        margin: 0; 
        padding: 0; 
        background:#f8f8f8;
    }

    .autocomplete_list li { 
        margin: 0; 
        text-align: left;
        cursor: pointer; 
        padding: 4px;
        border-bottom: 1px solid silver;	
    }

    .autocomplete_list .last_item { 
        border: none;	
    }

    .autocomplete_list .current_item { 
        background: #FEBF47; color: #000; 
    }

    .autocomplete_list span { 
        color: #111; float: right; padding-left: 2em; 
    }

    .autocomplete_icon { 
        background-image:url(/styles/pttep/images/background/autocomplete.gif);
        cursor: pointer;
        cursor: hand;	
    }
</style>
<script type="text/javascript" src="<?=JS_PATH?>autocomplete.js"></script>  
<script type="text/javascript">
                                            function make_autocom(autoObj, showObj) {

                                                var mkAutoObj = autoObj;
                                                var mkSerValObj = showObj;
                                                new Autocomplete(mkAutoObj, function () {
                                                    this.setValue = function (id) {
                                                        document.getElementById(mkSerValObj).value = id;
                                                    }
                                                    if (this.isModified) {
                                                        this.setValue("");

                                                        if (this.value.length < 1 && this.isNotClick)
                                                            return;

                                                        return "/admin/getdatauser?q=" + decodeURIComponent(this.value);
                                                    }
                                                });
                                            }

                                            make_autocom("word", "aid");
</script>
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



