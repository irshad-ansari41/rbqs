<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);

$type = !empty($getdata['type']) ? $getdata['type'] : '';

if ($type == 'Stock') {
    $results = get_stock_inventory_items();
} else {
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_items WHERE status='Active' ORDER  BY collection_name ASC");
}

echo json_encode($results);
