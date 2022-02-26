<?php
include_once 'popup.php';

function admin_ctm_purchase_order_items_project_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=purchase-order'));
        exit();
    }
    $po = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po where  id='{$id}'");
    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where  id='{$po->quotation_id}'");
    $client = get_client($quotation->client_id);


    if (!empty($postdata['status']) && $postdata['po_meta_id']) {
        if (in_array($postdata['status'], ['ORDERED', 'CONFIRMED', 'CANCELLED'])) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='{$postdata['status']}' , updated_at='{$date}' where id='{$postdata['po_meta_id']}' ");
        } else if ($postdata['status'] == 'ARRIVED') {
            make_model_project_arrived();
        }
    }

    if (!empty($postdata['arrived'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='ARRIVED', entry='{$postdata['entry']}', stk_inv_status='RESERVED', updated_at='{$date}' where id='{$postdata['po_meta_id']}' ");
        $all = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE po_id='{$id}' AND sup_code!='RB'");
        $allArrived = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE quotation_id='{$po->quotation_id}' AND sup_code!='RB' AND order_registry='ARRIVED'");
        if ($all == $allArrived) {
            create_project_delivery_note($quotation->id);
        }
    }

    if (!empty($postdata['update_project_job_order'])) {
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET project_job_order='{$postdata['project_job_order']}', order_registry='ARRIVED' WHERE id='{$po->quotation_id}'");
        $msg = 1;
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
    <?php if (!empty($msg)) {
        ?>
        <br/>
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> Your quotation has been update successfully.
        </div>
    <?php } ?>
    <div class="wrap">
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
                                            <td colspan='2' class='text-center'><b><?= $quotation->revised_no ? $quotation->revised_no : $quotation->id ?></b></td>
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
                                            <th>Collection<br/>Name</th>
                                            <th>Item<br/>Category</th>
                                            <th>Supplier<br/>Name</th>
                                            <th>Supplier<br/>Code</th>
                                            <th>Entry#</th>
                                            <th>Item<br/>Description</th>
                                            <th>Quantity</th>
                                            <th>PO#</th>
                                            <th>PO Date</th>
                                            <th>Confirmation#</th>
                                            <th>Client<br/>Name</th>
                                            <th>Quotation#</th>
                                            <th>Invoice# / Date</th>
                                            <th>Estimated Dispatch Date From Supplier</th>
                                            <th style="width:120px">Status</th>
                                        </tr>
                                        <?php
                                        $po_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta where po_id='{$id}' ORDER BY sup_code ASC");
                                        foreach ($po_items as $value) {
                                            $item = get_item($value->item_id);
                                            $category = get_item_category($item->category, 'name');
                                            $supplier_name = get_supplier($value->sup_code, 'name');
                                            $status[$value->order_registry] = $value->order_registry;
                                            ?>
                                            <tr>
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
                                                            <option value='ARRIVED' <?= $value->order_registry == 'ARRIVED' ? 'selected' : '' ?> >ARRIVED</option> 
                                                            <option value='TRANSIT' <?= $value->order_registry == 'TRANSIT' ? 'selected' : '' ?> disabled="disabled">TRANSIT</option>
                                                        </select>
                                                        <input type="hidden" name="po_meta_id" value="<?= $value->id ?>" />
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </table>

                                    <form method="post">
                                        <div class="row btn-bottom">
                                            <div class="col-sm-6 text-center"><br/>
                                                <a href="admin.php?page=purchase-order" class="btn btn-dark text-white btn-sm">Back</a>&nbsp;&nbsp;
                                                <a href='admin.php?page=purchase-order-view&id=<?= $id ?>' class='btn btn-primary btn-sm text-white' target='_blank'>View PO</a>
                                                <br/><br/>
                                            </div>
                                            <div class="col-sm-3 text-right">
                                                <?php if (has_role_super_and_admin() && in_array($po->status, ['Pending', 'ORDERED'])) { ?>
                                                    <form method="post">
                                                        <button type="submit" name="delete_po" value="delete"  onclick='return confirm(`are you sure you want to delete?`)'  
                                                                class="btn btn-danger btn-sm">Delete Purchase Order</button>
                                                    </form>
                                                <?php } ?>
                                            </div>
                                            <?php
                                            $a = array_unique($status);
                                            if (count($a) == 1 && !empty($status['ARRIVED'])) {
                                                ?>
                                                <div class="col-sm-2"><br/>
                                                    <a href="admin.php?page=project-delivery-note&qid=<?= $quotation->id ?>" 
                                                       class="btn btn-success btn-sm text-white" target="_blank" />Delivery Note</a>
                                                </div>
                                                <div class="col-sm-2">Project Job Order<br/>
                                                    <input type="text"  name="project_job_order" class="form-control" value="<?= $quotation->project_job_order ?>" 
                                                           required="required" placeholder="Project Job Order"/>
                                                </div>
                                                <div class="col-sm-2 text-right"><br/>
                                                    <button type="submit" name="update_project_job_order"  value="1" class="btn btn-warning  btn-sm">Update Project job Order</button>
                                                    <br/><br/>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>

    <?php
}
