<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
die;
include_once "../../../../wp-config.php";

include_once "SimpleXLSX.php";
global $wpdb;
$xlsx = SimpleXLSX::parse('excel/order-tracker.xlsx');
echo '<pre>';
if (!empty($xlsx)) {
    $i = 0;
    foreach ($xlsx->rows() as $elt) {

        if ($i > 0) {

            $collection_name = trim($elt[0]);

            $sup_code = trim($elt[2]);
            $entry = trim($elt[3]);
            $item_desc = trim($elt[4]);
            $qty = trim($elt[5]);
            $sales_person = trim($elt[6]);
            $po_no = trim($elt[7]);
            $po_date = trim($elt[8]);
            $confirmation_no = trim($elt[9]);
            $customer_name = trim($elt[10]);
            $qtn = trim($elt[11]);
            $status = trim($elt[12]);
            $dispatch_date = trim($elt[13]) == '1970-01-01 00:00:00' ? '' : $elt[13];
            $delivery_date = trim($elt[14]) == '1970-01-01 00:00:00' ? '' : $elt[14];
            $departure_date = trim($elt[15]) == '1970-01-01 00:00:00' ? '' : $elt[15];
            $arrival_date = trim($elt[16]) == '1970-01-01 00:00:00' ? '' : $elt[16];
            $invoice_no = $elt[17];
            $updated_at = date('Y-m-d H:i:s');

            $item_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}ctm_items WHERE collection_name = %s", $collection_name));
            $client_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}ctm_clients WHERE name = %s", $customer_name));
            $exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE po_id ='{$po_no}' AND entry ='{$entry}'");
            //$exist = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_quotation_po_meta WHERE entry ='{$entry}'");

            $items[] = $item_id;
            //if (!empty($item_id) && empty($exist)) {
            $note = 'Imported - ';
            $note .= "Client: $customer_name ";
            if (empty($qtn)) {
                $note .= " [QTN: $qtn]";
            }

            $data = ['po_id' => $po_no, 'quotation_id' => (int) $qtn, 'revised_no' => $qtn, 'item_id' => $item_id, 'client_id' => $client_id ? $client_id : 0, 'sup_code' => $sup_code, 'item_desc' => $item_desc, 'quantity' => $qty, 'entry' => $entry, 'confirmation_no' => $confirmation_no, 'order_registry' => strtoupper($status), 'dispatch_date' => $dispatch_date, 'delivery_date' => $delivery_date, 'departure_date' => $departure_date, 'arrival_date' => $arrival_date, 'invoice_no' => $invoice_no, 'ol_note' => $note, 'created_by' => $sales_person, 'updated_by' => 1, 'created_at' => $po_date, 'updated_at' => $updated_at];
            //$wpdb->insert("{$wpdb->prefix}ctm_quotation_po_meta", $data, wpdb_data_format($data));
            //wpdb_query_error();
            //print_r($elt);
            print_r($data);
            //}
        }

        $i++;
    }

    echo '</pre>';
    $msg = 'Client import has been done successfully';
}
