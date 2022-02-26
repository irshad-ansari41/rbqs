<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_employee_view_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $id = !empty($getdata['id']) ? $getdata['id'] : '';

    if (empty($id)) {
        wp_redirect(admin_url('/admin.php?page=employee'));
        exit();
    }

    $employee = get_employee($id);
    ?>
    <style>#welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .wp-list-table{table-layout: auto!important;}
        table tr td.collection_name{width:400px}
        table tr td.description{width:200px}
        #being_html{border: 1px solid #000;padding: 10px; border-radius: 3px;height: 100px}
        select#supplier-name:invalid,select#employee-name:invalid {
            height: 0px !important;
            opacity: 0 !important;
            position: absolute !important;
            display: flex !important;
            width:1px!important;
        }
        .chosen-container{width:100%!important;}
    </style>
    <div class="wrap">
        <span id="open-close-menu" title="Close & Open Side Menu" class="dashicons dashicons-editor-code"></span>
        <h1 class="wp-heading-inline">View Employee</h1>
        <br/><br/>
        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="welcome-to-aquila" class="postbox" style="max-width:800px;margin:auto"><br/>
                            <h2 class="hndle ui-sortable-handle"><span class=""></span></h2>
                            <div class="inside" >
                                <?php
                                $html = "<style>
                                        table tr td,p,strong,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table{width:100%;}
                                        table tr td{padding:2px!important;}
                                    </style>
                                    ";
                                $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                                <td style='text-align:left;vertical-align: middle;'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'><br/>
                                                    <br/>
                                                </td>
                                            </tr>
                                            <tr valign='top'>
                                                <td style='text-align:left;vertical-align: middle;'>
                                                    <h4><span style='font-size:26px;font-weight:bold'>EMPLOYEE PROFILE</span></h4>
                                                </td>
                                            </tr>
                                        </table>";

                                $table .= "<table style='width:100%'>

                                        <tr>
                                            <td style='width:200px'><span>Name</span></td>
                                            <td><span>: " . $employee->name . "</span></td>
                                            <td rowspan=8 colspan=2 style='text-align:right;vertical-align: top;'>
                                            <img src='" . get_image_src($employee->profile_image) . "' height=150 /></td>
                                        </tr>

                                        <tr>
                                            <td><span>Nationality</span></td>
                                            <td><span>: " . get_country($employee->nationality, 'name') . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Designation</span></td>
                                            <td><span>: " . $employee->designation . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Department</span></td>
                                            <td><span>: " . $employee->department . "</span></td>
                                        </tr>

                                        <tr>
                                            <td><span>Joining Date</span></td>
                                            <td><span>: " . $employee->joining_date . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Type of Contract</span></td>
                                            <td><span>: " . $employee->contract_type . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Visa</span></td>
                                            <td><span>: " . $employee->visa . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Sponsored By</span></td>
                                            <td><span>: " . $employee->sponsored_by . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Visa Status:</span></td>
                                            <td><span>: " . $employee->visa_status . "</span></td>
                                        </tr>
                                                

                                        <tr>
                                            <td colspan='4' style='height: 30px;'><strong>ADDRESS</strong></td></tr>
                                        <tr>
                                            <td style='width:200px' style='vertical-align:top'><span>Current</span></td>
                                            <td>: <span> " . $employee->address_current . "</span></td>
                                            <td style='vertical-align:top'><span>Home Country</span></td>
                                            <td>: <span> " . $employee->address_home_country . "</span></td>
                                        </tr>

                                        <tr><td colspan='4' style='height: 30px;'><strong>EMIRATES ID DETAILS</strong></td></tr>
                                        <tr>
                                            <td><span>EID Number</span></td>
                                            <td><span>: " . $employee->eid_number . "</span></td>
                                            <td colspan='2'><span>EID Possession</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>EID Issue Date</span></td>
                                            <td><span>: " . rb_date($employee->eid_issue_date) . "</span></td>
                                            <td colspan='2'><span>• $employee->eid_possession</span></td>
                                        </tr>
                                        <tr><td><span>EID Expiry Date</span></td>
                                            <td colspan='3'><span>: " . rb_date($employee->eid_expiry_date) . "</span></td>
                                        </tr>

                                        <tr><td colspan='4' style='height: 30px;'><strong>LABOR CARD DETAILS</strong></td></tr>
                                        <tr>
                                            <td><span>Work Permit Number</span></td>
                                            <td colspan='3'><span>: " . $employee->work_permit_number . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Personal Number</span></td>
                                            <td colspan='3'><span>: " . $employee->wp_personal_number . "</span></td>
                                        </tr>
                                        <tr><td><span>Expiry Date</span></td>
                                            <td colspan='3'><span>: " . rb_date($employee->wp_expiry_date) . "</span></td>
                                        </tr>

                                        <tr><td colspan='4' style='height: 30px;'><strong>PASSPORT DETAILS</strong></td></tr>
                                        <tr>
                                            <td><span>Passport Number</span></td>
                                            <td><span>: " . $employee->passport_no . "</span></td>
                                            <td colspan='2'><span>Passport Possession</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Passport Issue Date</span></td>
                                            <td><span>: " . rb_date($employee->passport_issue_date) . "</span></td>
                                            <td colspan='2'><span>• $employee->passport_possession</span></td>
                                        </tr>
                                        <tr><td><span>Passport Expiry Date</span></td>
                                            <td colspan='3'><span>: " . rb_date($employee->passport_expiry_date) . "</span></td>
                                        </tr>

                                        <tr><td colspan='4' style='height: 30px;'><strong>INSURANCE DETAILS</strong></td></tr>
                                        <tr>
                                            <td><span>Policy Number</span></td>
                                            <td><span>: " . $employee->policy_number . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Policy Effective Date</span></td>
                                            <td><span>: " . rb_date($employee->policy_effective_date) . "</span></td>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align:left'><span>Policy Expiry Date</span></td>
                                            <td><span>: " . rb_date($employee->policy_expiry_date) . "</span></td>
                                        </tr>

                                        <tr><td colspan='4' style='height: 30px;'> <strong>SALARY DETAILS</strong></td></tr>
                                        <tr>
                                            <td><span>Basic Salary</span></td>
                                            <td><span>: " . number_format($employee->basic_salary, 2) . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>HRA</span></td>
                                            <td><span>: " . number_format($employee->hra_salary, 2) . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Other Allowance</span></td>
                                            <td style='padding-bottom: 0'>
                                                <span style='display: inline-block;border-bottom: 1px solid #000; '>: 
                                                    " . number_format($employee->allowance_salary, 2) . "</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span>Total</span></td>
                                            <td style='padding-top: 0'>
                                                <span style='display: inline-block;border-top: 1px solid #000; '>: 
                                                    " . number_format($employee->total_salary, 2) . "</span>
                                            </td>                                            
                                        </tr>

                                        <tr><td colspan='4' style='height: 30px;'><strong>EMERGENCY CONTACT DETAILS</strong></td></tr>
                                        <tr><td colspan='2'><span>Current</span></td>
                                        <td colspan='2'><span>Home Country</span></td></tr>
                                        <tr>
                                            <td><span>Name</span></td>
                                            <td><span>: " . $employee->ecd_name_1 . "</span></td>
                                            <td><span>Name</span></td>
                                            <td><span>: " . $employee->ecd_name_2 . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Relationship</span></td>
                                            <td><span>: " . $employee->ecd_rel_1 . "</span></td>
                                            <td><span>Relationship</span></td>
                                            <td><span>: " . $employee->ecd_rel_2 . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Address</span></td>
                                            <td><span>: " . $employee->ecd_address_1 . "</span></td>
                                            <td><span>Address</span></td>
                                            <td><span>: " . $employee->ecd_address_2 . "</span></td>
                                        </tr>
                                        <tr>
                                            <td><span>Contact No</span></td>
                                            <td><span>: " . $employee->ecd_phone_1 . "</span></td>
                                            <td><span>Contact No</span></td>
                                            <td><span>: " . $employee->ecd_phone_2 . "</span></td>
                                        </tr>

                                    </table>";

                                $html .= $table;
                                echo $html .= "";
                                $pdf_file = make_pdf_file_name("EMPLOYEE_PROFILE{$employee->id}.pdf")['path'];
                                ?>
                                <br/><br/><br/>
                            </div>
                        </div>

                         <br/><br/><br/>

                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <a href="?page=employee"  class="button-secondary" >Back</a>&nbsp;
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url("pdf=1") . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success btn-sm text-white'>View PDF</a>&nbsp;&nbsp;&nbsp;";
                                }
                                ?>
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

            jQuery('.chosen-select').chosen();

            jQuery('#open-close-menu').click(() => {
                jQuery('#collapse-button').trigger('click');
            });
        });



        jQuery('#check_image').click(function (e) {
            file_uploader(e, 'image', 'check-image', false);
        });

        function file_uploader(e, input, output, multiple) {
            var custom_uploader;
            e.preventDefault();
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: multiple
            });
            custom_uploader.on('select', function () {
                var selection = custom_uploader.state().get('selection');
                var attachment_ids = selection.map(function (attachment) {
                    attachment = attachment.toJSON();
                    if (multiple == false) {
                        jQuery('#' + output).html('');
                    }
                    jQuery('#' + output).append("<input type='hidden' name='" + input + "' value='" + attachment.id + "'><img src='" + attachment.url + "' style='width:250px'>");
                }).join();
            });
            custom_uploader.open();
        }

    </script>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('employee_profile_copy_dir'));
}
