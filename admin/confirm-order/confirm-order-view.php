<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_confirm_order_view_page() {
    global $wpdb;

    $getdata = filter_input_array(INPUT_GET);
    $co_id = !empty($getdata['co_id']) ? $getdata['co_id'] : 0;

    if (empty($co_id)) {
        wp_redirect(admin_url('/admin.php?page=confirm-order'));
        exit();
    }

    $co = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_co where id='{$co_id}' ORDER BY id ASC ");
    $co_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_co_meta where co_id='{$co_id}' ORDER BY sup_code ASC ");

    $sales_person = get_qtn_sales_person($co->quotation_id);

    $client = get_client($co->client_id);
    $qtn = get_revised_no($co->quotation_id);

    $text1 = change_color_text(['SOFA', ' LOUNGE ARM CHAIR', ' CHAIR', ' DINING TABLE', ' SIDEBOARD', ' BOOKSHELF', ' CENTRE TABLE', ' TV STAND', ' CONSOLE', ' BAR',]);
    $text2 = change_color_text(['BAR STOOL', ' SIDE TABLE', ' WALL UNIT', ' OUTDOOR', ' BED', ' SLAT', ' MATTRESS', ' DRESSER & BST', ' ARTWORK', ' POUF', ' CUSHION',]);
    $text3 = change_color_text(['DESK', ' CLEANING KIT', ' LIGHTING', ' ACCESSORIES', ' RUG', ' MIRROR', ' WARDROBE', ' CHILDREN BEDROOM', ' CHAISELOUNGE', ' BENCH',]);
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
                         </style>
                         <div id='page-inner-content' class='postbox'>
                        
                        <div class='inside' style='max-width:800px;margin:auto'>
                        <br/>";

                        $html2 = " <table border='1' style='border-collapse: collapse;width:800px' cellpadding=3 cellspacing=3>
                                <tr valign='middle' class='bg-blue'>
                                    <th style='text-align: center;width:50px'><b>SUP</b></th>
                                    <th style='text-align: center;width:300px'><b>Item Description</b></th>
                                    <th style='text-align: center;'><b>QTY</b></th>
                                    <th style='text-align: center'><b>LOP & DATE</b></th>
                                    <th style='text-align: center'><b>Reserved<br/>from Stock /<br/> Entry #</b></th>
                                    <th style='text-align: center'><b>PO # & Date</b></th>
                                </tr>";


                        foreach ($co_meta as $value) {
                            $html2 .= "<tr>
                                        <td style='text-align:center'>{$value->sup_code}</td>
                                        <td>" . nl2br($value->item_desc) . "</td>
                                        <td style='text-align:center'>{$value->quantity}</td>
                                        <td style='text-align:center'></td>
                                        <td style='text-align:center'>" . $value->entry . "</td>
                                        <td style='text-align:center'>" . $value->po_id . '<br/>' . rb_date($value->po_date) . "</td>
                                    </tr>";
                        }
                        $html2 .= " </table>";

                        $html1 = "<table style='width: 800px;'>
                            <tr valign='top'>
                                <td style='text-align:right'>
                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                </td>
                            </tr>
                        </table>
                        <br/>
                            <h1 style='text-align:center;font-size:30px;font-weight: bold;'>CONFIRM ORDER PROCUREMENT</h1><br/><br/>
                            <table style='width: 800px;'>
                                <tr>
                                    <td style='width: 100%;' >
                                        <table border='0' style='border-collapse: collapse;width: 100%' cellpadding=3 cellspacing=3 >
                                            <tr> <td style='width:400px'>Customer: {$client->name} </td> "
                                . "<td style='text-align:right'>QTN: {$qtn}</td></tr>
                                            <tr> <td>Mobile: {$client->phone}</td> <td  style='text-align:right'>Date: " . rb_date($co->updated_at) . " </td></tr>
                                            <tr> <td>Email: {$client->email}</td> <td  style='text-align:right'>SP:  {$sales_person} </td></tr>
                                        </table>
                                        <br/>
                                    </td>
                                </tr>
                            </table>
                            
                            <table style='width: 800px;'>
                                <tr>
                                    <td style='width: 100%;' >
                                        <table border='1' style='border-collapse: collapse;width: 100%;background:#ddd' cellpadding=3 cellspacing=3 >
                                            <tr class='bg-blue'> <td style='text-align:center'>Categories</td></tr>
                                            <tr> <td>$text1</td></tr>
                                            <tr> <td>$text2 </td></tr>
                                            <tr> <td>$text3</td></tr>
                                        </table>
                                        <br/>
                                    </td>
                                </tr>
                            </table>

                            <br/>";


                        $html .= $html1;
                        $html .= $html2;
                        $html .= "<br/><br/><br/><br/>
                        </div>
                    </div>";
                        echo $html;
                        $pdf_file = $co->pdf_path;
                        store_pdf_path('ctm_quotation_co', $co->id, "QUOTATION_COP_{$co->id}_{$qtn}.pdf", $pdf_file);
                        ?>

                        <div class='row btn-bottom'>
                            <div class='col-sm-12 text-center'>
                                <a href = 'admin.php?page=confirm-order-items&co_id=<?= $co_id ?>&action=view' class='btn btn-dark btn-sm text-white'>Back</a>&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div><!--dashboard-widgets-wrap -->
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('cop_copy_dir'));
}