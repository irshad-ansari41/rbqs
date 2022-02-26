<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_suppoort_monthly_bonus_page() {
    global $wpdb;

    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);


    if (!empty($postdata['share_percent'])) {
        update_option('Share_Percent', $postdata['share_percent']);
    }
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $year = !empty($getdata['year']) ? $getdata['year'] : date('Y');
    $from_month = !empty($getdata['from_month']) ? $getdata['from_month'] : date('01');
    $to_month = !empty($getdata['to_month']) ? $getdata['to_month'] : date('12');

    $bonus = $commission = 0;
    for ($i = (int) $from_month; $i <= (int) $to_month; $i++) {
        $bonus += get_option('SP_Personal_Target_Bonus' . rb_date("{$year}-{$i}-01", '_F_Y'));
        $commission += get_option('Showroom_Target_Commission' . rb_date("{$year}-{$i}-01", '_F_Y'));
    }

    $args = array(
        'role' => 'sales',
        'orderby' => 'user_nicename',
        'order' => 'ASC'
    );
    //$sales = get_users($args);
    $share_percent = get_option('Share_Percent');

    $employees = get_active_employees('id,name,designation,department');

    $Share_Percent_setting = [];
    $j = 0;
    foreach ($employees as $value) {
        if (in_array($value->department, ['Accounts', 'Commercial', 'Logistics'])) {
            $Share_Percent_setting[$value->department][] = $value;
            $j++;
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
        table.form-table tr th,table.form-table tr td{text-align: left;vertical-align: bottom;}
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <h1 class="wp-heading-inline"></h1><br/>
                        <form id="filter-form1" method="get">
                            <input type="hidden" name="page" value="<?= $page ?>" />
                            <table class="form-table">
                                <tr>
                                    <td style="vertical-align:top">
                                        <span id="open-close-menu" style="margin:0" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                                        &nbsp;&nbsp;
                                    </td>
                                    <td>
                                        <select name="from_month" onchange="this.form.submit()">
                                            <option value="">Select From Month</option>
                                            <?php
                                            for ($i = 1; $i <= 12; $i++) {
                                                $select = $from_month == $i ? 'selected' : '';
                                                echo"<option value='" . ($i < 10 ? '0' : '') . "$i' $select>" . rb_date("$year-$i-01", 'F') . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="to_month" onchange="this.form.submit()">
                                            <option value="">Select To Month</option>
                                            <?php
                                            for ($i = 1; $i <= 12; $i++) {
                                                $select = $to_month == $i ? 'selected' : '';
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
                                    </style>";


                                $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:24px;font-weight:bold'>SUPPORT's BONUS and COMMISSION CALCULATION</span></h4>
                                            <span style='font-size:14px;'>For the month of from <b>" . rb_date("{$year}-{$from_month}-01", 'M, Y') . "</b> to <b>" . rb_date("{$year}-{$to_month}-01", 'M, Y') . "</b></span></td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                                $table .= "<table class='confirm-order-items' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=2>DEPARTMENT</th>
                                        <th rowspan=2>DESIGNATION</th>
                                        <th rowspan=2>STAFF</th>
                                        <th colspan=2>SUPPORT's Share</th>
                                        <th rowspan=2>Total Bonus and Commission</th>                                        
                                        <th rowspan=2>Amount</th>                                        
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th>SP's Personal Target Bonus</th>
                                        <th>Showroom Target Commission</th>
                                    </tr>
                                </thead>";

                                $table .= "<tbody>";
                                $total_share_amount = 0;


                                // Receipts 
                                $once = true;
                                foreach ($Share_Percent_setting as $key => $value) {
                                    $share_dept_amount = ($bonus + $commission) * $share_percent[$key] / 100;
                                    foreach ($value as $k => $v) {
                                        $share_amount = $share_dept_amount * $share_percent[$v->name] / 100;
                                        $table .= "<tr>";
                                        $table .= $k == 0 ? "<td rowspan='" . count($value) . "'>$key</td>" : '';
                                        $table .= "<td>$v->designation</td>
                                            <td>$v->name</td>";
                                        $table .= $once ? "<td rowspan='$j'>" . number_format($bonus, 2) . "</td>" : '';
                                        $table .= $once ? "<td rowspan='$j'>" . number_format($commission, 2) . "</td>" : '';
                                        $table .= $once ? "<td rowspan='$j'>" . number_format($bonus + $commission, 2) . "</td>" : '';
                                        $table .= "<td>" . number_format($share_amount, 2) . "</td>
                                            </tr>";

                                        $total_share_amount += $share_amount;
                                        $once = false;
                                    }
                                }

                                $table .= "<tr>
                                            <td colspan=6><b>TOTAL</b></td>
                                            <td><b>" . number_format($total_share_amount, 2) . "</b></td>
                                            </tr>";

                                $table .= "</tbody></table>";
                                $html .= $table;

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
                                echo $html;
                                
                                $fromMonth = rb_date("{$year}-{$from_month}-01", 'M_Y');
                                $toMonth = rb_date("{$year}-{$to_month}-01", 'M_Y');
                                $pdf_file = make_pdf_file_name("SUPPORT_BONUS_COMMISSION_CALCULATION_{$fromMonth}_To_{$toMonth}.pdf")['path'];
                                ?>
                            </div>	
                        </div>
                        <br/>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                }
                                ?>
                                <a href = '<?= export_excel_report($pdf_file, 'monthly_sales_bonus', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>

                            </div>
                        </div>
                        <br/>
                        <div id='page-inner-content' class='postbox'><br/>
                            <div class='inside' style='max-width:100%;margin:auto'>
                                <form method="post">
                                    <?php
                                    $per_dept = "<table class='form-table' border=1' style='width:500px'>
                                        <tr><td colspan=2 class='text-center'><span>% Share - <strong>per DEPARTMENT</strong></span></td></tr>";
                                    $per_staff = '';
                                    $total_dept = 0;
                                    foreach ($Share_Percent_setting as $key => $value) {
                                        $per_dept .= "<tr>
                                                <td><span>{$key}</span></td>
                                                <td><input type=number name='share_percent[{$key}]' value='{$share_percent[$key]}' placeholder='Share Percent' required min=0 max=100 step='0.01'/></td>
                                            </tr>";
                                        $total_dept += $share_percent[$key];
                                        $staff_total = 0;
                                        $per_staff .= "<table class='form-table' border=1 style='width:500px'>
                                                <tr><td colspan=3 class='text-center' ><strong>{$key}</strong></td></tr>"
                                                . "<tr><td><strong>Designation</strong></td>"
                                                . "<td><strong>Name</strong></td>"
                                                . "<td><strong>% Share / Person </strong></td></tr>";
                                        foreach ($value as $v) {
                                            $per_staff .= "<tr style='verticle-align:middle'>
                                                    <td style='vertical-align: middle;'><span>{$v->designation}</span></td>
                                                    <td style='vertical-align: middle;'><span>{$v->name}</span></td>
                                                    <td><input type=number name='share_percent[{$v->name}]' value='{$share_percent[$v->name]}' required placeholder='Share Percent' min=0 max=100 step='0.01' /></td>
                                                </tr>";
                                            $staff_total += $share_percent[$v->name];
                                        }
                                        $per_staff .= "<tr>
                                                    <td colspan=2><strong>Total</strong></td><td><strong>$staff_total</strong></td>
                                                </tr>
                                            </table>
                                            <br/>";
                                    }
                                    $per_dept .= "<tr>
                                                <td><strong>Total</strong></td><td><strong>{$total_dept}</strong></td>
                                            </tr>";

                                    $per_dept .= "</table>";
                                    ?>

                                    <?= $per_dept ?>
                                    <br/>
                                    % Share - <strong>per Staff</strong>
                                    <?= $per_staff ?>

                                    <table class="form-table" border="1" style='width:500px'>
                                        <tr>
                                            <td colspan="2" ><button type="submit"  class="button-primary btn-block" value="update" >Update</button></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <br/>
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
    pdf_copy($pdf_file, get_option('support_bonus_copy_dir'));
}
