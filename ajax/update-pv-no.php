<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb, $current_user;
$date = current_time('mysql');

$postdata = filter_input_array(INPUT_POST);

$invoice_no = !empty($postdata['invoice_no']) ? $postdata['invoice_no'] : '';
$sup_code = !empty($postdata['sup_code']) ? $postdata['sup_code'] : '';
$pv_no = !empty($postdata['pv_no']) ? $postdata['pv_no'] : '';

if (!empty($invoice_no) && !empty($sup_code)) {
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET pv_no='$pv_no', updated_by='{$current_user->ID}', updated_at='{$date}' where invoice_no='{$invoice_no}' AND sup_code='{$sup_code}'");
    echo json_encode(['status' => true]);
}
if (empty($pv_no)) {
    echo json_encode(['status' => false]);
}

