<?php

function admin_ctm_supplier_add() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    if (!empty($postdata)) {

        $name = !empty($postdata['name']) ? trim($postdata['name']) : '';
        $sup_code = !empty($postdata['sup_code']) ? trim($postdata['sup_code']) : '';
        $address = !empty($postdata['address']) ? trim($postdata['address']) : '';
        $email = !empty($postdata['email']) ? trim($postdata['email']) : '';
        $phone = !empty($postdata['phone']) ? trim($postdata['phone']) : '';
        $fax = !empty($postdata['fax']) ? trim($postdata['fax']) : '';
        $contact_person = !empty($postdata['contact_person']) ? trim($postdata['contact_person']) : '';
        $credit_terms = !empty($postdata['credit_terms']) ? trim($postdata['credit_terms']) : '';
        $supplier_charges = !empty($postdata['supplier_charges']) ? trim($postdata['supplier_charges']) : '';
        $iban = !empty($postdata['iban']) ? trim($postdata['iban']) : '';
        $swift_code = !empty($postdata['swift_code']) ? trim($postdata['swift_code']) : '';
        $country_origin = !empty($postdata['country_origin']) ? trim($postdata['country_origin']) : '';
        $trn = !empty($postdata['trn']) ? trim($postdata['trn']) : '';
        $sup_type = !empty($postdata['sup_type']) ? trim($postdata['sup_type']) : '';

        $fields = "name='$name', sup_code='$sup_code', address='$address',email='$email', phone='$phone',fax='$fax', contact_person='$contact_person', credit_terms='$credit_terms', supplier_charges='$supplier_charges', iban='$iban', swift_code='$swift_code', trn='$trn',sup_type='$sup_type', country_origin='$country_origin', updated_by='{$current_user->ID}', updated_at='{$date}'";

        $wpdb->query("INSERT INTO {$wpdb->prefix}ctm_suppliers SET {$fields}, created_by='{$current_user->ID}', created_at='{$date}'");
        wp_redirect("admin.php?page=rw-suppliers&msg=added");
    }

    $countries = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_country ORDER BY name ASC");
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; max-width: 480px;}.chosen-container{width:100%!important;max-width: 480px;}</style>
    <div class="wrap">
        <h1 class="wp-heading-inline">Add Supplier</h1>
        <a href="<?= admin_url('admin.php?page=rw-suppliers') ?>" id="add-new-item" class="page-title-action">Back</a><br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="add-new-item-form"  method="post">
                                    <input type="hidden" name="page"  value="<?= $page ?>" >

                                    <input type="hidden" name="action" value="Add" />

                                    <table class="form-table" style="width:100%">
                                        <tr>
                                            <td><label>Supplier Type:<span class="text-red">*</span></label><br/>
                                                <label class="font-weight-normal m-0">
                                                    <input type="radio" name="sup_type" class="sup_type" value="Local"  required>Local</label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <label class="font-weight-normal m-0">
                                                    <input type="radio" name="sup_type"  class="sup_type" value="International" required>International</label>
                                            </td>
                                            <td><div id="trn-div">
                                                    <label>TRN No:<span class="text-red">*</span></label><br/>
                                                    <input type="text" name="trn" id="trn" placeholder="TRN No" >
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Supplier Name:<span class="text-red">*</span></label><br/>
                                                <input type="text" name="name"  placeholder="Supplier Name" required>
                                            </td>

                                            <td><label>Supplier Code:<span class="text-red local">*</span></label><br/>
                                                <input type="text" name="sup_code" class="local"  placeholder="Supplier Code" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Supplier Email:<span class="text-red local">*</span></label><br/>
                                                <input type="email" name="email" class="local" placeholder="Supplier Email" required> 
                                            </td>

                                            <td><label>Supplier Phone:<span class="text-red local">*</span></label><br/>
                                                <input type="text" name="phone" class="local" placeholder="Supplier Phone" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Supplier Fax:</label><br/>
                                                <input type="text" name="fax" placeholder="Supplier Fax" >
                                            </td>
                                            <td><label>Contact Person:<span class="text-red local">*</span></label><br/>
                                                <input type="text" name="contact_person" class="local"  placeholder="Contact Person" required>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td><label>Country of Origin:</label><br/>
                                                <select name="country_origin" class="chosen-select">
                                                    <option value="">Select Country</option> 
                                                    <?php foreach ($countries as $value) { ?>
                                                        <option value="<?= $value->id ?>"><?= $value->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>  
                                        
                                            <td><label>Address:<span class="text-red local">*</span></label><br/>
                                                <textarea type="text"  name="address"   class="local"  placeholder="Address" row="4" required /></textarea>
                                            </td>


                                        </tr>
                                        <tr>
                                            <td ><label>Credit Terms:<span class="text-red local">*</span></label><br/>
                                                <input type="text" name="credit_terms" class="local"  placeholder="Credit Terms" required/>
                                            </td>
                                        
                                            <td><label>Packing & Transport Charges (%) :<span class="text-red local">*</span></label><br/>
                                                <input type="number" name="supplier_charges" class="local" min='0' max="100" step="0.01" class="local"  placeholder="Packing & Transport Charges (%)"  required/>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><label>IBAN:<span class="text-red local">*</span></label><br/>
                                                <input type="text" name="iban" class="local" placeholder="IBAN" required> 
                                            </td>

                                            <td><label>Swift Code:<span class="text-red local">*</span></label><br/>
                                                <input type="text" name="swift_code" class="local"  placeholder="Swift Code" required>
                                            </td>
                                        </tr>
                                        <tr>

                                        </tr>
                                        <tr>
                                            <td>
                                                <br/><input type="submit"  name="action" value="Add" class="button-primary" >
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="?page=<?= $page ?>"  class="button-secondary" >Cancel</a>
                                            </td>
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
        var trn = '<?= !empty($trn) ? $trn : '' ?>';
        jQuery(document).ready(() => {

            if (trn === '') {
                jQuery('#trn-div').hide();
            } else {
                jQuery('input.local,textarea.local').val('').removeAttr('required');
                jQuery('span.local').hide();
            }
            jQuery('.sup_type').click(function () {
                type = jQuery('input[name="sup_type"]:checked').val();
                if (type === 'Local') {
                    jQuery('#trn-div').show();
                    jQuery('#trn').val(trn).prop('required', true);
                    jQuery('input.local,textarea.local').val('').removeAttr('required');
                    jQuery('span.local').hide();
                }
                if (type === 'International') {
                    jQuery('#trn-div').hide();
                    jQuery('#trn').val('').removeAttr('required');

                    jQuery('input.local,textarea.local').prop('required', true);
                    jQuery('span.local').show();

                }
            });
            jQuery('.chosen-select').chosen();
        });
    </script>
    <?php
}
