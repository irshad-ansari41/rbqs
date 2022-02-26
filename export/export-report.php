<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

$getdata = filter_input_array(INPUT_GET);
$filename = !empty($getdata['file_name']) ? $getdata['file_name'] : '';
$option_key = !empty($getdata['option_key']) ? $getdata['option_key'] : '';

$data = get_option($option_key);

if (!empty($data)) {
    header("Content-Type: application/xls");
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $data;
}
exit;

