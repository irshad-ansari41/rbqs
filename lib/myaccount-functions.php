<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function create_tax_invoice_stock($qid) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_invoice where quotation_id='{$qid}'");

    update_qtn_meta_id_dnMeta_and_poMeta($qid);

    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$qid}'");
    $meta_ids = $wpdb->get_var("SELECT group_concat(id) FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$qid}'");
    $po_meta_ids = $wpdb->get_var("SELECT group_concat(po_meta_id) FROM {$wpdb->prefix}ctm_quotation_dn_meta where quotation_id='{$qid}'");

    if (empty($exist) && !empty($quotation)) {
        $data = ['series' => 'RB-' . date('Y'), 'quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no,
            'sales_person' => $quotation->sales_person, 'po_meta_ids' => $po_meta_ids, 'meta_ids' => $meta_ids, 'created_by' => $current_user->ID,
            'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_tax_invoice", $data, wpdb_data_format($data));
    } else if (!empty($quotation->id)) {
        $data = ['quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no,
            'sales_person' => $quotation->sales_person, 'po_meta_ids' => $po_meta_ids, 'meta_ids' => $meta_ids,
            'updated_by' => $current_user->ID, 'updated_at' => $date];
        $wpdb->update("{$wpdb->prefix}ctm_tax_invoice", $data, ['id' => $exist], wpdb_data_format($data), ['%d']);
    }
    return true;
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function create_tax_invoice_project($qid) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_invoice where quotation_id='{$qid}'");

    update_qtn_meta_id_dnMeta_and_poMeta($qid);

    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$qid}'");
    $meta_ids = $wpdb->get_var("SELECT group_concat(qtn_meta_id) FROM {$wpdb->prefix}ctm_quotation_dn_meta where quotation_id='{$qid}'");
    $po_meta_ids = $wpdb->get_var("SELECT group_concat(po_meta_id) FROM {$wpdb->prefix}ctm_quotation_dn_meta where quotation_id='{$qid}'");
    if (empty($exist) && !empty($quotation->id)) {
        $data = ['series' => 'RB-' . date('Y'), 'quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'sales_person' => $quotation->sales_person, 'po_meta_ids' => $po_meta_ids,
            'meta_ids' => $meta_ids, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_tax_invoice", $data, wpdb_data_format($data));
    } else if (!empty($quotation->id)) {
        $data = ['quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'sales_person' => $quotation->sales_person, 'po_meta_ids' => $po_meta_ids,
            'meta_ids' => $meta_ids, 'updated_by' => $current_user->ID, 'updated_at' => $date];
        $wpdb->update("{$wpdb->prefix}ctm_tax_invoice", $data, ['id' => $exist], wpdb_data_format($data), ['%d']);
    }
    return true;
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function create_tax_invoice_order($qid, $dn_id) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_invoice where quotation_id='{$qid}' AND dn_id='{$dn_id}'");

    update_qtn_meta_id_dnMeta_and_poMeta($qid);

    $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$qid}'");
    $meta_ids = $wpdb->get_var("SELECT group_concat(qtn_meta_id) FROM {$wpdb->prefix}ctm_quotation_dn_meta where quotation_id='{$qid}' AND dn_id='{$dn_id}'");
    $po_meta_ids = $wpdb->get_var("SELECT group_concat(po_meta_id) FROM {$wpdb->prefix}ctm_quotation_dn_meta where quotation_id='{$qid}' AND dn_id='{$dn_id}'");

    if (empty($exist) && !empty($quotation->id)) {
        $data = ['series' => 'RB-' . date('Y'), 'quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'sales_person' => $quotation->sales_person,
            'po_meta_ids' => $po_meta_ids, 'meta_ids' => $meta_ids, 'dn_id' => $dn_id, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID,
            'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_tax_invoice", $data, wpdb_data_format($data));
    } else if (!empty($quotation->id)) {
        $data = ['quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'sales_person' => $quotation->sales_person,
            'po_meta_ids' => $po_meta_ids, 'meta_ids' => $meta_ids, 'dn_id' => $dn_id, 'updated_by' => $current_user->ID, 'updated_at' => $date];
        $wpdb->update("{$wpdb->prefix}ctm_tax_invoice", $data, ['id' => $exist], wpdb_data_format($data), ['%d']);
    }
    return true;
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function create_tax_invoice_pjo($pjo_id) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_invoice WHERE pjo_id='$pjo_id'");
    if (empty($exist)) {
        $pjo = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_project_job_order WHERE id={$pjo_id}");
        $meta_ids = $wpdb->get_var("SELECT group_concat(qtn_meta_id) FROM {$wpdb->prefix}ctm_project_job_order_meta WHERE pjo_id={$pjo->id}");
        $data = ['series' => 'RB-' . date('Y'), 'pjo_id' => $pjo->id, 'quotation_id' => $pjo->quotation_id, 'revised_no' => $pjo->revised_no,
            'sales_person' => get_qtn_sales_person($pjo->quotation_id),
            'meta_ids' => $meta_ids, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID,
            'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_tax_invoice", $data, wpdb_data_format($data));
    }
    return true;
}

/**
 * 
 * @param type $qid
 */
function update_qtn_meta_id_dnMeta_and_poMeta($qid) {
    global $wpdb;
    $po_meta_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta where quotation_id='{$qid}' ORDER BY item_id ASC");
    $i = 1;
    foreach ($po_meta_items as $value) {
        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$value->quotation_id}' AND item_id='{$value->item_id}'");
        $qtn_meta_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$value->quotation_id}' AND item_id='{$value->item_id}' LIMIT " . (!empty($count) ? $count - $i : 0) . ",1");
        if (!empty($qtn_meta_id)) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta  SET qtn_meta_id = $qtn_meta_id where id='{$value->id}'");
        }
        if ($count > $i) {
            $i++;
        } else {
            $i = 1;
        }
    }
    $dn_meta_items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_dn_meta where quotation_id='{$qid}' ORDER BY item_id ASC");
    $j = 1;
    foreach ($dn_meta_items as $value) {
        $count = $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$value->quotation_id}' AND item_id='{$value->item_id}'");
        $qtn_meta_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$value->quotation_id}' AND item_id='{$value->item_id}' LIMIT " . (!empty($count) ? $count - $j : 0) . ",1");
        if (!empty($qtn_meta_id)) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_dn_meta  SET qtn_meta_id = $qtn_meta_id where id='{$value->id}'");
        }
        if ($count > $j) {
            $j++;
        } else {
            $j = 1;
        }
    }
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function create_tax_credit_note($tax_invoice_id, $postdata) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_credit_note where tax_invoice_id='{$tax_invoice_id}'");

    if (empty($exist)) {

        $tax_invoice = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_tax_invoice where id='{$tax_invoice_id}'");

        $meta_ids = !empty($postdata['meta_ids']) ? implode(',', array_keys($postdata['meta_ids'])) : $tax_invoice->meta_ids;
        $po_meta_ids = !empty($postdata['meta_ids']) ? implode(',', array_values($postdata['meta_ids'])) : $tax_invoice->po_meta_ids;

        $data = ['tax_invoice_id' => $tax_invoice->id, 'quotation_id' => $tax_invoice->quotation_id, 'revised_no' => $tax_invoice->revised_no, 'sales_person' => $tax_invoice->sales_person, 'meta_ids' => $meta_ids, 'po_meta_ids' => $po_meta_ids, 'cn_type' => $postdata['cn_type'], 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_tax_credit_note", $data, wpdb_data_format($data));
    }
    return true;
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function create_sales_reversal($qid, $receipt_no) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_sales_reversal where quotation_id='{$qid}'");

    if (empty($exist)) {

        $quotation = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotations where id='{$qid}'");

        $data = ['quotation_id' => $quotation->id, 'revised_no' => $quotation->revised_no, 'sales_person' => $quotation->sales_person, 'receipt_no' => $receipt_no, 'status' => 'Pending', 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];

        $wpdb->insert("{$wpdb->prefix}ctm_sales_reversal", $data, wpdb_data_format($data));
    }
    return true;
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @return boolean
 */
function create_store_credit_note($sales_reversal_id) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_store_credit_note where sales_reversal_id='{$sales_reversal_id}'");

    $sales_reversal = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_sales_reversal where id='{$sales_reversal_id}'");
    $data = ['sales_reversal_id' => $sales_reversal_id, 'quotation_id' => $sales_reversal->quotation_id, 'revised_no' => $sales_reversal->revised_no, 'sales_person' => $sales_reversal->sales_person,
        'receipt_no' => $sales_reversal->receipt_no, 'meta_ids' => $sales_reversal->meta_ids, 'paid_percent' => $sales_reversal->paid_percent, 'status' => 'Pending'];
    if (empty($exist)) {
        $data['created_by'] = $current_user->ID;
        $data['created_at'] = $date;
        $wpdb->insert("{$wpdb->prefix}ctm_store_credit_note", $data, wpdb_data_format($data));
    } else {
        $data['updated_by'] = $current_user->ID;
        $data['updated_at'] = $date;
        $wpdb->update("{$wpdb->prefix}ctm_store_credit_note", $data, ['id' => $exist], wpdb_data_format($data), ['%d']);
    }
    return true;
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_tax_invoice($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_tax_invoice WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_tax_invoice WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function get_tax_credit_note($id, $field = null) {
    global $wpdb;
    if ($field) {
        return $wpdb->get_var("SELECT $field FROM {$wpdb->prefix}ctm_tax_credit_note WHERE id='$id'");
    }
    return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_tax_credit_note WHERE id='$id'");
}

/**
 * 
 * @global type $wpdb
 * @param type $id
 * @param type $field
 * @return type
 */
function check_tax_credit_note($tax_invoice_id) {
    global $wpdb;
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_tax_credit_note WHERE tax_invoice_id='$tax_invoice_id'");
    return !empty($row) ? $row : false;
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $receipt_id
 */
function create_bank_deposit($receipt_id) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $data = ['receipt_id' => $receipt_id, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
    $wpdb->insert("{$wpdb->prefix}ctm_bank_deposits", $data, wpdb_data_format($data));
}

/**
 * 
 * @global type $wpdb
 * @param type $receipt_id
 * @return type
 */
function get_bank_deposit_balance($receipt_id) {
    global $wpdb;
    $paid_amount = $wpdb->get_var("SELECT sum(net_deposit) FROM {$wpdb->prefix}ctm_bank_deposits WHERE receipt_id='$receipt_id'");
    $total_amount = $wpdb->get_var("SELECT paid_amount FROM {$wpdb->prefix}ctm_receipts WHERE id='$receipt_id'");
    return $total_amount - $paid_amount;
}

/**
 * 
 * @global type $wpdb
 * @param type $receipt_id
 * @return type
 */
function get_cash_on_hold_balance($receipt_id) {
    global $wpdb;
    $net_deposit = $wpdb->get_var("SELECT sum(net_deposit) FROM {$wpdb->prefix}ctm_bank_deposits WHERE receipt_id='$receipt_id'");
    $charges = $wpdb->get_var("SELECT sum(charges) FROM {$wpdb->prefix}ctm_bank_deposits WHERE receipt_id='$receipt_id'");
    $total_amount = $wpdb->get_var("SELECT paid_amount FROM {$wpdb->prefix}ctm_receipts WHERE id='$receipt_id'");
    return $total_amount - ($net_deposit + $charges);
}

/**
 * 
 * @global type $wpdb
 */
function separate_cash_on_hold() {
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_receipts WHERE deposit_amount = '0.00'");
    foreach ($results as $value) {
        $hold = $value->paid_amount % 10;
        $deposit = $value->paid_amount - $hold;
        $deposit_amount = floor($deposit);
        $hold_cash = $hold + ($deposit - $deposit_amount);
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_receipts SET deposit_amount ='{$deposit_amount}', hold_cash='{$hold_cash}' WHERE id='$value->id'");
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $invoice_no
 * @return type
 */
function get_total_invoice_value($invoice_no) {
    global $wpdb;
    $value = $wpdb->get_var("SELECT invoice_amount FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE invoice_no='{$invoice_no}' ORDER BY CAST(REPLACE(entry,'/','') AS UNSIGNED INTEGER) ASC");
    return $value;
}

function make_credit_transaction($deposit_id, $payment_date, $verify_date, $particulars, $voucher_no, $amount) {
    global $wpdb;
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_bank_transactions WHERE deposit_id='$deposit_id'");
    if (empty($exist) && rb_float($amount)) {
        $account_name = PAYMENT_SOURCE[1];
        $closing = get_closing_amount($account_name);
        $data = ['deposit_id' => $deposit_id, 'transaction_date' => $payment_date, 'bank_date' => $verify_date, 'account_name' => $account_name, 'particulars' => $particulars, 'voucher_type' => 'Receipt', 'voucher_no' => $voucher_no, 'opening' => $closing, 'credit' => $amount, 'closing' => $closing + $amount, 'transaction_time' => date('YmdHis'), 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        credit_amount($data);
    } elseif (!empty($exist) && rb_float($amount)) {
        $data = ['bank_date' => $verify_date,];
        $wpdb->update("{$wpdb->prefix}ctm_bank_transactions", $data, ['id' => $exist], wpdb_data_format($data), '%d');
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $data
 * @return type
 */
function credit_amount($data) {
    global $wpdb;
    $wpdb->insert("{$wpdb->prefix}ctm_bank_transactions", $data, wpdb_data_format($data));
    return $wpdb->insert_id;
}

function make_debit_transaction($withdrawal_id, $account_name, $payment_date, $verify_date, $particulars, $voucher_no, $amount) {
    global $wpdb;
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_bank_transactions WHERE withdrawal_id='$withdrawal_id'");
    if (empty($exist) && rb_float($amount)) {
        $closing = get_closing_amount($account_name);
        if ($closing > $amount) {
            $data = ['withdrawal_id' => $withdrawal_id, 'transaction_date' => $payment_date, 'bank_date' => $verify_date, 'account_name' => $account_name, 'particulars' => $particulars, 'voucher_type' => 'Payment', 'voucher_no' => $voucher_no, 'opening' => $closing, 'debit' => $amount, 'closing' => $closing - $amount, 'transaction_time' => date('YmdHis'), 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
            debit_amount($data);
        }
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $data
 * @return type
 */
function debit_amount($data) {
    global $wpdb;
    $wpdb->insert("{$wpdb->prefix}ctm_bank_transactions", $data, wpdb_data_format($data));
    return $wpdb->insert_id;
}

/**
 * 
 * @global type $wpdb
 * @param type $account_name
 * @return type
 */
function get_closing_amount($account_name) {
    global $wpdb;
    $closing = $wpdb->get_var("SELECT closing FROM {$wpdb->prefix}ctm_bank_transactions WHERE account_name='{$account_name}' ORDER BY transaction_time DESC");
    return $closing ?? 0;
}

/**
 * 
 * @global type $wpdb
 * @param type $account_name
 */
function update_transaction($account_name) {
    global $wpdb;
    $rs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_bank_transactions where account_name='{$account_name}' AND transaction_date>'2020-12-31' order by transaction_date ASC");
    $opening = $wpdb->get_var("SELECT closing FROM {$wpdb->prefix}ctm_bank_transactions WHERE account_name='{$account_name}' AND id='236'");
    foreach ($rs as $value) {
        $sql = "UPDATE {$wpdb->prefix}ctm_bank_transactions SET opening=$opening,closing=opening+credit-debit where id=$value->id ";
        $wpdb->query($sql);
        $opening = $wpdb->get_var("SELECT closing FROM {$wpdb->prefix}ctm_bank_transactions where id='$value->id'");
    }
}

/**
 * 
 * @param type $qid
 * @return type
 */
function get_paid_percent($qid) {
    $paid_amount = get_qtn_paid_amount($qid);
    $total_amount = get_qtn_total_amount($qid);
    $percent = !empty($total_amount) && !empty($paid_amount) ? ($paid_amount / $total_amount) * 100 : 0;
    return $percent;
}

function check_quotation_confirm_partial_status($qid) {
    global $wpdb;
    $result1 = $wpdb->get_var("SELECT group_concat(order_registry) as order_registry FROM wp_ctm_quotation_po_meta where quotation_id='$qid'");
    $order_registries = array_unique(explode(',', $result1));
    $result2 = $wpdb->get_var("SELECT group_concat(stk_inv_status) as order_registry FROM wp_ctm_quotation_po_meta where quotation_id='$qid'");
    $stk_inv_statuses = array_unique(explode(',', $result2));
    if (in_array('DELIVERED', $stk_inv_statuses) && count($stk_inv_statuses) == 1) {
        return 'Completed';
    } else if (in_array('ORDERED', $order_registries) || in_array('CANCELLED', $order_registries) || in_array('DELIVERED TO FF', $order_registries) || in_array('CONFIRMED', $order_registries)) {
        return 'Partial';
    } else {
        return 'Completed';
    }
}
