<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_project_job_order_view_page() {

    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = current_time('mysql');
    $pjo_id = !empty($getdata['pjo_id']) ? $getdata['pjo_id'] : 0;
    if (empty($pjo_id)) {
        wp_redirect(admin_url('/admin.php?page=project-job-order'));
        exit();
    }

    if (!empty($postdata)) {
        $data = ['status' => $postdata['status'], 'updated_by' => $current_user->ID, 'updated_at' => $date];
        $wpdb->update("{$wpdb->prefix}ctm_project_job_order", $data, ['id' => $pjo_id], ['%s', '%s', '%s',], ['%d']);
    }
    $pjo = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_project_job_order where  id='{$pjo_id}'");

    if ($pjo->status == 'Draft') {
        wp_redirect(admin_url('/admin.php?page=project-job-order'));
        exit();
    }

    $pjo_meta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ctm_project_job_order_meta where pjo_id='{$pjo->id}'");

    $client = get_client($pjo->client_id);
    ?>

    <div class='wrap'>
        <div id='dashboard-widgets-wrap'>
            <div id='dashboard-widgets' class='columns-1'>
                <div id='postbox-container' class='postbox-container'>
                    <div id='normal-sortables' class='meta-box-sortables ui-sortable'>
                        <?php
                        $html = "<style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
   
                         table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                         .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                         .text-black{color:black;-webkit-print-color-adjust: exact;}
                         .text-red{color:red;-webkit-print-color-adjust: exact;}
                         </style>
                         <div id='page-inner-content' class='postbox'>
                        
                        <div class='inside' style='max-width:800px;margin:auto'>
                        <br/>";

                        $html .= "<table style='width: 800px;'>
                            <tr valign='top'>
                                <td style='vertical-align: bottom;'><b style='font-size:14px'>PJO#: 00{$pjo->id}</b></td>
                                <td style='text-align:right'>
                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                </td>
                            </tr>
                        </table>
                        <br/>
                            <h1 style='text-align:center;font-size:30px;font-weight: bold;'>PROJECT JOB ORDER</h1><br/><br/>
                            <table style='width: 800px;'>
                                <tr>
                                    <td style='width: 100%;' >
                                    <table border='0' style='border-collapse: collapse;width: 100%' cellpadding=3 cellspacing=3 >
                                    <tr> 
                                        <td style='width:100px;font-size:14px'>Client:</td>
                                        <td>{$client->name}</td>
                                        <td style='text-align:right;font-size:14px'>QTN: {$pjo->quotation_id}</td>
                                    </tr>
                                    <tr>
                                        <td style='font-size:14px'>Contact No:</td>
                                        <td>{$pjo->contact_no}</td>
                                        <td style='text-align:right;font-size:14px'>Date: " . rb_date($pjo->updated_at) . " </td>
                                    </tr>
                                    <tr>
                                        <td style='font-size:14px'>Requestd By:</td>
                                        <td>{$pjo->requested_by}</td>
                                        <td style='text-align:right'></td>
                                    </tr>
                                    <tr><td style='font-size:14px'>Address:</td><td>{$pjo->address}</td><td style='text-align:right'></td></tr>
                                        </table>
                                        <br/>
                                    </td>
                                </tr>
                            </table><br/>";

                        $html .= " <table border='1' style='border-collapse: collapse;width:800px' cellpadding=5 cellspacing=5>
                                <tr valign='middle' class='bg-blue'>
                                    <th style='text-align: center;width:50px'><b>Sr. No</b></th>
                                    <th style='text-align: center;width:150px'><b>Type</b></th>
                                    <th style='text-align: center;width:250px'><b>Details</b></th>
                                    <th style='text-align: center;'><b>Image</b></th>
                                    <th style='text-align: center;'><b>QTY</b></th>
                                    <th style='text-align: center'><b>Responsibility</b></th>
                                    <th style='text-align: center;width:120px'><b>Start<br/>Date & Time</b></th>
                                    <th style='text-align: center;width:120px'><b>Completion<br/>Date & Time</b></th>
                                </tr>";
                        $i = 0;
                        foreach ($pjo_meta as $value) {
                            $i++;
                            $item_name = get_item($value->item_id, 'collection_name');
                            $html .= "<tr>
                                        <td style='text-align:center'>{$i}</td>
                                        <td>$item_name</td>
                                        <td>" . nl2br($value->item_desc) . "<br/>" . nl2br($value->action) . "</td>
                                        <td style='text-align:center'><img src='" . get_image_src($value->image) . "' width=100 style='margin: auto;width: 100px;'></td>
                                        <td style='text-align:center'>{$value->quantity}</td>
                                        <td style='text-align:center'>{$value->responsibility}</td>
                                        <td style='text-align:center'>" . rb_date($value->start_date) . " " . rb_time($value->start_time) . "</td>
                                        <td style='text-align:center'>" . rb_date($value->end_date) . " " . rb_time($value->end_time) . "</td>
                                    </tr>";
                        }
                        $html .= "</table>";

                        $html .= "<br/><table style='width: 800px;'>
                                    <tr>
                                        <td><br/><b style='font-size:14px'>Remark:</b><br/><br/><br/></td>
                                    </tr>
                                </table>";

                        $html .= "<table style='width: 800px;'>
                                    <tr>
                                        <td><b  style='font-size:14px'>Approved By:</b></td><td style='text-align:right;font-size:14px'><b>Client acceptance Signature:</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan=2><b  style='font-size:14px'>Roche Bobois</b></td>
                                    </tr>
                                </table>";

                        $html .= "<br/><br/><br/><br/>
                        </div>
                    </div>";
                        echo $html;

                        $pdf_file = $pjo->pdf_path;
                        store_pdf_path('ctm_project_job_order', $pjo->id, "Project_Job_Order_{$pjo->id}.pdf", $pdf_file);
                        ?>

                        <div class='row btn-bottom'>
                            <div class='col-sm-4 text-center'>
                                <a href = 'admin.php?page=project-job-order' class='btn btn-dark btn-sm text-white'>Back</a>&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>";
                                }
                                ?>
                            </div>
                            <div class='col-sm-4 text-center'>
                                <?php
                                $qtn_meta_ids = $wpdb->get_var("SELECT group_concat(qtn_meta_id) FROM {$wpdb->prefix}ctm_project_job_order_meta WHERE pjo_id='{$pjo->id}' AND qtn_meta_id!=0 ");
                                $tax_invoice = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}ctm_tax_invoice WHERE pjo_id='$pjo->id'");
                                if ($pjo->qtn_type == 'Project' && empty($tax_invoice) && !empty($qtn_meta_ids)) {
                                    echo "<a href='" . admin_url() . "admin.php?page=tax-invoice&pjo_id={$pjo->id}&action=create' class='btn btn-danger btn-sm'>Create Tax Invoice</a>";
                                }
                                ?>
                            </div>
                            <div class='col-sm-4 text-center'>
                                <form method="post">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="">Change Status</option>
                                        <option value="Reschedule">Reschedule</option>
                                        <option value="Successful">Successful</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div><!--dashboard-widgets-wrap -->
    <?php
    generate_pdf($html, $pdf_file);

    pdf_copy($pdf_file, get_option('pjo_copy_dir'));
}
