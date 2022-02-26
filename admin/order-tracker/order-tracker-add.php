<?php

function admin_ctm_order_tracker_add_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $msg = 0;

    if (!empty($postdata['action'])) {

        $data = [
            'quotation_id' => $postdata['quotation_id'],
            'revised_no' => $postdata['revised_no'],
            'client_id' => $postdata['client_id'],
            'item_id' => $postdata['item_id'],
            'item_desc' => $postdata['item_desc'],
            'sup_code' => $postdata['sup_code'],
            'quantity' => $postdata['quantity'],
            'quantity' => $postdata['quantity'],
            'po_id' => $postdata['po_id'],
            'po_date' => $postdata['po_date'],
            'entry' => $postdata['entry'],
            'no_of_pkgs' => $postdata['no_of_pkgs'],
            'selling_price' => $postdata['selling_price'],
            'container_name' => $postdata['container_name'],
            'confirmation_no' => $postdata['confirmation_no'],
            'order_registry' => $postdata['order_registry'],
            'dispatch_date' => $postdata['dispatch_date'],
            'delivery_date' => $postdata['delivery_date'],
            'invoice_no' => $postdata['invoice_no'],
            'currency' => $postdata['currency'] ?? 'EURO',
            'cl_pkgs' => $postdata['cl_pkgs'],
            'cl_cbm' => $postdata['cl_cbm'],
            'cl_kg' => $postdata['cl_kg'],
            'cl_value' => $postdata['cl_value'],
            'invoice_amount' => $postdata['invoice_amount'],
            'invoice_date' => $postdata['invoice_date'],
            'due_date' => $postdata['due_date'],
            'air_shipment' => $postdata['air_shipment'],
            'updated_at' => $current_user->ID,
            'updated_at' => $date,
        ];

        if ($postdata['order_registry'] == 'TRANSIT') {
            $data['cl_status'] = 1; // send to offloading list
        }
        if ($postdata['order_registry'] == 'DELIVERED TO FF') {
            $data['cl_status'] = 0; // send to loading list
        }

        $wpdb->insert("{$wpdb->prefix}ctm_quotation_po_meta", $data, wpdb_data_format($data));

        $id = $wpdb->insert_id;

        $msg = 1;
    }
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
        <h1 class="wp-heading-inline">Add Order Tracker</h1>
        <a href="admin.php?page=order-tracker"  class="page-title-action" >Back</a>
        <br/><br/>
        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                #<?= $id ?> Record has been added successfully.
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
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Client Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Client:</label><br/>
                                                <select name="client_id" id="client-name" class="chosen-select">
                                                    <option >Loading...</option>
                                                </select>
                                            </td>

                                            <td><label>QTN:</label>
                                                <input type=number name="quotation_id"  placeholder="QTN">
                                            </td>

                                            <td><label>Revised QTN No:</label>
                                                <input type=text name="revised_no" placeholder="Revised QTN No">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-red text-uppercase"><br/>Order Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Collection Name:</label><br/>
                                                <select name="item_id" id="item-name" class="chosen-select">
                                                    <option >Loading...</option>
                                                </select>
                                            </td>
                                            <td><label>Item Category:</label>
                                                <input type=text id="item_category" readonly value="" >
                                            </td>
                                            <td><label>Supplier Code:</label>
                                                <input type=text id="sup_code" name="sup_code" readonly >
                                            </td>
                                            <td><label>Quantity:</label>
                                                <input type=number name="quantity" placeholder="Quantity" >
                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="4"><label>Description:</label><br/>
                                                <textarea id="item_desc" name="item_desc" rows="8"></textarea>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><label>PO#:</label>
                                                <input type=number name="po_id" placeholder="PO#" >
                                            </td>
                                            <td><label>PO Date:</label><br/>
                                                <input type=date name="po_date"  />
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>

                                        <tr>

                                            <td><label>Confirmation No:</label>
                                                <input type="text" name="confirmation_no" placeholder="Confirmation No">
                                            </td>
                                            <td><label>Dispatch Date:</label><br/>
                                                <input type="date" name="dispatch_date" >
                                            </td>
                                            <td><label>Delivery Date To FF:</label><br/>
                                                <input type="date" name="delivery_date" >
                                            </td>
                                            <td></td>


                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Item Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Entry #:</label>
                                                <input type=text name="entry" placeholder="Entry #" >
                                            </td>
                                            <td><label>No of packages:</label><br/>
                                                <input type=number name="no_of_pkgs" placeholder="No of packages">
                                            </td>
                                            <td><label>Invoice No:</label>
                                                <input type=text name="invoice_no" placeholder="Invoice No" >
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><label>Invoice Date:</label><br/>
                                                <input type=date name="invoice_date"  >
                                            </td>
                                            <td><label>Invoice Due Date:</label><br/>
                                                <input type=date name="due_date" >
                                            </td>
                                            <td colspan="2"><label>Currency:</label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="AED">AED</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="EURO">EURO(€)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="USD">USD($)</label><br/>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td><label>Item Cost Price:</label>
                                                <input type=number name="cl_value" step="0.01" placeholder="Item Cost Price" >
                                            </td>
                                            <td><label>Selling Price:</label>
                                                <input type=number name="selling_price" step="0.01" placeholder="Selling Price" >
                                            </td>
                                            <td><label>Container Name:</label>
                                                <select name="container_name">
                                                    <option value="">Select Container</option>
                                                    <?php
                                                    foreach ($containers as $value) {
                                                        echo "<option value='{$value->container_name}'>{$value->container_name}</option>";
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
                                                <input type=number name="cl_cbm" step="0.01" min='0'  placeholder="CBM" >
                                            </td>
                                            <td><label>Total Weight:</label><br/>
                                                <input type=number name="cl_kg" step="0.01" min='0' placeholder="Total Weight" >
                                            </td>
                                            <td><label>Total Packages:</label><br/>
                                                <input type=number name="cl_pkgs" min='0'  placeholder="Total Packages" >
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td><label>Total Invoice Amount:</label><br/>
                                                <input type=number name="invoice_amount" step="0.01" min='0' placeholder="Total Invoice Amount" >
                                            </td>
                                            <td><label>Status:</label>
                                                <select name="order_registry" id="status">
                                                    <option value='Pending'>Pending</option>
                                                    <option value='ORDERED'>ORDERED</option>
                                                    <option value='CONFIRMED'>CONFIRMED</option>
                                                    <option value='DELIVERED TO FF'>DELIVERED TO FF</option>
                                                    <option value='TRANSIT'>TRANSIT</option>
                                                </select>
                                            </td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td colspan="4">
                                                <br/><input type="submit"  name="action" value="Add" class="button-primary"  >
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
                    jQuery('#client-name').html('');
                    var html = '<option >Select Client</option>';
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
                    var html = '<option >Select Item</option>';
                    jQuery.each(data, function (i, item) {
                        html += `<option value="${item.id}">${item.collection_name}</option>`;
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
                        jQuery(`#item_desc`).val(`${item.description}`);
                        jQuery(`#item_category`).val(`${item.category_name}`);
                        jQuery(`#sup_code`).val(`${item.sup_code}`);
                    }
                });
            });
        });

    </script>
    <?php
}
