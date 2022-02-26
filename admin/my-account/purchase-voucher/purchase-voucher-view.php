<?php

function admin_ctm_purchase_voucher_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $id = !empty($getdata['id']) ? $getdata['id'] : 0;

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=purchase-voucher'));
        exit();
    }

    $purchase_voucher = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_purchase_vouchers where id='$id'");
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
        <h1 class="wp-heading-inline">View Purchase Voucher</h1>
        <a id="add-new-client" href="<?= "admin.php?page=purchase-voucher" ?>" class="page-title-action btn-primary" >Back</a>
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
                            <div id='welcome-to-aquila'>
                                <div class='inside' style='max-width:800px;margin:auto'>

                                    <?php
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
                                           
                                            <table width='800' cellpadding=0>
                                                <tr>
                                                <td colspan=2 style='text-align:left'><br/><br/>
                                                        <h6><span style='font-size:22px;'>PURCHASE VOUCHER</span></h6>
                                                    </td>
                                                </tr>
                                            </table>
                                             <table width=800 cellpadding=3 cellspacing=10>
                                                <tr>
                                                  <td><span>No: </span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$purchase_voucher->id}</span></td>
                                                  <td style='text-align: right;width:150px'>
                                                    <span>Date: </span>&nbsp;&nbsp;&nbsp;&nbsp;<span>" . rb_date($purchase_voucher->purchase_date) . "</span>
                                                    </div>
                                                  </td>
                                                </tr>
                                                <tr><td colspan=2 style='height: 10px;'></td></tr>
                                                <tr><td colspan=2>
                                                <b>" . get_supplier_by_id($purchase_voucher->sup_id, 'name') .
                                            "</b><br/>"
                                            . "Invocie No: $purchase_voucher->invoice_no"
                                            . "<br/>"
                                            . "Invocie Date: " . rb_date($purchase_voucher->invoice_date) . "
                                                              </td></tr>
                                                
                                                <tr><td colspan=2 style='height: 10px;'></td></tr>
                                                <tr>
                                                  <td style='border:1px solid #000;border-right: 0;vertical-align: middle;'><b>Description</b></td>
                                                  <td style='border:1px solid #000;border-left: 0;vertical-align: middle;'><b>Amount</b></td>
                                                </tr>
                                                
                                                <tr>
                                                  <td style='border:1px solid #000;border-right: 0;border-bottom: 0;vertical-align: middle;'><span>{$purchase_voucher->expense_type}</span></td>
                                                  <td style='border:1px solid #000;border-left: 0;border-bottom: 0;vertical-align: middle;'><span>" . number_format($purchase_voucher->amount, 2) . "</span></td>
                                                </tr>
                                                <tr>
                                                  <td style='border:1px solid #000;border-right: 0;border-top: 0;'><span>VAT</span></td>
                                                  <td style='border:1px solid #000;border-left: 0;border-top: 0;'><span>" . number_format($purchase_voucher->vat, 2) . "</span></td>
                                                </tr>
                                                
                                                <tr>
                                                <td><span>TOTAL</span></td>
                                                  <td><span>" . number_format($purchase_voucher->total_amount, 2) . "</span></td>
                                                </tr>

                                                <tr><td colspan=2 style='height:0;border-bottom:1px double #000'><br/></td></tr>
                                                <tr><td colspan=2 style='height:0'></td></tr>
                                                <tr><td colspan=2 style='border-top:1px double #000'></td></tr>
                                                
                                                <tr><td colspan=2><span>Narration:</span></td></tr>
                                                
                                                <tr><td colspan=2 style='border:1px solid #000;vertical-align: middle;padding:0px 3px'><span>" . nl2br($purchase_voucher->narration) . "</span></td></tr>
                                                
                                                <tr><td colspan=2><b><br/><br/></b></td></tr>
                                                
                                                <tr><td colspan=2 style='text-align:right'><a href='".admin_url("admin.php?page=payment-voucher-create&purchase_voucher_id[]={$purchase_voucher->id}")."' class='btn btn-primary btn-sm'>To PAY</a></td></tr>
                                                

                                              
                                              </table>
                                ";
                                    echo $html;
                                    $pdf_file = $purchase_voucher->pdf_path;
                                    store_pdf_path('ctm_purchase_vouchers', $purchase_voucher->id, "PURCHASE_VOUCHER_{$purchase_voucher->id}.pdf", $pdf_file);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <a href="admin.php?page=purchase-voucher"  class="button-secondary float-left" >Back</a>
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;&nbsp;";
                                    
                                   
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
    generate_pdf($html, $pdf_file);

    pdf_copy($pdf_file, get_option('purchase_voucher_copy_dir'));
}
