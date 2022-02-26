<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'send-po-email.php';

function admin_ctm_purchase_order_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=purchase-order'));
        exit();
    }

    if (!empty($postdata['send_email'])) {
        $email_status = rb_send_email($postdata);
        $msg = !empty($email_status) ? "<strong>Success!</strong> Email has been sent successfully." : 0;
    }
    
    if (!empty($getdata['action']) && $getdata['action'] == 'approve') {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po SET is_approved='Approved' WHERE id='{$id}'");
        wp_redirect(admin_url("admin.php?page=purchase-order-view&id=$id"));
    }

    if (!empty($postdata['upload'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po SET promo_image='{$postdata['image']}' WHERE id='{$id}'");
    }
    
   

    $po = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po WHERE id='{$id}'");
    $po_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE po_id='{$id}'");
    $supplier = get_supplier($po->sup_code);
    $client = get_client($po->client_id);
    $client_name = $client->name;
    $qtn = get_revised_no($po->quotation_id);
     make_model_send_po_email($po, $client);
    ?>

    <div class='wrap'>
        <div id='dashboard-widgets-wrap'>
            <div id='dashboard-widgets' class='columns-1'>
                <div id='postbox-container' class='postbox-container'>
                    <div id='normal-sortables' class='meta-box-sortables ui-sortable'>
                        <div id='page-inner-content' class='postbox'>
                            <div class='inside' style='max-width:800px;margin:auto'>
                                <br/>
                                <?php
                                $html = "<style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time],
                            #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], 
                            #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], 
                            #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, 
                            #page-inner-content textarea { width: 100%; }
                         table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                         .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                         </style>
                         
                        
                        <table style='width: 800px;'>
                            <tr valign='top'>
                                <td>";
                                if (empty($getdata['pdf'])) {
                                    $html .= "<form method='post'>
                                            <input id='item_image' class='button-primary' type='button' value='Add Item Image' /><br/><br/>
                                            <output id='item-image'>
                                            <img src='" . get_image_src($po->promo_image) . "' width=150  style='margin: auto;width: 150px; '>
                                            " . (!empty($po->promo_image) ? "<input type='hidden' name='image' value='$po->promo_image'/>" : "" ) . "</output>"
                                            . "<br/><br/><button type='submit' name=upload value=upload class='btn btn-primary btn-sm'>Upload</button></form>";
                                } else {
                                    $html .= "<img src='" . get_image_src($po->promo_image) . "' width=150  style='margin: auto;width: 150px; '>";
                                }


                                $html .= "</td>
                                <td style='text-align:right'>
                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                </td>
                            </tr>
                        </table>
                            <h1 style='text-align:center;font-size:22px;font-weight: bold;'>PURCHASE ORDER</h1>
                            <table>
                                <tr>
                                    <td style='width: 380px;padding: 0' >
                                        <table style='width: 380px' cellpadding=3 cellspacing=3>
                                            <tr>
                                                <td style='width: 105px;'><b>ORDER NO:&nbsp;&nbsp;&nbsp;&nbsp;</b><span class='text-red'>{$po->id}</span></td>
                                            </tr>
                                        </table>
                                        <table border='1' style='border-collapse: collapse;width: 100%' cellpadding=3 cellspacing=3 >
                                            <tr>
                                                <td rowspan='6' style='width: 105px;' class='bg-blue'><b>SUPPLIER</b></td>
                                                <td>{$supplier->name}</td>
                                            </tr>
                                            <tr> <td>{$supplier->address} </td> </tr>
                                            <tr> <td>Tel: {$supplier->phone}</td> </tr>
                                            <tr> <td>Fax: {$supplier->fax}</td> </tr>
                                            <tr> <td>EMAIL: {$supplier->email}</td> </tr>
                                            <tr> <td>ATTENTION: {$supplier->contact_person}</td> </tr>
                                        </table>
                                        <br/>
                                        <table border='1' style='border-collapse: collapse;width: 100%' cellpadding=3 cellspacing=3>
                                            <tr>
                                                <td style='width: 105px;'><b>REFERENCE</b></td>
                                                <td>CQUE: {$client_name} " . ($qtn) . "</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style='width:40px;padding: 0'><div style='width:40px'></div></td>
                                    <td style='vertical-align:top;padding: 0;width: 380px '>
                                        <table style='width: 380px' cellpadding=3 cellspacing=3>
                                            <tr>
                                                <td style='text-align:right'><b>DATE:&nbsp;&nbsp;&nbsp;&nbsp;</b>" . rb_date($po->created_at) . "</td>
                                            </tr>
                                        </table>
                                        <table border='1' style='border-collapse: collapse;width: 100%;text-align: center' cellpadding=3 cellspacing=3>
                                            <tr class='bg-blue'>
                                                <th style='width:50%'><b>DISPATCH THRU</b></th>
                                                <th><b>DESTINATION</b></th>
                                            </tr>
                                            <tr>
                                                <td>FRANCESCO FRANCESCONI</td>
                                                <td>DUBAI</td>
                                            </tr>
                                        </table>
                                        <br/>
                                        <table border='1' style='border-collapse: collapse;text-align: center;width: 100%' cellpadding=3 cellspacing=3>
                                            <tr class='bg-blue'>
                                                <th>IMPORT DOCUMENTS REQUIREMENT</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div style='height:100px'>
                                                        <p>
                                                            ORIGINAL INVOICE with HS CODE PER ITEM<br/>
                                                            ORIGINAL PACKING LIST 
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br/>";


                                $html .= " <table border='1' style='border-collapse: collapse;width:800px' cellpadding=3 cellspacing=3>
                                <tr valign='middle' class='bg-blue'>
                                    <th style='text-align: center;width: 105px'><b>QTY</b></th>
                                    <th style='text-align: center'><b>ITEM DESCRIPTION</b></th>
                                </tr>";


                                foreach ($po_meta as $value) {

                                    $html .= "<tr>
                                        <td style='padding: 40px;'>{$value->quantity}</td>
                                        <td style='padding-right: 300px;'>" . nl2br($value->item_desc) . "</td>
                                    </tr>";
                                }
                                $html .= " </table>";
                                $checked1 = $checked2 = $checked3 = '';
                                if ($value->order_registry == 'ORDERED') {
                                    $checked1 = 'checked="checked"';
                                }
                                if ($value->order_registry == 'CONFIRMED') {
                                    $checked1 = $checked2 = 'checked="checked"';
                                }
                                if ($value->order_registry == 'DELIVERED TO FF') {
                                    $checked1 = $checked2 = $checked3 = 'checked="checked"';
                                }

                                $html .= " <p style='padding: 15px 15px 0;font-weight: bold'>DELIVERY INSTRUCTION: All goods to be delivered in export packing labeled with CQUE & Item Name. Please
                                give your best Net Price after special discount and confirm the shipment date by sending the Proforma
                                Invoice indicating soonest dispatch date with CBM/Weight .</p><br/>";

                                if ($po->is_approved == 'Approved') {
                                    $asset_url = get_template_directory_uri() . '/assets/images/';
                                    $html .= "<img src='{$asset_url}signature.jpg' style='width:350px' />";
                                } else {
                                    $html .= "<table style='border-collapse: collapse;width:800px' cellpadding=3 cellspacing=3><tr>"
                                            . "<td style='vertical-align:top'><p style='padding: 15px;font-weight: bold'> Thanks & best regards,<br/> Ahmed HASSANI </p></td>"
                                            . "</tr></table>";
                                }
                                $html .= "<br/><br/>
                            ";
                                echo $html;
                                $pdf_file = $po->pdf_path;
                                store_pdf_path('ctm_quotation_po', $po->id, "Order_PO_{$po->id}_{$supplier->name}_{$client_name}_{$qtn}.pdf", $pdf_file);
                                ?>
                            </div>
                        </div>
                        <div class='row btn-bottom'>
                            <div class='col-sm-6 text-center'>
                                <a href = 'admin.php?page=purchase-order-items&id=<?= $id ?>&action=view' class='btn btn-dark btn-sm text-white'>Back</a>&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                                    echo ' <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myEmailModal" data-keyboard="false">Send Email</button>&nbsp;&nbsp;';
                                }
                                ?>
                            </div>
                            <?php if ($po->is_approved != 'Approved') { ?>
                                <div class='col-sm-6 text-center'>
                                    <a href = 'admin.php?page=purchase-order-view&id=<?= $id ?>&action=approve' 
                                       onclick='return confirm(`Are you sure you want to Approve?`)' class='btn btn-primary btn-sm text-white'>Approve</a>&nbsp;
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div><!--dashboard-widgets-wrap -->
    <script>
        jQuery('#add-new-item').click(() => {
            jQuery('#add-new-item-form').toggleClass('hide');
            jQuery('#page-inner-content').toggleClass('hide');
        });

        jQuery('#item_image').click(function (e) {
            file_uploader(e, 'image', 'item-image', false);
        });

        function file_uploader(e, input, output, multiple) {
            var custom_uploader;
            e.preventDefault();
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: multiple
            });
            custom_uploader.on('select', function () {
                var selection = custom_uploader.state().get('selection');
                var attachment_ids = selection.map(function (attachment) {
                    attachment = attachment.toJSON();
                    if (multiple == false) {
                        jQuery('#' + output).html('');
                    }
                    jQuery('#' + output).append("<input type='hidden' name='" + input + "' value='" + attachment.id + "'><img src='" + attachment.url + "' style='width:250px'>");
                }).join();
            });
            custom_uploader.open();
        }
    </script>
    <?php
    generate_pdf($html, $pdf_file);

    pdf_copy($pdf_file, get_option('po_copy_dir'));
}
