<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @global type $wpdb
 * @param type $data
 * @param type $month
 * @param type $comment
 */
function insert_or_update_ot($data, $month, $comment) {
    global $wpdb;
    foreach ($data as $key => $value) {
        $id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_hr_overtime_pays WHERE ot_month='{$month}' AND emp_id='{$key}'");
        if (!empty($id)) {
            $wpdb->query("UPDATE {$wpdb->prefix}ctm_hr_overtime_pays SET ot_pay='{$value}', comment='{$comment}' WHERE ot_month='{$month}' AND emp_id='{$key}'");
        } else {
            $wpdb->query("INSERT {$wpdb->prefix}ctm_hr_overtime_pays SET emp_id='{$key}', ot_pay='{$value}', ot_month='{$month}', comment='{$comment}' ");
        }
    }
}

/**
 * 
 * @param type $employees
 * @param type $overtimes
 * @return type
 */
function sync_overtime_with_emplyee($employees, $overtimes, $month) {

    $sync_arr = [];

    $a = array_column($employees, 'id');
    sort($a);
    $b = array_keys($overtimes);

    foreach ($a as $v) {
        if (in_array($v, $b)) {
            $sync_arr[$v] = $overtimes[$v];
        } else {
            $total_salary = get_employee($v, 'total_salary');
            $sync_arr[$v] = (object) ['id' => 0, 'emp_id' => 0, 'ot_time_from' => '', 'ot_time_to' => '', 'ot_rate' => 0, 'purpose' => '',
                        'comment' => '', 'total_salary' => $total_salary, 'hour_salary' => number_format($total_salary / rb_date($month, 't') / 9, 2)];
        }
    }
    return $sync_arr;
}

/**
 * 
 * @param type $attendance_dates
 * @param type $attendances
 * @return type
 */
function sync_attendance_with_emplyee($attendance_dates, $attendances) {
    $arr = [];
    if (count($attendances) != count($attendance_dates)) {
        $a = array_column($attendance_dates, 'attendance_date');
        sort($a);
        $b = array_keys($attendances);
        foreach ($a as $v) {
            if (in_array($v, $b)) {
                $arr[$v] = $attendances[$v];
            } else {
                $arr[$v] = (object) ['id' => 0, 'emp_id' => 0, 'full_day' => 0, 'half_day' => 0, 'un_paid_leave' => 0, 'paid_leave' => 0, 'sick_leave' => 0, 'late' => 0, 'early_out' => 0, 'attendance_date' => $v, 'note' => ''];
            }
        }
        $attendances = $arr;
    }
    return $attendances;
}

/**
 * 
 * @global type $wpdb
 * @param type $emp_id
 * @param type $month
 * @return type
 */
function get_overtime_of_employee($emp_id, $month) {
    global $wpdb;
    $ot_pay = $wpdb->get_var("SELECT ot_pay FROM {$wpdb->prefix}ctm_hr_overtime_pays WHERE ot_month='" . rb_date($month, 'Y-m-t') . "' AND emp_id='{$emp_id}'");
    return round($ot_pay);
}

/**
 * 
 * @param type $hours
 * @param type $minutes
 * @return type
 */
function convert_hour_minute_to_decimal($hours, $minutes) {
    return $hours + round($minutes / 60, 2);
}

/**
 * 
 * @global type $wpdb
 * @param type $fields
 * @return type
 */
function get_active_employees($fields = null) {
    global $wpdb;
    $field = $fields ? $fields : '*';
    $employees = $wpdb->get_results("SELECT {$field} FROM {$wpdb->prefix}ctm_hr_employees WHERE status='active' ORDER BY id ASC");
    return $employees;
}

/**
 * 
 * @global type $wpdb
 * @param type $fields
 * @return type
 */
function get_all_employees($fields = null) {
    global $wpdb;
    $field = $fields ? $fields : '*';
    $employees = $wpdb->get_results("SELECT {$field} FROM {$wpdb->prefix}ctm_hr_employees ORDER BY id ASC");
    return $employees;
}

/**
 * 
 * @global type $wpdb
 * @param type $employee_id
 * @param type $month
 * @return type
 */
function get_days_attended($employee_id, $month) {
    global $wpdb;
    $days = $wpdb->get_results("SELECT full_day,half_day,late,early_out  FROM {$wpdb->prefix}ctm_hr_attendances WHERE emp_id='$employee_id' AND attendance_date like '%$month%'");
    $full_day = $half_day = $late = $early_out = 0;
    foreach ($days as $value) {
        $full_day += $value->full_day ? 1 : 0;
        $half_day += $value->half_day ? 1 : 0;
        $late += $value->late ? 1 : 0;
        $early_out += $value->early_out ? 1 : 0;
    }
    return $full_day + $half_day + $late + $early_out;
}

/**
 * 
 * @global type $wpdb
 * @param type $emp_id
 * @param type $joining_date
 */
function make_attendence_from_joing_date($emp_id, $joining_date) {
    global $wpdb;
    $current_date = date('Y-m-d');
    $diff = rb_datediff($joining_date, $current_date);
    for ($i = 0; $i <= $diff->days; $i++) {
        $attendance_date = date('Y-m-d', strtotime($joining_date . "+{$i} days"));
        $exit = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_hr_attendances WHERE emp_id='{$emp_id}' AND attendance_date='{$attendance_date}'");
        if (empty($exit)) {
            $wpdb->query("INSERT INTO {$wpdb->prefix}ctm_hr_attendances SET emp_id='{$emp_id}', attendance_date='{$attendance_date}', full_day=1");
        }
    }
}
