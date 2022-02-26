<?php

function admin_ctm_tax_invoice_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;

    if (!empty($postdata['action'])) {
        create_tax_credit_note($id, $postdata);
        wp_redirect(admin_url('/admin.php?page=tax-credit-note&msg=1'));
        exit();
    }


    $tax_invoice = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_tax_invoice where id='{$id}'");

    if (empty($tax_invoice)) {
        wp_redirect(admin_url('/admin.php?page=tax-invoice'));
        exit();
    }

    $quotation = get_quotation($tax_invoice->quotation_id);

    if (empty($tax_invoice)) {
        wp_redirect(admin_url('/admin.php?page=tax-invoice'));
        exit();
    }
    $quotation_meta = [];
    foreach (explode(',', $tax_invoice->meta_ids) as $id) {
        $quotation_meta[] = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where id IN ({$id}) ORDER BY id ASC");
    }
    //$quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where id IN ({$tax_invoice->meta_ids}) ORDER BY id ASC");

    $client = get_client($quotation->client_id);

    $is_diss = !empty(rb_float($quotation_meta[0]->discount)) ? true : (rb_float($quotation->special_discount) ? true : false);

    if ($quotation->promo_type == 'Export') {
        $quotation->vat = 'wovat';
    }

    $qtn = get_revised_no($tax_invoice->quotation_id);
    ?><br/>
    <form method="post">
        <div id='welcome-to-aquila' class='postbox'>
            <div class='inside' style='max-width:825px;margin:auto'>
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
                                <h6><span style='font-size:22px;text-transform: uppercase;'>Tax Invoice</span></h6>
                            </td>
                        </tr>
                    </table>
                    <br/>
                     <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
              <tr class=bg-blue>
              <td colspan='3'><b>Customer Details</b></td><td colspan='3' class='text-center'><b>Tax Invoice # </b></td><td colspan='3'  class='text-center'><b>Date</b></td>
              </tr>
              <tr>
              <td><b>Name:</b></td><td colspan='2'><b>{$client->name}</b></td>"
                        . "<td colspan='3' class='text-center'><b>{$tax_invoice->series}-{$tax_invoice->id}</b></td>"
                        . "<td colspan='3' class='text-center'><b>" . rb_datetime($tax_invoice->created_at) . "</b></td>
              </tr>
              <tr>
              <td rowspan=2><b>Address</b></td><td colspan='2' ><b>{$client->address}</b></td>
                  <td colspan='3' class='bg-blue text-center' ><b>Quotation #</b></td>
                  <td colspan='3' class='bg-blue text-center'><b>Destination</b></td>
              </tr>
              <tr>
              <td colspan='2'><b>{$client->city}, {$client->country}</b></td><td colspan='3' class='text-center'><b>{$qtn}</b></td>"
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
                <td rowspan=2 class='text-center' style='width:100px'><b>" . ( $quotation->type == 'Stock' ? 'Entry#' : 'SUP') . "</b></td>
                <td rowspan=2 class='text-center'><b>Item Description</b></td>
                <td rowspan=2 class='text-center' width=50><b>Image</b></td>
                <td rowspan=2 class='text-center'><b>qty</b></td>
                <td class='text-center'><b>Unit Price<br/>(Incl.&nbsp;VAT)</b></td>
                <td class='text-center'><b>Net Price<br/>" . ($is_diss ? '(AFTER DISCOUNT)' : '' ) . "</b></td>
                <td class='text-center'><b>Price<br/>(EXCLUDING VAT)</b></td>
                <td class='text-center'><b>VAT<br/>@" . ($quotation->vat == 'wovat' ? 0 : 5) . "%</b></td>
                <td class='text-center'><b>Total</b></td>
              </tr>
              <tr class='text-center bg-blue'>
              <td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td>
              </tr>";

                $total_net_price = $total_vat = $total_incl_amt = $total_quantity = 0;
                foreach ($quotation_meta as $value) {
                    $item = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items where id='{$value->item_id}'");

                    $total_net_price += $value->net_price;
                    $total_vat += $value->vat;
                    $total_incl_amt += $value->total_incl_vat;
                    $total_quantity += $value->quantity;

                    $entry = $quotation->type == 'Stock' ? $value->entry : $value->sup_code;

                    if (empty($tax_invoice->pjo_id)) {
                        $po_meta_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_po_meta where id IN ({$tax_invoice->po_meta_ids}) AND entry='$entry'");
                        $checkbox = "<label><input type='checkbox' name='meta_ids[$value->id]' value='$po_meta_id'  />$entry</label>";
                    } else {
                        $checkbox = "<label>$entry</label>";
                    }
                    $html .= "<tr>"
                            . "<td class='fnt11 text-center' >" . (empty($getdata['pdf']) ? $checkbox : $entry) . "</td>"
                            . "<td class=fnt11 >" . nl2br($value->item_desc) . "</td>"
                            . "<td class=fnt11 ><img src='" . get_image_src($item->image) . "' width=50  style='margin: auto;width: 50px; '></td>"
                            . "<td class='fnt11 text-center' >{$value->quantity}</td>"
                            . "<td class='fnt11 text-center' > " . number_format($value->price_incl_vat, 2) . "</td>"
                            . "<td class='fnt11 text-center' >" . number_format($value->net_price + $value->vat, 2) . "</td>"
                            . "<td class='fnt11 text-center' >" . number_format($value->net_price, 2) . "</td>"
                            . "<td class='fnt11 text-center' >" . number_format($value->vat, 2) . "</td>"
                            . "<td class='fnt11 text-center' >" . number_format($value->total_incl_vat, 2) . "</td>"
                            . "</tr>";
                }

                $total_cost = $total_incl_amt + $quotation->freight_charge;

                $html .= "<tr class='text-left'>
                <td colspan=3><b>SUB TOTAL</b></td><td class='text-center'>{$total_quantity}</td><td></td><td></td><td class='text-center'><b>" . number_format($total_net_price, 2) . "</b></td>"
                        . "<td class='text-center'><b>" . number_format($total_vat, 2) . "</b></td><td class='text-center'><b>" . number_format($total_incl_amt, 2) . "</b></td>
                </tr>
                <tr class='text-left font-weight-bold'>
                <td colspan=8><b>DELIVERY CHARGE</b></td><td class='text-center'><b>" . number_format($quotation->freight_charge, 2) . "</b></td>
                </tr>
                <tr class='text-left font-weight-bold'>
                <td colspan=8><b>TOTAL COST</b></td><td class='text-center'><b>" . number_format($total_cost, 2) . "</b></td>
                </tr>";

                $html .= "<tr>
                <td colspan=9><b>AED:<b> <span id='word'>" . num_to_words($total_cost) . "</span></td>
                </tr>
                </table>
                <br/>
              
                <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
                    <tr class=bg-blue>
                        <td style='text-align:center'><b>BANK DETAILS</b></td>
                    </tr>
                    <tr>
                        <td style='text-align:center'>
                            <b>Beneficiary Name: Roche Bobois | Bank Name: Emirates NBD<br/>							
                            Bank Address: Al Maktoum Road Branch, P.O. Box: 52088, Dubai - U.A.E<br/>							
                            Bank Account No.: 101 102 112 17 01 | IBAN No.: AE55026000 101 102 112 17 01 | Swift Code: EBILAEAD	<br/>						
                            Currency: AED</b>							
                        </td>
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
              <br/><br/><br/><br/>";
                echo $html;
                $pdf_file = $tax_invoice->pdf_path;
                store_pdf_path('ctm_tax_invoice', $tax_invoice->id, "TAX_INVOICE_{$tax_invoice->id}.pdf", $pdf_file);
                ?>
            </div>
        </div>   
        <br/>
        <div class="row btn-bottom">
            <div class="col-sm-4 text-center">
                <a href="?page=tax-invoice"  class="button-secondary" >Back</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                } if (pdf_exist($pdf_file)) {
                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a> &nbsp;&nbsp;&nbsp;&nbsp;";
                }
                ?>
            </div>
            <?php if (empty($tax_invoice->pjo_id)) { ?>
                <div class="col-sm-8 text-center">
                    <?php $tax_credit_note = check_tax_credit_note($id); ?>

                    <label>
                        <input type="radio" name='cn_type' value="CN" required
                        <?= !empty($tax_credit_note->cn_type) ? ($tax_credit_note->cn_type == 'CN' ? 'checked disabled' : 'disabled') : '' ?>
                               >Without Goods
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        <input type="radio" name='cn_type' value="GCN" required 
                        <?= !empty($tax_credit_note->cn_type) ? ($tax_credit_note->cn_type == 'GCN' ? 'checked disabled' : 'disabled') : '' ?>
                               >With Goods
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    if (empty($tax_credit_note)) {
                        ?>
                        <button type="submit" class="btn btn-primary btn-sm" name="action" value="create"
                                onclick='return confirm(`Are you sure you want to create tax credit note?`)'>Create Tax Credit Note</button>
                                <?php
                            } else {
                                echo "<a href='" . admin_url() . "admin.php?page=tax-credit-note-view&id={$tax_credit_note->id}' class=' btn-dark btn-sm' title='View' >View Tax Credit Note </a>";
                            }
                            ?>
                </div>
            <?php } ?>
        </div>
    </form>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('tax_invoice_copy_dir'));
}
