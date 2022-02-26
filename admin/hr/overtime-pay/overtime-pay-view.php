<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_overtime_pay_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    $month = !empty($getdata['month']) ? $getdata['month'] : '';
    if (!empty($month)) {
        $month_state = explode('.', $getdata['month']);
        $from_date = $month_state[0];
        $to_date = $month_state[1];
    } else {
        $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : date('Y-m-01');
        $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : date('Y-m-t');
    }

    $ot_pay = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_overtime_pays WHERE ot_month='{$to_date}'  limit 1");
    $comment = !empty($postdata['comment']) ? $postdata['comment'] : (!empty($ot_pay->comment) ? $ot_pay->comment : '');

    $results = $wpdb->get_results("SELECT id,emp_id,ot_date,ot_time_from,ot_time_to,ot_rate,purpose FROM {$wpdb->prefix}ctm_hr_overtime_days WHERE ot_date>='{$from_date}' AND ot_date<='{$to_date}' ORDER BY ot_date ASC");
    $overtimes = [];
    foreach ($results as $value) {
        $value->total_salary = get_employee($value->emp_id, 'total_salary');
        $value->hour_salary = number_format($value->total_salary / rb_date($to_date, 't') / 9, 2);
        $overtimes[$value->ot_date . '|' . rb_time($value->ot_time_from) . ' to ' . rb_time($value->ot_time_to)][$value->emp_id] = $value;
    }

    $inner_sql = "SELECT group_concat(emp_id) FROM {$wpdb->prefix}ctm_hr_overtime_days WHERE ot_date>='{$from_date}' AND ot_date<='{$to_date}' group by emp_id";

    $employees = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_hr_employees WHERE id IN ($inner_sql) ORDER BY id ASC");
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
    <style>#OctFri2020_0{border-bottom:1px solid red;}</style>
    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                        <h1 class="wp-heading-inline"></h1>
                        <br/><br/>
                        <form id="add-new-leave-salary-form" method="get">
                            <input type="hidden" name="page"  value="<?= $getdata['page'] ?>" >
                            <table class="form-table" style="max-width:400px">
                                <tr>
                                <tr>
                                    <td>
                                        <select name="month" onchange="this.form.submit()">
                                            <option value="">Select Month</option>
                                            <?php
                                            for ($i = 0; $i <= 24; $i++) {
                                                $startdaymonthyear = date("Y-m-d", strtotime(date('Y-m-01') . " -$i months"));
                                                $enddaymonthyear = date("Y-m-t", strtotime(date('Y-m-01') . " -$i months"));
                                                $monthname = date("F - Y", strtotime(date('Y-m-01') . " -$i months"));
                                                //$months[]=[$startdaymonthyear,$enddaymonthyear,$month];
                                                $select = $month == "{$startdaymonthyear}.{$enddaymonthyear}" ? 'selected' : '';
                                                echo"<option value='{$startdaymonthyear}.{$enddaymonthyear}' $select>$monthname</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" name="from_date" class="search-input" placeholder="ID" value="<?= $from_date ?>" >
                                    </td>
                                    <td>
                                        <input type="date" name="to_date" class="search-input" placeholder="ID" value="<?= $to_date ?>" >
                                    </td>
                                    <td><button type="submit"  class="button-primary" value="Filter" >Filter</button></td>
                                    <td><a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a></td>
                                </tr>                                           
                            </table>
                            <br/>
                        </form>


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
                                            <h4><span style='font-size:26px;font-weight:bold'>OVERTIME PAY</span></h4>
                                            <span style='font-size:14px;'>
                                            " . (!empty($getdata['pdf']) ? $comment : "<input style='width:100%' type='text' id='comment' placeholder='comment' value='$comment' />") . "
                                            
                                            </span>
                                            </td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";



                                $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'><thead>

                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=3>Date</th>
                                        <th rowspan=3>Time</th>
                                        <th colspan='" . count($employees) * 2 . "'>Staff</th>
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>";
                                foreach ($employees as $value) {
                                    $table .= "<th colspan=2>$value->name</th>";
                                }
                                $table .= "</tr>";

                                $table .= "<tr valign=middle class='text-center bg-blue'>";
                                foreach ($employees as $value) {
                                    $table .= "<th>Hours</th>";
                                    $table .= "<th>Rate</th>";
                                }
                                $table .= "</tr><thead>";

                                $table .= "<tbody>";

                                $summary = $days = [];
                                $style = '';
                                $i = 0;
                                foreach ($overtimes as $key => $times) {

                                    $table .= "<tr valign=middle class='text-center'>";

                                    $date_time = explode('|', $key);

                                    $timestamp = rb_date($date_time[0], 'MDY');

                                    $table .= "<td class='{$timestamp}' style='width:80px;'>" . rb_date($date_time[0], 'd/M/y') . "</td>";

                                    $table .= "<td style='width:120px;'>{$date_time[1]}</td>";

                                    $times = sync_overtime_with_emplyee($employees, $times,$from_date);

                                    foreach ($times as $key => $value) {
                                        $diff = !empty($value->ot_time_from) ? rb_datediff($value->ot_time_from, $value->ot_time_to) : '';
                                        $hours = !empty($diff) ? convert_hour_minute_to_decimal($diff->h, $diff->i) : 0;
                                        $table .= !empty($hours) ? "<td>{$hours}</td><td>{$value->ot_rate}</td>" : "<td></td><td></td>";

                                        $summary[$key]['hours'][] = $hours;
                                        $summary[$key]['rate'][] = $value->ot_rate;
                                        $summary[$key]['salary'] = $value->total_salary;
                                        $summary[$key]['hour_salary'] = $value->hour_salary;
                                    }
                                    $table .= "</tr>";
                                    $i++;
                                }


                                $table .= "<tr valign=middle class='text-center'>";
                                $table .= "<td colspan=2><b>Total Addnl Hrs</b></td>";
                                foreach ($summary as $value) {
                                    $table .= "<td colspan=2><b>" . (array_sum($value['hours'])) . "</b></td>";
                                }
                                $table .= "</tr>";



                                $table .= "<tr valign=middle class='text-center'>";
                                $table .= "<td colspan=2><b>Salary / Month</b></td>";
                                foreach ($summary as $value) {
                                    $table .= "<td colspan=2>{$value['salary']}</td>";
                                }
                                $table .= "</tr>";

                                $table .= "<tr valign=middle class='text-center'>";
                                $table .= "<td colspan=2><b>Salary / Hour</b></td>";
                                foreach ($summary as $value) {
                                    $table .= "<td colspan=2>" . number_format($value['hour_salary'], 2) . "</td>";
                                }
                                $table .= "</tr>";

                                $table .= "<tr valign=middle class='text-center'>";
                                $table .= "<td colspan=2><b>Total Additional Salary</b></td>";
                                $ot = [];
                                foreach ($summary as $key => $value) {
                                    $op_pay = 0;
                                    foreach ($value['hours'] as $k => $hour) {
                                        $h = $hour;
                                        $r = $value['rate'][$k];
                                        $p = $value['hour_salary'];
                                        $op_pay += $h * $r * $p;
                                    }
                                    $ot[$key] = $op_pay;
                                    $table .= "<td colspan=2><b>" . round($op_pay) . "</b></td>";
                                }
                                $table .= "</tr>";

                                $table .= "</tbody>";
                                $table .= "</table><br/>";

                                echo $style;
                                echo $html .= $table;

                                insert_or_update_ot($ot, rb_date($from_date, 'Y-m-t'), $comment);

                                $pdf_file = !empty($ot_pay) ? $ot_pay->pdf_path : '';
                                if (!empty($ot_pay)) {
                                    store_pdf_path('ctm_hr_overtime_pays', $ot_pay->id, "OVERTIME_PAY_" . rb_date($from_date, 'm-Y') . ".pdf", $pdf_file);
                                }
                                ?>
                            </div>
                        </div>

                        <div class="row btn-bottom">
                            <div class="col-sm-6 text-center">
                                <a href="?page=overtime-pay" class="button-secondary" >Back</a>&nbsp;&nbsp;

                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                }
                                ?>
                                <a href= '<?= export_excel_report($pdf_file, 'bank_deposit_registory', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <br/>
                                <br/>
                            </div>
                            <div class="col-sm-6 text-center">
                                <form method="post"><input  type='hidden' name='comment' /><button type="submit" name="update" class="btn btn-primary btn-sm">Update</button></form>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#comment').on('input blur', function () {
                jQuery('input[name=comment]').val(jQuery(this).val());
            });
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('overtime_pay_copy_dir'));
}
