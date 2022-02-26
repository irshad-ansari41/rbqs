<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_edit_showroon_order_page() {


    global $wpdb, $current_user;
    $postdata = filter_input_array(INPUT_POST);
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : 0;
    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=showroom-order'));
        exit();
    }
    $date = current_time('mysql');
    if (!empty($postdata)) {
        $data = ['status' => $postdata['status'], 'updated_by' => $current_user->ID, 'updated_at' => $date];
        $wpdb->update("{$wpdb->prefix}ctm_quotations", $data, ['id' => $id], ['%s', '%s',], ['%d']);
        $quotation_id = $id;
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_quotations_meta WHERE quotation_id='{$id}'");
        save_sh_order_meta($quotation_id, $postdata);
        $msg = " <strong>Success!</strong> Your Flagship has been updated successfully.";
    }

    $items = $wpdb->get_results("SELECT id, collection_name FROM {$wpdb->prefix}ctm_items WHERE status='Active' ORDER BY collection_name ASC");
    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where  id='{$id}'");
    $quotation_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$quotation->id}' ORDER BY id ASC");
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .chosen-container,#client_name_chosen{min-width:250px!important}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Edit</h1>
        <div id="welcome-to-aquila" class="postbox">
            <div class="inside">
                <?php if (!empty($msg)) { ?>
                    <br/>
                    <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?= $msg ?>
                    </div>
                <?php } ?>
                <form id="new-quotation-form" method="post" action="">
                    <input type="hidden" name="sales_person" value="<?= get_user_meta($current_user->ID, 'sales_person', true) ?>">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h2 class="hndle ui-sortable-handle text-center"><span>Flagship Order Form</span></h2><br/>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="tbl-items" border="1" class="table-striped text-center boder-collapse" style="width: 100%;">
                                <tr>
                                    <th style="width:40px"></th>
                                    <th style="width:40px">Sr.</th>
                                    <th  style="width:150px">Supplier Code</th>
                                    <th style="width:150px">Item Name</th>
                                    <th>Item Description</th>
                                    <th style="width:100px">Quantity</th>
                                    <th style="width:140px">HS Code</th>
                                </tr>
                                <?php
                                foreach ($quotation_items as $key => $quo_item) {
                                    $item = get_item($quo_item->item_id);

                                    $i = $key + 1;
                                    ?>
                                    <tr id="tbl-row-<?= $i ?>" valign=top>
                                        <td><input type="checkbox" name="row" data-id="tbl-row-<?= $i ?>"></td>
                                        <td><?= $i ?>.</td>
                                        <td><input type="text" name="items[<?= $i ?>][code]" id="input-code-<?= $i ?>" value="<?= $item->sup_code ?>" readonly></td>
                                        <td>
                                            <select name="items[<?= $i ?>][id]" id="sel-name-<?= $i ?>" class="chosen-select" onchange="select_item(this.value, <?= $i ?>)" required>
                                                <option value="">Select Item</option>
                                                <?php
                                                foreach ($items as $value) {
                                                    ?>
                                                    <option value="<?= $value->id ?>"  <?= $quo_item->item_id == $value->id ? 'selected' : '' ?> ><?= $value->collection_name ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><textarea name="items[<?= $i ?>][desc]" id="input-desc-<?= $i ?>" rows=5><?= $quo_item->item_desc ?></textarea></td>
                                        <td><input type="number" name="items[<?= $i ?>][qty]" id="input-qty-<?= $i ?>" max="999" min="1" required  value="<?= $quo_item->quantity ?>"></td>
                                        <td><input type="text" name="items[<?= $i ?>][hs_code]" data-id="tbl-hs_code-<?= $i ?>"  value="<?= $item->hs_code ?>" readonly /></td>

                                    </tr>
                                <?php } ?>

                                <?php if (empty($quotation_items)) { ?>
                                    <tr id="tbl-row-1" valign=top>
                                        <td><input type="checkbox" name="row" data-id="tbl-row-1"></td>
                                        <td>1.</td>
                                        <td width="100"><input type="text" name="items[1][code]" id="input-code-1" placeholder="Supplier Code" readonly></td>
                                        <td style="width:150px"><select name="items[1][id]" id="sel-name-1" class="chosen-select"  onchange="select_item(this.value, 1)" required>
                                                <option value="">Select Item</option>
                                                <?php foreach ($items as $value) { ?>
                                                    <option value="<?= $value->id ?>" ><?= $value->collection_name ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><textarea name="items[1][desc]" id="input-desc-1" rows=5>Item Description</textarea></td>
                                        <td width="75"><input type="number" name="items[1][qty]" id="input-qty-1" max="999" min="1" placeholder="Quantity" required></td>
                                        <td><input type="text" name="items[1][hs_code]" id="input-hs_code-1" placeholder="HS Code" readonly /></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="7" class="text-left">
                                        <button type="button" id="add-row" class="btn btn-dark text-white btn-sm">Add Item</button>
                                        <button type="button" id="delete-row" class="btn btn-danger text-white btn-sm">Delete Item</button>
                                    </td>
                                </tr>
                            </table>                                               
                        </div>

                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="<?= admin_url("admin.php?page=showroom-order") ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
                        </div>
                        <div class="col-sm-6 text-right">
                            <button type="submit" value="Pending" name="status" <?= $quotation->status != 'Pending' ? 'disabled' : '' ?> class="btn btn-dark btn-sm">Save for Review</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(() => {

            jQuery('.chosen-select').chosen();
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery('#delete-row').click(() => {
                if (jQuery("input:checkbox[name=row]:checked").length <= 0) {
                    alert("Please select item you want to remove it.");
                }
                jQuery("input:checkbox[name=row]:checked").each(function () {
                    jQuery('#' + jQuery(this).data('id')).remove();
                });
            });
            jQuery('#add-row').click(() => {
                var rowCount = get_last_tr();
                var tr = `<tr id="tbl-row-${rowCount}" valign=top>
                        <td><input type="checkbox" name="row" data-id="tbl-row-${rowCount}"></td>
                        <td>${rowCount}.</td>
                        <td><input type="text" name="items[${rowCount}][code]" id="input-code-${rowCount}" placeholder="Supplier Code" readonly></td>
                        <td>
                            <select name="items[${rowCount}][id]" class="chosen-select" id="sel-name-${rowCount}" onchange="select_item(this.value,${rowCount})" >
                            <option value="">Select Item</option>
    <?php
    foreach ($items as $value) {
        echo "<option value='{$value->id}' >$value->collection_name</option>";
    }
    ?>
                            </select>
                        </td>
                        <td><textarea name="items[${rowCount}][desc]" id="input-desc-${rowCount}" rows=5>Item Description</textarea></td>
                        <td><input type="number" name="items[${rowCount}][qty]"  id="input-qty-${rowCount}" max="999" min="1" placeholder="Quantity" required></td>
                        <td><input type="text" name="items[${rowCount}][hs_code]" id="input-hs_code-${rowCount}" placeholder="HS Code" readonly></td>
                            </tr>`;
                jQuery('#tbl-row-' + (rowCount - 1)).after(tr);
                rowCount = rowCount + 1;
                jQuery('.chosen-select').chosen();
            });

        });

        function get_last_tr() {
            var arr = [];
            jQuery('table#tbl-items > tbody > tr').each(function () {
                if (this.id !== '') {
                    arr.push(this.id.replace('tbl-row-', ''));
                }
            });
            var max = Math.max(...arr);
            return max + 1;
        }

        function select_item(id, number) {
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-item.php",
                cache: false,
                method: 'get',
                data: {id: id},
                dataType: 'json',
                success: function (item) {
                    if (item) {
                        jQuery(`#input-desc-${number}`).val(`${item.description}`);
                        jQuery(`#input-code-${number}`).val(`${item.sup_code}`);
                        jQuery(`#input-hs_code-${number}`).val(`${item.hs_code}`);
                    } else {
                        jQuery(`#input-desc-${number}`).attr('placeholder', 'Item Description').val('');
                        jQuery(`#input-code-${number}`).attr('placeholder', 'Supplier Code').val('');
                        jQuery(`#input-hs_code-${number}`).attr('placeholder', 'HS Code').val('');
                    }
                }
            });
        }

    </script>
<?php } ?>
