<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_daily_schedule_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : date('Y-m-d'); //date('Y-m-01');
    $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : date('Y-m-d'); //date('Y-m-t');
    $transaction = !empty($getdata['transaction']) ? $getdata['transaction'] : '';

    $whr_from_date = !empty($from_date) ? $from_date . ' ' . '00:00:01' : date('Y-m-01 00:00:01');
    $whr_to_date = !empty($to_date) ? $to_date . ' ' . '23:59:59' : date('Y-m-t 23:59:59');

    $dn = $pjo = $qtn_confirm = $qtn_reserved = $receipts = $offloading = $stf = $qcr =$tax_invoices=$tax_credits=$sales_reversals= [];

    if (empty($transaction) || $transaction == 'Delivery') {
        $dn = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn WHERE delivery_date>='$from_date' AND delivery_date<='$to_date' ");
    }
    if (empty($transaction) || $transaction == 'pjo') {
        $results = $wpdb->get_results("SELECT t1.*,t2.start_date,t2.start_time,t2.end_time FROM {$wpdb->prefix}ctm_project_job_order t1 LEFT JOIN {$wpdb->prefix}ctm_project_job_order_meta t2 ON t1.id=t2.pjo_id WHERE t2.start_date >='$from_date' AND t2.start_date<='$to_date' AND t1.status='Pending'");
        $pjo = [];
        foreach ($results as $v) {
            if($v->status != 'Draft'){
                $pjo[$v->id.'-'.$v->start_date] = $v;
            }
        }
    }
    if (empty($transaction) || $transaction == 'Confirmed') {
        $qtn_confirm = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE status='CONFIRMED' AND con_res_date>='$from_date' AND con_res_date<='$to_date' ");
    }
    if (empty($transaction) || $transaction == 'Reserved') {
        $qtn_reserved = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE status='RESERVED' AND con_res_date>='$from_date' AND con_res_date<='$to_date' ");
    }
    if (empty($transaction) || $transaction == 'Receipt') {
        $receipts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE payment_date>='$from_date' AND payment_date<='$to_date'");
    }
    if (empty($transaction) || $transaction == 'Offloading') {
        $offloading = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE arrival_date>='$from_date' AND arrival_date<='$to_date' GROUP BY arrival_date");
    }
    if (empty($transaction) || $transaction == 'stf') {
        $stf = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_stock_transfer WHERE st_date>='$from_date' AND st_date<='$to_date'");
    }
    if (empty($transaction) || $transaction == 'qcr') {
        $qcr = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quality_control_report WHERE status='Proceed to Order' AND qcr_date>='$from_date' AND qcr_date<='$to_date'");
    }
    if (empty($transaction) || $transaction == 'performa-invoice') {
        $performa_invoice = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE status IN ('PURCHASED','CONFIRMED','DELIVERED','RESERVED') AND updated_at>='$whr_from_date' AND updated_at<='$whr_to_date'");
    }
    if (empty($transaction) || $transaction == 'tax-invoice') {
        $tax_invoices = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_tax_invoice WHERE created_at>='$whr_from_date' AND created_at<='$whr_to_date'");
    }
    if (empty($transaction) || $transaction == 'tax-credit') {
        $tax_credits = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_tax_credit_note WHERE status='Approved' AND updated_at>='$whr_from_date' AND updated_at<='$whr_to_date'");
    }
    if (empty($transaction) || $transaction == 'sales-reversal') {
        $sales_reversals = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_sales_reversal WHERE status='Approved' AND updated_at>='$whr_from_date' AND updated_at<='$whr_to_date'");
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


        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <form method="get">
                            <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
                            <table id="tbl-filter" cellpadding="5" cellspacing="5" style="width:800px">
                                <tr>
                                    <td>
                                        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                                    </td>
                                    <td>
                                        <label>From Date:</label><br/>
                                        <input type="date" name="from_date" value="<?= $from_date ?>" style="width:175px" required />
                                    </td>
                                    <td>
                                        <label>To Date:</label><br/>
                                        <input type="date" name="to_date" value="<?= $to_date ?>" style="width:175px" required />
                                    </td>
                                    <td><label>Transaction:</label><br/>
                                        <select name="transaction" onchange="this.form.submit()">
                                            <option value="" >Select Transaction</option>
                                            <option value="Delivery" <?= $transaction == 'Delivery' ? 'selected' : '' ?> >Delivery Note</option>
                                            <option value="Confirmed" <?= $transaction == 'Confirmed' ? 'selected' : '' ?> >Confirmed Quotation</option>
                                            <option value="Offloading" <?= $transaction == 'Offloading' ? 'selected' : '' ?> >Offloading</option>
                                            <option value="pjo" <?= $transaction == 'pjo' ? 'selected' : '' ?> >Project Job Order</option>
                                            <option value="Receipt" <?= $transaction == 'Receipt' ? 'selected' : '' ?> >Receipt</option>
                                            <option value="Reserved" <?= $transaction == 'Reserved' ? 'selected' : '' ?> >Reserved Quotation</option>
                                            <option value="stf" <?= $transaction == 'stf' ? 'selected' : '' ?> >Stock Transfer</option>
                                            <option value="qcr" <?= $transaction == 'qcr' ? 'selected' : '' ?> >Quality Check Report</option>
                                            <option value="performa-invoice" <?= $transaction == 'performa-invoice' ? 'selected' : '' ?> >Performa Invoicet</option>
                                            <option value="tax-invoice" <?= $transaction == 'tax-invoice' ? 'selected' : '' ?> >Tax Invoice</option>
                                            <option value="tax-credit" <?= $transaction == 'tax-credit' ? 'selected' : '' ?> >Tax Credit Note</option>
                                            <option value="sales-reversal" <?= $transaction == 'sales-reversal' ? 'selected' : '' ?> >Reversal of Sales</option>
                                        </select>
                                    </td>
                                    <td><br/><br/>
                                        <button type="submit" name="filter" value="1" class="btn btn-sm btn-primary ">Filter</button>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="admin.php?page=<?= $getdata['page'] ?>" class="btn btn-sm btn-secondary text-white">RESET</a>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <br/>
                        <form>
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

                                    $table = "<table width='800' style='width:100%'>
                            <tr valign='top'>
                            <td style='text-align:right'>
                            <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                            <br/>
                            </td>
                            </tr>
                            <tr valign=middl>
                            <td style='text-align:center'>
                            <h6><span style='font-size:22px;'>DAILY OPERATIONAL SCHEDULE</span></h6>
                            </td>
                            </tr>
                            </table><br/>";

                                    $table .= "<table confirm-order-items class='' border=1 style='border-collapse:collapse'>
                            <thead>
                            <tr valign=middle>
                            <th>DATE</th>
                            <th colspan=2>TIME</th>
                            <th>CUSTOMER</th>
                            <th>QTN # /<br/> PJO # /<br/> JOMPC # /<br/> STF #</th>
                            <th>LOCATION</th>
                            <th>RECEIPT #</th>
                            <th>PAYMENT<br/>STATUS</th>
                            <th>TRANSACTION</th>
                            <th>USER ID</th>
                            <th>USERNAME</th>
                            <th>NOTES</th>
                            </tr>
                            </thead>";

                                    $table .= "<tbody>";
                                    $tr = [];
                                    // Delivery Note
                                    foreach ($dn as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $client_name = get_client($value->client_id, 'name');
                                        $qtn = get_revised_no($value->quotation_id);
                                        $user = get_user($value->updated_by);
                                        $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$value->quotation_id'");
                                        $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='$value->quotation_id'");
                                        $location = !empty($quotation->city_id) ? get_location($quotation->city_id, 'city') : '';
                                        $balance_amount = get_qtn_balance_amount($value->quotation_id);
                                        $tr[] = [
                                            'date' => rb_date($value->delivery_date) . ' ' . rb_time($value->delivery_time_from),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->delivery_date) . "</td>
                                    <td>" . rb_time($value->delivery_time_from) . "</td>
                                    <td>" . rb_time($value->delivery_time_to) . "</td>
                                    <td>$client_name</td>
                                    <td>$qtn</td>
                                    <td>$location</td>
                                    <td>" . (!empty($receipt->id) ? $receipt->id : '') . "</td>
                                    <td>" . (!empty($receipt->id) && $balance_amount == 0 ? 'Full Payment' : '') . "</td>
                                    <td style='background:rgb(0,244,0)'><span style='color:#000'>Delivery</span></td>
                                    <td>$user->sp_id</td>
                                    <td>$user->display_name</td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }
                                    // Confirmed Quotation
                                    foreach ($qtn_confirm as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $client_name = get_client($value->client_id, 'name');
                                        $qtn = get_revised_no($value->id);
                                        $user = get_user($value->updated_by);
                                        $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$value->id'");
                                        $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='$value->id'");
                                        $location = get_location($quotation->city_id, 'city');
                                        $balance_amount = get_qtn_balance_amount($value->id);
                                        $bg = $value->type == 'Stock' ? '136,136,136' : ( $value->type == 'Order' ? '178,178,178' : '211,211,211');
                                        $tr[] = [
                                            'date' => rb_datetime($value->created_at),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->created_at) . "</td>
                                    <td>" . rb_time($value->created_at) . "</td>
                                    <td></td>
                                    <td>$client_name</td>
                                    <td>$qtn</td>
                                    <td>$location</td>
                                    <td>" . (!empty($receipt->id) ? $receipt->id : '') . "</td>
                                    <td>" . (!empty($receipt->id) && $balance_amount == 0 ? 'Full Payment' : '') . "</td>
                                    <td style='background:rgb($bg)'><span style='color:#fff'>Sales {$value->type}</span></td>
                                    <td>$user->sp_id</td>
                                    <td>$user->display_name</td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }
                                    // Reserved Quotations
                                    foreach ($qtn_reserved as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $client_name = get_client($value->client_id, 'name');
                                        $qtn = get_revised_no($value->id);
                                        $user = get_user($value->updated_by);
                                        $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$value->id'");
                                        $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='$value->id'");
                                        $location = get_location($quotation->city_id, 'city');
                                        $balance_amount = get_qtn_balance_amount($value->id);
                                        $tr[] = [
                                            'date' => rb_datetime($value->created_at),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->created_at) . "</td>
                                    <td>" . rb_time($value->created_at) . "</td>
                                    <td></td>
                                    <td>$client_name</td>
                                    <td>$qtn</td>
                                    <td>$location</td>
                                    <td>" . (!empty($receipt->id) ? $receipt->id : '') . "</td>
                                    <td>" . (!empty($receipt->id) && $balance_amount == 0 ? 'Full Payment' : '') . "</td>
                                    <td style='background:gray'><span style='color:#fff'>Sales {$value->type}</span></td>
                                    <td>$user->sp_id</td>
                                    <td>$user->display_name</td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }

                                    // Project Job Order 
                                    foreach ($pjo as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $client_name = get_client($value->client_id, 'name');
                                        $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$value->quotation_id'");
                                        $balance_amount = get_qtn_balance_amount($value->quotation_id);
                                        $user = get_user($value->updated_by);
                                        $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='$value->quotation_id'");
                                        $location = !empty($quotation->city_id) ? get_location($quotation->city_id, 'city') : '';
                                        $tr[] = [
                                            'date' => rb_date($value->start_date) . ' ' . rb_time($value->start_time, 'H:i a'),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->start_date) . "</td>
                                    <td>" . rb_time($value->start_time) . "</td>
                                    <td>" . rb_time($value->end_time) . "</td>
                                    <td style=width:150px>$client_name</td>
                                    <td>$value->quotation_id</td>
                                    <td>$location</td>
                                    <td>" . (!empty($receipt->id) ? $receipt->id : '') . "</td>
                                    <td>" . (!empty($receipt->id) && $balance_amount == 0 ? 'Full Payment' : '') . "</td>
                                    <td style='background:rgb(255,169,83)'><span style='color:#000'>PJO</span></td>
                                    <td>$user->sp_id</td>
                                    <td>$user->display_name</td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }

                                    // Receipts 
                                    foreach ($receipts as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $client_name = get_client($value->client_id, 'name');
                                        $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='$value->quotation_id'");
                                        $balance_amount = get_qtn_balance_amount($value->quotation_id);
                                        $user = get_user($value->updated_by);
                                        $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='$value->quotation_id'");
                                        $location = get_location($quotation->city_id, 'city');
                                        $pjo_meta = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_project_job_order_meta WHERE pjo_id='$value->id'");
                                        $tr[] = [
                                            'date' => rb_date($value->payment_date) . ' ' . rb_time($value->created_at),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->payment_date) . "</td>
                                    <td>" . rb_time($value->created_at) . "</td>
                                    <td></td>
                                    <td style=width:150px>$client_name</td>
                                    <td>$value->quotation_id</td>
                                    <td>$location</td>
                                    <td>$value->id</td>
                                    <td>" . ($balance_amount == 0 ? 'Full Payment' : 'Parcial') . "</td>
                                    <td style='background:rgb(255,66,66)'><span style='color:#fff'>Receipt</span></td>
                                    <td>$user->sp_id</td>
                                    <td>$user->display_name</td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }
                                    // Offloading 
                                    foreach ($offloading as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $tr[] = [
                                            'date' => rb_date($value->arrival_date) . rb_time($value->created_at),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->arrival_date) . "</td>
                                    <td>11:00 am</td>
                                    <td>05:00 pm</td>
                                    <td style=width:150px></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style='background:rgb(153,217,234)'><span style='color:#000'>Offloading</span></td>
                                    <td></td>
                                    <td></td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }
                                    // Stock Tranfer Form 
                                    foreach ($stf as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $user = get_user($value->updated_by);
                                        $tr[] = [
                                            'date' => rb_date($value->st_date) . rb_time($value->created_at),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->st_date) . "</td>
                                    <td>" . rb_time($value->created_at) . "</td>
                                    <td>" . rb_time($value->created_at) . "</td>
                                    <td style=width:150px></td>
                                    <td>{$value->id}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style='background:rgb(35 194 236)'><span style='color:#000'>Stock Transfer</span></td>
                                    <td>$user->sp_id</td>
                                    <td>$user->display_name</td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }

                                    // Quality Check Report 
                                    foreach ($qcr as $value) {
                                        $ds = set_daily_schedule($value->id, strtotime($value->created_at));
                                        $user = get_user($value->updated_by);
                                        $tr[] = [
                                            'date' => rb_date($value->qcr_date) . rb_time($value->created_at),
                                            'tr' => "<tr>
                                    <td>" . rb_date($value->qcr_date) . "</td>
                                    <td>" . rb_time($value->created_at) . "</td>
                                    <td>" . rb_time($value->created_at) . "</td>
                                    <td style=width:150px>" . get_client($value->client_id, 'name') . "</td>
                                    <td>{$value->id}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style='background:rgb(220 19 121)'><span style='color:#fff'>Quality Check Report</span></td>
                                    <td>$user->sp_id</td>
                                    <td>$user->display_name</td>
                                    <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
                                    </tr>"];
                                    }
                                    // Performa Invoice 
//                                    foreach ($performa_invoice as $value) {
//                                        $quotation = get_quotation($value->id);
//                                        $data = ['date' => $value->created_at, 'timefrom' => $value->created_at, 'timeto' => $value->created_at, 'customer' => get_client($quotation->client_id, 'name'), 'id' => $value->id, 'location' => get_location($quotation->city_id, 'city'), 'receipt' => $quotation->receipt_no, 'payment_status' => '', 'transaction' => 'Performa Invoice', 'user_id' => get_user($value->updated_by, 'sp_id'), 'username' => get_user($value->updated_by, 'name'), 'bg_color' => '#dc1378', 'fore_color' => '#fffff'];
//                                        $tr[] = ['date' => rb_datetime($value->created_at), 'tr' => daily_oparation_row($data)];
//                                    }
                                    // Tax Invoice 
                                    foreach ($tax_invoices as $value) {
                                        $quotation = get_quotation($value->quotation_id);
                                        $data = ['date' => $value->created_at, 'timefrom' => $value->created_at, 'timeto' => $value->created_at, 'customer' => get_client($quotation->client_id, 'name'), 'id' => $value->id, 'location' => get_location($quotation->city_id, 'city'), 'receipt' => $quotation->receipt_no, 'payment_status' => '', 'transaction' => 'Tax Invoice', 'user_id' => get_user($value->updated_by, 'sp_id'), 'username' => get_user($value->updated_by, 'name'), 'bg_color' => '#dc1378', 'fore_color' => '#fffff'];
                                        $tr[] = ['date' => rb_datetime($value->created_at), 'tr' => daily_oparation_row($data)];
                                    }
                                    // Tax Credit Note 
                                    foreach ($tax_credits as $value) {
                                        $quotation = get_quotation($value->quotation_id);
                                        $data = ['date' => $value->created_at, 'timefrom' => $value->created_at, 'timeto' => $value->created_at, 'customer' => get_client($quotation->client_id, 'name'), 'id' => $value->id, 'location' => get_location($quotation->city_id, 'city'), 'receipt' => $quotation->receipt_no, 'payment_status' => '', 'transaction' => 'Tax Credit Note', 'user_id' => get_user($value->updated_by, 'sp_id'), 'username' => get_user($value->updated_by, 'name'), 'bg_color' => '#dc1379', 'fore_color' => '#fffff'];
                                        $tr[] = ['date' => rb_datetime($value->created_at), 'tr' => daily_oparation_row($data)];
                                    }

                                    // Sales Reversal 
                                    foreach ($sales_reversals as $value) {
                                        $quotation = get_quotation($value->quotation_id);
                                        $data = ['date' => $value->created_at, 'timefrom' => $value->created_at, 'timeto' => $value->created_at, 'customer' => get_client($quotation->client_id, 'name'), 'id' => $value->id, 'location' => get_location($quotation->city_id, 'city'), 'receipt' => $quotation->receipt_no, 'payment_status' => '', 'transaction' => 'Sales of Reversal', 'user_id' => get_user($value->updated_by, 'sp_id'), 'username' => get_user($value->updated_by, 'name'), 'bg_color' => '#dc1380', 'fore_color' => '#fffff'];
                                        $tr[] = ['date' => rb_datetime($value->created_at), 'tr' => daily_oparation_row($data)];
                                    }

                                    usort($tr, function ($a, $b) {
                                        $t1 = strtotime($a['date']);
                                        $t2 = strtotime($b['date']);
                                        return $t2 - $t1;
                                    });

                                    foreach ($tr as $value) {
                                        $table .= $value['tr'];
                                    }

                                    $table .= "</tbody>";
                                    $table .= "</table>";
                                    $html .= $table;
                                    echo $html .= "";
                                    $pdf_file = make_pdf_file_name("DAILY_OPARATION_SCHEDULE_{$from_date}_{$to_date}.pdf")['path'];
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
                                    <a href = '<?= export_excel_report($pdf_file, 'daily_operational_report', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                    <br/>
                                    <br/>
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
    generate_pdf($html, $pdf_file);
}

function daily_oparation_row($data) {
    $getdata = filter_input_array(INPUT_GET);
    $ds = set_daily_schedule($data['id'], strtotime($data['date']));
    return "<tr>
            <td>" . rb_date($data['date']) . "</td>
            <td>" . rb_time($data['timefrom']) . "</td>
            <td>" . rb_time($data['timeto']) . "</td>
            <td style=width:150px>{$data['customer']}</td>
            <td>{$data['id']}</td>
            <td>{$data['location']}</td>
            <td>{$data['receipt']}</td>
            <td>{$data['payment_status']}</td>
            <td style='background:{$data['bg_color']}'><span style='color:{$data['fore_color']}'>{$data['transaction']}</span></td>
            <td>{$data['user_id']}</td>
            <td>{$data['username']}</td>
            <td>" . (empty($getdata['pdf']) ? "<input type='text' data-note_id='{$ds->id}' class=note style='width:100%' value='{$ds->note}' placeholder='Note' />" : '') . "</td>
            </tr>";
}
