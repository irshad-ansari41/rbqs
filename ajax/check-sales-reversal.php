<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include_once '../../../../wp-config.php';

global $wpdb;

$getdata = filter_input_array(INPUT_GET);
$qid = !empty($getdata['qid']) ? trim($getdata['qid']) : 0;
$revised_no = !empty($getdata['revised_no']) ? trim($getdata['revised_no']) : 0;
$receipt_no = !empty($getdata['receipt_no']) ? trim($getdata['receipt_no']) : 0;

if (!empty($receipt_no)) {
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE id='$receipt_no' ");
    echo json_encode(['qid' => $row->quotation_id, 'revised_no' => $row->revised_no, 'receipt_no' => $row->id]);
} else {
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE id='{$qid}' ");
    if (empty($row)) {
        $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations WHERE revised_no='{$revised_no}' ");
    }
    if (!empty($row)) {
        $receipt_no = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_receipts WHERE quotation_id='{$row->id}' ");
    }
    echo json_encode(['qid' => !empty($row) ? $row->id : false, 'revised_no' => !empty($row) ? $row->revised_no : '', 'receipt_no' => !empty($receipt_no) ? $receipt_no : false]);
}


