<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_cost_sheet_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('d-m-Y');

    $entry = !empty($getdata['entry']) ? $getdata['entry'] : '';
    $invoice_no = !empty($getdata['invoice_no']) ? $getdata['invoice_no'] : '';
    $cs_id = !empty($getdata['cs_id']) ? $getdata['cs_id'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (!empty($postdata['update'])) {
        $data = ['euro' => $postdata['euro_rate'], 'usd' => $postdata['usd_rate'], 'custom' => $postdata['custom_rate']];
        update_option("cs_coefficients_rate_{$id}", $data);
    }

    $_data = get_option("cost_sheet_data_{$id}");

    $data = $_data[$cs_id];

    $import_declarations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_import_declarations WHERE id='{$id}'");


    $sql = "SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE entry like '%{$entry}%' AND invoice_no='{$invoice_no}'";
    $results = $wpdb->get_results($sql);

    $cs_rate = get_option("cs_coefficients_rate_{$id}",['euro' => 4.6236, 'usd' => 3.693, 'custom' => 0.0499997667]);
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
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">View Cost Sheet</h1>
        <a href="<?= "admin.php?page=cost-sheet&id={$id}&action=cost-sheet-registry" ?>" class="page-title-action">Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">  
                        <div id='page-inner-content' class='postbox'><br/>
                            <div class='inside' style='max-width:100%;margin:auto'>
                                <form method="post">
                                    <table class="form-table">
                                        <tr>
                                            <td>
                                                <label>Euro Rate</label><br/>
                                                <input type="number"  name="euro_rate" step="0.0000000001" value="<?= $cs_rate['euro'] ?? '' ?>">
                                            </td>
                                            <td>
                                                <label>USD Rate</label><br/>
                                                <input type="number"  name="usd_rate" step="0.0000000001" value="<?= $cs_rate['usd'] ?? '' ?>">
                                            </td>
                                            <td>
                                                <label>Custom Column Rate</label><br/>
                                                <input type="number"  name="custom_rate" step="0.0000000001" value="<?= $cs_rate['custom'] ?? '' ?>">
                                            </td>

                                            <td> <label>&nbsp;</label><br/>
                                                <button type="submit"  class="button-primary" name="update" value="update" >Update</button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                                <br/><br/>
                                <?php
                                $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        #tbl-record table tr th{text-align:center;font-size:10px;}
                                        #tbl-record table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                    </style>";

                                $table = "<table id='tbl-record' border=1 cellpadding=3 style='text-align:center;border-collapse:collapse'>";



                                $table .= "<tr>
                                        <td colspan=10 style='text-align:left;font-weight:bold;padding: 10px;'>
                                            Supplier : <strong style='font-size:20px'>{$data['sup_name']}</strong><br/>
                                            Container # " . ($import_declarations->containers_name) . "<br/>
                                            Declaration # {$import_declarations->declaration_no}<br/>
                                            Invoice Date : {$data['invoice_date']}<br/>
                                            Invoice No.: {$data['invoice_no']}<br/>
                                        </td>
                                        <td colspan=2 style='text-align:right;vertical-align:top;font-weight:bold;padding: 10px;'>ENTRY # <strong style='font-size:20px'>{$entry}</strong></td>
                                    </tr>";

                                $table .= "<tr valign=middle class='bg-blue' style='font-size:14px;'>  
                                    <td  rowspan=2 style='text-align:center'><b>Entry #</b></td>
                                    <td  rowspan=2 style='text-align:center'><b>Item Description</b></td>
                                    <td  rowspan=2 style='text-align:center'><b>Qty</b></td>
                                    <td  style='text-align:center'><b>Unit Price<br/>Including<br/>Packing<br/>Charges</b></td>
                                    <td  style='text-align:center'><b>Total Price<br/>Including<br/>Packing<br/>Charges</b></td>
                                    <td  style='text-align:center'><b>Insurance<br/>&<br/>Freight Cost</b></td>
                                    <td  style='text-align:center'><b>Total Landed Cost<br/>(AED)</b></td>
                                    <td  style='text-align:center'><b>Customs<br/>Duty</b></td>
                                    <td  style='text-align:center'><b>TOTAL<br/>(Including<br/>Customs)</b></td>
                                    <td  style='text-align:center'><b>Input VAT</b></td>
                                    <td  style='text-align:center'><b>TOTAL<br/>(Including<br/>Customs +<br/>VAT)</b></td>
                                    <td  style='text-align:center'><b>RSP</b></td>
                                </tr>";

                                $table .= "<tr valign=middle class='bg-blue' style='font-weight:bold'>
                                    <td  style='text-align:center'><b>{$data['currency']}</b></td> 
                                    <td  style='text-align:center'><b>{$data['currency']}</b></td>
                                    <td  style='text-align:center'><b>(AED)</b></td>
                                    <td  style='text-align:center'><b>Exchnage Rate</b></td>
                                    <td  style='text-align:center'><b>(AED)</b></td>
                                    <td  style='text-align:center'><b>(AED)</b></td>
                                    <td  style='text-align:center'><b>(AED)</b></td>
                                    <td  style='text-align:center'><b>(AED)</b></td>
                                    <td  style='text-align:center'><b>(AED)</b></td>
                                </tr>";
                                $table .= "<tr valign=middle class='bg-blue' style='font-weight:bold'>
                                    <td  colspan='6'></td> 
                                    <td  style='text-align:center'><b>" . ($data['currency'] == 'EURO' ? $cs_rate['euro'] : $cs_rate['usd']) . "</b></td>
                                    <td  colspan='5'></td>
                                </tr>";



                                $table .= "<tbody>";
                                $i = 1;

                                $total_quantity = $total_unit_price = $total_total_price = $total_landed_code = 0;
                                $total_custom_duty = $total_inc_custom = $total_input_vat = $total_inc_custom_vat = $total_rsp = 0;
                                // Offloading 
                                foreach ($results as $value) {
                                    $unit_price = $value->cl_value;
                                    $total_quantity += $value->quantity;
                                    $total_unit_price += $unit_price;
                                    $total_total_price += $total_price = $value->quantity * $unit_price;
                                    $total_landed_code += $landed_cost = $total_price * ($value->currency == 'EURO' ? $cs_rate['euro'] : $cs_rate['usd']);
                                    $total_custom_duty += $custom_duty = $landed_cost * $cs_rate['custom'];
                                    $total_inc_custom += $inc_custom = $landed_cost + $custom_duty;
                                    $total_input_vat += $input_vat = get_vat_amount($inc_custom);
                                    $total_inc_custom_vat += $inc_custom_vat = $inc_custom + $input_vat;
                                    $total_rsp += $rsp = 0;

                                    $table .= "<tr>
                                            <td style='text-align:center'>{$value->entry}</td>
                                            <td style='text-align:left'>" . nl2br($value->item_desc) . "</td>
                                            <td style='text-align:center'>{$value->quantity}</td>
                                            <td style='text-align:center'>" . number_format($unit_price, 2) . "</td>
                                            <td style='text-align:center'>" . number_format($total_price, 2) . "</td>
                                            <td style='text-align:center'>-</td>
                                            <td style='text-align:center'>" . number_format($landed_cost, 2) . "</td>
                                            <td style='text-align:center'>" . number_format($custom_duty, 2) . "</td>
                                            <td style='text-align:center'>" . number_format($inc_custom, 2) . "</td>
                                            <td style='text-align:center'>" . number_format($input_vat, 2) . "</td>
                                            <td style='text-align:center'>" . number_format($inc_custom_vat, 2) . "</td>
                                            <td style='text-align:center'>" . (rb_float($rsp) ? number_format($rsp, 2) : '-') . "</td>
                                        </tr>";
                                    $i++;
                                }

                                $table .= "<tr style='font-weight:bold'>
                                            <td style='text-align:center'></td>
                                            <td style='text-align:left'></td>
                                            <td style='text-align:center'><b>{$total_quantity}</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_unit_price, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_total_price, 2) . "</b></td>
                                            <td style='text-align:center'>-</td>
                                            <td style='text-align:center'><b>" . number_format($total_landed_code, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_custom_duty, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_inc_custom, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_input_vat, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_inc_custom_vat, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . (rb_float($total_rsp) ? number_format($total_rsp, 2) : '-') . "</td>
                                           
                                        </tr>";

                                $table .= "<tr>
                                            <td style='text-align:center' colspan=4><strong>TOTAL AS INVOICE AMOUNT</strong></td>
                                            <td style='text-align:center'><strong>" . ($data['currency'] == 'EURO' ? '€&nbsp;' : '$&nbsp;') . number_format($total_total_price, 2) . "</strong></td>
                                            <td colspan=7></td>                                           
                                        </tr>";


                                $table .= "</tbody>";
                                $table .= "</table>";
                                $html .= $table;
                                echo $html .= "";
                                $pdf_file = make_pdf_file_name("COST_SHEET_VIEW_{$entry}.pdf")['path'];
                                ?>
                            </div>
                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <a href="<?= "admin.php?page=cost-sheet&id={$id}&action=cost-sheet-registry" ?>" class="btn btn-secondary btn-sm">Back</a>
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                }
                                ?>
                                <a href = '<?= export_excel_report($pdf_file, 'daily_operational_report', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
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
            jQuery('.chosen-select').chosen();
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file, null, 1);

    pdf_copy($pdf_file, get_option('single_cost_sheet_copy_dir'));
}
