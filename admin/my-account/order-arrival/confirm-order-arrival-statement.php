<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_confirm_order_arival_page() {

    global $wpdb;

    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($getdata['id']) ? $getdata['id'] : '';
    $create_date = !empty($postdata['create_date']) ? $postdata['create_date'] : '';

    if (empty($id)) {
        wp_redirect(admin_url("/admin.php?page=order-arrival-list"));
        exit();
    }



    if ($create_date) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_order_arrival SET create_date='$create_date' where id='{$id}'");
    }

    $order_arrival = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_order_arrival where id='{$id}'");

    $status = check_quotation_confirm_partial_status($order_arrival->quotation_id);
    $quotation = get_quotation($order_arrival->quotation_id);
    $qtn = get_revised_no($order_arrival->quotation_id);
    $sales_person = get_qtn_sales_person($order_arrival->quotation_id);
    $client = get_client($order_arrival->client_id);
    $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$order_arrival->quotation_id}' ORDER BY id ASC");

    if ($order_arrival->status == 'Partial') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_order_arrival SET status='$status'  where id='{$id}'");
        wp_redirect(admin_url("/admin.php?page=partial-order-arrival&id={$id}"));
        exit();
    }
    $percent = get_paid_percent($order_arrival->quotation_id);
    ?>
    <div class = "wrap">
        <div id = "dashboard-widgets-wrap">
            <div id = "dashboard-widgets" class = "columns-1">
                <div id = "postbox-container" class = "postbox-container">
                    <div id = "normal-sortables" class = "meta-box-sortables ui-sortable">
                        <span id = "open-close-menu" title = "Close & Open Side Menu" class = "dashicons dashicons-editor-code"></span>
                        <h1 class = "wp-heading-inline">&nbsp;</h1>
                        <a href="?page=order-arrival-list"  class="page-title-action" >Back</a>
                        <div id='page-inner-content' class='postbox'>
                            <div class='inside' style='max-width:100%;margin:auto'><br/>
                                <?php
                                $html = '';
                                if (!empty($quotation_meta)) {
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
                                .ARRIVED-status{background:#cef195;color:#000;border:1px solid #000;}
                                .DELIVERED-status{background:green;color:#000;border:1px solid #000;}
                            </style>
                            
                                    <table width='800' style='width:100%'>
                                        <tr valign='top'>
                                            <td style='text-align:left'></td>
                                            <td style='text-align:right'>
                                            <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan='2' style='text-align:center'><br/>
                                                <h6><span style='font-size:22px;text-transform: uppercase;'>Confirm Order Arrival Statement - T1</span></h6>
                                            </td>
                                        </tr>
                                    </table>
                                    <br/>
                                     <table border='1' style='border-collapse: collapse;width:100%' width=800 cellpadding=5 cellspacing=5>
                              <tr class=bg-blue>
                              <td colspan='3'><b>Customer Details</b></td><td colspan='4' class='text-center'><b>Quotation # </b></td><td colspan='3'  class='text-center'><b>Date</b></td>
                              </tr>
                              <tr>
                              <td><b>Name:</b></td><td colspan='2'><b>{$client->name}</b></td>"
                                            . "<td colspan='4' class='text-center'><b>{$qtn}</b></td>"
                                            . "<td colspan='3' class='text-center'><b>" . rb_datetime($quotation->created_at) . "</b></td>
                              </tr>
                              <tr>
                              <td rowspan=2><b>Address</b></td><td colspan='2' ><b>{$client->address}</b></td>
                                  <td colspan='4' class='bg-blue text-center' ><b>Sales Person #</b></td>
                                  <td colspan='3' class='bg-blue text-center'><b>Order Status</b></td>
                              </tr>
                              <tr>
                              <td colspan='2'><b>{$client->city}, {$client->country}</b></td><td colspan='4' class='text-center'><b>{$sales_person}</b></td>"
                                            . "<td colspan='3'  class='text-center'><b>{$status}</b></td>
                              </tr>
                              <tr>
                              <td><b>Phone</b></td><td colspan='2'><b>{$client->phone}</b></td>
                                  <td  class='bg-blue text-center' rowspan='3' colspan=7 ><b>Roche Bobois <br/>TRN# 100383178900003</b></td>
                              </tr>
                              <tr>
                              <td><b>Email</b></td><td colspan='2'><b>{$client->email}</b></td>
                              </tr>
                              <tr>
                              <td><b>TRN</b></td><td colspan='2'><b>{$client->trn}</b></td>
                              </tr>
                              <tr class='text-center bg-blue'>
                                <td rowspan=2 class='text-center'>Status (Ordered/<br/> Confirmed/<br/> Delivered to<br/> FF/<br/> Arrived/<br/> Delivered)</td>
                                <td rowspan=2 class='text-center'>Item Description</td>
                                <td rowspan=2 class='text-center' width=50>Image</td>
                                <td rowspan=2 class='text-center'>Qty</td>
                                <td rowspan=2 class='text-center'><b>Unit Price<br/>(Incl.&nbsp;VAT)</b></td>
                                <td rowspan=2 class='text-center'><b>Net Price<br/>(AFTER DISCOUNT)</b></td>
                                <td colspan=2  class='text-center'><b>Advance payment received</b></td>
                                <td rowspan=2 class='text-center'><b>Partial<br/> Delivery<br/> Amount<br/> Payable</b></td>
                                <td rowspan=2 class='text-center'><b>Advance<br/> Amount<br/> against<br/> balance<br/> items</b></td>
                              </tr>
                              <tr class='text-center bg-blue'>
                              <td class='text-center'>%</td><td class='text-center'>Amount</td>
                              </tr>";

                                    $total_net_price = $total_vat = $total_incl_amt = $advance = $total_advance = $net_price = $total_net_price = 0;
                                    $partial_amount_payable = $advance_amount_payable = 0;
                                    foreach ($quotation_meta as $value) {
                                        $item = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items where id='{$value->item_id}'");
                                        $po = $wpdb->get_row("SELECT order_registry,stk_inv_status FROM {$wpdb->prefix}ctm_quotation_po_meta where item_id='{$value->item_id}' AND quotation_id='{$value->quotation_id}'");

                                        if ($po->stk_inv_status == 'DELIVERED') {
                                            $item_status = 'DELIVERED';
                                        } else if ($po->order_registry == 'ARRIVED') {
                                            $item_status = 'ARRIVED';
                                        } else {
                                            $item_status = $po->order_registry;
                                        }

                                        $net_price = $value->net_price + $value->vat;
                                        $total_net_price += $net_price;
                                        $advance = ($value->net_price + $value->vat) / 100 * $percent;
                                        $total_advance += $advance;
                                        $balance = $net_price - $advance;

                                        $balance1 = $item_status == 'DELIVERED' ? $balance : 0;
                                        $balance2 = in_array($item_status, ['ARRIVED', 'TRANSIT']) ? $balance : 0;

                                        $partial_amount_payable += $item_status == 'DELIVERED' ? $balance1 : 0;
                                        $advance_amount_payable += in_array($item_status, ['ARRIVED', 'TRANSIT']) ? $balance : 0;

                                        $html .= "<tr>"
                                                . "<td class='fnt11 text-center {$item_status}-status' >{$item_status}</td>"
                                                . "<td class=fnt11 >" . nl2br($value->item_desc) . "</td>"
                                                . "<td class=fnt11 ><img src='" . get_image_src($item->image) . "' width=50  style='margin: auto;width: 50px; '></td>"
                                                . "<td class='fnt11 text-center' >{$value->quantity}</td>"
                                                . "<td class='fnt11 text-center' > " . number_format($value->price_incl_vat, 2) . "</td>"
                                                . "<td class='fnt11 text-center' >" . number_format($net_price, 2) . "</td>"
                                                . "<td class='fnt11 text-center' >{$percent}%</td>"
                                                . "<td class='fnt11 text-center' >" . number_format($advance, 2) . "</td>"
                                                . "<td class='fnt11 text-center' >" . number_format($balance1, 2) . "</td>"
                                                . "<td class='fnt11 text-center' >" . number_format($balance2, 2) . "</td>"
                                                . "</tr>";
                                    }

                                    $html .= "<tr class=''>
                                        <td colspan=5>SUB TOTAL
                                        </td><td class='text-center'>" . number_format($total_net_price, 2) . "</td>"
                                            . "<td  class='text-center'>{$percent}%</td>"
                                            . "<td  class='text-center'>" . number_format($total_advance, 2) . "</td>"
                                            . "<td  class='text-center'>" . number_format($partial_amount_payable, 2) . "</td>"
                                            . "<td  class='text-center'><b>" . number_format($advance_amount_payable, 2) . "</b></td>
                                        </tr></table>
                                        <br/>
                                        <table>
                                            <tr>
                                                <td>Order is in transit and expected to arrive by " . rb_date($order_arrival->create_date, 'd F Y') . ". Amount payable <b>" . (number_format($advance_amount_payable, 2)) . "</b> AED for complete delivery. <b>" . number_format($total_net_price / 100 * 5, 2) . "</b> AED equal to 5% of quotation value will be charged per month after free storage period as stated on our quotation.</b></td>
                                            </tr>
                                        </table>
                                        <br/><br/>
                                        <br/><br/>";
                                    echo $html;

                                    $pdf_file = make_pdf_file_name("confirm_order_arrival_statement_{$id}.pdf")['path'];
                                } else {
                                    echo "<br/><br/><br/><br/><br/><br/><br/><br/>";
                                }
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($pdf_file)) { ?>
                            <div class="row btn-bottom">
                                <div class="col-sm-6 text-center">
                                    <a href="?page=order-arrival-list"  class="button-secondary" >Back</a>
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;";
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-6 text-center">
                                    <form method="post">
                                        <input type="date" name="create_date" value="<?= $order_arrival->create_date ?>" required />
                                        <input type="submit" name="update" value="Update" class="button-primary" />
                                    </form>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div><!--dashboard-widgets-wrap -->
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery('.chosen-select').chosen();
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($getdata['client_id']) ? $getdata['client_id'] : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Search By Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
            });

        });
    </script>
    <?php
    if (!empty($html)) {
        generate_pdf($html, $pdf_file, null, 1);
        pdf_copy($pdf_file, get_option('confirm_order_arrival_dir'));
    }
}
