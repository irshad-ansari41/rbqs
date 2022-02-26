<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once "../../../../wp-config.php";

global $wpdb;
die;

include_once "SimpleXLSX.php";
$xlsx = SimpleXLSX::parse('excel/STOCK-INVENTORY-FINAL-LIST-14.11.2020.xlsx');

if (!empty($xlsx)) {
    $i = 0;
    foreach ($xlsx->rows() as $elt) {

        if ($i > 0) {

            $item_id = trim($elt[0]);
            $quantity = trim($elt[1]);
            $user_id = trim($elt[2]);
            $stk_inv_location = trim($elt[4]);
            $stk_inv_status = strtoupper(trim($elt[5]));

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $item = get_item($item_id);

            $data = ['client_id' => $user_id, 'item_id' => $item_id, 'item_desc' => $item->description, 'entry' => $item->entry,
                'sup_code' => $item->sup_code, 'quantity' => $quantity, 'order_registry' => 'ARRIVED',
                'stk_inv_location' => $stk_inv_location, 'stk_inv_status' => $stk_inv_status,
                'created_by' => 1, 'updated_by' => 1, 'created_at' => $created_at, 'updated_at' => $updated_at];
            
            echo '<pre>';
            print_r($elt);
            print_r($data);
            echo '</pre>';
        }
        $i++;
    }
}

