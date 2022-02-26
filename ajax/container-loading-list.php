<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$postdata = filter_input_array(INPUT_POST);

$po_meta_id = !empty($postdata['po_meta_id']) ? $postdata['po_meta_id'] : 0;
$status = !empty($postdata['status']) ? $postdata['status'] : 0;
$priority = !empty($postdata['priority']) ? $postdata['priority'] : 0;

if (!empty($status)) {
    $status = $status == 1 ? 1 : 0;
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET add_in_list='$status' where id='{$po_meta_id}'");
    echo json_encode(['status' => !empty($status) ? 'added' : 'removed']);
}

if (!empty($priority)) {
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET cl_priority='$priority' where id='{$po_meta_id}'");
    echo json_encode(['status' => !empty($status) ? 'added' : 'removed']);
}
