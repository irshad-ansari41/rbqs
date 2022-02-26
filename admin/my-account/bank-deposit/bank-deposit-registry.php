<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_bank_deposit_registry_page() {
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

    $sql = "SELECT t1.id,t1.change_amount, t1.charges, t1.hold_cash, t1.net_deposit, t1.payment_status, t1.bank_status, t1.verify_date,t1.updated_at, "
            . "t2.id as receipt_id, t2.quotation_id,t2.revised_no,t2.paid_amount,t2.total_amount,t2.received_from,t2.payment_date,t2.payment_method "
            . "FROM {$wpdb->prefix}ctm_bank_deposits t1 LEFT JOIN {$wpdb->prefix}ctm_receipts t2 ON t1.receipt_id=t2.id "
            . "WHERE t2.payment_method != 'Store credit' AND t1.verify_date>='$from_date' AND t1.verify_date<='$to_date' ORDER BY t1.verify_date ASC ";
    $results = $wpdb->get_results($sql);


    //echo '<pre>';
//    foreach ($results as $value) {
//        $receipt = get_receipt($value->receipt_id, ['payment_date', 'received_from']);
//        make_credit_transaction($value->id, $receipt->payment_date, $value->verify_date, $receipt->received_from, $value->receipt_id, $value->net_deposit);
//        sleep(1);
//    }
    //echo '</pre>';
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
                        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
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
                                        <label>From Verify Date:</label><br/>
                                        <input type="date" name="from_date"  value="<?= $from_date ?>" style="width:175px"  required />
                                    </td>
                                    <td>
                                        <label>To Verify Date:</label><br/>
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
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:26px;font-weight:bold'>Customer's Net Verified Payment</span></h4></td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                            $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=2>Receipt No</th>
                                        <th rowspan=2>QTN</th>
                                        <th rowspan=2>QTN<br/>Amount</th>
                                        <th rowspan=2>Type</th>
                                        <th rowspan=2>Receipt<br/>Amount</th>
                                        <th rowspan=2>% Paid</th>
                                        <th rowspan=2>Excluding<br/>VAT Amount</th>
                                        <th rowspan=2>Net of VAT</th>
                                        <th rowspan=2>Received From</th>
                                        <th rowspan=2>Payment Date</th>
                                        <th rowspan=2>Full / <br/> Advance / <br/> Balance</th>
                                        <th rowspan=2>Mode</th>
                                        <th rowspan=2>Customer<br/>Excess Paid<br/>Amount</th>
                                        <th rowspan=2>Card <br/> Charges</th>
                                        <th rowspan=2>Cash on <br/> Hold</th>
                                        <th rowspan=2>Net Deposit</th>
                                        <th colspan=2>Bank Verification</th>
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th>Verified</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>";

                            $table .= "<tbody>";
                            $total_amount = $paid_amount = $change_amount = $charges = $hold_cash = 0;
                            $total_hold_cash = $net_deposit = $total_exvat = $total_vat = 0;
                            $receipt_ids = [];
                            // Receipts 
                            foreach ($results as $value) {
                                $qtn = get_revised_no($value->quotation_id);
                                $type = get_quotation($value->quotation_id, 'type');

                                $total_amount += $value->total_amount;
                                $paid_amount += $value->paid_amount;
                                $change_amount += $value->change_amount;
                                $charges += $value->charges;

                                if (!in_array($value->receipt_id, $receipt_ids)) {
                                    $hold_cash = get_cash_on_hold_balance($value->receipt_id); //$value->hold_cash;
                                    $receipt_ids[] = $value->receipt_id;
                                } else {
                                    $hold_cash = 0;
                                }
                                $total_hold_cash += $hold_cash;


                                $net_deposit += $value->net_deposit;
                                $vat = get_vat_ex_amount($value->paid_amount);
                                $exvat = get_ex_vat_amount($value->paid_amount);
                                $total_vat += $vat;
                                $total_exvat += $exvat;
                                $table .= "<tr>
                                            <td>$value->receipt_id</td>
                                            <td>$qtn</td>
                                            <td>" . number_format($value->total_amount, 2) . "</td>
                                            <td>$type</td>
                                            <td>" . number_format($value->paid_amount, 2) . "</td>
                                            <td>" . number_format($value->paid_amount / $value->total_amount * 100, 2) . " %</td>
                                            <td>" . number_format($vat, 2) . "</td>
                                            <td>" . number_format($exvat, 2) . "</td>
                                            <td style='text-align:left'>$value->received_from</td>
                                            <td>" . rb_date($value->payment_date) . "</td>
                                            <td>$value->payment_status</td>
                                            <td style='text-align:left'>$value->payment_method</td>
                                            <td>" . number_format($value->change_amount, 2) . "</td>
                                            <td>" . number_format($value->charges, 2) . "</td>
                                            <td>" . number_format($hold_cash, 2) . "</td>
                                            <td>" . number_format($value->net_deposit, 2) . "</td>
                                            <td>$value->bank_status</td>
                                            <td>" . rb_date($value->verify_date) . "</td>
                                            </tr>";
                            }

                            $table .= "<tr>
                                            <td colspan=2><b>Total</b></td>
                                            <td><b>" . number_format($total_amount, 2) . "</b></td>
                                            <td></td>
                                            <td><b>" . number_format($paid_amount, 2) . "</b></td>
                                            <td></td>
                                            <td><b>" . number_format($total_vat, 2) . "</b></td>
                                            <td><b>" . number_format($total_exvat, 2) . "</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><b>" . number_format($change_amount, 2) . "</b></td>
                                            <td><b>" . number_format($charges, 2) . "</b></td>
                                            <td><b>" . number_format($total_hold_cash, 2) . "</b></td>
                                            <td><b>" . number_format($net_deposit, 2) . "</b></td>
                                            <td></td>
                                            <td></td>
                                            </tr>";

                            $table .= "</tbody>";
                            $table .= "</table>";

                            $html .= empty($getdata['pdf']) ? 'Total Deposit: <b>' . number_format($net_deposit, 2) . '</b>' : '';
                            $html .= $table;
                            echo $html .= "</div></div>";
                            $pdf_file = make_pdf_file_name("BANK_DEPOSIT_REGISTORY_{$from_date}_{$to_date}.pdf")['path'];
                            ?>

                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-center">
                                    <a href="?page=bank-deposit"  class="btn btn-dark btn-sm" >Back</a>&nbsp;&nbsp;
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                    }
                                    ?>
                                    <a href = '<?= export_excel_report($pdf_file, 'bank_deposit_registory', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
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
