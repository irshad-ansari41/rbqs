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

$entry = !empty($getdata['entry']) ? trim($getdata['entry']) : '';

$row = new stdClass();
if (!empty($entry)) {
    $row = $wpdb->get_row($wpdb->prepare("SELECT id,po_id,quotation_id,revised_no,client_id,item_id,item_desc,quantity,confirmation_no FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE entry = %s", $entry));
    if (!empty($row)) {
        $item = get_item($row->item_id);
        $row->collection_name = $item->collection_name;
        $row->supplier = get_supplier($item->sup_code, 'name');
        $row->category = get_item_category($item->category, 'name');
        $row->cque =  get_client($row->client_id,'name') . ($row->revised_no ? $row->revised_no : $row->quotation_id);
    }
}

echo json_encode($row);
