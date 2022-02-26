<?php
include_once 'email/send-client-account-email.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_ctm_account_report_page() {
    global $wpdb;
    $getdata = filter_input_array(INPUT_GET);
    $postdata = filter_input_array(INPUT_POST);
    $date = date('Y-m-d');

    if (!empty($postdata['send_email'])) {
        $email_status = rb_send_email($postdata);
        $msg = !empty($email_status) ? "<strong>Success!</strong> Email has been sent successfully." : 0;
    }

    $client_id = !empty($getdata['client_id']) ? $getdata['client_id'] : '';
    $from_date = !empty($getdata['from_date']) ? $getdata['from_date'] : date('Y-m-01');
    $to_date = !empty($getdata['to_date']) ? $getdata['to_date'] : date('Y-m-t');

    $whr_client_id = $client_id ? " client_id='{$client_id}'" : 0;
    $whr_from_date = $from_date ? " created_at >='{$from_date}'" : " created_at=" . date('Y-m-01');
    $whr_to_date = $to_date ? " created_at <='{$to_date}'" : " created_at=" . date('Y-m-t');

    $sql = "SELECT id,quotation_id,client_id,total_amount,paid_amount,balance_amount,payment_date,receipt_type FROM {$wpdb->prefix}ctm_receipts "
            . "where $whr_client_id  AND $whr_from_date AND $whr_to_date AND receipt_type!='Credit' ORDER BY id ASC";
    $results = $wpdb->get_results($sql);

    $client = get_client($client_id);
    $client_name = !empty($client) ? $client->name : '';

    $new_results = [];
    foreach ($results as $value) {
        $new_results[$value->quotation_id][] = $value;
    }
    if (!empty($client_id)) {
        make_model_send_account_email($client_id);
    }
    ?>
    <style>
        #welcome-to-aquila input[type=text], #welcome-to-aquila input[type=search], #welcome-to-aquila input[type=tel], #welcome-to-aquila input[type=time], 
        #welcome-to-aquila input[type=url], #welcome-to-aquila input[type=week], #welcome-to-aquila input[type=password], #welcome-to-aquila input[type=color], 
        #welcome-to-aquila input[type=date], #welcome-to-aquila input[type=datetime], #welcome-to-aquila input[type=datetime-local], #welcome-to-aquila input[type=email], 
        #welcome-to-aquila input[type=month], #welcome-to-aquila input[type=number], #welcome-to-aquila select, #welcome-to-aquila textarea { width: 100%; }
        .postbox-container{margin: auto;float: unset;}
        .hide,.toggle-indicator{display: none}
        .text-center{text-align:center!important;}
        .new-quotation{text-align:center;padding: 20px; margin: 20px auto; width: 200px; border: 1px solid #eee; background: #000; color: #fff; cursor: pointer;}
        table#confirm-order-items{table-layout: fixed;}
        table#confirm-order-items tr th,table#confirm-order-items tr td{font-size:12px;}
        table#confirm-order-items tr th{vertical-align: middle;}
        .attachment-large{width:50px;height: auto;}
        #tbl-filter{background: #fff; border: 20px solid #fff;}
    </style>
    <div class="wrap">
        <h1 class="wp-heading-inline">Client Statement of Account</h1>
        <br/><br/>
        <?php if (!empty($msg)) {
            ?>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?= $msg ?>
            </div>
        <?php } ?>
        <form method="get">
            <input type="hidden" name="page" value="<?= $getdata['page'] ?>" />
            <table id="tbl-filter" cellpadding="5" cellspacing="5" >
                <tr>
                    <td>Client: <br/>
                        <select name="client_id" id="client-name" class="chosen-select" style="width: 200px" required onchange="this.form.submit()">
                            <option value="">Loading...</option>
                        </select>
                        <?php
                        if (empty($getdata['client_id'])) {
                            echo "<br/><span style='color:red'>Please select client.</span>";
                        }
                        ?>
                    </td>
                    <td>From Date: <br/>
                        <input type="date" name="from_date"  value="<?= $from_date ?>" style="width:175px"  required />
                    </td>
                    <td>To Date:  <br/>
                        <input type="date" name="to_date" value="<?= $to_date ?>" style="width:175px"  required />
                    </td>

                    <td><br/>
                        <button type="submit" name="filter" value="1" class="btn btn-sm btn-primary ">Filter</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="admin.php?page=<?= $getdata['page'] ?>" class="btn btn-sm btn-secondary text-white">RESET</a>
                    </td>
                </tr>
            </table>
        </form>
        <br/>

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="columns-1">
                <div id="postbox-container" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div id="page-inner-content" class="postbox">


                            <?php
                            $html = "
                                    <style>#page-inner-content input[type=text], #page-inner-content input[type=search], #page-inner-content input[type=tel], #page-inner-content input[type=time], #page-inner-content input[type=url], #page-inner-content input[type=week], #page-inner-content input[type=password], #page-inner-content input[type=color], #page-inner-content input[type=date], #page-inner-content input[type=datetime], #page-inner-content input[type=datetime-local], #page-inner-content input[type=email], #page-inner-content input[type=month], #page-inner-content input[type=number], #page-inner-content select, #page-inner-content textarea { width: 100%; }
                                        table tr th,table tr td,p,b,span{font-size:12px;font-family:'Tahoma'}
                                        h6 span{text-transform:uppercase;}
                                        p{margin-bottom: 0;}
                                        table{empty-cells:hidden;}
                                        table tr td{font-size:12px;font-family:'Tahoma'}
                                        table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7),table tr td:nth-child(8),table tr td:nth-child(9){width: 55px;}
                                        .text-center{text-align:center;}
                                        .bg-blue{background:#c5d9f1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .bg-light{background:##eaedf1;background-color:#c5d9f1;-webkit-print-color-adjust: exact;}
                                        .attachment-large{width:50px;height: auto;}
                                        ul{margin: auto;padding: 0 15px;}
                                        ul#terms{ padding: 0; margin: 0;} 
                                        ul#terms li{ padding: 2px 0!important; margin: 0!important; text-align: left!important; width: 100%!important; font-size:12px; font-weight: bold;}
                                    </style>
                                    
                                    <div id='welcome-to-aquila' class='postbox'>
                                        <div class='inside' style='max-width:800px;margin:auto'>
                                            <br/>";
                            $table = "<table width='800'>
                                                <tr valign='top'>
                                                    <td colspan=2 style='text-align:right'>
                                                    <img src=" . get_template_directory_uri() . "/assets/images/logo.jpg class='img-responsive' width=250 height=48 style='margin: auto;width: 250px; height:48px;'>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2' style='text-align:center'><br/><br/>
                                                        <h6><span style='font-size:22px;'>CLIENT STATEMENT OF ACCOUNT</span></h6><br/><br/>
                                                    </td>
                                                </tr>
                                            </table>
                                             <table border='1' style='border-collapse: collapse' width=800 cellpadding=5 cellspacing=5>
                                      <tr class=bg-blue>
                                      <td colspan='5'><b>Customer Details</b></td><td class='text-center' colspan='2'><b>Date</b></td>
                                      </tr>
                                      <tr>
                                      <td><b>Name:</b></td><td colspan='4'><b>" . (!empty($client) ? $client->name : '') . "</b></td>"
                                    . "<td class='text-center' colspan='2'><b>" . rb_date('now') . "</b></td>
                                      </tr>
                                      
                                      <tr><td colspan='7'><br/></td></tr>
                                      <tr class='text-center bg-blue'>
                                        <td class='text-center' style='width: 60px;'><b>QTN #</b></td>
                                        <td class='text-center' style='width: 60px;'><b>TYPE</b></td>
                                        <td class='text-center' style='width: 65px;'><b>RECEIPT #</b></td>
                                        <td class='text-center' style='width: 65px;'><b>PAYMENT<br/>DATE</b></td>
                                        <td  style='width: 150px;'>&nbsp;</td>
                                        <td class='text-center' style='width: 65px;'><b>DEBIT</b></td>
                                        <td class='text-center' style='width: 65px;'><b>CREDIT</b></td>
                                      </tr>
                                      <tr><td colspan='7'><br/></td></tr>";

                            $balance_amount = 0;

                            foreach ($new_results as $value) {
                                $count = count($value);
                                $first = array_shift($value);
                                $last = array_pop($value);
                                $middle = $value;

                                $attr1 = first_transaction($first);
                                $table .= $attr1['html'];
                                $balance_amount += $attr1['balance_amount'];

                                if ($count > 2) {
                                    $arr = middle_transaction($middle);
                                    $table .= $arr['html'];
                                    $balance_amount += $arr['balance_amount'];
                                }
                                if ($count > 1) {
                                    $table .= last_transaction($last);
                                    $balance_amount = get_qtn_balance_amount($last->quotation_id);
                                }
                            }
                            $table .= "
                                    <tr class='font-weight-bold'>
                                       <td colspan=5 ><b>Balance Amount Receivable</b></td>
                                       <td class='text-center'><b></b></td>"
                                    . "<td class='text-center text-red'><b>" . number_format($balance_amount, 2) . "</b></td></td>
                                    </tr>

                                    <tr><td colspan='7'><br/></td></tr></table>";

                            $html .= $table;

                            $html .= "<br/><br/><br/><br/>


                                    </div>
                                    </div>";
                            echo $html;
                            $pdf_file = make_pdf_file_name("account_statement_{$client_name}_{$date}.pdf")['path'];
                            ?>

                        </div>
                        <div class="row btn-bottom">
                            <div class="col-sm-12 text-center">
                                <?php
                                if (empty($getdata['pdf']) || pdf_exist($pdf_file)) {
                                    echo "<a href='" . curr_url('pdf=1') . "' class='btn btn-warning btn-sm text-white'>" . (pdf_exist($pdf_file) ? 'Re-Generate PDF' : 'Generate PDF') . "</a>&nbsp;&nbsp;";
                                } if (pdf_exist($pdf_file)) {
                                    echo "<a href='" . download_pdf_url($pdf_file) . "' target='_blank' class='btn btn-success  btn-sm text-white'>View PDF</a>&nbsp;&nbsp;";
                                    echo '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myEmailModal" data-keyboard="false">Send Email</button>';
                                }
                                ?>
                                <a href = '<?= export_excel_report($pdf_file, 'account_statement', $table) ?>' class='btn btn-success text-white btn-sm '>Export as Excel</a>
                            </div>
                        </div>
                    </div>	
                </div>
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div>
    <script>
        jQuery(document).ready(() => {
            jQuery.ajax({
                url: "<?= get_template_directory_uri() ?>/ajax/get-clients.php",
                dataType: 'json',
                success: function (data) {
                    var client_id = '<?= !empty($getdata['client_id']) ? $getdata['client_id'] : 0; ?>';
                    jQuery('#client-name').html('');
                    var html = '<option value="">Select Client</option>';
                    jQuery.each(data, function (i, client) {
                        var selected = client_id === client.id ? 'selected' : '';
                        html += `<option value="${client.id}" ${selected}>${client.name}</option>`;
                    });
                    jQuery('#client-name').html(html);
                    jQuery('.chosen-select').chosen();
                }
            });
        });
    </script>
    <?php
    generate_pdf($html, $pdf_file);
    pdf_copy($pdf_file, get_option('client_statement_copy_dir'));
}

