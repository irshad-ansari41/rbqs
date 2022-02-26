<?php

function admin_ctm_client_order_delivery_status_page() {

    global $wpdb, $current_user;

    $getdata = filter_input_array(INPUT_GET);
    $page = !empty($getdata['page']) ? $getdata['page'] : '';
    $qid = !empty($getdata['qid']) ? $getdata['qid'] : '';
    $order_registry = !empty($getdata['order_registry']) ? $getdata['order_registry'] : '';
    $client_id = !empty($getdata['client_id']) ? $getdata['client_id'] : '';

    $quotations = $wpdb->get_results("SELECT id, revised_no from {$wpdb->prefix}ctm_quotations where client_id='{$client_id}' AND con_res_date IS NOT NULL");

    $whr_qid = !empty($qid) ? " (t1.quotation_id like  '%" . $qid . "%' or  t1.revised_no like  '%" . $qid . "%') " : 1;
    $whr_client_id = !empty($client_id) ? " t1.client_id ='{$client_id}'" : 1;
    $whr_status = !empty($order_registry) ? " t1.order_registry ='{$order_registry}'" : 1;

    if (!empty($client_id) || !empty($qid)) {
        $where = "WHERE $whr_qid AND $whr_client_id AND $whr_status ";
        $sql = "SELECT t1.id,t1.quotation_id,t1.revised_no,t1.client_id,t1.item_id,t1.entry,t1.item_desc,t1.quantity,t1.order_registry,t1.arrival_date,"
                . "t1.dispatch_date,t1.delivery_date,t1.entry, t1.cl_pkgs,t1.stk_inv_location,t1.stk_inv_status,t1.stk_inv_comment,t1.revised_no, t1.receipt_no "
                . "FROM {$wpdb->prefix}ctm_quotation_po_meta t1 LEFT JOIN {$wpdb->prefix}ctm_items t2 ON t1.item_id=t2.id $where "
                . "ORDER BY CAST(t1.entry AS UNSIGNED INTEGER)  DESC";

        $rs = $wpdb->get_results($sql);

        $client = get_client($client_id);
        $qtn = get_revised_no($qid);
        $sales_person = get_qtn_sales_person($qid);
    }
    ?>

    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                        <h1 class="wp-heading-inline">Customer Order & Delivery Status</h1>
                        <form id="filter-form1" method="get">
                            <input type="hidden" name="page" value="<?= $page ?>" />                                       
                            <table class="form-table" style="width:1000px">
                                <tr>
                                    <td>
                                        <select name="client_id" id="client-name" class="chosen-select" onchange="this.form.submit()">
                                            <option value="">Search By Client</option>
                                        </select>
                                        <?php echo empty($client_id) ? "<span style='color:red'>Please first select client.</span>" : ''; ?>
                                    </td>
                                    <td>
                                        <select name="qid" class="chosen-select" onchange="this.form.submit()">
                                            <option value="">Search By Quotation</option>
                                            <?php
                                            foreach ($quotations as $value) {
                                                $selected = $value->id == $qid ? 'selected' : '';
                                                echo "<option value='{$value->id}' $selected>" . (!empty($value->revised_no) ? $value->revised_no : $value->id) . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <?php echo empty($qid) ? "<span style='color:red'>Please select quotation.</span>" : ''; ?>
                                    </td>
                                    <td>
                                        <select name="order_registry" id="status" onchange="this.form.submit()">
                                            <option value='' >Search By Item Status</option>
                                            <?php
                                            foreach (PO_STATUS as $value) {
                                                echo "<option value='$value' " . ($order_registry == $value ? 'selected' : '') . " >$value</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <button type="submit"  class="button-primary" value="Filter" >Filter</button>
                                        &nbsp;&nbsp;<a href="?page=<?= $getdata['page'] ?>"  class="button-secondary" >Reset</a>
                                    </td>
                                </tr>
                            </table><br/>
                        </form>

                        <div id='page-inner-content' class='postbox'><br/>
                            <div class='inside' style='max-width:100%;margin:auto'>

                                <?php
                                $html = '';
                                if (!empty($rs) && !empty($client_id) && !empty($qid)) {
                                    $html = "<style>
                                         table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .bg-gainsboro{background:gainsboro;background-color:gainsboro;-webkit-print-color-adjust: exact;}
                                        table tr th{text-align:center;font-size:10px;}
                                        table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                        .color-container{ display: inline-block; font-size:10px;}
                                        #tag-color-1 tr td, #tag-color-1 tr td{text-align:left;}
                                        .color-tab{width: 80px; display: inline-block; border-radius: 3px; margin: 3px; text-align: center;}
                                        .Pending-status{background:yellow;color:#fff;border:1px solid #000;}
                                        .ORDERED-status{background:#000;color:#fff;border:1px solid #000;}
                                        .CONFIRMED-status{background:#dc3545;color:#000;border:1px solid #000;}
                                        .DELIVERED-TO-FF-status{background:#62b7f3;color:#000;border:1px solid #000;}
                                        .TRANSIT-status{background:pink;color:#000;border:1px solid #000;}
                                        .ARRIVED-status{background:#cef195;color:#000;border:1px solid #000;}
                                        .DELIVERED-status{background:green;color:#000;border:1px solid #000;}
                                        .edd-status{background:gainsboro;color:#000;border:1px solid #000;}
                                        .edt-status{background:gainsboro;color:#000;border:1px solid #000;}
                                        .eda-status{background:gainsboro;color:#000;border:1px solid #000;}
                                        
                                    </style> ";

                                    $html .= "<table  width='800' style='width:100%'>
                                                <tr valign='top'>
                                                    <td style='text-align:right'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                    </td>
                                                </tr>
                                            </table>
                        
                                            <h1 style='text-align:center;font-size:18px;font-weight: normal;'>CUSTOMER ORDER & DELIVERY STATUS</h1>
                                           <table  width='800' style='width:100%'>
                                                <tr>
                                                    <td style='width: 100%;' >
                                                        <table border='0' style='border-collapse: collapse;width: 100%' cellpadding=3 cellspacing=3 >
                                                            <tr> <td style='text-align:left'>Customer: {$client->name} </td> "
                                            . "<td style='text-align:right'>QTN: {$qtn}</td></tr>
                                                            <tr> <td style='text-align:left'>Mobile: {$client->phone}</td> <td  style='text-align:right'>Date: " . date('d.m.Y') . " </td></tr>
                                                            <tr> <td  style='text-align:left'>Email: {$client->email}</td> <td  style='text-align:right'>SP:  {$sales_person} </td></tr>
                                                        </table>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            </table>
                                            ";
                                    $html .= "<table id='tag-color-1'><tr>"
                                            . "<td><span class='color-container'><span class='color-tab ORDERED-status'>Ordered</span> Order Placed</span></td>"
                                            . "<td><span class='color-container'><span class='color-tab CONFIRMED-status'>Confirmed</span> Order in Production</span></td>"
                                            . "<td><span class='color-container'><span class='color-tab DELIVERED-TO-FF-status'>Delivered to FF</span> Order Delivered to RB Frieght Forwarder</span></td>"
                                            . "<td><span class='color-container'><span class='color-tab TRANSIT-status'>Transit</span> in Transit to Dubai</span></td>"
                                            . "</tr></table>";

                                    $html .= "<table id='tag-color-1'><tr>"
                                            . "<td><span class='color-container'><span class='color-tab ARRIVED-status'>Arrived</span> Order Arrived to RBFD Warehouse</span></td>"
                                            . "<td><span class='color-container'><span class='color-tab edd-status'>EDD to FF</span> Estimated Delivery Date to RB Forwarder</span></td>"
                                            . "<td><span class='color-container'><span class='color-tab edt-status'>ETD</span> Estimated Transit Date</span></td>"
                                            . "<td><span class='color-container'><span class='color-tab eda-status'>EDA</span> Estimated Date of Arrival at Dubai</span></td>"
                                            . "</tr></table><br/>";

                                    $html .= " <table border='1' style='border-collapse: collapse;width:100%' cellpadding=3 cellspacing=3>
                                <tr valign='middle' class='bg-gainsboro'>
                                    <th style='text-align: center;' colspan=2><b>Countermark</b></th>
                                    <th style='text-align: center;width:400px' rowspan=2>Item Description</th>
                                    <th style='text-align: center;' rowspan=2>QTY</th>
                                    <th style='text-align: center' rowspan=2>Status (Ordered/Confirmed/<br/> Delivered to FF/Arrived</br/> Arrived)</th>
                                    <th style='text-align: center' rowspan=2>Collection</th>
                                    <th style='text-align: center'  rowspan=2>Entry No.</th>
                                    <th style='text-align: center'><b>EDD to FF</b></th>
                                    <th style='text-align: center'><b>ETD</b></th>
                                    <th style='text-align: center'><b>ETA</b></th>
                                </tr>";
                                    $html .= "<tr valign='middle' class='bg-gainsboro'>
                                    <th style='text-align: center;width:70px'>Customer Name</th>
                                    <th style='text-align: center;width:70px'>Quote No.</th>
                                    <th style='text-align: center'>Date</th>
                                    <th style='text-align: center'>Date</th>
                                    <th style='text-align: center'>Date</th>
                                </tr>";
                                    foreach ($rs as $value) {
                                        $item = get_item($value->item_id);
                                        if ($value->stk_inv_status == 'DELIVERED' && $value->order_registry == 'ARRIVED') {
                                            $item_status = 'DELIVERED';
                                        } else if ($value->stk_inv_status != 'DELIVERED' && $value->order_registry == 'ARRIVED') {
                                            $item_status = 'ARRIVED';
                                        } else {
                                            $item_status = $value->order_registry;
                                        }
                                        $html .= "<tr valign='middle' class=''>
                                    <td style='text-align: center;'>{$client->name}</td>
                                    <td style='text-align: center;'>" . ($value->revised_no ? $value->revised_no : $value->quotation_id) . "</td>
                                    <td style='text-align: left'>" . nl2br($value->item_desc) . "</td>
                                    <td style='text-align: center'>{$value->quantity}</td>
                                    <td style='text-align: center' class='" . (str_replace(' ', '-', $item_status)) . "-status'>{$item_status}</td>
                                    <td style='text-align: center'>{$item->collection_name}</td>
                                    <td style='text-align: center'>{$value->entry}</td>
                                    <td style='text-align: center'>" . rb_date($value->dispatch_date) . "</td>
                                    <td style='text-align: center'>" . rb_date($value->delivery_date) . "</td>
                                    <td style='text-align: center'>" . rb_date($value->arrival_date) . "</td>
                                </tr>";
                                    }

                                    $html .= "</table>";

                                    $html .= "<br/><br/><br/><br/> ";
                                    echo $html;

                                    $pdf_file = make_pdf_file_name("customer_order_delivery_status_" . (!empty($client_id) ? $client_id : '') . (!empty($qid) ? '_' . $qid : '') . ".pdf")['path'];
                                } else {
                                    echo "<br/><br/><br/><br/><br/><br/><br/><br/>";
                                }
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($pdf_file)) { ?>
                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-center">
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;";
                                    }
                                    ?>
                                    <!--<a href = '<?= export_excel_report($pdf_file, 'customer_order_delivery_status', $html) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>-->
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div><!--dashboard-widgets-wrap -->
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery('.chosen-select').chosen();
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($getdata['client_id']) ? $getdata['client_id'] : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Search By Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('#client-name').trigger("chosen:updated");
                }
            });

        });
    </script>
    <?php
    if (!empty($html)) {
        generate_pdf($html, $pdf_file, null, 1);
        pdf_copy($pdf_file, get_option('customer_order_delivery_status_dir'));
    }
}
