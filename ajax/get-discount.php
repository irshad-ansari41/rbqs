<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include_once '../../../../wp-config.php';

global $wpdb;

$postdata = filter_input_array(INPUT_POST);
$city_id = !empty($postdata['city_id']) ? $postdata['city_id'] : 0;
$type = !empty($postdata['type']) ? $postdata['type'] : '';
$scope = !empty($postdata['scope']) ? $postdata['scope'] : '';
$promo_type = !empty($postdata['promo_type']) ? $postdata['promo_type'] : ''; //Local Export
$vat = !empty($postdata['vat']) ? $postdata['vat'] : ''; // wvat, wovat

$discount = get_discount($city_id, $type, $scope, $vat, $promo_type);

echo json_encode(['discount' => !empty($discount) ? $discount : 0]);
