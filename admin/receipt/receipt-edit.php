<?php

function admin_ctm_receipt_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=receipt'));
        exit();
    }

    if (!empty($postdata['action'])) {
        $paid_amount = !empty($postdata['paid_amount']) ? $postdata['paid_amount'] : 0.0;
        $balance_amount = !empty($postdata['balance_amount']) ? $postdata['balance_amount'] : 0.0;
        $paid_percent = !empty($postdata['paid_percent']) ? $postdata['paid_percent'] : 0.0;
        $word = !empty($postdata['word']) ? $postdata['word'] : '';
        $received_from = !empty($postdata['received_from']) ? $postdata['received_from'] : '';
        $payment_method = !empty($postdata['payment_method']) ? $postdata['payment_method'] : '';
        $payment_date = !empty($postdata['payment_date']) ? $postdata['payment_date'] : '';
        $check_no = !empty($postdata['check_no']) ? $postdata['check_no'] : '';
        $check_image = !empty($postdata['image']) ? $postdata['image'] : '';
        $check_date = !empty($postdata['check_date']) ? $postdata['check_date'] : '';
        $bank = !empty($postdata['bank']) ? $postdata['bank'] : '';
        $being = !empty($postdata['being']) ? $postdata['being'] : '';
        $note = !empty($postdata['note']) ? $postdata['note'] : '';
        $accountant = !empty($postdata['accountant']) ? $postdata['accountant'] : '';

        $data = ['paid_amount' => $paid_amount, 'balance_amount' => $balance_amount, 'paid_percent' => $paid_percent, 'word' => $word, 'received_from' => $received_from, 'payment_method' => $payment_method, 'payment_date' => $payment_date, 'check_no' => $check_no, 'check_image' => $check_image, 'check_date' => $check_date, 'bank' => $bank, 'being' => $being, 'note' => $note, 'accountant' => $accountant, 'updated_by' => $current_user->ID, 'updated_at' => $date];

        $wpdb->update("{$wpdb->prefix}ctm_receipts", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%d']);

        wp_redirect("admin.php?page={$getdata['page']}&id=$id&msg=updated");
        exit();
    }

    $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE id={$id}");
    $qtn = !empty($receipt->revised_no) ? $receipt->revised_no : $receipt->quotation_id;

    $adv_amount = !empty($receipt->adv_amount) ? $receipt->adv_amount : 0.00;
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
        <h1 class="wp-heading-inline">Edit Receipt</h1>
        <br/><br/>
        <?php if (!empty($getdata['msg'])) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Receipt has been updated successfully
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
                                                <input type=text name="id"  placeholder="Receipt No"  readonly value="<?= !empty($receipt) ? $receipt->id : '' ?>" required>
                                            </td>

                                            <td><label class="red-color">Amount:<span class="text-red">*</span></label>
                                                <input type=hidden name="total_amount" value="<?= !empty($receipt) ? $receipt->total_amount : '' ?>">
                                                <input type=text name="paid_amount" id="paid_amount" class="red-color" placeholder="Paid Amount" required 
                                                       value="<?= !empty($receipt) ? $receipt->paid_amount : '' ?>">
                                                <input type=hidden name="balance_amount" id="balance_amount" value="<?= !empty($receipt) ? $receipt->balance_amount : '' ?>">
                                                <input type=hidden name="paid_percent" id="paid_percent" value="<?= !empty($receipt) ? $receipt->paid_percent : '' ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>The sum of Dhs:</label>
                                                <input type=text name="word" id="word"  placeholder="The sum of Dhs" readonly value="<?= !empty($receipt) ? $receipt->word : '' ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Received From:<span class="text-red">*</span></label>
                                                <input type=text name="received_from"  placeholder="Received From" rows="5" required value="<?= !empty($receipt) ? $receipt->received_from : '' ?>" />
                                            </td>

                                            <td><label>Payment Method:<span class="text-red">*</span></label>
                                                <select name="payment_method" id="payment_method" required>
                                                    <option value="">Select Payment Method</option>
                                                    <?php
                                                    foreach (PAYMENT_METHOD as $value) {
                                                        $selected = $receipt->payment_method == $value ? 'selected' : '';
                                                        echo "<option value='$value' $selected> $value</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="check-detail">
                                                    <?php if ($receipt->payment_method == 'Check') { ?>
                                                        <label>Image:</label><br/>
                                                        <input id="check_image" class="button-primary" type="button" value="Upload Check Scan Copy" /><br/>
                                                        <output id="check-image"
                                                                ><?= !empty($receipt->check_image) ? "<input type='hidden' name='image' value='$receipt->check_image'/>" : "" ?>
                                                        </output>
                                                        <?= wp_get_attachment_image(!empty($receipt) ? $receipt->check_image : '', 'large') ?>
                                                        <?php
                                                    } else {
                                                        echo "<input type='hidden' name='image' value='0'/>";
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td style="vertical-align:top">
                                                <div class="check-detail">
                                                    <?php if ($receipt->payment_method == 'Check') { ?>
                                                        <label>Check Date:<span class="text-red">*</span></label><br/>
                                                        <input type="date" name='check_date' id='check_date' value="<?= $receipt->check_date ?>" required>
                                                        <?php
                                                    } else {
                                                        echo "<input type='hidden' name='check_date' value=''/>";
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Payment Date:<span class="text-red">*</span></label><br/>
                                                <input type="date" name="payment_date"  placeholder="Payment Date" rows="5" required value="<?= !empty($receipt) ? $receipt->payment_date : '' ?>">
                                            </td>

                                            <td><label>Check / Card Approval No. / Transfer No:</label>
                                                <input type=text name="check_no"  placeholder="Check No" rows="5" value="<?= !empty($receipt) ? $receipt->check_no : '' ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Bank:</label>
                                                <input type=text name="bank"  placeholder="Bank" rows="5" value="<?= !empty($receipt) ? $receipt->bank : '' ?>">
                                            </td>

                                            <td><label>Accountant:<span class="text-red">*</span></label>
                                                <input type=text name="accountant"  placeholder="Accountant" rows="5" required value="<?= !empty($receipt) ? $receipt->accountant : '' ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Being:</label>
                                                <div id="being_html" contenteditable="true"><?= !empty($receipt) ? $receipt->being : '' ?></div>
                                                <input type=hidden id="being" name="being" value="<?= !empty($receipt) ? $receipt->being : '' ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Note:</label>
                                                <input type=text name="note" value="<?= !empty($receipt) ? $receipt->note : '' ?>" />
                                            </td>
                                        </tr>
                                        <?php if (has_this_role('accounts')) { ?>
                                            <tr>
                                                <td colspan="2">
                                                    <input type=hidden name="qtn" value="<?= !empty($receipt) ? $receipt->qtn : '' ?>" />
                                                    <br/><input type="submit"  name="action" value="Update" class="button-primary"  >
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="admin.php?page=receipt"  class="button-secondary" >Back</a></td>
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
            jQuery('#paid_amount').on('input blur', function () {
                calculate_payment(<?= !empty($receipt) ? $receipt->total_amount : 0.00 ?>, jQuery('#paid_amount').val());
                number_to_word('#word', jQuery('#paid_amount').val());

            });
            if (jQuery('#paid_amount').val() !== '') {
                number_to_word('#word', jQuery('#paid_amount').val());
            }

            jQuery('#being_html').on('keyup blur', function () {
                jQuery('#being').val(jQuery(this).html());
            });

            var payment_method = '<?= $receipt->payment_method ?>';
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



        function currencyFormat(num) {
            if (num != '' && num != 'undefined') {
                console.log(num);
                return (num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
            return  num;
        }

        function calculate_payment(total_amount, pay_amount) {
            var totalAmount = parseFloat(total_amount);
            var payAmount = parseFloat(pay_amount);
            var adv_amount = parseFloat('<?= $adv_amount ?>');

            if (adv_amount == '0.00') {
                var percent = (payAmount * 100) / totalAmount;
                var remaing_amount = totalAmount - payAmount;
                var remaingAmount = remaing_amount;
                var perCent = percent.toFixed(2);
                var being = `Quotation <b><?= $qtn ?></b> Total Purchased Amount: <b>${currencyFormat(totalAmount)}</b>, Non-refundable Amount Received: <b>${currencyFormat(payAmount)}</b>,\n Balance Amount Receivable <b>${currencyFormat(remaingAmount)}</b> against QTN # <b><?= $qtn ?></b>`;
            } else {
                var percent = ((payAmount + adv_amount) * 100) / totalAmount;
                var remaing_amount = totalAmount - (payAmount + adv_amount);
                var remaingAmount = remaing_amount;
                var perCent = percent.toFixed(2);
                var being = `Quotation <b><?= $qtn ?></b> Total Purchased Amount: <b>${currencyFormat(totalAmount)}</b>, Last Payment: <b>${currencyFormat(adv_amount)}</b>, Non-refundable Amount Received: <b>${currencyFormat(payAmount)}</b>,\n Balance Amount Receivable <b>${currencyFormat(remaingAmount)}</b> against QTN # <b><?= $qtn ?></b>`;
            }

            jQuery('#being_html').html(being);
            jQuery('#being').val(being);
            jQuery('#balance_amount').val(remaingAmount);
            jQuery('#paid_percent').val(perCent);
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
