<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_import_declaration_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';


    $import_declarations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_import_declarations WHERE id='{$id}'");
    $containers_name = !empty($import_declarations) ? array_filter(explode(', ', $import_declarations->containers_name)) : [];


    $offloading = [];
    if (!empty($containers_name)) {
        $sql = "SELECT id,entry,sup_code,currency,invoice_no,invoice_amount,invoice_date,container_name,arrival_date,cl_value"
                . " FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name IN ('" . implode("', '", $containers_name) . "') "
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
        <h1 class="wp-heading-inline">Import Declaration</h1>
        <a href="<?= 'admin.php?page=import-declaration' ?>" class="page-title-action">Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">  
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
                                    <td rowspan=2 style='text-align:center'>Sr.&nbsp;No.</td>
                                    <td rowspan=2 style='text-align:center'>E #</td>
                                    <td rowspan=2 style='text-align:left'>Supplier</td>
                                    <td rowspan=2 style='text-align:center'>Invoice No.</td>
                                    <td rowspan=2 style='text-align:center'>Date</td>
                                    <td colspan=2 style='text-align:center'>Value</td>
                                    <td rowspan=2 v>H.S. Code</td>
                                    <td rowspan=2 style='text-align:center'>Country <br/>of Origin</td>
                                    <td rowspan=2 style='text-align:center'>Certificate<br/>of Origin</td>
                                    <td rowspan=2 style='text-align:center'>No. of <br/> Packages</td>
                                    <td rowspan=2 style='text-align:center'>CBM</td>
                                    <td rowspan=2 style='text-align:center'>Weight</td>
                                </tr>";

                                $table_body .= "<tr><td style='text-align:center'>EURO(€)</td>
                                    <td style='text-align:center'>USD($)</td>
                                </tr>";



                                $table .= "<tbody>";
                                $container = '';
                                $total_cl_pkgs = $total_cl_cbm = $total_cl_kg = $euro = $doller = $total_aed = 0;
                                $i = 1;
                                $x = 1;
                                $y = 0;
                                // Offloading 
                                foreach ($offloading as $cn_name => $items) {
                                    $cl_pkgs = $cl_cbm = $cl_kg = 0;
                                    foreach ($items as $key => $value) {
                                        $row = get_first_entry_details($value->entry, ['id', 'cl_pkgs', 'cl_cbm', 'cl_kg', 'invoice_amount']);
                                        $cl_pkgs += $row->cl_pkgs;
                                        $cl_cbm += $row->cl_cbm;
                                        $cl_kg += $row->cl_kg;
                                        $total_cl_pkgs += $row->cl_pkgs;
                                        $total_cl_cbm += $row->cl_cbm;
                                        $total_cl_kg += $row->cl_kg;
                                        $euro += ($value->currency == 'EURO' ? $row->invoice_amount : 0);
                                        $doller += ($value->currency == 'USD' ? $row->invoice_amount : 0);
                                        //$containers[$value->container_name]['name'] = $value->container_name;
                                        $supplier = get_supplier($value->sup_code);
                                        $item_ids = $wpdb->get_var("SELECT group_concat(item_id) FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE entry like '%$key%'");
                                        $hs = $wpdb->get_var("SELECT group_concat( distinct hs_code) FROM {$wpdb->prefix}ctm_items WHERE id IN ($item_ids) AND hs_code !=''");

                                        $tr[$cn_name][$key] = "
                                            
                                            <td style='text-align:center'>" . $key . "</td>
                                            <td style='text-align:left'>{$supplier->name}</td>
                                            <td style='text-align:right'>{$value->invoice_no}</td>
                                            <td style='text-align:right'>" . rb_date($value->invoice_date, 'd.m.y') . "</td>
                                            <td style='text-align:right'>" . ($value->currency == 'EURO' && rb_float($row->invoice_amount) ? '€ ' . number_format($row->invoice_amount, 2) : '') . "</td>
                                            <td style='text-align:right'>" . ($value->currency == 'USD' && rb_float($row->invoice_amount) ? '$ ' . number_format($row->invoice_amount, 2) : '') . "</td>
                                            <td style='text-align:center'>" . (str_replace(',', ', ', $hs)) . "</td>
                                            <td style='text-align:center'>" . get_country($supplier->country_origin, 'name') . "</td>
                                            <td style='text-align:center'><b>X</b></td>
                                            <td style='text-align:center'>{$row->cl_pkgs}</td>
                                            <td style='text-align:right'>" . number_format($row->cl_cbm, 1) . "</td>
                                            <td style='text-align:right'>{$row->cl_kg}</td>
                                        ";
                                        $i++;
                                    }
                                    $container .= "<strong style='font-size:16px;'>CONTAINER # <span style='font-size:16px;'>{$cn_name} (Consists of $cl_pkgs Packages | Gross Weight " . (number_format($cl_kg, 2)) . " | CBM " . (number_format($cl_cbm, 3)) . ") - Ref. SL No.: {$x} to " . ($i - 1) . "</span></strong><br/>";
                                    $x = $i;
                                    $y++;
                                }

                                $table_head = "<tr>
                                    <td colspan=13 style='text-align:center'>
                                        <h6><strong style='font-size:20px;'>ROCHE BOBOIS</strong></h6>
                                        <h6><strong style='font-size:20px;text-transform:uppercase'>P.O. Box 286, Al Barsha 1, Sheikh Zayed Road, Dubai - U.A.E</strong></h6>
                                        <strong style='font-size:16px;'>{$y} x 40' HIGH CUBE CONTAINER</strong><br/>
                                        $container
                                        <strong style='font-size:16px;text-transform:uppercase;'>From " . (!empty($import_declarations->from_location) ? $import_declarations->from_location : '<span style="color:red">XXXXX</span>') . " to Jebel Ali Dubai, Containing the following goods - ETA to Dubai - " . rb_date($arrival_date, 'd.m.Y') . " </strong><br/>
                                    </td>
                                </tr>";

                                $table .= $table_head;

                                
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
                                            <td style='text-align:right'><b>€ " . number_format($euro, 2) . "</b></td>
                                            <td style='text-align:right'><b>$ " . number_format($doller, 2) . "</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style='text-align:center'><b>" . number_format($total_cl_pkgs, 2) . "</b></td>
                                            <td style='text-align:right'><b>" . number_format($total_cl_cbm, 2) . "</b></td>
                                            <td style='text-align:right'><b>" . number_format($total_cl_kg, 2) . "</b></td>
                                        </tr>";

                                $table .= "</tbody>";
                                $table .= "</table>";
                                $html .= $table;
                                echo $html .= "";

                                $pdf_file = $import_declarations->pdf_path;
                                store_pdf_path('ctm_import_declarations', $import_declarations->id, "IMPORT_DECLARATION_" . rb_date($date) . ".pdf", $pdf_file);
                                ?>
                            </div>
                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
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

    pdf_copy($pdf_file, get_option('imp_decl_copy_dir'));
}
