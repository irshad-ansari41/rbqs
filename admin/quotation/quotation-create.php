<?php

function admin_ctm_quotation_create_page() {
    global $wpdb, $current_user;
    $postdata = filter_input_array(INPUT_POST);
    $getdata = filter_input_array(INPUT_GET);
    $step = !empty($getdata['step']) ? $getdata['step'] : 0;
    $type = !empty($getdata['type']) ? $getdata['type'] : '';
    $scope = !empty($getdata['scope']) ? $getdata['scope'] : '';
    $vat = !empty($getdata['vat']) ? $getdata['vat'] : '';
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $logo = !empty($postdata['logo']) ? $postdata['logo'] : site_url();
    $date = current_time('mysql');
    if (!empty($postdata)) {
        $data = ['client_id' => $postdata['client_id'], 'type' => $postdata['type'], 'scope' => $postdata['scope'], 'vat' => $postdata['vat'], 'sales_person' => $postdata['sales_person'], 'city_id' => $postdata['city_id'], 'country_id' => $postdata['country_id'], 'word' => $postdata['word'], 'terms' => $postdata['terms'], 'notes' => $postdata['notes'], 'logo' => $logo, 'promo_type' => $postdata['promo_type'], 'special_discount' => $postdata['special_discount'], 'freight_percent' => $postdata['freight_percent'], 'freight_charge' => $postdata['freight_charge'], 'total_amount' => $postdata['total_amount'], 'status' => $postdata['status'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_quotations", $data, wpdb_data_format($data));
        $quotation_id = $wpdb->insert_id;
        save_quotation_meta($quotation_id, $postdata);
        $msg = 1;
    }
    include 'terms.php';
    if ($type == 'Stock' && $scope == 'Promotion') {
        $terms = $stock_promotion;
    } else if ($type == 'Order' && $scope == 'Promotion') {
        $terms = $order_promotion;
    } else if ($type == 'Stock') {
        $terms = $stoke_terms;
    } else if ($type == 'Project') {
        $terms = $project_terms;
    } else {
        $terms = $order_terms;
    }


    $terms_id = 'terms';
    $terms_settings = array('wpautop' => true, 'media_buttons' => false, 'textarea_name' => $terms_id, 'textarea_rows' => get_option('default_post_edit_rows', 10), 'tabindex' => '', 'editor_css' => '', 'editor_class' => '', 'teeny' => false, 'dfw' => false, 'tinymce' => true, 'quicktags' => true);

    $notes = '';
    $notes_id = 'notes';
    $notes_settings = array('wpautop' => true, 'media_buttons' => false, 'textarea_name' => $notes_id, 'textarea_rows' => get_option('default_post_edit_rows', 3), 'tabindex' => '', 'editor_css' => '', 'editor_class' => '', 'teeny' => false, 'dfw' => false, 'tinymce' => true, 'quicktags' => true);

    $is_contry = 1;
    if ($type == 'Project') {
        $is_contry = "country_id=224";
    } elseif ($type == 'Stock' && $scope == 'Local') {
        //$is_contry = 1;
    } elseif ($type == 'Stock' && ($vat == 'wovat' || $vat == 'wvat')) {
        $is_contry = "country_id!=224";
    } else if ($type == 'Order' && $scope == 'local') {
        $is_contry = "country_id=224";
    } elseif ($type == 'Order' && ($vat == 'wovat' || $vat == 'wvat')) {
        $is_contry = "country_id!=224";
    }

    $countries = get_cache_results("SELECT id, name FROM {$wpdb->prefix}ctm_country WHERE id IN (select distinct concat(country_id) from {$wpdb->prefix}ctm_locations where $is_contry)", ['day' => true]);
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .chosen-container,#client_name_chosen{min-width:250px!important}
        .total {font-weight: bold;text-align: center;}
    </style>
    <input id="discount" type=hidden value='0' />
    <input id="vat_value" type=hidden  value="<?= $vat == 'wovat' ? 0 : 5 ?>"/>
    <div id="welcome-to-aquila" class="postbox">
        <div class="inside">
            <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
            <?php if (!empty($msg)) { ?>
                <br/>
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> Your quotation has been save successfully.
                </div>
            <?php } ?>
            <form id="new-quotation-form" method="post" action="">
                <input type="hidden" name="type" value="<?= ucfirst($type) ?>">
                <input type="hidden" name="scope" value="<?= ucfirst($scope) ?>">
                <input type="hidden" name="vat" value="<?= $vat ?>">
                <input type="hidden" name="sales_person" value="<?= get_user_meta($current_user->ID, 'sales_person', true) ?>">
                <div class="row">
                    <div class="col-sm-3"><br/>Promotion Logo<br/>
                        <input id="promotion_logo" class="button-primary" type="button" value="Add Promotion Logo" /><br/>
                        <output id="promotion-logo"></output>
                    </div>
                    <div class="col-sm-6 text-center"><br/>
                        <h2 class="hndle ui-sortable-handle text-center"><span><?= $type ?> Quotation  <?= $scope ? ' - ' . $scope : '' ?>
                                <?= $vat == 'wvat' ? ' w/ VAT ' : ($vat == 'wovat' ? ' Zero VAT ' : '') ?></span></h2><br/>
                    </div>
                </div>
                <?php if ($scope == 'Promotion') { ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <div style="background:lightgoldenrodyellow;padding: 15px;">
                                <label>Promotion Type:<span class="text-red">*</span></label><br/>
                                <label><input type="radio" name="promo_type" class="promo_type" value="Local" required />Local</label>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                <label><input type="radio" name="promo_type" class="promo_type" value="Export" required />Export</label>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    echo '<input type="hidden" name="promo_type" value=""  />';
                }
                ?>

                <div class="row">

                    <div class="col-sm-4">
                        <label>Client Name:<span class="text-red">*</span></label><br/>
                        <select name="client_id" id="client-name" class="chosen-select" onchange="select_client(this.id)" required>
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Email:<span class="text-red">*</span></label><br/>
                        <input type="email" id="client-email"  placeholder="Email" required readonly>
                    </div>
                    <div class="col-sm-4">
                        <label>Phone:<span class="text-red">*</span></label><br/>
                        <input type="text"  id="client-phone"  placeholder="Phone" required readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <label>Address:<span class="text-red">*</span></label><br/>
                        <textarea id="client-address" placeholder="Address" required rows="1" readonly></textarea>
                    </div>
                    <div class="col-sm-4">
                        <label>Roche Bobois TRN#:<span class="text-red">*</span></label><br/>
                        <input type="text" id="company-trn"  placeholder="TRN #" required value="100383178900003" readonly>
                    </div>
                    <div class="col-sm-4">
                        <label>Country:<span class="text-red">*</span></label><br/>
                        <select name="country_id" id='country_id' class="chosen-select" onchange="change_country(this.id)" required>
                            <option value="">Select Country</option>
                            <?php
                            foreach ($countries as $value) {
                                $cities = $wpdb->get_results("SELECT id,city FROM {$wpdb->prefix}ctm_locations where country_id='{$value->id}' AND status='Active' ORDER BY city ASC")
                                ?>
                                <option value="<?= $value->id ?>" data-cities='<?= json_encode($cities) ?>'><?= $value->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>City:<span class="text-red">*</span></label><br/>
                        <select name="city_id" class="chosen-select" id='city_id' onchange="change_city(this.value, '<?= $type ?>', '<?= $scope ?>', '<?= $vat ?>')" required>
                            <option value="">Select City</option>
                        </select>
                    </div>
                </div>
                <hr/>
                <br/>
                <br/>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="tbl-items" border="1" class="table-striped text-center boder-collapse" style="width: 100%;">
                            <thead class="bg-blue">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Sr.</th>
                                    <th style="width:140px">Entry #</th>
                                    <th style="width:150px">Item Name</th>
                                    <th>Item Description</th>
                                    <th width="100">Supplier Code</th>
                                    <th width="100">Price</th>
                                    <th width="90">Quantity</th>
                                    <th width="90">Discount(%)</th>
                                    <th width="100">Net Price<br/><?= $type == 'Stock' ? '(Excl. Vat)' : '(After Discount)' ?></th>
                                    <th width="75">VAT<br/>@<span id="show_vat_value"><?= $vat == 'wovat' ? 0 : 5 ?></span>%</th>
                                    <th width="100">Total</th>
                                </tr>
                            </thead>
                            <tr id="tbl-row-1" valign=top>
                                <td><input type="checkbox" name="row" data-id="tbl-row-1"></td>
                                <td>1.</td>
                                <td><input type="text" name="items[1][entry]" id="input-entry-1" <?= $type != 'Stock' ? 'disabled' : '' ?> /></td>
                                <td><input type="hidden"  name="items[1][po_meta_id]" id="input-stock-1" />
                                    <select name="items[1][item_id]" id="sel-name-1" class="chosen-select"  onchange="select_item(this.id, this.value, 1)" required>
                                        <option value="">Loading...</option>
                                    </select>
                                </td>
                                <td><textarea name="items[1][desc]" id="input-desc-1" rows="5"></textarea></td>
                                <td><input type="text" name="items[1][sup_code]" id="input-code-1" required readonly></td>
                                <td><input type="number" name="items[1][price]" id="input-price-1" step="0.01" min=0  class="inputprice" required></td>
                                <td><input type="number" name="items[1][qty]" id="input-qty-1" min=1 max=999 class="inputqty" required></td>
                                <td><input type="text" name="items[1][dis]" id="input-dis-1" min=0 max=100 value="0"  data-disc_value=0  readonly></td>
                                <td><input type="text" name="items[1][net]" id="input-net-1" step="0.01" min=0 value="0" readonly required></td>
                                <td><input type="text" name="items[1][vat]" id="input-vat-1" step="0.01" min=0 value="0" readonly required ></td>
                                <td><input type="text" name="items[1][total]" id="input-total-1" step="0.01" min=0  value="0" readonly required></td>
                            </tr>



                            <tr>
                                <td colspan="4" class="text-left">
                                    <button type="button" id="add-row" class="btn btn-dark text-white btn-sm">Add Item</button>
                                    <button type="button" id="delete-row" class="btn btn-danger text-white btn-sm">Delete Item</button>
                                    <?php if (current_user_can('rb_special_discount')) { ?>
                                        <button type="button" id="add-special-discount" class="btn btn-warning text-white btn-sm">Add Special Discount</button>
                                    <?php } ?>
                                </td>
                                <td>
                                    <div class="special-discount-tr hidden">
                                        <b>Special Discount:</b> <input type="number" step="0.00000000001" name="special_discount" value="0" id='special-discount-percent' style="width:100px" /> %
                                    </div>
                                </td>
                                <td colspan="4"><strong>Sub Total</strong></td>
                                <td><strong id='total-net-amt'>0.0</strong></td>
                                <td><strong id='total-vat'>0.0</strong></td>
                                <td><strong id='sub-total'>0.0</strong><input type="hidden" name="total_amount" value="0" id='total'></td>
                            </tr>

                            <tr>
                                <td colspan="5"></td>
                                <td colspan="4"><b>Freight Charge:</b> <input type="text" id="freight-percent" name="freight_percent" value="0" style="width: 50px;" readonly /> %</td>
                                <td colspan="2"></td>
                                <td><input type="text" id="freight-charge" readonly name="freight_charge" class="total" value="0" /></td>
                            </tr>


                            <tr><td class="text-right">AED:</td>
                                <td colspan="4"><input type="text" name="word" value="" id='word' readonly></td>
                                <td colspan="4"><strong>TOTAL</strong></td>
                                <td colspan="2"></td>
                                <td><input type="text" name="total_amount" id='total-amt' class="total" value='0' readonly /></td>
                            </tr>

                        </table>                                               
                    </div>

                </div>
                <hr/>
                <br/>

                <div class="row">
                    <div class="col-sm-12">
                        <?php wp_editor($terms, $terms_id, $terms_settings); ?>
                        <br/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <label>Notes:</label><br/>
                        <?php wp_editor($notes, $notes_id, $notes_settings); ?>
                        <br/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <a href="<?= gen_quote_back_url($page, $step, $type, $scope) ?>" class="btn btn-secondary text-white btn-sm">&lt; - Back</a>
                    </div>
                    <div class="col-sm-6 text-right">
                        <br/>
                        <button type="submit" name="status" value="Draft" class="btn btn-dark btn-sm">Save for Later</button>
                        <button type="submit" name="status" value="Pending" class="btn btn-primary btn-sm">Save and Continue</button>
                    </div>
                </div>
                <br/>
                <br/>

            </form>
        </div>
    </div>
    <script>
        var promoType = '';
        var type = '<?= $type ?>';
        jQuery(document).ready(() => {
            jQuery('#add-row,#delete-row').hide();
            jQuery('.chosen-select').chosen();
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery('#sel_name_1_chosen').mouseenter(function () {
                if (jQuery('#country_id').val() === '' || jQuery('#client-name').val() === '' || jQuery('#city_id').val() === '') {
                    alert('Please select client, country & city before adding item.');
                }
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

            jQuery('#add-special-discount').click(function () {
                jQuery('.special-discount-tr').toggleClass('hidden');

            });

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-items.php",
                dataType: 'json',
                data: {type: type},
                success: function (data) {
                    jQuery('#sel-name-1').html('');
                    var html = '<option value="">Select Item</option>';
                    jQuery.each(data, function (i, item) {
                        var po_meta_id = type === 'Stock' ? `data-po_meta_id=${item.po_meta_id}` : '';
                        var quantity = type === 'Stock' ? `data-quantity=${item.quantity}` : '';
                        html += `<option value="${item.id}" ${po_meta_id} ${quantity}>${item.collection_name}</option>`;
                    });
                    jQuery('#sel-name-1').html(html);
                    jQuery('#sel-name-1').trigger("chosen:updated");
                    jQuery('#add-row,#delete-row').show();
                }
            });

            jQuery('.promo_type').click(function () {
                promoType = jQuery('input[name="promo_type"]:checked').val();
                if (promoType === 'Local') {
                    jQuery('#vat_value').val(5);
                    jQuery('#show_vat_value').text(5);
                }
                if (promoType === 'Export') {
                    jQuery('#vat_value').val(0);
                    jQuery('#show_vat_value').text(0);
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
                        <td><input type="text" name="items[${rowCount}][entry]" id="input-entry-${rowCount}" <?= $type != 'Stock' ? 'disabled' : '' ?> /></td>
                        <td><input type="hidden" name="items[${rowCount}][po_meta_id]" id="input-stock-${rowCount}" />
                        <select name="items[${rowCount}][item_id]" class="chosen-select" id="sel-name-${rowCount}" 
                        onchange="select_item(this.id,this.value,${rowCount})" required>${items}</select></td>
            <td><textarea name="items[${rowCount}][desc]" id="input-desc-${rowCount}" rows="5"></textarea></td>
            <td><input type="text" name="items[${rowCount}][sup_code]" id="input-code-${rowCount}" required readonly></td>
            <td><input type="number" name="items[${rowCount}][price]" step="0.01" min=0  id="input-price-${rowCount}" class="inputprice" required></td>
            <td><input type="number" name="items[${rowCount}][qty]"  id="input-qty-${rowCount}" min="1" max="999" class="inputqty" required></td>
            <td><input type="text" name="items[${rowCount}][dis]" min=0 max=100 value=0 data-disc_value=0 id="input-dis-${rowCount}" readonly></td>
            <td><input type="text" name="items[${rowCount}][net]" step="0.01" min=0  id="input-net-${rowCount}" readonly required></td>
            <td><input type="text" name="items[${rowCount}][vat]" step="0.01" min=0  id="input-vat-${rowCount}" readonly required></td>
            <td><input type="text" name="items[${rowCount}][total]" step="0.01" min=0 id="input-total-${rowCount}" readonly required></td>
                            </tr>`;
                jQuery('#tbl-row-' + (rowCount - 1)).after(tr);
                jQuery('.chosen-select').chosen();

                refresh_calculation();

            });
            refresh_calculation();

            setInterval(function () {
                calculate();
            }, 3000);


        });



        function refresh_calculation() {
            setTimeout(function () {
                calculate();
            }, 1000);
            jQuery('#special-discount-percent,.inputprice,.inputqty').on('input keyup keypress blur', function () {
                calculate();
            });
        }

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


        function select_item(id, item_id, number) {
            var po_meta_id, quantity = 0;
            if (type === 'Stock') {
                po_meta_id = jQuery(`#${id}`).find(':selected').data('po_meta_id');
                quantity = jQuery(`#${id}`).find(':selected').data('quantity');
                jQuery(`#input-stock-${number}`).val(po_meta_id);
                jQuery(`#input-qty-${number}`).attr('max', `${quantity}`);
            }

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-item.php",
                cache: false,
                method: 'get',
                data: {id: item_id, po_meta_id: po_meta_id, type: type},
                dataType: 'json',
                success: function (item) {
                    if (item) {
                        jQuery(`#input-desc-${number}`).val(`${item.description}`);
                        jQuery(`#input-code-${number}`).val(`${item.sup_code}`);
                        jQuery(`#input-dis-${number}`).val(jQuery('#discount').val());
                        jQuery(`#input-dis-${number}`).data('disc_value', jQuery('#discount').val());
                        jQuery(`#input-entry-${number}`).val(`${item.entry}`);
                    } else {
                        jQuery(`#input-desc-${number}`).attr('placeholder', 'Item Description').val('');
                        jQuery(`#input-code-${number}`).attr('placeholder', 'Supplier Code').val('');
                        jQuery(`#input-entry-${number}`).attr('placeholder', 'Entry #').val('');
                    }
                }
            });

        }

        function select_client(id) {
            var client = jQuery('#' + id).find(':selected').data('item');
            jQuery(`#client-email`).val(client.email);
            jQuery(`#client-phone`).val(client.phone);
            jQuery(`#client-address`).val(client.address);
        }

        function calculate() {
            var tamt = 0, tvat = 0, incl = 0, vat = 0, amt = 0, freight_charge = 0;
            var freight = parseInt(jQuery(`#freight-percent`).val(), 10);
            var special_discount = jQuery(`#special-discount-percent`).val();
            var vat_value = parseInt(jQuery(`#vat_value`).val(), 10);
            var vat_name = promoType === 'Export' ? 'wovat' : '<?= $vat ?>';
            jQuery('table#tbl-items > tbody  > tr').each(function (index, tr) {
                if (tr.id !== '') {
                    var i = tr.id.replace('tbl-row-', '');

                    var dis_per = jQuery(`#input-dis-${i}`).data('disc_value');
                    jQuery(`#input-dis-${i}`).val(parseFloat(dis_per) + parseFloat(special_discount));

                    var dis = parseFloat((jQuery(`#input-price-${i}`).val() * jQuery(`#input-dis-${i}`).val()) / 100);
                    var price = jQuery(`#input-price-${i}`).val() - dis;
                    incl = (price * (jQuery(`#input-qty-${i}`).val())).toFixed(2);
                    var percent = vat_value;

                    if (type === 'Project') {
                        //exclusive
                        vat = ((incl * percent) / 100).toFixed(2);
                        amt = parseFloat(incl) + parseFloat(vat);
                        jQuery(`#input-net-${i}`).val(incl);
                        jQuery(`#input-vat-${i}`).val(vat);
                        jQuery(`#input-total-${i}`).val(amt);
                        tamt += parseFloat(incl);
                        tvat += parseFloat(vat);
                    } else if (vat_name === 'wvat') {
                        amt = ((incl / (100 + percent)) * 100).toFixed(2).replace(/\./g, '.').replace('.00', '');
                        vat = (incl - amt).toFixed(2);
                        jQuery(`#input-net-${i}`).val(amt);
                        jQuery(`#input-vat-${i}`).val(vat);
                        jQuery(`#input-total-${i}`).val(incl);
                        tamt += parseFloat(amt);
                        tvat += parseFloat(vat);
                    } else if (vat_name === 'wovat') {
                        //without
                        vat = 0;
                        amt = ((incl / (100 + 5)) * 100).toFixed(2).replace(/\./g, '.').replace('.00', '');
                        jQuery(`#input-net-${i}`).val(amt);
                        jQuery(`#input-vat-${i}`).val(vat);
                        jQuery(`#input-total-${i}`).val(amt);
                        tamt += parseFloat(amt);
                        tvat += parseFloat(vat);
                    } else {
                        //inclusive
                        amt = ((incl / (100 + percent)) * 100).toFixed(2).replace(/\./g, '.').replace('.00', '');
                        vat = (incl - amt).toFixed(2);
                        jQuery(`#input-net-${i}`).val(amt);
                        jQuery(`#input-vat-${i}`).val(vat);
                        jQuery(`#input-total-${i}`).val(incl);
                        tamt += parseFloat(amt);
                        tvat += parseFloat(vat);
                    }
                }
            });

            freight_charge = ((tamt * freight) / 100).toFixed(2);
            var sub_total = tvat + tamt;
            var total = (parseFloat(tamt) + parseFloat(tvat) + parseFloat(freight_charge)).toFixed(2);

            jQuery('#total-vat').text(tvat.toFixed(2));
            jQuery('#total-net-amt').text(tamt.toFixed(2));
            jQuery('#sub-total').text(sub_total.toFixed(2));
            jQuery('#freight-charge').val(freight_charge);
            jQuery('#total_amount').val(total);
            jQuery('#total-amt').val(total);
            number_to_word('#word', total);
        }

        function change_country(id) {
            jQuery('#city_id').empty();
            var options = ' <option value="">Select City</option>';
            var cities = jQuery(`#${id}`).find(':selected').data('cities');
            jQuery.each(cities, (i, item) => {
                options += `<option value='${item.id}'>${item.city}</option>`;
            });
            jQuery('#city_id').append(options);
            jQuery('#city_id').trigger("chosen:updated");

        }

        function change_city(id, type, scope, vat) {
            var promo_type = '';
            var promoType = jQuery('input[name="promo_type"]:checked').val();
            if (promoType !== '' || promoType !== 'undefined') {
                promo_type = promoType;
            }

            if (promoType === 'Local') {
                jQuery('#vat_value').val(5);
                jQuery('#show_vat_value').text(5);
            }

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-freight-charge.php",
                cache: false,
                method: 'post',
                data: {city_id: id, type: type, scope: scope, vat: vat, promo_type: promo_type},
                dataType: 'json',
                success: function (result) {
                    jQuery('#freight-percent').val(result.freight_charge);
                }
            });

            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-discount.php",
                cache: false,
                method: 'post',
                data: {city_id: id, type: type, scope: scope, vat: vat, promo_type: promo_type},
                dataType: 'json',
                success: function (result) {
                    jQuery('#discount').val(result.discount);
                }
            });

        }

        function number_to_word(id, amount) {
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/number-to-word.php",
                cache: false,
                method: 'get',
                data: {amount: amount},
                success: function (word) {
                    jQuery(id).val(word);
                }
            });
        }


        jQuery('#promotion_logo').click(function (e) {
            file_uploader(e, 'logo', 'promotion-logo', false);
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
                    if (multiple == false) {
                        jQuery('#' + output).html('');
                    }
                    jQuery('#' + output).append("<input type='hidden' name='" + input + "' value='" + attachment.id + "'><img src='" + attachment.url + "' style='width:250px'>");
                }).join();
            });
            custom_uploader.open();
        }


    </script>
    <?php
}
