<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_quality_control_report_add_page() {

    global $wpdb, $current_user;
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    if (!empty($postdata)) {

        $data = ['client_id' => $postdata['client_id'], 'po_id' => $postdata['po_id'], 'confirmation_no' => $postdata['confirmation_no'], 'invoice_ref' => $postdata['invoice_ref'], 'contact_no' => $postdata['contact_no'], 'notice_date' => $postdata['notice_date'], 'po_meta_id' => $postdata['po_meta_id'], 'item_id' => $postdata['item_id'], 'entry' => $postdata['entry'], 'complaint' => $postdata['complaint'], 'item_desc' => $postdata['item_desc'], 'quantity' => $postdata['quantity'], 'cque' => $postdata['cque'], 'reason' => $postdata['reason'], 'solution' => $postdata['solution'], 'action' => $postdata['action'], 'reported_by' => $postdata['reported_by'], 'qcr_date' => $postdata['qcr_date'], 'prepared_by' => $postdata['prepared_by'], 'status' => $postdata['status'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_quality_control_report", $data, wpdb_data_format($data));
        $msg=1;
    }

    $sp_id = get_user_meta($current_user->ID, 'sales_person', true);
    $sales_person = get_sales_person($sp_id);
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .chosen-container,#client_name_chosen{min-width:250px!important}
        .bg-gray{background-color: rgba(0,0,0,.05);}
        .bg-white{background-color: white;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Create Quality Control Report</h1>
        <a href="admin.php?page=quality-control-report"  class="page-title-action" >Back</a>
        <br/>
        <br/>
        <div id="welcome-to-aquila" class="postbox">
            <div class="inside">
                <?php if (!empty($msg)) { ?>
                    <br/>
                    <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success!</strong> Your Quality Control Report has been saved successfully.
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
                            <label>PO #:<span class="text-red">*</span></label><br/>
                            <input type="text" name="po_id" id="po_id" placeholder="PO No" required >
                        </div>
                        <div class="col-sm-3">
                            <label>Confirmation #:<span class="text-red">*</span></label><br/>
                            <input type="text"  name="confirmation_no" id="confirmation_no" placeholder="Confirmation No" required >
                        </div>
                        <div class="col-sm-3">
                            <label>Invoice Ref:<span class="text-red">*</span></label><br/>
                            <input type="text" name="invoice_ref" placeholder="Invoice Ref" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Phone:<span class="text-red">*</span></label><br/>
                            <input type="text" id="client-phone" name="contact_no" placeholder="Phone" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Notice Date:<span class="text-red">*</span></label><br/>
                            <input type="date" name="notice_date" required>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="tbl-items" border="1" class="table-striped text-center boder-collapse" style="width: 100%;">
                                <tr class="bg-gray">
                                    <th style="width:50px">Entry</th>
                                    <th style="width:100px">Supplier</th>
                                     <th colspan="2">Complaint Description</th>
                                    <th colspan="2">Item Description</th>
                                    <th>Collection</th>
                                    <th style="width:75px">Category</th>
                                    <th style="width:100px">QTY</th>
                                    <th style="width:100px">CQUE</th>
                                </tr>
                                <tr class="bg-white" valign=top>
                                    <td><input type="text" name="entry" id="entry" placeholder="Entry#" required /></td>
                                    <td><input type="text" name="supplier" id="supplier" placeholder="Supplier" readonly /></td>
                                    <td colspan="2"><textarea type="text" id="complaint" name="complaint" placeholder="Complaint Description" rows="4" required /></textarea></td>
                                    <td colspan="2"><textarea type="text" id="item_desc" name="item_desc" placeholder="Item Description" rows="4" required /></textarea></td>
                                    <td><input type="text" name="collection" id="collection" placeholder="Collection" readonly /></td>
                                    <td><input type="text" name='category' id="category" placeholder="Category" readonly /></td>
                                    <td><input type="number" name='quantity' id='quantity' min="1" placeholder="QTY" required /></td>
                                    <td><textarea type="text" name='cque' id='cque' placeholder="CQUE" rows="4" required /></textarea></td>
                                </tr>
                                <tr class="bg-white"><td colspan="10"><br/></td></tr>
                                <tr class="bg-gray">
                                    <th colspan="10">REASON OF REPORT</th>
                                </tr>
                                <tr class="bg-white" valign=top>
                                    <?php foreach (QCR_REASON as $value) { ?>
                                        <td <?= $value == 'Other' ? 'colspan=2' : '' ?>>
                                            <label for="<?= str_replace(' ', '_', $value) ?>"><?= $value ?></label>
                                        </td>
                                    <?php } ?>
                                </tr>
                                <tr class="bg-white" valign=top>
                                    <?php foreach (QCR_REASON as $value) { ?>
                                        <td <?= $value == 'Other' ? 'colspan=2' : '' ?>>
                                            <input type="radio" name="reason" id="<?= str_replace(' ', '_', $value) ?>" value="<?= $value ?>" required />
                                        </td>
                                    <?php } ?>
                                </tr>
                                <?php ?>
                                <tr class="bg-white"><td colspan="10"><br/></td></tr>
                                <tr class="bg-gray">
                                    <th colspan="10">SOLUTION</th>
                                </tr>
                                <tr class="bg-white" valign=top>
                                    <?php foreach (QCR_SOLUTION as $value) { ?>
                                        <td <?= $value == 'Parts To Be Sent' ? 'colspan=2' : ($value == 'Other' ? 'colspan=2' : '') ?>>
                                            <label for="<?= str_replace(' ', '_', ($value == 'Other' ? 'Other1' : $value)) ?>"><?= $value ?></label>
                                        </td>
                                    <?php } ?>
                                </tr>
                                <tr class="bg-white" valign=top>
                                    <?php foreach (QCR_SOLUTION as $value) { ?>
                                        <td <?= $value == 'Parts To Be Sent' ? 'colspan=2' : ($value == 'Other' ? 'colspan=2' : '') ?>>
                                            <input type="radio" name="solution" id="<?= str_replace(' ', '_', ($value == 'Other' ? 'Other1' : $value)) ?>" value="<?= $value ?>" required />
                                        </td>
                                    <?php } ?>
                                </tr>
                                <tr class="bg-white"><td colspan="10"><br/></td></tr>
                                <tr class="bg-gray">
                                    <th colspan="10">ACTION</th>
                                </tr>
                                <tr class="bg-white">
                                    <td colspan="10"><textarea type="text" name="action" placeholder="Action To Be Taken" rows="4" required /></textarea></td>
                                </tr>
                            </table>   
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-3">
                            <label>Reported By:<span class="text-red">*</span></label><br/>
                            <input type="text" name="reported_by" placeholder="Reported By" value="" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Date:<span class="text-red">*</span></label><br/>
                            <input type="date" name="qcr_date" value="<?= rb_date($date, 'Y-m-d') ?>" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Prepared By:<span class="text-red">*</span></label><br/>
                            <input type="text" name="prepared_by" placeholder="Requested By" value="<?= $sales_person->display_name ?>" required>
                        </div>
                    </div>


                    <br/>
                    <br/>
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="<?= admin_url('admin.php?page=quality-control-report') ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
                        </div>
                        <div class="col-sm-6 text-right">
                            <input type="hidden" name='item_id' id='item_id' />
                            <input type="hidden" name='po_meta_id' id='po_meta_id' />
                            <button type="submit" name='status' value="Draft" class="btn btn-dark btn-sm">Save for Later</button>
                            <button type="submit" name='status' value='Pending' class="btn btn-primary btn-sm">Save and Continue</button>
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
                    jQuery('#client-name').html('');
                    var html = '<option value="">Select Client</option>';
                    jQuery.each(data, function (i, client) {
                        var item = JSON.stringify(client);
                        html += `<option value="${client.id}" data-item='${item}'>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
            });

            jQuery('#entry').on('blur', function () {
                var entry = jQuery(this).val();
                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/get-item-by-entry.php",
                    dataType: 'json',
                    data: {entry: entry},
                    success: function (data) {
                        if (data !== null) {
                            data.po_id ? jQuery(`#po_id`).val(data.po_id) : '';
                            data.confirmation_no ? jQuery(`#confirmation_no`).val(data.confirmation_no) : '';
                            jQuery(`#po_meta_id`).val(data.id);
                            jQuery(`#item_id`).val(data.item_id);
                            jQuery(`#supplier`).val(data.supplier);
                            jQuery(`#item_desc`).val(data.item_desc);
                            jQuery(`#collection`).val(data.collection_name);
                            jQuery(`#category`).val(data.category);
                            jQuery(`#quantity`).val(data.quantity);
                            jQuery(`#cque`).val(data.cque);
                        } else {
                            alert('Invalid Entry #');
                            jQuery(`#supplier`).val('');
                            jQuery(`#item_desc`).val('');
                            jQuery(`#collection`).val('');
                            jQuery(`#category`).val('');
                            jQuery(`#category`).val(1);
                            jQuery(`#category`).val('');

                        }
                    }
                });
            });

        });


        function select_client(id) {
            var client = jQuery('#' + id).find(':selected').data('item');
            jQuery(`#client-phone`).val(client.phone);
            //jQuery(`#client-address`).val(client.address);
        }

    </script>
<?php } ?>