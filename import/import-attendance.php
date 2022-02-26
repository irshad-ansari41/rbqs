<?php
set_time_limit(0);
die;
include_once "../../../../wp-config.php";

include_once "SimpleXLSX.php";

$xlsx = SimpleXLSX::parse('excel/2012-2020.xlsx');

if (!empty($xlsx)) {
    $i = 0;
    foreach ($xlsx->rows() as $elt) {
        
        if ($i > 0) {

            $emp_id = trim($elt[1]);
            $full_date = trim($elt[2]);
            $half_day = trim($elt[3]);
            $un_paid_leave = trim($elt[4]);
            $paid_leave = trim($elt[5]);
            $sick_leave = trim($elt[6]);
            $late = trim($elt[7]);
            $early_out = trim($elt[8]);
            $note = trim($elt[9]);
            $attendance_date = trim($elt[10]);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $data = ['emp_id' => $emp_id, 'full_day' => $full_date, 'half_day' => $half_day, 'un_paid_leave' => $un_paid_leave,
                'paid_leave' => $paid_leave, 'sick_leave' => $sick_leave, 'late' => $late,'early_out'=>$early_out, 'note' => $note,'attendance_date'=>$attendance_date,
                'created_by' => 1, 'updated_by' => 1, 'created_at' => $created_at, 'updated_at' => $updated_at];

            //$wpdb->insert("{$wpdb->prefix}ctm_hr_attendances", array_map('trim', $data), wpdb_data_format($data));
            //wpdb_query_error();
//            echo '<pre>';
//            print_r($elt);
//            print_r($data);
//            echo '</pre>';
//            die;
        }
        $i++;
    }

    $msg = 'Client import has been done successfully';
}