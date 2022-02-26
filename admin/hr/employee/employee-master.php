<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_employee_master_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);

    $results = get_all_employees();
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
                        
                        <h1 class="wp-heading-inline">Employee Master</h1><a href="?page=employee"  class="page-title-action" >Back</a><br/><br/>
                        <form>
                            <?php
                            $html = "<style>
                                        table tr td,p,label,h1{font-size:12px;font-family:'Tahoma'}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        table tr th{text-align:center;font-size:10px;}
                                        table tr td{text-align:center;font-size:10px;}
                                        table{width:100%;}
                                    </style>
                                    <div id='page-inner-content' class='postbox'><br/>
                                        <div class='inside' style='max-width:100%;margin:auto'>";

                            $table = "<table  width='800' style='width:100%'>
                                            <tr valign='top'>
                                            <td style='text-align:left;vertical-align: middle;'><h4><span style='font-size:26px;font-weight:bold'>EMPLOYEE MASTER</span></h4></td>
                                            <td style='text-align:right;vertical-align: middle;'>
                                                <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                <br/>
                                            </td>
                                            </tr>
                                        </table><br/>";

                            $table .= "<table confirm-order-items class='' border=1 cellpadding=3 cellspacing=3 style='border-collapse:collapse'>
                                <thead>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th rowspan=2>NO</th>
                                        <th rowspan=2>NAME</th>
                                        <th rowspan=2>NATIONALITY</th>
                                        <th colspan=2>ADDRESS</th>
                                        <th colspan=2>EMERGENCY CONTACT DETAILS</th>
                                        <th rowspan=2>Sponsored By</th>
                                        <th rowspan=2>Designation</th>
                                        <th rowspan=2>Joining<br/>Date</th>
                                        <th rowspan=2>Type of<br/>Contract</th>
                                        <th rowspan=2>VISA</th>
                                        <th rowspan=2>STATUS</th>
                                        <th colspan=3>EMIRATES ID DETAILS</th>
                                        <th colspan=3>PASSPORT DETAILS</th>
                                        <th colspan=4>SALARY DETAILS</th>
                                        <th colspan=3>INSURANCE DETAILS</th>
                                    </tr>
                                    <tr valign=middle class='text-center bg-blue'>
                                        <th>Home Country</th>
                                        <th>Current</th>
                                        <th>Home Country</th>
                                        <th>Current</th>
                                        <th>EID<br/>Number</th>
                                        <th>Issue<br/>Date</th>
                                        <th>Expiry<br/>Date</th>
                                        <th>Passport<br/>Number</th>
                                        <th>Issue Date</th>
                                        <th>Expiry Date</th>
                                        <th>Basic<br/>Salary</th>
                                        <th>HRA</th>
                                        <th>Other<br/>Allowance</th>
                                        <th>TOTAL</th>
                                        <th>Policy Number</th>
                                        <th>Policy<br/>Effective Date</th>
                                        <th>Policy<br/>Expiry Date</th>
                                    </tr>
                                </thead>";

                            $table .= "<tbody>";
                            $total = 0;
                            // Receipts 
                            foreach ($results as $value) {
                                $total += 1;
                                $table .= "<tr>
                                            <td>$total</td>
                                            <td>$value->name</td>
                                            <td>" . get_country($value->nationality, 'name') . "</td>
                                            <td>$value->address_home_country</td>
                                            <td>$value->address_current</td>
                                            <td>$value->ecd_name_1,<br/>$value->ecd_rel_1,<br/>$value->ecd_address_1,<br/>$value->ecd_phone_1</td>
                                            <td>$value->ecd_name_2,<br/>$value->ecd_rel_2,<br/>$value->ecd_address_2,<br/>$value->ecd_phone_2</td>
                                            <td>$value->sponsored_by</td>
                                            <td>$value->designation</td>
                                            <td>" . rb_date($value->joining_date) . "</td>
                                            <td>$value->contract_type</td>
                                            <td>$value->visa</td>
                                            <td>$value->visa_status</td>
                                            <td>$value->eid_number</td>
                                            <td>" . rb_date($value->eid_issue_date) . "</td>
                                            <td>" . rb_date($value->eid_expiry_date) . "</td>
                                            <td>$value->passport_no</td>
                                            <td>" . rb_date($value->passport_issue_date) . "</td>
                                            <td>" . rb_date($value->passport_expiry_date) . "</td>
                                            <td>" . number_format($value->basic_salary, 2) . "</td>
                                            <td>" . number_format($value->hra_salary, 2) . "</td>
                                            <td>" . number_format($value->allowance_salary, 2) . "</td>
                                            <td>" . number_format($value->total_salary, 2) . "</td>
                                            <td>$value->policy_number</td>
                                            <td>" . rb_date($value->policy_effective_date) . "</td>
                                            <td>" . rb_date($value->policy_expiry_date) . "</td>
                                            </tr>";
                            }

                            $table .= "</tbody></table>";
                            $html .= $table;
                            echo $html .= "</div></div>";
                            $pdf_file = make_pdf_file_name("EMPLOYEE_MASTER.pdf")['path'];
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
}
