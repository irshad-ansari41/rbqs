<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_add_showroon_order_page() {
    global $wpdb, $current_user;
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    if (!empty($postdata)) {
        $data = ['sales_person' => $postdata['sales_person'], 'is_showroom' => 1, 'client_id' => FLAGSHIP_ID, 'status' => $postdata['status'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_quotations", $data, ['%s', '%d', '%d', '%s', '%s', '%s',]);
        $quotation_id = $wpdb->insert_id;
        save_sh_order_meta($quotation_id, $postdata);
        $msg = 1;
    }
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .chosen-container,#client_name_chosen{min-width:250px!important}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Create</h1>

        <div id="welcome-to-aquila" class="postbox">
            <div class="inside">

                <?php if (!empty($msg)) { ?>
                    <br/>
                    <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success!</strong> Your Flagship Order has been save successfully.
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
                                <tr id="tbl-row-1" valign=top>
                                    <td><input type="checkbox" name="row" data-id="tbl-row-1"></td>
                                    <td>1.</td>
                                    <td width="100"><input type="text" name="items[1][code]" id="input-code-1" placeholder="Supplier Code" required readonly></td>
                                    <td>
                                        <select name="items[1][id]" id="sel-name-1" class="chosen-select"  onchange="select_item(this.value, 1)" required>
                                            <option value="">Loading...</option>
                                        </select>
                                    </td>
                                    <td><textarea name="items[1][desc]" id="input-desc-1" rows=5>Item Description</textarea></td>
                                    <td><input type="number" name="items[1][qty]" id="input-qty-1" max="999" min="1" placeholder="Quantity" required></td>
                                    <td><input type="text" name="items[1][hs_code]" id="input-hs_code-1" placeholder="HS Code" readonly /></td>
                                </tr>
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
                            <button type="submit" value="Pending" name="status" class="btn btn-dark btn-sm">Save for Review</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>

        jQuery(document).ready(() => {
            jQuery('#add-row,#delete-row').hide();
            jQuery('.chosen-select').chosen();
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-items.php",
                dataType: 'json',
                success: function (data) {
                    jQuery('#sel-name-1').html('');
                    var html = '<option value="">Select Item</option>';
                    jQuery.each(data, function (i, item) {
                        html += `<option value="${item.id}">${item.collection_name}</option>`;
                    });
                    jQuery('#sel-name-1').html(html);
                    jQuery('#sel-name-1').trigger("chosen:updated");
                    jQuery('#add-row,#delete-row').show();
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
                var items = jQuery('#sel-name-1').html();
                var rowCount = get_last_tr();
                var tr = `<tr id="tbl-row-${rowCount}" valign=top>
                            <td><input type="checkbox" name="row"  data-id="tbl-row-${rowCount}"></td>
                            <td>${rowCount}.</td>
                            <td><input type="text" name="items[${rowCount}][code]" id="input-code-${rowCount}" placeholder="Supplier Code" required readonly></td>
                            <td><select name="items[${rowCount}][id]" class="chosen-select" id="sel-name-${rowCount}" onchange="select_item(this.value,${rowCount})" >${items}</select></td>
                            <td><textarea name="items[${rowCount}][desc]" id="input-desc-${rowCount}" rows=5>Item Description</textarea></td>
                            <td><input type="number" name="items[${rowCount}][qty]"  id="input-qty-${rowCount}" max="999" min="1" placeholder="Quantity" required></td>
                            <td><input type="text" name="items[${rowCount}][hs_code]" id="input-hs_code-${rowCount}" placeholder="HS Code" readonly ></td>
                        </tr>`;
                jQuery('#tbl-row-' + (rowCount - 1)).after(tr);
                jQuery('.chosen-select').chosen();
            });

        }
        );

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