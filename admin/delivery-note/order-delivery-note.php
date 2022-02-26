<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_order_delivery_note_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('Y-m-d');

    $dn_id = !empty($getdata['dn_id']) ? $getdata['dn_id'] : 0;
    if (empty($dn_id)) {
        wp_redirect(admin_url('/admin.php?page=delivery-note-list'));
        exit();
    }

    if (!empty($postdata['delivery_note'])) {
        $sql = "UPDATE {$wpdb->prefix}ctm_quotation_dn SET address='{$postdata['address']}', receiver_name='{$postdata['receiver_name']}', delivered_by='{$postdata['delivered_by']}', delivery_date='{$postdata['delivery_date']}', delivery_time_from='{$postdata['delivery_time_from']}', delivery_time_to='{$postdata['delivery_time_to']}', updated_by='{$current_user->ID}', updated_at='{$date}' WHERE id='{$postdata['dn_id']}'";
        $wpdb->query($sql);
        update_dn_meta_location($postdata);
    }

    if (!empty($postdata['status']) && $postdata['status'] == 'DELIVERED') {
        $quotation_id = $wpdb->get_var("SELECT quotation_id FROM {$wpdb->prefix}ctm_quotation_dn where id='{$dn_id}'");
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_dn SET status='DELIVERED', updated_at='{$date}' WHERE id='{$dn_id}'");
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET status='DELIVERED', updated_at='{$date}' WHERE id='{$quotation_id}'");
        create_tax_invoice_order($quotation_id,$dn_id);
    }

    if (!empty($postdata['tax_invoice'])) {
        $quotation_id = $wpdb->get_var("SELECT quotation_id FROM {$wpdb->prefix}ctm_quotation_dn WHERE id='{$dn_id}'");
         create_tax_invoice_order($quotation_id,$dn_id);
    }
    
    if (!empty($postdata['status']) && $postdata['status'] == 'CANCELLED') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_dn SET status='{$postdata['status']}', updated_at='{$date}' WHERE id='{$dn_id}'");
    }

    if (!empty($postdata['delete_dn']) && $postdata['delete_dn'] == 'delete') {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quotation_dn WHERE id='{$dn_id}'");
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quotation_dn_meta WHERE dn_id='{$dn_id}'");
        wp_redirect(admin_url('/admin.php?page=delivery-note-list'));
        exit();
    }

    $dn = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn WHERE id='{$dn_id}'");
    if (!empty($dn_id)) {
        $dn_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn_meta where dn_id='{$dn_id}'");
        $client = get_client($dn->client_id);
        $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$dn->quotation_id}'");
        $qtn = get_revised_no($dn->quotation_id);
        $city = $quotation?get_location($quotation->city_id, 'city'):'';
    }
    $asset_url = get_template_directory_uri() . '/assets/images/';
    ?>
    <div class="wrap">
        <?php
        $html = "
                                    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr th,table tr td,p,b,span{font-size:12px;font-family:'Tahoma'}
                                        h6 span{text-transform:uppercase;}
                                        p{margin-bottom: 0;}
                                        table{empty-cells:hidden;}
                                        table tr td{font-size:12px;font-family:'Tahoma'}
                                        table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7),table tr td:nth-child(8),table tr td:nth-child(9){width: 55px;}
                                        .text-center{text-align:center;}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .attachment-large{width:50px;height: auto;}
                                        ul{margin: auto;padding: 0 15px;}
                                        ul#terms{ padding: 0; margin: 0;} 
                                        ul#terms li{ padding: 2px 0!important; margin: 0!important; text-align: left!important; width: 100%!important; font-size:12px; font-weight: bold;}
                                        table {line-height: 25px;}
                                    </style>
                                    <form action='' method=post>
                                    <div id='welcome-to-aquila' class='postbox'>
                                        <div class='inside' style='max-width:800px;margin:auto'>
                                            <br/>
                                            <table width='800'>
                                                <tr valign='top'>
                                                    <td colspan=2 style='text-align:right'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' style='text-align:center'><br/><br/>
                                                        <h6><span style='font-size:22px;'>DELIVERY NOTE</span></h6><br/><br/>
                                                    </td>
                                                </tr>
                                            </table>
                                             <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
                                      <tr class=bg-blue>
                                      <td colspan='5'><b>Customer Details</b></td><td class='text-center'><b>Delivery Note # </b></td><td class='text-center'><b>Date</b></td>
                                      </tr>
                                      <tr>
                                      <td><b>Name:</b></td><td colspan='4'><b>{$client->name}</b></td>"
                . "<td class='text-center'><b>{$dn_id}</b></td><td class='text-center'><b>" . rb_date($dn->delivery_date) . "</b></td>
                                      </tr>
                                      <tr>
                                      <td rowspan=2><b>Address</b></td><td colspan='4'><b>" . ($dn->address ? $dn->address : $client->address) . "</b></td>
                                          <td class='bg-blue text-center' ><b>Quotation #</b></td><td class='bg-blue text-center'><b>Destination</b></td>
                                      </tr>
                                      <tr>
                                        <td colspan='4'><b>{$client->city}, {$client->country}</b></td>"
                . "<td class='text-center'><b>{$qtn}</b></td>"
                . "<td class='text-center'><b>{$city}</b></td>
                                      </tr>
                                      <tr>
                                      <td><b>Phone</b></td><td colspan='4'><b>{$client->phone}</b></td>
                                          <td  class='bg-blue text-center' rowspan='2' colspan=2 ><b>Roche Bobois<br/>TRN# 100383178900003</b></td>
                                      </tr>

                                      <tr>
                                      <td><b>TRN</b></td><td colspan='4'><b>{$client->trn}</b></td>
                                      </tr>
                                      <tr><td colspan='7'><br/></td></tr>
                                      <tr class='text-center bg-blue'>
                                      <td class='text-center' colspan=2><b>ENTRY #</b></td>
                                      <td class='text-center'><b>ITEM DESCRIPTION</b></td>
                                      <td class='text-center'><b>IMAGE</b></td>
                                      <td class='text-center'><b>QTY</b></td>
                                      <td class='text-center'><b>EXPORT PACKING</b></td>
                                      <td class='text-center'><b>LOCATION</b></td>
                                      </tr>";

        $total = 0;
        $i = 0;
        foreach ($dn_items as $value) {
            $total += $value->quantity;
            $image_id = get_item($value->item_id, 'image');
            $reversed = $value->status=='REVERSED'?'background: #e2e0e0; cursor: not-allowed;':'';
            $tr = "<tr>"
                . "<td class='text-center' colspan=2>{$value->entry}</td>"
                . "<td>" . nl2br($value->item_desc) . "</td>"
                . "<td class='text-center'><img src='" . get_image_src($image_id) . "' width=50  style='margin: auto;width: 50px; '/></td>"
                . "<td class='text-center'>{$value->quantity}</td>"
                . "<td class='text-center'>"
                . (empty($getdata['pdf']) ? "<input type='checkbox' name='dn[$value->id][export_packing]' value='1' " . (!empty($value->export_packing) ? 'checked' : '') . " />" : '')
                . (!empty($getdata['pdf']) && !empty($value->export_packing) ? "<img width='20' src='{$asset_url}tick.png'>" : '') . "</td>"
                . "<td class='text-center'><input type=text name=dn[$value->id][location] value='{$value->location}' /></td>"
                . "</tr>";
            $html .= !empty($getdata['pdf'])?($value->status!='REVERSED'?$tr:''):$tr;
            $i++;
        }
        $html .= "<tr class='text-right font-weight-bold'>
                <td colspan=4 class='text-center'><b>TOTAL</b></td><td class='text-center'><b>{$total}</b></td><td></td><td></td></td>
                </tr>

                <tr><td colspan='7'><br/></td></tr>
                <tr><td colspan='7' class='text-center'><b>GOODS RECEIVED IN GOOD CONDITION</b></td></tr>
                <tr><td colspan=2><b>RECEIVER'S NAME</b></td><td  colspan=3><b>{$dn->receiver_name}</b></td>
                <td><b>DELIVERED&nbsp;BY</td><td width='150'><b>{$dn->delivered_by}</b></td></tr>
                <tr><td colspan=2 rowspan=2><b>RECEIVER'S SIGN</td><td  colspan=3 rowspan=2></td><td><b>DELIVERY&nbsp;DATE</b></td>
                <td><b>" . rb_date($dn->delivery_date) . "</b></td></tr>
                <tr><td><b>DELIVERY&nbsp;TIME</td>
                <td><b>" . rb_time($dn->delivery_time_from, 'h:i a') . " " . rb_time($dn->delivery_time_to, 'h:i a') . "</b></td></tr>
                </table>

              <br/><br/><br/><br/>


              </div>
              </div>";
        echo $html;
        $pdf_file = $dn->pdf_path;
        store_pdf_path('ctm_quotation_dn', $dn->id, "DELIVERY_NOTE_{$dn->id}_{$client->name}_{$qtn}.pdf", $pdf_file);
        include_once 'fields.php';
        ?>
        <br/>
        <br/>
    </form>

    <div class="row btn-bottom">
        <div class="col-sm-6 text-center">
            <?php
            if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
            } if (pdf_exist($pdf_file)) {
                echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
            }
            ?>
        </div>
        <div class="col-sm-6 text-center">
            <?php if (has_role_super_and_admin() && $dn->status != 'DELIVERED') { ?>
                <form method="post">
                    <button type="submit" name="delete_dn" value="delete"  onclick='return confirm(`are you sure you want to delete?`)'  
                            class="btn btn-danger btn-sm">Delete Delivery Note</button>
                </form>
            <?php } ?>
        </div>
    </div>

    </div>


    <?php
    generate_pdf($html, $pdf_file);

    pdf_copy($pdf_file, get_option('dn_copy_dir'));
}