function first_transaction($value) {

    $sales_reversal = get_sales_reversal_by_qid($value->quotation_id);
    $tax_credit_note = get_tax_credit_note_by_qid($value->quotation_id);
    $store_credit_note = get_store_credit_by_qid($value->quotation_id);
    $qtn = get_revised_no($value->quotation_id);
    $quotation = get_quotation($value->quotation_id);

    $balance_amount = get_qtn_balance_amount($value->quotation_id);
    $html = "<tr><td class='text-center'>{$qtn}</td>"
            . "<td class='text-center'>$quotation->type</td><td></td><td></td>"
            . "<td>Total Purchased Amount</td>"
            . "<td>" . number_format($value->total_amount, 2) . "</td>"
            . "<td></td></tr>";
    $html .= "<tr>"
            . "<td></td><td></td>"
            . "<td class='text-center'>{$value->id}</td>"
            . "<td class='text-center'>" . rb_date($value->payment_date, 'd-M-Y') . "</td>"
            . "<td>Non-Refundable Amount Received</td>"
            . "<td></td><td class='text-center'>" . number_format($value->paid_amount, 2) . "</td>"
            . "</tr>";
    $html .= "<tr>"
            . "<td></td><td></td><td></td><td></td>"
            . "<td>Balance Amount Receivable</td><td></td>"
            . "<td class='text-center text-red'>" . number_format($value->balance_amount, 2) . "</td>"
            . "</tr><tr class='bg-light'><td colspan='7'><br/></td></tr>";

    if (!empty($sales_reversal) && rb_float($sales_reversal->total_amount)) {
        $balance_amount += $sales_reversal->deduct_amount;

        $html .= "<tr><td class='text-center'>{$qtn}</td>"
                . "<td class='text-center'>$quotation->type</td><td></td><td></td>"
                . "<td>Reversal of confirmed QTN</td>"
                . "<td>-" . number_format($sales_reversal->total_amount, 2) . "</td>"
                . "<td class='text-center'></td></tr>";
        $html .= "<tr><td class='text-center'></td>"
                . "<td class='text-center'></td><td class='text-center'>{$value->id}</td><td class='text-center'>1</td>"
                . "<td>Overpayment - Store Credit</td>"
                . "<td></td>"
                . "<td class='text-center'>-" . number_format($sales_reversal->total_amount - $store_credit_note->deduct_amount, 2) . "</td></tr>";
        $html .= "<tr><td class='text-center'></td>"
                . "<td class='text-center'></td><td></td><td></td>"
                . "<td>Balance Amount Receivable</td>"
                . "<td></td>"
                . "<td class='text-center text-red'>0.00</td></tr>"
                . "<tr class='bg-light'><td colspan='7'><br/></td></tr>";
    }
    if (!empty($tax_credit_note) && rb_float($tax_credit_note->total_amount)) {
        $balance_amount += $tax_credit_note->deduct_amount;
        $html .= "<tr><td class='text-center'>{$qtn}</td>"
                . "<td class='text-center'>$quotation->type</td><td></td><td></td>"
                . "<td>Tax Credit Note</td>"
                . "<td></td>"
                . "<td class='text-center'>-" . number_format($tax_credit_note->total_amount, 2) . "</td></tr>";
        $html .= "<tr><td></td><td></td><td></td><td></td>"
                . "<td>Charges</td>"
                . "<td class='text-center'>" . number_format($tax_credit_note->deduct_amount, 2) . "</td>"
                . "<td></td></tr>";
        $html .= "<tr><td></td><td></td><td></td><td></td>"
                . "<td>Store Credit Amount</td>"
                . "<td></td>"
                . "<td class='text-center'>-" . number_format($tax_credit_note->total_amount - $tax_credit_note->deduct_amount, 2) . "</td></tr>"
                . "<tr class='bg-light'><td colspan='7'><br/></td></tr>";
        $html .= "<tr><td></td><td></td><td></td><td></td>"
                . "<td>Reversal of Charges  </td>"
                . "<td></td>"
                . "<td class='text-center'>" . number_format($tax_credit_note->deduct_amount, 2) . "</td></tr>";
        $html .= "<tr><td></td><td></td><td></td><td></td>"
                . "<td>Store Credit Amount</td>"
                . "<td></td>"
                . "<td class='text-center'>" . number_format($tax_credit_note->total_amount, 2) . "</td></tr>"
                . "<tr class='bg-light'><td colspan='7'><br/></td></tr>";
    }

    return ['html' => $html, 'balance_amount' => $balance_amount];
}

