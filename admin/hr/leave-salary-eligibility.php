<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_leave_salary_eligibility_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $emp_id = !empty($getdata['emp_id']) ? $getdata['emp_id'] : '';

    $employees = get_all_employees();

    if (!empty($emp_id)) {
        $employee = get_employee($emp_id);
        $leave_salary_amount = number_format($employee->basic_salary + $employee->hra_salary, 2);

        $sql = "SELECT paid_leave,attendance_date FROM {$wpdb->prefix}ctm_hr_attendances WHERE emp_id='{$emp_id}' AND attendance_date>='{$employee->joining_date}' AND attendance_date<='" . date('Y-m-d') . "' ORDER BY attendance_date ASC";
        $results = $wpdb->get_results($sql);
        $attendances = [];
        foreach ($results as $value) {
            $attendances[rb_date($value->attendance_date, 'Y-m')][] = $value;
        }
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

                        <h1 class="wp-heading-inline"></h1>
                        <form id="add-new-leave-salary-form" method="get">
                            <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                            <table class="form-table" style="max-width:300px">
                                <tr>
                                    <td style="vertical-align:top">
                                        <span id="open-close-menu" title="Close & Open Side Menu" style="margin:0" class="dashicons dashicons-editor-code"></span>
                                    </td>
                                    <td>
                                        <select name="emp_id" onchange="this.form.submit()">
                                            <option>Select Employee</option>
                                            <?php foreach ($employees as $value) { ?>
                                                <option value="<?= $value->id ?>" <?= $emp_id == $value->id ? 'selected' : '' ?>><?= $value->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>                                           
                            </table>
                            <br/>
                        </form>
                        <br/>

                        <?php
                        //echo '<pre>';print_r($attendances);echo '</pre>'; die;

                        $html = $pdf_file = '';
                        if (!empty($emp_id)) {
                            ?>
                            <div id="page-inner-content">

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
                                            <h4><span style='font-size:26px;font-weight:bold'>LEAVE SALARY ELIGIBILITY</span></h4>
                                            </td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                                        $table .= "<table class='confirm-order-items' cellpadding=3 cellspacing=3 style='text-align:left'>
                                                <tr valign=middle><td style='width:100px'>Name</td><td style='width:100px'>$employee->name</td><td></td></tr>
                                                <tr valign=middle><td>Joining Date</td><td>" . rb_date($employee->joining_date) . "</td><td></td></tr>
                                                <tr valign=middle><td><br/></td><td></td><td></td></tr>
                                                <tr valign=middle><td>Basic Salary</td><td>$employee->basic_salary</td><td></td></tr>
                                                <tr valign=middle><td>HRA</td><td class='bb'>$employee->hra_salary</td><td></td></tr>
                                                <tr valign=middle><td></td><td>$leave_salary_amount</td><td></td></tr>
                                                <tr valign=middle><td></td><td class='bb bt'></td><td></td></tr>
                                                <tr valign=middle><td><br/></td><td></td><td></td></tr>
                                            </table>";

                                        $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                        
                                   
                                    
                                    <tr valign=middle class='text-center bg-blue'>
                                        <td>From</td>
                                        <td>To</td>
                                        <td>Days of<br/>Service<br/>(less UPL)</td>
                                        <td>Accumulated<br/>Days of<br/>service</td>
                                        <td>No. of Days<br/>on Leave</td>
                                        <td>Leave<br/>Days<br/>Entitled</td>
                                        <td>Balance<br/>Leave<br/>Days</td>
                                        <td>Leave<br/>Salary Due<br/>Date</td>
                                        <td>Leave<br/>Salary<br/>Amount</td>
                                        <td>Leave<br/>Salary<br/>Paid</td>
                                        <td>Balance<br/>Leave<br/>Salary<br/>Payable</td>
                                    </tr>";

                                        $table .= "<tbody>";
                                        update_user_meta($emp_id, 'accumulated_days', 0);
                                        
                                        $paid_leave = $entitled_days = $due_date = $balance_salary = $balance_leave = 0;

                                        foreach ($attendances as $key => $value) {

                                            $accumulated_days = (int)get_user_meta($emp_id, 'accumulated_days',true);
                                            $first_day = $value[0]->attendance_date;
                                            $start_day = rb_date($value[0]->attendance_date, 'd');

                                            $is_today = $key == date('Y-m') ? true : false;

                                            $end_day = $is_today ? date('d') : rb_date($key . '-01', 't'); //consider last day as one day 
                                            $service_days = ($end_day + 1) - $start_day;
                                            $accumulated_days += $service_days;
                                            update_user_meta($emp_id, 'accumulated_days',$accumulated_days);
                                            
                                            $paid_leave = $balance_leave = $paid_leave_salary = $leave_salary = $entitled_days = $due_date = 0;

                                            $balance_leave_salary = get_user_meta($emp_id, 'balance_leave_salary', true);
                                            if ($balance_leave_salary) {
                                                foreach ($value as $v) {
                                                    $paid_leave += $v->paid_leave ?: 0;
                                                }
                                                $balance_leave = $paid_leave ? $entitled_days - $paid_leave : 0;
                                                $paid_leave_salary = $balance_leave_salary;
                                                update_user_meta($emp_id, 'balance_leave_salary', 0);
                                                $balance_leave_salary=0;
                                            }

                                            if ($accumulated_days >= 365 && !$balance_leave_salary) {
                                                $entitled_days = 30;
                                                $balance_leave = 30;
                                                $due_date = rb_date($key . '-01', 't-M-Y');
                                                $balance_leave_salary = $leave_salary = $leave_salary_amount;
                                                update_user_meta($emp_id, 'balance_leave_salary', $leave_salary_amount);
                                                update_user_meta($emp_id, 'accumulated_days', 0);
                                            }

                                            if ($accumulated_days > 366) {
                                                foreach ($value as $v) {
                                                    $paid_leave += $v->paid_leave ?: 0;
                                                }
                                                $balance_leave = $paid_leave ? $entitled_days - $paid_leave : 0;
                                            }





                                            $table .= "<tr valign=middle class='text-center'>
                                        <td>" . rb_date($first_day, 'd-M-Y') . "</td>
                                        <td>" . ($is_today ? date('d-M-Y') : rb_date($first_day, 't-M-Y')) . "</td>
                                        <td>" . $service_days . "</td>
                                        <td>$accumulated_days</td>
                                        <td>" . ($paid_leave ? $paid_leave : '') . "</td>
                                        <td>" . ($entitled_days ? $entitled_days : '') . "</td>
                                        <td>" . ($balance_leave ? $balance_leave : '') . "</td>
                                        <td>" . ($due_date ? $due_date : '') . "</td>
                                        <td>" . ($leave_salary ? $leave_salary : '') . "</td>
                                        <td>" . ($paid_leave_salary ? $paid_leave_salary : '') . "</td>
                                        <td>" . ($balance_leave_salary ? $balance_leave_salary : '') . "</td>
                                    </tr>";
                                        }


                                        $table .= "</tbody></table>";
                                        $html .= $table;
                                        echo $html .= "";
                                        $pdf_file = make_pdf_file_name("LEAVE_SALARY_ATTENDANCE.pdf")['path'];
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
}
