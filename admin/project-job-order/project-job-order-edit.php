<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_project_job_order_edit_page() {

    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $pjo_id = !empty($getdata['pjo_id']) ? $getdata['pjo_id'] : 0;
    if (empty($pjo_id)) {
        wp_redirect(admin_url('/admin.php?page=project-job-order'));
        exit();
    }

    if (!empty($postdata)) {
        $qtn_type = get_qtn_type($postdata['quotation_id']);
        $data = ['client_id' => $postdata['client_id'], 'quotation_id' => $postdata['quotation_id'],'qtn_type' => $qtn_type, 'contact_no' => $postdata['contact_no'],
            'requested_by' => $postdata['requested_by'], 'address' => $postdata['address'],
            'status' => $postdata['status'], 'updated_by' => $current_user->ID, 'updated_at' => $date];

        $wpdb->update("{$wpdb->prefix}ctm_project_job_order", $data, ['id' => $pjo_id], wpdb_data_format($data), ['%d']);
        $wpdb->query("DELETE FROM {$wpdb->prefix}ctm_project_job_order_meta WHERE pjo_id='{$pjo_id}'");
        save_pjo_meta($pjo_id, $postdata);

        $msg = " <strong>Success!</strong> Your project job order has been save successfully.";
    }

    $pjo = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_project_job_order where id='{$pjo_id}'");
    $pjo_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_project_job_order_meta where pjo_id='{$pjo->id}'");
    $items = $wpdb->get_results("SELECT * from {$wpdb->prefix}ctm_items WHERE category=50");
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .chosen-container,#client_name_chosen{min-width:250px!important}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">EDIT PROJECT JOB ORDER</h1>
        <br/>
        <br/>
        <div id="welcome-to-aquila" class="postbox">
            <div class="inside">
                <?php if (!empty($msg)) { ?>
                    <br/>
                    <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success!</strong> Your Project Job Order has been updated successfully.
                    </div>
                <?php } ?>
                <form id="new-quotation-form" method="post" action="">               


                    <div class="row">

                        <div class="col-sm-3">
                            <label>Client Name:<span class="text-red">*</span></label><br/>
                            <select name="client_id" id="client-name" class="chosen-select" onchange="select_client(this.id)" required>
                                <option value="">Loading...</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <label>Phone:<span class="text-red">*</span></label><br/>
                            <input type="text"  id="client-phone" name="contact_no"  placeholder="Phone" value="<?= $pjo->contact_no ?>" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Quotation No:<span class="text-red">*</span></label><br/>
                            <input type="text" name="quotation_id"  placeholder="Quotation No" value="<?= $pjo->quotation_id ?>" required>
                        </div>

                        <div class="col-sm-3">
                            <label>Requested By:<span class="text-red">*</span></label><br/>
                            <input type="text" name="requested_by"  placeholder="Requested By" value="<?= $pjo->requested_by ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Address:<span class="text-red">*</span></label><br/>
                            <textarea type="text" name="address" id="client-address"  placeholder="Address" rows="2" required><?= $pjo->address ?></textarea>
                        </div>
                    </div>

                    <br/>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="tbl-items" border="1" class="table-striped text-center boder-collapse" style="width: 100%;">
                                <tr>
                                <tr>
                                    <th style="width:50px"></th>
                                    <th style="width:50px">Sr.</th>
                                    <th style="width:100px">Type</th>
                                    <th>Details</th>
                                    <th>Image</th>
                                    <th style="width:75px">QTY</th>
                                    <th style="width:100px">Responsibility</th>
                                    <th style="width:100px">Start Date & Time</th>
                                    <th style="width:100px">Completion Date & Time</th>
                                </tr>
                                </tr>
                                <?php
                                $i = 0;
                                foreach ($pjo_meta as $value) {
                                    $i++;
                                    ?>
                                    <tr id="tbl-row-<?= $i ?>" valign=top>
                                        <td><input type="checkbox" name="row" data-id="tbl-row-<?= $i ?>"></td>
                                        <td><?= $i ?>.</td>
                                        <td>
                                            <select name="items[<?= $i ?>][item_id]" id="sel-name-<?= $i ?>" class="chosen-select"  onchange="select_item(this.value, <?= $i ?>)" required>
                                                <?php
                                                $addoptions = $editoptions = '<option>Select Item</option>';
                                                foreach ($items as $item) {
                                                    $selected = $item->id == $value->item_id ? 'selected' : '';
                                                    $addoptions .= "<option value='{$item->id}'>{$item->collection_name}</option>";
                                                    $editoptions .= "<option value='{$item->id}' $selected>{$item->collection_name}</option>";
                                                }
                                                echo $editoptions;
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="items[<?= $i ?>][desc]" id="input-desc-<?= $i ?>" rows="5" required placeholder="Item Name" ><?= $value->item_desc ?></textarea>
                                            <textarea name="items[<?= $i ?>][action]" id="input-action-<?= $i ?>" rows="5" placeholder="Action to be Taken" ><?= $value->action ?></textarea>
                                        </td>
                                        <td>
                                            <input id="item-image-<?= $i ?>" data-input="items[<?= $i ?>][image]" data-output='output-image-<?= $i ?>' class="item-image button-primary" 
                                                   type="button" value="Add Image" /><br/>
                                            <output id="output-image-<?= $i ?>">
                                                <?= "<input type='hidden' name='items[{$i}][image]' value='$value->image'/>" ?>
                                            </output>
                                            <img src='<?= get_image_src($item->image); ?>' width=100 style='margin: auto;width: 100px;'>

                                        </td>
                                        <td><input type="number" name="items[<?= $i ?>][qty]" id="input-qty-<?= $i ?>" max="999" min="1" value="<?= $value->quantity ?>" required /></td>
                                        <td><input type="text" name="items[<?= $i ?>][responsibility]" id="input-responsibility-<?= $i ?>" value="<?= $value->responsibility ?>" required /></td>
                                        <td><input type="date" name="items[<?= $i ?>][start_date]" id="input-start-date-<?= $i ?>" value="<?= $value->start_date ?>" required>
                                            <br/><?= get_schedule_from("items[$i][start_time]", $value->start_time); ?></td>
                                        <td><input type="date" name="items[<?= $i ?>][end_date]" id="input-end-date-<?= $i ?>" value="<?= $value->end_date ?>"  required >
                                            <br/><?= get_schedule_to("items[$i][end_time]", $value->end_time); ?></td>
                                    </tr>
                                <?php } ?>

                                <?php if (empty($pjo_meta)) { ?>
                                    <tr id="tbl-row-1" valign=top>
                                        <td><input type="checkbox" name="row" data-id="tbl-row-1"></td>
                                        <td>1.</td>
                                        <td>
                                            <select name="items[1][item_id]" id="sel-name-1" class="chosen-select"  onchange="select_item(this.value, 1)" required>
                                                <?php
                                                $options = '<option>Select Item</option>';
                                                foreach ($items as $item) {
                                                    $options .= "<option value='{$item->id}'>{$item->collection_name}</option>";
                                                }
                                                echo $options;
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="items[1][desc]" id="input-desc-1" rows="5" placeholder="Item Name" required ></textarea>
                                            <textarea name="items[1][action]" id="input-action-1" rows="5" placeholder="Action to be Taken" required ></textarea>
                                        </td>
                                        <td>
                                            <input id="item-image-1" data-input="items[1][image]" data-output='output-image-1' class="item-image button-primary" 
                                                   type="button" value="Add Image" /><br/>
                                            <output id="output-image-1">
                                                <input type='hidden' name='items[1][image]' value="0"/>
                                            </output>
                                        </td>
                                        <td><input type="number" name="items[1][qty]" id="input-qty-1" max="999" min="1"  required /></td>
                                        <td><input type="text" name="items[1][responsibility]" id="input-responsibility-1" required /></td>
                                        <td><input type="date" name="items[1][start_date]" id="input-start-date-1" required>
                                            <br/><?= get_schedule_from('items[1][start_time]'); ?></td>
                                        <td><input type="date" name="items[1][end_date]" id="input-end-date-1"  required >
                                            <br/><?= get_schedule_to('items[1][end_time]'); ?></td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                    <td colspan="9" class="text-left">
                                        <button type="button" id="add-row" class="btn btn-dark text-white btn-sm">Add Item</button>
                                        <button type="button" id="delete-row" class="btn btn-danger text-white btn-sm">Delete Item</button>
                                    </td>
                                </tr>
                            </table>                                               
                        </div>
                    </div>
                    <hr/>
                    <br/>

                    <div class="row">
                        <div class="col-sm-6">
                            <a href="<?= admin_url('admin.php?page=project-job-order') ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
                        </div>
                        <div class="col-sm-6 text-right">
                            <br/>
                            <?php if ($pjo->status == 'Draft') { ?>
                                <button type="submit" name='status' value="Draft" class="btn btn-dark btn-sm">Save as Draft</button>
                                <button type="submit" name='status' value='Pending' class="btn btn-primary btn-sm">Save Project Job Order</button>
                            <?php } else { ?>
                                <button type="submit" name="status" value="Pending" class="btn btn-primary btn-sm">Update Project Job Order</button>
                            <?php } ?>
                        </div>
                    </div>
                    <br/>
                    <br/>
                </form>
            </div>
        </div>
    </div>

    <script>
        var promoType = '';
        jQuery(document).ready(() => {

            jQuery('.chosen-select').chosen();
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                data: {all: 'all'},
                success: function (data) {
                    var client_id = '<?= $pjo->client_id ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Select Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        var item = JSON.stringify(client);
                        html += `<option value="${client.id}"  ${selected} data-item='${item}'>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
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
                            <td><input type="checkbox" name="row"  data-id="tbl-row-${rowCount}"></tb>
                            <td>${rowCount}.</td>
                            <td><select name="items[${rowCount}][item_id]" id="sel-name-${rowCount}" class="chosen-select"  onchange="select_item(this.value, ${rowCount})" 
    required ><?= $addoptions ?></select></td>
                            <td>
                                <textarea name="items[${rowCount}][desc]" id="input-desc-${rowCount}" rows="5" placeholder='Item Description' required ></textarea>
                                <textarea name="items[${rowCount}][action]" id="input-action-${rowCount}" rows="5" placeholder="Action to be Taken" ></textarea>
                            </td>
                            <td>
                                <input id="item-image-${rowCount}" data-input="items[${rowCount}][image]" data-output='output-image-${rowCount}' class="item-image button-primary" 
                                       type="button" value="Add Image" /><br/>
                                <output id="output-image-${rowCount}"><input type='hidden' name='items[${rowCount}][image]' value='0'/></output>
                            </td>
                            <td><input type="number" name="items[${rowCount}][qty]"  id="input-qty-${rowCount}" max="999" min="1" required></td>
                            <td><input type="text" name="items[${rowCount}][responsibility]" id="input-responsibility-${rowCount}"  required></td>
                            <td><input type="date" name="items[${rowCount}][start_date]" id="input-start-date-${rowCount}" required>
    <br/><?= get_schedule_from('items[${rowCount}][start_time]'); ?></td>
                            <td><input type="date" name="items[${rowCount}][end_date]" id="input-end-date-${rowCount}" required>
    <br/><?= get_schedule_from('items[${rowCount}][end_time]'); ?></td>
                        </tr>`;
                jQuery('#tbl-row-' + (rowCount - 1)).after(tr);
                rowCount = rowCount + 1;
                jQuery('.chosen-select').chosen();
                jQuery('.item-image').click(function (e) {
                    var input = jQuery(this).data('input');
                    var output = jQuery(this).data('output');
                    file_uploader(e, input, output, false);
                });
            });

        });

        function get_last_tr() {
            var arr = [];
            jQuery('table#tbl-items > tbody  > tr').each(function () {
                if (this.id !== '') {
                    arr.push(this.id.replace('tbl-row-', ''));
                }
            });
            var max = Math.max(...arr);
            return max + 1;
        }

        function select_client(id) {
            var client = jQuery('#' + id).find(':selected').data('item');
            jQuery(`#client-phone`).val(client.phone);
            jQuery(`#client-address`).val(client.address);
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
                    } else {
                        jQuery(`#input-desc-${number}`).attr('placeholder', 'Item Description').val('');
                    }
                }
            });
        }

        jQuery('.item-image').click(function (e) {
            var input = jQuery(this).data('input');
            var output = jQuery(this).data('output');
            file_uploader(e, input, output, false);
        });

        function file_uploader(e, input, output, multiple) {
            var custom_uploader;
            e.preventDefault();
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: multiple
            });
            custom_uploader.on('select', function () {
                var selection = custom_uploader.state().get('selection');
                var attachment_ids = selection.map(function (attachment) {
                    attachment = attachment.toJSON();
                    if (multiple === false) {
                        jQuery('#' + output).html('');
                    }
                    jQuery('#' + output).append("<input type='hidden' name='" + input + "' value='" + attachment.id + "'><img src='" + attachment.url + "' style='width:100px'>");
                }).join();
            });
            custom_uploader.open();
        }

    </script>
<?php } ?>