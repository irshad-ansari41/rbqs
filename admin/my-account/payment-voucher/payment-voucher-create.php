<?php

function admin_ctm_payment_voucher_create_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $purchase_voucher_ids = $expense_type = '';

    $invoice_no = '';
    $amount = !empty($getdata['amount']) ? $getdata['amount'] : 0;
    $currency = 'AED';
    $pv_type = 'Other';
    $paid_to = !empty($getdata['paid_to']) ? $getdata['paid_to'] : '';
    $being = '';
    $has_vat = 0;

    if (!empty($getdata['purchase_voucher_id'])) {
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_purchase_vouchers WHERE id IN ('" . implode("', '", $getdata['purchase_voucher_id']) . "')");
        foreach ($results as $value) {
            $amount += $value->amount + $value->vat;
            $invoice_no .= $value->invoice_no . ', ';
            $being .= $value->narration . " \n";
        }
        $paid_to = get_supplier_by_id($value->sup_id, 'name');
        $currency = 'AED';
        $has_vat = rb_float($value->vat) ? 1 : 0;
        $pv_type = 'Supplier';
        $purchase_voucher_ids = implode(', ', $getdata['purchase_voucher_id']);
        $expense_type = $value->expense_type;
    }

    if (!empty($getdata['supplier_purchase_id'])) {
        $sql = "SELECT entry,sup_code,invoice_no,invoice_amount,currency FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE invoice_no  IN ('" . implode("', '", array_keys($getdata['supplier_purchase_id'])) . "') AND sup_code IN ('" . implode("', '", $getdata['supplier_purchase_id']) . "') GROUP BY invoice_no";
        $results = $wpdb->get_results($sql);

        foreach ($results as $value) {
            $amount += $value->invoice_amount;
            $invoice_no .= $value->invoice_no . ', ';

            $being .= $value->invoice_no . " \n";
        }
        $supplier = get_supplier($value->sup_code);
        $paid_to = $supplier->name;
        $currency = $value->currency;
        $has_vat = 0;
        $purchase_voucher_ids = '';
        $pv_type = 'Supplier';
        $expense_type = '';
    }



    if (!empty($postdata['action'])) { 

        $pv_type = !empty($postdata['pv_type']) ? $postdata['pv_type'] : '';
        $ref = !empty($postdata['ref']) ? $postdata['ref'] : '';
        $invoice_no = !empty($postdata['invoice_no']) ? $postdata['invoice_no'] : '';
        $expense_type = !empty($postdata['expense_type']) ? $postdata['expense_type'] : '';
        $purchase_voucher = !empty($postdata['purchase_voucher']) ? $postdata['purchase_voucher'] : '';
        $currency = !empty($postdata['currency']) ? $postdata['currency'] : '';
        $has_vat = !empty($postdata['has_vat']) ? $postdata['has_vat'] : 0;
        $amount = !empty($postdata['amount']) ? $postdata['amount'] : 0;
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

        $data = ['pv_type' => $pv_type, 'ref' => $ref, 'purchase_voucher' => $purchase_voucher, 'invoice_no' => $invoice_no, 'expense_type' => $expense_type, 'currency' => $currency, 'has_vat' => $has_vat, 'amount' => $amount, 'word' => $word, 'paid_to' => $paid_to, 'payment_method' => $payment_method, 'payment_date' => $payment_date, 'check_no' => $check_no, 'check_image' => $check_image, 'check_date' => $check_date, 'payment_source' => $payment_source, 'being' => $being, 'note' => $note, 'accountant' => $accountant, 'manager' => $manager, 'receiver' => $receiver, 'pv_date' => $date, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_payment_vouchers", array_map('trim', $data), wpdb_data_format($data));


        $id = $wpdb->insert_id;

        $withdrawal_id = $id;
        make_debit_transaction($withdrawal_id, $payment_source, $payment_date, $payment_date, $paid_to, $id, $amount);


        if (!empty($getdata['supplier_purchase_id'])) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET pv_no='{$id}' WHERE invoice_no IN ('" . implode("', '", array_keys($getdata['supplier_purchase_id'])) . "') AND  sup_code  IN ('" . implode("', '", $getdata['supplier_purchase_id']) . "')");
        }

        if (!empty($getdata['purchase_voucher_id'])) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_purchase_vouchers SET pv_no='{$id}' WHERE id IN ('" . implode("', '", $getdata['purchase_voucher_id']) . "')");
        }

        wp_redirect("admin.php?page=payment-voucher&msg=created");
        exit();
    }
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
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
        <h1 class="wp-heading-inline">Payment Voucher</h1>
        <a id="add-new-client" href="<?= "admin.php?page=payment-voucher" ?>" class="page-title-action btn-primary" >Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="add-new-item-form" method="post">
                                    <table class="form-table" style="width:100%">
                                        <tr>
                                            <td><label>Payment Voucher Type:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="pv_type" class="pv_type" value="Employee" required <?= $pv_type == 'Employee' ? 'checked' : '' ?>>Employee</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="pv_type"  class="pv_type" value="Supplier" required <?= $pv_type == 'Supplier' ? 'checked' : '' ?>>Supplier</label>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="pv_type"  class="pv_type" value="Other" required <?= $pv_type == 'Other' ? 'checked' : '' ?>>Other</label>



                                            </td>
                                            <td style="min-width: 300px;">
                                                <label>Ref:<span class="text-red">*</span></label>
                                                <input type=text name="ref" placeholder="Ref" value="" required >
                                            </td>
                                            <td><label>Purchase Voucher No:</label>
                                                <input type=text name="purchase_voucher" placeholder="Purchase Voucher No" value="<?= $purchase_voucher_ids ?>"  >
                                            </td>
                                            <td><label>Invoice No:</label>
                                                <input type=text name="invoice_no" placeholder="Invoice No" value="<?= $invoice_no ?>"  >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="red-color">Amount:<span class="text-red">*</span></label>
                                                <input type=number name="amount" step="0.01" min='0' class="red-color" id='amount' value="<?= $amount ?>" placeholder="Amount" required >
                                            </td>
                                            <td style="width:50%"><label>Currency:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" class="currency" value="AED"  required <?= $currency == 'AED' ? 'checked' : '' ?>>AED(د.إ)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" class="currency" value="EURO" required <?= $currency == 'EURO' ? 'checked' : '' ?>>EURO(€)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" class="currency" value="USD" required <?= $currency == 'USD' ? 'checked' : '' ?>>USD($)</label><br/>
                                            </td>
                                            <td style="width:50%"><label>Has VAT:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="has_vat" class="currency" value="1"  required <?= $has_vat == 1 ? 'checked' : '' ?>>With VAT</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="has_vat" class="currency" value="0" required <?= $has_vat == 0 ? 'checked' : '' ?>>Without VAT</label>
                                            </td>
                                            <td><label>Type of Expense:</label>
                                                <select name="expense_type" id="expense_type">
                                                    <option value="">Select Payment Expense Type</option>
                                                    <?php
                                                    $rb_payment_options = get_option('rb_payment_options', []);
                                                    foreach (array_filter($rb_payment_options['rb_expense_type']) as $value) {
                                                        $selected = $expense_type == $value ? 'selected' : '';
                                                        echo "<option value='{$value}' $selected>{$value}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td colspan="4"><label>The sum of Amount:</label>
                                                <input type=text name="word" id="word"  placeholder="The sum of Amount" />
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Paid To:<span class="text-red">*</span></label>
                                                <select name="paid_to" id="paid-to-select" class="chosen-select" required>
                                                </select>
                                                <input type="text" name="paid_to" id="paid-to-input" placeholder="Paid To" value="<?= $paid_to ?>" required />
                                            </td>

                                            <td><label>Payment Method:<span class="text-red">*</span></label>
                                                <select name="payment_method" id="payment_method" required>
                                                    <option value="">Select Payment Method</option>
                                                    <option value="Cash" >Cash</option>
                                                    <option value="Check" >Check</option>
                                                    <option value="Card" >Card</option>
                                                    <option value="Bank Transfer" >Bank Transfer</option>
                                                    <option value="Bank Deposit" >Bank Deposit</option>
                                                </select>
                                            </td>
                                            <td  colspan="2"><label>Payment Date:<span class="text-red">*</span></label><br/>
                                                <input type="date" name="payment_date"  placeholder="Payment Date" rows="5" required >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Check / Card Approval No. / Transfer No:</label>
                                                <input type=text name="check_no"  placeholder="Check No" >
                                            </td>
                                            <td>
                                                <div class="check-detail">
                                                    <label>Image:<span class="text-red">*</span></label><br/>
                                                    <input id="check_image" class="button-primary" type="button" value="Upload Check Scan Copy" /><br/>
                                                    <output id="check-image"></output>
                                                </div>
                                            </td>
                                            <td  colspan="2" style="vertical-align:top">
                                                <div class="check-detail">
                                                    <label>Check Date:<span class="text-red">*</span></label><br/>
                                                    <input type="date" name='check_date' id='check_date' >
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>

                                        </tr>
                                        <tr>
                                            <td><label>Source of Payment:<span class="text-red">*</span></label>
                                                <select name="payment_source" id="payment_source" required>
                                                    <option value="">Source of Payment</option>
                                                    <?php
                                                    foreach (PAYMENT_SOURCE as $value) {
                                                        echo "<option value='$value' class='" . (str_replace(' ', '', $value)) . "' >$value</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>

                                            <td><label>Accountant:<span class="text-red">*</span></label>
                                                <input type=text name="accountant"  placeholder="Accountant"  value="<?= $current_user->display_name ?>" required  />
                                            </td>
                                            <td  colspan="2"><label>Manager:</label><span class="text-red">*</span>
                                                <input type=text name="manager"  placeholder="Manager" required >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Receiver:<span class="text-red">*</span></label>
                                                <input type=text name="receiver"  placeholder="Receiver" required  />
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><label>Being:</label>
                                                <textarea id="being" name="being" rows="4" ><?= $being ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><label>Note:</label>
                                                <input type=text name="note" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <br/><input type="submit"  name="action" value="Create" class="button-primary"  >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="?page=payment-voucher"  class="button-secondary" >Cancel</a></td>
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

            show_paid_to();
            jQuery('.pv_type').click(function () {
                show_paid_to();
            });

            show_currency();
            jQuery('.currency').click(function () {
                show_currency();
            });

            jQuery('.check-detail').hide();
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


            number_to_word('#word', '<?= $amount ?>');

            jQuery('#amount').on('input blur', function () {
                number_to_word('#word', jQuery('#amount').val());

            });
            if (jQuery('#amount').val() !== '') {
                number_to_word('#word', jQuery('#amount').val());
            }
            jQuery('#being_html').on('keyup blur', function () {
                jQuery('#being').val(jQuery(this).html());
            });
        });

        function show_currency() {
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

        function show_paid_to() {
            var pv_type = jQuery('input[name="pv_type"]:checked').val();
            if (pv_type === 'Employee') {
                make_employee_drop_dow();
                jQuery('#paid-to-select').show().attr('required', true).attr('name', 'paid_to');
                jQuery('#paid-to-input').hide().removeAttr('required').removeAttr('name');
            }
            if (pv_type === 'Supplier') {
                make_supplier_drop_dow();
                jQuery('#paid-to-select').show().attr('required', true).attr('name', 'paid_to');
                jQuery('#paid-to-input').hide().removeAttr('required').removeAttr('name');
            }
            if (pv_type === 'Other') {
                jQuery("#paid-to-select").chosen("destroy");
                jQuery('#paid-to-select').hide().removeAttr('required').removeAttr('name');
                jQuery('#paid-to-input').show().attr('required', true).attr('name', 'paid_to');
            }
        }
        function make_employee_drop_dow() {
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-employees.php",
                dataType: 'json',
                success: function (data) {
                    jQuery('#paid-to-select').html('');
                    var html = '<option value="">Select Paid To</option>';
                    jQuery.each(data, function (i, employee) {
                        html += `<option value="${employee.name}">${employee.name}</option>`;
                    });

                    jQuery('#paid-to-select').html(html);
                    jQuery("#paid-to-select").chosen("destroy");
                    jQuery('.chosen-select').chosen();
                    jQuery('#paid-to-select').trigger("chosen:updated");
                }
            });
        }

        function make_supplier_drop_dow() {
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-suppliers.php",
                dataType: 'json',
                success: function (data) {
                    jQuery('#paid-to-select').html('');
                    var html = '<option value="">Select Paid To</option>';
                    jQuery.each(data, function (i, supplier) {
                        html += `<option value="${supplier.name}">${supplier.name}</option>`;
                    });
                    jQuery('#paid-to-select').html(html);
                    jQuery("#paid-to-select").chosen("destroy");
                    jQuery('.chosen-select').chosen();
                    jQuery('#paid-to-select').trigger("chosen:updated");
                }
            });
        }

        function calculate_payment(amount, pay_amount) {

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
