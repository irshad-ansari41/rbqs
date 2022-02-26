<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once '../../../../wp-config.php';

global $wpdb, $current_user;
$date = current_time('mysql');

$postdata = filter_input_array(INPUT_POST);
$note_id = !empty($postdata['note_id']) ? $postdata['note_id'] : '';
$note = !empty($postdata['note']) ? $postdata['note'] : '';
$wpdb->query("UPDATE {$wpdb->prefix}ctm_daily_schedule SET note='$note', updated_by='{$current_user->ID}', updated_at='{$date}' where id='{$note_id}'");
