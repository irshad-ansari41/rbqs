<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_attendance_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $attendance_date = !empty($getdata['attendance_date']) ? $getdata['attendance_date'] : date('Y-m-d');

    if (!empty($postdata['attendance_date']) && $postdata['attendance_date'] != $attendance_date) {
        $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_hr_attendances WHERE attendance_date='{$postdata['attendance_date']}'");
    }

    if (!empty($postdata['update']) && empty($exist)) {
        foreach ($postdata['attendance'] as $id => $value) {

            $emp_id = $value['emp_id'];
            $full_day = $value['type'] == 'X' ? 1 : 0;
            $half_day = $value['type'] == 'HD' ? 1 : 0;
            $un_paid_leave = $value['type'] == 'UPL' ? 1 : 0;
            $paid_leave = $value['type'] == 'PL' ? 1 : 0;
            $sick_leave = $value['type'] == 'SL' ? 1 : 0;
            $late = $value['type'] == 'LT' ? 1 : 0;
            $early_out = $value['type'] == 'EP' ? 1 : 0;

            $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_hr_attendances WHERE attendance_date='{$postdata['attendance_date']}' AND emp_id='{$emp_id}'");
            if (empty($exist)) {
                $data = ['emp_id' => $emp_id, 'full_day' => $full_day, 'half_day' => $half_day, 'un_paid_leave' => $un_paid_leave, 'paid_leave' => $paid_leave, 'sick_leave' => $sick_leave, 'late' => $late, 'early_out' => $early_out, 'note' => $value['note'], 'attendance_date' => $postdata['attendance_date'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date,];
                $wpdb->insert("{$wpdb->prefix}ctm_hr_attendances", array_map('trim', $data), wpdb_data_format($data));
            } else {
                $data = ['full_day' => $full_day, 'half_day' => $half_day, 'un_paid_leave' => $un_paid_leave, 'paid_leave' => $paid_leave, 'sick_leave' => $sick_leave, 'late' => $late, 'early_out' => $early_out, 'note' => $value['note'], 'attendance_date' => $postdata['attendance_date'], 'updated_by' => $current_user->ID, 'updated_at' => $date,];
                $wpdb->update("{$wpdb->prefix}ctm_hr_attendances", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%d']);
            }
        }
        $msg = 1;
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
        <h1 class="wp-heading-inline">Edit Attendance</h1>
        <a href="?page=attendance"  class="page-title-action" >Back</a>
        <br/><br/>
        <?php if (!empty($error)) { ?>
            <br/>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Attendance has been already exist for that day.
            </div>
            <?php
        }
        if (!empty($msg)) {
            ?>
            <br/>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Attendance has been updated successfully
            </div>
        <?php } ?>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside">
                                <form id="filter-form1" method="get">
                                    <input type="hidden" name="page" value="<?= $page ?>" />
                                    <table class="form-table">
                                        <tr>
                                            <td>
                                                <label>Attendance Date:<span class="text-red">*</span></label>
                                                <input type="date" name="attendance_date" value="<?= $attendance_date ?>" required/>
                                                <?= !$attendance_date ? '<span class="text-red">Please select date.</span>' : '' ?>
                                                <button type="submit" class="button-primary">Get Attendance</button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                                <br/> 
                                <?php if (!empty($employees)) { ?>
                                    <form id = "add-new-item-form" method = "post">
                                        <table class = "form-table" style = "width:100%;border-collapse: collapse;" border = "1" cellpadding = "5" cellspacing = "5">
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
                                            foreach ($employees as $emp) {

                                                $value = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_attendances WHERE emp_id='{$emp->id}' AND attendance_date='{$attendance_date}' ORDER BY id ASC");
                                                if (!empty($value)) {
                                                    $full_day = $value->full_day ? 'checked' : '';
                                                    $half_day = $value->half_day ? 'checked' : '';
                                                    $un_paid_leave = $value->un_paid_leave ? 'checked' : '';
                                                    $paid_leave = $value->paid_leave ? 'checked' : '';
                                                    $sick_leave = $value->sick_leave ? 'checked' : '';
                                                    $late = $value->late ? 'checked' : '';
                                                    $early_out = $value->early_out ? 'checked' : '';
                                                    ?>
                                                    <tr>
                                                        <td><label class="font-weight-normal"><?= $i++ ?></label>
                                                            <input type="hidden" name="attendance[<?= $value->id ?>][emp_id]" value="<?= $emp->id ?>" /></td>
                                                        <td><label class="font-weight-normal"><?= $emp->name ?></label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $value->id ?>][type]" value="X" required <?= $full_day ?> />X</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $value->id ?>][type]" value="HD" required <?= $half_day ?> />HD</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $value->id ?>][type]" value="UPL" required <?= $un_paid_leave ?> />UPL</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $value->id ?>][type]" value="PL" required <?= $paid_leave ?> />PL</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $value->id ?>][type]" value="SL" required <?= $sick_leave ?> />SL</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $value->id ?>][type]" value="LT" required <?= $late ?> />LT</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $value->id ?>][type]" value="EO" required <?= $early_out ?> />EO</label></td>
                                                        <td><input type="text" name="attendance[<?= $value->id ?>][note]" placeholder="Note" value="<?= $value->note ?>" /></td>
                                                    </tr>

                                                <?php } else { ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td><label class="font-weight-normal"><?= $emp->name ?></label>
                                                            <input type="hidden" name="attendance[<?= $emp->id ?>][emp_id]" value="<?= $emp->id ?>" r/></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $emp->id ?>][type]" value="X"  required />X</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $emp->id ?>][type]" value="HD" required />HD</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $emp->id ?>][type]" value="UPL" required />UPL</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $emp->id ?>][type]" value="PL" required />PL</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $emp->id ?>][type]" value="SL" required />SL</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $emp->id ?>][type]" value="LT" required />LT</label></td>
                                                        <td><label class="font-weight-normal">
                                                                <input type="radio" name="attendance[<?= $emp->id ?>][type]" value="EO" required />EO</label></td>
                                                        <td><input type="text" name="attendance[<?= $emp->id ?>][note]" placeholder="Note" /></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="2"><label>Change Attendance Date:<span class="text-red">*</span></label></td>
                                                <td colspan="8"><input type="date" name="attendance_date" value="<?= $attendance_date ?>" required/></td>
                                            </tr>
                                            <tr>
                                                <td colspan="10" style="text-align:left"><br/><br/>
                                                    <button type="submit"  name="update" value="update" class="button-primary">Update</button>
                                                    &nbsp;&nbsp;<a href="?page=attendance"  class="button-secondary" >Back</a></td>
                                            </tr>
                                        </table>
                                        <br/><br/><br/>
                                    </form>
                                    <?php
                                } else {
                                    echo "<span class='text-red'>No record found. Please change date.</span>";
                                }
                                ?>
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
