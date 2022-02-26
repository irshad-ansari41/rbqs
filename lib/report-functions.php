<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @global type $wpdb
 * @param type $sp_id
 * @param type $month
 * @return type
 */
function get_total_sales_by_sp_id($sp_id, $month) {
    global $wpdb;
    $sql = "SELECT t1.total_amount FROM {$wpdb->prefix}ctm_receipts t1 "
            . "LEFT JOIN {$wpdb->prefix}ctm_quotations t2 ON t1.quotation_id= t2.id "
            . "where t1.created_at like '%{$month}%' AND t2.sales_person='$sp_id' AND t1.receipt_type='New' "
            . "GROUP BY t1.quotation_id";
    $results = $wpdb->get_results($sql);
    $total_amount = 0;
    foreach ($results as $value) {
        $total_amount += $value->total_amount;
    }
    return $total_amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $sp_id
 * @param type $month
 * @return type
 */
function get_sales_reversal_by_sp_id($sp_id, $month) {
    global $wpdb;
    $sql = "SELECT sum(total_amount) as amount FROM {$wpdb->prefix}ctm_sales_reversal where status='Approved' AND created_at like '%{$month}%' AND sales_person='$sp_id'";
    $results = $wpdb->get_var($sql);
    return $results ?? 0;
}

/**
 * 
 * @global type $wpdb
 * @param type $sp_id
 * @param type $month
 * @return type
 */
function get_credit_note_by_sp_id($sp_id, $month) {
    global $wpdb;
    $sql = "SELECT sum(total_amount) as amount FROM {$wpdb->prefix}ctm_tax_credit_note where status='Approved' AND created_at like '%{$month}%' AND sales_person='$sp_id'";
    $results = $wpdb->get_var($sql);
    return $results ?? 0;
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @return type
 */
function get_sales_reversal_by_qid($qid) {
    global $wpdb;
    $total_amount = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_sales_reversal where status='Approved' AND quotation_id='$qid'");
    return $total_amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @return type
 */
function get_store_credit_by_qid($qid) {
    global $wpdb;
    $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_store_credit_note where status='Approved' AND  quotation_id='$qid'");
    return $result;
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @return type
 */
function get_tax_credit_note_by_qid($qid) {
    global $wpdb;
    $total_amount = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_tax_credit_note where status='Approved' AND quotation_id='$qid'");
    return $total_amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @return type
 */
function get_sales_reversal_of_month($month, $sp_ids = []) {
    global $wpdb;
    $sales_person = !empty($sp_ids) ? " sales_person IN ('" . implode("', '", $sp_ids) . "') " : 1;
    $sql = "SELECT * FROM {$wpdb->prefix}ctm_sales_reversal where  $sales_person AND status='Approved' AND created_at like '%$month%'";
    $total_amount = $wpdb->get_results($sql);
    return $total_amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @return type
 */
function get_tax_credit_note_of_month($month, $sp_ids = []) {
    global $wpdb;
    $sales_person = !empty($sp_ids) ? " sales_person IN ('" . implode("', '", $sp_ids) . "') " : 1;
    $sql = "SELECT * FROM {$wpdb->prefix}ctm_tax_credit_note where $sales_person AND status='Approved' AND created_at like '%$month%'";
    $total_amount = $wpdb->get_results($sql);
    return $total_amount;
}

/* * ]
 * 
 */

function get_sales_persons() {
    global $wpdb;
    $results = $wpdb->get_results("SELECT ID,display_name FROM {$wpdb->prefix}users ORDER BY display_name ASC");
    $users = [];
    foreach ($results as $value) {
        $sp_id = get_user_meta($value->ID, 'sales_person', true);
        if (!empty($sp_id)) {
            $users[] = ['ID' => $value->ID, 'name' => $value->display_name, 'sp_id' => $sp_id];
        }
    }
    return $users;
}
