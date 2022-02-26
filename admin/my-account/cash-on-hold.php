<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_cash_on_hold_page() {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    separate_cash_on_hold();

    if (!empty($postdata['submit'])) {
        $hold_deposit_amount = (float) get_option('hold_deposit_amount', 0);
        $amount = $postdata['amount'] + $hold_deposit_amount; 
        $receipts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE payment_method='Cash' AND hold_cash !='0.00'");
        foreach ($receipts as $value) {
            $amount -= $value->hold_cash;
            if ($amount > 0) {
                $sql = "UPDATE {$wpdb->prefix}ctm_receipts SET deposit_amount=paid_amount, hold_cash='0.00' WHERE id='$value->id'";
                $wpdb->query($sql);
                $data = ['receipt_id' => $value->id, 'payment_status' => 'Balance', 'change_amount' => 0, 'charges' => 0, 'hold_cash' => 0, 'net_deposit' => $value->hold_cash, 'bank_status' => 'Verified', 'verify_date' => $date, 'updated_by' => $current_user->ID, 'created_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
                $wpdb->insert("{$wpdb->prefix}ctm_bank_deposits", $data, wpdb_data_format($data));
                update_option('hold_deposit_amount', $amount);
            } else {
                break;
            }
            update_option('hold_deposit_amount', $amount);
        }
    }

    $receipts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE payment_method='Cash' AND hold_cash !='0.00' ");
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
                        <br/>
                        <span id="open-close-menu"  style="margin:0" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                        <br/><br/>

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
                                            
                                            <tr valign=middl>
                                                <td style='text-align:center'>
                                                    <h4><span style='font-size:26px;font-weight:bold'>ROCHE BOBOIS</span></h4>
                                                    <h6><span style='font-size:18px;'>CASH ON HOLD</span></h6>
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
                        $hold_cash = 0;
                        foreach ($receipts as $value) {
                            $client_name = get_client($value->client_id, 'name');
                            $qtn = get_revised_no($value->quotation_id);
                            $hold_cash += $value->hold_cash;

                            $table .= "<tr>
                                            <td>{$i}</td>
                                            <td>{$qtn}</td>
                                            <td>{$value->id}</td>
                                            <td>$client_name</td>
                                            <td>{$value->payment_method}</td>
                                            <td>" . number_format($value->hold_cash, 2) . "</td>
                                            <td>" . rb_date($value->payment_date) . "</td>
                                            <td>" . ($value->payment_method == 'Check' ? $value->check_no : '') . "</td>
                                            <td>" . rb_date($value->check_date) . "</td>
                                            <td>{$value->bank}</td>
                                        </tr>";
                            $i++;
                        }

                        $table .= "<tr>
                                            <td colspan=5><b>Total</b></td>
                                            <td><b>" . number_format($hold_cash, 2) . "</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>";

                        $table .= "</tbody>";
                        $table .= "</table>";



                        $html .= $table;
                        echo $html .= "</div></div>";
                        ?>

                        <div class="row btn-bottom">
                            <div class="col-sm-2 text-center">
                                <a href="?page=daily-cash-check"  class="btn btn-dark btn-sm" >Back</a>&nbsp;&nbsp;
                            </div>
                            <div class="col-sm-10 text-center">
                                <form method="post">
                                    Hold Remaining Amount:&nbsp;&nbsp;&nbsp;&nbsp;<b><?=get_option('hold_deposit_amount', 0)?></b>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="number" step="0.01" name="amount" placeholder="Deposit Amount" required />
                                    <button type="submit" name="submit" value="submit" class="btn btn-primary btn-sm">Deposit</button>
                                </form>
                            </div>
                        </div>
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
}
