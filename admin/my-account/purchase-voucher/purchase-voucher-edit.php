<?php

function admin_ctm_purchase_voucher_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=purchase-voucher'));
        exit();
    }

    if (!empty($postdata['action'])) {
        $data = ['sup_id' => $postdata['sup_id'], 'invoice_no' => $postdata['invoice_no'], 'invoice_date' => $postdata['invoice_date'], 'expense_type' => $postdata['expense_type'], 'amount' => $postdata['amount'], 'vat' => $postdata['vat'],'total_amount' => $postdata['total_amount'], 'narration' => $postdata['narration'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->update("{$wpdb->prefix}ctm_purchase_vouchers", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%d']);
        $msg = 1;
    }

    $purchase_voucher = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_purchase_vouchers WHERE id={$id}");
    $rb_purchase_options = get_option('rb_payment_options', []);
    $suppliers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_suppliers WHERE sup_type='Local'");
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .wp-list-table{table-layout: auto!important;}
        table tr td.collection_name{width:400px}
        table tr td.description{width:200px}
        #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;height: 100px}
        select#supplier-name:invalid,select#employee-name:invalid {
            height: 0px !important;
            opacity: 0 !important;
            position: absolute !important;
            display: flex !important;
            width:1px!important;
        }
        .chosen-container{width:100%!important;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Edit Purchase Voucher</h1>
        <a id="add-new-client" href="<?= "admin.php?page=purchase-voucher" ?>" class="page-title-action btn-primary" >Back</a>
        <br/><br/>
        <?php if (!empty($getdata['msg'])) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Purchase Voucher has been updated successfully
            </div>
        <?php } ?>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="add-new-item-form" method="post">
                                    <table class="form-table">
                                        <tr>
                                            <td><label>Local Supplier:<span class="text-red">*</span></label><br/>
                                                <select name="sup_id" class="chosen-select" required>
                                                    <option value="">Select Payment Expense Type</option>
                                                    <?php
                                                    foreach ($suppliers as $value) {
                                                        $selected = $purchase_voucher->sup_id == $value->id ? 'selected' : '';
                                                        echo "<option value='$value->id' $selected>$value->name</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Invoice No:<span class="text-red">*</span></label>
                                                <input type=text name="invoice_no" placeholder="Invoice No" value="<?= $purchase_voucher->invoice_no ?>" required >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Invoice Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="invoice_date" value="<?= $purchase_voucher->invoice_date ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Type of Expense:<span class="text-red">*</span></label>
                                                <select name="expense_type" id="expense_type" required>
                                                    <option value="">Select Payment Expense Type</option>
                                                    <?php
                                                    foreach ($rb_purchase_options['rb_expense_type'] as $value) {
                                                        $selected = $purchase_voucher->expense_type == $value ? 'selected' : '';
                                                        echo "<option value='{$value}' $selected>{$value}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Amount:<span class="text-red">*</span></label>
                                                <input type=number name="amount" step="0.01" min='0' class="red-color" id='amount' placeholder="Amount" value="<?= $purchase_voucher->amount ?>" required >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>VAT:<span class="text-red">*</span></label>
                                                <input type=number name="vat" step="0.01" min='0' class="red-color" id='vat' placeholder="VAT" value="<?= $purchase_voucher->vat ?>" required >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Total Amount:<span class="text-red">*</span></label>
                                                <input type=number name="total_amount" step="0.01" min='0' class="red-color" id='total_amount' value="<?= rb_float($purchase_voucher->total_amount)?$purchase_voucher->total_amount:$purchase_voucher->amount+$purchase_voucher->vat ?>" required readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Narration:<span class="text-red">*</span></label>
                                                <textarea name="narration" rows="4"  required><?= $purchase_voucher->narration ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br/><input type="submit"  name="action" value="Update" class="button-primary"  >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="?page=purchase-voucher"  class="button-secondary" >Cancel</a></td>
                                        </tr>
                                    </table>
                                    <br/><br/><br/>
                                </form>

                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>

    <script>
        jQuery(document).ready(() => {
            jQuery('.chosen-select').chosen();
            jQuery('#add-new-item').click(() => {
                jQuery('#add-new-item-form').toggleClass('hide');
                jQuery('#page-inner-content').toggleClass('hide');
            });
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery('#amount,#vat').on('input', () => {
                var amount = jQuery('#amount').val();
                var vat = jQuery('#vat').val();
                var _amount = parseFloat(!isNaN(amount) && amount !== '' ? amount : 0);
                var _vat = parseFloat(!isNaN(vat) && vat !== '' ? vat : 0);
                var totla_amount = parseFloat(_amount + _vat).toFixed(2);
                jQuery('#total_amount').val(totla_amount);
            });
        });
    </script>
    <?php
}
