<script type="text/javascript">
    $(document).ready(function () {
        function printPDF() {
            $("#frm_ptint").submit();
        }

    });
</script>
<?php
$data_search = @$data_search;
$search_in = get_array_value($data_search, "search_in", "");
$search_product_main = get_array_value($data_search, "search_product_main", "");
$search_status = get_array_value($data_search, "search_status", "");
$page_selected = get_array_value($data_search, "page_selected", "");
$search_order_by = get_array_value($data_search, "search_order_by", "");
$master_product_main = @$master_product_main;
//print_r($master_product_main);
?>

<form id="frm_ptint" name="frm_ptint" method="POST" action="<?= site_url('admin/print/print-return-card') ?>" class="cmxform form-horizontal tasi-form" target="_blank">
    <div id="result-msg-box"></div>

    <div class="row">
        <div class="col-md-12">
            <section class="panel">
                <header class="panel-heading">
                    <?= @$header_title ?>
                </header>
                <div class="panel-body">
                    <!-- <div class="form-group">
                            <label class="col-md-12 col-lg-2 control-label" for="ddl_print_type">Print</label>
                            <div class="col-md-12 col-lg-8">
                                    <label class="radio-inline">
                                            <input type="radio" id="ddl_print_type_0" name="ddl_print_type" value="1" checked /><label for="ddl_print_type_0">Barcode</label>
                                    </label>
                                    <label class="radio-inline">
                                            <input type="radio" id="ddl_print_type_1" name="ddl_print_type" value="2"  /><label for="ddl_print_type_1">สันปก</label>
                                    </label>
                                    < !- - <label class="radio-inline">
                                            <input type="radio" id="ddl_print_type_3" name="ddl_print_type" value="3"  /><label for="ddl_print_type_3">Barcode + สันปก</label>
                                    </label>  - ->
                            </div>
                    </div> -->
                    <div class="form-group">
                        <label class="col-md-12 col-lg-2 control-label" for="product_main_aid">Prefix</label>
                        <div class="col-md-12 col-lg-8">
                            <?php
                            $item_detail = "";
                            $product_main_aid = get_array_value($item_detail, "product_main_aid", "");
                            ?>
                            <select id="product_main_aid" name="product_main_aid" class="form-control chzn-select" >
                                <?php
                                if (is_var_array($master_product_main)) {
                                    foreach ($master_product_main as $m_item) {
                                        // print_r($m_item);
                                        if (get_array_value($m_item, "aid", "") == '1' || get_array_value($m_item, "aid", "") == '2' || get_array_value($m_item, "aid", "") == '8') {
                                            ?>
                                            <option value="<?= get_array_value($m_item, "aid", "") ?>" <?php if ($product_main_aid == get_array_value($m_item, "aid", "")) echo 'selected="selected"'; ?>><?= get_array_value($m_item, "name", "") ?></option>

                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label class="col-md-12 col-lg-2 control-label">เลือกรูปแบบ <br/> Print Barcode</label>
                        <div class="col-md-12 col-lg-8" style="margin-top:8px;">
                            <label class="radio-inline">
                                <input type="radio" name="choose" id="choose" value="1" checked="checked" onclick="document.getElementById('types').style.display = 'block'; document.getElementById('types2').style.display = 'block';document.getElementById('types1').style.display = 'none';"  />จาก B000001 ถึง B000999
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="choose" id="choose" value="2" onclick="document.getElementById('types1').style.display = 'block';document.getElementById('types2').style.display = 'none';document.getElementById('types').style.display = 'none';"/>B000111 และ B000222 และ B000333 
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="types2">
                        <label class="col-md-12 col-lg-2 control-label">Barcode Range</label>
                        <div class="col-md-12 col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">From</span>
                                <input class="form-control" type="text" id="range_from" name="range_from" placeholder="ระบุให้อยู่ในรูปแบบ B000001" />

                                <span class="input-group-addon">To</span>
                                <input class="form-control" type="text" id="range_to" name="range_to" placeholder="ระบุให้อยู่ในรูปแบบ B000999" />

                            </div>
                        </div>
                    </div>


                    <div class="form-group" id="types1" style="display:none;">
                        <label class="col-md-12 col-lg-2 control-label">Barcode Range</label>
                        <div class="col-md-12 col-lg-8">
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[0]" placeholder="Lable 1" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[1]" placeholder="Lable 2" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[2]" placeholder="Lable 3" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range" name="range[3]" placeholder="Lable 4" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[4]" placeholder="Lable 5" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[5]" placeholder="Lable 6" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[6]" placeholder="Lable 7" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[7]" placeholder="Lable 8" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[8]" placeholder="Lable 9" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[9]" placeholder="Lable 10" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[10]" placeholder="Lable 11" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[11]" placeholder="Lable 12" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[12]" placeholder="Lable 13" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[13]" placeholder="Lable 14" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[14]" placeholder="Lable 15" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[15]" placeholder="Lable 16" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[16]" placeholder="Lable 17" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[17]" placeholder="Lable 18" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[18]" placeholder="Lable 19" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[19]" placeholder="Lable 20" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[20]" placeholder="Lable 21" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[21]" placeholder="Lable 22" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[22]" placeholder="Lable 23" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[23]" placeholder="Lable 24" />

                            </div>
                            <div class="input-group">

                                <input class="form-control" type="text" id="range1" name="range[24]" placeholder="Lable 25" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range2" name="range[25]" placeholder="Lable 27" />
                                <span class="input-group-addon">,</span>
                                <input class="form-control" type="text" id="range3" name="range[26]" placeholder="Lable 27" />

                            </div>
                            <!-- <br/>
                            <div class="input-group">
                                    <input class="form-control" type="text" id="range4" name="range4" placeholder="ระบุให้อยู่ในรูปแบบ B000444" />
                                    <span class="input-group-addon">,</span>
                                    <input class="form-control" type="text" id="range5" name="range5" placeholder="ระบุให้อยู่ในรูปแบบ B000555" />
                                    <span class="input-group-addon">,</span>
                                    <input class="form-control" type="text" id="range6" name="range6" placeholder="ระบุให้อยู่ในรูปแบบ B000666" />
                            </div> -->
                        </div>
                    </div>
                    <div class="form-group" id="types">
                        <label class="col-md-12 col-lg-2 control-label">จุดเริ่มต้น</label>
                        <div class="col-md-12 col-lg-8">
                            <input class="form-control" type="text" id="item_start" name="item_start" value="1"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-8">
                            <a class="btn btn-primary" onclick="processSubmitOption('frm_ptint', '0');" />Print to PDF</a>
                        
                            <input type="button" class="btn btn-primary"  style="    background-color: #ccc;border: 1px solid #ccc;color: #000;" value="Reset Form" onClick="this.form.reset()"/>
                        </div>
                    </div>	

                </div>
            </section>
        </div>
    </div>
</form>