<?php
include_once 'popup.php';

function admin_ctm_purchase_order_items_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=purchase-order'));
        exit();
    }
    $po = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po where  id='{$id}'");
    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where  id='{$po->quotation_id}'");
    $date = current_time('mysql');
    $client = get_client($quotation->client_id);

    if (!empty($postdata['status']) && !empty($postdata['po_meta_id'])) {
        if (in_array($postdata['status'], ['ORDERED', 'CANCELLED'])) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='{$postdata['status']}', updated_at='{$date}' where id='{$postdata['po_meta_id']}'");
        } else if ($postdata['status'] == 'CONFIRMED') {
            make_model_confirm();
        } else if ($postdata['status'] == 'DELIVERED TO FF') {
            make_model_dff();
        }
    }

    if (!empty($postdata['confirm']) && !empty($postdata['po_meta_id'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='CONFIRMED', confirmation_no='{$postdata['confirmation_no']}',dispatch_date='{$postdata['dispatch_date']}', delivery_date='{$postdata['delivery_date']}', updated_at='{$date}' where id='{$postdata['po_meta_id']}'");
    }

    if (!empty($postdata['dff']) && !empty($postdata['po_meta_id'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='DELIVERED TO FF', entry='{$postdata['entry']}', currency='{$postdata['currency']}', invoice_no='{$postdata['invoice_no']}',invoice_amount='{$postdata['invoice_amount']}', invoice_date='{$postdata['invoice_date']}',due_date='{$postdata['due_date']}', updated_at='{$date}' where id='{$postdata['po_meta_id']}'");
    }

    if (!empty($postdata['is_container_loading'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET is_container_loading='1',cl_sequence_no='{$postdata['cl_sequence_no']}' where id='{$po->quotation_id}'");
    }

    if (!empty($postdata['delete_po'])) {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quotation_po where id='{$id}'");
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quotation_po_meta where po_id='{$id}'");
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_co SET status='Pending' where quotation_id='{$po->quotation_id}'");
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET status='CONFIRMED' where id='{$po->quotation_id}'");
        wp_redirect(admin_url('/admin.php?page=purchase-order'));
        exit();
    }
    
    set_purchase_order_status($id);
    ?>
    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
        #confirm-order-items th{text-align: center;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Purchase Order Items</h1>
        <br/><br/>


        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <div id="page-inner-content" class="postbox">
                            <br/>
                            <div class="inside">
                                <div id="page-inner-content1">
                                    <table border='1' style='border-collapse: collapse;width:100%' cellpadding=5 cellspacing=5>
                                        <tr class=bg-blue>
                                            <td colspan='4'><b>Customer Details</b></td>
                                            <td colspan='2' class='text-center'><b>Quotation # </b></td>
                                            <td colspan='3'  class='text-center'><b>Date</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Name:</b></td><td colspan='3'><b><?= $client->name ?></b></td>
                                            <td colspan='2' class='text-center'><b><?= (!empty($quotation->revised_no) ? $quotation->revised_no : $quotation->id); ?></b></td>
                                            <td colspan='3'  class='text-center'><b><?= rb_datetime($quotation->updated_at) ?></b></td>
                                        </tr>
                                        <tr>
                                            <td rowspan=2><b>Address</b></td>
                                            <td colspan='3'><b><?= $client->address ?></b></td>
                                            <td colspan='2' class='bg-blue text-center' ><b>Sales Person #</b></td>
                                            <td colspan='3' class='bg-blue text-center'><b>Destination</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan='3'><b><?= $client->city ?>, <?= $client->country ?></b></td>
                                            <td colspan='2' class='text-center'><b><?= $quotation->sales_person ?></b></td>
                                            <td colspan='3'  class='text-center'><b><?= $client->city ?></b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Phone</b></td><td colspan='3'><b><?= $client->phone ?></b></td>
                                            <td  class='bg-blue text-center' rowspan='3' colspan=5 ><b>Roche Bobois<br/>TRN# 100383178900003</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Email</b></td><td colspan='3'><b><?= $client->email ?></b></td>
                                        </tr>
                                        <tr>
                                            <td><b>TRN</b></td><td colspan='3'><b><?= $client->trn ?></b></td>
                                        </tr>
                                    </table>
                                    <table id="confirm-order-items" class="table table-striped table-sm">
                                        <tr valign="middle">
                                            <th>Sr.</th>
                                            <th>Collection Name</th>
                                            <th>Item Category</th>
                                            <th>Supplier Name</th>
                                            <th>Supplier Code</th>
                                            <th>Entry#</th>
                                            <th>Item Description</th>
                                            <th>Quantity</th>
                                            <th>PO#</th>
                                            <th>PO Date</th>
                                            <th>Confirmation#</th>
                                            <th>Client<br/>Name</th>
                                            <th>Quotation#</th>
                                            <th>Invoice# / Date</th>
                                            <th>Estimated Dispatch Date From Supplier</th>
                                            <th>Status</th>
                                        </tr>
                                        <?php
                                        $i = 1;
                                        $po_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta where po_id='{$id}' ORDER BY sup_code ASC");
                                        foreach ($po_items as $value) {
                                            $item = get_item($value->item_id);
                                            $category = get_item_category($item->category, 'name');
                                            $supplier_name = get_supplier($value->sup_code, 'name');
                                            ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= !empty($item->collection_name) ? $item->collection_name : '' ?></td>
                                                <td><?= $category ?></td>
                                                <td><?= $supplier_name ?></td>
                                                <td><?= $value->sup_code ?></td>
                                                <td><?= $value->entry ?></td>
                                                <td><?= nl2br($value->item_desc) ?></td>
                                                <td><?= $value->quantity ?></td>
                                                <td><?= $value->po_id ?></td>
                                                <td><?= rb_date($value->po_date) ?></td>
                                                <td><?= $value->confirmation_no ?></td>
                                                <td><?= $client->name ?></td>
                                                <td><?= !empty($quotation->revised_no) ? $quotation->revised_no : $quotation->id ?></td>
                                                <td><?= $value->invoice_no ?> <br/> <?= rb_date($value->invoice_date) ?></td>
                                                <td><?= rb_date($value->dispatch_date) ?></td>

                                                <td>
                                                    <form method="post">
                                                        <select name="status" id="status" onchange='if (confirm(`are you sure you want to change?`)) {
                                                                            this.form.submit()
                                                                        }'>
                                                            <option value='Pending' >Pending</option>
                                                            <option value='ORDERED' <?= $value->order_registry == 'ORDERED' ? 'selected' : '' ?> >ORDERED</option>
                                                            <option value='CONFIRMED' <?= $value->order_registry == 'CONFIRMED' ? 'selected' : '' ?> >CONFIRMED</option>
                                                            <option value='CANCELLED' <?= $value->order_registry == 'CANCELLED' ? 'selected' : '' ?>>CANCELLED</option>
                                                            <option value='DELIVERED TO FF' <?= $value->order_registry == 'DELIVERED TO FF' ? 'selected' : '' ?> >DELIVERED TO FF</option>
                                                            <option value='TRANSIT' <?= $value->order_registry == 'TRANSIT' ? 'selected' : '' ?> disabled="disabled">TRANSIT</option>
                                                            <option value='ARRIVED' <?= $value->order_registry == 'ARRIVED' ? 'selected' : '' ?> disabled="disabled">ARRIVED</option>
                                                        </select>
                                                        <input type="hidden" name="po_meta_id" value="<?= $value->id ?>" />
                                                    </form>
                                                </td>
                                                <?php ?>
                                            </tr>

                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <a href='admin.php?page=purchase-order-view&id=<?= $id ?>' class='btn btn-primary btn-sm text-white' target='_blank'>View PO</a>
                        </div>
                        <div class="col-sm-6 text-right">
                            <?php if (has_role_super_and_admin() && in_array($po->status, ['Pending', 'ORDERED'])) { ?>
                                <form method="post">
                                    <button type="submit" name="delete_po" value="delete"  onclick='return confirm(`are you sure you want to delete?`)'  
                                            class="btn btn-danger btn-sm">Delete Purchase Order</button>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('.chosen-select').chosen();
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
        });
    </script>
    <?php
}
