<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_petty_casht_registry_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $month = !empty($getdata['month']) ? $getdata['month'] : '';
    $account_name = !empty($getdata['account_name']) ? $getdata['account_name'] : PAYMENT_SOURCE[1];
    if (!empty($month)) {
        $month_state = explode('.', $getdata['month']);
        $from_date = $month_state[0];
        $to_date = $month_state[1];
    } else {
        $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : date('Y-m-01');
        $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : date('Y-m-t');
    }

    $sql = "SELECT * FROM {$wpdb->prefix}ctm_bank_transactions WHERE account_name='{$account_name}' AND transaction_date>='$from_date' AND transaction_date<='$to_date' ORDER BY transaction_time ASC ";
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
                            <table id="tbl-filter" cellpadding="5" cellspacing="5" style="width:900px">
                                <tr>
                                    <td><span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span></td>
                                    <td><label>Account:</label><br/>
                                        <select name="account_name" onchange="this.form.submit()">
                                            <option value="">Source Account</option>
                                            <?php
                                            foreach (PAYMENT_SOURCE as $value) {
                                                $selected = $value == $account_name ? 'selected' : '';
                                                ?>
                                                <option value="<?= $value ?>" <?= $selected ?>><?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
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
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:26px;font-weight:bold'>BANK RECONCILIATION STATEMENT</span></h4>
                                            <span style='font-size:18px;font-weight:bold'>{$account_name}</span></td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                            $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th>Trasaction Date</th>
                                        <th>Bank Date</th>
                                        <th>Particulars</th>
                                        <th>Voucher Type</th>
                                        <th>Voucher No</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                    </tr>
                                    
                                </thead>";

                            $table .= "<tbody>";
                            $total_debit = $paid_credit = 0;

                            foreach ($results as $value) {

                                $total_debit += $value->debit;
                                $paid_credit += $value->credit;

                                $table .= "<tr>
                                            <td>" . rb_date($value->transaction_date) . "</td>
                                            <td>" . rb_date($value->bank_date) . "</td>
                                            <td>$value->particulars</td>
                                            <td>$value->voucher_type</td>
                                            <td>$value->voucher_no</td>
                                            <td>" . number_format($value->debit, 2) . "</td>
                                            <td>" . number_format($value->credit, 2) . "</td>
                                            </tr>";
                            }

                            $table .= "<tr>
                                            <td colspan=5><b>Total</b></td>
                                            <td><b>" . number_format($total_debit, 2) . "</b></td>                                           
                                            <td><b>" . number_format($paid_credit, 2) . "</b></td>                                           
                                            </tr>";

                            $table .= "</tbody>";
                            $table .= "</table>";

                            $html .= empty($getdata['pdf']) ? 'Balance: <b>' . number_format(get_closing_amount($account_name), 2) . '</b>' : '';
                            $html .= $table;
                            echo $html .= "</div></div>";
                            $pdf_file = make_pdf_file_name("BANK_ACCOUNT_{$account_name}_{$from_date}_{$to_date}.pdf")['path'];
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
    generate_pdf($html, $pdf_file,null,1);
}
