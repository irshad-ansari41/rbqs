<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_attendance_add_page() {
    global $wpdb, $current_user;
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    if (!empty($postdata['create'])) {
        foreach ($postdata['attendance'] as $emp_id => $value) {
            $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_hr_attendances WHERE attendance_date='{$postdata['attendance_date']}' AND emp_id='{$emp_id}'");
            if (empty($exist)) {
                $full_day = $value['type'] == 'X' ? 1 : 0;
                $half_day = $value['type'] == 'HD' ? 1 : 0;
                $un_paid_leave = $value['type'] == 'UPL' ? 1 : 0;
                $paid_leave = $value['type'] == 'PL' ? 1 : 0;
                $sick_leave = $value['type'] == 'SL' ? 1 : 0;
                $late = $value['type'] == 'LT' ? 1 : 0;
                $early_out = $value['type'] == 'EP' ? 1 : 0;
                $data = ['emp_id' => $emp_id, 'full_day' => $full_day, 'half_day' => $half_day, 'un_paid_leave' => $un_paid_leave, 'paid_leave' => $paid_leave, 'sick_leave' => $sick_leave, 'late' => $late, 'early_out' => $early_out, 'note' => $value['note'], 'attendance_date' => $postdata['attendance_date'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date,];
                $wpdb->insert("{$wpdb->prefix}ctm_hr_attendances", array_map('trim', $data), wpdb_data_format($data));
            }
        }

        wp_redirect("admin.php?page=attendance&msg=created");
        exit();
    } else if (!empty($exist)) {
        $error = 1;
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
        <h1 class="wp-heading-inline">Add Attendance</h1>
        <a href="?page=attendance"  class="page-title-action" >Back</a>
        <br/><br/>
        <?php if (!empty($error)) { ?>
            <br/>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Attendance has been already exist for that day.
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
                                    <table class="form-table" style="width:100%;border-collapse: collapse;" border="1" cellpadding="5" cellspacing="5">

                                        <tr>
                                            <td colspan="10" style="text-align:left">
                                                <br/>
                                                <label>Attendance Date:<span class="text-red">*</span></label>
                                                <input type="date" name="attendance_date" required/> <br/> <br/>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>Sr.</b></td>
                                            <td><b>Name</b></td>
                                            <td><b>Full Day (X)</b></td>
                                            <td><b>Half Day (HD)</b></td>
                                            <td><b>Un Paid Leave</b></td>
                                            <td><b>Paid Leave (PL)</b></td>
                                            <td><b>Sick Leave (SL)</b></td>
                                            <td><b>Late (LT)</b></td>
                                            <td><b>Early Out (EO)</b></td>
                                            <td><b>Note</b></td>
                                        </tr>

                                        <?php
                                        $i = 1;
                                        foreach ($employees as $value) {
                                            ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><label class="font-weight-normal"><?= $value->name ?></label></td>
                                                <td><label class="font-weight-normal"><input type="radio" name="attendance[<?= $value->id ?>][type]" value="X" checked required />X</label></td>
                                                <td><label class="font-weight-normal"><input type="radio" name="attendance[<?= $value->id ?>][type]" value="HD" required />HD</label></td>
                                                <td><label class="font-weight-normal"><input type="radio" name="attendance[<?= $value->id ?>][type]" value="UPL" required />UPL</label></td>
                                                <td><label class="font-weight-normal"><input type="radio" name="attendance[<?= $value->id ?>][type]" value="PL" required />PL</label></td>
                                                <td><label class="font-weight-normal"><input type="radio" name="attendance[<?= $value->id ?>][type]" value="SL" required />SL</label></td>
                                                <td><label class="font-weight-normal"><input type="radio" name="attendance[<?= $value->id ?>][type]" value="LT" required />LT</label></td>
                                                <td><label class="font-weight-normal"><input type="radio" name="attendance[<?= $value->id ?>][type]" value="EO" required />EO</label></td>
                                                <td><input type="text" name="attendance[<?= $value->id ?>][note]" placeholder="Note" /></td>
                                            </tr>
                                        <?php } ?>

                                        <tr>
                                            <td colspan="10" style="text-align:left"><br/><br/>
                                                <button type="submit"  name="create" value="create" class="button-primary">Add</button>
                                                &nbsp;&nbsp;<a href="?page=attendance"  class="button-secondary" >Back</a></td>
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

            jQuery('#add-new-item').click(() => {
                jQuery('#add-new-item-form').toggleClass('hide');
                jQuery('#page-inner-content').toggleClass('hide');
            });

            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

        });



    </script>
    <?php
}
