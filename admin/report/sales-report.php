<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_sales_report_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $sp_ids = !empty($getdata['sp_ids']) ? $getdata['sp_ids'] : [];
    $month = !empty($getdata['month']) ? $getdata['month'] : date('m');
    $year = !empty($getdata['year']) ? $getdata['year'] : date('Y');

    $from_date = rb_date("{$year}-{$month}-01", 'd-m-Y');
    $to_date = rb_date("{$year}-{$month}-01", 't-m-Y');

    $whr_month = " t1.created_at like '%{$year}-{$month}%'";
    $whr_sp = !empty($sp_ids) ? " t2.sales_person IN ('" . implode("', '", $sp_ids) . "') " : 1;
    $sql = "SELECT t1.quotation_id,t1.revised_no,t1.total_amount,t2.type, t2.sales_person,t2.created_at "
            . "FROM {$wpdb->prefix}ctm_receipts t1 "
            . "LEFT JOIN {$wpdb->prefix}ctm_quotations t2 ON t1.quotation_id= t2.id "
            . "where $whr_month AND $whr_sp AND t1.receipt_type='New' GROUP BY t1.quotation_id ORDER BY t1.quotation_id DESC ";
    $results = $wpdb->get_results($sql);

    $total_sales = [];
    foreach ($results as $value) {
        $total_sales[$value->sales_person][] = $value;
    }

    $sales_reversal = get_sales_reversal_of_month("$year-$month", $sp_ids);
    foreach ($sales_reversal as $value) {
        $quotation = get_quotation($value->quotation_id);
        $total_sales[$quotation->sales_person][] = (object) ['sales_reversal' => $value->id, 'quotation_id' => $value->quotation_id, 'revised_no' => $value->revised_no, 'total_amount' => $value->total_amount, 'type' => $quotation->type, 'sales_person' => $quotation->sales_person, 'created_at' => $value->created_at];
    }

    $tax_credit_note = get_tax_credit_note_of_month("$year-$month", $sp_ids);
    foreach ($tax_credit_note as $value) {
        $quotation = get_quotation($value->quotation_id);
        $total_sales[$quotation->sales_person][] = (object) ['credit_note' => $value->id, 'quotation_id' => $value->quotation_id, 'revised_no' => $value->revised_no, 'total_amount' => $value->total_amount, 'type' => $quotation->type, 'sales_person' => $quotation->sales_person, 'created_at' => $value->created_at];
    }


    ksort($total_sales);
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], 
        #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], 
        #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
        #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
        #tbl-filter{background: #fff; border: 20px solid #fff;}
    </style>
    <div class="wrap">
        <h1 class="wp-heading-inline">Sales Report</h1>
        <br/>
        <form id="filter-form1" method="get">
            <input type="hidden" name="page" value="<?= $page ?>" />
            <table class="form-table">
                <tr>
                    <td style="vertical-align:top">
                        <span id="open-close-menu" style="margin:0" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                        &nbsp;&nbsp;
                    </td>
                    <td>
                        <select name="sp_ids[]" class="chosen-select" style="width: 200px" multiple="true" onchange="this.form.submit()" >
                            <option value="">Select Sales Person</option>
                            <?php
                            $sales = get_sales_persons();
                            foreach ($sales as $value) {
                                $selected = in_array($value['sp_id'], $sp_ids) ? 'selected' : '';
                                echo " <option value='{$value['sp_id']}' $selected>SP {$value['sp_id']} | {$value['name']}</option>";
                            }
                            ?>
                        </select>
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
                    <td><a href="?page=<?= $page ?>"  class="button-secondary" >Reset</a></td>
                </tr>
            </table>
        </form>
        <br/>

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="page-inner-content" class="postbox">
                            <div id='welcome-to-aquila' class='postbox'>
                                <div class='inside' style='max-width:800px;margin:auto'>

                                    <?php
                                    $html = "
                                    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr th,table tr td,p,b,span{font-size:12px;font-family:'Tahoma'}
                                        h6 span{text-transform:uppercase;}
                                        p{margin-bottom: 0;}
                                        table{empty-cells:hidden;}
                                        table tr td{font-size:12px;font-family:'Tahoma'}
                                        table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7),table tr td:nth-child(8),table tr td:nth-child(9){width: 55px;}
                                        .text-center{text-align:center;}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .bg-light{background:##eaedf1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .attachment-large{width:50px;height: auto;}
                                        ul{margin: auto;padding: 0 15px;}
                                        ul#terms{ padding: 0; margin: 0;} 
                                        ul#terms li{ padding: 2px 0!important; margin: 0!important; text-align: left!important; width: 100%!important; font-size:12px; font-weight: bold;}
                                    </style>";
                                    $table = "<table width='800'>
                                                <tr valign='top'>
                                                    <td colspan=2 align=right style='text-align:right;height:60px'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' style='text-align:center'><br/>
                                                        <h6><span style='font-size:22px;'>" . (!empty($sp_id) ? ' SALES PERSON ' : '') . "SALES REPORT</span></h6><br/><br/>
                                                    </td>
                                                </tr>
                                            </table>
                                    <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>";

                                    $table .= "<tr class=bg-blue>
                                      <td colspan='6'><b>Date</b></td></td>
                                      </tr>
                                      <tr>
                                        <td class='text-center'><b>From Date</b></td>
                                        <td class='text-center' colspan=2><b>{$from_date}</b></td>
                                        <td class='text-center'><b>To Date</b></td>
                                        <td class='text-center' colspan=2><b>{$to_date}</b></td>
                                      </tr>
                                      ";

                                    $table .= "<tr><td  colspan='6'><br/></td></tr>
                                      <tr class='text-center bg-blue'>
                                        <td class='text-center'><b>QTN #</b></td>
                                        <td class='text-center'><b>TYPE</b></td>
                                        <td class='text-center'><b>QTN DATE</b></td>
                                        <td class='text-center'><b>Amount<br/>(EXCLUDING VAT)</b></td>
                                        <td class='text-center'><b>VAT</b></td>
                                        <td class='text-center'><b>&nbsp;&nbsp;Total&nbsp;Amount&nbsp;&nbsp;</b></td>
                                      </tr>";

                                    $total_amount = $total_ex_amount = $total_vat_amount = 0;

                                    foreach ($total_sales as $key => $sales) {

                                        $subtotal_amount = $subtotal_ex_amount = $subtotal_vat_amount = 0;

                                        $sp = get_sales_person($key);
                                        $table .= "<tr><td colspan='6'>"
                                                . "<b>SP {$key} | {$sp->display_name} | " . number_format($sp->target, 2) . "</b></td>"
                                                . "</tr>";

                                        usort($sales, function($a, $b) {
                                            return $a->created_at <=> $b->created_at;
                                        });
                                        foreach ($sales as $value) {

                                            if (!empty($value->credit_note)) {
                                                $amount = -$value->total_amount;
                                                $vat = get_vat_ex_amount($amount);
                                                $ex_amount = get_ex_vat_amount($amount);

                                                $subtotal_ex_amount += $ex_amount;
                                                $subtotal_vat_amount += $vat;
                                                $subtotal_amount += $ex_amount + $vat;

                                                $table .= "<tr class='text-center text-red'>
                                        <td class='text-center'>" . ($value->revised_no ? $value->revised_no : $value->quotation_id) . "</td>
                                        <td class='text-center'>{$value->type}</td>
                                        <td class='text-center'>" . rb_date($value->created_at, 'd-M-Y h:i a') . "</td>
                                        <td class='text-center'>" . number_format($ex_amount, 2) . "</td>
                                        <td class='text-center'>" . number_format($vat, 2) . "</td>
                                        <td class='text-center'>" . number_format($amount, 2) . "<span style='float:right' title='Tax Credit Note'>&#8505;</span>" . "</td>
                                      </tr>";
                                            } else if (!empty($value->sales_reversal)) {
                                                $amount = -$value->total_amount;
                                                $vat = get_vat_ex_amount($amount);
                                                $ex_amount = get_ex_vat_amount($amount);

                                                $subtotal_ex_amount += $ex_amount;
                                                $subtotal_vat_amount += $vat;
                                                $subtotal_amount += $ex_amount + $vat;

                                                $table .= "<tr class='text-center text-red'>
                                        <td class='text-center'>" . ($value->revised_no ? $value->revised_no : $value->quotation_id) . "</td>
                                        <td class='text-center'>{$value->type}</td>
                                        <td class='text-center'>" . rb_date($value->created_at, 'd-M-Y h:i a') . "</td>
                                        <td class='text-center'>" . number_format($ex_amount, 2) . "</td>
                                        <td class='text-center'>" . number_format($vat, 2) . "</td>
                                        <td class='text-center'>" . number_format($amount, 2) . "<span style='float:right' title='Reversal of confirmed QTN'>&#8505;</span>" . "</td>
                                      </tr>";
                                            } else {
                                                $amount = $value->total_amount;

                                                $vat = get_vat_ex_amount($amount);
                                                $ex_amount = get_ex_vat_amount($amount);

                                                $subtotal_ex_amount += $ex_amount;
                                                $subtotal_vat_amount += $vat;
                                                $subtotal_amount += $ex_amount + $vat;

                                                $table .= "<tr class='text-center'>
                                        <td class='text-center'>" . ($value->revised_no ? $value->revised_no : $value->quotation_id) . "</td>
                                        <td class='text-center'>{$value->type}</td>
                                        <td class='text-center'>" . rb_date($value->created_at, 'd-M-Y h:i a') . "</td>
                                        <td class='text-center'>" . number_format($ex_amount, 2) . "</td>
                                        <td class='text-center'>" . number_format($vat, 2) . "</td>
                                        <td class='text-center'>" . number_format($amount, 2) . "</td>
                                      </tr>";
                                            }
                                        }
                                        $total_ex_amount += $subtotal_ex_amount;
                                        $total_vat_amount += $subtotal_vat_amount;
                                        $total_amount += $subtotal_amount;

                                        if (count($total_sales) > 1) {
                                            $table .= "
                                    <tr class='font-weight-bold'>
                                       <td class='text-center' colspan=3><b>Sub Total</b></td>
                                       <td class='text-center'><b>" . number_format($subtotal_ex_amount, 2) . "</b></td>
                                       <td class='text-center'><b>" . number_format($subtotal_vat_amount, 2) . "</b></td>"
                                                    . "<td class='text-center'><b>" . number_format($subtotal_amount, 2) . "</b></td></td>
                                    </tr>";
                                        }
                                    }


                                    $table .= "<tr><td  colspan='6'><br/></td></tr>
                                    <tr class='font-weight-bold'>
                                       <td class='text-center' colspan=3><b>Grand Total</b></td>
                                       <td class='text-center'><b>" . number_format($total_ex_amount, 2) . "</b></td>
                                       <td class='text-center'><b>" . number_format($total_vat_amount, 2) . "</b></td>"
                                            . "<td class='text-center'><b>" . number_format($total_amount, 2) . "</b></td></td>
                                    </tr>

                                    </table>";
                                    $html .= $table;

                                    $html .= "<br/><br/><br/><br/>


                                    ";
                                    echo $html;
                                    $pdf_file = make_pdf_file_name("account_report" . (!empty($sp_ids[0]) && count($sp_ids) == 1 ? '_SP' . $sp_ids[0] : '') . "_{$from_date}_{$to_date}.pdf")['path'];
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;";
                                }
                                ?>
                                <a href = '<?= export_excel_report($pdf_file, 'account_report', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
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
            jQuery('.chosen-select').chosen();
        }
        );
    </script>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('sales_report_copy_dir'));
}
