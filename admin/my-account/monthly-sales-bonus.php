<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_sales_monthly_bonus_page() {
    global $wpdb;

    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    if (!empty($postdata['update'])) {
        update_option('commission_on_each_percent', $postdata['commission_on_each_percent']);
    }

    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $month = !empty($getdata['month']) ? $getdata['month'] : date('m');
    $year = !empty($getdata['year']) ? $getdata['year'] : date('Y');

    $args = array(
        'role' => 'sales',
        'orderby' => 'user_nicename',
        'order' => 'ASC'
    );
    $results = get_users($args);

    $total_net_sale = $total_target = 0;
    foreach ($results as $key => $value) {
        $user = get_user($value->ID);
        $reversal_amount = get_sales_reversal_by_sp_id($user->sp_id, "$year-$month");
        $credit_note_amount = get_credit_note_by_sp_id($user->sp_id, "$year-$month");
        $sale_amount = get_total_sales_by_sp_id($user->sp_id, "$year-$month");
        $sale_amount_ex_vat = get_ex_vat_amount($sale_amount);
        $reversal_amt = ($reversal_amount + $credit_note_amount) / 1.05;
        $net_sale_amount = $sale_amount_ex_vat - $reversal_amt;
        $total_net_sale += $net_sale_amount;
        $total_target += $user->target;

        $sale['employee_id'] = $user->employee_id;
        $sale['sp_id'] = $user->sp_id;
        $sale['name'] = $user->display_name;
        $sale['target'] = $user->target;
        $sale['achiever_90'] = $user->achiever_90;
        $sale['achiever_100'] = $user->achiever_100;
        $sale['additional_10'] = $user->additional_10;
        $sale['support_share'] = $user->support_share;
        $sale['sale_amount_ex_vat'] = $sale_amount_ex_vat;
        $sale['reversal_amt'] = $reversal_amt;
        $sale['net_sale_amount'] = $net_sale_amount;
        $sales[$key] = (object) $sale;
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
                        <br/>
                        <div id='page-inner-content' class='postbox'><br/>
                            <div class='inside' style='max-width:100%;margin:auto'>
                                <strong>SHOWROOM TARGET & COMMISSION</strong>
                                <form method="post">
                                    <table class="form-table">
                                        <tr>
                                            <td>
                                                <label>Commission on each %</label>
                                                <input type="number"  name="commission_on_each_percent" 
                                                       value="<?= get_option('commission_on_each_percent', 50) ?>">
                                            </td>
                                            <td><button type="submit"  class="button-primary" name="update" value="update" >Update</button></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
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
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:24px;font-weight:bold'>MONTHLY SALES' BONUS and COMMISSION CALCULATION</span></h4>
                                            <span style='font-size:14px;'>For the month of <b>" . rb_date("{$year}-{$month}-01", 'F') . ", {$year}</b></span></td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                                $table .= "<table class='confirm-order-items' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=3>SP ID</th>
                                        <th rowspan=3>NAME</th>
                                        <th rowspan=3>TARGET SALES</th>
                                        <th colspan=3>ACTUAL</th>
                                        <th rowspan=3>%<br/>ACHIEVEMENT</th>
                                        <th colspan=9>BONUS</th>
                                        <th rowspan=2 colspan=2 >SHOWROOM TARGET COMMISSION</th>
                                        <th rowspan=3>TOTAL BONUS and COMMISSION</th>
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=2>SALES<br/>(EXCLUDING VAT AMOUNT)</th>
                                        <th rowspan=2>CREDIT NOTE / Reversal of confirmed QTN</th>
                                        <th rowspan=2>NET SALES</th>
                                        <th rowspan=2>For 90% Achiever</th>
                                        <th rowspan=2>For 100% Achiever</th>
                                        <th rowspan=2>For each Add'l 10%</th>
                                        <th rowspan=2>TOTAL</th>
                                        <th>SUPPORT's Share</th>
                                        <th rowspan=2>NET</th>
                                        <th colspan=2>ATTENDANCE</th>
                                        <th rowspan=2>FINAL</th>
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>                                       
                                        <th>Amount</th>
                                        <th>Day Attended<br/>(X+HD+LT+EO)</th>
                                        <th>Total Days in a Month</th>
                                        <th>% Showroom Cotribution</th>
                                        <th>Commission</th>
                                    </tr>
                                </thead>";

                                $table .= "<tbody>";
                                $toatl_sale_amount = $toatl_reversal_amount = $total_net_sale_amount = 0;
                                $total_achiever_90 = $total_achiever_100 = $total_additional_10 = $total_achiever = 0;
                                $total_support_share = $total_net_bonus = $total_final_bonus = $total_commission = $total_bonus_and_commission = 0;
                                // Bonus 
                                foreach ($sales as $value) {

                                    $attended_days = get_days_attended($value->employee_id, "$year-$month");
                                    $total_day = rb_date("01-$month-$year", 't');

                                    $achivement = ($value->net_sale_amount / $value->target) * 100;
                                    $achiever_90 = $achiever_100 = $additional_10 = 0;
                                    if ($achivement >= 90 && $achivement < 100) {
                                        $achiever_90 = $value->target * $value->achiever_90;
                                    }
                                    if ($achivement >= 100) {
                                        $achiever_100 = $value->target * $value->achiever_100;
                                        $each_percent = floor(($achivement - 100) / 10);
                                        for ($i = 1; $i <= $each_percent; $i++) {
                                            $additional_10 += $value->additional_10;
                                        }
                                    }


                                    $total_bonus = $achiever_90 + $achiever_100 + $additional_10;
                                    $support_share = $total_bonus * $value->support_share / 100;
                                    $net_bonus = $total_bonus - $support_share;
                                    $final_bonus = round($net_bonus * $attended_days / $total_day);

                                    $contribution = $commission = 0;
                                    if ($total_net_sale >= $total_target) {
                                        $contribution = $total_net_sale ? (($value->net_sale_amount / $total_net_sale) * 100) : 0;
                                        $commission = ($achivement >= 100) ? $contribution * get_option('commission_on_each_percent', 0) : 0;
                                    }
                                    $bonus_and_commission = round($final_bonus + $commission);

                                    $table .= "<tr>
                                            <td>{$value->sp_id}</td>
                                            <td>$value->name</td>
                                            <td>" . number_format($value->target, 2) . "</td>
                                            <td>" . number_format($value->sale_amount_ex_vat, 2) . "</td>
                                            <td class='text-red'>" . ($value->reversal_amt ? number_format($value->reversal_amt, 2) : '') . "</td>
                                            <td>" . number_format($value->net_sale_amount, 2) . "</td>
                                            <td>" . number_format($achivement, 2) . "%</td>
                                            <td>" . ($achiever_90 ? number_format($achiever_90, 2) : '') . "</td>
                                            <td>" . ($achiever_100 ? number_format($achiever_100, 2) : '') . "</td>
                                            <td>" . ($additional_10 ? number_format($additional_10, 2) : '') . "</td>
                                            <td>" . ($total_bonus ? number_format($total_bonus, 2) : '') . "</td>
                                            <td class='text-red'>" . ($support_share ? number_format($support_share, 2) : '') . "</td>
                                            <td>" . ($net_bonus ? number_format($net_bonus, 2) : '') . "</td>
                                            <td>" . ($net_bonus ? $attended_days : '-') . "</td>
                                            <td>" . ($net_bonus ? $total_day : '-') . "</td>
                                            <td>" . ($net_bonus ? number_format($final_bonus, 2) : '') . "</td>
                                            <td>" . ($contribution ? number_format($contribution, 2) . '%' : '') . "</td>
                                            <td>" . ($commission ? number_format($commission, 2) : '') . "</td>
                                            <td>" . ($bonus_and_commission ? number_format($bonus_and_commission, 2) : '') . "</td>
                                            </tr>";


                                    $toatl_sale_amount += $value->sale_amount_ex_vat;
                                    $toatl_reversal_amount += $value->reversal_amt;
                                    $total_net_sale_amount += $value->net_sale_amount;
                                    $total_achiever_90 += $achiever_90;
                                    $total_achiever_100 += $achiever_100;
                                    $total_additional_10 += $additional_10;
                                    $total_achiever += $total_bonus;
                                    $total_support_share += $support_share;
                                    $total_net_bonus += $net_bonus;
                                    $total_final_bonus += $final_bonus;
                                    $total_commission += $commission;
                                    $total_bonus_and_commission += $bonus_and_commission;
                                }


                                $table .= "<tr>
                                            <td colspan=3><b>TOTAL</b></td>
                                            <td><b>" . number_format($toatl_sale_amount, 2) . "</b></td>
                                            <td class='text-red'><b>" . number_format($toatl_reversal_amount, 2) . "</b></td>
                                            <td><b>" . number_format($total_net_sale_amount, 2) . "</b></td>
                                            <td></td>
                                            <td><b>" . number_format($total_achiever_90, 2) . "</b></td>
                                            <td><b>" . number_format($total_achiever_100, 2) . "</b></td>
                                            <td><b>" . number_format($total_additional_10, 2) . "</b></td>
                                            <td><b>" . number_format($total_achiever, 2) . "</b></td>
                                            <td class='text-red'><b>" . number_format($total_support_share, 2) . "</b></td>
                                            <td><b>" . number_format($total_net_bonus, 2) . "</b></td>
                                            <td><b></b></td>
                                            <td><b></b></td>
                                            <td><b>" . number_format($total_final_bonus, 2) . "</b></td>
                                            <td><b>" . '' . "</b></td>
                                            <td><b>" . ($total_commission ? number_format($total_commission, 2) : '') . "</b></td>
                                            <td><b>" . ($total_bonus_and_commission ? number_format($total_bonus_and_commission, 2) : '') . "</b></td>
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
                                echo $html .= "";

                                update_option('SP_Personal_Target_Bonus' . rb_date("{$year}-{$month}-01", '_F_Y'), $total_support_share);
                                update_option('Showroom_Target_Commission' . rb_date("{$year}-{$month}-01", '_F_Y'), $total_commission);
                                $pdf_file = make_pdf_file_name("MONTHLY_SALES_BONUS_" . rb_date("{$year}-{$month}-01", 'F') . ".pdf")['path'];
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
                                <a href = '<?= export_excel_report($pdf_file, 'monthly_sales_bonus', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                <br/>
                                <br/>
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
    pdf_copy($pdf_file, get_option('sales_bonus_copy_dir'));
}
