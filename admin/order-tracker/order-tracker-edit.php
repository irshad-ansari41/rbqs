<?php

function admin_ctm_order_tracker_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=order-tracker'));
        exit();
    }

    if (!empty($postdata)) {

        $data = [
            'quotation_id' => $postdata['quotation_id'],
            'revised_no' => $postdata['revised_no'],
            'client_id' => $postdata['client_id'],
            'item_id' => $postdata['item_id'],
            'item_desc' => $postdata['item_desc'],
            'sup_code' => $postdata['sup_code'],
            'po_id' => $postdata['po_id'],
            'po_date' => $postdata['po_date'],
            'confirmed_price' => $postdata['confirmed_price'],
            'confirmed_quantity' => $postdata['confirmed_quantity'],
            'entry' => $postdata['entry'],
            'no_of_pkgs' => $postdata['no_of_pkgs'],
            'selling_price' => $postdata['selling_price'],
            'container_name' => $postdata['container_name'],
            'confirmation_no' => $postdata['confirmation_no'],
            'dispatch_date' => $postdata['dispatch_date'],
            'delivery_date' => $postdata['delivery_date'],
            'invoice_no' => $postdata['invoice_no'],
            'currency' => $postdata['currency'],
            'cl_value' => $postdata['cl_value'],
            'cl_pkgs' => $postdata['cl_pkgs'],
            'cl_cbm' => $postdata['cl_cbm'],
            'cl_kg' => $postdata['cl_kg'],
            'invoice_amount' => $postdata['invoice_amount'],
            'invoice_date' => $postdata['invoice_date'],
            'due_date' => $postdata['due_date'],
            'air_shipment' => $postdata['air_shipment'],
            'stk_inv_status' => $postdata['stk_inv_status'],
            'updated_at' => $current_user->ID,
            'updated_at' => $date,
        ];

        if (!empty($postdata['update_status']) && $postdata['order_registry'] == 'TRANSIT') {
            $data['cl_status'] = 1; // send to offloading list
        }
        if (!empty($postdata['update_status']) && $postdata['order_registry'] == 'DELIVERED TO FF') {
            $data['cl_status'] = 0; // send to loading list
        }
        if (!empty($postdata['update_status']) && $postdata['order_registry'] == 'ARRIVED') {
            $data['stk_inv_location'] = 'FS'; // send to loading list
            $data['stk_inv_status'] = 'RESERVED'; // send to loading list
            $data['arrival_date'] = $postdata['arrival_date']; // send to loading list
        }
        if (!empty($postdata['update_status'])) {
            $data['order_registry'] = $postdata['order_registry'];
        }
        if (!empty($postdata['validate_price'])) {
            $data['cl_value'] = $postdata['confirmed_price'];
            //$data['quantity'] = $postdata['confirmed_quantity'];
        }

        if ($postdata['new_quantity'] < $postdata['quantity']) {
            stock_inventroy_status_change($id, $postdata['new_quantity'], $postdata['stk_inv_status']);
        }

        $wpdb->update("{$wpdb->prefix}ctm_quotation_po_meta", $data, ['id' => $id], wpdb_data_format($data), ['%d']);

        $msg = 1;
    }

    $po_meta_item = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE id={$id}");
    $item = get_item($po_meta_item->item_id);
    $category = !empty($item) ? get_item_category($item->category, 'name') : '';
    $containers = $wpdb->get_results("SELECT container_name FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name!='' GROUP BY container_name");
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
        <h1 class="wp-heading-inline">Edit Order Tracker</h1>
        <a href="admin.php?page=order-tracker"  class="page-title-action" >Back</a>
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
                        <div id="welcome-to-aquila" class="postbox">
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="add-new-item-form"  method="post">
                                    <input type=hidden name="page"  value="<?= $getdata['page'] ?>" >
                                    <input type=hidden name="id" value="<?= $id ?>" />
                                    <table class="form-table">
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Client Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Client:</label><br/>
                                                <select name="client_id" id="client-name" class="chosen-select">
                                                    <option value="">Loading...</option>
                                                </select>
                                            </td>

                                            <td><label>QTN:</label>
                                                <input type=number name="quotation_id" value="<?= $po_meta_item->quotation_id ?>" >
                                            </td>

                                            <td><label>Revised QTN No:</label>
                                                <input type=text name="revised_no" value="<?= $po_meta_item->revised_no ?>" >
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Order Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Collection Name:</label><br/>
                                                <select name="item_id" id="item-name" class="chosen-select">
                                                    <option value="">Loading...</option>
                                                </select>
                                                <?php /* if (!empty($po_meta_item->item_id)) { ?>
                                                  <input type=text readonly value="<?= get_item($po_meta_item->item_id, 'collection_name') ?>" >
                                                  <input type=hidden name="item_id" value="<?= $po_meta_item->item_id ?>" >
                                                  <?php } else { ?>
                                                  <select name="item_id" id="item-name" class="chosen-select">
                                                  <option value="">Loading...</option>
                                                  </select>
                                                  <?php } */ ?>
                                            </td>
                                            <td><label>Item Category:</label>
                                                <input type=text id="item_category" readonly value="<?= $category ?>" >
                                            </td>

                                            <td><label>Supplier Code:</label>
                                                <input type=text name="sup_code" id="sup_code" readonly value="<?= $po_meta_item->sup_code ?>" >
                                            </td>
                                            <td></td>

                                        </tr>
                                        <tr>
                                            <td colspan="4"><label>Description:</label><br/>
                                                <textarea name="item_desc" id="item_desc" rows="12"><?= $po_meta_item->item_desc ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><label>PO#:</label>
                                                <input type=number name="po_id" value="<?= $po_meta_item->po_id ?>">
                                            </td>
                                            <td><label>PO Date:</label><br/>
                                                <input type=date name="po_date" value="<?= rb_date($po_meta_item->po_date, 'Y-m-d') ?>" />
                                            </td>
                                            <td>
                                                <label>Confirmed Quantity:</label><br/>
                                                <input type=number min="0" name="confirmed_quantity" value="<?= $po_meta_item->confirmed_quantity ?>" />
                                            </td>
                                            <td>
                                                <label>Confirmed Price:</label><br/>
                                                <input type=number min="0" step="0.01" name="confirmed_price" value="<?= $po_meta_item->confirmed_price ?>" />
                                            </td>

                                        </tr>

                                        <tr>

                                            <td><label>Confirmation No:</label>
                                                <input type="text" name="confirmation_no" value="<?= $po_meta_item->confirmation_no ?>">
                                            </td>
                                            <td><label>Dispatch Date:</label><br/>
                                                <input type="date" name="dispatch_date" value="<?= $po_meta_item->dispatch_date ?>">
                                            </td>
                                            <td><label>Delivery Date To FF:</label><br/>
                                                <input type="date" name="delivery_date" value="<?= $po_meta_item->delivery_date ?>">
                                            </td>
                                            <td><br/><br/>
                                                <input type="submit"  name="validate_price" value="Validate" class="button-primary"  onclick='return confirm(`are you sure you want to validate price?`)' >
                                            </td>


                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Item Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Entry #:</label>
                                                <input type=text name="entry" value="<?= $po_meta_item->entry ?>" >
                                            </td>
                                            <td><label>No of packages:</label><br/>
                                                <input type=number name="no_of_pkgs" value="<?= $po_meta_item->no_of_pkgs ?>">
                                            </td>
                                            <td><label>Invoice No:</label>
                                                <input type=text name="invoice_no" value="<?= $po_meta_item->invoice_no ?>" >
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><label>Invoice Date:</label><br/>
                                                <input type=date name="invoice_date" value="<?= $po_meta_item->invoice_date ?>" >
                                            </td>
                                            <td><label>Invoice Due Date:</label><br/>
                                                <input type=date name="due_date" value="<?= $po_meta_item->due_date ?>" >
                                            </td>
                                            <td colspan="2"><label>Currency:</label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="AED" 
                                                           <?= $po_meta_item->currency == 'AED' ? 'checked' : '' ?>>AED</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="EURO" 
                                                           <?= $po_meta_item->currency == 'EURO' ? 'checked' : '' ?>>EURO(€)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="USD"  
                                                           <?= $po_meta_item->currency == 'USD' ? 'checked' : '' ?>>USD($)</label><br/>
                                            </td>
                                            <td>
                                                <label>Existing Quantity:</label><br/>
                                                <input type=number min="0" name="quantity" value="<?= $po_meta_item->quantity ?>" readonly />
                                            </td>
                                        </tr>
                                        <tr>

                                            <td>
                                                <label>New Quantity:</label><br/>
                                                <input type=number min="1" name="new_quantity" value="<?= $po_meta_item->quantity ?>" />
                                                <input type=hidden name="stk_inv_status" value="<?= $po_meta_item->stk_inv_status ?>" />
                                            </td>
                                            <td><label>Item Cost Price:</label>
                                                <input type=number name="cl_value" step="0.01" value="<?= $po_meta_item->cl_value ?>" >
                                            </td>
                                            <td><label>Selling Price:</label>
                                                <input type=number name="selling_price" step="0.01" value="<?= $po_meta_item->selling_price ?>" >
                                            </td>
                                            <td><label>Container Name:</label>
                                                <select name="container_name">
                                                    <option value="">Select Container</option>
                                                    <?php
                                                    foreach ($containers as $value) {
                                                        $selected = $value->container_name == $po_meta_item->container_name ? 'selected' : '';
                                                        echo "<option value='{$value->container_name}' $selected>{$value->container_name}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Entry Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>CBM:</label><br/>
                                                <input type=number name="cl_cbm" step="0.01" min='0' value="<?= $po_meta_item->cl_cbm ?>" >
                                            </td>
                                            <td><label>Total Weight:</label><br/>
                                                <input type=number name="cl_kg" step="0.01" min='0' value="<?= $po_meta_item->cl_kg ?>" >
                                            </td>
                                            <td><label>Total Packages:</label><br/>
                                                <input type=number name="cl_pkgs" min='0' value="<?= $po_meta_item->cl_pkgs ?>" >
                                            </td>


                                        </tr>
                                        <tr>
                                            <td><label>Total Invoice Amount:</label><br/>
                                                <input type=number name="invoice_amount" step="0.01" min='0' value="<?= $po_meta_item->invoice_amount ?>" >
                                            </td>
                                            <td><label>Status:<span class="text-red">*</span></label>
                                                <select name="order_registry">
                                                    <option value='Pending' >Pending</option>
                                                    <option value='CANCELLED' <?= $po_meta_item->order_registry == 'CANCELLED' ? 'selected' : '' ?> >CANCELLED</option>
                                                    <option value='CONFIRMED' <?= $po_meta_item->order_registry == 'CONFIRMED' ? 'selected' : '' ?> >CONFIRMED</option>
                                                    <option value='DELIVERED TO FF' <?= $po_meta_item->order_registry == 'DELIVERED TO FF' ? 'selected' : '' ?> >DELIVERED TO FF</option>
                                                    <option value='TRANSIT' <?= $po_meta_item->order_registry == 'TRANSIT' ? 'selected' : '' ?> >TRANSIT</option>
                                                    <option value='ARRIVED' <?= $po_meta_item->order_registry == 'ARRIVED' ? 'selected' : '' ?>  
                                                            <?= $po_meta_item->air_shipment ? '' : 'disabled' ?>>ARRIVED</option>
                                                </select>
                                                <?php
                                                if ($po_meta_item->order_registry == 'ARRIVED') {
                                                    echo "<input type=hidden name=order_registry value=ARRIVED />";
                                                }
                                                ?>
                                            </td>
                                            <td><label>Is Air Shipment:</label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="air_shipment" value="1" onclick="this.form.submit()"
                                                           <?= $po_meta_item->air_shipment == 1 ? 'checked' : '' ?>>Yes</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="air_shipment" value="0"  onclick="this.form.submit()"
                                                           <?= $po_meta_item->air_shipment == 0 ? 'checked' : '' ?>>No</label><br/>
                                            </td>                                                
                                            <td>
                                                <?php if ($po_meta_item->air_shipment) { ?>
                                                    <label>ETA Date:<span class="text-red">*</span></label><br/>
                                                    <input type="date" name="arrival_date" value="<?= $po_meta_item->arrival_date ?>" required />
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <label>Stock Status:<span class="text-red">*</span></label><br/>
                                                <select name="stk_inv_status">
                                                    <option value='' >Stock Status</option>
                                                    <?php
                                                    foreach (STOCK_STATUS as $value) {
                                                        $selected = $po_meta_item->stk_inv_status == $value ? 'selected' : '';
                                                        echo "<option value='$value' $selected>$value</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>

                                        </tr>

                                        <tr>
                                            <td colspan="4">
                                                <br/><input type="submit"  name="action" value="Update" class="button-primary"  >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="submit"  name="update_status" value="Update Status" class="button-primary"  onclick='return confirm(`are you sure you want to update status?`)' >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="admin.php?page=order-tracker"  class="button-secondary" >Back</a></td>
                                        </tr>

                                    </table>
                                    <br/>
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
                    var item_id = '<?= !empty($po_meta_item->item_id) ? $po_meta_item->item_id : 0 ?>';
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

            jQuery('#item-name').change(function(){
                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/get-item.php",
                    cache: false,
                    method: 'get',
                    data: {id: jQuery(this).val()},
                    dataType: 'json',
                    success: function (item) {
                        //jQuery(`#item_desc`).val(`${item.description}`);
                        jQuery(`#item_category`).val(`${item.category_name}`);
                        jQuery(`#sup_code`).val(`${item.sup_code}`);
                    }
                });
            });
        });

    </script>
    <?php
}
