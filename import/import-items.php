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
$xlsx = SimpleXLSX::parse('PRODUCT-MASTER-26.10.2020.xlsx');

if (!empty($xlsx)) {
    $i = 0;
    foreach ($xlsx->rows() as $elt) {

        if ($i > 0) {

            print_r($elt);

            $collection_name = trim($elt[0]);
            $item_desc = $elt[1];
            $sup_code = $elt[2];
            $entry = $elt[3];
            $category = trim($elt[4]);
            $hs_code = $elt[5];
            $image = $elt[6];

            $created_at = $updated_at = date('Y-m-d H:i:s');

            //$item_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_items WHERE collection_name='{$elt[0]}'");
            $item_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}ctm_items WHERE collection_name = %s", $collection_name));
            
            $category_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_item_category WHERE name='{$elt[4]}'");

            if (!empty($item_id)) {
                $data = ['id' => $item_id, 'collection_name' => $elt[0], 'description' => $elt[1], 'sup_code' => $elt[2], 'entry' => $elt[3], 'category' => $category_id ? $category_id : $category, 'hs_code' => $elt[5], 'image' => $elt[6], 'status' => 'Active', 'updated_at' => 1, 'updated_at' => $updated_at];
                //$wpdb->update("{$wpdb->prefix}ctm_items", $data, ['id' => $item_id], wpdb_data_format($data), ['%d']);
            } else {
                $data = ['collection_name' => $elt[0], 'description' => $elt[1], 'sup_code' => $elt[2], 'entry' => $elt[3], 'category' => $category_id ? $category_id : 0, 'hs_code' => $elt[5], 'image' => $elt[6], 'status' => 'Active', 'created_by' => 1, 'updated_at' => 1, 'created_at' => $created_at, 'updated_at' => $updated_at];
                //$wpdb->insert("{$wpdb->prefix}ctm_items", $data, wpdb_data_format($data));
            }

            print_r($data);
        }
        $i++;
    }
    $msg = 'Client import has been done successfully';
}