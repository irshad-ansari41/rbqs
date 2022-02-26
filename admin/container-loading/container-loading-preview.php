<?php

function admin_ctm_container_loading_preview_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    if (!empty($postdata['update'])) {
        update_option('containers_name', $postdata['container']);
    }

    if (!empty($postdata['send_to_offloading'])) {
        foreach ($postdata['cl'] as $entry => $value) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET order_registry='TRANSIT', cl_status=1,container_name='{$value}' where entry like '%{$entry}%' AND cl_status=0");
        }
        $msg = 'Container Loading List sent to Offloading List successfully.';
    }

    $rs1 = $wpdb->get_results("SELECT id,entry,item_desc,sup_code,container_name,po_id,invoice_no,invoice_date,delivery_date FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE add_in_list=1 AND cl_status=0 AND cl_priority='High' AND entry!='' ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) ASC");
    $high_resutls = [];
    foreach ($rs1 as $value) {
        $entry = strstr($value->entry, '/', true);
        $high_resutls[$entry ? $entry : $value->entry][] = $value;
    }

    $rs2 = $wpdb->get_results("SELECT id,entry,item_desc,sup_code,container_name,po_id,invoice_no,invoice_date,delivery_date FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE add_in_list=1 AND cl_status=0 AND cl_priority='Less' AND entry!='' ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) ASC");
    $less_resutls = [];
    foreach ($rs2 as $value) {
        $entry = strstr($value->entry, '/', true);
        $less_resutls[$entry ? $entry : $value->entry][] = $value;
    }

    $containers = array_filter(get_option('containers_name', [])) ?? [];
    ?>

    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:14px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">Preview Container Loading List</h1>
        <a href="admin.php?page=container-loading" class="page-title-action">Back</a>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <?php if (!empty($msg)) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>Success!</strong> <?= $msg ?>
                            </div>
                        <?php } ?>
                        <div id='page-inner-content' class='postbox'><br/>
                            <form method=post>
                                <strong>&nbsp;Containers Name</strong>
                                <table style="width:100%" class="form-table">
                                    <tr>
                                        <td><input type="text" name="container[1]" placeholder="Name" value="<?= $containers[1] ?? '' ?>"/></td>
                                        <td><input type="text" name="container[2]" placeholder="Name" value="<?= $containers[2] ?? '' ?>"/></td>
                                        <td><input type="text" name="container[3]" placeholder="Name" value="<?= $containers[3] ?? '' ?>"/></td>
                                        <td><input type="text" name="container[4]" placeholder="Name" value="<?= $containers[4] ?? '' ?>"/></td>

                                    </tr>
                                    <tr>
                                        <td><input type="text" name="container[5]" placeholder="Name" value="<?= $containers[5] ?? '' ?>"/></td>
                                        <td><input type="text" name="container[6]" placeholder="Name" value="<?= $containers[6] ?? '' ?>"/></td>
                                        <td><input type="text" name="container[7]" placeholder="Name" value="<?= $containers[7] ?? '' ?>"/></td>
                                        <td><input type="submit" name="update" value="Add & Update Container Name" class="btn btn-warning btn-sm " /></td>
                                    </tr>
                                </table>
                            </form>
                        </div>

                        <form method=post>
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:100%;margin:auto'>

                                    <?php
                                    $name = '<span style="color:red">' . ($containers ? '<br/>"' . implode('<br/>', $containers) . '"<br/>' : '""') . '</span>';
                                    $html = "<style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table#confirm-order-items-1 tr th,table#confirm-order-items-1 tr td{font-size:14px;text-align:center}
                                        table#confirm-order-items-2 tr th,table#confirm-order-items-2 tr td{font-size:14px;text-align:center}
                                        table{width:100%;}
                                    </style>
                                    ";

                                    $html .= "<table id='confirm-order-items-1' style='width:100%;border-collapse:collapse' border=1 cellpadding=3 cellspacing=3 >
                                                    <tr valign=middle>
                                                        <th colspan=" . (empty($getdata['pdf']) ? 13 : 12) . " style='text-align:center'>
                                                        <h1 style='text-align:center;font-size:18px;margin:0;padding:0;font-weight: bold; color:blue'>CONTAINER LOADING REQUEST " . date('d-M-Y') . "</h1>
                                                            </th>
                                                    </tr>
                                                    <tr valign=middle>
                                                        <th colspan=" . (empty($getdata['pdf']) ? 13 : 12) . " style='text-align:center'>ROCHE BOBOIS - LIST OF GOODS TO BE LOADED INTO $name CONTAINER SHIPMENT TO DUBAI</th>
                                                    </tr>
                                                    <tr valign=middle>";
                                    $html .= empty($getdata['pdf']) ? "<th>#</th>" : '';
                                    $html .= "<th>ENTRY</th>
                                                        <th>SPLIT ENTRY</th>
                                                        <th>Item Description</th>
                                                        <th>SUPPLIER</th>
                                                        <th>PO</th>
                                                        <th>Invoice #</th>
                                                        <th>Document<br/>Date</th>
                                                        <th>Delivery to<br/> Francesconi</th>
                                                        <th>VALUE</th>
                                                        <th>PKGS</th>
                                                        <th>CBM</th>
                                                        <th>KG</th>
                                                    </tr>";

                                    $cl_value = $cl_pkgs = $cl_cbm = $cl_kg = 0;

                                    foreach ($high_resutls as $key => $items) {
                                        $once = true;
                                        foreach ($items as $value) {
                                            $supplier_name = get_supplier($value->sup_code, 'name');
                                            $entry = make_entry_bold($value->entry);
                                            $count = count($items);
                                            $html .= "<tr>";
                                            if ($once && empty($getdata['pdf'])) {
                                                $html .= "<td rowspan='{$count}' style='width:150px;text-align:left;'>";
                                                foreach ($containers as $k => $v) {
                                                    $checked = $value->container_name == $v ? 'checked' : '';
                                                    $html .= "<label><input type='radio' name='cl[$key]' value='$v' $checked required >$v</label><br/>";
                                                }
                                                $html .= "</td>";
                                            }

                                            $html .= $once ? "<td rowspan='{$count}'>$key</td>" : '';

                                            $html .= "<td>{$entry}</td>"
                                                    . "<td style='text-align:left'>" . nl2br($value->item_desc) . "</td>";
                                            if ($once) {
                                                $row = get_first_entry_details($value->entry,['id','cl_pkgs','cl_cbm','cl_kg','invoice_amount']);
                                                    
                                                $html .= "
                                                <td rowspan='{$count}'>$supplier_name</td>
                                                <td rowspan='{$count}'>$value->po_id</td>
                                                <td rowspan='{$count}'>$value->invoice_no</td>
                                                <td rowspan='{$count}'>" . rb_date($value->invoice_date) . "</td>
                                                <td rowspan='{$count}'>" . rb_date($value->delivery_date) . "</td>
                                                <td rowspan='{$count}'> " . number_format($row->invoice_amount, 2) . "</td>
                                                <td rowspan='{$count}'>" . number_format($row->cl_pkgs, 2) . "</td>
                                                <td rowspan='{$count}'>" . number_format($row->cl_cbm, 2) . "</td>
                                                <td rowspan='{$count}'>" . number_format($row->cl_kg, 2) . "</td>";

                                                $cl_value += $row->invoice_amount;
                                                $cl_pkgs += $row->cl_pkgs;
                                                $cl_cbm += $row->cl_cbm;
                                                $cl_kg += $row->cl_kg;
                                            }

                                            "</tr>";

                                            $once = false;
                                        }
                                    }

                                    $html .= "<tr>
                                       <td colspan=" . (empty($getdata['pdf']) ? 9 : 8) . "><b style='font-size:14px'>Total</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_value, 2) . "</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_pkgs, 2) . "</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_cbm, 2) . "</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_kg, 2) . "</b></td>
                                    </tr>";

                                    $html .= "</table><br/><br/><br/>";

                                    // Less Priority
                                    if ($less_resutls) {
                                        $html .= "<pagebreak />";
                                        $html .= "<table id='confirm-order-items-2' style='width:100%;border-collapse:collapse' border=1 cellpadding=3 cellspacing=3 >";
                                        $html .= "<tr><th colspan=" . (empty($getdata['pdf']) ? 13 : 12) . " style='text-align:left;font-size:14px;'><b style='font-size:14px'>LESS PRIORITY</b></th></tr>";
                                        $html .= "<tr valign=middle>";
                                        $html .= empty($getdata['pdf']) ? "<th>#</th>" : '';
                                        $html .= "<th>ENTRY</th>
                                                        <th>SPLIT ENTRY</th>
                                                        <th>Item Description</th>
                                                        <th>SUPPLIER</th>
                                                        <th>PO</th>
                                                        <th>Invoice #</th>
                                                        <th>Document<br/>Date</th>
                                                        <th>Delivery to<br/> Francesconi</th>
                                                        <th>VALUE</th>
                                                        <th>PKGS</th>
                                                        <th>CBM</th>
                                                        <th>KG</th>
                                                    </tr>";

                                        $cl_value = $cl_pkgs = $cl_cbm = $cl_kg = 0;

                                        foreach ($less_resutls as $key => $items) {
                                            $once = true;
                                            foreach ($items as $value) {
                                                $supplier_name = get_supplier($value->sup_code, 'name');
                                                $entry = make_entry_bold($value->entry);
                                                $count = count($items);

                                                $html .= "<tr>";
                                                if ($once && empty($getdata['pdf'])) {
                                                    $html .= "<td rowspan='{$count}' style='width:150px;text-align:left;'>";
                                                    foreach ($containers as $k => $v) {
                                                        $checked = $value->container_name == $v ? 'checked' : '';
                                                        $html .= "<label><input type='radio' name='cl[$key]' value='$v' $checked required >$v</label><br/>";
                                                    }
                                                    $html .= "</td>";
                                                }

                                                $html .= $once ? "<td rowspan='{$count}'>$key</td>" : '';

                                                $html .= "<td>{$entry}</td>"
                                                        . "<td style='text-align:left'>" . nl2br($value->item_desc) . "</td>";
                                                if ($once) {
                                                    $row = get_first_entry_details($value->entry,['id','cl_pkgs','cl_cbm','cl_kg','invoice_amount']);

                                                    $html .= "
                                                <td rowspan='{$count}'>$supplier_name</td>
                                                <td rowspan='{$count}'>$value->po_id</td>
                                                <td rowspan='{$count}'>$value->invoice_no</td>
                                                <td rowspan='{$count}'>" . rb_date($value->invoice_date) . "</td>
                                                <td rowspan='{$count}'>" . rb_date($value->delivery_date) . "</td>
                                                <td rowspan='{$count}'> " . number_format($row->invoice_amount, 2) . "</td>
                                                <td rowspan='{$count}'>" . number_format($row->cl_pkgs, 2) . "</td>
                                                <td rowspan='{$count}'>" . number_format($row->cl_cbm, 2) . "</td>
                                                <td rowspan='{$count}'>" . number_format($row->cl_kg, 2) . "</td>";

                                                    $cl_value += $row->invoice_amount;
                                                    $cl_pkgs += $row->cl_pkgs;
                                                    $cl_cbm += $row->cl_cbm;
                                                    $cl_kg += $row->cl_kg;
                                                }

                                                "</tr>";

                                                $once = false;
                                            }
                                        }

                                        $html .= "<tr>
                                         <td colspan=" . (empty($getdata['pdf']) ? 9 : 8) . "><b style='font-size:14px'>Total</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_value, 2) . "</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_pkgs, 2) . "</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_cbm, 2) . "</b></td>
                                        <td><b style='font-size:14px'>" . number_format($cl_kg, 2) . "</b></td>
                                    </tr>";
                                        $html .= "</table>";
                                    }

                                    echo $html;

                                    $pdf_file = make_pdf_file_name("container_loading_list_$date.pdf")['path'];
                                    ?>
                                </div>
                            </div>


                            <div class="row btn-bottom">
                                <div class="col-sm-8">
                                    <a href="admin.php?page=container-loading" class="btn btn-secondary btn-sm" >Back</a>&nbsp;&nbsp;
    <?php
    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
        echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
    } if (pdf_exist($pdf_file)) {
        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
    }
    ?>&nbsp;&nbsp;
                                    <a href = '<?= export_excel_report($pdf_file, 'container_loading_list', $html) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                    <br/><br/>
                                </div>

                                <div class="col-sm-4 text-right">
                                    <input type="submit" name="send_to_offloading" value="Send to Offloading List"  <?= empty($containers) ? 'disabled' : '' ?>
                                           class="btn btn-primary btn-sm" />
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

            map_container(1), map_container(2), map_container(3), map_container(4), map_container(5), map_container(6), map_container(7);

            function map_container(id) {
                jQuery('#container-' + id).on('input', () => {
                    jQuery(`.container_${id} span`).text(jQuery('#container-' + id).val());
                    jQuery(`.container_${id} input`).val(jQuery('#container-' + id).val());
                });
            }


            jQuery('.ol_add_list').click(function () {
                var po_id = jQuery(this).val();
                var status = 0;
                if (jQuery(this).is(':checked')) {
                    status = 1;
                } else {
                    status = 0;
                }
                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/offloading-list.php",
                    type: "post",
                    dataType: "json",
                    data: {po_id: po_id, status: status},
                    success: function (response) {
                        if (response.status) {
                            alert(`Item ${response.status} to offloading list successfully`);
                        }
                    }
                });

            });
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file, null, true);
    pdf_copy($pdf_file, get_option('loading_copy_dir'));
}
