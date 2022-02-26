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
$html .= "<th>Name</th>";
$html .= "<th>Email</th>";
$html .= "<th>Email 2</th>";
$html .= "<th>Phone</th>";
$html .= "<th>Phone 2</th>";
$html .= "<th>Address</th>";
$html .= "<th>TRN</th>";
$html .= "<th>City</th>";
$html .= "<th>Country</th>";
$html .= "<th>Status</th>";
$html .= "<th>Created At</th>";
$html .= "</tr>";

$users = get_cache_results("SELECT * from {$wpdb->prefix}ctm_clients ORDER BY name ASC", ['minute' => true]);
foreach ($users as $value) {
    $html .= "<tr>"
            . "<td>{$value->id}</td>"
            . "<td>{$value->name}</td>"
            . "<td>{$value->email}</td>"
            . "<td>{$value->email2}</td>"
            . "<td>{$value->phone}</td>"
            . "<td>{$value->phone2}</td>"
            . "<td>{$value->address}</td>"
            . "<td>{$value->trn}</td>"
            . "<td>{$value->city}</td>"
            . "<td>{$value->country}</td>"
            . "<td>{$value->status}</td>"
            . "<td>{$value->updated_at}</td>"
            . "</tr>";
}

$html .= "</table>";

$curr_datetime = current_time('mysql');
$filename = "Client-list-" . date('d-M-Y') . ".xls";

array_to_xls_download($html, $filename);

exit;
