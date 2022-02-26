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
$html .= "<th>Description</th>";
$html .= "<th>Supplier Code</th>";
$html .= "<th>Entry</th>";
$html .= "<th>Category</th>";
$html .= "<th>HS Code</th>";
$html .= "<th>Created At</th>";
$html .= "</tr>";

$items = get_cache_results("SELECT * from {$wpdb->prefix}ctm_items ORDER BY collection_name ASC", ['minute' => true]);
foreach ($items as $item) {
    $category_nane = get_item_category($item->category, 'name');

    $html .= "<tr>"
            . "<td>{$item->id}</td>"
            . "<td>{$item->collection_name}</td>"
            . "<td>{$item->description}</td>"
            . "<td>{$item->sup_code}</td>"
            . "<td>{$item->entry}</td>"
            . "<td>{$category_nane}</td>"
            . "<td>{$item->hs_code}</td>"
            . "<td>{$item->updated_at}</td>"
            . "</tr>";
}

$html .= "</table>";

$curr_datetime = current_time('mysql');
$filename = "Item-list-" . date('d-M-Y') . ".xls";

array_to_xls_download($html, $filename);

exit;
