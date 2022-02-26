<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$postdata = filter_input_array(INPUT_POST);

$po_id = !empty($postdata['po_id']) ? $postdata['po_id'] : 0;
$status = !empty($postdata['status']) ? $postdata['status'] : 0;

$wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET ol_add_list='$status' where id='{$po_id}'");
echo json_encode(['status' => !empty($status) ? 'added' : 'removed']);
