<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$results = $wpdb->get_results("SELECT id, name,sup_code FROM {$wpdb->prefix}ctm_suppliers ORDER BY sup_code ASC");

echo json_encode($results);
