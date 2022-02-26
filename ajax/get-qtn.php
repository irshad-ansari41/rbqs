<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);
$qtn = !empty($getdata['qtn']) ? $getdata['qtn'] : 0;

$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='{$qtn}' ");
if (empty($row)) {
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE revised_no='{$qtn}' ");
}
echo json_encode(['qtn' => !empty($row) ? $row : false]);
