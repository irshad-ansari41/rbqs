<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_leave_salary_view_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    $leave_salary = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_leave_salaries WHERE id={$id}");
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
                        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
                        <h1 class="wp-heading-inline">Leave Salary</h1>
                        <form>
                            <?php
                            $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{font-size:10px;}
                                        table tr td{font-size:10px;}
                                        table#confirm-order-items tr td{font-size:16px;}
                                        table{width:100%;}
                                        .bb{border-bottom: 1px solid #000!important;}
                                        #page-inner-content{max-width: 800px;margin: auto;}
                                    </style>
                                    <div id='page-inner-content' class='postbox'><br/>
                                        <div class='inside' style='max-width:100%;margin:auto'>";

                            $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:26px;font-weight:bold'>LEAVE SALARY FORM</span></h4></td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                            $table .= "<table id='confirm-order-items' class='' cellpadding=10 cellspacing=10 style='border-collapse:collapse'>";
                            $table .= "<tbody>";
                            // Receipts 
                            $employee = get_employee($leave_salary->emp_id);
                            $table .= "
                                        <tr><td style='text-align:right;width:250px'>Date:</td> <td class='bb'>" . rb_date($leave_salary->created_at) . "</td></tr>
                                        <tr><td style='text-align:right;'>Name:</td> <td class='bb'>$employee->name</td></tr>
                                        <tr><td style='text-align:right;'>Joining Date:</td><td class='bb'>" . rb_date($employee->joining_date) . "</td></tr>
                                        <tr><td style='text-align:right;'>Date of Last Leave Salary:</td> <td class='bb'>" . rb_date($leave_salary->last_ls_date) . "</td></tr>
                                        <tr><td style='text-align:right;'>Leave Salary Paid for Period:</td> <td class='bb'>$leave_salary->ls_paid_period</td></tr>
                                        <tr><td style='text-align:right;'>Amount:</td> <td class='bb'>$leave_salary->amount</td></tr>
                                        <tr><td colspan=2  style='text-align:right;'><br/><br/><br/></td></tr>
                                        <tr><td style='text-align:right;'>Accountant's Signature:</td> <td class='bb'></td></tr>
                                         <tr><td colspan=2 style='text-align:right;'><br/><br/><br/></td></tr>
                                        <tr><td style='text-align:right;'>Managing Director's Signature:</td> <td class='bb'></td></tr>
                                         <tr><td colspan=2 style='text-align:right;'><br/><br/><br/></td></tr>
                                        <tr><td style='text-align:right;'>Received by:</td> <td></td></tr>
                                        <tr><td style='text-align:right;'>Employee's Signature:</td> <td class='bb'></td></tr>
                                        ";


                            $table .= "</tbody></table>";
                            $html .= $table;
                            echo $html .= "<br/></div></div>";
                            $pdf_file = $leave_salary->pdf_path;
                            store_pdf_path('ctm_hr_leave_salaries', $leave_salary->id, "LEAVE_SALARY_{$leave_salary->id}_{$employee->name}.pdf", $pdf_file);
                            ?>
                            <br/>
                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-center">
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                    }
                                    ?>
                                    <a href = '<?= export_excel_report($pdf_file, 'bank_deposit_registory', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                        </form>
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
                    url: "<?= get_template_directory_uri() ?>/ajax/ds-note.php",
                    type: "post",
                    dataType: "json",
                    data: {note_id: note_id, note: note},
                    success: function (response) {
                        if (response.status) {

                        }
                    }
                });
            });
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('leave_salary_copy_dir'));
}
