<?php

function admin_ctm_payment_voucher_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=payment-voucher'));
        exit();
    }

    if (!empty($postdata['action'])) {
        $pv_type = !empty($postdata['pv_type']) ? $postdata['pv_type'] : '';
        $ref = !empty($postdata['ref']) ? $postdata['ref'] : '';
        $currency = !empty($postdata['currency']) ? $postdata['currency'] : '';
        $invoice_no = !empty($postdata['invoice_no']) ? $postdata['invoice_no'] : '';
        $purchase_voucher = !empty($postdata['purchase_voucher']) ? $postdata['purchase_voucher'] : '';
        $expense_type = !empty($postdata['expense_type']) ? $postdata['expense_type'] : '';
        $has_vat = !empty($postdata['has_vat']) ? $postdata['has_vat'] : 0;
        $amount = !empty($postdata['amount']) ? $postdata['amount'] : 0.0;
        $word = !empty($postdata['word']) ? $postdata['word'] : '';
        $paid_to = !empty($postdata['paid_to']) ? $postdata['paid_to'] : '';
        $payment_method = !empty($postdata['payment_method']) ? $postdata['payment_method'] : '';
        $payment_date = !empty($postdata['payment_date']) ? $postdata['payment_date'] : '';
        $check_no = !empty($postdata['check_no']) ? $postdata['check_no'] : '';
        $check_image = !empty($postdata['image']) ? $postdata['image'] : '';
        $check_date = !empty($postdata['check_date']) ? $postdata['check_date'] : '';
        $payment_source = !empty($postdata['payment_source']) ? $postdata['payment_source'] : '';
        $being = !empty($postdata['being']) ? $postdata['being'] : '';
        $note = !empty($postdata['note']) ? $postdata['note'] : '';
        $accountant = !empty($postdata['accountant']) ? $postdata['accountant'] : '';
        $manager = !empty($postdata['manager']) ? $postdata['manager'] : '';
        $receiver = !empty($postdata['receiver']) ? $postdata['receiver'] : '';

        $data = ['pv_type' => $pv_type, 'ref' => $ref, 'invoice_no' => $invoice_no, 'purchase_voucher' => $purchase_voucher, 
        'expense_type' => $expense_type, 'currency' => $currency,'has_vat' => $has_vat, 'amount' => $amount, 'word' => $word, 'paid_to' => $paid_to, 
        'payment_method' => $payment_method, 'payment_date' => $payment_date, 'check_no' => $check_no, 'check_image' => $check_image, 
        'check_date' => $check_date,  'being' => $being, 'note' => $note, 'accountant' => $accountant, 'manager' => $manager, 
        'receiver' => $receiver, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 
        'updated_at' => $date];

        $wpdb->update("{$wpdb->prefix}ctm_payment_vouchers", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%d']);

        $withdrawal_id = $id;
        make_debit_transaction($withdrawal_id, $payment_source, $payment_date, $payment_date, $paid_to, $id, $amount);

        wp_redirect("admin.php?page={$getdata['page']}&id=$id&msg=updated");
        exit();
    }

    $payment_voucher = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_payment_vouchers WHERE id={$id}");

    if (empty($payment_voucher)) {
        wp_redirect(admin_url('/admin.php?page=payment-voucher'));
        exit();
    }
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .wp-list-table{table-layout: auto!important;}
        table tr td.collection_name{width:400px}
        table tr td.description{width:200px}
        /*#being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;height: 100px}*/
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
        <h1 class="wp-heading-inline">Edit Payment Voucher</h1>
        <a id="add-new-client" href="<?= "admin.php?page=payment-voucher" ?>" class="page-title-action btn-primary" >Back</a>
        <br/><br/>
        <?php if (!empty($getdata['msg'])) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Payment Voucher has been updated successfully
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
                                    <b style='color: red;font-size:22px;'><?= $payment_voucher->id ?></b>
                                    <table class="form-table" style="width:100%">
                                        <tr>
                                            <td><label>Payment Voucher Type:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="pv_type" class="pv_type"  value="Employee" required <?= $payment_voucher->pv_type == 'Employee' ? 'checked' : 'disabled' ?>>Employee</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="pv_type"  class="pv_type"  value="Supplier" required <?= $payment_voucher->pv_type == 'Supplier' ? 'checked' : 'disabled' ?>>Supplier</label>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="pv_type"  class="pv_type"  value="Other" required <?= $payment_voucher->pv_type == 'Other' ? 'checked' : 'disabled' ?>>Other</label>
                                                <input type=hidden name='pv_type' value="<?= $payment_voucher->pv_type ?>" />
                                            </td>
                                            <td style="min-width: 300px;">
                                                <label>Ref:<span class="text-red">*</span></label>
                                                <input type=text name="ref" placeholder="Ref" value="<?= $payment_voucher->ref ?>" required >
                                            </td>
                                            <td><label>Purchase Voucher No:</label>
                                                <input type=text name="purchase_voucher" placeholder="Purchase Voucher No"  value="<?= $payment_voucher->purchase_voucher ?>"  >
                                            </td>
                                            <td><label>Invoice No:</label>
                                                <input type=text name="invoice_no" placeholder="invoice_no" value="<?= $payment_voucher->invoice_no ?>"  >
                                            </td>

                                        </tr>
                                        <tr>
                                            <td><label class="red-color">Amount:<span class="text-red">*</span></label>
                                                <input type=number name="amount" step="0.01" min='0' id="amount" class="red-color" placeholder="Amount" required readonly 
                                                       value="<?= !empty($payment_voucher) ? $payment_voucher->amount : '' ?>">
                                            </td>
                                            <td><label>Currency:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="AED" required <?= $payment_voucher->currency == 'AED' ? 'checked' : 'disabled' ?>>AED(د.إ)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency"  value="EURO" required <?= $payment_voucher->currency == 'EURO' ? 'checked' : 'disabled' ?>>EURO(€)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="USD" required <?= $payment_voucher->currency == 'USD' ? 'checked' : 'disabled' ?>>USD($)</label><br/>
                                            </td>
                                            <td style="width:50%"><label>Has VAT:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="has_vat" class="currency" value="1"  required <?= $payment_voucher->has_vat == 1 ? 'checked' : '' ?>>With VAT</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="has_vat" class="currency" value="0" required <?= $payment_voucher->has_vat == 0 ? 'checked' : '' ?>>Without VAT</label>
                                            </td>
                                            <td><label>Type of Expense:<span class="text-red">*</span></label>
                                                <select name="expense_type" required>
                                                    <option value="">Select Payment Expense Type</option>
                                                    <?php
                                                    $rb_payment_options = get_option('rb_payment_options', []);
                                                    foreach ($rb_payment_options['rb_expense_type'] as $value) {
                                                        $selected = $payment_voucher->expense_type == $value ? 'selected' : '';

                                                        echo "<option value='{$value}' $selected>{$value}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td colspan="4"><label>The sum of Amount:</label>
                                                <input type=text name="word" id="word"  placeholder="The sum of Dhs" value="<?= $payment_voucher->word ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Paid To:<span class="text-red">*</span></label>
                                                <input type=text id='paid_to' name='paid_to' value="<?= $payment_voucher->paid_to ?>" />
                                            </td>

                                            <td><label>Payment Method:<span class="text-red">*</span></label>
                                                <select name="payment_method" id="payment_method" required>
                                                    <option value="">Select Payment Method</option>
                                                    <option value="Cash" <?= $payment_voucher->payment_method == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                                    <option value="Check" <?= $payment_voucher->payment_method == 'Check' ? 'selected' : '' ?>>Check</option>
                                                    <option value="Card" <?= $payment_voucher->payment_method == 'Card' ? 'selected' : '' ?>>Card</option>
                                                    <option value="Bank Transfer" <?= $payment_voucher->payment_method == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                                    <option value="Bank Deposit" <?= $payment_voucher->payment_method == 'Bank Deposit' ? 'selected' : '' ?>>Bank Deposit</option>
                                                </select>
                                            </td>
                                            <td colspan="2"><label>Payment Date:<span class="text-red">*</span></label><br/>
                                                <input type="date" name="payment_date"  placeholder="Payment Date" rows="5" required value="<?= $payment_voucher->payment_date ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Check / Card Approval No. / Transfer No:</label>
                                                <input type=text name="check_no"  placeholder="Check No" rows="5" value="<?= $payment_voucher->check_no ?>">
                                            </td>
                                            <td>
                                                <div class="check-detail">
                                                    <?php if ($payment_voucher->payment_method == 'Check') { ?>
                                                        <label>Image:</label><br/>
                                                        <input id="check_image" class="button-primary" type="button" value="Upload Check Scan Copy" /><br/>
                                                        <output id="check-image"
                                                                ><?= !empty($payment_voucher->check_image) ? "<input type='hidden' name='image' value='$payment_voucher->check_image'/>" : "" ?>
                                                        </output>
                                                        <?= wp_get_attachment_image(!empty($payment_voucher) ? $payment_voucher->check_image : '', 'large') ?>
                                                        <?php
                                                    } else {
                                                        echo "<input type='hidden' name='image' value='0'/>";
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td  colspan="2" style="vertical-align:top">
                                                <div class="check-detail">
                                                    <?php if ($payment_voucher->payment_method == 'Check') { ?>
                                                        <label>Check Date:<span class="text-red">*</span></label>
                                                        <input type="date" name='check_date' value="<?= $payment_voucher->check_date ?>" required>
                                                        <?php
                                                    } else {
                                                        echo "<input type='hidden' name='check_date' value=''/>";
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Source of Payment:</label>
                                                <select id="payment_source" >
                                                    <option value="">Source of Payment</option>
                                                    <?php
                                                    foreach (PAYMENT_SOURCE as $value) {
                                                        $selected = $payment_voucher->payment_source == $value ? 'selected' : '';
                                                            echo "<option value='$value' $selected class='".(str_replace(' ','',$value))."' disabled>$value</option>";                                                        
                                                    }
                                                    ?>
                                                </select>
                                            </td>

                                            <td><label>Accountant:<span class="text-red">*</span></label>
                                                <input type=text name="accountant"  placeholder="Accountant" rows="5" required value="<?= $payment_voucher->accountant ?>" />
                                            </td>
                                            <td colspan="2"><label>Manager:</label>
                                                <input type=text name="manager"  placeholder="Manager" rows="5" value="<?= $payment_voucher->manager ?>">
                                            </td>
                                        </tr>
                                        <tr>


                                            <td><label>Receiver:<span class="text-red">*</span></label>
                                                <input type=text name="receiver"  placeholder="Receiver" rows="5" required value="<?= $payment_voucher->receiver ?>" />
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><label>Being:</label>
                                                 <textarea id="being" name="being" rows="4" ><?= $payment_voucher->being ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><label>Note:</label>
                                                <input type=text name="note" value="<?= $payment_voucher->note ?>" />
                                            </td>
                                        </tr>
                                        <?php if (has_this_role('accounts')) { ?>
                                            <tr>
                                                <td colspan="4">
                                                    <br/><input type="submit"  name="action" value="Update" class="button-primary"  >
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="admin.php?page=payment-voucher"  class="button-secondary" >Back</a></td>
                                            </tr>
                                        <?php } ?>

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

            jQuery('#add-new-item').click(() => {
                jQuery('#add-new-item-form').toggleClass('hide');
                jQuery('#page-inner-content').toggleClass('hide');
            });

            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            show_currency();
            jQuery('.currency').click(function () {
                show_currency();
            });

            jQuery('#amount').on('input blur', function () {
                //calculate_payment(<?= !empty($payment_voucher) ? $payment_voucher->amount : 0.00 ?>, jQuery('#amount').val());
                //number_to_word('#word', jQuery('#amount').val());

            });
            if (jQuery('#amount').val() !== '') {
                //number_to_word('#word', jQuery('#amount').val());
            }

//            jQuery('#being_html').on('keyup blur', function () {
//                jQuery('#being').val(jQuery(this).html());
//            });

            var payment_method = '<?= $payment_voucher->payment_method ?>';
            payment_method !== 'Check' ? jQuery('.check-detail').hide() : '';

            jQuery('#payment_method').change(() => {
                var method = jQuery('#payment_method').val();
                if (method === 'Check') {
                    jQuery('.check-detail').show();
                    jQuery('#check_date').prop('required', true);
                } else {
                    jQuery('.check-detail').hide();
                    jQuery('#check_date').removeAttr('required');
                }
            });
        });


        function show_currency(){
            var currency = jQuery('input[name="currency"]:checked').val();
            if (currency === 'AED') {
                jQuery('.ENBD-AED,.CreditCard').show();
                jQuery('.ENBD-EUR,.ENBD-USD,.CICParis-EUR').hide();
            }
            if (currency === 'EURO') {
                jQuery('.ENBD-EUR,.CICParis-EUR').show();
                jQuery('.ENBD-AED,.CreditCard,.ENBD-USD').hide();
            }
            if (currency === 'USD') {
                jQuery('.ENBD-USD').show();
                jQuery('.ENBD-EUR,.CICParis-EUR,.ENBD-AED,.CreditCard').hide();
            }
        }

        function calculate_payment(total_amount, pay_amount) {

        }

        function number_to_word(id, amount) {
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/number-to-word.php",
                cache: false,
                method: 'get',
                data: {amount: amount},
                success: function (word) {
                    jQuery(id).val(word);
                }
            });
        }


        jQuery('#check_image').click(function (e) {
            file_uploader(e, 'image', 'check-image', false);
        });

        function file_uploader(e, input, output, multiple) {
            var custom_uploader;
            e.preventDefault();
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: multiple
            });
            custom_uploader.on('select', function () {
                var selection = custom_uploader.state().get('selection');
                var attachment_ids = selection.map(function (attachment) {
                    attachment = attachment.toJSON();
                    if (multiple == false) {
                        jQuery('#' + output).html('');
                    }
                    jQuery('#' + output).append("<input type='hidden' name='" + input + "' value='" + attachment.id + "'><img src='" + attachment.url + "' style='width:250px'>");
                }).join();
            });
            custom_uploader.open();
        }


    </script>
    <?php
}
