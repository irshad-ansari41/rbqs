<?php

function admin_ctm_confirm_order_items_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $msg = '';
    $co_id = !empty($getdata['co_id']) ? $getdata['co_id'] : 0;

    if (empty($co_id)) {
        wp_redirect(admin_url('/admin.php?page=confirm-order'));
        exit();
    }

    $co = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_co where id='{$co_id}'");

    if (!empty($postdata['confrim_po'])) {
        create_purchase_order($co->quotation_id);
        $msg = 'Your Confirm Order has been sent to Purchase Order.';
    }
    if (!empty($postdata['update_cop'])) {

        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_co SET updated_by='{$current_user->ID}', updated_at='{$date}' where id='{$co_id}'");
        foreach ($postdata['co'] as $id => $data) {
            $entry = !empty($data['entry']) ? trim($data['entry']) : '';
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_co_meta SET item_desc='{$data['item_desc']}' WHERE id='{$id}'");
            if ($entry) {
                $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_co_meta SET entry='{$entry}' WHERE id='{$id}'");
                $r = reserve_stock_in_cop($co->quotation_id, $co->revised_no, $co->client_id, $id, $entry);
                $msg = !empty($r) ? 'Item has beed Reserved in Stock.<br/>' : '';
            }
        }
        $msg .= 'Record has been updated successfully.';
    }
    if (!empty($postdata['delete_co'])) {
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quotation_co where id='{$co_id}'");
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quotation_co_meta where co_id='{$co_id}'");
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotations SET status='Pending' where id='{$co->quotation_id}'");
        wp_redirect(admin_url('/admin.php?page=confirm-order'));
        exit();
    }
    $client = get_client($co->client_id);
    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$co->quotation_id}'");
    ?>
    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: auto;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:100px;height: auto;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Confirm Order Items</h1>
        <br/><br/>
        <?php if (!empty($msg)) { ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> <?= $msg ?>
            </div>
        <?php } ?>

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
                                            <td colspan='2' class='text-center'><b><?= $revised_no = get_revised_no($co->quotation_id) ?></b></td>
                                            <td colspan='3'  class='text-center'><b><?= rb_datetime($co->updated_at) ?></b></td>
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
                                            <td colspan='3'  class='text-center'><b><?= get_location($quotation->city_id, 'city') ?></b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Phone</b></td><td colspan='3'><b><?= $client->phone ?></b></td>
                                            <td  class='bg-blue text-center' rowspan='3' colspan=5 ><b>ROCHE BOBOIS<br/>TRN # 100383178900003</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Email</b></td><td colspan='3'><b><?= $client->email ?></b></td>
                                        </tr>
                                        <tr>
                                            <td><b>TRN</b></td><td colspan='3'><b><?= $client->trn ?></b></td>
                                        </tr>
                                    </table>
                                    <form method="post">
                                        <table id="confirm-order-items" class="table table-striped table-sm">
                                            <tr valign="middle">
                                                <th>Sr.</th>
                                                <th>SUP</th>
                                                <th>Collection Name</th>
                                                <th style="width:250px">Description</th>
                                                <th>Image</th>
                                                <th>Quantity</th>
                                                <th style="width:108px">Reserved from Stock / Entry #</th>
                                                <th>PO #</th>
                                                <th>PO Date</th>
                                                <th>Updated At</th>
                                            </tr>
                                            <?php
                                            $i = 1;
                                            $co_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_co_meta where co_id='{$co_id}' ORDER BY sup_code ASC");
                                            foreach ($co_meta as $value) {
                                                $k = $value->id;
                                                $item = get_item($value->item_id);
                                                ?>
                                                <tr>
                                                    <td><?= $i++ ?></td>
                                                    <td><?= !empty($value->sup_code) ? $value->sup_code : '' ?></td>
                                                    <td><?= !empty($item->collection_name) ? $item->collection_name : '' ?></td>
                                                    <td>
                                                        <?php
                                                        $item_desc = !empty($value->item_desc) ? $value->item_desc : '';
                                                        if (has_this_role('commercial')) {
                                                            ?>
                                                            <textarea name="co[<?= $k ?>][item_desc]" rows="4"><?= $item_desc ?></textarea>
                                                        <?php } else { ?>
                                                            <p><?= nl2br($item_desc) ?></p>
                                                            <input type ="hidden" name="co[<?= $k ?>][item_desc]" value="<?= $item_desc ?>" /> 
                                                        <?php } ?>
                                                    </td>
                                                    <td><a href='<?= $image_src = get_image_src($item->image) ?>' target='_image'>
                                                            <img src='<?= $image_src ?>' width=100  style='margin: auto;width: 100px; '></a></td>
                                                    <td><?= $value->quantity ?></td>
                                                    <td><input type="text" name="co[<?= $k ?>][entry]" id="entry-1" class="form-control" value="<?= $value->entry ?>" <?= !empty($value->po_meta_id) ? 'disabled' : '' ?>  /></td>
                                                    <td>
                                                        <?= $value->po_id ?>
                                                    </td>
                                                    <td>
                                                        <?= rb_date($value->po_date) ?>
                                                    </td>

                                                    <td>
                                                        <?= rb_datetime($co->updated_at) ?>
                                                    </td>
                                                </tr>

                                            <?php } ?>
                                        </table>
                                        <div class="row btn-bottom">
                                            <div class="col-sm-8"></div>
                                            <div class="col-sm-2 text-right">
                                                <?php if (has_this_role('sales') || has_this_role('commercial')) { ?>
                                                    <button type="submit" name="update_cop" value="COP" class="btn btn-warning btn-sm">Update COP</button>
                                                <?php } ?>
                                                <br/><br/>
                                            </div>
                                            <div class="col-sm-2 text-right">
                                                <?php if (has_this_role('sales') || has_this_role('commercial')) { ?>
                                                    <a href='admin.php?page=confirm-order-view&co_id=<?= $co_id ?>' class='btn btn-secondary btn-sm text-white' target='_blank'>View COP</a>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-8">
                                <a href="admin.php?page=confirm-order" class="btn btn-dark btn-sm text-white">Back</a>
                            </div>
                            <div class="col-sm-2 text-right">
                                <?php if (has_role_super_and_admin() && $co->status == 'Pending') { ?>
                                    <form method="post">
                                        <button type="submit" name="delete_co" value="delete"  onclick='return confirm(`are you sure you want to delete?`)'  
                                                class="btn btn-danger btn-sm">Delete Confirm Order</button>
                                    </form>
                                <?php } ?>
                            </div>
                            <div class="col-sm-2 text-right">
                                <?php if ($quotation->status != 'PURCHASED' && has_this_role('commercial') && empty($value->po_id)) { ?>
                                    <form method="post">
                                        <button type="submit" name="confrim_po" value="PURCHASED" class="btn btn-primary btn-sm">Send to Purchase Order</button>
                                    </form>
                                <?php } ?>
                            </div>
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

            jQuery('#entry-1').on('input', () => {
                jQuery('#entry-2').val(jQuery('#entry-1').val());
            });
        });
    </script>
    <?php
}
