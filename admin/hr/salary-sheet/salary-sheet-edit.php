<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_salary_sheet_edit_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');

    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $month = !empty($getdata['month']) ? $getdata['month'] : date('Y-m-01');

    if (!empty($postdata['update'])) {

        $data = ['bank_transfer_pv_no' => $postdata['bank_transfer_pv_no'], 'al_ansari_ex_pv_no' => $postdata['al_ansari_ex_pv_no'], 'cash_pv_no' => $postdata['cash_pv_no'], 'check_pv_no' => $postdata['check_pv_no'], 'updated_by' => $current_user->ID, 'updated_at' => $date,];
        $wpdb->update("{$wpdb->prefix}ctm_hr_salary_sheets", array_map('trim', $data), ['id' => $postdata['id']], wpdb_data_format($data), ['%s']);

        foreach ($postdata['ss'] as $id => $value) {

            $data = ['ot_pay' => $value['ot_pay'], 'ls_pay' => $value['ls_pay'], 'ar_pay' => $value['ar_pay'], 'total' => $value['total'], 'loan_ac' => $value['loan_ac'], 'leave_other' => $value['leave_other'], 'salary_advance' => $value['salary_advance'], 'total_deductions' => $value['total_deductions'], 'net_amount' => $value['net_amount'], 'mode' => $value['mode'], 'wps_charge' => $value['wps_charge'],];

            $wpdb->update("{$wpdb->prefix}ctm_hr_salary_sheet_meta", array_map('trim', $data), ['id' => $id], wpdb_data_format($data), ['%s']);
        }

        wp_redirect("admin.php?page=salary-sheet-edit&msg=updated&month=$month");
        exit();
    }

    $results = [];
    $salary_sheet = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_salary_sheets WHERE month='{$month}' ORDER BY id ASC");
    if (!empty($salary_sheet)) {
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_hr_salary_sheet_meta WHERE salary_sheet_id='{$salary_sheet->id}' ORDER BY id ASC");
    }
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; min-width: auto!important; }
        .wp-list-table{table-layout: auto!important;}
        .form-table tr td{font-size: 12px; text-align: center;}

        .chosen-container{width:100%!important;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Edit Salary Sheet</h1>
        <a href="<?= "admin.php?page=salary-sheet" ?>" class="page-title-action btn-primary">Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <?php if (!empty($getdata['msg'])) { ?>
                                <br/>
                                <div class="alert alert-success alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    Salary Sheet has been updated successfully
                                </div>
                            <?php } ?>
                            <div class="inside">
                                <form id="filter-form1" method="get">
                                    <input type="hidden" name="page" value="<?= $page ?>" />
                                    <table class="form-table" style="max-width:200px">
                                        <tr>
                                            <td>
                                                <select name='month' id='month' onchange="this.form.submit()">
                                                    <option value="">Select Month</option>
                                                    <?php
                                                    for ($i = 1; $i <= 12; $i++) {
                                                        $year = date('Y');
                                                        $selected = $month == rb_date("$year-$i-01", 'Y-m-d') ? 'selected' : '';
                                                        ?>
                                                        <option value="<?= rb_date("$year-$i-01", 'Y-m-d') ?>" <?= $selected ?>><?= rb_date("$year-$i-01", 'M, Y') ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?= !$month ? '<span class="text-red">Please select month.</span>' : '' ?>
                                            </td>

                                        </tr>
                                    </table>
                                </form>
                                <br/> 
                                <?php
                                if (empty($salary_sheet)) {
                                    echo "<span class='text-red'>No Record Found.</span>";
                                } else {
                                    ?>
                                    <form id="add-new-item-form" method="post">
                                        <table id='tbl-items' class="form-table" style="width:100%;border-collapse: collapse;" border="1" cellpadding="3" cellspacing="3">

                                            <tr>
                                                <td colspan="3" ><label>Salary Sheet Month:<span class="text-red">*</span></label></td>
                                                <td colspan="2" style="text-align:left">
                                                    <select name='month' id='month' disabled>
                                                        <option value="">Select Month</option>
                                                        <?php
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            $year = date('Y');
                                                            $selected = $month == rb_date("$year-$i-01", 'Y-m-d') ? 'selected' : '';
                                                            ?>
                                                            <option value="<?= rb_date("$year-$i-01", 'Y-m-d') ?>" <?= $selected ?>><?= rb_date("$year-$i-01", 'M, Y') ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td colspan="10"></td>
                                            </tr>

                                            <tr>
                                                <td><b>Sr.</b></td>
                                                <td><b>Name</b></td>
                                                <td><b>Basic Salary</b></td>
                                                <td><b>HRA</b></td>
                                                <td><b>Other<br/>Allowance</b></td>
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
                                            foreach ($results as $value) {
                                                $employee = get_employee($value->emp_id);
                                                ?>
                                                <tr id='tbl-row-<?= $i ?>'>
                                                    <td><?= $i ?></td>
                                                    <td><label class="font-weight-normal"><?= $employee->name ?></label></td>
                                                    <td><input type='number' readonly id="input-basic-<?= $i ?>" value='<?= $employee->basic_salary ?>' /></td>
                                                    <td><input type='number' readonly id="input-hra-<?= $i ?>" value='<?= $employee->hra_salary ?>' /></td>
                                                    <td><input type='number' readonly id="input-allowance-<?= $i ?>" value='<?= $employee->allowance_salary ?>' /></td>
                                                    <td>
                                                        <?php $ot = get_overtime_of_employee($value->emp_id, $month); ?>
                                                        <input type="number" name="ss[<?= $value->id ?>][ot_pay]" value="<?= $ot ? $ot : $value->ot_pay ?>" class="refresh" id="input-ot-<?= $i ?>" placeholder="OT Pay"  /></td>
                                                    <td><input type="number" name="ss[<?= $value->id ?>][ls_pay]" value="<?= $value->ls_pay ?>" class="refresh" id="input-ls-<?= $i ?>" placeholder="Leave Salary"  /></td>
                                                    <td><input type="number" name="ss[<?= $value->id ?>][ar_pay]" value="<?= $value->ar_pay ?>" class="refresh" id="input-ar-<?= $i ?>" placeholder="Arrears"  /></td>

                                                    <td><input type="number" name="ss[<?= $value->id ?>][total]" id="input-total-<?= $i ?>" placeholder="Total" readonly /></td>
                                                    <td><input type="number" name="ss[<?= $value->id ?>][loan_ac]" min='0' class="refresh" placeholder="Employee Loan A/c" value="<?= $value->loan_ac ?>" id="input-loan-<?= $i ?>" /></td>
                                                    <td><input type="number" name="ss[<?= $value->id ?>][leave_other]" min='0'  class="refresh" id="input-leave-<?= $i ?>"placeholder="Leave / Others" value="<?= $value->leave_other ?>" /></td>
                                                    <td><input type="number" name="ss[<?= $value->id ?>][salary_advance]" min='0' class="refresh" id="input-advance-<?= $i ?>" placeholder="Salary Advance" value="<?= $value->salary_advance ?>"  /></td>
                                                    <td><input type="number" name="ss[<?= $value->id ?>][total_deductions]" readonly id="input-deduct-<?= $i ?>" placeholder="Total Deductions" value="<?= $value->total_deductions ?>" required /></td>
                                                    <td><input type="number" name="ss[<?= $value->id ?>][net_amount]" min='0' id="input-net-<?= $i ?>" placeholder="Net Amount" required /></td>
                                                    <td style="text-align:left">
                                                        <?php
                                                        $pd1 = $value->mode == 'Bank Transfer' ? 'checked' : '';
                                                        $pd2 = $value->mode == 'Al Ansari Exchange' ? 'checked' : '';
                                                        $pd3 = $value->mode == 'Cash' ? 'checked' : '';
                                                        $pd4 = $value->mode == 'Check' ? 'checked' : '';
                                                        ?>
                                                        <label class="font-weight-normal">
                                                            <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" data-id='wps-charge-<?= $i ?>' value="Bank Transfer" <?= $pd1 ?> required />Bank Transfer
                                                        </label><br/>
                                                        <label class="font-weight-normal">
                                                            <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" data-id='wps-charge-<?= $i ?>' value="Al Ansari Exchange"  <?= $pd2 ?>  required />Al Ansari Exchange
                                                        </label><br/>
                                                        <label class="font-weight-normal">
                                                            <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" data-id='wps-charge-<?= $i ?>' value="Cash"  <?= $pd3 ?> required />Cash
                                                        </label><br/>
                                                        <label class="font-weight-normal">
                                                            <input type="radio" name="ss[<?= $value->id ?>][mode]" class="mode" value="Check" data-id='wps-charge-<?= $i ?>' <?= $pd4 ?>  required />Check
                                                        </label>
                                                        <input type="number" name="ss[<?= $value->id ?>][wps_charge]" class=" <?= $pd2 ? '' : 'hidden' ?>" id='wps-charge-<?= $i ?>' min='0' step="0.01" <?= $pd2 ? '' : 'readonly' ?> value="<?= $value->wps_charge ?>" placeholder="WPS Charge" />
                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>

                                            <tr><td colspan="13" style="text-align:left"><br/></td></tr>

                                            <tr>
                                                <td colspan="3" style="text-align:left">Bank Transfer PV No.</td>
                                                <td colspan="2" style="text-align:left"><input type='number' name='bank_transfer_pv_no' 
                                                                                               value="<?= $salary_sheet->bank_transfer_pv_no ?>" placeholder="Payment Voucher No" /></td>
                                                <td colspan="10" style="text-align:left"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="text-align:left">Al Ansari Exchange PV No.</td>
                                                <td colspan="2" style="text-align:left">
                                                    <input type='number' name='al_ansari_ex_pv_no'  
                                                           value="<?= $salary_sheet->al_ansari_ex_pv_no ?>" placeholder="Payment Voucher No" /></td>
                                                <td colspan="10" style="text-align:left"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="text-align:left">Cash PV No.</td>
                                                <td colspan="2" style="text-align:left"><input type='number' name='cash_pv_no' 
                                                                                               value="<?= $salary_sheet->cash_pv_no ?>" placeholder="Payment Voucher No" /></td>
                                                <td colspan="10" style="text-align:left"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="text-align:left">CHECK PV No.</td>
                                                <td colspan="2" style="text-align:left"><input type='number' name='check_pv_no' 
                                                                                               value="<?= $salary_sheet->check_pv_no ?>" placeholder="Payment Voucher No" /></td>
                                                <td colspan="10" style="text-align:left"></td>
                                            </tr>

                                            <tr>
                                                <td colspan="15" style="text-align:left"><br/><br/>
                                                    <input type="hidden" name='id' value="<?= $salary_sheet->id ?>" />
                                                    <button type="submit"  name="update" value="update" class="button-primary">Update</button>
                                                    &nbsp;&nbsp;<a href="?page=salary-sheet"  class="button-secondary" >Cancel</a>
                                                </td>
                                            </tr>
                                        </table>
                                        <br/><br/><br/>
                                    </form>
                                <?php } ?>
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
                    ;
                } else {
                    jQuery(`#${id}`).removeAttr('required').prop('readonly', true).addClass('hidden');
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
