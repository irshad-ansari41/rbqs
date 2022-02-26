<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb;

$postdata = filter_input_array(INPUT_POST);
$po_id = !empty($postdata['po_id']) ? $postdata['po_id'] : '';
$comment = !empty($postdata['comment']) ? $postdata['comment'] : '';
$wpdb->query("UPDATE {$wpdb->prefix}ctm_quotation_po_meta SET stk_inv_comment='$comment' where id='{$po_id}'");
