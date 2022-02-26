<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_attendance_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $month = !empty($getdata['month']) ? $getdata['month'] : date('m');
    $year = !empty($getdata['year']) ? $getdata['year'] : date('Y');

    $attendance_dates = $wpdb->get_results("SELECT DISTINCT attendance_date FROM {$wpdb->prefix}ctm_hr_attendances WHERE attendance_date like '%{$year}-{$month}%' ORDER BY attendance_date ASC");
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_hr_attendances WHERE attendance_date like '%{$year}-{$month}%'  ORDER BY attendance_date ASC");
    $employees = [];
    foreach ($results as $value) {
        $employees[$value->emp_id][$value->attendance_date] = $value;
    }
    ksort($employees);
    ?>
    <style>
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                        <h1 class="wp-heading-inline">Attendance Sheet</h1>
                        <a id="add-new-client" href="<?= "admin.php?page={$page}-add" ?>" class="page-title-action btn-primary" >Add Attendance</a>
                        <a id="add-new-client" href="<?= "admin.php?page={$page}-edit" ?>" class="page-title-action btn-primary" >Edit Attendance</a>
                        <br/><br/> 
                        <?php if (!empty($getdata['msg'])) { ?>
                            <br/>
                            <div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                Attendance has been added successfully
                            </div>
                        <?php } ?>

                        <div id="page-inner-content">
                            <form id="filter-form1" method="get">
                                <input type="hidden" name="page" value="<?= $page ?>" />
                                <table class="form-table">
                                    <tr>
                                        <td>
                                            <select name="month" onchange="this.form.submit()">
                                                <option value="">Select Month</option>
                                                <?php
                                                for ($i = 1; $i <= 12; $i++) {
                                                    $select = $month == $i ? 'selected' : '';
                                                    echo"<option value='" . ($i < 10 ? '0' : '') . "$i' $select>" . rb_date("$year-$i-01", 'F') . "</option>";
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
                            </form>
                            <br/> 
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:100%;margin:auto'>
                                    <?php
                                    $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{text-align:center;font-size:10px;}
                                        table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                    </style>
                                    ";

                                    $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                            <td style='text-align:left;vertical-align: middle;'>
                                            <h4><span style='font-size:26px;font-weight:bold'>ATTENDANCE SHEET</span></h4>
                                             <span style='font-size:14px;'>For the period from <span class='text-red'>" . rb_date("$year-$month-01", 'd-M-Y') . "</span>,"
                                            . " to <span class='text-red'>" . rb_date("$year-$month-01", 't-M-Y') . "</span></span>
                                            </td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                                    $hd_rgb = 'rgb(250,192,144)';
                                    $upl_rgb = 'rgb(235,33,33)';
                                    $pl_rgb = 'rgb(147,205,221)';
                                    $sl_rgb = 'rgb(221,217,195)';
                                    $lt_rgb = 'rgb(179,162,199)';
                                    $eo_rgb = 'rgb(195,214,155)';

                                    $hd_color = "<b style='background: $hd_rgb;padding: 1px 5px;width: 10px; height: 14px;'>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;";
                                    $upl_color = "<b style='background: $upl_rgb;padding: 1px 5px;width: 10px; height: 14px;'>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;";
                                    $pl_color = "<b style='background: $pl_rgb;padding: 1px 5px;width: 10px; height: 14px;'>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;";
                                    $sl_color = "<b style='background: $sl_rgb;padding: 1px 5px;width: 10px; height: 14px;'>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;";
                                    $lt_color = "<b style='background: $lt_rgb;padding: 1px 5px;width: 10px; height: 14px;'>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;";
                                    $eo_color = "<b style='background: $eo_rgb;padding: 1px 5px;width: 10px; height: 14px;'>&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;";

                                    $th_days = '';
                                    foreach ($attendance_dates as $value) {
                                        $th_days .= "<th>" . rb_date($value->attendance_date, 'D') . "</th>";
                                    }

                                    $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=2>NAME</th>
                                        <th colspan=" . count($attendance_dates) . ">X = Full Day ; {$hd_color}HD = Half Day ; {$upl_color}UPL = Un Paid Leave ; {$pl_color}PL = Paid Leave ; {$sl_color}SL = Sick Leave ; {$lt_color}LT = Late ; {$eo_color}EO = Early Out</th>
                                        <th rowspan=2>Full<br/>Day<br/>(X)</th>
                                        <th rowspan=2>Half<br/>Day<br/>(HD)</th>
                                        <th rowspan=2>Un<br/>Paid<br/>Leave</th>
                                        <th rowspan=2>Paid<br/>Leave<br/>(PL)</th>
                                        <th rowspan=2>Sick<br/>Leave<br/>(SL)</th>
                                        <th rowspan=2>Late<br/>(LT)</th>
                                        <th rowspan=2>Early<br/>Out<br/>(EO)</th>
                                        <th rowspan=2>Total<br/>Days</th>
                                        <th rowspan=2>NOTE</th>
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>
                                       
                                        $th_days
                                        
                                    </tr>
                                    
                                    
                                </thead>";

                                    $table .= "<tbody>";

                                    // Receipts 


                                    foreach ($employees as $emp_id => $attendances) {

                                        $attendances = sync_attendance_with_emplyee($attendance_dates, $attendances);

                                        $table .= "<tr>
                                            <td id={$emp_id}>" . get_employee($emp_id, 'name') . "</td>";
                                        $full_day = $half_day = $paid_leave = $un_paid_leave = $sick_leave = $late = $early_out = $total_days = 0;

                                        foreach ($attendances as $value) {
                                            $style = '';
                                            if (empty($value->full_day) && empty($value->half_day) && empty($value->un_paid_leave) && empty($value->paid_leave) && empty($value->sick_leave) && empty($value->late) && empty($value->early_out)) {
                                                $style = 'background:lightgray;color:lightgray';
                                            } else if (!empty($value->half_day)) {
                                                $style = "background:$hd_rgb;color:#000";
                                            } else if (!empty($value->un_paid_leave)) {
                                                $style = "background:$upl_rgb;color:#000";
                                            } else if (!empty($value->paid_leave)) {
                                                $style = "background:$pl_rgb;color:#000";
                                            } else if (!empty($value->sick_leave)) {
                                                $style = "background:$sl_rgb;color:#000";
                                            } else if (!empty($value->late)) {
                                                $style = "background:$lt_rgb;color:#000";
                                            } else if (!empty($value->early_out)) {
                                                $style = "background:$eo_rgb;color:#000";
                                            }

                                            $table .= "<td style='$style'>" . rb_date($value->attendance_date, 'd') . "</td>";
                                            $full_day += $value->full_day;
                                            $half_day += $value->half_day;
                                            $un_paid_leave += $value->un_paid_leave;
                                            $paid_leave += $value->paid_leave;
                                            $sick_leave += $value->sick_leave;
                                            $late += $value->late;
                                            $early_out += $value->early_out;
                                        }
                                        
                                        $total_days = ($full_day + $paid_leave + $sick_leave + $late + $early_out + $un_paid_leave + ($half_day ? ($half_day / 2) : 0)) - $un_paid_leave;
                                        $table .= "<td>$full_day</td>
                                            <td>$half_day</td>
                                            <td>$un_paid_leave</td>
                                            <td>$paid_leave</td>
                                            <td>$sick_leave</td>
                                            <td>$late</td>
                                            <td>$early_out</td>
                                            <td>$total_days</td>
                                            <td>$value->note</td>
                                            </tr>";
                                    }

                                    $table .= "</tbody></table>";
                                    $html .= $table;
                                    echo $html .= "";
                                    $pdf_file = make_pdf_file_name("EMPLOYEE_ATTENDANCE_" . rb_date("$year-$month-01", 'F_Y') . ".pdf")['path'];
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
    generate_pdf($html, $pdf_file, null, 1);

    pdf_copy($pdf_file, get_option('attendance_sheet_copy_dir'));
}
