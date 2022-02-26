<?php

function admin_ctm_proforma_invoice_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=quotation'));
        exit();
    }

    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where  id='{$id}'");

    if ($quotation->status == 'Draft') {
        wp_redirect(admin_url('/admin.php?page=proforma-invoice'));
        exit();
    }

    $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$quotation->id}' ORDER BY id ASC");
    $client = get_client($quotation->client_id);

    $is_diss = get_discount($quotation->city_id, $quotation->type, $quotation->scope, $quotation->vat, $quotation->promo_type);
    $freight = get_freight_charge($quotation->city_id, $quotation->type, $quotation->scope, $quotation->vat, $quotation->promo_type);

    if ($quotation->promo_type == 'Export') {
        $quotation->vat = 'wovat';
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
                    <br/>";
    $table = "<table width='800'>
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
                                <h6><span style='font-size:22px;text-transform: uppercase;'>Proforma Invoice</span></h6>
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
            . "<td colspan='3' class='text-center'><b>" . rb_datetime($quotation->updated_at) . "</b></td>
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
                <td rowspan=2 class='text-center' width=50><b>Image</b></td>
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
        $image_src = get_image_src($item->image);
        $diss_price = ($value->price_incl_vat * $value->quantity * $is_diss) / 100;
        $net_price = ($value->price_incl_vat * $value->quantity) - $diss_price;
        $table .= "<tr>"
                . "<td class='fnt11 text-center' >" . ( $quotation->type == 'Stock' ? $value->entry : $value->sup_code) . "</td>"
                . "<td class=fnt11 >" . nl2br($value->item_desc) . "</td>"
                . "<td class=fnt11 ><img src='{$image_src}' width=50 style='margin: auto;width: 50px;'></td>"
                . "<td class='fnt11 text-center' >{$value->quantity}</td>"
                . "<td class='fnt11 text-center' > " . number_format($value->price_incl_vat, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($net_price, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($value->net_price, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($value->vat, 2) . "</td>"
                . "<td class='fnt11 text-center' >" . number_format($value->total_incl_vat, 2) . "</td>"
                . "</tr>";
    }

    $freight_charge = ($total_net_price * $freight) / 100;
    $total_cost = $total_incl_amt + $freight_charge;

    $table .= "<tr class='text-left font-weight-bold'>
                <td colspan=6><b>SUB TOTAL</b></td><td><b>" . number_format($total_net_price, 2) . "</b></td>"
            . "<td><b>" . number_format($total_vat, 2) . "</b></td><td><b>" . number_format($total_incl_amt, 2) . "</b></td>
                </tr>
                <tr class='text-left font-weight-bold'>
                <td colspan=8><b>DELIVERY CHARGE</b></td><td><b>" . number_format($freight_charge, 2) . "</b></td>
                </tr>
                <tr class='text-left font-weight-bold'>
                <td colspan=8><b>TOTAL COST</b></td><td><b>" . number_format($total_cost, 2) . "</b></td>
                </tr>";

    $table .= "<tr>
                <td colspan=9><b>AED:<b> <span id='word'>{$quotation->word}</span></td>
                </tr>
                </table>";

    if ($quotation->type == 'Order') {
        $table .= "<br/><br/>
                <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
                    <tr class=bg-blue>
                        <td style='text-align:center' colspan=2><b>PAYMENT TERMS</b></td>
                    </tr>
                    <tr>
                        <td><b>50% ADVANCE<b></td>
                        <td class='text-right'><b>" . number_format($total_cost / 2, 2) . "<b></td>
                    </tr>
                    <tr>
                        <td><b>BALANCE TO BE SETTLED BEFORE DELIVERY OF GOODS</b></td>
                        <td class='text-right'><b>" . number_format($total_cost / 2, 2) . "<b></td>
                    </tr>
                </table>";
    }

    $table .= "<br/><br/>
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
                <table width='800'>
                  <tr>
                      <td><b>For Roche Bobois</b></td>
                      <td style='text-align:right'><b>Signature of the customer</b></td>
                  </tr>
                </table>";
    $html .= $table;
    $html .= "<br/><br/><br/><br/>
              </div>
              </div>";
    echo $html;
    $pdf_file = make_pdf_file_name("PROFORMA_INVOICE_QTN_{$qtn}_{$client->name}_({$quotation->type}).pdf")['path'];
    ?>
    <div class="row btn-bottom">
        <div class="col-sm-12 text-center">
            <?php
            if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
            } if (pdf_exist($pdf_file)) {
                echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a> &nbsp;&nbsp;&nbsp;&nbsp;";
            }
            ?>
            <a href = '<?= export_excel_report($pdf_file, 'PROFORMA_INVOIC', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
        </div>
    </div>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('performa_invoice_copy_dir'));
}
?>