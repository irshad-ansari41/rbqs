<?php

function admin_ctm_sales_reversal_edit_page() {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;

    if (empty($id)) {
        create_tax_credit_note($id);
        wp_redirect(admin_url('/admin.php?page=tax-credit-note'));
        exit();
    }

    if (!empty($postdata['update']) && !empty($postdata['meta_ids'])) {
        $meta_ids = implode(',', $postdata['meta_ids']);
        $paid_percent = get_paid_percent($postdata['quotation_id']);
        $wpdb->get_row("UPDATE {$wpdb->prefix}ctm_sales_reversal set extra_charge='{$postdata['extra_charge']}', meta_ids='{$meta_ids}', paid_percent='{$paid_percent}', note='{$postdata['note']}' where id='{$id}'");
        create_store_credit_note($id);
    }

    $sales_reversal = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_sales_reversal where id='{$id}'");

    if (empty($sales_reversal)) {
        wp_redirect(admin_url('/admin.php?page=tax-credit-note'));
        exit();
    }
    $quotation = get_quotation($sales_reversal->quotation_id);
    $quotation_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$sales_reversal->quotation_id}' ORDER BY id ASC");

    $is_diss = !empty(rb_float($quotation_meta[0]->discount)) ? true : (rb_float($quotation->special_discount) ? true : false);

    if ($quotation->promo_type == 'Export') {
        $quotation->vat = 'wovat';
    }
    ?>

    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }

        .wp-list-table{table-layout: auto!important;}
        table tr td.collection_name{width:400px}
        table tr td.description{width:200px}
        #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;height: 100px}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Edit Reversal of confirmed QTN</h1>
        <br/><br/>
        <?php if (!empty($getdata['msg'])) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Bank Deposit has been updated successfully
            </div>
        <?php } ?>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <form method="post">
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">

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
                                    <br/>

                                    <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>

                                        <tr class='text-center bg-blue'>
                                            <td rowspan=2 class='text-center'><b><?= ( $quotation->type == 'Stock' ? 'Entry#' : 'SUP') ?></b></td>
                                            <td rowspan=2 class='text-center'><b>Item Description</b></td>
                                            <td rowspan=2 class='text-center' width=50><b>Image</b></td>
                                            <td rowspan=2 class='text-center'><b>qty</b></td>
                                            <td class='text-center'><b>Unit Price<br/>(Incl.&nbsp;VAT)</b></td>
                                            <td class='text-center'><b>Net Price<br/><?= ($is_diss ? '(AFTER DISCOUNT)' : '' ) ?></b></td>
                                            <td class='text-center'><b>Price<br/>(EXCLUDING VAT)</b></td>
                                            <td class='text-center'><b>VAT<br/>@<?= ($quotation->vat == 'wovat' ? 0 : 5) ?>%</b></td>
                                            <td class='text-center'><b>Total</b></td>
                                        </tr>
                                        <tr class='text-center bg-blue'>
                                            <td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td><td class='text-center'>AED</td>
                                        </tr>
                                        <?php
                                        $note = $sales_reversal->note ? $sales_reversal->note : 'Note: Amount to be refunded is net of 3% bank charge, if applicable and 2% transaction charge.';
                                        foreach ($quotation_meta as $value) {
                                            $item = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items where id='{$value->item_id}'");

                                            $image_src = get_image_src($item->image);
                                            $diss_price = ($value->price_incl_vat * $value->quantity * $is_diss) / 100;
                                            $net_price = ($value->price_incl_vat * $value->quantity) - $diss_price;

                                            $checked = in_array($value->id, explode(',', $sales_reversal->meta_ids)) ? 'checked' : '';
                                            ?>
                                            <tr>
                                                <td class='fnt11 text-center'>
                                                    <label><input type='checkbox' name='meta_ids[]' value='<?= $value->id ?>' <?= $checked ?> />
                                                        <?= ( $quotation->type == 'Stock' ? $value->entry : $value->sup_code) ?></label></td>
                                                <td class=fnt11 ><?= nl2br($value->item_desc) ?></td>
                                                <td class=fnt11 ><img src='<?= $image_src ?>' width=50  style='margin: auto;width: 50px; '></td>
                                                <td class='fnt11 text-center' ><?= $value->quantity ?></td>
                                                <td class='fnt11 text-center' ><?= number_format($value->price_incl_vat, 2) ?></td>
                                                <td class='fnt11 text-center' ><?= number_format($net_price, 2) ?></td>
                                                <td class='fnt11 text-center' ><?= number_format($value->net_price, 2) ?></td>
                                                <td class='fnt11 text-center' ><?= number_format($value->vat, 2) ?></td>
                                                <td class='fnt11 text-center' ><?= number_format($value->total_incl_vat, 2) ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                    <br/>
                                    <input type="text" name='note' placeholder='Note' style='width:100%;color:red;font-weight: bold' value="<?= $note ?>" />
                                </div>
                            </div>
                            <div class="row btn-bottom">
                                <div class="col-sm-3 text-right"><a href="admin.php?page=sales-reversal"  class="button-secondary" >Back</a></div>
                                <div class="col-sm-2 text-right">Extra Charge(%):</div>
                                <div class="col-sm-2 text-left">
                                    <input type="number" name="extra_charge" placeholder="Extra Charge"  step="0.0000000001" min="0" max="100" value="<?= $sales_reversal->extra_charge ?? '0.00' ?>" />
                                </div>
                                <div class="col-sm-3 text-center">
                                    <input type="hidden" name="quotation_id" value="<?= $sales_reversal->quotation_id ?>" />
                                    <button type="submit" name="update" class="button-primary" value="update" >Update Reversal of confirmed QTN</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                jQuery(document).ready(() => {
                    jQuery('#open-close-menu').click(() => {
                        jQuery('#collapse-button').trigger('click');
                    });
                });
            </script>
            <?php
        }
        