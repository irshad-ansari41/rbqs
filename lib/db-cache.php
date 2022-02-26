<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @global type $wpdb
 * @param type $sql
 * @param type $time ['day' => 1, 'hour' => 1, 'minute' => 1, 'second' => 1]
 * @return type
 */
function get_cache_results($sql, $time = null) {
    global $wpdb;

    $_time = set_cache_time($time);
    $file_name = CACHE_JSON_DIR . md5($sql) . '-' . $_time . ".json";

    $data = file_exists($file_name) ? file_get_contents($file_name) : '';
    if (!empty($data)) {
        return json_decode($data);
    } else {
        $data = $wpdb->get_results($sql);
        file_put_contents($file_name, json_encode($data));
    }
    return $data;
}

/**
 * 
 * @global type $wpdb
 * @param type $sql
 * @param type $time ['day' => 1, 'hour' => 1, 'minute' => 1, 'second' => 1]
 * @return type
 */
function get_cache_row($sql, $time = null) {
    global $wpdb;

    $_time = set_cache_time($time);
    $file_name = CACHE_JSON_DIR . md5($sql) . '-' . $_time . ".json";
    $data = file_exists($file_name) ? file_get_contents($file_name) : '';
    if (!empty($data)) {
        return json_decode($data);
    } else {
        $data = $wpdb->get_row($sql);
        file_put_contents($file_name, json_encode($data));
    }
    return $data;
}

/**
 * 
 * @global type $wpdb
 * @param type $sql
 * @param type $time ['day' => 1, 'hour' => 1, 'minute' => 1, 'second' => 1]
 * @return type
 */
function get_cache_var($sql, $time = null) {
    global $wpdb;

    $_time = set_cache_time($time);
    $file_name = CACHE_JSON_DIR . md5($sql) . '-' . $_time . ".json";
    $data = file_exists($file_name) ? file_get_contents($file_name) : '';
    if (!empty($data)) {
        return json_decode($data);
    } else {
        $data = $wpdb->get_var($sql);
        file_put_contents($file_name, json_encode($data));
    }
    return $data;
}

function set_cache_time($time = null) {
    create_cache_dir();
    /*
      if (!empty($time['day'])) {
      $time = date('d'); //d
      } elseif (!empty($time['hour'])) {
      $time = date('d-H'); //d-H
      } elseif (!empty($time['minute'])) {
      $time = date('d-H-i'); //d-H-i
      } else {
      $time = time();
      }
      $time = date('d-H-i');
      return $time;
     */

    return date('d-H-i');
}

/**
 * Create cache directory
 */
function create_cache_dir() {
    $old = umask(0);
    if (!is_dir(CACHE_JSON_DIR)) {
        mkdir(CACHE_JSON_DIR, 0755, true) || chmod(CACHE_JSON_DIR, 0755);
    }
    umask($old);
}
