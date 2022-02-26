<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_payment_registry_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $month = !empty($getdata['month']) ? $getdata['month'] : '';
    if (!empty($month)) {
        $month_state = explode('.', $getdata['month']);
        $from_date = $month_state[0];
        $to_date = $month_state[1];
    } else {
        $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : date('Y-m-01');
        $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : date('Y-m-t');
    }
    $sql = "SELECT * FROM {$wpdb->prefix}ctm_payment_vouchers WHERE payment_date>='$from_date' AND payment_date<='$to_date' ORDER BY id DESC ";
    $results = $wpdb->get_results($sql);

    ?>
    <style>
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <form method="get">
                            <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                            <table id="tbl-filter" cellpadding="5" cellspacing="5" style="width:700px">
                                <tr>
                                    <td><label>Month:</label><br/>
                                        <select name="month" onchange="this.form.submit()">
                                            <option value="">Select Month</option>
                                            <?php
                                            for ($i = 0; $i <= 12; $i++) {
                                                $startdaymonthyear = date("Y-m-d", strtotime(date('Y-m-01') . " -$i months"));
                                                $enddaymonthyear = date("Y-m-t", strtotime(date('Y-m-01') . " -$i months"));
                                                $monthname = date("F - Y", strtotime(date('Y-m-01') . " -$i months"));
                                                //$months[]=[$startdaymonthyear,$enddaymonthyear,$month];
                                                $select = $month == "{$startdaymonthyear}.{$enddaymonthyear}" ? 'selected' : '';
                                                echo"<option value='{$startdaymonthyear}.{$enddaymonthyear}' $select>$monthname</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label>From Payment Date:</label><br/>
                                        <input type="date" name="from_date"  value="<?= $from_date ?>" style="width:175px"  required />
                                    </td>
                                    <td>
                                        <label>To Payment Date:</label><br/>
                                        <input type="date" name="to_date"  value="<?= $to_date ?>" style="width:175px"  required />
                                    </td>

                                    <td><br/><br/>
                                        <button type="submit" name="filter" value="1" class="btn btn-sm btn-primary ">Filter</button>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="admin.php?page=<?= $getdata['page'] ?>" class="btn btn-sm btn-secondary text-white">RESET</a>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <br/>
                        <form>
                            <?php
                            $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{text-align:center;font-size:10px;}
                                        table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                    </style>
                                    <div id='page-inner-content' class='postbox'><br/>
                                        <div class='inside' style='max-width:100%;margin:auto'>";

                            $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:26px;font-weight:bold'>PAYMENT REGISTRY</span></h4></td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                            $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=2>Payment<br/> Voucher Date</th>
                                        <th rowspan=2>Payment<br/> Voucher Number</th>
                                        <th rowspan=2>Purchase<br/> Voucher No</th>
                                        <th rowspan=2>Invoice No</th>
                                        <th rowspan=2>Supplier</th>
                                        <th rowspan=2>Description</th>
                                        <th>EURO</th>
                                        <th>USD</th>
                                        <th colspan=3>AED</th>
                                        <th rowspan=2>Mode of Payment</th>
                                        <th rowspan=2>Payment Source</th>
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th>Amount</th>
                                        <th>Amount</th>
                                        <th>Amount</th>
                                        <th>VAT</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>";

                            $table .= "<tbody>";
                            $total_euro_amount = $total_usd_amount = $total_aed_amount = $total_vat_amount = $grand_total_amount = 0;

                            // Receipts 
                            foreach ($results as $value) {

                                $ids = explode(',', $value->purchase_voucher);
                                $count = count($ids);
                                $once = true;
                                $pus_vou_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_purchase_vouchers WHERE id IN ('" . implode("', '", $ids) . "')");

                                $euro_amount = $value->currency == 'EURO' ? $value->amount : 0;
                                $usd_amount = $value->currency == 'USD' ? $value->amount : 0;

                                if ($value->has_vat == 1) {
                                    $aed_amount = $value->currency == 'AED' ? get_ex_vat_amount($value->amount) : 0;
                                    $vat_amount = get_vat_amount($aed_amount);
                                } else {
                                    $aed_amount = $value->currency == 'AED' ? $value->amount : 0;
                                    $vat_amount = 0;
                                }

                                $total_amount = $aed_amount + $vat_amount;

                                $total_aed_amount += $aed_amount;
                                $total_vat_amount += $vat_amount;

                                $total_euro_amount += $euro_amount;
                                $total_usd_amount += $usd_amount;

                                $grand_total_amount += $total_amount;


                                if (!empty($pus_vou_results)) {
                                    foreach ($pus_vou_results as $v) {
                                        $table .= "<tr>";
                                        $table .= $once ? "<td rowspan=$count>" . rb_date($value->payment_date) . "</td>" : '';
                                        $table .= $once ? "<td rowspan=$count>$value->id</td>" : '';
                                        $table .= "<td>{$v->id}</td>";
                                        $table .= "<td>$v->invoice_no</td>";
                                        $table .= $once ? "<td rowspan=$count>$value->paid_to</td>" : '';
                                        $table .= "<td style='text-align:left'>$v->narration</td>";
                                        $table .= "<td>" . rb_float($euro_amount, 2) . "</td>";
                                        $table .= "<td>" . rb_float($usd_amount, 2) . "</td>";
                                        $table .= $once ? "<td rowspan=$count>" . rb_float($aed_amount, 2) . "</td>" : '';
                                        $table .= $once ? "<td rowspan=$count>" . rb_float($vat_amount, 2) . "</td>" : '';
                                        $table .= $once ? "<td rowspan=$count>" . rb_float($total_amount, 2) . "</td>" : '';
                                        $table .= $once ? "<td rowspan=$count>$value->payment_method</td>" : '';
                                        $table .= $once ? "<td rowspan=$count>$value->payment_source</td>" : '';
                                        $table .= "</tr>";
                                        $once = false;
                                    }
                                } else {
                                    $table .= "<tr>
                                            <td>" . rb_date($value->payment_date) . "</td>
                                            <td>$value->id</td>
                                            <td>$value->purchase_voucher</td>
                                            <td>$value->invoice_no</td>
                                            <td>$value->paid_to</td>
                                            <td style='text-align:left'>$value->being</td>
                                            <td>" . rb_float($euro_amount, 2) . "</td>
                                            <td>" . rb_float($usd_amount, 2) . "</td>
                                            <td>" . rb_float($aed_amount + $vat_amount, 2) . "</td>
                                            <td>" . rb_float($vat_amount, 2) . "</td>
                                            <td>" . rb_float($total_amount, 2) . "</td>
                                            <td>$value->payment_method</td>
                                            <td>$value->payment_source</td>
                                            </tr>";
                                }
                            }

                            $table .= "<tr>
                                            <td colspan=6><b>Total</b></td>
                                            <td><b>" . rb_float($total_euro_amount, 2) . "</b></td>
                                            <td><b>" . rb_float($total_usd_amount, 2) . "</b></td>
                                            <td><b>" . rb_float($total_aed_amount, 2) . "</b></td>
                                            <td><b>" . rb_float($total_vat_amount, 2) . "</b></td>
                                            <td><b>" . rb_float($grand_total_amount, 2) . "</b></td>
                                            <td></td>
                                            <td></td>
                                            </tr>";

                            $table .= "</tbody>";
                            $table .= "</table>";

                            $html .= $table;
                            echo $html .= "</div></div>";
                            $pdf_file = make_pdf_file_name("BANK_DEPOSIT_REGISTORY_{$from_date}_{$to_date}.pdf")['path'];
                            ?>

                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-center">
                                    <a href="?page=payment-voucher"  class="btn btn-dark btn-sm" >Back</a>&nbsp;&nbsp;
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                    }
                                    ?>
                                    <a href = '<?= export_excel_report($pdf_file, 'payment_source_deposit_registory', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                        </form>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery('.note').on('blur', function () {
                var note_id = jQuery(this).data('note_id');
                var note = jQuery(this).val();

                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/ds-note.php",
                    type: "post",
                    dataType: "json",
                    data: {note_id: note_id, note: note},
                    success: function (response) {
                        if (response.status) {

                        }
                    }
                });
            });
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
}
