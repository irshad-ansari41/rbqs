<?php

function admin_ctm_bank_account_add_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    if (!empty($postdata['action'])) {

        //DEBIT AMOUNT
        $closing = $wpdb->get_var("SELECT closing FROM {$wpdb->prefix}ctm_bank_transactions WHERE account_name='{$postdata['from_account']}' ORDER BY transaction_time DESC") ?? 0;
        if ($closing > $postdata['amount']) {
            $data = ['withdrawal_id' => null, 'transaction_date' => $postdata['transaction_date'], 'bank_date' => $postdata['bank_date'], 'account_name' => $postdata['from_account'], 'particulars' => $postdata['to_account'], 'voucher_type' => $postdata['voucher_no'], 'voucher_no' => $postdata['voucher_no'], 'opening' => $closing, 'debit' => $postdata['amount'], 'closing' => $closing - $postdata['amount'], 'transaction_time' => date('YmdHis'), 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
            $debit_id = debit_amount($data);
        } else {
            $error = "You do not have sufficient fund to complete this transaction.\n";
        }

        if (!empty($debit_id)) {
            //CREDIT AMOUNT
            $amount = $postdata['currency'] == 'EURO' ? $postdata['amount'] / $postdata['euro_rate'] : ($postdata['currency'] == 'USD' ? $postdata['amount'] / $postdata['usd_rate'] : $postdata['amount']);
            $closing = $wpdb->get_var("SELECT closing FROM {$wpdb->prefix}ctm_bank_transactions WHERE account_name='{$postdata['to_account']}' ORDER BY transaction_time DESC") ?? 0;
            $data = ['deposit_id' => null, 'transaction_date' => $postdata['transaction_date'], 'bank_date' => $postdata['bank_date'], 'account_name' => $postdata['to_account'], 'particulars' => $postdata['from_account'], 'voucher_type' => $postdata['voucher_type'], 'voucher_no' => $postdata['voucher_no'], 'opening' => $closing, 'credit' => $amount, 'closing' => $closing + $amount, 'transaction_time' => date('YmdHis'), 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
            credit_amount($data);
            $msg = 1;
        } else if (!empty($debit_id)) {
            $error = "Something went wrong.";
        }
    }
    $rb_payment_options = get_option('rb_payment_options', []);
    $euro_rate = $rb_payment_options['rb_exchange_rate']['EURO'] ?? 1;
    $usd_rate = $rb_payment_options['rb_exchange_rate']['USD'] ?? 1
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
        <h1 class="wp-heading-inline">Internal Transfer</h1>
        <a href="<?= 'admin.php?page=bank-account' ?>" class="page-title-action btn-primary">Back</a>
        <br/><br/>
        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Transaction has been done successfully
            </div>
        <?php } ?>
        <?php if (!empty($error)) { ?>
            <br/>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?= $error ?>
            </div>
        <?php } ?>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox">
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="add-new-item-form"  method="post">
                                    <input type=hidden name="page"  value="<?= $getdata['page'] ?>" >
                                    <table class="form-table">
                                        <tr><td colspan="2"><b style='font-size:22px;'>Balance:<span style="color: red;">
                                                        <?= number_format(get_closing_amount(PAYMENT_SOURCE[1]), 2) ?></span> AED</b></td></tr>
                                        <tr>
                                            <td><label>Source Account:<span class="text-red">*</span></label><br/>
                                                <select name="from_account" id="payment_source" required>
                                                    <option value="">Source Account</option>
                                                    <?php
                                                    foreach (PAYMENT_SOURCE as $value) {
                                                        $selected = $value == PAYMENT_SOURCE[1] ? 'selected' : '';
                                                        ?>
                                                        <option value="<?= $value ?>" <?= $selected ?>><?= $value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>

                                            <td><label>Destination Account:<span class="text-red">*</span></label><br/>
                                                <select name="to_account" id="payment_source" required>
                                                    <option value="">Destination Account</option>
                                                    <?php
                                                    foreach (PAYMENT_SOURCE as $value) {
                                                        $selected = $value == PAYMENT_SOURCE[1] ? 'disabled' : '';
                                                        ?>
                                                        <option value="<?= $value ?>" <?= $selected ?>><?= $value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Amount(AED):<span class="text-red">*</span></label><br/>
                                                <input type=number name='amount' step="0.01" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:50%"><label>Currency:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency"  class="currency" value="AED" required >AED</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency"  class="currency" value="EURO" required >EURO(€)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" class="currency" value="USD" required >USD($)</label><br/>
                                            </td>

                                            <td><label>Exchange Rate:<span class="text-red">*</span></label><br/>
                                                <input type=number name='aed_rate' id="aed_rate" step=1" min="1" max="1" value="1" readonly>
                                                <input type=number name='euro_rate' id="euro_rate" step="0.00001" value="<?= $euro_rate ?>" >
                                                <input type=number name='usd_rate' id="usd_rate" step="0.00001" value="<?= $usd_rate ?>" >
                                            </td>
                                        </tr>


                                        <tr>
                                            <td><label>Transaction Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name='transaction_date' required>
                                            </td>

                                            <td><label>Payment Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name='bank_date' required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Voucher Type:</label><br/>
                                                <input type=text name='voucher_type'>
                                            </td>

                                            <td><label>Voucher No:</label><br/>
                                                <input type=text name='voucher_no'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Note:</label><br/>
                                                <input type="text" name='note'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <br/><input type="submit" name="action" value="Submit" class="button-primary"  >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="admin.php?page=bank-account"  class="button-secondary" >Back</a></td>
                                        </tr>
                                    </table>

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
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
            jQuery('#euro_rate').hide();
            jQuery('#usd_rate').hide();
            jQuery('.currency').click(function () {
                var pv_type = jQuery('input[name="currency"]:checked').val();
                if (pv_type === 'AED') {
                    jQuery('#aed_rate').show();
                    jQuery('#aed_rate').attr('required', true);

                    jQuery('#usd_rate,#euro_rate').hide();
                    jQuery('#usd_rate,#euro_rate').removeAttr('required');
                }
                if (pv_type === 'USD') {
                    jQuery('#usd_rate').show();
                    jQuery('#usd_rate').attr('required', true);

                    jQuery('#aed_rate,#euro_rate').hide();
                    jQuery('#aed_rate,#euro_rate').removeAttr('required');
                }
                if (pv_type === 'EURO') {
                    jQuery('#euro_rate').show();
                    jQuery('#euro_rate').attr('required', true);

                    jQuery('#usd_rate,#aed_rate').hide();
                    jQuery('#usd_rate,#aed_rate').removeAttr('required');
                }
            });

        });



    </script>
    <?php
}
