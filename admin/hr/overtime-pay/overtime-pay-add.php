<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_overtime_pay_add_page() {
    global $wpdb, $current_user;
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    if (!empty($postdata['add'])) {
        $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_hr_overtime_days WHERE emp_id='{$postdata['emp_id']}' AND ot_date='{$postdata['ot_date']}'");
        if (empty($exist)) {
            $data = ['emp_id' => $postdata['emp_id'], 'department' => $postdata['department'], 'ot_date' => $postdata['ot_date'], 'ot_time_from' => $postdata['ot_time_from'], 'ot_time_to' => $postdata['ot_time_to'], 'ot_rate' => $postdata['ot_rate'], 'purpose' => $postdata['purpose'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
            $wpdb->insert("{$wpdb->prefix}ctm_hr_overtime_days", array_map('trim', $data), wpdb_data_format($data));

            wp_redirect("admin.php?page=overtime-pay&msg=added");
            exit();
        } else if (!empty($exist)) {
            $error = 1;
        }
    }
    $employees = get_active_employees();
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
        <h1 class="wp-heading-inline">Add Overtime</h1>
        <br/><br/>
        <?php if (!empty($error)) { ?>
            <br/>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Overtime Pay has been already exist for this employee.
            </div>
        <?php } ?>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>                            
                            <div class="inside">
                                <form id="add-new-item-form" method="post">
                                    <table class="form-table" style="width:600px">
                                        <tr>
                                            <td><label>Employee:<span class="text-red">*</span></label>
                                                <select name="emp_id" required>
                                                    <option>Select Employee</option>
                                                    <?php foreach ($employees as $value) { ?>
                                                        <option value="<?= $value->id ?>"><?= $value->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>

                                            <td><label>Department:<span class="text-red">*</span></label>
                                                <select name="department" required>
                                                    <option value="">Select Department</option> 
                                                    <option value="Accounts">Accounts</option>
                                                    <option value="Commercial">Commercial</option>
                                                    <option value="HR">HR</option>
                                                    <option value="Logistics">Logistics</option>
                                                    <option value="Office Boy">Office Boy</option>
                                                    <option value="Sales">Sales</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label>OT Rate:<span class="text-red">*</span></label>
                                                <input type="number" list="ot_pay" name="ot_rate" step="0.01" required />
                                                <datalist id="ot_pay">
                                                    <option value="1.25">1.25</option>
                                                    <option value="1.50">1.50</option>
                                                </datalist>
                                            </td>
                                            <td><label>Overtime Date:<span class="text-red">*</span></label><br/>
                                                <input type=date name="ot_date" required>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td><label>Time From:<span class="text-red">*</span></label><br/>
                                                <input type=time name="ot_time_from" required>
                                            </td>
                                            <td><label>Time To:<span class="text-red">*</span></label><br/>
                                                <input type=time name="ot_time_to" required>
                                            </td>
                                        </tr>

                                        <tr>

                                        </tr>
                                        <tr>
                                            <td colspan="2"><label>Purpose:<span class="text-red">*</span></label>
                                                <input type=text name="purpose" placeholder="Purpose" required >
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2">
                                                <br/><button type="submit" name="add" value="Add" class="button-primary" >Add Overtime</button>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="?page=overtime-pay"  class="button-secondary" >Back</a></td>
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



        jQuery('#check_image').click(function (e) {
            file_uploader(e, 'image', 'check-image', false);
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
