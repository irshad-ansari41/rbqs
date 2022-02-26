<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_import_declaration_internal_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (!empty($postdata['update_rate'])) {
        $data = ['euro' => $postdata['euro_rate'], 'usd' => $postdata['usd_rate'], 'custom' => $postdata['custom_rate']];
        update_option("coefficients_rate_{$id}", $data);
    }

    $import_declarations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_import_declarations WHERE id='{$id}'");
    $containers_name = !empty($import_declarations) ? array_filter(explode(', ', $import_declarations->containers_name)) : [];

    $offloading = [];
    if (!empty($containers_name)) {
        $sql = "SELECT id,entry,sup_code,currency,invoice_no,invoice_amount,invoice_date,container_name,arrival_date,cl_value"
                . " FROM {$wpdb->prefix}ctm_quotation_po_meta "
                . "WHERE container_name IN ('" . implode("', '", $containers_name) . "') "
                . "GROUP BY invoice_no "
                . "ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) ASC, container_name ASC ";
        $rs2 = $wpdb->get_results($sql);
        foreach ($rs2 as $value) {
            $entry = strstr($value->entry, '/', true);
            if (rb_float($value->cl_value)) {
                $offloading[$value->container_name][$entry ? $entry : $value->entry] = $value;
            }
        }
    }

    $arrival_date = !empty($value->arrival_date) ? $value->arrival_date : '';

    if (!empty($postdata['update_import'])) {
        update_option("update_import_{$arrival_date}", $postdata['int_imp_dec']);
    }

    $int_imp_dec = get_option("update_import_{$arrival_date}");

    $impd_rate = get_option("coefficients_rate_{$id}", ['euro' => 4.6236, 'usd' => 3.693, 'custom' => 0.0499997667]);
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
        <h1 class="wp-heading-inline">Internal Import Declaration</h1>
        <a href="<?= 'admin.php?page=import-declaration' ?>" class="page-title-action">Back</a>
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
                                                <input type="number"  name="euro_rate" step="0.0000000001" value="<?= $impd_rate['euro'] ?? '' ?>">
                                            </td>
                                            <td>
                                                <label>USD Rate</label><br/>
                                                <input type="number"  name="usd_rate" step="0.0000000001" value="<?= $impd_rate['usd'] ?? '' ?>">
                                            </td>
                                            <td>
                                                <label>Custom Column Rate</label><br/>
                                                <input type="number"  name="custom_rate" step="0.0000000001" value="<?= $impd_rate['custom'] ?? '' ?>">
                                            </td>
                                            <td> <label>&nbsp;</label><br/>
                                                <button type="submit"  class="button-primary" name="update_rate" value="update" >Update</button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>

                        <form method="post">
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:100%;margin:auto'>

                                    <?php
                                    $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{text-align:center;font-size:10px;}
                                        table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                        .bg1{background:#ECF0DF}
                                        .bg2{background:#DEEDF3}
                                        .bg3{background:#FAEADB}
                                        .bg4{background:#E2E0EB}
                                        .bg5{background:#EFDDDB}
                                        .bg6{background:#BBCCE1}
                                    </style>";


                                    $table = "<table border=1 cellpadding=3 style='text-align:center;border-collapse:collapse'>";

                                    $table_body = "<tr valign=middle>
                                    <td rowspan=2 style='text-align:center'>Sr.<br/>No.</td>
                                    <td rowspan=2 style='text-align:center'>E #</td>
                                    <td rowspan=2 style='text-align:center'>Supplier</td>
                                    <td rowspan=2 style='text-align:center'>Invoice No.</td>
                                    <td rowspan=2 style='text-align:center;width:100px'>Date</td>
                                    <td colspan=2 style='text-align:center'>Value</td>
                                    <td rowspan=2 v>H.S. Code</td>
                                    <td rowspan=2 style='text-align:center'>Country <br/>of Origin</td>
                                    <td rowspan=2 style='text-align:center'>Certificate<br/>of Origin</td>
                                    <td rowspan=2 style='text-align:center'>No. of <br/> Packages</td>
                                    <td rowspan=2 style='text-align:center'>CBM</td>
                                    <td rowspan=2 style='text-align:center'>Weight</td>
                                    <td rowspan=2 style='text-align:center' class='bg1'>AED</td>
                                    <td colspan=2 style='text-align:center' class='bg2'>Freight & insurance	</td>
                                    <td colspan=2 style='text-align:center' class='bg3'>Customs</td>
                                    <td colspan=2 style='text-align:center' class='bg4'>VAT</td>
                                    <td style='text-align:center' class='bg5'>Reverse Charge</td>
                                    <td rowspan=2 style='text-align:center'></td>
                                    <td colspan=3 style='text-align:center' class='bg6'>BOOKED IN TALLY</td>
                                </tr>
                                
                                <tr>
                                    <td style='text-align:center'>EURO(€)</td>
                                    <td style='text-align:center'>USD($)</td>
                                    <td style='text-align:center' class='bg2'>EURO(€)</td>
                                    <td style='text-align:center' class='bg2'>USD($)</td>
                                    <td style='text-align:center' class='bg3'>EURO(€)</td>
                                    <td style='text-align:center' class='bg3'>USD($)</td>
                                    <td style='text-align:center' class='bg4'>EURO(€)</td>
                                    <td style='text-align:center' class='bg4'>USD($)</td>
                                    <td style='text-align:center' class='bg5'>Tally</td>
                                    <td style='text-align:center' class='bg6'>IMPORTS TAXABLE</td>
                                    <td style='text-align:center' class='bg6'>VAT</td>
                                    <td style='text-align:center' class='bg6'>Difference</td>
                                </tr>";



                                    $table .= "<tbody>";
                                    $container = '';
                                    $total_cl_pkgs = $total_cl_cbm = $total_cl_kg = $euro = $doller = $total_aed = 0;
                                    $i = 1;
                                    $x = 1;
                                    $y = 0;
                                    $total_fre_ins_euro = $total_fre_ins_usd = $total_custom_eur = $total_custom_usd = 0;
                                    $total_tally = $total_vat_eur = $total_vat_usd = $total_tally_imp_tax = 0;
                                    $total_tally_vat = $total_tally_vat_diff = 0;
                                    // Offloading 
                                    foreach ($offloading as $cn_name => $items) {
                                        $cl_pkgs = $cl_cbm = $cl_kg = 0;
                                        foreach ($items as $key => $value) {

                                            $row = get_first_entry_details($value->entry, ['id', 'cl_pkgs', 'cl_cbm', 'cl_kg', 'invoice_amount']);
                                            $cl_pkgs += $row->cl_pkgs;
                                            $cl_cbm += $row->cl_cbm;
                                            $cl_kg += $row->cl_kg;

                                            $euro += ($value->currency == 'EURO' ? $row->invoice_amount : 0);
                                            $doller += ($value->currency == 'USD' ? $row->invoice_amount : 0);
                                            //$containers[$value->container_name]['name'] = $value->container_name;

                                            $item_ids = $wpdb->get_var("SELECT group_concat(item_id) FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE entry like '%$key%'");
                                            $hs = $wpdb->get_var("SELECT group_concat(distinct hs_code) FROM {$wpdb->prefix}ctm_items WHERE id IN ($item_ids) AND hs_code !=''");

                                            $supplier = get_supplier($value->sup_code);

                                            $aed = $row->invoice_amount * ($value->currency == 'EURO' ? $impd_rate['euro'] : $impd_rate['usd']);
                                            $total_aed += $aed;

                                            $fre_ins_euro = $int_imp_dec[$key]['freight_euro'] ?? 0;
                                            $fre_ins_usd = $int_imp_dec[$key]['freight_usd'] ?? 0;
                                            $total_fre_ins_euro += $fre_ins_euro;
                                            $total_fre_ins_usd += $fre_ins_usd;

                                            $custom_eur = $value->currency == 'EURO' ? $aed * $impd_rate['custom'] : 0;
                                            $custom_usd = $value->currency == 'USD' ? $aed * $impd_rate['custom'] : 0;
                                            $total_custom_eur += $custom_eur;
                                            $total_custom_usd += $custom_usd;

                                            $vat_eur = $value->currency == 'EURO' ? get_vat_amount($aed + $custom_eur) : 0;
                                            $vat_usd = $value->currency == 'USD' ? get_vat_amount($aed + $custom_usd) : 0;
                                            $total_vat_eur += $vat_eur;
                                            $total_vat_usd += $vat_usd;

                                            $tally = $aed + $custom_eur + $custom_usd + $fre_ins_euro + $fre_ins_usd;
                                            $total_tally += $tally;

                                            $tally_imp_tax = $int_imp_dec[$key]['tally_imp'] ?? 0;
                                            $total_tally_imp_tax += $tally_imp_tax;

                                            $tally_vat = $int_imp_dec[$key]['tally_vat'] ?? 0;
                                            $total_tally_vat += $tally_vat;

                                            $tally_vat_diff = ($vat_eur + $vat_usd) - $tally_vat;
                                            $total_tally_vat_diff += $tally_vat_diff;


                                            $tr_html = "
                                            <td style='text-align:center'>" . $key . "</td>
                                            <td style='text-align:center'>{$supplier->name}</td>
                                            <td style='text-align:right'>{$value->invoice_no}</td>
                                            <td style='text-align:right'>" . rb_date($value->invoice_date, 'd.m.y') . "</td>
                                            <td style='text-align:center'>" . ($value->currency == 'EURO' ? '€&nbsp;' . number_format($row->invoice_amount, 2) : '') . "</td>
                                            <td style='text-align:center'>" . ($value->currency == 'USD' ? '$&nbsp;' . number_format($row->invoice_amount, 2) : '') . "</td>
                                            <td style='text-align:center'>" . (str_replace(',', ', ', $hs)) . "</td>
                                            <td style='text-align:center'>{$supplier->country_origin}</td>
                                            <td style='text-align:center'><b>X</b></td>
                                            <td style='text-align:center'>{$row->cl_pkgs}</td>
                                            <td style='text-align:right'>" . number_format($row->cl_cbm, 1) . "</td>
                                            <td style='text-align:right'>{$row->cl_kg}</td>
                                            <td style='text-align:center' class='bg1'>" . number_format($aed, 2) . "</td>";

                                            if (empty($getdata['pdf'])) {
                                                $tr_html .= "<td style='text-align:center' class='bg6'>"
                                                        . "<input type='number' name='int_imp_dec[$key][freight_euro]' style='width:75px' step='0.01' value='{$fre_ins_euro}' ></td>
                                            <td style='text-align:center' class='bg6'>
                                            <input type='number' name='int_imp_dec[$key][freight_usd]' style='width:75px' step='0.01' value='{$fre_ins_usd}'></td>";
                                            } else {
                                                $tr_html .= "<td style='text-align:center' class='bg2'>" . number_format($fre_ins_euro, 2) . "</td>
                                            <td style='text-align:center' class='bg2'>" . number_format($fre_ins_usd, 2) . "</td>";
                                            }

                                            $tr_html .= "<td style='text-align:center' class='bg3'>" . number_format($custom_eur, 2) . "</td>
                                            <td style='text-align:center' class='bg3'>" . number_format($custom_usd, 2) . "</td>
                                            <td style='text-align:center' class='bg4'>" . number_format($vat_eur, 2) . "</td>
                                            <td style='text-align:center' class='bg4'>" . number_format($vat_usd, 2) . "</td>
                                            <td style='text-align:center' class='bg5'>" . number_format($tally, 2) . "</td>
                                            <td style='text-align:center'>-</td>";

                                            if (empty($getdata['pdf'])) {
                                                $tr_html .= "<td style='text-align:center' class='bg6'>
                                                <input type='number' name='int_imp_dec[$key][tally_imp]' style='width:75px' step='0.01' value='{$tally_imp_tax}' ></td>
                                            <td style='text-align:center' class='bg6'>
                                            <input type='number' name='int_imp_dec[$key][tally_vat]' style='width:75px' step='0.01' value='{$tally_vat}'></td>";
                                            } else {
                                                $tr_html .= "<td style='text-align:center' class='bg6'>" . number_format($tally_imp_tax, 2) . "</td>
                                            <td style='text-align:center' class='bg6'>" . number_format($tally_vat, 2) . "</td>";
                                            }

                                            $tr_html .= "<td style='text-align:center' class='bg6'>" . number_format($tally_vat_diff, 2) . "</td>
                                        ";

                                            $tr[$cn_name][$key] = $tr_html;
                                            $i++;
                                        }

                                        $container .= "<strong style='font-size:16px;'>CONTAINER # <span>{$cn_name} (Consists of $cl_pkgs Packages | Gross Weight " . (number_format($cl_kg, 2)) . " | CBM " . (number_format($cl_cbm, 3)) . ") - Ref. SL No.: {$x} to " . ($i - 1) . "</span></strong><br/>";
                                        $x = $i;
                                        $y++;
                                    }

                                    $table_head = "<tr>
                                    <td colspan=25 style='text-align:center'>
                                        <h6><strong style='font-size:20px;'>ROCHE BOBOIS</strong></h6>
                                        <h6><strong style='font-size:20px;text-transform:uppercase'>P.O. Box 286, Al Barsha 1, Sheikh Zayed Road, Dubai - U.A.E</strong></h6>
                                        <strong style='font-size:16px;'>{$y} x 40' HIGH CUBE CONTAINER</strong><br/>
                                        $container
                                        <strong style='font-size:16px;text-transform:uppercase;'>From " . ($import_declarations->from_location ?? '<span style="color:red">XXXXX</span>') . " to Jebel Ali Dubai, Containing the following goods - ETA to Dubai - " . rb_date($arrival_date, 'd.m.Y') . " </strong><br/>
                                    </td>
                                </tr>";

                                    $table .= $table_head;

                                    ksort($tr);
                                    $j = 1;
                                    $tr_html = '';
                                    foreach ($tr as $v1) {
                                        ksort($v1);
                                        foreach ($v1 as $v) {
                                            $tr_html .= "<tr><td style='text-align:center'>$j</td>$v</tr>";
                                            $j++;
                                        }
                                    }
                                    $table .= $table_body . $tr_html;

                                    $table .= "<tr style='font-weight:bold'>
                                            <td colspan=5 style='text-align:center'><b>Total</b></td>
                                            <td style='text-align:center'><b>€&nbsp;" . number_format($euro, 2) . "</b></td>
                                            <td style='text-align:center'><b>$&nbsp;" . number_format($doller, 2) . "</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style='text-align:center'><b>" . number_format($total_cl_pkgs, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_cl_cbm, 2) . "</b></td>
                                            <td style='text-align:center'><b>" . number_format($total_cl_kg, 2) . "</b></td>
                                            <td style='text-align:center' class='bg1'><b>" . number_format($total_aed, 2) . "</b></td>
                                            <td style='text-align:center' class='bg2'>" . number_format($total_fre_ins_euro, 2) . "</td>
                                            <td style='text-align:center' class='bg2'>" . number_format($total_fre_ins_usd, 2) . "</td>
                                            <td style='text-align:center' class='bg3'><b>" . number_format($total_custom_eur, 2) . "</b></td>
                                            <td style='text-align:center' class='bg3'><b>" . number_format($total_custom_usd, 2) . "</b></td>
                                            <td style='text-align:center' class='bg4'><b>" . number_format($total_vat_eur, 2) . "</b></td>
                                            <td style='text-align:center' class='bg4'><b>" . number_format($total_vat_usd, 2) . "</b></td>
                                            <td style='text-align:center' class='bg5'><b>" . number_format($total_tally, 2) . "</b></td>
                                            <td style='text-align:center'>-</td>
                                            <td style='text-align:center' class='bg6'><b>" . number_format($total_tally_imp_tax, 2) . "</b></td>
                                            <td style='text-align:center' class='bg6'><b>" . number_format($total_tally_vat, 2) . "</b></td>
                                            <td style='text-align:center' class='bg6'><b>" . number_format($total_tally_vat_diff, 2) . "</b></td>
                                        </tr>";

                                    $table .= "</tbody>";
                                    $table .= "</table>";
                                    $html .= $table;
                                    echo $html .= "";
                                    $pdf_file = make_pdf_file_name("IMPORT_DECLARATION_" . rb_date($date) . ".pdf")['path'];
                                    ?>
                                </div>
                            </div>

                            <div class="row btn-bottom">
                                <div class="col-sm-6 text-left">
                                    <a href="<?= 'admin.php?page=import-declaration' ?>" class="btn btn-secondary btn-sm">Back</a>
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
                                <div class="col-sm-6 text-right">
                                    <button type="submit" name="update_import" value="update" class="btn btn-primary btn-sm" >Update Import Declaration</button>
                                </div>
                            </div>
                        </form>
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
            jQuery('.note').on('blur', function () {
                var note_id = jQuery(this).data('note_id');
                var note = jQuery(this).val();

                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/ds-note.php",
                    type: "post",
                    dataType: "json",
                    data: {note_id: note_id, note: note},
                    success: function (response) {
                        if (response.status) {

                        }
                    }
                });
            });
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file, null, 1);

    pdf_copy($pdf_file, get_option('int_imp_decl_copy_dir'));
}
