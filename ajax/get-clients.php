<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);
$all = !empty($getdata['all']) ? $getdata['all'] : 0;
$client_id = !empty($getdata['client_id']) ? $getdata['client_id'] : 0;
$results=[];
if (!empty($all)) {
    $results = $wpdb->get_results("SELECT id,name,email,phone,address FROM {$wpdb->prefix}ctm_clients WHERE (status='Active' OR id='$client_id') ORDER BY name ASC");
} else {
    $results = $wpdb->get_results("SELECT id,name FROM {$wpdb->prefix}ctm_clients WHERE status='Active' ORDER BY name ASC");
}

echo json_encode($results);
