<?php

function admin_ctm_pre_delivery_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=stock-transfer'));
        exit();
    }

    $pdi = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_pre_delivery where id='$id'");
    $pdi_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_pre_delivery_meta where pdi_id='$id'");
    $client_name = get_client($pdi->client_id, 'name');
    ?>

    <div class="wrap">
        <?php
        $html = "<style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
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
                        </style>
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
                                            <h6><span style='font-size:22px;'>PRE DELIVERY QUALITY CONTROL</span></h6><br/><br/>
                                        </td>
                                    </tr>
                                </table>
                                 <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
                          <tr class=bg-blue>
                          <td colspan='4'><b>Customer Details</b></td><td class='text-center'><b>Date</b></td>
                          </tr>
                          <tr>
                          <td><b>Name:</b></td><td colspan='3' class='text-center'><b>{$client_name}</b></td><td class='text-center'><b>" . rb_date($pdi->pdi_date, 'd.m.Y') . "</b></td>
                          </tr>

                          <tr><td colspan='5'><br/></td></tr>

                          <tr class='text-center bg-blue'>
                          <td class='text-center'><b>SUPPLIER CODE</b></td><td class='text-center'><b>ENTRY #</b></td>
                          <td class='text-center'><b>ITEM DESCRIPTION</b></td><td class='text-center'><b>QTY</b></td><td class='text-center'><b>DAMAGED OR GOOD CONDITION</b></td>
                          </tr>";

        $total = 0;
        foreach ($pdi_meta as $value) {
            $po_meta = get_po_meta_data($value->po_meta_id);
            $total += $value->quantity;
            $html .= "<tr><td class='text-center'>{$po_meta->sup_code}</td>"
                    . "<td class='text-center'>{$po_meta->entry}</td>"
                    . "<td class='text-center'>" . nl2br($po_meta->item_desc) . "</td>"
                    . "<td class='text-center'>{$value->quantity}</td><td class='text-center'></td></tr>";
        }
        $html .= "<tr class='text-right font-weight-bold'>
                                    <td colspan=3 class='text-center'><b>TOTAL</b></td><td class='text-center'><b>{$total}</b></td><td></td></td>
                                    </tr>

                                    <tr><td colspan='5'><br/></td></tr>
                                    <tr><td colspan='5' class='text-center'><b>CONFIRM ABOVE MENTIONED ITEMS HAVE BEEN INSPECTED</b></td></tr>
                                    <tr><td colspan=2><b>INSPECTED BY</b></td><td><b></b></td><td><b>REQUESTED&nbsp;BY</td><td width='150'><b></b></td></tr>
                                    <tr><td colspan=2 rowspan=2><b>INSPECTION SUPERVISOR'S SIGNATURE WITH DATE</td><td rowspan=2></td><td><b>REQUEST&nbsp;DATE</b></td>
                                    <td><b>" . rb_date($pdi->pdi_date) . "</b></td></tr>
                                    <tr><td><b>SIGNATURE</td><td><b></b></td></tr>
                                    </table>
                                  <br/><br/><br/><br/>
                                  </div>
                                  </div>";
        echo $html;

        $pdf_file = $pdi->pdf_path;
        store_pdf_path('ctm_pre_delivery', $pdi->id, "pre_delivery_inspect_{$pdi->id}.pdf", $pdf_file);
        ?>



        <div class="row btn-bottom">
            <div class="col-sm-12 text-center">
                <?php
                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                } if (pdf_exist($pdf_file)) {
                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                }
                ?>

            </div><!--dashboard-widgets-wrap -->
        </div>
    </div>
    <?php
    generate_pdf($html, $pdf_file);
}
