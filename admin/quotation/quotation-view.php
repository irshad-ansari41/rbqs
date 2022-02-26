<?php
include_once 'send-quotation-email.php';
include_once 'send-in-whatsapp.php';

function admin_ctm_quotation_view_page() {
    global $wpdb;
    $postdata = filter_input_array(INPUT_POST);
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=quotation'));
        exit();
    }

    if (!empty($getdata['action']) && $getdata['action'] == 'PJO') {
        convert_project_to_pjp($id);
        wp_redirect(admin_url('/admin.php?page=project-job-order'));
        exit();
    }

    if (!empty($postdata['send_email'])) {
        $email_status = rb_send_email($postdata);
        $msg = !empty($email_status) ? "<strong>Success!</strong> Email has been sent successfully." : 0;
    }
    
    if (!empty($postdata['confrim_order'])) {
        create_confirm_order($postdata['id'], $postdata['receipt_no']);
        status_change_send_email('CONFIRMED');
        $msg = "<strong>Success!</strong> Quotation has been confirmed successfully.";
    }

    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$id}'");

    if ($quotation->status == 'Draft') {
        wp_redirect(admin_url('/admin.php?page=quotation'));
        exit();
    }

    $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$quotation->id}' ORDER BY id ASC");
    $client = get_client($quotation->client_id);

    if ($quotation->promo_type == 'Export') {
        $quotation->vat = 'wovat';
    }
    $is_diss = !empty(rb_float($quotation_meta[0]->discount)) ? true : (rb_float($quotation->special_discount) ? true : false);

    make_model_send_quotation_email($quotation, $client);
    make_model_send_whatsapp($quotation, $client);

    if (!empty($msg)) {
        ?>
        <br/>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?= $msg ?>
        </div>
        <?php
    }
    $qtn = get_revised_no($quotation->id);

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
            <div id='welcome-to-aquila' class='postbox'>
                <div class='inside' style='max-width:800px;margin:auto'>
                    
                    <table width='800'>
                        <tr valign='top'>
                            <td style='text-align:left'>
                            " . (!empty($quotation) ? wp_get_attachment_image($quotation->logo, 'thumb') : '') . "
                            </td>
                            <td style='text-align:right'>
                            <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan='2' style='text-align:center'><br/>
                                <h6><span style='font-size:22px;text-transform: uppercase;'>{$quotation->type} QUOTATION " . ($quotation->promo_type ? " - Promotion" : "") .
            ( $quotation->vat == 'wvat' ? ' w/ VAT ' : ($quotation->vat == 'wovat' ? ' Zero VAT ' : '') ) . "</span></h6>
                            </td>
                        </tr>
                    </table>
                    <br/>
                     <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
              <tr class=bg-blue>
              <td colspan='3'><b>Customer Details</b></td><td colspan='3' class='text-center'><b>Quotation # </b></td><td colspan='3'  class='text-center'><b>Date</b></td>
              </tr>
              <tr>
              <td><b>Name:</b></td><td colspan='2'><b>{$client->name}</b></td>"
            . "<td colspan='3' class='text-center'><b>{$qtn}</b></td>"
            . "<td colspan='3' class='text-center'><b>" . rb_datetime($quotation->created_at) . "</b></td>
              </tr>
              <tr>
              <td rowspan=2><b>Address</b></td><td colspan='2' ><b>{$client->address}</b></td>
                  <td colspan='3' class='bg-blue text-center' ><b>Sales Person #</b></td>
                  <td colspan='3' class='bg-blue text-center'><b>Destination</b></td>
              </tr>
              <tr>
              <td colspan='2'><b>{$client->city}, {$client->country}</b></td><td colspan='3' class='text-center'><b>{$quotation->sales_person}</b></td>"
            . "<td colspan='3'  class='text-center'><b>" . get_location($quotation->city_id, 'city') . "</b></td>
              </tr>
              <tr>
              <td><b>Phone</b></td><td colspan='2'><b>{$client->phone}</b></td>
                  <td  class='bg-blue text-center' rowspan='3' colspan=6 ><b>Roche Bobois <br/>TRN# 100383178900003</b></td>
              </tr>
              <tr>
              <td><b>Email</b></td><td colspan='2'><b>{$client->email}</b></td>
              </tr>
              <tr>
              <td><b>TRN</b></td><td colspan='2'><b>{$client->trn}</b></td>
              </tr>
              <tr class='text-center bg-blue'>
                <td rowspan=2 class='text-center'><b>" . ( $quotation->type == 'Stock' ? 'Entry#' : 'SUP') . "</b></td>
                <td rowspan=2 class='text-center'><b>Item Description</b></td>
                <td rowspan=2 class='text-center' width=100><b>Image</b></td>
                <td rowspan=2 class='text-center'><b>qty</b></td>
                <td class='text-center'><b>Unit Price</b></td>
                <td class='text-center'><b>Net Price<br/>" . ($is_diss ? '(AFTER DISCOUNT)' : '' ) . "</b></td>
                <td class='text-center'><b>Price<br/>(EXCLUDING VAT)</b></td>
                <td class='text-center'><b>VAT<br/>@" . ($quotation->vat == 'wovat' ? 0 : 5) . "%</b></td>
                <td class='text-center'><b>Total</b></td>
              </tr>
              <tr class='text-center bg-blue'>
              <td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td>
              </tr>";



    $total_net_price = $total_vat = $total_incl_amt = 0;
    foreach ($quotation_meta as $value) {
        $item = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items where id='{$value->item_id}'");

        $total_net_price += $value->net_price;
        $total_vat += $value->vat;
        $total_incl_amt += $value->total_incl_vat;

        $html .= "<tr>"
                . "<td class='fnt11 text-center' >" . ( $quotation->type == 'Stock' ? $value->entry : $value->sup_code) . "</td>"
                . "<td class=fnt11 >" . nl2br($value->item_desc) . "</td>"
                . "<td class=fnt11 ><img src='" . get_image_src($item->image) . "' width=100  style='margin: auto;width: 100px; '></td>"
                . "<td class='fnt11 text-center' >{$value->quantity}</td>"
                . "<td class='fnt11 text-center' > " . number_format($value->price_incl_vat, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($value->net_price + $value->vat, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($value->net_price, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($value->vat, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($value->total_incl_vat, 2) . "</td>"
                . "</tr>";
    }


    $html .= "<tr class='text-left font-weight-bold'>
                <td colspan=6><b>SUB TOTAL</b></td><td class='text-center'><b>" . number_format($total_net_price, 2) . "</b></td>"
            . "<td class='text-center'><b>" . number_format($total_vat, 2) . "</b></td><td class='text-center'><b>" . number_format($total_incl_amt, 2) . "</b></td>
                </tr>
                <tr class='text-left font-weight-bold'>
                <td colspan=8><b>DELIVERY CHARGE</b></td><td class='text-center'><b>" . number_format($quotation->freight_charge, 2) . "</b></td>
                </tr>
                <tr class='text-left font-weight-bold'>
                <td colspan=8><b>TOTAL COST</b></td><td class='text-center'><b>" . number_format($quotation->total_amount, 2) . "</b></td>
                </tr>";

    $html .= "<tr>
                <td colspan=9><b>AED:<b> <span id='word'>{$quotation->word}</span></td>
                </tr>
                </table>
                <br/>
              <div id='terms'> " . apply_filters('the_content', $quotation->terms) . "</div>
              <br/>
              <div id='notes'><b>" . (!empty($quotation->notes) ? 'Notes:' : '') . "</b><b style='color:red'>" . apply_filters('the_content', $quotation->notes) . "</b></div>
              <br/><br/>
              <table width='800'>
                  <tr>
                      <td><b>For Roche Bobois</b></td>
                      <td style='text-align:right'><b>Signature of the customer</b></td>
                  </tr>
              </table>
              <br/><br/><br/><br/>
              </div>
              </div>";
    echo $html;
    $pdf_file = $quotation->pdf_path;
    store_pdf_path('ctm_quotations', $quotation->id, "QTN_{$qtn}_{$client->name}_({$quotation->type}).pdf", $pdf_file);
    ?>
    <div class="row btn-bottom">
        <div class="col-sm-4">
            <?php
            if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
            } if (pdf_exist($pdf_file)) {
                $pdf_url = download_pdf_url($pdf_file);
                echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;";
                echo ' <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myEmailModal" data-keyboard="false">Send Email</button>&nbsp;&nbsp;';
                echo ' <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myWhatsAppModal" data-keyboard="false">Send In WhatsApp</button>&nbsp;&nbsp;';
            }
            ?>
        </div>

        <?php
        if ($quotation->type == 'Stock') {

            if (empty($quotation->receipt_no)) {
                echo "<div class='col-sm-6'>
                                <a href='admin.php?page=receipt-create&qid={$quotation->id}&revised_no={$quotation->revised_no}&client={$client->name}&client_id={$quotation->client_id}&amount={$quotation->total_amount}' class='btn btn-primary text-white btn-sm'  target='_blank'>Generate Receipt</a>
                            </div>";
            } else {
                ?>
                <div class="col-sm-2">Receipt No:</div>
                <div class="col-sm-2"><input type="text" name='receipt_no' size="35" class="form-control" disabled value="<?= $quotation->receipt_no ?>" /></div>
                <div class="col-sm-2">
                    <a href="admin.php?page=stock-delivery-note&qid=<?= $quotation->id ?>" class="btn btn-success btn-sm text-white" target="_blank" />Delivery Note</a>
                </div>
                <?php
            }
        } else if ($quotation->type == 'Order') {
            if (empty($quotation->receipt_no)) {
                echo "<div class='col-sm-6'>
                                <a href='admin.php?page=receipt-create&qid={$quotation->id}&revised_no={$quotation->revised_no}&client=" . urlencode($client->name) . "&client_id={$quotation->client_id}&amount={$quotation->total_amount}' class='btn btn-primary text-white btn-sm' target='_blank'>Generate Receipt</a>
                            </div>";
            } else {
                echo "<div class='col-sm-2'>Receipt No:</div>
                      <div class='col-sm-2'>
                      <form method='post'><input type='hidden' name='id' value='{$quotation->id}'/>
                      <input type='text' name='receipt_no' class='form-control' readonly value='{$quotation->receipt_no}' />
                <button type='submit' name='confrim_order' value='1' class='btn btn-warning btn-sm' " . ($quotation->status == 'CONFIRMED' ? 'disabled' : '') . ">Confirm Order</button>
                </form></div>";
            }
        } else if ($quotation->type == 'Project') {

            $paid_amount = get_qtn_paid_amount($quotation->id);

            if (empty($quotation->receipt_no)) {
                echo "<div class='col-sm-2'>
                                <a href='admin.php?page=receipt-create&qid={$quotation->id}&revised_no={$quotation->revised_no}&client=" . urlencode($client->name) . "&client_id={$quotation->client_id}&amount={$quotation->total_amount}' class='btn btn-primary text-white btn-sm' target='_blank'>Generate Receipt</a>
                            </div>";
            } else {
                echo "<div class='col-sm-2'>Receipt No:</div>
                      <div class='col-sm-2'><input type='text' class='form-control' readonly value='{$quotation->receipt_no}' /></div>";
            }

            $msg = '';
            $disabled = 'disabled';
            if (has_role_super_and_admin()|| has_this_role('commercial')) {
                $disabled = '';
            } else if ($paid_amount != $quotation->total_amount) {
                $msg = "Need full payment before Confirm Order<br/>";
                $disabled = 'disabled';
            }

            echo "<div class='col-sm-2'>{$msg}"
            . "<form method='post'>"
            . "<input type='hidden' name='id' value='{$quotation->id}'/>"
            . "<input type='hidden' name='receipt_no' value='{$quotation->receipt_no}' />"
            . "<button type='submit' name='confrim_order' value='1' class='btn btn-warning btn-sm' " . ($quotation->status == 'CONFIRMED' ? 'disabled' : $disabled) . ">Confirm Order</button>"
            . "</form>"
            . "</div>";
            if ($quotation->status != 'Converted to PJO') {
                echo "<div class='col-sm-2'><a href='admin.php?page=quotation-view&id={$id}&action=PJO' class='btn btn-warning btn-sm' onclick='return confirm(`Are you sure you want to Convert to PJO?`)'>Convert To PJO</a></div>";
            }elseif($quotation->status == 'Converted to PJO'){
                echo "<div class='col-sm-2'><a href='admin.php?page=quotation-view&id={$id}&action=PJO' class='btn btn-danger btn-sm'>Update Converted PJO</a></div>";

            }
        }
        ?>
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
            jQuery('.chosen-select').chosen();
        });
        function openInNewTab(url) {
            var win = window.open(url, '_blank');
            win.focus();
        }
    </script>
    <?php
    $watermark = in_array($quotation->status, ['RESERVED', 'CONFIRMED', 'DELIVERED']) ? $quotation->status : '';

    generate_pdf($html, $pdf_file, $watermark);

    pdf_copy($pdf_file, get_option('qtn_copy_dir'));
}
