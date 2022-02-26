<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_employee_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=employee'));
        exit();
    }

    if (!empty($postdata['update'])) {

        $data = [
            'name' => $postdata['name'],
            'nationality' => $postdata['nationality'],
            'address_home_country' => $postdata['address_home_country'],
            'address_current' => $postdata['address_current'],
            'ecd_name_1' => $postdata['ecd_name_1'],
            'ecd_rel_1' => $postdata['ecd_rel_1'],
            'ecd_address_1' => $postdata['ecd_address_1'],
            'ecd_phone_1' => $postdata['ecd_phone_1'],
            'ecd_name_2' => $postdata['ecd_name_2'],
            'ecd_rel_2' => $postdata['ecd_rel_2'],
            'ecd_address_2' => $postdata['ecd_address_2'],
            'ecd_phone_2' => $postdata['ecd_phone_2'],
            'sponsored_by' => $postdata['sponsored_by'],
            'designation' => $postdata['designation'],
            'department' => $postdata['department'],
            'joining_date' => $postdata['joining_date'],
            'contract_type' => $postdata['contract_type'],
            'visa' => $postdata['visa'],
            'visa_status' => $postdata['visa_status'],
            'eid_number' => $postdata['eid_number'],
            'eid_issue_date' => $postdata['eid_issue_date'],
            'eid_expiry_date' => $postdata['eid_expiry_date'],
            'eid_possession' => $postdata['eid_possession'],
            'passport_no' => $postdata['passport_no'],
            'passport_issue_date' => $postdata['passport_issue_date'],
            'passport_expiry_date' => $postdata['passport_expiry_date'],
            'passport_possession' => $postdata['passport_possession'],
            'basic_salary' => $postdata['basic_salary'],
            'hra_salary' => $postdata['hra_salary'],
            'allowance_salary' => $postdata['allowance_salary'],
            'total_salary' => $postdata['total_salary'],
            'policy_number' => $postdata['policy_number'],
            'policy_effective_date' => $postdata['policy_effective_date'],
            'policy_expiry_date' => $postdata['policy_expiry_date'],
            'work_permit_number' => $postdata['work_permit_number'],
            'wp_personal_number' => $postdata['wp_personal_number'],
            'wp_expiry_date' => $postdata['wp_expiry_date'],
            'profile_image' => $postdata['profile_image'],
            'eid_image' => $postdata['eid_image'],
            'passport_image' => $postdata['passport_image'],
            'visa_image' => $postdata['visa_image'],
            'labor_image' => $postdata['labor_image'],
            'status' => $postdata['status'],
            'updated_by' => $current_user->ID,
            'updated_at' => $date
        ];
        $wpdb->update("{$wpdb->prefix}ctm_hr_employees", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%s']);
        make_attendence_from_joing_date($id, $data['joining_date']);
    }
    $countries = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_country ORDER BY name ASC");
    $employee = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_employees WHERE id='{$id}'");
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .wp-list-table{table-layout: auto!important;}
        table tr td.collection_name{width:400px}
        table tr td.description{width:200px}
        #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;height: 100px}
        select#supplier-name:invalid,select#employee-name:invalid {
            height: 0px !important;
            opacity: 0 !important;
            position: absolute !important;
            display: flex !important;
            width:1px!important;
        }
        .chosen-container{width:100%!important;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Edit Employee</h1>
        <a href="?page=employee"  class="page-title-action" >Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="add-new-item-form" method="post">
                                    <table class="form-table" style="width:100%">

                                        <tr>
                                            <td colspan="3">
                                                <div class="check-detail">
                                                    <label>Profile Photo:</label>
                                                    <input id="input-profile-image" class="button-primary" type="button" value="Upload" /><br/>
                                                    <output id="output-profile-image"><br/>
                                                        <?= show_employee_documents($employee, 'profile_image') ?>
                                                    </output>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Name:<span class="text-red">*</span></label>
                                                <input type=text name="name"  placeholder="Name" value="<?= $employee->name ?>" required>
                                            </td>

                                            <td><label>Nationality:<span class="text-red">*</span></label>
                                                <select name="nationality" class="chosen-select" required>
                                                    <option value="">Select Country</option> 
                                                    <?php foreach ($countries as $value) { ?>
                                                        <option value="<?= $value->id ?>" <?= $employee->nationality == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <label>ADDRESS</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Home Country:<span class="text-red">*</span></label>
                                                <textarea name="address_home_country" required><?= $employee->address_home_country ?></textarea>
                                            </td>
                                            <td><label>Current:<span class="text-red">*</span></label>
                                                <textarea name="address_current" required><?= $employee->address_current ?></textarea>
                                            </td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <label>EMERGENCY CONTACT DETAILS</label>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Address Home Country</td>
                                            <td><label>Current</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>Name:<span class="text-red">*</span></label>
                                                <input type="text" name="ecd_name_1" value="<?= $employee->ecd_name_1 ?>" required />
                                                <br/>
                                                <label>Relationship:<span class="text-red">*</span></label>
                                                <input type="text" name="ecd_rel_1"  value="<?= $employee->ecd_rel_1 ?>" required />
                                                <br/>
                                                <label>Address:<span class="text-red">*</span></label>
                                                <textarea name="ecd_address_1"  required><?= $employee->ecd_address_1 ?></textarea>
                                                <br/>
                                                <label>Contact No:<span class="text-red">*</span></label>
                                                <input type="text" name="ecd_phone_1"  value="<?= $employee->ecd_phone_1 ?>" required />
                                            </td>
                                            <td>
                                                <label>Name:<span class="text-red">*</span></label>
                                                <input type="text" name="ecd_name_2"  value="<?= $employee->ecd_name_2 ?>" required />
                                                <br/>
                                                <label>Relationship:<span class="text-red">*</span></label>
                                                <input type="text" name="ecd_rel_2"  value="<?= $employee->ecd_rel_2 ?>" required />
                                                <br/>
                                                <label>Address:<span class="text-red">*</span></label>
                                                <textarea name="ecd_address_2" required><?= $employee->ecd_address_2 ?></textarea>
                                                <br/>
                                                <label>Contact No:<span class="text-red">*</span></label>
                                                <input type="text" name="ecd_phone_2"  value="<?= $employee->ecd_phone_2 ?>" required />
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Sponsored By:<span class="text-red">*</span></label>
                                                <input type=text name="sponsored_by"  placeholder="Sponsored By" value="<?= $employee->sponsored_by ?>" required>
                                            </td>
                                            <td><label>Designation:<span class="text-red">*</span></label>
                                                <select name='designation' required>
                                                    <option value="">Select Designation</option>
                                                    <?php
                                                    foreach (DESIGNATION as $value) {
                                                        $selected = $employee->designation == $value ? 'selected' : '';
                                                        echo "<option value='$value' $selected>$value</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><label>Department:<span class="text-red">*</span></label>
                                                <select name='department' required>
                                                    <option value="">Select Department</option>
                                                    <?php
                                                    foreach (DEPARTMENTS as $value) {
                                                        $selected = $employee->department == $value ? 'selected' : '';
                                                        echo "<option value='$value' $selected>$value</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Joining Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="joining_date"  placeholder="Joining Date" value="<?= $employee->joining_date ?>" required>
                                            </td>
                                            <td><label>Type of Contract:<span class="text-red">*</span></label>
                                                <input type=text name="contract_type"  placeholder="Type of Contract" value="<?= $employee->contract_type ?>" required>
                                            </td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td><label>Visa:<span class="text-red">*</span></label>
                                                <input type=text name="visa"  placeholder="Visa" value="<?= $employee->visa ?>" required>
                                            </td>
                                            <td><label>Visa Status:<span class="text-red">*</span></label>
                                                <input type=text name="visa_status"  placeholder="Visa Status" value="<?= $employee->visa_status ?>" required>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label>EMIRATES ID DETAILS</label>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td><label>EID Number:<span class="text-red">*</span></label>
                                                <input type=text name="eid_number"  placeholder="EID Number" value="<?= $employee->eid_number ?>" required>
                                            </td>
                                            <td><label>EID Issue Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="eid_issue_date" value="<?= $employee->eid_issue_date ?>" required>
                                            </td>
                                            <td><label>EID Expiry Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="eid_expiry_date" value="<?= $employee->eid_expiry_date ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><label>EID Possession:<span class="text-red">*</span></label>&nbsp;&nbsp;&nbsp;
                                                <label><input type=radio name="eid_possession" value="Employer" required
                                                              <?= $employee->eid_possession == 'Employer' ? 'checked' : '' ?> >Employer</label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <label><input type=radio name="eid_possession"  value="Employee" required  
                                                              <?= $employee->eid_possession == 'Employee' ? 'checked' : '' ?>>Employee</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label>PASSPORT DETAILS</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label>Passport Number:<span class="text-red">*</span></label><br/>
                                                <input type=text name="passport_no" value="<?= $employee->passport_no ?>" required>
                                            </td>
                                            <td><label>Passport Issue Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="passport_issue_date" value="<?= $employee->passport_issue_date ?>" required>
                                            </td>
                                            <td><label>Passport Expiry Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="passport_expiry_date" value="<?= $employee->passport_expiry_date ?>" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><label>Passport Possession:<span class="text-red">*</span></label>&nbsp;&nbsp;&nbsp;
                                                <label><input type=radio name="passport_possession" value="Employer" required
                                                              <?= $employee->passport_possession == 'Employer' ? 'checked' : '' ?> >Employer</label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <label><input type=radio name="passport_possession"  value="Employee" required  
                                                              <?= $employee->passport_possession == 'Employee' ? 'checked' : '' ?>>Employee</label>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <label>SALARY DETAILS</label>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Basic Salary:<span class="text-red">*</span></label>
                                                <input type=number name="basic_salary" step="0.01" min='0' placeholder="Basic Salary" value="<?= $employee->basic_salary ?>" required >
                                            </td>
                                            <td><label>HRA:<span class="text-red">*</span></label>
                                                <input type=number name="hra_salary"  placeholder="HRA" value="<?= $employee->hra_salary ?>" required />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><label>Other Allowance:<span class="text-red">*</span></label>
                                                <input type=number name="allowance_salary" step="0.01" min='0' placeholder="Other Allowance" value="<?= $employee->allowance_salary ?>" required >
                                            </td>
                                            <td><label>Total:<span class="text-red">*</span></label>
                                                <input type=number name="total_salary"  placeholder="Total" value="<?= $employee->total_salary ?>" required />
                                            </td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <label>INSURANCE DETAILS</label>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Policy Number:<span class="text-red">*</span></label>
                                                <input type=text name="policy_number" placeholder="Policy Number" value="<?= $employee->policy_number ?>" required >
                                            </td>
                                            <td><label>Policy Effective Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="policy_effective_date" value="<?= $employee->policy_effective_date ?>"  required />
                                            </td>

                                            <td style="text-align:left"><label>Policy Expiry Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="policy_expiry_date" value="<?= $employee->policy_expiry_date ?>" required />
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <label>LABOR CARD DETAILS</label>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>Work Permit Number:<span class="text-red">*</span></label>
                                                <input type=text name="work_permit_number" value="<?= $employee->work_permit_number ?>"required >
                                            </td>
                                            <td><label>Personal Number:<span class="text-red">*</span></label><br/>
                                                <input type=text name="wp_personal_number" value="<?= $employee->wp_personal_number ?>" required />
                                            </td>

                                            <td style="text-align:left"><label>Expiry Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="wp_expiry_date" value="<?= $employee->wp_expiry_date ?>" required />
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <label>EMPLOYEE DOCUMENTS</label>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <div class="check-detail">
                                                    <label>Emirate ID:</label>
                                                    <input id="input-eid-image" class="button-primary" type="button" value="Upload" /><br/>
                                                    <output id="output-eid-image">
                                                        <?= show_employee_documents($employee, 'eid_image') ?>
                                                    </output>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <div class="check-detail">
                                                    <label>Passport:</label>
                                                    <input id="input-passport-image" class="button-primary" type="button" value="Upload" /><br/>
                                                    <output id="output-passport-image">
                                                        <?= show_employee_documents($employee, 'passport_image') ?>
                                                    </output>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <div class="check-detail">
                                                    <label>Residence Visa:</label>
                                                    <input id="input-visa-image" class="button-primary" type="button" value="Upload" /><br/>
                                                    <output id="output-visa-image">
                                                        <?= show_employee_documents($employee, 'visa_image') ?>
                                                    </output>
                                                </div>
                                            </td>
                                        </tr>
                                        </tr>

                                        <tr>
                                        <tr>
                                            <td>
                                                <div class="check-detail">
                                                    <label>Labor Card:</label>
                                                    <input id="input-labor-image" class="button-primary" type="button" value="Upload" /><br/>
                                                    <output id="output-labor-image">                                                        
                                                        <?= show_employee_documents($employee, 'labor_image') ?>
                                                    </output>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <select  name="status" class="search-input">
                                                    <option value="">Status</option>
                                                    <option value="Active" <?= $employee->status == 'Active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="Inactive" <?= $employee->status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">
                                                <br/><button type="submit" name="update" value="Update" class="button-primary" >Update Employee</button>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="?page=employee"  class="button-secondary" >Cancel</a></td>
                                        </tr>

                                    </table>
                                    <br/><br/><br/>
                                </form>
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
        });



        jQuery('#input-profile-image').click(function (e) {
            file_uploader(e, 'profile_image', 'output-profile-image', false);
        });
        jQuery('#input-eid-image').click(function (e) {
            file_uploader(e, 'eid_image', 'output-eid-image', false);
        });
        jQuery('#input-passport-image').click(function (e) {
            file_uploader(e, 'passport_image', 'output-passport-image', false);
        });
        jQuery('#input-visa-image').click(function (e) {
            file_uploader(e, 'visa_image', 'output-visa-image', false);
        });
        jQuery('#input-labor-image').click(function (e) {
            file_uploader(e, 'labor_image', 'output-labor-image', false);
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
                    jQuery('#' + output).append("<input type='hidden' name='" + input + "' value='" + attachment.id + "'><object data='" + attachment.url + "' height='250px'></object>");
                }).join();
            });
            custom_uploader.open();
        }

    </script>
    <?php
}

function show_employee_documents($employee, $field) {
    $html = '';
    if (!empty($employee->$field)) {
        $image = get_image_src($employee->$field);
        $html .= "<object data='$image' height='150'></object>";
        $html .= "<input type='hidden' name='$field' value='{$employee->$field}' /><br/>";
        $html .= "<a href='$image' target='_blank'>View Document</a>";
    }
    return$html;
}
