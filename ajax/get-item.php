<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);
$id = !empty($getdata['id']) ? $getdata['id'] : 0;

$type = !empty($getdata['type']) ? $getdata['type'] : '';
$po_meta_id = !empty($getdata['po_meta_id']) ? $getdata['po_meta_id'] : '';

if ($type == 'Stock') {
    $row = get_stock_inventory_items($po_meta_id);
} else {
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_items  WHERE id='{$id}' ");
    $row->category_name = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}ctm_item_category  WHERE id='{$row->category}' ");
}


echo json_encode($row);
