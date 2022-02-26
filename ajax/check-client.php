<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);
$name = !empty($getdata['name']) ? trim($getdata['name']) : '';
$email = !empty($getdata['email']) ? trim($getdata['email']) : '';

$_name = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_clients WHERE name like '%{$name}%'");
$_email = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_clients WHERE email like '%{$email}%'");

echo json_encode(['name' => !empty($_name) ? true : false, 'email' => !empty($_email) ? true : false]);
