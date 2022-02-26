<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);
$id = !empty($getdata['receipt_id']) ? $getdata['receipt_id'] : 0;

$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE id='{$qtn}' ");

echo json_encode(['receipt' => !empty($row) ? $row : false]);
