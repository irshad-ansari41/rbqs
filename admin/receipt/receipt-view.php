<?php
include_once 'send-receipt-email.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_receipt_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    if (!empty($postdata['send_email'])) {
        $email_status = rb_send_email($postdata);
        $msg = !empty($email_status) ? "<strong>Success!</strong> Email has been sent successfully." : 0;
    }

    $id = !empty($getdata['id']) ? $getdata['id'] : 0;

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=receipt'));
        exit();
    }

    $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts where id='$id'");
    $qtn = get_revised_no($receipt->quotation_id);
    make_model_send_receipt_email($receipt);
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], 
        #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], 
        #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
        #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
        #tbl-filter{background: #fff; border: 20px solid #fff;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Receipt</h1>
        <br/><br/>
        <?php if (!empty($msg)) {
            ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?= $msg ?>
            </div>
        <?php } ?>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="page-inner-content" class="postbox">


                            <?php
                            $being = explode('<br />', nl2br($receipt->being));

                            $html = "
                                    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], 
                                    #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], 
                                    #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], 
                                    #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], 
                                    #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr th,table tr td,p,b,span{font-size:12px;font-family:'Tahoma'}
                                        h6 span{text-transform:uppercase;}
                                        p{margin-bottom: 0;}
                                        table{empty-cells:hidden;}
                                        table tr td{font-size:12px;font-family:'Tahoma';vertical-align: bottom;height:30px;}
                                        .bb{border-bottom: 1px dotted #000!important;}
                                        .text-center{text-align:center;}   
                                        .date{border-bottom: 1px dotted #000;display: inline-block; line-height: 25px;}
                                        table,th,tr,td,b,span,h6{box-sizing:unset;}
                                    </style>
                                    <div id='welcome-to-aquila' class='postbox'>
                                        <div class='inside' style='max-width:800px;margin:auto'>
                                            <br/>
                                            <table width='800' cellpadding=0>
                                                <tr valign='top'>
                                                    <td colspan=3 style='text-align:right'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                <td colspan=3 style='text-align:center'><br/><br/>
                                                        <h6><span style='font-size:22px;'>RECEIPT VOUCHER</span></h6><br/><br/>
                                                    </td>
                                                </tr>
                                            </table>
                                             <table width=800 cellpadding=3 cellspacing=10>
                                      
                                                <tr>
                                                  <td colspan=2 ><b>No: </b><b style='color: red;font-size:22px;'>{$receipt->id}</b></td>
                                                  <td colspan=2 style='text-align: right;'><b>Date: </b><b class='date'>" . rb_date($receipt->updated_at) . "</b></td>
                                                </tr>

                                              <tr>
                                                  <td style='width: 175px;'><b>Amount Dhs: </b></td>
                                                  <td><b class='amt-color' style='border: 1px solid #000; padding: 5px 10px; font-size:17px;'>" . number_format($receipt->paid_amount, 2) . "</b></td>
                                                  <td></td>
                                                  <td></td>
                                                </tr>


                                              <tr>
                                                  <td><b>Received From: </b></td>
                                                  <td colspan=3 class='bb'>{$receipt->received_from}</td>
                                              </tr>

                                              <tr>
                                                  <td><b>The sum of Dhs: </b></td>
                                                  <td colspan=3 class='bb'>{$receipt->word}</td>
                                              </tr>

                                              <tr>
                                                  <td colspan=4 class='bb'></td>
                                              </tr>


                                              <tr>
                                                  <td><b>Payment Method: </b></td>
                                                  <td class='bb'>{$receipt->payment_method}</td>
                                                  <td style='width:50px'><b>Date:</b></td>
                                                  <td class='bb'>" . rb_date($receipt->payment_date) . "</td>
                                                </tr>


                                              <tr>
                                                  <td><b>Check/ Card Approval No: </b></td>
                                                  <td class='bb'>{$receipt->check_no}</td>
                                                  <td><b>Bank:</b></td>
                                                  <td class='bb'>{$receipt->bank}</td>
                                                </tr>

                                              <tr>
                                                  <td><b>Being:</b></td>
                                                  <td colspan=3 class='bb'>" . (!empty($being[0]) ? $being[0] : '') . "</td>
                                                </tr>

                                              <tr>
                                                  <td colspan=4 class='bb' style='height:36px;padding-left: 185px;'>" . (!empty($being[1]) ? $being[1] : '') . "</td>
                                              </tr>
                                              <tr>
                                                  <td colspan=4 class='bb' style='height:36px;padding-left: 185px;'>" . $receipt->note . "</td>
                                              </tr>
                                              <tr>
                                                  <td colspan=2><br/>
                                                  *All Payment received are 'Non Refundable'<br/>*Check are subject to 'Realization'
                                                  </td>
                                                  <td colspan=2 style='width:200px' class='bb'><b>Accountant: </b>
                                                  <span>{$receipt->accountant}<span></td>
                                              </tr>
                                              
                                              </table>
                                </div>
                            </div>";
                            echo $html;
                            $pdf_file = $receipt->pdf_path;
                            store_pdf_path('ctm_receipts', $receipt->id, "RECEIPT_{$receipt->id}_{$receipt->received_from}_{$qtn}.pdf", $pdf_file);
                            ?>
                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    if (!$receipt->once) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                                        $wpdb->query("UPDATE {$wpdb->prefix}ctm_receipts SET once=1 where id='$id'");
                                    }
                                    echo "<a href='" . curr_url('pdf=2') . "'  target='_blank' class='btn btn-success  btn-sm text-white'>View Again</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                                    echo '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myEmailModal" data-keyboard="false">Send Email</button>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
        });
    </script>
    <?php
    $watermark = !empty($getdata['pdf']) && $getdata['pdf'] == 1 ? 'ORIGINAL' : (!empty($getdata['pdf']) && $getdata['pdf'] == 2 ? 'COPY' : '');
    generate_pdf($html, $pdf_file, $watermark);

    pdf_copy($pdf_file, get_option('receipt_copy_dir'));
}
