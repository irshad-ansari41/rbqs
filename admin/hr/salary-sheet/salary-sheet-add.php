<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_salary_sheet_add_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $month = !empty($getdata['month']) ? $getdata['month'] : '';

    if (!empty($month)) {
        $exist = $wpdb->get_var("SELECT * FROM {$wpdb->prefix}ctm_hr_salary_sheets WHERE month='{$month}'");
        if (!empty($exist)) {
            wp_redirect("admin.php?page=salary-sheet-add&error=exist");
            exit();
        }
    }

    if (!empty($postdata['create']) && empty($exist)) {

        $data = ['month' => $postdata['month'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date,];
        $wpdb->insert("{$wpdb->prefix}ctm_hr_salary_sheets", array_map('trim', $data), wpdb_data_format($data));
        $salary_sheet_id = $wpdb->insert_id;

        foreach ($postdata['ss'] as $emp_id => $value) {

            $data = ['salary_sheet_id' => $salary_sheet_id, 'emp_id' => $emp_id, 'ot_pay' => $value['ot_pay'], 'ls_pay' => $value['ls_pay'], 'ar_pay' => $value['ar_pay'], 'total' => $value['total'], 'loan_ac' => $value['loan_ac'], 'leave_other' => $value['leave_other'], 'salary_advance' => $value['salary_advance'], 'total_deductions' => $value['total_deductions'], 'net_amount' => $value['net_amount'], 'mode' => $value['mode'], 'wps_charge' => $value['wps_charge']];

            $wpdb->insert("{$wpdb->prefix}ctm_hr_salary_sheet_meta", array_map('trim', $data), wpdb_data_format($data));
        }

        wp_redirect("admin.php?page=salary-sheet&msg=created");
        exit();
    }

    $employees = get_active_employees();
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; min-width: auto!important; }
        .wp-list-table{table-layout: auto!important;}
        .form-table tr td{font-size: 12px; text-align: center;}

        .chosen-container{width:100%!important;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Add Salary Sheet</h1>
        <a href="<?= "admin.php?page=salary-sheet" ?>" class="page-title-action btn-primary">Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <?php if (!empty($getdata['error'])) { ?>
                                <br/>
                                <div class="alert alert-danger alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    Salary Sheet already exist for this month.
                                </div>
                            <?php } ?>
                            <div class="inside">
                                <form id="filter-form1" method="get">
                                    <input type="hidden" name="page" value="<?= $page ?>" />
                                    <table class="form-table" style="max-width:400px">
                                        <tr>
                                            <td colspan="3" ><label>Salary Sheet Month:<span class="text-red">*</span></label></td>
                                            <td colspan="3" style="text-align:left">                                                
                                                <select name="month" onchange="this.form.submit()">
                                                    <option value="">Select Month</option>
                                                    <?php
                                                    for ($i = 0; $i <= 24; $i++) {
                                                        $daymonthyear = date("Y-m-d", strtotime(date('Y-m-01') . " -$i months"));
                                                        $monthname = date("F - Y", strtotime(date('Y-m-01') . " -$i months"));
                                                        //$months[]=[$startdaymonthyear,$enddaymonthyear,$month];
                                                        $select = $month == "{$daymonthyear}" ? 'selected' : '';
                                                        echo"<option value='{$daymonthyear}' $select>$monthname</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td colspan="9"></td>
                                        </tr>
                                    </table>
                                </form>
                                <form id="add-new-item-form" method="post">
                                    <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                                    <table id='tbl-items' class="form-table" style="width:100%;border-collapse: collapse;" border="1" cellpadding="3" cellspacing="3">

                                        <tr>
                                            <td><b>Sr.</b></td>
                                            <td><b>Name</b></td>
                                            <td><b>Basic Salary</b></td>
                                            <td><b>HRA</b></td>
                                            <td><b>Other Allowance</b></td>
                                            <td><b>OT Pay </td>
                                            <td><b>Leave<br/>Salary</b></td>
                                            <td><b>Arrears</b></td>
                                            <td><b>Total</b></td>
                                            <td><b>Employee<br/>Loan A/c</b></td>
                                            <td><b>Leave /<br/>Others</b></td>
                                            <td><b>Salary<br/>Advance</b></td>
                                            <td><b>Total<br/>Deductions</b></td>
                                            <td><b>Net Amount</b></td>
                                            <td><b>Paid Through</b></td>
                                        </tr>

                                        <?php
                                        $i = 1;
                                        foreach ($employees as $value) {
                                            ?>
                                            <tr id='tbl-row-<?= $i ?>'>
                                                <td><?= $i ?></td>
                                                <td><label class="font-weight-normal"><?= $value->name ?></label></td>
                                                <td><input type='number' readonly id="input-basic-<?= $i ?>" value='<?= $value->basic_salary ?>' /></td>
                                                <td><input type='number' readonly id="input-hra-<?= $i ?>" value='<?= $value->hra_salary ?>' /></td>
                                                <td><input type='number' readonly id="input-allowance-<?= $i ?>" value='<?= $value->allowance_salary ?>' /></td>
                                                <td>
                                                    <?php $ot = get_overtime_of_employee($value->id, $month); ?>
                                                    <input type="number" name="ss[<?= $value->id ?>][ot_pay]" value="<?= $ot ? $ot : '' ?>" class="refresh" id="input-ot-<?= $i ?>" placeholder="OT Pay"  /></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][ls_pay]" class="refresh" id="input-ls-<?= $i ?>" placeholder="Leave Salary"  /></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][ar_pay]" class="refresh" id="input-ar-<?= $i ?>" placeholder="Arrears"  /></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][total]" id="input-total-<?= $i ?>" placeholder="Total" readonly /></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][loan_ac]" min='0' class="refresh"  id="input-loan-<?= $i ?>" placeholder="Employee Loan A/c"  ></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][leave_other]" min='0'  class="refresh" id="input-leave-<?= $i ?>"placeholder="Leave / Others" /></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][salary_advance]" min='0' class="refresh" id="input-advance-<?= $i ?>" placeholder="Salary Advance"  /></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][total_deductions]" readonly id="input-deduct-<?= $i ?>" placeholder="Total Deductions" required /></td>
                                                <td><input type="number" name="ss[<?= $value->id ?>][net_amount]" min='0' id="input-net-<?= $i ?>" placeholder="Net Amount" required /></td>
                                                <td style="text-align:left">
                                                    <label class="font-weight-normal">
                                                        <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" value="Bank Transfer" data-id='wps-charge-<?= $i ?>'  checked required />Bank Transfer
                                                    </label><br/>
                                                    <label class="font-weight-normal">
                                                        <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" value="Al Ansari Exchange" data-id='wps-charge-<?= $i ?>'  required />Al Ansari Exchange
                                                    </label><br/>
                                                    <label class="font-weight-normal">
                                                        <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" value="Cash" data-id='wps-charge-<?= $i ?>'  required />Cash
                                                    </label><br/>
                                                    <label class="font-weight-normal">
                                                        <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" value="Check" data-id='wps-charge-<?= $i ?>'  required />Check
                                                    </label>
                                                    <input type="number" name="ss[<?= $value->id ?>][wps_charge]" class="hidden" id='wps-charge-<?= $i ?>' min='0' step="0.01" value="15" readonly placeholder="WPS Charge" />
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>

                                        <tr>
                                            <td colspan="15" style="text-align:left"><br/><br/>
                                                <input type="hidden"  name="month" value="<?= $month ?>" />
                                                <button type="submit"  name="create" value="create" class="button-primary">Add</button>
                                                &nbsp;&nbsp;<a href="?page=salary-sheet"  class="button-secondary" >Cancel</a>
                                            </td>
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

            jQuery('.mode').click(function () {
                var mode = jQuery(this).val();
                var id = jQuery(this).data('id');
                if (mode === 'Al Ansari Exchange') {
                    jQuery(`#${id}`).prop('required', true).removeAttr('readonly').removeClass('hidden');
                } else {
                    jQuery(`#${id}`).removeAttr('required').prop('readonly', true).addClass('hidden');
                    ;
                }
            });

            refresh_calculation();
            setTimeout(function () {
                calculate();
            }, 1000);

        });

        function refresh_calculation() {
            jQuery('.refresh').on('input keyup keypress blur', function () {
                calculate();
            });
        }

        function calculate() {
            jQuery('table#tbl-items > tbody  > tr').each(function (index, tr) {
                if (tr.id !== '') {
                    var i = tr.id.replace('tbl-row-', '');

                    var basic_val = jQuery(`#input-basic-${i}`).val();
                    var hra_val = jQuery(`#input-hra-${i}`).val();
                    var allowance_val = jQuery(`#input-allowance-${i}`).val();
                    var ot_val = jQuery(`#input-ot-${i}`).val();
                    var ls_val = jQuery(`#input-ls-${i}`).val();
                    var ar_var = jQuery(`#input-ar-${i}`).val();

                    var basic = parseFloat(!isNaN(basic_val) ? basic_val : 0);
                    var hra = parseFloat(!isNaN(hra_val) ? hra_val : 0);
                    var allowance = parseFloat(!isNaN(allowance_val) ? allowance_val : 0);
                    var ot = parseFloat(!isNaN(ot_val) && ot_val !== '' ? ot_val : 0);
                    var ls = parseFloat(!isNaN(ls_val) && ls_val !== '' ? ls_val : 0);
                    var ar = parseFloat(!isNaN(ar_var) && ar_var !== '' ? ar_var : 0);

                    var total = (basic + hra + allowance + ot + ls + ar).toFixed(2);
                    jQuery(`#input-total-${i}`).val(total);

                    var loan_val = jQuery(`#input-loan-${i}`).val();
                    var leave_val = jQuery(`#input-leave-${i}`).val();
                    var advance_val = jQuery(`#input-advance-${i}`).val();

                    var loan = parseFloat(!isNaN(loan_val) && loan_val !== '' ? loan_val : 0);
                    var leave = parseFloat(!isNaN(leave_val) && leave_val !== '' ? leave_val : 0);
                    var advance = parseFloat(!isNaN(advance_val) && advance_val !== '' ? advance_val : 0);

                    var deduct = (loan + leave + advance).toFixed(2);
                    jQuery(`#input-deduct-${i}`).val(deduct);

                    jQuery(`#input-net-${i}`).val(total - deduct);
                }
            });
        }


    </script>
    <?php
}
