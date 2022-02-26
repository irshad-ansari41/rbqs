<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_daily_cash_check_page() {
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

    separate_cash_on_hold();
    $receipts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE payment_method IN ('Cash','Check') AND payment_date>='$from_date' AND payment_date<='$to_date'");
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
                                    <td colspan="4" style="text-align:left">
                                        <label>Payment Date(From ~ To):</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
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
                                        <input type="date" name="from_date"  value="<?= $from_date ?>" style="width:175px"  required />
                                    </td>
                                    <td>
                                        <input type="date" name="to_date"  value="<?= $to_date ?>" style="width:175px"  required />
                                    </td>

                                    <td>
                                        <button type="submit" name="filter" value="1" class="btn btn-sm btn-primary ">Filter</button>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="admin.php?page=<?= $getdata['page'] ?>" class="btn btn-sm btn-secondary text-white">RESET</a>
                                    </td>
                                    <td>
                                        <a id="add-new-client" href="<?= "admin.php?page=cash-on-hold" ?>" class="page-title-action btn-primary" target="_blank">Cash on Hold Registry</a>
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
                                                <td style='text-align:right'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                    <br/><br/>
                                                </td>
                                            </tr>
                                            <tr valign=middl>
                                                <td style='text-align:center'>
                                                    <h4><span style='font-size:26px;font-weight:bold'>ROCHE BOBOIS</span></h4>
                                                    <h6><span style='font-size:18px;'>DAILY CASH & CHECK DEPOSIT CONTROL</span></h6>
                                                </td>
                                            </tr>
                                        </table>";

                            $table .= "<table  width='800' style='width:100%'>
                                            <tr valign=middl>
                                                <td style='text-align:right'>
                                                <span>Date: " . rb_date('now') . "</span>
                                                </td>
                                            </tr>
                                        </table><br/>";

                            $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle>
                                        <th>S.NO</th>
                                        <th>QTN #</th>
                                        <th>RECEIPT #</th>
                                        <th>NAME OF CUSTOMER</th>
                                        <th>TYPE <br/> CASH / CHECK</th>
                                        <th>AMOUNT</th>
                                        <th>PAYMENT DATE</th>
                                        <th>CHECK #</th>
                                        <th>CHECK DATE</th>
                                        <th>BANK</th>
                                    </tr>
                                </thead>";

                            $table .= "<tbody>";
                            $i = 1;
                            $net_deposit = 0;
                            foreach ($receipts as $value) {
                                $client_name = get_client($value->client_id, 'name');
                                $qtn = get_revised_no($value->quotation_id);
                                $deposit_ampunt = $value->payment_method == 'Check'?($value->deposit_amount+$value->hold_cash):$value->deposit_amount;
                                $net_deposit += $deposit_ampunt;

                                $table .= "<tr>
                                            <td>{$i}</td>
                                            <td>{$qtn}</td>
                                            <td>{$value->id}</td>
                                            <td>$client_name</td>
                                            <td>{$value->payment_method}</td>
                                            <td>" . number_format($deposit_ampunt, 2) . "</td>
                                            <td>" . rb_date($value->payment_date) . "</td>
                                            <td>" . ($value->payment_method == 'Check' ? $value->check_no : '') . "</td>
                                            <td>" . rb_date($value->check_date) . "</td>
                                            <td>{$value->bank}</td>
                                        </tr>";
                                $i++;
                            }

                            $table .= "<tr>
                                            <td colspan=5><b>Total</b></td>
                                            <td><b>" . number_format($net_deposit, 2) . "</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>";

                            $table .= "</tbody>";
                            $table .= "</table>";

                            $table .= "<br/><br/><table width='800' style='width:100%' cellpadding=10 >
                                            <tr valign=middle>
                                                <td style='text-align:left;width:200px;font-size:12px'><strong>CHECK RECEIVED BY:</strong><br/><br/><br/><br/></td>
                                                <td style='text-align:center;width:200px;line-height: 15px;'><div>-----------------------------------</div>Name</td>
                                                <td style='width:200px'>&nbsp;</td>
                                                <td style='text-align:center;width:200px;line-height: 15px;'><div>-----------------------------------</div>Date & Signature</td>
                                                <td></td>
                                            </tr>
                                            <tr valign=middle>
                                                <td style='text-align:left;width:200px;font-size:12px'><strong>DEPOSIT SLIP RECEIVED BY:</strong><br/><br/><br/><br/></td>
                                                <td style='text-align:center;width:200px;line-height: 15px;'><div>-----------------------------------</div>Name</td>
                                                <td style='width:200px'>&nbsp;</td>
                                                <td style='text-align:center;width:200px;line-height: 15px;'><div>-----------------------------------</div>Date & Signature</td>
                                                <td></td>
                                            </tr>
                                        </table><br/><br/>";

                            $html .= $table;
                            echo $html .= "</div></div>";
                            $pdf_file = make_pdf_file_name("DAILY_CASH_CHECK_" . (!empty($value->payment_date) ? $value->payment_date : '') . ".pdf")['path'];
                            ?>

                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-center">
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                    }
                                    ?>
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
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('daily_cash_check_copy_dir'));
}
