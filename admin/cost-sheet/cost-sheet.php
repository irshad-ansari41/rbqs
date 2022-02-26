<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_cost_sheet_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    $import_declarations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_import_declarations WHERE id='{$id}'");
    $containers_name = !empty($import_declarations) ? array_filter(explode(', ', $import_declarations->containers_name)) : [];

    $po_items = [];
    if (!empty($containers_name)) {
        $sql = "SELECT entry,sup_code,invoice_no,invoice_date,invoice_amount,currency,cl_value FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name IN ('" . implode("', '", $containers_name) . "') GROUP BY invoice_no ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) ASC";
        $rs2 = $wpdb->get_results($sql);
        foreach ($rs2 as $value) {
            $entry = strstr($value->entry, '/', true);
            if (rb_float($value->cl_value)) {
                $po_items[$entry ? $entry : $value->entry] = $value;
            }
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
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span> 
        <h1 class="wp-heading-inline">Cost Sheet Registry</h1>
        <a href="<?= 'admin.php?page=import-declaration' ?>" class="page-title-action">Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">  
                        
                      
                        <div id='page-inner-content' class='postbox'><br/>
                            <div class='inside' style='max-width:100%;margin:auto'>

                                <?php
                                $name = '<span style="color:red">' . ($containers_name ? '<br/>' . implode('<br/>', $containers_name) . '<br/>' : '""') . '</span>';
                                $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        #tbl-record table tr th{text-align:center;font-size:10px;}
                                        #tbl-record table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                    </style>";

                                $table = "<table id='tbl-record' border=1 cellpadding=3 style='text-align:center;border-collapse:collapse'>";

                                $table .= "<tr>
                                    <td colspan=6 style='text-align:center'>
                                        <strong style='color:red;font-size:16px;'>{$name}</strong><br/>
                                    </td>
                                </tr>";

                                $table .= "<tr valign=middle class='bg-blue'>
                                    <td  style='text-align:center'>Sr.<br/>No.</td>
                                    <td  style='text-align:center'>E #</td>
                                    <td  style='text-align:center'>Supplier</td>
                                    <td  style='text-align:center'>Invoice No.</td>
                                    <td  style='text-align:center'>Date</td>
                                    <td  style='text-align:center'>Action</td>
                                </tr>";



                                $table .= "<tbody>";
                                $i = 1;

                                $data = [];
                                // Offloading 
                                foreach ($po_items as $key => $value) {
                                    $supplier = get_supplier($value->sup_code);
                                    $cs_id = "{$key}-" . str_replace([' ', '/'], ['-', '-'], $value->invoice_no);
                                    $data[$cs_id] = ['entry' => $key, 'sup_name' => $supplier->name, 'invoice_no' => $value->invoice_no, 'invoice_date' => rb_date($value->invoice_date), 'invoice_amount' => $value->invoice_amount, 'currency' => $value->currency,];

                                    $table .= "<tr>
                                            <td style='text-align:center'>" . $i . "</td>
                                            <td style='text-align:center'>{$key}</td>
                                            <td style='text-align:left'>{$supplier->name}</td>
                                            <td style='text-align:center'>{$value->invoice_no}</td>
                                            <td style='text-align:center'>{$value->invoice_date}</td>
                                            <td style='text-align:center'>
                                                <a href=" . admin_url("admin.php?page=cost-sheet-view") . "&id={$id}&cs_id={$cs_id}&entry={$key}&invoice_no=" . urlencode($value->invoice_no) . " target='_blank'>View</a>
                                            </td>
                                        </tr>";
                                    $i++;
                                }

                                update_option("cost_sheet_data_{$id}", $data);

                                $table .= "</tbody>";
                                $table .= "</table>";
                                $html .= $table;
                                echo $html .= "";
                                $pdf_file = make_pdf_file_name("COST_SHEET_{$id}.pdf")['path'];
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
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file, null, 1);
    
    pdf_copy($pdf_file, get_option('cost_sheet_copy_dir'));
}
