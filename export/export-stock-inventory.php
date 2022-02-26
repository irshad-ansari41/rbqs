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

$html .= "<th>Collection Name</th>";
$html .= "<th>Category</th>";
$html .= "<th>Supplier Code</th>";
$html .= "<th>Entry #</th>";
$html .= "<th>Description</th>";
$html .= "<th style='width:75px;'>Image</th>";
$html .= "<th>QTY</th>";
$html .= "<th>ETA</th>";
$html .= "<th>CQUE</th>";
$html .= "<th>QTN No.</th>";
$html .= "<th>Delivery Note</th>";
$html .= "<th>Delivery Date</th>";
$html .= "<th>HS Code</th>";
$html .= "<th>Location</th>";
$html .= "<th>Status</th>";
$html .= "<th>Comment</th>";
$html .= "<th>&nbsp;</th>";
$html .= "<th>ID</th>";
$html .= "<th>Item ID</th>";
$html .= "<th>Client ID</th>";
$html .= "<th>QTN ID</th>";
$html .= "</tr>";

$rs = get_cache_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE order_registry='ARRIVED' AND quantity>0 ORDER BY id asc", ['minute' => true]);

foreach ($rs as $value) {
    $sales_person = get_qtn_sales_person($value->quotation_id);
    $client_name = get_client($value->client_id, 'name');
    $item = get_item($value->item_id);
    $category = get_item_category($item->category, 'name');
    $qtn = get_revised_no($value->quotation_id);
    $cque = $client_name . ' ' . $qtn;
    $dn = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn where client_id='{$value->client_id}' AND quotation_id='{$value->quotation_id}'");
    $html .= "<tr>"
            . "<td>{$item->collection_name}</td>"
            . "<td>{$category}</td>"
            . "<td>{$value->sup_code}</td>"
            . "<td>$value->entry</td>"
            . "<td>" . nl2br($value->item_desc) . "</td>"
            . "<td style='width:75px;vertical-align:middle'><img src='" . get_image_src($item->image) . "' width=50 style='margin: 10px;width: 50px; '/></td>"
            . "<td>{$value->quantity}</td>"
            . "<td>{$value->arrival_date}</td>"
            . "<td>{$cque}</td>"
            . "<td>{$qtn}</td>"
            . "<td>" . (!empty($dn->id) ? $dn->id : '') . "</td>"
            . "<td>{$value->delivery_date}</td>"
            . "<td>{$item->hs_code}</td>"
            . "<td>{$value->stk_inv_location}</td>"
            . "<td>{$value->stk_inv_status}</td>"
            . "<td>{$value->stk_inv_comment}</td>"
            . "<td>&nbsp;</td>"
            . "<td>{$value->id}</td>"
            . "<td>{$value->item_id}</td>"
            . "<td>{$value->client_id}</td>"
            . "<td>{$value->quotation_id}</td>"
            . "</tr>";
}

$html .= "</table>";

$curr_datetime = current_time('mysql');
$filename = "Stock-Inventory-list-" . date('d-M-Y') . ".xls";

array_to_xls_download($html, $filename);

exit;
