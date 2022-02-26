<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_store_credit_note_view_page() {

    global $wpdb, $current_user;
    $date = current_time('mysql');
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    $error = '';
    $sales_reversal_id = $wpdb->get_var("SELECT sales_reversal_id FROM {$wpdb->prefix}ctm_store_credit_note where id='{$id}'");
    $sales_reversal_status = $wpdb->get_var("SELECT status FROM {$wpdb->prefix}ctm_sales_reversal where id='{$sales_reversal_id}'");

    if ($sales_reversal_status != 'Approved') {
        $error = 'Please approved "Reversal of confirmed QTN" status.';
    } else if (!empty($postdata['status']) && $postdata['status'] == 'Approved') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_store_credit_note set status='{$postdata['status']}',total_amount='{$postdata['total_amount']}',deduct_amount='{$postdata['deduct_amount']}',  updated_by='$current_user->ID', updated_at='{$date}' where id='{$id}'");

        $receipt = get_receipt($postdata['receipt_no']);
        $word = !empty($postdata['word']) ? $postdata['word'] : '';
        $deduct_amount = !empty($postdata['deduct_amount']) ? $postdata['deduct_amount'] : '';
        $total_amount = !empty($postdata['total_amount']) ? $postdata['total_amount'] : '';
        $paid_percent = get_paid_percent($receipt->quotation_id);

        /* Update Balance */
        $wpdb->get_row("UPDATE {$wpdb->prefix}ctm_receipts SET balance_amount='0.00'  where id='{$postdata['receipt_no']}'");

        /* Credit Entry */
        $data = ['quotation_id' => $receipt->quotation_id, 'revised_no' => $receipt->revised_no, 'client_id' => $receipt->client_id,
            'total_amount' => $total_amount, 'paid_amount' => -$deduct_amount, 'balance_amount' => 0, 'paid_percent' => $paid_percent,
            'word' => $word, 'received_from' => $receipt->received_from, 'payment_method' => 'Store Credit', 'payment_date' => $date, 'receipt_type' => 'Credit',
            'accountant' => $receipt->accountant, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_receipts", array_map('trim', $data), wpdb_data_format($data));

        /* Balance Entry */
        $data1 = ['quotation_id' => $receipt->quotation_id, 'revised_no' => $receipt->revised_no, 'client_id' => $receipt->client_id,
            'total_amount' => $receipt->total_amount - $total_amount, 'paid_amount' => $receipt->paid_amount - $deduct_amount, 'balance_amount' => $receipt->balance_amount - $deduct_amount, 'paid_percent' => $paid_percent,
            'word' => $word, 'received_from' => $receipt->received_from, 'payment_method' => $receipt->payment_method, 'payment_date' => $date, 'receipt_type' => 'Dummy',
            'accountant' => $receipt->accountant, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        if(rb_float($receipt->total_amount - $total_amount)){
            $wpdb->insert("{$wpdb->prefix}ctm_receipts", array_map('trim', $data1), wpdb_data_format($data1));
        }
    }

    $store_credit_note = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_store_credit_note where id='{$id}'");

    if (empty($store_credit_note)) {
        wp_redirect(admin_url('/admin.php?page=store-credit-note'));
        exit();
    }

    $quotation = get_quotation($store_credit_note->quotation_id);

    $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$store_credit_note->quotation_id}' AND id IN ({$store_credit_note->meta_ids}) ORDER BY id ASC");
    $client = get_client($quotation->client_id);

    $is_diss = !empty(rb_float($quotation_meta[0]->discount)) ? true : (rb_float($quotation->special_discount) ? true : false);

    if ($quotation->promo_type == 'Export') {
        $quotation->vat = 'wovat';
    }

    $qtn = get_revised_no($store_credit_note->quotation_id);
    ?>
    <div id='welcome-to-aquila' class='postbox'>
        <div class='inside' style='max-width:800px;margin:auto'>
            <?php
            $html = "
            <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], 
            #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], 
            #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
            #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
                table tr th,table tr td,p,b,span{font-size:11px;font-family:'Tahoma'}
                h6 span{text-transform:uppercase;}
                p{margin-bottom: 0;}
                table{empty-cells:hidden;}
                table tr td{font-size:11px;font-family:'Tahoma'}
                .fnt11{font-size:10px;font-family:'Tahoma'}
                table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7),table tr td:nth-child(8),table tr td:nth-child(9){width: 55px;}
                .text-center{text-align:center;}
                .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                .attachment-large{width:50px;height: auto;}
                ul{margin: auto;padding: 0 15px;}
                ul#terms{ padding: 0; margin: 0;} 
                ul#terms li{ padding: 2px 0!important; margin: 0!important; text-align: left!important; width: 100%!important; font-size:12px; font-weight: bold;}
            </style>
            
                    <br/>
                    <table width='800'>
                        <tr valign='top'>
                            <td style='text-align:left'></td>
                            <td style='text-align:right'>
                            <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan='2' style='text-align:center'><br/>
                                <h6><span style='font-size:22px;text-transform: uppercase;'>STORE CREDIT NOTE</span></h6>
                            </td>
                        </tr>
                    </table>
                    <br/>
                     <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
              <tr class=bg-blue>
              <td colspan='4'><b>Customer Details</b></td><td colspan='3' class='text-center'><b>Store Credit Note # </b></td><td colspan='3'  class='text-center'><b>Date</b></td>
              </tr>
              <tr>
              <td><b>Name:</b></td><td colspan='3'><b>{$client->name}</b></td>"
                    . "<td colspan='3' class='text-center'><b>{$store_credit_note->id}</b></td>"
                    . "<td colspan='3' class='text-center'><b>" . rb_datetime($store_credit_note->created_at) . "</b></td>
              </tr>
              <tr>
              <td rowspan=2><b>Address</b></td><td colspan='3' ><b>{$client->address}</b></td>
                  <td colspan='3' class='bg-blue text-center' ><b>Quotation #</b></td>
                  <td colspan='3' class='bg-blue text-center'><b>Receipt</b></td>
              </tr>
              <tr>
              <td colspan='3'><b>{$client->city}, {$client->country}</b></td><td colspan='3' class='text-center'><b>{$qtn}</b></td>"
                    . "<td colspan='3'  class='text-center'><b>{$store_credit_note->receipt_no}</b></td>
              </tr>
              <tr>
              <td><b>Phone</b></td><td colspan='3'><b>{$client->phone}</b></td>
                  <td  class='bg-blue text-center' rowspan='3' colspan=6 ><b>Roche Bobois <br/>TRN# 100383178900003</b></td>
              </tr>
              <tr>
              <td><b>Email</b></td><td colspan='3'><b>{$client->email}</b></td>
              </tr>
              <tr>
              <td><b>TRN</b></td><td colspan='3'><b>{$client->trn}</b></td>
              </tr>
              <tr class='text-center bg-blue'>
                <td rowspan=2 class='text-center'><b>" . ( $quotation->type == 'Stock' ? 'Entry#' : 'SUP') . "</b></td>
                <td rowspan=2 class='text-center'><b>Item Description</b></td>
                <td rowspan=2 class='text-center' width=50><b>Image</b></td>
                <td rowspan=2 class='text-center'><b>qty</b></td>
                <td class='text-center'><b>Unit Price<br/>(Incl.&nbsp;VAT)</b></td>
                <td class='text-center'><b>Net Price<br/>" . ($is_diss ? '(AFTER DISCOUNT)' : '' ) . "</b></td>
                <td class='text-center'><b>Price<br/>(EXCLUDING VAT)</b></td>
                <td class='text-center'><b>VAT<br/>@" . ($quotation->vat == 'wovat' ? 0 : 5) . "%</b></td>
                <td class='text-center'><b>Total</b></td>
                <td class='text-center'><b>Advance <br/>Payment <br/> Received</b></td>
              </tr>
              <tr class='text-center bg-blue'>
              <td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td>
              </tr>";

            $total_net_price = $total_vat = $total_incl_amt = $total_advance_amt = 0;
            foreach ($quotation_meta as $value) {
                $item = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items where id='{$value->item_id}'");

                $total_net_price += $value->total_incl_vat;
                $total_vat += $value->vat;
                $total_incl_amt += $total_net_price;
                $advance_amt = $value->total_incl_vat / 100 * $store_credit_note->paid_percent;
                $total_advance_amt += $advance_amt;

                $html .= "<tr>"
                        . "<td class='fnt11 text-center' >" . ( $quotation->type == 'Stock' ? $value->entry : $value->sup_code) . "</td>"
                        . "<td class=fnt11 >" . nl2br($value->item_desc) . "</td>"
                        . "<td class=fnt11 ><img src='" . get_image_src($item->image) . "' width=50  style='margin: auto;width: 50px; '></td>"
                        . "<td class='fnt11 text-center' >{$value->quantity}</td>"
                        . "<td class='fnt11 text-center' > " . number_format($value->price_incl_vat, 2) . "</td>"
                        . "<td class='fnt11 text-center' >" . number_format($value->net_price + $value->vat, 2) . "</td>"
                        . "<td class='fnt11 text-center' >" . number_format($value->net_price, 2) . "</td>"
                        . "<td class='fnt11 text-center' >" . number_format($value->vat, 2) . "</td>"
                        . "<td class='fnt11 text-center' >" . number_format($value->total_incl_vat, 2) . "</td>"
                        . "<td class='fnt11 text-center' >" . number_format($advance_amt, 2) . "</td>"
                        . "</tr>";
            }




            $html .= "<tr class=' font-weight-bold'>
                <td colspan=7><b>SUB TOTAL</b></td><td class='text-center'><b>" . number_format($total_vat, 2) . "</b></td>"
                    . "<td  class='text-center'><b>" . number_format($total_net_price, 2) . "</b></td>"
                    . "<td  class='text-center'><b>" . number_format($total_advance_amt, 2) . "</b></td>
                </tr>
                
                <tr class='text-left font-weight-bold'>
                <td colspan=9><b>TOTAL CREDIT AMOUNT</b></td><td class='text-center'><b>" . number_format($total_advance_amt, 2) . "</b></td>
                </tr>";

            $html .= "<tr>
                <td colspan=10><b>AED:<b> <span id='word'>" . num_to_words($total_advance_amt) . "</span></td>
                </tr>
                </table>
                <br/>
              
                <table>
                    <tr>
                        <td style='color:red'><b>Amount " . number_format($total_net_price, 2) . " AED to be deducted against QTN # {$qtn} and amount " . number_format($total_advance_amt, 2) . " AED equivalent to advance paid against cancelled item to be deducted against receipt # {$store_credit_note->receipt_no} and to be apply as store credit.</b></td>
                    </tr>
                </table>
                <br/><br/>
                <br/><br/>
              <table width='800'>
                  <tr>
                      <td><b>For Roche Bobois</b></td>
                      <td style='text-align:right'><b>Signature of the customer</b></td>
                  </tr>
              </table>
              <br/><br/><br/><br/>
              ";
            echo $html;
            $pdf_file = $store_credit_note->pdf_path;
            store_pdf_path('ctm_store_credit_note', $store_credit_note->id, "STORE_CREDIT_NOTE_{$store_credit_note->id}.pdf", $pdf_file);
            ?>
        </div>
    </div>
    <div class="row btn-bottom">
        <div class="col-sm-6 text-center">
            <a href="admin.php?page=store-credit-note"  class="button-secondary" >Back</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
            } if (pdf_exist($pdf_file)) {
                echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a> &nbsp;&nbsp;&nbsp;&nbsp;";
            }
            ?>
        </div>
        <div class="col-sm-6 text-center">
            <form method="post">
                <table cellpadding="5">
                    <tr><td colspan="2"><span class="text-red"><?= $error ?></span></td></tr>
                    <tr>
                        <td>
                            <select name='status' class="form-control" <?= $store_credit_note->status == 'Approved' ? 'disabled' : (!empty($error) ? 'disabled' : '') ?>>
                                <option value="Pending">Pending</option>
                                <option value="Approved" <?= $store_credit_note->status == 'Approved' ? 'selected' : (!empty($error) ? 'disabled' : '') ?>>Approved</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="total_amount" value="<?= $total_net_price ?>" />
                            <input type="hidden" name="deduct_amount" value="<?= $total_advance_amt ?>" />
                            <input type="hidden" name="word" value="<?= num_to_words($total_advance_amt) ?>" />
                            <input type="hidden" name="receipt_no" value="<?= $store_credit_note->receipt_no ?>" />
                            <input type="hidden" name="quotation_id" value="<?= $store_credit_note->quotation_id ?>" />
                            <button type="submit" name="update" value="1" class="btn btn-primary btn-sm" <?= (!empty($error) ? 'disabled' : '') ?>>Update</button>

                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('store_credit_note_dir'));
}
