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

$results = $wpdb->get_results("SELECT ID,display_name FROM {$wpdb->prefix}users ORDER BY display_name ASC");
$users = [];
foreach ($results as $value) {
    $caps = get_user_meta($value->ID, 'wp_capabilities', true);
    $roles = array_keys((array) $caps);
    if ($roles[0] == 'sales') {
        $users[] = ['ID' => $value->ID, 'name' => $value->display_name, 'sp_id' => get_user_meta($value->ID, 'sales_person', true)];
    }
}

echo json_encode($users);
