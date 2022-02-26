<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function copy_po_meta_row($po_meta_id) {
    global $wpdb, $current_user;
    $date = current_time('mysql');

    $insert_fields = "`po_id`,`qtn_meta_id`, `quotation_id`, `item_id`, `client_id`, `revised_no`, `sup_code`, `item_desc`, `quantity`, `entry`, `po_date`, `is_showroom`, `confirmation_no`, `dispatch_date`, `delivery_date`, `currency`, `invoice_no`, `invoice_amount`, `invoice_date`, `due_date`, `pv_no`, `departure_date`, `arrival_date`, `order_registry`, `arrival_notice`, `add_in_list`, `cl_status`, `container_name`,  `cl_value`, `cl_pkgs`, `cl_cbm`, `cl_kg`, `ol_status`, `ol_note`, `ol_add_list`, `ol_list_status`, `stk_inv_location`, `stk_inv_status`, `stk_inv_comment`, `receipt_no`, `created_by`, `updated_by`, `created_at`, `updated_at`";

    $select_fields = "`po_id`,`qtn_meta_id`, `quotation_id`, `item_id`, `client_id`, `revised_no`, `sup_code`, `item_desc`, `quantity`, `entry`, `po_date`, `is_showroom`, `confirmation_no`, `dispatch_date`, `delivery_date`, `currency`, `invoice_no`, `invoice_amount`, `invoice_date`, `due_date`, `pv_no`, `departure_date`, `arrival_date`, `order_registry`, `arrival_notice`, `add_in_list`, `cl_status`, `container_name`,  `cl_value`, `cl_pkgs`, `cl_cbm`, `cl_kg`, `ol_status`, `ol_note`, `ol_add_list`, `ol_list_status`, `stk_inv_location`, `stk_inv_status`, `stk_inv_comment`, `receipt_no`, '{$current_user->ID}', '{$current_user->ID}', '{$date}', '{$date}'";

    $sql = "INSERT INTO {$wpdb->prefix}ctm_quotation_po_meta ($insert_fields) SELECT $select_fields  FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE id = $po_meta_id";
    $wpdb->query($sql);
    return $wpdb->insert_id;
}

