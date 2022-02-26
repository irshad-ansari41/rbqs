<?php

include_once "../../../../wp-config.php";

include_once "SimpleXLSX.php";

$xlsx = SimpleXLSX::parse('excel/STOCK-INVENTOR-FINAL.xlsx');

if (!empty($xlsx)) {
    $i = 0;
    foreach ($xlsx->rows() as $elt) {

        if ($i > 0) {

            $name= trim($elt[0]);
            $desc = trim($elt[1]);
            $sup_code = trim($elt[3]);
            $category = trim($elt[4]);
            $entry = trim($elt[5]);
            $hs_code = trim($elt[6]);
            
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $category_id = $wpdb->get_var("select id FROM {$wpdb->prefix}ctm_item_category WHERE name='{$category}'");
 
            $data = ['collection_name' => $name, 'description' => $desc, 'sup_code' => $sup_code, 'entry' => $entry, 
                'category' => $category_id,
                'created_by' => 1, 'updated_by' => 1, 'created_at' => $created_at, 'updated_at' => $updated_at];

            $wpdb->insert("{$wpdb->prefix}ctm_items", array_map('trim', $data), wpdb_data_format($data));

            echo '<pre>';
            print_r($elt);
            print_r($data);
            echo '</pre>';
           
        }
        $i++;
    }
  
    $msg = 'Client import has been done successfully';
}