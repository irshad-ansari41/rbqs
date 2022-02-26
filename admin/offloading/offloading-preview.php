<?php

function admin_ctm_offloading_preview_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('d-m-Y');
    $page = !empty($getdata['page']) ? $getdata['page'] : '';

    if (!empty($postdata['offloading_list_status'])) {
        foreach ($postdata['ol'] as $value) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET ol_list_status=1 where id='{$value['po_id']}'");
        }
        $msg = 'Offloading List Status updated successfully.';
    }

    $containers_name = !empty($getdata['containers_name']) ? array_filter($getdata['containers_name']) : [];

    $containers = $wpdb->get_results("SELECT container_name FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name!='' GROUP BY container_name");

    $po_items = [];
    if (!empty($containers_name)) {
        $sql = "SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE container_name IN ('" . implode("', '", $containers_name) . "') ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) ASC";
        $resutls = $wpdb->get_results($sql);
        foreach ($resutls as $value) {
            $po_items[$value->sup_code][] = $value;
        }
    }
    ?>
    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
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
                        <?php if (!empty($msg)) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>Success!</strong> <?= $msg ?>
                            </div>
                        <?php } ?>

                        <form method="get">
                            <input type="hidden" name="page" value="<?= $page ?>" />
                            <table class="form-table">
                                <tr>
                                    <td style="vertical-align: top;text-align: left">
                                        <label>Select Containers</label>
                                        <select name="containers_name[]" required  class="chosen-select" multiple='true'>
                                            <option value="">Select Containers</option>
                                            <?php
                                            foreach ($containers as $value) {
                                                $selected = in_array($value->container_name, $containers_name) ? 'selected' : '';
                                                echo "<option value='{$value->container_name}' $selected>{$value->container_name}</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td style="vertical-align: top">
                                        <label>&nbsp;</label><br/>
                                        <input type="submit" name="update" value="Filter" class="btn btn-primary btn-sm" />
                                    </td>
                                    <td style="vertical-align: top">
                                        <label>&nbsp;</label><br/>
                                        <a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <br/>
                        <form method=post>
                            <div id='page-inner-content' class='postbox'><br/>
                                <div class='inside' style='max-width:100%;margin:auto'>
                                    <?php
                                    $html = "<style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{text-align:center;font-size:10px;}
                                        table tr td{font-size:10px;text-align:center;}
                                        table{width:100%;}
                                        table#confirm-order-items {table-layout: initial!important;}
                                        #confirm-order-items tr th{white-space: nowrap;}
                                    </style>
                                    ";

                                    $arrival_date = !empty($resutls[0]->arrival_date) ? rb_date($resutls[0]->arrival_date) : '';
                                    $table = "<table id='confirm-order-items' class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                                    <tr valign=middle>
                                                        <td colspan=12 style='text-align:center'>
                                                        <h1 style='text-align:center;font-size:18px;margin:0;padding:0;font-weight: bold; color:red'>
                                                        Container Offloading List ETA {$arrival_date}<br/>
                                                            " . implode('<br/>', $containers_name) . "
                                                            </h1>
                                                            </td>
                                                    </tr>
                                                   
                                                    <tr valign=middle>
                                                        <th>SL NO</th>
                                                        <th>SUPPLIER</th>
                                                        <th>SUPPLIER<br/>CODE</th>
                                                        <th>ENTRY</th>
                                                        <th>LIST OF GOOD - ITEM DESCRIPTION</th>
                                                        <th>IMAGE</th>
                                                        <th>QTY</th>
                                                        <th>PKG</th>
                                                        <th>CQUE</th>
                                                        <th>Quotation<br/>Status</th>
                                                        <th>Contact Number</th>
                                                        <th>NOTES</th>
                                                    </tr>";
                                    ?>
                                    <?php
                                    /**/

                                    $j = 1;
                                    $pkgs = 0;
                                    foreach ($po_items as $items) {
                                        $once = true;
                                        foreach ($items as $value) {
                                            $count = count($items);
                                            $client = get_client($value->client_id);
                                            $supplier_name = get_supplier($value->sup_code, 'name');
                                            $qtn = get_revised_no($value->quotation_id);
                                            $item = get_item($value->item_id);

                                            $table .= "<tr>";
                                            $table .= "<td>$j <input type=hidden name=ol[{$value->id}][po_id] value='{$value->id}' /></td>";
                                            $table .= $once ? "<td rowspan='{$count}'>$supplier_name</td>" : '';
                                            $table .= $once ? "<td rowspan='{$count}'>$value->sup_code</td>" : '';

                                            $table .= "<td>" . make_entry_bold($value->entry) . "</td>"
                                                    . "<td style='text-align:left'>" . nl2br($value->item_desc) . "</td>"
                                                    . "<td style='text-align:left'><a href='" . get_image_src($item->image) . "' target='_image'><img src='" . get_image_src($item->image) . "' width=100  style='margin: auto;width: 100px; '></a></td>"
                                                    . "<td>$value->quantity</td>";
                                            $pkgs +=  $value->cl_pkgs;

                                            $table .= $once ? "<td rowspan='{$count}'>$value->cl_pkgs</td>" : '';
                                           


                                            $table .= "
                                                <td>" . (!empty($client->name) ? $client->name : '') . ' ' . $qtn . "</td>
                                                <td>$value->ol_status</td>
                                                <td>" . (!empty($client->phone) ? ' ' . $client->phone : '') . '<br/>' . (!empty($client->phone2) ? ' ' . $client->phone2 : '') . "</td>
                                                <td>$value->ol_note</td>
                                                
                                            </tr>";
                                            $once = false;
                                            $j++;
                                        }
                                    }
                                    /**/

                                    $table .= "<tr><td colspan=7></td><td><b>{$pkgs}&nbsp;PKGS</b></td><td colspan=4></td></tr>";
                                    $table .= "</table>";
                                    $html .= $table;
                                    $html .= "";
                                    echo $html;
                                    $pdf_file = make_pdf_file_name("offloading_list_$date.pdf")['path'];
                                    ?>
                                </div>
                            </div>
                            <div class="row btn-bottom">
                                <div class="col-sm-4 text-left">
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                                    }
                                    ?>
                                    <br/><br/>
                                </div>
                                <div class="col-sm-2 text-left">
                                    <a href = '<?= export_excel_report($pdf_file, 'offloading_list', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                    <br/><br/>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <input type="submit" name="offloading_list_status" value="Update Offloading List Status" class="btn btn-warning btn-sm text-white" />
                                    <br/><br/>
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
            jQuery('.chosen-select').chosen();
        });
    </script>
    <?php
    $roles = rb_get_current_user_roles();
    if (in_array('accounts', $roles) || in_array('logistics', $roles)) {
        ?>
        <script>
            jQuery(document).ready(() => {
                jQuery("input,select").prop("disabled", true);
            });
        </script>
        <?php
    }
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('offloading_copy_dir'));
}
