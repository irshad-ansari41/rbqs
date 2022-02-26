<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_quality_control_report_view_page() {

    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=quality-control-report'));
        exit();
    }

    if (!empty($postdata['status']) && $postdata['status'] == 'Proceed to Order') {
        $qcr = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quality_control_report WHERE id='{$id}'");
        $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE qcr_id='{$id}'");
        if (empty($exist)) {
            $data = ['qcr_id' => $qcr->id, 'client_id' => $qcr->client_id, 'po_id' => $qcr->po_id, 'confirmation_no' => $qcr->confirmation_no, 'item_id' => $qcr->item_id, 'entry' => $qcr->entry, 'item_desc' => $qcr->item_desc, 'quantity' => $qcr->quantity, 'order_registry' => 'ORDERED', 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
            $wpdb->insert("{$wpdb->prefix}ctm_quotation_po_meta", array_map('trim', $data), wpdb_data_format($data));
        } else {
            $data = ['client_id' => $qcr->client_id, 'po_id' => $qcr->po_id, 'confirmation_no' => $qcr->confirmation_no, 'item_id' => $qcr->item_id, 'entry' => $qcr->entry, 'item_desc' => $qcr->item_desc, 'quantity' => $qcr->quantity, 'order_registry' => 'ORDERED', 'updated_by' => $current_user->ID, 'updated_at' => $date];
            $wpdb->update("{$wpdb->prefix}ctm_quotation_po_meta", ['qcr_id' => $id], array_map('trim', $data), wpdb_data_format($data), ['%d']);
        }
    }

    if (!empty($postdata['status'])) {
        $data = ['status' => $postdata['status'], 'updated_by' => $current_user->ID, 'updated_at' => $date];
        $wpdb->update("{$wpdb->prefix}ctm_quality_control_report", array_map('trim', $data), ['id' => $id], ['%s', '%s', '%s',], ['%d']);
    }

    if (!empty($postdata['status'])) {
        
    }

    $qcr = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quality_control_report WHERE id='{$id}'");



    if ($qcr->status == 'Draft') {
        wp_redirect(admin_url('/admin.php?page=quality-control-report'));
        exit();
    }

    $item = get_item($qcr->item_id);
    $supplier_name = get_supplier($item->sup_code, 'name');
    $client = get_client($qcr->client_id);
    $asset_url = get_template_directory_uri() . '/assets/images/';
    ?>

    <div class='wrap'>
        <div id='dashboard-widgets-wrap'>
            <div id='dashboard-widgets' class='columns-1'>
                <div id='postbox-container' class='postbox-container'>
                    <div id='normal-sortables' class='meta-box-sortables ui-sortable'>


                        <?php
                        $html = "<style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
   
                         table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                         .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                         .text-black{color:black;-webkit-print-color-adjust: exact;}
                         .text-red{color:red;-webkit-print-color-adjust: exact;}
                         .bg-gray{background-color: rgba(0,0,0,.05);}
                         .bg-white{background-color: white;}
                         .bg-pink{background-color: #e8b0b6;border-color: #e8b0b6;}
                         table.tb-item tr td{text-align:center;}
                         </style>
                         <div id='page-inner-content' class='postbox'>
                        
                        <div class='inside' style='max-width:800px;margin:auto'>
                        <br/>";

                        $html .= "<table style='width: 800px;'>
                            <tr valign='top'>
                                <td style='vertical-align: bottom;'><b style='font-size:14px'>QCR#: 00{$qcr->id}</b></td>
                                <td style='text-align:right'>
                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                </td>
                            </tr>
                        </table>
                        <br/>
                            <h1 style='text-align:center;font-size:30px;font-weight: bold;text-transform:uppercase'>Quality Control Report</h1><br/><br/>
                            <table border='0' style='border-collapse: collapse;width: 100%' cellpadding=3 cellspacing=3 >
                                    <tr> 
                                        <td style='font-size:14px'>Client: {$client->name}</td>
                                        <td style='text-align:right;font-size:14px'>Supplier: {$supplier_name}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-size:14px'>PO# {$qcr->po_id} | Confirmation # {$qcr->confirmation_no}</td>
                                        <td style='text-align:right;font-size:14px'>Invoice Ref: {$qcr->invoice_ref} </td>
                                    </tr>
                                     <tr>
                                        <td style='font-size:14px'>Mobile: {$qcr->contact_no}</td>
                                        <td style='text-align:right;font-size:14px'>Notice Date: " . rb_date($qcr->notice_date) . " </td>
                                    </tr>
                            </table><br/>";

                        $html .= "<table class='tb-item' border='1' style='border-collapse: collapse;width:800px' cellpadding=5 cellspacing=5>
                                <tr valign='top' class='bg-gray'>
                                    <th colspan='10' style='text-align: center;'><b>ITEM DESCRIPTION</b></th>
                                </tr>
                                <tr valign='top' class='bg-white'>
                                    <th style='text-align: center;width:50px'><b>Entry&nbsp;No.</b></th>
                                    <th style='text-align: center;width:150px'><b>Supplier</b></th>
                                    <th style='text-align: center;width:150px' colspan=2><b>Complaint</b></th>
                                    <th style='text-align: center;width:150px' colspan=2><b>Description</b></th>
                                    <th style='text-align: center;'><b>Collection</b></th>
                                    <th style='text-align: center;'><b>Category</b></th>
                                    <th style='text-align: center'><b>QTY</b></th>
                                    <th style='text-align: center;width:120px'><b>CQUE</b></th>
                                </tr>";

                        $html .= "<tr valign='top'>
                                        <td>$qcr->entry</td>
                                        <td>{$supplier_name}</td>
                                        <td style='text-align:left' colspan=2 class='text-red'>" . nl2br($qcr->complaint) . "</td>
                                        <td style='text-align:left' colspan=2>" . nl2br($qcr->item_desc) . "</td>
                                        <td style='text-align:center'>{$item->collection_name}</td>
                                        <td style='text-align:center'>" . get_item_category($item->category, 'name') . "</td>
                                        <td style='text-align:center'>{$qcr->quantity}</td>
                                        <td style='text-align:center'>{$qcr->cque}</td>
                                    </tr>";

                        $html .= "<tr class='bg-white'><td colspan='10'><br/></td></tr>";

                        $html .= "<tr class='bg-gray'><th colspan=10 style='text-align:center'><b>REASON OF REPORT</b></th></tr>";

                        $html .= "<tr class='bg-white' style='text-align:center' valign=top>";
                        foreach (QCR_REASON as $value) {
                            $html .= "<td " . ($value == 'Other' ? 'colspan=2' : '') . "><b>$value</b></td>";
                        }
                        $html .= "</tr>";

                        $html .= "<tr class='bg-white' style='text-align:center' valign=top>";
                        foreach (QCR_REASON as $value) {
                            $cehcked = $value == $qcr->reason ? "<img width='20' src='{$asset_url}tick.png'>" : '';
                            $html .= "<td " . ($value == 'Other' ? 'colspan=2' : '') . ">$cehcked</td>";
                        }
                        $html .= "</tr>";

                        $html .= "<tr class='bg-white'><td colspan='10'><br/></td></tr>";

                        $html .= "<tr class='bg-gray'><th colspan='10' style='text-align:center'><b>SOLUTION</b></th></tr>";

                        $html .= "<tr class='bg-white' style='text-align:center' valign=top>";
                        foreach (QCR_SOLUTION as $value) {
                            $html .= "<td " . ($value == 'Parts To Be Sent' ? 'colspan=2' : ($value == 'Other' ? 'colspan=2' : '')) . "><b>$value</b></td>";
                        }
                        $html .= "</tr>";

                        $html .= "<tr class='bg-white' style='text-align:center' valign=top>";
                        foreach (QCR_SOLUTION as $value) {
                            $cehcked = $value == $qcr->solution ? "<img width='20' src='{$asset_url}tick.png'>" : '';
                            $html .= "<td " . ($value == 'Parts To Be Sent' ? 'colspan=2' : ($value == 'Other' ? 'colspan=2' : '')) . ">$cehcked</td>";
                        }
                        $html .= "</tr>";

                        $html .= "<tr class='bg-white'><td colspan='10'><br/></td></tr>";

                        $html .= "<tr class='bg-gray'><th colspan='10' style='text-align:center'><b>ACTION</b></th></tr>
                                <tr class='bg-white'>
                                    <td colspan='10' style='text-align:left'><p style='font-size:18px;text-align:center'>" . nl2br($qcr->action) . "</p></td>
                                </tr>";

                        $html .= "</table>";

                        $html .= "<br/><table border='0' style='border-collapse: collapse;width: 100%' cellpadding=3 cellspacing=3 >
                                    <tr> 
                                        <td style='font-size:14px'>Reported By: {$qcr->reported_by}</td>
                                        <td style='text-align:right;font-size:14px'>Prepared By: " . $qcr->prepared_by . " </td>
                                    </tr>
                                     <tr>
                                        <td style='font-size:14px'>Date: " . rb_date($qcr->qcr_date) . "</td>
                                        <td style='text-align:right;font-size:14px'></td>
                                    </tr>
                            </table>";

                        $html .= "<br/><br/><br/><br/></div></div>";

                        echo $html;

                        $pdf_file = $qcr->pdf_path;
                        store_pdf_path('ctm_quality_control_report', $qcr->id, "Quality_Control_Report_{$qcr->id}.pdf", $pdf_file);
                        ?>


                        <div class='row btn-bottom'>
                            <div class='col-sm-6 text-center'>
                                <a href = 'admin.php?page=quality-control-report' class='btn btn-dark btn-sm text-white'>Back</a>&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                                }
                                ?>
                            </div>
                            <div class='col-sm-6 text-center'>
                                <form method="post">
                                    <button type='submit' name='status' value="Resolved" class='btn btn-dark  btn-sm text-white'
                                            <?= $qcr->status == 'Resolved' || $qcr->status == 'Proceed to Order' ? 'disabled' : '' ?>>Resolved</button>
                                    <button type='submit' name='status' value="Proceed to Order" class='btn btn-primary btn-sm text-white' 
                                            <?= $qcr->status == 'Proceed to Order' ? 'disabled' : '' ?>>Proceed to Order</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div><!--dashboard-widgets-wrap -->
    <?php
    generate_pdf($html, $pdf_file);

    pdf_copy($pdf_file, get_option('pjo_copy_dir'));
}
