<?php

function admin_ctm_bank_deposit_settle_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=bank-deposit'));
        exit();
    }

    if (!empty($postdata['action'])) {

        $data = ['receipt_id' => $postdata['receipt_id'], 'payment_status' => $postdata['payment_status'], 'change_amount' => $postdata['change_amount'], 'charges' => $postdata['charges'], 'hold_cash' => $postdata['hold_cash'], 'net_deposit' => $postdata['net_deposit'], 'bank_status' => $postdata['bank_status'], 'verify_date' => $postdata['verify_date'], 'updated_by' => $current_user->ID, 'created_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_bank_deposits", $data, wpdb_data_format($data));

        $receipt = get_receipt($postdata['receipt_id']);
        make_credit_transaction($wpdb->insert_id, $receipt->payment_date, $postdata['verify_date'], $receipt->received_from, $receipt->id, $postdata['net_deposit']);

        wp_redirect("admin.php?page=bank-deposit&id=$id&msg=settle");
        exit();
    }

    $bank_deposit = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_bank_deposits WHERE id={$id}");
    $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE id={$bank_deposit->receipt_id}");
    $qtn = !empty($receipt->revised_no) ? $receipt->revised_no : $receipt->quotation_id;
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
        <h1 class="wp-heading-inline">Settle Bank Deposit</h1>
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
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="add-new-item-form"  method="post">
                                    <input type=hidden name="page"  value="<?= $getdata['page'] ?>" >
                                    <input type=hidden name="id" value="<?= $id ?>" />
                                    <table class="form-table" style="width:100%">
                                        <tr>
                                        <tr>
                                            <td><label>Receipt No:<span class="text-red">*</span></label>
                                                <input type=text readonly value="<?= $receipt->id ?>" >
                                            </td>

                                            <td><label>QTN:<span class="text-red">*</span></label>
                                                <input type=text readonly value="<?= $qtn ?>" >
                                            </td>

                                            <td><label>Type:<span class="text-red">*</span></label>
                                                <input type=text readonly value="<?= get_quotation($receipt->quotation_id, 'type') ?>" >
                                            </td>
                                            <td><label class="text-red">Receipt Amount:<span class="text-red">*</span></label>
                                                <input type=text  readonly value="<?= $receipt->paid_amount ?>">
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><label>Received From:<span class="text-red">*</span></label>
                                                <input type=text  readonly value="<?= $receipt->received_from ?>" />
                                            </td>
                                            <td><label>Payment Date:<span class="text-red">*</span></label><br/>
                                                <input type="date" readonly value="<?= $receipt->payment_date ?>">
                                            </td>
                                            <td><label>Payment Method:<span class="text-red">*</span></label>
                                                <select disabled style="color: #000;border: 1px solid #000;">
                                                    <option value="">Select Payment Method</option>
                                                    <option value="Cash" <?= $receipt->payment_method == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                                    <option value="Check" <?= $receipt->payment_method == 'Check' ? 'selected' : '' ?>>Check</option>
                                                    <option value="Card" <?= $receipt->payment_method == 'Card' ? 'selected' : '' ?>>Card</option>
                                                    <option value="Bank Transfer" <?= $receipt->payment_method == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                                    <option value="Bank Deposit" <?= $receipt->payment_method == 'Bank Deposit' ? 'selected' : '' ?>>Bank Deposit</option>
                                                </select>
                                            </td>
                                            <td>
                                                <?php if ($receipt->payment_method == 'Check') { ?>
                                                    <label>Check Date:<span class="text-red">*</span></label><br/>
                                                    <input type="date" readonly value="<?= $receipt->check_date ?>" required>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <?php if ($receipt->payment_method == 'Check' && $receipt->check_image) { ?>
                                                    <label>Image:</label><br/>
                                                    <?= wp_get_attachment_image(!empty($receipt) ? $receipt->check_image : '', 'large') ?>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Payment Status:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light"><input type="radio" name="payment_status" value="Full" required />Full</label>&nbsp;&nbsp;
                                                <label class="font-weight-light"><input type="radio" name="payment_status" value="Advance" required />Advance</label>&nbsp;&nbsp;
                                                <label class="font-weight-light"><input type="radio" name="payment_status" value="Balance" required />Balance</label>&nbsp;&nbsp;
                                            </td>
                                            <td><label>Change:</label>
                                                <input type=number name="change_amount" id="change" min="0" step="0.01" max="<?= $bank_deposit->hold_cash ?>"  placeholder="Change" />
                                            </td>
                                            <td><label>Charges:</label>
                                                <input type=number name="charges" id="charges" min="0" step="0.01" max="<?= $bank_deposit->hold_cash ?>"  placeholder="Charges" />
                                            </td>
                                            <td><label>Cash on Hold:</label>
                                                <input type=number name="hold_cash" id="hold_cash" min="0" step="0.01" max="<?= $bank_deposit->hold_cash ?>" placeholder="Cash on Hold" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Net Deposit:</label>
                                                <input type=number name="net_deposit"  id="net_deposit" min="0" step="0.01"  placeholder="Net Deposit" value="<?= $bank_deposit->hold_cash ?>" required />
                                            </td>
                                            <td><label>Bank Verification Status:</label><br/>
                                                <label class="font-weight-light"><input type="radio" name="bank_status" value="Dummy" required />Dummy</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light"><input type="radio" name="bank_status" value="Verified" required />Verified</label>
                                                &nbsp;&nbsp;

                                            </td>
                                            <td><label>Bank Verification Date:</label><br/>
                                                <input type=date name="verify_date" >
                                            </td>
                                            <td/>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <input type=hidden name="receipt_id" value="<?= $receipt->id ?>" />
                                                <br/><input type="submit"  name="action" value="Update" class="button-primary"  >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="admin.php?page=bank-deposit"  class="button-secondary" >Back</a></td>
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
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
            jQuery('#net_deposit').on('input blur', () => {
                var receipt_amount = parseFloat(<?= $bank_deposit->hold_cash ?>);
                var net_amount = parseFloat(jQuery('#net_deposit').val());
                var charges = parseFloat(jQuery('#charges').val());

                net_amount = isNaN(net_amount) ? 0 : net_amount;
                charges = isNaN(charges) ? 0 : charges;

                if (net_amount > receipt_amount) {
                    var change_amount = net_amount - receipt_amount;
                    jQuery('#change').val(change_amount);
                    jQuery('#hold_cash').val(0);
                } else {
                    var hold_amount = receipt_amount - (net_amount + charges);
                    jQuery('#hold_cash').val(hold_amount);
                    jQuery('#change').val(0);
                }
            });
            jQuery('#charges').on('input blur', () => {
                var receipt_amount = parseFloat(<?= $bank_deposit->hold_cash ?>);
                var charges = parseFloat(jQuery('#charges').val());

                charges = isNaN(charges) ? 0 : charges;

                var net_amount = receipt_amount - charges;

                if (net_amount > receipt_amount) {
                    var change_amount = net_amount - receipt_amount;
                    jQuery('#change').val(change_amount);
                    jQuery('#hold_cash').val(0);
                } else {
                    var hold_amount = receipt_amount - (net_amount + charges);
                    jQuery('#hold_cash').val(hold_amount);
                    jQuery('#change').val(0);
                }


                jQuery('#net_deposit').val(net_amount);
                var hold_amount = receipt_amount - (net_amount + charges);
                jQuery('#hold_cash').val(hold_amount);
            });
        });



    </script>
    <?php
}