function get_po_meta_quantity($po_meta_id) {
    global $wpdb;
    return $wpdb->get_var("SELECT quantity FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE id='{$po_meta_id}'");
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @param type $client_id
 * @param type $qtype
 */
function create_pre_delivery($po_meta_id, $quantity) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $po_meta = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta where id='{$po_meta_id}'");
    $pdi_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_pre_delivery WHERE client_id='{$po_meta->client_id}' AND status='RESERVED' AND pdi_date='" . rb_date($date, 'Y-m-d') . "'");
    if (empty($pdi_id)) {

        $stfdata = ['client_id' => $po_meta->client_id, 'status' => 'RESERVED', 'pdi_date' => $date, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_pre_delivery", $stfdata, wpdb_data_format($stfdata));

        $pdi_id = $wpdb->insert_id;

        $meta_data = ['pdi_id' => $pdi_id, 'po_meta_id' => $po_meta_id, 'quantity' => $quantity,];
        $wpdb->insert("{$wpdb->prefix}ctm_pre_delivery_meta", $meta_data, wpdb_data_format($meta_data));
    } else {
        $meta_data = ['pdi_id' => $pdi_id, 'po_meta_id' => $po_meta_id, 'quantity' => $quantity,];
        $wpdb->insert("{$wpdb->prefix}ctm_pre_delivery_meta", $meta_data, wpdb_data_format($meta_data));
    }

    stock_inventroy_status_change($po_meta_id, $quantity, 'RESERVED');
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $id
 * @param type $quantity
 * @param type $location
 */
function stock_inventroy_location_change($po_meta_id, $quantity, $location) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $update_date = "updated_by='{$current_user->ID}', updated_at='$date'";
    if (get_po_meta_quantity($po_meta_id) != $quantity) {
        $id = copy_po_meta_row($po_meta_id);
        //update new record
        if ($quantity >= 1) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET quantity=quantity-$quantity WHERE id='{$id}'");
        }
    }

    //update old record
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET stk_inv_location='{$location}', quantity='{$quantity}', {$update_date} WHERE id='{$po_meta_id}'");
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $id
 * @param type $quantity
 * @param type $location
 */
function stock_inventroy_combine_quantity($po_meta_id) {
    global $wpdb;
    $po_meta = get_po_meta_data($po_meta_id);
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE entry='{$po_meta->entry}' AND "
            . "stk_inv_location='{$po_meta->stk_inv_location}' AND stk_inv_status='{$po_meta->stk_inv_status}'");

    if (count($results) > 1) {
        $quantity = array_sum(array_column($results, 'quantity'));
        $po_meta_ids = array_column($results, 'id');
        $id = copy_po_meta_row($po_meta_id);
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET quantity=$quantity WHERE id='{$id}'");
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET quantity=0 WHERE id in (" . implode(',', $po_meta_ids) . ')');
    }
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $id
 * @param type $quantity
 */
function stock_inventroy_status_change($po_meta_id, $quantity, $status) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $update_data = "updated_by='{$current_user->ID}', updated_at='$date' ";
    if (get_po_meta_quantity($po_meta_id) != $quantity) {
        $id = copy_po_meta_row($po_meta_id);
        //update new record
        if ($quantity >= 1) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET quantity=quantity-$quantity WHERE id='{$id}'");
        }
    }

    //Update Old Record
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET stk_inv_status='{$status}', quantity='{$quantity}', {$update_data} WHERE id='{$po_meta_id}'");
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @param type $client_id
 * @param type $qtype
 */
function create_stock_transfer($data) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $stf_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_stock_transfer WHERE from_location='{$data['from_location']}' AND to_location='{$data['to_location']}' AND st_date='" . rb_date($date, 'Y-m-d') . "'");
    if ($data['stf_type'] == 'New' || empty($stf_id)) {
        $stfdata = ['from_location' => $data['from_location'], 'to_location' => $data['to_location'], 'st_date' => $date, 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_stock_transfer", $stfdata, wpdb_data_format($stfdata));

        $stf_id = $wpdb->insert_id;

        $meta_data = ['stf_id' => $stf_id, 'po_meta_id' => $data['po_meta_id'], 'quantity' => $data['quantity'], 'cque' => $data['cque'], 'pkgs' => $data['pkgs'], 'purpose' => $data['purpose'], 'status' => 'Pending'];
        $wpdb->insert("{$wpdb->prefix}ctm_stock_transfer_meta", $meta_data, wpdb_data_format($meta_data));
        //stock_inventroy_location_change($data['po_meta_id'], $data['quantity'], $data['to_location']);
    } else {
        $stf_id = $data['stf_type'] == 'Existing' ? $data['stf_id'] : $stf_id;
        $meta_data = ['stf_id' => $stf_id, 'po_meta_id' => $data['po_meta_id'], 'quantity' => $data['quantity'], 'cque' => $data['cque'], 'pkgs' => $data['pkgs'], 'purpose' => $data['purpose'], 'status' => 'Pending'];
        $wpdb->insert("{$wpdb->prefix}ctm_stock_transfer_meta", $meta_data, wpdb_data_format($meta_data));
        //stock_inventroy_location_change($data['po_meta_id'], $data['quantity'], $data['to_location']);
    }
}

/**
 * 
 * @global type $wpdb
 * @param type $qid
 * @param type $client_id
 * @param type $qtype
 */
function create_order_delivery_note($po_meta_id, $quantity, $receipt_no) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $po_meta = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_quotation_po_meta where id='{$po_meta_id}'");
    $receipt = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_receipts where id='{$receipt_no}'");

    $dn_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_dn WHERE quotation_id='{$receipt->quotation_id}' AND client_id='{$receipt->client_id}' AND created_at like'%" . rb_date($date, 'Y-m-d') . "%'");

    if (empty($dn_id)) {

        $podata = ['quotation_id' => $receipt->quotation_id, 'revised_no' => $receipt->revised_no, 'client_id' => $receipt->client_id, 'qtn_type' => 'Order', 'status' => 'Pending', 'created_by' => $current_user->ID, 'updated_by' => $current_user->ID, 'created_at' => $date, 'updated_at' => $date];
        $wpdb->insert("{$wpdb->prefix}ctm_quotation_dn", $podata, wpdb_data_format($podata));

        $dn_id = $wpdb->insert_id;

        $dn_data = ['dn_id' => $dn_id, 'qtn_meta_id' => $po_meta->qtn_meta_id, 'po_meta_id' => $po_meta->id, 'quotation_id' => $receipt->quotation_id, 'item_id' => $po_meta->item_id, 'item_desc' => $po_meta->item_desc, 'quantity' => $po_meta->quantity, 'entry' => $po_meta->entry, 'location' => $po_meta->stk_inv_location, 'sup_code' => $po_meta->sup_code, 'status' => 'DELIVERED',];
        $wpdb->insert("{$wpdb->prefix}ctm_quotation_dn_meta", $dn_data, wpdb_data_format($dn_data));
    } else {
        $metadata = ['dn_id' => $dn_id, 'qtn_meta_id' => $po_meta->qtn_meta_id, 'po_meta_id' => $po_meta->id, 'quotation_id' => $receipt->quotation_id, 'item_id' => $po_meta->item_id, 'item_desc' => $po_meta->item_desc, 'quantity' => $po_meta->quantity, 'entry' => $po_meta->entry, 'location' => $po_meta->stk_inv_location, 'sup_code' => $po_meta->sup_code, 'status' => 'DELIVERED',];
        $wpdb->insert("{$wpdb->prefix}ctm_quotation_dn_meta", $metadata, wpdb_data_format($metadata));
    }
    $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET receipt_no='{$receipt_no}', quotation_id='{$receipt->quotation_id}', client_id='{$receipt->client_id}', updated_at='$date' WHERE id='{$po_meta_id}'");

    stock_inventroy_status_change($po_meta_id, $quantity, 'DELIVERED');
}

/**
 * 
 * @global type $wpdb
 * @global type $current_user
 * @param type $qid
 * @param type $client_id
 * @param type $po_meta_id
 * @param type $quantity
 * @param type $status
 * @return type
 */
function stock_qtn_update_stock_inventroy($qid, $client_id, $status) {
    global $wpdb, $current_user;
    $date = current_time('mysql');
    $update_data = "updated_by='{$current_user->ID}', updated_at='$date'";

    $qtn_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_quotations_meta where quotation_id='{$qid}' ORDER BY id ASC");
    foreach ($qtn_meta as $value) {
        if (get_po_meta_quantity($value->po_meta_id) != $value->quantity) {
            $id = copy_po_meta_row($value->po_meta_id);
            //Update new record
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET quantity=quantity-$value->quantity WHERE id='{$id}'");
        }

        //Update old record
        $wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET quotation_id='{$qid}', client_id='{$client_id}', stk_inv_status='{$status}', quantity='{$value->quantity}', {$update_data} WHERE id='{$value->po_meta_id}'");
    }
}
