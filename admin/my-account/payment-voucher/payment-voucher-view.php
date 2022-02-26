<?php
include_once 'send-payment-voucher-email.php';

function admin_ctm_payment_voucher_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    if (!empty($postdata['send_email'])) {
        $email_status = rb_send_email($postdata);
        $msg = !empty($email_status) ? "<strong>Success!</strong> Email has been sent successfully." : 0;
    }

    $id = !empty($getdata['id']) ? $getdata['id'] : 0;

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=payment-voucher'));
        exit();
    }

    $payment_voucher = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_payment_vouchers where id='$id'");
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
        <h1 class="wp-heading-inline">Payment Voucher</h1>
        <a id="add-new-client" href="<?= "admin.php?page=payment-voucher" ?>" class="page-title-action btn-primary" >Back</a>
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
                            $being = explode('<br />', nl2br($payment_voucher->being));

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
                                                        <h6><span style='font-size:22px;'>PAYMENT VOUCHER</span></h6><br/><br/>
                                                    </td>
                                                </tr>
                                            </table>
                                             <table width=800 cellpadding=3 cellspacing=10>
                                      
                                                <tr>
                                                  <td colspan=2 ><b>No: </b><b style='color: red;font-size:22px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$payment_voucher->id}</b></td>
                                                  <td colspan=2 style='text-align: right;width:150px'>
                                                    <div style='width:150px;text-align:left;float: right;'>
                                                    <b>Ref: </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b class='date'>$payment_voucher->ref</b><br/>
                                                    <b>Date: </b>&nbsp;&nbsp;&nbsp;&nbsp;<b class='date'>" . rb_date($payment_voucher->updated_at) . "</b><br/>
                                                    </div>
                                                  </td>
                                                </tr>

                                                <tr>
                                                  <td style='width: 100px;' colspan=4>
                                                        <b>Amount:&nbsp;&nbsp;&nbsp;&nbsp;</b>$payment_voucher->currency "
                                                        . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b class='amt-color' style='border: 1px solid #000; padding: 5px 10px; font-size:17px;'>"
                                                        . number_format($payment_voucher->amount, 2) . "</b>
                                                    </td>
                                                </tr>


                                                <tr>
                                                  <td class='bb' colspan=4><b>Paid To:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>{$payment_voucher->paid_to}</td>
                                                </tr>

                                                <tr>
                                                  <td colspan=4 class='bb'><b>The sum of {$payment_voucher->currency}:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>{$payment_voucher->word}</td>
                                                </tr>

                                                <tr>
                                                  <td colspan=4 class='bb'></td>
                                                </tr>


                                                <tr>
                                                  <td class='bb' colspan=2 style='width:50%'><b>Payment Method:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>{$payment_voucher->payment_method}</td>
                                                  <td class='bb' colspan=2><b>Date:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . rb_date($payment_voucher->payment_date) . "</td>
                                                </tr>
                                                
                                                <tr>
                                                  <td class='bb' colspan=2 style='width:50%'><b>Check No:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>{$payment_voucher->check_no}</td>
                                                  <td class='bb' colspan=2><b>Bank:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$payment_voucher->payment_source}</td>
                                                </tr>

                                                <tr>
                                                  <td colspan=4 class='bb'><b>Being:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . (!empty($being[0]) ? $being[0] : '') . "</td>
                                                </tr>

                                                <tr>
                                                  <td colspan=4 class='bb' style='height:36px;padding-left: 70px;'>" . (!empty($being[1]) ? $being[1] : '') . "</td>
                                                </tr>
                                                <tr>
                                                  <td colspan=4 class='bb' style='height:36px;padding-left: 70px;'>" . $payment_voucher->note . "</td>
                                                </tr>
                                                <tr>
                                                  <td  style='width:200px;text-align:center' class='bb'>{$payment_voucher->manager}</td>
                                                  <td colspan=2 style='text-align:center' class='bb'>{$payment_voucher->accountant}</td>
                                                  <td  style='width:200px;text-align:center' class='bb'>{$payment_voucher->receiver} </td>
                                                </tr>
                                                <tr>
                                                  <td  style='width:200px;text-align:center' ><b>Manager: </b></td>
                                                  <td colspan=2 style='text-align:center' ><b>Accountant: </b></td>
                                                  <td  style='width:200px;text-align:center' ><b>Receiver: </b></td>
                                                </tr>
                                              
                                              </table>
                                </div>
                            </div>";
                            echo $html;
                            $pdf_file = $payment_voucher->pdf_path;
                            store_pdf_path('ctm_payment_vouchers', $payment_voucher->id, "PAYMENT_VOUCHER_{$payment_voucher->id}_{$payment_voucher->paid_to}.pdf", $pdf_file);
                            ?>
                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <a href="admin.php?page=payment-voucher"  class="button-secondary float-left" >Back</a>
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    if (!$payment_voucher->once) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                                        $wpdb->query("UPDATE {$wpdb->prefix}ctm_payment_vouchers SET once=1 where id='$id'");
                                    }
                                    echo "<a href='" . curr_url('pdf=2') . "'  target='_blank' class='btn btn-success  btn-sm text-white'>View Again</a>&nbsp;&nbsp;&nbsp;&nbsp;";
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

    pdf_copy($pdf_file, get_option('payment_voucher_copy_dir'));
}
