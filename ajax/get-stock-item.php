<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);
$entry = !empty($getdata['entry']) ? $getdata['entry'] : '';

$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE entry='{$entry}' limit 1");

$row->item_desc = !empty($row->item_desc) ? nl2br($row->item_desc) : '';

echo json_encode($row);
