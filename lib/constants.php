<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('FLAGSHIP_ID')) {
    define('FLAGSHIP_ID', 5250);
}

if (!defined('CSS_JS_VERSION')) {
    define('CSS_JS_VERSION', '1.1.7');
}

if (!defined('THEME_DIR')) {
    define('THEME_DIR', get_template_directory());
}

if (!defined('THEME_URL')) {
    define('THEME_URL', get_template_directory_uri());
}

add_filter('upload_dir', 'wpcb_upload_dir_filter');

/**
 * 
 * @param type $uploads
 * @return string
 */
function wpcb_upload_dir_filter($uploads) {
    $uploads['path'] .= '/' . date('d');
    $uploads['url'] .= '/' . date('d');
    return $uploads;
}

$upload_dir = wp_upload_dir();

if (!defined('WP_UPLOAD_PATH')) {
    define('WP_UPLOAD_PATH', $upload_dir['path']);
}

if (!defined('WP_UPLOAD_URL')) {
    define('WP_UPLOAD_URL', $upload_dir['url']);
}

if (!defined('PDF_UPLOAD_PATH')) {
    define('PDF_UPLOAD_PATH', str_replace('/uploads/', '/uploads/pdf/', WP_UPLOAD_PATH) . '/');
}

if (!defined('PDF_UPLOAD_URL')) {
    define('PDF_UPLOAD_URL', str_replace('/uploads/', '/uploads/pdf/', WP_UPLOAD_URL) . '/');
}

if (!defined('CACHE_JSON_DIR')) {
    define('CACHE_JSON_DIR', str_replace('/uploads/', '/uploads/json/', WP_UPLOAD_PATH) . '/');
}

if (!defined('COMPANY_ADDRESS')) {
    define('COMPANY_ADDRESS', 'Dubai Flagship, Al Barsha 1st, Sh. Zayed Road, P.O. Box 286, Tel: + 971 4 399 03 93');
}

if (!defined('DELIVERY_TIME_FROM')) {
    define('DELIVERY_TIME_FROM', [8 => '08:30 AM', 9 => '09:30 AM', 10 => '10 AM', 11 => '11 AM', 12 => '12 PM', 14 => '2 PM', 15 => '3 PM', 16 => '4 PM', 17 => '5 PM', 18 => '6 PM']);
}

if (!defined('DELIVERY_TIME_TO')) {
    define('DELIVERY_TIME_TO', [10 => '10 AM', 11 => '11 AM', 12 => '12 PM', 14 => '2 PM', 15 => '3 PM', 16 => '4 PM', 17 => '5 PM', 18 => '6 PM', 19 => '7 PM']);
}


if (!defined('STOCK_LOCATION')) {
    define('STOCK_LOCATION', ['WH', 'FS', 'TSAR', 'PH', 'ER1', 'ER2', 'ER4', 'FF', 'Basement 3', 'Sample Room',]);
}

if (!defined('STOCK_STATUS')) {
    define('STOCK_STATUS', ['AVAILABLE', 'RESERVED', 'DELIVERED', 'DAMAGED', 'USED',]);
}

if (!defined('QCR_REASON')) {
    define('QCR_REASON', ['Damage', 'Missing Element', 'Wrong Delivery', 'Manufacturing Defect', 'Poor Finish/Quality', 'Poor Packing', 'Elements&nbsp;Not Working', 'Transport Damage', 'Other']);
}

if (!defined('QCR_SOLUTION')) {
    define('QCR_SOLUTION', ['Replacement', 'Reupholstery', 'Touch-Up', 'Adjustment', 'Solution From Supplier', 'Parts To Be Sent', 'Credit Note', 'Other',]);
}

if (!defined('QTN_STATUS')) {
    define('QTN_STATUS', ['Draft', 'Pending', 'CONFIRMED', 'RESERVED', 'PURCHASED', 'DELIVERED', 'Converted to PJO',]);
}

if (!defined('PO_STATUS')) {
    define('PO_STATUS', ['Pending', 'ORDERED', 'CONFIRMED', 'CANCELLED', 'DELIVERED TO FF', 'TRANSIT', 'ARRIVED',]);
}

if (!defined('PAYMENT_METHOD')) {
    define('PAYMENT_METHOD', ['Cash', 'Check', 'Card', 'Bank Transfer', 'Bank Deposit', 'Bank Deposit', 'Store Credit']);
}

if (!defined('PAYMENT_SOURCE')) {
    define('PAYMENT_SOURCE', [1 => 'ENBD - AED', 2 => 'ENBD - EUR', 3 => 'ENBD - USD', 4 => 'CIC Paris - EUR', 5 => 'Credit Card']);
}

if (!defined('DEPARTMENTS')) {
    define('DEPARTMENTS', ['accounts' => 'Accounts', 'commercial' => 'Commercial','customer-experience'=>'Customer Experience', 'logistics' => 'Logistics', 'personal' => 'Personal', 'sales' => 'Sales']);
}

if (!defined('DESIGNATION')) {
    define('DESIGNATION', ['accountant' => 'Accountant', 'commercial' => 'Commercial | Procurement','customer-experience'=>'Customer Ambassador',  'driver' => 'Driver', 'delivery_team' => 'Delivery Team', 'carpenter' => 'Carpenter', 'office_boy' => 'Office Boy', 'tailor' => 'Tailor', 'plumber' => 'Plumber', 'electromechanical_engineer' => 'Electromechanical Engineer', 'cleaner' => 'Cleaner', 'maintence_coordinator' => 'Maintence Coordinator', 'ac_technical' => 'AC Technical', 'stable_watchman' => 'Stable Watchman', 'sales_interior_designer' => 'Sales | Interior Designer']);
}