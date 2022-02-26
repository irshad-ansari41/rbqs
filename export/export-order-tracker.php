<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include_once '../../../../wp-config.php';
global $wpdb;

function array_to_xls_download($data, $filename) {
    header("Content-Type: application/xls");
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $data;
}

$html = "<table id='leads' border=1 style='border-collapse:collapse' cellpadding=5 cellspacing=5 >";
$html .= "<tr style='font-weight: bold;'>";
$html .= "<th>ID</th>";
$html .= "<th>Collection Name</th>";
$html .= "<th>Category</th>";
$html .= "<th>Supplier Code</th>";
$html .= "<th>Entry #</th>";
$html .= "<th>Description</th>";
$html .= "<th>QTY</th>";
$html .= "<th>Sales Person</th>";
$html .= "<th>PO No.</th>";
$html .= "<th>PO Date</th>";
$html .= "<th>Confirmation</th>";
$html .= "<th>Customer Name</th>";
$html .= "<th>QTN No.</th>";
$html .= "<th>Status</th>";
$html .= "<th>Dispatch Date From Factory</th>";
$html .= "<th>Delivery Date To FF</th>";
$html .= "<th>Departure From Italy</th>";
$html .= "<th>Arrival Date In Dubai</th>";
$html .= "<th>InvoiceNo</th>";
$html .= "</tr>";

$rs = get_cache_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta ORDER BY po_id DESC", ['minute' => true]);

foreach ($rs as $value) {
    $sales_person = get_qtn_sales_person($value->quotation_id);
    $client_name = get_client($value->client_id, 'name');
    $item = get_item($value->item_id);
    $category = get_item_category($item->category, 'name');
    $qtn = get_revised_no($value->quotation_id);

    $html .= "<tr>"
            . "<td>{$item->id}</td>"
            . "<td>{$item->collection_name}</td>"
            . "<td>{$category}</td>"
            . "<td>{$value->sup_code}</td>"
            . "<td>{$value->entry}</td>"
            . "<td>" . nl2br($value->item_desc) . "</td>"
            . "<td>{$value->quantity}</td>"
            . "<td>{$sales_person}</td>"
            . "<td>{$value->po_id}</td>"
            . "<td>{$value->po_date}</td>"
            . "<td>{$value->confirmation_no}</td>"
            . "<td>{$client_name}</td>"
            . "<td>{$qtn}</td>"
            . "<td>{$value->order_registry}</td>"
            . "<td>{$value->dispatch_date}</td>"
            . "<td>{$value->delivery_date}</td>"
            . "<td>{$value->departure_date}</td>"
            . "<td>{$value->arrival_date}</td>"
            . "<td>{$value->invoice_no}</td>"
            . "</tr>";
}

$html .= "</table>";

$curr_datetime = current_time('mysql');
$filename = "Order-Tracker-list-" . date('d-M-Y') . ".xls";

array_to_xls_download($html, $filename);

exit;
