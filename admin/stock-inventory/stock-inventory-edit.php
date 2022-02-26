<?php

function admin_ctm_stock_inventory_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=stock-inventory'));
        exit();
    }

    if (!empty($postdata['action'])) {

        $data = ['quotation_id' => $postdata['quotation_id'], 'revised_no' => $postdata['revised_no'], 'client_id' => $postdata['client_id'], 'item_id' => $postdata['item_id'], 'entry' => $postdata['entry'], 'quantity' => $postdata['quantity'], 'order_registry' => 'ARRIVED', 'item_desc' => $postdata['item_desc'], 'stk_inv_location' => $postdata['stk_inv_location'], 'stk_inv_status' => $postdata['stk_inv_status'], 'updated_by' => $current_user->ID, 'updated_at' => $date];

        $wpdb->update("{$wpdb->prefix}ctm_quotation_po_meta", $data, ['id' => $id], wpdb_data_format($data), ['%d']);

        $msg = 1;
    }

    $po_meta_item = get_po_meta_data($id);
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
        <h1 class="wp-heading-inline">Edit Stock Inventory</h1>
        <br/><br/>
        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Record has been updated successfully
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
                                            <td><label>Client:</label><br/>
                                                <select name="client_id" id="client-name" class="chosen-select">
                                                    <option value="">Loading...</option>
                                                </select>
                                            </td>

                                            <td><label>QTN No:</label>
                                                <input type=number name="quotation_id" value="<?= $po_meta_item->quotation_id ?>" >
                                            </td>
                                            <td><label>Revised QTN No:</label>
                                                <input type=text name="revised_no" value="<?= $po_meta_item->revised_no ?>" >
                                            </td>


                                        </tr>
                                        <tr>
                                            <td><label>Collection Name:</label><br/>
                                                <?php if (!empty($po_meta_item->item_id)) { ?>
                                                    <input type=text readonly value="<?= get_item($po_meta_item->item_id, 'collection_name') ?>" >
                                                    <input type=hidden name="item_id" value="<?= $po_meta_item->item_id ?>" >
                                                <?php } else { ?>
                                                    <select name="item_id" id="item-name" class="chosen-select">
                                                        <option value="">Loading...</option>
                                                    </select>
                                                <?php } ?>
                                            </td>
                                            <td><label>Entry#:<span class="text-red">*</span></label>
                                                <input type=text name="entry" placeholder="Entry #" required value="<?= $po_meta_item->entry ?>">
                                            </td>
                                            <td><label>Quantity:<span class="text-red">*</span></label>
                                                <input type=number name="quantity" min="0" max="999" required placeholder="Quantity" 
                                                       value="<?= $po_meta_item->quantity ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><label>Item Description#:<span class="text-red">*</span></label><br/>
                                                <textarea name="item_desc" required rows="8"><?= $po_meta_item->item_desc ?></textarea>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td><label>Location:<span class="text-red">*</span></label>
                                                <select name='stk_inv_location' required>
                                                    <option value="">Select Location</option>
                                                    <?php
                                                    foreach (STOCK_LOCATION as $value) {
                                                        $selected = $po_meta_item->stk_inv_location == $value ? 'selected' : '';
                                                        echo "<option value='{$value}' $selected>{$value}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><label>Status:<span class="text-red">*</span></label>
                                                <select name='stk_inv_status' required>
                                                    <option value="">Select Status</option>
                                                    <?php
                                                    foreach (STOCK_STATUS as $value) {
                                                        $selected = $po_meta_item->stk_inv_status == $value ? 'selected' : '';
                                                        echo "<option value='{$value}' $selected>{$value}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td></td>
                                        </tr>


                                        <tr>
                                            <td colspan="4">
                                                <br/><input type="submit"  name="action" value="Update" class="button-primary"  >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="admin.php?page=stock-inventory"  class="button-secondary" >Back</a></td>
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
            jQuery('.chosen-select').chosen();
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($po_meta_item->client_id) ? $po_meta_item->client_id : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Select Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
            });
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-items.php",
                dataType: 'json',
                success: function (data) {
                    var item_id = '<?= !empty($po_meta_item->item_id) ? $po_meta_item->item_id : 0; ?>';
                    jQuery('#item-name').html('');
                    var html = '<option value="">Select Item</option>';
                    jQuery.each(data, function (i, item) {
                        var selected = item_id === item.id ? 'selected' : '';
                        html += `<option value="${item.id}" ${selected}>${item.collection_name}</option>`;
                    });
                    jQuery('#item-name').html(html);
                    jQuery('#item-name').trigger("chosen:updated");
                }
            });
        });

    </script>
    <?php
}
