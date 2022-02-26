<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_salary_sheet_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $month = !empty($getdata['month']) ? $getdata['month'] : date('m');
    $year = !empty($getdata['year']) ? $getdata['year'] : date('Y');

    $salary_sheet = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_salary_sheets WHERE month like '%{$year}-{$month}%' ORDER BY id ASC");
    if (!empty($salary_sheet)) {
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_hr_salary_sheet_meta WHERE salary_sheet_id='{$salary_sheet->id}' ORDER BY id ASC");
    }
    ?>
    <style>
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .confirm-order-items tr td{text-align: left;}
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                        <h1 class="wp-heading-inline">Salary Sheet</h1>
                        <a id="add-new-client" href="<?= "admin.php?page={$page}-add" ?>" class="page-title-action btn-primary" target="_blank">Add Salary Sheet</a>
                        <a id="add-new-client" href="<?= "admin.php?page={$page}-edit" ?>" class="page-title-action btn-primary" target="_blank">Edit Salary Sheet</a>
                        <br/><br/>
                        <form id="add-new-leave-salary-form" method="get">
                            <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                            <table class="form-table" style="max-width:400px">
                                <tr>
                                    <td>
                                        <select name="month" onchange="this.form.submit()">
                                            <option value="">Select Month</option>
                                            <?php
                                            for ($i = 1; $i <= 12; $i++) {
                                                $select = $month == $i ? 'selected' : '';
                                                echo"<option value='".($i<10?'0':'')."$i' $select>" . rb_date("$year-$i-01", 'F') . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" list="year" name="year" placeholder="Year" class="year" pattern="\d{4}" value="<?= $year ?>" required/>
                                        <datalist id="year">
                                            <?php
                                            $earliest_year = 2010;
                                            $latest_year = date('Y');
                                            foreach (range($latest_year, $earliest_year) as $i) {
                                                $selected = $i === $year ? 'selected' : '';
                                                echo "<option value='$i' $selected>$i</option>";
                                            }
                                            ?>
                                        </datalist>
                                    </td>
                                    <td><button type="submit"  class="button-primary" value="Filter" >Filter</button></td>
                                    <td><a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
                                </tr>                                           
                            </table>
                            <br/>
                        </form>



                        <?php
                        $html = $pdf_file = '';

                        if (empty($results)) {
                            echo "<span class='text-red'>No Record Found.</span>";
                        } else {
                            ?>
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:100%;margin:auto'>
                                    <?php
                                    $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{text-align:center;font-size:10px;}
                                        table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                        .bb{border-bottom:1px solid #000;}
                                        .bt{border-top:1px solid #000;}
                                    </style>
                                    ";

                                    $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                            <td style='text-align:left;vertical-align: middle;'>
                                            <h4><span style='font-size:26px;font-weight:bold'>SALARY SHEET</span></h4>
                                            <span style='font-size:14px;'>For the period from <span class='text-red'>" . rb_date($salary_sheet->month, 'd-M-Y') . "</span>,"
                                            . " to <span class='text-red'>" . rb_date($salary_sheet->month, 't-M-Y') . "</span></span>
                                            </td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";



                                    $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                        
                                   
                                    
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th>Particulars</th>
                                        <th>Basic<br/>Salary</th>
                                        <th>H.R.A</th>
                                        <th>Other<br/>Allowance</th>
                                        <th>OT Pay /<br/>Leave Salary<br/>/ Arrears</th>
                                        <th>Total</th>
                                        <th>Employee<br/>Loan A/c</th>
                                        <th>Leave /<br/>Others</th>
                                        <th>Salary<br/>Advance</th>
                                        <th>Total<br/>Deductions</th>
                                        <th>Net<br/>Amount</th>
                                    </tr>";

                                    $table .= "<tbody>";
                                    $mode = [];
                                    $tr_bank_trasfer = $tr_aae = $tr_cash = $tr_check = '';
                                    $basic_salary = $hra_salary = $allowance_salary = $ot_sl_ar_pay = $total = $leave_other = 0;
                                    $salary_advance = $total_deductions = $net_amount = $bt_amount = $cash_amount = $aae_amount = $check_amount = $wps_charge = 0;
                                    foreach ($results as $value) {
                                        $employee = get_employee($value->emp_id);
                                        $tr = "<tr valign=middle class='text-center'>
                                                    <td>$employee->name</td>
                                                    <td>" . rb_float($employee->basic_salary) . "</td>
                                                    <td>" . rb_float($employee->hra_salary) . "</td>
                                                    <td>" . rb_float($employee->allowance_salary) . "</td>
                                                    <td>" . rb_float($value->ot_pay + $value->ls_pay + $value->ar_pay) . "</td>
                                                    <td>" . rb_float($value->total) . "</td>
                                                    <td>" . $value->loan_ac . "</td>
                                                    <td>" . rb_float($value->leave_other) . "</td>
                                                    <td>" . rb_float($value->salary_advance) . "</td>
                                                    <td>" . rb_float($value->total_deductions) . "</td>
                                                    <td>" . rb_float($value->net_amount) . "</td>
                                                </tr>";


                                        if ($value->mode == 'Bank Transfer') {
                                            $tr_bank_trasfer .= $tr;
                                            $bt_amount += $value->net_amount;
                                            $mode[$value->mode]['amount'] = $bt_amount;
                                            $mode[$value->mode]['wps'] = 0;
                                            $mode[$value->mode]['pv_no'] = $salary_sheet->bank_transfer_pv_no;
                                        } else if ($value->mode == 'Al Ansari Exchange') {
                                            $tr_aae .= $tr;
                                            $aae_amount += $value->net_amount;
                                            $wps_charge += $value->wps_charge;
                                            $mode[$value->mode]['amount'] = $aae_amount;
                                            $mode[$value->mode]['wps'] = $wps_charge;
                                            $mode[$value->mode]['pv_no'] = $salary_sheet->al_ansari_ex_pv_no;
                                        } else if ($value->mode == 'Check') {
                                            $tr_check .= $tr;
                                            $check_amount += $value->net_amount;
                                            $mode[$value->mode]['amount'] = $check_amount;
                                            $mode[$value->mode]['wps'] = 0;
                                            $mode[$value->mode]['pv_no'] = $salary_sheet->check_pv_no;
                                        } else {
                                            $tr_cash .= $tr;
                                            $cash_amount += $value->net_amount;
                                            $mode[$value->mode]['amount'] = $cash_amount;
                                            $mode[$value->mode]['wps'] = 0;
                                            $mode[$value->mode]['pv_no'] = $salary_sheet->cash_pv_no;
                                        }
                                        $basic_salary += $employee->basic_salary;
                                        $hra_salary += $employee->hra_salary;
                                        $allowance_salary += $employee->allowance_salary;
                                        $ot_sl_ar_pay += $value->ot_pay + $value->ls_pay + $value->ar_pay;
                                        $total += $value->total;
                                        $leave_other += $value->leave_other;
                                        $salary_advance += $value->salary_advance;
                                        $total_deductions += $value->total_deductions;
                                        $net_amount += $value->net_amount;
                                    }

                                    $table .= $tr_bank_trasfer ? $tr_bank_trasfer . "<tr><td colspan=10><b>PAID THROUGH BANK TRANSFER</b></td><td><b>" . number_format($bt_amount, 2) . "</b></td></tr>" : '';
                                    $table .= "<tr><td colspan=11><br/></td></tr>";

                                    $table .= $tr_aae ? $tr_aae . "<tr><td colspan=10><b>PAID THROUGH AL ANSARI EXCHANGE</b></td><td><b>" . number_format($aae_amount, 2) . "</b></td></tr>" : '';

                                    $table .= "<tr><td colspan=11><br/></td></tr>";

                                    $table .= $tr_cash ? $tr_cash . "<tr><td colspan=10><b>PAID BY CASH</b></td><td><b>" . number_format($cash_amount, 2) . "</b></td></tr>" : '';

                                    $table .= "<tr><td colspan=11><br/></td></tr>";

                                    $table .= $tr_check ? $tr_check . "<tr><td colspan=10><b>PAID BY CHECK</b></td><td><b>" . number_format($check_amount, 2) . "</b></td></tr>" : '';

                                    $table .= "<tr><td colspan=11><br/></td></tr>";

                                    $table .= "<tr valign=middle class='text-center'>
                                                    <td><b>Grand Total</b></td>
                                                    <td><b>" . number_format($basic_salary, 2) . "</b></td>
                                                    <td><b>" . number_format($hra_salary, 2) . "</b></td>
                                                    <td><b>" . number_format($allowance_salary, 2) . "</b></td>
                                                    <td><b>" . number_format($ot_sl_ar_pay, 2) . "</b></td>
                                                    <td><b>" . number_format($total, 2) . "</b></td>
                                                    <td></td>
                                                    <td><b>" . number_format($leave_other, 2) . "</b></td>
                                                    <td><b>" . number_format($salary_advance, 2) . "</b></td>
                                                    <td><b>" . number_format($total_deductions, 2) . "</b></td>
                                                    <td><b>" . number_format($net_amount, 2) . "</b></td>
                                                </tr>";

                                    $table .= "</tbody></table>";
                                    $html .= $table;

                                    $total1 = $total2 = $total3 = 0;
                                    $summary = "<br/><br/><table border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse;max-width:400px'>
                                            <tr valign='top'>
                                            <td><b>Mode</b></td>
                                            <td><b>Salary</b></td>
                                            <td><b>WPS Charge</b></td>
                                            <td><b>Total</b></td>
                                            <td><b>PV No</b></td>
                                            </tr>";

                                    foreach ($mode as $k => $v) {
                                        $wps = 0;
                                        if (!empty($v['wps'])) {
                                            $wps = (25 + $v['wps']) * 1.05;
                                        }
                                        $total1 += $v['amount'];
                                        $total2 += $wps;
                                        $total3 += $amount = $wps + $v['amount'];
                                        $paid_to = $k == 'Check' ? 'Check For Mr. Malek' : $k;
                                        $issue_pv = admin_url("admin.php?page=payment-voucher-create&paid_to=$paid_to&amount=$amount");
                                        $summary .= "<tr valign='top'>
                                            <td>{$paid_to}</td>
                                            <td>" . number_format($v['amount'], 2) . "</td>
                                            <td>" . number_format($wps, 2) . "</td>
                                            <td>" . number_format($wps + $v['amount'], 2) . "</td>
                                            <td>".(!empty($v['pv_no'])?$v['pv_no']:"<a href='$issue_pv' target='_blank'>Issue Payment Voucher</a>")."</td>
                                            </tr>";
                                    }


                                    $summary .= "<tr valign='top'>
                                            <td><b>Grand Total</b></td>
                                            <td>" . number_format($total1, 2) . "</td>
                                            <td>" . number_format($total2, 2) . "</td>
                                            <td>" . number_format($total3, 2) . "</td>
                                            <td></td>
                                            </tr>";


                                    $summary .= "</table><br/>";

                                    $html .= $summary;

                                    $sign = "<br/><br/><table cellpadding=3 cellspacing=3 style='border-collapse:collapse;max-width:800px'>
                                            <tr valign='top'>
                                            <td style='width:80px'>Prepared by:</td>
                                            <td>____________________________</td>
                                            <td style='width:350px'>&nbsp;</td>
                                            <td style='width:80px'>Approved by:</td>
                                            <td>____________________________</td>
                                            </tr>
                                            <tr valign='top'><td colspan=5><br/></td></tr>
                                            <tr valign='top'>
                                            <td>Date:</td>
                                            <td>____________________________</td>
                                            <td style='width:350px'>&nbsp;</td>
                                            <td>Date:</td>
                                            <td>____________________________</td>
                                            </tr>";

                                    $sign .= "</table><br/>";

                                    $html .= $sign;

                                    echo $html .= "";
                                    $pdf_file = $salary_sheet->pdf_path;
                                    store_pdf_path('ctm_hr_salary_sheets', $salary_sheet->id, "SALARY_SHEET{$salary_sheet->id}_{$salary_sheet->month}.pdf", $pdf_file);
                                    ?>
                                </div>
                            </div>

                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-center">
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                    }
                                    ?>
                                    <a href = '<?= export_excel_report($pdf_file, 'bank_deposit_registory', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                    <br/>
                                    <br/>
                                </div>
                            </div>


                        <?php } ?>

                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('salary_sheet_copy_dir'));
}
