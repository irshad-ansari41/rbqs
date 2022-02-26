<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_emp_adv_loan_view_page() {
    global $wpdb, $current_user;
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    $emp_adv_loan = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ctm_hr_emp_adv_loan WHERE id={$id}");

    $checked1 = !empty($emp_adv_loan) && $emp_adv_loan->payment_term == 1 ? 'checked="checked"' : '';
    $checked2 = !empty($emp_adv_loan) && $emp_adv_loan->payment_term == 2 ? 'checked="checked"' : '';
    $checked3 = !empty($emp_adv_loan) && $emp_adv_loan->payment_term == 3 ? 'checked="checked"' : '';
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
                        <h1 class="wp-heading-inline">Employee Advance Loan</h1>
                        <form>
                            <?php
                            $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{font-size:10px;}
                                        table tr td{font-size:10px;}
                                        table#confirm-order-items-1 tr td{font-size:16px;}
                                        table#confirm-order-items-2 tr td{font-size:16px;}
                                        table{width:100%;}
                                        .bt{border-top: 1px solid #000!important;}
                                        .bb{border-bottom: 1px solid #000!important;}
                                        #page-inner-content{max-width: 800px;margin: auto;}
                                    </style>
                                    <div id='page-inner-content' class='postbox'><br/>
                                        <div class='inside' style='max-width:100%;margin:auto'>";

                            $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                            <tr valign='top'>
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:26px;font-weight:bold'>ADVANCE / LOAN APPLICATION FORM</span></h4></td>
                                            
                                            </tr>
                                        </table><br/>";


                            // Receipts 
                            $employee = get_employee($emp_adv_loan->emp_id);
                            $table .= "<table id='confirm-order-items-1' class='' cellpadding=8 cellspacing=8 style='border-collapse:collapse'>
                                        <tr><td style='text-align:right;width:150px' >Date:</td><td class='bb' >" . rb_date($emp_adv_loan->created_at) . "</td></tr>
                                        <tr><td style='text-align:right;' >Name:</td> <td class='bb' >$employee->name</td></tr>
                                        <tr><td style='text-align:right;' >Sponsored By:</td><td class='bb' >$employee->sponsored_by</td></tr>
                                        <tr><td style='text-align:right;' >Degisnation:</td><td class='bb' >$employee->designation</td></tr>
                                        <tr><td style='text-align:right;' >Joining Date:</td><td class='bb' >" . rb_date($employee->joining_date) . "</td></tr>
                                        <tr><td style='text-align:right;' >Amount:</td><td class='bb' >$emp_adv_loan->amount</td></tr>";
                            $table .= "</table>";

                            $table .= "<table id='confirm-order-items-2' class='' cellpadding=8 cellspacing=8 style='border-collapse:collapse'> 
                                        <tr><td colspan=4 style='text-align:right;'><br/></td></tr>
                                        <tr><td colspan=4>Specify the reason for the Salary Advance / Loan:</td></tr>
                                        <tr><td colspan=4 class='bb'>$emp_adv_loan->reason_1</td></tr>
                                        <tr><td colspan=4 class='bb'>$emp_adv_loan->reason_2</td></tr>
                                        <tr><td colspan=4 style='text-align:right;'><br/></td></tr>
                                        <tr><td colspan=4>Kindly, specify the payment terms:</td></tr>
                                        <tr>
                                            <td style='width:30px'><input type=checkbox value='1' $checked1 /> </td>
                                            <td style='width:300px'>To be deducted from monthly salary</td>
                                            <td style='text-align: right;width:150px'>Amount:</td>
                                            <td class='bb'>" . rb_float($emp_adv_loan->installment) . "</td>
                                        </tr>
                                        <tr>
                                            <td style=''width:30px><input type=checkbox value='1' $checked2 /> </td>
                                            <td>To be paid at once</td>
                                            <td style='text-align: right;'>Date:</td>
                                            <td class='bb'>" . rb_date($emp_adv_loan->pay_date) . "</td>
                                        </tr>
                                        <tr>
                                            <td style=''width:30px><input type=checkbox value='1' $checked3 /> </td>
                                            <td>Salary Advance</td>
                                            <td style='text-align: right;'>Month:</td>
                                            <td class='bb'>" . date('F, Y', mktime(0, 0, 0, $emp_adv_loan->month, 1)) . "</td>
                                        </tr>";
                            $table .= "</table>";

                            $table .= "<br/>";

                            $table .= "<table id='confirm-order-items-2' class='' cellpadding=8 cellspacing=8 style='border-collapse:collapse'> 
                            <tr><td class='bt' style='text-align: center;'>Applicant Signature</td><td>&nbsp;</td></tr>
                            <tr><td colspan=2></td></tr>
                            <tr><td></td><td class='bt'  style='text-align: center;'><strong>Managing Director</strong></td></tr>";
                            $table .= "</table>";

                            $table .= "<br/>";

                            $table .= "<table id='confirm-order-items-2' class='' cellpadding=8 cellspacing=8 style='border-collapse:collapse'>
                            <tr><td colspan=4 class='bt'><b>NOTE:</b></td></tr>
                            <tr><td colspan=4>*** In case you have a company loan, you will not be able to proceed in your vacation/ leave until your loan is settled.
                            </td></tr>
                            <tr><td colspan=4>*** Loan is not exceeding gratuity amount</td></tr>
                            <tr><td colspan=4>*** Repayment cannot exceed 3 month</td></tr>
                            <tr><td colspan=4 class='bb'>*** Management has right to refuse this request without prejudice & without reason</td></tr>";
                            $table .= "</table>";


                            $html .= $table;
                            echo $html .= "</div></div>";
                            $pdf_file = $emp_adv_loan->pdf_path;
                            store_pdf_path('ctm_hr_emp_adv_loan', $emp_adv_loan->id, "LEAVE_SALARY_{$emp_adv_loan->id}_{$employee->name}.pdf", $pdf_file);
                            ?>
                            <br/>
                            <div class="row btn-bottom">
                                <div class="col-sm-12 text-center">
                                    <?php
                                    if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                        echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;
                            &nbsp;
                            ";
                                    } if (pdf_exist($pdf_file)) {
                                        echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;
                            &nbsp;
                            &nbsp;
                            ";
                                    }
                                    ?>
                                    <a href='<?= export_excel_report($pdf_file, 'bank_deposit_registory', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
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
                    url: "<?= get_template_directory_uri() ?>/ajax/ds - note.php",
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
    pdf_copy($pdf_file, get_option('employee_loan_copy_dir'));
}
