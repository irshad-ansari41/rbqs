<?php

function admin_ctm_order_tracker_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=order-tracker'));
        exit();
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
        <h1 class="wp-heading-inline">View Order Tracker</h1>
        <a href="admin.php?page=order-tracker"  class="page-title-action" >Back</a>
        <br/><br/>
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
                                            <td colspan="4" class="text-red text-uppercase"><br/>Client Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Client:</label><br/>
                                                <input type=text readonly value="<?= get_client($po_meta_item->client_id, 'name') ?>" >
                                            </td>

                                            <td><label>QTN:</label>
                                                <input type=number readonly value="<?= $po_meta_item->quotation_id ?>" >
                                            </td>

                                            <td><label>Revised QTN No:</label>
                                                <input type=text readonly value="<?= $po_meta_item->revised_no ?>" >
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Order Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Collection Name:</label><br/>
                                                <input type=text readonly value="<?= get_item($po_meta_item->item_id, 'collection_name') ?>" >
                                            </td>
                                            <td><label>Item Category:</label>
                                                <input type=text readonly value="<?= $category ?>" >
                                            </td>

                                            <td><label>Supplier Code:</label>
                                                <input type=text readonly value="<?= $po_meta_item->sup_code ?>" >
                                            </td>
                                            <td></td>

                                        </tr>
                                        <tr>
                                            <td colspan="3"><label>Description:</label><br/>
                                                <textarea readonly rows="12"><?= $po_meta_item->item_desc ?></textarea>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>

                                            <td><label>PO#:</label>
                                                <input type=number readonly value="<?= $po_meta_item->po_id ?>">
                                            </td>
                                            <td><label>PO Date:</label><br/>
                                                <input type=date readonly value="<?= rb_date($po_meta_item->po_date, 'Y-m-d') ?>" />
                                            </td>
                                            <td>
                                                <label>Confirmed Quantity:</label><br/>
                                                <input type=number min="0" readonly value="<?= $po_meta_item->confirmed_quantity ?>" />
                                            </td>
                                            <td>
                                                <label>Confirmed Price:</label><br/>
                                                <input type=number min="0" step="0.01" readonly value="<?= $po_meta_item->confirmed_price ?>" />
                                            </td>
                                        </tr>

                                        <tr>

                                            <td><label>Confirmation No:</label>
                                                <input type="text" readonly value="<?= $po_meta_item->confirmation_no ?>">
                                            </td>
                                            <td><label>Dispatch Date:</label><br/>
                                                <input type="date" readonly value="<?= $po_meta_item->dispatch_date ?>">
                                            </td>
                                            <td><label>Delivery Date To FF:</label><br/>
                                                <input type="date" readonly value="<?= $po_meta_item->delivery_date ?>">
                                            </td>
                                            <td></td>


                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-red text-uppercase"><br/>Item Details</td>
                                        </tr>
                                        <tr>
                                            <td><label>Entry #:</label>
                                                <input type=text readonly value="<?= $po_meta_item->entry ?>" >
                                            </td>
                                            <td><label>No of packages:</label><br/>
                                                <input type=number readonly value="<?= $po_meta_item->no_of_pkgs ?>">
                                            </td>
                                            <td><label>Invoice No:</label>
                                                <input type=text readonly value="<?= $po_meta_item->invoice_no ?>" >
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><label>Invoice Date:</label><br/>
                                                <input type=date readonly value="<?= $po_meta_item->invoice_date ?>" >
                                            </td>
                                            <td><label>Invoice Due Date:</label><br/>
                                                <input type=date readonly value="<?= $po_meta_item->due_date ?>" >
                                            </td>
                                            <td><label>Currency:</label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="EURO" 
                                                           <?= $po_meta_item->currency == 'EURO' ? 'checked' : '' ?> disabled>EURO(€)</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="currency" value="USD"  
                                                           <?= $po_meta_item->currency == 'USD' ? 'checked' : '' ?> disabled>USD($)</label><br/>
                                            </td>
                                            <td></td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <label>Quantity:</label><br/>
                                                <input type=number min="0" step="0.01" value="<?= $po_meta_item->quantity ?>" readonly />
                                            </td>
                                            <td><label>Item Cost Price:</label>
                                                <input type=number readonly step="0.01" value="<?= $po_meta_item->cl_value ?>" >
                                            </td>
                                            <td><label>Container Name:</label>
                                                <select name="container_name" disabled>
                                                    <option value="">Select Container</option>
                                                    <?php
                                                    foreach ($containers as $value) {
                                                        $selected = $value->container_name == $po_meta_item->container_name ? 'selected' : '';
                                                        echo "<option value='{$value->container_name}' $selected >{$value->container_name}</option>";
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
                                                <input type=number readonly step="0.01" min='0' value="<?= $po_meta_item->cl_cbm ?>" >
                                            </td>
                                            <td><label>Total Weight:</label><br/>
                                                <input type=number readonly step="0.01" min='0' value="<?= $po_meta_item->cl_kg ?>" >
                                            </td>
                                            <td><label>Total Packages:</label><br/>
                                                <input type=number readonly min='0' value="<?= $po_meta_item->cl_pkgs ?>" >
                                            </td>
                                            <td><label>Total Invoice Amount:</label><br/>
                                                <input type=number readonly step="0.01" min='0' value="<?= $po_meta_item->invoice_amount ?>" >
                                            </td>

                                        </tr>
                                        <tr>

                                            <td><label>Status:<span class="text-red">*</span></label>
                                                <input type="text"  value="<?= $po_meta_item->order_registry ?>" readonly />
                                            </td>
                                            <td><label>Is Air Shipment:</label><br/>
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="air_shipment" value="1"
                                                           <?= $po_meta_item->air_shipment == 1 ? 'checked disabled' : 'disabled' ?>>Yes</label>
                                                &nbsp;&nbsp;
                                                <label class="font-weight-light" style="font-size: 12px;">
                                                    <input type="radio" name="air_shipment" value="0" 
                                                           <?= $po_meta_item->air_shipment == 0 ? 'checked disabled' : 'disabled' ?>>No</label><br/>
                                            </td>                                                
                                            <td>
                                                <?php if ($po_meta_item->air_shipment) { ?>
                                                    <label>ETA Date:<span class="text-red">*</span></label><br/>
                                                    <input type="date" name="arrival_date" value="<?= $po_meta_item->arrival_date ?>" readonly />
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <label>Stock Status:<span class="text-red">*</span></label><br/>
                                                <input type="text"  value="<?= $po_meta_item->stk_inv_status ?>" readonly />
                                            </td>


                                        </tr>


                                    </table>
                                    <br/><br/><br/>
                                </form>

                            </div>
                        </div>
                        <a href="admin.php?page=order-tracker"  class="button-secondary">Back</a>
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
