<?php

function admin_ctm_receipt_settle_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    $id = !empty($getdata['old_id']) ? $getdata['old_id'] : '';

    if (!empty($id)) {
        $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE id={$id}");
        $qid = $receipt->quotation_id;
        $revised_no = $receipt->revised_no;
        $qtn = $revised_no ? $revised_no : $qid;
        $avd_amount = get_qtn_paid_amount($receipt->quotation_id);
        $balanceAmount = get_qtn_balance_amount($receipt->quotation_id);

        $qtn_being = "Quotation <b>{$qtn}</b> Total Purchased Amount: <b>" . number_format($receipt->total_amount, 2) . "</b>, Last Payment: <b>" . number_format($avd_amount, 2) . "</b> Non-refundable Amount Received: <b>" . number_format($balanceAmount, 2) . "</b>,\n Balance Amount Receivable <b>0.00</b> against QTN # <b>{$qtn}</b>";

        $adv_amount = !empty($postdata['avd_amount']) ? $postdata['avd_amount'] : $avd_amount;
        echo $total_amount = !empty($postdata['total_amount']) ? $postdata['total_amount'] : $receipt->total_amount;
        $paid_amount = !empty($postdata['paid_amount']) ? $postdata['paid_amount'] : $balanceAmount;

        $balance_amount = !empty($postdata['balance_amount']) ? $postdata['balance_amount'] : 0.0;
        $paid_percent = !empty($postdata['paid_percent']) ? $postdata['paid_percent'] : 0.0;
        $word = !empty($postdata['word']) ? $postdata['word'] : '';
        $received_from = !empty($postdata['received_from']) ? $postdata['received_from'] : $receipt->received_from;
        $payment_method = !empty($postdata['payment_method']) ? $postdata['payment_method'] : '';
        $payment_date = !empty($postdata['payment_date']) ? $postdata['payment_date'] : '';
        $check_no = !empty($postdata['check_no']) ? $postdata['check_no'] : '';
        $check_image = !empty($postdata['image']) ? $postdata['image'] : '';
        $check_date = !empty($postdata['check_date']) ? $postdata['check_date'] : '';
        $bank = !empty($postdata['bank']) ? $postdata['bank'] : '';
        $being = !empty($postdata['being']) ? $postdata['being'] : $qtn_being;
        $note = !empty($postdata['note']) ? $postdata['note'] : '';
        $accountant = !empty($postdata['accountant']) ? $postdata['accountant'] : '';
        $action = !empty($postdata['action']) ? $postdata['action'] : '';


        if ($action == 'Create') {

            $data = ['quotation_id' => $qid, 'revised_no' => $revised_no, 'client_id' => $receipt->client_id,'receipt_type' => 'Settle', 'total_amount' => $total_amount, 'paid_amount' => $paid_amount, 'adv_amount' => $adv_amount, 'balance_amount' => $balance_amount, 'paid_percent' => $paid_percent, 'word' => $word, 'received_from' => $received_from, 'payment_method' => $payment_method, 'payment_date' => $payment_date, 'check_no' => $check_no, 'check_image' => $check_image, 'check_date' => $check_date, 'bank' => $bank, 'being' => $being, 'note' => $note, 'accountant' => $accountant, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

            $wpdb->insert("{$wpdb->prefix}ctm_receipts", $data, wpdb_data_format($data));

            $receipt_no = $wpdb->insert_id;
            
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET receipt_no = '{$receipt_no}' WHERE id='{$qid}'");

            create_bank_deposit($receipt_no);

            wp_redirect("admin.php?page={$getdata['page']}&msg=created");
            exit();
        }
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
        <h1 class="wp-heading-inline">Settle Receipt</h1>
        <br/><br/>
        <?php if (!empty($getdata['msg'])) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Receipt has been created successfully
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
                                    <table class="form-table" style="width:100%">
                                        <tr>
                                        <tr>
                                            <td><label class="red-color">Amount:<span class="text-red">*</span></label>
                                                <input type=hidden name="total_amount" value="<?= !empty($total_amount) ? $total_amount : 0 ?>">
                                                <input type=text name="paid_amount" id="paid_amount" class="red-color" placeholder="Paid Amount" required 
                                                       value="<?= !empty($paid_amount) ? $paid_amount : 0 ?>">
                                                <input type=hidden name="balance_amount" id="balance_amount" value="<?= !empty($balance_amount) ? $balance_amount : 0 ?>">
                                                <input type=hidden name="paid_percent" id="paid_percent" value="<?= !empty($paid_percent) ? $paid_percent : 0 ?>">
                                                <input type=hidden name="avd_amount" value="<?= !empty($avd_amount) ? $avd_amount : 0 ?>">
                                            </td>

                                            <td><label>The sum of Dhs:</label>
                                                <input type=text name="word" id="word"  placeholder="The sum of Dhs" readonly value="<?= !empty($word) ? $word : '' ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Received From:<span class="text-red">*</span></label>
                                                <input type=text name="received_from"  placeholder="Received From" rows="5" required value="<?= !empty($received_from) ? $received_from : '' ?>" />
                                            </td>

                                            <td><label>Payment Method:<span class="text-red">*</span></label>
                                                <select name="payment_method" id="payment_method" required>
                                                    <option value="">Select Payment Method</option>
                                                    <?php
                                                    foreach (PAYMENT_METHOD as $value) {
                                                        echo "<option value='$value'> $value</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="check-detail">
                                                    <label>Image:</label><br/>
                                                    <input id="check_image" class="button-primary" type="button" value="Upload Check Scan Copy" /><br/>
                                                    <output id="check-image"></output>
                                                </div>
                                            </td>
                                            <td style="vertical-align:top">
                                                <div class="check-detail">
                                                    <label>Check Date:<span class="text-red">*</span></label><br/>
                                                    <input type="date" name='check_date' id='check_date' >
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Payment Date:<span class="text-red">*</span></label><br/>
                                                <input type="date" name="payment_date"  placeholder="Payment Date" rows="5" required value="<?= !empty($payment_date) ? $payment_date : '' ?>">
                                            </td>

                                            <td><label>Check / Card Approval No. / Transfer No:</label>
                                                <input type=text name="check_no"  placeholder="Check No" rows="5" value="<?= !empty($check_no) ? $check_no : '' ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Bank:</label>
                                                <input type=text name="bank"  placeholder="Bank" rows="5" value="<?= !empty($bank) ? $bank : '' ?>">
                                            </td>

                                            <td><label>Accountant:<span class="text-red">*</span></label>
                                                <input type=text name="accountant"  placeholder="Accountant" rows="5" required value="<?= !empty($accountant) ? $accountant : $current_user->display_name ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Being:</label>
                                                <div id="being_html" contenteditable="true"><?= !empty($being) ? $being : '' ?></div>
                                                <input type=hidden id="being" name="being" value="<?= !empty($being) ? $being : '' ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Note:</label>
                                                <input type=text name="note" value="<?= !empty($note) ? $note : '' ?>" />
                                            </td>
                                        </tr>
                                        <?php if (!empty($paid_amount)) { ?>
                                            <tr>
                                                <td colspan="2">
                                                    <br/><input type="submit"  name="action" value="Create" class="button-primary"  >
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="?page=receipt"  class="button-secondary" >Cancel</a></td>
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
                calculate_payment(<?= $total_amount ?>, <?= $avd_amount ?>, jQuery('#paid_amount').val());
                number_to_word('#word', jQuery('#paid_amount').val());

            });
            if (jQuery('#paid_amount').val() !== '') {
                number_to_word('#word', jQuery('#paid_amount').val());
            }
            jQuery('#being_html').on('keyup blur', function () {
                jQuery('#being').val(jQuery(this).html());
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
        });

        function currencyFormat(num) {
            return (num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function calculate_payment(total_amount, avd_amount, pay_amount) {
            var totalAmount = parseFloat(total_amount);
            var avdAmount = parseFloat(avd_amount);
            var payAmount = parseFloat(pay_amount);
            var percent = ((avdAmount + payAmount) * 100) / totalAmount;
            var remaing_amount = totalAmount - (avdAmount + payAmount);
            var remaingAmount = remaing_amount;
            var perCent = percent.toFixed(2);

            var being = `Quotation <b><?= $qtn ?></b> Total Purchased Amount: <b>${currencyFormat(totalAmount)}</b>, 
                         Last Payment: <b>${currencyFormat(avdAmount)}</b> Non-refundable Amount Received: <b>${currencyFormat(payAmount)}</b>,\n 
                         Balance Amount Receivable <b>${currencyFormat(remaingAmount)}</b> against QTN # <b><?= $qtn ?></b>`;
            
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
