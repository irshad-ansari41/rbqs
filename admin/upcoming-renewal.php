<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_renewal_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);

    if (!empty($getdata['emp_id'])) {
        include_once 'employer-profile/send-reminder-email.php';
        send_remider_email($getdata);
    }

    if (!empty($postdata['send_email'])) {
        $email_status = rb_send_email($postdata);
        $msg = !empty($email_status) ? "<strong>Success!</strong> Email has been sent successfully." : 0;
    }

    $options = get_option("rb_employer_options");
    $reminder_days = $options['rb_reminder'];
    $rb_renewals = $options['rb_renewal'];
    $rb_vehicles = $options['rb_vehicle'];
    $employees = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_hr_employees WHERE status='active'");
    ?>
    <style>
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
    </style>
    <div class="wrap">
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <?php if (!empty($msg)) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <?= $msg ?>
                            </div>
                            <?php
                        }

                        $html = "<style>
                            table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                            .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                            table tr th{text-align:center;font-size:12px;}
                            table tr td{text-align:center;font-size:12px;}
                            table{width:100%;}
                            .send-reminder{font-size:12px;}
                            textarea{width: 100%;}
                            .note{width:250px;}
                            </style>
                            <div id='page-inner-content' class='postbox'><br/>
                            <div class='inside' style='max-width:100%;margin:auto'>";

                        $table = "<table width='800' style='width:100%'>
                            <tr valign='top'>
                            <td style='text-align:right'>
                            <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                            <br/><br/>
                            </td>
                            </tr>
                            <tr valign=middl>
                            <td style='text-align:center'>
                            <h6><span style='font-size:22px;'>UPCOMING RENEWALS</span></h6>
                            </td>
                            </tr>
                            </table><br/>";

                        $table .= "<table confirm-order-items cellpadding='5' border=1 style='border-collapse:collapse'>
                            <thead>
                            <tr valign=middle>
                            <th>EXPIRY DATE</th>
                            <th>DAYS REMAINING</th>
                            <th>DOCUMENT</th>
                            <th>DOCUMENT POSSESSION</th>
                            <th>NAME</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th style='width:250px;'>Note</th>
                            </tr>
                            </thead>";

                        $table .= "<tbody>";
                        $tr = [];
                        // Employee Note
                        foreach ($employees as $value) {

                            $diff = rb_datediff(date('Y-m-d'), $value->passport_expiry_date);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['passport']) {
                                $ur = set_upcoming_renewal($value->id, strtotime($value->passport_expiry_date));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id={$value->id}&type=Passport&name={$value->name}");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>" . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $value->passport_expiry_date,
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($value->passport_expiry_date) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>Passport</td>
                                                    <td>$value->passport_possession</td>
                                                    <td>$value->name</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                    "];
                            }

                            $diff = rb_datediff(date('Y-m-d'), $value->eid_expiry_date);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['visa']) {
                                $ur = set_upcoming_renewal($value->id, strtotime($value->eid_expiry_date));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id={$value->id}&type=Emirate ID&name={$value->name}");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $value->eid_expiry_date,
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($value->eid_expiry_date) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>Emirate ID</td>
                                                    <td>$value->eid_possession</td>
                                                    <td>$value->name</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                    "];
                            }

                            $diff = rb_datediff(date('Y-m-d'), $value->policy_expiry_date);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['insurance']) {
                                $ur = set_upcoming_renewal($value->id, strtotime($value->policy_expiry_date));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id={$value->id}&type=Insurance Policy&name={$value->name}");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $value->policy_expiry_date,
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($value->policy_expiry_date) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>Insurance Policy</td>
                                                    <td></td>
                                                    <td>$value->name</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                    "];
                            }

                            $diff = rb_datediff(date('Y-m-d'), $value->wp_expiry_date);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['labor_card']) {
                                $ur = set_upcoming_renewal($value->id, strtotime($value->wp_expiry_date));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id={$value->id}&type=Work Permit&name={$value->name}");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $value->wp_expiry_date,
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($value->wp_expiry_date) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>Work Permit</td>
                                                    <td></td>
                                                    <td>$value->name</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                            }
                        }

                        // EMPLOYER
                        if (!empty($rb_renewals['commercial_license'])) {
                            $diff = rb_datediff(date('Y-m-d'), $rb_renewals['commercial_license']['expiry_date']);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['commercial_license']) {
                                $ur = set_upcoming_renewal(101, strtotime($rb_renewals['commercial_license']['expiry_date']));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id=101&type=COMMERCIAL LICENSE&name=ROCHE BOBOIS");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $rb_renewals['commercial_license']['expiry_date'],
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($rb_renewals['commercial_license']['expiry_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>COMMERCIAL LICENSE</td>
                                                    <td></td>
                                                    <td>ROCHE BOBOIS</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                            }
                        }

                        if (!empty($rb_renewals['establishment_card'])) {
                            $diff = rb_datediff(date('Y-m-d'), $rb_renewals['establishment_card']['expiry_date']);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['establishment_card']) {
                                $ur = set_upcoming_renewal(102, strtotime($rb_renewals['establishment_card']['expiry_date']));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id=102&type=ESTABLISHMENT CARD&name=ROCHE BOBOIS");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $rb_renewals['establishment_card']['expiry_date'],
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($rb_renewals['establishment_card']['expiry_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>ESTABLISHMENT CARD</td>
                                                    <td></td>
                                                    <td>ROCHE BOBOIS</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                            }
                        }

                        if (!empty($rb_renewals['customs_code'])) {
                            $diff = rb_datediff(date('Y-m-d'), $rb_renewals['customs_code']['expiry_date']);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['customs_code']) {
                                $ur = set_upcoming_renewal(103, strtotime($rb_renewals['customs_code']['expiry_date']));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id=103&type=CUSTOMS CODE&name=ROCHE BOBOIS");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $rb_renewals['customs_code']['expiry_date'],
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($rb_renewals['customs_code']['expiry_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>CUSTOMS CODE</td>
                                                    <td></td>
                                                    <td>ROCHE BOBOIS</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                            }
                        }

                        if (!empty($rb_renewals['lease_contract'])) {
                            $diff = rb_datediff(date('Y-m-d'), $rb_renewals['lease_contract']['end_date']);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['lease_contract']) {
                                $ur = set_upcoming_renewal(104, strtotime($rb_renewals['lease_contract']['end_date']));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id=104&type=LEASE CONTRACT&name=ROCHE BOBOIS");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $rb_renewals['lease_contract']['end_date'],
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($rb_renewals['lease_contract']['end_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>LEASE CONTRACT</td>
                                                    <td></td>
                                                    <td>ROCHE BOBOIS</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td></td>
                                                    </tr>
                                        "];
                            }
                        }

                        if (!empty($rb_renewals['vat_returns_filing'])) {
                            $diff = rb_datediff(date('Y-m-d'), $rb_renewals['vat_returns_filing']['to_date']);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['vat_returns_filing']) {
                                $ur = set_upcoming_renewal(105, strtotime($rb_renewals['vat_returns_filing']['to_date']));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id=105&type=VAT RETURNS FILING&name=ROCHE BOBOIS");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $rb_renewals['vat_returns_filing']['to_date'],
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($rb_renewals['vat_returns_filing']['to_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>VAT RETURNS FILING</td>
                                                    <td></td>
                                                    <td>ROCHE BOBOIS</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                            }
                        }

                        if (!empty($rb_renewals['amc'])) {
                            $diff = rb_datediff(date('Y-m-d'), $rb_renewals['amc']['end_date']);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['amc']) {
                                $ur = set_upcoming_renewal(106, strtotime($rb_renewals['amc']['end_date']));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id=106&type=ANNUAL MAINTAINANCE CONTRACT&name=ROCHE BOBOIS");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                        . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $rb_renewals['amc']['end_date'],
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($rb_renewals['amc']['end_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>ANNUAL MAINTAINANCE CONTRACT</td>
                                                    <td></td>
                                                    <td>ROCHE BOBOIS</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                            }
                        }

                        if (!empty($rb_renewals['container_arrival'])) {
                            $diff = rb_datediff(date('Y-m-d'), $rb_renewals['container_arrival']['arrival_date']);
                            if (!empty($diff->days) && $diff->days <= $reminder_days['container_arrival']) {
                                $ur = set_upcoming_renewal(107, strtotime($rb_renewals['container_arrival']['arrival_date']));
                                $style = ur_status($ur->status, $diff->invert);
                                $data_url = admin_url("admin.php?page=renewal&emp_id=107&type=CONTAINER ARRIVAL&name=ROCHE BOBOIS");
                                $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                        . "<option value=''>Pending</option>"
                                        . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>" . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                        . "</select>";
                                $tr[] = [
                                    'date' => $rb_renewals['container_arrival']['arrival_date'],
                                    'tr' => "   <tr $style>
                                                    <td>" . rb_date($rb_renewals['container_arrival']['arrival_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>CONTAINER ARRIVAL</td>
                                                    <td></td>
                                                    <td>ROCHE BOBOIS</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                            }
                        }

                        foreach ($rb_vehicles as $value) {
                            if (!empty($value['to_date'])) {
                                $diff = rb_datediff(date('Y-m-d'), $value['to_date']);
                                if (!empty($diff->days) && $diff->days <= $reminder_days['vehicle_insurance']) {
                                    $ur = set_upcoming_renewal(201, strtotime($value['to_date']));
                                    $style = ur_status($ur->status, $diff->invert);
                                    $data_url = admin_url("admin.php?page=renewal&emp_id=201&type=Vehicle Insurance Policy&name={$value['model']}");
                                    $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                    $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                            . "<option value=''>Pending</option>"
                                            . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>"
                                            . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                            . "</select>";
                                    $tr[] = [
                                        'date' => $value['to_date'],
                                        'tr' => "   <tr $style>
                                                    <td>" . rb_date($value['to_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>Vehicle Insurance Policy</td>
                                                    <td></td>
                                                    <td>{$value['model']}</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                                }
                            }
                            if (!empty($value['end_date'])) {
                                $diff = rb_datediff(date('Y-m-d'), $value['end_date']);
                                if (!empty($diff->days) && $diff->days <= $reminder_days['vehicle_permit_no']) {
                                    $ur = set_upcoming_renewal(202, strtotime($value['end_date']));
                                    $style = ur_status($ur->status, $diff->invert);
                                    $data_url = admin_url("admin.php?page=renewal&emp_id=202&type=Vehicle Ads. Permit Ref&name={$value['model']}");
                                    $textarea = "<textarea class='note' row=2 placeholder='Notes' data-note_id='{$ur->id}'>{$ur->note}</textarea>";
                                    $status = "<select class='ur-status'  data-ur_id='{$ur->id}'>"
                                            . "<option value=''>Pending</option>"
                                            . "<option value='Processing' " . ($ur->status == 'Processing' ? 'selected' : '') . ">Processing</option>" . "<option value='Renewed' " . ($ur->status == 'Renewed' ? 'selected' : '') . ">Renewed</option>"
                                            . "</select>";
                                    $tr[] = [
                                        'date' => $value['end_date'],
                                        'tr' => "   <tr $style>
                                                    <td>" . rb_date($value['end_date']) . "</td>
                                                    <td>" . ($diff->invert ? '-' : '+') . $diff->days . "</td>
                                                    <td>Vehicle Ads. Permit Ref</td>
                                                    <td></td>
                                                    <td>{$value['model']}</td>
                                                    <td>$status</td>
                                                    <td><a href='$data_url' class='btn btn-primary btn-sm send-reminder'>Send Reminder</a></td>
                                                    <td>$textarea</td>
                                                    </tr>
                                        "];
                                }
                            }
                        }



                        usort($tr, function ($a, $b) {
                            $t1 = strtotime($a['date']);
                            $t2 = strtotime($b['date']);
                            return $t2 - $t1;
                        });

                        foreach ($tr as $value) {
                            $table .= $value['tr'];
                        }

                        $table .= "</tbody>";
                        $table .= "</table>";
                        $html .= $table;
                        echo $html .= "</div></div>";
                        $pdf_file = make_pdf_file_name("RENEWALS.pdf")['path'];
                        ?>

                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                }
                                ?>
                                <a href = '<?= export_excel_report($pdf_file, 'daily_operational_report', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                <br/>
                                <br/>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });

            jQuery('.note').on('blur', function () {
                var note_id = jQuery(this).data('note_id');
                var note = jQuery(this).val();

                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/update-ur.php",
                    type: "post",
                    dataType: "json",
                    data: {note_id: note_id, note: note},
                    success: function (response) {
                        if (response.status) {

                        }
                    }
                });
            });

            jQuery('.ur-status').change(function () {
                var ur_id = jQuery(this).data('ur_id');
                var status = jQuery(this).val();
                jQuery.ajax({
                    url: "<?= get_template_directory_uri() ?>/ajax/update-ur.php",
                    type: "post",
                    dataType: "json",
                    data: {ur_id: ur_id, status: status},
                    success: function (response) {}
                });
            });

        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
}

function ur_status($status, $invert) {
    $style = '';
    if ($status == 'Renewed') {
        $style = "style='background:#0d840d;color:#fff'";
    } elseif ($status == 'Processing') {
        $style = "style='background:#d0d03e;color:#000'";
    } elseif ($invert) {
        $style = "style='background:rgb(200,46,42);color:#fff'";
    }
    return $style;
}