function middle_transaction($data) {
    $balance_amount = 0;
    $html = '';
    foreach ($data as $value) {
        $balance_amount += get_qtn_balance_amount($value->quotation_id);
        ;
        $html = "<tr>"
                . "<td></td><td></td>"
                . "<td class='text-center'>{$value->id}</td>"
                . "<td class='text-center'>" . rb_date($value->payment_date, 'd-M-Y') . "</td>"
                . "<td>Non-Refundable Amount Received</td>"
                . "<td></td>"
                . "<td class='text-center'>" . number_format($value->paid_amount, 2) . "</td>"
                . "</tr>";
        $html .= "<tr>"
                . "<td></td><td></td><td></td><td></td>"
                . "<td>Balance Amount Receivable</td>"
                . "<td></td>"
                . "<td class='text-center text-red'>" . number_format($value->balance_amount, 2) . "</td>"
                . "</tr><tr class='bg-light'><td colspan='7'><br/></td></tr>";
    }
    return ['html' => $html, 'balance_amount' => $balance_amount];
}

function last_transaction($value) {

    $html = "<tr>"
            . "<td></td><td></td>"
            . "<td class='text-center'>{$value->id}</td>"
            . "<td class='text-center'>" . rb_date($value->payment_date, 'd-M-Y') . "</td>"
            . "<td>Non-Refundable Amount Received</td>"
            . "<td></td>"
            . "<td class='text-center'>" . number_format($value->paid_amount, 2) . "</td>"
            . "</tr>";
    $html .= "<tr>"
            . "<td></td><td></td><td></td><td></td>"
            . "<td>Balance Amount Receivable</td>"
            . "<td></td>"
            . "<td class='text-center text-red'>" . number_format($value->balance_amount, 2) . "</td>"
            . "</tr>"
            . "<tr class='bg-light'><td colspan='7'><br/></td></tr>";
    return $html;
}
