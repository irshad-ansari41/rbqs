<?php

function admin_ctm_petty_cash_edit_page() {
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

        $data = ['transaction_date' => $postdata['transaction_date'], 'bank_date' => $postdata['bank_date'], 'particulars' => $postdata['particulars'], 'note' => $postdata['note'], 'updated_by' => $current_user->ID, 'updated_at' => $date];

        $wpdb->update("{$wpdb->prefix}ctm_bank_transactions", $data, ['id' => $id], wpdb_data_format($data), ['%d']);

        $msg = 1;
    }

    $transaction = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_bank_transactions WHERE id={$id}");
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
        <h1 class="wp-heading-inline">Edit Transaction</h1>
        <a href="<?= 'admin.php?page=bank-account' ?>" class="page-title-action btn-primary">Back</a>
        <br/><br/>
        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Transaction has been updated successfully
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
                                    <input type=hidden name="id" value="<?= $id ?>" />
                                    <table class="form-table">
                                        <tr>
                                            <td><label>Transaction Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name='transaction_date'  value="<?= $transaction->transaction_date ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Payment Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name='bank_date'  value="<?= $transaction->bank_date ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Particulars:<span class="text-red">*</span></label>
                                                <input type=text name='particulars' value="<?= $transaction->particulars ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Voucher Type:<span class="text-red">*</span></label>
                                                <input type=text readonly value="<?= $transaction->voucher_type ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Voucher No:<span class="text-red">*</span></label>
                                                <input type=text readonly value="<?= $transaction->voucher_no ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Note:</label><br/>
                                                <input type="text" name='note' value="<?= $transaction->note ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <br/><input type="submit"  name="action" value="Update" class="button-primary"  >
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
        });
    </script>
    <?php
}
