<?php

function admin_ctm_stock_inventory_add_page() {
    global $wpdb, $current_user;
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    if (!empty($postdata['action'])) {

        $item = get_item($postdata['item_id']);

        $data = ['quotation_id' => $postdata['quotation_id'],'revised_no'=>$postdata['revised_no'], 'client_id' => $postdata['client_id'], 'item_id' => $postdata['item_id'], 'entry' => $postdata['entry'],'quantity' => $postdata['quantity'], 'order_registry' => 'ARRIVED', 'item_desc' => $item->description, 'sup_code' => $item->sup_code, 'stk_inv_location' => $postdata['stk_inv_location'], 'stk_inv_status' => $postdata['stk_inv_status'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_quotation_po_meta", array_map('trim', $data), wpdb_data_format($data));
        $id = $wpdb->insert_id;
        $msg = 1;
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
        <h1 class="wp-heading-inline">Add Stock Item</h1>
        <br/><br/>
        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Stock ID <?= $id ?> has been added successfully.
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
                                    <input type=hidden name="id" value="<?= $id ?>" />
                                    <table class="form-table" style="">
                                        <tr>
                                            <td><label>Client:<span class="text-red">*</span></label><br/>
                                                <select name="client_id" id="client-name" class="chosen-select" required>
                                                    <option value="">Loading...</option>
                                                </select>
                                            </td>
                                            <td><label>QTN:</label>
                                                <input type=number name="quotation_id" placeholder="QTN No" >
                                            </td>
                                            <td><label>Revised QTN No:</label>
                                                <input type=text name="revised_no" placeholder="Revised QTN No" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Collection Name:<span class="text-red">*</span></label><br/>                                                                                  <select name="item_id" id="item-name" class="chosen-select" required>
                                                    <option value="">Loading...</option>
                                                </select>
                                            </td>
                                            <td><label>Entry#:<span class="text-red">*</span></label>
                                                <input type=text name="entry" placeholder="Entry #" required>
                                            </td>
                                            <td><label>Quantity:<span class="text-red">*</span></label>
                                                <input type=number name="quantity" min="1" max="999" required placeholder="Quantity" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Location:<span class="text-red">*</span></label>
                                                <select name='stk_inv_location' required>
                                                    <option value="">Select Location</option>
                                                    <?php
                                                    foreach (STOCK_LOCATION as $value) {
                                                        echo "<option value='{$value}'>{$value}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><label>Status:<span class="text-red">*</span></label>
                                                <select name='stk_inv_status' required>
                                                    <option value="">Select Status</option>
                                                    <?php
                                                    foreach (STOCK_STATUS as $value) {
                                                        $selected = $value=='AVAILABLE'?'selected':'';
                                                        echo "<option value='{$value}' $selected>{$value}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br/><input type="submit"  name="action" value="Add" class="button-primary"  >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="admin.php?page=stock-inventory"  class="button-secondary" >Back</a>
                                            </td>
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
                    jQuery('#client-name').html('');
                    var html = '<option value="">Select Client</option>';
                    jQuery.each(data, function (i, client) {
                        html += `<option value="${client.id}">${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
            });
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-items.php",
                dataType: 'json',
                success: function (data) {
                    jQuery('#item-name').html('');
                    var html = '<option value="">Select Item</option>';
                    jQuery.each(data, function (i, item) {
                        html += `<option value="${item.id}">${item.collection_name}</option>`;
                    });
                    jQuery('#item-name').html(html);
                    jQuery('#item-name').trigger("chosen:updated");
                }
            });
        });

    </script>
    <?php
}
